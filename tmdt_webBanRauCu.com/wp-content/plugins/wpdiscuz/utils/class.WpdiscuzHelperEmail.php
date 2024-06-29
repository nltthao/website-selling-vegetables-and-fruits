<?php

if ( ! defined( "ABSPATH" ) ) {
	exit();
}

class WpdiscuzHelperEmail implements WpDiscuzConstants {

	/**
	 * @var WpdiscuzOptions
	 */
	private $options;

	/**
	 * @var WpdiscuzDBManager
	 */
	private $dbManager;

	/**
	 * @var WpdiscuzHelper
	 */
	private $helper;

	public function __construct( $options, $dbManager, $helper ) {
		$this->options   = $options;
		$this->dbManager = $dbManager;
		$this->helper    = $helper;
		add_action( "init", [ &$this, "addSubscriptionRewriteRule" ] );
		add_action( "wp_ajax_wpdAddSubscription", [ &$this, "addSubscription" ] );
		add_action( "wp_ajax_nopriv_wpdAddSubscription", [ &$this, "addSubscription" ] );
		add_action( "wp_ajax_wpdCheckNotificationType", [ &$this, "checkNotificationType" ] );
		add_action( "wp_ajax_nopriv_wpdCheckNotificationType", [ &$this, "checkNotificationType" ] );
		add_action( "comment_post", [ &$this, "notificationFromDashboard" ], 10, 2 );
		add_filter( "template_include", [ &$this, "subscriptionRequestsActions" ] );
		add_filter( "query_vars", [ &$this, "addQueryVars" ] );
	}

	public function addSubscriptionRewriteRule() {

		$rules = get_option( "rewrite_rules", [] );
		$regex = "wpdiscuzsubscription/([a-z0-9-]+)[/]?$";
		add_rewrite_rule( $regex, 'index.php?wpdiscuzsubscription=$matches[1]', "top" );

		if ( ! isset( $rules[ $regex ] ) ) {
			flush_rewrite_rules();
		}
	}

	public function addQueryVars( $query_vars ) {

		if ( ! in_array( "wpdiscuzsubscription", $query_vars ) ) {
			$query_vars[] = "wpdiscuzsubscription";
		}

		return $query_vars;
	}

	public function subscriptionRequestsActions( $template ) {
		global $wpDiscuzSubscriptionMessage;
		$wpDiscuzSubscriptionMessage = "";
		$action                      = get_query_var( "wpdiscuzsubscription" );
		if ( ! $action ) {
			return $template;
		}

		if ( $action === "confirm" && isset( $_GET["wpdiscuzConfirmID"] ) && isset( $_GET["wpdiscuzConfirmKey"] ) && isset( $_GET["wpDiscuzComfirm"] ) ) {
			$this->dbManager->notificationConfirm( sanitize_text_field( $_GET["wpdiscuzConfirmID"] ), sanitize_text_field( $_GET["wpdiscuzConfirmKey"] ) );
			$wpDiscuzSubscriptionMessage = $this->options->getPhrase( "wc_comfirm_success_message" );
		} else if ( $action === "unsubscribe" && isset( $_GET["wpdiscuzSubscribeID"] ) && isset( $_GET["key"] ) ) {
			$this->dbManager->unsubscribe( sanitize_text_field( $_GET["wpdiscuzSubscribeID"] ), sanitize_text_field( $_GET["key"] ) );
			$wpDiscuzSubscriptionMessage = $this->options->getPhrase( "wc_unsubscribe_message" );
		} else if ( $action === "deletecomments" && isset( $_GET["key"] ) ) {
			$decodedEmail = get_transient( self::TRS_USER_HASH . trim( sanitize_text_field( $_GET["key"] ) ) );
			if ( $decodedEmail ) {
				$comments = get_comments( [ "author_email" => $decodedEmail, "status" => "all", "fields" => "ids" ] );
				if ( $comments ) {
					foreach ( $comments as $k => $cid ) {
						wp_delete_comment( $cid, true );
					}
				}
				$wpDiscuzSubscriptionMessage = $this->options->getPhrase( "wc_comments_are_deleted" );
			}
		} else if ( $action === "deletesubscriptions" && isset( $_GET["key"] ) ) {

			$decodedEmail = get_transient( self::TRS_USER_HASH . trim( sanitize_text_field( $_GET["key"] ) ) );
			if ( $decodedEmail ) {
				$this->dbManager->unsubscribeByEmail( $decodedEmail );
			}

			$wpDiscuzSubscriptionMessage = $this->options->getPhrase( "wc_cancel_subs_success" );
		} else if ( $action === "deletefollows" && isset( $_GET["key"] ) ) {

			$decodedEmail = get_transient( self::TRS_USER_HASH . trim( sanitize_text_field( $_GET["key"] ) ) );
			if ( $decodedEmail ) {
				$this->dbManager->unfollowByEmail( $decodedEmail );
			}

			$wpDiscuzSubscriptionMessage = $this->options->getPhrase( "wc_cancel_follows_success" );
		} else if ( $action === "follow" ) {
			if ( isset( $_GET["wpdiscuzFollowID"] ) && isset( $_GET["wpdiscuzFollowKey"] ) && isset( $_GET["wpDiscuzComfirm"] ) ) {
				if ( $_GET["wpDiscuzComfirm"] ) {
					$this->dbManager->confirmFollow( sanitize_text_field( $_GET["wpdiscuzFollowID"] ), sanitize_text_field( $_GET["wpdiscuzFollowKey"] ) );
					$wpDiscuzSubscriptionMessage = $this->options->getPhrase( "wc_follow_confirm_success" );
				} else {
					$this->dbManager->cancelFollow( sanitize_text_field( $_GET["wpdiscuzFollowID"] ), sanitize_text_field( $_GET["wpdiscuzFollowKey"] ) );
					$wpDiscuzSubscriptionMessage = $this->options->getPhrase( "wc_follow_cancel_success" );
				}
			}
		} else if ( $action === "bulkmanagement" ) {
			$wpDiscuzSubscriptionMessage = esc_html__( "Something is wrong.", "wpdiscuz" );
			if ( $this->emailDeleteLinks() ) {
				$wpDiscuzSubscriptionMessage = esc_html__( "Email sent successfully.", "wpdiscuz" );
			}
		} else {
			return $template;
		}

		return apply_filters( "wpdiscuz_subscription_template_path", WPDISCUZ_DIR_PATH . "/themes/unsubscription.php", $wpDiscuzSubscriptionMessage );
	}

	private function sendBulkManagementEmail() {
		$this->helper->validateNonce();
		$currentUser = WpdiscuzHelper::getCurrentUser();
		if ( $currentUser->exists() ) {
			$currentUserEmail = $currentUser->user_email;

			if ( $currentUserEmail ) {
				$siteUrl           = site_url();
				$blogTitle         = html_entity_decode( get_option( "blogname" ), ENT_QUOTES );
				$hashValue         = $this->generateUserActionHash( $currentUserEmail );
				$deleteCommentsUrl = $siteUrl . "/wpdiscuzsubscription/deletecomments/?key=$hashValue";
				$unsubscribeUrl    = $siteUrl . "/wpdiscuzsubscription/deletesubscriptions/?key=$hashValue";
				$unfollowUrl       = $siteUrl . "/wpdiscuzsubscription/deletefollows/?key=$hashValue";

				$subject = $this->options->getPhrase( "wc_user_settings_delete_links" );

				$message = str_replace( [ "[SITE_URL]", "[BLOG_TITLE]", "[DELETE_COMMENTS_URL]" ], [
					$siteUrl,
					$blogTitle,
					$deleteCommentsUrl
				], $this->options->getPhrase( "wc_user_settings_delete_all_comments_message" ) );

				$message .= $this->options->getPhrase( "wc_user_settings_delete_all_subscriptions_message" );

				if ( strpos( $message, "[DELETE_SUBSCRIPTIONS_URL]" ) !== false ) {
					$message = str_replace( "[DELETE_SUBSCRIPTIONS_URL]", $unsubscribeUrl, $message );
				}

				$message .= $this->options->getPhrase( "wc_user_settings_delete_all_follows_message" );

				if ( strpos( $message, "[DELETE_FOLLOWS_URL]" ) !== false ) {
					$message = str_replace( "[DELETE_FOLLOWS_URL]", $unfollowUrl, $message );
				}

				$this->userActionMail( $currentUserEmail, $subject, $message );
			}
		}
	}

	public function emailDeleteLinksAction() {
		$this->emailDeleteLinks();
		wp_die();
	}

	private function emailDeleteLinks() {
		$this->helper->validateNonce();
		$currentUser      = WpdiscuzHelper::getCurrentUser();
		$currentUserEmail = "";
		$isGuest          = true;

		if ( $currentUser->exists() ) {
			$currentUserEmail = $currentUser->user_email;
			$isGuest          = false;
		} else {
			$currentUserEmail = isset( $_COOKIE[ "comment_author_email_" . COOKIEHASH ] ) ? $_COOKIE[ "comment_author_email_" . COOKIEHASH ] : "";
		}


		if ( $currentUserEmail ) {
			$siteUrl           = site_url();
			$blogTitle         = html_entity_decode( get_option( "blogname" ), ENT_QUOTES );
			$hashValue         = $this->generateUserActionHash( $currentUserEmail );
			$deleteCommentsUrl = $siteUrl . "/wpdiscuzsubscription/deletecomments/?key=$hashValue";
			$unsubscribeUrl    = $siteUrl . "/wpdiscuzsubscription/deletesubscriptions/?key=$hashValue";
			$unfollowUrl       = $siteUrl . "/wpdiscuzsubscription/deletefollows/?key=$hashValue";

			$subject = $this->options->getPhrase( "wc_user_settings_delete_links" );

			$message = str_replace( [ "[SITE_URL]", "[BLOG_TITLE]", "[DELETE_COMMENTS_URL]" ], [
				$siteUrl,
				$blogTitle,
				$deleteCommentsUrl
			], $this->options->getPhrase( "wc_user_settings_delete_all_comments_message" ) );

			$message .= $this->options->getPhrase( "wc_user_settings_delete_all_subscriptions_message" );

			if ( strpos( $message, "[DELETE_SUBSCRIPTIONS_URL]" ) !== false ) {
				$message = str_replace( "[DELETE_SUBSCRIPTIONS_URL]", $unsubscribeUrl, $message );
			}

			if ( ! $isGuest ) {
				$message .= $this->options->getPhrase( "wc_user_settings_delete_all_follows_message" );
			}

			if ( strpos( $message, "[DELETE_FOLLOWS_URL]" ) !== false ) {
				$message = str_replace( "[DELETE_FOLLOWS_URL]", $unfollowUrl, $message );
			}

			return $this->userActionMail( $currentUserEmail, $subject, $message );
		}

		return false;
	}

	public function userActionMail( $email, $subject, $message ) {
		$siteUrl   = get_site_url();
		$blogTitle = get_option( "blogname" );
		$fromName  = html_entity_decode( $blogTitle, ENT_QUOTES );
		$parsedUrl = parse_url( $siteUrl );
		$domain    = isset( $parsedUrl["host"] ) ? WpdiscuzHelper::fixEmailFrom( $parsedUrl["host"] ) : "";
		$fromEmail = "no-reply@" . $domain;
		$headers[] = "Content-Type: text/html; charset=UTF-8";
		$headers[] = "From: " . $fromName . " <" . $fromEmail . "> \r\n";
		$subject   = html_entity_decode( $subject, ENT_QUOTES );
		$message   = html_entity_decode( $message, ENT_QUOTES );

		return wp_mail( $email, $subject, do_shortcode( $message ), $headers );
	}

	public function generateUserActionHash( $email ) {
		$hashedEmail = hash_hmac( "sha256", $email, get_option( self::OPTION_SLUG_HASH_KEY ) );
		$hashKey     = self::TRS_USER_HASH . $hashedEmail;
		$hashExpire  = apply_filters( "wpdiscuz_delete_all_content", 3 * DAY_IN_SECONDS );
		set_transient( $hashKey, $email, $hashExpire );

		return $hashedEmail;
	}

	public function addSubscription() {
		$success                      = 0;
		$currentUser                  = WpdiscuzHelper::getCurrentUser();
		$subscribeFormNonce           = WpdiscuzHelper::sanitize( INPUT_POST, "wpdiscuz_subscribe_form_nonce", "FILTER_SANITIZE_STRING" );
		$subscriptionType             = WpdiscuzHelper::sanitize( INPUT_POST, "wpdiscuzSubscriptionType", "FILTER_SANITIZE_STRING" );
		$postId                       = WpdiscuzHelper::sanitize( INPUT_POST, "postId", FILTER_SANITIZE_NUMBER_INT );
		$showSubscriptionBarAgreement = WpdiscuzHelper::sanitize( INPUT_POST, "show_subscription_agreement", FILTER_SANITIZE_NUMBER_INT );
		$form                         = wpDiscuz()->wpdiscuzForm->getForm( $postId );
		if ( $currentUser && $currentUser->ID ) {
			$email = $currentUser->user_email;
		} else {
			$email = WpdiscuzHelper::sanitize( INPUT_POST, "wpdiscuzSubscriptionEmail", "FILTER_SANITIZE_STRING" );
		}
		if ( ! $currentUser->exists() && $form->isShowSubscriptionBarAgreement() && ! $showSubscriptionBarAgreement && ( $subscriptionType === WpdiscuzCore::SUBSCRIPTION_POST || $subscriptionType === WpdiscuzCore::SUBSCRIPTION_ALL_COMMENT ) ) {
			$email = "";
		}
		$addSubscription = apply_filters( "wpdiscuz_before_subscription_added", true );
		if ( $addSubscription && wp_verify_nonce( $subscribeFormNonce, "wpdiscuz_subscribe_form_nonce_action" ) && $email && filter_var( $email, FILTER_VALIDATE_EMAIL ) !== false && in_array( $subscriptionType, [
				self::SUBSCRIPTION_POST,
				self::SUBSCRIPTION_ALL_COMMENT
			] ) && $postId ) {
			$noNeedMemberConfirm = ( $currentUser->ID && ! $this->options->subscription["enableMemberConfirm"] );
			$noNeedGuestsConfirm = ( ! $currentUser->ID && ! $this->options->subscription["enableGuestsConfirm"] );
			if ( $noNeedMemberConfirm || $noNeedGuestsConfirm ) {
				$confirmData = $this->dbManager->addEmailNotification( $postId, $postId, $email, $subscriptionType, 1 );
				if ( $confirmData ) {
					$success = 1;
				}
			} else {
				$confirmData = $this->dbManager->hasSubscription( $postId, $email );
				if ( $confirmData && ! intval( $confirmData["confirm"] ) ) {
					$success = $this->confirmEmailSender( $confirmData["id"], $confirmData["activation_key"], $postId, $email ) ? 1 : - 1;
				} else {
					$confirmData = $this->dbManager->addEmailNotification( $postId, $postId, $email, $subscriptionType, 0 );
					if ( $confirmData ) {
						$success = $this->confirmEmailSender( $confirmData["id"], $confirmData["activation_key"], $postId, $email ) ? 1 : - 1;
						if ( $success < 0 ) {
							$this->dbManager->unsubscribe( $confirmData["id"], $confirmData["activation_key"] );
						}
					}
				}
			}
		}
		if ( $success == - 1 ) {
			wp_send_json_error( esc_html( $this->options->getPhrase( "wc_unable_sent_email" ) ) );
		} else if ( $success == 0 ) {
			wp_send_json_error( esc_html( $this->options->getPhrase( "wc_subscription_fault" ) ) );
		} else {
			$noNeedMemberConfirm = ( $currentUser->ID && ! $this->options->subscription["enableMemberConfirm"] );
			$noNeedGuestsConfirm = ( ! $currentUser->ID && ! $this->options->subscription["enableGuestsConfirm"] );
			if ( $noNeedMemberConfirm || $noNeedGuestsConfirm ) {
				wp_send_json_success( esc_html( $this->options->getPhrase( "wc_subscribe_message" ) ) );
			} else {
				wp_send_json_success( esc_html( $this->options->getPhrase( "wc_confirm_email" ) ) );
			}
		}
	}

	public function confirmEmailSender( $id, $activationKey, $postId, $email ) {
		$confirm_url     = $this->dbManager->confirmLink( $id, $activationKey, $postId );
		$unsubscribe_url = $this->dbManager->unsubscribeLink( $postId, $email );
		$siteUrl         = get_site_url();
		$blogTitle       = get_option( "blogname" );
		$postTitle       = get_the_title( $postId );

		$search  = [ "[SITE_URL]", "[POST_URL]", "[BLOG_TITLE]", "[POST_TITLE]" ];
		$replace = [ $siteUrl, get_permalink( $postId ), $blogTitle, $postTitle ];

		$subject = $this->options->subscription["emailSubjectSubscriptionConfirmation"];
		$message = wpautop( $this->options->subscription["emailContentSubscriptionConfirmation"] );

		$subject = apply_filters( "wpdiscuz_confirm_email_subject_pre_replace", $subject, $postId, $email );
		$message = apply_filters( "wpdiscuz_confirm_email_content_pre_replace", $message, $postId, $email );

		$subject = str_replace( [ "[BLOG_TITLE]", "[POST_TITLE]" ], [ $blogTitle, $postTitle ], $subject );
		$message = str_replace( $search, $replace, $message );

		if ( strpos( $message, "[CONFIRM_URL]" ) === false ) {
			$message .= "<br/><br/><a href='$confirm_url'>" . $this->options->getPhrase( "wc_confirm_email" ) . "</a>";
		} else {
			$message = str_replace( "[CONFIRM_URL]", $confirm_url, $message );
		}

		if ( strpos( $message, "[CANCEL_URL]" ) === false ) {
			$message .= "<br/><br/><a href='$unsubscribe_url'>" . $this->options->getPhrase( "wc_ignore_subscription" ) . "</a>";
		} else {
			$message = str_replace( "[CANCEL_URL]", $unsubscribe_url, $message );
		}

		$subject = apply_filters( "wpdiscuz_confirm_email_subject", $subject, $postId, $email );
		$message = apply_filters( "wpdiscuz_confirm_email_content", $message, $postId, $email );

		$headers   = [];
		$fromName  = html_entity_decode( $blogTitle, ENT_QUOTES );
		$parsedUrl = parse_url( $siteUrl );
		$domain    = isset( $parsedUrl["host"] ) ? WpdiscuzHelper::fixEmailFrom( $parsedUrl["host"] ) : "";
		$fromEmail = "no-reply@" . $domain;
		$headers[] = "Content-Type: text/html; charset=UTF-8";
		$headers[] = "From: " . $fromName . " <" . $fromEmail . "> \r\n";
		$subject   = html_entity_decode( $subject, ENT_QUOTES );
		$message   = html_entity_decode( $message, ENT_QUOTES );

		return wp_mail( $email, $subject, do_shortcode( $message ), $headers );
	}

	/**
	 * send email
	 */
	public function emailSender( $emailData, $commentId, $subject, $message, $subscriptionType ) {
		global $wp_rewrite;
		$comment    = get_comment( $commentId );
		$post       = get_post( $comment->comment_post_ID );
		$postAuthor = get_userdata( $post->post_author );

		if ( ! apply_filters( "wpdiscuz_email_notification", true, $emailData, $comment, $subscriptionType ) ) {
			return;
		}

		if ( $emailData["email"] === $postAuthor->user_email && ( ( get_option( "moderation_notify" ) && $comment->comment_approved !== "1" ) || ( get_option( "comments_notify" ) && $comment->comment_approved === "1" ) ) ) {
			return;
		}

		$unsubscribeUrl = site_url( '/wpdiscuzsubscription/unsubscribe/' );
		$unsubscribeUrl .= "?wpdiscuzSubscribeID=" . $emailData["id"] . "&key=" . $emailData["activation_key"];

		$siteUrl   = get_site_url();
		$blogTitle = get_option( "blogname" );
		$postTitle = get_the_title( $comment->comment_post_ID );
		if ( $subscriptionType === self::SUBSCRIPTION_COMMENT ) {
			$parentComment = get_comment( $comment->comment_parent );
			$subscriber    = $parentComment && $parentComment->comment_author ? $parentComment->comment_author : $this->options->getPhrase( "wc_anonymous" );
		} else {
			$user       = get_user_by( "email", $emailData["email"] );
			$subscriber = $user && $user->display_name ? $user->display_name : "";
		}

		$subject = apply_filters( "wpdiscuz_email_subject_pre_replace", $subject, $comment, $emailData );
		$message = apply_filters( "wpdiscuz_email_content_pre_replace", $message, $comment, $emailData );

		$commentAuthor = $comment->comment_author ? $comment->comment_author : $this->options->getPhrase( "wc_anonymous" );
		$search        = [
			"[SITE_URL]",
			"[POST_URL]",
			"[BLOG_TITLE]",
			"[POST_TITLE]",
			"[SUBSCRIBER_NAME]",
			"[COMMENT_URL]",
			"[COMMENT_AUTHOR]",
			"[COMMENT_CONTENT]"
		];
		$replace       = [
			$siteUrl,
			urldecode_deep( get_permalink( $comment->comment_post_ID ) ),
			$blogTitle,
			$postTitle,
			$subscriber,
			urldecode_deep( get_comment_link( $commentId ) ),
			$commentAuthor,
			wpautop( $comment->comment_content )
		];

		$subject = str_replace( [ "[BLOG_TITLE]", "[POST_TITLE]", "[COMMENT_AUTHOR]" ], [
			$blogTitle,
			$postTitle,
			$commentAuthor
		], $subject );
		$message = str_replace( $search, $replace, $message );


		if ( strpos( $message, "[UNSUBSCRIBE_URL]" ) === false ) {
			$message .= "<br/><br/><a href='$unsubscribeUrl'>" . $this->options->getPhrase( "wc_unsubscribe" ) . "</a>";
		} else {
			$message = str_replace( "[UNSUBSCRIBE_URL]", $unsubscribeUrl, $message );
		}

		$subject = apply_filters( "wpdiscuz_email_subject", $subject, $comment, $emailData );
		$message = apply_filters( "wpdiscuz_email_content", $message, $comment, $emailData );

		$headers   = [];
		$fromName  = html_entity_decode( $blogTitle, ENT_QUOTES );
		$parsedUrl = parse_url( $siteUrl );
		$domain    = isset( $parsedUrl["host"] ) ? WpdiscuzHelper::fixEmailFrom( $parsedUrl["host"] ) : "";
		$fromEmail = "no-reply@" . $domain;
		$headers[] = "Content-Type: text/html; charset=UTF-8";
		$headers[] = "From: " . $fromName . " <" . $fromEmail . "> \r\n";
		$subject   = html_entity_decode( $subject, ENT_QUOTES );
		$message   = html_entity_decode( $message, ENT_QUOTES );
		wp_mail( $emailData["email"], $subject, do_shortcode( $message ), $headers );
	}

	/**
	 * Check notification type and send email to post new comments subscribers
	 */
	public function checkNotificationType() {
		$postId = WpdiscuzHelper::sanitize( INPUT_POST, "postId", FILTER_SANITIZE_NUMBER_INT, 0 );;
		$commentId   = WpdiscuzHelper::sanitize( INPUT_POST, "comment_id", FILTER_SANITIZE_NUMBER_INT, 0 );
		$email       = isset( $_POST["email"] ) ? sanitize_email( trim( $_POST["email"] ) ) : "";
		$isParent    = WpdiscuzHelper::sanitize( INPUT_POST, "isParent", "FILTER_SANITIZE_STRING" );
		$currentUser = WpdiscuzHelper::getCurrentUser();
		if ( $currentUser && $currentUser->user_email ) {
			$email = $currentUser->user_email;
		}
		if ( $commentId && $email && $postId && ( $comment = get_comment( $commentId ) ) ) {
			if ( apply_filters( "wpdiscuz_enable_user_mentioning", $this->options->subscription["enableUserMentioning"] ) && $this->options->subscription["sendMailToMentionedUsers"] && ( $mentionedUsers = $this->helper->getMentionedUsers( $comment->comment_content ) ) ) {
				$this->sendMailToMentionedUsers( $mentionedUsers, $comment );
			}
			do_action( "wpdiscuz_before_sending_emails", $commentId, $comment );
			$this->notifyPostSubscribers( $postId, $commentId, $email );
			$this->notifyFollowers( $postId, $commentId, $email );
			if ( ! $isParent ) {
				$parentCommentId    = $comment->comment_parent;
				$parentComment      = get_comment( $parentCommentId );
				$parentCommentEmail = $parentComment->comment_author_email;
				$this->notifyAllCommentSubscribers( $postId, $commentId, $email );
				if ( $parentCommentEmail !== $email ) {
					$this->notifyCommentSubscribers( $parentCommentId, $comment->comment_ID, $email );
				}
			}
		}
		wp_die();
	}

	/**
	 * Send notifications for new comments on the post (including replies)
	 *
	 * @param $postId      int
	 * @param $commentId   int
	 * @param $email       string
	 */
	public function notifyPostSubscribers( $postId, $commentId, $email ) {
		$emailsArray = $this->dbManager->getPostNewCommentNotification( $postId, $email );
		$subject     = $this->options->subscription["emailSubjectPostComment"];
		$message     = wpautop( $this->options->subscription["emailContentPostComment"] );

		if ( ! $emailsArray || ! is_array( $emailsArray ) ) {
			return;
		}

		foreach ( $emailsArray as $k => $eRow ) {
			$subscriberUserId = $eRow["id"];
			$subscriberEmail  = $eRow["email"];
			$this->emailSender( $eRow, $commentId, $subject, $message, self::SUBSCRIPTION_POST );
			do_action( "wpdiscuz_notify_post_subscribers", $postId, $commentId, $subscriberUserId, $subscriberEmail );
		}
	}

	/**
	 * Send notifications for new comments on the post (including replies)
	 *
	 * @param $postId           int
	 * @param $newCommentId     int
	 * @param $email            string
	 */
	public function notifyAllCommentSubscribers( $postId, $newCommentId, $email ) {
		$emailsArray = $this->dbManager->getAllNewCommentNotification( $postId, $email );
		$subject     = $this->options->subscription["emailSubjectAllCommentReply"];
		$message     = wpautop( $this->options->subscription["emailContentAllCommentReply"] );

		if ( ! $emailsArray || ! is_array( $emailsArray ) ) {
			return;
		}

		$helperOptimization = wpDiscuzHelperOptimization();

		foreach ( $emailsArray as $k => $eRow ) {
			$subscriberUserId = $eRow["id"];
			$subscriberEmail  = $eRow["email"];

			$args = [
				"post_id"      => $postId,
				"status"       => "approve",
				"author_email" => $subscriberEmail,
				"fields"       => "ids",
			];

			/**
			 * @var $subscriberComments WP_Comment[]
			 */
			$subscriberComments = get_comments( $args );

			if ( ! $subscriberComments || ! is_array( $subscriberComments ) ) {
				continue;
			}

			$tree       = [];
			$tree       = $helperOptimization->getCommentParentsTree( $newCommentId, $tree );
			$tree       = array_diff( $tree, [ $newCommentId ] );
			$hasReplies = array_intersect( $subscriberComments, $tree );

			if ( empty( $hasReplies ) ) {
				continue;
			}

			$this->emailSender( $eRow, $newCommentId, $subject, $message, self::SUBSCRIPTION_ALL_COMMENT );
			do_action( "wpdiscuz_notify_all_comment_subscribers", $postId, $newCommentId, $subscriberUserId, $subscriberEmail );
		}
	}

	/**
	 * Send notifications for new replies to an individual comment
	 * (includes all replies)
	 *
	 * @param $parentCommentId    int
	 * @param $newCommentId       int
	 * @param $email              string  email address to exclude (the comment author email)
	 */
	public function notifyCommentSubscribers( $parentCommentId, $newCommentId, $email ) {
		$emailsArray = $this->dbManager->getNewReplyNotification( $parentCommentId, $email );
		$subject     = $this->options->subscription["emailSubjectCommentReply"];
		$message     = wpautop( $this->options->subscription["emailContentCommentReply"] );

		if ( ! $emailsArray || ! is_array( $emailsArray ) ) {
			return;
		}

		foreach ( $emailsArray as $k => $eRow ) {
			$subscriberUserId = $eRow["id"];
			$subscriberEmail  = $eRow["email"];
			$this->emailSender( $eRow, $newCommentId, $subject, $message, self::SUBSCRIPTION_COMMENT );
			do_action( "wpdiscuz_notify_comment_subscribers", $parentCommentId, $newCommentId, $subscriberUserId, $subscriberEmail );
		}
	}

	/**
	 * When a comment is approved from the admin comments.php or posts.php... notify the subscribers
	 *
	 * @param $commentId       int
	 * @param $approved        bool
	 */
	public function notificationFromDashboard( $commentId, $approved ) {
		$wpdiscuz         = wpDiscuz();
		$referer          = isset( $_SERVER["HTTP_REFERER"] ) ? $_SERVER["HTTP_REFERER"] : "";
		$comment          = get_comment( $commentId );
		$commentsPage     = strpos( $referer, "edit-comments.php" ) !== false;
		$postCommentsPage = ( strpos( $referer, "post.php" ) !== false ) && ( strpos( $referer, "action=edit" ) !== false );
		$isLoadWpdiscuz   = false;
		$post             = get_post( $comment->comment_post_ID );
		if ( $post && is_object( $post ) ) {
			$form           = $wpdiscuz->wpdiscuzForm->getForm( $post->ID );
			$isLoadWpdiscuz = $form->getFormID() && ( comments_open( $post ) || $post->comment_count ) && post_type_supports( $post->post_type, "comments" );
		}
		if ( $approved == 1 && ( $commentsPage || $postCommentsPage ) && $comment && $isLoadWpdiscuz ) {
			$postId        = $comment->comment_post_ID;
			$email         = $comment->comment_author_email;
			$parentComment = $comment->comment_parent ? get_comment( $comment->comment_parent ) : 0;
			if ( apply_filters( "wpdiscuz_enable_user_mentioning", $this->options->subscription["enableUserMentioning"] ) && $this->options->subscription["sendMailToMentionedUsers"] && ( $mentionedUsers = $this->helper->getMentionedUsers( $comment->comment_content ) ) ) {
				$this->sendMailToMentionedUsers( $mentionedUsers, $comment );
			}
			do_action( "wpdiscuz_before_sending_emails", $commentId, $comment );
			$this->notifyPostSubscribers( $postId, $commentId, $email );
			if ( $parentComment ) {
				$parentCommentEmail = $parentComment->comment_author_email;
				$this->notifyAllCommentSubscribers( $postId, $commentId, $email );
				if ( $parentCommentEmail !== $email ) {
					$this->notifyCommentSubscribers( $parentComment->comment_ID, $commentId, $email );
				}
			}
		}
	}

	/**
	 * When a comment is approved (after being held for moderation)... notify the author
	 *
	 * @param $comment  WP_Comment
	 */
	public function notifyOnApproving( $comment ) {
		if ( $comment ) {
			$wpdiscuz       = wpDiscuz();
			$isLoadWpdiscuz = false;
			$post           = get_post( $comment->comment_post_ID );
			$postId         = $comment->comment_post_ID;
			if ( $post && is_object( $post ) ) {
				$postId         = $post->ID;
				$form           = $wpdiscuz->wpdiscuzForm->getForm( $post->ID );
				$isLoadWpdiscuz = $form->getFormID() && ( comments_open( $post ) || $post->comment_count ) && post_type_supports( $post->post_type, "comments" );
			}
			if ( $isLoadWpdiscuz ) {
				$user = $comment->user_id ? get_userdata( $comment->user_id ) : null;
				if ( $user ) {
					$email = $user->user_email;
				} else {
					$email = $comment->comment_author_email;
				}
				if ( apply_filters( "wpdiscuz_send_email_on_approving", true, $email, $comment ) ) {

					$siteUrl   = get_site_url();
					$blogTitle = get_option( "blogname" );
					$postTitle = get_the_title( $comment->comment_post_ID );
					$search    = [
						"[SITE_URL]",
						"[POST_URL]",
						"[BLOG_TITLE]",
						"[POST_TITLE]",
						"[COMMENT_URL]",
						"[COMMENT_AUTHOR]",
						"[COMMENT_CONTENT]"
					];
					$replace   = [
						$siteUrl,
						urldecode_deep( get_permalink( $comment->comment_post_ID ) ),
						$blogTitle,
						$postTitle,
						urldecode_deep( get_comment_link( $comment->comment_ID ) ),
						$comment->comment_author,
						wpautop( $comment->comment_content )
					];

					$subject = $this->options->subscription["emailSubjectCommentApproved"];
					$message = wpautop( $this->options->subscription["emailContentCommentApproved"] );

					$subject = apply_filters( "wpdiscuz_comment_approved_email_subject_pre_replace", $subject, $postId, $email );
					$message = apply_filters( "wpdiscuz_comment_approved_email_content_pre_replace", $message, $postId, $email );

					$subject = str_replace( [ "[BLOG_TITLE]", "[POST_TITLE]", "[COMMENT_AUTHOR]" ], [
						$blogTitle,
						$postTitle,
						$comment->comment_author
					], $subject );
					$message = str_replace( $search, $replace, $message );

					$subject = apply_filters( "wpdiscuz_comment_approved_email_subject", $subject, $postId, $email );
					$message = apply_filters( "wpdiscuz_comment_approved_email_content", $message, $postId, $email );

					$headers   = [];
					$fromName  = html_entity_decode( $blogTitle, ENT_QUOTES );
					$parsedUrl = parse_url( $siteUrl );
					$domain    = isset( $parsedUrl["host"] ) ? WpdiscuzHelper::fixEmailFrom( $parsedUrl["host"] ) : "";
					$fromEmail = "no-reply@" . $domain;
					$headers[] = "Content-Type: text/html; charset=UTF-8";
					$headers[] = "From: " . $fromName . " <" . $fromEmail . "> \r\n";
					$subject   = html_entity_decode( $subject, ENT_QUOTES );
					$message   = html_entity_decode( $message, ENT_QUOTES );
					wp_mail( $email, $subject, do_shortcode( $message ), $headers );
				}
			}
		}
	}

	public function followConfirmEmail( $postId, $id, $key, $email ) {
		$confirmUrl = $this->dbManager->followConfirmLink( $id, $key );
		$cancelUrl  = $this->dbManager->followCancelLink( $id, $key );
		$siteUrl    = get_site_url();
		$blogTitle  = get_option( "blogname" );
		$postTitle  = get_the_title( $postId );
		$search     = [ "[SITE_URL]", "[POST_URL]", "[BLOG_TITLE]", "[POST_TITLE]" ];
		$replace    = [ $siteUrl, urldecode_deep( get_permalink( $postId ) ), $blogTitle, $postTitle ];

		$subject = $this->options->subscription["emailSubjectFollowConfirmation"];
		$message = wpautop( $this->options->subscription["emailContentFollowConfirmation"] );

		$subject = apply_filters( "wpdiscuz_follow_confirm_email_subject_pre_replace", $subject, $postId, $email );
		$message = apply_filters( "wpdiscuz_follow_confirm_email_content_pre_replace", $message, $postId, $email );

		$subject = str_replace( [ "[BLOG_TITLE]", "[POST_TITLE]" ], [ $blogTitle, $postTitle ], $subject );
		$message = str_replace( $search, $replace, $message );

		if ( strpos( $message, "[CONFIRM_URL]" ) === false ) {
			$message .= "<br/><br/><a href='$confirmUrl'>" . $this->options->getPhrase( "wc_follow_confirm" ) . "</a>";
		} else {
			$message = str_replace( "[CONFIRM_URL]", $confirmUrl, $message );
		}

		if ( strpos( $message, "[CANCEL_URL]" ) === false ) {
			$message .= "<br/><br/><a href='$cancelUrl'>" . $this->options->getPhrase( "wc_follow_cancel" ) . "</a>";
		} else {
			$message = str_replace( "[CANCEL_URL]", $cancelUrl, $message );
		}

		$subject = apply_filters( "wpdiscuz_follow_confirm_email_subject", $subject, $postId, $email );
		$message = apply_filters( "wpdiscuz_follow_confirm_email_content", $message, $postId, $email );

		$headers   = [];
		$fromName  = html_entity_decode( $blogTitle, ENT_QUOTES );
		$parsedUrl = parse_url( $siteUrl );
		$domain    = isset( $parsedUrl["host"] ) ? WpdiscuzHelper::fixEmailFrom( $parsedUrl["host"] ) : "";
		$fromEmail = "no-reply@" . $domain;
		$headers[] = "Content-Type: text/html; charset=UTF-8";
		$headers[] = "From: " . $fromName . " <" . $fromEmail . "> \r\n";
		$subject   = html_entity_decode( $subject, ENT_QUOTES );
		$message   = html_entity_decode( $message, ENT_QUOTES );

		return wp_mail( $email, $subject, do_shortcode( $message ), $headers );
	}

	public function notifyFollowers( $postId, $commentId, $email ) {
		$followersData    = $this->dbManager->getUserFollowers( $email );
		$comment          = get_comment( $commentId );
		$post             = get_post( $comment->comment_post_ID );
		$postAuthor       = get_userdata( $post->post_author );
		$moderationNotify = get_option( "moderation_notify" );
		$commentsNotify   = get_option( "comments_notify" );

		$siteUrl    = get_site_url();
		$blogTitle  = get_option( "blogname" );
		$postTitle  = get_the_title( $post );
		$postUrl    = urldecode_deep( get_permalink( $post ) );
		$commentUrl = urldecode_deep( get_comment_link( $comment ) );

		$subject = $this->options->subscription["emailSubjectFollowComment"];
		$message = wpautop( $this->options->subscription["emailContentFollowComment"] );

		$subject = apply_filters( "wpdiscuz_follow_email_subject_pre_replace", $subject, $postId, $email );
		$message = apply_filters( "wpdiscuz_follow_email_content_pre_replace", $message, $postId, $email );

		$search  = [ "[SITE_URL]", "[POST_URL]", "[BLOG_TITLE]", "[POST_TITLE]", "[COMMENT_URL]", "[COMMENT_CONTENT]" ];
		$replace = [ $siteUrl, $postUrl, $blogTitle, $postTitle, $commentUrl, wpautop( $comment->comment_content ) ];

		$subject = str_replace( [ "[BLOG_TITLE]", "[POST_TITLE]" ], [ $blogTitle, $postTitle ], $subject );
		$message = str_replace( $search, $replace, $message );

		$subject = apply_filters( "wpdiscuz_follow_email_subject", $subject, $postId, $email );
		$message = apply_filters( "wpdiscuz_follow_email_content", $message, $postId, $email );

		$fromName  = html_entity_decode( $blogTitle, ENT_QUOTES );
		$parsedUrl = parse_url( $siteUrl );
		$domain    = isset( $parsedUrl["host"] ) ? WpdiscuzHelper::fixEmailFrom( $parsedUrl["host"] ) : "";
		$fromEmail = "no-reply@" . $domain;
		$data      = [
			"site_url"     => $siteUrl,
			"blog_title"   => $blogTitle,
			"from_name"    => $fromName,
			"from_email"   => $fromEmail,
			"content_type" => "text/html",
		];

		foreach ( $followersData as $k => $followerData ) {
			if ( ( $followerData["follower_email"] === $postAuthor->user_email ) && ( ( $moderationNotify && $comment->comment_approved === "0" ) || ( $commentsNotify && $comment->comment_approved === "1" ) ) ) {
				return;
			}
			$subject = str_replace( [ "[COMMENT_AUTHOR]" ], [ $followerData["user_name"] ], $subject );
			$body    = str_replace( [ "[COMMENT_AUTHOR]", "[FOLLOWER_NAME]" ], [
				$followerData["user_name"],
				$followerData["follower_name"]
			], $message );
			$this->emailToFollower( $followerData, $comment, $subject, $body, $data );
			do_action( "wpdiscuz_notify_followers", $comment, $followerData );
		}
	}

	private function emailToFollower( $followerData, $comment, $subject, $message, $data ) {

		if ( ! apply_filters( "wpdiscuz_follow_email_notification", true, $followerData, $comment ) ) {
			return;
		}

		$cancelLink = site_url( "/wpdiscuzsubscription/follow/" ) . "?wpdiscuzFollowID={$followerData["id"]}&wpdiscuzFollowKey={$followerData["activation_key"]}&wpDiscuzComfirm=0";
		if ( strpos( $message, "[CANCEL_URL]" ) === false ) {
			$message .= "<br/><br/><a href='$cancelLink'>" . esc_html__( "Unfollow", "wpdiscuz" ) . "</a>";
		} else {
			$message = str_replace( "[CANCEL_URL]", $cancelLink, $message );
		}
		$headers         = [];
		$mailContentType = $data["content_type"];
		$headers[]       = "Content-Type:  $mailContentType; charset=UTF-8";
		$headers[]       = "From: " . $data["from_name"] . " <" . $data["from_email"] . "> \r\n";
		$subject         = html_entity_decode( $subject, ENT_QUOTES );
		$message         = html_entity_decode( $message, ENT_QUOTES );
		wp_mail( $followerData["follower_email"], $subject, do_shortcode( $message ), $headers );

	}

	public function sendMailToMentionedUsers( $users, $comment ) {
		$post   = get_post( $comment->comment_post_ID );
		$postId = $post->ID;

		$parsedUrl = parse_url( get_site_url() );
		$domain    = isset( $parsedUrl["host"] ) ? WpdiscuzHelper::fixEmailFrom( $parsedUrl["host"] ) : "";
		$fromEmail = "no-reply@" . $domain;
		$fromName  = html_entity_decode( get_option( "blogname" ), ENT_QUOTES );
		$headers[] = "Content-Type: text/html; charset=UTF-8";
		$headers[] = "From: " . $fromName . " <" . $fromEmail . "> \r\n";

		$siteUrl       = get_site_url();
		$blogTitle     = get_option( "blogname" );
		$postTitle     = get_the_title( $post );
		$postUrl       = urldecode_deep( get_permalink( $post ) );
		$commentUrl    = urldecode_deep( get_comment_link( $comment ) );
		$commentAuthor = $comment->comment_author;

		$subject = $this->options->subscription["emailSubjectUserMentioned"];
		$message = wpautop( $this->options->subscription["emailContentUserMentioned"] );

		$subject = apply_filters( "wpdiscuz_mentioned_user_email_subject_pre_replace", $subject, $postId );
		$message = apply_filters( "wpdiscuz_mentioned_user_email_content_pre_replace", $message, $postId );

		$subject = str_replace( [ "[BLOG_TITLE]", "[POST_TITLE]" ], [ $blogTitle, $postTitle ], $subject );
//$message = wpautop($this->options->subscription["emailContentUserMentioned"]);

		$search  = [
			"[SITE_URL]",
			"[POST_URL]",
			"[BLOG_TITLE]",
			"[POST_TITLE]",
			"[MENTIONED_USER_NAME]",
			"[COMMENT_URL]",
			"[COMMENT_AUTHOR]"
		];
		$replace = [ $siteUrl, $postUrl, $blogTitle, $postTitle, "", $commentUrl, $commentAuthor ];

		foreach ( $users as $k => $user ) {
			if ( $user["email"] !== $comment->comment_author_email ) {
				if ( apply_filters( "wpducm_mail_to_mentioned_user", true, $user, $comment ) ) {
					$replace[4] = $user["name"];
					$message    = str_replace( $search, $replace, $message );

					$subject = apply_filters( "wpdiscuz_mentioned_user_mail_subject", $subject, $user, $comment );
					$message = apply_filters( "wpdiscuz_mentioned_user_mail_body", $message, $user, $comment );

					$subject = apply_filters( "wpdiscuz_mentioned_user_email_subject", $subject, $postId );
					$message = apply_filters( "wpdiscuz_mentioned_user_email_content", $message, $postId );

					if ( $subject && $message ) {
						wp_mail( $user["email"], $subject, $message, $headers );
					}
				}
			}
		}
	}

}
