<?php
/*
Plugin Name: EMBDesign Back Button
Description: Adds a "Back" button with icon to the top-left corner on all pages of embdesign.shop
Version: 1.2
Author: EMBDesign
*/

add_action('wp_footer', 'emb_back_button_html');
add_action('wp_enqueue_scripts', 'emb_back_button_assets');

function emb_back_button_html() {
    if (is_front_page()) return;

    echo '<div id="emb-back-button" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i> Back
          </div>';
}

function emb_back_button_assets() {
    // Load Font Awesome
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
        array(),
        '6.4.0'
    );

    // Inject custom style
    wp_add_inline_style('font-awesome', "
        #emb-back-button {
            position: fixed;
            top: 100px;
            left: 20px;
            background: #97B067;
            color: #fff;
            padding: 10px 18px;
            border-radius: 30px;
            font-size: 16px;
            font-family: 'Segoe UI', sans-serif;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 9999;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #emb-back-button:hover {
            background: #7aa353;
            transform: scale(1.05);
        }

        @media(max-width: 600px) {
            #emb-back-button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }
    ");
}
