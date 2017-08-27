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
     * @param array $attributes array of attributes to set on the the
     *                          ElementCriteralModel
     *
     * @return array
     */
    public function getAllUrls($attributes = array())
    {
        return craft()->routeMap->getAllUrls($attributes);
    }

    /**
     * Return the public URLs for a section
     *
     * @param string $section
     * @param array  $attributes array of attributes to set on the the
     *                           ElementCriteralModel
     *
     * @return array
     */
    public function getSectionUrls($section, $attributes = array())
    {
        return craft()->routeMap->getSectionUrls($section, $attributes);
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

    /**
     * Get all of the assets of the type $assetTypes that are used in the Entry
     * that matches the $url
     *
     * @param string $url
     * @param array  $assetTypes
     *
     * @return array
     */
    public function getUrlAssetUrls($url, $assetTypes = array('image'))
    {
        return craft()->routeMap->getUrlAssetUrls($url, $assetTypes = array('image'));
    }
}