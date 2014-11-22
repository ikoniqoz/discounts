<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');
/**
 * SHOP			A full featured shopping cart system for PyroCMS
 *
 * @author		Salvatore Bordonaro
 * @version		1.0.0.051
 * @website		http://www.inspiredgroup.com.au/
 * @system		PyroCMS 2.1.x
 *
 */
class Events_Shop_Discounts
{

	protected $ci;

	public $mod_details = array(
			      'name'=> 'Discounts', //Label of the module
			      'namespace'=>'shop_discounts', 
			      'product-tab'=> TRUE, //This is to tell the core that we want a tab
			      'prod_tab_order'=> 11, //This is to tell the core that we want a tab
			      'cart'=> TRUE, //this is to be hooked up with the core cart process
			      'has_admin'=> FALSE, 
	);

	/**
	 * Get the CI instance into this object
	 * 
	 * @param unknown_type $var
	 */
	public function __get($var) 
	{
		if (isset(get_instance()->$var)) 
		{
			return get_instance()->$var;
		}
	}	

	// Put code here for everywhere
	public function __construct() 
	{


		//New events to replace all of the above -
		Events::register('SHOPEVT_AdminProductGet', array($this, 'shopevt_admin_product_get'));	
		Events::register('SHOPEVT_AdminProductSave', array($this, 'shopevt_admin_product_save'));	
		Events::register('SHOPEVT_AdminProductDelete', array($this, 'shopevt_admin_product_delete'));
		Events::register('SHOPEVT_AdminProductDuplicate', array($this, 'shopevt_admin_product_duplicate'));

		//cart price updating
		Events::register('SHOPEVT_BeforeCartUpdate', array($this, 'shopevt_BeforeCartUpdate'));
		Events::register('SHOPEVT_BeforeCartItemAdded', array($this, 'shopevt_BeforeCartItemAdded'));



		//Store Front event
		Events::register('SHOPEVT_GetVariances', array($this, 'shopevt_get_variances'));

	}

	/**
	 * This function/Event will be called just before the item is added to the cart
	 *
	 * This event will calculate and discount the item approprietly based on the 
	 * total qty (in cart AND requested to add to cart) and update accordingly.
	 *
	 */
	public function shopevt_BeforeCartItemAdded($data)
	{

		//do not continue as discounts are ONLY by logged in users
		// who may have discounts applied
		if(!$this->current_user) return; 


		$this->load->model('shop/products_variances_m');

		$variance = $this->products_variances_m->get($data->product['variance']);

		if($variance->discountable)
		{

			$this->load->model('shop_discounts/discounts_m');

			$dic_amount = $this->discounts_m->getDiscountByUser( $variance->product_id , $this->current_user->group_id , $data->product['new_qty'] );


			if($dic_amount)
			{

				// the value in DB is whole (50) which is 50% off.
				// we need to convert it to a percentage
				// for this;
				//
				//	value / 100 = Percent
				$percentage_value = $dic_amount / 100;


				// To get the discount amount, multiple the value BY the percentage
				//so; 10 x 0.5 = 5
				$disc = $variance->price  *  $percentage_value;


				// Assign the new discounted price
				$data->product['price'] = $variance->price - $disc;


				// Assign the amount discounted
				$data->product['discount'] = $disc;


				// Assign the reason for discount
				$data->product['discount_message'] = $this->get_discMessage($dic_amount, $variance->price); //'User eligable for ' . $dic_amount . '% discount';



				// The original price does not need to be set. It is already set in the array

			}

		}
		

	}
	/**
	 *	
	 *	object(stdClass)[41]
	 *	  public 'update_data' => 
	 *	    array (size=1)
	 *	      0 => 
	 *	        array (size=4)
	 *	          'rowid' => string 'd3d9446802a44259755d38e6d163e820' (length=32)
	 *	          'id' => string '10' (length=2)
	 *	          'qty' => string '2' (length=1)
	 *	          'new_qty' => string '2' (length=1)
	 *
	 */
	public function shopevt_BeforeCartUpdate($data)
	{
		if(!$this->current_user) return; //do not continue as discounts are ONLY by logged in users

		$this->load->model('shop/products_variances_m');
		$this->load->model('shop_discounts/discounts_m');

		foreach($data->update_data as $key => $row_item)
		{
			$variance = $this->products_variances_m->get( $row_item['id'] );

			if($variance->discountable)
			{
				$dic_amount = $this->discounts_m->getDiscountByUser( $variance->product_id , $this->current_user->group_id , $row_item['qty'] );

				if($dic_amount)
				{
					$percentage_value = $dic_amount / 100;
					$disc = $variance->price  *  $percentage_value;

					//must update by key on the object for pass by reference
					$data->update_data[$key]['price'] = $variance->price - $disc;
					$data->update_data[$key]['discount'] = $disc;
					$data->update_data[$key]['discount_message'] = $this->get_discMessage($dic_amount, $variance->price); //'User eligable for ' . $dic_amount . '% discount';
				}
				else
				{
					//none applied so we use the standard rate
					$data->update_data[$key]['price'] = $variance->price;
					$data->update_data[$key]['discount'] = 0;
					$data->update_data[$key]['discount_message'] = '';					
				}
			}
		}
	}

	private function get_discMessage($dic_amount, $orig_price=0, $min_qty = 0)
	{
		return 'User eligable for ' . $dic_amount . '% discount, Original price: $' . $orig_price; //' min purchase is ' . $min_qty;
	}

	/**
	 * When a list of product variances are collected, the discounts module (this)
	 * willn eed to adjust the price values for display
	 */
	public function shopevt_get_variances($in_object)
	{

		// ONLY Logged in users and if applyDisc is set 
		// Do not continue as discounts are ONLY available for logged in users
		if( ($in_object->applyDisc==FALSE) || (!$this->current_user) ) return;


		// Load the discount Model
		$this->load->model('shop_discounts/discounts_m');


		// Iterate over the variations until we have calcul;ated all the values
		foreach( $in_object->returnarray as $key =>$variant )
		{
			//var_dump($variant);
			if($variant['variant_discountable'] == 1)
			{
				//we will pass QTY as 1, as at this point we do not know the QTY
				$disc_percent = $this->discounts_m->getDiscountByUser( $in_object->product_id, $this->current_user->group_id, 1 );

				//same as :: if the discount is > 0
				if($disc_percent)
				{
					//fix the percent value from 20 to 0.2
					$disc_percent = $disc_percent / 100;
					$disc = $variant['variant_price'] * $disc_percent;
					$new_price = $variant['variant_price'] - $disc;
					$variant['variant_price'] = $new_price;
					$in_object->returnarray[$key] = $variant;
				}
			}
		}

	}



	public function shopevt_admin_product_delete($deleted_product_id) 
	{
		$this->load->model('shop_discounts/discounts_admin_m');

		$this->discounts_admin_m->delete_by_product( $deleted_product_id );

	}

	public function shopevt_admin_product_duplicate($duplicateData = array()) 
	{
		$or_id  = $duplicateData['OriginalProduct'];
		$new_id = $duplicateData['NewProduct'];

		$this->load->model('shop_discounts/discounts_admin_m');
		$this->discounts_admin_m->duplicate( $or_id ,$new_id );

	}


	/**
	 * This will be called when the admin product data has been requested.
	 * It will inform all other modules to fetch any data that may be associated
	 * The ID of the product is passed (always by ID and Never by SLUG)
	 */
	public function shopevt_admin_product_get($product) 
	{
		// Send data back
		$product->module_tabs[] = (object) $this->mod_details;


		$this->load->model('shop_discounts/discounts_admin_m','discounts_admin_m');
		$this->load->library('users/ion_auth');
        $this->load->model('groups/group_m');

  		$the_list = (array)$this->group_m->get_all() ;

		$discounts = $this->discounts_admin_m->getByProduct($product);
		$pyroUserGroups = array_for_select( $the_list ,'id', 'description');

		$product->modules['shop_discounts']=array();
		$product->modules['shop_discounts']['groups'] = $pyroUserGroups; //select box of pyro user groups
		$product->modules['shop_discounts']['discounts'] = $discounts; //array of discounts in DB
	}


}
/* End of file events.php */