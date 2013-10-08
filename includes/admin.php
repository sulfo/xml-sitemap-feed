<?php
/* ------------------------------
 *      XMLSF Admin CLASS
 * ------------------------------ */
 
 class XMLSF_Admin extends XMLSitemapFeed {

	/**
	* SETTINGS
	*/

	// TODO refer to support forum !

	public function sitemaps_settings_field() {
		$options = parent::get_sitemaps();
		$disabled = ('1' == get_option('blog_public')) ? false : true;
		$prefix = parent::prefix();

		echo '<fieldset id="xmlsf_sitemaps"><legend class="screen-reader-text">'.__('XML Sitemaps','xml-sitemap-feed').'</legend>
			<label><input type="checkbox" name="'.$prefix.'sitemaps[sitemap]" id="xmlsf_sitemaps_index" value="'.XMLSF_NAME.'" '.checked(isset($options['sitemap']), true, false).' '.disabled($disabled, true, false).' /> '.__('XML Sitemap Index','xml-sitemap-feed').'</label>';//xmlsf
		if (isset($options['sitemap']))
			echo '<span class="description"> &nbsp;&ndash;&nbsp; <a href="#xmlsf" id="xmlsf_link">'.translate('Settings').'</a> &nbsp;&ndash;&nbsp; <a href="'.trailingslashit(get_bloginfo('url')). ( ('' == get_option('permalink_structure')) ? '?feed=sitemap' : $options['sitemap'] ) .'" target="_blank">'.translate('View').'</a></span>';

		echo '<br />
			<label><input type="checkbox" name="'.$prefix.'sitemaps[sitemap-news]" id="xmlsf_sitemaps_news" value="'.XMLSF_NEWS_NAME.'" '.checked(isset($options['sitemap-news']), true, false).' '.disabled($disabled, true, false).' /> '.__('Google News Sitemap','xml-sitemap-feed').'</label>';
		if (isset($options['sitemap-news']))
			echo '<span class="description"> &nbsp;&ndash;&nbsp; <a href="#xmlnf" id="xmlnf_link">'.translate('Settings').'</a> &nbsp;&ndash;&nbsp; <a href="'.trailingslashit(get_bloginfo('url')). ( ('' == get_option('permalink_structure')) ? '?feed=sitemap-news' : $options['sitemap-news'] ) .'" target="_blank">'.translate('View').'</a></span>';

		echo '
		</fieldset>';
		echo '
    <script type="text/javascript">
        jQuery( document ).ready( function() {
            jQuery( "input[name=\'blog_public\']" ).on( \'change\', function() {
			jQuery("#xmlsf_sitemaps input").each(function() {
			  var $this = jQuery(this);
			  $this.attr("disabled") ? $this.removeAttr("disabled") : $this.attr("disabled", "disabled");
			});
			jQuery("#xmlsf_ping input").each(function() {
			  var $this = jQuery(this);
			  $this.attr("disabled") ? $this.removeAttr("disabled") : $this.attr("disabled", "disabled");
			});
            });
            jQuery( "#xmlsf_link" ).click( function(event) {
	     	        event.preventDefault();
	     	        jQuery("html, body").animate({
			  scrollTop: jQuery("a[name=\'xmlsf\']").offset().top - 30
			}, 1000);
	    });
            jQuery( "#xmlnf_link" ).click( function(event) {
	     	        event.preventDefault();
	     	        jQuery("html, body").animate({
			  scrollTop: jQuery("a[name=\'xmlnf\']").offset().top - 30
			}, 1000);
	    });
        });
    </script>';
	}

	/* PINGS */
	
	public function ping_settings_field() {
		$options = parent::get_ping();
		$defaults = parent::defaults('ping');
		$update_services = get_option('ping_sites');
		$prefix = parent::prefix();
		$names = array(
			'google' => array (
				'name' => __('Google','xml-sitemap-feed'),
				),
			'bing' => array (
				'name' => __('Bing & Yahoo','xml-sitemap-feed'),
				),
			'yandex' => array (
				'name' => __('Yandex','xml-sitemap-feed'),
				),
			'baidu' => array (
				'name' => __('Baidu','xml-sitemap-feed'),
				),
			'others' => array (
				'name' => __('Ping-O-Matic','xml-sitemap-feed'),
				)
			);
		foreach ( $names as $key => $values ) {
			if (array_key_exists($key,$defaults) && is_array($values))
				$defaults[$key] += $values;
		}
		echo '
		<fieldset id="xmlsf_ping"><legend class="screen-reader-text">'.translate('Update Services').'</legend>
			';
		foreach ( $defaults as $key => $values ) {

			echo '
				<input type="hidden" name="'.$prefix.'ping['.
				$key.'][uri]" value="'.
				$values['uri'].'" />';
			if ( isset($values['type']) && $values['type'] == 'RPC' ) {
				$active = ( strpos($update_services,untrailingslashit($values['uri'])) === false ) ? false : true;
			} else {
				$active = !empty($options[$key]['active']) ? true : false;
			}
			echo '
				<label><input type="checkbox" name="'.$prefix.'ping['.
				$key.'][active]" id="xmlsf_ping_'.
				$key.'" value="1"'.
				checked( $active, true, false).' /> ';
			echo isset($names[$key]) && !empty($names[$key]['name']) ? $names[$key]['name'] : $key ;
			echo '</label>';
			
			echo ' <span class="description">';
			if (!empty($options[$key]['pong']))
				foreach ((array)$options[$key]['pong'] as $pretty => $time)
					echo sprintf(__('Successfully pinged for %1$s on %2$s GMT.','xml-sitemap-feed'),$pretty, $time).' ';
			echo '</span><br />';
		}

		echo '
		</fieldset>';
	}

	public function sanitize_ping_settings($new) {
		$defaults = parent::defaults('ping');
		$old = parent::get_option('ping');
		$sanitized = array();
		$update_services = get_option('ping_sites');
		$update_services_new = $update_services;

		foreach ($defaults as $key => $values) {
			if(!isset($new[$key]))
				continue;

			if ( isset($values['type']) && $values['type']=='RPC' && isset($values['uri']) ) {
				// did we toggle the option?
				$changed = true;
				if ( isset($old[$key]) ) {
					$old_active = isset($old[$key]['active']) ? $old[$key]['active'] : '';
					$new_active = isset($new[$key]['active']) ? $new[$key]['active'] : '';
					if ( $old_active == $new_active )
						$changed = false;
				}
				
				if ( $changed ) { 
					// then change the ping_sites list according to option
					if ( !empty($new[$key]['active']) && strpos($update_services,untrailingslashit($values['uri'])) === false )
						$update_services_new .= "\n" . $values['uri'];
					elseif ( empty($new[$key]['active']) && strpos($update_services,untrailingslashit($values['uri'])) !== false )
						$update_services_new = str_replace(array(trailingslashit($values['uri']),untrailingslashit($values['uri'])),'',$update_services_new);
				} else { 
					// or change the option according to ping_sites
					if ( strpos($update_services,untrailingslashit($values['uri'])) !== false )
						$new[$key]['active'] = '1';
					else
						unset($new[$key]['active']);
				}
			} 
			if ( is_array($new[$key]) ) {
				$sanitized += array( $key => $new[$key] );
			}
		}

		if($update_services_new != $update_services)
			update_option('ping_sites',$update_services_new);

		return $sanitized;
	}

	/* ROBOTS */
	
	public function robots_settings_field() {
		echo '<label>'.sprintf(__('Rules that will be appended to the %s generated by WordPress:','xml-sitemap-feed'),'<a href="'.trailingslashit(get_bloginfo('url')).'robots.txt" target="_blank">robots.txt</a>').'<br /><textarea name="'.parent::prefix().'robots" id="xmlsf_robots" class="large-text" cols="50" rows="6" />'.esc_attr( parent::get_robots() ).'</textarea></label>
		<p class="description">'.__('These rules will not have effect when you are using a static robots.txt file.','xml-sitemap-feed').'<br /><span style="color: red" class="error">'.__('Only add rules here when you know what you are doing, otherwise you might break search engine access to your site.','xml-sitemap-feed').'</span></p>';
	}

	public function reset_settings_field() {
		echo '
		<label><input type="checkbox" name="'.parent::prefix().'sitemaps[reset]" value="1" /> '.
				__('Clear all XML Sitemap Feed options from the database and start fresh with the default settings.','xml-sitemap-feed').'</label>';
		echo '
		<p class="description">'.sprintf(__('Disabling and reenabling the %s plugin will have the same effect.','xml-sitemap-feed'),__('XML Sitemap & Google News Feeds','xml-sitemap-feed')).'</p>';
	}

	/**
	* XML SITEMAP SECTION
	*/
	
	public function xml_sitemap_settings() {
		echo '<p><a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=XML%20Sitemap%20Feeds&item_number='.XMLSF_VERSION.'&no_shipping=0&tax=0&charset=UTF%2d8" title="'.
		sprintf(__('Donate to keep the free %s plugin development & support going!','xml-sitemap-feed'),__('XML Sitemap & Google News Feeds','xml-sitemap-feed')).'"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" style="border:none;float:right;margin:4px 0 0 10px" alt="'.
		sprintf(__('Donate to keep the free %s plugin development & support going!','xml-sitemap-feed'),__('XML Sitemap & Google News Feeds','xml-sitemap-feed')).'" width="92" height="26" /></a>'.
		sprintf(__('These settings control the XML Sitemaps generated by the %s plugin.','xml-sitemap-feed'),__('XML Sitemap & Google News Feeds','xml-sitemap-feed')).' '.
		sprintf(__('For ping options, go to %s.','xml-sitemap-feed'),'<a href="options-writing.php">'.__('Writing Settings').'</a>').'</p>';
	}
	
	public function post_types_settings_field() {
		$options = parent::get_post_types();
		$defaults = parent::defaults('post_types');
		$prefix = parent::prefix();
		$do_note = false;

		$post_types = get_post_types(array('public'=>true),'objects');
		if ( !is_array($post_types) || is_wp_error($post_types) )
			return;
			
		echo '<fieldset id="xmlsf_post_types"><legend class="screen-reader-text">'.__('XML Sitemaps for post types','xml-sitemap-feed').'</legend>
			';
		foreach ( $post_types  as $post_type ) {
			// skip unallowed post types
			if (in_array($post_type->name,parent::disabled_post_types()))
				continue;
				
			$count = wp_count_posts( $post_type->name );
			
			echo '
				<input type="hidden" name="'.$prefix.'post_types['.
				$post_type->name.'][name]" value="'.
				$post_type->name.'" />';

			echo '
				<label><input type="checkbox" name="'.$prefix.'post_types['.
				$post_type->name.'][active]" id="xmlsf_post_types_'.
				$post_type->name.'" value="1" '.
				checked( !empty($options[$post_type->name]["active"]), true, false).' /> '.
				$post_type->label.'</label> ('.
				$count->publish.')';
			
			if (!empty($options[$post_type->name]['active'])) {
				
				echo ' &nbsp;&ndash;&nbsp; <span class="description"><a id="xmlsf_post_types_'.$post_type->name.'_link" href="#xmlsf_post_types_'.$post_type->name.'_settings">'.translate('Settings').'</a></span><br />
    <script type="text/javascript">
        jQuery( document ).ready( function() {
            jQuery("#xmlsf_post_types_'.$post_type->name.'_settings").hide();
            jQuery("#xmlsf_post_types_'.$post_type->name.'_link").click( function(event) {
            		event.preventDefault();
			jQuery("#xmlsf_post_types_'.$post_type->name.'_settings").toggle("slow");
	    });
        });
    </script>
    				<ul style="margin-left:18px" id="xmlsf_post_types_'.$post_type->name.'_settings">';

				
				if ( isset($defaults[$post_type->name]['archive']) ) {
					$archives = array (
								'yearly' => __('Year','xml-sitemap-feed'),
								'monthly' => __('Month','xml-sitemap-feed') 
								);
					$archive = !empty($options[$post_type->name]['archive']) ? $options[$post_type->name]['archive'] : $defaults[$post_type->name]['archive'];
					echo ' 
					<li><label>'.__('Split by','xml-sitemap-feed').' <select name="'.$prefix.'post_types['.
						$post_type->name.'][archive]" id="xmlsf_post_types_'.
						$post_type->name.'_archive">
						<option value="">'.translate('None').'</option>';
					foreach ($archives as $value => $translation)
						echo '
						<option value="'.$value.'" '.
						selected( $archive == $value, true, false).
						'>'.$translation.'</option>';
					echo '</select>
					</label> <span class="description"> '.__('Split by year if you experience errors or slow sitemaps. In very rare cases, split by month is needed.','xml-sitemap-feed').'</span></li>';
				}

				$priority_val = !empty($options[$post_type->name]['priority']) ? $options[$post_type->name]['priority'] : $defaults[$post_type->name]['priority'];
				echo ' 
					<li><label>'.__('Priority','xml-sitemap-feed').' <input type="number" step="0.1" min="0.1" max="0.9" name="'.$prefix.'post_types['.
					$post_type->name.'][priority]" id="xmlsf_post_types_'.
					$post_type->name.'_priority" value="'.$priority_val.'" class="small-text"></label> <span class="description">'.__('Priority can be overridden on individual posts.','xml-sitemap-feed').' *</span></li>';

				echo '
					<li><label><input type="checkbox" name="'.$prefix.'post_types['.
					$post_type->name.'][dynamic_priority]" value="1" '.
					checked( !empty($options[$post_type->name]['dynamic_priority']), true, false).' /> '.__('Automatically adjusts Priority according to relative age and comment count.','xml-sitemap-feed').'</label> <span class="description">'.__('Sticky posts will not be subject to reduction by age. Individual posts with fixed Priority will always keep that value.','xml-sitemap-feed').'</span></li>';
					
				echo '
					<li><label><input type="checkbox" name="'.$prefix.'post_types['.
					$post_type->name.'][update_lastmod_on_comments]" value="1" '.
					checked( !empty($options[$post_type->name]["update_lastmod_on_comments"]), true, false).' /> '.__('Update Lastmod and Changefreq on comments.','xml-sitemap-feed').'</label> <span class="description">'.__('Set this if discussion on your site warrants reindexation upon each new comment.','xml-sitemap-feed').'</li>';
										
				$image = isset($options[$post_type->name]['tags']['image']) ? $options[$post_type->name]['tags']['image'] : $defaults[$post_type->name]['tags']['image'];
				echo ' 
					<li><label>'.__('Add image tags for','xml-sitemap-feed').' <select name="'.$prefix.'post_types['.
						$post_type->name.'][tags][image]">
						<option value="">'.translate('None').'</option>
						<option value="featured" '.
						selected( $image == "featured", true, false).
						'>'.translate('Featured Image').'</option>
						<option value="attached" '.
						selected( $image == "attached", true, false).
						'>'.__('Attached images','xml-sitemap-feed').'</option>
					</select></label></li>
				
				</ul>';
			} else {
				echo '<br />';
			}
		}

		echo '
		<p class="description">* '.__('Priority settings do not affect ranking in search results in any way. They are only meant to suggest search engines which URLs to index first. Once a URL has been indexed, its Priority becomes meaningless until its Lastmod is updated.','xml-sitemap-feed').' <a href="#xmlsf_post_types_note_1_more" id="xmlsf_post_types_note_1_link">'.translate('[more]').'</a> 
		<span id="xmlsf_post_types_note_1_more">'.__('Maximum Priority (1.0) is reserved for the front page, individual posts and, when allowed, posts with high comment count.','xml-sitemap-feed').' '.__('Priority values are taken as relative values. Setting all to the same (high) value is pointless.','xml-sitemap-feed').'</span></p>
<script type="text/javascript">
jQuery( document ).ready( function() {
    jQuery("#xmlsf_post_types_note_1_more").hide();
    jQuery("#xmlsf_post_types_note_1_link").click( function(event) {
	event.preventDefault();
	jQuery("#xmlsf_post_types_note_1_link").hide();
	jQuery("#xmlsf_post_types_note_1_more").show("slow");
    });
});
</script>';
		echo '
		</fieldset>';
	}

	public function taxonomies_settings_field() {
		$options = parent::get_taxonomies();
		$active = parent::get_option('post_types');
		$output = '';

		foreach ( get_taxonomies(array('public'=>true),'objects') as $taxonomy ) {
			// skip unallowed post types
			if (in_array($taxonomy->name,parent::disabled_taxonomies()))
				continue;

			$skip = true;
			foreach ( $taxonomy->object_type as $post_type)
				if (!empty($active[$post_type]['active']) && $active[$post_type]['active'] == '1')
					$skip = false; 
			if ($skip) continue; // skip if none of the associated post types are active
			
			$count = wp_count_terms( $taxonomy->name );
			$output .= '
				<label><input type="checkbox" name="'.parent::prefix().'taxonomies['.
				$taxonomy->name.']" id="xmlsf_taxonomies_'.
				$taxonomy->name.'" value="'.
				$taxonomy->name.'"'.
				checked(in_array($taxonomy->name,$options), true, false).' /> '.
				$taxonomy->label.'</label> ('.
				$count.') ';

//			if ( in_array($taxonomy->name,$options) && empty($taxonomy->show_tagcloud) )
//				echo '<span class="description error" style="color: red">'.__('This taxonomy type might not be suitable for public use. Please check the urls in the taxonomy sitemap.','xml-sitemap-feed').'</span>';

			$output .= '
				<br />';
		}
		
		if ($output) {
			echo '
		<fieldset id="xmlsf_taxonomies"><legend class="screen-reader-text">'.__('XML Sitemaps for taxonomies','xml-sitemap-feed').'</legend>
			';

			echo $output;

			echo '
			<p class="description">'.__('It is generally not recommended to include taxonomy pages, unless their content brings added value.','xml-sitemap-feed').' <a href="#xmlsf_taxonomies_note_1_more" id="xmlsf_taxonomies_note_1_link">'.translate('[more]').'</a> 
			<span id="xmlsf_taxonomies_note_1_more">'.__('For example, when you use category descriptions with information that is not present elsewhere on your site or if taxonomy pages list posts with an excerpt that is different from, but complementary to the post content. In these cases you might consider including certain taxonomies. Otherwise, if you fear <a href="http://moz.com/learn/seo/duplicate-content">negative affects of duplicate content</a> or PageRank spread, you might even consider disallowing indexation of taxonomies.','xml-sitemap-feed').' '.
			sprintf(__('You can do this by adding specific robots.txt rules in the %s field above.','xml-sitemap-feed'),'<strong>'.__('Additional robots.txt rules','xml-sitemap-feed').'</strong>');
			echo '</span></p>
<script type="text/javascript">
jQuery( document ).ready( function() {
    jQuery("#xmlsf_taxonomies_note_1_more").hide();
    jQuery("#xmlsf_taxonomies_note_1_link").click( function(event) {
	event.preventDefault();
	jQuery("#xmlsf_taxonomies_note_1_link").hide();
	jQuery("#xmlsf_taxonomies_note_1_more").show("slow");
    });
});
</script>
		</fieldset>';
		} else {
			echo '
		<p style="color: red" class="error">'.__('No taxonomies available for the currently included post types.','xml-sitemap-feed').'</p>';
		}
	}

	public function custom_sitemaps_settings_field() {
		$lines = parent::get_custom_sitemaps();
		
		echo '<label>'.__('Additional XML Sitemaps to append to the main XML Sitemap Index:','xml-sitemap-feed').'<br />
			<textarea name="'.parent::prefix().'custom_sitemaps" id="xmlsf_custom_sitemaps" class="large-text" cols="50" rows="4" />'. implode("\n",$lines) .'</textarea></label>
		<p class="description">'.__('Add the full URL, including protocol (http/https) and domain, of any XML Sitemap that you want to append to the Sitemap Index. Start each URL on a new line.','xml-sitemap-feed').'<br /><span style="color: red" class="error">'.__('Only valid sitemaps are allowed in the Sitemap Index. Use your Google/Bing Webmaster Tools to verify!','xml-sitemap-feed').'</span></p>';
		
	}

	public function urls_settings_field() {
		$urls = parent::get_urls();
		$lines = array();
		
		if(!empty($urls)) {
			foreach($urls as $arr) {
				if(is_array($arr)) 
					$lines[] = implode(" ",$arr);
			}
		}
		
		echo '<label>'.__('Additional URLs to append in an extra XML Sitemap:','xml-sitemap-feed').'<br />
			<textarea name="'.parent::prefix().'urls" id="xmlsf_urls" class="large-text" cols="50" rows="4" />'. implode("\n",$lines) .'</textarea></label>
		<p class="description">'.__('Add the full URL, including protocol (http/https) and domain, of any (static) page that you want to append to the ones already included by WordPress. Optionally add a priority value between 0 and 1, separated with a space after the URL. Start each URL on a new line.','xml-sitemap-feed').'</p>';
		
	}

	public function domains_settings_field() {
		$default = parent::domain();
		$domains = (array) parent::get_option('domains');

		echo '<label>'.__('Additional domains to allow in the XML Sitemaps:','xml-sitemap-feed').'<br /><textarea name="'.parent::prefix().'domains" id="xmlsf_domains" class="large-text" cols="50" rows="4" />'. implode("\n",$domains) .'</textarea></label>
		<p class="description">'.sprintf(__('By default, only the domain %s as used in your WordPress site address is allowed. This means that all URLs that use another domain (custom URLs or using a plugin like Page Links To) are filtered from the XML Sitemap. However, if you are the verified owner of other domains in your Google/Bing Webmaster Tools account, you can include these in the same sitemap. Add these domains, without protocol (http/https) each on a new line. Note that if you enter a domain with www, all URLs without it or with other subdomains will be filtered.','xml-sitemap-feed'),'<strong>'.$default.'</strong>').'</p>';
		
	}


	/**
	* GOOGLE NEWS SITEMAP SECTION
	*/
	
	public function news_sitemap_settings() {
		echo '<p><a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=XML%20Sitemap%20Feeds&item_number='.XMLSF_VERSION.'&no_shipping=0&tax=0&charset=UTF%2d8" title="'.
		sprintf(__('Donate to keep the free %s plugin development & support going!','xml-sitemap-feed'),__('XML Sitemap & Google News Feeds','xml-sitemap-feed')).'"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" style="border:none;float:right;margin:4px 0 0 10px" alt="'.
		sprintf(__('Donate to keep the free %s plugin development & support going!','xml-sitemap-feed'),__('XML Sitemap & Google News Feeds','xml-sitemap-feed')).'" width="92" height="26" /></a>'.
		sprintf(__('These settings control the Google News Sitemap generated by the %s plugin.','xml-sitemap-feed'),__('XML Sitemap & Google News Feeds','xml-sitemap-feed')).' '.__('When you are done configuring and preparing your news content and you are convinced your site adheres to the <a href="https://support.google.com/news/publisher/answer/40787?ref_topic=2484652" target="_blank">Google News guidelines</a>, go ahead and <a href="https://support.google.com/news/publisher/troubleshooter/3179220?#ts=3179198" target="_blank">submit your site for inclusion</a>!','xml-sitemap-feed').' '.
		sprintf(__('For ping options, go to %s.','xml-sitemap-feed'),'<a href="options-writing.php">'.__('Writing Settings').'</a>').'</p>';

	}

	//TODO: publication name allow tag %category% ... post_types (+ exclusion per post or none + allow inclusion per post), limit to category ... 
	public function news_name_field() {
		$options = parent::get_option('news_tags');

		$name = !empty($options['name']) ? $options['name'] : '';
		echo '
			<input type="text" name="'.parent::prefix().'news_tags[name]" id="xmlsf_news_name" value="'.$name.'" class="regular-text"> <span class="description">'.sprintf(__('By default, the general %s setting will be used.','xml-sitemap-feed'),'<a href="options-general.php">'.translate('Site Title').'</a>').'</span>';
	}

	public function news_post_type_field() {
		$defaults = parent::defaults('news_tags');
		$options = parent::get_option('news_tags');
		$prefix = parent::prefix();

		$news_post_type = !empty($options['post_type']) ? $options['post_type'] : $defaults['post_type'];

		$post_types = get_post_types(array('publicly_queryable' =>true),'objects');
		if ( !is_array($post_types) || is_wp_error($post_types) )
			return;
			
		foreach ( $post_types as $post_type ) {
			// skip unallowed post types
			if ( !is_object($post_type) || in_array($post_type->name,parent::disabled_post_types()) )
				continue;			

			$checked = in_array($post_type->name,$news_post_type) || $news_post_type == 'any' ? true : false;
			$disabled = false;
			if ( isset($options['categories']) && is_array($options['categories']) ) {
				// need to disable all post types that do not have the category taxonomy
				$taxonomies = get_object_taxonomies($post_type->name,'names');
				if ( !in_array('category',(array)$taxonomies) ) {
					$disabled = true;
					$checked = false;
				}
			}
				
			echo '
			<label><input type="checkbox" name="'.$prefix.'news_tags[post_type][]" id="xmlsf_post_type_'.
				$post_type->name.'" value="'.$post_type->name.'" '.
				checked( $checked, true, false).' '.
				disabled( $disabled, true, false).' /> '.
				$post_type->label.'</label><br />';
		}
	}

	public function news_categories_field() {
		$options = parent::get_option('news_tags');		

		$all_categories = get_terms( 'category', array('hide_empty' => 0,'hierachical' => true) );
		$selected_categories = isset($options['categories']) && is_array($options['categories']) ? $options['categories'] : array();
		$count = count($all_categories);

		if ($count==0) {
			translate('No categories');
			return;
		}

		$size = $count < 15 ? $count : 15;
		echo '
		<label>'.__('Limit to posts in these post categories:','xml-sitemap-feed').'<br />
			<select multiple name="'.parent::prefix().'news_tags[categories][]" size="'.$size.'">';
		
		foreach($all_categories as $category) {
			$depth = count( explode( '%#%', get_category_parents($category, false, '%#%') ) ) - 2;
			$pad = str_repeat('&nbsp;', $depth * 3);

			$cat_name = apply_filters('list_cats', $category->name, $category);
			echo '
				<option class="depth-'.$depth.'" value="'.$category->term_id.'" '.
				selected( in_array($category->term_id,$selected_categories), true, false ).
				'>'.$pad.$cat_name.'</option>
			';
		}
		echo '</select>
		</label> 
		<p class="description">'.__('If you wish to limit posts that will feature in your News Sitemap to certain categories, select them here. Use the Ctrl/Cmd key plus click to select more than one or to deselect. If no categories are selected, posts of all categories will be included in your News Sitemap.','xml-sitemap-feed').'<br /><span style="color: red" class="error">'.__('Please be aware that limiting by post category will rule out all custom post types that do not use post categories, even if you selected them to be included (above).','xml-sitemap-feed').'</span></p>';
	}

	public function news_image_field() {
		$options = parent::get_option('news_tags');

		$image = !empty($options['image']) ? $options['image'] : '';
		echo '
		<label>'.__('Add image tags for','xml-sitemap-feed').' <select name="'.parent::prefix().'news_tags[image]">
			<option value="">'.translate('None').'</option>
			<option value="featured" '.
			selected( $image == "featured", true, false).
			'>'.translate('Featured Image').'</option>
			<option value="attached" '.
			selected( $image == "attached", true, false).
			'>'.__('Attached images','xml-sitemap-feed').'</option>
			';
		echo '</select></label> 
		<p class="description">'.__('Note: Google News prefers at most one image per article in the News Sitemap. If multiple valid images are specified, the crawler will have to pick one arbitrarily. Images in News Sitemaps should be in jpeg or png format.','xml-sitemap-feed').' <a href="https://support.google.com/news/publisher/answer/185541" target="_blank">'.translate('More information...').'</a></p>';
	}

	public function news_access_field() {
		$options = parent::get_option('news_tags');
		$prefix = parent::prefix();

		$access = !empty($options['access']) ? $options['access'] : '';
		$access_default = !empty($access['default']) ? $access['default'] : '';
		$access_password = !empty($access['password']) ? $access['password'] : '';
		echo '
		<fieldset id="xmlsf_news_access"><legend class="screen-reader-text">'.__('Access (&lt;access&gt; tag)','xml-sitemap-feed').'</legend>
			'.sprintf(__('The &lt;access&gt; tag specifies whether an article is available to all readers (%1$s), or only to those with a free (%2$s) or paid membership (%3$s) to your site.','xml-sitemap-feed'),translate('Public'),__('Registration','xml-sitemap-feed'),__('Subscription','xml-sitemap-feed')).'

		    <ul>';
			
		echo '
			<li><label>'.__('Tag normal posts as','xml-sitemap-feed').' <select name="'.$prefix.'news_tags[access][default]" id="xmlsf_news_tags_access_default">
				<option value="">'.translate('Public').'</option>
				<option value="Registration" '.selected( "Registration" == $access_default, true, false).'>'.__('Registration','xml-sitemap-feed').'</option>
				<option value="Subscription" '.selected( "Subscription" == $access_default, true, false).'>'.__('Subscription','xml-sitemap-feed').'</option>
			</select></label></li>';
		echo '
			
			<li><label>'.sprintf(__('Tag %s posts as','xml-sitemap-feed'),translate('Password Protected')).' <select name="'.$prefix.'news_tags[access][password]" id="xmlsf_news_tags_access_password">
				<option value="Registration" '.selected( "Registration" == $access_password, true, false).'>'.__('Registration','xml-sitemap-feed').'</option>
				<option value="Subscription" '.selected( "Subscription" == $access_password, true, false).'>'.__('Subscription','xml-sitemap-feed').'</option>
			</select></label></li>';
		echo '
		    </ul>
			
			<p class="description">'.__('Note: The &lt;access&gt; tag is required when applicable. Do not leave it to Public when your content is not.','xml-sitemap-feed').' <a href="https://support.google.com/news/publisher/answer/93992" target="_blank">'.translate('More information...').'</a></p>
		</fieldset>';
	}

	public function news_genres_field() {
		$options = parent::get_option('news_tags');
		$prefix = parent::prefix();
		$gn_genres = parent::gn_genres();
/*		$gn_genres_tr = array( 
				'gn-pressrelease' => __('Press Release','xml-sitemap-feed'), 
				'gn-satire' => __('Satire','xml-sitemap-feed'), 
				'gn-blog' => __('Blog','xml-sitemap-feed'), 
				'gn-oped' => __('Op-Ed','xml-sitemap-feed'), 
				'gn-opinion' => __('Opinion','xml-sitemap-feed'), 
				'gn-usergenerated' => __('User-Generated','xml-sitemap-feed') 
				);
*/
		$genres = !empty($options['genres']) ? $options['genres'] : array();
		$genres_default = !empty($genres['default']) ? $genres['default'] : '';
		echo '
		<fieldset id="xmlsf_news_genres"><legend class="screen-reader-text">'.__('Genres (&lt;genres&gt; tag)','xml-sitemap-feed').'</legend>
			'.__('The &lt;genres&gt; tag specifies one or more properties for an article, namely, whether it is a press release, a blog post, an opinion, an op-ed piece, user-generated content, or satire.','xml-sitemap-feed').' '.__('You can assign Google News genres when writing a new post.','xml-sitemap-feed');
		echo '<input type="hidden" name="'.$prefix.'news_tags[genres][active]" value="';
		echo !empty($genres['active']) ? $genres['active'] : '';
		echo '" />';
/*		echo '
			<p><label><input type="checkbox" name="'.$prefix.'news_tags[genres][active]" id="xmlsf_news_tags_genres" value="1"'.
				checked( !empty($genres['active']), true, false).' /> '
				.__('Use Google News genres','xml-sitemap-feed').'</label> </p>';
*/
		echo '
			<ul>
			<li><label>'.__('Default genre:','xml-sitemap-feed').' <select name="'.$prefix.'news_tags[genres][default]" id="xmlsf_news_tags_genres_default">
						<option value="">'.translate('None').'</option>';
		foreach ( $gn_genres as $slug => $name) {
			echo '
						<option value="'.$name.'" '.selected( $name == $genres_default, true, false ).'>'.$name.'</option>';
		}
		echo '
			</select></label></li>
			</ul>
			<p class="description">'.__('Note: The &lt;genres&gt; tag is required when applicable and restricted to the list provided above.','xml-sitemap-feed').' <a href="https://support.google.com/news/publisher/answer/93992" target="_blank">'.translate('More information...').'</a></p>
		</fieldset>';

	}
	
	public function news_keywords_field() {
		$options = parent::get_option('news_tags');
		$prefix = parent::prefix();

		$keywords = !empty($options['keywords']) ? $options['keywords'] : array();
		$keywords_from = !empty($keywords['from']) ? $keywords['from'] : '';
		echo '
		<fieldset id="xmlsf_news_keywords"><legend class="screen-reader-text">'.__('Topics (&lt;keywords&gt; tag)','xml-sitemap-feed').'</legend>
			'.__('The &lt;keywords&gt; tag is used to help classify the articles you submit to Google News by <strong>topic</strong>.','xml-sitemap-feed').'
			<ul>
			<li><label>'.sprintf(__('Use %s for topics.','xml-sitemap-feed'),' <select name="'.$prefix.'news_tags[keywords][from]" id="xmlsf_news_tags_keywords_from">
						<option value="">'.translate('None').'</option>
						<option value="category" '.selected( "category" == $keywords_from, true, false).'>'.translate('Categories').'</option>
						<option value="post_tag" '.selected( "post_tag" == $keywords_from, true, false).'>'.translate('Tags').'</option>
			</select>').'</label></li>';
		if ("category" != $keywords_from) {
			echo '
			<li><label>'.__('Default topic(s):','xml-sitemap-feed').' <input type="text" name="'.$prefix.'news_tags[keywords][default]" id="xmlsf_news_tags_keywords_default" value="';
			echo !empty($keywords['default']) ? $keywords['default'] : '';
			echo '" class="regular-text"></label> <span class="description">'.__('Separate with a comma.','xml-sitemap-feed').'</span></li>';
		}
		echo '
			</ul>
			<p class="description">'.__('Keywords may be drawn from, but are not limited to, the list of <a href="http://www.google.com/support/news_pub/bin/answer.py?answer=116037" target="_blank">existing Google News keywords</a>.','xml-sitemap-feed').'</p>
		</fieldset>';
	}
	
	public function news_locations_field() {
		$options = parent::get_option('news_tags');
		$prefix = parent::prefix();

		$locations = !empty($options['locations']) ? $options['locations'] : array();
		echo '
		<fieldset id="xmlsf_news_locations"><legend class="screen-reader-text">'.__('Locations (&lt;geo_locations&gt; tag)','xml-sitemap-feed').'</legend>
		'.__('The &lt;geo_locations&gt; tag is used identify the geographic location of your articles.','xml-sitemap-feed').' '.__('You can assign locations when writing a new post.','xml-sitemap-feed');
		echo '<input type="hidden" name="'.$prefix.'news_tags[locations][active]" value="';
		echo !empty($locations['active']) ? $locations['active'] : '';
		echo '" />';	
		echo '
			<ul>
			<li><label>'.__('Default location:','xml-sitemap-feed').' <input type="text" name="'.$prefix.'news_tags[locations][default]" id="xmlsf_news_tags_locations_default" value="';
		echo !empty($locations['default']) ? $locations['default'] : '';
		echo '" class="regular-text"></label> <span class="description">'.__('Separate with a comma.','xml-sitemap-feed').'</span></li>
			</ul>
			<p class="description">'.__('You should list location entities from smallest entity to largest. For example: <code>Detroit, Michigan, USA</code> or <code>Rhône-Alpes, France</code>.','xml-sitemap-feed').' <a href="https://support.google.com/news/publisher/answer/1662970" target="_blank">'.translate('More information...').'</a></p>
		</fieldset>';
	}


	//sanitize callback functions

	public function sanitize_robots_settings($new) {
		return trim(strip_tags((string)$new));
	}
	
	public function sanitize_sitemaps_settings($new) {
		$old = parent::get_sitemaps();

		if (isset($new['reset']) && $new['reset'] == '1') // if reset is checked, set transient to clear all settings
			set_transient('xmlsf_clear_settings','');

		if( '1' == get_option('blog_public') ) {
			if ($old != $new && !isset($new['reset'])) // when sitemaps are added or removed, set transient to flush rewrite rules
				set_transient('xmlsf_flush_rewrite_rules','');
		
			if (empty($old['sitemap-news']) && !empty($new['sitemap-news']))
				set_transient('xmlsf_create_genres','');
			
			$sanitized = $new;
		} else {
			$sanitized = $old;
		}

		return $sanitized;
	}
	
	public function sanitize_post_types_settings( $new = array() ) {
		$old = parent::get_post_types();
		$defaults = parent::defaults('post_types');
		$sanitized = $new;
		$flush = false;
		
		foreach ($new as $post_type => $settings) {

			// when post types are (de)activated, set transient to flush rewrite rules
			if ( ( !empty($old[$post_type]['active']) && empty($settings['active']) ) || ( empty($old[$post_type]['active']) && !empty($settings['active']) ) )
				$flush = true;

			if ( isset($settings['priority']) && is_numeric($settings['priority']) ) {
				$sanitized[$post_type]['priority'] = $this->sanitize_priority($settings['priority'],0.1,0.9);
			} else {
				$sanitized[$post_type]['priority'] = $defaults[$post_type]['priority'];
			}
		}
		
		if ($flush)
			set_transient('xmlsf_flush_rewrite_rules','');

		return $sanitized;
	}

	private function sanitize_priority($priority, $min = 0, $max = 1) {
			$priority = (float)$priority;
			if ($priority < $min || $priority === 0 )
				return (string)$min;
			elseif ($priority >= $max)
				return (string)$max;
			else
				return (string)$priority;
	}

	public function sanitize_taxonomies_settings($new) {
		$old = parent::get_taxonomies();

		if ($old != $new) // when taxonomy types are added or removed, set transient to flush rewrite rules
			set_transient('xmlsf_flush_rewrite_rules','');

		return $new;
	}

	public function sanitize_custom_sitemaps_settings($new) {
		$old = parent::get_custom_sitemaps();
		$callback = create_function('$a','return filter_var($a,FILTER_VALIDATE_URL);');
		$input_arr = explode("\n",trim(strip_tags((string)$new)));
		$sanitized = array();

		foreach ($input_arr as $line) {
			$line = filter_var(esc_url(trim($line)),FILTER_VALIDATE_URL,FILTER_FLAG_PATH_REQUIRED);
			if(!empty($line)) 
				$sanitized[] = $line;
		}

		return $sanitized;
	}

	public function sanitize_urls_settings($new) {
		$old = parent::get_urls();
		$input_arr = explode("\n",trim(strip_tags((string)$new)));
		$sanitized = array();
		$callback = create_function('$a','return filter_var($a,FILTER_VALIDATE_URL) || is_numeric($a);');

		foreach ($input_arr as $line) {
			if(empty($line)) 
				continue;

			$arr = array_values(array_filter(explode(" ",trim($line)),$callback));

			if(isset($arr[0])) {
				if(isset($arr[1]))
					$arr[1] = $this->sanitize_priority($arr[1]);
				else
					$arr[1] = '0.5';
				
				$sanitized[] = array( esc_url($arr[0]) , $arr[1] );
			}
		}

		if (empty($old)) {
			if (!empty($sanitized))
				set_transient('xmlsf_flush_rewrite_rules','');
		} else if (empty($sanitized)) {
			set_transient('xmlsf_flush_rewrite_rules','');		
		}

		return (!empty($sanitized)) ? $sanitized : '';
	}

	public function sanitize_domains_settings($new) {
		$default = parent::domain();
		$input = explode("\n",trim(strip_tags((string)$new)));
		$sanitized = array();

		foreach ($input as $line) {
			$line = trim($line);
			$parsed_url = parse_url(trim(filter_var($line,FILTER_SANITIZE_URL)));
			// Before PHP version 5.4.7, parse_url will return the domain as path when scheme is omitted so we do:
			$domain = trim(!empty($parsed_url['host']) ? $parsed_url['host'] : array_shift(array_filter(explode('/', $parsed_url['path'])))); 

			// filter out empties and default domain
			if(!empty($domain) && $domain !== $default && strpos($domain,".".$default) === false)
				$sanitized[] = $domain;
		}
		
		return (!empty($sanitized)) ? $sanitized : '';
	}

	public function sanitize_news_tags_settings($new) {
		return $new;
	}

	
	// action links
	
	public function add_action_link( $links ) {
		$settings_link = '<a href="' . admin_url('options-reading.php') . '#blog_public">' . translate('Settings') . '</a>';
		array_unshift( $links, $settings_link ); 
		return $links;
	}

	/**
	* META BOX
	*/

	/* Adds a box to the side column */
	public function add_meta_box() 
	{
		// Only include metaboxes on post types that are included
		foreach (parent::get_post_types() as $post_type) {
			if (isset($post_type["active"]))
				add_meta_box(
				    'xmlsf_section',
				    __( 'XML Sitemap', 'xml-sitemap-feed' ),
				    array($this,'meta_box'),
				    $post_type['name'],
				    'side'
				);
		}
	}

	public function meta_box($post) 
	{
		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'xmlsf_sitemap_nonce' );

		// The actual fields for data entry
		// Use get_post_meta to retrieve an existing value from the database and use the value for the form
		$value = get_post_meta( $post->ID, '_xmlsf_exclude', true );
		$priority = get_post_meta( $post->ID, '_xmlsf_priority', true );
		$disabled = '';
		
		// disable options and (visibly) set excluded to true for private posts
		if ( 'private' == $post->post_status ) {
			$disabled = ' disabled="disabled"';
			$value = true;
		} 
		
		// disable options and (visibly) set priority to 1 for front page
		if ( $post->ID == get_option('page_on_front') ) {
			$disabled = ' disabled="disabled"';
			$priority = '1'; // force excluded to true for private posts
		}
		
		echo '<p><label><input type="checkbox" name="xmlsf_exclude" id="xmlsf_exclude" value="1"'.checked(!empty($value), true, false).$disabled.' > ';
		_e('Exclude from XML Sitemap','xml-sitemap-feed');
		echo '</label></p>';

		echo '<p><label>';
		_e('Priority','xml-sitemap-feed');
		echo ' <input type="number" step="0.1" min="0" max="1" name="xmlsf_priority" id="xmlsf_priority" value="'.$priority.'" class="small-text"'.$disabled.'></label> <span class="description">';
		printf(__('Leave empty for automatic Priority as configured on %1$s > %2$s.','xml-sitemap-feed'),translate('Settings'),translate('Reading'));
		echo '</span></p>';
	}
  
	/* When the post is saved, save our meta data */
	function save_metadata( $post_id ) 
	{
		if ( !isset($post_id) )
			$post_id = (int)$_REQUEST['post_ID'];

		if ( !current_user_can( 'edit_post', $post_id ) || !isset($_POST['xmlsf_sitemap_nonce']) || !wp_verify_nonce($_POST['xmlsf_sitemap_nonce'], plugin_basename( __FILE__ )) )
			return;

		// _xmlsf_priority
		if ( isset($_POST['xmlsf_priority']) && is_numeric($_POST['xmlsf_priority']) ) {
			update_post_meta($post_id, '_xmlsf_priority', $this->sanitize_priority($_POST['xmlsf_priority']) );
		} else {
			delete_post_meta($post_id, '_xmlsf_priority');
		}
		
		// _xmlsf_exclude
		if ( isset($_POST['xmlsf_exclude']) && $_POST['xmlsf_exclude'] != '' ) {
			update_post_meta($post_id, '_xmlsf_exclude', $_POST['xmlsf_exclude']);
		} else {
			delete_post_meta($post_id, '_xmlsf_exclude');
		}
		
	}

	/**
	* CONSTRUCTOR
	*/

	function __construct() {
		$sitemaps = parent::get_sitemaps();
		$prefix = parent::prefix();
		
		// sitemaps
		register_setting('reading', $prefix.'sitemaps', array($this,'sanitize_sitemaps_settings') );
		add_settings_field($prefix.'sitemaps', __('Enable XML sitemaps','xml-sitemap-feed'), array($this,'sitemaps_settings_field'), 'reading');

		// robots rules only when permalinks are set
		$rules = get_option( 'rewrite_rules' );
		if( get_option('permalink_structure') && isset( $rules['robots\.txt$'] ) ) {
			register_setting('reading', $prefix.'robots', array($this,'sanitize_robots_settings') );
			add_settings_field($prefix.'robots', __('Additional robots.txt rules','xml-sitemap-feed'), array($this,'robots_settings_field'), 'reading');
		}

		if ( get_option('blog_public') ) {
			if ( is_multisite() && is_plugin_active_for_network(XMLSF_PLUGIN_BASENAME) )
				add_settings_field($prefix.'reset', __('Reset XML sitemaps','xml-sitemap-feed'), array($this,'reset_settings_field'), 'reading');
		
			if ( isset($sitemaps['sitemap']) ) {
				// XML SITEMAP SETTINGS
				add_settings_section('xml_sitemap_section', '<a name="xmlsf"></a>'.__('XML Sitemap','xml-sitemap-feed'), array($this,'xml_sitemap_settings'), 'reading');
				// post_types
				register_setting('reading', $prefix.'post_types', array($this,'sanitize_post_types_settings') );
				add_settings_field($prefix.'post_types', __('Include post types','xml-sitemap-feed'), array($this,'post_types_settings_field'), 'reading', 'xml_sitemap_section');
				// taxonomies
				register_setting('reading', $prefix.'taxonomies', array($this,'sanitize_taxonomies_settings') );
				add_settings_field($prefix.'taxonomies', __('Include taxonomies','xml-sitemap-feed'), array($this,'taxonomies_settings_field'), 'reading', 'xml_sitemap_section');
				// custom domains
				register_setting('reading', $prefix.'domains', array($this,'sanitize_domains_settings') );
				add_settings_field($prefix.'domains', __('Allowed domains','xml-sitemap-feed'), array($this,'domains_settings_field'), 'reading', 'xml_sitemap_section');
				// custom urls
				register_setting('reading', $prefix.'urls', array($this,'sanitize_urls_settings') );
				add_settings_field($prefix.'urls', __('Include custom URLs','xml-sitemap-feed'), array($this,'urls_settings_field'), 'reading', 'xml_sitemap_section');
				// custom sitemaps
				register_setting('reading', $prefix.'custom_sitemaps', array($this,'sanitize_custom_sitemaps_settings') );
				add_settings_field($prefix.'custom_sitemaps', __('Include custom XML Sitemaps','xml-sitemap-feed'), array($this,'custom_sitemaps_settings_field'), 'reading', 'xml_sitemap_section');

				// POST META BOX
				add_action( 'add_meta_boxes', array($this,'add_meta_box') );
				add_action( 'save_post', array($this,'save_metadata') );
			}

			if ( isset($sitemaps['sitemap-news']) ) {
				// XML SITEMAP SETTINGS
				add_settings_section('news_sitemap_section', '<a name="xmlnf"></a>'.__('Google News Sitemap','xml-sitemap-feed'), array($this,'news_sitemap_settings'), 'reading');

				// tags
				register_setting('reading', $prefix.'news_tags', array($this,'sanitize_news_tags_settings') );
				add_settings_field($prefix.'news_name', '<label for="xmlsf_news_name">'.__('Publication name','xml-sitemap-feed').'</label>', array($this,'news_name_field'), 'reading', 'news_sitemap_section');
				add_settings_field($prefix.'news_post_type', __('Include post types','xml-sitemap-feed'), array($this,'news_post_type_field'), 'reading', 'news_sitemap_section');
				add_settings_field($prefix.'news_categories', translate('Categories'), array($this,'news_categories_field'), 'reading', 'news_sitemap_section');
				add_settings_field($prefix.'news_image', translate('Images'), array($this,'news_image_field'), 'reading', 'news_sitemap_section');
				add_settings_field($prefix.'news_access', __('Access (&lt;access&gt; tag)','xml-sitemap-feed'), array($this,'news_access_field'), 'reading', 'news_sitemap_section');
				add_settings_field($prefix.'news_genres', __('Genres (&lt;genres&gt; tag)','xml-sitemap-feed'), array($this,'news_genres_field'), 'reading', 'news_sitemap_section');
				add_settings_field($prefix.'news_keywords', __('Topics (&lt;keywords&gt; tag)','xml-sitemap-feed'), array($this,'news_keywords_field'), 'reading', 'news_sitemap_section');
				add_settings_field($prefix.'news_locations', __('Locations (&lt;geo_locations&gt; tag)','xml-sitemap-feed'), array($this,'news_locations_field'), 'reading', 'news_sitemap_section');
			}

			if ( isset($sitemaps['sitemap']) || isset($sitemaps['sitemap-news']) ) {
					register_setting('writing', $prefix.'ping', array($this,'sanitize_ping_settings') );
					add_settings_field($prefix.'ping', translate('Update Services'), array($this,'ping_settings_field'), 'writing');
			}
		}

		// ACTION LINK
		add_filter('plugin_action_links_' . XMLSF_PLUGIN_BASENAME, array($this, 'add_action_link') );
	}

}

/* ----------------------
*      INSTANTIATE
* ---------------------- */

if ( class_exists('XMLSitemapFeed') )
	$xmlsf_admin = new XMLSF_Admin();
