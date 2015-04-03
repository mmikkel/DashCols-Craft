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

    protected $_customFields = null;

    /*
    * Get FieldTypes that aren't supported by DashCols
    *
    */
    public function getUnsupportedFieldTypes()
    {
        return array( 'Rich Text', 'Table', 'Matrix' );
    }

    /*
    * Return map of default fields in Craft
    *
    */
    public function getDefaultFields( $target = false )
    {

        $defaultFields = array(
            'uri' => Craft::t( 'URI' ),
            'postDate' => Craft::t( 'Post Date' ),
            'expiryDate' => Craft::t( 'Expiry Date' ),
            'section' => Craft::t( 'Section' ),
        );

        switch ( $target ) {

            case 'singles' :
            case 'categoryGroup' :

                return array_intersect_key( $defaultFields, array_flip( array( 'uri' ) ) );

                break;

            case 'section' :

                return array_intersect_key( $defaultFields, array_flip( array( 'uri', 'postDate', 'expiryDate' ) ) );

                break;

            default :

                return $defaultFields;

        }

    }

    /*
    * Return map of metadata fields in Craft
    *
    */
    public function getMetaFields( $target = false )
    {
        
        $metaFields = array(
            'id' => Craft::t( 'ID' ),
            'dateUpdated' => Craft::t( 'Updated Date' ),
            'authorId' => Craft::t( 'Author' ),
        );

        switch ( $target ) {

            case 'categoryGroup' :

                return array_intersect_key( $metaFields, array_flip( array( 'id', 'dateUpdated' ) ) );

                break;

            default :

                return $metaFields;

        }

    }

    public function getCustomFields()
    {
        return $this->_customFields ?: array();
    }

    public function getCustomFieldByHandle( $handle )
    {
        return isset( $this->_customFields[ $handle ] ) ? $this->_customFields[ $handle ] : false;
    }

    /*
    * Gets and caches fields from a DashCols layout's FieldLayoutModel
    *
    */
    public function setLayout( $dashColsLayout )
    {

        $this->_customFields = array();

        if (
            ! $dashColsLayout
            || ! $dashColsLayout->fieldLayoutId
            || ! $fieldLayout = craft()->fields->getLayoutById( $dashColsLayout->fieldLayoutId )
        ) {
            return false;
        }

        $fieldLayoutFieldModels = $fieldLayout->getFields();
        $fields = array();
        
        foreach ( $fieldLayoutFieldModels as $fieldLayoutFieldModel ) {
            if ( ! in_array( $fieldLayoutFieldModel->field->getFieldType()->name, $this->getUnsupportedFieldTypes() ) ) {
                $this->_customFields[ $fieldLayoutFieldModel->field->handle ] = $fieldLayoutFieldModel->field;
            }
        }

    }

}