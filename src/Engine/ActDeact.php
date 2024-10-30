<?php
namespace Bookster_Extra_Options\Engine;

use Bookster_Extra_Options\Features\Utils\SingletonTrait;

/** Activate, Deactive, Uninstall hooks.*/
class ActDeact {
    use SingletonTrait;

    protected function __construct() {
        // Activate plugin when new blog is added
        add_action( 'wpmu_new_blog', [ $this, 'activate_new_site' ] );
    }

    /**
     * Fired when a new site is activated with a WPMU environment.
     *
     * @param int $blog_id ID of the new blog.
     */
    public function activate_new_site( int $blog_id ) {
        if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
            return;
        }

        switch_to_blog( $blog_id );
        self::single_activate();
        restore_current_blog();
    }

    /**
     * Fired when the plugin is activated.
     *
     * @param bool $network_wide True if active in a multiste, false if classic site.
     */
    public static function activate( bool $network_wide ) {
        if ( function_exists( 'is_multisite' ) && is_multisite() ) {
            if ( $network_wide ) {
                // Get all blog ids
                /** @var array<\WP_Site> $blogs */
                $blogs = get_sites();

                foreach ( $blogs as $blog ) {
                    switch_to_blog( (int) $blog->blog_id );
                    self::single_activate();
                    restore_current_blog();
                }

                return;
            }
        }

        self::single_activate();
    }

    /**
     * Fired when the plugin is deactivated.
     *
     * @param bool $network_wide True if WPMU superadmin uses
     * "Network Deactivate" action, false if
     * WPMU is disabled or plugin is
     * deactivated on an individual blog.
     */
    public static function deactivate( bool $network_wide ) {
        if ( function_exists( 'is_multisite' ) && is_multisite() ) {
            if ( $network_wide ) {
                // Get all blog ids
                /** @var array<\WP_Site> $blogs */
                $blogs = get_sites();

                foreach ( $blogs as $blog ) {
                    switch_to_blog( (int) $blog->blog_id );
                    self::single_deactivate();
                    restore_current_blog();
                }

                return;
            }
        }

        self::single_deactivate();
    }

    /** Fired when the plugin is uninstalled. */
    public static function uninstall() {
        if ( function_exists( 'is_multisite' ) && is_multisite() ) {
                // Get all blog ids
                /** @var array<\WP_Site> $blogs */
                $blogs = get_sites();

            foreach ( $blogs as $blog ) {
                switch_to_blog( (int) $blog->blog_id );
                self::single_uninstall();
                restore_current_blog();
            }

            return;
        }

        self::single_uninstall();
    }

    /** Procedure run when version update */
    public static function upgrade_procedure() {
        if ( ! is_admin() ) {
            return;
        }
        // Here: Update Schema if needed
    }

    /** Fired for each blog when the plugin is activated. */
    private static function single_activate() {
        self::upgrade_procedure();
        // Here: Init default settings if needed
        flush_rewrite_rules();
    }

    /** Fired for each blog when the plugin is deactivated. */
    private static function single_deactivate() {
        flush_rewrite_rules();
    }

    /** The plugin is uninstall single site. */
    private static function single_uninstall() {
        // Here: Delete settings if needed
        wp_cache_flush();
    }
}
