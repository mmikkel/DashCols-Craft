<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m151103_153210_dashcols_addAssetSourceIdColumn extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{
        // Adds column to store meta fields
		$this->addColumnAfter( 'dashcols_layouts', 'assetSourceId', ColumnType::Int, 'categoryGroupId' );
		$this->addForeignKey( 'dashcols_layouts', 'assetSourceId', 'assetsources', 'id', 'CASCADE', 'CASCADE' );
		return true;
	}
}
