<?php
/**
 * Fonctions spécifiques à vingtdixneuf.
 *
 * @since 1.0.0
 */

// Arrêter le chargement en cas d'accès direct.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ajout du style du thème parent à la queue des styles chargés.
 *
 * @since 1.0.0
 */
function vingtdixneuf_enqueue_parent_style() {
    wp_enqueue_style( 'twentynineteen', get_template_directory_uri() . '/style.css', array(), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'vingtdixneuf_enqueue_parent_style' );


if ( function_exists( 'buddypress' ) ) {
	require get_theme_file_path( 'inc/bp-custom.php' );
}
