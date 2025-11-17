<?php
/**
 * Plugin Name: WC Advanced Product Share
 * Description: Adds direct-sharing buttons (WhatsApp, Messenger, etc.) with QR and mobile support to WooCommerce product pages.
 * Version: 2.1
 * Author: Cobraz
 * License: GPL2+
 */

if (!defined('ABSPATH')) exit;

// Enqueue styles and QRious from CDN
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('wcaps-style', plugin_dir_url(__FILE__) . 'assets/style.css');
    wp_enqueue_script('wcaps-qrious', 'https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js', [], null, true);
    wp_add_inline_script('wcaps-qrious', '
        document.addEventListener("DOMContentLoaded", function() {
            const qr = new QRious({
                element: document.getElementById("product-qr"),
                value: window.location.href,
                size: 140
            });
        });
    ');
});

// Output share buttons on product pages
add_action('woocommerce_single_product_summary', function () {
    if (!is_product()) return;

    global $product;
    $facebook_app_id = '123456789012345'; // 🔁 Replace this with your real Facebook App ID

    $product_url = esc_url(get_permalink($product->get_id()));
    $product_title = esc_html($product->get_name());
    $encoded_url = urlencode($product_url);
    $encoded_title = urlencode($product_title);
    $home_url = urlencode(home_url());
    ?>

    <div class="wcaps-share-box">
        <strong>📤 Share this design with someone:</strong>
        <div class="wcaps-buttons">

           <!-- WhatsApp -->
<!-- WhatsApp -->
<!-- WhatsApp -->
<a href="https://wa.me/?text=<?php echo $encoded_title . '%20' . $encoded_url; ?>"
   target="_blank"
   rel="noopener"
   style="background-color: #25D366; border-radius: 6px; padding: 6px;">
   <img src="https://img.icons8.com/fluency/48/whatsapp.png" alt="WhatsApp" />
</a>



            <!-- Telegram -->
            <a href="https://t.me/share/url?url=<?php echo $encoded_url; ?>&text=<?php echo $encoded_title; ?>" target="_blank" rel="noopener">
                <img src="https://img.icons8.com/fluency/48/telegram-app.png" alt="Telegram" />
            </a>

            <!-- Facebook Messenger (Send to user) -->
            <a href="https://www.facebook.com/dialog/send?app_id=<?php echo $facebook_app_id; ?>&link=<?php echo $encoded_url; ?>&redirect_uri=<?php echo $home_url; ?>" target="_blank" rel="noopener">
                <img src="https://img.icons8.com/fluency/48/facebook-messenger.png" alt="Messenger" />
            </a>

            <!-- Facebook Share (to wall if needed) -->
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $encoded_url; ?>" target="_blank" rel="noopener">
                <img src="https://img.icons8.com/fluency/48/facebook.png" alt="Facebook" />
            </a>

            <!-- SMS -->
            <a href="sms:?&body=<?php echo $encoded_title . '%20' . $encoded_url; ?>">
                <img src="https://img.icons8.com/fluency/48/sms.png" alt="SMS" />
            </a>

            <!-- Email -->
            <a href="mailto:?subject=<?php echo $encoded_title; ?>&body=<?php echo $encoded_url; ?>">
                <img src="https://img.icons8.com/fluency/48/apple-mail.png" alt="Email" />
            </a>

            <!-- Copy Link -->
            <a href="#" onclick="navigator.clipboard.writeText('<?php echo $product_url; ?>'); alert('Link copied!'); return false;">
                <img src="https://img.icons8.com/fluency/48/copy.png" alt="Copy Link" />
            </a>

            <!-- Native Share (iOS / Android) -->
            <a href="#" onclick="nativeShare('<?php echo $product_title; ?>', '<?php echo $product_url; ?>'); return false;">
                <img src="https://img.icons8.com/fluency/48/share-rounded.png" alt="Native Share" />
            </a>
        </div>

        <!-- QR Code Section -->
        <div class="qr-container">
            <canvas id="product-qr"></canvas>
            <p>Scan QR to open this product</p>
        </div>
    </div>

    <script>
        function nativeShare(title, url) {
            if (navigator.share) {
                navigator.share({ title: title, url: url });
            } else {
                alert("Native sharing is not supported on this device.");
            }
        }
    </script>
   <script>
document.addEventListener("DOMContentLoaded", function () {
    const isMobile = /Android|webOS|iPhone|iPad|iPod/i.test(navigator.userAgent);
    if (isMobile) {
        const whatsappBtn = document.createElement("a");
        whatsappBtn.href = "https://wa.me/?text=<?php echo rawurlencode($product_title . ' ' . $product_url); ?>";
        whatsappBtn.target = "_blank";
        whatsappBtn.rel = "noopener";
        whatsappBtn.style = "display:inline-block;background-color:#25D366;color:#fff;padding:10px 16px;border-radius:6px;font-weight:bold;text-decoration:none;margin-top:12px;";
        whatsappBtn.innerHTML = `<img src="https://img.icons8.com/ios-filled/24/ffffff/whatsapp.png" style="vertical-align:middle;margin-right:8px;"> Share via WhatsApp`;

        const container = document.querySelector('.wcaps-buttons');
        if (container) container.prepend(whatsappBtn);
    }
});
// Output share buttons on product pages
add_action('woocommerce_single_product_summary', function () {

</script>




<?php }, 36);
