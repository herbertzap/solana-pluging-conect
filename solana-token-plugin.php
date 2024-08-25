<?php
/**
 * Plugin Name: Solana Lottery Plugin
 * Description: Un plugin que integra la lotería Solana utilizando @solana/web3.js y @solana/actions.
 * Version: 1.0
 * Author: Tu Nombre
 * License: GPL2
 */

defined('ABSPATH') or die('No script kiddies please!');

// Enqueue scripts for the login page
function solana_enqueue_login_scripts() {
    if ('wp-login.php' === $GLOBALS['pagenow']) {
        wp_enqueue_script('solana-web3-js', 'https://unpkg.com/@solana/web3.js@rc/dist/index.development.js', [], null, true);
        wp_enqueue_script('solana-login-script', plugins_url('/js/solana-login.js', __FILE__), ['solana-web3-js'], null, true);
    }
}
add_action('login_enqueue_scripts', 'solana_enqueue_login_scripts');

// Add the Solana login button to the login form
function solana_add_login_button() {
    echo '<div style="margin-top: 20px; text-align: center;">
            <button id="connect-wallet" class="button button-primary">Iniciar sesión con Solana</button>
          </div>';
}
add_action('login_form', 'solana_add_login_button');

// Register REST API endpoint for Solana authentication
add_action('rest_api_init', function() {
    register_rest_route('solana-login/v1', '/authenticate', array(
        'methods' => 'POST',
        'callback' => 'solana_verify_signature',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('solana/v1', '/lottery', array(
        'methods' => 'GET',
        'callback' => 'solana_lottery_get',
    ));

    register_rest_route('solana/v1', '/lottery', array(
        'methods' => 'POST',
        'callback' => 'solana_lottery_post',
    ));
});

// Verify Solana signature and authenticate user
function solana_verify_signature(WP_REST_Request $request) {
    $publicKey = $request->get_param('publicKey');
    $signature = $request->get_param('signature');
    $message = $request->get_param('message');

    error_log('PublicKey: ' . $publicKey);
    error_log('Signature: ' . $signature);
    error_log('Message: ' . $message);

    // Implement the actual signature verification logic here
    $is_valid_signature = verify_solana_signature($publicKey, $signature, $message);

    if ($is_valid_signature) {
        $user = get_user_by('email', $publicKey . '@solana.login');
        if (!$user) {
            // Create new user if not exists
            $user_id = wp_create_user($publicKey, wp_generate_password(), $publicKey . '@solana.login');
            $user = get_user_by('id', $user_id);
        }

        // Log in the user
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);

        return new WP_REST_Response(['success' => true, 'redirect_url' => home_url()]);
    } else {
        return new WP_REST_Response(['success' => false, 'message' => 'Autenticación fallida'], 401);
    }
}

// Function to simulate signature verification
function verify_solana_signature($publicKey, $signature, $message) {
    // Implement actual signature verification logic here
    return true; // Simulated successful verification
}


// Enqueue Solana Actions script for the lottery page
function solana_lottery_enqueue_scripts() {
    if (is_front_page() || is_page('lottery')) {
        wp_enqueue_script('solana-web3-js', 'https://unpkg.com/@solana/web3.js@rc/dist/index.development.js', [], null, true);
        wp_enqueue_script('solana-actions-js', 'https://unpkg.com/@solana/actions@latest/dist/index.iife.js', ['solana-web3-js'], null, true);
        wp_enqueue_script('solana-lottery', plugins_url('/js/solana-lottery.js', __FILE__), ['solana-web3-js', 'solana-actions-js'], null, true);
    }
}
add_action('wp_enqueue_scripts', 'solana_lottery_enqueue_scripts', 20);

// Add the lottery button to the front page
function solana_lottery_button() {
    if (is_front_page() || is_page('lottery')) {
        echo '<div style="text-align: center; margin-top: 20px;">
                <button id="buy-ticket" class="button button-primary">Comprar Ticket de Lotería</button>
              </div>';
    }
}
add_action('wp_footer', 'solana_lottery_button');
