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

    protected   $_version = '1.1.9',
                $_developer = 'Mats Mikkel Rummelhoff',
                $_developerUrl = 'http://mmikkel.no',
                $_pluginName = 'DashCols',
                $_pluginUrl = 'https://github.com/mmikkel/DashCols-Craft';

    public function getName()
    {
         return $this->getSettings()->name ?: $this->_pluginName;
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
            'name' => array( AttributeType::String, 'default' => $this->_pluginName ),
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

        if ( ! craft()->request->isCpRequest() ) {
            return false;
        }

        craft()->dashCols_layouts->init();
        $this->includeResources();

    }

    protected function includeResources()
    {

        if ( craft()->request->isAjaxRequest() ) {
            return false;
        }

        $segments = craft()->request->segments;

        if ( ! is_array( $segments ) || empty( $segments ) ) {
            return false;
        }

        switch ( $segments[ 0 ] ) {

            case 'entries' : case 'categories' :

                // Index tables
                craft()->templates->includeCssResource( 'dashcols/css/dashcols.index.css' );
                craft()->templates->includeJsResource( 'dashcols/js/dashcols.index.js' );

                break;

            case 'dashcols' :

                // DashCols' CP section
                craft()->templates->includeCssResource( 'dashcols/css/dashcols.cp.css' );
                craft()->templates->includeJsResource( 'dashcols/js/dashcols.cp.js' );

                break;

        }

    }

    /*
    *   Modify entry table attributes
    *
    */
    public function modifyEntryTableAttributes( &$attributes, $source )
    {
        craft()->dashCols_layouts->setLayoutFromEntrySource( $source );
        craft()->dashCols_attributes->modifyIndexTableAttributes( $attributes );
    }

    /*
    *   Modify category table attributes
    *
    */
    public function modifyCategoryTableAttributes( &$attributes, $source )
    {
        craft()->dashCols_layouts->setLayoutFromCategorySource( $source );
        craft()->dashCols_attributes->modifyIndexTableAttributes( $attributes );
    }

    /*
    *   Modify sortable attributes
    *
    */
    public function modifyEntrySortableAttributes( &$attributes )
    {
        craft()->dashCols_attributes->modifyIndexSortableAttributes( $attributes );
    }

    public function modifyCategorySortableAttributes( &$attributes )
    {
        craft()->dashCols_attributes->modifyIndexSortableAttributes( $attributes );
    }

    /*
    *   Get table attribute HTML
    *
    */
    public function getEntryTableAttributeHtml( EntryModel $entry, $attribute )
    {
        return craft()->dashCols_attributes->getAttributeHtml( $entry, $attribute );
    }

    public function getCategoryTableAttributeHtml( CategoryModel $category, $attribute )
    {
        return craft()->dashCols_attributes->getAttributeHtml( $category, $attribute );
    }

}
