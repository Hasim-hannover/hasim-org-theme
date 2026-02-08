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
                        <li><a class="hp-share__link hp-share__link--x" href="https://x.com/intent/tweet?url=<?php echo $share_url; ?>&amp;text=<?php echo $share_title; ?>" target="_blank" rel="noopener noreferrer" aria-label="Auf X teilen"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a></li>
                        <li><a class="hp-share__link hp-share__link--li" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer" aria-label="Auf LinkedIn teilen"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a></li>
                        <li><a class="hp-share__link hp-share__link--mail" href="mailto:?subject=<?php echo $share_title; ?>&amp;body=<?php echo $share_text; ?>%20<?php echo $share_url; ?>" aria-label="Per E-Mail teilen"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></a></li>
                        <li><button class="hp-share__link hp-share__link--copy" data-url="<?php echo esc_url( get_permalink() ); ?>" aria-label="Link kopieren" type="button"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></button></li>
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
