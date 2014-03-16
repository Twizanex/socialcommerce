<?php
	/**
	 * Elgg products - start page
	 * 
	 * @package Elgg products
	 * @license http://www.gnu.org/licenses/gpl-2.0.html
	 * @author twentyfiveautumn.com
	 * @copyright twentyfiveautumn.com 2014
	 * @link http://twentyfiveautumn.com/
 	**/
	
	function socialcommerce_init() {
	    
	    // Load system configuration
		global $CONFIG;
		// load socialcommerce model
		require(get_config('pluginspath').'socialcommerce/modules/module.php');
		
		// make products show up in seach results
		elgg_register_entity_type( 'object', 'stores' );
			
		// Set up menu for logged in users
			if (elgg_is_logged_in()) {
				elgg_register_menu_item('site', array(
					'name' => 'stores',
					'text' => elgg_echo('item:object:stores'),
					'href' => 'socialcommerce/' . $_SESSION['user']->username.'/all/products/',
				));
			}
			
		// register js files ->   elgg_register_js( $name, $url, $location = 'head', $priority = null )
		elgg_register_js( 'jquery.validate', $CONFIG->url.'mod/socialcommerce/js/socialcommerce/checkout/jquery.validate.js', $location = 'footer', $priority = null );
		elgg_register_js( 'jquery.steps.min', $CONFIG->url.'mod/socialcommerce/js/socialcommerce/checkout/jquery.steps.min.js', $location = 'footer', $priority = null );
		elgg_register_js( 'jquery.accordion', $CONFIG->url.'mod/socialcommerce/js/socialcommerce/checkout/jquery.accordion.js', $location = 'footer', $priority = null );
		elgg_register_js( 'socialcommerce.checkout', $CONFIG->url.'mod/socialcommerce/js/socialcommerce/checkout/socialcommerce.checkout.js', $location = 'footer', $priority = null );
						
		//	register css
		elgg_register_css('jquery.steps', $CONFIG->url.'mod/socialcommerce/views/default/socialcommerce/css/checkout/jquery.steps.css', $priority = null);
		
		// register ajax views
		elgg_register_ajax_view('socialcommerce/change_country_state');
		elgg_register_ajax_view('socialcommerce/checkout');
		
		//we use google jquery instead of Elgg's as it is more up-to-date and required for bootstrap
	$google_jquery = 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js';
	elgg_register_js('jquery', $google_jquery);
				
		// extend CSS
			elgg_extend_view("css", "socialcommerce/css");
			
			elgg_extend_view("js", "socialcommerce/js/rating");
			
			elgg_extend_view("index/righthandside", "socialcommerce/products_list",600);
			elgg_extend_view("index/righthandside", "socialcommerce/most_popular_products",600);
					
			elgg_extend_view("page_elements/header_contents", "socialcommerce/header");
			
			elgg_extend_view("page/elements/head", "socialcommerce/extend_header");
			
			elgg_extend_view("page_elements/footer", "socialcommerce/extend_footer",400);
		
		// Extend hover-over menu	
			elgg_extend_view("profile/menu/links","socialcommerce/menu");
					
		// Load the language file
			register_translations($CONFIG->pluginspath . "socialcommerce/languages/");
			
		// Register a page handler, so we can have nice URLs
			elgg_register_page_handler("socialcommerce", "socialcommerce_page_handler");
			
		// Register an image handler for stores
			elgg_register_page_handler("storesimage","socialcommerce_image_handler");
			
		// Add widgets
			if(elgg_is_admin_logged_in()){
				elgg_register_widget_type('recent', elgg_echo("stores:recent:widget"),elgg_echo("stores:recent:widget:description"));
			}
			
			elgg_register_widget_type('mostly',elgg_echo("stores:mostly:widget"),elgg_echo("stores:mostly:widget:description"));
			
			if(!elgg_is_admin_logged_in()){
				elgg_register_widget_type('purchased',elgg_echo("stores:purchased:widget"),elgg_echo("stores:purchased:widget:description"));
			}
			
		// Register a URL handler for files
			elgg_register_entity_url_handler('object', 'stores', 'stores_url');
			elgg_register_entity_url_handler('object', 'category', 'category_url');
			elgg_register_entity_url_handler('object', 'cart', 'cart_url');
			
		// Now override icons
			elgg_register_plugin_hook_handler('entity:icon:url', 'object', 'socialcommerce_image_hook');
			
		// Register socialcommerce settings
			register_socialcommerce_settings();
			
		// Register country and state for socialcommerce
			register_country_state();

		sc_register_subtypes();
		
	    	if (elgg_get_context() == "stores" || elgg_get_context() == "socialcommerce") {
	    		if(!isset($_REQUEST['search_viewtype']))
	    			set_input('search_viewtype','list');
	    	}
    }
   
	// load product types
 	require(get_config('pluginspath').'socialcommerce/modules/product_types.php');
		
	function socialcommerce_pagesetup() {
		global $CONFIG;
		/*****	add menu items	*****/

		$menu_item = array(
			'name' => 'category',			
			'text' => elgg_echo('stores:category'), 			
			'href' => get_config('url').'socialcommerce/'. $_SESSION['user']->username .'/category/',			
			'contexts' => array('stores', 'socialcommerce'),	
			'parent_name' => 'stores',	
			);
			elgg_register_menu_item('site', $menu_item);
		
		if (elgg_get_context() == "stores" or elgg_get_context() == "socialcommerce") {
			if (isset($_SESSION['guid']) && elgg_is_logged_in()) {	

				if( elgg_is_admin_logged_in() ){
				
					// load socialcommerce menu
					require(get_config('pluginspath').'socialcommerce/modules/menu.php');
				
					$menu_item = array(
						'name' => 'new_product',			
						'text' => elgg_echo('stores:addpost'), 			
						'href' => get_config('url').'socialcommerce/'. $_SESSION['user']->username .'/add/',			
						'contexts' => array('stores', 'socialcommerce'),	
						'parent_name' => 'stores',	
						);
						elgg_register_menu_item('site', $menu_item);
			
					$menu_item = array(
						'name' => 'new_category',			
						'text' => elgg_echo('stores:addcategory'), 			
						'href' => get_config('url').'socialcommerce/'. $_SESSION['user']->username .'/addcategory/',			
						'contexts' => array('stores', 'socialcommerce'),	
						'parent_name' => 'stores',	
						);
						elgg_register_menu_item('site', $menu_item);
			
					$menu_item = array(
						'name' => 'sold_items',			
						'text' => elgg_echo('stores:sold:products'), 			
						'href' => get_config('url').'socialcommerce/'. $_SESSION['user']->username .'/sold/',			
						'contexts' => array('stores', 'socialcommerce'),	
						'parent_name' => 'stores',	
						);
						elgg_register_menu_item('site', $menu_item);
						
				}		//	end if( elgg_is_admin_logged_in() ){
					
			} 
			
		$page_owner = elgg_get_logged_in_user_entity();
			
		$menu_item = array(
			'name' => 'stores_user',			
			'text' => sprintf(elgg_echo('stores:user'), $page_owner->username), 			
			'href' => get_config('url').'socialcommerce/'. $page_owner->username .'/products/',			
			'contexts' => array('stores', 'socialcommerce'),	
			'parent_name' => 'stores',	
			);
			elgg_register_menu_item('site', $menu_item);
			
		$menu_item = array(
			'name' => 'stores_user_friends',			
			'text' => sprintf(elgg_echo('stores:user:friends'), $page_owner->username), 			
			'href' => get_config('url').'socialcommerce/'. $page_owner->username .'/friends/products/',			
			'contexts' => array('stores', 'socialcommerce'),	
			'parent_name' => 'stores',	
			);
			elgg_register_menu_item('site', $menu_item);
		}
	}
	
	function socialcommerce_page_handler( $page ) {
		global $CONFIG;
		$base_path = get_config('pluginspath').'socialcommerce/pages/socialcommerce/';
		
		/*****	The first component of a socialcommerce URL is the username	*****/
		if (isset($page[0]) && !is_numeric($page[0])){
			set_input('username', $page[0]);
		}
		if(is_numeric($page[0])){
			set_input('stores_guid', $page[0]);
		}
		$page_0 = array('all','login');
		if(in_array($page[0],$page_0)){
			switch($page[0]) {
				case "all":				require($base_path.'all.php');
										break;
				case "login":           require($base_path.'login.php');
									  	break;
			}
			return true;
		}
		// The second part dictates what we're doing
		if (isset($page[1])) {
			switch($page[1]) {
				case "add":				require($base_path.'add.php');
										break;
				case "addcategory":		require($base_path.'add_category.php'); 
										break;
				case "address":			require($base_path.'address.php');
										break;
				case "all":				require($base_path.'all.php');
										break;
				case "buy":				set_input('stores_guid', $page[2]);
										require($base_path.'buy.php');
										break;
				case "cancel":			sc_view_cancel_page();
										break;
				case "cart":			require($base_path.'cart.php');
										break;
				case "cart_success":	view_success_page();
										break;
				case "category":		require($base_path.'category.php'); 
										break;
				case "cateread":		set_input('guid',$page[2]);
										require(dirname(dirname(dirname(__FILE__))) . '/pages/entities/index.php');
										break;
				case "checkout":		require($base_path.'checkout.php'); 
									  	break;
				case "checkout_address": require($base_path.'checkout_address.php'); 
									  	break;
				case "checkout_process": require($base_path.'checkout_process.php'); 
									  	break;
				case "confirm":			require($base_path.'cart_confirm.php');
										break;						
				case "country_state":	require($base_path.'manage_country_state.php'); 	
									  	break;						
				case "currency_settings": require($base_path.'load_currency_settings.php'); 
									  	break;						
				case "delete":			require($CONFIG->pluginspath.'socialcommerce/actions/delete.php'); 
										break;	
				case "edit":			require($base_path.'edit.php'); 	
										break;
				case "edit_address":	require($base_path.'edit_address.php'); 	
										break;
				case "edit_category":	require($base_path.'add_category.php'); 
										break;
				case "friends":			require($base_path.'friends.php'); 	
										break;
				case "ipn":				makepayment_paypal();
										break;						
				case "more_order_item":	require($base_path.'more_order_item.php');
									  	break;						
				case "my_account":		require($base_path.'my_account.php');
									  	break;	
				case "order":			set_input('search_viewtype', 'list');
										require($base_path.'order.php');
										break;
				case "order_products":	if($page[2]) { set_input('guid',$page[2]); }
										require($base_path.'order_products.php'); 
										break;
				case "product_cate":	require($base_path.'product_category.php');
									  	break;
				case "read":			require($base_path.'product.php');
										break;
				case "search":			require(get_config('pluginspath').'socialcommerce/search.php'); 
										break;
				case "sold":			require($base_path.'sold.php');
									  	break;
				case "type":			require($base_path.'product_type.php');
									  	break;
				case "view_address":	require($base_path.'address_view.php'); 
									  	break;
				case "wishlist":		require($base_path.'wishlist.php');
										break;
				default:				echo "request for $identifier $page[1]"; 
										break;
			}
		/*****	If the URL is just 'socialcommerce/username', or just 'socialcommerce/', load index.php	*****/
		} else {
			require($base_path.'index.php'); 
			return true;
		}
		return false;
	}
	
	/**
	 * This hooks into the getIcon API and provides nice user image for users where possible.
	 *
	 * @param unknown_type $hook
	 * @param unknown_type $entity_type
	 * @param unknown_type $returnvalue
	 * @param unknown_type $params
	 * @return unknown
	 */
	 
	function socialcommerce_image_hook($hook, $entity_type, $returnvalue, $params) {
		if ((!$returnvalue) && ($hook == 'entity:icon:url') && ($params['entity'] instanceof ElggEntity)) {
			$entity = $params['entity'];
			$type = $entity->type;
			$viewtype = $params['viewtype'];
			$size = $params['size'];
			
			if ($icontime = $entity->icontime) {
				$icontime = "{$icontime}";
			} else {
				$icontime = "default";
			}
			
			$filehandler = new ElggFile();
			$filehandler->owner_guid = $entity->owner_guid;
			$filehandler->setFilename("socialcommerce/" . $entity->guid . $size . ".jpg");
			
			if ($filehandler->exists()) {
				$url = get_config('url')."storesimage/{$entity->guid}/$size/$icontime.jpg";
				return $url;
			}
		}
	}
	
	/**
	 * Handle stores Image.
	 * @param unknown_type $page
	 */
	 
	function socialcommerce_image_handler($page) {
		// The username should be the file we're getting
		if (isset($page[0])) {
			set_input('stores_guid',$page[0]);
		}
		if (isset($page[1])) {
			set_input('size',$page[1]);
		}
		// Include the standard profile index
		include( get_config('pluginspath').'socialcommerce/graphics/icon.php' );
	}
	
	/**
	 * Returns an overall product type from the mimetype
	 *
	 * @param string $mimetype The MIME type
	 * @return string The overall type
	 */
	 
	function get_general_product_type($mimetype) {
		
		switch($mimetype) {
			
			case "application/msword":	return "document";
										break;
			case "application/pdf":		return "document";
										break;
		}
		
		if (substr_count($mimetype,'text/'))
			return "document";
			
		if (substr_count($mimetype,'audio/'))
			return "audio";
			
		if (substr_count($mimetype,'image/'))
			return "image";
			
		if (substr_count($mimetype,'video/'))
			return "video";

		if (substr_count($mimetype,'opendocument'))
			return "document";	
			
		return "general";
		
	}
	
	/**
	 * Returns a list of producttypes to search specifically on
	 *
	 * @param int|array $owner_guid The GUID(s) of the owner(s) of the files 
	 * @param true|false $friends Whether we're looking at the owner or the owner's friends
	 * @return string The typecloud
	 */
	function get_storestype_cloud($owner_guid = "", $friends = false) {
		
		if($friends) {
			if ($friendslist = get_user_friends($user_guid, $subtype, 999999, 0)) {
				$friendguids = array();
				foreach($friendslist as $friend) {
					$friendguids[] = $friend->getGUID();
				}
			}
			$friendofguid = $owner_guid;
			$owner_guid = $friendguids;
		} else {
			$friendofguid = false;
		}
		return elgg_view("socialcommerce/typecloud", array('owner_guid' => $owner_guid, 'friend_guid' => $friendofguid, 'types' => get_tags(0,10,'simpletype','object','stores',$owner_guid)));
	}
	
	/**
	 * Populates the ->getUrl() method for file objects
	 *
	 * @param ElggEntity $entity File entity
	 * @return string File URL
	 */
	function stores_url( $entity ) {
		$title = $entity->title;
		$title = elgg_get_friendly_title($title);
		return get_config('url').'socialcommerce/'.$entity->getOwnerEntity()->username.'/read/'.$entity->getGUID().'/'.$title;
	}
	
	/**
	 * Populates the ->getUrl() method for file objects
	 *
	 * @param ElggEntity $entity File entity
	 * @return string File URL
	 */
	function category_url($entity) {
		$title = $entity->title;
		$title = elgg_get_friendly_title($title);
		return get_config('url').'socialcommerce/'.$entity->getOwnerEntity()->username.'/cateread/'.$entity->getGUID().'/'.$title;
	}
	
	function cart_url($entity) {
		$title = $entity->title;
		$title = elgg_get_friendly_title($title);
		return get_config('url').'socialcommerce/'.$entity->getOwnerEntity()->username.'/cart/'.$entity->getGUID().'/'.$title;
	}
	
	/**
	 * Populates the ->getUrl() method for file objects
	 *
	 * @param ElggEntity $entity File entity
	 * @return string File URL
	 */
	function addcartURL( $entity ) {
		$title = $entity->title;
		$title = elgg_get_friendly_title( $title );								//	@todo - I have no idea why $title is in here...
		return	get_config('url').'action/socialcommerce/add_to_cart/';
	}
	
	/**
	 * Update an item of metadata for stores.
	 *
	 * @param int $id
	 * @param string $name
	 * @param string $value
	 * @param string $value_type
	 * @param int $owner_guid
	 * @param int $access_id
	 */
	 
	function update_metadata_for_stores($id, $name, $value, $value_type, $owner_guid, $access_id) {
		global $CONFIG;

		$id = (int)$id;

		if(!$md = elgg_get_metadata_from_id($id)) { return false; }	
		
		// If memcached then we invalidate the cache for this entry
		static $metabyname_memcache;
		if((!$metabyname_memcache) && (is_memcache_available())) { $metabyname_memcache = new ElggMemcache('metabyname_memcache'); }
		if($metabyname_memcache) { $metabyname_memcache->delete("{$md->entity_guid}:{$md->name_id}"); }
		
		//$name = sanitise_string(trim($name));
		//$value = sanitise_string(trim($value));
		$value_type = detect_extender_valuetype($value, sanitise_string(trim($value_type)));
		
		$owner_guid = (int)$owner_guid;
		if ($owner_guid==0) $owner_guid = elgg_get_logged_in_user_guid();
		
		$access_id = (int)$access_id;
		
		$access = get_access_sql_suffix();
		
		// Support boolean types (as integers)
		if (is_bool($value)) {
			if ($value)
				$value = 1;
			else
				$value = 0;
		}
		
		// Add the metastring
		$value = add_metastring($value);
		if (!$value) return false;
		
		$name = add_metastring($name);
		if(!$name) { return false;	}
		
		// If ok then add it
		$result = update_data("UPDATE {$CONFIG->dbprefix}metadata set value_id='$value', value_type='$value_type', access_id=$access_id, owner_guid=$owner_guid where id=$id and name_id='$name'");
		if ($result!==false) {
			$obj = elgg_get_metadata_from_id($id);
			if (elgg_trigger_event('update', 'metadata', $obj)) {
				return true;
			} else {
				elgg_delete_metadata(array('metadata_id' => $id));
			}
		}
		return $result;
	}
	
	function get_stores_from_relationship($relationship,$relationship_guid, $metaname = "",$metavalue = "",$type = "", $subtype = "",$owner_guid = "", $metaorder_by = "", $order_by = "", $order = "ASC",$count=false){
		global $CONFIG;
		
		$relationship = sanitise_string($relationship);
		$relationship_guid = (int)$relationship_guid;
		$type = sanitise_string($type);
		$subtype = get_subtype_id($type, $subtype);
		$owner_guid = (int)$owner_guid;
		
		if($metaorder_by){
			$order_by = " CAST( v.string AS unsigned ) ".$order;
		}elseif ($order_by){
			$order_by = " e.".sanitise_string($order_by) . $order;
		}else {
			$order_by = " e.time_created desc";
		}
		
		$where = "";
		if ($relationship!="")
			$where = " AND r.relationship='$relationship' ";
		if ($relationship_guid)
			$where .= " AND r.guid_one='$relationship_guid' ";
		if ($type != "")
			$where .= " AND e.type='$type' ";
		if ($subtype)
			$where .= " AND e.subtype=$subtype ";
			
		if(is_array($owner_guid)){
			$where .= " AND e.owner_guid IN (" . implode(",",$owner_guid) . ")";
		}else{
			$where .= " AND e.owner_guid=$owner_guid ";
		}
		if($metaname){
			$nameid = get_metastring_id($metaname);
			if($nameid){
				$where .= " and m.name_id=".$nameid;
			}else{
				$where .= " and m.name_id=0";
			}
		}	
		if($metavalue || $metavalue == '0'){
			$valueid = get_metastring_id($metavalue);
			if($valueid){
				$where .= " and m.value_id=".$valueid;
			}else{
				$where .= " and m.value_id=0";
			}
		}
		
		$query = "SELECT SQL_CALC_FOUND_ROWS e.*, v.string as value FROM {$CONFIG->dbprefix}entity_relationships r JOIN {$CONFIG->dbprefix}entities e ON e.guid = r.guid_two JOIN {$CONFIG->dbprefix}metadata m ON e.guid = m.entity_guid JOIN {$CONFIG->dbprefix}metastrings v ON m.value_id = v.id WHERE (1 = 1) ".$where." AND e.enabled='yes' AND m.enabled='yes'  ORDER BY ".$order_by." ".$limit;			
		$sections = get_data($query);
		return $sections;
	}
	
	function get_sold_products($metavalue=null, $limit, $offset=0 ){
		global $CONFIG;
		$nameid = get_metastring_id('product_owner_guid');
		if($nameid){
			$where = " and m.name_id=".$nameid;
		}else{
			$where = " and m.name_id=0";
		}
		if($metavalue != null){
			$valueid = get_metastring_id($metavalue);
			if($valueid){
				$where .= " and m.value_id =".$valueid;
			}else{
				$where .= " and m.value_id=0";
			}
		}
		$m1_nameid = get_metastring_id('product_id');
		if($m1_nameid){
			$where .= " and m1.name_id=".$m1_nameid;
		}
		$where .= " and e.type='object'";
		$subtypeid = get_subtype_id('object','order_item');
		if($subtypeid){
			$where .= " and e.subtype=".$subtypeid;
		}else{
			$where .= " and e.subtype=-1";
		}
		
		$order = " order by e.time_created desc";	
		
		if($limit){
			$limit = " limit ".$offset.",".$limit;
		}else{
			$limit = "";
		}
		
		$query = "SELECT SQL_CALC_FOUND_ROWS DISTINCT v.string AS value, e.guid AS guid, e.owner_guid as owner_guid, e.container_guid as container_guid from {$CONFIG->dbprefix}metadata m JOIN {$CONFIG->dbprefix}entities e on e.guid = m.entity_guid JOIN {$CONFIG->dbprefix}metadata m1 ON e.guid = m1.entity_guid JOIN {$CONFIG->dbprefix}metastrings v on m1.value_id = v.id where (1 = 1) ".$where." and m.enabled='yes' GROUP BY v.string  ".$order." ".$limit;
		$sold_products = get_data($query);
		return $sold_products;
	}
	
	function get_purchased_orders($metaname=null,$metavalue=null,$type=null,$subtype=null,$where_spval=false,$where_spval_con=null,$metaorder=fale,$entityorder=null,$order='ASC',$limit=null,$offset=0,$count=false,$owner=0,$container=0,$id_not_in=null,$title=null,$where_con=""){
		global $CONFIG;
		if($metaname){
			$nameid = get_metastring_id($metaname);
			if($nameid){
				$where = " and m.name_id=".$nameid;
			}else{
				$where = " and m.name_id=0";
			}
		}
		if($metavalue != null){
			$metavalues = explode(',',$metavalue);
			foreach($metavalues as $metavalue){
				$valueid = get_metastring_id($metavalue);
				if($valueid <= 0)
					$valueid = 0;
				$metavalue_in .= !empty($metavalue_in) ? ",".$valueid : $valueid;
			}
			
			if($metavalue_in){
				$where .= " and m.value_id IN(".$metavalue_in.")";
			}else{
				$where .= " and m.value_id=0";
			}
		}
		if($type){
			$where .= " and e.type='".$type."'";
		}
		if($subtype){
			$subtypeid = get_subtype_id('object',$subtype);
			if($subtypeid){
				$where .= " and e.subtype=".$subtypeid;
			}else{
				$where .= " and e.subtype=-1";
			}
		}
		
		if(is_array($owner)){
			$where .= " AND e.owner_guid IN (" . implode(",",$owner) . ")";
		}else{
			if($owner > 0)
				$where .= " AND e.owner_guid=$owner ";
		}
		
		if($container > 0)
			$where .= " and e.container_guid=".$container;
			
		if(is_array($id_not_in)){
			$entity_guids = get_not_in_ids($id_not_in);
			if(!empty($entity_guids)){
				$where .= " and e.guid NOT IN(".$entity_guids.") ";
			}
		}	
		if($title){
			$where .= " and o.title='".$title."'";
		}
		if($where_spval){
			$current_date = strtotime(date("m/d/Y"));
			$where .= " and v.string {$where_spval_con} {$current_date}";
		}
		if($where_con){
			$where .= " {$where_con} ";
		}
		
		if($metaorder){
			$order = " order by  CAST( v.string AS unsigned ) ".$order;
		}elseif($entityorder){
			$order = " order by e.".$entityorder." ".$order;
		}else{
			$order = " order by e.time_created desc";
		}
		
		if($limit){
			$limit = " limit ".$offset.",".$limit;
		}else{
			$limit = "";
		}
		
		//$access = get_stores_access_sql_suffix();
		$query = "SELECT SQL_CALC_FOUND_ROWS e.guid AS guid, e.owner_guid as owner_guid, e.container_guid as container_guid, v.string as value from {$CONFIG->dbprefix}metadata m JOIN {$CONFIG->dbprefix}entities e on e.guid = m.entity_guid JOIN {$CONFIG->dbprefix}metastrings v on m.value_id = v.id JOIN {$CONFIG->dbprefix}objects_entity o on e.guid = o.guid where (1 = 1) ".$where." and m.enabled='yes' ".$order." ".$limit;
		$propositions = get_data($query);
		if($count){
			$count = get_data("SELECT FOUND_ROWS( ) AS count");
			return $count[0]->count;
		}
		return $propositions;
	}
	
	function get_stores_access_sql_suffix($table_prefix = ""){
		global $ENTITY_SHOW_HIDDEN_OVERRIDE;  
		
		$sql = "";
		
		if ($table_prefix)
				$table_prefix = sanitise_string($table_prefix) . ".";
		
			$access = get_access_list();
			
			$owner = elgg_get_logged_in_user_guid();
			if (!$owner) $owner = -1;
			
			global $is_admin;
			
			if (isset($is_admin) && $is_admin == true) {
				$sql = " (1 = 1) ";
			}

			if (empty($sql))
				$sql = " ({$table_prefix}e.access_id in {$access} or ({$table_prefix}e.access_id = 0 and {$table_prefix}e.owner_guid = $owner))";

		if (!$ENTITY_SHOW_HIDDEN_OVERRIDE)
			$sql .= " and {$table_prefix}e.enabled='yes'";
		
		return $sql;
	}
	
	function gettags(){
		$products = elgg_get_entities(array( 	
			'type' => 'object',
			'subtype' => 'stores',
			)); 			
			
		foreach ($products as $product){
			if(!empty($product->tags)){
				if(is_array($product->tags)){
					foreach ($product->tags as $tag)
						$tagarr[$tag] = $tag;
				}else{
					$tagarr[$tag] = $product->tags;
				}
			}
		}
		return elgg_view( 'socialcommerce/tagsmenu', array( 'tags'=>$tagarr ));
	}
	
	/*****	send email function	*****/

	 function stores_send_mail( $from, $to, $subject, $message, $headers = null) {
	 	
	 	if(is_object($from)){
	 		$from_name = $from->name;
	 		$from_email = $from->email;
	 	}else{
	 		$from_name = $from;
	 		$from_email = $from;
	 	}
	 	
	 	if(is_object($to)){
	 		$to_email = $to->email;
	 	}else{
	 		$to_email = $to;
	 	}
	 	
	 	if(!$headers){
		 	$headers = "From: \"$from_name\" <$from_email>\r\n"
				. "Content-Type: text/html; charset=iso-8859-1\r\n"
	    		. "MIME-Version: 1.0\r\n"
	    		. "Content-Transfer-Encoding: 8bit\r\n";
	 	}
       	return mail( $to_email, $subject, $message, $headers );
	 }
	 
	 function get_site_admin() {
	 	global $CONFIG;
		$access = get_access_sql_suffix('e');
	 	
		$row = get_data_row("SELECT e.* from {$CONFIG->dbprefix}users_entity u join {$CONFIG->dbprefix}entities e on e.guid=u.guid where u.admin='yes' and $access limit 1");
		if ($row) {
			return new ElggUser($row);
		}
		else {
			return false;
		}
	}

	/*****	Override the order_can_create function to return true for create order	****/
	
	function order_can_create($hook_name, $entity_type, $return_value, $parameters) {
		$entity = $parameters['entity'];
		$context = elgg_get_context();
		if ($context == 'add_order' && $entity->getSubtype() == "") {
			return true;
		}elseif ($context == 'add_order' && $entity->getSubtype() == "rating"){
			return true;
		}elseif ($context == 'add_order' && $entity->getSubtype() == "cart"){
			return true;
		}elseif ($context == 'add_order' && $entity->getSubtype() == "cart_item"){
			return true;
		}elseif ($context == 'add_order' && $entity->getSubtype() == "stores"){
			return true;
		}elseif ($context == 'add_order' && $entity->getSubtype() == "order"){
			return true;
		}elseif ($context == 'add_order' && $entity->getSubtype() == "order_item"){
			return true;
		}elseif ($context == 'add_order' && $entity->getSubtype() == "transaction"){
			return true;
		}elseif ($context == 'add_order'){
			return true;
		}elseif ($context == 'add_settings' && $entity->getSubtype() == "s_currency"){
			return true;
		}elseif ($context == 'related_products'){
			return true;
		}
		return null;
  	}
  	// Make sure the stores initialization function is called on initialization
		elgg_register_event_handler('init','system','socialcommerce_init');
		elgg_register_event_handler('init','system','sc_product_fields_setup', 10000); 	// Ensure this runs after other plugins
		
		elgg_register_event_handler('pagesetup','system','socialcommerce_pagesetup');
		
  	// Override permissions
		elgg_register_plugin_hook_handler('permissions_check','user','order_can_create');
		elgg_register_plugin_hook_handler('permissions_check','object','order_can_create');
		
	/*****	register actions	*****/
		$action_path = $CONFIG->pluginspath.'socialcommerce/actions/';
	
		elgg_register_action("socialcommerce/add", $action_path.'add.php');
		elgg_register_action("socialcommerce/edit", $action_path.'edit.php');
		elgg_register_action("socialcommerce/delete", $action_path.'delete.php');
		elgg_register_action("socialcommerce/icon", $action_path.'icon.php');
		elgg_register_action("socialcommerce/add_category", $action_path.'add_category.php');
		elgg_register_action("socialcommerce/edit_category", $action_path.'edit_category.php');
		elgg_register_action("socialcommerce/delete_category", $action_path.'delete_category.php');
		elgg_register_action("socialcommerce/remove_cart", $action_path.'remove_cart.php');
		elgg_register_action("socialcommerce/update_cart", $action_path.'update_cart.php');
		elgg_register_action("socialcommerce/add_address", $action_path.'add_address.php');
		elgg_register_action("socialcommerce/add_address_new", $action_path.'add_address_new.php');
		elgg_register_action("address/address", $action_path.'address/address.php');
		elgg_register_action("socialcommerce/edit_address", $action_path.'edit_address.php');
		elgg_register_action("socialcommerce/delete_address", $action_path.'delete_address.php');
		elgg_register_action("socialcommerce/makepayment", $action_path.'makepayment.php');
		elgg_register_action("socialcommerce/add_order", $action_path.'add_order.php');
		elgg_register_action("socialcommerce/change_order_status", $action_path.'change_order_status.php');
		elgg_register_action("socialcommerce/add_wishlist", $action_path.'add_wishlist.php');	// remove once the new add wishlist form is working
		elgg_register_action("products/add_wishlist", $action_path.'products/add_wishlist.php');
		elgg_register_action("socialcommerce/remove_wishlist", $action_path.'remove_wishlist.php');
		elgg_register_action("socialcommerce/download", $action_path.'download.php');
		elgg_register_action("socialcommerce/contry_tax", $action_path.'contry_tax.php');
		elgg_register_action("socialcommerce/addcommon_tax", $action_path.'addcommon_tax.php');
		elgg_register_action("socialcommerce/addcountry_tax", $action_path.'addcountry_tax.php');
		elgg_register_action('socialcommerce/manage_socialcommerce', $action_path.'manage_socialcommerce.php');
		elgg_register_action('socialcommerce/settings/save', $action_path.'socialcommerce/settings/save.php' );
		elgg_register_action('socialcommerce/add_to_cart', $action_path.'socialcommerce/add_to_cart.php' );
?>
