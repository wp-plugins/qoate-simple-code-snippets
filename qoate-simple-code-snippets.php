<?php
/*
Plugin Name: Qoate Simple Code Snippets
Plugin URI: http://qoate.com/wordpress-plugins/simple-code-snippets/
Description: Add code snippets to your posts easily, WordPress codex style.
Version: 2.1
Author: Danny van Kooten
Author URI: http://qoate.com
License: GPL2
*/

/*  Copyright 2010  Danny van Kooten  (email : danny@qoate.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class Qoate_Simple_Code_Snippets {

	function __construct()
	{
		remove_filter('the_content','wpautop');
		add_filter('the_content','wpautop',99);
		add_action('wp_print_styles',array(&$this,'add_stylesheet'));
		add_shortcode('code', array(&$this,'replace_code'));
		add_filter('the_excerpt_rss',array(&$this,'strip_shortcodes'));
		add_filter('the_content_rss',array(&$this,'strip_shortcodes'));
		add_filter('the_excerpt',array(&$this,'strip_shortcodes'),1);
		add_filter('the_content',array(&$this,'strip_shortcodes'),99);
		
		if(is_admin()) {
			$plugin = plugin_basename(__FILE__); 
			add_filter("plugin_action_links_$plugin", array(&$this,'add_settings_link'));
		}
	}
	
	function replace_code($atts,$content)
	{
		if(version_compare(PHP_VERSION,'5.2.3')== -1) {
			$content ='<pre class="qoate-code">'.htmlspecialchars($content,ENT_NOQUOTES,'UTF-8').'</pre>';
		} else {
			$content ='<pre class="qoate-code">'.htmlspecialchars($content,ENT_NOQUOTES,'UTF-8',false).'</pre>';
		}
		
		return $content;
	}
	
	function add_stylesheet()
	{
		wp_enqueue_style('qoate_scs_style', WP_CONTENT_URL . '/plugins/qoate-simple-code-snippets/css/scs-style.css');
	}
	
	function strip_shortcodes($content)
	{
		$content=str_replace('[code]','<pre>',$content);
		$content=str_replace('[/code]','</pre>',$content);
		return $content;
	}
	
	function add_settings_link($links)
	{
		$settings_link = '<a target="_blank" href="http://DannyvanKooten.com/">DannyvanKooten.com</a>'; 
		array_unshift($links, $settings_link); 
		return $links; 
	}

}

$Qoate_Simple_Code_Snippets = new Qoate_Simple_Code_Snippets();
?>