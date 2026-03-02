<?php
/**
 * Template Name: Wer hier schreibt
 *
 * Persönliche Seite — wer ich bin, was mich bewegt,
 * warum dieses Journal existiert.
 *
 * @package Hasimuener_Journal
 * @version 4.0.0
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<article class="hp-mission" aria-label="<?php the_title_attribute(); ?>">

    <header class="hp-mission__header">
        <span class="hp-kicker">Zwischenräume</span>
        <h1 class="hp-mission__title"><?php the_title(); ?></h1>
        <p class="hp-mission__subline">Gedanken aus den Zwischenräumen — zwischen Sprachen, Kulturen und dem, was als normal gilt.</p>
    </header>

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

            <h3>Drei Sprachen, drei Welten</h3>
            <p>Ich bin Kurde. Geboren in Nordkurdistan, aufgewachsen in Deutschland. Zwischen drei Sprachen, drei Geschichten, drei Vorstellungen davon, was Ordnung bedeutet.</p>
            <p>Im <strong>Deutschen</strong> höre ich den Staat, in dem ich lebe. Verwaltung, Regeln, den Anspruch auf Ordnung — und die stille Erwartung, sich einzufügen.</p>
            <p>Im <strong>Türkischen</strong> höre ich die Herkunft meiner Familie — und einen Nationalstaat, der das Kurdische jahrzehntelang unterdrückt hat. Stolz und Schweigen in einer Sprache.</p>
            <p>Im <strong>Kurdischen</strong> höre ich die Perspektive derer, die keine Eroberer waren. Die keine imperialen Strukturen gebaut haben, um andere zu beherrschen. Eine Sprache, die überlebt hat, weil Menschen sich geweigert haben zu schweigen.</p>
            <p>Dieser dreifache Blick ist kein Nachteil. Er ist mein Werkzeug.</p>

            <h3>Warum ich schreibe</h3>
            <p>Ich bin politisch. Lokal, konkret, auf der Straße und am Schreibtisch. Ich organisiere mich dort, wo ich lebe — nicht als Beruf, sondern weil es notwendig ist.</p>
            <p>Was mich antreibt: Die Frage, wer in dieser Gesellschaft erinnert wird und wer vergessen. Wer sprechen darf und wer erklärt bekommt, dass jetzt nicht der richtige Moment ist. Wer Strukturen baut und wer von ihnen zerrieben wird.</p>
            <p>Ich bin systemkritisch. Nicht aus Pose, sondern aus Erfahrung. Wer zwischen Kulturen aufwächst, lernt früh, dass das, was als „normal" gilt, immer eine Entscheidung war — von jemandem, der die Macht hatte, sie zu treffen.</p>

            <h3>Was du hier findest</h3>
            <p>„Zwischenräume" ist das Journal für diese Gedanken. Macht und Technologie. Identität und Widerstand. Medien und die Geschichten, die sie erzählen — oder verschweigen.</p>
            <p>Meine Interessen sind vielfältig, meine Haltung ist klar: Ich schreibe für die, die zwischen den Stühlen sitzen. Weil genau dort der beste Blick ist.</p>
            <p>Kein Tracking, keine Werbung, keine Cookies. Gehostet in Deutschland. Die einzige Statistik, die mich interessiert, ist welche Texte gelesen werden — nicht von wem. Dafür nutze ich <a href="https://www.kokoanalytics.com/" target="_blank" rel="noopener">Koko Analytics</a>, cookiefrei.</p>

        </div>
    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
