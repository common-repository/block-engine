<?php
/**
 * Plugin Name: Gutenberg Blocks & Template Library - Block Engine
 * Plugin URI: https://wpblockengine.com
 * Description: Gutenberg Blocks & Template Library
 * Author: Block Engine
 * Author URI: https://wpblockengine.com
 * Version: 1.0.1
 * License: GPL3+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: block-engine
 * Domain Path: /languages
 *
 * @package UB
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Define BLOCK_ENGINE_PLUGIN_FILE.
if (!defined('BLOCK_ENGINE_FILE')) {
    define('BLOCK_ENGINE_FILE', __FILE__);
}

// Define BLOCK_ENGINE_VERSION.
if (!defined('BLOCK_ENGINE_VERSION')) {
    define('BLOCK_ENGINE_VERSION', '1.0.1');
}

// Define BLOCK_ENGINE_PLUGIN_URI.
if (!defined('BLOCK_ENGINE_PLUGIN_URI')) {
    define('BLOCK_ENGINE_PLUGIN_URI', plugins_url('', BLOCK_ENGINE_FILE));
}

// Define BLOCK_ENGINE_PLUGIN_DIR.
if (!defined('BLOCK_ENGINE_PLUGIN_DIR')) {
    define('BLOCK_ENGINE_PLUGIN_DIR', plugin_dir_path(BLOCK_ENGINE_FILE));
}


// Include the main Block_Engine class.
if (!class_exists('Block_Engine')) {
    include_once dirname(__FILE__) . '/includes/class-block-engine.php';
}


/**
 * Main instance of Block_Engine.
 *
 * Returns the main instance of Block_Engine to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return Block_Engine
 */
function block_engine()
{
    return Block_Engine::instance();
}

// Global for backwards compatibility.
$GLOBALS['block-engine-instance'] = block_engine();

include_once dirname(__FILE__) . '/includes/guten/init.php';

