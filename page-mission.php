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

            <h3>Die Rose weiß es</h3>
            <p>Ich bin jemand, der glaubt, dass eine Rose weiß, dass sie schön ist. Dass der Regen weiß, warum er fällt. Dass nichts auf dieser Welt selbstverständlich ist — nicht der Morgen, nicht das Gespräch mit einem Fremden, nicht die Tatsache, dass wir hier sind und einander lesen können.</p>
            <p>Das klingt vielleicht groß. Ist es aber nicht. Es ist das Gegenteil: Wer merkt, dass nichts selbstverständlich ist, wird leiser. Wird aufmerksamer. Schaut genauer hin.</p>

            <h3>Zwischen zwei Welten</h3>
            <p>Ich bin mit sieben Jahren aus der Türkei nach Deutschland gekommen, kurdische Familie. Wenn du zwischen zwei Sprachen aufwächst, zwischen zwei Arten, die Welt zu verstehen, lernst du etwas, das dir niemand beibringen kann: Es gibt immer noch eine andere Seite.</p>
            <p>Das hat mich nicht klüger gemacht. Aber es hat mich gelehrt, zuzuhören, bevor ich spreche. Fragen zu stellen, bevor ich antworte. Und damit zu leben, dass ich nicht alles weiß — und dass das in Ordnung ist.</p>

            <h3>Wahrhaftigkeit, nicht Wahrheit</h3>
            <p>Ich suche hier nicht die große Wahrheit. Dafür bin ich der Falsche. Was ich suche, ist etwas anderes: <strong>Wahrhaftigkeit</strong>. Ehrlich sein mit dem, was ich sehe. Ehrlich sein mit dem, was ich nicht verstehe. Keine fertigen Thesen, kein Belehren, kein erhobener Zeigefinger.</p>
            <p>Ich arbeite als Entwickler, ich baue digitale Systeme. Aber dieses Journal ist kein Tech-Blog. Es geht um die Fragen, die übrig bleiben, wenn man aufhört, alles für gegeben zu halten: <em>Wie wollen wir leben? Was übersehen wir? Was könnten wir anders machen?</em></p>

            <h3>Was du hier findest</h3>
            <p>Dieses Journal ist ein offener Raum:</p>
            <ul>
                <li><strong>Essays</strong> — längere Gedanken über Gesellschaft, Technologie und das, was dazwischen liegt</li>
                <li><strong>Notizen</strong> — kürzere Beobachtungen, Fragen, Ideen im Werden</li>
            </ul>
            <p>Keine Antworten, die so tun, als wären sie fertig. Einfach jemand, der hinschaut und aufschreibt, was er sieht — in der Hoffnung, dass es anderen auch etwas gibt.</p>
            <p>Wer mitlesen will, ist willkommen. Wirklich.</p>

        </div>
    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
