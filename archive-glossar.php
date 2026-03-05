<?php
/**
 * Archive Template: Glossar
 *
 * Alphabetisch sortierter Index aller Glossar-Einträge
 * mit Buchstaben-Navigation. Zentriertes, großzügiges Layout.
 *
 * @package Hasimuener_Journal
 * @version 5.2.0
 */

get_header(); ?>

<?php hp_breadcrumbs(); ?>

<header class="hp-glossar-archive-header">
    <div class="hp-glossar-archive-header__inner">
        <span class="hp-kicker">Wissensbasis</span>
        <h1 class="hp-glossar-archive-header__title">Glossar</h1>
        <p class="hp-glossar-archive-header__desc">Begriffe, Konzepte, strukturelle Einordnungen — eine vernetzte Wissensbasis, die mit dem Journal wächst.</p>
    </div>
</header>

<?php
// Alle Glossar-Einträge laden (alphabetisch)
$hp_glossar_query = new WP_Query( [
    'post_type'      => 'glossar',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
] );

if ( $hp_glossar_query->have_posts() ) :

    // Nach Anfangsbuchstaben gruppieren
    $hp_grouped = [];
    while ( $hp_glossar_query->have_posts() ) {
        $hp_glossar_query->the_post();
        $letter = mb_strtoupper( mb_substr( get_the_title(), 0, 1 ) );
        $hp_grouped[ $letter ][] = [
            'id'    => get_the_ID(),
            'title' => get_the_title(),
            'url'   => get_permalink(),
            'kurz'  => get_post_meta( get_the_ID(), '_hp_glossar_kurz', true ),
        ];
    }
    wp_reset_postdata();
    ksort( $hp_grouped );
?>

    <!-- Buchstaben-Navigation -->
    <nav class="hp-glossar-nav" aria-label="Alphabetische Navigation">
        <div class="hp-glossar-nav__inner">
            <?php foreach ( array_keys( $hp_grouped ) as $letter ) : ?>
                <a class="hp-glossar-nav__letter" href="#glossar-<?php echo esc_attr( $letter ); ?>"><?php echo esc_html( $letter ); ?></a>
            <?php endforeach; ?>
        </div>
    </nav>

    <div class="hp-glossar-index" role="main">
        <?php foreach ( $hp_grouped as $letter => $entries ) : ?>

            <section class="hp-glossar-index__section" id="glossar-<?php echo esc_attr( $letter ); ?>">
                <h2 class="hp-glossar-index__letter"><?php echo esc_html( $letter ); ?></h2>

                <div class="hp-glossar-index__entries">
                    <?php foreach ( $entries as $entry ) : ?>
                        <article class="hp-glossar-card">
                            <h3 class="hp-glossar-card__title">
                                <a href="<?php echo esc_url( $entry['url'] ); ?>"><?php echo esc_html( $entry['title'] ); ?></a>
                            </h3>
                            <?php if ( $entry['kurz'] ) : ?>
                                <p class="hp-glossar-card__def"><?php echo esc_html( $entry['kurz'] ); ?></p>
                            <?php endif; ?>
                            <a class="hp-glossar-card__more" href="<?php echo esc_url( $entry['url'] ); ?>" aria-label="<?php echo esc_attr( $entry['title'] ); ?> weiterlesen">Weiterlesen &rarr;</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

        <?php endforeach; ?>
    </div>

<?php else : ?>
    <div class="hp-glossar-index hp-glossar-index--empty" role="main">
        <p class="hp-empty">Das Glossar wird mit der Zeit wachsen. Noch keine Einträge vorhanden.</p>
    </div>
<?php endif; ?>

<?php get_footer(); ?>
