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
class Product extends Admin_Controller
{

	protected $section = 'products';
	private $data;


	public function __construct()
	{
		parent::__construct();
		role_or_die('shop_discounts', 'admin_discounts');
	}

	public function index(){}



	/**
	 * 
	 */
	public function edit($product_id,$discount_id)
	{

		$this->load->model('shop_discounts/discounts_admin_m');
		$this->load->library('users/ion_auth');
        $this->load->model('groups/group_m');

  		$the_list = (array)$this->group_m->get_all() ;

		$pyroUserGroups = array_for_select( $the_list ,'id', 'description');

		//get the discount record
		$discount_record = $this->discounts_admin_m->get($discount_id);

		$percent_list = array();
		for($i=0;$i <100; $i++)
			$percent_list[] = $i;

		$this->template
				->set_layout(FALSE)
				->set('jsexec', "$('.tooltip-s').tipsy()")
				->set('discount',$discount_record)
				->set('percent_list',$percent_list)
				->set('pyroUserGroups',$pyroUserGroups)
				->build('admin/products/partials/edit');

	}

	public function save($product_id)
	{

		$return_array = $this->getAjaxReturnObject();

		if($input = $this->input->post())
		{

			// TODO: This whole sections needs validation
			if(!(is_numeric($input['min_qty'])))
			{
				$input['min_qty'] = 0;
			}

			$this->load->model('shop_discounts/discounts_admin_m');


			if($disc = $this->discounts_admin_m->edit($input))
			{
				$disc['status'] = 'success';
			}
			else
			{
				$disc['message'] = "Failed to add. You may have already assigned a discount to this UserGroup";
			}


			$this->sendAjaxReturnObject($disc);

		}
	}




	private function getAjaxReturnObject()
	{
		$ret_array = array();
		$ret_array['status'] = 'error';
		$ret_array['message'] = '';
		return $ret_array;
	}

	private function sendAjaxReturnObject($array_object)
	{
		echo json_encode($array_object);die;
	}

}