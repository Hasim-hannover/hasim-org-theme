<?php
/**
 * Asset-Loading — Hasimuener Journal
 *
 * Bedarfsgesteuertes Laden von Styles und Scripts.
 * Preload für kritische Ressourcen (Fonts).
 * Defer für render-blockierende GP-Scripts.
 *
 * Strategie:
 * - Parent-CSS wird explizit eingereiht (korrekte Kaskade).
 * - Child-CSS hängt von Parent + GP-Main ab.
 * - JS nur auf Single-Views (TOC, Footnotes, Share).
 * - GP Auto-Enqueue für Child wird dedupliziert.
 *
 * @package Hasimuener_Journal
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/* -----------------------------------------
   Styles & Scripts
   ----------------------------------------- */

/**
 * Enqueue-Logik: Parent → Child → conditionals.
 *
 * Child-CSS hängt von zwei Handles ab:
 * - generatepress-style: Theme-Header (immer minimal)
 * - generate-style: GP main.min.css (Haupt-CSS)
 * So ist die Kaskade Parent → GP → Child garantiert.
 */
function hp_journal_enqueue_styles(): void {
	$theme_version = wp_get_theme()->get( 'Version' );

	// Parent-Theme — nötig für korrekte CSS-Kaskade
	wp_enqueue_style(
		'generatepress-style',
		get_template_directory_uri() . '/style.css',
		[],
		$theme_version
	);

	// Child-Theme — NACH Parent + GP main.min.css
	wp_enqueue_style(
		'hp-journal-style',
		get_stylesheet_directory_uri() . '/style.css',
		[ 'generatepress-style', 'generate-style' ],
		$theme_version
	);

	// Journal JS — auf allen Seiten (Header-Nav, Glossar-Tooltips, TOC, Share etc.)
	wp_enqueue_script(
		'hp-journal-js',
		get_stylesheet_directory_uri() . '/assets/js/journal.js',
		[],
		$theme_version,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'hp_journal_enqueue_styles' );

/* -----------------------------------------
   Duplikat-Bereinigung
   ----------------------------------------- */

/**
 * GP reiht die Child-style.css automatisch ein (handle: generate-child).
 * Da wir sie explizit als 'hp-journal-style' laden, entsteht ein Duplikat.
 * → Dequeue + Deregister auf Priorität 20 (nach GP-Enqueue).
 */
function hp_dequeue_duplicate_styles(): void {
	wp_dequeue_style( 'generate-child' );
	wp_deregister_style( 'generate-child' );
}
add_action( 'wp_enqueue_scripts', 'hp_dequeue_duplicate_styles', 20 );

/* -----------------------------------------
   Font-Preloading
   ----------------------------------------- */

/**
 * Kritische Schriften per <link rel="preload"> vorladen.
 *
 * Bricht die CSS → Font-Kette auf: Der Browser startet den
 * Font-Download parallel zum CSS-Parsing statt sequentiell
 * danach. Nur die drei auf der Startseite sichtbaren Gewichte.
 */
function hp_preload_critical_fonts(): void {
	$font_dir = get_stylesheet_directory_uri() . '/fonts';
	$fonts    = [
		'merriweather-v33-latin-300.woff2',
		'merriweather-v33-latin-700.woff2',
		'merriweather-v33-latin-900.woff2',
	];

	foreach ( $fonts as $file ) {
		printf(
			'<link rel="preload" href="%s/%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( $font_dir ),
			esc_attr( $file )
		);
	}
}
add_action( 'wp_head', 'hp_preload_critical_fonts', 1 );

/* -----------------------------------------
   RSS-Feed Discovery
   ----------------------------------------- */

/**
 * Gibt <link rel="alternate"> für den RSS-Feed im <head> aus.
 * WordPress entfernt feed_links standardmäßig nicht, aber
 * da wir den GP-Header ersetzen, stellen wir es explizit sicher.
 */
function hp_rss_feed_links(): void {
	printf(
		'<link rel="alternate" type="application/rss+xml" title="%s — Feed" href="%s" />' . "\n",
		esc_attr( get_bloginfo( 'name' ) ),
		esc_url( get_feed_link() )
	);
}
add_action( 'wp_head', 'hp_rss_feed_links', 2 );

/* -----------------------------------------
   Script-Defer (GP Menu)
   ----------------------------------------- */

/**
 * Setzt defer auf GP-Menü-Scripts (2 KiB).
 *
 * Diese Scripts blockieren das Rendering, werden aber erst
 * bei Nutzerinteraktion gebraucht. Mit defer: paralleler
 * Download, Ausführung erst nach HTML-Parsing.
 *
 * @param string $tag    Kompletter <script>-Tag.
 * @param string $handle WordPress-Handle des Scripts.
 * @param string $src    Script-URL.
 * @return string Modifizierter Tag.
 */
function hp_defer_gp_menu_script( string $tag, string $handle, string $src ): string {
	$defer_handles = [ 'generate-menu', 'generate-navigation' ];

	if ( in_array( $handle, $defer_handles, true ) && false === strpos( $tag, 'defer' ) ) {
		$tag = str_replace( ' src=', ' defer src=', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'hp_defer_gp_menu_script', 10, 3 );
