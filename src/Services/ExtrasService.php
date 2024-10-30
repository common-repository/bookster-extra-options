<?php
namespace Bookster_Extra_Options\Services;

use Bookster\Services\BaseService;
use Bookster_Extra_Options\Features\Utils\SingletonTrait;
use Bookster\Features\Utils\RandomUtils;

/**
 * ExtrasService
 *
 * @method static ExtrasService get_instance()
 */
class ExtrasService extends BaseService {
    use SingletonTrait;

    public const SERVICE_EXTRAS_OPTION = 'bookster_extra_service_extras';
    public const VAR_ACTIVE_EXTRAS     = 'booksterActiveExtras';

    private $localized = false;

    public function patch_data( $data ) {
        return update_option( self::SERVICE_EXTRAS_OPTION, $data );
    }

    public function get_data() {
        return get_option( self::SERVICE_EXTRAS_OPTION, [] );
    }

    public function find_by_id( $extra_id ) {
        $extras = $this->get_data();
        $extra  = null;
        foreach ( $extras as $ex ) {
            if ( $ex['extra_id'] === $extra_id ) {
                $extra = $ex;
                break;
            }
        }
        if ( empty( $extra ) ) {
            return false;
        }
        return $extra;
    }

    public function insert( $attributes ) {
        $extras                 = $this->get_data();
        $extra_id               = RandomUtils::gen_unique_id();
        $attributes['extra_id'] = $extra_id;
        array_push( $extras, $attributes );
        $success = $this->patch_data( $extras );
        if ( false === $success ) {
            throw new \Exception( 'Error Adding Extra' );
        }
    }

    public function update( string $extra_id, array $data ) {
        $extras = $this->get_data();
        foreach ( $extras as $key => $extra ) {
            if ( $extra['extra_id'] === $extra_id ) {
                $data['extra_id'] = $extra_id;
                $extras[ $key ]   = $data;
            }
        }
        $success = $this->patch_data( $extras );
        if ( false === $success ) {
            throw new \Exception( 'Error Saving Extra' );
        }
    }

    public function delete( string $extra_id ) {
        $extras = $this->get_data();
        foreach ( $extras as $key => $extra ) {
            if ( $extra['extra_id'] === $extra_id ) {
                unset( $extras[ $key ] );
            }
        }
        $this->patch_data( $extras );
    }

    public function get_active_extra() {
        $all_extras    = $this->get_data();
        $active_extras = [];
        foreach ( $all_extras as $extra ) {
            if ( $extra['activated'] ) {
                if ( ! empty( $extra['cover_id'] ) ) {
                    $extra['transient_cover_url'] = wp_get_attachment_image_url( $extra['cover_id'], 'medium_large' );
                }
                array_push( $active_extras, $extra );
            }
        }
        return $active_extras;
    }

    public function localized_active_extras( $handle ) {
        if ( false === $this->localized ) {
            $this->localized = wp_localize_script(
                $handle,
                self::VAR_ACTIVE_EXTRAS,
                $this->get_active_extra()
            );
        }
    }
}
