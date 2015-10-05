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

class DashCols_LayoutRecord extends BaseRecord
{
	public function getTableName()
	{
		return 'dashcols_layouts';
	}

	/**
	 * @access protected
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array(
			'sectionId' => AttributeType::Number,
			'categoryGroupId' => AttributeType::Number,
			'userGroupId' => AttributeType::Number,
			'listingHandle' => AttributeType::String,
			'fieldLayoutId' => AttributeType::Number,
			'hiddenFields' => AttributeType::Mixed,
			'metaFields' => AttributeType::Mixed,
		);
	}

	/**
	 * @access public
	 * @return array
	 */
	public function defineRelations()
	{
		return array(
			'section' => array(
				static::BELONGS_TO,
				'SectionRecord',
				'sectionId',
				'onDelete' => static::CASCADE,
			),
			'categoryGroup' => array(
				static::BELONGS_TO,
				'CategoryGroupRecord',
				'categoryGroupId',
				'onDelete' => static::CASCADE,
			),
			'userGroup' => array(
				static::BELONGS_TO,
				'UserGroupRecord',
				'userGroupId',
				'onDelete' => static::CASCADE,
			),
			'fieldLayout' => array(
				static::BELONGS_TO,
				'FieldLayoutRecord',
				'onDelete' => static::SET_NULL
			),
		);
	}

}