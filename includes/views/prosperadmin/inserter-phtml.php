<?php
require_once(PROSPERINSERT_MODEL . '/Admin.php');
$prosperAdmin = new Model_Insert_Admin();

$prosperAdmin->adminHeader( __( 'Content Insert Settings', 'prosperent-suite' ), true, 'prosperent_compare_options', 'prosper_autoComparer' );

echo '<p class="prosper_settingDesc">' . __( 'ProsperInsert is a great tool for anyone looking to promote a product, and makes it very easy to do so.', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Insert Products into All Posts/Pages', 'prosperent-suite' ) . '</h2>';
echo '<p class="prosper_settingDesc" style="border:none;">' . __( 'This uses the Page/Post titles to create a ProsperInsert above or below the content for all posts/pages.<br>You can also choose words to exclude from page titles.<br>For example, if you use review in the titles you can exclude it from the ProsperInsert query by inserting that below.', 'prosperent-suite' ) . '</p>';								

echo $prosperAdmin->checkbox( 'prosper_inserter_posts', __( 'Add ProsperInsert to <strong>All</strong> Posts?', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'prosper_inserter_pages', __( 'Add ProsperInsert to <strong>All</strong> Pages?', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->radio( 'prosper_inserter', array('top'=> 'Above', 'bottom'=> 'Below'), __( 'Insert <strong>Above</strong> or <strong>Below</strong> content?', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->radio( 'prosper_insertView', array('grid'=> 'Grid', 'list'=> 'List'), __( 'Which view do you want to use?', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

$options = get_option('prosper_autoComparer');

if($options['prosper_insertView'] == 'grid' || !isset($options['prosper_insertView']))
{
	echo $prosperAdmin->textinput( 'prosper_insertGridImage', __( 'Enter <strong>Grid</strong> Image Width', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Only changes the size for <strong>grid</strong> content inserter product images.</span></a>', 'prosper_textinputsmall');
	echo '<p class="prosper_desc">' . __( "Defaults to 200.", 'prosperent-suite' ) . '</p>';
}
	
echo $prosperAdmin->textinput( 'PI_Limit', __( 'Number of Products to Insert', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>ProsperInsert of Page/Post Limit</span></a>', 'prosper_textinputsmall' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'prosper_inserter_negTitles', __( 'Words to exclude from Titles', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span><strong>Seperate by commas.</strong></span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

$prosperAdmin->adminFooter();