<?php
	/**
	 * Elgg category - edit category
	 * 
	 * @package Elgg SocialCommerce
	 * @license http://www.gnu.org/licenses/gpl-2.0.html
	 * @author ray peaslee
	 * @copyright twentyfiveautumn.com 2015
	 * @link http://twentyfiveautumn.com/
	 * @version elgg 1.9.4
	 **/

	admin_gatekeeper();

	$category = (int) get_input('category_guid');
	$title = elgg_view_title(elgg_echo('category:edit'));
	
	if ($category = get_entity($category)) {
		if ($category->canEdit()) { 
    		$content = $title.'<div class="contentWrapper">'.elgg_view("socialcommerce/forms/edit_category", array('entity' => $category)).'</div>';
    		$sidebar .= elgg_view("socialcommerce/sidebar");
			$sidebar .= gettags();
			
			$params = array(
				'title' => $title,
				'content' => $content,
				'sidebar' => $sidebar,
				);
			$body = elgg_view_layout('one_sidebar', $params);
			echo elgg_view_page(elgg_echo('category:edit'), $body);
		}
	} else {
		forward();
	}
