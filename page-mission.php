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

<?php
$hp_home_url       = home_url( '/' );
$hp_essay_url      = get_post_type_archive_link( 'essay' );
$hp_note_url       = get_post_type_archive_link( 'note' );
$hp_contact_url    = hp_get_contact_page_url();
$hp_newsletter_url = hp_get_newsletter_anchor_url();
?>

<article id="main-content" class="hp-mission" aria-label="<?php the_title_attribute(); ?>" role="main">

    <header class="hp-mission__header">
        <span class="hp-kicker">Über</span>
        <h1 class="hp-mission__title"><?php the_title(); ?></h1>
        <p class="hp-mission__subline">Zwischen Sprachen, Erinnerung und politischen Räumen entsteht hier ein Journal, das auf Klärung statt Polemik zielt.</p>
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

            <div class="hp-mission__intro" aria-label="Was Sie hier erwartet">
                <p class="hp-mission__intro-label">Was Sie hier erwartet</p>
                <p>Hier schreibe ich über <strong>Macht</strong>, <strong>Medien</strong>, <strong>Erinnerung</strong> und Gesellschaft aus einer Perspektive zwischen Sprachen, Erfahrungen und politischen Räumen.</p>
                <p>Mein Ziel ist Klärung, nicht Polemik. Diese Seite skizziert meine Perspektive, meine Haltung und den Anspruch dieses Journals als Ort für Gespräch, Widerspruch und genaues Hinsehen.</p>
            </div>

            <section class="hp-mission__section" aria-labelledby="mission-perspektive">
                <h2 id="mission-perspektive">Meine Perspektive: Zwischen drei Sprachen und Welten</h2>
                <p>Geboren in Nordkurdistan, aufgewachsen in Deutschland. Ich lebe und denke zwischen Deutsch, Kurdisch und Türkisch. Diese drei Sprachen haben mich geprägt, und keine von ihnen ist für mich nur ein Werkzeug. In ihnen liegen Erinnerungen, Beziehungen, Verletzungen und Möglichkeiten.</p>
                <p>Kurdisch ist die Sprache meiner Herkunft und meiner Nähe. Türkisch gehört zu meiner Geschichte und zu den Räumen, in denen viele Erfahrungen meines Lebens verhandelt wurden. Deutsch ist die Sprache, in der ich vieles öffentlich ausarbeite. Zwischen diesen Sprachen zu leben heißt für mich, nicht nur Wörter zu übersetzen, sondern Erfahrungen, Empfindlichkeiten und Sichtweisen.</p>
            </section>

            <blockquote class="hp-mission__quote">
                <p>Ich lebe und denke zwischen Deutsch, Kurdisch und Türkisch.</p>
            </blockquote>

            <section class="hp-mission__section" aria-labelledby="mission-themen">
                <h2 id="mission-themen">Was mich antreibt: Themen, Begriffe, Fragen</h2>
                <p>Meine Themen sind Sprache, <strong>Erinnerung</strong>, Identität, Medien, <strong>Macht</strong> und die Frage, wie Menschen unter unterschiedlichen Bedingungen miteinander leben können, ohne einander die Würde abzusprechen.</p>
                <p>Begriffe wie <strong>Macht</strong>, <strong>Freiheit</strong>, <strong>Erinnerung</strong> und die Aufgabe, <strong>Brücken zu bauen</strong>, hebe ich bewusst hervor. Sie sind für mich keine neutralen Schlagworte. In ihnen verdichten sich Erfahrungen, Konflikte und Möglichkeiten; sie helfen mir, sichtbar zu machen, was Menschen prägt, trennt und trotzdem verbinden kann.</p>
                <p>Mich interessieren weniger Parolen als die Strukturen, die Wahrnehmung formen. Ich frage, warum Ideologien Staaten prägen, warum sich bestimmte Muster wiederholen und wie man ihnen mit Klarheit, Haltung und Besonnenheit begegnen kann.</p>
            </section>

            <section class="hp-mission__section" aria-labelledby="mission-haltung">
                <h2 id="mission-haltung">Meine Haltung: Dialog statt Lagerdenken</h2>
                <p><strong>Ich verstehe mich als Brückenbauer</strong> zwischen den Welten, die mir vertraut sind. Das heißt nicht, Unterschiede kleinzureden. Es heißt, sie ernst zu nehmen, ohne aus ihnen Feindbilder zu machen.</p>
                <p>Ich schreibe nicht aus dem Wunsch heraus, Lager zu bedienen. Ich suche den Dialog, auch dort, wo er anstrengend ist: zwischen Gemeinschaften, zwischen politischen Sprachen, zwischen individueller Erfahrung und öffentlicher Ordnung, auch im Gespräch mit staatlichen Institutionen.</p>
                <p>Konkret kann das bedeuten, die Sprache von Sicherheit und Ordnung mit den Erfahrungen derer zusammenzudenken, die Ohnmacht erleben; oder Konflikte zwischen Gemeinschaften so zu benennen, dass Widerspruch möglich bleibt, ohne das Gegenüber aus dem Gespräch zu drängen.</p>
                <p>Dafür suche ich eine Sprache, die Zusammenhänge hält: eine Sprache, die Unterschiede benennt, ohne sie zu verhärten, und Kritik formuliert, ohne Menschen oder Institutionen vorschnell zu Gegnern zu erklären.</p>
            </section>

            <section class="hp-mission__section" aria-labelledby="mission-freiheit">
                <h2 id="mission-freiheit">Freiheit als gelebte Praxis</h2>
                <p><strong>Freiheit ist für mich eine Praxis.</strong> Sie zeigt sich darin, dass Menschen sprechen, zuhören, widersprechen und Verantwortung miteinander teilen können. Sie ist nicht nur ein Recht, sondern auch eine Form des Umgangs: im Privaten, im Politischen und in der Öffentlichkeit.</p>
                <p>Gerade deshalb beschäftigt mich auch, wie Ohnmacht entsteht. Hierarchische Ordnung kann Menschen ohnmächtig machen. Diese Ohnmacht geht für mich selten nur von einer einzelnen Instanz aus; sie entsteht oft in der Wechselwirkung zwischen Staat, Gesellschaft und Individuum.</p>
                <p>Wo Menschen nicht mehr sprechen, widersprechen oder mitgestalten können, gerät Freiheit unter Druck. Sie zu bewahren heißt für mich deshalb auch, die Bedingungen sichtbar zu machen, die Menschen klein halten.</p>
            </section>

            <section class="hp-mission__section" aria-labelledby="mission-ki">
                <h2 id="mission-ki">Technik, Urteilskraft und Verantwortung</h2>
                <p>Diese Fragen enden nicht bei Geschichte und Politik. Sie verschieben sich gerade in den technologischen Alltag.</p>
                <p>Im Zeitalter der Künstlichen Intelligenz wird neu sichtbar, was menschliche Urteilskraft trägt. Wenn Redaktionen, Verwaltungen oder Bildungseinrichtungen Aufgaben an Systeme delegieren, geht es nicht nur um Effizienz, sondern um Urteilskraft, Erinnerung, Empathie und Verantwortung.</p>
                <p>Technik ist für mich weder Heilsversprechen noch Feindbild. Sie verändert, wie wir über Arbeit, Wert, Öffentlichkeit und Menschsein nachdenken. Gerade deshalb muss man sie nicht nur nutzen, sondern auch gesellschaftlich und ethisch befragen.</p>
                <p>Auch hier geht es für mich am Ende um dieselbe Frage: wie Urteilskraft, Verantwortung und Freiheit unter neuen Bedingungen bewahrt werden können.</p>
            </section>

            <section class="hp-mission__section" aria-labelledby="mission-journal">
                <h2 id="mission-journal">Warum dieses Journal? Ein Raum für Klärung</h2>
                <p>Dieses Journal ist ein Ort für solche Klärungen. Kein Tribunal, keine ideologische Schule, kein moralischer Hochsitz. Eher ein Raum, in dem Erfahrungen, Begriffe und Perspektiven miteinander ins Gespräch kommen können.</p>
                <p>Die Gedanken zu <strong>Macht</strong>, <strong>Freiheit</strong>, <strong>Erinnerung</strong> und dem <strong>Bauen von Brücken</strong> werden in den Schriften dieses Journals weiter ausgearbeitet. Diese Seite markiert den Ausgangspunkt; die Essays und Notizen gehen in die Tiefe.</p>
                <p>Kein Tracking, keine Werbung, keine Cookies. Nicht aus Technikfeindlichkeit, sondern weil Aufmerksamkeit für mich keine Ware ist. Dieser Ort soll ruhig genug sein, damit Gedanken sich entfalten können und aus Rede im besten Fall Gespräch wird.</p>
            </section>

            <section class="hp-mission__section" aria-labelledby="mission-einladung">
                <h2 id="mission-einladung">Eine Einladung zum Mitdenken und Widersprechen</h2>
                <p><strong>Ich sehe niemanden als Feind.</strong> Das bedeutet nicht Naivität, sondern eine Entscheidung für eine andere Haltung: genau hinsehen, klar benennen, Widerspruch aushalten und trotzdem ansprechbar bleiben.</p>
                <p>Wer hier liest, ist eingeladen, mitzudenken, mitzufragen und, wenn nötig, auch zu widersprechen. Mich interessiert kein zustimmendes Publikum, sondern ein Gespräch, in dem Kritik klärt statt verhärtet.</p>
            </section>

            <div class="hp-mission__cta" aria-label="Nächste Schritte">
                <h2>Nächste Schritte</h2>
                <div class="hp-mission__cta-grid">
                    <a class="hp-mission__cta-card" href="<?php echo esc_url( $hp_home_url ); ?>">
                        <span class="hp-mission__cta-title">Zu den neuesten Texten</span>
                        <span class="hp-mission__cta-copy">Aktuelle Essays und Notizen auf der Startseite des Journals.</span>
                    </a>

                    <a class="hp-mission__cta-card" href="<?php echo esc_url( $hp_essay_url ); ?>">
                        <span class="hp-mission__cta-title">Zu den Essays</span>
                        <span class="hp-mission__cta-copy">Längere Analysen zu Macht, Medien, Erinnerung und Gesellschaft.</span>
                    </a>

                    <a class="hp-mission__cta-card" href="<?php echo esc_url( $hp_note_url ); ?>">
                        <span class="hp-mission__cta-title">Zu den Notizen</span>
                        <span class="hp-mission__cta-copy">Kürzere Beobachtungen, Quellen und Fragmente aus der laufenden Arbeit.</span>
                    </a>

                    <a class="hp-mission__cta-card" href="<?php echo esc_url( $hp_newsletter_url ); ?>">
                        <span class="hp-mission__cta-title">Neue Texte per E-Mail</span>
                        <span class="hp-mission__cta-copy">Kurze Hinweise, sobald ein neuer Essay erscheint oder ein Text weiterführt.</span>
                    </a>

                    <a class="hp-mission__cta-card" href="<?php echo esc_url( $hp_contact_url ); ?>">
                        <span class="hp-mission__cta-title">Kontakt / Dialog beginnen</span>
                        <span class="hp-mission__cta-copy">Per E-Mail Kontakt aufnehmen und das Gespräch eröffnen.</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
