<?php

function block_engine_render_feature_box_block($attributes){
    extract($attributes);

    $column1 = '<div class="block_engine_feature_1">
    <img class="block_engine_feature_one_img" src="' . $imgOneURL . '" alt="' . $imgOneAlt . '"/>
    <p class="block_engine_feature_one_title"'.($blockID==''?' style="text-align: '.$title1Align.';"':'').'>' . $columnOneTitle . '</p>
    <p class="block_engine_feature_one_body"'.($blockID==''?' style="text-align: '.$body1Align.';"':'').'>' . $columnOneBody . '</p></div>';

    $column2 = '<div class="block_engine_feature_2">
    <img class="block_engine_feature_two_img" src="' . $imgTwoURL . '" alt="' . $imgTwoAlt . '"/>
    <p class="block_engine_feature_two_title"'.($blockID==''?' style="text-align: '.$title2Align.';"':'').'>' . $columnTwoTitle . '</p>
    <p class="block_engine_feature_two_body"'.($blockID==''?' style="text-align: '.$body2Align.';"':'').'>' . $columnTwoBody . '</p></div>';

    $column3 = '<div class="block_engine_feature_3">
    <img class="block_engine_feature_three_img" src="'.$imgThreeURL.'" alt="' . $imgThreeAlt . '"/>
    <p class="block_engine_feature_three_title"'.($blockID==''?' style="text-align: '.$title3Align.';"':'').'>' . $columnThreeTitle . '</p>
    <p class="block_engine_feature_three_body"'.($blockID==''?' style="text-align: '.$body3Align.';"':'').'>' . $columnThreeBody . '</p></div>';

    $columns = $column1;

    if((int)$column >= 2){
        $columns .= $column2;
    }
    if((int)$column >= 3){
        $columns .= $column3;
    }

    return '<div class="block_engine_feature_box column_'.$column.(isset($className) ? ' ' . esc_attr($className) : '').'"'
        .($blockID==''?: ' id="block_engine_feature_box_'.$blockID.'"').'>'.
    $columns.'</div>';
}

function block_engine_register_feature_box_block() {
	if ( function_exists( 'register_block_type' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
        register_block_type( 'block-engine/feature-box', array(
            'attributes' => $defaultValues['block-engine/feature-box']['attributes'],
			'render_callback' => 'block_engine_render_feature_box_block'));
	}
}

add_action('init', 'block_engine_register_feature_box_block');