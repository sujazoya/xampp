<?php
/**
 * Plugin Name: EMBDesign Go To Page
 * Description: Adds a "Go to page number" field to WooCommerce shop and archive pages.
 * Version: 1.0
 * Author: EMBDesign
 * Author URI: https://embdesign.shop/
 * License: GPL2+
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('woocommerce_after_shop_loop', 'emb_go_to_page_form', 15);
function emb_go_to_page_form() {
    if (!is_shop() && !is_product_category() && !is_product_tag()) return;

    global $wp_query;
    $total_pages = $wp_query->max_num_pages;
    $base_url = trailingslashit(get_pagenum_link(1));

    ?>
    <form id="emb-go-to-page" onsubmit="return embGoToPage();" action="#" method="get">
        <label for="go-page-input" style="margin-right:10px; color:#D6D85D; font-weight:bold;">Go to page:</label>
        <input type="number" id="go-page-input" min="1" max="<?php echo esc_attr($total_pages); ?>" required>
        <button type="submit">Go</button>
    </form>

    <script>
    function embGoToPage() {
        var input = document.getElementById('go-page-input');
        var page = parseInt(input.value);
        var max = parseInt(input.max);
        if (page < 1 || page > max) {
            alert("Please enter a page number between 1 and " + max);
            return false;
        }
        let baseUrl = "<?php echo esc_js($base_url); ?>";
        window.location.href = baseUrl + 'page/' + page + '/';
        return false;
    }
    </script>

    <style>
    #emb-go-to-page {
        margin: 25px auto 10px;
        text-align: center;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    #emb-go-to-page input[type=number] {
        padding: 6px 12px;
        border: 2px solid #D6D85D;
        border-radius: 6px;
        font-size: 16px;
        width: 90px;
    }

    #emb-go-to-page button {
        background-color: #D6D85D;
        color: #0d1f1a;
        border: none;
        padding: 6px 14px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
        font-size: 16px;
    }

    #emb-go-to-page button:hover {
        background-color: #bfc34c;
    }
    </style>
    <?php
}
