<?php
/*
Plugin Name: Most Viewed Products Grid
Description: Displays a responsive grid of the most viewed WooCommerce products with mobile-friendly design.
Version: 1.1
Author: Sujauddin Sekh
*/

if (!defined('ABSPATH')) exit;

class Most_Viewed_Products_Grid {
    public function __construct() {
        add_shortcode('most_viewed_products_grid', [$this, 'render_grid']);
        add_action('wp_head', [$this, 'track_views']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);

        // Initialize views only once
        add_action('init', [$this, 'initialize_product_views_once']);
    }

    // Track product views
    public function track_views() {
        if (is_singular('product')) {
            global $post;
            $views = (int) get_post_meta($post->ID, 'product_views', true);
            update_post_meta($post->ID, 'product_views', $views + 1);
        }
    }

    // Initialize views for all products only once
    public function initialize_product_views_once() {
        // Run only once on plugin activation
        $flag = get_option('mvp_initialized');
        if ($flag === 'yes') return;

        $products = get_posts([
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => [
                [
                    'key' => 'product_views',
                    'compare' => 'NOT EXISTS'
                ]
            ]
        ]);

        foreach ($products as $product) {
            add_post_meta($product->ID, 'product_views', 1, true);
        }

        update_option('mvp_initialized', 'yes');
    }

    // Enqueue custom styles
    public function enqueue_styles() {
        wp_enqueue_style('most-viewed-grid-style', plugins_url('css/most-viewed-grid.css', __FILE__));
    }

    // Render most viewed product grid
    public function render_grid($atts) {
        if (!class_exists('WooCommerce')) {
            return '<p>WooCommerce is required.</p>';
        }

        // Shortcode attributes with defaults
        $atts = shortcode_atts([
            'title' => 'Most Viewed Designs',
            'columns' => 4,
            'count' => 8,
            'category' => ''
        ], $atts);

        $query_args = [
            'post_type' => 'product',
            'posts_per_page' => $atts['count'],
            'meta_key' => 'product_views',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'post_status' => 'publish'
        ];

        // Add category filter if specified
        if (!empty($atts['category'])) {
            $query_args['tax_query'] = [
                [
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => $atts['category']
                ]
            ];
        }

        $query = new WP_Query($query_args);

        if (!$query->have_posts()) {
            return '<p>No products found.</p>';
        }

        ob_start();
        ?>
        <div class="most-viewed-products-section">
            <h2 class="section-title"><?php echo esc_html($atts['title']); ?></h2>
            <div class="most-viewed-grid" data-columns="<?php echo esc_attr($atts['columns']); ?>">
                <?php while ($query->have_posts()) : $query->the_post(); 
                    global $product;
                    $views = get_post_meta(get_the_ID(), 'product_views', true);
                ?>
                    <div class="mvp-product-card">
                        <a href="<?php the_permalink(); ?>" class="mvp-product-link">
                            <div class="mvp-image-container">
                                <?php
                                if (has_post_thumbnail()) {
                                    the_post_thumbnail('woocommerce_thumbnail', ['class' => 'mvp-product-image']);
                                } else {
                                    echo wc_placeholder_img('woocommerce_thumbnail');
                                }
                                ?>
                                <div class="mvp-view-count">
                                    <i class="fas fa-eye"></i> <?php echo esc_html($views); ?> views
                                </div>
                            </div>
                            <div class="mvp-product-info">
                                <h3 class="mvp-product-title"><?php the_title(); ?></h3>
                                <div class="mvp-price"><?php echo $product->get_price_html(); ?></div>
                                <?php if ($product->is_on_sale()) : ?>
                                    <div class="mvp-sale-badge">Sale</div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

new Most_Viewed_Products_Grid();