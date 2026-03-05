<?php
/**
 * Template: Wissensgraph
 *
 * Interaktive D3.js-Visualisierung aller Verbindungen zwischen
 * Essays, Notizen, Glossar-Einträgen und Themenfeldern.
 *
 * Setup: Im WP-Admin eine Seite „Wissensgraph" (Slug: wissensgraph)
 * anlegen. Das Template wird automatisch via Dateiname zugewiesen.
 *
 * @package Hasimuener_Journal
 * @since   6.0.0
 */

get_header(); ?>

<main id="main-content" class="hp-graph">

	<header class="hp-graph__header">
		<span class="hp-kicker">Wissensbasis</span>
		<h1 class="hp-graph__title">Wissensgraph</h1>
		<p class="hp-graph__desc">Alle Verbindungen zwischen Essays, Notizen, Glossar und Themenfeldern — interaktiv.</p>
	</header>

	<!-- Filter-Controls -->
	<div class="hp-graph__controls" role="toolbar" aria-label="Graph-Filter">
		<button class="hp-graph__filter hp-graph__filter--active" data-type="essay" type="button" aria-pressed="true">
			<span class="hp-graph__filter-dot hp-graph__filter-dot--essay" aria-hidden="true"></span>
			Essays
		</button>
		<button class="hp-graph__filter hp-graph__filter--active" data-type="note" type="button" aria-pressed="true">
			<span class="hp-graph__filter-dot hp-graph__filter-dot--note" aria-hidden="true"></span>
			Notizen
		</button>
		<button class="hp-graph__filter hp-graph__filter--active" data-type="glossar" type="button" aria-pressed="true">
			<span class="hp-graph__filter-dot hp-graph__filter-dot--glossar" aria-hidden="true"></span>
			Glossar
		</button>
		<button class="hp-graph__filter hp-graph__filter--active" data-type="topic" type="button" aria-pressed="true">
			<span class="hp-graph__filter-dot hp-graph__filter-dot--topic" aria-hidden="true"></span>
			Themenfelder
		</button>
	</div>

	<!-- Legende -->
	<div class="hp-graph__legend" role="img" aria-label="Legende: Farbcodes der Knotentypen">
		<div class="hp-graph__legend-item">
			<span class="hp-graph__legend-circle hp-graph__legend-circle--essay" aria-hidden="true"></span>
			<span>Essay</span>
		</div>
		<div class="hp-graph__legend-item">
			<span class="hp-graph__legend-circle hp-graph__legend-circle--note" aria-hidden="true"></span>
			<span>Notiz</span>
		</div>
		<div class="hp-graph__legend-item">
			<span class="hp-graph__legend-circle hp-graph__legend-circle--glossar" aria-hidden="true"></span>
			<span>Glossar</span>
		</div>
		<div class="hp-graph__legend-item">
			<span class="hp-graph__legend-circle hp-graph__legend-circle--topic" aria-hidden="true"></span>
			<span>Themenfeld</span>
		</div>
	</div>

	<!-- Zoom-Controls -->
	<div class="hp-graph__zoom" aria-label="Zoom-Steuerung">
		<button class="hp-graph__zoom-btn" id="hp-graph-zoom-in" type="button" aria-label="Hineinzoomen">+</button>
		<button class="hp-graph__zoom-btn" id="hp-graph-zoom-out" type="button" aria-label="Herauszoomen">−</button>
		<button class="hp-graph__zoom-btn" id="hp-graph-zoom-reset" type="button" aria-label="Zoom zurücksetzen">⟳</button>
	</div>

	<!-- Graph Container -->
	<div class="hp-graph__canvas" id="hp-graph-canvas" role="img" aria-label="Interaktiver Wissensgraph: Visualisiert Verbindungen zwischen Inhalten">
		<div class="hp-graph__loading" id="hp-graph-loading">
			<p>Graph wird geladen …</p>
		</div>
		<div class="hp-graph__error" id="hp-graph-error" hidden>
			<p>Der Graph konnte nicht geladen werden. Bitte später erneut versuchen.</p>
		</div>
	</div>

	<!-- Detail-Panel -->
	<aside class="hp-graph__detail" id="hp-graph-detail" hidden aria-live="polite">
		<button class="hp-graph__detail-close" id="hp-graph-detail-close" type="button" aria-label="Details schließen">&times;</button>
		<div class="hp-graph__detail-content" id="hp-graph-detail-content">
			<!-- Wird von JS befüllt -->
		</div>
	</aside>

</main>

<?php get_footer(); ?>
