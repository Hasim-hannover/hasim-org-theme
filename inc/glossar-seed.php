<?php
/**
 * Glossar-Seed: Beispiel-Eintrag „Nordkurdistan"
 *
 * Dieses Script wird einmalig über admin_init ausgeführt
 * und legt den ersten Glossar-Eintrag an. Nach erfolgreicher
 * Ausführung deaktiviert es sich selbst (Option-Flag).
 *
 * ENTFERNEN nach dem ersten Laden im WP-Admin.
 *
 * @package Hasimuener_Journal
 * @since   5.1.0
 */

defined( 'ABSPATH' ) || exit;

function hp_seed_glossar_nordkurdistan(): void {

    // Nur einmal ausführen
    if ( get_option( 'hp_glossar_seed_done' ) ) {
        return;
    }

    // Prüfen ob der Eintrag schon existiert
    $existing = get_page_by_title( 'Nordkurdistan', OBJECT, 'glossar' );
    if ( $existing ) {
        update_option( 'hp_glossar_seed_done', true );
        return;
    }

    $content = <<<'HTML'
<!-- wp:heading -->
<h2>Geografie und Bezeichnung</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Nordkurdistan (kurdisch: <em>Bakurê Kurdistanê</em>, kurz <em>Bakur</em>) bezeichnet die mehrheitlich kurdisch besiedelten Gebiete im Osten und Südosten der heutigen Türkei. Die Region umfasst historisch die Provinzen Diyarbakır, Şırnak, Hakkari, Van, Mardin, Batman, Siirt und weitere — Gebiete, die der türkische Staat als seinen „Südosten" beansprucht.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Die Bezeichnung „Nordkurdistan" ist selbst ein politischer Akt: Sie verweigert die Staatsgeografie als einzigen Bezugsrahmen und setzt eine kurdische Selbstverortung dagegen. Kurdistan als Ganzes — aufgeteilt zwischen Türkei (Nord), Irak (Süd), Iran (Ost) und Syrien (West/Rojava) — existiert auf keiner offiziellen Landkarte als souveräner Staat. Die Benennung macht eine Wirklichkeit sichtbar, die Nationalstaaten unsichtbar halten.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Sprachpolitik als Machtinstrument</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>In Nordkurdistan war das Sprechen, Schreiben und Veröffentlichen auf Kurdisch über Jahrzehnte verboten — nicht durch Vergessen, sondern durch aktive staatliche Unterdrückung. Das Verbot der kurdischen Sprache (bis in die 1990er Jahre strafrechtlich verfolgt) ist ein paradigmatisches Beispiel dafür, wie hierarchisch organisierte Staaten Erinnerung und Identität kontrollieren: nicht durch Auslöschung allein, sondern durch die Kontrolle der Mittel, mit denen eine Gemeinschaft sich selbst beschreiben kann.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Strukturelle Einordnung</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Nordkurdistan ist kein bloßes geografisches Faktum, sondern ein Prüfstein für die Kernthese dieses Journals: Die hierarchisch-irrationale Struktur staatlicher Ordnung manifestiert sich in der Kontrolle über Sprache, Raum und Erinnerung. Wer die Benennung eines Ortes bestimmt, bestimmt, was dort gedacht werden kann. Die Spannung zwischen „Südosten der Türkei" und „Nordkurdistan" ist keine semantische Spielerei — sie markiert den Riss zwischen staatlichem Anspruch und gesellschaftlicher Wirklichkeit.</p>
<!-- /wp:paragraph -->
HTML;

    $post_id = wp_insert_post( [
        'post_type'    => 'glossar',
        'post_status'  => 'publish',
        'post_title'   => 'Nordkurdistan',
        'post_content' => $content,
        'post_excerpt' => 'Die mehrheitlich kurdisch besiedelten Gebiete im Osten und Südosten der heutigen Türkei — ein Begriff, der Staatsgeografie als einzigen Bezugsrahmen verweigert.',
    ] );

    if ( ! is_wp_error( $post_id ) ) {
        // Kurzdefinition
        update_post_meta(
            $post_id,
            '_hp_glossar_kurz',
            'Die mehrheitlich kurdisch besiedelten Gebiete im Osten der heutigen Türkei. Die Bezeichnung setzt eine kurdische Selbstverortung gegen die türkische Staatsgeografie.'
        );

        // Synonyme für Auto-Linking
        update_post_meta(
            $post_id,
            '_hp_glossar_synonyme',
            'Bakur, Bakurê Kurdistanê'
        );

        // Topic zuweisen (falls vorhanden)
        $topic = get_term_by( 'slug', 'erinnerungspolitik', 'topic' );
        if ( $topic ) {
            wp_set_object_terms( $post_id, [ $topic->term_id ], 'topic' );
        }

        update_option( 'hp_glossar_seed_done', true );
    }
}
add_action( 'admin_init', 'hp_seed_glossar_nordkurdistan' );
