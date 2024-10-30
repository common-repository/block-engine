<?php

function block_engine_render_divider_block($attributes){
    extract($attributes);
    return '<div class="block_engine_divider'.(isset($className) ? ' ' . esc_attr($className) : '').'" '.
    ($blockID==''?'style="border-top: '.$borderSize.'px '.$borderStyle.' '.$borderColor.'; margin-top: '.$borderHeight.'px; margin-bottom: '.$borderHeight.'px;"' :'id="block_engine_divider_'.$blockID.'"').'></div>';
}

function block_engine_register_divider_block(){
    if ( function_exists( 'register_block_type' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
        register_block_type( 'block-engine/divider', array(
            'attributes' => $defaultValues['block-engine/divider']['attributes'],
            'render_callback' => 'block_engine_render_divider_block'));
    }
}

add_action('init', 'block_engine_register_divider_block');