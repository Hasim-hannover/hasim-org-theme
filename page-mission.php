<?php
/**
 * Template Name: Struktur, aus Liebe gebaut
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
        <p class="hp-mission__subline">Warum ich digitale Räume so baue, dass der Mensch darin nicht verloren geht.</p>
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

            <h3>Drei Sprachen, drei Welten</h3>
            <p>Ich lebe zwischen drei Sprachen: Deutsch, Türkisch, Kurdisch.<br>
            Das ist nicht „multikulturell". Es ist ein Spannungsfeld. Denn jede dieser Sprachen trägt eine eigene Geschichte — und eine eigene Vorstellung davon, was Ordnung bedeutet.</p>
            <p>Im <strong>Deutschen</strong> höre ich den Staat, in dem ich lebe. Verwaltung, Regeln, den Anspruch auf Ordnung — und die stille Erwartung, sich einzufügen.</p>
            <p>Im <strong>Türkischen</strong> höre ich die Herkunft meiner Familie, aber auch einen Nationalstaat, der das Kurdische jahrzehntelang unterdrückt hat. Stolz und Schweigen in einer Sprache.</p>
            <p>Im <strong>Kurdischen</strong> höre ich etwas anderes. Die Perspektive derer, die keine Eroberer waren. Die keine imperialen Strukturen gebaut haben, um andere zu beherrschen. Eine Sprache, die überlebt hat, weil Menschen sich geweigert haben, sie aufzugeben.</p>
            <p>Dieser dreifache Blick hat mich geprägt. Ich habe gelernt, Systeme zu hinterfragen. Ich sehe Nationalismus und Ausgrenzung dort, wo andere nur „Normalität" sehen.</p>
            <p><strong>Deshalb baue ich Dinge, die dem Menschen dienen. Nicht solche, die ihn kleiner machen, als er ist.</strong></p>

            <h3>Kein Transhumanist</h3>
            <p>Vielleicht reagiere ich deshalb so scharf auf den Hype, alles zu automatisieren und den Menschen „abzuschaffen". Ich mache da nicht mit.</p>
            <p>Empathie, Zweifel, das Bedürfnis nach Nähe — das sind keine Fehler im System. Sie sind der Sinn.</p>
            <p>Technik ist ein Werkzeug. Ein gutes Werkzeug macht den Rücken frei. Aber es darf uns niemals ersetzen. Und niemals diktieren, wie wir zu leben haben.</p>

            <h3>Sehen und gesehen werden</h3>
            <p>Warum schreibe ich das hier auf?</p>
            <p>Weil ich an die Dialektik der Rose glaube. Eine Rose ist schön, egal ob jemand hinsieht. Aber sie blüht, um gesehen zu werden.</p>
            <p>Mir geht es genauso. Je tiefer der Blick nach innen, desto klarer der Blick nach außen.</p>
            <p>Ich schreibe, um meine Gedanken zu ordnen. Um ehrlich zu mir selbst zu sein. Und ich glaube: Wenn wir aufhören, Menschen in Schubladen zu stecken — Herkunft, Status, Datenpunkte —, fangen wir erst an, sie wirklich zu sehen.</p>

            <h3>Was du hier findest</h3>
            <p>„Zwischenräume" ist der Ort für diese Gedanken. Kein Marketing-Lärm. Keine fertigen Weisheiten. Sondern der Versuch, genau hinzuschauen.</p>
            <p><strong>Ein Versprechen:</strong><br>
            Hier gibt es keine Überwachung. Keine Tracker, keine Werbung, keinen Cookie-Banner. Dieses Journal setzt keine Cookies — die einzige Statistik, die mich interessiert, ist welche Texte gelesen werden, nicht wer sie liest. Dafür nutze ich <a href="https://www.kokoanalytics.com/" target="_blank" rel="noopener">Koko Analytics</a>, cookiefrei. Gehostet bei <a href="https://www.hostpress.de/" target="_blank" rel="noopener">HostPress</a> in Deutschland.</p>
            <p>Weil Respekt vor deiner Freiheit die Basis von allem ist.</p>
            <p>Willkommen.</p>

        </div>
    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
