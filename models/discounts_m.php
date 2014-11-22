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
class Discounts_m extends MY_Model {


	public $_table = 'shop_discounts';
	protected $i_exist = FALSE;
	public function __construct() 
	{	
		parent::__construct();
	}

	public function getDiscountByUser( $product_id, $group_id, $qty=1 )
	{
		$result =  $this->db->where('product_id',$product_id)->where('group_id',$group_id)->where('min_qty <=', $qty)->order_by('min_qty','desc')->get($this->_table)->row();
		//var_dump($result);die;
		if($result)
		{	
			return $result->value;
		}
		return 0;
	}

	public function getDiscountByProduct( $product_id )
	{	
		$result = $this->where('product_id',$product_id)->get_all();

		return $result;
	}
}