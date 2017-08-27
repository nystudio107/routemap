<?php
/**
 * Route Map plugin for Craft CMS
 *
 * Route Map Variable
 *
 * @author    nystudio107
 * @copyright Copyright (c) 2017 nystudio107
 * @link      https://nystudio107.com
 * @package   RouteMap
 * @since     1.0.0
 */

namespace Craft;

class RouteMapVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Return all of the public URLs
     *
     * @return array
     */
    public function getAllUrls()
    {
        return craft()->routeMap->getAllUrls();
    }

    /**
     * Return the public URLs for a section
     *
     * @param string $section
     *
     * @return array
     */
    public function getSectionUrls($section)
    {
        return craft()->routeMap->getSectionUrls($section);
    }

    /**
     * Return all of the route rules
     *
     * @param string $format 'Craft'|'React'|'Vue'
     *
     * @return array
     */
    public function getAllRouteRules($format)
    {
        return craft()->routeMap->getAllRouteRules($format);
    }

    /**
     * @param string $section
     * @param string $format 'Craft'|'React'|'Vue'
     *
     * @return array
     */
    public function getSectionRouteRules($section, $format)
    {
        return craft()->routeMap->getSectionRouteRules($section, $format);
    }
}