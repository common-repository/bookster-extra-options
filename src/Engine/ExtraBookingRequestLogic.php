<?php
namespace Bookster_Extra_Options\Engine;

use Bookster_Extra_Options\Features\Utils\SingletonTrait;
use Bookster\Features\Utils\Decimal;
use Bookster\Features\Utils\RandomUtils;
use Bookster\Features\Booking\Details;
use Bookster\Features\Booking\Details\BookingItem;
use Bookster_Extra_Options\Services\ExtrasService;
use Bookster\Engine\BEPages\ManagerPage;
use Bookster\Features\Auth\Caps;

/**
 * ExtraLogic
 *
 * @method static ExtraLogic get_instance()
 */
class ExtraBookingRequestLogic {
    use SingletonTrait;

    private $extra_service;

    protected function __construct() {
        $this->extra_service = ExtrasService::get_instance();

        if ( current_user_can( Caps::MANAGE_SHOP_RECORDS_CAP ) ) {
            add_filter( 'plugin_action_links_' . plugin_basename( BOOKSTER_EXTRA_OPTIONS_PLUGIN_FILE ), [ $this, 'add_action_links' ] );
        }

        add_filter( 'bookster_booking_payload_allowed_keys', [ $this, 'add_payload_allowed_keys_extra' ], 10, 1 );
        add_filter( 'bookster_booking_duration', [ $this, 'add_duration_extra' ], 10, 2 );
        add_filter( 'bookster_make_booking_details', [ $this, 'add_booking_extra' ], 10, 2 );
    }

    /**
     * @param Details $blueprint
     * @param array   $booking_request_input
     * @return Details
     */
    public function add_booking_extra( $blueprint, $booking_request_input ) {
        if ( empty( $booking_request_input['bookingMetaInput']['extraBookingItems'] ) ) {
            return $blueprint;
        }

        $extra_service_items = [];

        foreach ( $booking_request_input['bookingMetaInput']['extraBookingItems'] as $extra_item ) {
            $extra = $this->extra_service->find_by_id( $extra_item['id'] );
            if ( $extra ) {
                $extra_service_items[] = new BookingItem(
                    RandomUtils::gen_unique_id(),
                    $extra['name'],
                    $extra_item['quantity'],
                    Decimal::from_string( $extra['price'] ),
                    Decimal::zero()
                );
            }
        }

        if ( count( $extra_service_items ) === 0 ) {
            return $blueprint;
        }

        $blueprint->booking->items = array_merge( $blueprint->booking->items, $extra_service_items );
        return $blueprint;
    }

    public function add_payload_allowed_keys_extra( $allowed_keys ) {
        $allowed_keys['bookingMetaInput'][] = 'extraBookingItems';
        return $allowed_keys;
    }

    public function add_duration_extra( $booking_duration, $booking_input ) {
        if ( empty( $booking_input['bookingMetaInput']['extraBookingItems'] ) ) {
            return $booking_duration;
        }

        foreach ( $booking_input['bookingMetaInput']['extraBookingItems'] as $extra_item ) {
            $extra_model = $this->extra_service->find_by_id( $extra_item['id'] );
            if ( $extra_model ) {
                $booking_duration['duration'] += $extra_model['duration'] * $extra_item['quantity'];
            }
        }

        return $booking_duration;
    }

    public function add_action_links( array $links ) {
        return array_merge(
            [
                'manage' => '<a href="' . admin_url( 'admin.php?page=' . ManagerPage::MENU_SLUG . '#/services/extras' ) . '">' . __( 'Manage', 'bookster-extra-options' ) . '</a>',
            ],
            $links
        );
    }
}
