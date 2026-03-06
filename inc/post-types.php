<?php
/**
 * Custom Post Types — Hasimuener Journal
 *
 * Essay: Langform-Analyse (ScholarlyArticle / Longread).
 * Note:  Kurze Beobachtung, Fragment, Quellenverweis.
 *
 * Beide CPTs nutzen Gutenberg (show_in_rest), die Taxonomie
 * „topic" und Custom Fields für Metadaten-Erweiterungen.
 *
 * @package Hasimuener_Journal
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registriert die Custom Post Types „essay" und „note".
 *
 * Archiv-Slugs (/essays/, /notizen/) sind SEO-freundlich
 * und unabhängig von der WordPress-Permalink-Basis.
 */
function hp_register_post_types(): void {

	// --- Essay ---
	register_post_type( 'essay', [
		'labels' => [
			'name'               => 'Essays',
			'singular_name'      => 'Essay',
			'add_new'            => 'Neuer Essay',
			'add_new_item'       => 'Neuen Essay erstellen',
			'edit_item'          => 'Essay bearbeiten',
			'view_item'          => 'Essay ansehen',
			'all_items'          => 'Alle Essays',
			'search_items'       => 'Essays durchsuchen',
			'not_found'          => 'Keine Essays gefunden.',
			'not_found_in_trash' => 'Keine Essays im Papierkorb.',
		],
		'public'        => true,
		'has_archive'   => true,
		'rewrite'       => [ 'slug' => 'essays', 'with_front' => false ],
		'menu_icon'     => 'dashicons-media-text',
		'menu_position' => 5,
		'supports'      => [ 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields', 'comments' ],
		'taxonomies'    => [ 'topic' ],
		'show_in_rest'  => true,
	] );

	// --- Notiz ---
	register_post_type( 'note', [
		'labels' => [
			'name'               => 'Notizen',
			'singular_name'      => 'Notiz',
			'add_new'            => 'Neue Notiz',
			'add_new_item'       => 'Neue Notiz erstellen',
			'edit_item'          => 'Notiz bearbeiten',
			'view_item'          => 'Notiz ansehen',
			'all_items'          => 'Alle Notizen',
			'search_items'       => 'Notizen durchsuchen',
			'not_found'          => 'Keine Notizen gefunden.',
			'not_found_in_trash' => 'Keine Notizen im Papierkorb.',
		],
		'public'        => true,
		'has_archive'   => true,
		'rewrite'       => [ 'slug' => 'notizen', 'with_front' => false ],
		'menu_icon'     => 'dashicons-edit-page',
		'menu_position' => 6,
		'supports'      => [ 'title', 'editor', 'excerpt', 'revisions', 'custom-fields', 'comments' ],
		'taxonomies'    => [ 'topic' ],
		'show_in_rest'  => true,
	] );
}
add_action( 'init', 'hp_register_post_types' );
