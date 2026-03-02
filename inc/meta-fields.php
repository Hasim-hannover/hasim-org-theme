<?php
/**
 * Custom Meta Fields — Hasimuener Journal
 *
 * Social-Teaser-Feld für Essays: Ein Hook-Satz, der ausschließlich
 * für Automation (X/Social-Posting via REST API) abrufbar ist.
 * Wird NICHT im Frontend ausgegeben.
 *
 * Implementierung:
 * - register_post_meta → REST API + Gutenberg-Zugriff
 * - Inline-JS Sidebar-Panel → native Editor-Integration
 *   (kein externer Build-Step, kein React-Overhead)
 *
 * @package Hasimuener_Journal
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/* -----------------------------------------
   Meta-Feld registrieren
   ----------------------------------------- */

/**
 * Registriert `_hp_social_teaser` als REST-fähiges Meta-Feld
 * auf dem CPT essay.
 *
 * auth_callback: Nur Nutzer mit edit_posts-Capability dürfen
 * das Feld lesen/schreiben — verhindert unautorisierte
 * REST-API-Zugriffe.
 */
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
add_action( 'init', 'hp_register_social_meta' );

/* -----------------------------------------
   Gutenberg Sidebar Panel
   ----------------------------------------- */

/**
 * Registriert ein Sidebar-Panel im Block-Editor für Essays.
 *
 * Nutzt wp_add_inline_script statt einer externen JS-Datei,
 * da die Logik <40 Zeilen umfasst und kein Build-Step
 * gerechtfertigt ist. Wird nur auf Essay-Screens geladen.
 */
function hp_social_teaser_editor_assets(): void {
	$screen = get_current_screen();
	if ( ! $screen || 'essay' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_script(
		'hp-social-teaser-panel',
		false, // Kein File — Inline-Script reicht
		[ 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-compose' ],
	);

	$inline_js = <<<'JS'
( function() {
    var el          = wp.element.createElement;
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
            title: '\ud835\udd4f  Social-Media Teaser',
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
add_action( 'enqueue_block_editor_assets', 'hp_social_teaser_editor_assets' );
