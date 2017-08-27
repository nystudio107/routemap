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
    const ROUTEMAP_CACHE_ASSETS = 'Assets';
    const ROUTEMAP_CACHE_ALLURLS = 'AllUrls';

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
        $urls = array();
        // Just return the data if it's already cached
        $cacheKey = $this->getCacheKey(
            $this::ROUTEMAP_CACHE_URLS . $this::ROUTEMAP_CACHE_ALLURLS,
            array(
                $attributes,
            )
        );
        $cachedData = $this->getCachedValue($cacheKey);
        if ($cachedData !== false) {
            return $cachedData;
        }
        // Get all of the sections
        $sections = craft()->sections->getAllSections();
        foreach ($sections as $section) {
            if ($section->hasUrls) {
                $urls = array_merge(
                    $urls,
                    $this->getSectionUrls(
                        $section->handle,
                        $attributes
                    )
                );
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
     * @param array  $attributes array of attributes to set on the the
     *                           ElementCriteralModel
     *
     * @return array
     */
    public function getSectionUrls($section, $attributes = array())
    {
        $urls = array();
        if (!empty($section)) {
            // Just return the data if it's already cached
            $cacheKey = $this->getCacheKey(
                $this::ROUTEMAP_CACHE_URLS,
                array(
                    $section,
                    $attributes,
                )
            );
            $cachedData = $this->getCachedValue($cacheKey);
            if ($cachedData !== false) {
                return $cachedData;
            }

            // @TODO: This should be extended to handle multiple locales

            // Get all of the entries in the section
            $criteria = craft()->elements->getCriteria(ElementType::Entry);
            $criteria->section = $section;
            $criteria->limit = null;

            // Add in any custom attributes to set on the ElementCriteriaModel
            if (!empty($attributes)) {
                $criteria->setAttributes($attributes);
            }

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
        $cacheKey = $this->getCacheKey(
            $this::ROUTEMAP_CACHE_RULES . $this::ROUTEMAP_CACHE_ALLURLS,
            array(
                $format,
            )
        );
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
        $cacheKey = $this->getCacheKey(
            $this::ROUTEMAP_CACHE_RULES,
            array(
                $section,
                $format,
            )
        );
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
        $assetUrls = array();
        // Extract a URI from the URL
        $uri = parse_url($url, PHP_URL_PATH);
        $uri = ltrim($uri, '/');
        // Just return the data if it's already cached
        $cacheKey = $this->getCacheKey(
            $this::ROUTEMAP_CACHE_ASSETS,
            array(
                $uri,
                $assetTypes,
            )
        );
        $cachedData = $this->getCachedValue($cacheKey);
        if ($cachedData !== false) {
            return $cachedData;
        }

        // Find the element that matches this URI
        $element = craft()->elements->getElementByUri($uri, craft()->language, true);
        if ($element) {
            // Iterate through the fields in this Entry
            $fieldLayouts = $element->fieldLayout->getFields();
            foreach ($fieldLayouts as $fieldLayout) {
                $field = craft()->fields->getFieldById($fieldLayout->fieldId);
                // @TODO: Add support for Neo blocks
                switch ($field->type) {
                    case "Neo":
                        break;

                    // Iterate through all of the matrix blocks
                    case "Matrix":
                        $blocks = $element[$field->handle];
                        foreach ($blocks as $block) {
                            $matrixBlockTypeModel = $block->getType();
                            $matrixFields = $matrixBlockTypeModel->getFields();

                            foreach ($matrixFields as $matrixField) {
                                switch ($matrixField->type) {
                                    // Get the URLs of all assets of the type $assetTypes
                                    case "FocusPoint_FocusPoint":
                                    case "Assets":
                                        $assets = $block[$matrixField->handle];
                                        foreach ($assets as $asset) {
                                            if (in_array($asset->kind, $assetTypes)) {
                                                array_push($assetUrls, $asset->getUrl());
                                            }
                                        }
                                        break;
                                }
                            }
                        }
                        break;

                    // Get the URLs of all assets of the type $assetTypes
                    case "FocusPoint_FocusPoint":
                    case "Assets":
                        $assets = $element[$field->handle];
                        foreach ($assets as $asset) {
                            if (in_array($asset->kind, $assetTypes)) {
                                array_push($assetUrls, $asset->getUrl());
                            }
                        }
                        break;
                }
            }

            // Cache the result
            $this->setCachedValue($cacheKey, $assetUrls);
        }

        return $assetUrls;
    }

    /**
     * Invalidate the caches by setting the timestamp to now
     */
    public function invalidateCache()
    {
        // Invalidate the caches by setting the timestamp to now
        $cacheKey = $this->getCacheKey(
            $this::ROUTEMAP_CACHE_PREFIX . $this::ROUTEMAP_CACHE_TIMESTAMP
        );
        craft()->cache->set($cacheKey, time());
    }

    // Protected Methods
    // =========================================================================

    /**
     * Generate a cache key with the combination of the $prefix and an md5()
     * hashed version of the flattened $args array
     *
     * @param string $prefix
     * @param array  $args
     *
     * @return string
     */
    protected function getCacheKey($prefix, $args = array())
    {
        $cacheKey = $prefix;
        $flattenedArgs = '';
        // If an array of $args is passed in, flatten it into a concatenated string
        if (!empty($args)) {
            foreach ($args as $arg) {
                if ((is_object($arg) || is_array($arg)) && !empty($arg)) {
                    $flattenedArgs .= http_build_query($arg);
                }
                if (is_string($arg)) {
                    $flattenedArgs .= $arg;
                }
            }
            // Make an md5 hash out of it
            $flattenedArgs = md5($flattenedArgs);
        }

        return $cacheKey . $flattenedArgs;
    }

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
        $cacheKey = $this->getCacheKey(
            $this::ROUTEMAP_CACHE_PREFIX . $this::ROUTEMAP_CACHE_TIMESTAMP
        );
        $cacheTimeStamp = craft()->cache->get($cacheKey);
        if (($cacheTimeStamp === false) || (!$cacheTimeStamp)) {
            return false;
        }

        // Get the cached data
        $cacheKey = $this->getCacheKey(
            $this::ROUTEMAP_CACHE_PREFIX . $key
        );
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
        $cacheKey = $this->getCacheKey(
            $this::ROUTEMAP_CACHE_PREFIX . $this::ROUTEMAP_CACHE_TIMESTAMP
        );
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
        $cacheKey = $this->getCacheKey(
            $this::ROUTEMAP_CACHE_PREFIX . $key
        );
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
        // Normalize the URL
        $route['url'] = $this->normalizeUri($route['url']);
        // Transform the URLs depending on the format requested
        switch ($format) {
            // React & Vue routes have a leading / and {slug} -> :slug
            case $this::ROUTE_FORMAT_REACT:
            case $this::ROUTE_FORMAT_VUE:
                $matchRegEx = "`{(.*?)}`i";
                $replaceRegEx = ":$1";
                $route['url'] = preg_replace($matchRegEx, $replaceRegEx, $route['url']);
                // Add a leading /
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

    /**
     * Normalize the URI
     *
     * @param $url
     *
     * @return string
     */
    protected function normalizeUri($url)
    {
        // Handle the special '__home__' URI
        if ($url == '__home__') {
            $url = '/';
        }

        return $url;
    }
}
