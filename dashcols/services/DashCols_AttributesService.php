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

	public function modifyEntryTableAttributes( &$attributes, $source )
	{
		if ( ! $dashColsLayout = craft()->dashCols->getLayoutFromEntrySource( $source ) ) {
			return false;
		}
		$this->modifyTableAttributes( $attributes, $dashColsLayout );
	}

	public function modifyCategoryTableAttributes( &$attributes, $source )
	{
		if ( ! $dashColsLayout = craft()->dashCols->getLayoutFromCategorySource( $source ) ) {
			return false;
		}
		$this->modifyTableAttributes( $attributes, $dashColsLayout );
	}

	protected function modifyTableAttributes( &$attributes, DashCols_LayoutModel $dashColsLayout )
	{

		// Get custom fields
		if ( $dashColsLayout->fieldLayoutId && $fieldLayout = craft()->fields->getLayoutById( $dashColsLayout->fieldLayoutId ) ) {

			$fieldLayoutFieldModels = $fieldLayout->getFields();
			$unsupportedFieldTypes = craft()->dashCols_fields->getUnsupportedFieldTypes();
			$fields = array();

			foreach ( $fieldLayoutFieldModels as $fieldLayoutFieldModel ) {
				if ( ! in_array( $fieldLayoutFieldModel->field->getFieldType()->name, $unsupportedFieldTypes ) ) {
					$attributes[ $fieldLayoutFieldModel->field->handle ] = Craft::t( $fieldLayoutFieldModel->field->name );
					$fields[] = $fieldLayoutFieldModel->field;
				}
			}

			// Cache fields in the fields service (used for setting lookups)
			craft()->dashCols_fields->setFields( $fields );

		}

		// Remove hidden fields
		if ( $dashColsLayout->hiddenFields && is_array( $dashColsLayout->hiddenFields ) ) {

			foreach ( $dashColsLayout->hiddenFields as $attribute ) {
				if ( isset( $attributes[ $attribute ] ) ) {
					unset( $attributes[ $attribute ] );
				}
			}

		}

		// TODO: Add meta fields

	}

}