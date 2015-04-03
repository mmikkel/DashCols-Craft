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

    protected $_fields = null;

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

            case 'entries' :

                return $defaultFields;

                break;

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

    public function setFields( $fields )
    {

        $this->_fields = array();

        // Cache fields by handle
        if ( is_array( $fields ) ) {
            foreach ( $fields as $field ) {
                $this->_fields[ $field->handle ] = $field;
            }
        }

    }

    public function getFields()
    {
        return $this->_fields ?: array();
    }

    public function getFieldByHandle( $handle )
    {
        return isset( $this->_fields[ $handle ] ) ? $this->_fields[ $handle ] : false;
    }

}