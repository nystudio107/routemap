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

        // Invalidate our caches whenever an entry is saved
        craft()->on('entries.onBeforeSaveEntry', function (Event $event) {
            craft()->routeMap->invalidateCache();
        });
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
        return Craft::t('Returns a list of public routes for sections with URLs');
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

    /**
     * @return mixed
     */
    public function getSettingsHtml()
    {
        return craft()->templates->render('routemap/RouteMap_Settings', array(
            'settings' => $this->getSettings(),
        ));
    }

    /**
     * @param mixed $settings The plugin's settings
     *
     * @return mixed
     */
    public function prepSettings($settings)
    {
        // Modify $settings here...
        return $settings;
    }

    /**
     * @return array
     */
    protected function defineSettings()
    {
        return array(
            'someSetting' => array(AttributeType::String, 'label' => 'Some Setting', 'default' => ''),
        );
    }
}
