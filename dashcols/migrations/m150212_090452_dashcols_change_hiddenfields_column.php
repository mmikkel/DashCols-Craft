<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m150212_090452_dashcols_change_hiddenfields_column extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{
        $this->alterColumn( 'dashcols_layouts', 'hiddenFields', 'string' );
		return true;
	}
}
