<?php
/**
 * Plugin Name: EmbDesign Google Login
 * Description: Custom endpoint to log in WooCommerce users with Google ID token (from Android app).
 * Version: 1.0.0
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Register REST API route
add_action('rest_api_init', function () {
    register_rest_route('google-login/v1', '/auth', array(
        'methods'  => 'POST',
        'callback' => 'embdesign_google_login',
        'permission_callback' => '__return_true'
    ));
});

function embdesign_google_login(WP_REST_Request $request) {
    $id_token = sanitize_text_field($request->get_param('id_token'));

    if (empty($id_token)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Missing ID token'
        ), 400);
    }

    // Verify Google token
    $google_response = wp_remote_get("https://oauth2.googleapis.com/tokeninfo?id_token=" . $id_token);
    if (is_wp_error($google_response)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Google verification failed'
        ), 400);
    }

    $google_data = json_decode(wp_remote_retrieve_body($google_response), true);

    if (!isset($google_data['email'])) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Invalid Google token'
        ), 400);
    }

    $email     = sanitize_email($google_data['email']);
    $firstName = sanitize_text_field($google_data['given_name'] ?? '');
    $lastName  = sanitize_text_field($google_data['family_name'] ?? '');

    // Find or create WP user
    $user = get_user_by('email', $email);

    if (!$user) {
        $random_password = wp_generate_password(12, false);
        $user_id = wp_create_user($email, $random_password, $email);

        if (is_wp_error($user_id)) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'User creation failed'
            ), 500);
        }

        wp_update_user(array(
            'ID'           => $user_id,
            'first_name'   => $firstName,
            'last_name'    => $lastName,
            'display_name' => $firstName . ' ' . $lastName,
        ));

        $user = get_user_by('id', $user_id);
    }

    // Log the user in
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true); // true = remember me

    return new WP_REST_Response(array(
        'success' => true,
        'message' => 'Login successful',
        'user'    => array(
            'id'    => $user->ID,
            'email' => $user->user_email,
            'name'  => $user->display_name,
        )
    ));
}
