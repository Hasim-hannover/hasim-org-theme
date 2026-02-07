<?php
/**
 * Single Template: Essay
 * 
 * Longform-Artikel mit Beitragsbild-Hero, Inhaltsverzeichnis, Lesedauer, Topic-Pills.
 * TOC wird via JS aus H2/H3-Elementen generiert.
 *
 * @package Hasimuener_Journal
 * @version 2.2.0
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<article class="essay-article" aria-label="<?php the_title_attribute(); ?>">

    <?php if ( has_post_thumbnail() ) : ?>

        <!-- HERO: Full-bleed Beitragsbild mit Overlay-Text -->
        <header class="essay-hero" role="banner">
            <div class="essay-hero__image-wrap">
                <?php the_post_thumbnail( 'full', array(
                    'class'   => 'essay-hero__img',
                    'loading' => 'eager',
                ) ); ?>
                <div class="essay-hero__overlay" aria-hidden="true"></div>
            </div>

            <div class="essay-hero__content">
                <span class="hp-kicker hp-kicker--light">Essay</span>
                <h1 class="essay-hero__title"><?php the_title(); ?></h1>

                <?php if ( has_excerpt() ) : ?>
                    <p class="essay-hero__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
                <?php endif; ?>
            </div>
        </header>

        <!-- Meta-Leiste unter dem Hero -->
        <div class="essay-meta-bar">
            <div class="essay-meta-bar__inner">
                <div class="hp-meta">
                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                        <?php echo esc_html( get_the_date( 'j. F Y' ) ); ?>
                    </time>
                    <span class="hp-meta__separator"></span>
                    <span class="hp-reading-time"><?php echo esc_html( hp_reading_time() ); ?></span>
                </div>

                <?php
                $topics = get_the_terms( get_the_ID(), 'topic' );
                if ( $topics && ! is_wp_error( $topics ) ) : ?>
                    <ul class="hp-topics" aria-label="Themenfelder">
                        <?php foreach ( $topics as $topic ) : ?>
                            <li><a class="hp-topic-pill" href="<?php echo esc_url( get_term_link( $topic ) ); ?>"><?php echo esc_html( $topic->name ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <?php
            $caption = get_the_post_thumbnail_caption();
            if ( $caption ) : ?>
                <p class="essay-meta-bar__caption"><?php echo esc_html( $caption ); ?></p>
            <?php endif; ?>
        </div>

    <?php else : ?>

        <!-- FALLBACK: Kein Beitragsbild → klassischer Header -->
        <header class="single-header" role="banner">
            <span class="hp-kicker">Essay</span>
            <h1 class="single-header__title"><?php the_title(); ?></h1>

            <?php if ( has_excerpt() ) : ?>
                <p class="single-header__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
            <?php endif; ?>

            <div class="hp-meta">
                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                    <?php echo esc_html( get_the_date( 'j. F Y' ) ); ?>
                </time>
                <span class="hp-meta__separator"></span>
                <span class="hp-reading-time"><?php echo esc_html( hp_reading_time() ); ?></span>
            </div>

            <?php
            $topics = get_the_terms( get_the_ID(), 'topic' );
            if ( $topics && ! is_wp_error( $topics ) ) : ?>
                <ul class="hp-topics" aria-label="Themenfelder">
                    <?php foreach ( $topics as $topic ) : ?>
                        <li><a class="hp-topic-pill" href="<?php echo esc_url( get_term_link( $topic ) ); ?>"><?php echo esc_html( $topic->name ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </header>

    <?php endif; ?>

    <!-- Inhalt -->
    <div class="single-body">

        <!-- TOC wird via JS befüllt -->
        <nav class="hp-toc" id="js-toc" aria-label="Inhaltsverzeichnis" hidden>
            <span class="hp-toc__title">Inhalt</span>
            <ol id="js-toc-list"></ol>
        </nav>

        <div class="prose">
            <?php the_content(); ?>
        </div>

        <!-- Artikel-Fußzeile -->
        <footer class="essay-footer">
            <hr class="journal-rule" aria-hidden="true">
            <div class="hp-meta">
                <span>Veröffentlicht am <?php echo esc_html( get_the_date( 'j. F Y' ) ); ?></span>
                <span class="hp-meta__separator"></span>
                <span><?php echo esc_html( hp_reading_time() ); ?></span>
            </div>
            <?php
            $topics_footer = get_the_terms( get_the_ID(), 'topic' );
            if ( $topics_footer && ! is_wp_error( $topics_footer ) ) : ?>
                <ul class="hp-topics" aria-label="Themenfelder">
                    <?php foreach ( $topics_footer as $topic ) : ?>
                        <li><a class="hp-topic-pill" href="<?php echo esc_url( get_term_link( $topic ) ); ?>"><?php echo esc_html( $topic->name ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </footer>

    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
