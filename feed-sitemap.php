<?php
/**
 * XML Sitemap Feed Template for displaying an XML Sitemap feed.
 *
 * @package XML Sitemap Feed plugin for WordPress
 */

//header('HTTP/1.1 200 OK'); or header("Status: 200"); or status_header('200'); or add_filter( 'status_header', '...'); ??
status_header('200');
header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

// NOTE 1: feed_content_type('rss-http') should output text/xml which we need for our XML Sitemap
// NOTE 2: not using WP_PLUGIN_URL to avoid problems when installed in /mu-plugins/
// alternative to explore: echo plugins_url('sitemap.xsl.php', __FILE__)
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?>
<?xml-stylesheet type="text/xsl" href="'.get_option('home').'/'.str_replace(ABSPATH,"", XMLSF_PLUGIN_DIR).'/sitemap.xsl.php?v='.XMLSF_VERSION.'&amp;url='.get_option('home').'/'.str_replace(ABSPATH,"", XMLSF_PLUGIN_DIR).'"?>
<!-- generated-on="'.date('Y-m-d\TH:i:s+00:00').'" -->
<!-- generator="XML Sitemap Feed plugin for WordPress" -->
<!-- generator-url="http://4visions.nl/wordpress-plugins/xml-sitemap-feed/" -->
<!-- generator-version="'.XMLSF_VERSION.'" -->
';

// presets are changable; please read comments:
$max_priority = 1.0;	// Maximum priority value for any URL in the sitemap; set to any other value between 0 and 1.
$min_priority = 0;	// Minimum priority value for any URL in the sitemap; set to any other value between 0 and 1.
			// NOTE: Changing these values will influence each URL's priority. Priority values are taken by 
			// search engines to represent RELATIVE priority within the site domain. Forcing all URLs
			// to a priority of above 0.5 or even fixing them all to 1.0 - for example - is useless.
$frontpage_priority = 1.0;	// Your front page priority, usually the same as max priority but if you have any reason
				// to change it, please be my guest; set to any other value between 0 and 1.

$maxURLS = 50000;	// maximum number of URLs allowed in any sitemap.
$level_weight = 0.1;	// Makes a sub-page loose 10% for each level; set to any other value between 0 and 1.
$month_weight = 0.1;	// Fall-back value normally ignored by automatic priority calculation, which
			// makes a post loose 10% of priority monthly; set to any other value between 0 and 1.

// editing below here is not advised!

global $wp_query;
// change the main query
query_posts( array(
	'posts_per_page' => -1,
	'post_type' => 'any', 
	'post_status' => 'publish', 
	'caller_get_posts' => '1'
	)
); 

// force is_feed condition to true to allow WP Super Cache to include the sitemap in its feeds cache
$wp_query->is_feed = true;

// force is_404 condition to fals to prevent WP treating the sitemap query as a 404 when the site
// has no posts, only pages
$wp_query->is_404 = false;

// setup site variables
$_post_count = wp_count_posts('post');
$_page_count = wp_count_posts('page');
$_totalcommentcount = wp_count_comments();

$lastmodified_gmt = get_lastmodified('GMT'); // last posts or page modified date
$lastmodified = mysql2date('U',$lastmodified_gmt); // last posts or page modified date in Unix seconds
$firstmodified = mysql2date('U',get_firstmodified('GMT')); // uses new get_firstmodified() function defined in xml-sitemap.php !

// calculated presets
if ($_totalcommentcount->approved > 0)
	$comment_weight =  ($max_priority - $min_priority) / $_totalcommentcount->approved;
else
	$comment_weight = 0;

if ($_post_count->publish > $_page_count->publish) { // site emphasis on posts
	$post_priority = 0.8;
	$page_priority = 0.4;
} else { // site emphasis on pages
	$post_priority = 0.4;
	$page_priority = 0.8;
}

if ( $lastmodified > $firstmodified ) // valid blog age found ?
	$age_weight = ($post_priority - $min_priority) / ($lastmodified - $firstmodified); // calculate relative age weight
else
	$age_weight = $month_weight / 2629744 ; // else just do 10% per month (that's a month in seconds)

// prepare counter to limit the number of URLs to the absolute max of 50.000
$counter = 1;

// start with the main URL
?>
<urlset	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
	xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc><?php bloginfo_rss('url') ?>/</loc>
		<lastmod><?php echo mysql2date('Y-m-d\TH:i:s+00:00', $lastmodified_gmt, false); ?></lastmod>
		<changefreq>daily</changefreq>
		<priority>1.0</priority>
	</url>
<?php
// and loop away!
if ( have_posts() ) : while ( have_posts() && $counter < $maxURLS ) : the_post();

	$thispostmodified_gmt = $post->post_modified_gmt; // post GMT timestamp
	$thispostmodified = mysql2date('U',$thispostmodified_gmt); // post Unix timestamp
	$lastcomment = array();

	if ($post->comment_count && $post->comment_count > 0) {
		$lastcomment = get_comments( array(
						'status' => 'approve',
						'$number' => 1,
						'post_id' => $post->ID,
						) );
		$lastcommentsdate = mysql2date('U',$lastcomment[0]->comment_date_gmt); // last comment timestamp
		if ( $lastcommentsdate > $thispostmodified ) {
			$thispostmodified = $lastcommentsdate; // replace post with comment Unix timestamp
			$thispostmodified_gmt = $lastcomment[0]->comment_date_gmt; // and replace modified GMT timestamp
		}
	}
	$lastactivityage = (gmdate('U') - $thispostmodified); // post age

	if($post->post_type == "page") {
		if ($post->ID == get_option('page_on_front')) // check if we are not doing the front page twice
			continue;
		
		if (!is_array($post->ancestors)) { // $post->ancestors seems always empty (something to do with http://core.trac.wordpress.org/ticket/10381 ?) so we probably need to do it ourselves... 
			$page_obj = $post;
			$ancestors = array();
			while($page_obj->post_parent!=0) {
				$page_obj = get_page($page_obj->post_parent);
				$ancestors[] = $page_obj->ID;
			}
		} else { 
			$ancestors = $post->ancestors;
		}
		$offset = (($post->comment_count - $average_commentcount) * $comment_weight) - (count($ancestors) * $level_weight);
		$priority = $page_priority + $offset;
	} else {
		$offset = (($post->comment_count - $average_commentcount) * $comment_weight) - (($lastmodified - $thispostmodified) * $age_weight);
		$priority = $post_priority + $offset;
	}
	// trim priority
	$priority = ($priority > $max_priority) ? $max_priority : $priority;
	$priority = ($priority < $min_priority) ? $min_priority : $priority;
?>
	<url>
		<loc><?php the_permalink() ?></loc>
		<lastmod><?php echo mysql2date('Y-m-d\TH:i:s+00:00', $thispostmodified_gmt, false) ?></lastmod>
<?php 	if(($lastactivityage/86400) < 7) { // last activity less than 1 week old ?>
		<changefreq>daily</changefreq>
<?php	} else if(($lastactivityage/604800) < 12) { // last activity between 1 and 12 weeks old ?>
		<changefreq>weekly</changefreq>
<?php	} else if(($lastactivityage/604800) < 52) { // last activity between 12 and 52 weeks old ?>
		<changefreq>monthly</changefreq>
<?php 	} else { ?>
		<changefreq>yearly</changefreq>
<?php	} ?>
		<priority><?php echo round($priority,1) ?></priority>
	</url>
<?php 
	$counter++;

endwhile; endif; 
?>
</urlset>
