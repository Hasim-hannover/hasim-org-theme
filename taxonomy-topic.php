<?php
/**
 * Taxonomy Template: Themenfeld (topic)
 *
 * Zeigt alle Beiträge eines Themenfelds — Essays und Notizen
 * gemischt, chronologisch sortiert.
 *
 * @package Hasimuener_Journal
 * @since   5.0.0
 */

get_header();

$hp_term = get_queried_object();
?>

<main id="main-content" class="hp-topic-archive">
    <div class="hp-topic-archive__inner">

        <header class="hp-topic-archive__header">
            <span class="hp-kicker">Themenfeld</span>
            <h1 class="hp-topic-archive__title"><?php single_term_title(); ?></h1>
            <?php if ( $hp_term && $hp_term->description ) : ?>
                <p class="hp-topic-archive__desc"><?php echo esc_html( $hp_term->description ); ?></p>
            <?php endif; ?>
        </header>

        <?php if ( have_posts() ) : ?>
        <div class="hp-topic-archive__list">

            <?php while ( have_posts() ) : the_post(); ?>
	            <article class="archive-item" id="post-<?php the_ID(); ?>">
	                <div class="hp-meta">
	                    <?php
	                    switch ( get_post_type() ) {
	                        case 'essay':
	                            $hp_type_label = 'Essay';
	                            break;
	                        case 'note':
	                            $hp_type_label = 'Notiz';
	                            break;
	                        case 'glossar':
	                            $hp_type_label = 'Glossar';
	                            break;
	                        default:
	                            $hp_type_label = 'Beitrag';
	                            break;
	                    }
	                    ?>
	                    <span class="hp-search__type"><?php echo esc_html( $hp_type_label ); ?></span>
                    <span class="hp-meta__separator"></span>
                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                        <?php echo esc_html( get_the_date( 'j. F Y' ) ); ?>
                    </time>
                    <span class="hp-meta__separator"></span>
                    <span class="hp-reading-time"><?php echo esc_html( hp_reading_time() ); ?></span>
                </div>

                <h2 class="archive-item__title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>

                <?php if ( has_excerpt() || get_the_content() ) : ?>
                    <p class="archive-item__excerpt">
                        <?php echo esc_html( wp_trim_words( get_the_excerpt(), 28, ' …' ) ); ?>
                    </p>
                <?php endif; ?>
            </article>
            <?php endwhile; ?>

        </div>

        <?php the_posts_pagination( [
            'mid_size'  => 1,
            'prev_text' => '&larr; Zurück',
            'next_text' => 'Weiter &rarr;',
        ] ); ?>

        <?php else : ?>
        <div class="hp-topic-archive__empty">
            <p class="hp-empty">Noch keine Beiträge in diesem Themenfeld.</p>
        </div>
        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>
