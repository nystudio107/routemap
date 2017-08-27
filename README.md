# Route Map plugin for Craft CMS

Returns a list of Craft/Vue/React route rules and entry & asset URLs for ServiceWorkers from Craft entries

## Installation

To install Route Map, follow these steps:

1. Download & unzip the file and place the `routemap` directory into your `craft/plugins` directory
2.  -OR- do a `git clone https://github.com/nystudio107/routemap.git` directly into your `craft/plugins` folder.  You can then update it with `git pull`
3.  -OR- install with Composer via `composer require nystudio107/routemap`
4. Install plugin in the Craft Control Panel under Settings > Plugins
5. The plugin folder should be named `routemap` for Craft to see it.  GitHub recently started appending `-master` (the branch name) to the name of the folder for zip file downloads.

Route Map works on Craft 2.4.x and Craft 2.5.x.

## Route Map Overview

Route Map is a plugin to help bridge the routing gap between frontend technologies like Vue/React and Craft CMS. Using Route Map, you can define your routes in Craft CMS as usual, and use an XHR to get a list of the routes in JSON format for use in your Vue/React frontend.

This allows you to create your routes dynamically in Craft CMS using the GUI, and have them translate automatically to your frontend framework of choice.

Route Map also assists with ServiceWorkers by providing a list of all of the URLs on your Craft CMS site, or just the specific sections you're interested in. You can limit the URLs returned via any ElementCriteriaModel attributes, and Route Map can even return a list of URLs to all of the Assets that a particular URL has (whether in Assets fields, or embedded in Matrix/Neo blocks).

This allows you, for instance, to have a ServiceWorker that will automatically pre-cache the latest 5 blog entries on your site, as well as any images displayed on those pages, so that they will work with offline browsing.

Route Map maintains a cache of each requested set of URLs for excellent performance for repeated requests. This cache is automatically cleared whenever entries are created or modified.

## Configuring Route Map

There's nothing to configure.

## Using Route Map in your Twig Templates

-Insert text here-

## Using Route Map via XHR

-Insert text here-

## Route Map Roadmap

Some things to do, and ideas for potential features:

* Add support for Category Groups / Category URLs
* Add support for Commerce Products / Variant URLs
* Add support for multiple locales

Brought to you by [nystudio107](https://nystudio107.com)
