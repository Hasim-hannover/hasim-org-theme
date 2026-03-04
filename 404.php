<?php
/**
 * Template: 404 — Seite nicht gefunden
 *
 * Redaktionelle 404-Seite mit Suchfeld und Themenübersicht.
 *
 * @package Hasimuener_Journal
 * @since   5.0.0
 */

get_header(); ?>

<main id="main-content" class="hp-404">
    <div class="hp-404__inner">

        <header class="hp-404__header">
            <span class="hp-kicker">404</span>
            <h1 class="hp-404__title">Seite nicht gefunden</h1>
            <p class="hp-404__desc">Die angeforderte Seite existiert nicht — möglicherweise wurde sie verschoben oder entfernt.</p>
        </header>

        <div class="hp-404__search">
            <p class="hp-404__search-label">Im Journal suchen:</p>
            <?php get_search_form(); ?>
        </div>

        <nav class="hp-404__nav" aria-label="Weiterführende Bereiche">
            <h2 class="hp-404__nav-title">Oder hier weiterlesen</h2>
            <ul class="hp-404__links">
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Startseite</a></li>
                <li><a href="<?php echo esc_url( get_post_type_archive_link( 'essay' ) ); ?>">Essays</a></li>
                <li><a href="<?php echo esc_url( get_post_type_archive_link( 'note' ) ); ?>">Notizen</a></li>
                <li><a href="<?php echo esc_url( get_post_type_archive_link( 'glossar' ) ); ?>">Glossar</a></li>
            </ul>
        </nav>

        <?php
        // Letzte drei Essays als Empfehlung
        $hp_recent = new WP_Query( [
            'post_type'      => 'essay',
            'posts_per_page' => 3,
            'post_status'    => 'publish',
        ] );

        if ( $hp_recent->have_posts() ) : ?>
        <section class="hp-404__recent" aria-label="Aktuelle Essays">
            <h2 class="hp-404__nav-title">Aktuelle Essays</h2>
            <ul class="hp-404__recent-list">
                <?php while ( $hp_recent->have_posts() ) : $hp_recent->the_post(); ?>
                <li class="hp-404__recent-item">
                    <a href="<?php the_permalink(); ?>">
                        <span class="hp-404__recent-title"><?php the_title(); ?></span>
                        <time class="hp-404__recent-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                            <?php echo esc_html( get_the_date( 'j. F Y' ) ); ?>
                        </time>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>
        </section>
        <?php
        wp_reset_postdata();
        endif; ?>

    </div>
</main>

<?php get_footer(); ?>
