<?php
/**
 * Admin-Bereich für Kontakte und Anfragen.
 *
 * Bündelt Newsletter-Abonnements und Kontaktanfragen
 * in einem sichtbaren Bereich des WordPress-Backends.
 *
 * @package Hasimuener_Journal
 * @since   6.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tabellenname für Kontaktanfragen.
 */
function hp_get_contact_submissions_table_name(): string {
	global $wpdb;

	return $wpdb->prefix . 'hp_contact_submissions';
}

/**
 * Versionskennung der Kontakttabelle.
 */
function hp_get_contact_submissions_db_version(): string {
	return '1.0.0';
}

/**
 * Installiert oder aktualisiert die Tabelle für Kontaktanfragen.
 */
function hp_maybe_install_contact_submissions_table(): void {
	$installed_version = (string) get_option( 'hp_contact_submissions_db_version', '' );

	if ( hp_get_contact_submissions_db_version() === $installed_version ) {
		return;
	}

	global $wpdb;

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$table_name      = hp_get_contact_submissions_table_name();
	$charset_collate = $wpdb->get_charset_collate();
	$sql             = "CREATE TABLE {$table_name} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		status varchar(20) NOT NULL DEFAULT 'new',
		name varchar(190) NOT NULL DEFAULT '',
		email varchar(190) NOT NULL DEFAULT '',
		subject varchar(190) NOT NULL DEFAULT '',
		message longtext NULL,
		source_url varchar(255) NOT NULL DEFAULT '',
		ip_hash char(64) NOT NULL DEFAULT '',
		user_agent_hash char(64) NOT NULL DEFAULT '',
		mail_sent tinyint(1) unsigned NOT NULL DEFAULT 0,
		autoresponse_sent tinyint(1) unsigned NOT NULL DEFAULT 0,
		created_at datetime NOT NULL,
		updated_at datetime NOT NULL,
		PRIMARY KEY  (id),
		KEY status (status),
		KEY created_at (created_at),
		KEY email (email)
	) {$charset_collate};";

	dbDelta( $sql );

	update_option( 'hp_contact_submissions_db_version', hp_get_contact_submissions_db_version(), false );
}
add_action( 'init', 'hp_maybe_install_contact_submissions_table', 26 );

/**
 * Speichert eine Kontaktanfrage lokal.
 *
 * @param array<string, string> $fields Validierte Formularwerte.
 */
function hp_store_contact_submission( array $fields, bool $mail_sent, bool $autoresponse_sent ): bool {
	global $wpdb;

	$table_name    = hp_get_contact_submissions_table_name();
	$source_url    = '';
	$request_uri   = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$remote_addr   = isset( $_SERVER['REMOTE_ADDR'] ) ? trim( (string) wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$user_agent    = isset( $_SERVER['HTTP_USER_AGENT'] ) ? trim( (string) wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

	if ( '' !== $request_uri && '/' === $request_uri[0] ) {
		$source_url = home_url( $request_uri );
	}

	$now = current_time( 'mysql' );

	$inserted = $wpdb->insert(
		$table_name,
		[
			'status'            => 'new',
			'name'              => (string) ( $fields['name'] ?? '' ),
			'email'             => strtolower( trim( (string) ( $fields['email'] ?? '' ) ) ),
			'subject'           => (string) ( $fields['subject'] ?? '' ),
			'message'           => (string) ( $fields['message'] ?? '' ),
			'source_url'        => $source_url,
			'ip_hash'           => '' !== $remote_addr ? hash( 'sha256', $remote_addr ) : '',
			'user_agent_hash'   => '' !== $user_agent ? hash( 'sha256', $user_agent ) : '',
			'mail_sent'         => $mail_sent ? 1 : 0,
			'autoresponse_sent' => $autoresponse_sent ? 1 : 0,
			'created_at'        => $now,
			'updated_at'        => $now,
		],
		[
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%s',
			'%s',
		]
	);

	return false !== $inserted;
}

/**
 * Zählt Kontaktanfragen nach Status.
 *
 * @return array{new:int,read:int,total:int}
 */
function hp_get_contact_submission_counts(): array {
	global $wpdb;

	$table_name = hp_get_contact_submissions_table_name();
	$rows       = $wpdb->get_results( "SELECT status, COUNT(*) AS total FROM {$table_name} GROUP BY status", ARRAY_A );
	$counts     = [
		'new'   => 0,
		'read'  => 0,
		'total' => 0,
	];

	if ( ! is_array( $rows ) ) {
		return $counts;
	}

	foreach ( $rows as $row ) {
		$status = isset( $row['status'] ) ? (string) $row['status'] : '';
		$total  = isset( $row['total'] ) ? (int) $row['total'] : 0;

		if ( isset( $counts[ $status ] ) ) {
			$counts[ $status ] = $total;
			$counts['total']  += $total;
		}
	}

	return $counts;
}

/**
 * Lädt eine Kontaktanfrage.
 *
 * @return array<string, string>|null
 */
function hp_get_contact_submission( int $submission_id ): ?array {
	global $wpdb;

	if ( $submission_id <= 0 ) {
		return null;
	}

	$table_name = hp_get_contact_submissions_table_name();
	$row        = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE id = %d LIMIT 1",
			$submission_id
		),
		ARRAY_A
	);

	return is_array( $row ) ? array_map( 'strval', $row ) : null;
}

/**
 * Lädt die letzten Kontaktanfragen.
 *
 * @return array<int, array<string, string>>
 */
function hp_get_recent_contact_submissions( int $limit = 40, string $search = '' ): array {
	global $wpdb;

	$table_name = hp_get_contact_submissions_table_name();
	$limit      = max( 1, min( 100, $limit ) );
	$search     = trim( $search );
	$sql        = "SELECT id, status, name, email, subject, message, mail_sent, autoresponse_sent, created_at
		FROM {$table_name}
		WHERE 1=1";
	$params     = [];

	if ( '' !== $search ) {
		$like      = '%' . $wpdb->esc_like( $search ) . '%';
		$sql      .= ' AND (name LIKE %s OR email LIKE %s OR subject LIKE %s OR message LIKE %s)';
		$params[]  = $like;
		$params[]  = $like;
		$params[]  = $like;
		$params[]  = $like;
	}

	$sql      .= ' ORDER BY created_at DESC LIMIT %d';
	$params[]  = $limit;
	$query     = $wpdb->prepare( $sql, ...$params );
	$rows      = $wpdb->get_results( $query, ARRAY_A );

	if ( ! is_array( $rows ) ) {
		return [];
	}

	return array_map(
		static function ( array $row ): array {
			return array_map( 'strval', $row );
		},
		$rows
	);
}

/**
 * Markiert eine Kontaktanfrage als gelesen.
 */
function hp_mark_contact_submission_as_read( int $submission_id ): bool {
	global $wpdb;

	if ( $submission_id <= 0 ) {
		return false;
	}

	$table_name = hp_get_contact_submissions_table_name();
	$result     = $wpdb->update(
		$table_name,
		[
			'status'     => 'read',
			'updated_at' => current_time( 'mysql' ),
		],
		[ 'id' => $submission_id ],
		[ '%s', '%s' ],
		[ '%d' ]
	);

	return false !== $result;
}

/**
 * Registriert den Top-Level-Adminbereich.
 */
function hp_register_contacts_admin_menu(): void {
	add_menu_page(
		'Kontakte',
		'Kontakte',
		'manage_options',
		'hp-contacts',
		'hp_render_contacts_overview_page',
		'dashicons-email-alt2',
		56
	);

	add_submenu_page(
		'hp-contacts',
		'Übersicht',
		'Übersicht',
		'manage_options',
		'hp-contacts',
		'hp_render_contacts_overview_page'
	);

	add_submenu_page(
		'hp-contacts',
		'Kontaktanfragen',
		'Anfragen',
		'manage_options',
		'hp-contact-submissions',
		'hp_render_contact_submissions_page'
	);
}
add_action( 'admin_menu', 'hp_register_contacts_admin_menu', 9 );

/**
 * Exportiert Kontaktanfragen als CSV.
 */
function hp_export_contact_submissions(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sie haben nicht die erforderlichen Rechte.', 'hasimuener-journal' ) );
	}

	check_admin_referer( 'hp_contact_submissions_export' );

	global $wpdb;

	$table_name = hp_get_contact_submissions_table_name();
	$rows       = $wpdb->get_results(
		"SELECT id, status, name, email, subject, message, source_url, mail_sent, autoresponse_sent, created_at
		FROM {$table_name}
		ORDER BY created_at DESC",
		ARRAY_A
	);

	if ( ! is_array( $rows ) ) {
		$rows = [];
	}

	nocache_headers();
	header( 'Content-Type: text/csv; charset=UTF-8' );
	header( 'Content-Disposition: attachment; filename="hasimuener-kontaktanfragen-' . gmdate( 'Y-m-d' ) . '.csv"' );

	$output = fopen( 'php://output', 'w' );

	if ( false === $output ) {
		exit;
	}

	fprintf( $output, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );
	fputcsv( $output, [ 'id', 'status', 'name', 'email', 'subject', 'message', 'source_url', 'mail_sent', 'autoresponse_sent', 'created_at' ] );

	foreach ( $rows as $row ) {
		fputcsv(
			$output,
			[
				(string) ( $row['id'] ?? '' ),
				(string) ( $row['status'] ?? '' ),
				(string) ( $row['name'] ?? '' ),
				(string) ( $row['email'] ?? '' ),
				(string) ( $row['subject'] ?? '' ),
				(string) ( $row['message'] ?? '' ),
				(string) ( $row['source_url'] ?? '' ),
				(string) ( $row['mail_sent'] ?? '' ),
				(string) ( $row['autoresponse_sent'] ?? '' ),
				(string) ( $row['created_at'] ?? '' ),
			]
		);
	}

	fclose( $output );
	exit;
}
add_action( 'admin_post_hp_export_contact_submissions', 'hp_export_contact_submissions' );

/**
 * Übersicht der Kontakte.
 */
function hp_render_contacts_overview_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$newsletter_counts = function_exists( 'hp_get_newsletter_admin_counts' )
		? hp_get_newsletter_admin_counts()
		: [ 'active' => 0, 'pending' => 0, 'unsubscribed' => 0, 'total' => 0 ];
	$contact_counts = hp_get_contact_submission_counts();
	?>
	<div class="wrap">
		<h1>Kontakte</h1>
		<p>Hier laufen die bestätigten Newsletter-Abonnements und die über das Kontaktformular eingegangenen Anfragen zusammen.</p>

		<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;max-width:980px;margin:24px 0;">
			<div style="background:#fff;border:1px solid #dcdcde;border-radius:12px;padding:20px;">
				<h2 style="margin-top:0;">Newsletter</h2>
				<p style="margin:0 0 8px;"><strong><?php echo esc_html( (string) $newsletter_counts['active'] ); ?></strong> aktiv bestätigt</p>
				<p style="margin:0 0 8px;"><strong><?php echo esc_html( (string) $newsletter_counts['pending'] ); ?></strong> warten auf Bestätigung</p>
				<p style="margin:0;"><a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=hp-newsletter' ) ); ?>">Zum Newsletter</a></p>
			</div>

			<div style="background:#fff;border:1px solid #dcdcde;border-radius:12px;padding:20px;">
				<h2 style="margin-top:0;">Kontaktanfragen</h2>
				<p style="margin:0 0 8px;"><strong><?php echo esc_html( (string) $contact_counts['new'] ); ?></strong> neu</p>
				<p style="margin:0 0 8px;"><strong><?php echo esc_html( (string) $contact_counts['total'] ); ?></strong> insgesamt</p>
				<p style="margin:0;"><a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=hp-contact-submissions' ) ); ?>">Zu den Anfragen</a></p>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Rendert die Anfragen-Seite.
 */
function hp_render_contact_submissions_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$submission_id = isset( $_GET['submission'] ) ? absint( $_GET['submission'] ) : 0;
	$search        = isset( $_GET['s'] ) ? sanitize_text_field( (string) wp_unslash( $_GET['s'] ) ) : '';
	$submission    = $submission_id > 0 ? hp_get_contact_submission( $submission_id ) : null;

	if ( $submission && 'new' === $submission['status'] ) {
		hp_mark_contact_submission_as_read( (int) $submission['id'] );
		$submission = hp_get_contact_submission( (int) $submission['id'] );
	}

	$submissions = hp_get_recent_contact_submissions( 80, $search );
	$export_url  = wp_nonce_url(
		add_query_arg(
			[
				'action' => 'hp_export_contact_submissions',
			],
			admin_url( 'admin-post.php' )
		),
		'hp_contact_submissions_export'
	);
	?>
	<div class="wrap">
		<h1>Kontaktanfragen</h1>
		<p>Die Nachrichten werden zusätzlich zu den E-Mails intern gespeichert, damit du sie im Admin durchsuchen und nachverfolgen kannst.</p>

		<p><a class="button button-secondary" href="<?php echo esc_url( $export_url ); ?>">Alle Anfragen als CSV exportieren</a></p>

		<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display:flex;flex-wrap:wrap;gap:10px;align-items:end;max-width:980px;margin:20px 0;">
			<input type="hidden" name="page" value="hp-contact-submissions">
			<p style="margin:0;min-width:320px;flex:1 1 420px;">
				<label for="hp-contact-search" style="display:block;font-weight:600;margin-bottom:6px;">Suche</label>
				<input id="hp-contact-search" class="regular-text" type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="Name, E-Mail, Betreff oder Text">
			</p>
			<p style="margin:0;">
				<button class="button button-secondary" type="submit">Suchen</button>
			</p>
		</form>

		<?php if ( $submission ) : ?>
			<div style="max-width:900px;background:#fff;border:1px solid #dcdcde;border-radius:12px;padding:20px;margin:20px 0 28px;">
				<h2 style="margin-top:0;">Anfrage von <?php echo esc_html( $submission['name'] ); ?></h2>
				<p><strong>E-Mail:</strong> <a href="mailto:<?php echo esc_attr( $submission['email'] ); ?>"><?php echo esc_html( $submission['email'] ); ?></a></p>
				<p><strong>Betreff:</strong> <?php echo '' !== $submission['subject'] ? esc_html( $submission['subject'] ) : 'Nicht angegeben'; ?></p>
				<p><strong>Eingang:</strong> <?php echo esc_html( $submission['created_at'] ); ?></p>
				<p><strong>Versand an dich:</strong> <?php echo '1' === $submission['mail_sent'] ? 'ja' : 'nein'; ?> | <strong>Bestätigung an Absender:</strong> <?php echo '1' === $submission['autoresponse_sent'] ? 'ja' : 'nein'; ?></p>
				<?php if ( '' !== $submission['source_url'] ) : ?>
					<p><strong>Quelle:</strong> <a href="<?php echo esc_url( $submission['source_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $submission['source_url'] ); ?></a></p>
				<?php endif; ?>
				<div style="margin-top:16px;padding:16px 18px;background:#f6f7f7;border-radius:10px;border:1px solid #e0e0e0;white-space:pre-wrap;"><?php echo esc_html( $submission['message'] ); ?></div>
			</div>
		<?php endif; ?>

		<table class="widefat striped">
			<thead>
				<tr>
					<th>Status</th>
					<th>Name</th>
					<th>E-Mail</th>
					<th>Betreff</th>
					<th>Eingang</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( $submissions ) : ?>
					<?php foreach ( $submissions as $row ) : ?>
						<tr>
							<td><?php echo esc_html( 'new' === $row['status'] ? 'neu' : 'gelesen' ); ?></td>
							<td><?php echo esc_html( $row['name'] ); ?></td>
							<td><a href="mailto:<?php echo esc_attr( $row['email'] ); ?>"><?php echo esc_html( $row['email'] ); ?></a></td>
							<td><?php echo '' !== $row['subject'] ? esc_html( $row['subject'] ) : '—'; ?></td>
							<td><?php echo esc_html( $row['created_at'] ); ?></td>
							<td><a class="button button-small" href="<?php echo esc_url( admin_url( 'admin.php?page=hp-contact-submissions&submission=' . absint( $row['id'] ) ) ); ?>">Ansehen</a></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="6">Keine Kontaktanfragen für diese Auswahl gefunden.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}
