(function(window) {

	if (!window.$) {
        return;
    }

	var DashCols_Index = {
		$sortButton : null
	};

	DashCols_Index.init = function () {

		var updateHandler = $.proxy(onUpdate, this),
			resizeHandler = $.proxy(onResize, this);

		$(document).ajaxComplete(updateHandler);
		$(window).on('resize', resizeHandler);

	}

	DashCols_Index.evalResponsiveTable = function () {

		// Get DOM elements
		var $tableView = $('#content .tableview:first'),
			$table = $('#content .tableview:first table:first');

		if ($tableView.length === 0 || $table.length === 0) {
			return false;
		}

		if ($table.outerWidth() > $tableView.outerWidth()) {
			$tableView.addClass('dashCols-scrollable');
		} else {
			$tableView.removeClass('dashCols-scrollable');
		}

	}

	DashCols_Index.updateEditButton = function () {

		var $editButton = $('#dashCols-editButton'),
			hasEditButton = $editButton.length > 0;

		if (hasEditButton)
		{
			if (this.editUrl) $editButton.attr('href', this.editUrl);
			else $editButton.remove();
		}
		else if(this.editUrl)
		{
			var editButtonHtml = '<a href="' + this.editUrl + '" id="dashCols-editButton" class="btn">Edit columns</a>';
			$('#content').append(editButtonHtml);
		}

	}

	DashCols_Index.updateSortButton = function ()
	{

		if (this.$sortButton === null) {
			this.$sortButton = $('.sortmenubtn:first');
			if (this.$sortButton.length > 0) {
				this.$sortButton.on('click mouseenter', $.proxy(onSortMenuButtonClick, this));
			}
		}

		if (this.$sortButton.length === 0) {
			return false;
		}

		this.updateSortMenu();

	}

	DashCols_Index.updateSortMenu = function () {

		var $sortAttributes = $('.menu ul.sort-attributes:first');

		if ($sortAttributes.length === 0) {
			requestAnimationFrame($.proxy(this.updateSortMenu, this));
			return false;
		}

		var $sortAttributesItems = $sortAttributes.find('li'),
			$sortAttributeItem,
			$indexTableColumns = $('.tableview .data th'),
			attribute,
			attributeValue,
			attributes = [];

		$indexTableColumns.each(function () {
			attribute = $(this).data('attribute') || false;
			if (attribute) {
				attributes.push(attribute);
			}
		});

		$sortAttributesItems.show().each(function () {
			$sortAttributeItem = $(this);
			attributeValue = $sortAttributeItem.find('a:first').data('attr');
			if (attributeValue !== 'structure' && $.inArray(attributeValue, attributes) === -1) {
				$sortAttributeItem.hide();
			}
		});

	}

	function onUpdate(e, status, requestData) {

		this.editUrl = Craft.baseCpUrl + '/dashcols/',
		this.entryIndex = false;

		if (requestData.url.indexOf('elementIndex/getElements') === -1) {
			return false;
		}

		// Quo vadis?
		var currentUrl = e ? e.target.URL : window.location.href,
			uri = currentUrl.replace(Craft.baseCpUrl, ''),
			segments = uri.split('/');

		if (segments[0].length === 0) {
			segments.shift();
		}

		switch (segments[0]) {

			case 'entries' :

				if (!segments[1]) {
					this.editUrl += 'entries';
				} else if (segments[1] === 'singles') {
					this.editUrl += 'entries/singles';
				} else {
					this.editUrl += 'entries/section/' + segments[1] || '';
				}

				this.entryIndex = Craft.EntryIndex || false;

				break;

			case 'categories' :

				this.editUrl += 'categories/' + (segments[1] || '');
				this.entryIndex = Craft.CategoryIndex || false;

				break;

			case 'users' :

				var source = Craft.getLocalStorage('elementindex.User').selectedSource,
					groupId = source.replace(/^group:/, '');

				this.editUrl += segments[0] + (!isNaN(groupId) ? '/' + groupId : '');
				this.entryIndex = Craft.UserIndex || false;

				break;

			case 'assets' :

				var source = Craft.getLocalStorage('elementindex.Asset').selectedSource,
					folderId = source.replace(/^folder:/, '');

				this.editUrl += segments[0] + (!isNaN(folderId) ? '/' + folderId : '');
				this.entryIndex = Craft.AssetIndex || false;

				break;

		}

		this.evalResponsiveTable();

		// Return if CP Section is disabled
		var dashColsSettings = window._DashCols || {};
		if (dashColsSettings.hasOwnProperty('cpSectionDisabled') && dashColsSettings.cpSectionDisabled) return false;

		this.updateEditButton();
		this.updateSortButton();

	}

	function onResize(e) {
		this.evalResponsiveTable();
	}

	function onSortMenuButtonClick(e) {
		this.updateSortMenu();
	}

	$(document).ready($.proxy(DashCols_Index.init, DashCols_Index));

} (window));