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
 * Prüft, ob sich der aktuelle Kontext auf Essay-/Notiz-Kommentare bezieht.
 *
 * @param int|null $post_id Optionale Post-ID.
 * @return bool
 */
function hp_is_editorial_comment_target( ?int $post_id = null ): bool {
	if ( $post_id ) {
		return in_array( get_post_type( $post_id ), [ 'essay', 'note' ], true );
	}

	if ( is_singular( [ 'essay', 'note' ] ) ) {
		return true;
	}

	if ( isset( $_POST['comment_post_ID'] ) ) {
		$comment_post_id = (int) wp_unslash( $_POST['comment_post_ID'] );

		if ( $comment_post_id > 0 ) {
			return in_array( get_post_type( $comment_post_id ), [ 'essay', 'note' ], true );
		}
	}

	return false;
}

/**
 * Liefert die Anti-Spam-Konfiguration für redaktionelle Kommentare.
 *
 * @return array{min_seconds:int,max_links:int,rate_window:int,max_age:int}
 */
function hp_get_editorial_comment_antispam_settings(): array {
	return [
		'min_seconds' => 4,
		'max_links'   => 2,
		'rate_window' => 45,
		'max_age'     => DAY_IN_SECONDS,
	];
}

/**
 * Erzeugt den Prüf-Token für das Kommentarformular.
 *
 * @param int $post_id      Post-ID.
 * @param int $rendered_at  Render-Zeitpunkt.
 * @return string
 */
function hp_get_editorial_comment_render_token( int $post_id, int $rendered_at ): string {
	return wp_hash( $post_id . '|' . $rendered_at . '|hp-editorial-comment' );
}

/**
 * Bricht einen Kommentar mit verständlicher Meldung ab.
 *
 * @param string $message Fehlermeldung.
 */
function hp_reject_editorial_comment( string $message ): void {
	wp_die(
		esc_html( $message ),
		esc_html__( 'Kommentar nicht gesendet', 'hasimuener-journal' ),
		[
			'response'  => 400,
			'back_link' => true,
		]
	);
}

/**
 * Liefert einen anonymisierten Schlüssel für Rate-Limits.
 *
 * @param array<string, mixed> $commentdata Kommentar-Daten.
 * @return string
 */
function hp_get_editorial_comment_rate_key( array $commentdata = [] ): string {
	if ( is_user_logged_in() ) {
		return 'user_' . get_current_user_id();
	}

	$ip = '';

	if ( ! empty( $commentdata['comment_author_IP'] ) ) {
		$ip = (string) $commentdata['comment_author_IP'];
	} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = (string) wp_unslash( $_SERVER['REMOTE_ADDR'] );
	}

	if ( '' === $ip ) {
		return 'guest_' . md5( wp_get_session_token() ?: 'anonymous' );
	}

	return 'guest_' . md5( $ip );
}

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
 * Gibt versteckte Anti-Spam-Felder im Formular aus.
 */
function hp_render_editorial_comment_antispam_fields(): void {
	if ( ! hp_is_editorial_comment_target() ) {
		return;
	}

	$post_id = get_the_ID() ?: get_queried_object_id();

	if ( ! $post_id ) {
		return;
	}

	$rendered_at = time();
	$token       = hp_get_editorial_comment_render_token( (int) $post_id, $rendered_at );
	?>
	<div class="hp-comment-antispam" aria-hidden="true">
		<label for="hp-comment-website">Website</label>
		<input id="hp-comment-website" type="text" name="hp_comment_website" value="" tabindex="-1" autocomplete="off">
	</div>
	<input type="hidden" name="hp_comment_rendered_at" value="<?php echo esc_attr( (string) $rendered_at ); ?>">
	<input type="hidden" name="hp_comment_render_token" value="<?php echo esc_attr( $token ); ?>">
	<?php
}
add_action( 'comment_form_after_fields', 'hp_render_editorial_comment_antispam_fields' );
add_action( 'comment_form_logged_in_after', 'hp_render_editorial_comment_antispam_fields' );

/**
 * Prüft stillen Anti-Spam-Schutz für Essays/Notizen.
 *
 * @param array<string, mixed> $commentdata Kommentar-Daten.
 * @return array<string, mixed>
 */
function hp_validate_editorial_comment_antispam( array $commentdata ): array {
	$post_id = isset( $commentdata['comment_post_ID'] ) ? (int) $commentdata['comment_post_ID'] : 0;

	if ( ! hp_is_editorial_comment_target( $post_id ) ) {
		return $commentdata;
	}

	$settings = hp_get_editorial_comment_antispam_settings();
	$honeypot = isset( $_POST['hp_comment_website'] ) ? trim( (string) wp_unslash( $_POST['hp_comment_website'] ) ) : '';

	if ( '' !== $honeypot ) {
		hp_reject_editorial_comment( 'Der Kommentar konnte nicht gespeichert werden. Bitte versuche es erneut.' );
	}

	$rendered_at = isset( $_POST['hp_comment_rendered_at'] ) ? (int) wp_unslash( $_POST['hp_comment_rendered_at'] ) : 0;
	$token       = isset( $_POST['hp_comment_render_token'] ) ? (string) wp_unslash( $_POST['hp_comment_render_token'] ) : '';

	if ( $rendered_at <= 0 || '' === $token ) {
		hp_reject_editorial_comment( 'Das Kommentarformular ist abgelaufen. Bitte lade die Seite neu und versuche es erneut.' );
	}

	$expected = hp_get_editorial_comment_render_token( $post_id, $rendered_at );

	if ( ! hash_equals( $expected, $token ) ) {
		hp_reject_editorial_comment( 'Das Kommentarformular ist nicht mehr gültig. Bitte lade die Seite neu.' );
	}

	$elapsed = time() - $rendered_at;

	if ( $elapsed < $settings['min_seconds'] ) {
		hp_reject_editorial_comment( 'Bitte nimm dir einen Moment Zeit und sende den Kommentar dann erneut.' );
	}

	if ( $elapsed > $settings['max_age'] ) {
		hp_reject_editorial_comment( 'Das Kommentarformular ist abgelaufen. Bitte lade die Seite neu und versuche es erneut.' );
	}

	$content    = isset( $commentdata['comment_content'] ) ? (string) $commentdata['comment_content'] : '';
	$link_count = preg_match_all( '/(?:https?:\/\/|www\.|<a\s)/iu', $content );

	if ( false !== $link_count && $link_count > $settings['max_links'] ) {
		hp_reject_editorial_comment( 'Bitte reduziere die Zahl der Links im Kommentar.' );
	}

	$rate_key        = 'hp_comment_rate_' . hp_get_editorial_comment_rate_key( $commentdata );
	$last_comment_at = (int) get_transient( $rate_key );

	if ( $last_comment_at > 0 && ( time() - $last_comment_at ) < $settings['rate_window'] ) {
		hp_reject_editorial_comment( 'Bitte warte einen kurzen Moment, bevor du den nächsten Kommentar sendest.' );
	}

	return $commentdata;
}
add_filter( 'preprocess_comment', 'hp_validate_editorial_comment_antispam', 5 );

/**
 * Aktiviert nach erfolgreichem Speichern das kurze Rate-Limit.
 *
 * @param int                $comment_id       Kommentar-ID.
 * @param int|string         $comment_approved Freigabestatus.
 * @param array<string,mixed> $commentdata     Kommentar-Daten.
 */
function hp_mark_editorial_comment_rate_limit( int $comment_id, $comment_approved, array $commentdata ): void {
	unset( $comment_id, $comment_approved );

	$post_id = isset( $commentdata['comment_post_ID'] ) ? (int) $commentdata['comment_post_ID'] : 0;

	if ( ! hp_is_editorial_comment_target( $post_id ) ) {
		return;
	}

	$settings = hp_get_editorial_comment_antispam_settings();
	$rate_key = 'hp_comment_rate_' . hp_get_editorial_comment_rate_key( $commentdata );

	set_transient( $rate_key, time(), $settings['rate_window'] );
}
add_action( 'comment_post', 'hp_mark_editorial_comment_rate_limit', 10, 3 );

/**
 * Deaktiviert für Essays/Notizen die WordPress-Standardpflicht für E-Mail.
 *
 * Name/Pseudonym bleibt über eigene Validierung verpflichtend.
 *
 * @param mixed $pre_option Vorbelegter Optionswert.
 * @return mixed
 */
function hp_relax_editorial_comment_email_requirement( $pre_option ) {
	if ( hp_is_editorial_comment_target() ) {
		return 0;
	}

	return $pre_option;
}
add_filter( 'pre_option_require_name_email', 'hp_relax_editorial_comment_email_requirement' );

/**
 * Erzwingt bei Essays/Notizen wenigstens einen öffentlichen Namen/Pseudonym.
 *
 * @param array<string, mixed> $commentdata Kommentar-Daten.
 * @return array<string, mixed>
 */
function hp_require_editorial_comment_author( array $commentdata ): array {
	$post_id = isset( $commentdata['comment_post_ID'] ) ? (int) $commentdata['comment_post_ID'] : 0;

	if ( ! hp_is_editorial_comment_target( $post_id ) ) {
		return $commentdata;
	}

	$author = isset( $commentdata['comment_author'] ) ? trim( (string) $commentdata['comment_author'] ) : '';

	if ( '' === $author && is_user_logged_in() ) {
		$user   = wp_get_current_user();
		$author = trim( (string) ( $user->display_name ?: $user->user_login ) );
		$commentdata['comment_author'] = $author;
	}

	$commentdata['comment_author_email'] = '';
	$commentdata['comment_author_url']   = '';

	if ( '' === $author ) {
		wp_die(
			esc_html__( 'Bitte gib einen Namen oder ein Pseudonym an.', 'hasimuener-journal' ),
			esc_html__( 'Kommentar unvollständig', 'hasimuener-journal' ),
			[ 'response' => 400 ]
		);
	}

	return $commentdata;
}
add_filter( 'preprocess_comment', 'hp_require_editorial_comment_author' );

/**
 * Hält redaktionelle Kommentare standardmäßig zur Moderation zurück.
 *
 * @param int|string        $approved    Freigabestatus.
 * @param array<string, mixed> $commentdata Kommentar-Daten.
 * @return int|string
 */
function hp_moderate_editorial_comments( $approved, array $commentdata ) {
	$post_id = isset( $commentdata['comment_post_ID'] ) ? (int) $commentdata['comment_post_ID'] : 0;

	if ( hp_is_editorial_comment_target( $post_id ) && ! current_user_can( 'moderate_comments' ) ) {
		return 0;
	}

	return $approved;
}
add_filter( 'pre_comment_approved', 'hp_moderate_editorial_comments', 10, 2 );

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
 * Liefert eine knappe Datenschutz-Erklärung für das Formular.
 *
 * @return string
 */
function hp_get_comment_privacy_notice(): string {
	$privacy_url = get_privacy_policy_url();
	$privacy_cta = $privacy_url
		? ' Mehr in der <a href="' . esc_url( $privacy_url ) . '">Datenschutzerklärung</a>.'
		: '';

	return 'Für Kommentare erfassen wir hier nur den öffentlichen Namen oder dein Pseudonym und den Kommentartext.' . $privacy_cta;
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
		'comment_notes_before' => '<div class="hp-comments__form-intro"><p class="hp-comments__form-lede">' . esc_html( $copy['lede'] ) . '</p><p class="hp-comments__form-note">Sachlicher Widerspruch ist willkommen. Kommentare werden moderiert und danach freigeschaltet.</p><p class="hp-comments__form-privacy">' . wp_kses_post( hp_get_comment_privacy_notice() ) . '</p></div>',
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
	if ( ! hp_is_editorial_comment_target() ) {
		return $fields;
	}

	$commenter = wp_get_current_commenter();

	$fields['author']  = '<p class="comment-form-author"><label for="author">Name oder Pseudonym <span class="hp-comment-field__required" aria-hidden="true">*</span></label><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ?? '' ) . '" size="30" maxlength="245" autocomplete="name" placeholder="Wie soll dein Kommentar erscheinen?" aria-required="true" required><span class="hp-comment-field__hint">Wird öffentlich neben dem Kommentar angezeigt.</span></p>';
	$fields['cookies'] = '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . ( ! empty( $commenter['comment_author'] ) ? ' checked' : '' ) . '><label for="wp-comment-cookies-consent">Meinen Namen für das nächste Kommentieren in diesem Browser speichern.</label></p>';

	unset( $fields['email'] );
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
	if ( ! hp_is_editorial_comment_target() ) {
		return $field;
	}

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
	if ( ! hp_is_editorial_comment_target() ) {
		return $defaults;
	}

	return array_merge( $defaults, hp_get_comment_form_args() );
}
add_filter( 'comment_form_defaults', 'hp_customize_comment_form_defaults' );

/**
 * Erzeugt lokale Initialen statt externer Avatar-Abfragen.
 *
 * @param WP_Comment $comment Kommentarobjekt.
 * @return string
 */
function hp_get_comment_author_initials( WP_Comment $comment ): string {
	$name = trim( wp_strip_all_tags( (string) $comment->comment_author ) );

	if ( '' === $name ) {
		return 'G';
	}

	$parts    = preg_split( '/[\s\-]+/u', $name ) ?: [ $name ];
	$initials = '';

	foreach ( array_slice( $parts, 0, 2 ) as $part ) {
		if ( '' === $part ) {
			continue;
		}

		$initial = function_exists( 'mb_substr' ) ? mb_substr( $part, 0, 1 ) : substr( $part, 0, 1 );
		$initials .= function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $initial ) : strtoupper( $initial );
	}

	return '' !== $initials ? $initials : 'G';
}

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
				<span class="hp-comment__avatar-badge" aria-hidden="true"><?php echo esc_html( hp_get_comment_author_initials( $comment ) ); ?></span>
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
