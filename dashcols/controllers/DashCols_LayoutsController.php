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

class DashCols_LayoutsController extends BaseController
{

	/**
	 * @access public
	 * @return mixed
	 */
	public function actionGetIndex(array $variables = array())
	{

		if (craft()->dashCols->isCpSectionDisabled()) {
			throw new HttpException(404);
		}

		// Get layout targets
		$variables['channels'] = craft()->dashCols->getChannels();
		$variables['structures'] = craft()->dashCols->getStructures();
		$variables['categoryGroups'] = craft()->dashCols->getCategoryGroups();
		$variables['userGroups'] = craft()->dashCols->getUserGroups();
		$variables['assetSources'] = craft()->dashCols->getAssetSources();

		// Get tabs
		$variables['tabs'] = craft()->dashCols->getCpTabs();
		$variables['selectedTab'] = 'dashColsIndex';

		// Render
		return $this->renderTemplate('dashCols/_layouts', $variables);

	}

	/**
	 * @access public
	 * @return mixed
	 */
	public function actionEditEntriesLayout(array $variables = array())
	{

		// Get tab nav items (all sections)
		$variables['tabNav'] = array(
			array(
				'name' => Craft::t('All entries'),
				'url' => 'dashcols/entries',
			),
			array(
				'name' => Craft::t('Singles'),
				'url' => 'dashcols/entries/singles',
			),
		);
		$allSections = craft()->dashCols->getSections();
		if ($allSections)
		{
			foreach ($allSections as $section)
			{
				$variables['tabNav'][] = array(
					'name' => $section->name,
					'handle' => $section->handle,
					'url' => 'dashcols/entries/section/' . $section->handle,
				);
			}
		}

		// Set selected tab
		$variables['selectedTab'] = 'entries';

		if (!isset($variables['sourceHandle'])) $variables['sourceHandle'] = 'entries';

		if (!isset($variables['sectionHandleOrId']))
		{
			$variables['listingHandle'] = $variables['sourceHandle'];
			return $this->actionEditListingLayout($variables);
		}

		// Get section
		if (!isset($variables['sectionHandleOrId'])) throw new HttpException(404);
		$section = craft()->dashCols->getSectionByHandleOrId($variables['sectionHandleOrId']);
		if (!$section) throw new HttpException(404);
		$variables['section'] = $section;
		
		// Get layout model
		$variables['layout'] = craft()->dashCols_layouts->getLayoutBySectionId($section->id);
		if (!$variables['layout']) $variables['layout'] = new DashCols_LayoutModel();

		// Breadcrumb		
		$variables['crumb'] = array(
			'label' => Craft::t($section->name),
			'url' => UrlHelper::getUrl('dashcols/entries/section/' . $section->handle),
		);

		// Get default + meta fields
		$variables['defaultFields'] = craft()->dashCols_fields->getDefaultFields('section');
		$variables['metaFields'] = craft()->dashCols_fields->getMetaFields('section');

		// Get redirect URL
		$variables['redirectUrl'] = UrlHelper::getUrl('entries/' . $section->handle);

		return $this->renderEditLayout($variables);

	}

	/**
	 * @access public
	 * @return mixed
	 */
	public function actionEditCategoryGroupLayout(array $variables = array())
	{

		if (!isset($variables['categoryGroupHandleOrId'])) {
			$categoryGroups = craft()->dashCols->getCategoryGroups();
			if (!$categoryGroups) throw new HttpException(404);
			craft()->request->redirect(UrlHelper::getUrl('dashcols/categories/' . $categoryGroups[0]->handle));
			exit();
		}

		// Get tab nav items (all category groups)
		$allCategoryGroups = craft()->dashCols->getCategoryGroups();
		$variables['tabNav'] = array();
		if ($allCategoryGroups)
		{
			foreach ($allCategoryGroups as $categoryGroup)
			{
				$variables['tabNav'][] = array(
					'name' => $categoryGroup->name,
					'handle' => $categoryGroup->handle,
					'url' => 'dashcols/categories/' . $categoryGroup->handle,
				);
			}
		}

		// Set selected tab
		$variables['selectedTab'] = 'categories';

		// Get category group
		$categoryGroup = craft()->dashCols->getCategoryGroupByHandleOrId($variables['categoryGroupHandleOrId']);
		if (!$categoryGroup) throw new HttpException(404);
		$variables['categoryGroup'] = $categoryGroup;

		// Get layout model
		$variables['layout'] = craft()->dashCols_layouts->getLayoutByCategoryGroupId($categoryGroup->id);
		if (!$variables['layout']) $variables['layout'] = new DashCols_LayoutModel();

		$variables['crumb'] = array(
			'label' => Craft::t($categoryGroup->name),
			'url' => UrlHelper::getUrl('dashcols/categories/' . $categoryGroup->handle),
		);

		// Get default + meta fields
		$variables['defaultFields'] = craft()->dashCols_fields->getDefaultFields('categories');
		$variables['metaFields'] = craft()->dashCols_fields->getMetaFields('categories');

		// Get redirect URL
		$variables['redirectUrl'] = UrlHelper::getUrl('categories/' . $categoryGroup->handle);

		return $this->renderEditLayout($variables);

	}

	/**
	 * @access public
	 * @return mixed
	 */
	public function actionEditAssetSourceLayout(array $variables = array())
	{

		if (!isset($variables['assetSourceHandleOrId'])) {
			$assetSources = craft()->dashCols->getAssetSources();
			if (!$assetSources) throw new HttpException(404);
			craft()->request->redirect(UrlHelper::getUrl('dashcols/assets/' . $assetSources[0]->handle));
			exit();
		}

		// Get tab nav items (all asset sources)
		$allAssetSources = craft()->dashCols->getAssetSources();
		$variables['tabNav'] = array();
		if ($allAssetSources)
		{
			foreach ($allAssetSources as $assetSource)
			{
				$variables['tabNav'][] = array(
					'name' => $assetSource->name,
					'handle' => $assetSource->handle,
					'url' => 'dashcols/assets/' . $assetSource->handle,
				);
			}
		}

		// Set selected tab
		$variables['selectedTab'] = 'assets';

		// Get asset source
		$assetSource = craft()->dashCols->getAssetSourceByHandleOrId($variables['assetSourceHandleOrId']);
		if (!$assetSource) throw new HttpException(404);
		$variables['assetSource'] = $assetSource;

		// Get layout model
		$variables['layout'] = craft()->dashCols_layouts->getLayoutByAssetSourceId($assetSource->id);
		if (!$variables['layout']) $variables['layout'] = new DashCols_LayoutModel();

		$variables['crumb'] = array(
			'label' => Craft::t($assetSource->name),
			'url' => UrlHelper::getUrl('dashcols/assets/' . $assetSource->handle),
		);

		// Get default + meta fields
		$variables['defaultFields'] = craft()->dashCols_fields->getDefaultFields('assets');
		$variables['metaFields'] = craft()->dashCols_fields->getMetaFields('assets');

		// Get redirect URL
		$variables['redirectUrl'] = UrlHelper::getUrl('assets');

		return $this->renderEditLayout($variables);

	}

	/**
	 * @access public
	 * @return mixed
	 */
	public function actionEditUserGroupLayout(array $variables = array())
	{

		// Get tab nav items (all user groups)
		$variables['tabNav'] = array(
			array(
				'name' => Craft::t('All users'),
				'url' => 'dashcols/users',
			),
		);
		$allUserGroups = craft()->dashCols->getUserGroups();
		if ($allUserGroups)
		{
			foreach ($allUserGroups as $userGroup)
			{
				$variables['tabNav'][] = array(
					'name' => $userGroup->name,
					'handle' => $userGroup->handle,
					'url' => 'dashcols/users/' . $userGroup->handle,
				);
			}
		}

		// Set selected tab
		$variables['selectedTab'] = 'users';

		if (!isset($variables['userGroupHandleOrId'])) {
			$variables['listingHandle'] = 'users'; // All users
			return $this->actionEditListingLayout($variables);
		}

		// Get user group
		$userGroup = craft()->dashCols->getUserGroupByHandleOrId($variables['userGroupHandleOrId']);
		if (!$userGroup) throw new HttpException(404);
		$variables['userGroup'] = $userGroup;

		// Get layout model
		$variables['layout'] = craft()->dashCols_layouts->getLayoutByUserGroupId($userGroup->id);
		if (!$variables['layout']) $variables['layout'] = new DashCols_LayoutModel();

		$variables['crumb'] = array(
			'label' => Craft::t($userGroup->name),
			'url' => UrlHelper::getUrl('dashcols/users/' . $userGroup->handle),
		);

		// Get default + meta fields
		$variables['defaultFields'] = craft()->dashCols_fields->getDefaultFields('users');
		$variables['metaFields'] = craft()->dashCols_fields->getMetaFields('users');

		// Get redirect URL
		$variables['redirectUrl'] = UrlHelper::getUrl('users');

		return $this->renderEditLayout($variables);

	}

	/**
	 * @access public
	 * @return mixed
	 */
	public function actionEditListingLayout(array $variables = array())
	{

		if (!isset($variables['listingHandle']) || !in_array($variables['listingHandle'], array('entries', 'singles', 'users'))) {
			throw new HttpException(404);
		}

		// Get listing attributes
		$listing = craft()->dashCols->getListingByHandle($variables['listingHandle']);
		if (!$listing) throw new HttpException(404);
		$listingHandle = $variables['listingHandle'];
		$listingEditUrl = UrlHelper::getUrl('dashcols/' . $listing->url);


		// Get layout model
		$variables['layout'] = craft()->dashCols_layouts->getLayoutByListingHandle($listingHandle);
		if (!$variables['layout']) $variables['layout'] = new DashCols_LayoutModel();

		// Breadcrumb
		$variables['crumb'] = array(
			'label' => $listing->name,
			'url' => $listingEditUrl,
		);

		// Get default + meta fields
		$variables['defaultFields'] = craft()->dashCols_fields->getDefaultFields($listingHandle);
		$variables['metaFields'] = craft()->dashCols_fields->getMetaFields($listingHandle);

		// Get redirect URL
		$variables['redirectUrl'] = UrlHelper::getUrl($listing->url);

		return $this->renderEditLayout($variables);

	}

	/**
	 * @access protected
	 * @return mixed
	 */
	protected function renderEditLayout(array $variables = array())
	{

		if (craft()->dashCols->isCpSectionDisabled()) {
			throw new HttpException(404);
		}

		// Build breadcrumbs
		$variables['crumbs'] = array(
			array(
				'label' => craft()->dashCols->getPlugin()->getName(),
				'url' => UrlHelper::getUrl('dashcols'),
			),
			array(
				'label' => Craft::t('Edit layouts'),
				'url' => UrlHelper::getUrl('dashcols/layouts'),
			),
			$variables['crumb'],
		);

		// Get tabs
		$variables['tabs'] = craft()->dashCols->getCpTabs();

		// Render
		return $this->renderTemplate('dashCols/_layouts/_edit', $variables);

	}

	public function actionSaveLayout()
	{

		if (craft()->dashCols->isCpSectionDisabled()) {
			throw new HttpException(404);
		}

		$this->requirePostRequest();

		$request = craft()->request;

		$layout = new DashCols_LayoutModel();
		$layout->id = ($layoutId = $request->getPost('layoutId')) ? $layoutId : null;

		$layout->sectionId = $request->getPost('sectionId');
		$layout->categoryGroupId = $request->getPost('categoryGroupId');
		$layout->assetSourceId = $request->getPost('assetSourceId');
		$layout->userGroupId = $request->getPost('userGroupId');
		$layout->listingHandle = $request->getPost('listingHandle');

		if ($layout->sectionId) {
			$section = craft()->dashCols->getSectionById($layout->sectionId);
		} else if ($layout->categoryGroupId) {
			$section = craft()->dashCols->getCategoryGroupById($layout->categoryGroupId);
		} else if ($layout->assetSourceId) {
			$section = craft()->dashCols->getAssetSourceById($layout->assetSourceId);
		} else if ($layout->userGroupId) {
			$section = craft()->dashCols->getUserGroupById($layout->userGroupId);
		} else if ($layout->listingHandle) {
			$section = craft()->dashCols->getListingByHandle($layout->listingHandle);
		} else {
			throw new HttpException(404);
		}

		$fieldLayout = craft()->fields->assembleLayoutFromPost();
		$fieldLayout->type = ElementType::Asset;

		$layout->setFieldLayout($fieldLayout);

		// Get hidden fields
		$hiddenFields = array();
		foreach ($request->getPost('hiddenFields') as $key => $value) {
			if ($value !== '1') {
				$hiddenFields[] = $key;
			}
		}
		$layout->hiddenFields = !empty($hiddenFields) ? $hiddenFields : false;

		// Get meta fields
		$metaFields = array();
		foreach ($request->getPost('metaFields') as $key => $value) {
			if ($value === '1') {
				$metaFields[] = $key;
			}
		}
		$layout->metaFields = !empty($metaFields) ? $metaFields : false;

		if (craft()->dashCols_layouts->saveLayout($layout)) {
			craft()->userSession->setNotice(Craft::t('Layout for ' . $section->name . ' saved!'));
			$this->redirectToPostedUrl($layout);
		} else {
			craft()->userSession->setError(Craft::t('Something went wrong. Layout not saved.'));
		}

		craft()->urlManager->setRouteVariables(array(
			'layout' => $layout,
		));

	}

}
