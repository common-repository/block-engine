<?php
/**
 * Block_Engine setup
 *
 * @package Block_Engine
 * @since   1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Main Block_Engine Class.
 *
 * @class Block_Engine
 */
final class Block_Engine
{

    /**
     * Block_Engine version.
     *
     * @var string
     */
    public $version = BLOCK_ENGINE_VERSION;

    /**
     * The single instance of the class.
     *
     * @var Block_Engine
     * @since 1.0.0
     */
    protected static $_instance = null;


    /**
     * Main Block_Engine Instance.
     *
     * Ensures only one instance of Block_Engine is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see mb_aec_addons()
     * @return Block_Engine - Main instance.
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cloning is forbidden.', 'block-engine'), '1.0.0');
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Unserializing instances of this class is forbidden.', 'block-engine'), '1.0.0');
    }

    /**
     * Auto-load in-accessible properties on demand.
     *
     * @param mixed $key Key name.
     * @return mixed
     */
    public function __get($key)
    {
        if (in_array($key, array(''), true)) {
            return $this->$key();
        }
    }

    /**
     * Block_Engine Constructor.
     */
    public function __construct()
    {

        $this->define_constants();
        $this->includes();
        $this->init_hooks();


        do_action('block_engine_loaded');
    }

    /**
     * Hook into actions and filters.
     *
     * @since 1.0.0
     */
    private function init_hooks()
    {


        register_activation_hook(BLOCK_ENGINE_FILE, array('Block_Engine_Install', 'install'));

        add_action('init', array($this, 'init'), 0);


    }

    /**
     * Define Block_Engine Constants.
     */
    private function define_constants()
    {

        $this->define('BLOCK_ENGINE_ABSPATH', dirname(BLOCK_ENGINE_FILE) . '/');
        $this->define('BLOCK_ENGINE_BASENAME', plugin_basename(BLOCK_ENGINE_FILE));
    }

    /**
     * Define constant if not already set.
     *
     * @param string $name Constant name.
     * @param string|bool $value Constant value.
     */
    private function define($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     * @return bool
     */
    private function is_request($type)
    {
        switch ($type) {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined('DOING_AJAX');
            case 'cron':
                return defined('DOING_CRON');
            case 'frontend':
                return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON') && !defined('REST_REQUEST');
        }
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes()
    {
        /**
         * Class autoloader.
         */
        include_once BLOCK_ENGINE_ABSPATH . 'includes/class-block-engine-autoloader.php';

        include_once BLOCK_ENGINE_ABSPATH . 'includes/helper/class-block-engine-api-helper.php';
        include_once BLOCK_ENGINE_ABSPATH . 'includes/class-block-engine-hooks.php';
        include_once BLOCK_ENGINE_ABSPATH . 'includes/class-block-engine-ajax.php';


        if ($this->is_request('admin')) {
            Block_Engine_Admin::instance();
        }

        if ($this->is_request('frontend')) {
            Block_Engine_Frontend::instance();
        }


    }


    /**
     * Init Block_Engine when WordPress Initialises.
     */
    public function init()
    {
        // Before init action.
        do_action('before_block_engine_init');

        // Set up localisation.
        $this->load_plugin_textdomain();


        // Init action.
        do_action('block_engine_init');
    }

    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present.
     *
     * Locales found in:
     *      - WP_LANG_DIR/block-engine/block-engine-LOCALE.mo
     *      - WP_LANG_DIR/plugins/block-engine-LOCALE.mo
     */
    public function load_plugin_textdomain()
    {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, 'block-engine');
        unload_textdomain('block-engine');
        load_textdomain('block-engine', WP_LANG_DIR . '/block-engine/block-engine-' . $locale . '.mo');
        load_plugin_textdomain('block-engine', false, plugin_basename(dirname(BLOCK_ENGINE_FILE)) . '/i18n/languages');
    }

    /**
     * Ensure theme and server variable compatibility and setup image sizes.
     */
    public function setup_environment()
    {

        $this->define('BLOCK_ENGINE_TEMPLATE_PATH', $this->template_path());

    }

    /**
     * Get the plugin url.
     *
     * @return string
     */
    public function plugin_url()
    {
        return untrailingslashit(plugins_url('/', BLOCK_ENGINE_FILE));
    }

    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function plugin_path()
    {
        return untrailingslashit(plugin_dir_path(BLOCK_ENGINE_FILE));
    }

    /**
     * Get the template path.
     *
     * @return string
     */
    public function template_path()
    {
        return apply_filters('block_engine_template_path', 'block-engine/');
    }

    /**
     * Get the template path.
     *
     * @return string
     */
    public function plugin_template_path()
    {
        return apply_filters('block_engine_plugin_template_path', $this->plugin_path() . '/templates/');
    }

    /**
     * Get Ajax URL.
     *
     * @return string
     */
    public function ajax_url()
    {
        return admin_url('admin-ajax.php', 'relative');
    }


}
