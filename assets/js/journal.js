/**
 * Hasimuener Journal — Frontend JS
 * 
 * 1. Inhaltsverzeichnis (TOC) aus H2/H3 generieren
 * 2. Footnote Smooth Scroll
 * 
 * Geladen nur auf Singles (essay, note, post).
 * Kein Framework. Kein Build-Step. ~2KB.
 *
 * @package Hasimuener_Journal
 * @version 2.0.0
 */

( function () {
    'use strict';

    /* =========================================
       1. TABLE OF CONTENTS
       ========================================= */

    function buildTOC() {
        var tocNav  = document.getElementById( 'js-toc' );
        var tocList = document.getElementById( 'js-toc-list' );

        if ( ! tocNav || ! tocList ) return;

        var prose    = document.querySelector( '.prose' );
        if ( ! prose ) return;

        var headings = prose.querySelectorAll( 'h2, h3' );
        if ( headings.length < 2 ) return; // TOC nur bei 2+ Überschriften

        headings.forEach( function ( heading, index ) {
            // ID generieren falls nicht vorhanden
            if ( ! heading.id ) {
                heading.id = 'section-' + ( index + 1 );
            }

            var li = document.createElement( 'li' );
            if ( heading.tagName === 'H3' ) {
                li.classList.add( 'toc-h3' );
            }

            var a = document.createElement( 'a' );
            a.href = '#' + heading.id;
            a.textContent = heading.textContent;

            // Smooth Scroll
            a.addEventListener( 'click', function ( e ) {
                e.preventDefault();
                smoothScrollTo( heading );
                history.pushState( null, '', '#' + heading.id );
            } );

            li.appendChild( a );
            tocList.appendChild( li );
        } );

        // TOC sichtbar machen
        tocNav.removeAttribute( 'hidden' );
    }

    /* =========================================
       2. FOOTNOTE SMOOTH SCROLL
       ========================================= */

    function initFootnoteScroll() {
        document.addEventListener( 'click', function ( e ) {
            var link = e.target.closest( 'a[href^="#fn"], a[href^="#fnref"], a.footnote-backref, sup.footnote-ref a' );
            if ( ! link ) return;

            var targetId = link.getAttribute( 'href' );
            if ( ! targetId || targetId.charAt( 0 ) !== '#' ) return;

            var target = document.getElementById( targetId.substring( 1 ) );
            if ( ! target ) return;

            e.preventDefault();
            smoothScrollTo( target );
            history.pushState( null, '', targetId );
        } );
    }

    /* =========================================
       HELPER: Smooth Scroll
       ========================================= */

    function smoothScrollTo( element ) {
        var offset = 20; // Abstand zum oberen Rand

        if ( 'scrollBehavior' in document.documentElement.style ) {
            // Native smooth scroll
            window.scrollTo( {
                top: element.getBoundingClientRect().top + window.pageYOffset - offset,
                behavior: 'smooth'
            } );
        } else {
            // Fallback
            window.scrollTo( 0, element.getBoundingClientRect().top + window.pageYOffset - offset );
        }

        // Focus für Accessibility
        element.setAttribute( 'tabindex', '-1' );
        element.focus( { preventScroll: true } );
    }

    /* =========================================
       INIT
       ========================================= */

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }

    function init() {
        buildTOC();
        initFootnoteScroll();
    }

} )();
