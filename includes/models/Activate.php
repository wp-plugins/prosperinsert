<?php
require_once(PROSPERINSERT_MODEL . '/Base.php');
/**
 * Base Abstract Model
 *
 * @package Model
 */
class Model_Insert_Activate extends Model_Insert_Base
{
	protected $_options;
	
	public function prosperActivate()
	{
		$this->_options = $this->getOptions();
		
		$this->prosperDefaultOptions();		
		$this->prosperOptionActivateAdd();
	}
	
	public function prosperDeactivate()
	{		

	}
	
	public function prosperOptionActivateAdd() 
	{
		add_option('prosperInsertActivationRedirect', true);
	}

	public function prosperActivateRedirect() 
	{
		if (get_option('prosperInsertActivationRedirect', false)) 
		{
			delete_option('prosperInsertActivationRedirect');
			if(!isset($_GET['activate-multi']))
			{
				wp_redirect( admin_url( 'admin.php?page=prosperinsert_general' ) );
			}
		}
	}
	
	public function prosperDefaultOptions()
	{
		if (!is_array(get_option('prosperSuite')))
		{
			$opt = array(
				'Target' => 1
			);	
			update_option('prosperSuite', $opt);
		}

		if (!is_array(get_option('prosper_autoComparer')))
		{
			$opt = array(
				'Enable_AC'    => 1,
				'Link_to_Merc' => 1,
				'PI_Limit'	   => 1
			);				
			update_option( 'prosper_autoComparer', $opt );
		}
		
		if (!is_array(get_option('prosper_advanced')))
		{
			$opt = array(
				'Title_Structure' => 0,
				'Image_Masking'	  => 0,
				'URL_Masking'	  => 0,
				'Base_URL'		  => 'products'
			);			
			update_option( 'prosper_advanced', $opt );
		}
		
		if (!is_array(get_option('prosper_themes')))
		{
			$opt = array(
				'Set_Theme' => 'Default'
			);			
			update_option( 'prosper_themes', $opt );
		}
	}
}
