<?php
/**
 * Template: Suchergebnisse
 *
 * Zeigt Suchergebnisse für Essays, Notizen und Glossar-Einträge.
 *
 * @package Hasimuener_Journal
 * @since   5.0.0
 */

get_header(); ?>

<main id="main-content" class="hp-search">
    <div class="hp-search__inner">

        <header class="hp-search__header">
            <span class="hp-kicker">Suche</span>
            <h1 class="hp-search__title">
                <?php
                printf(
                    'Ergebnisse für „%s"',
                    '<span class="hp-search__query">' . esc_html( get_search_query() ) . '</span>'
                );
                ?>
            </h1>
            <p class="hp-search__count">
                <?php
                global $wp_query;
                printf(
                    '%d %s gefunden',
                    (int) $wp_query->found_posts,
                    $wp_query->found_posts === 1 ? 'Ergebnis' : 'Ergebnisse'
                );
                ?>
            </p>
        </header>

        <div class="hp-search__form-wrap">
            <?php get_search_form(); ?>
        </div>

        <?php if ( have_posts() ) : ?>
        <div class="hp-search__results">

            <?php while ( have_posts() ) : the_post(); ?>
	            <article class="hp-search__item">
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
	                        case 'page':
	                            $hp_type_label = 'Seite';
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
                </div>

                <h2 class="hp-search__item-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>

                <?php if ( has_excerpt() || get_the_content() ) : ?>
                    <p class="hp-search__excerpt">
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
        <div class="hp-search__empty">
            <p>Keine Ergebnisse für diese Suche. Versuche es mit anderen Begriffen oder stöbere in den Bereichen:</p>
            <ul class="hp-404__links">
                <li><a href="<?php echo esc_url( get_post_type_archive_link( 'essay' ) ); ?>">Essays</a></li>
                <li><a href="<?php echo esc_url( get_post_type_archive_link( 'note' ) ); ?>">Notizen</a></li>
                <li><a href="<?php echo esc_url( get_post_type_archive_link( 'glossar' ) ); ?>">Glossar</a></li>
            </ul>
        </div>
        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>
