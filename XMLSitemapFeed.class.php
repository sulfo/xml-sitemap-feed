<?php
/* ------------------------------
 *      XMLSitemapFeed CLASS
 * ------------------------------ */

class XMLSitemapFeed {

	/**
	* Plugin variables
	*/
	
	public $base_name = 'sitemap';

	public $news_name = 'sitemap-news';

	public $extension = 'xml';
	
	private $defaults = array(
				'sitemap_feed' => '1',
				'news_feed' => '0',
				'news_tags' => array(),
				'post_types' => array('page','post'),
				'taxonomies' => array('category','post_tag'),
				);
				
	private $do_news_feed = true;
	
	private $do_news_tags = array('post'); // TODO replace by get_option + defaults['news_tags']
	
	private $post_types = array();
	
	private $taxonomies = array();
	
	private $sitemaps = array();
		
	private function sitemaps() {
		if ( '1' == get_option('blog_public') ) {
			$blog_url = trailingslashit(get_bloginfo('url'));
			$this->sitemaps[] = $blog_url.$this->base_name.'.'.$this->extension;

			if ( '1' == get_option('news_feed', $this->defaults['news_feed']) )					
				$this->sitemaps[] = $blog_url.$this->news_name.'.'.$this->extension;
		}
	}

	public function defaults($key) {
		if (!isset($key))
			return $this->defaults;
		
		$append = array();

		if ($key == 'taxonomies')
			$append = get_taxonomies(array('public'=>true,'_builtin'=>false),'names');
		if ($key == 'post_types')
			$append = get_post_types(array('public'=>true,'_builtin'=>false),'names');
		
		return $this->defaults[$key] + $append;
	}

	public function get_sitemaps() {
		// if sitemaps are not set yet, do it first
		if (empty($this->sitemaps))
			$this->sitemaps();
		
		return $this->sitemaps;
	}

	private function post_types() {
		$this->post_types = get_option('XMLSitemapFeed_post_types',$this->defaults('post_types'));
	}
	
	public function get_post_types() {
		// if post types are not set yet, do it first
		if (empty($this->post_types))
			$this->post_types();
		
		return $this->post_types;
	}
	
/* delete ?
	private function populate_taxonomies() {
		$this->taxonomies = get_option('XMLSitemapFeed_taxonomies',$this->defaults('taxonomies'));
	}
	
	public function taxonomies() {
		// if taxonomy types are not set yet, do it first
		if (empty($this->taxonomies))
			$this->populate_taxonomies();
		
		return $this->taxonomies;
	}
*/
	public function do_news_feed() {		
		return $this->do_news_feed;
	}
		
	public function do_news_tags( $post_type = '' ) {
		if ( is_array( $post_type ) )
			foreach ( $post_type as $type )
				if ( in_array( $type, $this->do_news_tags ) )
					return true;
		
		if ( in_array( $post_type, $this->do_news_tags ) )
			return true;
		
		return false;
	}
	
	/**
	* SETTINGS
	*/

	// add our FancyBox Media Settings Section on Settings > Media admin page
	public function privacy_settings_section() {
		echo '<p>...</p>';
	}

	private $privacy_setting_array = array(
						'option1' => array(
							'title' => 'Title',
							'label' => 'Test',
							'type' => 'checkbox', // select, function, checkbox, text, number, hidden...
							//'function' => 'functionname',
							//'options' => array(''=>'',''=>''),
							'default' => '',
							//'class' => '', // used for text fields
							'description' => '',
							//'sanitize_callback' => 'functionname'
							),
						'option2' => array(
							'label' => 'Test 2',
							'type' => 'function', // select, function, checkbox, text, number, hidden...
							'function' => 'test',
							'description' => 'Test 2 description',
							)
		
					);
	
	public function test($args) {
error_log('called test function with parameter: ' . print_r($args, true));
	}
	
	// add our Settings Fields
	function settings_fields($settings){
		$disabled = ('0' == get_option('blog_public')) ? true : false;
		foreach ( $settings as $name => $args ) {
			switch($args['type']) {
				case 'select' :
					echo '<label>'.$args['label'].' <select name="XMLSitemapFeed_'.$name.'" id="XMLSitemapFeed_'.$name.'">';
					foreach ($args['options'] as $optionkey => $optionvalue) {
						echo '
						<option value="'.esc_attr($optionkey).'"'.selected($optionkey, get_option('XMLSitemapFeed_'.$name, $args['default']), false).' '.disabled($disabled, true, false).' >'.$optionvalue.'</option>';
					}
					echo '
					</select> '.$args['description'];
					break;
				case 'function' :
					call_user_func(array($this,$args['function']),$args);
					echo ' '.$args['description'];
					break;
				case 'checkbox' :
					echo '
					<label><input type="checkbox" name="XMLSitemapFeed_'.$name.'" id="XMLSitemapFeed_'.$name.'" value="1" '.checked('1', get_option('XMLSitemapFeed_'.$name, $args['default']), false).' '.disabled($disabled, true, false).' /> '.$args['label'].'</label> '.$args['description'];
					break;
				case 'text' :
					echo '<label>'.$args['label'].'<input type="text" name="XMLSitemapFeed_'.$name.'" id="XMLSitemapFeed_'.$name.'" value="'.esc_attr( get_option('XMLSitemapFeed_'.$name, $args['default']) ).'" class="'.$args['class'].'"'.disabled($disabled, true, false).' /> '.$args['description'];
					break;
				case 'number' :
					echo '<label>'.$args['label'].' <input type="number" step="0.1" min="0" max="1" name="XMLSitemapFeed_'.$name.'" id="XMLSitemapFeed_'.$name.'" value="'.esc_attr( get_option('XMLSitemapFeed_'.$name, $args['default']) ).'" class="'.$args['class'].'"'.disabled($disabled, true, false).' /> '.$args['description'];
					break;
				case 'hidden' :
					echo '
					<input type="hidden" name="XMLSitemapFeed_'.$name.'" id="XMLSitemapFeed_'.$name.'" value="'.esc_attr( get_option('XMLSitemapFeed_'.$name, $args['default']) ).'" /> ';
					break;
				default :
					echo $args['description'];
			}
		}
	}

	private function checkboxes($args) {
		if (is_array($optionvalue)) {
			foreach ($args['options'] as $optionkey => $optionvalue) {
//.TODO
			}
			
		}

		echo '
			<input type="checkbox" name="'.$args['id'].'" id="'.$args['id'].'" value="1" '.checked('1', get_option($args['id'], $args['default']), false).' '.disabled($disabled, true, false).' /> '.$args['label'].'</label> ';
	}

	public function register_settings($option_group = 'privacy', $settings) {
		foreach ($settings as $name => $value) {
			//if ( isset($value['id']) ) {
				register_setting( $option_group, 'XMLSitemapFeed_'.$name, $value['sanitize_callback'] );
				add_settings_field( 'XMLSitemapFeed_'.$name, $value['label'], array($this,'settings_fields'), $option_group, 'xml-sitemap-feed_'.$option_group.'_section', array($name => $value));
			//}
		}
	}
		
	/**
	* FEED TEMPLATES
	*/

	// set up the sitemap template
	public function load_template() {
		load_template( XMLSF_PLUGIN_DIR . '/feed-sitemap.php' );
	}

	// set up the news sitemap template
	public function load_template_news() {
		load_template( XMLSF_PLUGIN_DIR . '/feed-sitemap-news.php' );
	}

	// set up the sitemap index template
	public function load_template_index() {
		load_template( XMLSF_PLUGIN_DIR . '/feed-sitemap-index.php' );
	}

	// set up the taxonomy sitemap template
	public function load_template_taxonomy() {
		load_template( XMLSF_PLUGIN_DIR . '/feed-sitemap-taxonomy.php' );
	}

	// override default feed limit
	public function filter_limits( $limits ) {
		return 'LIMIT 0, 50000';
	}

	// override default feed limit for taxonomy sitemaps
	public function filter_limits_taxonomy( $limits ) {
		return 'LIMIT 0, 1';
	}

	// override default feed limit for GN
	public function filter_news_limits( $limits ) {
		return 'LIMIT 0, 1000';
	}

	// Create a new filtering function that will add a where clause to the query,
	// used for the Google News Sitemap
	public function filter_news_where( $where = '' ) {
		// only posts from the last 2 days
		return $where . " AND post_date > '" . date('Y-m-d H:i:s', strtotime('-49 hours')) . "'";
	}
		
	public function init() {

		// TEXT DOMAIN
		
		if ( is_admin() ) // text domain must be in init even if it is for admin only
			load_plugin_textdomain('xml-sitemap-feed', false, dirname(plugin_basename( __FILE__ )) . '/languages' );
		
		// LANGUAGE PLUGINS

		// check for Polylang and add filter
		global $polylang;
		if (isset($polylang))
			add_filter('xml_sitemap_url', array($this, 'polylang'), 99);

		// check for qTranslate and add filter
		elseif (defined('QT_LANGUAGE'))
			add_filter('xml_sitemap_url', array($this, 'qtranslate'), 99);

		// check for xLanguage and add filter
		elseif (defined('xLanguageTagQuery'))
			add_filter('xml_sitemap_url', array($this, 'xlanguage'), 99);

	}

	public function admin_init() {

		// UPGRADE RULES after (site wide) plugin upgrade
		// do it only on admin_init since flush rules on init gives
		// strange results with Polylang.

		if (get_option('xml-sitemap-feed-version') != XMLSF_VERSION) {
			flush_rewrite_rules(false);
			update_option('xml-sitemap-feed-version', XMLSF_VERSION);
		}
		
		// SETTINGS
		
		add_settings_section('xml-sitemap-feed_privacy_section', __('XML Sitemaps','xml-sitemap-feed'), array($this,'privacy_settings_section'), 'privacy');

		$this->register_settings('privacy',$this->privacy_setting_array);
	
		//add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'easy_fancybox_add_action_link');

	}

	/**
	* ROBOTSTXT 
	*/

	// add sitemap location in robots.txt generated by WP
	// available filter : xml_sitemap_url
	public function robots() {

		if (empty($this->sitemaps))
			$this->sitemaps();

		// hook for filter 'xml_sitemap_url' provides an array here and MUST get an array returned
		//$sitemap_array = apply_filters('xml_sitemap_url',$this->sitemaps);

		echo "\n# XML & Google News Sitemap Feeds - version ".XMLSF_VERSION." (http://status301.net/wordpress-plugins/xml-sitemap-feed/)";

		if ( !empty($this->sitemaps) )
			foreach ( $this->sitemaps as $url )
				echo "\nSitemap: " . $url;
		else
			echo "\n# Warning: XML Sitemaps are disabled. Please see your Privacy settings.";
	
		echo "\n\n";
	}
	
	/**
	* DE-ACTIVATION
	*/

	public function deactivate() {
		global $wp_rewrite;
		remove_action('generate_rewrite_rules', array($this, 'rewrite_rules') );
		$wp_rewrite->flush_rules();
		delete_option('xml-sitemap-feed-version');
	}

	/**
	* REWRITES
	*/

	/**
	 * Remove the trailing slash from permalinks that have an extension,
	 * such as /sitemap.xml (thanks to Permalink Editor plugin for WordPress)
	 *
	 * @param string $request
	 */
	 
	public function trailingslash($request) {
		if (pathinfo($request, PATHINFO_EXTENSION)) {
			return untrailingslashit($request);
		}
		return trailingslashit($request);
	}

	/**
	 * Add sitemap rewrite rules
	 *
	 * @param string $wp_rewrite
	 */
	 
	public function rewrite_rules($wp_rewrite) {
		
		$feed_rules = array(
			$this->base_name . '\.xml$' => $wp_rewrite->index . '?feed=sitemap-index',
			$this->base_name . '-any\.xml$' => $wp_rewrite->index . '?feed=sitemap', //
			$this->base_name . '\.([a-z0-9_-]+)?\.xml$' => $wp_rewrite->index . '?feed=sitemap_post&category_name=$matches[1]',
		);

		// add rules for custom public post types
		if (empty($this->post_types))
			$this->post_types();
		
		foreach ( $this->post_types as $post_type ) {
			//$feed_rules[XMLSF_NAME.'-'.$post_type.'\.xml$'] = $wp_rewrite->index.'?feed=sitemap-'.$post_type;
			$feed_rules[ $this->base_name . '-posttype-' .$post_type . '\.([0-9]+)?\.?' . $this->extension . '$' ] = $wp_rewrite->index . '?feed=sitemap_' . $post_type . '&m=$matches[1]';
		}
		
		// add rules for custom public post taxonomies
		$taxonomies = get_option('XMLSitemapFeed_taxonomies',$this->defaults('taxonomies'));

		foreach ( $taxonomies as $taxonomy ) {
			$feed_rules[ $this->base_name . '-taxonomy-' . $taxonomy . '\.' . $this->extension . '$' ] = $wp_rewrite->index . '?feed=sitemap-taxonomy&taxonomy=' . $taxonomy;
			//$feed_rules[XMLSF_NAME.'_'.$taxonomy_type.'\.([a-z0-9_-]+)?\.xml$'] = $wp_rewrite->index.'?feed=sitemap-taxonomy&taxonomy='.$taxonomy_type.'&'.$taxonomy_type.'=$matches[1]';
		}
		
		if ( $this->do_news_feed ) 
			$feed_rules[ $this->news_name . '\.' . $this->extension . '$' ] = $wp_rewrite->index . '?feed=' . $this->news_name;

		$wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
	}
	
	/**
	* REQUEST FILTER
	*/

	public function filter_request( $request ) {
		if ( isset($request['feed']) && strpos($request['feed'],'sitemap') == 0 ) {

			if (empty($this->post_types))
				$this->post_types();

			if ( $request['feed'] == 'sitemap' ) {
				
				// setup actions and filters
				add_action('do_feed_sitemap', array($this, 'load_template'), 10, 1);
				add_filter( 'post_limits', array($this, 'filter_limits') );

				// modify request parameters
				$request['post_type'] = $this->post_types;
				$request['orderby'] = 'modified';

				$request['no_found_rows'] = true;
				$request['update_post_meta_cache'] = false;
				$request['update_post_term_cache'] = false;

				return $request;
			}

			if ( $request['feed'] == 'sitemap-index' ) {
				// setup actions and filters
				add_action('do_feed_sitemap-index', array($this, 'load_template_index'), 10, 1);

				return $request;
			}

			if ( $request['feed'] == 'sitemap-taxonomy' ) {
				// setup actions and filters
				add_action('do_feed_sitemap-taxonomy', array($this, 'load_template_taxonomy'), 10, 1);
//				add_filter( 'post_limits', array( $this, 'filter_limits_taxonomy' ) );

				$request['no_found_rows'] = true;
				$request['update_post_meta_cache'] = false;
				$request['update_post_term_cache'] = false;
				$request['post_status'] = 'publish';

				return $request;
			}

			if ( $request['feed'] == $this->news_name ) {
				// disable caching
				define( 'DONOTCACHEPAGE', 1 ); // wp super cache
				// TODO w3tc
				
				// setup actions and filters
				add_action('do_feed_'.$this->news_name, array($this, 'load_template_news'), 10, 1);
				add_filter( 'post_limits', array($this, 'filter_news_limits') );
				add_filter( 'posts_where', array($this, 'filter_news_where'), 10, 1  );

				// modify request parameters
				$types_arr = explode(',',XMLSF_NEWS_POST_TYPE);
				$request['post_type'] = (in_array('any',$types_arr)) ? 'any' : $types_arr;

				$request['no_found_rows'] = true;
				$request['update_post_meta_cache'] = false;
				//$request['update_post_term_cache'] = false; // << TODO test: can we disable or do we need this for terms?

				return $request;
			}

			foreach ($this->post_types as $post_type ) {
				if ( $request['feed'] == 'sitemap_'.$post_type ) {
					// setup actions and filters
					add_action('do_feed_sitemap_'.$post_type, array($this, 'load_template'), 10, 1);
					add_filter( 'post_limits', array($this, 'filter_limits') );

					// modify request parameters
					$request['post_type'] = $post_type;
					$request['orderby'] = 'modified';
					
					$request['no_found_rows'] = true;
					$request['update_post_meta_cache'] = false;
					$request['update_post_term_cache'] = false;

					return $request;
				}
			}

		}

		return $request;
	}

	/**
	* MULTI-LANGUAGE PLUGIN FILTERS
	*/

	// Polylang
	public function polylang($input) {
		global $polylang;
		$options = get_option('polylang');

		if (is_array($input)) { // got an array? return one!
			if ('1' == $options['force_lang'] )
				foreach ( $input as $url )
					foreach($polylang->get_languages_list() as $language)
						$return[] = $polylang->add_language_to_link($url,$language);
			else
				foreach ( $input as $url )
					foreach($polylang->get_languages_list() as $language)
						$return[] = add_query_arg('lang', $language->slug, $url);
		} else { // not an array? do nothing, Polylang does all the work :)
			$return = $input;
		}

		return $return;
	}

	// qTranslate
	public function qtranslate($input) {
		global $q_config;

		if (is_array($input)) // got an array? return one!
			foreach ( $input as $url )
				foreach($q_config['enabled_languages'] as $language)
					$return[] = qtrans_convertURL($url,$language);
		else // not an array? just convert the string.
			$return = qtrans_convertURL($input);

		return $return;
	}

	// xLanguage
	public function xlanguage($input) {
		global $xlanguage;
	
		if (is_array($input)) // got an array? return one!
			foreach ( $input as $url )
				foreach($xlanguage->options['language'] as $language)
					$return[] = $xlanguage->filter_link_in_lang($url,$language['code']);
	 	else // not an array? just convert the string.
	       	$return = $xlanguage->filter_link($input);

		return $return;
	}

	/**
	* CONSTRUCTOR
	*/

	function XMLSitemapFeed() {
		//constructor in php4
		$this->__construct(); // just call the php5 one.
	}
	
	function __construct() {
		
		// REQUEST main filtering function
		add_filter('request', array($this, 'filter_request'), 1 );
		
		// TEXT DOMAIN, LANGUAGE PLUGIN FILTERS ...
		add_action('init', array($this,'init'));

		// REWRITES
		add_action('generate_rewrite_rules', array($this, 'rewrite_rules') );
		add_filter('user_trailingslashit', array($this, 'trailingslash') );
		
		// REGISTER SETTINGS, SETTINGS FIELDS, UPGRADE checks...
		add_action('admin_init', array($this,'admin_init'));
		
		// ROBOTSTXT
		add_action('do_robotstxt', array($this, 'robots') );

		// DE-ACTIVATION
		register_deactivation_hook( XMLSF_PLUGIN_DIR . '/xml-sitemap.php', array($this, 'deactivate') );
	}

}
