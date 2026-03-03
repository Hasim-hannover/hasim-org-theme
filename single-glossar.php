<?php
/**
 * Single Template: Glossar-Eintrag
 *
 * Zeigt einen Begriff mit Kurzdefinition, ausführlichem Kontext
 * und Rückverlinkungen zu Essays/Notizen, die den Begriff verwenden.
 *
 * @package Hasimuener_Journal
 * @version 5.1.0
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<article class="hp-glossar-entry" aria-label="Glossar: <?php the_title_attribute(); ?>">

    <header class="hp-glossar-entry__header">
        <span class="hp-kicker">Glossar</span>
        <h1 class="hp-glossar-entry__title"><?php the_title(); ?></h1>

        <?php
        $hp_kurz = get_post_meta( get_the_ID(), '_hp_glossar_kurz', true );
        if ( $hp_kurz ) : ?>
            <p class="hp-glossar-entry__kurz"><?php echo esc_html( $hp_kurz ); ?></p>
        <?php endif; ?>

        <?php
        $hp_synonyme = get_post_meta( get_the_ID(), '_hp_glossar_synonyme', true );
        if ( $hp_synonyme ) : ?>
            <p class="hp-glossar-entry__synonyme">
                <span class="hp-glossar-entry__synonyme-label">Auch:</span>
                <?php echo esc_html( $hp_synonyme ); ?>
            </p>
        <?php endif; ?>
    </header>

    <div class="hp-glossar-entry__body prose">
        <?php the_content(); ?>
    </div>

    <?php
    // Themenfelder
    $topics = get_the_terms( get_the_ID(), 'topic' );
    if ( $topics && ! is_wp_error( $topics ) ) : ?>
        <footer class="hp-glossar-entry__footer">
            <ul class="hp-topics" aria-label="Themenfelder">
                <?php foreach ( $topics as $topic ) : ?>
                    <li><a class="hp-topic-pill" href="<?php echo esc_url( get_term_link( $topic ) ); ?>"><?php echo esc_html( $topic->name ); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </footer>
    <?php endif; ?>

    <?php
    // Rückverlinkungen: Essays & Notizen, die diesen Begriff enthalten
    $hp_title     = get_the_title();
    $hp_related   = new WP_Query( [
        'post_type'      => [ 'essay', 'note' ],
        'post_status'    => 'publish',
        'posts_per_page' => 10,
        's'              => $hp_title,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ] );

    if ( $hp_related->have_posts() ) : ?>
        <aside class="hp-glossar-entry__related" aria-label="Beiträge zu diesem Begriff">
            <h2 class="hp-glossar-entry__related-heading">Beiträge, die diesen Begriff verwenden</h2>
            <ul class="hp-glossar-entry__related-list">
                <?php while ( $hp_related->have_posts() ) : $hp_related->the_post(); ?>
                    <li>
                        <a href="<?php the_permalink(); ?>">
                            <span class="hp-glossar-entry__related-type"><?php echo 'essay' === get_post_type() ? 'Essay' : 'Notiz'; ?></span>
                            <?php the_title(); ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </aside>
    <?php
        wp_reset_postdata();
    endif;
    ?>

    <nav class="hp-glossar-entry__nav" aria-label="Glossar-Navigation">
        <a href="<?php echo esc_url( get_post_type_archive_link( 'glossar' ) ); ?>">&larr; Alle Begriffe</a>
    </nav>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
