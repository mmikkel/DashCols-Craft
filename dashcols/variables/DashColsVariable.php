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

class DashColsVariable
{

	private $_plugin = null;

	public function getPlugin()
	{
		return craft()->dashCols->getPlugin();
	}

	public function getCpTabs()
	{
		return craft()->dashCols->getCpTabs();
	}

	public function getPluginUrl()
	{
		return $this->getPlugin()->getPluginUrl();
	}

	public function getPluginName()
	{
		return $this->getPlugin()->getName();
	}

	public function version()
	{
		return $this->getPlugin()->getVersion();
	}

}