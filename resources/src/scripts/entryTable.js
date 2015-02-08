( function( window ) {

	var DashColsEntryTable = {};

	DashColsEntryTable.init = function () {

		var updateHandler = $.proxy( onUpdate, this ),
			resizeHandler = $.proxy( onResize, this );

		$( document ).ajaxComplete( updateHandler );
		$( window ).on( 'resize', resizeHandler );

	}

	function onUpdate ( e ) {

		this.evalResponsiveTable();

		if ( $( '#nav-dashcols' ).length > 0 ) {
			this.updateEditButton( e ? e.target.URL : window.location.href );
		}

	}

	function onResize ( e ) {

		this.evalResponsiveTable();

	}

	DashColsEntryTable.evalResponsiveTable = function () {

		var $tableview = $( '#content .tableview:first' ),
			$table = $( '#content .tableview:first table:first' );

		if ( $tableview.length === 0 || $table.length === 0 ) {
			return false;
		}

		if ( $table.outerWidth() > $tableview.outerWidth() ) {
			$tableview.addClass( 'dashCols-scrollable' );
		} else {
			$tableview.removeClass( 'dashCols-scrollable' );
		}

	}

	DashColsEntryTable.updateEditButton = function ( currentUrl ) {

		var uri = currentUrl.replace( Craft.baseCpUrl, '' ),
			segments = uri.split( '/' ),
			editUrl = Craft.baseCpUrl + '/dashcols/layouts/';

		if ( segments[ 0 ].length === 0 ) {
			segments.shift();
		}

		switch ( segments[ 0 ] ) {

			case 'entries' :

				if ( ! segments[ 1 ] ) {
					editUrl += 'listing/entries';
				} else if ( segments[ 1 ] === 'singles' ) {
					editUrl += 'listing/singles';
				} else {
					editUrl += 'section/' + segments[ 1 ] || '';
				}

				break;

			case 'categories' :

				editUrl += 'category-group/' + segments[ 1 ] || '';

				break;

			default :

				editUrl = false;

		}

		var $editButton = $( '.dashCols-editButton:first' );

		if ( $editButton.length === 0 && editUrl ) {
			var editButtonHtml = '<a href="' + editUrl + '" class="btn dashCols-editButton">Edit columns</a>';
			$( '#content' ).append( editButtonHtml );	
		} else {
			if ( ! editUrl ) {
				$editButton.remove();
			} else {
				$editButton.attr( 'href', editUrl );
			}
		}

	}

	$( document ).ready( $.proxy( DashColsEntryTable.init, DashColsEntryTable ) );

} ( window ) );