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

            <p>Geboren in Nordkurdistan, aufgewachsen in Deutschland. Ich lebe und denke zwischen Kurdisch, Türkisch und Deutsch. Diese drei Sprachen haben mich geprägt, und keine von ihnen ist für mich nur ein Werkzeug. In ihnen liegen Erinnerungen, Beziehungen, Verletzungen und Möglichkeiten.</p>

            <p>Kurdisch ist die Sprache meiner Herkunft und meiner Nähe. Türkisch gehört zu meiner Geschichte und zu den Räumen, in denen viele Erfahrungen meines Lebens verhandelt wurden. Deutsch ist die Sprache, in der ich vieles öffentlich ausarbeite. Ich stelle diese Sprachen nicht gegeneinander. Ich versuche, zwischen ihnen zu übersetzen: nicht nur Wörter, sondern Erfahrungen, Empfindlichkeiten und Sichtweisen.</p>

            <p>Ich verstehe mich als Brückenbauer zwischen den Welten, die mir vertraut sind. Das heißt nicht, Unterschiede kleinzureden. Es heißt, sie ernst zu nehmen, ohne aus ihnen Feindbilder zu machen. Mich interessiert, wie Verständigung möglich wird, wo Biografien, Begriffe und politische Erfahrungen auseinandergehen.</p>

            <p>Ich schreibe nicht aus dem Wunsch heraus, Lager zu bedienen. Ich suche den Dialog, auch dort, wo er anstrengend ist: zwischen Gemeinschaften, zwischen politischen Sprachen, zwischen individueller Erfahrung und öffentlicher Ordnung, auch im Gespräch mit staatlichen Institutionen. Kritik bleibt notwendig, aber sie verliert für mich ihren Sinn, wenn sie nur noch Verhärtung erzeugt.</p>

            <p>Meine Themen sind Sprache, Erinnerung, Identität, Medien, Macht und die Frage, wie Menschen unter unterschiedlichen Bedingungen miteinander leben können, ohne einander die Würde abzusprechen. Ich interessiere mich weniger für Parolen als für die Strukturen, die Wahrnehmung formen, und für die Möglichkeiten, ihnen mit Klarheit, Haltung und Besonnenheit zu begegnen.</p>

            <p>Freiheit ist für mich keine Pose und kein Rückzug ins eigene Lager. Sie zeigt sich darin, dass Menschen sprechen, zuhören, widersprechen und Verantwortung miteinander teilen können. Sie ist nicht nur ein Recht, sondern auch eine Praxis des Umgangs: im Privaten, im Politischen und in der Öffentlichkeit.</p>

            <p>Im Zeitalter der Künstlichen Intelligenz wird diese Frage neu gestellt. Wenn Maschinen mehr Aufgaben übernehmen, geht es nicht nur um Effizienz, sondern um Urteilskraft, Erinnerung, Empathie und Verantwortung. Technik ist für mich weder Heilsversprechen noch Feindbild. Sie verändert, wie wir über Arbeit, Wert, Öffentlichkeit und Menschsein nachdenken, und genau deshalb muss man sie ernst nehmen.</p>

            <p>Dieses Journal ist ein Ort für solche Klärungen. Kein Tribunal, keine ideologische Schule, kein moralischer Hochsitz. Eher ein Raum, in dem Erfahrungen, Begriffe und Perspektiven miteinander ins Gespräch kommen können. Nicht um Unterschiede aufzulösen, sondern um sie verständlich zu machen.</p>

            <p>Ich sehe niemanden als Feind. Das bedeutet nicht Naivität, sondern eine Entscheidung für eine andere Haltung: genau hinsehen, klar benennen, Widerspruch aushalten und trotzdem ansprechbar bleiben. Wer hier liest, ist eingeladen, mitzudenken, mitzufragen und, wenn nötig, auch zu widersprechen.</p>

            <p>Kein Tracking, keine Werbung, keine Cookies. Nicht aus Technikfeindlichkeit, sondern weil Aufmerksamkeit für mich keine Ware ist. Dieser Ort soll ruhig genug sein, damit Gedanken sich entfalten können und aus Rede im besten Fall Gespräch wird.</p>

        </div>
    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
