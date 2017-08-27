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
    );

    /**
     * Return all of the public URLs
     *
     * @return array
     */
    public function actionGetAllUrls()
    {
        $this->returnJson(
            craft()->routeMap->getAllUrls()
        );
    }

    /**
     * Return the public URLs for a section
     *
     * @param string $section
     *
     * @return array
     */
    public function actionGetSectionUrls($section)
    {
        $this->returnJson(
            craft()->routeMap->getSectionUrls($section)
        );
    }

    /**
     * Return all of the route rules
     *
     * @param string $format 'Craft'|'React'|'Vue'
     *
     * @return array
     */
    public function actionGetAllRouteRules($format)
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
    public function actionGetSectionRouteRules($section, $format)
    {
        $this->returnJson(
            craft()->routeMap->getSectionRouteRules($section, $format)
        );
    }
}