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

            <p>Geboren in Nordkurdistan — den Regionen, die der türkische Staat als seinen Südosten beansprucht. Aufgewachsen in Deutschland. Drei Sprachen, drei Welten, keine davon neutral.</p>

            <p>Kurdisch ist die Sprache, die mir gehört und die ein Staat mir nehmen wollte. Türkisch ist die Sprache, die an ihre Stelle gesetzt wurde — in Nordkurdistan war selbst das Sprechen des Kurdischen lange verboten. Deutsch ist die Sprache, in der ich gelernt habe, beides zu benennen. Jede dieser Sprachen trägt eine Machtgeschichte. Zusammen formen sie den Blick, aus dem heraus ich schreibe.</p>

            <p>Ich bin politisch — lokal, auf der Straße und am Schreibtisch. Was mich antreibt, beginnt beim eigenen Dasein: bei der Erfahrung, dass die Welt, in die man geworfen wird, nicht zufällig geordnet ist, sondern hierarchisch. Aus dieser Erfahrung werden Fragen: Was geschieht mit Erinnerung in hierarchisch organisierten Gesellschaften? Wie formt staatliche Macht, was gedacht und gesagt werden darf?</p>

            <p>Und dann ist da die Frage nach der Freiheit — nicht als Ideal, sondern als Problem. Was bedeutet Freiheit, wenn die Realität, in der sie stattfinden soll, monopolistisch und hierarchisch verfasst ist? Ein System, das Ordnung durch Unterordnung herstellt, beeinflusst nicht nur, wer frei ist — es formt, was Freiheit überhaupt bedeuten darf. Der Begriff selbst wird zum Produkt der Verhältnisse, die ihn begrenzen.</p>

            <p>Ich glaube: Wenn Menschen erkennen, dass sie frei sind — jenseits der Ordnung, die ihnen ihre Stelle zuweist —, dann sind sie es auf der Stelle. Nicht weil das System verschwindet, sondern weil es seine Selbstverständlichkeit verliert. Freiheit beginnt dort, wo das Bewusstsein dem System zuvorkommt.</p>

            <p>Das klingt einfach. Ist es nicht. Freiheit ist zugleich abstrakt und konkret — ein philosophischer Begriff und eine gelebte Bedingung. Sie lässt sich nicht losgelöst denken: nicht ohne die Systeme, in denen sie stattfindet, nicht ohne die anderen Menschen, nicht ohne die Welt, auf die sie sich bezieht. Genau deshalb muss der Begriff immer wieder durchsucht werden: Was meinen wir, wenn wir ihn benutzen? Wessen Freiheit? Unter welchen Bedingungen? Mein Freiheitsbegriff entsteht dort, wo Erfahrung auf Struktur trifft, wo das eigene Leben die Theorie prüft.</p>

            <p>Im Zeitalter der Künstlichen Intelligenz wird diese Frage dringlicher. Wenn Maschinen leisten, was bisher Menschen leisteten, verschiebt sich die Frage von Können auf Sein — und auf Werden. Denn Leben ist nicht Zustand, sondern Prozess: ein fortwährendes Entfalten, das sich keiner Bilanz fügt. Die kapitalistische Logik hat darauf eine klare Antwort: Was effizienter ersetzt werden kann, verliert an Wert. Der Mensch wird, gemessen an den Maßstäben des Systems, überflüssig — nicht weil er es ist, sondern weil das System nur in Verwertung denkt.</p>

            <p>Doch genau darin liegt eine unbeabsichtigte Offenbarung. Dass eine Maschine die Arbeit des Menschen übernehmen kann, zeigt nicht die Überflüssigkeit des Menschen — es zeigt die Absurdität eines Systems, das seinen Sinn aus endloser Produktion und Verwertung zieht. Die Überproduktion war immer schon überflüssig. KI macht es nur sichtbar. Was als Krise des Menschen erscheint, ist in Wahrheit eine Krise des Systems, das ihn nur als Funktion kannte.</p>

            <p>Dagegen steht etwas, das sich nicht automatisieren lässt: Empathie, Verbundenheit, die Fähigkeit, dem Dasein einen Sinn zu geben, der über Funktion hinausgeht. Wenn Gesellschaften keine Strategien entwickeln, die auf diesen Grundlagen aufbauen — auf Liebe zum Dasein an sich —, dann überlassen sie die Definition von Wert einer Logik, die den Menschen nicht braucht. Die Frage ist nicht, ob KI den Menschen ersetzt. Die Frage ist, ob der Mensch sich selbst als das erkennt, was keine Maschine sein kann.</p>

            <p>Dies ist der Ort für diese Fragen. Macht, Erinnerung, Freiheit, Identität, Medien. Keine fertigen Antworten — aber der Anspruch, die Strukturen freizulegen, die Antworten verhindern.</p>

            <p>Das gilt auch für diesen Ort selbst: kein Tracking, keine Werbung, keine Cookies. Wer Machtverhältnisse analysiert, darf sie nicht nebenbei reproduzieren.</p>

        </div>
    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
