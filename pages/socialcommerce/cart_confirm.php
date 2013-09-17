<?php
	/**
	 * Elgg cart - confirm page
	 * 
	 * @package Elgg SocialCommerce
	 * @license http://www.gnu.org/licenses/gpl-2.0.html
	 * @author twentyfiveautumn.com
	 * @copyright twentyfiveautumn.com 2013
	 * @link http://twentyfiveautumn.com/
	 **/ 
	 
	gatekeeper();
	 
	// Load Elgg engine
	require_once(get_config('path').'engine/start.php');
	global $CONFIG;
	
	// Get the current page's owner
		$page_owner = elgg_get_page_owner_entity();
		if ($page_owner === false || is_null($page_owner)) {
			$page_owner = $_SESSION['user'];
			elgg_set_page_owner_guid($_SESSION['guid']);
		}

	// Set stores title
		if($page_owner == $_SESSION['user']){
			$title = elgg_view_title(elgg_echo('cart:confirm'));
		}
	
	// Get objects
	elgg_set_context('confirm');
	$content = elgg_view("socialcommerce/cart_confirm");
	elgg_set_context('stores');
	$content = $title.'<div class="contentWrapper stores">'.$content.'</div>';
	$sidebar .= gettags();
		
	$params = array(
		'title' => $title,
		'content' => $content,
		'sidebar' => $sidebar,
		);
	$body = elgg_view_layout('one_sidebar', $params);
	echo elgg_view_page($title, $body);
?>