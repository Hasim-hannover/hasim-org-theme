<?php
/**
 * Archive Template: Glossar
 *
 * Alphabetisch sortierter Index aller Glossar-Einträge
 * mit Buchstaben-Navigation (Sprungmarken A–Z).
 *
 * @package Hasimuener_Journal
 * @version 5.1.0
 */

get_header(); ?>

<header class="archive-header" role="banner">
    <span class="hp-kicker">Wissensbasis</span>
    <h1 class="archive-header__title">Glossar</h1>
    <p class="archive-header__desc">Begriffe, Konzepte, strukturelle Einordnungen — eine vernetzte Wissensbasis, die mit dem Journal wächst.</p>
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
        <?php foreach ( array_keys( $hp_grouped ) as $letter ) : ?>
            <a class="hp-glossar-nav__letter" href="#glossar-<?php echo esc_attr( $letter ); ?>"><?php echo esc_html( $letter ); ?></a>
        <?php endforeach; ?>
    </nav>

    <div class="hp-glossar-index" role="main">
        <?php foreach ( $hp_grouped as $letter => $entries ) : ?>

            <section class="hp-glossar-index__section" id="glossar-<?php echo esc_attr( $letter ); ?>">
                <h2 class="hp-glossar-index__letter"><?php echo esc_html( $letter ); ?></h2>

                <dl class="hp-glossar-index__list">
                    <?php foreach ( $entries as $entry ) : ?>
                        <dt class="hp-glossar-index__term">
                            <a href="<?php echo esc_url( $entry['url'] ); ?>"><?php echo esc_html( $entry['title'] ); ?></a>
                        </dt>
                        <?php if ( $entry['kurz'] ) : ?>
                            <dd class="hp-glossar-index__def"><?php echo esc_html( $entry['kurz'] ); ?></dd>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </dl>
            </section>

        <?php endforeach; ?>
    </div>

<?php else : ?>
    <div class="hp-glossar-index" role="main">
        <p class="hp-empty">Das Glossar wird mit der Zeit wachsen. Noch keine Einträge vorhanden.</p>
    </div>
<?php endif; ?>

<?php get_footer(); ?>
