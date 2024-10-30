<?php

function block_engine_render_number_box_block($attributes){
    extract($attributes);

    $column1 = '<div class="block_engine_number_1"'.($blockID==''?' style="border-color: '.$borderColor.';"':'').'>
        <div class="block_engine_number_box_number"'.($blockID==''?' style="background-color: '.$numberBackground.';"':'').'>
            <p class="block_engine_number_one_number"'.($blockID==''?' style="color: '.$numberColor.';"':'').'>'.$columnOneNumber.'</p>
        </div>
        <p class="block_engine_number_one_title"'.($blockID==''?' style="text-align: '.$title1Align.';"':'').'>'.$columnOneTitle.'</p>
        <p class="block_engine_number_one_body"'.($blockID==''?' style="text-align: '.$body1Align.';"':'').'>'.$columnOneBody.'</p>
    </div>';

    $column2 = '<div class="block_engine_number_2"'.($blockID==''?' style="border-color: '.$borderColor.';"':'').'>
        <div class="block_engine_number_box_number"'.($blockID==''?' style="background-color: '.$numberBackground.';"':'').'>
            <p class="block_engine_number_two_number"'.($blockID==''?' style="color: '.$numberColor.';"':'').'>'.$columnTwoNumber.'</p>
        </div>
        <p class="block_engine_number_two_title"'.($blockID==''?' style="text-align: '.$title2Align.';"':'').'>'.$columnTwoTitle.'</p>
        <p class="block_engine_number_two_body"'.($blockID==''?' style="text-align: '.$body2Align.';"':'').'>'.$columnTwoBody.'</p>
    </div>';

    $column3 = '<div class="block_engine_number_3"'.($blockID==''?' style="border-color: '.$borderColor.';"':'').'>
        <div class="block_engine_number_box_number"'.($blockID==''?' style="background-color: '.$numberBackground.';"':'').'>
            <p class="block_engine_number_three_number"'.($blockID==''?' style="color: '.$numberColor.';"':'').'>'.$columnThreeNumber.'</p>
        </div>
        <p class="block_engine_number_three_title"'.($blockID==''?' style="text-align: '.$title3Align.';"':'').'>'.$columnThreeTitle.'</p>
        <p class="block_engine_number_three_body"'.($blockID==''?' style="text-align: '.$body3Align.';"':'').'>'.$columnThreeBody.'</p>
    </div>';

    $columns = $column1;

    if((int)$column >= 2){
        $columns .= $column2;
    }
    if((int)$column >= 3){
        $columns .= $column3;
    }

    return '<div class="block_engine_number_box column_'.$column.(isset($className) ? ' ' . esc_attr($className) : '').
            '"'.($blockID==''?'':' id="block-engine-number-box-'.$blockID.'"').'>'.$columns.'</div>';
}

function block_engine_register_number_box_block() {
	if ( function_exists( 'register_block_type' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
        register_block_type( 'block-engine/number-box', array(
            'attributes' => $defaultValues['block-engine/number-box']['attributes'],
			'render_callback' => 'block_engine_render_number_box_block'));
	}
}

add_action('init', 'block_engine_register_number_box_block');