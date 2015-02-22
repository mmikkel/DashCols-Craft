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