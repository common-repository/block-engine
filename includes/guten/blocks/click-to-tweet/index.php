<?php
/**
 * Click to tweet block.
 */

/**
 * Registering meta for the tweet.
 */
function block_engine_register_meta() {
	register_meta( 'post', 'block_engine_ctt_via', array(
		'show_in_rest' => true,
		'single' => true
	) );
}

add_action( 'init', 'block_engine_register_meta' );

/**
 * Rendering the block dynamically.
 *
 * @param $attributes
 *
 * @return string
 *
 */
function block_engine_render_click_to_tweet_block( $attributes ) {
    extract($attributes);
	$via = get_post_meta( get_the_ID(), 'block_engine_ctt_via', true );
	$via = ( $via ) ? '&via=' . str_replace( '@', '', $via ) : false;
    $tweet = preg_replace('/<br><br>$/', '<br>', $ubTweet);
	$tweet_url  = ( $tweet ) ? rawurlencode( preg_replace('/<.+?>/', '', str_replace("<br>","\n",$tweet) )) : false;

    /*$tweetFontSize = isset( $attributes['tweetFontSize'] ) ? "font-size:{$attributes['tweetFontSize']}" : "font-size: 20";
	$tweetColor = isset( $attributes['tweetColor'] ) ? "color:{$attributes['tweetColor']}" : "color: #444444";
    $borderColor = isset( $attributes['borderColor'] ) ? "border-color:{$attributes['borderColor']}" : "border-color: #CCCCCC";
    */

	$permalink = esc_url( get_the_permalink() );
	$url       = apply_filters( 'block_engine_click_to_tweet_url', "http://twitter.com/intent/tweet?&text={$tweet_url}&url={$permalink}{$via}" );

    $output = '';
    if($blockID == ''){
        $output .= sprintf('<div class="block_engine_click_to_tweet%1$s" style="border-color: %2$s;">', (isset($className) ? ' ' . esc_attr($className) : ''), $borderColor );
        $output .= sprintf( '<div class="block_engine_tweet" style="font-size: %1$spx; color: %2$s">', $tweetFontSize, $tweetColor );
    }
    else{
        $output .= sprintf('<div class="block_engine_click_to_tweet%1$s" id="%2$s">', (isset($className) ? ' ' . esc_attr($className) : ''), esc_attr('block_engine_click_to_tweet_' . $blockID ));
        $output .= sprintf( '<div class="block_engine_tweet">');
    }

    $output .= $tweet;
	$output .= sprintf('</div>');
	$output .= sprintf( '<div class="block_engine_click_tweet">' );
	$output .= sprintf( '<span>');
	$output .= sprintf( '<i></i>');
	$output .= sprintf( '<a target="_blank" href="%1$s">' . __( 'Click to Tweet', 'block-engine' ) . '</a>',  $url  );
	$output .= sprintf( '</span>');
	$output .= sprintf( '</div>');
    $output .= sprintf( '</div>');

	return $output;
}

/**
 * Registering dynamic block.
 */
function block_engine_register_click_to_tweet_block() {
	if ( function_exists( 'register_block_type' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
		register_block_type( 'block-engine/click-to-tweet', array(
            'attributes' => $defaultValues['block-engine/click-to-tweet']['attributes'],
			'render_callback' => 'block_engine_render_click_to_tweet_block'));
	}
}

add_action('init', 'block_engine_register_click_to_tweet_block');
