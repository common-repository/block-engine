<?php

function block_engine_render_countdown_block($attributes){
    //used to display initial rendering
    extract($attributes);

    $timeLeft = $endDate - time();
    $seconds = $timeLeft % 60;
    $minutes = (($timeLeft - $seconds) % 3600) / 60;
    $hours = (($timeLeft - $minutes * 60 - $seconds) % 86400) / 3600;
    $days = (($timeLeft - $hours * 3600 - $minutes * 60 - $seconds) % 604800) / 86400;
    $weeks = ($timeLeft - $days * 86400 - $hours * 3600 - $minutes * 60 - $seconds) / 604800;

    $defaultFormat = '<span class="block_engine_countdown_week">' . $weeks . '</span> ' . __( 'weeks', 'block-engine' )
        . ' <span class="block_engine_countdown_day">' . $days . '</span> ' . __('days', 'block-engine')
        . ' <span class="block_engine_countdown_hour">' . $hours . '</span> ' . __( 'hours', 'block-engine' )
        . ' <span class="block_engine_countdown_minute">' . $minutes . '</span> ' . __( 'minutes', 'block-engine' )
        . ' <span class="block_engine_countdown_second">' . $seconds . '</span> ' . __( 'seconds', 'block-engine' );

    if(!function_exists('block_engine_generateCircle')){
        function block_engine_generateCircle($label, $value, $limit, $color){
            $circlePath="M 50,50 m 0,-35 a 35,35 0 1 1 0,70 a 35,35 0 1 1 0,-70";
            $prefix="block_engine_countdown_circle_";
            return '<div class="'.$prefix.$label.'">
                        <svg height="70" width="70" viewBox="0 0 100 100">
                            <path class="'.$prefix.'trail" d="'.$circlePath.'" stroke-width="3" ></path>
                            <path class="'.$prefix.'path" d="'.$circlePath.'" stroke="'.$color.
                                '" stroke-width="3" style="stroke-dasharray: '.$value*219.911/$limit.'px, 219.911px;"></path>
                        </svg>
                        <div class="'.$prefix.'label block_engine_countdown_'.$label.'">'.$value.'</div>
                    </div>';
        }
    }
    
    $circularFormat = '<div class="block_engine_countdown_circular_container">
                        '.block_engine_generateCircle("week", $weeks, 52, $circleColor)
                        .block_engine_generateCircle("day", $days, 7, $circleColor)
                        .block_engine_generateCircle("hour", $hours, 24, $circleColor)
                        .block_engine_generateCircle("minute", $minutes, 60, $circleColor)
                        .block_engine_generateCircle("second", $seconds, 60, $circleColor).'
                        <p>'.__( 'Weeks', 'block-engine' ).'</p>
                        <p>'.__( 'Days', 'block-engine' ).'</p>
                        <p>'.__( 'Hours', 'block-engine' ).'</p>
                        <p>'.__( 'Minutes', 'block-engine' ).'</p>
                        <p>'.__( 'Seconds', 'block-engine' ).'</p>
                    </div>';

    $odometerSeparator = '<span class="block-engine-countdown-separator">:</span>';

    $emptySpan = '<span></span>';

    $odometerFormat = '<div class="block-engine-countdown-odometer-container">
                        <span>'.__( 'Weeks', 'block-engine' ).'</span>'.$emptySpan.'<span>'.__( 'Days', 'block-engine' ).'</span>'.$emptySpan.
                        '<span>'.__( 'Hours', 'block-engine' ).'</span>'.$emptySpan.'<span>'.__( 'Minutes', 'block-engine' ).'</span>'.$emptySpan.'<span>'.__( 'Seconds', 'block-engine' ).'</span>
                        <div class="block-engine-countdown-odometer block_engine_countdown_week">' . $weeks .'</div> 
                        '. $odometerSeparator.' <div class="block-engine-countdown-odometer block_engine_countdown_day">' . $days . '</div>
                        '. $odometerSeparator.'<div class="block-engine-countdown-odometer block_engine_countdown_hour">' . ($hours < 10 ? '0' . $hours : $hours) . '</div>
                        '. $odometerSeparator.'<div class="block-engine-countdown-odometer block_engine_countdown_minute">' . ($minutes < 10 ? '0' . $minutes : $minutes) . '</div>
                        '. $odometerSeparator.'<div class="block-engine-countdown-odometer block_engine_countdown_second">' . ($seconds < 10 ? '0' . $seconds : $seconds) . '</div></div>';

    $selctedFormat = $defaultFormat;
    
    if($style=='Regular'){
        $selectedFormat = $defaultFormat;
    }
    elseif ($style=='Circular') {
        $selectedFormat = $circularFormat;
    }
    else{
        $selectedFormat = $odometerFormat;
    }

    if($timeLeft > 0){
        return '<div '.($blockID==''?'': 'id="block_engine_countdown_'.$blockID.'"' ).'class="block-engine-countdown'.
                (isset($className)?' '.esc_attr($className):'').
                '" data-expirymessage="'.$expiryMessage.'" data-enddate="'.$endDate.'">
            '.$selectedFormat
            .'</div>';
    }
    else return '<div class="block-engine-countdown'.(isset($className) ? ' ' . esc_attr($className) : '').'" '.
        ($blockID==''?'style="text-align:'.$messageAlign.';' :'id="block_engine_countdown_'.$blockID.'"').'>'.$expiryMessage.'</div>';
}

function block_engine_register_countdown_block() {
	if( function_exists( 'register_block_type' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
		register_block_type( 'block-engine/countdown', array(
            'attributes' => $defaultValues['block-engine/countdown']['attributes'],
            'render_callback' => 'block_engine_render_countdown_block'));
    }
}

add_action( 'init', 'block_engine_register_countdown_block' );

function block_engine_countdown_add_frontend_assets() {
    require_once dirname(dirname(__DIR__)) . '/common.php';

    $presentBlocks = block_engine_get_present_blocks();

    foreach( $presentBlocks as $block ){
        if($block['blockName'] == 'block-engine/countdown'){
            wp_enqueue_script(
                'block_engine-countdown-script',
                BLOCK_ENGINE_PLUGIN_URI. '/includes/guten/blocks/countdown/js/front.build.js',
                array(  ),
                BLOCK_ENGINE_VERSION,
                true
            );
            if(!isset($block['attrs']['style'])){ //odometer, the default style, is selected
                wp_enqueue_script(
                    'block_engine-countdown-odometer-script',
                    BLOCK_ENGINE_PLUGIN_URI. '/includes/guten/blocks/countdown/js/odometer.js',
                    array(  ),
                    BLOCK_ENGINE_VERSION,
                    true
                );
                break;
            }
        }
    }
}

add_action( 'wp_enqueue_scripts', 'block_engine_countdown_add_frontend_assets' );