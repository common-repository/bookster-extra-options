<?php
namespace Bookster_Extra_Options;

use Bookster_Extra_Options\Features\Utils\SingletonTrait;
use Bookster\Features\Enums\AddonStatusEnum;

/** Bookster Paypal Gateway Initializer */
class Initialize {
    use SingletonTrait;

    /** The Constructor that load the engine classes */
    protected function __construct() {
        \Bookster_Extra_Options\Engine\ActDeact::get_instance();
        \Bookster_Extra_Options\Engine\Enqueue::get_instance();
        \Bookster_Extra_Options\Engine\RestAPI::get_instance();
        \Bookster_Extra_Options\Engine\ExtraBookingRequestLogic::get_instance();
        \Bookster_Extra_Options\Engine\ExtendBookingModel::get_instance();

        add_filter( 'bookster_addon_infos', [ $this, 'add_activated_addons' ] );
    }

    public function add_activated_addons( $addon_infos ) {
        $addon_infos = array_map(
            function( $addon_info ) {
                if ( 'bookster-extra-options' === $addon_info['slug'] ) {
                    $addon_info['installStatus']  = AddonStatusEnum::ACTIVATED;
                    $addon_info['currentVersion'] = BOOKSTER_EXTRA_OPTIONS_VERSION;
                }
                return $addon_info;
            },
            $addon_infos
        );

        return $addon_infos;
    }
}
