<?php
/**
 * Kontaktseite & Kontaktformular — Hasimuener Journal
 *
 * Native Kontaktlösung ohne Plugin:
 * - automatische Anlage einer Kontakt-Seite
 * - serverseitige Validierung
 * - stiller Spam-Schutz
 * - Mailversand ausschliesslich via Brevo Transactional API
 *
 * @package Hasimuener_Journal
 * @since   6.3.0
 */

defined( 'ABSPATH' ) || exit;

$hp_contact_local_config = __DIR__ . '/contact-local.php';

if ( file_exists( $hp_contact_local_config ) ) {
	require_once $hp_contact_local_config;
}

/**
 * Liefert die primäre Kontaktadresse.
 */
function hp_get_contact_email(): string {
	return 'hallo@hasimuener.de';
}

/**
 * Öffentlicher Titel der Kontaktseite.
 */
function hp_get_contact_page_title(): string {
	return 'Anfragen & Zusammenarbeit';
}

/**
 * Auswahloptionen für die Art der Anfrage.
 *
 * @return array<string, string>
 */
function hp_get_contact_inquiry_type_options(): array {
	return [
		'editorial'   => 'Redaktionelle Anfrage',
		'essay'       => 'Gastbeitrag / Essay',
		'interview'   => 'Interview / Gespräch / Vortrag',
		'cooperation' => 'Kooperation',
		'writing'     => 'Schreibprojekt / Textanfrage',
		'other'       => 'Sonstiges',
	];
}

/**
 * Liefert die lesbare Bezeichnung eines Anfragetyps.
 */
function hp_get_contact_inquiry_type_label( string $inquiry_type ): string {
	$options = hp_get_contact_inquiry_type_options();

	if ( isset( $options[ $inquiry_type ] ) ) {
		return $options[ $inquiry_type ];
	}

	return $options['other'];
}

/**
 * Normalisiert eingegebene Websites oder Links.
 */
function hp_normalize_contact_website_url( string $url ): string {
	$url = trim( $url );

	if ( '' === $url ) {
		return '';
	}

	if ( ! preg_match( '#^[a-z][a-z0-9+\-.]*://#i', $url ) ) {
		$url = 'https://' . ltrim( $url, '/' );
	}

	return esc_url_raw( $url, [ 'http', 'https' ] );
}

/**
 * Baut eine knappe interne Betreffzeile für neue Anfragen.
 *
 * @param array<string, string> $fields Validierte Formularfelder.
 */
function hp_get_contact_submission_subject( array $fields ): string {
	$subject = hp_get_contact_inquiry_type_label( (string) ( $fields['inquiry_type'] ?? '' ) );

	if ( ! empty( $fields['organization'] ) ) {
		$subject .= ' - ' . trim( (string) $fields['organization'] );
	}

	if ( function_exists( 'mb_substr' ) ) {
		return (string) mb_substr( $subject, 0, 190 );
	}

	return substr( $subject, 0, 190 );
}

/**
 * Liefert einen API-tauglichen Sendernamen.
 */
function hp_get_contact_brevo_sender_name(): string {
	return hp_brevo_get_from_name();
}

/**
 * Liefert die gespeicherte Kontakt-Seite, falls vorhanden.
 */
function hp_get_contact_page_id(): int {
	$page_id = (int) get_option( 'hp_contact_page_id', 0 );

	if ( $page_id > 0 && 'page' === get_post_type( $page_id ) ) {
		hp_assign_contact_page_template( $page_id );
		return $page_id;
	}

	$page = get_page_by_path( 'kontakt', OBJECT, 'page' );

	if ( $page instanceof WP_Post ) {
		hp_assign_contact_page_template( (int) $page->ID );
		update_option( 'hp_contact_page_id', (int) $page->ID, false );
		return (int) $page->ID;
	}

	return 0;
}

/**
 * Stellt sicher, dass die Kontaktseite das richtige Template nutzt.
 */
function hp_assign_contact_page_template( int $page_id ): void {
	if ( $page_id <= 0 ) {
		return;
	}

	if ( 'page-kontakt.php' !== get_page_template_slug( $page_id ) ) {
		update_post_meta( $page_id, '_wp_page_template', 'page-kontakt.php' );
	}

	$page = get_post( $page_id );

	if ( $page instanceof WP_Post && in_array( $page->post_title, [ '', 'Kontakt' ], true ) ) {
		wp_update_post(
			[
				'ID'         => $page_id,
				'post_title' => hp_get_contact_page_title(),
			]
		);
	}
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
		'post_title'     => hp_get_contact_page_title(),
		'post_name'      => 'kontakt',
		'post_content'   => '',
		'comment_status' => 'closed',
		'ping_status'    => 'closed',
	] );

	if ( ! is_wp_error( $page_id ) && $page_id > 0 ) {
		hp_assign_contact_page_template( (int) $page_id );
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
 * Betreff der automatischen Eingangsbestätigung.
 */
function hp_get_contact_autoreply_subject(): string {
	return 'Ihre Nachricht an hasimuener.org ist eingegangen';
}

/**
 * Liefert einen zustellungsfreundlichen Absendernamen.
 */
function hp_get_contact_mail_sender_name(): string {
	return 'Hasim Uener';
}

/**
 * Baut das HTML-Template der automatischen Eingangsbestätigung.
 *
 * @param array<string, string> $fields Validierte Formularfelder.
 */
function hp_get_contact_autoreply_html( array $fields ): string {
	$site_name      = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$contact_email  = hp_get_contact_email();
	$contact_mailto = 'mailto:' . $contact_email;
	$site_url       = home_url( '/' );
	$imprint_url    = home_url( '/impressum/' );
	$privacy_url    = home_url( '/datenschutz/' );
	$name_line      = '' !== $fields['name'] ? esc_html( $fields['name'] ) : 'Guten Tag';
	$inquiry_line   = esc_html( hp_get_contact_inquiry_type_label( (string) ( $fields['inquiry_type'] ?? '' ) ) );
	$organization   = '' !== (string) ( $fields['organization'] ?? '' ) ? esc_html( (string) $fields['organization'] ) : 'Nicht angegeben';
	$timeframe      = '' !== (string) ( $fields['timeframe'] ?? '' ) ? esc_html( (string) $fields['timeframe'] ) : 'Nicht angegeben';
	$website_line   = 'Nicht angegeben';

	if ( ! empty( $fields['website_url'] ) ) {
		$website_url  = (string) $fields['website_url'];
		$website_line = '<a href="' . esc_url( $website_url ) . '" style="color:#b12a2a;text-decoration:none;">' . esc_html( $website_url ) . '</a>';
	}

	return '<!doctype html>
<html lang="de">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>' . esc_html( hp_get_contact_autoreply_subject() ) . '</title>
</head>
<body style="margin:0;padding:0;background:#f4f5f7;color:#222222;">
	<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f4f5f7;margin:0;padding:24px 0;">
		<tr>
			<td align="center">
				<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:640px;background:#ffffff;border:1px solid rgba(17,17,17,0.08);border-radius:18px;overflow:hidden;">
					<tr>
						<td style="padding:28px 32px 14px;border-top:4px solid #b12a2a;">
							<p style="margin:0 0 10px;font-family:Georgia,Times New Roman,serif;font-size:12px;line-height:1.5;letter-spacing:1.8px;text-transform:uppercase;color:#696969;">' . esc_html( $site_name ) . '</p>
							<h1 style="margin:0;font-family:Georgia,Times New Roman,serif;font-size:30px;line-height:1.2;color:#111111;font-weight:700;">Ihre Nachricht ist eingegangen.</h1>
						</td>
					</tr>
					<tr>
						<td style="padding:0 32px 8px;">
							<p style="margin:0 0 14px;font-family:Georgia,Times New Roman,serif;font-size:17px;line-height:1.75;color:#333333;">' . $name_line . ', vielen Dank für Ihre Nachricht über hasimuener.org. Sie wurde direkt weitergeleitet.</p>
							<p style="margin:0 0 14px;font-family:Georgia,Times New Roman,serif;font-size:17px;line-height:1.75;color:#333333;">Ich melde mich, sobald ich inhaltlich antworten kann. Wenn Sie in der Zwischenzeit etwas ergänzen möchten, können Sie direkt auf diese E-Mail antworten.</p>
						</td>
					</tr>
					<tr>
						<td style="padding:8px 32px 8px;">
							<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #e6e1d8;border-radius:14px;background:#faf8f5;">
								<tr>
									<td style="padding:16px 18px;">
										<p style="margin:0 0 8px;font-family:Arial,Helvetica,sans-serif;font-size:11px;line-height:1.4;letter-spacing:1.5px;text-transform:uppercase;color:#696969;">Zusammenfassung</p>
										<p style="margin:0 0 6px;font-family:Georgia,Times New Roman,serif;font-size:15px;line-height:1.6;color:#222222;"><strong>Art der Anfrage:</strong> ' . $inquiry_line . '</p>
										<p style="margin:0 0 6px;font-family:Georgia,Times New Roman,serif;font-size:15px;line-height:1.6;color:#222222;"><strong>Organisation / Medium / Projekt:</strong> ' . $organization . '</p>
										<p style="margin:0 0 6px;font-family:Georgia,Times New Roman,serif;font-size:15px;line-height:1.6;color:#222222;"><strong>Website oder Link:</strong> ' . $website_line . '</p>
										<p style="margin:0 0 6px;font-family:Georgia,Times New Roman,serif;font-size:15px;line-height:1.6;color:#222222;"><strong>Zeitraum / Terminbezug:</strong> ' . $timeframe . '</p>
										<p style="margin:0;font-family:Georgia,Times New Roman,serif;font-size:15px;line-height:1.6;color:#222222;"><strong>Antwortadresse:</strong> <a href="' . esc_url( $contact_mailto ) . '" style="color:#b12a2a;text-decoration:none;">' . esc_html( $contact_email ) . '</a></p>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="padding:16px 32px 30px;">
							<p style="margin:0 0 16px;font-family:Georgia,Times New Roman,serif;font-size:16px;line-height:1.7;color:#333333;">Mit freundlichen Grüßen<br>Haşim Üner</p>
							<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-top:1px solid #ece7df;padding-top:14px;margin-top:14px;">
								<tr>
									<td style="padding-top:14px;">
										<p style="margin:0 0 6px;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.6;color:#696969;">Haşim Üner</p>
										<p style="margin:0 0 6px;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.6;color:#696969;"><a href="' . esc_url( $contact_mailto ) . '" style="color:#b12a2a;text-decoration:none;">' . esc_html( $contact_email ) . '</a></p>
										<p style="margin:0 0 6px;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.6;color:#696969;"><a href="' . esc_url( $site_url ) . '" style="color:#b12a2a;text-decoration:none;">hasimuener.org</a></p>
										<p style="margin:10px 0 0;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.6;color:#696969;"><a href="' . esc_url( $imprint_url ) . '" style="color:#b12a2a;text-decoration:none;">Impressum</a> · <a href="' . esc_url( $privacy_url ) . '" style="color:#b12a2a;text-decoration:none;">Datenschutz</a></p>
									</td>
								</tr>
							</table>
							<p style="margin:14px 0 0;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.6;color:#696969;">Diese E-Mail wurde automatisch nach dem Absenden des Kontaktformulars erzeugt.</p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>';
}

/**
 * Baut die Textversion der automatischen Eingangsbestätigung.
 *
 * @param array<string, string> $fields Validierte Formularfelder.
 */
function hp_get_contact_autoreply_text( array $fields ): string {
	$contact_email = hp_get_contact_email();
	$site_url      = home_url( '/' );
	$imprint_url   = home_url( '/impressum/' );
	$privacy_url   = home_url( '/datenschutz/' );
	$name_line     = '' !== $fields['name'] ? $fields['name'] : 'Guten Tag';
	$inquiry_line  = hp_get_contact_inquiry_type_label( (string) ( $fields['inquiry_type'] ?? '' ) );
	$organization  = '' !== (string) ( $fields['organization'] ?? '' ) ? (string) $fields['organization'] : 'Nicht angegeben';
	$website_url   = '' !== (string) ( $fields['website_url'] ?? '' ) ? (string) $fields['website_url'] : 'Nicht angegeben';
	$timeframe     = '' !== (string) ( $fields['timeframe'] ?? '' ) ? (string) $fields['timeframe'] : 'Nicht angegeben';

	return implode(
		"\n\n",
		[
			'Ihre Nachricht ist eingegangen.',
			$name_line . ', vielen Dank für Ihre Nachricht über hasimuener.org. Sie wurde direkt weitergeleitet.',
			'Ich melde mich, sobald ich inhaltlich antworten kann. Wenn Sie in der Zwischenzeit etwas ergänzen möchten, können Sie direkt auf diese E-Mail antworten.',
			'Zusammenfassung',
			'Art der Anfrage: ' . $inquiry_line . "\n" . 'Organisation / Medium / Projekt: ' . $organization . "\n" . 'Website oder Link: ' . $website_url . "\n" . 'Zeitraum / Terminbezug: ' . $timeframe . "\n" . 'Antwortadresse: ' . $contact_email,
			'Mit freundlichen Grüßen' . "\n" . 'Haşim Üner',
			'Kontakt: ' . $contact_email . "\n" . 'Website: ' . $site_url . "\n" . 'Impressum: ' . $imprint_url . "\n" . 'Datenschutz: ' . $privacy_url,
			'Diese E-Mail wurde automatisch nach dem Absenden des Kontaktformulars erzeugt.',
		]
	);
}

/**
 * Template-Parameter fuer die automatische Eingangsbestätigung.
 *
 * @param array<string, string> $fields Validierte Formularfelder.
 * @return array<string, mixed>
 */
function hp_get_contact_autoreply_template_params( array $fields ): array {
	return [
		'name'           => (string) ( $fields['name'] ?? '' ),
		'email'          => (string) ( $fields['email'] ?? '' ),
		'organization'   => (string) ( $fields['organization'] ?? '' ),
		'website'        => (string) ( $fields['website_url'] ?? '' ),
		'inquiry_type'   => hp_get_contact_inquiry_type_label( (string) ( $fields['inquiry_type'] ?? '' ) ),
		'timeframe'      => (string) ( $fields['timeframe'] ?? '' ),
		'message'        => (string) ( $fields['message'] ?? '' ),
		'contact_email'  => hp_get_contact_email(),
		'contact_name'   => hp_get_contact_mail_sender_name(),
		'site_url'       => home_url( '/' ),
		'imprint_url'    => home_url( '/impressum/' ),
		'privacy_url'    => home_url( '/datenschutz/' ),
	];
}

/**
 * Versendet die automatische Eingangsbestätigung.
 *
 * @param array<string, string> $fields Validierte Formularfelder.
 */
function hp_send_contact_autoreply( array $fields ): bool {
	$response = hp_brevo_send_transactional_email(
		[
			'template_key'   => 'contact_autoreply',
			'to'             => [
				[
					'email' => $fields['email'],
					'name'  => $fields['name'],
				],
			],
			'subject'        => hp_get_contact_autoreply_subject(),
			'html_content'   => hp_get_contact_autoreply_html( $fields ),
			'text_content'   => hp_get_contact_autoreply_text( $fields ),
			'params'         => hp_get_contact_autoreply_template_params( $fields ),
			'reply_to_email' => hp_get_contact_email(),
			'reply_to_name'  => hp_get_contact_brevo_sender_name(),
			'tags'           => [ 'contact-form', 'contact-autoreply' ],
		]
	);

	return ! empty( $response['success'] );
}

/**
 * Baut die Textversion der internen Kontaktbenachrichtigung.
 *
 * @param array<string, string> $fields Validierte Formularfelder.
 */
function hp_get_contact_notification_text( array $fields ): string {
	$organization = '' !== (string) ( $fields['organization'] ?? '' ) ? (string) $fields['organization'] : 'Nicht angegeben';
	$website_url  = '' !== (string) ( $fields['website_url'] ?? '' ) ? (string) $fields['website_url'] : 'Nicht angegeben';
	$timeframe    = '' !== (string) ( $fields['timeframe'] ?? '' ) ? (string) $fields['timeframe'] : 'Nicht angegeben';
	$inquiry_type = hp_get_contact_inquiry_type_label( (string) ( $fields['inquiry_type'] ?? '' ) );

	return implode(
		"\n\n",
		[
			'Neue Nachricht über das Kontaktformular von hasimuener.org',
			'Name: ' . $fields['name'],
			'E-Mail: ' . $fields['email'],
			'Art der Anfrage: ' . $inquiry_type,
			'Organisation / Medium / Projekt: ' . $organization,
			'Website oder Link: ' . $website_url,
			'Zeitraum / Terminbezug: ' . $timeframe,
			'Interner Betreff: ' . ( '' !== $fields['subject'] ? $fields['subject'] : 'Nicht angegeben' ),
			'Beschreibung des Anliegens:',
			$fields['message'],
		]
	);
}

/**
 * Template-Parameter fuer interne Kontaktbenachrichtigungen.
 *
 * @param array<string, string> $fields Validierte Formularfelder.
 * @return array<string, mixed>
 */
function hp_get_contact_notification_template_params( array $fields ): array {
	return [
		'name'          => (string) ( $fields['name'] ?? '' ),
		'email'         => (string) ( $fields['email'] ?? '' ),
		'organization'  => (string) ( $fields['organization'] ?? '' ),
		'website'       => (string) ( $fields['website_url'] ?? '' ),
		'inquiry_type'  => hp_get_contact_inquiry_type_label( (string) ( $fields['inquiry_type'] ?? '' ) ),
		'timeframe'     => (string) ( $fields['timeframe'] ?? '' ),
		'subject'       => (string) ( $fields['subject'] ?? '' ),
		'message'       => (string) ( $fields['message'] ?? '' ),
		'contact_email' => hp_get_contact_email(),
	];
}

/**
 * Versendet die interne Benachrichtigung über eine neue Kontaktanfrage.
 *
 * @param array<string, string> $fields Validierte Formularfelder.
 */
function hp_send_contact_notification( array $fields ): bool {
	$reply_name = preg_replace( '/[\r\n]+/', ' ', $fields['name'] );
	$subject    = '' !== $fields['subject'] ? $fields['subject'] : 'Neue Nachricht über das Kontaktformular';
	$mail_body  = hp_get_contact_notification_text( $fields );
	$response   = hp_brevo_send_transactional_email(
		[
			'template_key'   => 'contact_notification',
			'to'             => [
				[
					'email' => hp_get_contact_email(),
					'name'  => hp_get_contact_brevo_sender_name(),
				],
			],
			'subject'        => '[hasimuener.org] ' . $subject,
			'text_content'   => $mail_body,
			'params'         => hp_get_contact_notification_template_params( $fields ),
			'reply_to_email' => $fields['email'],
			'reply_to_name'  => is_string( $reply_name ) && '' !== $reply_name ? $reply_name : $fields['email'],
			'tags'           => [ 'contact-form', 'contact-notification' ],
		]
	);

	return ! empty( $response['success'] );
}

/**
 * Verarbeitet native Kontaktanfragen.
 */
function hp_handle_contact_form_submission(): void {
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) ) {
		wp_safe_redirect( hp_get_contact_page_url() );
		exit;
	}

	$website_input = isset( $_POST['hp_contact_website_url'] ) ? sanitize_text_field( (string) wp_unslash( $_POST['hp_contact_website_url'] ) ) : '';

	$fields = [
		'name'         => isset( $_POST['hp_contact_name'] ) ? sanitize_text_field( (string) wp_unslash( $_POST['hp_contact_name'] ) ) : '',
		'email'        => isset( $_POST['hp_contact_email'] ) ? sanitize_email( (string) wp_unslash( $_POST['hp_contact_email'] ) ) : '',
		'organization' => isset( $_POST['hp_contact_organization'] ) ? sanitize_text_field( (string) wp_unslash( $_POST['hp_contact_organization'] ) ) : '',
		'website_url'  => $website_input,
		'inquiry_type' => isset( $_POST['hp_contact_inquiry_type'] ) ? sanitize_key( (string) wp_unslash( $_POST['hp_contact_inquiry_type'] ) ) : '',
		'timeframe'    => isset( $_POST['hp_contact_timeframe'] ) ? sanitize_text_field( (string) wp_unslash( $_POST['hp_contact_timeframe'] ) ) : '',
		'subject'      => '',
		'message'      => isset( $_POST['hp_contact_message'] ) ? trim( sanitize_textarea_field( (string) wp_unslash( $_POST['hp_contact_message'] ) ) ) : '',
	];

	$flash = [
		'status' => 'error',
		'fields' => $fields,
	];

	$nonce = isset( $_POST['hp_contact_nonce'] ) ? (string) wp_unslash( $_POST['hp_contact_nonce'] ) : '';

	if ( ! wp_verify_nonce( $nonce, 'hp_contact_submit' ) ) {
		$flash['message'] = 'Das Formular ist nicht mehr gültig. Bitte laden Sie die Seite neu und versuchen Sie es erneut.';
		hp_redirect_contact_form( $flash );
	}

	$honeypot = isset( $_POST['hp_contact_website'] ) ? trim( (string) wp_unslash( $_POST['hp_contact_website'] ) ) : '';

	if ( '' !== $honeypot ) {
		$flash['message'] = 'Die Nachricht konnte nicht gesendet werden. Bitte versuchen Sie es erneut.';
		hp_redirect_contact_form( $flash );
	}

	$settings    = hp_get_contact_form_settings();
	$rendered_at = isset( $_POST['hp_contact_rendered_at'] ) ? (int) wp_unslash( $_POST['hp_contact_rendered_at'] ) : 0;
	$token       = isset( $_POST['hp_contact_render_token'] ) ? (string) wp_unslash( $_POST['hp_contact_render_token'] ) : '';

	if ( $rendered_at <= 0 || '' === $token || ! hash_equals( hp_get_contact_form_render_token( $rendered_at ), $token ) ) {
		$flash['message'] = 'Das Formular ist abgelaufen. Bitte laden Sie die Seite neu und versuchen Sie es erneut.';
		hp_redirect_contact_form( $flash );
	}

	$elapsed = time() - $rendered_at;

	if ( $elapsed < $settings['min_seconds'] ) {
		$flash['message'] = 'Bitte nehmen Sie sich einen kurzen Moment Zeit und senden Sie die Nachricht dann erneut.';
		hp_redirect_contact_form( $flash );
	}

	if ( $elapsed > $settings['max_age'] ) {
		$flash['message'] = 'Das Formular ist abgelaufen. Bitte laden Sie die Seite neu und versuchen Sie es erneut.';
		hp_redirect_contact_form( $flash );
	}

	$link_count = preg_match_all( '/(?:https?:\/\/|www\.|<a\s)/iu', $fields['message'] );

	if ( false !== $link_count && $link_count > $settings['max_links'] ) {
		$flash['message'] = 'Bitte reduzieren Sie die Zahl der Links in Ihrer Nachricht.';
		hp_redirect_contact_form( $flash );
	}

	$rate_key     = 'hp_contact_rate_' . hp_get_contact_form_rate_key();
	$last_sent_at = (int) get_transient( $rate_key );

	if ( $last_sent_at > 0 && ( time() - $last_sent_at ) < $settings['rate_window'] ) {
		$flash['message'] = 'Bitte warten Sie einen kurzen Moment, bevor Sie eine weitere Nachricht senden.';
		hp_redirect_contact_form( $flash );
	}

	if ( '' === $fields['name'] ) {
		$flash['message'] = 'Bitte geben Sie Ihren Namen an.';
		hp_redirect_contact_form( $flash );
	}

	if ( '' === $fields['email'] || ! is_email( $fields['email'] ) ) {
		$flash['message'] = 'Bitte geben Sie eine gültige E-Mail-Adresse an.';
		hp_redirect_contact_form( $flash );
	}

	if ( '' !== $website_input ) {
		$fields['website_url'] = hp_normalize_contact_website_url( $website_input );

		if ( '' === $fields['website_url'] ) {
			$flash['message'] = 'Bitte geben Sie eine gültige Website oder einen gültigen Link an.';
			hp_redirect_contact_form( $flash );
		}
	}

	if ( ! array_key_exists( $fields['inquiry_type'], hp_get_contact_inquiry_type_options() ) ) {
		$flash['message'] = 'Bitte wählen Sie die Art Ihrer Anfrage aus.';
		hp_redirect_contact_form( $flash );
	}

	if ( '' === $fields['message'] ) {
		$flash['message'] = 'Bitte beschreiben Sie Ihr Anliegen kurz.';
		hp_redirect_contact_form( $flash );
	}

	$fields['subject'] = hp_get_contact_submission_subject( $fields );

	$mail_sent = hp_send_contact_notification( $fields );

	$autoresponse_sent = false;

	if ( $mail_sent && strtolower( $fields['email'] ) !== strtolower( hp_get_contact_email() ) ) {
		$autoresponse_sent = hp_send_contact_autoreply( $fields );
	}

	if ( function_exists( 'hp_store_contact_submission' ) ) {
		hp_store_contact_submission( $fields, $mail_sent, $autoresponse_sent );
	}

	if ( $mail_sent ) {
		hp_brevo_sync_contact_submission( $fields );
	}

	if ( ! $mail_sent ) {
		$flash['message'] = 'Die Nachricht konnte technisch nicht versendet werden. Sie können alternativ direkt an ' . hp_get_contact_email() . ' schreiben.';
		hp_redirect_contact_form( $flash );
	}

	set_transient( $rate_key, time(), $settings['rate_window'] );

	hp_redirect_contact_form( [
		'status'  => 'success',
		'message' => $autoresponse_sent
			? 'Vielen Dank. Ihre Nachricht wurde versendet. Eine kurze Bestätigung ist per E-Mail unterwegs.'
			: 'Vielen Dank. Ihre Nachricht wurde versendet.',
	] );
}
add_action( 'admin_post_nopriv_hp_send_contact', 'hp_handle_contact_form_submission' );
add_action( 'admin_post_hp_send_contact', 'hp_handle_contact_form_submission' );
