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
class Plugin_Shop_Discounts extends Plugin
{
	public $version = '1.0.0';
	public $name = array(
		'en' => 'NitroCart Discounts',
	);
	public $description = array(
		'en' => 'Access user and cart information for almost any part of SHOP.',
	);


	/**
	 * Returns a PluginDoc array that PyroCMS uses
	 * to build the reference in the admin panel
	 *
	 * All options are listed here but refer
	 * to the Asset plugin for a larger example
	 *
	 * @return array
	 */
	public function _self_doc()
	{
		$info = array(
			'product' => array(
				'description' => array(
					'en' => 'shop_discounts:product.'
				),
				'single' => false,
				'double' => true,
				'variables' => 'id',
				'attributes' => array(
					'id' => array(
						'type' => 'int',
						'required' => true,
					),

				),
			),

		);

		return $info;
	}


	/**
	 * Get discounts by product
	 */
	function product()
	{
		if($this->db->table_exists('shop_discounts'))
		{
			$x = explode(',', $this->attribute('x', '') );
			$id = intval( $this->attribute('id','0') );
			$this->load->model('shop_discounts/discounts_m');

			$gid =($this->current_user)? intval($this->current_user->group_id): -1;

			if(in_array('BYUSER', $x) && $gid > -1) $this->discounts_m->where('group_id',$gid);
			if(in_array('QTY_DESC', $x)) $this->discounts_m->order_by('min_qty','desc');
			if(in_array('QTY_ASC', $x)) $this->discounts_m->order_by('min_qty','asc');
			return (array) $this->discounts_m->getDiscountByProduct( $id );
		}
		return ''; //return blank
	}

}
/* End of file plugin.php */