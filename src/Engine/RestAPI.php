<?php
namespace Bookster_Extra_Options\Engine;

use Bookster_Extra_Options\Features\Utils\SingletonTrait;
use Bookster_Extra_Options\Controllers\ExtraController;

/**
 * Service Extra Rest API
 */
class RestAPI {
    use SingletonTrait;

    protected function __construct() {
        add_action( 'rest_api_init', [ $this, 'add_extra_endpoint' ] );
    }

    public function add_extra_endpoint() {
        ExtraController::get_instance();
    }
}
