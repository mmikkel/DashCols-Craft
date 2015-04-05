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

	protected 	$_sessionKey = '_dashCols_layouts',
				$_layouts = null,
				$_currentLayout = null;

	public function init()
	{

		if ( $layouts = craft()->httpSession->get( $this->_sessionKey ) ) {
			$layouts = unserialize( $layouts );
		} else {
			$layouts = $this->getLayouts();
			craft()->httpSession->add( $this->_sessionKey, serialize( $layouts ) );
		}
		
		foreach ( $layouts as $layout ) {
			// Cache the layout's fields to the FieldsService
			craft()->dashCols_fields->addCustomFields( $layout->customFields );
		}

	}

	public function getLayouts()
	{
		if ( $this->_layouts === null ) {
			
			$layouts = array();
			$records = DashCols_LayoutRecord::model()->findAll();
			
			foreach ( $records as $record ) {
				
				$layout = DashCols_LayoutModel::populateModel( $record );

				// Get the layouts' fields
				$layout->customFields = craft()->dashCols_fields->getCustomFieldsFromFieldLayout( $layout->getFieldLayout() );
				
				$layouts[] = $layout;

			}

			$this->_layouts = $layouts;

		}
		
		return $this->_layouts;

	}

	/*
	*  Returns the current layout
	*
	*/
	public function getLayout()
	{
		return $this->_currentLayout ?: false;
	}

	/*
	*  Sets current layout
	*
	*/
	public function setLayout( $dashColsLayout )
	{
		$this->_currentLayout = $dashColsLayout;
	}

	/*
	*  Sets current layout from entry source
	*
	*/
	public function setLayoutFromEntrySource( $source )
	{
		$layout = $this->getLayoutFromEntrySource( $source );
        $this->setLayout( $layout ?: false );
	}

	/*
	*  Sets current layout from category source
	*
	*/
    public function setLayoutFromCategorySource( $source )
    {
    	$layout = $this->getLayoutFromCategorySource( $source );
    	$this->setLayout( $layout ?: false );
    }

    /*
    *	Returns layout from entry source
    *
    */
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

	/*
    *	Returns layout from category source
    *
    */
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

	/*
    *	Returns layout from section ID
    *
    */
	public function getLayoutBySectionId( $sectionId )
	{
		$layouts = $this->getLayouts();
		foreach ( $layouts as $layout ) {
			if ( $layout->sectionId === $sectionId ) {
				return $layout;
			}
		}
		return false;
	}

	/*
    *	Returns layout from category group ID
    *
    */
	public function getLayoutByCategoryGroupId( $categoryGroupId )
	{
		$layouts = $this->getLayouts();
		foreach ( $layouts as $layout ) {
			if ( $layout->categoryGroupId === $categoryGroupId ) {
				return $layout;
			}
		}
		return false;
	}

	/*
    *	Returns layout from listing handle
    *
    */
	public function getLayoutByListingHandle( $listingHandle )
	{
		$layouts = $this->getLayouts();
		foreach ( $layouts as $layout ) {
			if ( $layout->listingHandle === $listingHandle ) {
				return $layout;
			}
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

			// Null layouts stored in session
			craft()->httpSession->add( $this->_sessionKey, null );
			return true;

		}

		return false;

	}

}