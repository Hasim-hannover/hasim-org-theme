<?php
/**
 * Archive Template: Notizen
 * 
 * Zeigt alle Notizen als kompakte Listenansicht.
 *
 * @package Hasimuener_Journal
 * @version 2.0.0
 */

get_header(); ?>

<header class="archive-header" role="banner">
    <span class="hp-kicker">Archiv</span>
    <h1 class="archive-header__title">Notizen</h1>
    <p class="archive-header__desc">Kürzere Beobachtungen, Quellen und Fragmente aus der laufenden Arbeit.</p>
</header>

<?php if ( have_posts() ) : ?>
<div class="archive-list" role="main">

    <?php while ( have_posts() ) : the_post(); ?>

        <?php get_template_part( 'template-parts/content', 'note' ); ?>

    <?php endwhile; ?>

</div>

<?php the_posts_pagination( array(
    'mid_size'  => 1,
    'prev_text' => '&larr; Zurück',
    'next_text' => 'Weiter &rarr;',
) ); ?>

<?php else : ?>
    <div class="archive-list" role="main">
        <p class="hp-empty">Noch keine Notizen veröffentlicht.</p>
    </div>
<?php endif; ?>

<?php get_footer(); ?>
