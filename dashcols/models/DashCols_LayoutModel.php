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

class DashCols_LayoutModel extends BaseModel
{

	/**
	 * @access protected
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array(
			'id' => AttributeType::Number,
			'sectionId' => AttributeType::Number,
			'categoryGroupId' => AttributeType::Number,
			'listingHandle' => AttributeType::String,
			'fieldLayoutId' => AttributeType::Number,
			'customFields' => AttributeType::Mixed,
			'hiddenFields' => AttributeType::Mixed,
			'metaFields' => AttributeType::Mixed,
		);
	}

	/**
	 * @return array
	 */
	public function behaviors()
	{
		return array(
			'fieldLayout' => new FieldLayoutBehavior( null ),
		);
	}

}