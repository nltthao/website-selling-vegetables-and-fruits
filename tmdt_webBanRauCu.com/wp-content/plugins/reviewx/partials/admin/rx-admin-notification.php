<?php
    $rx_admin_notification = get_option( 'rx_admin_notification' );
    $checked = "";
    $is_checked = false;
    $disabled = "";

    if( $rx_admin_notification == 1 ) {
        $checked = "checked";
        $is_checked = 1;
    }

    if( ! $rx_admin_notification ) {
        $disabled = "disabled";
        $is_checked = 0;
    }

    $rx_users_email = get_option('admin_email');
    $rx_selected_rating = get_option('rx_selected_notification_rating');
    $rx_selected_email = get_option('rx_notification_admin_email');
?>

<div class="rx-settings-right">
    <div class="rx-sidebar">
        <div class="rx-sidebar-block rx-notification-block">
            <h3 class="rx-meta-section-title">
                <?php echo __( 'Review Admin Notification Email Setting','reviewx' ); ?>   
            </h3>
            <div class="rx-notification-field-heading-big-title">
                <div class="rx-admin-notification-wrap">
                    <h3 class="rx-admin-notification-heading"> <?php echo __('Admin Notification', 'reviewx' ); ?> </h3>
                    <h3 class="rx-admin-notification-heading"> <?php echo __('When to Notify', 'reviewx' ); ?> </h3>
                    <h3 class="rx-admin-notification-heading"> <?php echo __('Admin Email', 'reviewx' ); ?> </h3>                  
                </div>
                <div class="rx-select-rating-wrap">
                    <div class="rx-control rx-notification-mail-switch">
                        <label class="switch">
                            <input id="rx-notification-email-swither" class="rx_reminder_email_swither" type="checkbox" name="rx_option_review_admin_notification" <?php echo $checked; ?> enable-admin-notification=<?php echo  $is_checked; ?> />
                            <span class="slider round"></span>
                        </label>
                    </div>  
                    
                    <div class="rx-rating-select-fld" style="display: flex;">
                        <select class="rx-select-rating" id="rx-select-rating" <?php echo $disabled; ?> field-enable-type=<?php echo $checked; ?> >
                            <option value="all" <?php echo ($rx_selected_rating == 'all') ? 'selected': ''; ?> > <?php echo __('For All Rating ', 'reviewx' ); ?></option>
                            <option value="five" <?php echo ($rx_selected_rating == 'five') ? 'selected': ''; ?> > <?php echo __('Only for 5 Star', 'reviewx' ); ?></option>
                            <option value="four" <?php echo ($rx_selected_rating == 'four') ? 'selected': ''; ?> > <?php echo __('Greater then 4 Star', 'reviewx' ); ?></option>
                            <option value="three" <?php echo ($rx_selected_rating == 'three') ? 'selected': ''; ?> > <?php echo __('Less then 3 Star', 'reviewx' ); ?></option>
                        </select>
                    </div>

                    <div class="rx-admin-email-warp">  
                        <input class="rx-admin-email-list" id="rx-admin-email-list" type="text" value= <?php echo ( ! $rx_selected_email ) ? $rx_users_email : $rx_selected_email; ?> <?php echo $disabled; ?> field-enable-type= <?php echo $checked; ?> />                  
                    </div>
                </div>
            </div>
            <div class="rx-notification-btn-wrap">
                <button class="rx-notification-setting-btn" type="button"><?php echo __( 'Save', 'reviewx' ); ?></button>
            </div>
            <div class="rx-admin-notification-notice">
                <p class="rx-admin-notification-notice-content">
                    <?php echo __('To use email feature you have to make sure that your WordPress mail delivery is working properly. Don’t worry you can test that using ', 'reviewx' ); ?>
                    <a href="https://wordpress.org/plugins/fluent-smtp/" target="_blank">Fluent SMTP</a> 
                </p>
            </div>
        </div>
    </div>
</div>