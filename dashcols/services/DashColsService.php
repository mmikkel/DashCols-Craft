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
			$_categoryGroups = null;

	/*
	* Returns the DashCols plugin for use in variables and the like
	*
	*/
	public function getPlugin()
	{
		if ( $this->_plugin === null ) {
			$this->_plugin = craft()->plugins->getPlugin( 'dashCols' );
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
				'url' => UrlHelper::getUrl( 'dashcols' ),
			),
			'about' => array(
				'label' => Craft::t( 'About DashCols' ),
				'url' => UrlHelper::getUrl( 'dashcols/about' ),
			),
			'settings' => array(
				'label' => Craft::t( 'Settings' ),
				'url' => UrlHelper::getUrl( 'settings/plugins/dashcols' ),
			),
		);
	}

	public function isCpSectionDisabled()
	{
		$settings = $this->getPlugin()->getSettings();
        return isset( $settings[ 'cpSectionDisabled' ] ) && $settings[ 'cpSectionDisabled' ];
	}

	public function getSections()
	{
		if ( $this->_sections === null ) {
			$this->_sections = craft()->sections->allSections;
        }
        return $this->_sections;
	}

	public function getChannels()
	{
		if ( $this->_channels === null ) {
			$sections = $this->getSections();
			$channels = array();
			foreach ( $sections as $section ) {
				if ( $section->type == 'channel' ) {
					$channels[] = $section;
				}
			}
			$this->_channels = $channels;
		}

		return $this->_channels;
	}

	public function getStructures()
	{
		if ( $this->_structures === null ) {
			$sections = $this->getSections();
			$structures = array();
			foreach ( $sections as $section ) {
				if ( $section->type == 'structure' ) {
					$structures[] = $section;
				}
			}
			$this->_structures = $structures;
		}

		return $this->_structures;
	}

	public function getSectionById( $sectionId )
	{
		foreach ( $this->getSections() as $section ) {
			if ( $section->id == $sectionId ) {
				return $section;
			}
		}
		return false;
	}

	public function getSectionByHandle( $sectionHandle )
	{
		foreach ( $this->getSections() as $section ) {
			if ( $section->handle == $sectionHandle ) {
				return $section;
			}
		}
		return false;
	}

	public function getCategoryGroups()
	{
		if ( $this->_categoryGroups === null ) {
			$this->_categoryGroups = craft()->categories->allGroups;
		}
		return $this->_categoryGroups;
	}

	public function getCategoryGroupById( $categoryGroupId )
	{
		foreach ( $this->getCategoryGroups() as $categoryGroup ) {
			if ( $categoryGroup->id == $categoryGroupId ) {
				return $categoryGroup;
			}
		}
		return false;
	}

	public function getCategoryGroupByHandle( $categoryGroupHandle )
	{
		foreach ( $this->getCategoryGroups() as $categoryGroup ) {
			if ( $categoryGroup->handle == $categoryGroupHandle ) {
				return $categoryGroup;
			}
		}
		return false;
	}

	public function getListingByHandle( $listingHandle )
	{
		$listing = new SectionModel();
		switch ( $listingHandle ) {
			case 'singles' :
				$listing->name = 'Singles';
				break;
			case '*' :
			case 'entries' :
				$listing->name = 'All entries';
				break;
			default :
				return false;
		}
		return $listing;
	}

}