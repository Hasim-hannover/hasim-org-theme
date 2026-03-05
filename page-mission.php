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

            <?php
            ob_start(); ?>
            <p>Geboren in Nordkurdistan — den Regionen, die der türkische Staat als seinen Südosten beansprucht. Aufgewachsen in Deutschland. Drei Sprachen, drei Welten, keine davon neutral.</p>

            <p>Kurdisch ist die Sprache, die mir gehört und die ein Staat mir nehmen wollte. Türkisch ist die Sprache, die an ihre Stelle gesetzt wurde — in Nordkurdistan war selbst das Sprechen des Kurdischen lange verboten. Deutsch ist die Sprache, in der ich gelernt habe, beides zu benennen. Jede dieser Sprachen trägt eine Machtgeschichte. Zusammen formen sie den Blick, aus dem heraus ich schreibe.</p>

            <p>Ich bin politisch — lokal, auf der Straße und am Schreibtisch. Was mich antreibt, beginnt beim eigenen Dasein: bei der Erfahrung, dass die Welt, in die man geworfen wird, nicht zufällig geordnet ist, sondern hierarchisch. Aus dieser Erfahrung werden Fragen: Was geschieht mit Erinnerung in hierarchisch organisierten Gesellschaften? Wie formt staatliche Macht, was gedacht und gesagt werden darf?</p>

            <p>Und dann ist da die Frage nach der Freiheit — nicht als Ideal, sondern als Problem. Was bedeutet Freiheit an sich, wenn die Realität, in der sie stattfinden soll, hierarchisch verfasst ist? Man kann sagen, Freiheit sei individuell. Doch jeder individuelle Freiheitsbegriff entsteht innerhalb konkreter Verhältnisse — er ist nicht losgelöst von der Ordnung, die ihn begrenzt. Mein Freiheitsbegriff ist kein abstrakter: Er entsteht dort, wo Erfahrung auf Struktur trifft, wo das eigene Leben die Theorie prüft.</p>

            <p>Dies ist der Ort für diese Fragen. Macht, Erinnerung, Freiheit, Identität, Medien. Keine fertigen Antworten — aber der Anspruch, die Strukturen freizulegen, die Antworten verhindern.</p>

            <p>Das gilt auch für diesen Ort selbst: kein Tracking, keine Werbung, keine Cookies. Wer Machtverhältnisse analysiert, darf sie nicht nebenbei reproduzieren.</p>
            <?php
            echo apply_filters( 'the_content', ob_get_clean() ); ?>

        </div>
    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
