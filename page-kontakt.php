<?php
/**
 * Template Name: Kontakt
 *
 * Template: Kontakt
 *
 * Native Kontaktseite mit E-Mail-Option und reduziertem Formular.
 * Die Seite wird, falls sie im System fehlt, automatisch angelegt.
 *
 * @package Hasimuener_Journal
 * @since   6.3.0
 */

get_header();

$hp_contact_flash   = hp_consume_contact_flash();
$hp_contact_status  = isset( $hp_contact_flash['status'] ) ? (string) $hp_contact_flash['status'] : '';
$hp_contact_message = isset( $hp_contact_flash['message'] ) ? (string) $hp_contact_flash['message'] : '';
$hp_contact_fields  = isset( $hp_contact_flash['fields'] ) && is_array( $hp_contact_flash['fields'] ) ? $hp_contact_flash['fields'] : [];

$hp_name_value    = isset( $hp_contact_fields['name'] ) ? (string) $hp_contact_fields['name'] : '';
$hp_email_value   = isset( $hp_contact_fields['email'] ) ? (string) $hp_contact_fields['email'] : '';
$hp_subject_value = isset( $hp_contact_fields['subject'] ) ? (string) $hp_contact_fields['subject'] : '';
$hp_message_value = isset( $hp_contact_fields['message'] ) ? (string) $hp_contact_fields['message'] : '';

$hp_contact_email        = hp_get_contact_email();
$hp_contact_email_label  = antispambot( $hp_contact_email );
$hp_contact_mailto       = 'mailto:' . $hp_contact_email;
$hp_essay_url            = get_post_type_archive_link( 'essay' );
$hp_note_url             = get_post_type_archive_link( 'note' );
$hp_privacy_url          = get_privacy_policy_url();
$hp_rendered_at          = time();
$hp_render_token         = hp_get_contact_form_render_token( $hp_rendered_at );
?>

<main id="main-content" class="hp-contact">
	<div class="hp-contact__inner">

		<header class="hp-contact__header">
			<span class="hp-kicker">Kontakt</span>
			<h1 class="hp-contact__title"><?php the_title(); ?></h1>
			<p class="hp-contact__subline">Für Hinweise, Fragen, Anfragen oder begründeten Widerspruch. Öffentliche Einwände gehören meist unter die Texte; für Vertrauliches gibt es diese Seite.</p>
		</header>

		<section class="hp-contact__channels" aria-label="Kontaktwege">
			<a class="hp-contact__channel" href="<?php echo esc_url( $hp_contact_mailto ); ?>">
				<span class="hp-contact__channel-label">Direkt per E-Mail</span>
				<strong class="hp-contact__channel-title"><?php echo wp_kses_post( $hp_contact_email_label ); ?></strong>
				<span class="hp-contact__channel-copy">Für direkte Rückfragen, Hinweise und Anfragen ohne Umweg.</span>
			</a>

			<a class="hp-contact__channel" href="<?php echo esc_url( $hp_essay_url ); ?>">
				<span class="hp-contact__channel-label">Öffentliche Diskussion</span>
				<strong class="hp-contact__channel-title">Im Journal antworten</strong>
				<span class="hp-contact__channel-copy">Wenn es um Argumente, Ergänzungen oder Widerspruch zu einem Text geht, ist der Kommentar unter Essays oder Notizen meist der passendere Ort.</span>
			</a>

			<a class="hp-contact__channel" href="#kontakt-formular">
				<span class="hp-contact__channel-label">Vertraulich schreiben</span>
				<strong class="hp-contact__channel-title">Formular öffnen</strong>
				<span class="hp-contact__channel-copy">Für Nachrichten, die nicht öffentlich erscheinen sollen und direkt als E-Mail ankommen.</span>
			</a>
		</section>

		<div class="hp-contact__layout">
			<aside class="hp-contact__aside" aria-label="Hinweise zur Kontaktaufnahme">
				<h2 class="hp-contact__aside-title">Welcher Weg ist der richtige?</h2>
				<p>Öffentliche Einwände, Nachfragen und Ergänzungen sind im Journal selbst oft produktiver, weil sie das Gespräch sichtbar machen.</p>
				<p>Private Hinweise, Anfragen, Einladungen oder persönliche Rückmeldungen gehören eher hierher.</p>
				<p>Das Formular ist bewusst schlicht gehalten: keine CRM-Strecke, kein Newsletter-Opt-in, keine unnötigen Pflichtfelder.</p>
				<div class="hp-contact__aside-links">
					<a href="<?php echo esc_url( $hp_essay_url ); ?>">Zu den Essays</a>
					<a href="<?php echo esc_url( $hp_note_url ); ?>">Zu den Notizen</a>
				</div>
			</aside>

			<section class="hp-contact__form-shell" aria-labelledby="kontakt-formular-title">
				<div class="hp-contact__form-header">
					<p class="hp-contact__form-eyebrow">Nachricht senden</p>
					<h2 id="kontakt-formular-title" class="hp-contact__form-title">Direkter Kontakt ohne Plugin-Overhead.</h2>
					<p class="hp-contact__form-lede">Deine Nachricht wird nicht auf der Website veröffentlicht, sondern direkt per E-Mail weitergeleitet und intern in der Website-Verwaltung dokumentiert.</p>
				</div>

				<?php if ( '' !== $hp_contact_message ) : ?>
					<div class="hp-contact__notice hp-contact__notice--<?php echo 'success' === $hp_contact_status ? 'success' : 'error'; ?>" aria-live="polite">
						<p><?php echo esc_html( $hp_contact_message ); ?></p>
					</div>
				<?php endif; ?>

				<form id="kontakt-formular" class="hp-contact__form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
					<input type="hidden" name="action" value="hp_send_contact">
					<input type="hidden" name="hp_contact_rendered_at" value="<?php echo esc_attr( (string) $hp_rendered_at ); ?>">
					<input type="hidden" name="hp_contact_render_token" value="<?php echo esc_attr( $hp_render_token ); ?>">
					<?php wp_nonce_field( 'hp_contact_submit', 'hp_contact_nonce' ); ?>

					<div class="hp-contact__honeypot" aria-hidden="true">
						<label for="hp-contact-website">Website</label>
						<input id="hp-contact-website" type="text" name="hp_contact_website" value="" tabindex="-1" autocomplete="off">
					</div>

					<p class="hp-contact__field">
						<label for="hp-contact-name">Name</label>
						<input id="hp-contact-name" name="hp_contact_name" type="text" maxlength="120" autocomplete="name" value="<?php echo esc_attr( $hp_name_value ); ?>" required>
					</p>

					<p class="hp-contact__field">
						<label for="hp-contact-email">E-Mail</label>
						<input id="hp-contact-email" name="hp_contact_email" type="email" maxlength="190" autocomplete="email" value="<?php echo esc_attr( $hp_email_value ); ?>" required>
					</p>

					<p class="hp-contact__field hp-contact__field--full">
						<label for="hp-contact-subject">Betreff <span class="hp-contact__field-optional">optional</span></label>
						<input id="hp-contact-subject" name="hp_contact_subject" type="text" maxlength="160" value="<?php echo esc_attr( $hp_subject_value ); ?>">
					</p>

					<p class="hp-contact__field hp-contact__field--full">
						<label for="hp-contact-message">Nachricht</label>
						<textarea id="hp-contact-message" name="hp_contact_message" rows="10" maxlength="8000" required><?php echo esc_textarea( $hp_message_value ); ?></textarea>
					</p>

					<p class="hp-contact__privacy">
						Mit dem Absenden wird deine Nachricht ausschließlich zur Bearbeitung deiner Anfrage verarbeitet.
						<?php if ( $hp_privacy_url ) : ?>
							Mehr in der <a href="<?php echo esc_url( $hp_privacy_url ); ?>">Datenschutzerklärung</a>.
						<?php endif; ?>
					</p>

					<div class="hp-contact__actions">
						<button class="hp-contact__submit" type="submit">Nachricht senden</button>
						<a class="hp-contact__mail-link" href="<?php echo esc_url( $hp_contact_mailto ); ?>">Lieber direkt mailen</a>
					</div>
				</form>
			</section>
		</div>
	</div>
</main>

<?php get_footer(); ?>
