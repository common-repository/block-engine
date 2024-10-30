<?php

function block_engine_render_expand_portion_block($attributes, $content){
    extract($attributes);
    return '<div class="block-engine-expand-portion block-engine-expand-'.$displayType.
        ($displayType == 'full' ? ' block-engine-hide' : '').
        (isset($className) ? ' ' . esc_attr($className) : '').'">'.
        $content.
        '<a class="block-engine-expand-toggle-button">'.$clickText.'</a>'
        .'</div>';
}

function block_engine_register_expand_portion_block($attributes){
    if ( function_exists( 'register_block_type' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
        register_block_type( 'block-engine/expand-portion', array(
            'attributes' => $defaultValues['block-engine/expand-portion']['attributes'],
			'render_callback' => 'block_engine_render_expand_portion_block'));
	}
}

function block_engine_render_expand_block($attributes, $content){
    extract($attributes);
    return '<div class="block-engine-expand '.(isset($className) ? ' ' . esc_attr($className) : '')
    .'" id="block-engine-expand-'.$blockID.'">'.$content.'</div>';
}

function block_engine_register_expand_block($attributes){
    if ( function_exists( 'register_block_type' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
        register_block_type( 'block-engine/expand', array(
            'attributes' => $defaultValues['block-engine/expand']['attributes'],
			'render_callback' => 'block_engine_render_expand_block'));
	}
}

function block_engine_expand_block_add_frontend_assets() {
    require_once dirname(dirname(__DIR__)) . '/common.php';

    $presentBlocks = block_engine_get_present_blocks();

    foreach( $presentBlocks as $block ){
        if($block['blockName'] == 'block-engine/expand' || $block['blockName'] == 'block-engine/expand-portion'){
            wp_enqueue_script(
                'block_engine-expand-block-front-script',
                BLOCK_ENGINE_PLUGIN_URI. '/includes/guten/blocks/expand/js/front.build.js',
                array( ),
                BLOCK_ENGINE_VERSION,
                true
            );
            break;
        }
    }
}

add_action('init', 'block_engine_register_expand_block');
add_action('init', 'block_engine_register_expand_portion_block');
add_action( 'wp_enqueue_scripts', 'block_engine_expand_block_add_frontend_assets' );