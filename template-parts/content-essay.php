<?php
/**
 * Template Part: Essay-Listenelement
 * 
 * Verwendet in archive-essay.php und überall wo Essays gelistet werden.
 *
 * @package Hasimuener_Journal
 */
?>

<article class="archive-item" id="post-<?php the_ID(); ?>">
    <div class="hp-meta">
        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
            <?php echo esc_html( get_the_date( 'j. F Y' ) ); ?>
        </time>
        <span class="hp-meta__separator"></span>
        <span class="hp-reading-time"><?php echo esc_html( hp_reading_time() ); ?></span>
    </div>

    <h2 class="archive-item__title">
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h2>

    <?php
    $topics = get_the_terms( get_the_ID(), 'topic' );
    if ( $topics && ! is_wp_error( $topics ) ) : ?>
        <ul class="hp-topics" aria-label="Themenfelder">
            <?php foreach ( $topics as $topic ) : ?>
                <li><a class="hp-topic-pill" href="<?php echo esc_url( get_term_link( $topic ) ); ?>"><?php echo esc_html( $topic->name ); ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ( has_excerpt() || get_the_content() ) : ?>
        <p class="archive-item__excerpt">
            <?php echo esc_html( wp_trim_words( get_the_excerpt(), 32, ' …' ) ); ?>
        </p>
    <?php endif; ?>
</article>
