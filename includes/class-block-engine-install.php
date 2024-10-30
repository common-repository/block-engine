<?php
/**
 * Yatra install setup
 *
 * @package Yatra
 * @since   1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Main Block_Engine_Install Class.
 *
 * @class Yatra
 */
final class Block_Engine_Install
{

    public static function install()
    {
        $block_engine_version = get_option('block_engine_plugin_version');
        update_option('block_engine_plugin_version', BLOCK_ENGINE_VERSION);
        update_option('block_engine_plugin_db_version', BLOCK_ENGINE_VERSION);

        //save install date
        if (false == get_option('block_engine_install_date')) {
            update_option('block_engine_install_date', current_time('timestamp'));
        }
    }


}