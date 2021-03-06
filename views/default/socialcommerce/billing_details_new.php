<?php
	/**
	 * Elgg view - billing details
	 * 
	 * @package Elgg products
	 * @license http://www.gnu.org/licenses/gpl-2.0.html
	 * @author twentyfiveautumn.com
	 * @copyright twentyfiveautumn.com 2014
	 * @link http://twentyfiveautumn.com/
	 **/ 
	
	$page_owner = elgg_get_logged_in_user_entity();
	
	if(

	$address = elgg_get_entities_from_metadata(array(
								'metadata_name_value_pair' => array('name' => 'billing', 'value' =>  1),
								'type_subtype_pairs' => array('object' => 'address'),
								'owner_guid' => elgg_get_page_owner_guid(),
								))
	){

		if($address) { $selected_address = $address->guid; }
		
		$action = elgg_get_config('url')."ajax/view/socialcommerce/checkout";
		$exist = elgg_echo('billing:address:exist');
		$exist_address = elgg_view("socialcommerce/list_address", array('entity'=>$address,'display'=>'list','selected'=>$selected_address,'type'=>'billing'));
		
		$new = elgg_echo('billing:address:new');

	/*	form for current billing address	*/
		
		$form_vars = array('action' => $action, 'id'=> 'current_billing_address_form' ); 
		$body_vars = array('checked' => 'checked','exist_address' => $exist_address); 
		$address_billing = elgg_view_form("address/billing", $form_vars, $body_vars);
	
	/*	form to add a new billing address	*/
		$body_vars = array('type' => 'billing'); 
		$address_add = elgg_view_form("address/address", $form_vars, $body_vars);
		
		$submit_input = elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo('billing:address')));
		
		$billing_checkboxes = elgg_view('input/radio', array(
					'name' => 'billing_address_type',
					'id' => 'billing_address_type',
					'align' => 'horizontal',
					'value' => 1,
					'options' => array(elgg_echo('billing:address:exist') => 1, elgg_echo('billing:address:new') => 2),
					));
		
		$address_details = <<<EOF
			{$billing_checkboxes}
				<div id = "current_billing_address">
				{$address_billing}
				</div>
				<div id = "add_billing_address">
				{$address_add}
				</div>
			<hr>
	
EOF;
	}else{
		$body_vars = array('type' => 'billing'); 
		$address_details = elgg_view_form("address/address", $form_vars, $body_vars);
	}
	echo $address_details;
?>
