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

    protected   $_version = '1.3',
                $_developer = 'Mats Mikkel Rummelhoff',
                $_developerUrl = 'http://mmikkel.no',
                $_pluginName = 'DashCols',
                $_pluginUrl = 'https://github.com/mmikkel/DashCols-Craft',
                $_minVersion = '2.3';

    public function getName()
    {
         return $this->_pluginName;
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
            'cpSectionDisabled' => array(AttributeType::Bool, 'default' => false),
       );
    }

    public function getSettingsHtml()
    {
        return craft()->templates->render('dashcols/settings', array(
            'settings' => $this->getSettings(),
        ));
    }

    public function registerCpRoutes()
    {

        return array(
            
            'dashcols' => array('action' => 'dashCols/layouts/getIndex'),
            
            // Entries
            'dashcols/entries(/(?P<sourceHandle>[-\w]+))?(/(?P<sectionHandleOrId>[-\w]+))?' => array('action' => 'dashCols/layouts/editEntriesLayout'),
            
            // Category group
            'dashcols/categories(/(?P<categoryGroupHandleOrId>[-\w]+))?' => array('action' => 'dashCols/layouts/editCategoryGroupLayout'),
            
            // User groups
            'dashcols/users(/(?P<userGroupHandleOrId>[-\w]+))?' => array('action' => 'dashCols/layouts/editUserGroupLayout'),

            // Asset sources
            'dashcols/assets(/(?P<assetSourceHandleOrId>[-\w]+))?' => array('action' => 'dashCols/layouts/editAssetSourceLayout'),

            // Todo: Add custom element types here

        );
    }

    public function init () {

        parent::init();

        if (!craft()->request->isCpRequest() || !craft()->userSession->getUser() || !$this->isCraftRequiredVersion()) {
            return false;
        }

        craft()->dashCols_layouts->init();
        $this->includeResources();

    }

    public function getCraftRequiredVersion()
    {
        return $this->_minVersion;
    }

    public function isCraftRequiredVersion()
    {
        return version_compare(craft()->getVersion(), $this->getCraftRequiredVersion(), '>=');
    }

    protected function includeResources()
    {

        if (craft()->request->isAjaxRequest()) {
            return false;
        }

        $segments = craft()->request->segments;

        if (!is_array($segments) || empty($segments)) {
            return false;
        }

        $elementIndexes = array('entries', 'categories', 'users', 'assets');

        // Todo: Add custom element types here

        if (in_array($segments[0], $elementIndexes))
        {
            // Index tables
            craft()->templates->includeCssResource('dashcols/css/dashcols.index.css');
            craft()->templates->includeJsResource('dashcols/js/dashcols.index.js');
        }
        else if ($segments[0] === 'dashcols')
        {
            // DashCols' CP section
            craft()->templates->includeCssResource('dashcols/css/dashcols.cp.css');
            craft()->templates->includeJsResource('dashcols/js/dashcols.cp.js');
        }
        else
        {
            return false;
        }

        $settings = json_encode($this->getSettings()->attributes);
        craft()->templates->includeJs('window._DashCols='.$settings.';');

    }

    /*
    *   Modify index table attributes
    *
    */
    public function modifyEntryTableAttributes(&$attributes, $source)
    {
        craft()->dashCols_layouts->setLayoutFromEntrySource($source);
        craft()->dashCols_attributes->modifyIndexTableAttributes($attributes);
    }

    public function modifyCategoryTableAttributes(&$attributes, $source)
    {
        craft()->dashCols_layouts->setLayoutFromCategorySource($source);
        craft()->dashCols_attributes->modifyIndexTableAttributes($attributes);
    }

    public function modifyAssetTableAttributes(&$attributes, $source)
    {
        craft()->dashCols_layouts->setLayoutFromAssetSource($source);
        craft()->dashCols_attributes->modifyIndexTableAttributes($attributes);
    }

    public function modifyUserTableAttributes(&$attributes, $source)
    {
        craft()->dashCols_layouts->setLayoutFromUserSource($source);
        craft()->dashCols_attributes->modifyIndexTableAttributes($attributes);
    }

    /*
    *   Modify sortable attributes
    *
    */
    public function modifyEntrySortableAttributes(&$attributes)
    {
        craft()->dashCols_attributes->modifyIndexSortableAttributes($attributes);
    }

    public function modifyCategorySortableAttributes(&$attributes)
    {
        craft()->dashCols_attributes->modifyIndexSortableAttributes($attributes);
    }

    public function modifyAssetSortableAttributes(&$attributes)
    {
        craft()->dashCols_attributes->modifyIndexSortableAttributes($attributes);   
    }

    public function modifyUserSortableAttributes(&$attributes)
    {
        craft()->dashCols_attributes->modifyIndexSortableAttributes($attributes);
    }

    /*
    *   Get table attribute HTML
    *
    */
    public function getEntryTableAttributeHtml(EntryModel $entry, $attribute)
    {
        return craft()->dashCols_attributes->getAttributeHtml($entry, $attribute);
    }

    public function getCategoryTableAttributeHtml(CategoryModel $category, $attribute)
    {
        return craft()->dashCols_attributes->getAttributeHtml($category, $attribute);
    }

    public function getAssetTableAttributeHtml(AssetFileModel $asset, $attribute)
    {
        return craft()->dashCols_attributes->getAttributeHtml($asset, $attribute);
    }

    public function getUserTableAttributeHtml(UserModel $user, $attribute)
    {
        return craft()->dashCols_attributes->getAttributeHtml($user, $attribute);
    }

}
