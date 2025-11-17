<?php
/**
 * Plugin Name: EmbDesign Hero Section
 * Description: Adds a beautiful animated hero section with action cards. Use shortcode [emb_homepage_hero].
 * Version: 1.0
 * Author: Sujauddin Sekh
 */

defined('ABSPATH') || exit;

function emb_hero_section_shortcode() {
    ob_start();
    ?>

    <!-- EmbDesign Hero Section -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary: #00C6FF;
            --secondary: #EE00FF;
            --light: #f8f9fa;
            --accent: #FF2D75;
            --transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .emb-hero-section * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .emb-hero-section {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: var(--light);
            padding: 8rem 5% 5rem;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .emb-hero-section h1 {
            font-size: 3.5rem;
            margin-bottom: 1.2rem;
            background: linear-gradient(to right, var(--light), #ccc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: fadeInUp 0.8s ease-out;
        }

        .emb-hero-section p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 3rem;
            animation: fadeInUp 0.8s ease-out 0.2s forwards;
            opacity: 0;
        }

        .emb-action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            max-width: 900px;
            margin: 0 auto;
            animation: fadeInUp 0.8s ease-out 0.4s forwards;
            opacity: 0;
        }

        .emb-action-card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            padding: 2rem 1.5rem;
            transition: var(--transition);
            cursor: pointer;
            text-align: center;
            display: block;
            text-decoration: none;
            color: var(--light);
        }

        .emb-action-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .emb-action-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: var(--transition);
        }

        .emb-action-card:hover .emb-action-icon {
            transform: scale(1.2);
        }

        .emb-floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .emb-floating-element {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            animation: float 15s infinite linear;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-50px) rotate(180deg);
            }
            100% {
                transform: translateY(0) rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .emb-hero-section h1 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 480px) {
            .emb-hero-section h1 {
                font-size: 2rem;
            }
            .emb-hero-section p {
                font-size: 1rem;
            }
        }
    </style>

    <section class="emb-hero-section">
        <div class="hero-text">
            <h1>Transform Your Digital Presence</h1>
            <p>Discover premium design resources for all your creative projects. From stunning templates to UI kits, we have everything to elevate your work.</p>
        </div>

        <div class="emb-action-grid">
            <a href="/design-categories" class="emb-action-card">
                <div class="emb-action-icon"><i class="fas fa-th-large"></i></div>
                <h3>Design Categories</h3>
            </a>
            <a href="/latest-designs" class="emb-action-card">
                <div class="emb-action-icon"><i class="fas fa-star"></i></div>
                <h3>Latest Designs</h3>
            </a>
            <a href="/shop" class="emb-action-card">
                <div class="emb-action-icon"><i class="fas fa-shopping-cart"></i></div>
                <h3>Shop</h3>
            </a>
            <a href="/free-designs" class="emb-action-card">
                <div class="emb-action-icon"><i class="fas fa-gift"></i></div>
                <h3>Free Designs</h3>
            </a>
        </div>

        <div class="emb-floating-elements">
            <div class="emb-floating-element" style="width: 300px; height: 300px; top: 10%; left: 5%; animation-duration: 20s;"></div>
            <div class="emb-floating-element" style="width: 200px; height: 200px; top: 60%; left: 70%; animation-duration: 25s;"></div>
            <div class="emb-floating-element" style="width: 150px; height: 150px; top: 30%; left: 80%; animation-duration: 15s;"></div>
            <div class="emb-floating-element" style="width: 250px; height: 250px; top: 70%; left: 10%; animation-duration: 30s;"></div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.hero-text h1, .hero-text p, .emb-action-grid').forEach(el => {
                el.style.opacity = '1';
            });
        });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('emb_homepage_hero', 'emb_hero_section_shortcode');
