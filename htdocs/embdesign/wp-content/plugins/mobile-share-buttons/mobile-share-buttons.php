<?php
/**
 * Plugin Name: Woo Product Share Buttons
 * Description: Adds WhatsApp, Telegram, Facebook, Twitter, and Copy Link share buttons to WooCommerce product pages.
 * Version: 1.0
 * Author: ChatGPT for Sujauddin
 */

defined('ABSPATH') || exit;

// Enqueue styles and JS
add_action('wp_enqueue_scripts', function () {
    if (is_product()) {
        wp_enqueue_style('wpsb-style', plugin_dir_url(__FILE__) . 'style.css');
        wp_enqueue_script('wpsb-script', plugin_dir_url(__FILE__) . 'script.js', [], null, true);
    }
});

// Add share buttons below Add to Cart
add_action('woocommerce_after_add_to_cart_button', function () {
    global $product;
    $url   = urlencode(get_permalink($product->get_id()));
    $title = urlencode($product->get_name());

    echo '<div class="wpsb-share-buttons">
        <strong>Share:</strong>
        <a class="wpsb whatsapp" href="https://wa.me/?text=' . $title . '%20' . $url . '" target="_blank">WhatsApp</a>
        <a class="wpsb telegram" href="https://t.me/share/url?url=' . $url . '&text=' . $title . '" target="_blank">Telegram</a>
        <a class="wpsb facebook" href="https://www.facebook.com/sharer/sharer.php?u=' . $url . '" target="_blank">Facebook</a>
        <a class="wpsb twitter" href="https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title . '" target="_blank">Twitter</a>
        <button class="wpsb copy-btn" data-link="' . esc_url(get_permalink()) . '">Copy Link</button>
    </div>';
});
