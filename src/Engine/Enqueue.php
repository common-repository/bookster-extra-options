<?php
namespace Bookster_Extra_Options\Engine;

use Bookster_Extra_Options\Features\Utils\SingletonTrait;
use Bookster_Extra_Options\Services\ExtrasService;
use Bookster\Features\Scripts\ScriptName;
use Bookster\Features\Scripts\EnqueueLogic;

/**
 * Enqueue
 *
 * @method static Enqueue get_instance()
 */
class Enqueue {
    use SingletonTrait;

    public const STYLE_EXTRA     = 'bookster/style/extra';
    public const FRONTEND_SCRIPT = 'bookster/module/bookster-extra-options/frontend';
    public const ADMIN_SCRIPT    = 'bookster/module/bookster-extra-options/admin';

    /** @var EnqueueLogic */
    private $enqueue_logic;
    private $extras_service;

    protected function __construct() {
        $this->enqueue_logic  = EnqueueLogic::get_instance();
        $this->extras_service = ExtrasService::get_instance();

        add_action( 'bookster_after_enqueue_script', [ $this, 'localized_active_extra' ], 10, 1 );
        if ( ! $this->enqueue_logic->is_prod() ) {
            return;
        }

        add_action( 'init', [ $this, 'register_all_scripts' ] );

        add_filter( 'bookster_scripts_dependencies', [ $this, 'add_extra_script' ], 10, 1 );
        add_action( 'bookster_after_enqueue_script', [ $this, 'add_extra_style' ], 10, 0 );
    }

    public function register_all_scripts() {
        wp_register_style( self::STYLE_EXTRA, BOOKSTER_EXTRA_OPTIONS_PLUGIN_URL . 'assets/dist/extra-options/style.css', [], BOOKSTER_EXTRA_OPTIONS_VERSION );

        $deps = [ ScriptName::LIB_CORE, ScriptName::LIB_ICONS, ScriptName::LIB_COMPONENTS, ScriptName::LIB_BOOKING, 'react', 'react-dom', 'wp-hooks', 'wp-i18n' ];

        wp_register_script( self::FRONTEND_SCRIPT, BOOKSTER_EXTRA_OPTIONS_PLUGIN_URL . 'assets/dist/extra-options/frontend.js', $deps, BOOKSTER_EXTRA_OPTIONS_VERSION, false );
        wp_set_script_translations( self::FRONTEND_SCRIPT, 'bookster-extra-options', BOOKSTER_EXTRA_OPTIONS_PLUGIN_PATH . 'languages' );

        wp_register_script( self::ADMIN_SCRIPT, BOOKSTER_EXTRA_OPTIONS_PLUGIN_URL . 'assets/dist/extra-options/admin.js', $deps, BOOKSTER_EXTRA_OPTIONS_VERSION, false );
        wp_set_script_translations( self::ADMIN_SCRIPT, 'bookster-extra-options', BOOKSTER_EXTRA_OPTIONS_PLUGIN_PATH . 'languages' );
    }

    public function add_extra_script( $deps ) {
        if ( is_admin() ) {
            $deps[] = self::ADMIN_SCRIPT;
        } else {
            $deps[] = self::FRONTEND_SCRIPT;
        }
        return $deps;
    }

    public function add_extra_style() {
        wp_enqueue_style( self::STYLE_EXTRA );
    }

    public function localized_active_extra( $script_name ) {
        if ( ! is_admin() ) {
            $this->extras_service->localized_active_extras( $script_name );
        }
    }
}
