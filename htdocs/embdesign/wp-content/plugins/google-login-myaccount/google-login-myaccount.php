<?php
/**
 * Plugin Name: Emb Google Login for WooCommerce + Mobile Support
 * Description: Adds mobile-friendly Google Login button with WooCommerce support and full redirect flow.
 * Version: 1.2
 * Author: EmbDesign Team
 */

defined('ABSPATH') || exit;

add_filter('query_vars', function ($vars) {
    $vars[] = 'emb_google_login';
    return $vars;
});

// Unique username generator
function wp_unique_username($username) {
    $i = 1;
    $base = $username;
    while (username_exists($username)) {
        $username = $base . $i;
        $i++;
    }
    return $username;
}

// Unified template_redirect handler
add_action('template_redirect', function () {
    // Avoid caching login pages
    if (is_page('my-account') || intval(get_query_var('emb_google_login')) === 1) {
        nocache_headers();
    }

    // Handle Google Login POST callback
    if (intval(get_query_var('emb_google_login')) === 1) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['credential'])) {
            wp_safe_redirect(wp_login_url());
            exit;
        }

        $credential = sanitize_text_field($_POST['credential']);
        $payload = explode('.', $credential);
        if (count($payload) < 2) wp_die('Invalid credential');

        $data = json_decode(base64_decode(strtr($payload[1], '-_', '+/')), true);
        if (!isset($data['email'])) wp_die('Missing email');

        $email = sanitize_email($data['email']);
        $name = sanitize_text_field($data['name'] ?? 'Google User');
        $user = get_user_by('email', $email);

        if (!$user) {
            $username = sanitize_user(current(explode('@', $email)));
            $username = wp_unique_username($username);
            $user_id = wp_create_user($username, wp_generate_password(), $email);
            wp_update_user(['ID' => $user_id, 'display_name' => $name]);
            $user = get_user_by('id', $user_id);
        }

        wp_set_auth_cookie($user->ID, true);
        wp_redirect(wc_get_page_permalink('myaccount'));
        exit;
    }
});

// Inject Google Sign-In button on login form
add_action('woocommerce_before_customer_login_form', function () {
    if (is_user_logged_in()) return;
    ?>
    <div class="emb-google-login-wrapper" style="margin-bottom: 20px;">
        <div id="g_id_onload"
             data-client_id="576211564660-q463ruu4ceuoee36ifeh5eos63ihc2of.apps.googleusercontent.com"
             data-login_uri="<?php echo esc_url(home_url('/?emb_google_login=1')); ?>"
             data-auto_prompt="false">
        </div>
        <div class="g_id_signin"
             data-type="standard"
             data-size="large"
             data-theme="outline"
             data-text="continue_with"
             data-shape="rectangular"
             data-logo_alignment="left">
        </div>
    </div>
    <?php
});

// Google JS and styles
add_action('wp_head', function () {
    ?>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
        .emb-google-login-wrapper {
            text-align: center;
            margin-top: 20px;
        }

        @media screen and (max-width: 768px) {
            .emb-google-login-wrapper .g_id_signin {
                width: 90% !important;
                max-width: 320px !important;
            }
        }
    </style>
    <?php
});

// Add JS to handle Google sign-in callback
add_action('wp_footer', function () {
    ?>
    <script>
        window.addEventListener('load', function () {
            if (window.google && google.accounts && google.accounts.id) {
                google.accounts.id.initialize({
                    client_id: '576211564660-q463ruu4ceuoee36ifeh5eos63ihc2of.apps.googleusercontent.com',
                    callback: (response) => {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '<?php echo esc_url(home_url('/?emb_google_login=1')); ?>';
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'credential';
                        input.value = response.credential;
                        form.appendChild(input);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });

                google.accounts.id.renderButton(
                    document.querySelector('.g_id_signin'),
                    {
                        theme: "outline",
                        size: "large",
                        width: '100%',
                        shape: "rectangular",
                        text: "continue_with"
                    }
                );
            }
        });
    </script>
    <?php
});

// Optional: handle GET fallback (for mobile WebViews)
add_action('init', function () {
    if (isset($_GET['google_id_token'])) {
        nocache_headers();

        $id_token = sanitize_text_field($_GET['google_id_token']);
        $client_id = '576211564660-q463ruu4ceuoee36ifeh5eos63ihc2of.apps.googleusercontent.com';

        $response = wp_remote_get("https://oauth2.googleapis.com/tokeninfo?id_token=$id_token");
        if (is_wp_error($response)) return;

        $body = json_decode(wp_remote_retrieve_body($response), true);
        if ($body && isset($body['email']) && $body['aud'] === $client_id) {
            $email = $body['email'];
            $user = get_user_by('email', $email);

            if (!$user) {
                $username = sanitize_user(current(explode('@', $email)));
                $password = wp_generate_password();
                $user_id = wp_create_user($username, $password, $email);
                $user = get_user_by('id', $user_id);
            }

            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID);
            do_action('wp_login', $user->user_login, $user);

            wp_redirect(site_url('/my-account'));
            exit;
        }
    }
});
