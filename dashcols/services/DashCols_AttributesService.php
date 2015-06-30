<?php namespace Craft;

/**
 * DashCols by Mats Mikkel Rummelhoff
 *
 * @author      Mats Mikkel Rummelhoff <http://mmikkel.no>
 * @package     DashCols
 * @since       Craft 2.3
 * @copyright   Copyright (c) 2015, Mats Mikkel Rummelhoff
 * @license     http://opensource.org/licenses/mit-license.php MIT License
 * @link        https://github.com/mmikkel/dashcols-craft
 */

class DashCols_AttributesService extends BaseApplicationComponent
{

	private $_element,
			$_attribute,
			$_attributeHandle,
			$_attributeField;

	public function modifyIndexTableAttributes( &$attributes )
	{

		// Get layout
		if ( ! $dashColsLayout = craft()->dashCols_layouts->getLayout() ) {
			return false;
		}

		// Add meta fields
		if ( ( $metaFields = $dashColsLayout->metaFields ) && is_array( $metaFields ) ) {

			$allMetaFields = craft()->dashCols_fields->getMetaFields();

			foreach ( $metaFields as $attribute ) {

				if ( isset( $allMetaFields[ $attribute ] ) ) {
					$attributes[ $attribute ] = $allMetaFields[ $attribute ];
				}

			}

		}

		// Add custom fields
		if ( $customFields = $dashColsLayout->customFields ) {
			foreach ( $customFields as $customFieldHandle => $customField ) {
				$attributes[ $customFieldHandle ] = $customField;
			}

		}
		
		// Remove hidden fields
		if ( $dashColsLayout->hiddenFields && is_array( $dashColsLayout->hiddenFields ) ) {

			foreach ( $dashColsLayout->hiddenFields as $attribute ) {
				if ( isset( $attributes[ $attribute ] ) ) {
					unset( $attributes[ $attribute ] );
				}
			}

		}

	}

	public function modifyIndexSortableAttributes( &$attributes )
	{
		$sortableFields = craft()->dashCols_fields->getSortableFields();
		$attributes += $sortableFields;
	}

	 /**
	 * @access public
	 * @return mixed
	 */
	public function getAttributeHtml( $element, $attribute )
	{

		// Don't do anything for default attributes
		$defaultFields = craft()->dashCols_fields->getDefaultFields();
        if ( in_array( $attribute, array_keys( $defaultFields ) ) ) {
            return null;
        }

        // A little hack to retrieve the full author from the author ID
		if ( $attribute === 'authorId' ) {
			$attribute = 'author';
		} else if ( $attribute === 'typeId' ) {
			$attribute = 'type';
		}

		// Return early if element doesn't have the attribute
		if ( ! $elementAttribute = @$element->$attribute ) {
			return false;
		}

		// Cache the element and attribute value
		$this->_element = $element;
		$this->_attribute = $elementAttribute;
		$this->_attributeHandle = $attribute;

		// Cache data about the attribute's field
		$customFields = craft()->dashCols_fields->getCustomFields();
		$this->_attributeField = craft()->dashCols_fields->getCustomFieldByHandle( $this->_attributeHandle );

		// Return html from string or object value
		$attributeValue = is_object( $elementAttribute ) ? $this->_getObjectAttributeHtml() : $this->_getStringValueTableAttributeHtml();

		$attributeCssClasses = array( 'dashCols-attribute' );

		if ( $this->_attributeField ) {
			$attributeCssClasses[] = 'dashCols-' . lcfirst( $this->_attributeField->fieldType->classHandle );
		}

		return '<span class="' . implode( ' ', $attributeCssClasses ) . '">' . $attributeValue . '</span>';

	}

	/**
	 * @access private
	 * @return string
	 *
	 * Method returns attribute HTML for string values
	 *
	 */
	private function _getStringValueTableAttributeHtml() {

		$classHandle = $this->_attributeField ? $this->_attributeField->fieldType->classHandle : '';

		switch ( $classHandle ) {

			case 'Lightswitch' :
				
				$attributeHtml = $this->_getLightswitchTableAttributeHtml();
				
				break;

			case 'Color' :
				
				$attributeHtml = $this->_getColorTableAttributeHtml();
				
				break;

			default :

				// Could be a URL!
				if ( filter_var( $this->_attribute, FILTER_VALIDATE_URL ) ) {
					
					// ...but is it an external URL?
					$siteUrl = craft()->urlHelper->getSiteUrl();
					$url = $this->_attribute;
					$urlComponents = parse_url( $url );
  					$isExternal = ! empty( $urlComponents[ 'host' ] ) && strcasecmp( $urlComponents[ 'host' ], $_SERVER[ 'HTTP_HOST' ] );

					if ( ! $isExternal ) {
						$attributeHtml = '<a href="' . $url .'" class="go">' . $url . '</a>';
					} else {
						$attributeHtml = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
					}

				} else {
					
					// Ye olde generic string, I guess.
					$attributeHtml = trim( strip_tags( $this->_attribute ) );
					if ( mb_strlen( $attributeHtml ) > 47 ) {
						$attributeHtml = mb_substr( $attributeHtml, 0, 47 ) . '...';
					}

				}

				break;

		}

		return $attributeHtml;

	}

	/**
	 * @access private
	 * @return string
	 *
	 * Method returns attribute HTML for object values
	 *
	 */
	private function _getObjectAttributeHtml()
	{

		if ( $class = @get_class( $this->_attribute ) ) {

			switch ( $class ) {

				case 'Craft\ElementCriteriaModel' :

					return $this->_getElementCriteriaTableAttributeHtml();

					break;

				case 'Craft\DateTime' :

					return $this->_getDateTimeTableAttributeHtml();

					break;

				case 'Craft\MultiOptionsFieldData' :
				case 'Craft\SingleOptionFieldData' :

					return $this->_getOptionsFieldDataTableAttributeHtml();

					break;

				case 'Craft\UserModel' :

					return $this->_getUserTableAttributeHtml();

					break;

				case 'Craft\EntryTypeModel' :

					return $this->_getEntryTypeTableAttributeHtml();

				default :

					return 'Unknown class: ' . $class;

			}

		}

		return false;

	}

	/**
	 * @access private
	 * @return string
	 *
	 * Method returns attribute HTML for ElementCriteriaModel instances
	 *
	 */
	private function _getElementCriteriaTableAttributeHtml()
	{

		// Element types
		$classHandle = $this->_attribute->elementType->classHandle;

		switch ( $classHandle ) {

			case 'Asset' :

				return $this->_getAssetTableAttributeHtml();

				break;

			case 'User' :

				return $this->_getUserTableAttributeHtml();

				break;

			case 'Tag' :

				return $this->_getTagTableAttributeHtml();

				break;

			case 'Category' :
			case 'Entry' :

				return $this->_getEntryTableAttributeHtml();

				break;

			default :

				return 'ElementClass: ' . $classHandle;

		}

	}

	/**
	 * @access private
	 * @return string
	 *
	 * Method returns attribute HTML for Assets
	 *
	 */
	private function _getAssetTableAttributeHtml()
	{

		if ( ! $asset = $this->_attribute[ 0 ] ) {
			return false;
		}

		$totalCount = count( $this->_attribute );

		// I can haz SVG transforms?
		$svgTransformSupport = version_compare( craft()->getVersion(), '2.4', '>=' ) && craft()->images->isImagick();

		if ( $asset->kind === 'image' && ( strtolower( $asset->extension ) !== 'svg' || $svgTransformSupport ) ) {

			// Image
			$assetWidth = 60;
			$assetHeight = 60;

			$attributeHtmlClass = 'image';

			$attributeHtml = '<img src="' . $asset->getThumbUrl( $assetWidth, $assetHeight ) . '" width="' . $assetWidth . '" height="' . $assetHeight . '" alt="' . $asset->title . '" />';

			if ( $totalCount > 1 ) {
				$attributeHtml .= '<div class="dashCols-assetFileCount">' . $totalCount . ' ' . Craft::t( 'files' ) . '</div>';
			}

		} else {

			// File
			$iconSize = 20;

			$attributeHtmlClass = 'file';

			$attributeHtml = '<div class="dashCols-assetFile"><img src="' . $asset->getIconUrl( $iconSize ) . '" alt="" class="dashCols-assetFileIcon" />&nbsp;<span class="dashCols-assetFilename">'.$asset->filename . '</span></div>';

			if ( $totalCount > 1 ) {
				$attributeHtml .= '<div class="dashCols-assetFileCount">+ ' . ( $totalCount - 1 ) . ' ' . Craft::t( 'more' ) . '</div>';
			}

		}

		return '<div class="dashCols-assetField' . ucfirst( $attributeHtmlClass ) . '">' . $attributeHtml . '</div>';

	}

	/**
	 * @access private
	 * @return string
	 *
	 * Method returns attribute HTML for Entries and Categories
	 *
	 */
	private function _getEntryTableAttributeHtml()
	{

		$elements = $this->_attribute->find();
		$temp = array();

		foreach ( $elements as $element ) {

			$attribute = $element->title;

			if ( $element->cpEditUrl ) {
				$attribute = '<a href="' . $element->cpEditUrl . '">' . $attribute . '</a>';
			}

			$temp[] = $attribute;

		}

		return ! empty( $temp ) ? implode( ', ', $temp ) : false;

	}

	/**
	 * @access private
	 * @return string
	 *
	 * Method returns attribute HTML for Users
	 *
	 */
	private function _getUserTableAttributeHtml()
	{

		if ( ! $this->_attribute->id ) {
			$elements = $this->_attribute->find();
		} else {
			$elements = array( $this->_attribute );
		}

		$temp = array();

		foreach ( $elements as $element ) {

			$name = '';

			if ( $firstName = $element->firstName ) {
				$name = $firstName . ' ';
			}

			if ( $lastName = $element->lastName ) {
				$name .= $lastName;
			}

			$attribute = $name !== '' ? trim( $name ) : $element->name;

			if ( $element->cpEditUrl ) {
				$attribute = '<a href="' . $element->cpEditUrl . '">' . $attribute . '</a>';
			}

			$temp[] = $attribute;

		}

		return ! empty( $temp ) ? implode( ', ', $temp ) : false;

	}

	/**
	 * @access private
	 * @return string
	 *
	 * Method returns attribute HTML for Users
	 *
	 */
	private function _getEntryTypeTableAttributeHtml()
	{
		return $this->_attribute->name ?: '';
	}

	/**
	 * @access private
	 * @return string
	 *
	 * Method returns attribute HTML for Tags
	 *
	 */
	private function _getTagTableAttributeHtml()
	{

		$elements = $this->_attribute->find();
		$temp = array();

		foreach ( $elements as $element ) {

			$temp[] = $element->title;

		}

		return ! empty( $temp ) ? implode( ', ', $temp ) : false;

	}

	/**
	 * @access private
	 * @return string
	 *
	 * Method returns attribute HTML for MultiOptionsFieldData and SingleOptionFieldData
	 *
	 */
	private function _getOptionsFieldDataTableAttributeHtml()
	{

		$options = $this->_attribute->getOptions();
		$temp = array();

		foreach ( $options as $option ) {
			if ( $option->selected ) {
				$temp[] = $option->label;
			}
		}

		return ! empty( $temp ) ? implode( ', ', $temp ) : false;

	}

	/**
	 * @access private
	 * @return string
	 *
	 * Method returns attribute HTML for Date/Time
	 *
	 */
	private function _getDateTimeTableAttributeHtml()
	{

		if ( $this->_attributeField ) {

			$settings = $this->_attributeField->settings;

			if ( $settings[ 'showDate' ] ) {
				$date[] = $this->_attribute->localeDate();
			}

			if ( $settings[ 'showTime' ] ) {
				$date[] = $this->_attribute->localeTime();
			}

			return implode( ' ', $date );

		}

		return $this->_attribute->localeDate() . ' ' . $this->_attribute->localeTime();

	}

	/**
	 * @access private
	 * @return string
	 *
	 * Method returns attribute HTML for Color
	 *
	 */
	private function _getColorTableAttributeHtml()
	{

		return '<span class="dashCols-hex" style="background-color: ' . $this->_attribute . ';" title="' . $this->_attribute . '"></span>';

	}

	/**
	 * @access private
	 * @return string
	 *
	 * Method returns attribute HTML for Lightswitch
	 *
	 */
	private function _getLightswitchTableAttributeHtml()
	{
		return $this->_attribute === '1' ? '<span class="dashCols-lightswitchOn"></span>' : '';
	}

}