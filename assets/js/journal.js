/**
 * Hasimuener Journal — Frontend JS
 * 
 * 1. Sticky TOC mit Active-Section-Tracking (IntersectionObserver)
 * 2. Lesefortschritts-Balken
 * 3. Footnote Smooth Scroll
 * 4. Share — Link kopieren
 * 
 * Geladen nur auf Singles (essay, note, post).
 * Kein Framework. Kein Build-Step. ~3KB.
 *
 * @package Hasimuener_Journal
 * @version 3.1.0
 */

( function () {
    'use strict';

    var headingsArray = [];
    var tocItems      = [];

    /* =========================================
       1. TABLE OF CONTENTS + ACTIVE TRACKING
       ========================================= */

    function buildTOC() {
        var tocNav  = document.getElementById( 'js-toc' );
        var tocList = document.getElementById( 'js-toc-list' );

        if ( ! tocNav || ! tocList ) return;

        var prose = document.querySelector( '.prose' );
        if ( ! prose ) return;

        var headings = prose.querySelectorAll( 'h2, h3' );
        if ( headings.length < 2 ) return;

        headings.forEach( function ( heading, index ) {
            if ( ! heading.id ) {
                heading.id = 'section-' + ( index + 1 );
            }

            var li = document.createElement( 'li' );
            li.setAttribute( 'data-target', heading.id );

            if ( heading.tagName === 'H3' ) {
                li.classList.add( 'toc-h3' );
            }

            var a = document.createElement( 'a' );
            a.href = '#' + heading.id;
            a.textContent = heading.textContent;

            a.addEventListener( 'click', function ( e ) {
                e.preventDefault();
                smoothScrollTo( heading );
                history.pushState( null, '', '#' + heading.id );
            } );

            li.appendChild( a );
            tocList.appendChild( li );

            headingsArray.push( heading );
            tocItems.push( li );
        } );

        // TOC sichtbar machen
        tocNav.removeAttribute( 'hidden' );
        tocNav.setAttribute( 'data-visible', 'true' );

        // Active Section Tracking starten
        initActiveTracking();
    }

    function initActiveTracking() {
        // IntersectionObserver: beobachtet Überschriften
        if ( ! ( 'IntersectionObserver' in window ) ) return;

        var currentIndex = -1;

        var observer = new IntersectionObserver( function ( entries ) {
            entries.forEach( function ( entry ) {
                if ( entry.isIntersecting ) {
                    var idx = headingsArray.indexOf( entry.target );
                    if ( idx !== -1 ) {
                        setActive( idx );
                    }
                }
            } );
        }, {
            rootMargin: '-10% 0px -75% 0px',
            threshold: 0
        } );

        headingsArray.forEach( function ( heading ) {
            observer.observe( heading );
        } );

        // Fallback: Scroll-basiert für präzisere Erkennung
        var ticking = false;
        window.addEventListener( 'scroll', function () {
            if ( ! ticking ) {
                requestAnimationFrame( function () {
                    updateActiveOnScroll();
                    ticking = false;
                } );
                ticking = true;
            }
        }, { passive: true } );
    }

    function updateActiveOnScroll() {
        var scrollPos = window.pageYOffset + 120;
        var active    = -1;

        for ( var i = 0; i < headingsArray.length; i++ ) {
            if ( headingsArray[ i ].offsetTop <= scrollPos ) {
                active = i;
            }
        }

        if ( active >= 0 ) {
            setActive( active );
        }
    }

    function setActive( index ) {
        tocItems.forEach( function ( item, i ) {
            if ( i === index ) {
                item.classList.add( 'is-active' );
            } else {
                item.classList.remove( 'is-active' );
            }
        } );
    }

    /* =========================================
       2. READING PROGRESS BAR
       ========================================= */

    function initReadingProgress() {
        var bar   = document.getElementById( 'js-reading-bar' );
        var prose = document.querySelector( '.prose' );

        if ( ! bar || ! prose ) return;

        var ticking = false;

        window.addEventListener( 'scroll', function () {
            if ( ! ticking ) {
                requestAnimationFrame( function () {
                    updateProgress( bar, prose );
                    ticking = false;
                } );
                ticking = true;
            }
        }, { passive: true } );
    }

    function updateProgress( bar, prose ) {
        var proseRect  = prose.getBoundingClientRect();
        var proseTop   = prose.offsetTop;
        var proseHeight = prose.offsetHeight;
        var scrollPos  = window.pageYOffset;
        var windowH    = window.innerHeight;

        var start    = proseTop;
        var end      = proseTop + proseHeight - windowH;
        var progress = ( scrollPos - start ) / ( end - start );

        progress = Math.max( 0, Math.min( 1, progress ) );
        bar.style.width = ( progress * 100 ) + '%';
    }

    /* =========================================
       3. FOOTNOTE SMOOTH SCROLL
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
        var offset = 24;

        if ( 'scrollBehavior' in document.documentElement.style ) {
            window.scrollTo( {
                top: element.getBoundingClientRect().top + window.pageYOffset - offset,
                behavior: 'smooth'
            } );
        } else {
            window.scrollTo( 0, element.getBoundingClientRect().top + window.pageYOffset - offset );
        }

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
        initReadingProgress();
        initFootnoteScroll();
        initShareCopyLink();
    }

    /* =========================================
       4. SHARE — Link kopieren
       ========================================= */

    function initShareCopyLink() {
        var buttons = document.querySelectorAll( '.hp-share__link--copy' );

        buttons.forEach( function( btn ) {
            btn.addEventListener( 'click', function() {
                var url = btn.getAttribute( 'data-url' );
                if ( ! url ) return;

                if ( navigator.clipboard && navigator.clipboard.writeText ) {
                    navigator.clipboard.writeText( url ).then( function() {
                        showCopied( btn );
                    } );
                } else {
                    // Fallback
                    var input = document.createElement( 'input' );
                    input.value = url;
                    document.body.appendChild( input );
                    input.select();
                    document.execCommand( 'copy' );
                    document.body.removeChild( input );
                    showCopied( btn );
                }
            } );
        } );
    }

    function showCopied( btn ) {
        var original = btn.innerHTML;
        btn.innerHTML = '✓';
        btn.classList.add( 'is-copied' );

        setTimeout( function() {
            btn.innerHTML = original;
            btn.classList.remove( 'is-copied' );
        }, 2000 );
    }

} )();

/* =========================================
   MOBILE NAVIGATION — Hamburger Toggle
   =========================================
   Öffnet/schließt das Mobile-Menü.
   Kein Framework. Kein Build-Step.
*/
( function () {
    'use strict';

    function init() {
        var toggle = document.querySelector( '.hp-nav__toggle' );
        var mobile = document.getElementById( 'hp-nav-mobile' );
        var searchToggle = document.querySelector( '.hp-nav__search-toggle' );
        var searchPanel = document.getElementById( 'hp-nav-search' );
        var headerBar = document.querySelector( '.hp-header-bar' );

        if ( ! toggle || ! mobile ) return;

        toggle.addEventListener( 'click', function () {
            var expanded = toggle.getAttribute( 'aria-expanded' ) === 'true';
            toggle.setAttribute( 'aria-expanded', String( ! expanded ) );
            toggle.setAttribute( 'aria-label', expanded ? 'Menü öffnen' : 'Menü schließen' );

            if ( expanded ) {
                // Schließen
                mobile.setAttribute( 'data-open', 'false' );
                if ( headerBar ) headerBar.classList.remove( 'hp-header-bar--menu-open' );
                setTimeout( function () {
                    mobile.setAttribute( 'hidden', '' );
                }, 300 );
            } else {
                if ( searchToggle && searchPanel && searchToggle.getAttribute( 'aria-expanded' ) === 'true' ) {
                    searchToggle.setAttribute( 'aria-expanded', 'false' );
                    searchToggle.setAttribute( 'aria-label', 'Suche öffnen' );
                    searchPanel.setAttribute( 'hidden', '' );
                    if ( headerBar ) headerBar.classList.remove( 'hp-header-bar--search-open' );
                }
                // Öffnen
                mobile.removeAttribute( 'hidden' );
                // Force reflow für Animation
                void mobile.offsetHeight;
                mobile.setAttribute( 'data-open', 'true' );
                if ( headerBar ) headerBar.classList.add( 'hp-header-bar--menu-open' );
            }
        } );

        // ESC schließt Menü
        document.addEventListener( 'keydown', function ( e ) {
            if ( e.key === 'Escape' && toggle.getAttribute( 'aria-expanded' ) === 'true' ) {
                toggle.click();
                toggle.focus();
            }
        } );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }
} )();

/* =========================================
   NAVIGATION SEARCH TOGGLE
   =========================================
   Öffnet/schließt das Suchfeld in der Navigation.
*/
( function () {
    'use strict';

    function init() {
        var toggle = document.querySelector( '.hp-nav__search-toggle' );
        var panel  = document.getElementById( 'hp-nav-search' );
        var menuToggle = document.querySelector( '.hp-nav__toggle' );
        var menuPanel = document.getElementById( 'hp-nav-mobile' );
        var headerBar = document.querySelector( '.hp-header-bar' );

        if ( ! toggle || ! panel ) return;

        toggle.addEventListener( 'click', function () {
            var expanded = toggle.getAttribute( 'aria-expanded' ) === 'true';
            toggle.setAttribute( 'aria-expanded', String( ! expanded ) );
            toggle.setAttribute( 'aria-label', expanded ? 'Suche öffnen' : 'Suche schließen' );

            if ( expanded ) {
                panel.setAttribute( 'hidden', '' );
                if ( headerBar ) headerBar.classList.remove( 'hp-header-bar--search-open' );
            } else {
                if ( menuToggle && menuPanel && menuToggle.getAttribute( 'aria-expanded' ) === 'true' ) {
                    menuToggle.setAttribute( 'aria-expanded', 'false' );
                    menuToggle.setAttribute( 'aria-label', 'Menü öffnen' );
                    menuPanel.setAttribute( 'data-open', 'false' );
                    menuPanel.setAttribute( 'hidden', '' );
                    if ( headerBar ) headerBar.classList.remove( 'hp-header-bar--menu-open' );
                }
                panel.removeAttribute( 'hidden' );
                if ( headerBar ) headerBar.classList.add( 'hp-header-bar--search-open' );
                var input = panel.querySelector( 'input[type="search"]' );
                if ( input ) input.focus();
            }
        } );

        document.addEventListener( 'keydown', function ( e ) {
            if ( e.key === 'Escape' && toggle.getAttribute( 'aria-expanded' ) === 'true' ) {
                toggle.setAttribute( 'aria-expanded', 'false' );
                toggle.setAttribute( 'aria-label', 'Suche öffnen' );
                panel.setAttribute( 'hidden', '' );
                if ( headerBar ) headerBar.classList.remove( 'hp-header-bar--search-open' );
                toggle.focus();
            }
        } );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }
} )();

/* =========================================
   HEADER SCROLL STATE
   =========================================
   Lässt die Navigationsleiste beim Scrollen als
   eigenständige schwebende Leiste auftreten.
*/
( function () {
    'use strict';

    function init() {
        var headerBar = document.querySelector( '.hp-header-bar' );
        var masthead = document.querySelector( '.hp-masthead' );
        var ticking = false;

        if ( ! headerBar || ! masthead ) return;

        function update() {
            var threshold = Math.max( 24, masthead.offsetHeight - 16 );
            var scrollY = window.pageYOffset || window.scrollY || 0;
            headerBar.classList.toggle( 'hp-header-bar--scrolled', scrollY > threshold );
            ticking = false;
        }

        function onScroll() {
            if ( ticking ) return;
            ticking = true;
            window.requestAnimationFrame( update );
        }

        update();
        window.addEventListener( 'scroll', onScroll, { passive: true } );
        window.addEventListener( 'resize', update );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }
} )();

/* =========================================
   GLOSSAR — TOOLTIP
   =========================================
   Erzeugt ein einziges schwebendes Tooltip-Overlay
   und positioniert es an jedem .hp-glossar-term.
   Funktioniert mit Maus (hover) und Tastatur (focus).
   Mobile: Tap öffnet/schließt.
*/
( function () {
    'use strict';

    var tooltip = null;
    var activeEl = null;
    var hideTimer = null;

    function createTooltip() {
        tooltip = document.createElement( 'div' );
        tooltip.className  = 'hp-gtt';
        tooltip.id         = 'hp-gtt';
        tooltip.setAttribute( 'role', 'tooltip' );
        tooltip.setAttribute( 'aria-live', 'polite' );
        tooltip.innerHTML  =
            '<strong class="hp-gtt__term"></strong>' +
            '<p class="hp-gtt__def"></p>' +
            '<a class="hp-gtt__link" href="#">Im Glossar lesen \u2192</a>';
        document.body.appendChild( tooltip );

        tooltip.addEventListener( 'mouseenter', function () {
            clearTimeout( hideTimer );
        } );
        tooltip.addEventListener( 'mouseleave', function () {
            scheduleHide();
        } );
    }

    function show( el ) {
        clearTimeout( hideTimer );
        activeEl = el;

        tooltip.querySelector( '.hp-gtt__term' ).textContent = el.dataset.term  || '';
        tooltip.querySelector( '.hp-gtt__def'  ).textContent = el.dataset.def   || '';
        tooltip.querySelector( '.hp-gtt__link' ).href        = el.dataset.url   || '#';

        tooltip.classList.add( 'hp-gtt--visible' );
        position( el );
    }

    function scheduleHide() {
        hideTimer = setTimeout( hide, 200 );
    }

    function hide() {
        tooltip.classList.remove( 'hp-gtt--visible' );
        activeEl = null;
    }

    function position( el ) {
        var rect   = el.getBoundingClientRect();
        var tW     = tooltip.offsetWidth  || 280;
        var tH     = tooltip.offsetHeight || 120;
        var vW     = window.innerWidth;
        var vH     = window.innerHeight;
        var scroll = window.scrollY || window.pageYOffset;

        // Versuche oberhalb zu platzieren
        var top  = rect.top + scroll - tH - 10;
        var left = rect.left + ( rect.width / 2 ) - ( tW / 2 );

        // Fällt oben raus → unterhalb
        if ( top < scroll + 8 ) {
            top = rect.bottom + scroll + 10;
            tooltip.classList.add( 'hp-gtt--below' );
        } else {
            tooltip.classList.remove( 'hp-gtt--below' );
        }

        // Rechts begrenzen
        if ( left + tW > vW - 12 ) { left = vW - tW - 12; }
        if ( left < 12 )           { left = 12; }

        tooltip.style.top  = top  + 'px';
        tooltip.style.left = left + 'px';
    }

    function init() {
        var terms = document.querySelectorAll( '.hp-glossar-term' );
        if ( ! terms.length ) return;

        createTooltip();

        terms.forEach( function ( el ) {
            // Maus
            el.addEventListener( 'mouseenter', function () { show( el ); } );
            el.addEventListener( 'mouseleave', scheduleHide );
            // Tastatur
            el.addEventListener( 'focus',  function () { show( el ); } );
            el.addEventListener( 'blur',   scheduleHide );
            // Mobile: Toggle bei Tap
            el.addEventListener( 'click', function ( e ) {
                e.stopPropagation();
                if ( activeEl === el && tooltip.classList.contains( 'hp-gtt--visible' ) ) {
                    hide();
                } else {
                    show( el );
                }
            } );
        } );

        // Klick außerhalb schließt
        document.addEventListener( 'click', function () { hide(); } );

        // Repositionieren bei Scroll
        window.addEventListener( 'scroll', function () {
            if ( activeEl && tooltip.classList.contains( 'hp-gtt--visible' ) ) {
                position( activeEl );
            }
        }, { passive: true } );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }

} )();
