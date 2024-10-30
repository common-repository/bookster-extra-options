<?php
namespace Bookster_Extra_Options\Engine;

use Bookster\Models\BookingModel;
use Bookster\Models\BookingMetaModel;
use Bookster\Services\BookingMetasService;
use Bookster_Extra_Options\Features\Utils\SingletonTrait;
use Bookster\Models\Database\QueryBuilder;

/**
 * Extend the Booking model
 */
class ExtendBookingModel {
    use SingletonTrait;

    /** @var BookingMetasService */
    private $booking_metas_service;

    protected function __construct() {
        $this->booking_metas_service = BookingMetasService::get_instance();

        add_filter( 'bookster_booking_subquery_json_args', [ $this, 'add_subquery_select_clause' ], 10, 1 );
        add_filter( 'bookster_booking_info_query_builder', [ $this, 'add_booking_include_clause' ], 10, 1 );

        add_filter( 'bookster_data_model_' . BookingModel::TABLE . '_properties', [ $this, 'register_extra_property' ] );
        add_filter( 'bookster_data_model_' . BookingModel::TABLE . '_excluded_save_fields', [ $this, 'register_extra_protected_property' ] );

        add_filter( 'bookster_data_model_' . BookingModel::TABLE . '_init', [ $this, 'cast_extra_property' ] );
        add_action( 'bookster_data_model_' . BookingModel::TABLE . '_saved', [ $this, 'update_extra_after_booking_saved' ], 10, 1 );
    }

    /**
     * @param string[] $bookings_json_args
     * @return string[]
     */
    public function add_subquery_select_clause( $bookings_json_args ) {
        $bookingmeta_tablename = BookingMetaModel::get_tablename();

        $bookings_json_args[] = "'_extraBookingItems'";
        $bookings_json_args[] = "(
            SELECT _bookmeta.`meta_value`
            FROM $bookingmeta_tablename AS _bookmeta
            WHERE _bookmeta.`appointment_id` = booking.`appointment_id`
            AND _bookmeta.`customer_id` = booking.`customer_id`
            AND _bookmeta.`meta_key` = 'extraBookingItems'
        )";

        return $bookings_json_args;
    }

    public function add_booking_include_clause( QueryBuilder $builder ): QueryBuilder {
        $bookingmeta_tablename = BookingMetaModel::get_tablename();

        $builder->select(
            "(SELECT _bookmeta.`meta_value`
            FROM $bookingmeta_tablename AS _bookmeta
            WHERE _bookmeta.`appointment_id` = booking.`appointment_id`
            AND _bookmeta.`customer_id` = booking.`customer_id`
            AND _bookmeta.`meta_key` = 'extraBookingItems'
            ) AS '_extraBookingItems'"
        );

        return $builder;
    }

    public function cast_extra_property( BookingModel $booking ): BookingModel {
        if ( null !== $booking->extraBookingItems || null === $booking->_extraBookingItems ) {
            return $booking;
        }

        $booking->extraBookingItems = json_decode( $booking->_extraBookingItems, true );
        return $booking;
    }

    public function update_extra_after_booking_saved( BookingModel $booking ) {
        if ( null === $booking->extraBookingItems ) {
            return;
        }

        $this->booking_metas_service->upsert(
            $booking->appointment_id,
            $booking->customer_id,
            'extraBookingItems',
            $booking->extraBookingItems
        );
    }

    public function register_extra_property( array $properties ): array {
        return array_merge( $properties, [ 'extraBookingItems' ] );
    }

    public function register_extra_protected_property( array $protected_properties ): array {
        return array_merge( $protected_properties, [ '_extraBookingItems', 'extraBookingItems' ] );
    }
}
