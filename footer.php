<?php
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

$hp_year    = gmdate( 'Y' );
?>

        </div><!-- .site-content (GeneratePress) -->
    </div><!-- .inside-site-main (GeneratePress) -->

    <footer class="hp-colophon" role="contentinfo" aria-label="Kolophon">
        <div class="hp-colophon__inner">

            <!-- Spalte 1: Herausgeber -->
            <div class="hp-colophon__col hp-colophon__mission">
                <span class="hp-colophon__label">Herausgeber</span>
                <p>Haşim Üner — Medienwissenschaftler. Publizist.</p>
                <div class="hp-colophon__links">
                    <a class="hp-colophon__mission-link" href="<?php echo esc_url( home_url( '/mission/' ) ); ?>">Mission lesen &rarr;</a>
                </div>
                <a class="hp-colophon__social" href="https://x.com/_0239983326111" target="_blank" rel="noopener noreferrer" aria-label="Haşim Üner auf X">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    <span>Auf X folgen</span>
                </a>
            </div>

            <!-- Spalte 2: Themenindex -->
            <nav class="hp-colophon__col hp-colophon__index" aria-label="Themenindex">
                <span class="hp-colophon__label">Themenfelder</span>
                <?php
                $hp_footer_topics = hp_get_curated_topics();

                if ( $hp_footer_topics ) : ?>
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
                <ul class="hp-colophon__legal">
                    <li><a href="<?php echo esc_url( get_post_type_archive_link( 'glossar' ) ); ?>">Glossar</a></li>
                    <li><a href="<?php echo esc_url( hp_get_contact_page_url() ); ?>">Anfragen</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/impressum/' ) ); ?>">Impressum</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/datenschutz/' ) ); ?>">Datenschutz</a></li>
                </ul>
            </div>

        </div><!-- .hp-colophon__inner -->
        <div class="hp-colophon__transparency">
            <p>Dieses Journal ist ein eigenständiger publizistischer Ort. Meine berufliche Arbeit im Bereich digitaler Strategie und Webentwicklung findet getrennt davon auf <a href="https://hasimuener.de/">hasimuener.de</a> statt.</p>
        </div>
        <p class="hp-colophon__closing">Zwischen Sprachen und Perspektiven beginnt Verständigung.</p>
    </footer>

</div><!-- .site (GeneratePress) -->

<?php wp_footer(); ?>
</body>
</html>
