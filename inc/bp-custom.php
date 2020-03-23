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
function vingtdixneuf_register_member_types_with_directory() {
	/**
	 * Pour plus d'informations sur les types de membre BuddyPress
	 *
	 * @see https://codex.buddypress.org/developer/member-types/
	 */
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
add_action( 'bp_register_member_types', 'vingtdixneuf_register_member_types_with_directory' );

/**
 * Force l'affichage des membres du même type que celui de l'utilisateur connecté
 * dans le répertoire des membres.
 */
function vingtdixneuf_get_members_directory_permalink( $url = '' ) {
	$current_user_id     = get_current_user_id();
	$current_member_type = bp_get_member_type( $current_user_id );

	if ( $current_user_id && 'participants' === $current_member_type ) {
		$url = trailingslashit( $url ) . 'type/participant/';
	} elseif ( $current_user_id && 'staff' === $current_member_type ) {
		$url = trailingslashit( $url ) . 'type/staff/';
	}

	return $url;
}
add_filter( 'bp_get_members_directory_permalink', 'vingtdixneuf_get_members_directory_permalink' );

/**
 * Donne accès à certaine page BuddyPress en fonction du type de membre connecté.
 */
function vingtdixneuf_user_router() {
	$current_component = bp_current_component();

	// Non connecté.
	if ( $current_component && ! is_user_logged_in() ) {
		wp_die(
			'<h1>' . esc_html__( 'Accès restreint.', 'text-domain' ) . '</h1>' .
			'<p>' . esc_html__( 'Vous devez être authentifié pour afficher cette page', 'text-domain' ) . '</p>',
			403
		);
	}

	$current_user_id     = get_current_user_id();
	$current_member_type = bp_get_member_type( $current_user_id );

	// Les pages liées aux membres.
	if ( bp_is_user() || 'members' === $current_component ) {
		// Les membres d'un type peuvent consulter le repertoire des membres du même type.
		if ( 'members' === $current_component && $current_member_type && $current_member_type !== bp_get_current_member_type() ) {
			wp_die(
				'<h1>' . esc_html__( 'Accès restreint.', 'text-domain' ) . '</h1>' .
				'<p>' . esc_html__( 'Votre profil ne vous permet pas de consulter cette page', 'text-domain' ) . '</p>',
				403
			);
		}

		// Les membres d'un type peuvent consulter les pages des membres du même type.
		if ( bp_is_user() && $current_member_type !== bp_get_member_type( bp_displayed_user_id() ) ) {
			wp_die(
				'<h1>' . esc_html__( 'Accès restreint.', 'text-domain' ) . '</h1>' .
				'<p>' . esc_html__( 'Votre profil ne vous permet pas de consulter ce membre.', 'text-domain' ) . '</p>',
				403
			);
		}

		/**
		 * Pour les autres pages BuddyPress on utilise le menu correspondant au type de membre
		 * pour restreindre l'accès.
		 */
	} elseif ( is_buddypress() && $current_member_type ) {
		$menu_items = wp_get_nav_menu_items( $current_member_type );
		$urls       = wp_list_pluck( $menu_items, 'url' );

		$current_url = wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
		$has_access  = false;

		foreach ( $urls as $url ) {
			if ( false !== strpos( $url, $current_url ) ) {
				$has_access = true;
			}
		}

		// Cas spécifique des groupes.
		if ( bp_is_group() && in_array( bp_get_groups_directory_permalink(), $urls, true ) ) {
			$has_access = true;
		}

		if ( ! $has_access ) {
			wp_die(
				'<h1>' . esc_html__( 'Accès restreint.', 'text-domain' ) . '</h1>' .
				'<p>' . esc_html__( 'Votre profil ne vous permet pas de consulter cette zone du site.', 'text-domain' ) . '</p>',
				403
			);
		}
	}
}
add_action( 'bp_screens', 'vingtdixneuf_user_router' );
