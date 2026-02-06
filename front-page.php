<?php
/**
 * Template: Front Page — Hasimuener Journal
 * Description: Digitale Publikation im NZZ/ZEIT-Stil. Deutsch. Manifest + Newsroom.
 *
 * @package Hasimuener_Journal
 */

get_header(); ?>

<main id="journal-front" class="journal-front" role="main">

    <!-- 1. THE MASTHEAD -->
    <section class="masthead" aria-label="Masthead">
        <div class="masthead__inner">
            <h1 class="masthead__headline">Ergebnisse messen keine&nbsp;Zeit.</h1>
            <p class="masthead__subline">Digitale Architektur &amp; KI-Strategie für Unternehmen, die entkoppelt von alten Strukturen wachsen.</p>
            <a href="/manifest" class="masthead__link">Das Manifest lesen &rarr;</a>
        </div>
    </section>

    <hr class="journal-rule" aria-hidden="true">

    <!-- 2. THE EDITORIAL (Manifest) -->
    <article class="lead-editorial" aria-label="Editorial">
        <header class="lead-editorial__header">
            <span class="lead-editorial__kicker">Editorial</span>
            <h2 class="lead-editorial__title">Das Ende der fakturierbaren Stunde</h2>
        </header>

        <div class="lead-editorial__columns">
            <div class="lead-editorial__col">
                <p>
                    Seit Jahrzehnten operiert die Beratungsbranche auf einer fehlerhaften
                    Prämisse: dass der Wert intellektueller Arbeit in Zeit gemessen werden
                    kann. Ein erfahrener Architekt, der ein systemisches Problem in vierzig
                    Minuten löst, ist nach dieser Logik weniger wert als ein Junior-Entwickler,
                    der vierzig Stunden daran kreist. Das Modell belohnt Ineffizienz. Es
                    bestraft Meisterschaft.
                </p>
                <p>
                    Im Zeitalter generativer KI wird dieser Widerspruch unhaltbar. Wenn ein
                    präzise formulierter Prompt in Sekunden produziert, wofür einst Tage
                    manueller Arbeit nötig waren, hört der Stundensatz auf, als Maßeinheit
                    für Wert zu funktionieren. Was bleibt, ist die Architektur — die
                    Entscheidungen über Struktur, Sequenz und Systemdesign, die kein Modell
                    autonom treffen kann.
                </p>
            </div>
            <div class="lead-editorial__col">
                <p>
                    Ich habe das Stundenmodell daher vollständig aufgegeben. An seine Stelle
                    treten Module: klar umrissene Einheiten architektonischen Werts, jedes
                    mit einem definierten Ergebnis, einer fixen Investition und einem
                    messbaren Resultat. Der Auftraggeber zahlt für die Transformation —
                    nicht für die Zeit, die sie beansprucht.
                </p>
                <p>
                    Dies ist keine Preisstrategie. Es ist eine philosophische Position.
                    Ich bin überzeugt, dass Souveränität — digital, operativ, strategisch —
                    mit der Weigerung beginnt, Expertise zur Ware zu machen. Architektur
                    ist keine Arbeit. Sie ist Urteilsvermögen. Und Urteilsvermögen hat
                    keinen Stundensatz.
                </p>
            </div>
        </div>
    </article>

    <hr class="journal-rule" aria-hidden="true">

    <!-- 3. THE DEPARTMENTS (Kompetenzfelder) -->
    <section class="competence-grid" aria-label="Kompetenzfelder">
        <header class="competence-grid__header">
            <h2 class="competence-grid__title">Kompetenzfelder</h2>
        </header>

        <div class="competence-grid__columns">

            <article class="competence-card">
                <span class="competence-card__number">I</span>
                <h3 class="competence-card__title">WordPress-Ökosysteme</h3>
                <p class="competence-card__desc">
                    High-End-Entwicklung. Server-Level-Caching, Core-Web-Vitals-Optimierung
                    und Theme-Architektur für Ladezeiten unter einer Sekunde. Kein Baukasten.
                    Kein Ballast. Reines Engineering.
                </p>
                <span class="competence-card__label">Technisches Fundament</span>
            </article>

            <article class="competence-card">
                <span class="competence-card__number">II</span>
                <h3 class="competence-card__title">KI-Orchestrierung</h3>
                <p class="competence-card__desc">
                    Prozesse automatisieren. Von intelligenten Content-Pipelines bis zur
                    Workflow-Orchestrierung — Systeme, die lernen, sich anpassen und
                    operativen Aufwand reduzieren. Ohne menschliches Babysitting.
                </p>
                <span class="competence-card__label">KI &amp; Automatisierung</span>
            </article>

            <article class="competence-card">
                <span class="competence-card__number">III</span>
                <h3 class="competence-card__title">Der Retainer</h3>
                <p class="competence-card__desc">
                    Strategische Konstante. Eine langfristige architektonische Partnerschaft,
                    die sicherstellt, dass Ihre digitale Infrastruktur sich bewusst entwickelt —
                    nicht reaktiv. Quartalsreviews. Jahres-Roadmaps.
                </p>
                <span class="competence-card__label">Strategische Kontinuität</span>
            </article>

        </div>
    </section>

    <hr class="journal-rule" aria-hidden="true">

    <!-- 4. THE NEWSROOM (Aktuelle Analysen) -->
    <section class="newsroom" aria-label="Aktuelle Analysen">
        <header class="newsroom__header">
            <h2 class="newsroom__title">Aktuelle Analysen</h2>
        </header>

        <?php
        $hp_latest_posts = new WP_Query( array(
            'posts_per_page' => 3,
            'post_status'    => 'publish',
            'post_type'      => 'post',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        if ( $hp_latest_posts->have_posts() ) : ?>
            <div class="newsroom__list">
                <?php while ( $hp_latest_posts->have_posts() ) : $hp_latest_posts->the_post(); ?>

                    <article class="newsroom__item">
                        <time class="newsroom__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                            <?php echo esc_html( get_the_date( 'j. F Y' ) ); ?>
                        </time>
                        <h3 class="newsroom__item-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <?php if ( has_excerpt() || get_the_content() ) : ?>
                            <p class="newsroom__excerpt">
                                <?php echo esc_html( wp_trim_words( get_the_excerpt(), 28, ' …' ) ); ?>
                            </p>
                        <?php endif; ?>
                    </article>

                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p class="newsroom__empty">Noch keine Analysen veröffentlicht.</p>
        <?php endif;
        wp_reset_postdata(); ?>
    </section>

    <hr class="journal-rule" aria-hidden="true">

    <!-- 5. COLOPHON -->
    <section class="journal-colophon" aria-label="Kolophon">
        <p class="journal-colophon__text">
            Hasimuener Journal &middot; Digitale Architektur &middot; Hannover
        </p>
    </section>

</main>

<?php get_footer(); ?>
