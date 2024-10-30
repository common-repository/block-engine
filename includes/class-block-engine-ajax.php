<?php
if (!class_exists('Block_Engine_Aajax')) {
    class Block_Engine_Aajax
    {
        function __construct()
        {
            add_action('wp_ajax_block_engine_fetch_tmpl_data', [$this, 'fetch_tmpl_data']);


        }

        public function fetch_tmpl_data()
        {
            $template_id = !isset($_POST['item_id']) ? '' : $_POST['item_id'];

            $template_data_url = Block_Engine_Api_Helper::$api_url . 'templates.php?item_id=' . $template_id;

            $response = wp_remote_get($template_data_url, [
                'key' => '',
                'host' => $_SERVER['HTTP_HOST']
            ]);

            $template = $response['body'];

            echo $template;

            exit;
        }


    }
}

return new Block_Engine_Aajax();