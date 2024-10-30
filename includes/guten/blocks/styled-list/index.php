<?php

function block_engine_render_styled_list_block($attributes){
    extract($attributes);

    $listItems = '';

    if(json_encode($listItem) == '[' . join(',', array_fill(0, 3,'{"text":"","selectedIcon":"check","indent":0}')) . ']'){
        require  dirname(dirname(__DIR__)) . '/common.php';
        $listItems = str_replace('<li>', '<li><span class="fa-li"><svg xmlns="http://www.w3.org/2000/svg" height="'.(0.4 + $iconSize * 0.1) .
             'em", width="'.(0.4 + $iconSize * 0.1) . 'em" viewBox="0, 0, '.$block_engine_font_awesome_icon[$selectedIcon][0].', '.$block_engine_font_awesome_icon[$selectedIcon][1]
            .'"><path fill="'.$iconColor.'" d="'.$block_engine_font_awesome_icon[$selectedIcon][2].'"></svg></span>', $list);
    }
    else{
        $sortedItems = [];

        foreach($listItem as $elem){
            $last = count($sortedItems) - 1;
            if (count($sortedItems) == 0 || $sortedItems[$last][0]['indent'] < $elem['indent']) {
                array_push($sortedItems, array($elem));
            }
            else if ($sortedItems[$last][0]['indent'] == $elem['indent']){
                array_push($sortedItems[$last], $elem);
            }
            else{
                while($sortedItems[$last][0]['indent'] > $elem['indent']){
                    array_push($sortedItems[count($sortedItems) - 2], array_pop($sortedItems));
                    $last = count($sortedItems) - 1;
                }
                if($sortedItems[$last][0]['indent'] == $elem['indent']){
                    array_push($sortedItems[$last], $elem);
                }
            }
        }
    
        while(count($sortedItems) > 1 &&
            $sortedItems[count($sortedItems) - 1][0]['indent'] > $sortedItems[count($sortedItems) - 2][0]['indent']){
            array_push($sortedItems[count($sortedItems) - 2], array_pop($sortedItems));
        }
    
        $sortedItems = $sortedItems[0];
    
        if (!function_exists('block_engine_makeList')) {
            function block_engine_makeList($num, $item, $color, $size){
                require  dirname(dirname(__DIR__)) . '/common.php';
                static $outputString = '';
                if($num == 0 && $outputString != ''){
                    $outputString = '';
                }
                if (array_key_exists("indent", $item)){                
                    $outputString .= '<li><span class="fa-li"><svg xmlns="http://www.w3.org/2000/svg"
                        height="'.(0.4 + $size * 0.1) . 'em", width="'.(0.4 + $size * 0.1) . 'em" viewBox="0, 0, '.$block_engine_font_awesome_icon[$item['selectedIcon']][0].', '.$block_engine_font_awesome_icon[$item['selectedIcon']][1]
                        .'"><path fill="'.$color.'" d="'.$block_engine_font_awesome_icon[$item['selectedIcon']][2].'"></svg></span>'.($item['text']==''?'<br/>':$item['text']).'</li>';
                }
                else{
                    $outputString = substr_replace($outputString, '<ul class="fa-ul">',
                        strrpos($outputString, '</li>'), strlen('</li>'));
    
                    forEach($item as $key => $subItem){
                        block_engine_makeList($key+1, $subItem, $color, $size);
                    }
                    $outputString .= '</ul>' . '</li>';
                }
                return $outputString;
            }
        }
    
        foreach($sortedItems as $key => $item){
            $listItems = block_engine_makeList($key, $item, $iconColor, $iconSize);
        }
    }

    return '<div class="block_engine_styled_list '.(isset($className) ? ' ' . esc_attr($className): '') .'"'
            .($blockID == '' ? '' : ' id="block_engine_styled_list-'.$blockID.'"').
            '><ul class="fa-ul">'.$listItems.'</ul></div>';
}

function block_engine_register_styled_list_block() {
	if ( function_exists( 'register_block_type' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
        register_block_type( 'block-engine/styled-list', array(
            'attributes' => $defaultValues['block-engine/styled-list']['attributes'],
            'render_callback' => 'block_engine_render_styled_list_block'));
	}
}

add_action('init', 'block_engine_register_styled_list_block');