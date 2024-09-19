<?php
/**
 * Plugin Name: Ads.txt Editor
 * Description: A simple plugin to edit the ads.txt file directly from the WordPress admin.
 * Version: 1.0
 * Author: kkawataki.com
 */

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Function to create the admin menu under Settings
function ate_create_admin_menu() {
    add_options_page(
        'Ads.txt Editor',          // Page title
        'Ads.txt Editor',          // Menu title
        'manage_options',          // Capability
        'ads-txt-editor',          // Menu slug
        'ate_admin_page_callback'  // Callback function
    );
}
add_action('admin_menu', 'ate_create_admin_menu');

// Callback function for the admin page
function ate_admin_page_callback() {
    // Check if the form has been submitted
    if (isset($_POST['ate_ads_txt_content'])) {
        // Verify the nonce for security
        check_admin_referer('ate_save_ads_txt', 'ate_ads_txt_nonce');

        // Sanitize the input and update the ads.txt file
        $ads_txt_content = sanitize_textarea_field($_POST['ate_ads_txt_content']);
        ate_update_ads_txt($ads_txt_content);
        echo '<div class="updated"><p>ads.txt file has been updated.</p></div>';
    }

    // Read the current ads.txt content
    $ads_txt_content = ate_get_ads_txt_content();
    ?>
    <div class="wrap">
        <h1>Ads.txt Editor</h1>
        <form method="post" action="">
            <?php wp_nonce_field('ate_save_ads_txt', 'ate_ads_txt_nonce'); ?>
            <textarea name="ate_ads_txt_content" rows="20" cols="100" class="large-text"><?php echo esc_textarea($ads_txt_content); ?></textarea>
            <?php submit_button('Save Changes'); ?>
        </form>
    </div>
    <?php
}

// Function to get the current content of ads.txt
function ate_get_ads_txt_content() {
    $file_path = ABSPATH . 'ads.txt';

    // Check if the file exists
    if (file_exists($file_path)) {
        return file_get_contents($file_path);
    }

    return ''; // Return an empty string if the file does not exist
}

// Function to update the content of ads.txt
function ate_update_ads_txt($content) {
    $file_path = ABSPATH . 'ads.txt';

    // Write the content to the ads.txt file
    file_put_contents($file_path, $content);
}