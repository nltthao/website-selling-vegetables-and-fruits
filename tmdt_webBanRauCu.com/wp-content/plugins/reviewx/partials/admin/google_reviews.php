<?php

use ReviewX\Controllers\Storefront\Modules\GoogleReviews;

$is_pro = ReviewX_Helper::is_pro();
if (class_exists('WooCommerce')) {
    $wc_coupons = ReviewX_Helper::rx_available_coupon_codes();
    $wc_products = ReviewX_Helper::woocommerce_product_list();
    $categories = get_terms(['taxonomy' => 'product_cat']);
}
// $review_for_discount = get_option('review_for_discount');
// $register_user = get_option('register_user');
// $rx_coupon_verified_user = get_option('rx_coupon_verified_user');
// $coupon_photo_review = get_option('coupon_photo_review');
// $coupon_select_rating = get_option('coupon_select_rating');
// $rx_coupon_type = get_option('rx_coupon_type');
// $rx_coupon_selected_coupon = get_option('rx_coupon_selected_coupon');
// $rx_coupon_prefix = get_option('rx_coupon_prefix');
// $rx_coupon_discount_type = get_option('rx_coupon_discount_type');
// $rx_coupon_discount_amount = get_option('rx_coupon_discount_amount');
// $rx_coupon_free_shipping = get_option('rx_coupon_free_shipping');
// $rx_coupon_start_date = get_option('rx_coupon_start_date');
// $rx_coupon_end_date_select = get_option('rx_coupon_end_date_select');
// $rx_coupon_end_date = get_option('rx_coupon_end_date');
// $rx_coupon_use_limit_one = get_option('rx_coupon_use_limit_one');
// $rx_coupon_use_limit_multiple = get_option('rx_coupon_use_limit_multiple');
// $rx_coupon_use_limit_value = get_option('rx_coupon_use_limit_value');
// $rx_coupon_minimum_requinment = get_option('rx_coupon_minimum_requinment');
// $rx_coupon_minimum_purchase_amount = get_option('rx_coupon_minimum_purchase_amount');
// $rx_coupon_minimum_quantity = get_option('rx_coupon_minimum_quantity');
// $rx_coupon_cat_name = get_option('rx_coupon_cat_name');
// $rx_coupon_product_name = get_option('rx_coupon_product_name');
// $rx_coupon_email_title = get_option('rx_coupon_email_title');
// $email_content = get_option('email_content');
// if (!is_array($rx_coupon_cat_name)) {
//     $rx_coupon_cat_name = [];
// }
// if (!is_array($rx_coupon_product_name)) {
//     $rx_coupon_product_name = [];
// }

// $dafault_email_conent = "<p>Dear <strong>[CUSTOMER_NAME]</strong>,</p><p>Thank you so much for leaving reviews on my store.</p><p>As a token of appreciation, we would like to offer you a discount coupon for your next purchases in our shop.</p><p>Coupon code: <strong> [COUPON_CODE] </strong>.<br />Date expires: <strong>[DATE_EXPIRES]</strong>.<br />Yours sincerely!</p>";
if (isset($_POST['submit'])) {
    // run validation if you're not doing it in js


    $rx_review_api = isset($_POST['rx_review_api']) ? $_POST['rx_review_api'] : '';
    $rx_review_id = isset($_POST['rx_review_id']) ? $_POST['rx_review_id'] : '';

    $data = [
        'rx_review_api' => sanitize_text_field($rx_review_api),
        'rx_review_id' => sanitize_text_field($rx_review_id)
    ];

    if (empty(get_option('rx_location_review'))) {
        add_option('rx_location_review', serialize($data));
    }
    update_option('rx_location_review', $data);

    // $status = $wpdb->insert('www_options', array(
    //     'option_name'  => $option_name,
    //     'option_value' => $option_value,

    // ));

    if (file_exists(REVIEWX_GOOGLE_JSON_REVIEW)) {
        $test = REVIEWX_GOOGLE_JSON_REVIEW;
        unlink($test);
    }
}
?>

<div class="rx-metabox-wrapper">
    <div class="rx-settings-header ">
        <div class="rx-header-left">
            <div class="rx-admin-header">
                <img src="<?php echo esc_url(assets('admin/images/ReviewX.svg')); ?>" alt="<?php esc_attr_e('ReviewX', 'reviewx') ?>">
                <div>
                    <h2 class="rx-plugin-tagline"><?php esc_html_e('Review for Discount', 'reviewx'); ?></h2>
                    <h3 class="rx-export-plugin-tagline2">
                        <?php esc_html_e('Send discount coupons to customers who left reviews', 'reviewx'); ?>

                    </h3>
                </div>
            </div>
        </div>
        <div class="rx-header-right">
            <span><?php esc_html_e('ReviewX', 'reviewx'); ?>: <strong><?php echo REVIEWX_VERSION; ?></strong></span>
            <?php
            if (class_exists('ReviewXPro')) :
            ?>
                <span><?php esc_html_e('ReviewX Pro', 'reviewx'); ?>: <strong><?php echo REVIEWX_PRO_VERSION; ?></strong></span>
            <?php endif; ?>
        </div>
    </div>
    <form class="rx-google-reviews" id="rx-google-reviews" action="" method="post">
        <div class="rx-coupon-page-tab">
            <div class="rx-coupon-tab-container">
                <div class="rx-metatab-menu">
                    <ul>
                        <li class="rx-coupon-tab1 active">
                            <span class="rx-menu-icon">
                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" style="enable-background:new 0 0 100 100;" xml:space="preserve">
                                    <path class="st0" d="M89.9,77.8c-0.5,0.9-0.8,1.8-1.4,2.4c-2.5,2.6-5.1,5.2-7.7,7.8c-1.4,1.4-2.9,1.4-4.5,0.4
                                                c-2.3-1.4-4.6-2.8-6.9-4.1c-0.7-0.4-1.6-0.2-2.4-0.2c-0.1,0-0.1,0.1-0.2,0.1c-4.2,0.9-6.3,3.6-6.5,7.9c-0.1,1.7-0.8,3.3-1.2,5
                                                c-0.5,1.9-1.6,2.9-3.6,2.9c-3.5,0-7,0-10.5,0c-1.9,0-3.1-0.9-3.5-2.7c-0.8-3-1.6-6-2.2-9.1c-0.3-1.4-1.2-1.9-2.3-2.6
                                                c-3.5-2.3-6.4-1.1-9.5,0.9c-6,4-5.5,3.4-10.3-1.3c-1.5-1.5-3-3.1-4.6-4.6c-1.6-1.5-1.8-3.1-0.6-5c1.3-2.1,2.6-4.1,3.8-6.3
                                                c0.4-0.8,0.3-1.9,0.2-2.8c-0.7-4.3-3.6-6-7.7-6.4c-1.7-0.2-3.3-0.8-5-1.2c-2-0.5-3-1.6-3-3.8c0.1-3.4,0.1-6.8,0-10.3
                                                c0-2,0.9-3.2,2.8-3.7c3-0.7,5.9-1.5,8.9-2.2c1.4-0.3,2-1,2.7-2.2c1.7-2.9,1.2-5.3-0.7-7.9c-1.2-1.7-2.2-3.5-3.2-5.3
                                                c-0.9-1.5-0.7-2.9,0.5-4.1c2.6-2.6,5.1-5.2,7.7-7.7c1.4-1.4,2.9-1.4,4.5-0.4c2.5,1.5,4.9,3.1,7.5,4.4c0.6,0.4,1.6,0.2,2.4,0.2
                                                c0.4,0,0.8-0.5,1.2-0.6c3.8-0.6,5.1-3.2,5.5-6.7c0.2-2,1-4,1.5-6.1C41.9,0.9,43,0,44.8,0c3.6,0,7.1,0,10.7,0c1.9,0,3,1,3.5,2.8
                                                c0.8,3,1.6,6,2.2,9.1c0.3,1.3,1.1,1.8,2.1,2.5c3.5,2.3,6.3,1.2,9.5-0.9c6.6-4.4,5.9-3.7,11,1.3c1.5,1.5,2.9,3,4.4,4.4
                                                c1.4,1.4,1.6,2.9,0.6,4.6c-1.4,2.2-2.6,4.5-4,6.7c-0.7,1.1-0.5,2-0.3,3.3c1,4.1,3.7,5.6,7.6,6c1.8,0.2,3.5,0.9,5.2,1.3
                                                c1.9,0.5,2.9,1.6,2.9,3.7c0,3.5,0,7,0,10.5c0,1.9-0.9,3.1-2.7,3.5c-3,0.8-6,1.6-9.1,2.2c-1.3,0.3-1.9,1-2.5,2.1
                                                c-2.1,3.4-1.4,6.3,0.9,9.3C87.9,74,88.8,75.9,89.9,77.8z M50,70.5c11.1,0.2,20.4-8.9,20.6-20.1c0.2-11.4-8.8-20.7-20.2-20.9
                                                c-11.2-0.2-20.6,9-20.7,20.2C29.6,61.1,38.6,70.3,50,70.5z"></path>
                                </svg>
                            </span>
                            <span class="rx-menu-title">Configuration</span>
                        </li>
                        <!-- <li class="rx-coupon-tab2">
                            <span class="rx-menu-icon">
                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" style="enable-background:new 0 0 100 100;" xml:space="preserve">
                                    <g>
                                        <path class="st0" d="M65,26.3c-8.5,0-17.1,0-25.6,0c-3,0-4.1-1-4.1-4c0-5.3,0-10.6,0-15.9c0-2.8,1-3.8,3.8-3.8c17.2,0,39.8,0,57,0
                                                    c2.8,0,3.8,1,3.8,3.8c0,5.4,0,10.7,0,16.1c0,2.8-1,3.8-3.8,3.8C87.6,26.3,73.6,26.3,65,26.3z"></path>
                                        <path class="st0" d="M64.9,38.1c8.5,0,22.5,0,31,0c3.1,0,4.1,1,4.1,4c0,5.3,0,10.6,0,15.9c0,2.8-1,3.8-3.8,3.8c-17.2,0-39.8,0-57,0
                                                    c-2.8,0-3.8-1-3.9-3.8c0-5.4,0-10.7,0-16.1c0-2.8,1-3.8,3.8-3.8C47.8,38.1,56.4,38.1,64.9,38.1z"></path>
                                        <path class="st0" d="M65,97.4c-8.5,0-17.1,0-25.6,0c-3.1,0-4.1-1-4.1-4c0-5.3,0-10.6,0-15.9c0-2.8,1.1-3.8,4-3.9
                                                    c17.1,0,39.6,0,56.8,0c2.8,0,3.8,1.1,3.9,3.8c0,5.4,0,10.7,0,16.1c0,2.8-1,3.8-3.8,3.8C87.6,97.4,73.6,97.4,65,97.4z"></path>
                                        <path class="st0" d="M23.5,14.6c0,2.7,0,5.5,0,8.2c0,2.4-1.1,3.5-3.5,3.5c-5.6,0-11.1,0-16.7,0c-2.4,0-3.5-1.1-3.5-3.5
                                                    c0-5.6,0-11.1,0-16.7c0-2.4,1.1-3.5,3.5-3.5c5.6,0,11.1,0,16.7,0c2.4,0,3.5,1.1,3.5,3.5C23.5,8.9,23.5,11.8,23.5,14.6z"></path>
                                        <path class="st0" d="M11.6,38.1c2.7,0,5.5,0,8.2,0c2.5,0,3.6,1.1,3.6,3.6c0,5.5,0,11,0,16.5c0,2.5-1.1,3.6-3.6,3.6
                                                    c-5.6,0-11.1,0-16.7,0c-2.2,0-3.4-1.2-3.4-3.4c0-5.6,0-11.3,0-16.9c0-2.3,1.2-3.4,3.6-3.4C6.1,38.1,8.9,38.1,11.6,38.1z"></path>
                                        <path class="st0" d="M23.5,85.4c0,2.8,0,5.6,0,8.4c0,2.5-1.1,3.6-3.6,3.6c-5.5,0-11,0-16.5,0c-2.5,0-3.6-1.1-3.6-3.6
                                                    c0-5.5,0-11,0-16.5c0-2.5,1.1-3.6,3.7-3.6c5.5,0,11,0,16.5,0c2.5,0,3.5,1.1,3.6,3.6C23.5,80,23.5,82.7,23.5,85.4z"></path>
                                    </g>
                                </svg>
                            </span>
                            <span class="rx-menu-title">Coupon</span>
                        </li>
                        <li class="rx-coupon-tab3">
                            <span class="rx-menu-icon">
                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" style="enable-background:new 0 0 100 100;" xml:space="preserve">
                                    <g>
                                        <circle class="st0" cx="50" cy="28.1" r="28.1"></circle>
                                        <path class="st0" d="M29.9,59.5c5.8,3.8,12.7,5.9,20.1,5.9s14.3-2.2,20.1-5.9V100L50,82.7L29.9,100V59.5z"></path>
                                    </g>
                                </svg>
                            </span>
                            <span class="rx-menu-title">Email</span>
                        </li> -->
                    </ul>
                </div>
            </div>
        </div>
        <div class="rx-coupon-pages">
            <div class="rx-coupon-pages-wrapper">
                <div class="rx-coupon-content-center">
                    <div class="rx-coupon-page1">
                        <div class="rx-coupon-page1-content">
                            <div class="rx-coupon-discount-amount">
                                <h3 class="rx-coupon-field-heading" style="font-size:16px"><?php echo __('Google API key', 'reviewx'); ?></h3>
                                <div class="rx-coupon-tooltip-container">
                                    <?php
                                    if (get_option('rx_location_review')) {
                                        $data = get_option('rx_location_review');
                                    }

                                    ?>
                                    <input class="rx_coupon_discount_amount" id="google-review-location-id" type="text" name="rx_review_api" value="<?php if (!empty($data['rx_review_api'])) {
                                                                                                                                                        echo esc_textarea($data['rx_review_api']);
                                                                                                                                                    } ?>" />
                                    <div class="rx-coupon-discount-amount-tooltip rx-coupon-tooltip">
                                        <img class="rx-coupon-img" style="height: 20px; width:20px;" src="<?php echo esc_url(assets('admin/images/info.png')); ?>;" alt="<?php esc_attr_e('Info', 'reviewx') ?>" />
                                        <span class="rx-coupon-discount-amount-tooltiptext" style="width:400px"><?php _e('It is not mandatory. However, without a Google API key, reviews will not be automatically updated every day.

Please create your own Google API key and enter it here.', 'reviewx'); ?><a href="https://reviewx.io/docs/how-to-add-google-reviews-to-your-website-using-reviewx/" target="_blacnk">Learn More</a></span>
                                    </div>
                                </div>
                            </div>
                            <div class="rx-coupon-discount-amount">
                                <h3 class="rx-coupon-field-heading" style="font-size:16px"><?php echo __('Place ID', 'reviewx'); ?><span style="color:red">*</span></h3>
                                <div class="rx-coupon-tooltip-container">
                                    <input class="rx_coupon_discount_amount" id="google-review-location-api" type="text" name="rx_review_id" value="<?php if (!empty($data['rx_review_id'])) {
                                                                                                                                                        echo esc_textarea($data['rx_review_id']);
                                                                                                                                 } ?>" />
                                    <div class="rx-coupon-discount-amount-tooltip rx-coupon-tooltip">
                                        <img class="rx-coupon-img" style="height: 20px; width:20px;" src="<?php echo esc_url(assets('admin/images/info.png')); ?>;" alt="<?php esc_attr_e('Info', 'reviewx') ?>" />
                                        <span class="rx-coupon-discount-amount-tooltiptext" style="width:400px"><?php _e('A place ID is a textual identifier that uniquely identifies a place.', 'reviewx'); ?><a href="https://reviewx.io/docs/how-to-add-google-reviews-to-your-website-using-reviewx/" target="_blacnk">Learn More</a></span>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($data['rx_review_id'])) : ?>
                                <div class="rx-coupon-discount-amount">
                                    <h3 class="rx-coupon-field-heading"style="font-size:16px"><?php echo __('GMB Review Shortcode', 'reviewx'); ?></h3>
                                    <div class="rx-coupon-tooltip-container">
                                        <input class="rx_coupon_discount_amount" type="text" value="[rvx_review_google]" readonly />
                                        <div class="rx-coupon-discount-amount-tooltip rx-coupon-tooltip">
                                            <img class="rx-coupon-img" style="height: 20px; width:20px;" src="<?php echo esc_url(assets('admin/images/info.png')); ?>;" alt="<?php esc_attr_e('Info', 'reviewx') ?>" />
                                            <span class="rx-coupon-discount-amount-tooltiptext"><?php _e('Copy this shortcode and put it where you want to display the widget.', 'reviewx'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="rx-coupon-bottom-devider"></div>
                        <div class="rx-coupon-page3-buttons">
                            <input type="submit" class="rx-coupon-page3-next" name="submit" value="Save" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php
    if(!empty($data['rx_review_id'])){
        echo GoogleReviews::rxGoogleReviews();
    }
    
    ?>


</div>