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
class Discounts extends Admin_Controller
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
	 * removes a image from a product (ref) only
	 */
	public function add($product_id)
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

			if($input['value'] <= 0)
			{
				if($disc = $this->discounts_admin_m->create($input))
				{
					$disc['status'] = 'success';
				}
				else
				{
					$disc['message'] = "Failed to add. You may have already assigned a discount to this UserGroup";
				}
			}
			else
			{
				$disc['message'] = "You can not add a ZERO valued discount.";
			}
			

			$this->sendAjaxReturnObject($disc);

		}
	}


	public function addpop($product_id)
	{

		$this->load->model('shop_discounts/discounts_admin_m');
		$this->load->library('users/ion_auth');
        $this->load->model('groups/group_m');

  		$the_list = (array)$this->group_m->get_all() ;

		$pyroUserGroups = array_for_select( $the_list ,'id', 'description');

		$this->template
				->set_layout(FALSE)
				->set('jsexec', "$('.tooltip-s').tipsy()")
				->set('pyroUserGroups',$pyroUserGroups)
				->build('admin/products/partials/addpop');

	}

	public function remove($id=0)
	{

		$return_array = $this->getAjaxReturnObject();

		$this->load->model('shop_discounts/discounts_admin_m');

		if($this->discounts_admin_m->delete($id))
		{
			$return_array['status'] = 'success';
		}


		$this->sendAjaxReturnObject($return_array);
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