<?php
/**
 * ProsperAdmin Controller
 *
 * @package 
 * @subpackage 
 */
class ProsperInsertAdminController
{
    /**
     * the class constructor
     *
     * @package 
     * @subpackage 
     *
     */
    public function __construct()
    {
		add_action('admin_menu', array($this, 'registerSettingsPage' ), 5);
		add_action( 'network_admin_menu', array( $this, 'registerNetworkSettingsPage' ) );
		
		require_once(PROSPERINSERT_MODEL . '/Admin.php');
		$prosperAdmin = new Model_Insert_Admin();
		
		add_action( 'admin_init', array( $prosperAdmin, 'optionsInit' ) );
		add_action( 'admin_enqueue_scripts', array( $prosperAdmin, 'prosperAdminCss' ));	
		add_action( 'init', array( $prosperAdmin, 'init' ), 20 );
		add_filter( 'plugin_action_links', array( $prosperAdmin, 'addActionLink' ), 10, 2 );		
	}
		
	/**
	 * Register the menu item and its sub menu's.
	 *
	 * @global array $submenu used to change the label on the first item.
	 */
	public function registerSettingsPage() 
	{
		add_menu_page(__('ProsperInsert Settings', 'prosperent-suite'), __( 'ProsperInsert', 'prosperent-suite' ), 'manage_options', 'prosperinsert_general', array( $this, 'generalPage' ), PROSPERINSERT_IMG . '/prosperentWhite.png' );	
		add_submenu_page('prosperinsert_general', __( 'ProsperInsert', 'prosperent-suite' ), __( 'Content Insert', 'prosperent-suite' ), 'manage_options', 'prosper_insert', array( $this, 'inserterPage' ) );
		add_submenu_page('prosperinsert_general', __( 'Advanced Options', 'prosperent-suite' ), __( 'Advanced', 'prosperent-suite' ), 'manage_options', 'prosperinsert_advanced', array( $this, 'advancedPage' ));
		add_submenu_page('prosperinsert_general', __( 'Themes', 'prosperent-suite' ), __( 'Themes', 'prosperent-suite' ), 'manage_options', 'prosperinsert_themes', array( $this, 'themesCssPage' ) );
		
		global $submenu;
		if (isset($submenu['prosperinsert_general']))
			$submenu['prosperinsert_general'][0][0] = __('General Settings', 'prosperent-suite' );
		
	}	
		
	/**
	 * Register the settings page for the Network settings.
	 */
	function registerNetworkSettingsPage() 
	{
		add_menu_page( __('ProsperInsert Settings', 'prosperent-suite'), __( 'Prosperent', 'prosperent-suite' ), 'delete_users', 'prosperinsert_general', array( $this, 'networkConfigPage' ), PROSPERINSERT_IMG . '/prosperentWhite.png' );
	}
		
	/**
	 * Loads the form for the network configuration page.
	 */
	function networkConfigPage() 
	{
		require_once(PROSPERINSERT_VIEW . '/prosperadmin/network-phtml.php' );
	}
		
	/**
	 * Loads the form for the general settings page.
	 */
	public function generalPage() 
	{
		if ( isset( $_GET['page'] ) && 'prosperinsert_general' == $_GET['page'] )
			require_once( PROSPERINSERT_VIEW . '/prosperadmin/general-phtml.php' );
	}
	
	/**
	 * Loads the form for the inserter page.
	 */
	public function inserterPage() 
	{	
		if ( isset( $_GET['page'] ) && 'prosper_insert' == $_GET['page'] )
			require_once( PROSPERINSERT_VIEW . '/prosperadmin/inserter-phtml.php' );
	}
	
	/**
	 * Loads the form for the product search page.
	 */
	public function advancedPage() 
	{	
		if ( isset( $_GET['page'] ) && 'prosperinsert_advanced' == $_GET['page'] )
			require_once( PROSPERINSERT_VIEW . '/prosperadmin/advanced-phtml.php' );
	}		
	
	/**
	 * Loads the form for the product search page.
	 */
	public function themesCssPage() 
	{	
		if ( isset( $_GET['page'] ) && 'prosperinsert_themes' == $_GET['page'] )
			require_once( PROSPERINSERT_VIEW . '/prosperadmin/themes-phtml.php' );
	}	
}
 
$prosperInsertAdmin = new ProsperInsertAdminController;