<?php
/**
 * Breadcrumbs — Hasimuener Journal
 *
 * BreadcrumbList JSON-LD Schema (unsichtbar).
 * Gibt Google strukturierte Pfad-Daten für die Suchergebnisse,
 * ohne sichtbare Navigation im Frontend.
 *
 * @package Hasimuener_Journal
 * @since   5.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Baut die Breadcrumb-Items für den aktuellen Seitenkontext
 * und gibt BreadcrumbList JSON-LD im <head> aus.
 */
function hp_breadcrumbs_schema_output(): void {
	if ( is_front_page() ) {
		return;
	}

	$items = [];

	$items[] = [
		'name' => 'Startseite',
		'url'  => home_url( '/' ),
	];

	if ( is_singular( 'essay' ) ) {
		$items[] = [
			'name' => 'Essays',
			'url'  => get_post_type_archive_link( 'essay' ),
		];
		$topics = get_the_terms( get_the_ID(), 'topic' );
		if ( $topics && ! is_wp_error( $topics ) ) {
			$items[] = [
				'name' => $topics[0]->name,
				'url'  => get_term_link( $topics[0] ),
			];
		}
		$items[] = [ 'name' => get_the_title() ];

	} elseif ( is_singular( 'note' ) ) {
		$items[] = [
			'name' => 'Notizen',
			'url'  => get_post_type_archive_link( 'note' ),
		];
		$topics = get_the_terms( get_the_ID(), 'topic' );
		if ( $topics && ! is_wp_error( $topics ) ) {
			$items[] = [
				'name' => $topics[0]->name,
				'url'  => get_term_link( $topics[0] ),
			];
		}
		$items[] = [ 'name' => get_the_title() ];

	} elseif ( is_singular( 'glossar' ) ) {
		$items[] = [
			'name' => 'Glossar',
			'url'  => get_post_type_archive_link( 'glossar' ),
		];
		$items[] = [ 'name' => get_the_title() ];

	} elseif ( is_singular( 'page' ) ) {
		$items[] = [ 'name' => get_the_title() ];

	} elseif ( is_post_type_archive( 'essay' ) ) {
		$items[] = [ 'name' => 'Essays' ];

	} elseif ( is_post_type_archive( 'note' ) ) {
		$items[] = [ 'name' => 'Notizen' ];

	} elseif ( is_post_type_archive( 'glossar' ) ) {
		$items[] = [ 'name' => 'Glossar' ];

	} elseif ( is_tax( 'topic' ) ) {
		$items[] = [ 'name' => 'Themenfelder' ];
		$items[] = [ 'name' => single_term_title( '', false ) ];

	} elseif ( is_search() ) {
		$items[] = [ 'name' => 'Suche' ];

	} elseif ( is_404() ) {
		$items[] = [ 'name' => '404' ];
	}

	if ( count( $items ) < 2 ) {
		return;
	}

	// JSON-LD ausgeben
	$list_items = [];
	foreach ( $items as $i => $item ) {
		$entry = [
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $item['name'],
		];
		if ( isset( $item['url'] ) ) {
			$entry['item'] = $item['url'];
		}
		$list_items[] = $entry;
	}

	$schema = [
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list_items,
	];

	echo "\n<!-- Hasim Üner: BreadcrumbList JSON-LD -->\n";
	echo '<script type="application/ld+json">';
	echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	echo "</script>\n";
}
add_action( 'wp_head', 'hp_breadcrumbs_schema_output', 6 );

	$schema = [
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list_items,
	];

	add_action( 'wp_head', function () use ( $schema ) {
		echo "\n<!-- Hasim Üner: BreadcrumbList JSON-LD -->\n";
		echo '<script type="application/ld+json">';
		echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		echo "</script>\n";
	}, 6 );
}
