<?php
/**
 * Plugin Name: Easy Videos
 * Plugin URI: https://elnayef.com
 * Description: This plugin creates the Easy Video custom post type and fetches data via the youtube api, after that it creates a new post with the retrieved data.
 * Version: 1.0.0
 * Author: Mohammad Elnayef
 * Author URI: https://www.linkedin.com/in/mohammad-elnayef/
 */

use Plugin\EasyVideos\Utility;
use Plugin\EasyVideos\PluginController;

require_once dirname(__FILE__).'/classes/PluginController.php';
require_once dirname(__FILE__).'/classes/Utility.php';

Utility::hasRightsToAccessFile();

// Main entry point of the plugin.
$plugin = new PluginController();
$plugin->init();


