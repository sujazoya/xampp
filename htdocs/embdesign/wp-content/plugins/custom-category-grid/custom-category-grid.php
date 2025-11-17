<?php
/**
 * Plugin Name: Category Display
 * Description: Show WooCommerce categories with animation and green card styling using [custom_categories_grid]. Excludes 'Bundles' and its subcategories.
 * Version: 1.2
 * Author: Sujauddin
 * License: GPL2
 */

if (!defined('ABSPATH')) exit;

// Enqueue CSS
function ccg_enqueue_styles() {
    wp_enqueue_style('ccg-style', plugin_dir_url(__FILE__) . 'css/style.css');
}
add_action('wp_enqueue_scripts', 'ccg_enqueue_styles');

// Get all excluded term IDs (Bundles + subcategories)
function ccg_get_excluded_ids() {
    $excluded_ids = [];

    $bundles = get_term_by('slug', 'bundles', 'product_cat');
    if ($bundles) {
        $excluded_ids[] = $bundles->term_id;

        // Get direct children
        $children = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'parent' => $bundles->term_id,
        ]);
        foreach ($children as $child) {
            $excluded_ids[] = $child->term_id;

            // Nested children (if any)
            $subchildren = get_terms([
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
                'parent' => $child->term_id,
            ]);
            foreach ($subchildren as $subchild) {
                $excluded_ids[] = $subchild->term_id;
            }
        }
    }
    return $excluded_ids;
}

// Shortcode to display category grid
function ccg_category_grid_shortcode() {
    ob_start();
    $excluded_ids = ccg_get_excluded_ids();
    ?>
    <div class="custom-cat-grid">
        <?php
        $terms = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'exclude' => $excluded_ids,
        ]);

        foreach ($terms as $term) {
            $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
            $image_url = wp_get_attachment_url($thumbnail_id);
            $link = get_term_link($term);
            ?>
            <a class="custom-cat-item" href="<?php echo esc_url($link); ?>">
                <div class="custom-cat-card">
                    <div class="custom-cat-image">
                        <?php if ($image_url): ?>
                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($term->name); ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/200" alt="No Image">
                        <?php endif; ?>
                    </div>
                    <div class="custom-cat-name"><?php echo esc_html($term->name); ?></div>
                </div>
            </a>
        <?php } ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_categories_grid', 'ccg_category_grid_shortcode');
