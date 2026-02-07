<?php
/**
 * Template: Front Page — Hasimuener Journal
 * 
 * Sektionen:
 * 1. Masthead (Hero / Mission)
 * 2. Featured Essay (neuester Essay)
 * 3. Aktuelle Notizen (3 neueste)
 * 4. Themenfelder (Taxonomie: topic)
 * 5. Kompetenzfelder (statisch)
 * 6. Kolophon
 *
 * @package Hasimuener_Journal
 * @version 2.0.0
 */

get_header(); ?>

<main id="journal-front" class="journal-front" role="main">

    <!-- ==========================================
         1. MASTHEAD
         ========================================== -->
    <section class="masthead" aria-label="Masthead">
        <div class="masthead__inner">
            <h1 class="masthead__headline">Ergebnisse messen keine&nbsp;Zeit.</h1>
            <p class="masthead__subline">Digitale Architektur &amp; KI-Strategie für Unternehmen, die entkoppelt von alten Strukturen wachsen.</p>
            <a href="/manifest" class="masthead__link">Das Manifest lesen &rarr;</a>
        </div>
    </section>

    <hr class="journal-rule" aria-hidden="true">

    <!-- ==========================================
         2. FEATURED ESSAY
         ========================================== -->
    <?php
    $hp_featured = new WP_Query( array(
        'post_type'      => 'essay',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
    ) );

    if ( $hp_featured->have_posts() ) :
        while ( $hp_featured->have_posts() ) : $hp_featured->the_post(); ?>

    <article class="featured-essay" aria-label="Featured Essay">
        <header class="featured-essay__header">
            <span class="hp-kicker">Essay</span>
            <h2 class="featured-essay__title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h2>
            <div class="hp-meta">
                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                    <?php echo esc_html( get_the_date( 'j. F Y' ) ); ?>
                </time>
                <span class="hp-meta__separator"></span>
                <span class="hp-reading-time"><?php echo esc_html( hp_reading_time() ); ?></span>
            </div>
        </header>

        <?php if ( has_excerpt() ) : ?>
            <p class="featured-essay__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
        <?php endif; ?>

        <?php
        $topics = get_the_terms( get_the_ID(), 'topic' );
        if ( $topics && ! is_wp_error( $topics ) ) : ?>
            <ul class="hp-topics" aria-label="Themenfelder">
                <?php foreach ( $topics as $topic ) : ?>
                    <li><a class="hp-topic-pill" href="<?php echo esc_url( get_term_link( $topic ) ); ?>"><?php echo esc_html( $topic->name ); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <a href="<?php the_permalink(); ?>" class="featured-essay__continue">Weiterlesen &rarr;</a>
    </article>

        <?php endwhile;
    else : ?>
        <section class="featured-essay" aria-label="Featured Essay">
            <span class="hp-kicker">Essay</span>
            <p class="hp-empty">Noch keine Essays veröffentlicht. Der erste Essay erscheint hier.</p>
        </section>
    <?php endif;
    wp_reset_postdata(); ?>

    <hr class="journal-rule" aria-hidden="true">

    <!-- ==========================================
         3. AKTUELLE NOTIZEN
         ========================================== -->
    <section class="notes-section" aria-label="Aktuelle Notizen">
        <header>
            <h2 class="hp-section-title">Aktuelle Notizen</h2>
        </header>

        <?php
        $hp_notes = new WP_Query( array(
            'post_type'      => 'note',
            'posts_per_page' => 3,
            'post_status'    => 'publish',
        ) );

        if ( $hp_notes->have_posts() ) : ?>
            <div class="notes-list">
                <?php while ( $hp_notes->have_posts() ) : $hp_notes->the_post(); ?>

                <article class="notes-list__item">
                    <div class="hp-meta">
                        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                            <?php echo esc_html( get_the_date( 'j. F Y' ) ); ?>
                        </time>
                        <span class="hp-meta__separator"></span>
                        <span class="hp-reading-time"><?php echo esc_html( hp_reading_time() ); ?></span>
                    </div>
                    <h3 class="notes-list__title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <?php if ( has_excerpt() || get_the_content() ) : ?>
                        <p class="notes-list__excerpt">
                            <?php echo esc_html( wp_trim_words( get_the_excerpt(), 24, ' …' ) ); ?>
                        </p>
                    <?php endif; ?>
                </article>

                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p class="hp-empty">Noch keine Notizen veröffentlicht.</p>
        <?php endif;
        wp_reset_postdata(); ?>
    </section>

    <hr class="journal-rule" aria-hidden="true">

    <!-- ==========================================
         4. THEMENFELDER (Taxonomie)
         ========================================== -->
    <?php
    $hp_topics = get_terms( array(
        'taxonomy'   => 'topic',
        'hide_empty' => false,
    ) );

    if ( $hp_topics && ! is_wp_error( $hp_topics ) ) : ?>
    <section class="topics-section" aria-label="Themenfelder">
        <header>
            <h2 class="hp-section-title">Themenfelder</h2>
        </header>
        <div class="topics-grid">
            <?php foreach ( $hp_topics as $topic ) : ?>
                <a class="hp-topic-pill" href="<?php echo esc_url( get_term_link( $topic ) ); ?>">
                    <?php echo esc_html( $topic->name ); ?>
                    <?php if ( $topic->count > 0 ) : ?>
                        <span>(<?php echo (int) $topic->count; ?>)</span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <hr class="journal-rule" aria-hidden="true">
    <?php endif; ?>

    <!-- ==========================================
         5. KOMPETENZFELDER
         ========================================== -->
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

    <!-- ==========================================
         6. KOLOPHON
         ========================================== -->
    <section class="journal-colophon" aria-label="Kolophon">
        <p class="journal-colophon__text">
            Hasimuener Journal &middot; Digitale Architektur &middot; Hannover
        </p>
    </section>

</main>

<?php get_footer(); ?>
