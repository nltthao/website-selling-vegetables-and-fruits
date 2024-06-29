<?php
if (!defined("ABSPATH")) {
    exit();
}
if (wp_is_block_theme()) {
    ?>
    <!doctype html>
    <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <?php wp_head(); ?>
        </head>
        <body <?php body_class(); ?>>
            <?php wp_body_open(); ?>
            <div class="wp-site-blocks">
                <header class="wp-block-template-part site-header">
                    <?php block_header_area(); ?>
                </header>
                <?php
            } else {
                get_header();
            }
            do_action("wpdiscuz_subscription_template_before");
            ?>
            <div style="margin: 0 auto; padding: 50px 0; max-width:800px" class="wpdc-unsubscription-main">
                <h2 class="wpdc-unsubscription-message">
                    <?php
                    global $wpDiscuzSubscriptionMessage;
                    $wpdiscuz = wpDiscuz();
                    esc_html_e($wpDiscuzSubscriptionMessage);
                    ?>
                </h2><br>
                <?php
                $currentUser = WpdiscuzHelper::getCurrentUser();
                $userEmail = isset($_COOKIE["comment_author_email_" . COOKIEHASH]) ? $_COOKIE["comment_author_email_" . COOKIEHASH] : "";
                if ($currentUser->exists()) {
                    $userEmail = $currentUser->user_email;
                }

                if ($userEmail) {
                    ?>
                    <div class="wpdc-unsubscription-bulk">
                        <a href="<?php echo site_url("/wpdiscuzsubscription/bulkmanagement/"); ?>"
                           class="wpdc-unsubscription-manage-link">
                               <?php esc_html_e($wpdiscuz->options->getPhrase("wc_user_settings_email_me_delete_links")) ?>
                        </a>( <?php esc_html_e($userEmail); ?> )
                        <div class="wpdc-unsubscription-manage-link-desc">
                            <?php esc_html_e($wpdiscuz->options->getPhrase("wc_user_settings_email_me_delete_links_desc")) ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php
            do_action("wpdiscuz_subscription_template_after");
            if (wp_is_block_theme()) {
                ?>
                <footer class="wp-block-template-part site-footer">
                    <?php block_footer_area(); ?>
                </footer>
            </div>
            <?php wp_footer(); ?>
        </body>
        <?php
    } else {
        get_footer();
    }
    ?>
