<?php
/**
 * Kommentar-Template — Hasimuener Journal
 *
 * @package Hasimuener_Journal
 * @since   6.2.0
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}

$hp_comment_count = get_comments_number();
$hp_copy          = hp_get_comment_prompt_copy();
?>

<div id="comments" class="hp-comments__shell">
	<header class="hp-comments__intro">
		<p class="hp-comments__eyebrow"><?php echo esc_html( $hp_copy['eyebrow'] ); ?></p>
		<h2 class="hp-comments__headline"><?php echo esc_html( $hp_copy['headline'] ); ?></h2>
		<p class="hp-comments__lede"><?php echo esc_html( $hp_copy['lede'] ); ?></p>

		<div class="hp-comments__summary">
			<span class="hp-comments__count">
				<?php
				printf(
					/* translators: %s: number of comments */
					esc_html( _n( '%s Kommentar', '%s Kommentare', $hp_comment_count, 'default' ) ),
					number_format_i18n( $hp_comment_count )
				);
				?>
			</span>
			<span class="hp-comments__summary-note">Moderiert, sachlich, öffentlich.</span>
		</div>
	</header>

	<?php if ( have_comments() ) : ?>
		<ol class="comment-list">
			<?php
			wp_list_comments( [
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 56,
				'callback'    => 'hp_render_journal_comment',
			] );
			?>
		</ol>

		<?php the_comments_pagination( [
			'prev_text' => '&larr; Ältere Kommentare',
			'next_text' => 'Neuere Kommentare &rarr;',
		] ); ?>
	<?php else : ?>
		<div class="hp-comments__empty">
			<p><?php echo esc_html( $hp_copy['empty'] ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( ! comments_open() && $hp_comment_count > 0 ) : ?>
		<p class="hp-comments__closed">Die Diskussion ist geschlossen. Die vorhandenen Kommentare bleiben sichtbar.</p>
	<?php endif; ?>

	<?php if ( comments_open() ) : ?>
		<div class="hp-comments__form-wrap">
			<?php comment_form( hp_get_comment_form_args() ); ?>
		</div>
	<?php endif; ?>
</div>
