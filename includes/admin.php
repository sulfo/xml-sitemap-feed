<?php
/* ------------------------------
 *      XMLSF Admin CLASS
 * ------------------------------ */
 
 class XMLSF_Admin extends XMLSitemapFeed {

	/**
	* SETTINGS
	*/

	// add our FancyBox Media Settings Section on Settings > Media admin page
	// TODO get a donation button in there and refer to support forum !
	public function privacy_settings_section() {
		echo '<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=XML%20Sitemap%20Feeds&item_number='.XMLSF_VERSION.'&no_shipping=0&tax=0&charset=UTF%2d8&currency_code=EUR" title="'.sprintf(__('Donate to keep the free %s plugin development & support going!','easy-fancybox'),__('XML Sitemap & Google News Feeds','xml-sitemap-feed')).'"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" style="border:none;float:left;margin:4px 10px 0 0" alt="'.sprintf(__('Donate to keep the free %s plugin development & support going!','easy-fancybox'),__('XML Sitemap & Google News Feeds','xml-sitemap-feed')).'" width="92" height="26" /></a>'.sprintf(__('These settings control the XML Sitemaps generated by the %s plugin.','xml-sitemap-feed'),__('XML Sitemap & Google News Feeds','xml-sitemap-feed')).'<br/>';
		echo ('1' == get_option('blog_public')) ? __('Note:','xml-sitemap-feed').' '.sprintf(__('XML Sitemaps will be disabled if you set the option %1$s (above) to %2$s.','xml-sitemap-feed'),'<strong>'.__('Search Engine Visibility').'</strong>','<strong>'.__('Discourage search engines from indexing this site').'</strong>') : '<span style="color: red" class="error">'.sprintf(__('XML Sitemaps are disabled because you have set the option %1$s (above) to %2$s.','xml-sitemap-feed'),'<strong>'.__('Search Engine Visibility').'</strong>','<strong>'.__('Discourage search engines from indexing this site').'</strong>').'</span>';
		echo '</p>
    <script type="text/javascript">
        jQuery( document ).ready( function() {
            jQuery( "input[name=\'blog_public\']" ).on( \'change\', function() {
			jQuery("#xmlsf_sitemaps input").each(function() {
			  var $this = jQuery(this);
			  $this.attr("disabled") ? $this.removeAttr("disabled") : $this.attr("disabled", "disabled");
			});
            });
            jQuery( "#xmlsf_sitemaps_index" ).on( \'change\', function() {
			jQuery("#xmlsf_post_types input:not([type=\'hidden\']),#xmlsf_post_types select,#xmlsf_taxonomies input:not([type=\'hidden\']),#xmlsf_ping input").each(function() {
			  var $this = jQuery(this);
			  $this.attr("disabled") ? $this.removeAttr("disabled") : $this.attr("disabled", "disabled");
			});
            });
        });
    </script>';
	}
	
	public function sitemaps_settings_field() {
		$options = parent::get_sitemaps();
		$disabled = ('1' == get_option('blog_public')) ? false : true;

		echo '<div id="xmlsf_sitemaps">
			<label><input type="checkbox" name="xmlsf_sitemaps[sitemap]" id="xmlsf_sitemaps_index" value="'.XMLSF_NAME.'" '.checked(isset($options['sitemap']), true, false).' '.disabled($disabled, true, false).' /> '.__('Regular XML Sitemaps','xml-sitemap-feed').'</label>';
		if (isset($options['sitemap']))
			echo '<span class="description">&nbsp; &ndash; &nbsp;<a href="'.trailingslashit(get_bloginfo('url')). ( ('' == get_option('permalink_structure')) ? '?feed=sitemap' : $options['sitemap'] ) .'" target="_blank">'.__('View').'</a></span>';
		//<a href="#">'.__('Settings').'</a> | <a href="#">'.__('Advanced').'</a> | <a href="#">'.__('Advanced Settings').'</a> | ...
		//__('Note: if you do not include any post or taxonomy types below, the sitemap will only contain your sites root url.','xml-sitemap-feed')
		echo '<br />
			<label><input type="checkbox" name="xmlsf_sitemaps[sitemap-news]" id="xmlsf_sitemaps_news" value="'.XMLSF_NEWS_NAME.'" '.checked(isset($options['sitemap-news']), true, false).' '.disabled($disabled, true, false).' /> '.__('Google News Sitemap','xml-sitemap-feed').'</label>';
		if (isset($options['sitemap-news']))
			echo '<span class="description">&nbsp; &ndash; &nbsp;<a href="'.trailingslashit(get_bloginfo('url')). ( ('' == get_option('permalink_structure')) ? '?feed=sitemap-news' : $options['sitemap-news'] ) .'" target="_blank">'.__('View').'</a></span>';
		echo '
		</div>';
	}

	public function post_types_settings_field() {
		$options = parent::get_post_types();
		$defaults = parent::defaults('post_types');
		$do_note = false;

		echo '<div id="xmlsf_post_types">
			';
		foreach ( get_post_types(array('public'=>true),'objects') as $post_type ) {
			$count = wp_count_posts( $post_type->name );
				
			echo '
				<input type="hidden" name="xmlsf_post_types['.
				$post_type->name.'][name]" value="'.
				$post_type->name.'" />';

			echo '
				<label><input type="checkbox" name="xmlsf_post_types['.
				$post_type->name.'][active]" id="xmlsf_post_types_'.
				$post_type->name.'" value="1"'.
				checked( !empty($options[$post_type->name]["active"]), true, false).' /> '.
				$post_type->label.'</label> ('.
				$count->publish.')';
			
			if (!empty($options[$post_type->name]['active'])) {
/* Find a better way...			
				if ( !empty($options[$post_type->name]["tags"]) )
					foreach ( (array)$options[$post_type->name]["tags"] as $tag )
						echo '
							<input type="hidden" name="xmlsf_post_types['.
							$post_type->name.'][tags][]" value="'.$tag.'" />';
				else
					echo '
						<input type="hidden" name="xmlsf_post_types['.
						$post_type->name.'][tags][]" value="image" />
						<input type="hidden" name="xmlsf_post_types['.
						$post_type->name.'][tags][]" value="video" />';*/
				
				if ( isset($defaults[$post_type->name]['archive']) ) {
					$archives = array (
								'yearly' => __('year'),
								'monthly' => __('month') 
								);
					$archive = !empty($options[$post_type->name]['archive']) ? $options[$post_type->name]['archive'] : $defaults[$post_type->name]['archive'];
					echo ' 
					<label>'.__('devided by','xml-sitemap-feed').' <select name="xmlsf_post_types['.
						$post_type->name.'][archive]" id="xmlsf_post_types_'.
						$post_type->name.'_archive">
						<option value=""></option>';
					foreach ($archives as $value => $translation)
						echo '
						<option value="'.$value.'" '.
						selected( $archive == $value, true, false).
						'>'.$translation.'</option>';
					echo '</select>
					</label>
					';
				}
				
				if ( isset($defaults[$post_type->name]['priority'])) {
					$priority_calc = !empty($options[$post_type->name]['priority']['calculation']) ? $options[$post_type->name]['priority']['calculation'] : $defaults[$post_type->name]['priority']['calculation'];
					echo '&nbsp; &ndash;&nbsp; 
						<label>'.sprintf(__('Use a %s priority','xml-sitemap-feed'),'<select name="xmlsf_post_types['.
						$post_type->name.'][priority][calculation]" id="xmlsf_post_types_'.
						$post_type->name.'_priority_calculation">
							<option value="dynamic" '
							. selected( $priority_calc == 'dynamic', true, false)
							.'>'.__('dynamic','xml-sitemap-feed').'</option>
							<option value="static" '
							. selected( $priority_calc == 'static', true, false)
							.'>'.__('static','xml-sitemap-feed').'</option>
						</select>').'</label>';

					$priority_val = !empty($options[$post_type->name]['priority']['value']) ? $options[$post_type->name]['priority']['value'] : $defaults[$post_type->name]['priority']['value'];
					echo ' 
						<label>'.sprintf(__('with initial value %s','xml-sitemap-feed'),'<input type="number" step="0.1" min="0.1" max="0.9" name="xmlsf_post_types['.
						$post_type->name.'][priority][value]" id="xmlsf_post_types_'.
						$post_type->name.'_priority_value" value="'.$priority_val.'" class="small-text">').'</label>';
					$do_note = true;
				}
			}

			echo '
				<br />';
		}

		if ($do_note) echo '
		<p class="description">'.__('Dynamic priority is calculated by the initial value ajusted according to the relative last modification age and comment count. Sticky posts always get the maximum initial priority value of 1. A different priority can be set on a post by post basis.','xml-sitemap-feed').' '.__('Note:','xml-sitemap-feed').' '.__('Priority does not affect ranking in search results in any way. It is only meant to suggest search engines which URLs to index first. Once a URL has been indexed, its priority becomes meaningless untill the post content is modified.','xml-sitemap-feed').'</p>';
		echo '
		</div>';
	}

	public function taxonomies_settings_field() {
		$options = parent::get_taxonomies();
		$active = parent::get_option('post_types');
		$skipped_all = true;

		echo '<div id="xmlsf_taxonomies">
			';
		foreach ( get_taxonomies(array('public'=>true),'objects') as $taxonomy ) {

			$skip = true;
			foreach ( $taxonomy->object_type as $post_type)
				if (!empty($active[$post_type]['active']) && $active[$post_type]['active'] == '1')
					$skip = false; 
			if ($skip) continue; // skip if none of the associated post types are active
			
			$skipped_all = false;
			$count = wp_count_terms( $taxonomy->name );
			echo '
				<label><input type="checkbox" name="xmlsf_taxonomies['.
				$taxonomy->name.']" id="xmlsf_taxonomies_'.
				$taxonomy->name.'" value="'.
				$taxonomy->name.'"'.
				checked(in_array($taxonomy->name,$options), true, false).' /> '.
				$taxonomy->label.'</label> ('.
				$count.')<br />';
		}
		
		if ($skipped_all)
			echo '
		<p style="color: red" class="error">'.__('No taxonomies available for the currently included post types.','xml-sitemap-feed').'</p>';

		echo '
		<p class="description">' . __('Note:','xml-sitemap-feed').' '.__('It is generally not recommended to include taxonomy pages, unless their content brings added value. For example, when you use category descriptions with information that is not present elsewhere on your site or if taxonomy pages list posts with an excerpt that is different from, but complementary to the post content. In these cases you might consider including certain taxonomies. Otherwise, you might even consider disallowing indexation by adding specific robots.txt rules below.','xml-sitemap-feed');
		echo '</p>
		</div>';
	}

	public function ping_settings_field() {
		$options = parent::get_ping();
		$pings = parent::get_pings();
		// search engines
		$se = array(
			'google' => __('Google','xml-sitemap-feed'),
			'bing' => __('Bing','xml-sitemap-feed'),
			);

		echo '
		<div id="xmlsf_ping">
			';
		foreach ( $options as $name => $values ) {

			echo '
				<input type="hidden" name="xmlsf_ping['.
				$name.'][uri]" value="'.
				$values['uri'].'" />';

			echo '
				<label><input type="checkbox" name="xmlsf_ping['.
				$name.'][active]" id="xmlsf_ping_'.
				$name.'" value="1"'.
				checked( !empty($options[$name]["active"]), true, false).' /> '.
				$se[$name].'</label>';
			
			echo ' <span class="description">';
			if (isset($pings[$name]))
				foreach ((array)$pings[$name] as $pretty => $time)
					echo sprintf(__('Successfully pinged for %1$s on %2$s GMT.','xml-sitemap-feed'),$pretty, $time).' ';
			echo '</span><br />';
		}

		echo '
		</div>';
	}

	public function robots_settings_field() {
		echo '<label for="xmlsf_robots">'.sprintf(__('Rules to append to %s generated by WordPress.','xml-sitemap-feed'),'<a href="'.trailingslashit(get_bloginfo('url')).'robots.txt" target="_blank">robots.txt</a>').'</label> <span style="color: red" class="error">'.__('Only add rules here when you know what you are doing, otherwise you might break search engine access to your site.','xml-sitemap-feed').'</span><br /><textarea name="xmlsf_robots" id="xmlsf_robots" class="large-text" cols="50" rows="5" />'.esc_attr( parent::get_robots() ).'</textarea><p class="description">'.__('Note:','xml-sitemap-feed').' '.__('These rules will not have effect when you are using a static robots.txt file.','xml-sitemap-feed').'</p>';
	}

	public function reset_settings_field() {

		echo '
		<label><input type="checkbox" name="xmlsf_sitemaps[reset]" value="1" /> '.
				__('Clear all XML Sitemap Feed options from the database and start fresh with the default settings.','xml-sitemap-feed').'</label>';
		echo '
		<p class="description">'.__('Note:','xml-sitemap-feed').' '.sprintf(__('Disabling and reenabling the %s plugin will have the same effect.','xml-sitemap-feed'),__('XML Sitemap & Google News Feeds','xml-sitemap-feed')).'</p>';
	}

	//sanitize callback functions
	
	public function sanitize_robots_settings($new) {
		return trim(strip_tags($new));
	}
	
	public function sanitize_sitemaps_settings($new) {
		$old = parent::get_sitemaps();
		if (isset($new['reset']) && $new['reset'] == '1') // if reset is checked, set transient to clear all settings
			set_transient('xmlsf_clear_settings','');
		elseif ($old != $new) // when sitemaps are added or removed, set transient to flush rewrite rules
			set_transient('xmlsf_flush_rewrite_rules','');
		return $new;
	}
	
	public function sanitize_post_types_settings( $new = array() ) {
		$old = parent::get_post_types();
		$defaults = parent::defaults('post_types');
		$sanitized = $new;
		
		foreach ($new as $post_type => $settings) {

			// when post types are (de)activated, set transient to flush rewrite rules
			if ( ( !empty($old[$post_type]['active']) && empty($settings['active']) ) || ( empty($old[$post_type]['active']) && !empty($settings['active']) ) )	
				set_transient('xmlsf_flush_rewrite_rules','');
			
			if ( isset($settings['priority']) && is_array($settings['priority']) ) {
				if ( empty($settings['priority']['calculation']) 
					|| !in_array( $settings['priority']['calculation'], array('dynamic','static') ) ) 
					$sanitized[$post_type]['priority']['calculation'] = $defaults[$post_type]['priority']['calculation'];
				if (is_numeric($settings['priority']['value'])) {
					if ($settings['priority']['value'] <= 0)
						$sanitized[$post_type]['priority']['value'] = '0.1';
					elseif ($settings['priority']['value'] >= 1)
						$sanitized[$post_type]['priority']['value'] = '0.9';
				} else {
					$sanitized[$post_type]['priority']['value'] = $defaults[$post_type]['priority']['value'];
				}
			} else {
				$sanitized[$post_type]['priority'] = $defaults[$post_type]['priority'];
			}
		}
		return $sanitized;
	}

	public function sanitize_taxonomies_settings($new) {
		$old = parent::get_taxonomies();
		if ($old != $new) // when taxonomy types are added or removed, set transient to flush rewrite rules
			set_transient('xmlsf_flush_rewrite_rules','');
		return $new;
	}

	public function sanitize_ping_settings($new) {
		return $new;
	}
	
	function add_action_link( $links ) {
		$settings_link = '<a href="' . admin_url('options-reading.php') . '#xmlsf">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link ); 
		return $links;
	}

	/**
	* CONSTRUCTOR
	*/

	function XMLSitemapFeed() {
		//constructor in php4
		$this->__construct(); // just call the php5 one.
	}
	
	function __construct() {
		
		// SETTINGS
		add_settings_section('xmlsf_main_section', '<a name="xmlsf"></a>'.__('XML Sitemaps','xml-sitemap-feed'), array($this,'privacy_settings_section'), 'reading');
		// sitemaps
		register_setting('reading', 'xmlsf_sitemaps', array($this,'sanitize_sitemaps_settings') );
		add_settings_field('xmlsf_sitemaps', __('Enable XML sitemaps','xml-sitemap-feed'), array($this,'sitemaps_settings_field'), 'reading', 'xmlsf_main_section');

		$sitemaps = parent::get_sitemaps();
		if (isset($sitemaps['sitemap'])) {
			// post_types
			register_setting('reading', 'xmlsf_post_types', array($this,'sanitize_post_types_settings') );
			add_settings_field('xmlsf_post_types', __('Include post types','xml-sitemap-feed'), array($this,'post_types_settings_field'), 'reading', 'xmlsf_main_section');
			// taxonomies
			register_setting('reading', 'xmlsf_taxonomies', array($this,'sanitize_taxonomies_settings') );
			add_settings_field('xmlsf_taxonomies', __('Include taxonomies','xml-sitemap-feed'), array($this,'taxonomies_settings_field'), 'reading', 'xmlsf_main_section');
			// pings
			register_setting('reading', 'xmlsf_ping', array($this,'sanitize_ping_settings') );
			add_settings_field('xmlsf_ping', __('Ping on Publish','xml-sitemap-feed'), array($this,'ping_settings_field'), 'reading', 'xmlsf_main_section');
		}
		
		//robots only when permalinks are set
		if(''!=get_option('permalink_structure')) {
			register_setting('reading', 'xmlsf_robots', array($this,'sanitize_robots_settings') );
			add_settings_field('xmlsf_robots', __('Additional robots.txt rules','xml-sitemap-feed'), array($this,'robots_settings_field'), 'reading', 'xmlsf_main_section');
		}

		add_settings_field('xmlsf_reset', __('Reset XML sitemaps','xml-sitemap-feed'), array($this,'reset_settings_field'), 'reading', 'xmlsf_main_section');
	
		add_filter('plugin_action_links_' . XMLSF_PLUGIN_BASENAME, array($this, 'add_action_link') );
	}

}

/* ----------------------
*      INSTANTIATE
* ---------------------- */

if ( class_exists('XMLSitemapFeed') )
	$xmlsf_admin = new XMLSF_Admin();

