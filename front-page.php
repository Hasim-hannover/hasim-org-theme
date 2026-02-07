<?php
/**
 * Template: Front Page — Hasimuener Journal
 *
 * Editoriales Layout: Hero (Latest Essay) → Notizen → Themenfelder.
 * Kein Slider, kein Carousel — statisch-redaktionell.
 *
 * @package Hasimuener_Journal
 * @version 3.0.0
 */

get_header(); ?>

<main id="journal-front" class="journal-front" role="main">

    <!-- ==========================================
         1. EDITORIAL HERO — Neuester Essay
         ========================================== -->
    <?php
    $hp_hero = new WP_Query( [
        'post_type'      => 'essay',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
    ] );

    if ( $hp_hero->have_posts() ) :
        while ( $hp_hero->have_posts() ) : $hp_hero->the_post(); ?>

    <section class="editorial-hero" aria-label="Aktueller Essay">
        <div class="editorial-hero__grid">

            <div class="editorial-hero__meta hp-overline">
                <span>Essay</span>
                <span class="hp-overline__sep" aria-hidden="true"></span>
                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                    <?php echo esc_html( get_the_date( 'j. F Y' ) ); ?>
                </time>
                <span class="hp-overline__sep" aria-hidden="true"></span>
                <span><?php echo esc_html( hp_reading_time() ); ?></span>
            </div>

            <h1 class="editorial-hero__title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h1>

            <?php if ( has_excerpt() ) : ?>
                <p class="editorial-hero__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
            <?php endif; ?>

            <a href="<?php the_permalink(); ?>" class="editorial-hero__cta" aria-label="<?php the_title_attribute(); ?> — Ganzen Essay lesen">Ganzen Essay lesen &rarr;</a>
        </div>
    </section>

        <?php endwhile;
    else : ?>

    <!-- Fallback: Kein Essay vorhanden -->
    <section class="editorial-hero editorial-hero--empty" aria-label="Aktueller Essay">
        <div class="editorial-hero__grid">
            <div class="editorial-hero__meta hp-overline"><span>Journal</span></div>
            <h1 class="editorial-hero__title">Verstehen, was sich&nbsp;verändert.</h1>
            <p class="editorial-hero__excerpt">Essays und Analysen zu Gesellschaft, Wissenschaft und den Strukturen, die unser Denken formen.</p>
        </div>
    </section>

    <?php endif;
    wp_reset_postdata(); ?>

    <hr class="journal-rule" aria-hidden="true">

    <!-- ==========================================
         2. AKTUELLE NOTIZEN
         ========================================== -->
    <section class="notes-section" aria-label="Aktuelle Notizen">
        <header>
            <h2 class="hp-section-title">Aktuelle Notizen</h2>
        </header>

        <?php
        $hp_notes = new WP_Query( [
            'post_type'      => 'note',
            'posts_per_page' => 3,
            'post_status'    => 'publish',
        ] );

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
         3. THEMENFELDER (Taxonomie)
         ========================================== -->
    <?php
    $hp_topics = get_terms( [
        'taxonomy'   => 'topic',
        'hide_empty' => false,
    ] );

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

</main>

<?php get_footer(); ?>
