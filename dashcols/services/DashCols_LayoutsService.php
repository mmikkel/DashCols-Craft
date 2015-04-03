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

class DashCols_LayoutsService extends BaseApplicationComponent
{

	protected $_currentLayout = null;


	/*
	*  Returns the current layout
	*
	*/
	public function getLayout()
	{
		return $this->_currentLayout ?: false;
	}

	/*
	*  Cache current layout and its fieldLayout
	*
	*/
	public function setLayout( $dashColsLayout )
	{

		$this->_currentLayout = $dashColsLayout;
		craft()->dashCols_fields->setLayout( $dashColsLayout );

	}

	/*
	*  Set layout from entry source
	*
	*/
	public function setLayoutFromEntrySource( $source )
	{

		if ( $this->_currentLayout !== null ) {
			return false;
		}

		$layout = $this->getLayoutFromEntrySource( $source );
        $this->setLayout( $layout ?: false );

	}

	/*
	*  Set layout from category source
	*
	*/
    public function setLayoutFromCategorySource( $source )
    {

    	if ( $this->_currentLayout !== null ) {
			return false;
		}

    	$layout = $this->getLayoutFromCategorySource( $source );
    	$this->setLayout( $layout ?: false );

    }

    public function getLayoutFromEntrySource( $source )
	{

		$layout = false;

		switch ( $source ) {
            
            case '*' : case 'entries' : case 'singles' :
                
                // Listing
                if ( $source === '*' ) {
                    $source = 'entries';
                }
                
                $layout = $this->getLayoutByListingHandle( $source );

                break;

            default :
                
                if ( strpos( $source, ':' ) === false ) {
            	
            		// Let's hope this is a valid handle
            		if ( ! $section = craft()->sections->getSectionByHandle( $source ) ) {
            			return false;
            		}
            	
            		$sectionId = $section->id;
            	
            	} else {
            	
            		// Section ID
	        		$temp = explode( ':', $source );
	            
	                if ( $temp[ 0 ] != 'section' || ! isset( $temp[ 1 ] ) || ! is_numeric( $temp[ 1 ] ) ) {
	                    return false;
	                }

	                $sectionId = $temp[ 1 ];	

            	}

                $layout = $this->getLayoutBySectionId( $sectionId );

        }

        return $layout;

	}

	public function getLayoutFromCategorySource( $source )
	{

		if ( strpos( $source, ':' ) === false ) {
    	
    		// Let's hope this is a valid handle
    		if ( ! $categoryGroup = craft()->categories->getCategoryGroupByHandle( $source ) ) {
    			return false;
    		}
    	
    		$categoryGroupId = $categoryGroup->id;
    	
    	} else {
    	
    		$temp = explode( ':', $source );
	    
	        if ( $temp[ 0 ] != 'group' || ! isset( $temp[ 1 ] ) || ! is_numeric( $temp[ 1 ] ) ) {
	            return false;
	        }
	    
	        $categoryGroupId = $temp[ 1 ];

    	}
    	
    	$layout = $this->getLayoutByCategoryGroupId( $categoryGroupId );

    	return $layout;

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

	/*
	*  Save layout to db
	*
	*/
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

		$dashColsLayoutRecord->hiddenFields = $dashColsLayout->hiddenFields;
		$dashColsLayoutRecord->metaFields = $dashColsLayout->metaFields;

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