<?php
/**
 * Base Abstract Model
 *
 * @package Model
 */
abstract class Model_Insert_Base
{
	protected $_options;
	
	protected $_version;
	
	public $widget;
	
	private $_endPoints = array(
		'fetchMerchant'	   => 'http://api.prosperent.com/api/merchant?',
		'fetchProducts'	   => 'http://api.prosperent.com/api/search?',
		'fetchUkProducts'  => 'http://api.prosperent.com/api/uk/search?',
		'fetchCaProducts'  => 'http://api.prosperent.com/api/ca/search?',
		'fetchCoupons'	   => 'http://api.prosperent.com/api/coupon/search?',
		'fetchLocal'	   => 'http://api.prosperent.com/api/local/search?',
		'fetchCelebrities' => 'http://api.prosperent.com/api/celebrity?',
		'fetchTrends'	   => 'http://api.prosperent.com/api/trends?'
	);
	
	public function init()
	{
		$this->_options = $this->getOptions();
		$this->_version = $this->getVersion();
	
		if ($this->_options['Api_Key'] && strlen($this->_options['Api_Key']) == 32)
		{ 				
			add_action('wp_enqueue_scripts', array($this, 'prosperStylesheets'));	
			require_once(PROSPERINSERT_INCLUDE . '/ProsperInsertController.php');
		}
		else
		{
			add_action( 'admin_notices', array($this, 'prosperBadSettings' ));
		}	
	}
	
	public function getVersion()
	{
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			
		$pluginFolder = get_plugins('/' . PROSPERINSERT_FOLDER);				
		return $pluginFolder[PROSPERINSERT_FILE]['Version'];
	}

	/**
	 * Retrieve all the options
	 *
	 * @return array of options
	 */
	public function getOptions($option = null)
	{
		if (!isset($this->_options))
		{
			$this->_options = array();
			foreach ($this->getProsperOptionsArray() as $opt)
			{ 
				$this->_options = array_merge($this->_options, (array) get_option($opt));
			}
		}

		return $this->_options;
	}
		
	public function prosperStylesheets()
	{
		$css = PROSPERINSERT_CSS . '/products.min.css';

		// Product Search CSS for results and search
		if ($this->_options['Set_Theme'] != 'Default')
		{
			$dir = PROSPERINSERT_THEME . '/' . $this->_options['Set_Theme'];
			if($newTheme = glob($dir . "/*.css"))
			{			
				$css = str_replace(' ', '%20', (content_url(preg_replace('/.*\/wp-content/i', '', $newTheme[0]))));
			}
		}

		wp_register_style( 'prospere_main_style', $css, array(), $this->_version );
		wp_enqueue_style( 'prospere_main_style' );
	}	
	
	public function prosperBadSettings()
	{			
		if ( isset( $_GET['page'] ) && 'prosperinsert_general' == $_GET['page'] )
		{
			$url = admin_url( 'admin.php?page=prosperinsert_general' );
			echo '<div class="error" style="padding:6px 0;">';
			echo _e( '<span style="font-size:14px; padding-left:10px;">Your <strong>API Key</strong> is either incorrect or missing. </span></br>', 'my-text-domain' );
			echo _e('<span style="font-size:14px; padding-left:10px;">Please enter your <strong>Prosperent API Key</strong>.</span></br>', 'my-text-domain' ); 
			echo _e('<span style="font-size:14px; padding-left:10px;">Go to the Prosperent Suite <a href="' . $url . '">General Settings</a> and follow the directions to get your API Key.</span>', 'my-text-domain' );
			echo '</div>';		
		}
	}
	
	/**
	 * Retrieve an array of all the options the plugin uses. It can't use only one due to limitations of the options API.
	 *
	 * @return array of options.
	 */
	public function getProsperOptionsArray()
	{
		$optarr = array('prosperSuite', 'prosper_autoComparer', 'prosper_advanced', 'prosper_themes');
        return apply_filters( 'prosper_options', $optarr );
	}
	
	public function shortCodeExtract($atts, $shortcode)
	{	
		return shortcode_atts(array(
			'q'      => '', // query
			'utt'    => 0, // use Title as Topics
			'utg'    => 0, // use Tags as Topics
			'h'      => 90, // height
			'w'      => 'auto',	// width	
			'c'      => 0, // use coupons, deprecated
			'b'      => '', // brand
			'm'      => '', // merchant
			'l'      => 1, // limit
			'cl'     => '', // comparison limit, deprecated
			'ct'     => 'US', // country
			'id'     => '',  // product/catalog id	
			'gtm'    => 0, // go to merchant
			'v'      => 'list', // view
			'w'	     => '', // width
			'ws'     => 'px', // width style (px, em, %)
			'css'    => '', // additional css
			'state'  => '', // state
			'city'   => '', // city 
			'z'	 	 => '', // zipCode
			'ft'  	 => 'fetchProducts', // fetch method
			'sale'   => 0, // on sale products only
			'gimgsz' => 200	 // grid image size		
		), $atts, $shortcode);
	}
	
	public function prosperCustomAdd()
	{
		// Add only in Rich Editor mode
		if (get_user_option('rich_editing') == 'true')
		{
			add_filter('mce_external_plugins', array($this, 'prosperTinyRegister'));
			add_filter('mce_buttons', array($this, 'prosperTinyAdd'));
		}
	}
	
	public function prosperTinyRegister($plugin_array)
	{		
		if (get_bloginfo('version') >= 3.9)
		{
			$plugin_array['prosperinsert'] = PROSPERINSERT_JS . '/prosperent3.9.min.js?ver=' . $this->_version;
		}
		else
		{
			$plugin_array['prosperinsert'] = PROSPERINSERT_JS . '/prosperent.min.js?ver=' . $this->_version;
		}
		return $plugin_array;
	}	
	
	public function prosperTinyAdd($buttons)
	{
		array_push( $buttons, '|', 'prosperinsert');
		return $buttons;
	}
	
	public function qTagsProsper($id, $display, $arg1, $arg2)
	{
		?>
		<script type="text/javascript">
			QTags.addButton(<?php echo "'" . $id . "', '" . $display . "', '" . $arg1 . "', '" . $arg2 . "'"; ?>);
		</script>
		<?php
	}
	
	public function doOutputBuffer()
	{
		ob_start();
	}
	
	public function apiCall ($settings, $fetch)
	{	
		if (empty($this->_options))
		{
			$options = $this->getOptions();
		}
		else
		{
			$options = $this->_options;
		}		

		if ($options['prosperSid'] || $options['prosperSidText'])
		{
			$sid = '';
			foreach ($options['prosperSid'] as $sidPiece)
			{
				switch ($sidPiece)
				{
					case 'blogname':
						$sid .= get_bloginfo('name');
						break;
					case 'interface':
						$sid .= 'insert';
						break;
					case 'query':
						$sid .= $settings['query'];
						break;
					case 'page':
						$sid .= get_the_title();
						break;	
					case 'pageNumber':
						$sid .= $settings['page'];
						break;
				}
			}
			if (preg_match('/(^\$_(SERVER|SESSION|COOKIE))\[(\'|")(.+?)(\'|")\]/', $options['prosperSidText'], $regs))
			{
				if ($regs[1] == '$_SERVER')
				{
					$sid .= $_SERVER[$regs[4]];
				}
				elseif ($regs[1] == '$_SESSION')
				{
					$sid .= $_SESSION[$regs[4]];
				}
				elseif ($regs[1] == '$_COOKIE')
				{
					$sid .= $_COOKIE[$regs[4]];
				}				
			}
		}

		$settings = array_merge($settings, array(
			'api_key' 	   => $options['Api_Key'],
			'visitor_ip'   => $_SERVER['REMOTE_ADDR'],
			'sid'		   => $sid,
		));	
		
		// Set the URL
		$url = $this->_endPoints[$fetch] . http_build_query ($settings);

		$curl = curl_init();

		// Set options
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url,
			CURLOPT_CONNECTTIMEOUT => 30,
			CURLOPT_TIMEOUT => 30
		));

		// Send the request
		$response = curl_exec($curl);

		// Close request
		curl_close($curl);

		// Convert the json response to an array
		$response = json_decode($response, true);

		// Check for errors
		if (count($response['errors']))
		{
			throw new Exception(implode('; ', $response['errors']));
		}

		return array('results' => $response['data'], 'totalAvailable' => $response['totalRecordsAvailable'], 'total' => $response['totalRecordsFound'], 'facets' => $response['facets']);
	}
	
	public function trendsApiCall ($settings, $fetch, $categories = array(), $merchants = array())
	{
		if (empty($this->_options))
		{
			$options = $this->getOptions();
		}
		else
		{
			$options = $this->_options;
		}	

		if ($fetch === 'fetchCoupons')
		{
			$filter  = 'filterCouponId';
			$catalog = 'coupons';
		}
		elseif ($fetch === 'fetchLocal')
		{
			$filter = 'filterLocalId';
			$catalog = 'local';
		}
		else
		{
			$filter = 'filterCatalogId';
			if ($this->_options['Country'] === 'US')
			{
				$catalog = 'US';
			}
			elseif ($this->_options['Country'] === 'CA')
			{
				$catalog = 'CA';
			}
			else 
			{
				$catalog = 'UK';
			}
		}
		
		// calculate date range
		$prevNumDays = 60;
		$startDate   = date('Ymd', time() - 86400 * $prevNumDays);
		$endDate     = date('Ymd');		
		
		$apiCall = array(
			'api_key' 	     	   => $this->_options['Api_Key'],
			'visitor_ip'     	   => $_SERVER['REMOTE_ADDR'],	
			'limit'		     	   => $options['Pagination_Limit'],
			'enableFacets'   	   => array('catalogId'),
			'filterCatalog'  	   => $catalog,
			'filterCategory' 	   => $categories,			
			'filterMerchant'	   => $merchants,
			'filterCommissionDate' => $startDate . ',' . $endDate
		);

		$apiCall = array_filter($apiCall);
		
		// Set the URL
		$url = $this->_endPoints['fetchTrends'] . http_build_query ($apiCall);

		$curl = curl_init();

		// Set options
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url,
			CURLOPT_CONNECTTIMEOUT => 30,
			CURLOPT_TIMEOUT => 30
		));

		// Send the request
		$response = curl_exec($curl);

		// Close request
		curl_close($curl);

		// Convert the json response to an array
		$response = json_decode($response, true);

		// Check for errors
		if (count($response['errors']) || empty($response['facets']['catalogId']))
		{
			return array();
		}

		//$api->setDateRange('commission', $startDate, $endDate)
		//	->fetchTrends();
		
		// set productId as key in array
		$keys = array();
		foreach ($response['facets']['catalogId'] as $data)
		{
			$keys[] = $data['value'];
		}

		if ($fetch === 'fetchCoupons')
		{
			$filter = 'filterCouponId';
		}
		elseif ($fetch === 'fetchLocal')
		{
			$filter = 'filterLocalId';
		}
		else
		{
			$filter = 'filterCatalogId';
		}

		// fetch trend data from api
		$settings = array_merge(array(
			$filter		   => $keys,
			'limit'		   => $options['Pagination_Limit'],
			'enableFacets' => FALSE
		), $settings);

		$results = $this->apiCall($settings, $fetch, $lifetime);

		return $results;
	}
}
