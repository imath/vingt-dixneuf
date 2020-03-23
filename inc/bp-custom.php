<?php
/**
 * Fonctions spécifiques à BuddyPress.
 *
 * Pour plus d'autonomie vis à vis des thèmes, je vous recommande de positionner ce fichier
 * dans le répertoire `/wp-content/plugins/` de votre WordPress.
 * @see https://codex.buddypress.org/themes/bp-custom-php/
 */

// Arrêter le chargement en cas d'accès direct.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * S'accroche au hook `bp_register_member_types` de BuddyPress pour définir
 * les types de membre disponibles pour la communauté.
 */
function bbg_register_member_types_with_directory() {
	$member_types = array(
		'staff' => array(
			'labels' => array(
				'name' => __( 'Staff', 'text-domain' ),
			),
			'has_directory' => 'staff', // ! Utilisé comme terminaison d'URL.
		),
		'participants'  => array(
			'labels' => array(
				'name'          => __( 'Participants', 'text-domain' ),
				'singular_name' => __( 'Participant', 'text-domain' ),
			),
			'has_directory' => 'participant', // ! Utilisé comme terminaison d'URL.
		),
	);

	foreach ( $member_types as $key_type => $args_type ) {
		bp_register_member_type( $key_type, $args_type );
	}
}
add_action( 'bp_register_member_types', 'bbg_register_member_types_with_directory' );
