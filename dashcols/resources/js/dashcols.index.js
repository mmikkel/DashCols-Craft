( function( window ) {

	if ( ! window.$ ) {
        return;
    }

	var DashCols_Index = {
		$sortButton : null
	};

	DashCols_Index.init = function () {

		var updateHandler = $.proxy( onUpdate, this ),
			resizeHandler = $.proxy( onResize, this );

		$( document ).ajaxComplete( updateHandler );
		$( window ).on( 'resize', resizeHandler );

	}

	DashCols_Index.evalResponsiveTable = function () {

		// Get DOM elements
		var $tableView = $( '#content .tableview:first' ),
			$table = $( '#content .tableview:first table:first' );

		if ( $tableView.length === 0 || $table.length === 0 ) {
			return false;
		}

		if ( $table.outerWidth() > $tableView.outerWidth() ) {
			$tableView.addClass( 'dashCols-scrollable' );
		} else {
			$tableView.removeClass( 'dashCols-scrollable' );
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

	DashCols_Index.updateSortButton = function ()
	{

		if ( this.$sortButton === null ) {
			this.$sortButton = $( '.sortmenubtn:first' );
			if ( this.$sortButton.length > 0 ) {
				this.$sortButton.on( 'click', $.proxy( onSortMenuButtonClick, this ) );
			}
		}

		if ( this.$sortButton.length === 0 ) {
			return false;
		}

		this.updateSortMenu();

	}

	DashCols_Index.updateSortMenu = function () {

		var $sortAttributes = $( '.menu ul.sort-attributes:first' );

		if ( $sortAttributes.length === 0 ) {
			return false;
		}

		var $sortAttributesItems = $sortAttributes.find( 'li' ),
			$sortAttributeItem,
			$indexTableColumns = $( '.tableview .data th' ),
			attribute,
			attributeValue,
			attributes = [];

		$indexTableColumns.each( function () {
			attribute = $( this ).data( 'attribute' ) || false;
			if ( attribute ) {
				attributes.push( attribute );
			}
		} );

		$sortAttributesItems.show().each( function () {
			$sortAttributeItem = $( this );
			attributeValue = $sortAttributeItem.find( 'a:first' ).data( 'attr' );
			if ( attributeValue !== 'structure' && $.inArray( attributeValue, attributes ) === -1 ) {
				$sortAttributeItem.hide();
			}
		} );

	}

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

			case 'users' :

				// TEMP
				var userGroupHandles = {
					1 : 'accountManagers',
					2 : 'investedUsers',
					3 : 'publicUsers',
					4 : 'level1',
					5 : 'level2',
					6 : 'level3',
					7 : 'level4',
					8 : 'level5'
				}
				// END TEMP

				var key = $('#sidebar nav a.sel').data('key');
				var groupId = key.replace(/^group:/, '');

				if (!isNaN(groupId)) {
					this.editUrl += 'users/' + userGroupHandles[groupId];
				} else {
					this.editUrl += 'listing/users';
				}

				this.entryIndex = Craft.UserIndex || false;

				break;

		}

		this.evalResponsiveTable();

		if ( $( '#nav-dashcols' ).length > 0 ) {
			this.updateEditButton();
			this.updateSortButton();
		}

	}

	function onResize ( e ) {
		this.evalResponsiveTable();
	}

	function onSortMenuButtonClick( e ) {
		this.updateSortMenu();
	}

	$( document ).ready( $.proxy( DashCols_Index.init, DashCols_Index ) );

} ( window ) );