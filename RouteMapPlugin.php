<?php
/**
 * Route Map plugin for Craft CMS
 *
 * Returns a list of public routes for sections with URLs
 *
 * @author    nystudio107
 * @copyright Copyright (c) 2017 nystudio107
 * @link      https://nystudio107.com
 * @package   RouteMap
 * @since     1.0.0
 */

namespace Craft;

class RouteMapPlugin extends BasePlugin
{
    /**
     * @return mixed
     */
    public function init()
    {
        parent::init();
        // any time an element is saved or deleted we need to invalidate.
        // unfortunately there is no onDeleteElement...
        $invalidateEvents = array(
            'elements.onSaveElement',
            'entries.onDeleteEntry',
            'sections.onDeleteSection',
            'categories.onDeleteCategory',
            'categories.onDeleteGroup',
            'assets.onDeleteAsset',
            'localization.onDeleteLocale',
            'commerce_products.onDeleteProduct',
        );

        foreach($invalidateEvents as $event) {
            craft()->on($event, function (Event $event) {
                craft()->routeMap->invalidateCache();
            });
        }

    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return Craft::t('Route Map');
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Returns a list of Craft/Vue/React route rules and entry & asset URLs for ServiceWorkers from Craft entries');
    }

    /**
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'https://github.com/nystudio107/routemap/blob/master/README.md';
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/nystudio107/routemap/master/releases.json';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getSchemaVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getDeveloper()
    {
        return 'nystudio107';
    }

    /**
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'https://nystudio107.com';
    }

    /**
     * @return bool
     */
    public function hasCpSection()
    {
        return false;
    }
}
