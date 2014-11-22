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
class Discounts_admin_m extends MY_Model {


	public $_table = 'shop_discounts';


	public function __construct()
	{
		parent::__construct();
	}

	public function getByProduct($product)
	{
		return $this->where('product_id',$product->id)->get_all();
	}

	public function create($inputs)
	{

		$result =  $this->where('product_id',$inputs['product_id'])->where('group_id',$inputs['group_id'])->where('min_qty',$inputs['min_qty'])->get_all();

		if(count($result) >= 1)
		{
			return FALSE;
		}

		$to_insert = array(
				'product_id' 	=> (int)$inputs['product_id'],
				'min_qty' 		=> $inputs['min_qty'],
				'group_id' 		=> (int)$inputs['group_id'],
				'modifier' 		=> 0,
				'value' 		=> (int) $inputs['value'],
		);

		$this->insert($to_insert); //returns id

		$to_insert['id']  = $to_insert['disc_id'] = $this->db->insert_id();

		return $to_insert;
	}


	public function edit($inputs)
	{

		$discount_id = (int)$inputs['disc_id'];
		$product_id = (int)$inputs['product_id'];


		$to_update = array(
				'min_qty' 		=> $inputs['min_qty'],
				'group_id' 		=> (int)$inputs['group_id'],
				'value' 		=> (int) $inputs['value'],
		);

		$this->update($discount_id, $to_update); //returns id

		$a =  $this->get($discount_id);
		$a->disc_id = $discount_id;

		return (array) $a;

	}

	public function duplicate( $or_id , $new_id )
	{

		//fetch all rows where prod id = $or_id
		$original_product_discounts = $this->where('product_id',$or_id)->get_all();

		foreach($original_product_discounts AS $discount)
		{
			//create the input
			$to_insert = array(
					'product_id' 	=> $new_id ,
					'min_qty' 		=> $discount->min_qty,
					'group_id' 		=> $discount->group_id,
					'modifier' 		=> $discount->modifier,
					'value' 		=> $discount->value,
			);

			//Add record
			$this->insert($to_insert); //returns id

		}

		return TRUE;
	}

	public function delete_by_product( $product_id )
	{

		$items_to_delete = $this->where('product_id',$product_id)->get_all();

		foreach($items_to_delete AS $discount)
		{
			$this->delete($discount->id);
		}

		return TRUE;
	}


}