<?php
/**
 * Block Engine frontend setup
 *
 * @package Block_Engine
 * @since   1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Main Block_Engine_Frontend Class.
 *
 * @class Yatra
 */
final class Block_Engine_Frontend
{

    /**
     * The single instance of the class.
     *
     * @var Block_Engine_Frontend
     * @since 1.0.0
     */
    protected static $_instance = null;


    /**
     * Main Block_Engine_Frontend Instance.
     *
     * Ensures only one instance of Block_Engine_Frontend is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @return Block_Engine_Frontend - Main instance.
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Yatra Constructor.
     */
    public function __construct()
    {
        $this->includes();
        $this->init_hooks();
        do_action('block_engine_frontend_loaded');
    }

    /**
     * Hook into actions and filters.
     *
     * @since 1.0.0
     */
    private function init_hooks()
    {


    }


    /**
     * Include required core files used in frontend.
     */
    public function includes()
    {
        include_once BLOCK_ENGINE_ABSPATH . 'includes/class-block-engine-assets.php';


    }


}
