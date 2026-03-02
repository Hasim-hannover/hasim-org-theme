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
		'macht-und-technologie' => [
			'name'        => 'Macht & Technologie',
			'description' => 'Wie digitale Systeme, Algorithmen und Plattformen Machtverhältnisse formen — und wer davon profitiert.',
		],
		'identitaet-und-widerstand' => [
			'name'        => 'Identität & Widerstand',
			'description' => 'Erfahrungen zwischen Kulturen, Sprachen und Systemen. Selbstbestimmung, Erinnerung, Diaspora.',
		],
		'medien-und-narrative' => [
			'name'        => 'Medien & Narrative',
			'description' => 'Welche Geschichten erzählt werden, welche nicht — und warum das wichtig ist.',
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

/* -----------------------------------------
   Einmalige Migration: v1 → v2 Themenfelder
   ----------------------------------------- */

/**
 * Einmalige Migration: Alte Themenfelder → neue Struktur.
 * Nach erfolgreichem Lauf die Option setzen und diese Funktion entfernen.
 */
function hp_migrate_topics_v2(): void {
	if ( get_option( 'hp_topics_migrated_v2' ) ) {
		return;
	}

	// Alte Terms löschen (nur wenn keine Posts zugeordnet)
	$old_slugs = [
		'digitale-macht',
		'code-und-politik',
		'gesellschaft-und-wandel',
		'identitaet-und-zugehoerigkeit',
	];

	foreach ( $old_slugs as $slug ) {
		$term = get_term_by( 'slug', $slug, 'topic' );
		if ( $term && $term->count === 0 ) {
			wp_delete_term( $term->term_id, 'topic' );
		}
	}

	// Flag zurücksetzen damit neue Defaults angelegt werden
	delete_option( 'hp_default_topics_created' );

	update_option( 'hp_topics_migrated_v2', true );
}
add_action( 'init', 'hp_migrate_topics_v2', 15 );
