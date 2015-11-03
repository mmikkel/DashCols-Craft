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

class DashCols_FieldsService extends BaseApplicationComponent
{

    // Cache all custom fields here
    protected $_customFields = null;

    /*
    * Get FieldTypes that aren't supported by DashCols
    *
    */
    public function getUnsupportedFieldTypes()
    {
        return array('Rich Text', 'Table', 'Matrix');
    }

    /*
    * Return map of default fields in Craft
    *
    */
    public function getDefaultFields($target = false)
    {

        switch ($target) {

            case 'singles' :
            case 'categories' :

                return array(
                    'uri' => Craft::t('URI'),
                );

                break;

            case 'section' :

                return array(
                    'uri' => Craft::t('URI'),
                    'postDate' => Craft::t('Post Date'),
                    'expiryDate' => Craft::t('Expiry Date'),
                );

                break;

            case 'users' :

                return array(
                    'firstName' => Craft::t('First Name'),
                    'lastName' => Craft::t('Last Name'),
                    'email' => Craft::t('Email'),
                    'dateCreated' => Craft::t('Join Date'),
                    'lastLoginDate' => Craft::t('Last Login'),
                );

                break;

            case 'assets' :

                return array(
                    'filename' => Craft::t('Filename'),
                    'size' => Craft::t('Size'),
                    'dateModified' => Craft::t('Date Modified'),
                );

                break;

            default :

                return array(
                    'uri' => Craft::t('URI'),
                    'postDate' => Craft::t('Post Date'),
                    'expiryDate' => Craft::t('Expiry Date'),
                    'section' => Craft::t('Section'),
                );

        }

    }

    /*
    * Return map of metadata fields in Craft
    *
    */
    public function getMetaFields($target = false)
    {

        switch ($target) {

            case 'categories' :

                return array(
                    'id' => Craft::t('ID'),
                    'dateUpdated' => Craft::t('Updated Date'),
                );

                break;

            case 'users' :

                return array(
                    'id' => Craft::t('ID'),
                    'preferredLocale' => Craft::t('Preferred Locale'),
                    'weekStartDay' => Craft::t('Week Start Day'),   
                );

                break;

            case 'assets' :

                return array(
                    'id' => Craft::t('ID'),
                    'kind' => Craft::t('Kind'),
                    'width' => Craft::t('Width'),
                    'height' => Craft::t('Height'),
                    'dateCreated' => Craft::t('Created Date'),
                    'dateUpdated' => Craft::t('Updated Date'),
                );

                break;

            default :

                return array(
                    'id' => Craft::t('ID'),
                    'dateUpdated' => Craft::t('Updated Date'),
                    'authorId' => Craft::t('Author'),
                    'typeId' => Craft::t('Entry Type'),
                    'kind' => Craft::t('Kind'),
                    'width' => Craft::t('Width'),
                    'height' => Craft::t('Height'),
                    'dateCreated' => Craft::t('Created Date'),
                    'dateUpdated' => Craft::t('Updated Date'),
                    'preferredLocale' => Craft::t('Preferred Locale'),
                    'weekStartDay' => Craft::t('Week Start Day'),
                );

        }

    }

    /*
    *   Get all custom fields
    *
    */
    public function getCustomFields()
    {
        return $this->_customFields ?: array();
    }

    /*
    *   Add custom fields to the cache
    *
    */
    public function addCustomFields($fields)
    {

        $customFields = $this->getCustomFields();

        foreach ($fields as $field) {

            if (! isset($customFields[$field->handle])) {
                $customFields[$field->handle] = $field;
            }

        }

        $this->_customFields = $customFields;

    }

    /*
    *   Get custom field by handle
    *
    */
    public function getCustomFieldByHandle($handle)
    {
        return isset($this->_customFields[$handle]) ? $this->_customFields[$handle] : false;
    }

    /*
    *   Add a FieldLayoutModel's fields to the cache
    *
    */
    public function getCustomFieldsFromFieldLayout(FieldLayoutModel $fieldLayout)
    {

        $fields = array();
        $fieldLayoutFieldModels = $fieldLayout->getFields();

        foreach ($fieldLayoutFieldModels as $fieldLayoutFieldModel) {
            if (! in_array($fieldLayoutFieldModel->field->getFieldType()->name, $this->getUnsupportedFieldTypes())) {
                $fields[$fieldLayoutFieldModel->field->handle] = $fieldLayoutFieldModel->field;
            }
        }

        return $fields;

    }

    /*
    *   Get all sortable fields
    *
    */
    public function getSortableFields()
    {

        $sortableAttributeTypes = array(
            AttributeType::Number,
            AttributeType::DateTime,
            AttributeType::String,
            AttributeType::Bool,
       );

        $sortableFields = $this->getMetaFields();
        $customFields = $this->getCustomFields();

        foreach ($customFields as $handle => $field) {

            $fieldTypeContentAttribute = $field->fieldType->defineContentAttribute();

            if (is_array($fieldTypeContentAttribute)) {
                $fieldTypeContentAttribute = array_shift($fieldTypeContentAttribute);
            }

            if (in_array($fieldTypeContentAttribute, $sortableAttributeTypes)) {
                $sortableFields[$handle] = $field;
            }

        }

        return $sortableFields;
    }

}