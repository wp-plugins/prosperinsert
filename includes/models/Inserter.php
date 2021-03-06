<?php
require_once(PROSPERINSERT_MODEL . '/Base.php');
/**
 * Search Model
 *
 * @package Model
 */
class Model_Insert_Inserter extends Model_Insert_Base
{
	protected $_shortcode = 'compare';
	
	public $states = array(
		'alabama'		 =>'AL',
		'alaska'		 =>'AK',
		'arizona'		 =>'AZ',
		'arkansas'		 =>'AR',
		'california'	 =>'CA',
		'colorado'		 =>'CO',
		'connecticut'	 =>'CT',
		'DC'	 		 =>'DC',
		'delaware'		 =>'DE',
		'florida'		 =>'FL',
		'georgia'		 =>'GA',
		'hawaii'		 =>'HI',
		'idaho'		 	 =>'ID',
		'illinois'		 =>'IL',
		'indiana'		 =>'IN',
		'iowa'			 =>'IA',
		'kansas'		 =>'KS',
		'kentucky'		 =>'KY',
		'louisiana'		 =>'LA',
		'maine'			 =>'ME',
		'maryland'		 =>'MD',
		'massachusetts'	 =>'MA',
		'michigan'		 =>'MI',
		'minnesota'		 =>'MN',
		'mississippi'	 =>'MS',
		'missouri'		 =>'MO',
		'montana'		 =>'MT',
		'nebraska'		 =>'NE',
		'nevada'		 =>'NV',
		'new hampshire'	 =>'NH',
		'new jersey'	 =>'NJ',
		'new mexico'	 =>'NM',
		'new york'		 =>'NY',
		'north carolina' =>'NC',
		'north dakota'	 =>'ND',
		'ohio'			 =>'OH',
		'oklahoma'		 =>'OK',
		'oregon'		 =>'OR',
		'pennsylvania'	 =>'PA',
		'rhode island'   =>'RI',
		'south carolina' =>'SC',
		'south dakota'   =>'SD',
		'tennessee'      =>'TN',
		'texas'			 =>'TX',
		'utah'			 =>'UT',
		'vermont'		 =>'VT',
		'virginia'		 =>'VA',
		'washington'	 =>'WA',
		'west virginia'	 =>'WV',
		'wisconsin'		 =>'WI',
		'wyoming'		 =>'WY'
	);	
	
	public $_options;

	public function __construct()
	{
		$this->_options = $this->getOptions();
	}
	
	public function qTagsInsert()
	{
		$id 	 = 'productInsert';
		$display = 'ProsperInsert';
		$arg1 	 = '[compare q="QUERY" b="BRAND" m="MERCHANT" l="LIMIT" ct="US" gtm="GO TO MERCHANT?" c="USE COUPONS?" v="GRID OR LIST"]';
		$arg2 	 = '[/compare]';		
	
		$this->qTagsProsper($id, $display, $arg1, $arg2);
	}
	
	public function contentInserter($text)
	{		
		$newTitle = get_the_title();

		if (preg_match('/\[prosperNewQuery="(.+)"\]/i', $text, $regs))
		{
			$newTitle = $regs[1];
			$text = preg_replace('/\[prosperNewQuery="(.+)"\]/i', '', $text);
		}
		
		if ($this->_options['prosper_inserter_negTitles'])
		{
			if(function_exists('prosper_negatives') === false)
			{
				function prosper_negatives($negative)
				{
					return '/\b' . trim($negative) . '\b/i';
				}
			}	

			$exclude = array_map(
				"prosper_negatives",
				explode(',', $this->_options['prosper_inserter_negTitles'])
			);

			$newTitle = preg_replace($exclude, '', $newTitle);
		}
		
		if (!$newTitle)
		{
			return trim($text);
		}
		
		$insert = '<p>[compare q="' . $newTitle . '" l="' . ($this->_options['PI_Limit'] ? $this->_options['PI_Limit'] : 1) . '" v="' . ($this->_options['prosper_insertView'] ? $this->_options['prosper_insertView'] : 'list') . '"][/compare]</p>';
		
		if ('top' == $this->_options['prosper_inserter'])
		{
			$content = $insert . $text;
		}
		else
		{
			$content = $text . $insert;
		}
		
		if ($this->_options['prosper_inserter_pages'] && $this->_options['prosper_inserter_posts'])
		{
			if( is_singular() && is_main_query() ) 
			{
				$text = $content;
			}
			
			if(is_single()) 
			{
				$text = $content;	
			}
		}
		elseif($this->_options['prosper_inserter_posts'])
		{
			if(is_single()) 
			{
				$text = $content;
			}				
		}
		elseif($this->_options['prosper_inserter_pages'])
		{
			if( is_singular() && is_main_query() ) 
			{
				$text = $content;
			}
		}		

		return trim($text);
	}
	
	public function inserterShortcode($atts, $content = null)
	{
		$target  = $this->_options['Target'] ? '_blank' : '_self';
		$base 	 = $this->_options['Base_URL'] ? $this->_options['Base_URL'] : 'products';
		$homeUrl = home_url();
		$type 	 = 'product';

		$pieces = $this->shortCodeExtract($atts, $this->_shortcode);
		$pieces = array_filter($pieces);
		
		$fetch = $pieces['ft'];

		// Remove links within links
		$content = strip_tags($content);

		$id = $pieces['id'] ? array_map('trim', explode(',',  rtrim($pieces['id'], ","))) : '';
		
		$limit = 1;		
		if ($pieces['cl'] && $pieces['cl'] > $pieces['l'])
		{
			$limit = $pieces['cl'];
		}
		elseif ($pieces['l'] > 1)
		{		
			$limit = $pieces['l'];
		}
		elseif ($id)
		{
			$limit = count($id);
		}

		if ($fetch === 'fetchLocal')
		{
			$recordId = 'localId';
			$type = 'local';
		
			if (strlen($pieces['state']) > 2)
			{
				$state = $this->states[strtolower($pieces['state'])];
			}
			else
			{
				$state = $pieces['state'];
			}

			$settings = array(
				'imageSize'		  => $pieces['v'] === 'grid' && $pieces['gimgsz'] > 125 ? '250x250' : '125x125',
				'limit'           => $limit,
				'filterState'	  => $state ? $state : '',
				'filterCity'	  => $pieces['city'] ? $pieces['city'] : '',
				'filterZipCode'	  => $pieces['z'] ? $pieces['z'] : '',
				'query'           => trim(strip_tags($pieces['q'] ? $pieces['q'] : $content)),
				'filterMerchant'  => $pieces['m'] ? array_map('trim', explode(',',  $pieces['m'])) : '',
				'filterLocalId'   => $id,		
			);
		}
		elseif ($fetch === 'fetchCoupons' || $pieces['c'])
		{		
			$recordId = 'couponId';
			
			$settings = array(
				'imageSize'		 => '120x60',
				'limit'          => $limit,
				'query'          => trim(strip_tags($pieces['q'] ? $pieces['q'] : $content)),
				'filterMerchant' => $pieces['m'] ? explode(',', trim($pieces['m'])) : '',		
				'filterCouponId' => $id,
			);			

			$imageLoader = 'small';
			$type = 'coupon';
		}
		elseif ($fetch === 'fetchProducts')
		{
			$recordId = 'catalogId';
			if ($pieces['ct'] === 'UK')
			{
				$fetch = 'fetchUkProducts';
				$currency = 'GBP';
			}
			elseif ($pieces['ct'] === 'CA')
			{
				$fetch = 'fetchCaProducts';
				$currency = 'CAD';
			}
			else 
			{
				$currency = 'USD';
			}	

			$settings = array(
				'imageSize'		  => $pieces['v'] === 'grid' && $pieces['gimgsz'] > 125 ? '250x250' : '125x125',
				'limit'           => $limit,
				'query'           => trim(strip_tags($pieces['q'] ? $pieces['q'] : $content)),
				'filterMerchant'  => $pieces['m'] ? array_map('trim', explode(',',  $pieces['m'])) : '',
				'filterBrand'	  => $pieces['b'] ? array_map('trim', explode(',',  $pieces['b'])) : '',			
				'filterProductId' => $id,
				'filterPriceSale' => $pieces['sale'] ? '0.01,' : ''
			);
		}
		
		$settings = array_filter($settings);

		if (count($settings) < 3)
		{
			return;
		}
		
		$allData = $this->apiCall($settings, $fetch);

		if (!$allData['results'])
		{
			$count = count($settings);
			for ($i = 0; $i <= $count; $i++)
			{
				array_pop($settings);

				if(count($settings) < 3)
				{
					return;
				}
			
				$allData = $this->apiCall($settings, $fetch);
				
				if ($allData['results'])
				{
					break;
				}	 
			}
		}
		
		$prodSubmit = home_url('/') . $base;	
		
		// CHECK INTO THIS AFTER STORE IS COMPLETE
		if (!$this->_options['Enable_PPS'])
		{
			if ($storeUrl = get_query_var('storeUrl'))
			{    
				$storeUrl = rawurldecode($storeUrl);
				$storeUrl = str_replace(',SL,', '/', $storeUrl);
				header('Location:http://prosperent.com/' . $storeUrl);
				exit;
			}
		}
		
		$results = $allData['results'];
		
		$insertProd = PROSPERINSERT_VIEW . '/prosperinsert/insertProd.php';
		
		// Inserter PHTML file
		if ($this->_options['Set_Theme'] != 'Default')
		{
			$dir = PROSPERINSERT_THEME . '/' . $this->_options['Set_Theme'];
			if($newTheme = glob($dir . "/*.php"))
			{			
				foreach ($newTheme as $theme)
				{
					if (preg_match('/insertProd.php/i', $theme))
					{
						$insertProd = $theme;
					}			
				}
			}
		}
		
		ob_start();
		require($insertProd);
		$insert = ob_get_clean();
		return $insert;
	}
}