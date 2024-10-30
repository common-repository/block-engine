<?php
if (!class_exists('Block_Engine_Assets')) {
    class Block_Engine_Assets
    {
        function __construct()
        {
            add_action('wp_enqueue_scripts', array($this, 'scripts'));


        }

        public function scripts($hook)
        {


        }



    }
}

return new Block_Engine_Assets();