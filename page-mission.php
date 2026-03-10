<?php
/**
 * Template Name: Mission
 *
 * Mission-Seite des Journals.
 *
 * @package Hasimuener_Journal
 * @version 6.7.0
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<?php
$hp_essay_url   = get_post_type_archive_link( 'essay' );
$hp_note_url    = get_post_type_archive_link( 'note' );
$hp_contact_url = hp_get_contact_page_url();
?>

<main id="main-content" class="hp-mission" aria-labelledby="mission-title" role="main">

	<header class="hp-mission__hero">
		<span class="hp-kicker">Mission</span>
		<h1 id="mission-title" class="hp-mission__title">Mission</h1>
		<p class="hp-mission__lede">Diese Seite ist kein Ort für Lagerdenken. Sie ist ein Versuch, zwischen Sprachen, Erfahrungen und politischen Wirklichkeiten Verständigung offenzuhalten – mit Klarheit, mit Haltung und ohne Menschen aus dem Gespräch zu drängen.</p>
	</header>

	<div class="single-body single-body--with-toc hp-mission__frame">

		<aside class="hp-toc hp-mission__toc" aria-label="Inhaltsverzeichnis" data-visible="true">
			<span class="hp-toc__title">Inhalt</span>
			<ol>
				<li><a href="#worum-es-hier-geht">Worum es hier geht</a></li>
				<li><a href="#warum-ich-so-schreibe">Warum ich so schreibe</a></li>
				<li><a href="#bruecken-statt-lager">Brücken statt Lager</a></li>
				<li><a href="#was-bewahrt-werden-muss">Was bewahrt werden muss</a></li>
				<li><a href="#kritik-ohne-entmenschlichung">Kritik ohne Entmenschlichung</a></li>
				<li><a href="#technik-oeffentlichkeit-verantwortung">Technik, Öffentlichkeit, Verantwortung</a></li>
				<li><a href="#einladung-zum-mitdenken">Einladung zum Mitdenken</a></li>
			</ol>
		</aside>

		<div class="single-body__main hp-mission__content">

			<section class="hp-mission__section" aria-labelledby="worum-es-hier-geht">
				<h2 id="worum-es-hier-geht">Worum es hier geht</h2>
				<p>Ich schreibe über Macht, Medien, Erinnerung, Sprache und Gesellschaft. Nicht, um fertige Antworten zu verkaufen, sondern um Zusammenhänge sichtbar zu machen. Mich interessiert, was Menschen trennt, wie Deutungen entstehen und unter welchen Bedingungen Verständigung trotzdem möglich bleibt.</p>
				<p>Dieses Journal sucht keine schnelle Zustimmung. Es soll ein Ort sein, an dem Genauigkeit wichtiger ist als Lautstärke, Widerspruch möglich bleibt und Gedanken nicht sofort in Lager zerfallen.</p>
			</section>

			<section class="hp-mission__section" aria-labelledby="warum-ich-so-schreibe">
				<h2 id="warum-ich-so-schreibe">Warum ich so schreibe</h2>
				<p>Ich lebe und denke zwischen Deutsch, Kurdisch und Türkisch. Diese drei Sprachen sind für mich keine bloßen Werkzeuge. In ihnen liegen Herkunft, Nähe, Konflikt, Missverständnis und Erfahrung.</p>
				<p>Zwischen ihnen zu leben heißt nicht nur, Wörter zu übersetzen. Es heißt, Perspektiven, Verletzungen, historische Erfahrungen und politische Empfindlichkeiten lesen zu lernen. Genau daraus entsteht mein Blick auf Gesellschaft: nicht eindimensional, nicht national verengt, nicht ideologisch beruhigt.</p>
			</section>

			<section class="hp-mission__section" aria-labelledby="bruecken-statt-lager">
				<h2 id="bruecken-statt-lager">Brücken statt Lager</h2>
				<p>Mich interessiert kein Publikum, das nur bestätigt, was es ohnehin schon denkt. Mich interessiert ein Raum, in dem Unterschiede ernst genommen werden, ohne daraus Feindbilder zu machen.</p>
				<p>Verständigung heißt nicht, Konflikte zu beschönigen. Verständigung heißt, sie so zu benennen, dass Kritik möglich bleibt, ohne das Gegenüber aus dem Gespräch zu drängen. Ich halte das nicht für Schwäche, sondern für eine Form geistiger Disziplin.</p>
				<div class="hp-mission__statement" aria-label="Kernpositionierung">
					<p>Ich verteidige nicht Lager, sondern die Möglichkeit von Verständigung.</p>
				</div>
			</section>

			<section class="hp-mission__section" aria-labelledby="was-bewahrt-werden-muss">
				<h2 id="was-bewahrt-werden-muss">Was bewahrt werden muss</h2>
				<p>Kultur ist für mich keine Folklore und kein Museum. Sie lebt in Sprache, Erinnerung, Gesten, Erfahrungen und Formen des Zusammenlebens.</p>
				<p>Wo Sprache verarmt, Erinnerung verdrängt und Würde beschädigt wird, wird auch Verständigung ärmer. Darum gehört zum Schutz von Kultur für mich nicht nur Bewahrung, sondern auch Übersetzung: die Fähigkeit, Erfahrungen lesbar zu machen, bevor sie verzerrt, vereinnahmt oder vergessen werden.</p>
			</section>

			<section class="hp-mission__section" aria-labelledby="kritik-ohne-entmenschlichung">
				<h2 id="kritik-ohne-entmenschlichung">Kritik ohne Entmenschlichung</h2>
				<p>Kritik ist notwendig. Aber sie verliert ihren Wert, wenn sie nur herabsetzt. Ich will Zustände, Strukturen, Narrative und Ideologien benennen, die Menschen klein halten oder gegeneinander aufbringen – ohne dabei selbst in dieselbe Verrohung zu kippen, die ich kritisiere.</p>
				<p>Mich interessieren Klarheit ohne Kälte, Haltung ohne Hass und Urteilskraft ohne moralische Eitelkeit.</p>
			</section>

			<section class="hp-mission__section" aria-labelledby="technik-oeffentlichkeit-verantwortung">
				<h2 id="technik-oeffentlichkeit-verantwortung">Technik, Öffentlichkeit, Verantwortung</h2>
				<p>Die Frage nach Macht und Urteilskraft stellt sich heute auch im technologischen Alltag neu. Im Zeitalter algorithmischer Öffentlichkeit und künstlicher Intelligenz geht es nicht nur um Effizienz, sondern um Aufmerksamkeit, Verantwortung und die Bedingungen, unter denen Menschen noch selbst urteilen können.</p>
				<p>Auch deshalb braucht es Orte, die nicht nur reagieren, sondern denken. Orte, an denen Sprache nicht bloß Output ist, sondern ein Mittel der Unterscheidung, der Erinnerung und der Verantwortung.</p>
			</section>

			<section class="hp-mission__section" aria-labelledby="einladung-zum-mitdenken">
				<h2 id="einladung-zum-mitdenken">Einladung zum Mitdenken</h2>
				<p>Dieses Journal will kein Tribunal sein und keine ideologische Schule. Es soll ein ruhiger Ort sein für <a href="<?php echo esc_url( $hp_essay_url ); ?>">Essays</a>, <a href="<?php echo esc_url( $hp_note_url ); ?>">Notizen</a> und Gespräche, in denen Erinnerung, Sprache, Politik und Gesellschaft ernst genommen werden.</p>
				<p>Wer hier liest, ist eingeladen mitzudenken, zu widersprechen und die eigene Perspektive zu schärfen. Nicht, um am Ende alle gleich zu machen. Sondern damit Verständigung überhaupt eine Chance behält.</p>
			</section>

			<section class="hp-mission__closing" aria-label="Abschluss und nächste Schritte">
				<p>Keine Werbung, keine Ablenkung, kein Lärm um seiner selbst willen. Dieser Ort soll ruhig genug sein, damit Gedanken sich entfalten können und aus Rede wieder Gespräch werden kann.</p>

				<div class="hp-mission__cta" aria-label="Nächste Schritte">
					<div class="hp-mission__cta-grid">
						<a class="hp-mission__cta-card" href="<?php echo esc_url( $hp_essay_url ); ?>">
							<span class="hp-mission__cta-title">Zu den Essays</span>
							<span class="hp-mission__cta-copy">Längere Analysen zu Macht, Medien, Erinnerung, Sprache und Gesellschaft.</span>
						</a>

						<a class="hp-mission__cta-card" href="<?php echo esc_url( $hp_note_url ); ?>">
							<span class="hp-mission__cta-title">Zu den Notizen</span>
							<span class="hp-mission__cta-copy">Kürzere Beobachtungen, Fragmente und Zwischengedanken aus der laufenden Arbeit.</span>
						</a>

						<a class="hp-mission__cta-card" href="<?php echo esc_url( $hp_contact_url ); ?>">
							<span class="hp-mission__cta-title">Anfragen &amp; Zusammenarbeit</span>
							<span class="hp-mission__cta-copy">Für redaktionelle Gespräche, Kooperationen und ausgewählte Projekte.</span>
						</a>
					</div>
				</div>
			</section>

		</div>
	</div>

</main>

<?php endwhile; ?>

<?php get_footer(); ?>
