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

	private $_sections = null,
			$_channels = null,
			$_structures = null,
			$_categoryGroups = null;

	/*
	* Return CP section tabs
	*
	*/
	public function getCpTabs()
	{

		return array(
			'layouts' => array(
				'label' => Craft::t( 'Edit Layouts' ),
				'url' => UrlHelper::getUrl( 'dashcols/layouts' ),
			),
			'about' => array(
				'label' => Craft::t( 'About' ),
				'url' => UrlHelper::getUrl( 'dashcols/about' ),
			),
		);
		
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

	public function getLayoutBySectionId( $sectionId )
	{
		$dashColsLayoutRecord = DashCols_LayoutRecord::model()->findByAttributes( array(
			'sectionId' => $sectionId,
		) );
		return $dashColsLayoutRecord ? DashCols_LayoutModel::populateModel( $dashColsLayoutRecord ) : false;
	}

	public function getLayoutByCategoryGroupId( $categoryGroupId )
	{
		$dashColsLayoutRecord = DashCols_LayoutRecord::model()->findByAttributes( array(
			'categoryGroupId' => $categoryGroupId,
		) );
		return $dashColsLayoutRecord ? DashCols_LayoutModel::populateModel( $dashColsLayoutRecord ) : false;	
	}

	public function getLayoutByListingHandle( $listingHandle )
	{
		$dashColsLayoutRecord = DashCols_LayoutRecord::model()->findByAttributes( array(
			'listingHandle' => $listingHandle,
		) );
		return $dashColsLayoutRecord ? DashCols_LayoutModel::populateModel( $dashColsLayoutRecord ) : false;	
	}

	public function getFieldLayoutBySectionId( $sectionId )
	{
		if ( $dashColsLayoutRecord = $this->getLayoutBySectionId( $sectionId ) ) {
			$dashColsLayout = DashCols_LayoutModel::populateModel( $dashColsLayoutRecord );
			return $dashColsLayout->getFieldLayout();

		}
		return false;
	}

	public function getFieldLayoutByCategoryGroupId( $categoryGroupId )
	{
		if ( $dashColsLayoutRecord = $this->getLayoutByCategoryGroupId( $categoryGroupId ) ) {
			$dashColsLayout = DashCols_LayoutModel::populateModel( $dashColsLayoutRecord );
			return $dashColsLayout->getFieldLayout();

		}
		return false;
	}

	public function getFieldLayoutByListingHandle( $listingHandle )
	{
		if ( $dashColsLayoutRecord = $this->getLayoutByListingHandle( $listingHandle ) ) {
			$dashColsLayout = DashCols_LayoutModel::populateModel( $dashColsLayoutRecord );
			return $dashColsLayout->getFieldLayout();

		}
		return false;
	}

	public function saveLayout( DashCols_LayoutModel $dashColsLayout )
	{

		$existingLayout = false;

		if ( $dashColsLayout->id ) {
			if ( ! $dashColsLayoutRecord = DashCols_LayoutRecord::model()->findById( $dashColsLayout->id ) ) {
				throw new Exception( Craft::t( 'Could not find layout with ID "{id}"', array( 
					'id' => $dashColsLayout->id,
				) ) );
			}
			$existingLayout = DashCols_LayoutModel::populateModel( $dashColsLayoutRecord );
		} else {
			$dashColsLayoutRecord = new DashCols_LayoutRecord();
		}

		if ( $dashColsLayout->sectionId ) {
			$dashColsLayoutRecord->sectionId = $dashColsLayout->sectionId;
		} else if ( $dashColsLayout->categoryGroupId ) {
			$dashColsLayoutRecord->categoryGroupId = $dashColsLayout->categoryGroupId;
		} else if ( $dashColsLayout->listingHandle ) {
			$dashColsLayoutRecord->listingHandle = $dashColsLayout->listingHandle;
		} else {
			throw new Exception( Craft::t( 'Unknown target for layout' ) );
		}
		
		$dashColsLayoutRecord->validate();

		$dashColsLayout->addErrors( $dashColsLayoutRecord->getErrors() );

		if ( ! $dashColsLayout->hasErrors() ) {
			
			$transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;

			try	{
				
				if ( $existingLayout && $existingLayout->fieldLayoutId ) {
					craft()->fields->deleteLayoutById( $existingLayout->fieldLayoutId );
				}

				$fieldLayout = $dashColsLayout->getFieldLayout();
				craft()->fields->saveLayout( $fieldLayout );

				$dashColsLayout->fieldLayoutId = $fieldLayout->id;
				$dashColsLayoutRecord->fieldLayoutId = $fieldLayout->id;

				if ( ! $dashColsLayout->id ) {
					$dashColsLayoutRecord->save();
				} else {
					$dashColsLayoutRecord->update();
				}

				$dashColsLayout->id = $dashColsLayoutRecord->id;

				if ( $transaction !== null ) {
					$transaction->commit();
				}
				
			} catch (\Exception $e) {

				if ( $transaction !== null ) {
					$transaction->rollback();
				}

				throw $e;

			}

			return true;

		}

		return false;

	}

}