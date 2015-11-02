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

		// Layout targets
		$variables['listings'] = array(
			'entries' => Craft::t('All entries'),
			'singles' => Craft::t('Singles'),
		);
		$variables['channels'] = craft()->dashCols->getChannels();
		$variables['structures'] = craft()->dashCols->getStructures();
		$variables['categoryGroups'] = craft()->dashCols->getCategoryGroups();
		$variables['userGroups'] = craft()->dashCols->getUserGroups();

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
	public function actionEditSectionLayout(array $variables = array())
	{

		if (!isset($variables['sectionHandle'])) {
			throw new HttpException(404);
		}

		$variables['section'] = craft()->dashCols->getSectionByHandle($variables['sectionHandle']);
		if (!$variables['section'] || $variables['section']->type === 'single') {
			throw new HttpException(404);
		}

		$variables['sectionId'] = $variables['section']->id;

		// Get layout model
		if (!$variables['layout'] = craft()->dashCols_layouts->getLayoutBySectionId($variables['sectionId'])) {
			$variables['layout'] = new DashCols_LayoutModel();
		}

		$variables['crumb'] = array(
			'label' => Craft::t($variables['section']->name),
			'url' => UrlHelper::getUrl('dashcols/layouts/section/' . $variables['section']->handle),
		);

		// Set selected tab
		$variables['tabs'][$variables['section']->handle] = array(
			'label' => Craft::t('Edit layout'),
			'url' => UrlHelper::getUrl('dashcols/layouts/section/' . $variables['section']->handle),
		);
		$variables['selectedTab'] = $variables['section']->handle;

		// Get default fields
		$variables['defaultFields'] = craft()->dashCols_fields->getDefaultFields('section');

		// Get meta fields
		$variables['metaFields'] = craft()->dashCols_fields->getMetaFields('section');

		return $this->renderEditLayout($variables);

	}

	/**
	 * @access public
	 * @return mixed
	 */
	public function actionEditCategoryGroupLayout(array $variables = array())
	{

		if (!isset($variables['categoryGroupHandle'])) {
			throw new HttpException(404);
		}

		$variables['section'] = craft()->dashCols->getCategoryGroupByHandle($variables['categoryGroupHandle']);
		if (!$variables['section']) {
			throw new HttpException(404);
		}

		$variables['categoryGroupId'] = $variables['section']->id;

		// Get layout model
		if (!$variables['layout'] = craft()->dashCols_layouts->getLayoutByCategoryGroupId($variables['categoryGroupId'])) {
			$variables['layout'] = new DashCols_LayoutModel();
		}

		$variables['crumb'] = array(
			'label' => Craft::t($variables['section']->name),
			'url' => UrlHelper::getUrl('dashcols/layouts/category-group/' . $variables['section']->handle),
		);

		// Set selected tab
		$variables['tabs'][$variables['section']->handle] = array(
			'label' => Craft::t('Edit layout'),
			'url' => UrlHelper::getUrl('dashcols/layouts/category-group/' . $variables['section']->handle),
		);
		$variables['selectedTab'] = $variables['section']->handle;

		// Get default fields
		$variables['defaultFields'] = craft()->dashCols_fields->getDefaultFields('categoryGroup');

		// Get meta fields
		$variables['metaFields'] = craft()->dashCols_fields->getMetaFields('categoryGroup');

		return $this->renderEditLayout($variables);

	}

	/**
	 * @access public
	 * @return mixed
	 */
	public function actionEditUserGroupLayout(array $variables = array())
	{

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
			'url' => UrlHelper::getUrl('dashcols/layouts/users/' . $userGroup->handle),
		);

		// Set selected tab
		$variables['tabs'][$userGroup->handle] = array(
			'label' => Craft::t('Edit layout') . ': ' . $userGroup->name,
			'url' => UrlHelper::getUrl('dashcols/layouts/users/' . $userGroup->handle),
		);
		$variables['selectedTab'] = $userGroup->handle;

		// Get default fields
		$variables['defaultFields'] = craft()->dashCols_fields->getDefaultFields('users');

		// Get meta fields
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
		$listingEditUrl = UrlHelper::getUrl('dashcols/layouts/' . $listingHandle);

		// Get layout model
		$variables['layout'] = craft()->dashCols_layouts->getLayoutByListingHandle($listingHandle);
		if (!$variables['layout']) $variables['layout'] = new DashCols_LayoutModel();

		// Breadcrumb
		$variables['crumb'] = array(
			'label' => $listing->name,
			'url' => $listingEditUrl,
		);

		// Build tabs
		$variables['tabs'][$listingHandle] = array(
			'label' => Craft::t('Edit layout') . ': ' . $listing->name,
			'url' => $listingEditUrl,
		);
		
		$variables['selectedTab'] = $listingHandle;

		// Get default fields
		$variables['defaultFields'] = craft()->dashCols_fields->getDefaultFields($listingHandle);

		// Get meta fields
		$variables['metaFields'] = craft()->dashCols_fields->getMetaFields($listingHandle);

		// Get redirect URL
		$variables['redirectUrl'] = UrlHelper::getUrl($listingHandle);

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

		// Get tabs & breadcrumbs
		$variables['tabs'] = array_merge(craft()->dashCols->getCpTabs(), $variables['tabs']);
		$variables['crumbs'] = array(
			array(
				'label' => 'DashCols',
				'url' => UrlHelper::getUrl('dashcols'),
			),
			array(
				'label' => Craft::t('Edit layouts'),
				'url' => UrlHelper::getUrl('dashcols/layouts'),
			),
			$variables['crumb'],
		);

		// Get layout URLs
		$variables['layoutUrls'] = array(
			array(
				'label' => Craft::t('All entries'),
				'url' => UrlHelper::getUrl('dashcols/layouts/listing/entries'),
				'active' => $variables['selectedTab'] === 'entries',
			),
			array(
				'label' => Craft::t('Singles'),
				'url' => UrlHelper::getUrl('dashcols/layouts/listing/singles'),
				'active' => $variables['selectedTab'] === 'singles',
			),
			array(
				'label' => Craft::t('All users'),
				'url' => UrlHelper::getUrl('dashcols/layouts/users'),
				'active' => $variables['selectedTab'] === 'users',
			),
		);

		foreach (craft()->dashCols->getSections() as $section) {
			if (isset($section->type) && $section->type === 'single') {
				continue;
			}
			$variables['layoutUrls'][$section->handle] = array(
				'label' => $section->name,
				'url' => UrlHelper::getUrl('dashcols/layouts/section/' . $section->handle),
				'active' => isset($variables['section']) && $variables['section']->handle === $section->handle,
			);
		}

		foreach (craft()->dashCols->getCategoryGroups() as $categoryGroup) {
			$variables['layoutUrls'][$categoryGroup->handle] = array(
				'label' => $categoryGroup->name,
				'url' => UrlHelper::getUrl('dashcols/layouts/category-group/' . $categoryGroup->handle),
				'active' => isset($variables['categoryGroup']) && $variables['categoryGroup']->handle === $categoryGroup->handle,
			);
		}

		foreach (craft()->dashCols->getUserGroups() as $userGroup) {
			$variables['layoutUrls'][$userGroup->handle] = array(
				'label' => $userGroup->name,
				'url' => UrlHelper::getUrl('dashcols/layouts/users/' . $userGroup->handle),
				'active' => isset($variables['userGroup']) && $variables['userGroup']->handle === $userGroup->handle,
			);
		}

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
		$layout->userGroupId = $request->getPost('userGroupId');
		$layout->listingHandle = $request->getPost('listingHandle');

		if ($layout->sectionId) {
			$section = craft()->dashCols->getSectionById($layout->sectionId);
		} else if ($layout->categoryGroupId) {
			$section = craft()->dashCols->getCategoryGroupById($layout->categoryGroupId);
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
