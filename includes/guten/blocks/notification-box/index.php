<?php

function block_engine_render_notification_box_block($attributes){
    extract($attributes);
    return '<div>
    <div class="wp-block-block-engine-notification-box '.$block_engine_selected_notify.
        (isset($className) ? ' ' . esc_attr($className) : '').'"'.($blockID==''? :' id="block-engine-notification-box-'.$blockID.'"').'>
        <p class="block_engine_notify_text"'.($blockID==''?' style="text-align: '.$align.';"':'').'>'.$block_engine_notify_info.'</p>
    </div>
</div>';
}

function block_engine_register_notification_box_block() {
	if ( function_exists( 'register_block_type' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
        register_block_type( 'block-engine/notification-box', array(
            'attributes' => $defaultValues['block-engine/notification-box']['attributes'],
			'render_callback' => 'block_engine_render_notification_box_block'));
	}
}

add_action('init', 'block_engine_register_notification_box_block');