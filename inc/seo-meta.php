<?php
/**
 * SEO-Meta — Hasimuener Journal
 *
 * Leichtgewichtiger Ersatz für The SEO Framework.
 * Liefert Meta-Description, Open Graph und Twitter Cards
 * direkt aus dem Theme — kein Plugin nötig.
 *
 * Architektur:
 * - register_post_meta → Gutenberg-Zugriff + REST API
 * - Inline-JS Sidebar-Panel → Beschreibungsfeld im Editor
 * - wp_head-Output → description, OG, Twitter Card
 *
 * Fallback-Kette für Description:
 * 1. Manuelles Meta-Feld `_hp_meta_description`
 * 2. Beitrags-Excerpt (falls vorhanden)
 * 3. Automatischer Trim auf 160 Zeichen aus Post-Content
 * 4. Site-Tagline (Startseite / Archive)
 *
 * @package Hasimuener_Journal
 * @since   4.1.0
 */

defined( 'ABSPATH' ) || exit;

/* =========================================
   1. META-FELD REGISTRIEREN
   ========================================= */

/**
 * Registriert `_hp_meta_description` für Essays und Notes.
 *
 * Das Feld ist REST-fähig und im Block-Editor verfügbar.
 * auth_callback beschränkt Zugriff auf Nutzer mit edit_posts.
 */
function hp_register_seo_meta(): void {
	$args = [
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
		'auth_callback'     => static function(): bool {
			return current_user_can( 'edit_posts' );
		},
		'show_in_rest'      => true,
		'default'           => '',
	];

	register_post_meta( 'essay', '_hp_meta_description', $args );
	register_post_meta( 'note', '_hp_meta_description', $args );
	register_post_meta( 'post', '_hp_meta_description', $args );
	register_post_meta( 'page', '_hp_meta_description', $args );
}
add_action( 'init', 'hp_register_seo_meta' );

/* =========================================
   2. GUTENBERG SIDEBAR PANEL
   ========================================= */

/**
 * Editor-Panel: Meta-Description für SEO.
 *
 * Inline-JS-Ansatz identisch zu meta-fields.php —
 * kein Build-Step, kein React-Overhead.
 * Wird auf allen unterstützten Post-Types geladen.
 */
function hp_seo_meta_editor_assets(): void {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	$supported = [ 'essay', 'note', 'post', 'page' ];
	if ( ! in_array( $screen->post_type, $supported, true ) ) {
		return;
	}

	wp_enqueue_script(
		'hp-seo-meta-panel',
		false, // Inline-Script
		[ 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-compose' ],
	);

	$inline_js = <<<'JS'
( function() {
    var el          = wp.element.createElement;
    var PluginPanel = wp.editPost.PluginDocumentSettingPanel;
    var TextControl = wp.components.TextareaControl;
    var useSelect   = wp.data.useSelect;
    var useDispatch = wp.data.useDispatch;

    var META_KEY  = '_hp_meta_description';
    var MAX_CHARS = 160;

    function SeoMetaPanel() {
        var meta = useSelect( function( select ) {
            return select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {};
        }, [] );

        var editPost = useDispatch( 'core/editor' ).editPost;
        var value    = meta[ META_KEY ] || '';
        var charInfo = value.length + ' / ' + MAX_CHARS + ' Zeichen';

        return el( PluginPanel, {
            name:  'hp-seo-meta',
            title: 'SEO — Meta-Beschreibung',
            icon:  'search',
        },
            el( TextControl, {
                label:    'Meta-Description',
                help:     charInfo + ' — Wird in Google-Snippets und Social-Media-Previews angezeigt.',
                value:    value,
                onChange: function( newVal ) {
                    if ( newVal.length <= MAX_CHARS ) {
                        var newMeta = {};
                        newMeta[ META_KEY ] = newVal;
                        editPost( { meta: newMeta } );
                    }
                },
                rows: 3,
            })
        );
    }

    wp.plugins.registerPlugin( 'hp-seo-meta', {
        render: SeoMetaPanel,
        icon:   'search',
    });
})();
JS;

	wp_add_inline_script( 'hp-seo-meta-panel', $inline_js );
}
add_action( 'enqueue_block_editor_assets', 'hp_seo_meta_editor_assets' );

/* =========================================
   3. MISSION: TITLE + DESCRIPTION OVERRIDES
   ========================================= */

/**
 * Prüft, ob die aktuelle Anfrage die Mission-Seite ist.
 *
 * @return bool
 */
function hp_is_mission_page(): bool {
	return ! is_admin() && is_page( 'mission' );
}

/**
 * Erzwingt einen stabilen Dokumenttitel für /mission/.
 *
 * @param string $title Vorheriger Titel.
 * @return string
 */
function hp_filter_mission_document_title( string $title ): string {
	if ( ! hp_is_mission_page() ) {
		return $title;
	}

	return 'Mission – Haşim Üner';
}
add_filter( 'pre_get_document_title', 'hp_filter_mission_document_title' );

/* =========================================
   4. DESCRIPTION RESOLVER
   ========================================= */

/**
 * Ermittelt die beste verfügbare Description.
 *
 * Fallback-Kette: Meta-Feld → Excerpt → Content-Trim → Tagline.
 * Maximal 160 Zeichen, kein HTML.
 *
 * @return string Bereinigte Description oder leer.
 */
function hp_get_meta_description(): string {
	$desc = '';

	if ( hp_is_mission_page() ) {
		return 'Essays und Notizen über Macht, Medien, Erinnerung, Sprache und Gesellschaft – mit dem Versuch, Verständigung zwischen Perspektiven offenzuhalten.';
	}

	if ( is_singular() ) {
		$post = get_queried_object();

		// 1. Manuelles Feld
		$custom = get_post_meta( $post->ID, '_hp_meta_description', true );
		if ( $custom ) {
			$desc = $custom;
		}
		// 2. Beitrags-Excerpt
		elseif ( has_excerpt( $post->ID ) ) {
			$desc = wp_strip_all_tags( get_the_excerpt( $post ) );
		}
		// 3. Content-Trim
		else {
			$desc = wp_trim_words( wp_strip_all_tags( $post->post_content ), 25, ' …' );
		}
	} elseif ( is_front_page() || is_home() ) {
		$desc = get_bloginfo( 'description' );
	} elseif ( is_post_type_archive() ) {
		$obj = get_queried_object();
		if ( $obj && ! empty( $obj->description ) ) {
			$desc = $obj->description;
		}
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$desc = term_description();
		$desc = wp_strip_all_tags( $desc );
	}

	// Auf 160 Zeichen begrenzen
	if ( mb_strlen( $desc ) > 160 ) {
		$desc = mb_substr( $desc, 0, 157 ) . '…';
	}

	return trim( $desc );
}

/* =========================================
   5. HEAD-OUTPUT: META + OPEN GRAPH + TWITTER
   ========================================= */

/**
 * Gibt Meta-Description, Open-Graph- und Twitter-Card-Tags aus.
 *
 * Priorität 5 → vor Theme-/Plugin-Ausgaben.
 * Prüft, ob The SEO Framework aktiv ist — falls ja,
 * wird NICHTS ausgegeben (Dopplung vermeiden).
 */
function hp_output_seo_meta_tags(): void {
	// Sicherheitsnetz: Falls TSF doch noch aktiv ist, nichts ausgeben
	if ( defined( 'THE_SEO_FRAMEWORK_VERSION' ) ) {
		return;
	}

	$desc      = hp_get_meta_description();
	$title     = wp_get_document_title();
	$url       = hp_get_current_url();
	$site_name = get_bloginfo( 'name' );
	$locale    = get_locale();
	$image     = hp_get_seo_image();

	echo "\n<!-- Haşim Üner: SEO-Meta -->\n";

	// Canonical URL
	printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( $url ) );

	// Meta-Description
	if ( $desc ) {
		printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $desc ) );
	}

	// Open Graph
	printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $url ) );
	printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( $site_name ) );
	printf( '<meta property="og:locale" content="%s" />' . "\n", esc_attr( $locale ) );

	if ( $desc ) {
		printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $desc ) );
	}

	if ( is_singular() ) {
		echo '<meta property="og:type" content="article" />' . "\n";
		printf(
			'<meta property="article:published_time" content="%s" />' . "\n",
			esc_attr( get_the_date( 'c' ) )
		);
		printf(
			'<meta property="article:modified_time" content="%s" />' . "\n",
			esc_attr( get_the_modified_date( 'c' ) )
		);
	} else {
		echo '<meta property="og:type" content="website" />' . "\n";
	}

	if ( $image ) {
		printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $image ) );

		// OG-Image-Dimensionen für korrektes Social-Preview beim Erstshare
		if ( is_singular() ) {
			$img_id = get_post_thumbnail_id();
			if ( $img_id ) {
				$img_meta = wp_get_attachment_image_src( $img_id, 'large' );
				if ( $img_meta ) {
					printf( '<meta property="og:image:width" content="%d" />' . "\n", $img_meta[1] );
					printf( '<meta property="og:image:height" content="%d" />' . "\n", $img_meta[2] );
				}
			}
		}

		printf( '<meta property="og:image:alt" content="%s" />' . "\n", esc_attr( $title ) );
	}

	if ( is_singular() ) {
		echo '<meta property="article:author" content="Haşim Üner" />' . "\n";
	}

	// Twitter Card
	echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
	echo '<meta name="twitter:site" content="@_0239983326111" />' . "\n";
	echo '<meta name="twitter:creator" content="@_0239983326111" />' . "\n";
	printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $title ) );

	if ( $desc ) {
		printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( $desc ) );
	}

	if ( $image ) {
		printf( '<meta name="twitter:image" content="%s" />' . "\n", esc_url( $image ) );
	}
}
add_action( 'wp_head', 'hp_output_seo_meta_tags', 3 );

/* =========================================
   5. HILFSFUNKTIONEN
   ========================================= */

/**
 * Aktuelle URL sauber ermitteln.
 *
 * @return string Vollständige URL der aktuellen Seite.
 */
function hp_get_current_url(): string {
	if ( is_singular() ) {
		return get_permalink();
	}

	if ( is_front_page() || is_home() ) {
		return home_url( '/' );
	}

	if ( is_post_type_archive() ) {
		return get_post_type_archive_link( get_queried_object()->name );
	}

	if ( is_tax() || is_category() || is_tag() ) {
		return get_term_link( get_queried_object() );
	}

	// Fallback
	return home_url( add_query_arg( null, null ) );
}

/**
 * Bestes verfügbares Bild für Social-Previews.
 *
 * Reihenfolge: Beitragsbild → erstes Bild im Content → null.
 *
 * @return string|null Bild-URL oder null.
 */
function hp_get_seo_image(): ?string {
	if ( is_singular() ) {
		$post = get_queried_object();

		// Beitragsbild
		if ( has_post_thumbnail( $post->ID ) ) {
			$url = get_the_post_thumbnail_url( $post->ID, 'large' );
			if ( $url ) {
				return $url;
			}
		}

		// Erstes Bild im Content als Fallback
		preg_match( '/<img[^>]+src=["\']([^"\']+)/i', $post->post_content, $matches );
		if ( ! empty( $matches[1] ) ) {
			return $matches[1];
		}
	}

	// Site-Icon als letzter Fallback
	$site_icon = get_site_icon_url( 512 );
	return $site_icon ?: null;
}
