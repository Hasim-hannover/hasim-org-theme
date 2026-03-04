<?php
/**
 * Single Template: Glossar-Eintrag
 *
 * Zeigt einen Begriff mit Kurzdefinition, ausführlichem Kontext
 * und Rückverlinkungen zu Essays/Notizen, die den Begriff verwenden.
 *
 * Design: Zentriert, großzügig, redaktionell — konsistent mit
 * single-essay.php und single-note.php.
 *
 * @package Hasimuener_Journal
 * @version 5.2.0
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<main id="main-content">

<!-- Header: Zentriert, großzügig, editoriales Auftreten -->
<header class="hp-glossar-hero">
    <div class="hp-glossar-hero__inner">
        <span class="hp-kicker">Glossar</span>
        <h1 class="hp-glossar-hero__title"><?php the_title(); ?></h1>

        <?php
        $hp_kurz = get_post_meta( get_the_ID(), '_hp_glossar_kurz', true );
        if ( $hp_kurz ) : ?>
            <p class="hp-glossar-hero__kurz"><?php echo esc_html( $hp_kurz ); ?></p>
        <?php endif; ?>

        <?php
        $hp_synonyme = get_post_meta( get_the_ID(), '_hp_glossar_synonyme', true );
        if ( $hp_synonyme ) :
            $hp_syn_list = array_map( 'trim', explode( ',', $hp_synonyme ) );
        ?>
            <div class="hp-glossar-hero__synonyme">
                <?php foreach ( $hp_syn_list as $syn ) : ?>
                    <span class="hp-glossar-syn-pill"><?php echo esc_html( $syn ); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php
        $topics = get_the_terms( get_the_ID(), 'topic' );
        if ( $topics && ! is_wp_error( $topics ) ) : ?>
            <ul class="hp-topics hp-glossar-hero__topics" aria-label="Themenfelder">
                <?php foreach ( $topics as $topic ) : ?>
                    <li><a class="hp-topic-pill" href="<?php echo esc_url( get_term_link( $topic ) ); ?>"><?php echo esc_html( $topic->name ); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</header>

<!-- Body: Prose mit Lesebreite -->
<article class="hp-glossar-body" aria-label="<?php the_title_attribute(); ?>">
    <div class="hp-glossar-body__content prose">
        <?php the_content(); ?>
    </div>

    <?php
    // Rückverlinkungen: Essays & Notizen, die diesen Begriff enthalten
    $hp_title   = get_the_title();
    $hp_related = new WP_Query( [
        'post_type'      => [ 'essay', 'note' ],
        'post_status'    => 'publish',
        'posts_per_page' => 10,
        's'              => $hp_title,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ] );

    if ( $hp_related->have_posts() ) : ?>
        <aside class="hp-glossar-related" aria-label="Beiträge zu diesem Begriff">
            <h2 class="hp-glossar-related__heading">Diesen Begriff verwenden</h2>
            <ul class="hp-glossar-related__list">
                <?php while ( $hp_related->have_posts() ) : $hp_related->the_post(); ?>
                    <li class="hp-glossar-related__item">
                        <span class="hp-glossar-related__type"><?php echo 'essay' === get_post_type() ? 'Essay' : 'Notiz'; ?></span>
                        <a class="hp-glossar-related__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </aside>
    <?php
        wp_reset_postdata();
    endif;
    ?>

    <nav class="hp-glossar-backnav" aria-label="Glossar-Navigation">
        <a href="<?php echo esc_url( get_post_type_archive_link( 'glossar' ) ); ?>">
            <span aria-hidden="true">&larr;</span> Alle Begriffe im Glossar
        </a>
    </nav>

</article>
</main>

<?php endwhile; ?>

<?php get_footer(); ?>
