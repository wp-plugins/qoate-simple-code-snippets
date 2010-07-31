<?php
add_action('wp_print_styles','add_qoate_scs_style');
remove_filter('the_content','wpautop');

function add_qoate_scs_style(){
	wp_enqueue_style('qoate_scs_style', WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)). '/qoate-scs-style.css');
}

function qoate_replace_code($atts,$content) {
	return '<pre class="qoate-code">'.htmlspecialchars($content,ENT_NOQUOTES,'UTF-8',false).'</pre>';
}
add_shortcode('code', 'qoate_replace_code');
?>

