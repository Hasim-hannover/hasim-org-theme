<?php
/**
 * Zentraler Brevo-Transactional-Service.
 *
 * - versendet transaktionale E-Mails ausschliesslich ueber die Brevo API
 * - kapselt Brevo-Requests und Fehlerbehandlung
 * - synchronisiert Kontakte optional mit Brevo Contacts
 * - stellt einen admin-geschuetzten Test-Endpunkt bereit
 *
 * @package Hasimuener_Journal
 * @since   6.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Liest den ersten verfuegbaren Konfigurationswert aus Konstanten oder Umgebungsvariablen.
 *
 * @param array<int, string> $constant_names Mögliche Konstantennamen.
 * @param array<int, string> $env_names      Mögliche ENV-Namen.
 */
function hp_brevo_get_config_value( array $constant_names, array $env_names = [] ): string {
	foreach ( $constant_names as $constant_name ) {
		if ( defined( $constant_name ) ) {
			$value = constant( $constant_name );

			if ( is_string( $value ) || is_numeric( $value ) ) {
				$value = trim( (string) $value );

				if ( '' !== $value ) {
					return $value;
				}
			}
		}
	}

	foreach ( $env_names as $env_name ) {
		$value = getenv( $env_name );

		if ( is_string( $value ) ) {
			$value = trim( $value );

			if ( '' !== $value ) {
				return $value;
			}
		}
	}

	return '';
}

/**
 * Liefert den Brevo API-Key.
 */
function hp_brevo_get_api_key(): string {
	$key = hp_brevo_get_config_value(
		[ 'BREVO_API_KEY', 'HP_BREVO_API_KEY' ],
		[ 'BREVO_API_KEY', 'HP_BREVO_API_KEY' ]
	);

	if ( '' === $key || 0 !== strpos( $key, 'xkeysib-' ) ) {
		return '';
	}

	return $key;
}

/**
 * Liefert die verifizierte From-Adresse.
 */
function hp_brevo_get_from_email(): string {
	$email = hp_brevo_get_config_value(
		[ 'FROM_EMAIL', 'BREVO_FROM_EMAIL', 'HP_BREVO_FROM_EMAIL' ],
		[ 'FROM_EMAIL', 'BREVO_FROM_EMAIL', 'HP_BREVO_FROM_EMAIL' ]
	);

	if ( '' === $email && function_exists( 'hp_get_contact_email' ) ) {
		$email = trim( (string) hp_get_contact_email() );
	}

	return is_email( $email ) ? $email : '';
}

/**
 * Liefert den Anzeigenamen des Absenders.
 */
function hp_brevo_get_from_name(): string {
	$name = hp_brevo_get_config_value(
		[ 'FROM_NAME', 'BREVO_FROM_NAME', 'HP_BREVO_FROM_NAME' ],
		[ 'FROM_NAME', 'BREVO_FROM_NAME', 'HP_BREVO_FROM_NAME' ]
	);

	if ( '' === $name && function_exists( 'hp_get_contact_mail_sender_name' ) ) {
		$name = trim( (string) hp_get_contact_mail_sender_name() );
	}

	if ( '' === $name ) {
		$name = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	}

	return $name;
}

/**
 * Liefert optional die konfigurierte Brevo-Listen-ID.
 */
function hp_brevo_get_contact_list_id(): int {
	$value = hp_brevo_get_config_value(
		[ 'CONTACT_LIST_ID', 'BREVO_CONTACT_LIST_ID', 'HP_BREVO_CONTACT_LIST_ID' ],
		[ 'CONTACT_LIST_ID', 'BREVO_CONTACT_LIST_ID', 'HP_BREVO_CONTACT_LIST_ID' ]
	);

	return ctype_digit( $value ) ? (int) $value : 0;
}

/**
 * Liefert optionale Template-IDs als Mapping.
 *
 * Erwartetes Format:
 * {"contact_autoreply":123,"contact_notification":456}
 *
 * @return array<string, int>
 */
function hp_brevo_get_template_ids(): array {
	$raw_value = hp_brevo_get_config_value(
		[ 'BREVO_TEMPLATE_IDS', 'HP_BREVO_TEMPLATE_IDS' ],
		[ 'BREVO_TEMPLATE_IDS', 'HP_BREVO_TEMPLATE_IDS' ]
	);

	if ( '' === $raw_value ) {
		return [];
	}

	$decoded = json_decode( $raw_value, true );

	if ( ! is_array( $decoded ) ) {
		return [];
	}

	$template_ids = [];

	foreach ( $decoded as $key => $value ) {
		$template_key = sanitize_key( (string) $key );
		$template_id  = is_numeric( $value ) ? (int) $value : 0;

		if ( '' !== $template_key && $template_id > 0 ) {
			$template_ids[ $template_key ] = $template_id;
		}
	}

	return $template_ids;
}

/**
 * Liefert die Template-ID fuer einen logischen Schlüssel.
 */
function hp_brevo_get_template_id( string $template_key ): int {
	$template_ids = hp_brevo_get_template_ids();
	$template_key = sanitize_key( $template_key );

	return isset( $template_ids[ $template_key ] ) ? (int) $template_ids[ $template_key ] : 0;
}

/**
 * Prüft, ob die Pflichtkonfiguration vorhanden ist.
 */
function hp_brevo_has_required_config(): bool {
	return '' !== hp_brevo_get_api_key() && '' !== hp_brevo_get_from_email();
}

/**
 * Liefert fehlende Konfigurationswerte fuer den Produktivbetrieb.
 *
 * @return array<int, string>
 */
function hp_brevo_get_missing_required_config(): array {
	$missing = [];

	if ( '' === hp_brevo_get_api_key() ) {
		$missing[] = 'BREVO_API_KEY';
	}

	if ( '' === hp_brevo_get_from_email() ) {
		$missing[] = 'FROM_EMAIL';
	}

	if ( '' === hp_brevo_get_from_name() ) {
		$missing[] = 'FROM_NAME';
	}

	return $missing;
}

/**
 * Key fuer globale Rate-Limit-Sperren gegen Brevo-429.
 */
function hp_brevo_get_rate_limit_key(): string {
	return 'hp_brevo_rate_limited_until';
}

/**
 * Prüft, ob Brevo aktuell serverseitig gedrosselt wurde.
 */
function hp_brevo_is_rate_limited(): bool {
	$retry_until = (int) get_transient( hp_brevo_get_rate_limit_key() );

	return $retry_until > time();
}

/**
 * Setzt eine temporäre globale Brevo-Drosselung.
 */
function hp_brevo_mark_rate_limited( int $retry_after_seconds ): void {
	$retry_after_seconds = max( 15, $retry_after_seconds );
	set_transient( hp_brevo_get_rate_limit_key(), time() + $retry_after_seconds, $retry_after_seconds );
}

/**
 * Schreibt API-Fehler knapp ins PHP-Log.
 *
 * @param array<string, mixed> $context Kontext fuer die Diagnose.
 */
function hp_brevo_log( string $message, array $context = [] ): void {
	if ( empty( $context ) ) {
		error_log( '[Hasimuener Brevo] ' . $message );
		return;
	}

	$encoded_context = wp_json_encode( $context );
	error_log( '[Hasimuener Brevo] ' . $message . ' ' . ( false !== $encoded_context ? $encoded_context : '' ) );
}

/**
 * Fuehrt einen authentifizierten API-Request gegen Brevo aus.
 *
 * @param string               $method  HTTP-Methode.
 * @param string               $path    API-Pfad relativ zu /v3.
 * @param array<string, mixed> $payload JSON-Payload.
 * @return array{success:bool,status_code:int,body:array<string,mixed>,error:string,retry_after:int}
 */
function hp_brevo_request( string $method, string $path, array $payload = [] ): array {
	if ( ! hp_brevo_has_required_config() ) {
		return [
			'success'     => false,
			'status_code' => 0,
			'body'        => [],
			'error'       => 'missing_config',
			'retry_after' => 0,
		];
	}

	if ( hp_brevo_is_rate_limited() ) {
		return [
			'success'     => false,
			'status_code' => 429,
			'body'        => [],
			'error'       => 'rate_limited',
			'retry_after' => 0,
		];
	}

	$args = [
		'method'      => strtoupper( $method ),
		'timeout'     => 20,
		'data_format' => 'body',
		'headers'     => [
			'accept'       => 'application/json',
			'api-key'      => hp_brevo_get_api_key(),
			'content-type' => 'application/json',
		],
	];

	if ( ! empty( $payload ) ) {
		$args['body'] = wp_json_encode( $payload );
	}

	$response = wp_remote_request( 'https://api.brevo.com/v3' . $path, $args );

	if ( is_wp_error( $response ) ) {
		hp_brevo_log(
			'request_failed',
			[
				'path'  => $path,
				'error' => $response->get_error_message(),
			]
		);

		return [
			'success'     => false,
			'status_code' => 0,
			'body'        => [],
			'error'       => $response->get_error_message(),
			'retry_after' => 0,
		];
	}

	$status_code = (int) wp_remote_retrieve_response_code( $response );
	$retry_after = (int) wp_remote_retrieve_header( $response, 'retry-after' );
	$body_raw    = (string) wp_remote_retrieve_body( $response );
	$body        = json_decode( $body_raw, true );

	if ( ! is_array( $body ) ) {
		$body = [];
	}

	if ( 429 === $status_code ) {
		hp_brevo_mark_rate_limited( $retry_after > 0 ? $retry_after : 60 );
	}

	if ( $status_code >= 200 && $status_code < 300 ) {
		return [
			'success'     => true,
			'status_code' => $status_code,
			'body'        => $body,
			'error'       => '',
			'retry_after' => $retry_after,
		];
	}

	$error_message = isset( $body['message'] ) && is_string( $body['message'] )
		? $body['message']
		: 'brevo_api_error';

	hp_brevo_log(
		'api_error',
		[
			'path'        => $path,
			'status_code' => $status_code,
			'error'       => $error_message,
		]
	);

	return [
		'success'     => false,
		'status_code' => $status_code,
		'body'        => $body,
		'error'       => $error_message,
		'retry_after' => $retry_after,
	];
}

/**
 * Bereitet Template-Parameter fuer JSON vor.
 *
 * @param array<string, mixed> $params Parameter.
 * @return array<string, mixed>
 */
function hp_brevo_prepare_template_params( array $params ): array {
	$prepared = [];

	foreach ( $params as $key => $value ) {
		$prepared_key = sanitize_key( (string) $key );

		if ( '' === $prepared_key ) {
			continue;
		}

		if ( is_array( $value ) ) {
			$prepared[ $prepared_key ] = hp_brevo_prepare_template_params( $value );
			continue;
		}

		if ( is_bool( $value ) || is_int( $value ) || is_float( $value ) || null === $value ) {
			$prepared[ $prepared_key ] = $value;
			continue;
		}

		if ( is_scalar( $value ) ) {
			$prepared[ $prepared_key ] = (string) $value;
		}
	}

	return $prepared;
}

/**
 * Normalisiert Empfaengerangaben.
 *
 * @param array<int, array<string, mixed>> $recipients Empfaenger.
 * @return array<int, array<string, string>>
 */
function hp_brevo_normalize_recipients( array $recipients ): array {
	$normalized = [];

	foreach ( $recipients as $recipient ) {
		$email = isset( $recipient['email'] ) ? sanitize_email( (string) $recipient['email'] ) : '';

		if ( '' === $email || ! is_email( $email ) ) {
			continue;
		}

		$name = isset( $recipient['name'] ) ? trim( preg_replace( '/[\r\n]+/', ' ', (string) $recipient['name'] ) ) : '';

		$normalized[] = [
			'email' => $email,
			'name'  => $name,
		];
	}

	return $normalized;
}

/**
 * Vereinheitlicht Tags.
 *
 * @param array<int, mixed> $tags Roh-Tags.
 * @return array<int, string>
 */
function hp_brevo_normalize_tags( array $tags ): array {
	$tags = array_map( 'strval', $tags );
	$tags = array_map( 'sanitize_key', $tags );
	$tags = array_filter(
		$tags,
		static function ( string $tag ): bool {
			return '' !== $tag;
		}
	);

	return array_values( array_unique( $tags ) );
}

/**
 * Versendet eine transaktionale E-Mail ueber Brevo.
 *
 * @param array<string, mixed> $args Versanddaten.
 * @return array{success:bool,message_id:string,error:string,status_code:int}
 */
function hp_brevo_send_transactional_email( array $args ): array {
	$recipients = [];

	if ( isset( $args['to'] ) && is_array( $args['to'] ) ) {
		$recipients = hp_brevo_normalize_recipients( $args['to'] );
	} elseif ( ! empty( $args['to_email'] ) ) {
		$recipients = hp_brevo_normalize_recipients(
			[
				[
					'email' => (string) $args['to_email'],
					'name'  => isset( $args['to_name'] ) ? (string) $args['to_name'] : '',
				],
			]
		);
	}

	if ( empty( $recipients ) ) {
		return [
			'success'     => false,
			'message_id'  => '',
			'error'       => 'missing_recipient',
			'status_code' => 0,
		];
	}

	$template_id = isset( $args['template_id'] ) ? (int) $args['template_id'] : 0;

	if ( $template_id <= 0 && ! empty( $args['template_key'] ) ) {
		$template_id = hp_brevo_get_template_id( (string) $args['template_key'] );
	}

	$payload = [
		'sender' => [
			'email' => hp_brevo_get_from_email(),
			'name'  => hp_brevo_get_from_name(),
		],
		'to'     => $recipients,
	];

	if ( ! empty( $args['reply_to_email'] ) ) {
		$reply_to_email = sanitize_email( (string) $args['reply_to_email'] );

		if ( '' !== $reply_to_email && is_email( $reply_to_email ) ) {
			$payload['replyTo'] = [
				'email' => $reply_to_email,
				'name'  => ! empty( $args['reply_to_name'] ) ? trim( preg_replace( '/[\r\n]+/', ' ', (string) $args['reply_to_name'] ) ) : $reply_to_email,
			];
		}
	}

	if ( ! empty( $args['subject'] ) ) {
		$payload['subject'] = trim( (string) $args['subject'] );
	}

	if ( $template_id > 0 ) {
		$payload['templateId'] = $template_id;

		if ( ! empty( $args['params'] ) && is_array( $args['params'] ) ) {
			$payload['params'] = hp_brevo_prepare_template_params( $args['params'] );
		}
	} else {
		$html_content = isset( $args['html_content'] ) ? trim( (string) $args['html_content'] ) : '';
		$text_content = isset( $args['text_content'] ) ? trim( (string) $args['text_content'] ) : '';

		if ( '' === (string) ( $payload['subject'] ?? '' ) || ( '' === $html_content && '' === $text_content ) ) {
			return [
				'success'     => false,
				'message_id'  => '',
				'error'       => 'missing_message_content',
				'status_code' => 0,
			];
		}

		if ( '' !== $html_content ) {
			$payload['htmlContent'] = $html_content;
		}

		if ( '' !== $text_content ) {
			$payload['textContent'] = $text_content;
		}
	}

	if ( ! empty( $args['tags'] ) && is_array( $args['tags'] ) ) {
		$tags = hp_brevo_normalize_tags( $args['tags'] );

		if ( ! empty( $tags ) ) {
			$payload['tags'] = $tags;
		}
	}

	$response = hp_brevo_request( 'POST', '/smtp/email', $payload );

	return [
		'success'     => ! empty( $response['success'] ),
		'message_id'  => isset( $response['body']['messageId'] ) && is_string( $response['body']['messageId'] ) ? $response['body']['messageId'] : '',
		'error'       => (string) ( $response['error'] ?? '' ),
		'status_code' => (int) ( $response['status_code'] ?? 0 ),
	];
}

/**
 * Teilt einen Vollnamen in Vor- und Nachname fuer Brevo-Standardattribute.
 *
 * @return array{first_name:string,last_name:string}
 */
function hp_brevo_split_name( string $full_name ): array {
	$full_name = trim( preg_replace( '/\s+/', ' ', $full_name ) );

	if ( '' === $full_name ) {
		return [
			'first_name' => '',
			'last_name'  => '',
		];
	}

	$parts = preg_split( '/\s+/', $full_name );

	if ( ! is_array( $parts ) || empty( $parts ) ) {
		return [
			'first_name' => $full_name,
			'last_name'  => '',
		];
	}

	if ( 1 === count( $parts ) ) {
		return [
			'first_name' => (string) $parts[0],
			'last_name'  => '',
		];
	}

	$last_name = (string) array_pop( $parts );

	return [
		'first_name' => trim( implode( ' ', $parts ) ),
		'last_name'  => $last_name,
	];
}

/**
 * Erstellt bzw. aktualisiert einen Kontakt in Brevo.
 *
 * @param array<string, mixed> $args Kontaktdaten.
 * @return array{success:bool,error:string,status_code:int}
 */
function hp_brevo_upsert_contact( array $args ): array {
	$email = isset( $args['email'] ) ? sanitize_email( (string) $args['email'] ) : '';

	if ( '' === $email || ! is_email( $email ) ) {
		return [
			'success'     => false,
			'error'       => 'invalid_email',
			'status_code' => 0,
		];
	}

	$payload = [
		'email'         => $email,
		'updateEnabled' => true,
	];

	if ( ! empty( $args['attributes'] ) && is_array( $args['attributes'] ) ) {
		$payload['attributes'] = hp_brevo_prepare_template_params( $args['attributes'] );
	}

	if ( ! empty( $args['list_ids'] ) && is_array( $args['list_ids'] ) ) {
		$list_ids = array_values(
			array_filter(
				array_map( 'intval', $args['list_ids'] ),
				static function ( int $list_id ): bool {
					return $list_id > 0;
				}
			)
		);

		if ( ! empty( $list_ids ) ) {
			$payload['listIds'] = $list_ids;
		}
	}

	$response = hp_brevo_request( 'POST', '/contacts', $payload );

	return [
		'success'     => ! empty( $response['success'] ),
		'error'       => (string) ( $response['error'] ?? '' ),
		'status_code' => (int) ( $response['status_code'] ?? 0 ),
	];
}

/**
 * Synchronisiert eine Kontaktanfrage als Brevo-Kontakt ohne Marketing-Listen.
 *
 * @param array<string, string> $fields Formularfelder.
 * @return array{success:bool,error:string,status_code:int}
 */
function hp_brevo_sync_contact_submission( array $fields ): array {
	$name_parts = hp_brevo_split_name( (string) ( $fields['name'] ?? '' ) );
	$attributes = [];

	if ( '' !== $name_parts['first_name'] ) {
		$attributes['FIRSTNAME'] = $name_parts['first_name'];
	}

	if ( '' !== $name_parts['last_name'] ) {
		$attributes['LASTNAME'] = $name_parts['last_name'];
	}

	return hp_brevo_upsert_contact(
		[
			'email'      => (string) ( $fields['email'] ?? '' ),
			'attributes' => $attributes,
		]
	);
}

/**
 * Synchronisiert einen bestaetigten Newsletter-Kontakt optional in die Brevo-Liste.
 *
 * @param array<string, string> $subscriber Datensatz.
 * @return array{success:bool,error:string,status_code:int}
 */
function hp_brevo_sync_newsletter_contact( array $subscriber ): array {
	$list_id  = hp_brevo_get_contact_list_id();
	$list_ids = $list_id > 0 ? [ $list_id ] : [];

	return hp_brevo_upsert_contact(
		[
			'email'    => (string) ( $subscriber['email'] ?? '' ),
			'list_ids' => $list_ids,
		]
	);
}

/**
 * Entfernt einen Newsletter-Kontakt serverseitig aus der optionalen Brevo-Liste.
 *
 * @return array{success:bool,error:string,status_code:int}
 */
function hp_brevo_unsubscribe_newsletter_contact( string $email ): array {
	$email = sanitize_email( $email );

	if ( '' === $email || ! is_email( $email ) ) {
		return [
			'success'     => false,
			'error'       => 'invalid_email',
			'status_code' => 0,
		];
	}

	$payload = [
		'emailBlacklisted' => true,
	];

	$list_id = hp_brevo_get_contact_list_id();

	if ( $list_id > 0 ) {
		$payload['unlinkListIds'] = [ $list_id ];
	}

	$response = hp_brevo_request( 'PUT', '/contacts/' . rawurlencode( $email ), $payload );

	return [
		'success'     => ! empty( $response['success'] ),
		'error'       => (string) ( $response['error'] ?? '' ),
		'status_code' => (int) ( $response['status_code'] ?? 0 ),
	];
}

/**
 * Registriert einen admin-geschuetzten Test-Endpunkt fuer Brevo.
 */
function hp_brevo_register_test_route(): void {
	register_rest_route(
		'hp/v1',
		'/brevo/test-email',
		[
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'hp_brevo_handle_test_email_request',
			'permission_callback' => static function (): bool {
				return current_user_can( 'manage_options' );
			},
			'args'                => [
				'email' => [
					'type'     => 'string',
					'required' => false,
				],
			],
		]
	);
}
add_action( 'rest_api_init', 'hp_brevo_register_test_route' );

/**
 * Sendet eine Testmail ueber den zentralen Brevo-Service.
 */
function hp_brevo_handle_test_email_request( WP_REST_Request $request ): WP_REST_Response {
	$email = sanitize_email( (string) $request->get_param( 'email' ) );

	if ( '' === $email ) {
		$current_user = wp_get_current_user();
		$email        = is_email( $current_user->user_email ) ? $current_user->user_email : hp_brevo_get_from_email();
	}

	if ( '' === $email || ! is_email( $email ) ) {
		return new WP_REST_Response(
			[
				'success' => false,
				'error'   => 'invalid_email',
			],
			400
		);
	}

	$result = hp_brevo_send_transactional_email(
		[
			'to'           => [
				[
					'email' => $email,
					'name'  => '',
				],
			],
			'subject'      => 'Brevo Test - hasimuener.org',
			'html_content' => '<p>Dies ist eine serverseitige Testmail aus dem zentralen Brevo-Service von hasimuener.org.</p>',
			'text_content' => 'Dies ist eine serverseitige Testmail aus dem zentralen Brevo-Service von hasimuener.org.',
			'tags'         => [ 'brevo-test' ],
		]
	);

	$status_code = ! empty( $result['success'] )
		? 200
		: ( 429 === (int) $result['status_code'] ? 429 : 500 );

	return new WP_REST_Response(
		[
			'success'    => ! empty( $result['success'] ),
			'message_id' => (string) ( $result['message_id'] ?? '' ),
			'error'      => (string) ( $result['error'] ?? '' ),
			'email'      => $email,
		],
		$status_code
	);
}
