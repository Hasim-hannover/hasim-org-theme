<?php
/**
 * Template Name: Warum ich schreibe
 *
 * Persönliche Seite — wer ich bin, was mich bewegt,
 * warum dieses Journal existiert.
 *
 * @package Hasimuener_Journal
 * @version 3.1.0
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<article class="hp-mission" aria-label="<?php the_title_attribute(); ?>">

    <header class="hp-mission__header">
        <span class="hp-kicker">Zwischenräume</span>
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

            <h3>Kein Transhumanist</h3>
            <p>Ich bin ein großer Bewunderer von Technik. Wenn man sie vernünftig einsetzt, ist sie Magie. Aber ich bin kein Transhumanist.</p>
            <p>Ich glaube nicht daran, dass wir das Menschliche „überwinden" oder mit der Maschine verschmelzen müssen, um besser zu sein. Empathie, Zweifel und das Bedürfnis nach Nähe sind keine Fehler im System, die man wegoptimieren muss. Sie sind der Sinn.</p>
            <p>Technik soll uns den Rücken freihalten, damit wir Menschen bleiben können — sie soll uns nicht ersetzen.</p>

            <h3>Die Dialektik der Rose</h3>
            <p>Eine Rose muss nicht erklärt bekommen, dass sie schön ist. Aber sie blüht, um gesehen zu werden. Sie will gefallen, sie will Resonanz.</p>
            <p>Das ist ein zutiefst menschliches Prinzip: Wir wollen nicht im Verborgenen blühen. Wir suchen Kontakt.</p>
            <p>Doch das Internet ist heute oft ein Ort, an dem alle schreien und niemand zuhört. Ein Ort der Kälte, nicht der Begegnung. Wir müssen aufpassen, dass wir in diesem Lärm nicht verlernen, was echte Resonanz bedeutet.</p>

            <h3>Struktur als Fürsorge</h3>
            <p>Wenn man seine Wurzeln verpflanzt — wie ich als Kind kurdischer Herkunft aus der Türkei nach Deutschland —, lernt man schnell: Regeln können ausgrenzen. Systeme können kalt sein.</p>
            <p>Aber ich habe auch gelernt: Gute Organisation kann Freiheit schenken. Doch Struktur hat nur dann eine positive Wirkung, wenn sie mit dem „Super Skill" Empathie gebaut wird. Wenn sie aus Liebe getan wird.</p>
            <p><strong>Struktur ohne Liebe ist niemals harmlos. Sie ist Kälte.</strong></p>
            <p>Sie reduziert den Menschen auf eine Funktion, auf eine Nummer. Sie ordnet nicht, um zu helfen, sondern um verfügbar zu machen.</p>
            <p>Struktur mit Liebe hingegen ist wie ein Rankgitter für die Rose: Sie engt nicht ein, sondern sie gibt den Halt, den man braucht, um über sich hinauszuwachsen.</p>

            <h3>Was du hier findest</h3>
            <p>Dieses Journal ist mein Versuch, laut zu denken — aber ohne zu schreien. Ein Ort, an dem ich aufschreibe, was mich bewegt, bevor ich es in fertige Antworten presse.</p>
            <ul>
                <li><strong>Essays</strong> — Über Technik, die dem Leben dient. Über Strukturen, die Menschen tragen oder erdrücken.</li>
                <li><strong>Notizen</strong> — Kürzere Gedanken, Beobachtungen, Fragen, die noch reifen dürfen.</li>
            </ul>
            <p>Hier geht es um <strong>Wahrhaftigkeit</strong>.</p>
            <p>Wer den Unterschied zwischen kalter Optimierung und liebevoller Struktur spürt: Willkommen.</p>

        </div>
    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
