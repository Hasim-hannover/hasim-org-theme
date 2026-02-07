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

    <!-- Lesefortschritt -->
    <div class="reading-progress" id="js-reading-progress" aria-hidden="true">
        <div class="reading-progress__bar" id="js-reading-bar"></div>
    </div>

    <!-- Inhalt mit Sticky TOC -->
    <div class="single-body single-body--with-toc">

        <!-- TOC: auf Desktop sticky neben dem Text -->
        <aside class="hp-toc" id="js-toc" aria-label="Inhaltsverzeichnis" hidden>
            <span class="hp-toc__title">Inhalt</span>
            <ol id="js-toc-list"></ol>
        </aside>

        <div class="single-body__main">
            <div class="prose">
                <?php the_content(); ?>
            </div>

            <!-- Artikel-Fußzeile -->
            <footer class="essay-footer">
                <hr class="journal-rule" aria-hidden="true">

                <!-- Teilen -->
                <?php
                $share_url   = rawurlencode( get_permalink() );
                $share_title = rawurlencode( get_the_title() );
                $share_text  = rawurlencode( get_the_title() . ' — ' . get_bloginfo( 'name' ) );
                ?>
                <nav class="hp-share" aria-label="Beitrag teilen">
                    <span class="hp-share__label">Teilen</span>
                    <ul class="hp-share__list">
                        <li><a class="hp-share__link hp-share__link--x" href="https://x.com/intent/tweet?url=<?php echo $share_url; ?>&amp;text=<?php echo $share_title; ?>" target="_blank" rel="noopener noreferrer" aria-label="Auf X teilen">𝕏</a></li>
                        <li><a class="hp-share__link hp-share__link--li" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer" aria-label="Auf LinkedIn teilen">in</a></li>
                        <li><a class="hp-share__link hp-share__link--mail" href="mailto:?subject=<?php echo $share_title; ?>&amp;body=<?php echo $share_text; ?>%20<?php echo $share_url; ?>" aria-label="Per E-Mail teilen">✉</a></li>
                        <li><button class="hp-share__link hp-share__link--copy" data-url="<?php echo esc_url( get_permalink() ); ?>" aria-label="Link kopieren" type="button">🔗</button></li>
                    </ul>
                </nav>

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

    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
