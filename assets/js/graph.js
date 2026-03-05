/**
 * Wissensgraph — D3.js Force-Directed Graph
 *
 * Interaktive Visualisierung der Beziehungen zwischen
 * Essays, Notizen, Glossar-Einträgen und Themenfeldern.
 *
 * Abhängigkeit: D3.js v7 (per CDN geladen).
 * Daten: REST-Endpoint /wp-json/hp/v1/graph
 *
 * @package Hasimuener_Journal
 * @since   6.0.0
 */

( function() {
	'use strict';

	/* =========================================
	   KONFIGURATION
	   ========================================= */

	var CONFIG = {
		colors: {
			essay:   '#b12a2a',
			note:    '#555555',
			glossar: '#2a6cb1',
			topic:   '#111111',
		},
		edgeColor:     '#d0d0d0',
		edgeHighlight: '#888888',
		dimOpacity:    0.1,
		minRadius:     6,
		maxRadius:     28,
		labelOffset:   4,
		mobileBreak:   768,
	};

	var prefersReducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/* =========================================
	   STATE
	   ========================================= */

	var state = {
		nodes:        [],
		edges:        [],
		activeTypes:  { essay: true, note: true, glossar: true, topic: true },
		selectedNode: null,
		simulation:   null,
		svg:          null,
		g:            null,
		zoom:         null,
		linkSel:      null,
		nodeSel:      null,
		labelSel:     null,
		width:        0,
		height:       0,
	};

	/* =========================================
	   INITIALISIERUNG
	   ========================================= */

	function init() {
		var canvas = document.getElementById( 'hp-graph-canvas' );
		if ( ! canvas || typeof d3 === 'undefined' || typeof hpGraph === 'undefined' ) {
			return;
		}

		fetchData();
		bindControls();
	}

	/* =========================================
	   DATEN LADEN
	   ========================================= */

	function fetchData() {
		var loading = document.getElementById( 'hp-graph-loading' );
		var error   = document.getElementById( 'hp-graph-error' );

		fetch( hpGraph.restUrl, {
			headers: { 'X-WP-Nonce': hpGraph.nonce },
		} )
		.then( function( res ) {
			if ( ! res.ok ) { throw new Error( 'HTTP ' + res.status ); }
			return res.json();
		} )
		.then( function( data ) {
			if ( loading ) { loading.hidden = true; }
			state.nodes = data.nodes || [];
			state.edges = data.edges || [];
			buildGraph();
		} )
		.catch( function() {
			if ( loading ) { loading.hidden = true; }
			if ( error )   { error.hidden = false; }
		} );
	}

	/* =========================================
	   GRAPH AUFBAUEN
	   ========================================= */

	function buildGraph() {
		var canvas = document.getElementById( 'hp-graph-canvas' );
		state.width  = canvas.clientWidth;
		state.height = canvas.clientHeight;

		// Node-Verbindungsanzahl berechnen
		var linkCount = {};
		state.edges.forEach( function( e ) {
			linkCount[ e.source ] = ( linkCount[ e.source ] || 0 ) + 1;
			linkCount[ e.target ] = ( linkCount[ e.target ] || 0 ) + 1;
		} );

		// Radius-Skala
		var maxLinks = 1;
		state.nodes.forEach( function( n ) {
			var c = linkCount[ n.id ] || 0;
			if ( c > maxLinks ) { maxLinks = c; }
		} );

		var radiusScale = d3.scaleSqrt()
			.domain( [ 0, maxLinks ] )
			.range( [ CONFIG.minRadius, CONFIG.maxRadius ] );

		state.nodes.forEach( function( n ) {
			n._linkCount = linkCount[ n.id ] || 0;
			n._radius    = radiusScale( n._linkCount );
		} );

		// SVG anlegen
		state.svg = d3.select( '#hp-graph-canvas' )
			.append( 'svg' )
			.attr( 'width', '100%' )
			.attr( 'height', '100%' )
			.attr( 'viewBox', '0 0 ' + state.width + ' ' + state.height )
			.attr( 'role', 'img' )
			.attr( 'aria-label', 'Wissensgraph: Interaktive Netzwerk-Visualisierung' );

		// Zoom-Verhalten
		state.zoom = d3.zoom()
			.scaleExtent( [ 0.3, 4 ] )
			.on( 'zoom', function( event ) {
				state.g.attr( 'transform', event.transform );
			} );

		state.svg.call( state.zoom );

		// Container-Gruppe
		state.g = state.svg.append( 'g' );

		// Edges
		state.linkSel = state.g.append( 'g' )
			.attr( 'class', 'hp-graph__edges' )
			.selectAll( 'line' )
			.data( state.edges )
			.enter()
			.append( 'line' )
			.attr( 'stroke', CONFIG.edgeColor )
			.attr( 'stroke-width', function( d ) { return Math.max( 1, d.weight ); } )
			.attr( 'stroke-opacity', 0.6 );

		// Node-Gruppen
		state.nodeSel = state.g.append( 'g' )
			.attr( 'class', 'hp-graph__nodes' )
			.selectAll( 'g' )
			.data( state.nodes )
			.enter()
			.append( 'g' )
			.attr( 'class', 'hp-graph__node' )
			.attr( 'tabindex', '0' )
			.attr( 'role', 'button' )
			.attr( 'aria-label', function( d ) {
				var typeLabel = { essay: 'Essay', note: 'Notiz', glossar: 'Glossar', topic: 'Themenfeld' };
				return ( typeLabel[ d.type ] || d.type ) + ': ' + d.label;
			} );

		// Kreise
		state.nodeSel.append( 'circle' )
			.attr( 'r', function( d ) { return d._radius; } )
			.attr( 'fill', function( d ) { return CONFIG.colors[ d.type ] || '#999'; } )
			.attr( 'stroke', '#fff' )
			.attr( 'stroke-width', 1.5 );

		// Labels (standardmäßig versteckt, bei Hover/Fokus sichtbar)
		state.labelSel = state.nodeSel.append( 'text' )
			.text( function( d ) { return d.label; } )
			.attr( 'class', 'hp-graph__label' )
			.attr( 'dy', function( d ) { return -d._radius - CONFIG.labelOffset; } )
			.attr( 'text-anchor', 'middle' )
			.attr( 'opacity', 0 )
			.attr( 'aria-hidden', 'true' );

		// Interaktionen
		state.nodeSel
			.on( 'mouseenter', handleNodeHover )
			.on( 'mouseleave', handleNodeUnhover )
			.on( 'click', handleNodeClick )
			.on( 'keydown', function( event, d ) {
				if ( event.key === 'Enter' || event.key === ' ' ) {
					event.preventDefault();
					handleNodeClick( event, d );
				}
			} )
			.on( 'focus', handleNodeHover )
			.on( 'blur', handleNodeUnhover );

		// Drag
		var drag = d3.drag()
			.on( 'start', dragStart )
			.on( 'drag', dragging )
			.on( 'end', dragEnd );

		state.nodeSel.call( drag );

		// Force-Simulation
		state.simulation = d3.forceSimulation( state.nodes )
			.force( 'link', d3.forceLink( state.edges )
				.id( function( d ) { return d.id; } )
				.distance( 80 )
			)
			.force( 'charge', d3.forceManyBody().strength( -200 ) )
			.force( 'center', d3.forceCenter( state.width / 2, state.height / 2 ) )
			.force( 'collision', d3.forceCollide().radius( function( d ) { return d._radius + 2; } ) )
			.on( 'tick', ticked );

		if ( prefersReducedMotion ) {
			// Keine Animation: Simulation sofort berechnen
			state.simulation.alpha( 1 ).alphaDecay( 0.05 );
			for ( var i = 0; i < 300; i++ ) { state.simulation.tick(); }
			state.simulation.stop();
			ticked();
		} else {
			state.simulation.on( 'end', function() {
				state.simulation.stop();
			} );
		}

		// Zoom-Buttons binden
		bindZoomButtons();

		// Resize-Handler
		window.addEventListener( 'resize', debounce( handleResize, 250 ) );
	}

	/* =========================================
	   SIMULATION TICK
	   ========================================= */

	function ticked() {
		state.linkSel
			.attr( 'x1', function( d ) { return d.source.x; } )
			.attr( 'y1', function( d ) { return d.source.y; } )
			.attr( 'x2', function( d ) { return d.target.x; } )
			.attr( 'y2', function( d ) { return d.target.y; } );

		state.nodeSel
			.attr( 'transform', function( d ) {
				return 'translate(' + d.x + ',' + d.y + ')';
			} );
	}

	/* =========================================
	   HOVER / FOKUS
	   ========================================= */

	function handleNodeHover( event, d ) {
		var connected = getConnectedIds( d.id );

		// Nicht-verbundene dimmen
		state.nodeSel
			.attr( 'opacity', function( n ) {
				return ( n.id === d.id || connected[ n.id ] ) ? 1 : CONFIG.dimOpacity;
			} );

		state.linkSel
			.attr( 'stroke', function( e ) {
				var src = typeof e.source === 'object' ? e.source.id : e.source;
				var tgt = typeof e.target === 'object' ? e.target.id : e.target;
				return ( src === d.id || tgt === d.id ) ? CONFIG.edgeHighlight : CONFIG.edgeColor;
			} )
			.attr( 'stroke-opacity', function( e ) {
				var src = typeof e.source === 'object' ? e.source.id : e.source;
				var tgt = typeof e.target === 'object' ? e.target.id : e.target;
				return ( src === d.id || tgt === d.id ) ? 1 : 0.1;
			} );

		// Label einblenden
		state.labelSel
			.attr( 'opacity', function( n ) {
				return ( n.id === d.id || connected[ n.id ] ) ? 1 : 0;
			} );
	}

	function handleNodeUnhover() {
		state.nodeSel.attr( 'opacity', 1 );
		state.linkSel
			.attr( 'stroke', CONFIG.edgeColor )
			.attr( 'stroke-opacity', 0.6 );
		state.labelSel.attr( 'opacity', 0 );
	}

	/* =========================================
	   CLICK → DETAIL-PANEL
	   ========================================= */

	function handleNodeClick( event, d ) {
		event.stopPropagation();
		state.selectedNode = d;
		showDetail( d );
	}

	function showDetail( d ) {
		var panel   = document.getElementById( 'hp-graph-detail' );
		var content = document.getElementById( 'hp-graph-detail-content' );
		if ( ! panel || ! content ) { return; }

		var typeLabel = { essay: 'Essay', note: 'Notiz', glossar: 'Glossar-Eintrag', topic: 'Themenfeld' };
		var html = '';

		html += '<span class="hp-graph__detail-type hp-graph__detail-type--' + escHtml( d.type ) + '">' + escHtml( typeLabel[ d.type ] || d.type ) + '</span>';
		html += '<h2 class="hp-graph__detail-title">' + escHtml( d.label ) + '</h2>';

		// Meta-Infos
		if ( d.meta ) {
			if ( d.meta.reading_time ) {
				html += '<p class="hp-graph__detail-meta">' + escHtml( d.meta.reading_time );
				if ( d.meta.date ) { html += ' · ' + escHtml( d.meta.date ); }
				html += '</p>';
			}
			if ( d.meta.excerpt ) {
				html += '<p class="hp-graph__detail-excerpt">' + escHtml( d.meta.excerpt ) + '</p>';
			}
			if ( d.meta.kurz ) {
				html += '<p class="hp-graph__detail-excerpt">' + escHtml( d.meta.kurz ) + '</p>';
			}
			if ( d.meta.description ) {
				html += '<p class="hp-graph__detail-excerpt">' + escHtml( d.meta.description ) + '</p>';
			}
			if ( d.meta.count !== undefined ) {
				html += '<p class="hp-graph__detail-meta">' + d.meta.count + ' Beiträge</p>';
			}
		}

		// Verbundene Nodes
		var connected = getConnectedNodes( d.id );
		if ( connected.length > 0 ) {
			html += '<h3 class="hp-graph__detail-subtitle">Verbindungen</h3>';
			html += '<ul class="hp-graph__detail-links">';
			connected.forEach( function( n ) {
				html += '<li>';
				html += '<a href="' + escAttr( n.url ) + '" class="hp-graph__detail-link">';
				html += '<span class="hp-graph__detail-link-dot hp-graph__detail-link-dot--' + escHtml( n.type ) + '" aria-hidden="true"></span>';
				html += escHtml( n.label );
				html += '</a></li>';
			} );
			html += '</ul>';
		}

		// Link zum Beitrag
		if ( d.url ) {
			html += '<a href="' + escAttr( d.url ) + '" class="hp-graph__detail-cta">Zum Beitrag →</a>';
		}

		content.innerHTML = html;
		panel.hidden = false;
	}

	function hideDetail() {
		var panel = document.getElementById( 'hp-graph-detail' );
		if ( panel ) { panel.hidden = true; }
		state.selectedNode = null;
	}

	/* =========================================
	   FILTER
	   ========================================= */

	function applyFilter() {
		state.nodeSel.each( function( d ) {
			var visible = state.activeTypes[ d.type ];
			d3.select( this ).attr( 'visibility', visible ? 'visible' : 'hidden' );
		} );

		state.linkSel.each( function( e ) {
			var src = typeof e.source === 'object' ? e.source : findNode( e.source );
			var tgt = typeof e.target === 'object' ? e.target : findNode( e.target );
			var visible = src && tgt && state.activeTypes[ src.type ] && state.activeTypes[ tgt.type ];
			d3.select( this ).attr( 'visibility', visible ? 'visible' : 'hidden' );
		} );
	}

	/* =========================================
	   DRAG
	   ========================================= */

	function dragStart( event, d ) {
		if ( ! event.active ) { state.simulation.alphaTarget( 0.3 ).restart(); }
		d.fx = d.x;
		d.fy = d.y;
	}

	function dragging( event, d ) {
		d.fx = event.x;
		d.fy = event.y;
	}

	function dragEnd( event, d ) {
		if ( ! event.active ) { state.simulation.alphaTarget( 0 ); }
		d.fx = null;
		d.fy = null;
	}

	/* =========================================
	   ZOOM-BUTTONS
	   ========================================= */

	function bindZoomButtons() {
		var zoomIn    = document.getElementById( 'hp-graph-zoom-in' );
		var zoomOut   = document.getElementById( 'hp-graph-zoom-out' );
		var zoomReset = document.getElementById( 'hp-graph-zoom-reset' );

		if ( zoomIn ) {
			zoomIn.addEventListener( 'click', function() {
				state.svg.transition().duration( 300 ).call( state.zoom.scaleBy, 1.4 );
			} );
		}
		if ( zoomOut ) {
			zoomOut.addEventListener( 'click', function() {
				state.svg.transition().duration( 300 ).call( state.zoom.scaleBy, 0.7 );
			} );
		}
		if ( zoomReset ) {
			zoomReset.addEventListener( 'click', function() {
				state.svg.transition().duration( 300 ).call(
					state.zoom.transform,
					d3.zoomIdentity
				);
			} );
		}
	}

	/* =========================================
	   CONTROLS BINDEN
	   ========================================= */

	function bindControls() {
		// Filter-Toggles
		var filters = document.querySelectorAll( '.hp-graph__filter' );
		filters.forEach( function( btn ) {
			btn.addEventListener( 'click', function() {
				var type   = btn.getAttribute( 'data-type' );
				var active = ! state.activeTypes[ type ];
				state.activeTypes[ type ] = active;
				btn.setAttribute( 'aria-pressed', active ? 'true' : 'false' );
				btn.classList.toggle( 'hp-graph__filter--active', active );
				applyFilter();
			} );
		} );

		// Detail-Panel schließen
		var closeBtn = document.getElementById( 'hp-graph-detail-close' );
		if ( closeBtn ) {
			closeBtn.addEventListener( 'click', hideDetail );
		}

		// ESC schließt Detail-Panel
		document.addEventListener( 'keydown', function( e ) {
			if ( e.key === 'Escape' ) { hideDetail(); }
		} );

		// Klick auf Canvas schließt Detail-Panel
		var canvas = document.getElementById( 'hp-graph-canvas' );
		if ( canvas ) {
			canvas.addEventListener( 'click', function( e ) {
				if ( e.target === canvas || e.target.tagName === 'svg' ) {
					hideDetail();
				}
			} );
		}
	}

	/* =========================================
	   RESIZE
	   ========================================= */

	function handleResize() {
		var canvas = document.getElementById( 'hp-graph-canvas' );
		if ( ! canvas || ! state.svg ) { return; }

		state.width  = canvas.clientWidth;
		state.height = canvas.clientHeight;

		state.svg.attr( 'viewBox', '0 0 ' + state.width + ' ' + state.height );

		if ( state.simulation ) {
			state.simulation.force( 'center', d3.forceCenter( state.width / 2, state.height / 2 ) );
			state.simulation.alpha( 0.3 ).restart();
		}
	}

	/* =========================================
	   HILFSFUNKTIONEN
	   ========================================= */

	function getConnectedIds( nodeId ) {
		var ids = {};
		state.edges.forEach( function( e ) {
			var src = typeof e.source === 'object' ? e.source.id : e.source;
			var tgt = typeof e.target === 'object' ? e.target.id : e.target;
			if ( src === nodeId ) { ids[ tgt ] = true; }
			if ( tgt === nodeId ) { ids[ src ] = true; }
		} );
		return ids;
	}

	function getConnectedNodes( nodeId ) {
		var ids = getConnectedIds( nodeId );
		return state.nodes.filter( function( n ) { return ids[ n.id ]; } );
	}

	function findNode( id ) {
		for ( var i = 0; i < state.nodes.length; i++ ) {
			if ( state.nodes[ i ].id === id ) { return state.nodes[ i ]; }
		}
		return null;
	}

	function escHtml( str ) {
		if ( ! str ) { return ''; }
		var div = document.createElement( 'div' );
		div.appendChild( document.createTextNode( String( str ) ) );
		return div.innerHTML;
	}

	function escAttr( str ) {
		return escHtml( str ).replace( /"/g, '&quot;' );
	}

	function debounce( fn, ms ) {
		var timer;
		return function() {
			clearTimeout( timer );
			timer = setTimeout( fn, ms );
		};
	}

	/* =========================================
	   START
	   ========================================= */

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}

} )();
