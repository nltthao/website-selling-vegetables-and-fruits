<?php
$is_pro = ReviewX_Helper::is_pro();
if (class_exists('WooCommerce')) {
    $wc_coupons = ReviewX_Helper::rx_available_coupon_codes();
    $wc_products = ReviewX_Helper::woocommerce_product_list();
    $categories = get_terms(['taxonomy' => 'product_cat']);
}
$review_for_discount = get_option('review_for_discount');
$register_user = get_option('register_user');
$rx_coupon_verified_user = get_option('rx_coupon_verified_user');
$coupon_photo_review = get_option('coupon_photo_review');
$coupon_select_rating = get_option('coupon_select_rating');
$rx_coupon_type = get_option('rx_coupon_type');
$rx_coupon_selected_coupon = get_option('rx_coupon_selected_coupon');
$rx_coupon_prefix = get_option('rx_coupon_prefix');
$rx_coupon_discount_type = get_option('rx_coupon_discount_type');
$rx_coupon_discount_amount = get_option('rx_coupon_discount_amount');
$rx_coupon_free_shipping = get_option('rx_coupon_free_shipping');
$rx_coupon_start_date = get_option('rx_coupon_start_date');
$rx_coupon_end_date_select = get_option('rx_coupon_end_date_select');
$rx_coupon_end_date = get_option('rx_coupon_end_date');
$rx_coupon_use_limit_one = get_option('rx_coupon_use_limit_one');
$rx_coupon_use_limit_multiple = get_option('rx_coupon_use_limit_multiple');
$rx_coupon_use_limit_value = get_option('rx_coupon_use_limit_value');
$rx_coupon_minimum_requinment = get_option('rx_coupon_minimum_requinment');
$rx_coupon_minimum_purchase_amount = get_option('rx_coupon_minimum_purchase_amount');
$rx_coupon_minimum_quantity = get_option('rx_coupon_minimum_quantity');
$rx_coupon_cat_name = get_option('rx_coupon_cat_name');
$rx_coupon_product_name = get_option('rx_coupon_product_name');
$rx_coupon_email_title = get_option('rx_coupon_email_title');
$email_content = get_option('email_content');
if (!is_array($rx_coupon_cat_name)) {
    $rx_coupon_cat_name = [];
}
if (!is_array($rx_coupon_product_name)) {
    $rx_coupon_product_name = [];
}

$dafault_email_conent = "<p>Dear <strong>[CUSTOMER_NAME]</strong>,</p><p>Thank you so much for leaving reviews on my store.</p><p>As a token of appreciation, we would like to offer you a discount coupon for your next purchases in our shop.</p><p>Coupon code: <strong> [COUPON_CODE] </strong>.<br />Date expires: <strong>[DATE_EXPIRES]</strong>.<br />Yours sincerely!</p>";

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
    <form class="rx-coupon-form" id="rx-coupon-form">
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
                            <span class="rx-menu-title">Settings</span>
                        </li>
                        <li class="rx-coupon-tab2">
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
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="rx-coupon-pages">
            <div class="rx-coupon-pages-wrapper">
                <div class="rx-coupon-content-center">
                    <div class="rx-coupon-page1">
                        <div class="rx-coupon-page1-content">
                            <div class="rx-coupon-settings">
                                <div class="rx-coupon-settings-container">
                                    <h3 class="rx-coupon-field-heading"><?php _e('Enable Review for Discount', 'reviewx'); ?></h3>
                                    <label class="rx-coupon-settings-label"><?php _e('Enable the generation of discount coupons for customers who provide reviews', 'reviewx'); ?></label>
                                </div>
                                <label class="switch">
                                    <input class="rx-review-for-discount" type="checkbox" name="review_for_discount" <?php echo ($review_for_discount == 'on') ? 'checked' : ''; ?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <div class="rx-coupon-enable-field" style="display: none;">
                                <div class="rx-coupon-settings">
                                    <div class="rx-coupon-settings-container">
                                        <h3 class="rx-coupon-field-heading"> <?php _e('Register User', 'reviewx'); ?></h3>
                                        <label class="rx-coupon-settings-label"><?php _e('Send coupons only if the customers email is registered on your store', 'reviewx'); ?></label>
                                    </div>
                                    <label class="switch">
                                        <input class="rx-meta-field" type="checkbox" name="register_user" <?php echo ($register_user == 'on') ? 'checked' : ''; ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="rx-coupon-settings">
                                    <div class="rx-coupon-settings-container">
                                        <h3 class="rx-coupon-field-heading"><?php _e('Verified Review', 'reveiwx'); ?></h3>
                                        <label class="rx-coupon-settings-label"> <?php _e('Send coupons only for reviews from purchased customers', 'reviewx'); ?></label>
                                    </div>
                                    <label class="switch">
                                        <input class="rx-meta-field" type="checkbox" name="rx_coupon_verified_user" <?php echo ($rx_coupon_verified_user == 'on') ? 'checked' : ''; ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="rx-coupon-settings">
                                    <div class="rx-coupon-settings-container">
                                        <h3 class="rx-coupon-field-heading"> <?php _e('Only for Photo Review', 'reviewx'); ?></h3>
                                        <label class="rx-coupon-settings-label"> <?php _e('Send coupons only for reviews that include photos', 'reviewx'); ?> </label>
                                    </div>
                                    <label class="switch">
                                        <div class="rx-coupon-pro-fld">
                                            <input class="rx-meta-field" type="checkbox" name="coupon_photo_review" <?php echo ($coupon_photo_review == 'on' && \ReviewX_Helper::is_pro()) ? 'checked' : ''; ?> <?php echo !$is_pro ? "disabled" : null; ?>>
                                            <span class="slider round <?php echo !$is_pro ? 'rx-coupon-pro' : ' '; ?>"></span>
                                            <?php if (!\ReviewX_Helper::is_pro()) : ?>
                                                <div class="rx-label">
                                                    <?php if (!ReviewX_Helper::is_pro()) :  // check is pro 
                                                    ?>
                                                        <div class="rx-pro-checkbox">
                                                        <?php endif; ?>
                                                        <?php if (!ReviewX_Helper::is_pro()) :  // check is pro 
                                                        ?>
                                                            <sup class="rx-pro-label"><?php _e('Pro', 'reviewx'); ?></sup>
                                                        <?php endif; ?>
                                                        <?php if (!ReviewX_Helper::is_pro()) : // check is pro 
                                                        ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </label>
                                </div>
                                <div class="rx-coupon-settings">
                                    <div class="rx-coupon-settings-container">
                                        <h3 class="rx-coupon-field-heading"> <?php _e('Required Rating', 'reviewx'); ?></h3>
                                        <label class="rx-coupon-settings-label"><?php _e('Only send coupons for reviews with a range of ratings or for all', 'reviewx'); ?></label>
                                    </div>
                                    <label class="switch">
                                        <select class="rx-coupon-select-rating" name="coupon_select_rating" id="rx-select-coupon">
                                            <option value="all" <?php echo ($coupon_select_rating == 'all') ? 'selected' : '';
                                                                ?>> <?php echo __('For All Rating ', 'reviewx'); ?></option>
                                            <option value="5" <?php echo ($coupon_select_rating == '5') ? 'selected' : '';
                                                                ?>> <?php echo __('5', 'reviewx'); ?></option>
                                            <option value="4" <?php echo ($coupon_select_rating == '4') ? 'selected' : '';
                                                                ?>> <?php echo __('4>=5', 'reviewx'); ?></option>
                                            <option value="3" <?php echo ($coupon_select_rating == '3') ? 'selected' : '';
                                                                ?>> <?php echo __('3>=4', 'reviewx'); ?></option>
                                            <option value="2" <?php echo ($coupon_select_rating == '2') ? 'selected' : '';
                                                                ?>> <?php echo __('2>=3', 'reviewx'); ?></option>
                                            <option value="1" <?php echo ($coupon_select_rating == '1') ? 'selected' : '';
                                                                ?>> <?php echo __('1>=2', 'reviewx'); ?></option>
                                        </select>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="rx-coupon-bottom-devider"></div>
                        <div class="rx-coupon-page1-buttons">
                            <input type="button" class="rx-coupon-page1-next" value="Next" />
                        </div>
                    </div>
                    <div class="rx-coupon-page2" style="display: none;">
                        <div class="rx-coupon-page2-content">
                            <div class="rx-coupon-type-fld">
                                <h3 class="rx-coupon-field-heading"><?php _e('Coupon Type', 'reviewx'); ?></h3>
                                <div class="rx-coupon-tooltip-container">
                                    <select class="rx-coupon-type" name="rx_coupon_type" id="rx-coupon-type">
                                        <option value="existing_coupon" <?php echo ($rx_coupon_type == 'existing_coupon') ? 'selected' : '';
                                                                        ?>> <?php echo __('Existing Coupon', 'reviewx'); ?></option>
                                        <option value="generate_new" <?php echo ($rx_coupon_type == 'generate_new') ? 'selected' : '';
                                                                        ?>> <?php echo __('Generate New', 'reviewx'); ?></option>
                                    </select>
                                    <div class="rx-coupon-type-tooltip rx-coupon-tooltip">
                                        <img class="rx-coupon-img" style="height: 20px; width:20px;" src="<?php echo esc_url(assets('admin/images/info.png')); ?>;" alt="<?php esc_attr_e('Info', 'reviewx') ?>" />
                                        <span class="rx-coupon-type-tooltiptext"><?php _e('Choose to send an existing coupon or generate unique coupons', 'reviewx' );?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="rx-coupon-select-fld">
                                <h3 class="rx-coupon-field-heading"><?php echo __('Select Coupon', 'reviewx'); ?></h3>
                                <select class="rx-select-coupon" name="rx_coupon_selected_coupon" id="rx-select-coupon">
                                    <?php
                                    foreach ($wc_coupons as $coupon) {
                                    ?>
                                        <option value="<?php echo $coupon; ?>" <?php echo ($rx_coupon_selected_coupon == $coupon) ? 'selected' : '';
                                                                                ?>> <?php echo $coupon; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="rx-coupon-generate-new-fld" style="display: none;">
                                <div class="rx-coupon-code-prefix">
                                    <h3 class="rx-coupon-field-heading"><?php echo __('Coupon Prefix', 'reviewx'); ?></h3>
                                    <div class="rx-coupon-tooltip-container">
                                        <input class="rx-coupon-prefix <?php echo !$is_pro ? 'rx-coupon-pro' : ''; ?>" id="rx-coupon-prefix" type="text" name="rx_coupon_prefix" <?php echo !$is_pro ? 'disabled' : ''; ?> value="<?php echo $rx_coupon_prefix; ?>" />
                                        <?php if (!\ReviewX_Helper::is_pro()) : ?>
                                            <div class="rx-label rx-coupon-prefix-pro-lbl">
                                                <?php if (!ReviewX_Helper::is_pro()) :  // check is pro 
                                                ?>
                                                    <div class="rx-pro-checkbox">
                                                    <?php endif; ?>
                                                    <?php if (!ReviewX_Helper::is_pro()) :  // check is pro 
                                                    ?>
                                                        <sup class="rx-pro-label"><?php _e('Pro', 'reviewx'); ?></sup>
                                                    <?php endif; ?>
                                                    <?php if (!ReviewX_Helper::is_pro()) : // check is pro 
                                                    ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="rx-coupon-prefix-tooltip rx-coupon-tooltip">
                                            <img class="rx-coupon-img" style="height: 20px; width:20px;" src="<?php echo esc_url(assets('admin/images/info.png')); ?>;" alt="<?php esc_attr_e('Info', 'reviewx') ?>" />
                                            <span class="rx-coupon-prefix-tooltiptext"><?php _e( 'Coupon prefix will be added to uniquely generated coupons', 'reviewx' );?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="rx-coupon-discount-type">
                                    <h3 class="rx-coupon-field-heading"><?php echo __('Discount Type', 'reviewx'); ?></h3>
                                    <select class="rx_coupon_discunt_type" name="rx_coupon_discount_type" id="rx_coupon_discount_type">
                                        <option value="fixed_cart" <?php echo ($rx_coupon_discount_type == 'fixed_cart') ? 'selected' : '';
                                                                    ?>> <?php echo __('Fixed', 'reviewx'); ?></option>
                                        <option value="percent" <?php echo ($rx_coupon_discount_type == 'percent') ? 'selected' : '';
                                                                ?>> <?php echo __('Percentage', 'reviewx'); ?></option>
                                    </select>
                                </div>
                                <div class="rx-coupon-discount-amount">
                                    <h3 class="rx-coupon-field-heading"><?php echo __('Discount Amount', 'reviewx'); ?></h3>
                                    <div class="rx-coupon-tooltip-container">
                                        <input class="rx_coupon_discount_amount" id="rx-coupon-discount-amount" type="number" name="rx_coupon_discount_amount" value="<?php echo $rx_coupon_discount_amount; ?>" />
                                        <div class="rx-coupon-discount-amount-tooltip rx-coupon-tooltip">
                                            <img class="rx-coupon-img" style="height: 20px; width:20px;" src="<?php echo esc_url(assets('admin/images/info.png')); ?>;" alt="<?php esc_attr_e('Info', 'reviewx') ?>" />
                                            <span class="rx-coupon-discount-amount-tooltiptext"><?php _e('Value of the coupon', 'reviewx'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="rx-coupon-free-shipping-container">
                                    <h3 class="rx-coupon-field-heading"><?php echo __('Free Shipping', 'reviewx'); ?></h3>
                                    <div class="rx-coupon-tooltip-container">
                                        <div class="rx-free-shipping-switch">
                                            <label class="switch">
                                                <input class="rx-coupon-free-shipping" type="checkbox" name="rx_coupon_free_shipping" <?php echo ($rx_coupon_free_shipping == 'on') ? 'checked' : ''; ?>>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <div class="rx-coupon-free-shipping-tooltip">
                                            <img class="rx-coupon-img" style="height: 20px; width:20px;" src="<?php echo esc_url(assets('admin/images/info.png')); ?>;" alt="<?php esc_attr_e('Info', 'reviewx') ?>" />
                                            <span class="rx-coupon-free-shipping-tooltiptext"><?php _e('Enable the option if the coupon grants free shipping. A free shipping method must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting)', 'reviewx' );?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="rx-coupon-section-heading">
                                    <h2><?php echo __('Minimum Requirnments', 'reveiwx'); ?></h2>
                                </div>
                                <div class="rx-coupon-requirement-select">
                                    <div class="rx-coupon-non-select">
                                        <input type="radio" class="rx-coupon-no-requinment" name="rx_coupon_minimum_requinment" value="rx_coupon_no_requinment" <?php echo ($rx_coupon_minimum_requinment == 'rx_coupon_no_requinment') ? 'checked' : ''; ?> />
                                        <div class="rx-coupon-non-select-lebel"><?php echo __('No Minimum Requirnment', 'reviewx'); ?></div>
                                    </div>
                                    <div class="rx-coupon-minimum-purchase">
                                        <input type="radio" class="rx-coupon-purchase-requinment" name="rx_coupon_minimum_requinment" value="rx_coupon_purchase_requinment" <?php echo ($rx_coupon_minimum_requinment == 'rx_coupon_purchase_requinment') ? 'checked' : ''; ?> />
                                        <div class="rx-coupon-purchase-requinment"><?php echo __('Minimum Purchase Amount', 'reviewx'); ?></div>
                                    </div>
                                    <div class="rx-coupon-tooltip-container">
                                        <input class="rx-coupon-minimum-purchase" id="rx-coupon-minimum-purchase" name="rx_coupon_minimum_purchase_amount" type="number" value="<?php echo $rx_coupon_minimum_purchase_amount; ?>" />
                                        <div class="rx-coupon-minimum-purchase-tooltip">
                                            <img class="rx-coupon-img" style="height: 20px; width:20px;" src="<?php echo esc_url(assets('admin/images/info.png')); ?>;" alt="<?php esc_attr_e('Info', 'reviewx') ?>" />
                                            <span class="rx-coupon-minimum-purchase-tooltiptext"><?php _e( 'The minimum purchase amount to use the coupon', 'reviewx' );?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="rx-coupon-section-heading">
                                    <h2><?php echo __('Active Date', 'reviewx'); ?></h2>
                                </div>
                                <div class="rx-coupon-date">
                                    <div class="rx-coupon-start-date">
                                        <div class="rx-coupon-start-title">
                                            <?php echo __('Start Date', 'reviewx'); ?>
                                        </div>
                                        <input type="date" class="rx_coupon_start_date" name="rx_coupon_start_date" value="<?php echo $rx_coupon_start_date; ?>" />
                                    </div>
                                    <div class="rx-coupon-end-date-select">
                                        <input class="rx-coupon-end-date-check" type="checkbox" name="rx_coupon_end_date_select" <?php echo ($rx_coupon_end_date_select == 'on') ? 'checked' : ''; ?> />
                                        <p class="rx-coupon-end-date-title"><?php echo __('Set End Date', 'reviewx'); ?></p>
                                    </div>
                                    <div class="rx-coupon-end-date-container">
                                        <div class="rx-coupon-start-date">
                                            <div class="rx-coupon-end-title">
                                                <?php echo __('End Date', 'reviewx'); ?>
                                            </div>
                                            <div class="rx-coupon-tooltip-container">
                                                <input type="date" class="rx_coupon_end_date" name="rx_coupon_end_date" value="<?php echo $rx_coupon_end_date; ?>" />
                                                <div class="rx-coupon-end-date-tooltip rx-coupon-tooltip">
                                                    <img class="rx-coupon-img" style="height: 20px; width:20px;" src="<?php echo esc_url(assets('admin/images/info.png')); ?>;" alt="<?php esc_attr_e('Info', 'reviewx') ?>" />
                                                    <span class="rx-coupon-end-date-tooltiptext"><?php _e( 'The coupon will expire after this date', 'reviewx' );?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="rx-coupon-section-heading">
                                    <h2><?php echo __('Applies To', 'reviewx'); ?></h2>
                                </div>
                                <div class="rx-coupon-applies-section">
                                    <div class="rx-coupon-applies">
                                        <label class="rx-coupon-applies-lbl"> <?php _e('Specific Category', 'reviewx'); ?></label>
                                        <div class="rx-coupon-tooltip-container">
                                            <div class="rx-coupon-applies-select">
                                                <select class="rx-coupon-applies-cat-value" name="rx_coupon_cat_name" multiple="multiple" <?php echo !$is_pro ? "disabled" : ""; ?>>
                                                    <?php foreach ($categories as $catagory) : ?>
                                                        <option value="<?php echo $catagory->term_id; ?>" <?php echo (in_array($catagory->term_id, $rx_coupon_cat_name)) ? 'selected' : ''; ?>><?php echo $catagory->name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <?php if (!\ReviewX_Helper::is_pro()) : ?>
                                                    <div class="rx-label rx-applies-pro-label">
                                                        <?php if (!ReviewX_Helper::is_pro()) :  // check is pro 
                                                        ?>
                                                            <div class="rx-pro-checkbox">
                                                            <?php endif; ?>
                                                            <?php if (!ReviewX_Helper::is_pro()) :  // check is pro 
                                                            ?>
                                                                <sup class="rx-pro-label"><?php _e('Pro', 'reviewx'); ?></sup>
                                                            <?php endif; ?>
                                                            <?php if (!ReviewX_Helper::is_pro()) : // check is pro 
                                                            ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="rx-coupon-applies-cat-tooltip rx-coupon-tooltip">
                                                <img class="rx-coupon-img" style="height: 20px; width:20px;" src="<?php echo esc_url(assets('admin/images/info.png')); ?>;" alt="<?php esc_attr_e('Info', 'reviewx') ?>" />
                                                <span class="rx-coupon-applies-cat-tooltiptext"> <?php _e( 'Coupons will be applied to selected specific categories', 'reviewx' );?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rx-coupon-applies">
                                        <label class="rx-coupon-applies-lbl"><?php _e('Specific Product', 'reviewx'); ?> </label>
                                        <div class="rx-coupon-tooltip-container">
                                            <div class="rx-coupon-applies-select">
                                                <select class="rx-coupon-applies-product-value" name="rx_coupon_product_name" multiple="multiple" <?php echo !$is_pro ? "disabled" : ""; ?>>
                                                    <?php foreach ($wc_products as $product) : ?>
                                                        <option value="<?php echo $product->ID; ?>" <?php echo (in_array($product->ID, $rx_coupon_product_name)) ? 'selected' : ''; ?>><?php echo $product->post_title; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <?php if (!\ReviewX_Helper::is_pro()) : ?>
                                                    <div class="rx-label rx-applies-pro-label">
                                                        <?php if (!ReviewX_Helper::is_pro()) :  // check is pro 
                                                        ?>
                                                            <div class="rx-pro-checkbox">
                                                            <?php endif; ?>
                                                            <?php if (!ReviewX_Helper::is_pro()) :  // check is pro 
                                                            ?>
                                                                <sup class="rx-pro-label"><?php _e('Pro', 'reviewx'); ?></sup>
                                                            <?php endif; ?>
                                                            <?php if (!ReviewX_Helper::is_pro()) : // check is pro 
                                                            ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="rx-coupon-applies-product-tooltip rx-coupon-tooltip">
                                                <img class="rx-coupon-img" style="height: 20px; width:20px;" src="<?php echo esc_url(assets('admin/images/info.png')); ?>;" alt="<?php esc_attr_e('Info', 'reviewx') ?>" />
                                                <span class="rx-coupon-applies-product-tooltiptext"> <?php _e( 'Coupons will be applied to selected specific products', 'reviewx' );?> </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="rx-coupon-section-heading">
                                    <h2><?php echo __('Limit', 'reviewx'); ?></h2>
                                </div>
                                <div class="rx-coupon-limit">
                                    <div class="rx-coupon-one-use">
                                        <input type="checkbox" name="rx_coupon_use_limit_one" <?php echo ($rx_coupon_use_limit_one == 'on') ? 'checked' : ''; ?>>
                                        <label class="rx-coupon-limit-label"><?php echo __('Limit to one use per customer', 'reviewx'); ?></label>
                                    </div>
                                    <div class="rx-coupon-use-limit">
                                        <div class="rx-coupon-per-use-limit">
                                            <input type="checkbox" class="rx_coupon_use_limit_multiple" name="rx_coupon_use_limit_multiple" <?php echo ($rx_coupon_use_limit_multiple == 'on') ? 'checked' : ''; ?>>
                                            <label class="rx-coupon-limit-label"><?php echo __('Limit number of times this discount can be used in total', 'reviewx'); ?></label>
                                        </div>
                                        <input type="number" class="rx_coupon_use_limit_value" name="rx_coupon_use_limit_value" value="<?php echo $rx_coupon_use_limit_value; ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="rx-coupon-bottom-devider"></div>
                        <div class="rx-coupon-page2-buttons">
                            <input type="button" class="rx-coupon-page2-prev" value="Prev" />
                            <input type="button" class="rx-coupon-page2-next" value="Next" />
                        </div>
                    </div>
                    <div class="rx-coupon-page3" style="display: none;">
                        <div class="rx-coupon-page3-content">
                            <div class="rx-coupon-email-title">
                                <label class="rx-coupon-email-title"><?php echo __('Email Title', 'reviewx'); ?></label>
                                <input type="text" class="rx-coupon-email-title" placeholder="Tile test" name="rx_coupon_email_title" value="<?php echo ($rx_coupon_email_title) ? $rx_coupon_email_title : 'You got a new coupon code!'; ?>" />
                            </div>
                            <div class="rx-email-coupon-content">
                                <label class="rx-coupon-email-content"> <?php echo __('Email Content', 'reviewx'); ?></label>
                                <?php wp_editor(($email_content) ? $email_content : $dafault_email_conent, 'distribution', array('theme_advanced_buttons1' => 'bold, italic, ul, pH, pH_min', "media_buttons" => false, "textarea_rows" => 8, "tabindex" => 4)); ?>
                            </div>
                        </div>
                        <div class="rx-coupon-bottom-devider"></div>
                        <div class="rx-coupon-page3-buttons">
                            <input type="button" class="rx-coupon-page3-prev" value="Prev" />
                            <input type="hidden" id="rxdiscount" name="rxdiscount" value="<?php echo wp_create_nonce('create'); ?>">
                            <input type="submit" class="rx-coupon-page3-next" value="Save" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>