1<?php
/**
 * Footer Template — Kolophon
 *
 * Dreispaltiges Kolophon: Herausgeber · Themenindex · Meta.
 * Ersetzt den GeneratePress-Standard-Footer.
 *
 * @package Hasimuener_Journal
 * @version 3.1.0
 */

defined( 'ABSPATH' ) || exit;

$hp_theme   = wp_get_theme();
$hp_version = $hp_theme->get( 'Version' );
$hp_year    = gmdate( 'Y' );
?>

        </div><!-- .site-content (GeneratePress) -->
    </div><!-- .inside-site-main (GeneratePress) -->

    <footer class="hp-colophon" role="contentinfo" aria-label="Kolophon">
        <div class="hp-colophon__inner">

            <!-- Spalte 1: Herausgeber -->
            <div class="hp-colophon__col hp-colophon__mission">
                <span class="hp-colophon__label">Herausgeber</span>
                <p>Herausgegeben von Hasim Üner — Medienwissenschaftler &amp;&nbsp;Digitalstratege.</p>
                <a class="hp-colophon__mission-link" href="<?php echo esc_url( home_url( '/mission/' ) ); ?>">Mehr zur Mission &rarr;</a>
            </div>

            <!-- Spalte 2: Themenindex -->
            <nav class="hp-colophon__col hp-colophon__index" aria-label="Themenindex">
                <span class="hp-colophon__label">Themenfelder</span>
                <?php
                $hp_footer_topics = get_terms( [
                    'taxonomy'   => 'topic',
                    'hide_empty' => false,
                    'orderby'    => 'name',
                    'order'      => 'ASC',
                ] );

                if ( $hp_footer_topics && ! is_wp_error( $hp_footer_topics ) ) : ?>
                    <ul class="hp-colophon__topic-list">
                        <?php foreach ( $hp_footer_topics as $ft ) : ?>
                            <li><a href="<?php echo esc_url( get_term_link( $ft ) ); ?>"><?php echo esc_html( $ft->name ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p class="hp-colophon__empty">Keine Themen angelegt.</p>
                <?php endif; ?>
            </nav>

            <!-- Spalte 3: Meta / Legal -->
            <div class="hp-colophon__col hp-colophon__meta">
                <span class="hp-colophon__label">Meta</span>
                <p>&copy; <?php echo esc_html( $hp_year ); ?> Hasimuener Journal</p>
                <ul class="hp-colophon__legal">
                    <li><a href="<?php echo esc_url( home_url( '/impressum/' ) ); ?>">Impressum</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/datenschutz/' ) ); ?>">Datenschutz</a></li>
                </ul>
                <p class="hp-colophon__version">Theme v<?php echo esc_html( $hp_version ); ?></p>
            </div>

        </div><!-- .hp-colophon__inner -->
    </footer>

</div><!-- .site (GeneratePress) -->

<?php wp_footer(); ?>
</body>
</html>
