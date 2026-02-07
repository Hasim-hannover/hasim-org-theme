<?php
/**
 * Page Template: Mission (Slug: mission)
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

            <h3>Die Haltung</h3>
            <p>Ich bin mit 7 Jahren nach Deutschland gekommen. Wer mit kurdischen Wurzeln aufwächst, entscheidet sich nicht für Politik – man wird politisiert. Man lernt früh, Codes zu lesen: kulturelle, sprachliche und gesellschaftliche. Man versteht, wie Machtstrukturen funktionieren und was es bedeutet, wenn man sie nicht mitgestalten darf.</p>

            <h3>Die Expertise</h3>
            <p>Als studierter Medienwissenschaftler habe ich gelernt, diese Strukturen theoretisch zu zerlegen. Als Entwickler und Digital-Stratege habe ich gelernt, sie technisch zu bauen.</p>
            <p>Ich sehe heute, dass sich Geschichte im Digitalen wiederholt: Algorithmen sind keine neutralen Werkzeuge, sondern die Gesetzestexte des 21. Jahrhunderts. Wer den Code nicht versteht, wird regiert. Wer ihn versteht, kann gestalten.</p>

            <h3>Das Ziel</h3>
            <p>Das <strong>Hasimuener Journal</strong> ist die Schnittstelle dieser drei Welten: Die politische Dringlichkeit, die medienwissenschaftliche Analyse und die technische Präzision.</p>
            <p>Hier geht es nicht um Hype. Es geht um digitale Souveränität, evidenzbasierte Kritik und die Architektur unserer Zukunft.</p>

        </div>
    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
