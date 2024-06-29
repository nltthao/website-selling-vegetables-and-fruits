<?php

namespace ReviewX\Modules;

class VersionUpdater {

    /**
     * Add a flag in DB,
     * so that backend run compatibilities for new plugin version.
     *
     * @param  \WP_Upgrader $upgrader
     * @param  array  $options
     * @return void
     */
    public static function update_plugin_v147(\WP_Upgrader $upgrader, $options)
    {
        if (
            is_array( $options ) &&
            $options['action'] == 'update' &&
            $options['type'] == 'plugin' &&
            isset( $options['plugins'] ) &&
            is_array( $options['plugins'] )
        ) {
            foreach ( $options['plugins'] as $plugin ) {
                if ( $plugin == plugin_basename( REVIEWX_FILE ) ) {
                    update_option( 'reviewx_update_v147', 1 );    // adding flug for 1.4.7
                    break;
                }
            }
        }
    }

    public static function delete_email_template()
    {
        if ( get_option( 'reviewx_update_v147' ) ) {
            delete_option( '_rx_option_email_editor' );
            delete_option( 'reviewx_update_v147' );
        }
    }

}
