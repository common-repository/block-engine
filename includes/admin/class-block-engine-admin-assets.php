<?php
if (!class_exists('Block_Engine_Admin_Assets')) {
    class Block_Engine_Admin_Assets
    {
        function __construct()
        {

            add_action('admin_enqueue_scripts', array($this, 'load_admin_scripts'));

        }

        public function load_admin_scripts($hook)
        {

            wp_register_style('block-engine-font-awesome', BLOCK_ENGINE_PLUGIN_URI . '/assets/lib/font-awesome/css/font-awesome.css', false, '5.9.0');
            wp_enqueue_style('block-engine-font-awesome');

        }

    }

}
return new Block_Engine_Admin_Assets();