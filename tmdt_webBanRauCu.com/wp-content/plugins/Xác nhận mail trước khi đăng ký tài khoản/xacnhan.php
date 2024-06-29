<?php
/*
Plugin Name: Xác nhận mail trước khi đăng ký tài khoản woocommerce
Plugin URI: https://webantam.com
Description: Bật plugin này lên nếu bạn muốn Xác nhận mail trước khi đăng ký tài khoản woocommerce
Version: 1.1
Author: Web An Tâm
Author URI: https://webantam.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
 // them xac nhan email vào funtion.php webantam.com

 add_filter( "um_get_option_filter__checkmail_email_on", "um_get_option_filter__checkmail_email_on_custom", 10, 1 );

 function um_get_option_filter__checkmail_email_on_custom( $value ) {
 
     if( in_array( UM()->form()->form_id, array( '1234', '5678' ) )) return false;
     return $value;
 }
 
 
 // // them xac nhan email khi dang ky tai khoan
 
 function wc_registration_redirect( $redirect_to ) {     // prevents the user from logging in automatically after registering their account
     wp_logout();
     wp_redirect( '/verify/?n=1');                       // redirects to a confirmation message
     exit;
 }
 
 function wp_authenticate_user( $userdata ) {            // when the user logs in, checks whether their email is verified
     $has_activation_status = get_user_meta($userdata->ID, 'is_activated', false);
     if ($has_activation_status) {                           // checks if this is an older account without activation status; skips the rest of the function if it is
         $isActivated = get_user_meta($userdata->ID, 'is_activated', true);
         if ( !$isActivated ) {
             my_user_register( $userdata->ID );              // resends the activation mail if the account is not activated
             $userdata = new WP_Error(
                 'my_theme_confirmation_error',
                 __( '<strong>Error:</strong> Tài khoản của bạn phải được kích hoạt trước khi bạn có thể đăng nhập. Vui lòng nhấp vào liên kết trong email kích hoạt đã được gửi cho bạn.<br /> Nếu bạn không nhận được email kích hoạt trong vòng vài phút, hãy kiểm tra thư mục thư rác hoặc <a href="/verify/?u='.$userdata->ID.'">bấm vào đây để gửi lại</a>.' )
             );
         }
     }
     return $userdata;
 }
 
 function my_user_register($user_id) {               // when a user registers, sends them an email to verify their account
     $user_info = get_userdata($user_id);                                            // gets user data
     $code = md5(time());                                                            // creates md5 code to verify later
     $string = array('id'=>$user_id, 'code'=>$code);                                 // makes it into a code to send it to user via email
     update_user_meta($user_id, 'is_activated', 0);                                  // creates activation code and activation status in the database
     update_user_meta($user_id, 'activationcode', $code);
     $url = get_site_url(). '/verify/?p=' .base64_encode( serialize($string));       // creates the activation url
     $html = ( 'Vui lòng nhấn vào <a href="'.$url.'">đây</a> để xác minh địa chỉ email của bạn và hoàn tất quá trình đăng ký.' ); // This is the html template for your email message body
     wc_mail($user_info->user_email, __( 'Xác nhận tài khoản' ), $html);          // sends the email to the user
 }
 
 function my_init(){                                 // handles all this verification stuff
     if(isset($_GET['p'])){                                                  // If accessed via an authentification link
         $data = unserialize(base64_decode($_GET['p']));
         $code = get_user_meta($data['id'], 'activationcode', true);
         $isActivated = get_user_meta($data['id'], 'is_activated', true);    // checks if the account has already been activated. We're doing this to prevent someone from logging in with an outdated confirmation link
         if( $isActivated ) {                                                // tạo thông báo lỗi nếu tài khoản đã hoạt động
             wc_add_notice( __( 'Tài khoản này đã được kích hoạt. Vui lòng đăng nhập bằng tên người dùng và mật khẩu của bạn.' ), 'error' );
         }
         else {
             if($code == $data['code']){                                     // checks whether the decoded code given is the same as the one in the data base
                 update_user_meta($data['id'], 'is_activated', 1);           // updates the database upon successful activation
                 $user_id = $data['id'];                                     // logs the user in
                 $user = get_user_by( 'id', $user_id ); 
                 if( $user ) {
                     wp_set_current_user( $user_id, $user->user_login );
                     wp_set_auth_cookie( $user_id );
                     do_action( 'wp_login', $user->user_login, $user );
                 }
                 wc_add_notice( __( '<strong>Success:</strong> Tài khoản của bạn đã được kích hoạt thành công! Bạn đã đăng nhập và bây giờ có thể sử dụng tài khoản của bạn tại trang web của chúng tôi.' ), 'notice' );
             } else {
                 wc_add_notice( __( '<strong>Error:</strong> Kích hoạt tài khoản không thành công. Vui lòng thử lại sau vài phút hoặc <a href="/verify/?u='.$userdata->ID.'">gửi lại email kích hoạt</a>.<br />Xin lưu ý rằng mọi liên kết kích hoạt đã gửi trước đó sẽ mất hiệu lực ngay khi email kích hoạt mới được gửi.<br />Nếu việc xác minh không thành công nhiều lần, vui lòng liên hệ với quản trị viên của chúng tôi.' ), 'error' );
             }
         }
     }
     if(isset($_GET['u'])){                                          // If resending confirmation mail
         my_user_register($_GET['u']);
         wc_add_notice( __( 'Email kích hoạt của bạn đã được gửi lại. Vui lòng kiểm tra email và thư mục thư rác của bạn.' ), 'notice' );
     }
     if(isset($_GET['n'])){                                          // If account has been freshly created
         wc_add_notice( __( 'Cảm ơn bạn đã tạo tài khoản . Bạn sẽ cần phải xác nhận địa chỉ mail của mình để kích hoạt tài khoản. Một mail chứa liên kết kích hoạt đã được gửi đến địa chỉ mail của bạn. Nếu email không đến trong vòng vài phút, hãy kiểm tra thư mục thư rác của bạn.' ), 'notice' );
     }
 }
 
 // the hooks to make it all work
 add_action( 'init', 'my_init' );
 add_filter('woocommerce_registration_redirect', 'wc_registration_redirect');
 add_filter('wp_authenticate_user', 'wp_authenticate_user',10,2);
 add_action('user_register', 'my_user_register',10,2);
 
 
  // them xac nhan email vào funtion.php webantam.com end

