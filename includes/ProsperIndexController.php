<?php
/**
 * ProsperIndex Controller
 *
 * @package 
 * @subpackage 
 */
class ProsperInsertIndexController
{	
    public function __construct()
    {		
		require_once(PROSPERINSERT_MODEL . '/Activate.php');
		$prosperActivate = new Model_Insert_Activate();
		
		register_activation_hook(PROSPERINSERT_PATH . PROSPERINSERT_FILE, array($prosperActivate, 'prosperActivate'));
		register_deactivation_hook(PROSPERINSERT_PATH . PROSPERINSERT_FILE, array($prosperActivate, 'prosperDeactivate'));

		add_action('admin_init', array($prosperActivate, 'prosperActivateRedirect'));
		add_action('admin_init', array($prosperActivate, 'prosperCustomAdd'));
		add_action('init', array($prosperActivate, 'doOutputBuffer'));	
		add_action('init', array($prosperActivate, 'init'));
	}
}
 
$prosperInsertIndex = new ProsperInsertIndexController;