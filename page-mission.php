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

            <h3>Was ich gelernt habe</h3>
            <p>Ich habe Medienwissenschaft studiert und arbeite als Entwickler und Digitalstratege. Das eine hilft mir zu verstehen, wie Strukturen funktionieren — in Medien, in Technologie, in Gesellschaft. Das andere hilft mir, selbst etwas zu bauen.</p>
            <p>Beides zusammen hat mir gezeigt: Die wichtigsten Entscheidungen unserer Zeit werden in Code geschrieben, nicht in Parlamenten. Wer das ignoriert, wird gestaltet statt zu gestalten.</p>

            <h3>Was mich antreibt</h3>
            <p>Ich glaube, dass wir bessere Formen des Zusammenlebens finden können. Partizipativer, kommunaler, weniger abhängig von Nationalstaaten und Marktlogik. Denker wie Öcalan, Bookchin und auch Nietzsche haben mir dabei Türen geöffnet — aber ich folge keiner Lehre blind. Ich versuche, eigene Gedanken zu entwickeln, auch wenn die nicht immer fertig sind.</p>
            <p>Dieses Journal ist der Ort, an dem ich das tue. Keine fertigen Antworten, sondern Versuche. Essays, Notizen, Fragen.</p>

            <h3>Was das hier ist</h3>
            <p>Das Hasimuener Journal ist kein Nachrichtenportal und kein Aktivismus-Blog. Es ist ein Ort zum Nachdenken — über digitale Macht, über Gesellschaft, über die Frage, wie wir leben wollen. Manchmal analytisch, manchmal persönlich, immer im Werden.</p>
            <p>Wer mitlesen oder mitdenken will, ist willkommen.</p>

        </div>
    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
