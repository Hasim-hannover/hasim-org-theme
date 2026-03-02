<?php
/**
 * Taxonomien — Hasimuener Journal
 *
 * „topic" (Themenfeld): Hierarchische Taxonomie, die Essays,
 * Notizen und Standard-Posts thematisch bündelt.
 *
 * Default-Terms werden einmalig bei Theme-Aktivierung oder
 * erstem Lauf angelegt (Option-Flag verhindert Mehrfachausführung).
 *
 * @package Hasimuener_Journal
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registriert die Taxonomie „topic" für thematische Zuordnung.
 *
 * Hierarchisch wie Kategorien, aber semantisch als
 * redaktionelles Themenfeld gedacht — nicht als
 * Navigations-Kategorie.
 */
function hp_register_taxonomies(): void {

	register_taxonomy( 'topic', [ 'essay', 'note', 'post' ], [
		'labels' => [
			'name'          => 'Themenfelder',
			'singular_name' => 'Themenfeld',
			'search_items'  => 'Themenfelder durchsuchen',
			'all_items'     => 'Alle Themenfelder',
			'edit_item'     => 'Themenfeld bearbeiten',
			'add_new_item'  => 'Neues Themenfeld',
			'new_item_name' => 'Neuer Themenfeld-Name',
			'menu_name'     => 'Themenfelder',
		],
		'public'       => true,
		'hierarchical' => true,
		'rewrite'      => [ 'slug' => 'thema', 'with_front' => false ],
		'show_in_rest' => true,
	] );
}
add_action( 'init', 'hp_register_taxonomies' );

/* -----------------------------------------
   Standard-Themenfelder (Seeding)
   ----------------------------------------- */

/**
 * Prüft per Option-Flag, ob Default-Topics schon angelegt wurden.
 * Läuft auf init mit Priorität 20 (nach Taxonomy-Registrierung).
 */
function hp_maybe_create_default_topics(): void {
	if ( get_option( 'hp_default_topics_created' ) ) {
		return;
	}
	hp_create_default_topics();
	update_option( 'hp_default_topics_created', true );
}
add_action( 'init', 'hp_maybe_create_default_topics', 20 );

/**
 * Legt die redaktionellen Standard-Themenfelder an.
 *
 * Idempotent: Bestehende Terme werden nicht überschrieben.
 * Kann auch manuell aufgerufen werden (z. B. nach Staging-Reset).
 */
function hp_create_default_topics(): void {
	$defaults = [
		'digitale-macht' => [
			'name'        => 'Digitale Macht',
			'description' => 'Algorithmen, Plattformen, Überwachung — wer kontrolliert die Technologie, die uns kontrolliert?',
		],
		'code-und-politik' => [
			'name'        => 'Code & Politik',
			'description' => 'Wenn Infrastruktur politisch wird: Software, Systeme und ihre gesellschaftlichen Folgen.',
		],
		'gesellschaft-und-wandel' => [
			'name'        => 'Gesellschaft & Wandel',
			'description' => 'Wie wir zusammenleben — und wie es anders ginge.',
		],
		'medien-und-narrative' => [
			'name'        => 'Medien & Narrative',
			'description' => 'Welche Geschichten erzählt werden, welche nicht — und warum das wichtig ist.',
		],
		'identitaet-und-zugehoerigkeit' => [
			'name'        => 'Identität & Zugehörigkeit',
			'description' => 'Erfahrungen zwischen Kulturen, Sprachen und Systemen.',
		],
	];

	foreach ( $defaults as $slug => $data ) {
		if ( ! term_exists( $slug, 'topic' ) ) {
			wp_insert_term( $data['name'], 'topic', [
				'slug'        => $slug,
				'description' => $data['description'],
			] );
		}
	}
}
