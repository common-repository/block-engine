<?php
function block_engine_render_styled_box_block($attributes){
    extract($attributes);
    $renderedBlock = '';
    if($mode == 'notification'){
        $renderedBlock = '<div class="block-engine-notification-text">'.$text[0].'</div>';
    }
    else if($mode == 'number'){
        foreach(range(0, count($text)-1) as $i){
            $renderedBlock .= '<div class="block-engine-number-panel">
                <div class="block-engine-number-container">
                    <p class="block-engine-number-display">'.$number[$i].'</p>
                </div>
                <p class="block-engine-number-box-title">'.$title[$i].'</p>
                <p class="block-engine-number-box-body">'.$text[$i].'</p>
            </div>';
        }
    }
    else if($mode == 'feature'){
        foreach(range(0, count($text)-1) as $i){
            $renderedBlock .= '<div class="block-engine-feature">'.
                ($image[$i]['url'] == '' ? '' :
                    '<img class="block-engine-feature-img" src="'.$image[$i]['url'].'"/>').
                    '<p class="block-engine-feature-title">'.$title[$i].'</p>
                    <p class="block-engine-feature-body">'.$text[$i].'</p>
            </div>';
        }
    }

    return '<div class="block-engine-styled-box block-engine-'.$mode.'-box'.(isset($className) ? ' ' . esc_attr($className) : '')
            .'" id="block-engine-styled-box-'.$blockID.'">'.
                $renderedBlock.'</div>';
}

function block_engine_register_styled_box_block() {
	if( function_exists( 'register_block_type' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
		register_block_type( 'block-engine/styled-box', array(
            'attributes' => $defaultValues['block-engine/styled-box']['attributes'],
            'render_callback' => 'block_engine_render_styled_box_block'));
    }
}

add_action('init', 'block_engine_register_styled_box_block');