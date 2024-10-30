<?php

function block_engine_render_testimonial_block($attributes){
    extract($attributes);
    return '<div>
    <div class="block_engine_testimonial'.(isset($className) ? ' ' . esc_attr($className) : '').
            '"'.($blockID==''?'style= "background-color: '.$backgroundColor.'; color: '.$textColor.';"'
                :' id="block_engine_testimonial_'.$blockID.'"').'>
        <div class="block_engine_testimonial_img">
            <img src="'.$imgURL.'" alt="'.$imgAlt.'" height="100" width="100" />
        </div>
        <div class="block_engine_testimonial_content">
            <p class="block_engine_testimonial_text"'.
                ($blockID==''?' style="font-size: '.$textSize.'px; text-align: '.$textAlign.';"':'').'>'.
                $block_engine_testimonial_text.'</p>
        </div>
        <div class="block_engine_testimonial_sign">
            <p class="block_engine_testimonial_author"'.
                ($blockID==''?' style="font-size: '.$textSize.'px; text-align: '.$authorAlign.';"':'').'>'.
                $block_engine_testimonial_author.'</p>
            <p class="block_engine_testimonial_author_role"'.
                ($blockID==''?' style="font-size: '.$textSize.'px; text-align: '.$authorRoleAlign.';"':'').'>'.
                $block_engine_testimonial_author_role.'</p>
        </div>
    </div>
</div>';
}

function block_engine_register_testimonial_block() {
	if( function_exists( 'register_block_type' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
		register_block_type( 'block-engine/testimonial', array(
            'attributes' =>$defaultValues['block-engine/testimonial']['attributes'],
            'render_callback' => 'block_engine_render_testimonial_block'));
    }
}

add_action('init', 'block_engine_register_testimonial_block');