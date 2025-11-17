<?php
/**
 * Plugin Name: Auto Cache Cleaner
 * Plugin URI:  https://example.com
 * Description: Automatically clears all WordPress caches every 12 hours (supports WP Rocket, W3 Total Cache, WP Super Cache, LiteSpeed, and transients).
 * Version:     1.1.0
 * Author:      Your Name
 * Author URI:  https://example.com
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Auto_Cache_Cleaner {

    const CRON_HOOK = 'auto_cache_cleaner_event';

    public function __construct() {
        register_activation_hook( __FILE__, [ $this, 'activate' ] );
        register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );

        // Add custom interval
        add_filter( 'cron_schedules', [ $this, 'add_custom_interval' ] );

        // Hook into WP-Cron
        add_action( self::CRON_HOOK, [ $this, 'clear_all_caches' ] );
    }

    /**
     * Add a 12-hour interval to WP-Cron
     */
    public function add_custom_interval( $schedules ) {
        if ( ! isset( $schedules['every12hours'] ) ) {
            $schedules['every12hours'] = [
                'interval' => 12 * HOUR_IN_SECONDS,
                'display'  => __( 'Every 12 Hours' ),
            ];
        }
        return $schedules;
    }

    /**
     * Activate: schedule cron
     */
    public function activate() {
        if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
            wp_schedule_event( time(), 'every12hours', self::CRON_HOOK );
        }

        // Run once immediately on activation
        $this->clear_all_caches();
    }

    /**
     * Deactivate: clear cron
     */
    public function deactivate() {
        wp_clear_scheduled_hook( self::CRON_HOOK );
    }

    /**
     * Clear all caches
     */
    public function clear_all_caches() {
        global $wpdb;

        // Delete transients
        $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%'" );

        // WP Super Cache
        if ( function_exists( 'wp_cache_clear_cache' ) ) {
            wp_cache_clear_cache();
        }

        // W3 Total Cache
        if ( function_exists( 'w3tc_flush_all' ) ) {
            w3tc_flush_all();
        }

        // WP Rocket
        if ( function_exists( 'rocket_clean_domain' ) ) {
            rocket_clean_domain();
            if ( function_exists( 'rocket_clean_minify' ) ) {
                rocket_clean_minify();
            }
        }

        // LiteSpeed Cache
        if ( class_exists( 'LiteSpeed_Cache_API' ) ) {
            LiteSpeed_Cache_API::purge_all();
        }

        // Object Cache
        if ( function_exists( 'wp_cache_flush' ) ) {
            wp_cache_flush();
        }

        // Allow other plugins to hook
        do_action( 'auto_cache_cleaner_after_clear' );
    }
}

new Auto_Cache_Cleaner();
