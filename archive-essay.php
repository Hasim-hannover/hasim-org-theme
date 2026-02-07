<?php
/**
 * Archive Template: Essays
 * 
 * Zeigt alle Essays als Listenansicht mit Datum, Lesezeit, Excerpt.
 *
 * @package Hasimuener_Journal
 * @version 2.0.0
 */

get_header(); ?>

<header class="archive-header" role="banner">
    <span class="hp-kicker">Archiv</span>
    <h1 class="archive-header__title">Essays</h1>
    <p class="archive-header__desc">Langform-Analysen zu Technologie, Architektur und digitaler Souveränität.</p>
</header>

<?php if ( have_posts() ) : ?>
<div class="archive-list" role="main">

    <?php while ( have_posts() ) : the_post(); ?>

        <?php get_template_part( 'template-parts/content', 'essay' ); ?>

    <?php endwhile; ?>

</div>

<?php the_posts_pagination( array(
    'mid_size'  => 1,
    'prev_text' => '&larr; Zurück',
    'next_text' => 'Weiter &rarr;',
) ); ?>

<?php else : ?>
    <div class="archive-list" role="main">
        <p class="hp-empty">Noch keine Essays veröffentlicht.</p>
    </div>
<?php endif; ?>

<?php get_footer(); ?>
