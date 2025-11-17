<?php
/*
Plugin Name: WooCommerce Wishlist
Description: Adds a FontAwesome wishlist heart to WooCommerce product cards (shop + single).
Version: 2.0
Author: EMBDesign
*/
if (!defined('ABSPATH')) exit;
class WC_Wishlist {
    public function __construct() {
        add_action('init', [$this, 'start_session']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('woocommerce_after_shop_loop_item', [$this, 'render_heart_loop'], 15);
        add_action('woocommerce_after_add_to_cart_button', [$this, 'render_heart_single'], 5);
        add_action('wp_ajax_toggle_wishlist', [$this, 'toggle_wishlist']);
        add_action('wp_ajax_nopriv_toggle_wishlist', [$this, 'toggle_wishlist']);
        add_shortcode('woocommerce_wishlist', [$this, 'render_wishlist_page_shortcode']);
         add_action('wp_ajax_get_wishlist_count', [$this, 'get_wishlist_count']);
    add_action('wp_ajax_nopriv_get_wishlist_count', [$this, 'get_wishlist_count']);
    }
    public function start_session() {
        if (!headers_sent() && session_status() === PHP_SESSION_NONE) {
    session_start();
     }

    }
    public function enqueue_assets() {
        wp_enqueue_style('wc-fa', plugin_dir_url(__FILE__) . 'assets/fontawesome/css/all.min.css', [], '6.5.0');
        wp_enqueue_style('wc-wishlist-style', plugin_dir_url(__FILE__) . 'wishlist.css', ['wc-fa']);
        wp_enqueue_script('wc-wishlist-script', plugin_dir_url(__FILE__) . 'wishlist.js', ['jquery'], null, true);
        wp_localize_script('wc-wishlist-script', 'wc_wishlist_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('wishlist_nonce')
        ]);
    }
    public function get_wishlist() {
        return is_user_logged_in()
            ? get_user_meta(get_current_user_id(), '_wishlist', true) ?: []
            : ($_SESSION['wishlist'] ?? []);
    }
    public function get_wishlist_count() {
    check_ajax_referer('wishlist_nonce', 'nonce');
    $wishlist = $this->get_wishlist();
    wp_send_json(['success' => true, 'count' => count($wishlist)]);
}
    public function save_wishlist($wishlist) {
        is_user_logged_in()
            ? update_user_meta(get_current_user_id(), '_wishlist', $wishlist)
            : $_SESSION['wishlist'] = $wishlist;
    }
    public function toggle_wishlist() {
        check_ajax_referer('wishlist_nonce', 'nonce');
        $product_id = intval($_POST['product_id']);
        $wishlist = $this->get_wishlist();
        $added = !in_array($product_id, $wishlist);
        $wishlist = $added ? array_merge($wishlist, [$product_id]) : array_diff($wishlist, [$product_id]);
        $this->save_wishlist(array_values($wishlist));
        wp_send_json(['success' => true, 'added' => $added]);
    }
    public function render_heart_loop() {
        global $product;
        if (!$product) return;
        $this->render_heart($product->get_id(), false);
    }
    public function render_heart_single() {
        global $product;
        if (!$product) return;
        $this->render_heart($product->get_id(), true);
    }
    private function render_heart($product_id, $is_single = false) {
        $wishlist = $this->get_wishlist();
        $active = in_array($product_id, $wishlist);
        $class = 'wishlist-heart' . ($active ? ' active' : '') . ($is_single ? ' single-heart' : '');
        $title = $active ? 'Remove from wishlist' : 'Add to wishlist';
        $icon_class = $active ? 'fa-solid' : 'fa-regular';
        echo '<div class="wishlist-icon-wrap' . ($is_single ? ' single' : '') . '">
            <a href="#" class="' . esc_attr($class) . '" title="' . esc_attr($title) . '" data-product-id="' . esc_attr($product_id) . '">
                <i class="fa-heart ' . esc_attr($icon_class) . '"></i>
            </a>
        </div>';
    }
    public function render_wishlist_page_shortcode() {
        $wishlist = $this->get_wishlist();
        ob_start();
        echo '<div class="woocommerce"><h2>Your Wishlist</h2>';
        if (!empty($wishlist)) {
            echo '<ul class="products columns-4">';
            foreach ($wishlist as $product_id) {
                if (!get_post_status($product_id)) continue;
                $post_object = get_post($product_id);
                setup_postdata($GLOBALS['post'] =& $post_object);
                wc_get_template_part('content', 'product');
            }
            echo '</ul>';
            wp_reset_postdata();
        } else {
            echo '<p class="woocommerce-info">Your wishlist is empty.</p>';
        }
        echo '</div>';
        return ob_get_clean();
    }
}
new WC_Wishlist();
