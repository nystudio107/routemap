<?php
/**
 * Route Map plugin for Craft CMS
 *
 * RouteMap Service
 *
 * @author    nystudio107
 * @copyright Copyright (c) 2017 nystudio107
 * @link      https://nystudio107.com
 * @package   RouteMap
 * @since     1.0.0
 */

namespace Craft;

class RouteMapService extends BaseApplicationComponent
{
    // Constants
    // =========================================================================

    const ROUTE_FORMAT_CRAFT = 'Craft';
    const ROUTE_FORMAT_REACT = 'React';
    const ROUTE_FORMAT_VUE = 'Vue';

    const ROUTEMAP_CACHE_PREFIX = 'RouteMap';
    const ROUTEMAP_CACHE_TIMESTAMP = 'CacheTimeStamp';
    const ROUTEMAP_CACHE_DATA = 'CacheData';
    const ROUTEMAP_CACHE_RULES = 'Rules';
    const ROUTEMAP_CACHE_URLS = 'Urls';
    const ROUTEMAP_CACHE_ALLURLS = 'AllUrls';

    // Public Methods
    // =========================================================================

    /**
     * Return all of the public URLs
     *
     * @return array
     */
    public function getAllUrls()
    {
        $urls = array();
        // Just return the data if it's already cached
        $cacheKey = $this::ROUTEMAP_CACHE_URLS . $this::ROUTEMAP_CACHE_ALLURLS;
        $cachedData = $this->getCachedValue($cacheKey);
        if ($cachedData !== false) {
            return $cachedData;
        }
        // Get all of the sections
        $sections = craft()->sections->getAllSections();
        foreach ($sections as $section) {
            if ($section->hasUrls) {
                $urls = array_merge($urls, $this->getSectionUrls($section->handle));
            }
        }

        // @TODO: Support CategoryGroupts & Category URLs

        // @TODO: Commerce Products & Variant URLs

        // Cache the result
        $this->setCachedValue($cacheKey, $urls);

        return $urls;
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
        $urls = array();
        if (!empty($section)) {
            // Just return the data if it's already cached
            $cacheKey = $this::ROUTEMAP_CACHE_URLS . $section;
            $cachedData = $this->getCachedValue($cacheKey);
            if ($cachedData !== false) {
                return $cachedData;
            }

            // @TODO: This should be extended to handle multiple locales

            // Get all of the entries in the section
            $criteria = craft()->elements->getCriteria(ElementType::Entry);
            $criteria->section = $section;
            $criteria->limit = null;

            // Iterate through the entries and grab their URLs
            foreach ($criteria as $entry) {
                array_push($urls, $entry->url);
            }

            // Cache the result
            $this->setCachedValue($cacheKey, $urls);
        }

        return $urls;
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
        $routeRules = array();
        // Just return the data if it's already cached
        $cacheKey = $this::ROUTEMAP_CACHE_RULES . $this::ROUTEMAP_CACHE_ALLURLS . $format;
        $cachedData = $this->getCachedValue($cacheKey);
        if ($cachedData !== false) {
            return $cachedData;
        }
        // Get all of the sections
        $sections = craft()->sections->getAllSections();
        foreach ($sections as $section) {
            if ($section->hasUrls) {
                $route = $this->getSectionRouteRules($section->handle, $format);
                if (!empty($route)) {
                    $routeRules[$section->handle] = $route;
                }
            }
        }

        // @TODO: Support CategoryGroupts & Category URLs

        // @TODO: Commerce Products & Variant URLs

        // Cache the result
        $this->setCachedValue($cacheKey, $routeRules);

        return $routeRules;
    }

    /**
     * @param string $section
     * @param string $format 'Craft'|'React'|'Vue'
     *
     * @return array
     */
    public function getSectionRouteRules($section, $format)
    {
        $route = array();
        // Just return the data if it's already cached
        $cacheKey = $this::ROUTEMAP_CACHE_RULES . $section . $format;
        $cachedData = $this->getCachedValue($cacheKey);
        if ($cachedData !== false) {
            return $cachedData;
        }
        // Get the actual section
        $sectionModel = craft()->sections->getSectionByHandle($section);
        if ($sectionModel) {
            // Get section data to return
            $route = array(
                'handle'   => $sectionModel->handle,
                'type'     => $sectionModel->type,
                'url'      => $sectionModel->getUrlFormat(),
                'template' => $sectionModel->template,
            );

            // @TODO: This should be extended to handle multiple locales

            // Normalize the routes based on the format
            $route = $this->normalizeFormat($format, $route);

            // Cache the result
            $this->setCachedValue($cacheKey, $route);
        }

        return $route;
    }

    /**
     * Invalidate the caches by setting the timestamp to now
     */
    public function invalidateCache()
    {
        // Invalidate the caches by setting the timestamp to now
        $cacheKey = $this::ROUTEMAP_CACHE_PREFIX . $this::ROUTEMAP_CACHE_TIMESTAMP;
        craft()->cache->set($cacheKey, time());
    }

    // Protected Methods
    // =========================================================================

    /**
     * Get a value from our timestamped cache
     *
     * @param string $key
     *
     * @return bool|mixed
     */
    protected function getCachedValue($key)
    {
        // If the cache timestamp doesn't exist, or is not set, assume the value is not cached
        $cacheKey = $this::ROUTEMAP_CACHE_PREFIX . $this::ROUTEMAP_CACHE_TIMESTAMP;
        $cacheTimeStamp = craft()->cache->get($cacheKey);
        if (($cacheTimeStamp === false) || (!$cacheTimeStamp)) {
            return false;
        }

        // Get the cached data
        $cacheKey = $this::ROUTEMAP_CACHE_PREFIX . $key;
        $data = craft()->cache->get($cacheKey);
        // If it's not in the cache, it's not in the cache
        if ($data === false) {
            return false;
        }
        // If there's no timestamp or data, assume it's not cached
        if (empty($data[$this::ROUTEMAP_CACHE_TIMESTAMP]) || empty($data[$this::ROUTEMAP_CACHE_DATA])) {
            return false;
        }
        // If the data timestamp is older than the cache timestamp, assume it's not cached
        $dataTimeStamp = $data[$this::ROUTEMAP_CACHE_TIMESTAMP];
        if ($dataTimeStamp < $cacheTimeStamp) {
            return false;
        }

        // If we made it this far, return the cached data
        return $data[$this::ROUTEMAP_CACHE_DATA];
    }

    /**
     * Set a value in our timestamped cache
     *
     * @param string $key
     * @param mixed  $data
     */
    protected function setCachedValue($key, $data)
    {
        // If the cache timestamp doesn't exist, set it
        $cacheKey = $this::ROUTEMAP_CACHE_PREFIX . $this::ROUTEMAP_CACHE_TIMESTAMP;
        $cacheTimeStamp = craft()->cache->get($cacheKey);
        if (($cacheTimeStamp === false) || (!$cacheTimeStamp)) {
            $this->invalidateCache();
        }
        // Bundle up the data into an array with a timestamp
        $cacheData = array(
            $this::ROUTEMAP_CACHE_TIMESTAMP => time(),
            $this::ROUTEMAP_CACHE_DATA      => $data,
        );
        // Cache the data
        $cacheKey = $this::ROUTEMAP_CACHE_PREFIX . $key;
        craft()->cache->set($cacheKey, $cacheData);
    }

    /**
     * Normalize the routes based on the format
     *
     * @param string $format 'Craft'|'React'|'Vue'
     * @param array  $route
     *
     * @return array
     */
    protected function normalizeFormat($format, $route)
    {
        // Handle the special '__home__' URI
        if ($route['url'] == '__home__') {
            $route['url'] = '/';
        }

        // Transform the URLs depending on the format requested
        switch ($format) {
            // React & Vue routes have a leading / and {slug} -> :slug
            case $this::ROUTE_FORMAT_REACT:
            case $this::ROUTE_FORMAT_VUE:
                $matchRegEx = "`{(.*?)}`i";
                $replaceRegEx = ":$1";
                $route['url'] = preg_replace($matchRegEx, $replaceRegEx, $route['url']);
                $route['url'] = '/' . ltrim($route['url'], '/');
                break;

            // Craft-style URLs don't need to be changed
            case $this::ROUTE_FORMAT_CRAFT:
            default:
                // Do nothing
                break;
        }

        return $route;
    }
}
