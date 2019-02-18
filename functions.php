<?php
/**
 * fonctions spécifiques à vingtdixneuf.
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Enqueues the Parent Theme's CSS.
 *
 * @since 1.0.0
 */
function vingtdixneuf_enqueue_parent_style() {
	// Enqueue the TwentyNineteen stylesheet.
    wp_enqueue_style( 'twentynineteen', get_template_directory_uri() . '/style.css', array(), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'vingtdixneuf_enqueue_parent_style' );
