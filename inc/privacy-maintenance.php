<?php
/**
 * Datenschutz-Wartung ohne Plugin.
 *
 * Regelt native Löschfristen und die tägliche Bereinigung
 * über WordPress-Cron.
 *
 * @package Hasimuener_Journal
 * @since   6.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Aufbewahrungsfrist für Kontaktanfragen im Admin.
 */
function hp_get_contact_submission_retention_days(): int {
	return 365;
}

/**
 * Aufbewahrungsfrist für unbestätigte Newsletter-Anmeldungen.
 */
function hp_get_newsletter_pending_retention_days(): int {
	return 30;
}

/**
 * Aufbewahrungsfrist für minimierte Sperrnotizen nach Austragung.
 */
function hp_get_newsletter_suppression_retention_days(): int {
	return 730;
}

/**
 * Plant die tägliche Datenschutz-Bereinigung.
 */
function hp_schedule_privacy_cleanup(): void {
	if ( ! wp_next_scheduled( 'hp_run_privacy_cleanup' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'hp_run_privacy_cleanup' );
	}
}
add_action( 'init', 'hp_schedule_privacy_cleanup', 40 );

/**
 * Führt alle Bereinigungen aus.
 */
function hp_run_privacy_cleanup(): void {
	if ( function_exists( 'hp_cleanup_newsletter_pending_subscribers' ) ) {
		hp_cleanup_newsletter_pending_subscribers();
	}

	if ( function_exists( 'hp_cleanup_newsletter_suppressions' ) ) {
		hp_cleanup_newsletter_suppressions();
	}

	if ( function_exists( 'hp_cleanup_contact_submissions' ) ) {
		hp_cleanup_contact_submissions();
	}
}
add_action( 'hp_run_privacy_cleanup', 'hp_run_privacy_cleanup' );
