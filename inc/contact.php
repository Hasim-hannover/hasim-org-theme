<?php
/**
 * Kontaktseite & Kontaktformular — Hasimuener Journal
 *
 * Native Kontaktlösung ohne Plugin:
 * - automatische Anlage einer Kontakt-Seite
 * - serverseitige Validierung
 * - stiller Spam-Schutz
 * - Mailversand via wp_mail()
 *
 * @package Hasimuener_Journal
 * @since   6.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Liefert die primäre Kontaktadresse.
 */
function hp_get_contact_email(): string {
	return 'hallo@hasimuener.de';
}

/**
 * Liefert die gespeicherte Kontakt-Seite, falls vorhanden.
 */
function hp_get_contact_page_id(): int {
	$page_id = (int) get_option( 'hp_contact_page_id', 0 );

	if ( $page_id > 0 && 'page' === get_post_type( $page_id ) ) {
		return $page_id;
	}

	$page = get_page_by_path( 'kontakt', OBJECT, 'page' );

	if ( $page instanceof WP_Post ) {
		update_option( 'hp_contact_page_id', (int) $page->ID, false );
		return (int) $page->ID;
	}

	return 0;
}

/**
 * Liefert die URL der Kontaktseite.
 */
function hp_get_contact_page_url(): string {
	$page_id = hp_get_contact_page_id();

	if ( $page_id > 0 ) {
		$permalink = get_permalink( $page_id );

		if ( is_string( $permalink ) && '' !== $permalink ) {
			return $permalink;
		}
	}

	return home_url( '/kontakt/' );
}

/**
 * Legt die Kontaktseite einmalig an, falls sie fehlt.
 */
function hp_bootstrap_contact_page(): void {
	if ( wp_installing() || ! post_type_exists( 'page' ) ) {
		return;
	}

	if ( hp_get_contact_page_id() > 0 ) {
		return;
	}

	$slug_conflict = get_posts( [
		'name'              => 'kontakt',
		'post_type'         => 'any',
		'post_status'       => [ 'publish', 'future', 'draft', 'pending', 'private' ],
		'fields'            => 'ids',
		'posts_per_page'    => 1,
		'suppress_filters'  => true,
		'no_found_rows'     => true,
		'ignore_sticky_posts' => true,
	] );

	if ( ! empty( $slug_conflict ) ) {
		return;
	}

	$page_id = wp_insert_post( [
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'post_title'     => 'Kontakt',
		'post_name'      => 'kontakt',
		'post_content'   => '',
		'comment_status' => 'closed',
		'ping_status'    => 'closed',
	] );

	if ( ! is_wp_error( $page_id ) && $page_id > 0 ) {
		update_option( 'hp_contact_page_id', (int) $page_id, false );
	}
}
add_action( 'init', 'hp_bootstrap_contact_page', 25 );

/**
 * Einstellungen für Formularschutz und Drosselung.
 *
 * @return array{min_seconds:int,max_age:int,rate_window:int,max_links:int}
 */
function hp_get_contact_form_settings(): array {
	return [
		'min_seconds' => 4,
		'max_age'     => DAY_IN_SECONDS,
		'rate_window' => 90,
		'max_links'   => 3,
	];
}

/**
 * Erstellt den Prüf-Token des Kontaktformulars.
 */
function hp_get_contact_form_render_token( int $rendered_at ): string {
	return wp_hash( $rendered_at . '|hp-contact-form' );
}

/**
 * Erzeugt einen anonymisierten Rate-Limit-Schlüssel.
 */
function hp_get_contact_form_rate_key(): string {
	if ( is_user_logged_in() ) {
		return 'user_' . get_current_user_id();
	}

	$ip = '';

	if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = (string) wp_unslash( $_SERVER['REMOTE_ADDR'] );
	}

	if ( '' === $ip ) {
		return 'guest_' . md5( wp_get_session_token() ?: 'anonymous' );
	}

	return 'guest_' . md5( $ip );
}

/**
 * Speichert eine Kurzmitteilung für den nächsten Redirect.
 *
 * @param array<string, mixed> $payload Flash-Daten.
 */
function hp_store_contact_flash( array $payload ): string {
	$token = strtolower( wp_generate_password( 24, false, false ) );
	set_transient( 'hp_contact_flash_' . $token, $payload, 10 * MINUTE_IN_SECONDS );

	return $token;
}

/**
 * Holt Flash-Daten einmalig aus dem Redirect.
 *
 * @return array<string, mixed>
 */
function hp_consume_contact_flash(): array {
	$token = isset( $_GET['contact'] ) ? sanitize_key( (string) wp_unslash( $_GET['contact'] ) ) : '';

	if ( '' === $token ) {
		return [];
	}

	$key   = 'hp_contact_flash_' . $token;
	$flash = get_transient( $key );

	delete_transient( $key );

	return is_array( $flash ) ? $flash : [];
}

/**
 * Leitet mit Flash-Daten zurück auf die Kontaktseite.
 *
 * @param array<string, mixed> $payload Flash-Daten.
 */
function hp_redirect_contact_form( array $payload ): void {
	$token = hp_store_contact_flash( $payload );
	$url   = add_query_arg( 'contact', rawurlencode( $token ), hp_get_contact_page_url() );

	wp_safe_redirect( $url );
	exit;
}

/**
 * Verarbeitet native Kontaktanfragen.
 */
function hp_handle_contact_form_submission(): void {
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) ) {
		wp_safe_redirect( hp_get_contact_page_url() );
		exit;
	}

	$fields = [
		'name'    => isset( $_POST['hp_contact_name'] ) ? sanitize_text_field( (string) wp_unslash( $_POST['hp_contact_name'] ) ) : '',
		'email'   => isset( $_POST['hp_contact_email'] ) ? sanitize_email( (string) wp_unslash( $_POST['hp_contact_email'] ) ) : '',
		'subject' => isset( $_POST['hp_contact_subject'] ) ? sanitize_text_field( (string) wp_unslash( $_POST['hp_contact_subject'] ) ) : '',
		'message' => isset( $_POST['hp_contact_message'] ) ? trim( sanitize_textarea_field( (string) wp_unslash( $_POST['hp_contact_message'] ) ) ) : '',
	];

	$flash = [
		'status' => 'error',
		'fields' => $fields,
	];

	$nonce = isset( $_POST['hp_contact_nonce'] ) ? (string) wp_unslash( $_POST['hp_contact_nonce'] ) : '';

	if ( ! wp_verify_nonce( $nonce, 'hp_contact_submit' ) ) {
		$flash['message'] = 'Das Formular ist nicht mehr gültig. Bitte lade die Seite neu und versuche es erneut.';
		hp_redirect_contact_form( $flash );
	}

	$honeypot = isset( $_POST['hp_contact_website'] ) ? trim( (string) wp_unslash( $_POST['hp_contact_website'] ) ) : '';

	if ( '' !== $honeypot ) {
		$flash['message'] = 'Die Nachricht konnte nicht gesendet werden. Bitte versuche es erneut.';
		hp_redirect_contact_form( $flash );
	}

	$settings    = hp_get_contact_form_settings();
	$rendered_at = isset( $_POST['hp_contact_rendered_at'] ) ? (int) wp_unslash( $_POST['hp_contact_rendered_at'] ) : 0;
	$token       = isset( $_POST['hp_contact_render_token'] ) ? (string) wp_unslash( $_POST['hp_contact_render_token'] ) : '';

	if ( $rendered_at <= 0 || '' === $token || ! hash_equals( hp_get_contact_form_render_token( $rendered_at ), $token ) ) {
		$flash['message'] = 'Das Formular ist abgelaufen. Bitte lade die Seite neu und versuche es erneut.';
		hp_redirect_contact_form( $flash );
	}

	$elapsed = time() - $rendered_at;

	if ( $elapsed < $settings['min_seconds'] ) {
		$flash['message'] = 'Bitte nimm dir einen kurzen Moment Zeit und sende die Nachricht dann erneut.';
		hp_redirect_contact_form( $flash );
	}

	if ( $elapsed > $settings['max_age'] ) {
		$flash['message'] = 'Das Formular ist abgelaufen. Bitte lade die Seite neu und versuche es erneut.';
		hp_redirect_contact_form( $flash );
	}

	$link_count = preg_match_all( '/(?:https?:\/\/|www\.|<a\s)/iu', $fields['message'] );

	if ( false !== $link_count && $link_count > $settings['max_links'] ) {
		$flash['message'] = 'Bitte reduziere die Zahl der Links in deiner Nachricht.';
		hp_redirect_contact_form( $flash );
	}

	$rate_key     = 'hp_contact_rate_' . hp_get_contact_form_rate_key();
	$last_sent_at = (int) get_transient( $rate_key );

	if ( $last_sent_at > 0 && ( time() - $last_sent_at ) < $settings['rate_window'] ) {
		$flash['message'] = 'Bitte warte einen kurzen Moment, bevor du eine weitere Nachricht sendest.';
		hp_redirect_contact_form( $flash );
	}

	if ( '' === $fields['name'] ) {
		$flash['message'] = 'Bitte gib deinen Namen an.';
		hp_redirect_contact_form( $flash );
	}

	if ( '' === $fields['email'] || ! is_email( $fields['email'] ) ) {
		$flash['message'] = 'Bitte gib eine gültige E-Mail-Adresse an.';
		hp_redirect_contact_form( $flash );
	}

	if ( '' === $fields['message'] ) {
		$flash['message'] = 'Bitte schreibe eine Nachricht.';
		hp_redirect_contact_form( $flash );
	}

	$reply_name = preg_replace( '/[\r\n]+/', ' ', $fields['name'] );
	$subject    = '' !== $fields['subject'] ? $fields['subject'] : 'Neue Nachricht über das Kontaktformular';
	$mail_body  = implode(
		"\n\n",
		[
			'Neue Nachricht über das Kontaktformular von hasimuener.org',
			'Name: ' . $fields['name'],
			'E-Mail: ' . $fields['email'],
			'Betreff: ' . ( '' !== $fields['subject'] ? $fields['subject'] : 'Nicht angegeben' ),
			'Nachricht:',
			$fields['message'],
		]
	);

	$headers = [
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $reply_name . ' <' . $fields['email'] . '>',
	];

	$mail_sent = wp_mail(
		hp_get_contact_email(),
		'[hasimuener.org] ' . $subject,
		$mail_body,
		$headers
	);

	if ( ! $mail_sent ) {
		$flash['message'] = 'Die Nachricht konnte technisch nicht versendet werden. Du kannst alternativ direkt an ' . hp_get_contact_email() . ' schreiben.';
		hp_redirect_contact_form( $flash );
	}

	set_transient( $rate_key, time(), $settings['rate_window'] );

	hp_redirect_contact_form( [
		'status'  => 'success',
		'message' => 'Danke. Deine Nachricht wurde versendet.',
	] );
}
add_action( 'admin_post_nopriv_hp_send_contact', 'hp_handle_contact_form_submission' );
add_action( 'admin_post_hp_send_contact', 'hp_handle_contact_form_submission' );
