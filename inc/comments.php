<?php
/**
 * Kommentare — Hasimuener Journal
 *
 * Aktiviert Kommentare für Essays und Notizen konsistent,
 * öffnet Diskussionen standardmäßig und liefert eine
 * eigenständige, redaktionell gestaltete Kommentar-Ausgabe.
 *
 * @package Hasimuener_Journal
 * @since   6.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Öffnet Kommentare standardmäßig für neu angelegte Essays/Notizen.
 *
 * Respektiert spätere manuelle Änderungen, da nur beim ersten Insert
 * aufgerufen wird.
 *
 * @param int          $post_id     Post-ID.
 * @param WP_Post      $post        Post-Objekt.
 * @param bool         $update      Ob es ein Update ist.
 * @param WP_Post|null $post_before Vorheriger Zustand.
 */
function hp_open_comments_for_new_editorial_posts( int $post_id, WP_Post $post, bool $update, ?WP_Post $post_before ): void {
	if ( $update || wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	if ( ! in_array( $post->post_type, [ 'essay', 'note' ], true ) ) {
		return;
	}

	if ( 'open' === $post->comment_status ) {
		return;
	}

	remove_action( 'wp_after_insert_post', 'hp_open_comments_for_new_editorial_posts', 10 );
	wp_update_post( [
		'ID'             => $post_id,
		'comment_status' => 'open',
	] );
	add_action( 'wp_after_insert_post', 'hp_open_comments_for_new_editorial_posts', 10, 4 );
}
add_action( 'wp_after_insert_post', 'hp_open_comments_for_new_editorial_posts', 10, 4 );

/**
 * Einmalige Migration: Öffnet Kommentare für bestehende Essays/Notizen.
 *
 * Da Notizen bisher kein Comment-Support-Flag hatten, bleiben ältere
 * Beiträge sonst trotz Template-Einbindung stumm.
 */
function hp_migrate_editorial_comments_open(): void {
	if ( (int) get_option( 'hp_comments_bootstrap_v1', 0 ) ) {
		return;
	}

	$post_ids = get_posts( [
		'post_type'      => [ 'essay', 'note' ],
		'post_status'    => [ 'publish', 'future', 'draft', 'pending', 'private' ],
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'orderby'        => 'ID',
		'order'          => 'ASC',
	] );

	foreach ( $post_ids as $post_id ) {
		if ( 'open' === get_post_field( 'comment_status', $post_id ) ) {
			continue;
		}

		wp_update_post( [
			'ID'             => (int) $post_id,
			'comment_status' => 'open',
		] );
	}

	update_option( 'hp_comments_bootstrap_v1', 1, false );
}
add_action( 'init', 'hp_migrate_editorial_comments_open', 25 );

/**
 * Enqueue für verschachtelte Antworten.
 */
function hp_enqueue_editorial_comment_reply(): void {
	if ( is_singular( [ 'essay', 'note' ] ) && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'hp_enqueue_editorial_comment_reply' );

/**
 * Liefert die Intro-Texte für die Diskussion.
 *
 * @return array{eyebrow: string, headline: string, lede: string, empty: string}
 */
function hp_get_comment_prompt_copy(): array {
	if ( is_singular( 'note' ) ) {
		return [
			'eyebrow'  => 'Diskussion zur Notiz',
			'headline' => 'Welcher Gedanke fehlt hier noch?',
			'lede'     => 'Kurze Einwände, Ergänzungen, Gegenbeispiele oder Quellenhinweise sind ausdrücklich willkommen.',
			'empty'    => 'Noch keine Kommentare. Die erste präzise Ergänzung fehlt noch.',
		];
	}

	return [
		'eyebrow'  => 'Diskussion zum Essay',
		'headline' => 'Was widerspricht, ergänzt oder schärft diesen Text?',
		'lede'     => 'Gute Kommentare sind hier Teil des Denkens: Einwände, Nachfragen, Korrekturen und Quellen sind ausdrücklich willkommen.',
		'empty'    => 'Noch keine Kommentare. Der erste gute Einwand kann hier den Text weiterbringen.',
	];
}

/**
 * Einladende Formular-Konfiguration.
 *
 * @return array<string, mixed>
 */
function hp_get_comment_form_args(): array {
	$copy = hp_get_comment_prompt_copy();

	return [
		'class_form'           => 'comment-form hp-comment-form',
		'title_reply'          => 'Kommentar schreiben',
		'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title hp-comments__reply-title">',
		'title_reply_after'    => '</h3>',
		'title_reply_to'       => 'Antwort an %s',
		'cancel_reply_link'    => 'Antwort abbrechen',
		'label_submit'         => 'Kommentar veröffentlichen',
		'comment_notes_before' => '<div class="hp-comments__form-intro"><p class="hp-comments__form-lede">' . esc_html( $copy['lede'] ) . '</p><p class="hp-comments__form-note">Sachlicher Widerspruch ist willkommen. Kommentare werden moderiert und danach freigeschaltet.</p></div>',
		'comment_notes_after'  => '<p class="hp-comments__form-footnote">Mit dem Absenden erklärst du dich mit einer öffentlichen Veröffentlichung deines Kommentars einverstanden.</p>',
		'logged_in_as'         => '',
		'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
	];
}

/**
 * Reduziert Reibung im Formular.
 *
 * @param array<string, string> $fields Standardfelder.
 * @return array<string, string>
 */
function hp_customize_comment_form_fields( array $fields ): array {
	$commenter = wp_get_current_commenter();
	$req       = get_option( 'require_name_email' );
	$required  = $req ? ' required' : '';
	$aria_req  = $req ? " aria-required='true'" : '';

	$fields['author'] = '<p class="comment-form-author"><label for="author">Name</label><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ?? '' ) . '" size="30" maxlength="245" placeholder="Dein Name"' . $aria_req . $required . '></p>';
	$fields['email']  = '<p class="comment-form-email"><label for="email">E-Mail</label><input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ?? '' ) . '" size="30" maxlength="100" placeholder="name@beispiel.de"' . $aria_req . $required . '></p>';

	unset( $fields['url'] );

	return $fields;
}
add_filter( 'comment_form_default_fields', 'hp_customize_comment_form_fields' );

/**
 * Kommentar-Textarea mit klarer Einladung.
 *
 * @param string $field Standardfeld.
 * @return string
 */
function hp_customize_comment_form_textarea( string $field ): string {
	return '<p class="comment-form-comment"><label for="comment">Kommentar</label><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" placeholder="Frage, Widerspruch, Ergänzung oder Quellenhinweis …" required></textarea></p>';
}
add_filter( 'comment_form_field_comment', 'hp_customize_comment_form_textarea' );

/**
 * Formulartitel an den Inhaltstyp anpassen.
 *
 * @param array<string, mixed> $defaults Defaults.
 * @return array<string, mixed>
 */
function hp_customize_comment_form_defaults( array $defaults ): array {
	return array_merge( $defaults, hp_get_comment_form_args() );
}
add_filter( 'comment_form_defaults', 'hp_customize_comment_form_defaults' );

/**
 * Rendert einen einzelnen Kommentar.
 *
 * @param WP_Comment $comment Kommentarobjekt.
 * @param array      $args    Walker-Argumente.
 * @param int        $depth   Tiefe.
 */
function hp_render_journal_comment( WP_Comment $comment, array $args, int $depth ): void {
	$GLOBALS['comment'] = $comment;
	?>
	<li <?php comment_class( 'hp-comment' ); ?> id="comment-<?php comment_ID(); ?>">
		<article class="hp-comment__card" id="div-comment-<?php comment_ID(); ?>">
			<div class="hp-comment__avatar">
				<?php echo get_avatar( $comment, 56, '', '', [ 'class' => 'hp-comment__avatar-img' ] ); ?>
			</div>

			<div class="hp-comment__body">
				<header class="hp-comment__header">
					<div class="hp-comment__identity">
						<h4 class="hp-comment__author"><?php comment_author_link(); ?></h4>
						<p class="hp-comment__meta">
							<a href="<?php echo esc_url( get_comment_link( $comment ) ); ?>">
								<?php echo esc_html( get_comment_date( 'j. F Y', $comment ) ); ?>
								<span aria-hidden="true">·</span>
								<?php echo esc_html( get_comment_time( 'H:i', false, false, $comment ) ); ?>
							</a>
						</p>
					</div>
					<?php edit_comment_link( 'Bearbeiten', '<span class="hp-comment__edit">', '</span>' ); ?>
				</header>

				<?php if ( '0' === $comment->comment_approved ) : ?>
					<p class="hp-comment__awaiting">Dein Kommentar wartet auf Moderation.</p>
				<?php endif; ?>

				<div class="hp-comment__content">
					<?php comment_text(); ?>
				</div>

				<div class="hp-comment__actions">
					<?php
					comment_reply_link( array_merge( $args, [
						'depth'      => $depth,
						'max_depth'  => $args['max_depth'],
						'reply_text' => 'Antworten',
						'before'     => '<span class="hp-comment__reply">',
						'after'      => '</span>',
					] ) );
					?>
				</div>
			</div>
		</article>
	<?php
}
