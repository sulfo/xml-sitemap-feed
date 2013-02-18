<?php
/**
 * Google News Sitemap Feed Template
 *
 * @package XML Sitemap Feed plugin for WordPress
 */

status_header('200'); // force header('HTTP/1.1 200 OK') for sites without posts
// TODO test if we can do without it
header('Content-Type: text/xml; charset=' . get_bloginfo('charset'), true);

echo '<?xml version="1.0" encoding="'.get_bloginfo('charset').'"?>
<?xml-stylesheet type="text/xsl" href="' . plugins_url('/sitemap.xsl.php',XMLSF_PLUGIN_DIR . '/feed-sitemap.php') . '?ver=' . XMLSF_VERSION . '"?>
<!-- generated-on="'.date('Y-m-d\TH:i:s+00:00').'" -->
<!-- generator="XML & Google News Sitemap Feed plugin for WordPress" -->
<!-- generator-url="http://4visions.nl/wordpress-plugins/xml-sitemap-feed/" -->
<!-- generator-version="'.XMLSF_VERSION.'" -->

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 
		http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
';

// PRESETS are changable -- please read comments:

$max_priority = 0.7;	// Maximum priority value for any URL in the sitemap; set to any other value between 0 and 1.
$min_priority = 0.2;	// Minimum priority value for any URL in the sitemap; set to any other value between 0 and 1.
			// NOTE: Changing these values will influence each URL's priority. Priority values are taken by 
			// search engines to represent RELATIVE priority within the site domain. Forcing all URLs
			// to a priority of above 0.5 or even fixing them all to 1.0 - for example - is useless.

$level_weight = 0.1;	// TODO Makes a sub-term gain or loose priority for each level; set to any other value between 0 and 1.

$tax_obj = get_taxonomy(get_query_var('taxonomy'));
foreach ( $tax_obj->object_type as $post_type) {
	echo "<!-- $post_type -->
";
	$_post_count = wp_count_posts($post_type);
	$postcount += $_post_count->publish;
}

//$_terms_count = wp_count_terms(get_query_var('taxonomy'));
//$average_count = $_post_count->publish / $_terms_count;

// TODO find a way around term language filtering by Polylang !!!!!!!

/*
 Solution on http://wordpress.org/support/topic/query-all-language-terms?replies=6#post-3415389 ?
 Does not work anymore ;(
*/

$terms = get_terms( get_query_var('taxonomy'), array(
						'orderby' => 'count',
						'order' => 'DESC',
						//'lang' => 'en,nl',
						'hierachical' => 0,
						'number' => 50000 ) );

if ( $terms ) : 

    foreach ( $terms as $term ) : 
    
    // calculate priority based on number of posts
    // or maybe take child taxonomy terms into account.?
	//pad_counts 
	// (boolean) If true, count all of the children along with the $terms. 
	// 1 (true) 
	// 0 (false) - Default

	$priority = $min_priority + ( $term->count / ( $postcount / 2 ) );
	$priority = ($priority > $max_priority) ? $max_priority : $priority;
	
	// get the latest post in this taxonomy item, to use its post_date as lastmod
	$posts = get_posts ( array(
		 	'numberposts' => 1, 
			'no_found_rows' => true, 
			'update_post_meta_cache' => false, 
			'update_post_term_cache' => false, 
			'update_cache' => false,
			'tax_query' => array(
					array(
						'taxonomy' => $term->taxonomy,
						'field' => 'slug',
						'terms' => $term->slug
					)
				)
			)
		);
	?>
	<url>
		<loc><?php echo get_term_link( $term ); ?></loc>
	 	<priority><?php echo number_format($priority,1) ?></priority>
		<lastmod><?php echo mysql2date('Y-m-d\TH:i:s+00:00', $posts[0]->post_date_gmt, false); ?></lastmod>
		<changefreq><?php
			$lastactivityage = (gmdate('U') - mysql2date('U', $posts[0]->post_date_gmt));
		 	if(($lastactivityage/86400) < 1) { // last activity less than 1 day old 
		 		echo 'hourly';
		 	} else if(($lastactivityage/86400) < 7) { // last activity less than 1 week old 
		 		echo 'daily';
		 	} else if(($lastactivityage/86400) < 30) { // last activity between 1 week and one month old 
		 		echo 'weekly';
		 	} else if(($lastactivityage/86400) < 365) { // last activity between 1 month and 1 year old 
		 		echo 'monthly';
		 	} else {
		 		echo 'yearly';
		 	} ?></changefreq>
	</url>
<?php 
    endforeach;
else : 
?>
	<url>
		<loc><?php echo esc_url( trailingslashit(home_url()) ); ?></loc>
	</url>
<?php
endif; 

?></urlset>
<?php
	echo '<!-- Queries executed '.get_num_queries().' | Posts total '.($_post_count->publish + $_page_count->publish);
	if(function_exists('memory_get_usage'))
		echo ' | Peak memory usage '.round(memory_get_peak_usage()/1024/1024,2).'M';
	echo ' -->';
?>
