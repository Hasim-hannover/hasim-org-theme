<?php
/**
 * Template Part: Glossar-Listenelement
 *
 * Kompakte Darstellung eines Glossar-Eintrags für Listenansichten.
 *
 * @package Hasimuener_Journal
 */
?>

<article class="archive-item hp-glossar-item" id="post-<?php the_ID(); ?>">

    <h2 class="archive-item__title">
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h2>

    <?php
    $hp_kurz = get_post_meta( get_the_ID(), '_hp_glossar_kurz', true );
    if ( $hp_kurz ) : ?>
        <p class="hp-glossar-item__kurz"><?php echo esc_html( $hp_kurz ); ?></p>
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

</article>
