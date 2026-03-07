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
 * Liefert die kuratierte Themenstruktur für das Journal.
 *
 * @return array<string, array{name:string, description:string}>
 */
function hp_get_default_topics_config(): array {
	return [
		'macht-und-ordnung' => [
			'name'        => 'Macht & Ordnung',
			'description' => 'Staat, Hierarchie, Technologie, Kontrolle',
		],
		'erinnerung-und-identitaet' => [
			'name'        => 'Erinnerung & Identität',
			'description' => 'Gedächtnis, Widerstand, Zugehörigkeit, Wandel',
		],
		'medien-und-sprache' => [
			'name'        => 'Medien & Sprache',
			'description' => 'Narrative, Diskurs, Sichtbarkeit, Medienkritik',
		],
	];
}

/**
 * Legt fest, welche alten Slugs in welche neue Themenstruktur überführt werden.
 *
 * @return array<string, string[]>
 */
function hp_get_topic_migration_map(): array {
	return [
		'macht-und-ordnung' => [
			'macht-und-technologie',
			'digitale-macht',
			'code-und-politik',
		],
		'erinnerung-und-identitaet' => [
			'identitaet-und-widerstand',
			'identitaet-und-zugehoerigkeit',
			'gesellschaft-und-wandel',
		],
		'medien-und-sprache' => [
			'medien-und-narrative',
		],
	];
}

/**
 * Liefert die kuratierten Themen in definierter Reihenfolge.
 *
 * @param bool $hide_empty Ob leere Terms ausgeblendet werden sollen.
 * @return array<int, WP_Term>
 */
function hp_get_curated_topics( bool $hide_empty = false ): array {
	$terms = [];

	foreach ( hp_get_default_topics_config() as $slug => $topic_data ) {
		unset( $topic_data );

		$term = get_term_by( 'slug', $slug, 'topic' );

		if ( ! $term ) {
			continue;
		}

		if ( $hide_empty && 0 === (int) $term->count ) {
			continue;
		}

		$terms[] = $term;
	}

	return $terms;
}

/**
 * Führt Objektzuordnungen eines alten Terms in einen Zielterm über.
 *
 * @param int $target_term_id Ziel-Term-ID.
 * @param int $source_term_id Quell-Term-ID.
 */
function hp_merge_topic_term_into_target( int $target_term_id, int $source_term_id ): void {
	$object_ids = get_objects_in_term( $source_term_id, 'topic' );

	if ( ! is_wp_error( $object_ids ) ) {
		foreach ( array_map( 'intval', $object_ids ) as $object_id ) {
			wp_add_object_terms( $object_id, [ $target_term_id ], 'topic' );
			wp_remove_object_terms( $object_id, [ $source_term_id ], 'topic' );
		}
	}

	wp_delete_term( $source_term_id, 'topic' );
}

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
	$defaults = hp_get_default_topics_config();

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

/* -----------------------------------------
   Einmalige Migration: v2/vLegacy → v3 Themenfelder
   ----------------------------------------- */

/**
 * Überführt ältere Topic-Strukturen in die neue kuratierte Dreierstruktur.
 *
 * Bestehende Zuordnungen zu Essays und Notizen bleiben erhalten.
 */
function hp_migrate_topics_v3(): void {
	if ( get_option( 'hp_topics_migrated_v3' ) ) {
		return;
	}

	$defaults      = hp_get_default_topics_config();
	$migration_map = hp_get_topic_migration_map();

	foreach ( $defaults as $target_slug => $topic_data ) {
		$target_term = get_term_by( 'slug', $target_slug, 'topic' );

		if ( ! $target_term && isset( $migration_map[ $target_slug ] ) ) {
			foreach ( $migration_map[ $target_slug ] as $legacy_slug ) {
				$legacy_term = get_term_by( 'slug', $legacy_slug, 'topic' );

				if ( $legacy_term ) {
					wp_update_term( $legacy_term->term_id, 'topic', [
						'name'        => $topic_data['name'],
						'slug'        => $target_slug,
						'description' => $topic_data['description'],
					] );

					$target_term = get_term( $legacy_term->term_id, 'topic' );
					break;
				}
			}
		}

		if ( ! $target_term ) {
			$inserted = wp_insert_term( $topic_data['name'], 'topic', [
				'slug'        => $target_slug,
				'description' => $topic_data['description'],
			] );

			if ( ! is_wp_error( $inserted ) ) {
				$target_term = get_term( (int) $inserted['term_id'], 'topic' );
			}
		} else {
			wp_update_term( $target_term->term_id, 'topic', [
				'name'        => $topic_data['name'],
				'slug'        => $target_slug,
				'description' => $topic_data['description'],
			] );

			$target_term = get_term( $target_term->term_id, 'topic' );
		}

		if ( ! $target_term || is_wp_error( $target_term ) || empty( $migration_map[ $target_slug ] ) ) {
			continue;
		}

		foreach ( $migration_map[ $target_slug ] as $legacy_slug ) {
			$legacy_term = get_term_by( 'slug', $legacy_slug, 'topic' );

			if ( ! $legacy_term || (int) $legacy_term->term_id === (int) $target_term->term_id ) {
				continue;
			}

			hp_merge_topic_term_into_target( (int) $target_term->term_id, (int) $legacy_term->term_id );
		}
	}

	delete_option( 'hp_default_topics_created' );
	hp_create_default_topics();
	update_option( 'hp_topics_migrated_v3', true );
}
add_action( 'init', 'hp_migrate_topics_v3', 30 );
