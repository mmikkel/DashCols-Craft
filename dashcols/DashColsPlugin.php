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

    protected   $_version = '1.0.2',
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

    public function getDefaultFieldTypes()
    {
        return $this->defaultFieldTypes;
    }

    public function hasCpSection()
    {
        return true;
    }

    public function init () {

        parent::init();

        if ( craft()->request->isCpRequest() ) {
            craft()->templates->includeCssResource( 'dashCols/dist/css/dashcols.css' );
            craft()->templates->includeJsResource( 'dashCols/dist/js/entryTable.js' );
        }

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

    /*
    * Adds and removes entry table attributes based on settings
    *
    */
    public function modifyEntryTableAttributes( &$attributes, $source )
    {

        $fieldLayoutId = false;
        $sectionEditUrl = false;

        switch ( $source ) {

            case '*' : case 'singles' :

                // Listing
                if ( $source === '*' ) {
                    $source = 'entries';
                }

                if ( $dashColsLayout = craft()->dashCols->getLayoutByListingHandle( $source ) ) {
                    $fieldLayoutId = $dashColsLayout->fieldLayoutId;
                }

                $sectionEditUrl = UrlHelper::getUrl( 'dashcols/layouts/listing/' . $source );

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

                $sectionEditUrl = UrlHelper::getUrl( 'dashcols/layouts/section/' . $temp[ 1 ] );

        }

        if ( ! $fieldLayoutId ) {
            return false;
        }

        // Get field layout and fields
        $fieldLayout = craft()->fields->getLayoutById( $fieldLayoutId );
        $fields = $fieldLayout->getFields();

        foreach ( $fields as $field ) {
            if ( ! in_array( $field->field->getFieldType()->name, $this->unsupportedFieldTypes ) ) {
                $attributes[ $field->field->handle ] = Craft::t( $field->field->name );
            }
        }

    }

    /*
    * Adds and removes entry table attributes based on settings
    *
    */
    public function modifyCategoryTableAttributes( &$attributes, $source )
    {

        $fieldLayoutId = false;

        // Category group
        $temp = explode( ':', $source );
        if ( $temp[ 0 ] != 'group' || ! isset( $temp[ 1 ] ) || ! is_numeric( $temp[ 1 ] ) ) {
            return false;
        }

        if ( $dashColsLayout = craft()->dashCols->getLayoutByCategoryGroupId( $temp[ 1 ] ) ) {
            $fieldLayoutId = $dashColsLayout->fieldLayoutId;
        }

        if ( ! $fieldLayoutId ) {
            return false;
        }

        // Get field layout and fields
        $fieldLayout = craft()->fields->getLayoutById( $fieldLayoutId );
        $fields = $fieldLayout->getFields();

        foreach ( $fields as $field ) {
            if ( ! in_array( $field->field->getFieldType()->name, $this->unsupportedFieldTypes ) ) {
                $attributes[ $field->field->handle ] = Craft::t( $field->field->name );
            }
        }

    }

    public function getEntryTableAttributeHtml( EntryModel $entry, $attribute )
    {
        return craft()->dashCols_attributeHtml->getAttributeHtml( $entry, $attribute );
    }

    public function getCategoryTableAttributeHtml( CategoryModel $category, $attribute )
    {
        return craft()->dashCols_attributeHtml->getAttributeHtml( $category, $attribute );
    }

}
