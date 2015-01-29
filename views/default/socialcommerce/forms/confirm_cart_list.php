<?php
	/**
	 * Elgg products shopping cart form
	 * 
	 * @package Elgg products
	 * @license http://www.gnu.org/licenses/gpl-2.0.html
	 * @author twentyfiveautumn.com
	 * @copyright twentyfiveautumn.com 2014
	 * @link http://twentyfiveautumn.com/
	 **/ 
	
	$user = elgg_get_logged_in_user_entity();
	 
	if(elgg_is_logged_in()){
		if($vars['not_allow'] == 1){
			$hidden = '<input type="hidden" name="not_allow" value="1">';
			$action = "#";
		}else{
			$action = elgg_get_config('url').'socialcommerce/'.$user->username.'/checkout/';
		}
		$username = "/".$user->username;
	}
	$submit_input = elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo('check:out')));
	$buy_more = elgg_echo('buy:more');
	$hidden_values = elgg_view('input/securitytoken');
	$buy_more_link = elgg_get_config('url').'socialcommerce'.$username."/all";

$form_body = <<< BOTTOM
		<form method="post" id="checkout_form" action="$action" >
			<div class="content_area_user_bottom">
				<div class="bottom_content">
					<span class="buy_more"><a href="$buy_more_link">$buy_more</a></span>
					<span>$submit_input</span>&nbsp;
					<span class="space"></span>
					$hidden_values
				</div>
			</div>
		</form>
BOTTOM;

echo $form_body;
