<?php
/**
 * Route Map plugin for Craft CMS
 *
 * RouteMap Controller
 *
 * @author    nystudio107
 * @copyright Copyright (c) 2017 nystudio107
 * @link      https://nystudio107.com
 * @package   RouteMap
 * @since     1.0.0
 */

namespace Craft;

class RouteMapController extends BaseController
{

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     * @access protected
     */
    protected $allowAnonymous = array(
        'actionGetAllUrls',
        'actionGetSectionUrls',
        'actionGetAllRouteRules',
        'actionGetSectionRouteRules',
        'actionGetUrlAssetUrls',
    );

    /**
     * Return all of the public URLs
     *
     * @param array $attributes array of attributes to set on the the
     *                          ElementCriteriaModel
     *
     * @return array
     */
    public function actionGetAllUrls()
    {
        $attributes = craft()->request->getParam('attributes', array());
        $this->returnJson(
            craft()->routeMap->getAllUrls($attributes)
        );
    }

    /**
     * Return the public URLs for a section
     *
     * @param string $section
     * @param array  $attributes array of attributes to set on the the
     *                           ElementCriteriaModel
     *
     * @return array
     */
    public function actionGetSectionUrls($section)
    {
        $attributes = craft()->request->getParam('attributes', array());
        $this->returnJson(
            craft()->routeMap->getSectionUrls($section, $attributes)
        );
    }

    /**
     * Return all of the route rules
     *
     * @param string $format 'Craft'|'React'|'Vue'
     *
     * @return array
     */
    public function actionGetAllRouteRules($format = 'Craft')
    {
        $this->returnJson(
            craft()->routeMap->getAllRouteRules($format)
        );
    }

    /**
     * @param string $section
     * @param string $format 'Craft'|'React'|'Vue'
     *
     * @return array
     */
    public function actionGetSectionRouteRules($section, $format = 'Craft')
    {
        $this->returnJson(
            craft()->routeMap->getSectionRouteRules($section, $format)
        );
    }

    /**
     * Get all of the assets of the type $assetTypes that are used in the Entry
     * that matches the $url
     *
     * @param string $url
     * @param array  $assetTypes
     *
     * @return array
     */
    public function actionGetUrlAssetUrls($url)
    {
        $assetTypes = craft()->request->getParam('assetTypes', array('image'));
        $this->returnJson(
            craft()->routeMap->getUrlAssetUrls($url, $assetTypes)
        );
    }
}