<?php
/**
 * Template Name: Mission / Über das Journal
 *
 * Statische Seite „Über das Journal" — elegante Typografie,
 * keine Sidebar, max. Lesezeilenbreite.
 *
 * @package Hasimuener_Journal
 * @version 3.0.0
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<article class="hp-mission" aria-label="<?php the_title_attribute(); ?>">

    <header class="hp-mission__header">
        <span class="hp-kicker">Journal</span>
        <h1 class="hp-mission__title"><?php the_title(); ?></h1>
    </header>

    <div class="hp-mission__body">

        <figure class="hp-mission__portrait">
            <img
                src="https://hasimuener.org/wp-content/uploads/2026/02/1f15d682-34e3-475d-9be1-add51e9b9d3b.jpg"
                alt="Hasim Üner — Herausgeber"
                width="280"
                height="280"
                loading="eager"
            >
        </figure>

        <div class="hp-mission__prose prose">

            <h3>Woher ich komme</h3>
            <p>Ich bin mit sieben Jahren aus der Türkei nach Deutschland gekommen, kurdische Familie. Wer so aufwächst, sucht sich Politik nicht aus — sie ist einfach da. Man lernt früh, zwischen den Zeilen zu lesen. Man merkt, dass Regeln nicht für alle gleich gelten. Und man stellt Fragen, die andere nicht stellen müssen.</p>
            <p>Das hat mich geprägt, mehr als jedes Studium.</p>

            <h3>Was ich mache</h3>
            <p>Ich arbeite als Entwickler und Digitalstratege. Ich baue Systeme — und ich sehe, wie Systeme gebaut werden. Wer die Infrastruktur einer Gesellschaft versteht, versteht auch ihre Machtverhältnisse. Algorithmen entscheiden, wer sichtbar wird. Plattformen bestimmen, wie wir kommunizieren. Code ist kein neutrales Werkzeug — er formt, wie wir leben.</p>
            <p>Genau dort setze ich an.</p>

            <h3>Was du hier findest</h3>
            <p>Dieses Journal dreht sich um eine Frage: <strong>Wer gestaltet die digitale Gesellschaft — und nach wessen Regeln?</strong></p>
            <p>Ich schreibe über Technologie, die politisch ist. Über Machtstrukturen, die im Code stecken. Über Alternativen zum Status quo — nicht als Theorie, sondern aus der Praxis von jemandem, der diese Systeme selbst baut.</p>
            <p>Konkret heißt das:</p>
            <ul>
                <li><strong>Essays</strong> — Analysen zu digitaler Macht, Plattformökonomie und gesellschaftlichem Wandel</li>
                <li><strong>Notizen</strong> — Kürzere Gedanken, Beobachtungen, Fragen im Werden</li>
            </ul>
            <p>Keine fertigen Antworten. Kein Aktivismus. Kein Nachrichtenportal. Nachdenken mit offenem Ausgang — von jemandem, der lieber baut als belehrt.</p>
            <p>Wer mitlesen will, ist willkommen.</p>

        </div>
    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
