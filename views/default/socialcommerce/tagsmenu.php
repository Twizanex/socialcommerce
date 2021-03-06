<?php
	/**
	 * Elgg view - tags menu
	 * 
	 * @package Elgg products
	 * @license http://www.gnu.org/licenses/gpl-2.0.html
	 * @author twentyfiveautumn.com
	 * @copyright twentyfiveautumn.com 2014
	 * @link http://twentyfiveautumn.com/
	 **/ 
	 
	$tags = $vars['tags'];
	
	if (is_array($vars['tags']) && sizeof($vars['tags'])) {
		$all = 'all';
		$vars['tags'][] = $all;
		$vars['tags'] = array_reverse( $vars['tags'] );
		foreach($vars['tags'] as $tag) { 
			print_r( $tagarr );

			$label = $tag != 'all' ? elgg_echo($tag) : elgg_echo('all') ;
			
			$url = elgg_get_config('url').'socialcommerce/'.$_SESSION['user']->username.'/search/subtype/stores/';
			
			if ($tag != "all")
				$url .= 'md_type/simpletype/tag/'.urlencode($tag);
				
			$inputtag = get_input('tag');
			if ($inputtag == $tag || (empty($inputtag) && $tag == "all")) {
				$class = ' class="selected" ';
			} else {
				$class = "";
			}
				
			$submenu .= elgg_register_menu_item( $inputtag, array('text' => $label, 'href' => $url ));
		}
	}
