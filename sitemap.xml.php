<?php
/* ---------------------------
    XML Sitemap Feed Template
   --------------------------- */

// priority presets
$frontpage_priority = 1.0;
$post_priority = 0.7;
$minpost_priority = 0.1;
$maxpost_priority = 0.9;
$page_priority = 0.4;

$lastpostmodified = get_lastpostmodified('GMT');
$totalcommentcount = get_comment_count();

global $post;
query_posts( array(
	'posts_per_page' => '-1', 
	'post_type' => 'any', 
	'post_status' => 'publish', 
	'caller_get_posts' => '1'
	)
); 

// start the xml output
header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?>
<?xml-stylesheet type="text/xsl" href="'.get_option('home').'/'.str_replace(ABSPATH,"", XMLSF_PLUGIN_DIR).'/sitemap.xsl.php?v='.XMLSF_VERSION.'"?>
<!-- generated-on="'.date('Y-m-d\TH:i:s+00:00').'" -->
<!-- generator="XML Sitemap Feed plugin for WordPress" -->
<!-- generator-url="http://4visions.nl/en/index.php?section=57" -->
<!-- generator-version="'.XMLSF_VERSION.'" -->
'; ?>
<urlset	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
	xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc><?php bloginfo_rss('url') ?>/</loc>
		<lastmod><?php echo mysql2date('Y-m-d\TH:i:s+00:00', $lastpostmodified, false); ?></lastmod>
		<changefreq>daily</changefreq>
		<priority>1.0</priority>
	</url>
<?php
// first check if there is a static page set as frontpage and exclude it to avoid double url
//$has_page_as_front = $wpdb->get_results("SELECT option_value FROM $wpdb->options WHERE option_name = 'show_on_front'");
//if ($has_page_as_front[0]->option_value == "page") {
//	$frontpage = $wpdb->get_results("SELECT option_value FROM $wpdb->options WHERE option_name = 'page_on_front'");
//	$frontpage_id = $frontpage[0]->option_value;
//} else {
//	$frontpage_id = -1;
//}

// and loop away!
while ( have_posts() ) : the_post();

	//setup_postdata($post); 
	$post_modified_time = get_post_modified_time('Y-m-d H:i:s', true);
	$post_comment_count = get_comment_count($post->ID);
	$priority_down = (($lastpostmodified - $post_modified_time) > 0) ? ($lastpostmodified - $post_modified_time)/10 : 0;
	$priority_up = ($post_comment_count['approved'] > 0) ? ($post_comment_count['approved']/$totalcommentcount['approved'])*10 : 0;
	$priority = $post_priority - $priority_down + $priority_up;
	$priority = ($priority > $maxpost_priority) ? $maxpost_priority : $priority;
	$priority = ($priority < $minpost_priority) ? $minpost_priority : $priority;
?>
	<url>
		<loc><?php the_permalink_rss() ?></loc>
		<lastmod><?php echo mysql2date('Y-m-d\TH:i:s+00:00', $post_modified_time, false) ?></lastmod>
<?php if($post->post_type == "page") { ?>
		<changefreq>monthly</changefreq>
		<priority><?php echo $page_priority ?></priority>
<?php } else {
		if($post_comment_count['approved'] > 10) { ?>
		<changefreq>daily</changefreq>
<?php		} else if($post_comment_count['approved'] > 0) { ?>
		<changefreq>weekly</changefreq>
<?php 		} else { ?>
		<changefreq>monthly</changefreq>
<?php		} ?>
		<priority><?php echo $priority ?></priority>
<?php } ?>
	</url>
<?php endwhile; ?>
</urlset>
