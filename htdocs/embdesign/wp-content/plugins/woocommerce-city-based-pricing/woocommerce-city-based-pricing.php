<?php
/**
 * Plugin Name: WooCommerce Kolkata Double Price
 * Description: Double WooCommerce product price for users in Kolkata only.
 * Version: 1.0
 * Author: ChatGPT
 */

add_action('wp_enqueue_scripts', function() {
    if (is_product() || is_shop() || is_cart() || is_checkout()) {
        wp_enqueue_script('kolkata-city-detector', plugin_dir_url(__FILE__) . 'js/city-detector.js', [], null, true);
    }
});

// Double price only for Kolkata
add_filter('woocommerce_product_get_price', 'wcdp_double_price_for_kolkata', 10, 2);
add_filter('woocommerce_product_get_regular_price', 'wcdp_double_price_for_kolkata', 10, 2);

function wcdp_double_price_for_kolkata($price, $product) {
    if (!isset($_COOKIE['user_city'])) return $price;

    $city = strtolower(sanitize_text_field($_COOKIE['user_city']));

    if ($city === 'kolkata') {
        return $price * 2; // 👈 Double the price for Kolkata users
    }

    return $price;
}
