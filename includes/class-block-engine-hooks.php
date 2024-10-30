<?php
/**
 * Block_Engine_Hooks setup
 *
 * @package Block_Engine_Hooks
 * @since   1.0.0
 */

/**
 * Main Block_Engine_Hooks Class.
 *
 * @class Block_Engine_Hooks
 */
class Block_Engine_Hooks
{

    /**
     * The single instance of the class.
     *
     * @var Block_Engine_Hooks
     * @since 1.0.0
     */
    protected static $_instance = null;


    /**
     * Main Block_Engine_Hooks Instance.
     *
     * Ensures only one instance of Block_Engine_Hooks is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @return Block_Engine_Hooks - Main instance.
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        self::$_instance->includes();

        return self::$_instance;
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes()
    {
        include_once BLOCK_ENGINE_ABSPATH . 'includes/hooks/class-block-engine-blocks-hooks.php';

    }


}

Block_Engine_Hooks::instance();