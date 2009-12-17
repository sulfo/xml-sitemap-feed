<?php
/* ---------------------------
    XML Sitemap Feed Template
   --------------------------- */

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'feed-xml.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Please do not load this page directly. Thanks!');

// priority presets
$frontpage_priority = 1.0;
$post_priority = 0.7;
$minpost_priority = 0.1;
$maxpost_priority = 0.9;
$page_priority = 0.5;

$lastpostmodified = get_lastpostmodified('GMT');

// start the xml output
@header('Content-Type: text/xml; charset=' . get_option('blog_charset'));

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?>
<?xml-stylesheet type="text/xsl" href="'.get_option('home').'/?feed=sitemap.xsl"?>
<!-- generated-on="'.date('Y-m-d\TH:i:s+00:00').'" -->
<!-- generator="XML Sitemap Feed plugin for WordPress" -->
<!-- generator-url="http://4visions.nl/en/index.php?section=57" -->
<!-- generator-version="'.XMLSFVERSION.'" -->
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
$has_page_as_front = $wpdb->get_results("SELECT option_value FROM $wpdb->options WHERE option_name = 'show_on_front'");
if ($has_page_as_front[0]->option_value == "page") {
	$frontpage = $wpdb->get_results("SELECT option_value FROM $wpdb->options WHERE option_name = 'page_on_front'");
	$frontpage_id = $frontpage[0]->option_value;
} else {
	$frontpage_id = -1;

}

// get all posts and pages
$post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND ID != $frontpage_id ORDER BY post_date_gmt DESC LIMIT 1000");

// and loop away!
if ($post_ids) {
	global $wp_query;
	$wp_query->in_the_loop = true;

	while ( $next_posts = array_splice($post_ids, 0, 20) ) {
		$where = "WHERE ID IN (".join(',', $next_posts).")";
		$posts = $wpdb->get_results("SELECT * FROM $wpdb->posts $where ORDER BY post_date_gmt DESC");
		foreach ($posts as $post) {
			setup_postdata($post); 
			$post_modified_time = get_post_modified_time('Y-m-d H:i:s', true);
			$priority_down = (($lastpostmodified - $post_modified_time) > 0) ? ($lastpostmodified - $post_modified_time)/10 : 0;
			$priority_up = ($post->comment_count > 0) ? $post->comment_count/10 : 0;
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
		if($post->comment_count > 0) { ?>
		<changefreq>weekly</changefreq>
<?php 		} else { ?>
		<changefreq>monthly</changefreq>
<?php		} ?>
		<priority><?php echo $priority ?></priority>
<?php } ?>
	</url>
<?php 		} 
	} 
} ?>
</urlset>
