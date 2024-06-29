<?php 
    // $current_tab = get_option( '_rx_builder_current_tab' );
    
    // if( ! $current_tab ) {
    $current_tab = 'go_admin_notification_tab';
    // }

    $totaltabs = count( $tabs );
    $position = intval( array_search( $current_tab, array_keys( $tabs) ) + 1 );

?>
<div class="rx-metabox-wrapper">

    <div class="rx-settings-header">

        <div class="rx-header-left">
            <div class="rx-admin-header">
                <img src="<?php echo esc_url(assets('admin/images/ReviewX.svg')); ?>" alt="<?php echo esc_attr__('ReviewX', 'reviewx')?>">
                <h2><?php _e( 'Advanced Multi-criteria Rating & Reviews for WooCommerce', 'reviewx' ); ?></h2>
            </div>
        </div>

        <div class="rx-header-right">
            <span><?php _e( 'ReviewX', 'reviewx' ); ?>: <strong><?php echo esc_html( REVIEWX_VERSION ); ?></strong></span>
            <?php 
                if( class_exists('ReviewXPro') ):
            ?>
            <span><?php _e( 'ReviewX Pro', 'reviewx' ); ?>: <strong><?php echo esc_html( REVIEWX_PRO_VERSION ); ?></strong></span>
            <?php endif; ?>
        </div>

    </div>

    <div class="rx-metatab-menu">
    <?php if( ! empty( $tabs ) ) : ?>
        <ul>
            <?php 
                $tid = 1;
                $checker = 1;
                $tabids = array();
                foreach( $tabs as $id => $tab ) {
                    $tabids[]   = $id;
                    $active     = $current_tab === $id ? ' active' : '';                    
                    $class      = isset( $tab['icon'] ) ? ' rx-has-icon rx-general-setting-li' : '';
                    $class      .= $active;

                    if( $position > $id ) {
                        $completeClass = ' rx-complete';
                        $class      .= $completeClass;
                    }
                    ?>
                        <li data-tabid="<?php echo $tid++; ?>" class="<?php echo esc_attr( $class ); ?>" data-tab="<?php echo esc_attr( $id ); ?>">
                            <?php if( isset( $tab['icon'] ) ) : ?>
                                <span class="rx-menu-icon">
                                    <?php if( $id == 'go_admin_notification_tab' ) { ?>
                                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                viewBox="0 0 100 100" style="enable-background:new 0 0 100 100;" xml:space="preserve">

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
                                                c-11.2-0.2-20.6,9-20.7,20.2C29.6,61.1,38.6,70.3,50,70.5z"/>
                                            </svg>                                    
                                        <?php } elseif( $id = 'go_license_tab' ) { ?>
                                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	                                            viewBox="0 0 100 100" style="enable-background:new 0 0 100 100;" xml:space="preserve">
                                                <g>
                                                    <circle class="st0" cx="50" cy="28.1" r="28.1"/>
                                                    <path class="st0" d="M29.9,59.5c5.8,3.8,12.7,5.9,20.1,5.9s14.3-2.2,20.1-5.9V100L50,82.7L29.9,100V59.5z"/>
                                                </g>
                                                </svg>
                                        <?php } ?> 
                                </span>
                            <?php endif; ?>
                            <span class="rx-menu-title"><?php echo esc_html($tab['title']); ?></span>
                        </li>
                    <?php
                }
            ?>
        </ul>
    <?php endif; ?>    
    </div>
    <div class="rx-meta-main-container">
        <div class="rx-meta-contents rx-metatab-wrapper" data-totaltab="<?php echo esc_attr( $totaltabs ); ?>">
            <div class="rx-form-builder-section">
                <form method="post" id="rx-builder-form" action="<?php echo self::get_form_action( '', false ); ?>">                    
                    <input type="hidden" name="rx_builder_current_page" id="rx_builder_current_page" value="rx-settings">
                    <?php \ReviewX\Modules\OptimisticLock::input(\ReviewX\Constants\LockForm::SETTINGS); ?>
                    <input type="hidden" name="rx-tab-nonce" class="rx-tab-nonce" value="<?php echo wp_create_nonce( "special-string" ); ?>">
                    <?php 
                        wp_nonce_field( $builder_args['id'], $builder_args['id'] . '_nonce' );
                        $tabid = 1;
                        $is_pro = false;
                        foreach( $tabs as $id => $tab  ) {

                            $active = $current_tab === $id ? ' active ' : 'active';                                    
                            $sections = ReviewX_Helper::sorter( $tab['sections'], 'priority', 'ASC' );   
                            ?>

                            <div id="rx-<?php echo esc_attr( $id ) ?>" class="rx-metatab-content <?php echo esc_attr( $active ); ?>">
                            <?php
                                if( $id == 'content_tab' ):
                                    echo '<div class="rx-meta-content-tab-parent-section">';
                                endif;  
                            ?>

                            <?php 
                                do_action( 'rx_builder_before_tab', $id, $tab );   
                                foreach( $sections as $sec_id => $section ) {
                                    /**
                                     * This will go with section_id, and tab_id
                                     */
                                    do_action( 'rx_builder_before_section', $sec_id, $section, $id );
                                    if( isset( $section['fields'] ) ) : 
                                        $fields = ReviewX_Helper::sorter( $section['fields'], 'priority', 'ASC' );
                                        if( ! empty( $fields ) )  :
                                        $is_pro = isset( $section['is_pro'] ) ? $section['is_pro'] : false;
                                    ?>
                                        <div class="rx-meta-parent-section">
                                            <div id="rx-meta-section-<?php echo esc_attr( $sec_id ); ?>" class="rx-meta-section">                                        
                                                <h3 class="rx-meta-section-title">
                                                    <?php echo esc_html( $section['title'] ); ?>    
                                                </h3>  
                                                <table>
                                                    <?php                                                                                
                                                        foreach( $fields as $key => $field ) {
                                                            \ReviewX\Controllers\Admin\Core\ReviewxMetaBox::render_option_field( $key, $field, '', $id, $is_pro );
                                                        }
                                                    ?>
                                                </table>                                     
                                            </div>
                                        </div>
                                    <?php
                                        endif;
                                    endif;
                                    if( isset( $section['view'] ) ) : 
                                        do_action( 'rx_builder_before_section_view', $sec_id, $section, $id );
                                            call_user_func( $section['view'] );
                                        do_action( 'rx_builder_after_section_view', $sec_id, $section, $id );
                                    endif;
                                    /**
                                     * This will go with section_id, and tab_id
                                     */
                                    do_action( 'rx_builder_after_section', $sec_id, $section, $id );
                                }
                            ?>
                            <?php
                                if( $id == 'content_tab' ):
                                    echo '</div>';
                                endif;  
                            ?>
                            <div class="quick-builder-submit-btn-wrap">
                                <?php
                                $tabid = ++$tabid;
                                if( !class_exists('ReviewXPro') ) {
                                if( $tabid <= $totaltabs ) {
                                    ?>
                                    <input type="hidden" name="rx-setting-nonce" class="rx-setting-nonce" value="<?php echo wp_create_nonce( "special-string" ); ?>">
                                    <button class="rx_save_setting_tab quick-builder-submit-btn" type="button" data-tabid="<?php echo esc_attr($tabid); ?>"><?php esc_html_e( $id == 'email_tab' ? 'Save & Send email' : 'Save', 'reviewx' );?></button>
                                <?php } } ?>
                                <?php if( class_exists('ReviewXPro') ) {
                                if( $tabid < $totaltabs ) {
                                    ?>
                                    <input type="hidden" name="rx-setting-nonce" class="rx-setting-nonce" value="<?php echo wp_create_nonce( "special-string" ); ?>">
                                    <button class="rx_save_setting_tab quick-builder-submit-btn" type="button" data-tabid="<?php echo esc_attr($tabid); ?>"><?php esc_html_e( $id == 'email_tab' ? 'Save & Send email' : 'Save', 'reviewx' );?></button>
                                <?php } } ?>    
                            </div>
                            <?php do_action( 'rx_builder_after_tab', $id, $tab ); ?>
                            </div>
                            <?php
                        }
                    ?>
                </form>
            </div>
            <!-- Load license section -->
            <div class="rx-license-section">
                <div class="rx-sidebar-block">
                    <div class="rx-admin-sidebar-logo">
                        <img alt="<?php _e( 'ReviewX', 'reviewx' ) ?>" src="<?php echo esc_url(assets('admin/images/ReviewX_icon.svg')); ?>">
                    </div>
                    <div class="rx-admin-sidebar-cta">
                        <?php     
                            if( class_exists('ReviewXPro') ) {
                                printf( __( '<a rel="nofollow" href="%s" target="_blank">Manage License</a>', 'reviewx' ), esc_url('https://reviewx.io/checkout/your-account/') );
                            }else{
                                printf( __( '<a rel="nofollow" href="%s" target="_blank">Upgrade to Pro</a>', 'reviewx' ), esc_url('https://reviewx.io/upgrade/reviewx-pro') );
                            }
                        ?>                                  
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
        include_once REVIEWX_PARTIALS_PATH . 'admin/footer-info-block.php';
    ?>
</div>