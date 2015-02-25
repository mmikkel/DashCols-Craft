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

    protected   $_version = '1.1.7',
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

            $dashColsSegments = array( 'entries', 'categories', 'dashcols' );
            $segments = craft()->request->segments;

            if ( is_array( $segments ) && isset( $segments[ 0 ] ) && in_array( $segments[ 0 ], $dashColsSegments ) ) {
                craft()->templates->includeCssResource( 'dashcols/css/dashcols.min.css' );
            }

        }

    }

    public function modifyEntryTableAttributes( &$attributes, $source )
    {
        craft()->dashCols_attributes->modifyEntryTableAttributes( $attributes, $source );
        craft()->templates->includeJsResource( 'dashcols/js/entryTable.min.js' );
    }

    public function modifyCategoryTableAttributes( &$attributes, $source )
    {
        craft()->dashCols_attributes->modifyCategoryTableAttributes( $attributes, $source );
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

}
