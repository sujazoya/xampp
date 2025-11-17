<?php
/**
 * Plugin Name: Delete My Account (Advanced UI)
 * Plugin URI: https://embdesign.shop/
 * Description: Allow users to securely request account and data deletion. Includes a styled UI, admin notifications, and WooCommerce My Account integration.
 * Version: 1.0
 * Author: EMBDesign
 * License: GPL2+
 */

if (!defined('ABSPATH')) exit;

// 1. Register endpoint for /request-delete
add_action('init', function () {
    add_rewrite_endpoint('request-delete', EP_ROOT | EP_PAGES);
});

// 2. Add item to WooCommerce My Account menu
add_filter('woocommerce_account_menu_items', function ($items) {
    $items['request-delete'] = __('Request Data Deletion', 'delete-my-account');
    return $items;
});

// 3. Endpoint page: content + form + email + confirmation
add_action('woocommerce_account_request-delete_endpoint', function () {
    if (!is_user_logged_in()) {
        echo '<div class="woocommerce-error">You must be logged in to request deletion.</div>';
        return;
    }

    $user = wp_get_current_user();

    if (isset($_POST['dma_request_delete']) && wp_verify_nonce($_POST['dma_nonce'], 'dma_request')) {
        // Email to admin
        $admin_email = get_option('admin_email');
        $subject = '🔒 Data Deletion Request: ' . $user->user_email;
        $message = "A user requested data deletion:\n\n";
        $message .= "Name: {$user->display_name}\n";
        $message .= "Email: {$user->user_email}\n";
        $message .= "Username: {$user->user_login}\n";
        $message .= "User ID: {$user->ID}\n\n";
        $message .= "Please take action per your privacy policy.";
        wp_mail($admin_email, $subject, $message);

        // Email to user
        wp_mail($user->user_email, 'We Received Your Deletion Request', 
            "Hi {$user->display_name},\n\nWe received your request to delete your account and data. Our admin will process this shortly. If this wasn't you, please contact support.\n\nRegards,\n" . get_bloginfo('name'));

        echo '<div class="woocommerce-message">✅ Your deletion request has been submitted. We will contact you shortly.</div>';
        return;
    }

    // Display styled form
    echo '
    <div class="woocommerce-account-delete-section">
        <h2>🗑 Request Data Deletion</h2>
        <p>We\'re sorry to see you go. By clicking the button below, you will notify our admin to delete your account and all stored data.</p>
        <p class="notice">⚠️ This is not automatic. You will be contacted after the deletion is completed.</p>
        <form method="post">
            ' . wp_nonce_field('dma_request', 'dma_nonce', true, false) . '
            <button type="submit" name="dma_request_delete" class="delete-btn" onclick="return confirm(\'Are you sure you want to request account deletion?\')">
                Request Data Deletion
            </button>
        </form>
    </div>';
});

// 4. Shortcode: [request_delete_account]
add_shortcode('request_delete_account', function () {
    if (!is_user_logged_in()) return '';
    $url = esc_url(home_url('/request-delete'));
    return '<a class="request-delete-link" href="' . $url . '" onclick="return confirm(\'Are you sure you want to request deletion of your account and data?\')">Request Account/Data Deletion</a>';
});

// 5. Inline CSS for styling the section
add_action('wp_enqueue_scripts', function () {
    if (is_account_page()) {
        wp_add_inline_style('woocommerce-inline', '
            .woocommerce-account-delete-section {
                max-width: 600px;
                margin: auto;
                padding: 30px;
                border: 1px solid #ddd;
                border-radius: 12px;
                background: #f9f9f9;
                box-shadow: 0 4px 10px rgba(0,0,0,0.05);
                font-family: sans-serif;
                color: #333;
            }
            .woocommerce-account-delete-section h2 {
                color: #cc0000;
                margin-bottom: 20px;
            }
            .woocommerce-account-delete-section p {
                font-size: 16px;
                margin-bottom: 10px;
            }
            .woocommerce-account-delete-section .notice {
                font-size: 14px;
                color: #666;
                background: #fff3cd;
                padding: 10px;
                border: 1px solid #ffeeba;
                border-radius: 6px;
                margin-bottom: 20px;
            }
            .woocommerce-account-delete-section .delete-btn {
                background: #cc0000;
                color: #fff;
                padding: 12px 24px;
                font-size: 16px;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                transition: background 0.3s ease;
            }
            .woocommerce-account-delete-section .delete-btn:hover {
                background: #a30000;
            }
            .request-delete-link {
                color: #cc0000;
                font-weight: bold;
            }
        ');
    }
});
