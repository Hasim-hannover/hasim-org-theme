<?php
/**
 * Breadcrumbs — Hasimuener Journal
 *
 * Visuelle Breadcrumb-Navigation + BreadcrumbList JSON-LD Schema.
 * Zeigt Lesern ihre Position in der Wissensstruktur.
 *
 * Aufruf: hp_breadcrumbs() in Templates — gibt die Breadcrumb-
 * Navigation aus und registriert das Schema für wp_head.
 *
 * @package Hasimuener_Journal
 * @since   5.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gibt die Breadcrumb-Navigation aus und registriert Schema.
 *
 * Unterstützte Kontexte:
 * - Single Essay/Note/Glossar (mit optionalem Topic)
 * - Archive Essay/Note/Glossar
 * - Taxonomy topic
 * - Seiten (page)
 * - Suche, 404
 */
function hp_breadcrumbs(): void {
	if ( is_front_page() ) {
		return;
	}

	$items = [];

	// Startseite immer als erstes Element
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

	// HTML-Ausgabe
	echo '<nav class="hp-breadcrumbs" aria-label="Brotkrümel-Navigation">';
	echo '<ol class="hp-breadcrumbs__list">';

	foreach ( $items as $i => $item ) {
		$is_last = ( $i === count( $items ) - 1 );

		echo '<li class="hp-breadcrumbs__item">';
		if ( ! $is_last && isset( $item['url'] ) ) {
			printf(
				'<a class="hp-breadcrumbs__link" href="%s">%s</a>',
				esc_url( $item['url'] ),
				esc_html( $item['name'] )
			);
		} else {
			printf(
				'<span class="hp-breadcrumbs__current" aria-current="page">%s</span>',
				esc_html( $item['name'] )
			);
		}
		echo '</li>';
	}

	echo '</ol>';
	echo '</nav>';

	// Schema registrieren für wp_head (einmalig)
	hp_breadcrumbs_schema( $items );
}

/**
 * Gibt BreadcrumbList JSON-LD aus.
 *
 * @param array $items Breadcrumb-Items mit 'name' und optionalem 'url'.
 */
function hp_breadcrumbs_schema( array $items ): void {
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

	add_action( 'wp_head', function () use ( $schema ) {
		echo "\n<!-- Hasim Üner: BreadcrumbList JSON-LD -->\n";
		echo '<script type="application/ld+json">';
		echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		echo "</script>\n";
	}, 6 );
}
