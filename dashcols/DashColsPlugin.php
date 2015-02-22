<?php namespace Craft;

/**
 * DashCols makes it easy to add custom fields to element index tables.
 *
 * @author      Mats Mikkel Rummelhoff <http://mmikkel.no>
 * @package     DashCols
 * @since       Craft 2.3
 * @copyright   Copyright (c) 2015, Mats Mikkel Rummelhoff
 * @license     http://opensource.org/licenses/mit-license.php MIT License
 * @link        https://github.com/mmikkel/DashCols-Craft
 */

class DashColsPlugin extends BasePlugin
{

    public      $unsupportedFieldTypes = array( 'Rich Text', 'Table', 'Matrix' );

    protected   $_version = '1.1.5',
                $_developer = 'Mats Mikkel Rummelhoff',
                $_developerUrl = 'http://mmikkel.no',
                $_pluginUrl = 'https://github.com/mmikkel/DashCols-Craft';

    public function getName()
    {
         return Craft::t( 'DashCols' );
    }

    public function getVersion()
    {
        return $this->_version;
    }

    public function getDeveloper()
    {
        return $this->_developer;
    }

    public function getDeveloperUrl()
    {
        return $this->_developerUrl;
    }

    public function getPluginUrl()
    {
        return $this->_pluginUrl;
    }

    public function hasCpSection()
    {
        return craft()->dashCols->isCpSectionDisabled() ? false : true;
    }

    protected function defineSettings()
    {
        return array(
            'cpSectionDisabled' => array( AttributeType::Bool, 'default' => false ),
        );
    }

    public function getSettingsHtml()
    {
        return craft()->templates->render( 'dashcols/settings', array(
            'settings' => $this->getSettings(),
        ) );
    }

    public function prepSettings( $settings )
    {
        if ( isset( $settings[ 'cpSectionDisabled' ] ) )
        {

        }
        return $settings;
    }

    public function registerCpRoutes()
    {

        return array(
            'dashcols' => array( 'action' => 'dashCols/layouts/getIndex' ),
            'dashcols/layouts' => array( 'action' => 'dashCols/layouts/getIndex' ),
            'dashcols/layouts/section/(?P<sectionHandle>[-\w]+)' => array( 'action' => 'dashCols/layouts/editSectionLayout' ),
            'dashcols/layouts/category-group/(?P<categoryGroupHandle>[-\w]+)' => array( 'action' => 'dashCols/layouts/editCategoryGroupLayout' ),
            'dashcols/layouts/listing/(?P<listingHandle>[-\w]+)' => array( 'action' => 'dashCols/layouts/editListingLayout' ),
        );

    }

    public function init () {

        parent::init();

        if ( craft()->request->isCpRequest() ) {
            craft()->templates->includeCssResource( 'dashcols/css/dashcols.min.css' );
        }

    }

    /*
    * Adds and removes entry table attributes based on settings
    *
    */
    public function modifyEntryTableAttributes( &$attributes, $source )
    {

        $dashColsLayout = false;
        $fieldLayoutId = false;

        switch ( $source ) {

            case '*' : case 'singles' :

                // Listing
                if ( $source === '*' ) {
                    $source = 'entries';
                }
                if ( $dashColsLayout = craft()->dashCols->getLayoutByListingHandle( $source ) ) {
                    $fieldLayoutId = $dashColsLayout->fieldLayoutId;
                }

                break;

            default :

                // Section
                $temp = explode( ':', $source );
                if ( $temp[ 0 ] != 'section' || ! isset( $temp[ 1 ] ) || ! is_numeric( $temp[ 1 ] ) ) {
                    return false;
                }
                if ( $dashColsLayout = craft()->dashCols->getLayoutBySectionId( $temp[ 1 ] ) ) {
                    $fieldLayoutId = $dashColsLayout->fieldLayoutId;
                }

        }

        if ( $dashColsLayout ) {
            $this->_removeDefaultAttributes( $dashColsLayout, $attributes );
        }

        if ( $fieldLayoutId ) {
            $this->_addFieldLayoutAttributes( $fieldLayoutId, $attributes );
        }

        craft()->templates->includeJsResource( 'dashcols/js/entryTable.min.js' );

    }

    /*
    * Adds and removes entry table attributes based on settings
    *
    */
    public function modifyCategoryTableAttributes( &$attributes, $source )
    {

        $dashColsLayout = false;
        $fieldLayoutId = false;

        // Category group
        $temp = explode( ':', $source );
        if ( $temp[ 0 ] != 'group' || ! isset( $temp[ 1 ] ) || ! is_numeric( $temp[ 1 ] ) ) {
            return false;
        }

        if ( $dashColsLayout = craft()->dashCols->getLayoutByCategoryGroupId( $temp[ 1 ] ) ) {
            $fieldLayoutId = $dashColsLayout->fieldLayoutId;
        }

        if ( $dashColsLayout ) {
            $this->_removeDefaultAttributes( $dashColsLayout, $attributes );
        }

        if ( $fieldLayoutId ) {
            $this->_addFieldLayoutAttributes( $fieldLayoutId, $attributes );
        }

        craft()->templates->includeJsResource( 'dashcols/js/entryTable.min.js' );

    }

    public function getEntryTableAttributeHtml( EntryModel $entry, $attribute )
    {
        return craft()->dashCols_attributeHtml->getAttributeHtml( $entry, $attribute );
    }

    public function getCategoryTableAttributeHtml( CategoryModel $category, $attribute )
    {
        return craft()->dashCols_attributeHtml->getAttributeHtml( $category, $attribute );
    }

    protected function _removeDefaultAttributes( DashCols_LayoutModel $dashColsLayout, &$attributes )
    {
        if ( ! is_array( $dashColsLayout->hiddenFields ) ) {
            return false;
        }
        foreach ( $dashColsLayout->hiddenFields as $attribute ) {
            if ( isset( $attributes[ $attribute ] ) ) {
                unset( $attributes[ $attribute ] );
            }
        }
    }

    protected function _addFieldLayoutAttributes( $fieldLayoutId, &$attributes )
    {

        if ( ! $fieldLayoutId ) {
            return false;
        }

        if ( ! $fieldLayout = craft()->fields->getLayoutById( $fieldLayoutId ) ) {
            return false;
        }

        $fieldLayoutFieldModels = $fieldLayout->getFields();
        $fields = array();

        foreach ( $fieldLayoutFieldModels as $fieldLayoutFieldModel ) {
            if ( ! in_array( $fieldLayoutFieldModel->field->getFieldType()->name, $this->unsupportedFieldTypes ) ) {
                $attributes[ $fieldLayoutFieldModel->field->handle ] = Craft::t( $fieldLayoutFieldModel->field->name );
                $fields[] = $fieldLayoutFieldModel->field;
            }
        }

        // Cache fields (used for lookups by the AttributeHtml service)
        craft()->dashCols_fields->setFields( $fields );

    }

}
