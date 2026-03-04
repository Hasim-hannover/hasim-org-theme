<?php
/**
 * Suchformular — Hasimuener Journal
 *
 * Barrierefreies Suchformular mit aria-label.
 * Durchsucht Essays, Notizen und Glossar-Einträge.
 *
 * @package Hasimuener_Journal
 * @since   5.0.0
 */
?>

<form role="search" method="get" class="hp-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Im Journal suchen">
    <label class="hp-search-form__label" for="hp-search-input">
        <span class="screen-reader-text">Suche nach:</span>
    </label>
    <input
        type="search"
        id="hp-search-input"
        class="hp-search-form__input"
        placeholder="Essays, Notizen, Begriffe durchsuchen …"
        value="<?php echo get_search_query(); ?>"
        name="s"
    >
    <button type="submit" class="hp-search-form__submit" aria-label="Suche starten">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
    </button>
</form>
