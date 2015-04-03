( function( window ) {

	var DashCols_Index = {};

	DashCols_Index.init = function () {

		var updateHandler = $.proxy( onUpdate, this ),
			resizeHandler = $.proxy( onResize, this );

		$( document ).ajaxComplete( updateHandler );
		$( window ).on( 'resize', resizeHandler );

	}

	DashCols_Index.evalResponsiveTable = function () {

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

	DashCols_Index.updateEditButton = function () {

		var $editButton = $( '.dashCols-editButton:first' );

		if ( $editButton.length === 0 && this.editUrl ) {
			var editButtonHtml = '<a href="' + this.editUrl + '" class="btn dashCols-editButton">Edit columns</a>';
			$( '#content' ).append( editButtonHtml );	
		} else {
			if ( ! this.editUrl ) {
				$editButton.remove();
			} else {
				$editButton.attr( 'href', this.editUrl );
			}
		}

	}

	/*
	* TODO Update the sort button manually, because Craft won't refresh it on AJAX
	*
	*/
	// DashCols_Index.updateSortButton = function ()
	// {

	// }

	function onUpdate( e, status, requestData ) {

		this.editUrl = Craft.baseCpUrl + '/dashcols/layouts/',
		this.entryIndex = false;

		if ( requestData.url.indexOf( 'elementIndex/getElements' ) === -1 ) {
			return false;
		}

		// Quo vadis?
		var currentUrl = e ? e.target.URL : window.location.href,
			uri = currentUrl.replace( Craft.baseCpUrl, '' ),
			segments = uri.split( '/' );

		if ( segments[ 0 ].length === 0 ) {
			segments.shift();
		}

		switch ( segments[ 0 ] ) {

			case 'entries' :

				if ( ! segments[ 1 ] ) {
					this.editUrl += 'listing/entries';
				} else if ( segments[ 1 ] === 'singles' ) {
					this.editUrl += 'listing/singles';
				} else {
					this.editUrl += 'section/' + segments[ 1 ] || '';
				}

				this.entryIndex = Craft.EntryIndex || false;
		
				break;

			case 'categories' :

				this.editUrl += 'category-group/' + ( segments[ 1 ] || '' );
				this.entryIndex = Craft.CategoryIndex || false;

				break;

		}

		this.evalResponsiveTable();

		if ( $( '#nav-dashcols' ).length > 0 ) {
			//this.updateSortButton();
			this.updateEditButton();
		}

	}

	function onResize ( e ) {
		this.evalResponsiveTable();
	}

	$( document ).ready( $.proxy( DashCols_Index.init, DashCols_Index ) );

} ( window ) );