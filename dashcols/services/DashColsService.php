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

class DashColsService extends BaseApplicationComponent
{

	private $_plugin = null,
			$_sections = null,
			$_channels = null,
			$_structures = null,
			$_categoryGroups = null,
			$_userGroups = null;

	/*
	* Returns the DashCols plugin for use in variables and the like
	*
	*/
	public function getPlugin()
	{
		if ($this->_plugin === null) {
			$this->_plugin = craft()->plugins->getPlugin('dashCols');
		}
		return $this->_plugin;
	}

	/*
	* Return CP section tabs
	*
	*/
	public function getCpTabs()
	{
		return array(
			'dashColsIndex' => array(
				'label' => '',
				'url' => UrlHelper::getUrl('dashcols'),
			),
			'about' => array(
				'label' => Craft::t('About DashCols'),
				'url' => UrlHelper::getUrl('dashcols/about'),
			),
			'settings' => array(
				'label' => Craft::t('Settings'),
				'url' => UrlHelper::getUrl('settings/plugins/dashcols'),
			),
		);
	}

	public function isCpSectionDisabled()
	{
		$settings = $this->getPlugin()->getSettings();
        return isset($settings['cpSectionDisabled']) && $settings['cpSectionDisabled'];
	}

	/*
	*	Entries
	*
	*/
	public function getSections()
	{
		if ($this->_sections === null) {
			$this->_sections = craft()->sections->allSections;
        }
        return $this->_sections;
	}

	public function getChannels()
	{
		if ($this->_channels === null) {
			$sections = $this->getSections();
			$channels = array();
			foreach ($sections as $section) {
				if ($section->type == 'channel') {
					$channels[] = $section;
				}
			}
			$this->_channels = $channels;
		}

		return $this->_channels;
	}

	public function getStructures()
	{
		if ($this->_structures === null) {
			$sections = $this->getSections();
			$structures = array();
			foreach ($sections as $section) {
				if ($section->type == 'structure') {
					$structures[] = $section;
				}
			}
			$this->_structures = $structures;
		}

		return $this->_structures;
	}

	public function getSectionByHandleOrId($sectionHandleOrId)
	{
		return ctype_digit($sectionHandleOrId) ? $this->getSectionById($sectionHandleOrId) : $this->getSectionByHandle($sectionHandleOrId);
	}

	public function getSectionById($sectionId)
	{
		foreach ($this->getSections() as $section) {
			if ($section->id == $sectionId) {
				return $section;
			}
		}
		return false;
	}

	public function getSectionByHandle($sectionHandle)
	{
		foreach ($this->getSections() as $section) {
			if ($section->handle == $sectionHandle) {
				return $section;
			}
		}
		return false;
	}

	/*
	*	Category groups
	*
	*/
	public function getCategoryGroups()
	{
		if ($this->_categoryGroups === null) {
			$this->_categoryGroups = craft()->categories->allGroups;
		}
		return $this->_categoryGroups;
	}

	public function getCategoryGroupByHandleOrId($categoryGroupHandleOrId)
	{
		return ctype_digit($categoryGroupHandleOrId) ? $this->getCategoryGroupById($categoryGroupHandleOrId) : $this->getCategoryGroupByHandle($categoryGroupHandleOrId);
	}

	public function getCategoryGroupById($categoryGroupId)
	{
		foreach ($this->getCategoryGroups() as $categoryGroup) {
			if ($categoryGroup->id == $categoryGroupId) {
				return $categoryGroup;
			}
		}
		return false;
	}

	public function getCategoryGroupByHandle($categoryGroupHandle)
	{
		foreach ($this->getCategoryGroups() as $categoryGroup) {
			if ($categoryGroup->handle == $categoryGroupHandle) {
				return $categoryGroup;
			}
		}
		return false;
	}

	/*
	*	User groups
	*
	*/
	public function getUserGroups()
	{
		if ($this->_userGroups === null) {
			$this->_userGroups = craft()->userGroups->allGroups;
		}
		return $this->_userGroups;
	}

	public function getUserGroupByHandleOrId($userGroupHandleOrId)
	{
		return ctype_digit($userGroupHandleOrId) ? $this->getUserGroupById($userGroupHandleOrId) : $this->getUserGroupByHandle($userGroupHandleOrId);
	}

	public function getUserGroupById($userGroupId)
	{
		foreach ($this->getUserGroups() as $userGroup)
		{
			if ($userGroup->id === $userGroupId) return $userGroup;
		}
		return false;
	}

	public function getUserGroupByHandle($userGroupHandle)
	{
		foreach ($this->getUserGroups() as $userGroup)
		{
			if ($userGroup->handle === $userGroupHandle) return $userGroup;
		}
		return false;
	}

	/*
	*	Listings
	*
	*/
	public function getListingByHandle($listingHandle)
	{
		$listing = (object) array();
		switch ($listingHandle) {
			case '*' : case 'entries' :
				$listing->name = Craft::t('All entries');
				$listing->url = 'entries';
				break;
			case 'singles' :
				$listing->name = Craft::t('Singles');
				$listing->url = 'entries/singles';
				break;
			case 'users' :
				$listing->name = Craft::t('All users');
				$listing->url = 'users';
				break;
			default :
				return false;
		}
		return $listing;
	}

}