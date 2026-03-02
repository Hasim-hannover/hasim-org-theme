<?php
/**
 * Template Name: Über mich
 *
 * Persönliche Seite — wer ich bin, was mich bewegt,
 * warum dieses Journal existiert.
 *
 * @package Hasimuener_Journal
 * @version 5.0.0
 */

/*
 * TODO (manuell im WP-Admin):
 * 1. Design → Menüs: Menüpunkt „Warum" umbenennen in „Über" (URL /mission/ bleibt)
 * 2. Seite „Mission" bearbeiten: Seitentitel ändern zu „Über" (beeinflusst <title>-Tag & SEO)
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<article class="hp-mission" aria-label="<?php the_title_attribute(); ?>">

    <div class="hp-mission__body">

        <figure class="hp-mission__portrait">
            <img
                src="https://hasimuener.org/wp-content/uploads/2026/02/1f15d682-34e3-475d-9be1-add51e9b9d3b.jpg"
                alt="Hasim Üner"
                width="280"
                height="280"
                loading="eager"
            >
        </figure>

        <div class="hp-mission__prose prose">

            <p>Geboren in Nordkurdistan, aufgewachsen in Deutschland. Drei Sprachen, drei Welten, ein Blick, der Systeme hinterfragt.</p>

            <p>Ich bin politisch — lokal, auf der Straße und am Schreibtisch. Was mich antreibt, ist das Dasein an sich. Was geschieht mit Erinnerung in hierarchisch organisierten Gesellschaften? Wie formt staatliche Macht, was gedacht und gesagt werden darf?</p>

            <p>„Zwischenräume" ist das Journal für diese Fragen. Macht, Identität, Medien. Keine fertigen Antworten. Der Versuch, Strukturen sichtbar zu machen.</p>

            <p>Kein Tracking. Keine Werbung. Keine Cookies.</p>

            <!-- TODO: Kontaktweg ergänzen (E-Mail, Social-Link o. Ä.) -->

        </div>
    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
