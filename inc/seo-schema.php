<?php
/**
 * SEO: JSON-LD Schema — Hasimuener Journal
 *
 * Strukturierte Daten (Schema.org) als JSON-LD im <head>
 * von Single-Essay-Seiten.
 *
 * Typ: ScholarlyArticle — semantisch passend für
 * analytische Langform-Texte mit Quellenverweisen.
 * Google erkennt diesen Typ und kann ihn in den
 * Knowledge Graph übernehmen.
 *
 * Hinweis: Meta-Description und OG-Tags werden von
 * inc/seo-meta.php verwaltet. Keine doppelte Ausgabe im Theme.
 *
 * @package Hasimuener_Journal
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Injiziert ScholarlyArticle JSON-LD für Essay-Singles.
 *
 * Felder: headline, datePublished, dateModified, abstract,
 * author (Person), publisher (Organization), image, wordCount,
 * mainEntityOfPage, inLanguage.
 */
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
			'@id' => home_url( '/' ) . '#organization',
		],
		'mainEntityOfPage' => [
			'@type' => 'WebPage',
			'@id'   => get_permalink( $post ),
		],
		'url'           => get_permalink( $post ),
		'inLanguage'    => get_locale(),
	];

	// Beitragsbild als Schema-Image
	if ( has_post_thumbnail( $post->ID ) ) {
		$img_url = get_the_post_thumbnail_url( $post->ID, 'full' );
		if ( $img_url ) {
			$schema['image'] = $img_url;
		}
	}

	// Wortanzahl — relevantes Signal für Longform-Erkennung
	$word_count = str_word_count( wp_strip_all_tags( $post->post_content ) );
	if ( $word_count > 0 ) {
		$schema['wordCount'] = $word_count;
	}

	echo "\n<!-- Hasim Üner: JSON-LD -->\n";
	echo '<script type="application/ld+json">';
	echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
	echo "</script>\n";
}
add_action( 'wp_head', 'hp_essay_jsonld_schema', 5 );

/* =========================================
   Organization + WebSite Schema (global)
   ========================================= */

/**
 * Gibt Organization + WebSite JSON-LD auf jeder Seite aus.
 *
 * Organization: Repräsentiert das Journal als Herausgeber.
 * WebSite: Bietet Google Sitelinks-Searchbox und verknüpft
 * die Website mit dem Organization-Entity.
 *
 * Wird nur einmal pro Request ausgegeben.
 */
function hp_org_website_jsonld_schema(): void {

	$site_name = get_bloginfo( 'name' );
	$site_url  = home_url( '/' );
	$site_desc = get_bloginfo( 'description' );
	$locale    = get_locale();

	$graph = [
		'@context' => 'https://schema.org',
		'@graph'   => [],
	];

	// --- Organization ---
	$org = [
		'@type'       => 'Organization',
		'@id'         => $site_url . '#organization',
		'name'        => $site_name,
		'url'         => $site_url,
		'description' => $site_desc ?: 'Essays und Analysen zu Macht, Medien und Gesellschaft. Von Hasim Üner.',
		'founder'     => [
			'@type'  => 'Person',
			'name'   => 'Hasim Üner',
			'jobTitle' => 'Medienwissenschaftler & Publizist',
		],
		'foundingDate'    => '2024',
		'publishingPrinciples' => $site_url . 'mission/',
	];

	// Logo (Site-Icon als Fallback)
	$site_icon = get_site_icon_url( 512 );
	if ( $site_icon ) {
		$org['logo'] = [
			'@type'      => 'ImageObject',
			'url'        => $site_icon,
			'contentUrl' => $site_icon,
			'caption'    => $site_name,
		];
		$org['image'] = $site_icon;
	}

	// Soziale Profile
	$org['sameAs'] = [
		'https://x.com/_0239983326111',
	];

	$graph['@graph'][] = $org;

	// --- WebSite ---
	$website = [
		'@type'       => 'WebSite',
		'@id'         => $site_url . '#website',
		'name'        => $site_name,
		'url'         => $site_url,
		'description' => $site_desc ?: '',
		'inLanguage'  => $locale,
		'publisher'   => [
			'@id' => $site_url . '#organization',
		],
	];

	// Sitelinks-Searchbox (Google)
	$website['potentialAction'] = [
		'@type'       => 'SearchAction',
		'target'      => [
			'@type'        => 'EntryPoint',
			'urlTemplate'  => $site_url . '?s={search_term_string}',
		],
		'query-input' => 'required name=search_term_string',
	];

	$graph['@graph'][] = $website;

	echo "\n<!-- Hasim Üner: Organization + WebSite JSON-LD -->\n";
	echo '<script type="application/ld+json">';
	echo wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
	echo "</script>\n";
}
add_action( 'wp_head', 'hp_org_website_jsonld_schema', 4 );
