<?php
/*
Plugin Name: Qoate Simple Code Snippets
Plugin URI: http://dannyvankooten.com/wordpress-plugins/simple-code-snippets/
Description: Add code snippets to your posts easily, WordPress codex style.
Version: 2.1.1
Author: Danny van Kooten
Author URI: http://dannyvankooten.com
License: GPL2
*/

/*  Copyright 2010  Danny van Kooten  (email : danny@vkimedia.com)

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
			add_action('wp_dashboard_setup', array(&$this,'widget_setup'));	
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
	
	function dashboard_widget() {
			$options = get_option('dvkdbwidget');
			if (isset($_POST['dvk_removedbwidget'])) {
				$options['dontshow'] = true;
				update_option('dvkdbwidget',$options);
			}		
			
			if ($options['dontshow']) {
				echo "If you reload, this widget will be gone and never appear again, unless you decide to delete the database option 'dvkdbwidget'.";
				return;
			}
			
			require_once(ABSPATH.WPINC.'/rss.php');
			if ( $rss = fetch_rss( 'http://feeds.feedburner.com/dannyvankooten' ) ) {
				echo '<div class="rss-widget">';
				echo '<a href="http://dannyvankooten.com/" title="Go to DannyvanKooten.com"><img src="http://static.dannyvankooten.com/images/dvk-64x64.png" class="alignright" alt="DannyvanKooten.com"/></a>';			
				echo '<ul>';
				$rss->items = array_slice( $rss->items, 0, 3 );
				foreach ( (array) $rss->items as $item ) {
					echo '<li>';
					echo '<a target="_blank" class="rsswidget" href="'.clean_url( $item['link'], $protocolls=null, 'display' ).'">'. $item['title'] .'</a> ';
					echo '<span class="rss-date">'. date('F j, Y', strtotime($item['pubdate'])) .'</span>';
					echo '<div class="rssSummary">'. $this->text_limit($item['summary'],250) .'</div>';
					echo '</li>';
				}
				echo '</ul>';
				echo '<div style="border-top: 1px solid #ddd; padding-top: 10px; text-align:center;">';
				echo '<a target="_blank" style="margin-right:10px;" href="http://feeds.feedburner.com/dannyvankooten"><img src="'.get_bloginfo('wpurl').'/wp-includes/images/rss.png" alt=""/> Subscribe with RSS</a>';
				echo '<a target="_blank" href="http://dannyvankooten.com/newsletter/"><img src="http://static.dannyvankooten.com/images/email-icon.png" alt=""/> Subscribe by email</a>';
				echo '<form class="alignright" method="post"><input type="hidden" name="dvk_removedbwidget" value="true"/><input title="Remove this widget from all users dashboards" type="submit" value="X"/></form>';
				echo '</div>';
				echo '</div>';
			}
		}

		function widget_setup() {
			$options = get_option('dvkdbwidget');
			if (!$options['dontshow'])
		    	wp_add_dashboard_widget( 'dvk_db_widget' , 'Latest posts on DannyvanKooten.com' , array(&$this, 'dashboard_widget'));
		}
		
		function text_limit( $text, $limit, $finish = '...') {
			if( strlen( $text ) > $limit ) {
		    	$text = substr( $text, 0, $limit );
				$text = substr( $text, 0, - ( strlen( strrchr( $text,' ') ) ) );
				$text .= $finish;
			}
			return $text;
		}

}

$Qoate_Simple_Code_Snippets = new Qoate_Simple_Code_Snippets();
?>