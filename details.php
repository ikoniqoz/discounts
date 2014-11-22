<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');
/**
 * SHOP			A full featured shopping cart system for PyroCMS
 *
 * @author		Salvatore Bordonaro
 * @version		1.0.0.051
 * @website		http://www.inspiredgroup.com.au/
 * @system		PyroCMS 2.2.x
 *
 */
class Module_Shop_Discounts extends Module
{

	/**
	 * New dev version uses YMD as the final decimal format.
	 * Only for dev builds
	 *
	 * @var string
	 */
	public $version = '2.2.1';

	public $mod_details = array(
			      'name'=> 'Discounts', //Label of the module
			      'namespace'=>'shop_discounts',
			      'product-tab'=> TRUE, //This is to tell the core that we want a tab
			      'prod_tab_order'=> 1, //This is to tell the core that we want a tab
			      'cart'=> TRUE, //this is to be hooked up with the core cart process
			      'has_admin'=> FALSE,
	);

	//List of tables used
	protected $module_tables = array(

			'shop_discounts' 	=> array(
				'id' 			=> array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'primary' => TRUE),
				'product_id' 	=> array('type' => 'INT', 'constraint' => '11', 'null' => TRUE, 'unsigned' => TRUE),
				'min_qty' 		=> array('type' => 'INT', 'constraint' => '4', 'null' => TRUE, 'unsigned' => TRUE, 'default' => 0),
				'group_id' 		=> array('type' => 'INT', 'constraint' => '11', 'null' => TRUE, 'unsigned' => TRUE),
				'modifier' 		=> array('type' => 'INT', 'constraint' => '11', 'null' => TRUE, 'unsigned' => TRUE),
				'value' 		=> array('type' => 'INT', 'constraint' => '11', 'null' => TRUE, 'unsigned' => TRUE),
			),
	);


	public function __construct()
	{
        $this->load->library('shop/nitrocore_library');   
		$this->ci = get_instance();
	}


	/**
	 * info()
	 * @description: Creates 2 arrays to diplay for the module naviagtion
	 *			   One array is returned based on the user selection in the settings
	 *
	 */
	public function info()
	{


		$info =  array(
			'name' => array(
				'en' => 'NitroCart Discounts',
			),
			'description' => array(
				'en' => 'NitroCart <i>A full featured shopping cart system for PyroCMS!</i>',
			),
			'skip_xss' => TRUE,
			'frontend' => TRUE,
			'backend' => TRUE,
			'menu' => FALSE,
			'author' => 'Salvatore Bordonaro',
            'roles' => array(
            	'admin_manage',
	            'admin_discounts',
            ),
			'sections' => array()
		);


		return $info;

	}


	/*
	 * The menu is handled by the main SHOP module
	 */
    public function admin_menu(&$menu)
    {

	}



	public function install()
	{

        // Support for sub 2.2.0 menus
        if ( CMS_VERSION < '2.2.0' ) {
            return FALSE;
        }

		if(!$this->isRequiredInstalled())
		{
			return FALSE;
		}

		$tables_installed = $this->install_tables( $this->module_tables );


		if( $tables_installed  )
		{
			//register this module with SHOP
			Events::trigger("SHOPEVT_RegisterModule", $this->mod_details);


			return TRUE;
		}

		return FALSE;

	}


	/*
	 */
	public function uninstall()
	{

		foreach($this->module_tables as $table_name => $table_data)
		{
			$this->dbforge->drop_table($table_name);
		}


		//Remove categories from the core module DB
		Events::trigger("SHOPEVT_DeRegisterModule", $this->mod_details);

		return TRUE;

	}


	/*
	 */
	public function upgrade($old_version)
	{

		switch ($old_version)
		{
			case '1.0.1':
				break;
			default:
				break;

		}


		return TRUE;

	}


	public function help()
	{
		return "No documentation has been added for this module.<br />Contact the module developer for assistance.";
	}



	private function init_templates()
	{
		 return TRUE;
	}


	private function init_settings()
	{
		return TRUE;
	}

	public function isRequiredInstalled()
	{

		$this->ci->load->model('module/module_m');
		$module_core = $this->ci->module_m->get_by('slug', 'shop' );

    	if( $module_core && $module_core->installed == TRUE)
    	{
    		$module = $this->ci->module_m->get_by('slug', 'shop' );
    		if( $module && $module->installed == TRUE)
    		{
				//we can now install this shop module
				return TRUE;
			}
    	}

    	return FALSE;
	}

}
/* End of file details.php */