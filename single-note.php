<?php
/**
 * Single Template: Notiz
 * 
 * Kürzeres Format. Kein TOC. Kompakt, fokussiert.
 *
 * @package Hasimuener_Journal
 * @version 2.0.0
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<header class="single-header" role="banner">
    <span class="hp-kicker">Notiz</span>
    <h1 class="single-header__title"><?php the_title(); ?></h1>

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
        <ul class="hp-topics" aria-label="Themenfelder" style="margin-top: 1rem;">
            <?php foreach ( $topics as $topic ) : ?>
                <li><a class="hp-topic-pill" href="<?php echo esc_url( get_term_link( $topic ) ); ?>"><?php echo esc_html( $topic->name ); ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</header>

<article class="single-body" aria-label="<?php the_title_attribute(); ?>">

    <div class="prose">
        <?php the_content(); ?>
    </div>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
