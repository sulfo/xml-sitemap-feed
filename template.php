<?php
/**
 * XML Sitemap Feed Template 2.1
 **/

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'feed-sitemap.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Please do not load this page directly. Thanks!');

header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
$more = 1;

$lastpostmodified = get_lastpostmodified('GMT');

$post_priority = 0.7;
$minpost_priority = 0.3;
$maxpost_priority = 0.9;
$page_priority = 0.6;
$frontpage_priority = 1.0;

?>
<?php echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<!-- generator="XML Sitemap Feed WordPress plugin/2.1" -->
<urlset	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
	xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc><?php bloginfo_rss('url') ?></loc>
		<lastmod><?php echo mysql2date('Y-m-d\TH:i:s+00:00', $lastpostmodified, false); ?></lastmod>
		<changefreq>daily</changefreq>
		<priority>1.0</priority>
	</url>
<?php

$post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT 1000");

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
 			$priority = ( $priority > $maxpost_priority ) ? $maxpost_priority : $priority;
 			$priority = ( $priority < $minpost_priority ) ? $minpost_priority : $priority;
?>
	<url>
		<loc><?php the_permalink_rss() ?></loc>
		<lastmod><?php echo mysql2date('Y-m-d\TH:i:s+00:00', $post_modified_time, false) ?></lastmod>
<?php if($post->post_type == "page") { ?>
		<changefreq>monthly</changefreq>
		<priority>0.5</priority>
<?php } else {
		if($post->comment_count > 0) { ?>
		<changefreq>weekly</changefreq>
<?php 		} else { ?>
		<changefreq>monthly</changefreq>
<?php		} ?>
		<priority><?php echo $priority ?></priority>
		<?php //echo "<test>" . $post_priority . " - " . $priority_down . " + " . $priority_up . "</test>" ?>
<?php } ?>
	</url>
<?php 		} 
	} 
} ?>
</urlset>
