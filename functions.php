<?php
/**
 * Hasimuener Journal — functions.php
 * 
 * GeneratePress Child Theme.
 * Custom Post Types, Taxonomie, Lesedauer, Body-Klassen,
 * Social-Teaser Meta, JSON-LD Schema, Enqueues.
 *
 * @package Hasimuener_Journal
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

/* =========================================
   0. GENERATEPRESS — Standard-Meta deaktivieren
   ========================================= */

/**
 * Entfernt die GeneratePress-eigene Ausgabe von Datum, Autor
 * und Post-Meta auf unseren Custom Post Types.
 *
 * Strategie 1: remove_action (verschiedene Prioritäten durchprobieren)
 * Strategie 2: GP-Filter → leeren String zurückgeben
 * Strategie 3: CSS-Fallback (nuclear option)
 */

// Strategie 1: Hooks entfernen (Priority 10 + 20 + 30)
add_action( 'wp', 'hp_disable_gp_post_meta' );
function hp_disable_gp_post_meta(): void {
    if ( ! is_singular( [ 'essay', 'note' ] ) ) {
        return;
    }

    foreach ( [ 10, 15, 20, 30 ] as $prio ) {
        remove_action( 'generate_after_entry_title', 'generate_post_meta', $prio );
        remove_action( 'generate_after_entry_content', 'generate_footer_meta', $prio );
        remove_action( 'generate_after_entry_title', 'generate_entry_meta_header', $prio );
    }
}

// Strategie 2: GP-Output-Filter → leer
add_filter( 'generate_post_date_output', 'hp_suppress_gp_meta_on_cpt' );
add_filter( 'generate_post_author_output', 'hp_suppress_gp_meta_on_cpt' );
add_filter( 'generate_post_categories_output', 'hp_suppress_gp_meta_on_cpt' );
add_filter( 'generate_post_tags_output', 'hp_suppress_gp_meta_on_cpt' );
add_filter( 'generate_post_comments_link_output', 'hp_suppress_gp_meta_on_cpt' );
function hp_suppress_gp_meta_on_cpt( string $output ): string {
    if ( is_singular( [ 'essay', 'note' ] ) ) {
        return '';
    }
    return $output;
}

// Strategie 3: Ganzen Entry-Header/Footer-Meta-Container verstecken
add_filter( 'generate_header_entry_meta_items', 'hp_strip_gp_meta_items', 10, 1 );
add_filter( 'generate_footer_entry_meta_items', 'hp_strip_gp_meta_items', 10, 1 );
function hp_strip_gp_meta_items( $items ) {
    if ( is_singular( [ 'essay', 'note' ] ) ) {
        return [];
    }
    return $items;
}

/* =========================================
   1. ENQUEUES — Styles & Scripts
   ========================================= */

add_action( 'wp_enqueue_scripts', 'hp_journal_enqueue_styles' );
function hp_journal_enqueue_styles() {
    $theme_version = wp_get_theme()->get( 'Version' );

    /*
     * Parent style.css (nur Theme-Header, ~1 KiB) wird NICHT mehr
     * separat geladen — GP's main.min.css enthält alle Styles.
     * → Spart eine render-blocking Anfrage.
     */

    // Child Theme — abhängig von GP's Haupt-CSS
    wp_enqueue_style(
        'hp-journal-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'generate-style' ),
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

/**
 * Doppelt geladenes Child-CSS entfernen.
 *
 * WordPress / GeneratePress kann die Child-style.css automatisch
 * einreihen (z. B. via get_stylesheet_uri()). Wir laden sie bereits
 * explizit als 'hp-journal-style' → Duplikat dequeuen.
 *
 * Außerdem: Parent style.css (nur Theme-Header) ist unnötig,
 * da GP's main.min.css alle Styles enthält.
 */
add_action( 'wp_enqueue_scripts', 'hp_dequeue_duplicate_styles', 20 );
function hp_dequeue_duplicate_styles(): void {
    // Mögliche Auto-Handles für Child-Theme-CSS
    wp_dequeue_style( 'generate-child' );
    wp_deregister_style( 'generate-child' );

    // Parent style.css (nur Header-Metadaten, ~1 KiB)
    wp_dequeue_style( 'generatepress-style' );
    wp_deregister_style( 'generatepress-style' );
}

/**
 * Kritische Schriften vorladen (Preload).
 *
 * Bricht die CSS → Font-Kette auf: Browser beginnt Font-Download
 * parallel zum CSS statt sequentiell danach.
 *
 * Nur die drei auf der Startseite sichtbaren Gewichte werden
 * vorgeladen: 300 (Fließtext), 700 (Zwischenüberschriften), 900 (Titel).
 */
add_action( 'wp_head', 'hp_preload_critical_fonts', 1 );
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

/**
 * GP menu.min.js → defer-Attribut setzen.
 *
 * Das GP-Menü-Script (2 KiB) blockiert das Rendering,
 * wird aber erst bei Nutzerinteraktion gebraucht.
 * Mit defer wird es parallel geladen und erst nach HTML-Parsing ausgeführt.
 */
add_filter( 'script_loader_tag', 'hp_defer_gp_menu_script', 10, 3 );
function hp_defer_gp_menu_script( string $tag, string $handle, string $src ): string {
    $defer_handles = [ 'generate-menu', 'generate-navigation' ];

    if ( in_array( $handle, $defer_handles, true ) ) {
        // Nur hinzufügen wenn nicht schon vorhanden
        if ( false === strpos( $tag, 'defer' ) ) {
            $tag = str_replace( ' src=', ' defer src=', $tag );
        }
    }

    return $tag;
}

/**
 * SEO: Meta-Description für die Startseite ausgeben.
 */
add_action( 'wp_head', 'hp_meta_description', 2 );
function hp_meta_description(): void {
    if ( is_front_page() ) {
        $desc = 'Zwischenräume — Essays und Analysen zu Gesellschaft, Wissenschaft und Digitalisierung. Herausgegeben von Hasim Üner.';
        printf(
            '<meta name="description" content="%s">' . "\n",
            esc_attr( $desc )
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

    // Standard-Themenfelder anlegen (einmalig bei Theme-Aktivierung)
    hp_create_default_topics();
}

/**
 * Legt Standard-Themenfelder auch an, wenn das Theme bereits aktiv ist.
 * Prüft per Option, ob es schon gelaufen ist — läuft also nur einmal.
 */
add_action( 'init', 'hp_maybe_create_default_topics', 20 );
function hp_maybe_create_default_topics(): void {
    if ( get_option( 'hp_default_topics_created' ) ) {
        return;
    }
    hp_create_default_topics();
    update_option( 'hp_default_topics_created', true );
}

/**
 * Legt die Standard-Themenfelder an, falls sie noch nicht existieren.
 * Kann auch manuell aufgerufen werden.
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

/* =========================================
   7. SOCIAL TEASER META FIELD
   ========================================= */

/**
 * Registriert das Custom Meta Field `_hp_social_teaser`
 * für den Post Type `essay`.
 *
 * - Im Block-Editor als Sidebar-Panel sichtbar.
 * - Via REST API lesbar/beschreibbar (für Automatisierungs-Tools).
 * - Nicht im Frontend ausgegeben.
 */
add_action( 'init', 'hp_register_social_meta' );
function hp_register_social_meta(): void {
    register_post_meta( 'essay', '_hp_social_teaser', [
        'type'              => 'string',
        'single'            => true,
        'sanitize_callback' => 'sanitize_textarea_field',
        'auth_callback'     => static fn(): bool => current_user_can( 'edit_posts' ),
        'show_in_rest'      => true,
        'default'           => '',
    ] );
}

/**
 * Gutenberg Sidebar Panel — Social Teaser.
 * Registriert ein sichtbares Panel in der Editor-Sidebar
 * (sichtbarer als Classic Meta Box, die ganz unten versteckt ist).
 */
add_action( 'enqueue_block_editor_assets', 'hp_social_teaser_editor_assets' );
function hp_social_teaser_editor_assets(): void {
    $screen = get_current_screen();
    if ( ! $screen || 'essay' !== $screen->post_type ) {
        return;
    }

    wp_enqueue_script(
        'hp-social-teaser-panel',
        false, // no file — we use inline
        [ 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-compose' ],
    );

    $inline_js = <<<'JS'
( function() {
    var el          = wp.element.createElement;
    var Fragment    = wp.element.Fragment;
    var PluginPanel = wp.editPost.PluginDocumentSettingPanel;
    var TextControl = wp.components.TextareaControl;
    var useSelect   = wp.data.useSelect;
    var useDispatch = wp.data.useDispatch;

    var META_KEY = '_hp_social_teaser';

    function SocialTeaserPanel() {
        var meta = useSelect( function( select ) {
            return select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {};
        }, [] );

        var editPost = useDispatch( 'core/editor' ).editPost;
        var value = meta[ META_KEY ] || '';

        return el( PluginPanel, {
            name:  'hp-social-teaser',
            title: '𝕏  Social-Media Teaser',
            icon:  'share',
        },
            el( TextControl, {
                label: 'Hook-Satz für X / Social Media',
                help:  'Wird NICHT im Frontend angezeigt. Nur für Automation via REST API abrufbar.',
                value: value,
                onChange: function( newVal ) {
                    var newMeta = {};
                    newMeta[ META_KEY ] = newVal;
                    editPost( { meta: newMeta } );
                },
                rows: 3,
            })
        );
    }

    wp.plugins.registerPlugin( 'hp-social-teaser', {
        render: SocialTeaserPanel,
        icon:   'share',
    });
})();
JS;

    wp_add_inline_script( 'hp-social-teaser-panel', $inline_js );
}

/* =========================================
   8. JSON-LD SCHEMA — ScholarlyArticle
   ========================================= */

/**
 * Injiziert strukturierte Daten (Schema.org) als JSON-LD
 * in den <head> von Single-Essay-Seiten.
 *
 * Typ: ScholarlyArticle (https://schema.org/ScholarlyArticle).
 */
add_action( 'wp_head', 'hp_essay_jsonld_schema', 5 );
function hp_essay_jsonld_schema(): void {
    if ( ! is_singular( 'essay' ) ) {
        return;
    }

    $post    = get_queried_object();
    $author  = get_the_author_meta( 'display_name', $post->post_author );
    $excerpt = has_excerpt( $post->ID )
        ? wp_strip_all_tags( get_the_excerpt( $post ) )
        : wp_trim_words( wp_strip_all_tags( $post->post_content ), 40, ' …' );

    $schema = [
        '@context'      => 'https://schema.org',
        '@type'         => 'ScholarlyArticle',
        'headline'      => get_the_title( $post ),
        'datePublished' => get_the_date( 'c', $post ),
        'dateModified'  => get_the_modified_date( 'c', $post ),
        'abstract'      => $excerpt,
        'author'        => [
            '@type' => 'Person',
            'name'  => $author,
        ],
        'publisher'     => [
            '@type' => 'Organization',
            'name'  => get_bloginfo( 'name' ),
            'url'   => home_url( '/' ),
        ],
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id'   => get_permalink( $post ),
        ],
        'url'           => get_permalink( $post ),
        'inLanguage'    => get_locale(),
    ];

    // Beitragsbild als image
    if ( has_post_thumbnail( $post->ID ) ) {
        $img_url = get_the_post_thumbnail_url( $post->ID, 'full' );
        if ( $img_url ) {
            $schema['image'] = $img_url;
        }
    }

    // Wortanzahl
    $word_count = str_word_count( wp_strip_all_tags( $post->post_content ) );
    if ( $word_count > 0 ) {
        $schema['wordCount'] = $word_count;
    }

    echo "\n<!-- Zwischenräume: JSON-LD -->\n";
    echo '<script type="application/ld+json">';
    echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
    echo "</script>\n";
}