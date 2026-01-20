<?php
/**
 * Plugin Name: Internal Content Feedback
 * Description: Adds private internal feedback notes to posts and pages.
 * Version: 1.0
 * Author: Ankita Bhamidimarri
 */

if (!defined('ABSPATH')) exit;

/**
 * Add meta box to post and page editor
 */
add_action('add_meta_boxes', 'icf_add_meta_box');

function icf_add_meta_box() {
    add_meta_box(
        'icf_meta_box',
        'Internal Feedback',
        'icf_render_meta_box',
        ['post', 'page'],
        'side',
        'default'
    );
}

/**
 * Render the meta box UI
 */
function icf_render_meta_box($post) {
    wp_nonce_field('icf_save_feedback', 'icf_nonce');

    $saved_feedback = get_post_meta($post->ID, '_icf_feedback', true);
    ?>
    <textarea
        name="icf_feedback"
        style="width:100%;"
        rows="4"
        placeholder="Enter internal feedback here..."
    ><?php echo esc_textarea($saved_feedback); ?></textarea>
    <?php
}

/**
 * Save feedback securely
 */
add_action('save_post', 'icf_save_feedback');

function icf_save_feedback($post_id) {

    if (!isset($_POST['icf_nonce'])) return;
    if (!wp_verify_nonce($_POST['icf_nonce'], 'icf_save_feedback')) return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (!current_user_can('edit_post', $post_id)) return;

    if (!isset($_POST['icf_feedback'])) return;

    update_post_meta(
        $post_id,
        '_icf_feedback',
        sanitize_textarea_field($_POST['icf_feedback'])
    );
}
