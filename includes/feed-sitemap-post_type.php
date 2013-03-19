<?php
/**
 * XML Sitemap Feed Template for displaying an XML Sitemap feed.
 *
 * @package XML Sitemap Feed plugin for WordPress
 */

status_header('200'); // force header('HTTP/1.1 200 OK') even for sites without posts
header('Content-Type: text/xml; charset=' . get_bloginfo('charset'), true);

global $xmlsf;
$post_type = get_query_var('post_type');
foreach ( $xmlsf->get_do_tags($post_type) as $tag )
	$$tag = true;

echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . '"?>
<?xml-stylesheet type="text/xsl" href="' . plugins_url('xsl/sitemap.xsl.php',__FILE__) . '?ver=' . XMLSF_VERSION . '"?>
<!-- generated-on="' . date('Y-m-d\TH:i:s+00:00') . '" -->
<!-- generator="XML & Google News Sitemap Feed plugin for WordPress" -->
<!-- generator-url="http://status301.net/wordpress-plugins/xml-sitemap-feed/" -->
<!-- generator-version="' . XMLSF_VERSION . '" -->
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
echo !empty($news) ? '
	xmlns:news="http://www.google.com/schemas/sitemap-news/0.9" ' : '';
echo '
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 
		http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd';
echo !empty($news) ? '
		http://www.google.com/schemas/sitemap-news/0.9 
		http://www.google.com/schemas/sitemap-news/0.9/sitemap-news.xsd' : '';
echo '">
';

// PRESETS are changable -- please read comments:

$max_priority = 0.9;	// Maximum priority value for any URL in the sitemap; set to any other value between 0 and 1.
$min_priority = 0;	// Minimum priority value for any URL in the sitemap; set to any other value between 0 and 1.
			// NOTE: Changing these values will influence each URL's priority. Priority values are taken by 
			// search engines to represent RELATIVE priority within the site domain. Forcing all URLs
			// to a priority of above 0.5 or even fixing them all to 1.0 - for example - is useless.
$frontpage_priority = 1.0;	// Your front page priority, usually the same as max priority but if you have any reason
				// to change it, please be my guest; set to any other value between 0 and 1.

$level_weight = 0.1;	// Makes a sub-page gain or loose priority for each level; set to any other value between 0 and 1.
$month_weight = -0.1;	// Fall-back value normally ignored by automatic priority calculation, which
			// makes a post loose 10% of priority monthly; set to any other value between 0 and 1.
$firstcomment_bonus = 0.1;

// EDITING below here is NOT ADVISED!

// setup site variables
$_post_count = wp_count_posts($post_type);
//$_page_count = wp_count_posts('page');
$_totalcommentcount = wp_count_comments($post_type);

$lastmodified_gmt = get_lastmodified('GMT'); // last posts or page modified date
$lastmodified = mysql2date('U',$lastmodified_gmt); // last posts or page modified date in Unix seconds
$firstdate = mysql2date('U',get_firstdate('GMT')); // uses new get_firstdate() function defined in xml-sitemap/hacks.php !

// calculated presets
if ($_totalcommentcount->approved > 0) {
	$average_commentcount = $_totalcommentcount->approved/($_post_count->publish);
	//$comment_weight =  $average_commentcount / $_totalcommentcount->approved;
} else {
	//$comment_weight = 0;
	$average_commentcount = 0;
}

$blogbonus = 0.4;
$sitebonus = 0;

if ( $lastmodified > $firstdate ) // valid blog age found ?
	$age_weight = ($blogbonus - $min_priority) / ($firstdate - $lastmodified); // calculate relative age weight
else
	$age_weight = $month_weight / 2629744 ; // if not ? malus per month (that's a month in seconds)

// any ID's we need to exclude?
$excluded = $xmlsf->get_excluded($post_type);

// loop away!
if ( have_posts() ) :
    while ( have_posts() ) : 
	the_post();

	// check if we are not dealing with an external URL :: Thanks to Francois Deschenes :)
	// or if page is in the exclusion list (like front pages)
	if ( !preg_match('/^' . preg_quote(home_url(), '/') . '/i', get_permalink()) || in_array($post->ID, $excluded) )
		continue;
?>
	<url>
		<loc><?php the_permalink_rss(); ?></loc>
		<lastmod><?php echo $xmlsf->get_lastmod(); ?></lastmod>
		<changefreq><?php echo $xmlsf->get_changefreq(); ?></changefreq>
	 	<priority><?php echo $xmlsf->get_priority(); ?></priority>
 	</url>
<?php 
    endwhile; 
endif; 
?></urlset>
<?php $xmlsf->_e_usage(); ?>
