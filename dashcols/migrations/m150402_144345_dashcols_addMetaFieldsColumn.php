<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m150402_144345_dashcols_addMetaFieldsColumn extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{
        // Adds column to store meta fields
        $this->addColumn( 'dashcols_layouts', 'metaFields', 'string' );
		return true;
	}
}
