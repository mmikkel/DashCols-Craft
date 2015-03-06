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

class DashCols_AttributeHtmlService extends BaseApplicationComponent
{

	private $_element,
			$_attribute,
			$_attributeHandle;

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

		// Return early if element doesn't have the attribute
		if ( ! $elementAttribute = @$element->$attribute ) {
			return false;
		}

		// Cache the element and attribute value
		$this->_element = $element;
		$this->_attribute = $elementAttribute;
		$this->_attributeHandle = $attribute;

		// Return html from string or object value
		return is_object( $elementAttribute ) ? $this->_getObjectAttributeHtml() : $this->_getStringValueTableAttributeHtml();

	}

	/**
	 * @access private
	 * @return string
	 *
	 * Method returns attribute HTML for string values
	 *
	 */
	private function _getStringValueTableAttributeHtml() {

		// TODO: Check type of field for color and lightswitch!

		if ( filter_var( $this->_attribute, FILTER_VALIDATE_URL ) ) {
			// URL
			return '<a href="' . $this->_attribute .'" class="go">' . $this->_attribute . '</a>';
		} else if ( preg_match( '/^#[a-f0-9]{6}$/i', $this->_attribute ) ) {
			// Hex color code?
			return $this->_getColorTableAttributeHtml();
		} else if ( $this->_attribute === '1' || $this->_attribute === '0' ) {
			// If 1 or 0 – probably a lightswitch
			return $this->_getLightswitchTableAttributeHtml();
		}

		// Return string value
		return trim( strip_tags( $this->_attribute ) );

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

		if ( $asset->kind === 'image' && strtolower( $asset->extension ) !== 'svg' ) {

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

		$elements = $this->_attribute->find();
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

		if ( $field = craft()->dashCols_fields->getFieldByHandle( $this->_attributeHandle ) ) {

			$settings = $field->settings;

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

		return '<span class="dashcols-hexcolor" style="display: block; width: 20px; height: 20px; border-radius: 2px;' . ( strtolower( $this->_attribute ) === '#ffffff' ? 'border: 1px solid #E5E5E5; ' : '' ) . 'background-color: ' . $this->_attribute . ';" title="' . $this->_attribute . '"></span>';

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

		return $this->_attribute === '1' ? '<span class="dashCols-lightswitch"></span>' : false;

	}

}