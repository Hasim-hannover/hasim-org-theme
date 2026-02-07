<?php
/**
 * Hasimuener Journal — functions.php
 * 
 * GeneratePress Child Theme.
 * Custom Post Types, Taxonomie, Lesedauer, Body-Klassen, Enqueues.
 *
 * @package Hasimuener_Journal
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

/* =========================================
   1. ENQUEUES — Styles & Scripts
   ========================================= */

add_action( 'wp_enqueue_scripts', 'hp_journal_enqueue_styles' );
function hp_journal_enqueue_styles() {
    $theme_version = wp_get_theme()->get( 'Version' );

    // Parent Theme
    wp_enqueue_style(
        'generatepress-style',
        get_template_directory_uri() . '/style.css',
        array(),
        $theme_version
    );

    // Child Theme
    wp_enqueue_style(
        'hp-journal-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'generatepress-style' ),
        $theme_version
    );

    // Journal JS (TOC, Footnotes) — nur auf Singles laden
    if ( is_singular( array( 'essay', 'note', 'post' ) ) ) {
        wp_enqueue_script(
            'hp-journal-js',
            get_stylesheet_directory_uri() . '/assets/js/journal.js',
            array(),
            $theme_version,
            true
        );
    }
}

/* =========================================
   2. CUSTOM POST TYPES
   ========================================= */

add_action( 'init', 'hp_register_post_types' );
function hp_register_post_types() {

    // Essay
    register_post_type( 'essay', array(
        'labels' => array(
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
        ),
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => array( 'slug' => 'essays', 'with_front' => false ),
        'menu_icon'     => 'dashicons-media-text',
        'menu_position' => 5,
        'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
        'taxonomies'    => array( 'topic' ),
        'show_in_rest'  => true,
    ) );

    // Note (Notiz)
    register_post_type( 'note', array(
        'labels' => array(
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
        ),
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => array( 'slug' => 'notizen', 'with_front' => false ),
        'menu_icon'     => 'dashicons-edit-page',
        'menu_position' => 6,
        'supports'      => array( 'title', 'editor', 'excerpt', 'revisions', 'custom-fields' ),
        'taxonomies'    => array( 'topic' ),
        'show_in_rest'  => true,
    ) );
}

/* =========================================
   3. CUSTOM TAXONOMY — Themenfeld
   ========================================= */

add_action( 'init', 'hp_register_taxonomies' );
function hp_register_taxonomies() {

    register_taxonomy( 'topic', array( 'essay', 'note', 'post' ), array(
        'labels' => array(
            'name'          => 'Themenfelder',
            'singular_name' => 'Themenfeld',
            'search_items'  => 'Themenfelder durchsuchen',
            'all_items'     => 'Alle Themenfelder',
            'edit_item'     => 'Themenfeld bearbeiten',
            'add_new_item'  => 'Neues Themenfeld',
            'new_item_name' => 'Neuer Themenfeld-Name',
            'menu_name'     => 'Themenfelder',
        ),
        'public'       => true,
        'hierarchical' => true,
        'rewrite'      => array( 'slug' => 'thema', 'with_front' => false ),
        'show_in_rest' => true,
    ) );
}

/* =========================================
   4. LESEDAUER BERECHNEN
   ========================================= */

/**
 * Gibt die geschätzte Lesedauer in Minuten zurück.
 * 200 Wörter/Minute — konservativ für anspruchsvolle Texte.
 *
 * @param int|null $post_id Post-ID (default: aktueller Post).
 * @return string z.B. "4 Min. Lesezeit"
 */
function hp_reading_time( $post_id = null ) {
    $post_id = $post_id ?: get_the_ID();
    $content = get_post_field( 'post_content', $post_id );
    $words   = str_word_count( wp_strip_all_tags( $content ) );
    $minutes = max( 1, (int) ceil( $words / 200 ) );

    return sprintf( '%d Min. Lesezeit', $minutes );
}

/* =========================================
   5. BODY-KLASSEN
   ========================================= */

add_filter( 'body_class', 'hp_custom_body_classes' );
function hp_custom_body_classes( $classes ) {

    if ( is_singular( 'essay' ) ) {
        $classes[] = 'single-essay';
        $classes[] = 'editorial-longform';
    }

    if ( is_singular( 'note' ) ) {
        $classes[] = 'single-note';
        $classes[] = 'editorial-short';
    }

    if ( is_front_page() ) {
        $classes[] = 'journal-home';
    }

    if ( is_post_type_archive( 'essay' ) ) {
        $classes[] = 'archive-essays';
    }

    if ( is_post_type_archive( 'note' ) ) {
        $classes[] = 'archive-notes';
    }

    return $classes;
}

/* =========================================
   6. FLUSH REWRITE RULES (einmalig)
   ========================================= */

add_action( 'after_switch_theme', 'hp_flush_rewrite_rules' );
function hp_flush_rewrite_rules() {
    hp_register_post_types();
    hp_register_taxonomies();
    flush_rewrite_rules();
}