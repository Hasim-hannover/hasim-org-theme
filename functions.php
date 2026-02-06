<?php
add_action( 'wp_enqueue_scripts', 'hp_journal_enqueue_styles' );
function hp_journal_enqueue_styles() {
    // Lädt das Eltern-Theme
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    
    // Lädt dein Child-Theme mit einem Zeitstempel (Cache-Buster)
    wp_enqueue_style( 'child-style', 
        get_stylesheet_directory_uri() . '/style.css', 
        array('parent-style'), 
        time() // Das hier zwingt den Browser zum Neuladen!
    );
}