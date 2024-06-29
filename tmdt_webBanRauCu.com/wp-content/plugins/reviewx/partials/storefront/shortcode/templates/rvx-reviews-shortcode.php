<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="rxReviews" class="rxReviews">
    <?php
        global $reviewx_shortcode;
            $settings 				= \ReviewX\Controllers\Admin\Core\ReviewxMetaBox::get_option_settings();
            $allow_img_filter 		= get_option( '_rx_option_allow_img' );    
            $allow_video_filter     = get_option( '_rx_option_allow_video' );       
            $allow_share            = get_option( '_rx_option_allow_share_review' );
            $allow_like_dislike     = get_option( '_rx_option_allow_like_dislike' );   
            
            $filter_recent  		= get_option( '_rx_option_filter_recent' );   
            $filter_photo  		    = get_option( '_rx_option_filter_photo' ); 
            $filter_video  		    = get_option( '_rx_option_filter_video' );   
            $filter_text  		    = get_option( '_rx_option_filter_text' );   
            $filter_top_rated  		= get_option( '_rx_option_filter_rating' );   
            $filter_low_rated       = get_option( '_rx_option_filter_low_rating' );              

            $review_per_page  		= $settings->review_per_page;
            $review_style    	    = $settings->review_style;

    ?>  
    
    <div class="rx_review_sort_list">
        <div class="rx_listing_container_style_2">                       
        <?php 
        $reviewx_shortcode['rx_review_id']  = $atts['review_ids'];
        $ids = explode(",", $reviewx_shortcode['rx_review_id'] );
        $comments = [];

        foreach($ids as $id ) {
            $comments[] = get_comment( $id );
        }
        if( is_array( $comments) && count( $comments ) ) { ?>
            <!--- Start spinner -->
            <div class="rx_content_loader">
                <div class="rx_double_spinner">
                    <div></div>
                    <div>
                        <div></div>
                    </div>
                </div>
            </div>		
            <!--- End spinner -->	
            <ul id="rx-commentlist" class="rx_listing_style_2 rx_listing">
                <?php
                    foreach ($comments as $comment) {
                        $comment_gallery_meta   = get_comment_meta( $comment->comment_ID, 'reviewx_attachments', true);
                        $get_rating             = get_comment_meta( $comment->comment_ID, 'rating', true);
                        $verified_wc_review 	    = \ReviewX_Helper::wc_review_is_from_verified_owner ( $comment->comment_ID, $comment->comment_post_ID );
                        $comment_video_url      = get_comment_meta( $comment->comment_ID, 'reviewx_video_url', true );
                        $rx_highlight           = get_comment_meta( $comment->comment_ID, 'reviewx_highlight', true );
                        $get_review_title       = get_comment_meta( $comment->comment_ID, 'reviewx_title', true );
                        $comment_like           = get_comment_meta( $comment->comment_ID, 'comment_like', true ); 
                        $anonymouser            = get_comment_meta( $comment->comment_ID, 'reviewx_anonymouse_user', true );
                        $allow_customer_location = get_option('_rx_option_allow_location');
                        $country_code            = get_comment_meta( $comment->comment_ID, 'reviewx_location', true );
                        $country_flag 	        = \ReviewX_Helper::country_flag( $country_code );
                        $pagination_item 	    = '';
                        /**
                         * @var $reviewer_censored_name censored name
                         */
                        $reviewer_censored_name    = get_option('_rx_option_allow_reviewer_name_censor');
                        // skip child comment
                        ?>
                            <li class="review rx_review_block <?php echo esc_attr( $rx_highlight ); ?> <?php echo esc_attr( $pagination_item ); ?>">	
                                <div class="rx_flex rx_review_wrap">
                                    <div class="rx_author_info">
                                        <div class="rx_thumb"> 
                                            <?php 
                                                $comment_object 		= $anonymouser != 1 ? $comment : '';   //check anonymous, default gravatar for anonymous
                                                echo get_avatar( $comment_object, apply_filters( 'woocommerce_review_gravatar_size', '70' ), '' ); 									
                                            ?> 
                                        </div>
                                        <div class="rx_author_name">
                                            <h4>
                                                <?php
                                                $data               = array();
                                                $data['comment_id'] = $comment->comment_ID;
                                                $data['author']     = $comment->comment_author;
                                                apply_filters( 'print_reviewer_name', $data );
                                                ?>
                                            </h4>
                                            <?php if( $allow_customer_location == 1 ): ?>
                                            <div class="rx-country-code-flag">
                                                <div class="rx-country-flag"><?php echo $country_flag; ?></div>
                                                <div class="rx-country-code"><?php echo $country_code; ?></div>
                                            </div>
                                            <?php endif; ?>
                                        </div>								                 
                                    </div>
                                    <div class="rx_body">
                                        <div class="rx_flex rx_rating_section">
                                            <?php if ( '0' === $comment->comment_approved ) { ?>
                                                <div class="rx_approval_notice">
                                                    <em><svg width="18" height="18" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 1376v-160q0-14-9-23t-23-9h-96v-512q0-14-9-23t-23-9h-320q-14 0-23 9t-9 23v160q0 14 9 23t23 9h96v320h-96q-14 0-23 9t-9 23v160q0 14 9 23t23 9h448q14 0 23-9t9-23zm-128-896v-160q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v160q0 14 9 23t23 9h192q14 0 23-9t9-23zm640 416q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg> <?php esc_html_e('Your review is awaiting for approval', 'reviewx-pro' ); ?></em>
                                                </div>
                                            <?php } ?> 								
                                            <div class="review_rating">
                                                <?php echo reviewx_show_star_rating( $get_rating ); ?>  
                                            </div>
                                            
                                            <?php 
                                            if(class_exists('ReviewXPro')){
                                                if( current_user_can('manage_options') && check_comment_parent_id( $comment->comment_ID ) == 0 && get_current_user_id() != get_comment_author_id( $comment->comment_ID ) ) : ?> 
                                                    <div class="rx_admin_heighlights" data-review-id="<?php echo esc_attr( $comment->comment_ID ); ?>">
                                                        <?php 
                                                            if( ! empty( $rx_highlight ) ) {
                                                                ?>
                                                                <span>   
                                                                    <svg style="height: 13px; vertical-align: middle; color :#f75677 " aria-hidden="true" focusable="false" data-prefix="fas" data-icon="trash" class="svg-inline--fa fa-trash fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.72 23.72 0 0 0-21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0-16-16zM53.2 467a48 48 0 0 0 47.9 45h245.8a48 48 0 0 0 47.9-45L416 128H32z"></path></svg>
                                                                    <?php echo esc_html__( 'Remove', 'reviewx-pro' ); ?>
                                                                </span>
                                                                <?php                                            
                                                            } else {
                                                                ?>
                                                                <span>
                                                                    <svg style="height: 15px; vertical-align: middle" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="adjust" class="svg-inline--fa fa-adjust fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M8 256c0 136.966 111.033 248 248 248s248-111.034 248-248S392.966 8 256 8 8 119.033 8 256zm248 184V72c101.705 0 184 82.311 184 184 0 101.705-82.311 184-184 184z"></path></svg>
                                                                    <?php echo esc_html__( 'Highlight', 'reviewx-pro' ); ?>
                                                                </span>
                                                                <?php                                          
                                                            }
                                                        ?>                            
                                                    </div>                                                                          
                                                <?php endif; ?> 
                                            <?php } ?>
                                        </div>
            
                                        <?php
                                            if( ! empty( $get_review_title ) ) {
                                                ?>
                                                <h4 class="review_title"><?php echo html_entity_decode( $get_review_title ); ?></h4>
                                                <?php
                                            }
                                        ?>  
                                        <?php comment_text($comment); ?>                                        
                                        
                                        <div class="rx_flex rx_varified">                                            
                                            <div class="rx_review_calender">
                                                <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                     viewBox="-49 141 512 512" style="enable-background:new -49 141 512 512;" xml:space="preserve">
                                                    <g>
                                                        <path class="st0" d="M383,161h-10.8H357h-40h-91h-40H96H56h-7.8H31c-44.1,0-80,35.9-80,80v312c0,44.1,35.9,80,80,80h352
                                                        c42.1,0,76.7-32.7,79.8-74c0.1-1,0.2-2,0.2-3V241C463,196.9,427.1,161,383,161z M423,553c0,22.1-17.9,40-40,40H31
                                                        c-22.1,0-40-17.9-40-40V241c0-22.1,17.9-40,40-40h25v20c0,11,9,20,20,20s20-9,20-20v-20h90v20c0,11,9,20,20,20s20-9,20-20v-20h91
                                                        v20c0,11,9,20,20,20c11,0,20-9,20-20v-20h26c22.1,0,40,17.9,40,40V553z"/>
                                                        <circle class="st0" cx="76" cy="331" r="20"/>
                                                        <circle class="st0" cx="250" cy="331" r="20"/>
                                                        <circle class="st0" cx="337" cy="331" r="20"/>
                                                        <circle class="st0" cx="76" cy="418" r="20"/>
                                                        <circle class="st0" cx="76" cy="505" r="20"/>
                                                        <circle class="st0" cx="163" cy="331" r="20"/>
                                                        <circle class="st0" cx="163" cy="418" r="20"/>
                                                        <circle class="st0" cx="163" cy="505" r="20"/>
                                                        <circle class="st0" cx="250" cy="418" r="20"/>
                                                        <circle class="st0" cx="337" cy="418" r="20"/>
                                                        <circle class="st0" cx="250" cy="505" r="20"/>
                                                    </g>
                                                </svg>
                                                <span> <?php echo date_i18n(get_option('date_format'), strtotime($comment->comment_date)); ?></span>
                                            </div>  
            
                                            <?php if( $verified_wc_review ) { ?>
                                            <div class="rx_varified_user">
                                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                     viewBox="0 0 80 80" style="enable-background:new 0 0 80 80;" xml:space="preserve">
                                                    <style type="text/css">
                                                        .st1{fill:#FFFFFF;}
                                                    </style>
                                                        <circle class="st0" cx="40" cy="40" r="40"/>
                                                        <path class="st1" d="M20.4,41.1c0-0.3,0.1-0.7,0.3-1c0.5-1.1,1.5-1.9,2.5-2.7c1.2-1,2.7-0.7,3.9,0.4c2.1,2.1,4.2,4.2,6.3,6.3
                                                    c0.2,0.2,0.3,0.4,0.5,0.6c0.3-0.3,0.5-0.5,0.7-0.7c6-6,11.9-11.9,17.9-17.9c1.7-1.7,3.3-1.7,4.9,0c0.7,0.8,1.3,1.6,1.9,2.5
                                                    c0.8,1.2,0.1,1.8-1.6,3.5c-7.3,7.4-14.7,14.7-22,22c-1.3,1.3-2.3,1.3-3.6,0c-3.4-3.4-6.9-6.8-10.2-10.3
                                                    C20.5,42.4,20.3,41.7,20.4,41.1z"/>
                                                </svg>
                                                <span><?php esc_html_e( 'Verified Review', 'reviewx-pro' ); ?></span>
                                            </div>                                                   							
                                            <?php } ?>                                          
                                        </div>							
                                        <?php 
                                            if( !empty( $comment_gallery_meta ) || ! empty( $comment_video_url ) ) {	
                                                ?>
                                                <div class="rx_flex rx_photos" <?php echo ($review_style == "review_style_one") ? 'style="justify-content: flex-end"' : ' '; ?>>
                                                <?php
                                                if(class_exists('ReviewXPro')){
                                                    if( ! empty( $comment_video_url ) ) {
                                                        $video_details = determine_video_url_type( $comment_video_url );  
                                                        ?>
                                                        <div class="rx_photo rx_video">
                                                            <a class="rx-popup-video" href="<?php echo esc_url( $comment_video_url ); ?>">
            
                                                                <?php if( $video_details['video_type'] == 'youtube' && $video_details['video_id'] != '' ) { ?>
                                                                
                                                                    <img src="<?php echo esc_url( sprintf('https://img.youtube.com/vi/%s/default.jpg', $video_details['video_id'] ) ); ?>" class="img-fluid" style="margin: 0 auto" alt="<?php esc_attr_e( 'ReviewX', 'reviewx-pro' ); ?>">
            
                                                                <?php } else if( $video_details['video_type'] == 'vimeo' && $video_details['video_id'] != '' ) { 
                                                                $vimeo_thumbnail = get_vimeo_video_thumb( $video_details['video_id'] );
                                                                $vimeo_thumbnail = preg_replace("/^http:/i", "https:", $vimeo_thumbnail);
                                                                ?>    
                                                                
                                                                <img src="<?php echo esc_url( $vimeo_thumbnail ); ?>" class="img-fluid" style="margin: 0 auto" alt="<?php esc_attr_e( 'ReviewX', 'reviewx-pro' ); ?>">
                                                                
                                                                <?php } else { ?>
                                                                    <img src="<?php echo esc_url( plugins_url( '/', __FILE__ ) . '../../../assets/images/video-icon.png' ); ?>" class="img-fluid" style="margin: 0 auto" alt="<?php esc_attr_e( 'ReviewX', 'reviewx-pro' ); ?>">
                                                                
                                                                <?php } ?> 													
                                                                
                                                                <div class="rx_overlay">
                                                                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                                            viewBox="0 0 100 100" style="enable-background:new 0 0 100 100;" xml:space="preserve">
                                                                        <style type="text/css">
                                                                            .st0{fill:#9BA1B0;}
                                                                        </style>
                                                                                        <path class="st0" d="M21.2,2.2C12.2-3,4.9,1.3,4.9,11.7v76.7c0,10.4,7.3,14.6,16.3,9.5l67-38.4c9-5.2,9-13.6,0-18.7L21.2,2.2z
                                                                                M21.2,2.2"/>
                                                                    </svg>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <?php												
                                                        }
                                                    }   
                                                    ?>
                                                    <?php
                                                        if( $allow_img_filter == 1 ) {					
                                                            foreach ( (array) $comment_gallery_meta as $comment_gallery_id ) {
                                                                if( is_array( $comment_gallery_id ) ) {
                                                                    foreach ( $comment_gallery_id as $comment_gallery_id_val ) {
                                                                        $img_url 		= wp_get_attachment_image_src( $comment_gallery_id_val );
                                                                        $full_img_url 	= wp_get_attachment_image_src( $comment_gallery_id_val, 'full' );
                                                                        ?>
                                                                            <div class="rx_photo">
                                                                                <div class="popup-link">
                                                                                    <a href="<?php echo esc_url( $full_img_url[0] ); ?>">
                                                                                        <img src="<?php echo esc_url( $img_url[0] ); ?>" class="img-fluid" alt="<?php esc_attr_e('ReviewX', 'reviewx-pro'); ?>">
                                                                                    </a>
                                                                                </div>
                                                                            </div>												
                                                                        <?php
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                </div>
                                            <?php
                                            }                    
                                        ?>
            
                                        <div class="rx_flex rx_meta">
                                            <?php if(class_exists('ReviewXPro')) : ?>
                                            <?php if( $allow_share == 1 ) : ?>
                                            <div class="rx_flex rx_share">
                                                <p><?php echo ! empty(get_theme_mod('reviewx_share_on_label') ) ? get_theme_mod('reviewx_share_on_label') : esc_html__( 'Share on', 'reviewx-pro' ) ?></p>
                                                <div class="social-links">
                                                    <?php 
                                                        $string = '';
                                                        for( $i = 0; $i< floor((int)$get_rating); $i++ ) {
                                                            $string .='★';
                                                        }                                    
                                                        $combined_text = $string .' '. $comment->comment_content;
                                                        $data = array(
                                                            'review' => $combined_text     
                                                        );  
                                                        apply_filters( 'rx_social_sharing', $data );                               
                                                    ?>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <?php endif; ?>

                                            <?php if(class_exists('ReviewXPro')) : ?>
                                            <?php 
                                                if( $allow_like_dislike == 1 ) :
                                            ?>
                                            <div class="rx_flex rx_helpful">
                                                <div class="rx_flex rx_review_vote_icon">
                                                    <p><?php echo ! empty(get_theme_mod('reviewx_helpful_label') ) ? get_theme_mod('reviewx_helpful_label') : esc_html__( 'Helpful?', 'reviewx-pro' ); ?></p>
                                                    <?php if( is_user_logged_in() ) { ?>
                                                    <button type="button" class="reviewx_like like" like_id="<?php echo esc_attr( $comment->comment_ID ); ?>">
                                                    <?php } else { ?>
                                                    <button type="button" class="like_login_required like" like_id="<?php echo esc_attr( $comment->comment_ID ); ?>" data-title="<?php echo esc_html__('You have to login first', 'reviewx-pro' );?>">
                                                    <?php } ?>
                                                    <div class="rx_helpful_count_box">
                                                        <div class="rx_helpful_style_2_svg">
                                                            <svg width="25px" height="25px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 80 80" style="enable-background:new 0 0 80 80;" xml:space="preserve">
                                                                <g>
                                                                    <g>
                                                                        <path d="M5.8,77.2h8.3c3.2,0,5.8-2.6,5.8-5.8V33.1c0-3.2-2.6-5.8-5.8-5.8H5.8c-3.2,0-5.8,2.6-5.8,5.8v38.3
                                                                            C0,74.6,2.6,77.2,5.8,77.2L5.8,77.2z M5.8,77.2"></path>
                                                                        <path d="M42.6,3.1c-3.3,0-5,1.7-5,10c0,7.9-7.7,14.3-12.6,17.6v41.3c5.3,2.5,16,6.1,32.6,6.1h5.3c6.5,0,12-4.7,13.1-11.1l3.7-21.7
                                                                            c1.4-8.2-4.9-15.6-13.1-15.6H50.9c0,0,2.5-5,2.5-13.3C53.4,6.4,45.9,3.1,42.6,3.1L42.6,3.1z M42.6,3.1"></path>
                                                                    </g>
                                                                </g>
                                                            </svg>
                                                        </div>
                                                        <div class="rx_helpful_count_val reviewx_like_val-<?php echo esc_attr( $comment->comment_ID ); ?>">
                                                            <?php echo $result = ( ! empty( $comment_like ) ? $result = sizeof($comment_like) : 0 ); ?>
                                                        </div>
                                                    </div>
                                                    </button>
                                                    <?php echo '<input type="hidden" name="rx-voted-nonce" id="rx-voted-nonce" value="'.wp_create_nonce( "special-string" ).'">'; ?>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="rx_flex rx_meta">
                                            <?php if(class_exists('ReviewXPro')) : ?>
                                            <?php  if( current_user_can('manage_options') && check_review_has_child( $comment->comment_ID ) == 0 ) { ?>
                                                <span class="rx-admin-reply" data-review-id="<?php echo esc_attr( $comment->comment_ID ); ?>" data-product-id="<?php echo get_review_product_id( $comment->comment_ID) ; ?>"><?php echo !empty(get_theme_mod('reviewx_reply_label') ) ? get_theme_mod('reviewx_reply_label') : __( 'Reply', 'reviewx-pro' ); ?></span>
                                            <?php } else { ?>
                                                <span class="rx-admin-reply" style="display:none;" data-review-id="<?php echo esc_attr( $comment->comment_ID ); ?>" data-product-id="<?php echo get_review_product_id( $comment->comment_ID) ; ?>"><?php echo !empty(get_theme_mod('reviewx_reply_label') ) ? get_theme_mod('reviewx_reply_label') : __( 'Reply', 'reviewx-pro' ); ?></span>
                                            <?php } ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- Start admin reply area -->
                            <?php
                                if(class_exists('ReviewXPro')) {					
                                    if( ! empty( reviewx_get_comment_by_id( $comment->comment_ID ) ) ) { 
                                        $child_comment = reviewx_get_comment_by_id( $comment->comment_ID );
                                        $reply = (array)$child_comment;
                                        ?>
                                        <ul class="children">
                                            <li class="comment byuser comment-author-admin bypostauthor odd alt depth-2 rx_review_reply_item">
                                                <div class="rx_flex rx_review_wrap">
                                                    <div class="rx_thumb">
                                                        <?php 
                                                            $shop_icon_url = get_option( '_rx_option_icon_upload' );
                                                            if( !empty($shop_icon_url) ){  ?>
                                                            <img src="<?php echo esc_url($shop_icon_url); ?>" alt="<?php echo __('ReviewX shop icon',  'reviewx-pro'); ?>"/>
                                                        <?php 
                                                            } else {
                                                        ?>
                                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 66 66" style="enable-background:new 0 0 66 66;" xml:space="preserve">
                                                            <g>
                                                                <path class="st0" d="M50,21.2c-0.3,2.8-1.5,4.9-3.7,6.3c-1.5,1-3.3,1.5-5.1,1.4c-3.8-0.2-6.5-2.7-7.8-7.4c-0.5,2.9-1.8,5-4.2,6.4
                                                                c-1.6,0.9-3.3,1.2-5.1,1c-3.8-0.5-6.6-3.3-7.3-7.5c-0.2,0.6-0.2,1.1-0.4,1.6c-1,3.6-4.3,6.1-8,6c-3.8,0-7.3-2.5-8.2-6
                                                                c-0.3-1.1-0.2-2.1,0.4-3.1C3.8,15,6.9,10.1,10,5.1c0.3-0.4,0.6-0.6,1.1-0.6c13.9,0,27.8,0,41.8,0c0.5,0,0.8,0.2,1.1,0.6
                                                                c3.8,5.1,7.7,10.2,11.5,15.3c0.5,0.6,0.6,1.2,0.5,1.9c-0.6,3.8-3.5,6.4-7.3,6.7c-3.8,0.3-7.1-2-8.2-5.7C50.3,22.6,50.2,22,50,21.2z
                                                                "/>
                                                                <path class="st0" d="M49.6,29.8c-4.8,4.3-11.7,4.2-16.4,0c-4.9,4.4-11.9,4.1-16.4,0c-1.5,1.4-3.3,2.4-5.4,2.9
                                                                c-2.1,0.5-4.1,0.4-6.2-0.2v23.1c0,3.3,2.7,6,6,6h13.2c0.2,0,0.4-0.2,0.4-0.4c0,0,0,0,0,0V43.4c0-1,0.8-1.8,1.8-1.8h12.9
                                                                c1,0,1.8,0.8,1.8,1.8v17.7c0,0,0,0,0,0c0,0.2,0.2,0.4,0.4,0.4h13.2c3.3,0,6-2.7,6-6V32.6C55.3,33.2,53.7,32.9,49.6,29.8z"/>
                                                            </g>
                                                        </svg>
                                                        <?php } ?>
                                                    </div>	
                                                    <div class="rx_body">										
                                                        <div class="rx_flex comment-header">
                                                            <div class="rx_flex child-comment-heading">
                                                                <h4 class="review_title"><?php echo get_bloginfo( 'name' ); ?></h4>
                                                                <div class="owner_arrow">
                                                                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                                        viewBox="0 0 66 55" style="enable-background:new 0 0 66 55;" xml:space="preserve">
                                                                        <path class="st0" d="M25.9,40.1C51.4,36.4,62.3,18.2,66-0.1c-9.1,12.8-21.9,18.6-40.1,18.6V3.6L0.4,29.1l25.5,25.5V40.1L25.9,40.1z"
                                                                        />
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                            <div class="rx_review_calender">
                                                                <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                                    viewBox="-49 141 512 512" style="enable-background:new -49 141 512 512;" xml:space="preserve">
                                                                    <g>
                                                                        <path class="st0" d="M383,161h-10.8H357h-40h-91h-40H96H56h-7.8H31c-44.1,0-80,35.9-80,80v312c0,44.1,35.9,80,80,80h352
                                                                        c42.1,0,76.7-32.7,79.8-74c0.1-1,0.2-2,0.2-3V241C463,196.9,427.1,161,383,161z M423,553c0,22.1-17.9,40-40,40H31
                                                                        c-22.1,0-40-17.9-40-40V241c0-22.1,17.9-40,40-40h25v20c0,11,9,20,20,20s20-9,20-20v-20h90v20c0,11,9,20,20,20s20-9,20-20v-20h91
                                                                        v20c0,11,9,20,20,20c11,0,20-9,20-20v-20h26c22.1,0,40,17.9,40,40V553z"/>
                                                                        <circle class="st0" cx="76" cy="331" r="20"/>
                                                                        <circle class="st0" cx="250" cy="331" r="20"/>
                                                                        <circle class="st0" cx="337" cy="331" r="20"/>
                                                                        <circle class="st0" cx="76" cy="418" r="20"/>
                                                                        <circle class="st0" cx="76" cy="505" r="20"/>
                                                                        <circle class="st0" cx="163" cy="331" r="20"/>
                                                                        <circle class="st0" cx="163" cy="418" r="20"/>
                                                                        <circle class="st0" cx="163" cy="505" r="20"/>
                                                                        <circle class="st0" cx="250" cy="418" r="20"/>
                                                                        <circle class="st0" cx="337" cy="418" r="20"/>
                                                                        <circle class="st0" cx="250" cy="505" r="20"/>
                                                                    </g>
                                                                </svg>
                                                                <span> <?php echo date_i18n(get_option('date_format'), strtotime($reply['comment_date'])); ?></span>
                                                            </div>
                                                            <?php if ( current_user_can( 'manage_options' ) ) { ?>
                                                            <div class="rx_flex rx_meta">
                                                                <span class="admin-reply-edit-icon" data-review-id="<?php echo esc_attr( $reply['comment_ID'] ); ?>">
                                                                    <svg viewBox="0 -1 401.52289 401" xmlns="http://www.w3.org/2000/svg"><path d="m370.589844 250.972656c-5.523438 0-10 4.476563-10 10v88.789063c-.019532 16.5625-13.4375 29.984375-30 30h-280.589844c-16.5625-.015625-29.980469-13.4375-30-30v-260.589844c.019531-16.558594 13.4375-29.980469 30-30h88.789062c5.523438 0 10-4.476563 10-10 0-5.519531-4.476562-10-10-10h-88.789062c-27.601562.03125-49.96875 22.398437-50 50v260.59375c.03125 27.601563 22.398438 49.96875 50 50h280.589844c27.601562-.03125 49.96875-22.398437 50-50v-88.792969c0-5.523437-4.476563-10-10-10zm0 0"/><path d="m376.628906 13.441406c-17.574218-17.574218-46.066406-17.574218-63.640625 0l-178.40625 178.40625c-1.222656 1.222656-2.105469 2.738282-2.566406 4.402344l-23.460937 84.699219c-.964844 3.472656.015624 7.191406 2.5625 9.742187 2.550781 2.546875 6.269531 3.527344 9.742187 2.566406l84.699219-23.464843c1.664062-.460938 3.179687-1.34375 4.402344-2.566407l178.402343-178.410156c17.546875-17.585937 17.546875-46.054687 0-63.640625zm-220.257812 184.90625 146.011718-146.015625 47.089844 47.089844-146.015625 146.015625zm-9.40625 18.875 37.621094 37.625-52.039063 14.417969zm227.257812-142.546875-10.605468 10.605469-47.09375-47.09375 10.609374-10.605469c9.761719-9.761719 25.589844-9.761719 35.351563 0l11.738281 11.734375c9.746094 9.773438 9.746094 25.589844 0 35.359375zm0 0"/></svg>
                                                                </span>
                                                                <span class="admin-reply-delete-icon" data-review-id="<?php echo esc_attr( $reply['comment_ID'] ); ?>">
                                                                    <svg viewBox="-57 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="m156.371094 30.90625h85.570312v14.398438h30.902344v-16.414063c.003906-15.929687-12.949219-28.890625-28.871094-28.890625h-89.632812c-15.921875 0-28.875 12.960938-28.875 28.890625v16.414063h30.90625zm0 0"/><path d="m344.210938 167.75h-290.109376c-7.949218 0-14.207031 6.78125-13.566406 14.707031l24.253906 299.90625c1.351563 16.742188 15.316407 29.636719 32.09375 29.636719h204.542969c16.777344 0 30.742188-12.894531 32.09375-29.640625l24.253907-299.902344c.644531-7.925781-5.613282-14.707031-13.5625-14.707031zm-219.863282 312.261719c-.324218.019531-.648437.03125-.96875.03125-8.101562 0-14.902344-6.308594-15.40625-14.503907l-15.199218-246.207031c-.523438-8.519531 5.957031-15.851562 14.472656-16.375 8.488281-.515625 15.851562 5.949219 16.375 14.472657l15.195312 246.207031c.527344 8.519531-5.953125 15.847656-14.46875 16.375zm90.433594-15.421875c0 8.53125-6.917969 15.449218-15.453125 15.449218s-15.453125-6.917968-15.453125-15.449218v-246.210938c0-8.535156 6.917969-15.453125 15.453125-15.453125 8.53125 0 15.453125 6.917969 15.453125 15.453125zm90.757812-245.300782-14.511718 246.207032c-.480469 8.210937-7.292969 14.542968-15.410156 14.542968-.304688 0-.613282-.007812-.921876-.023437-8.519531-.503906-15.019531-7.816406-14.515624-16.335937l14.507812-246.210938c.5-8.519531 7.789062-15.019531 16.332031-14.515625 8.519531.5 15.019531 7.816406 14.519531 16.335937zm0 0"/><path d="m397.648438 120.0625-10.148438-30.421875c-2.675781-8.019531-10.183594-13.429687-18.640625-13.429687h-339.410156c-8.453125 0-15.964844 5.410156-18.636719 13.429687l-10.148438 30.421875c-1.957031 5.867188.589844 11.851562 5.34375 14.835938 1.9375 1.214843 4.230469 1.945312 6.75 1.945312h372.796876c2.519531 0 4.816406-.730469 6.75-1.949219 4.753906-2.984375 7.300781-8.96875 5.34375-14.832031zm0 0"/></svg>
                                                                </span>
                                                            </div>	
                                                            <?php } ?>										
                                                        </div>
                                                        <div class="comment-body">
                                                            <div class="comment-content">                                                            
                                                                <?php comment_text($child_comment); ?>  
                                                            </div>
                                                        </div> 										
                                                    </div>								
                                                </div>
                                            </li>
                                        </ul>
                                    <?php 				
                                    } }?>

                                <!-- End admin reply area -->
                            </li>
                            <?php				
                            }
                        }
                    ?>
            </ul>        
</div>