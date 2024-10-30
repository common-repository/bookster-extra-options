<?php
/**
 * Bookster Service Extra
 *
 * @package             Bookster_Extra_Options
 * @author              WPBookster
 * @copyright           Copyright 2023-2024, Bookster
 * @license             http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or later
 *
 * @wordpress-plugin
 * Plugin Name:         Bookster Extra Options
 * Plugin URI:          https://wpbookster.com/
 * Requires Plugins:    bookster
 * Description:         Official Bookster Service Extra addon - Offer Extra Products, Equipment, Variances to your Customers with Additional Fees
 * Version:             1.0.2
 * Requires at least:   5.2
 * Requires PHP:        7.2
 * Author:              WPBookster
 * Author URI:          https://wpbookster.com/about
 * Text Domain:         bookster-extra-options
 * License:             GPL v3 or later
 * License URI:         https://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'We\'re sorry, but you can not directly access this file.' );
}

define( 'BOOKSTER_EXTRA_OPTIONS_VERSION', '1.0.2' );

define( 'BOOKSTER_EXTRA_OPTIONS_PLUGIN_FILE', __FILE__ );
define( 'BOOKSTER_EXTRA_OPTIONS_PLUGIN_PATH', plugin_dir_path( BOOKSTER_EXTRA_OPTIONS_PLUGIN_FILE ) );
define( 'BOOKSTER_EXTRA_OPTIONS_PLUGIN_URL', plugin_dir_url( BOOKSTER_EXTRA_OPTIONS_PLUGIN_FILE ) );
define( 'BOOKSTER_EXTRA_OPTIONS_PLUGIN_BASENAME', plugin_basename( BOOKSTER_EXTRA_OPTIONS_PLUGIN_FILE ) );

add_action(
    'init',
    function() {
        load_plugin_textdomain( 'bookster-extra-options', false, dirname( BOOKSTER_EXTRA_OPTIONS_PLUGIN_BASENAME ) . '/languages' );
    }
);

function bookster_extra_options_activate( bool $network_wide ) {
    if ( class_exists( '\Bookster_Extra_Options\Engine\ActDeact' ) ) {
        \Bookster_Extra_Options\Engine\ActDeact::activate( $network_wide );
    }
}
function bookster_extra_options_deactivate( bool $network_wide ) {
    if ( class_exists( '\Bookster_Extra_Options\Engine\ActDeact' ) ) {
        \Bookster_Extra_Options\Engine\ActDeact::deactivate( $network_wide );
    }
}
function bookster_extra_options_uninstall() {
    if ( class_exists( '\Bookster_Extra_Options\Engine\ActDeact' ) ) {
        \Bookster_Extra_Options\Engine\ActDeact::uninstall();
    }
}
register_activation_hook( BOOKSTER_EXTRA_OPTIONS_PLUGIN_FILE, 'bookster_extra_options_activate' );
register_deactivation_hook( BOOKSTER_EXTRA_OPTIONS_PLUGIN_FILE, 'bookster_extra_options_deactivate' );
register_uninstall_hook( BOOKSTER_EXTRA_OPTIONS_PLUGIN_FILE, 'bookster_extra_options_uninstall' );

require_once BOOKSTER_EXTRA_OPTIONS_PLUGIN_PATH . 'vendor/autoload.php';
if ( ! wp_installing() ) {
    add_action(
        'plugins_loaded',
        function () {
            /** Require Dependencies: (min.any < ver < max.any) => OK */
            $max_bookster_version = '3.0';
            $min_bookster_version = '2.0';

            if ( ! defined( 'BOOKSTER_VERSION' ) ) {
                add_action(
                    'admin_notices',
                    function() {
                        echo wp_kses_post(
                            sprintf(
                                '<div class="notice notice-error"><p>%s</p></div>',
                                __( '"Bookster - Extra Options" requires Bookster plugin installed and activated.', 'bookster-extra-options' )
                            )
                        );
                    }
                );

                return;
            }

            if ( ! version_compare( $min_bookster_version . '.any', BOOKSTER_VERSION, '<' ) ) {
                add_action(
                    'admin_notices',
                    function() use ( $min_bookster_version ) {
                        $notice = sprintf(
                            /* translators: %1$s - Bookster Extra Version. %2$s - Minimum Supporting Bookster Version */
                            __( '"Bookster - Extra %1$s" requires Bookster version %2$s. Please update Bookster plugin!', 'bookster-extra-options' ),
                            BOOKSTER_EXTRA_OPTIONS_VERSION,
                            $min_bookster_version
                        );

                        echo wp_kses_post(
                            sprintf(
                                '<div class="notice notice-error"><p>%s</p></div>',
                                $notice
                            )
                        );
                    }
                );

                return;
            }//end if

            if ( ! version_compare( BOOKSTER_VERSION, $max_bookster_version . '.any', '<' ) ) {
                add_action(
                    'admin_notices',
                    function() {
                        $notice = sprintf(
                            /* translators: %s - Bookster Version */
                            __( '"Bookster %s" requires new addon version. Please update Bookster Extra Options!', 'bookster-extra-options' ),
                            BOOKSTER_VERSION
                        );

                        echo wp_kses_post(
                            sprintf(
                                '<div class="notice notice-error"><p>%s</p></div>',
                                $notice
                            )
                        );
                    }
                );

                return;
            }//end if

            // Make sure Bookster classes loaded.
            if ( class_exists( '\Bookster\Initialize' ) ) {
                \Bookster_Extra_Options\Initialize::get_instance();
            }
        }
    );
}//end if
