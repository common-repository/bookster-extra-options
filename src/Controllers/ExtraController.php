<?php
namespace Bookster_Extra_Options\Controllers;

use Bookster\Controllers\BaseRestController;
use Bookster\Features\Auth\RestAuth;
use Bookster_Extra_Options\Features\Utils\SingletonTrait;
use Bookster_Extra_Options\Services\ExtrasService;

/**
 * Extra Controller
 *
 * @method static ExtraController get_instance()
 */
class ExtraController extends BaseRestController {
    use SingletonTrait;

    /** @var ExtrasService */
    private $extras_service;

    protected function __construct() {
        $this->extras_service = ExtrasService::get_instance();
        $this->init_hooks();
    }

    protected function init_hooks() {
        register_rest_route(
            self::REST_NAMESPACE,
            '/extras/public',
            [
                [
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'exec_get_all_extras' ],
                    'permission_callback' => '__return_true',
                ],
            ]
        );

        register_rest_route(
            self::REST_NAMESPACE,
            '/extras',
            [
                [
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => [ $this, 'exec_post_extra' ],
                    'permission_callback' => [ RestAuth::class, 'require_manage_shop_records_cap' ],
                ],
                [
                    'methods'             => 'PATCH',
                    'callback'            => [ $this, 'exec_patch_extras' ],
                    'permission_callback' => [ RestAuth::class, 'require_manage_shop_records_cap' ],
                ],
            ]
        );

        $extra_id_args = [
            'extra_id' => [
                'type'     => 'string',
                'required' => true,
            ],
        ];
        register_rest_route(
            self::REST_NAMESPACE,
            '/extras/(?P<extra_id>[a-z0-9_-]+)',
            [
                [
                    'methods'             => 'PATCH',
                    'callback'            => [ $this, 'exec_patch_extra' ],
                    'permission_callback' => [ RestAuth::class, 'require_manage_shop_records_cap' ],
                    'args'                => $extra_id_args,
                ],
                [
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'callback'            => [ $this, 'exec_delete_extra' ],
                    'permission_callback' => [ RestAuth::class, 'require_manage_shop_records_cap' ],
                    'args'                => $extra_id_args,
                ],
            ]
        );
    }

    public function get_all_extras() {
        $extras = $this->extras_service->get_data();
        $data   = [];
        foreach ( $extras as $extra ) {
            if ( ! empty( $extra['cover_id'] ) ) {
                $extra['transient_cover_url'] = wp_get_attachment_image_url( $extra['cover_id'], 'medium_large' );
            }
            array_push( $data, $extra );
        }
        return [
            'data' => $data,
        ];
    }

    public function post_extra( \WP_REST_Request $request ) {
        $args = $request->get_json_params();
        $this->extras_service->insert( $args );
        return $this->get_all_extras();
    }

    public function patch_extra( \WP_REST_Request $request ) {
        $args = $request->get_json_params();
        $this->extras_service->update( $request->get_param( 'extra_id' ), $args );
        return $this->get_all_extras();
    }

    public function patch_extras( \WP_REST_Request $request ) {
        $args = $request->get_json_params();
        $this->extras_service->patch_data( $args['extraModels'] );
        return $this->get_all_extras();
    }

    public function delete_extra( \WP_REST_Request $request ) {
        $this->extras_service->delete( $request->get_param( 'extra_id' ) );
        return $this->get_all_extras();
    }

    public function exec_get_all_extras( $request ) {
        return $this->exec_read( [ $this, 'get_all_extras' ], $request );
    }

    public function exec_post_extra( $request ) {
        return $this->exec_write( [ $this, 'post_extra' ], $request );
    }

    public function exec_patch_extra( $request ) {
        return $this->exec_write( [ $this, 'patch_extra' ], $request );
    }

    public function exec_patch_extras( $request ) {
        return $this->exec_write( [ $this, 'patch_extras' ], $request );
    }

    public function exec_delete_extra( $request ) {
        return $this->exec_write( [ $this, 'delete_extra' ], $request );
    }
}
