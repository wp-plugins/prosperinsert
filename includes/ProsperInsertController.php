<?php
/**
 * ProsperInsert Controller
 *
 * @package 
 * @subpackage 
 */
class ProsperInserterController
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
		require_once(PROSPERINSERT_MODEL . '/Inserter.php');
		$prosperInserter = new Model_Insert_Inserter();

		if(is_admin())
		{
			add_action('admin_print_footer_scripts', array($prosperInserter, 'qTagsInsert'));
		}
		else
		{ 
			add_shortcode('compare', array($prosperInserter, 'inserterShortcode'));
		}
		
		if ($prosperInserter->_options['prosper_inserter_posts'] || $prosperInserter->_options['prosper_inserter_pages'])
		{
			add_filter('the_content', array($prosperInserter, 'contentInserter'), 2);
		}
    }
}
 
$prosperInserter = new ProsperInserterController;