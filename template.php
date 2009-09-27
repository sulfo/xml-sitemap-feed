<?php
/**
 * XML Sitemap Feed Template 1.0
 **/

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'feed-sitemap.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Please do not load this page directly. Thanks!');

header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
$more = 1;

?>
<?php echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<!-- generator="XML Sitemap WordPress plugin/1.0" -->
<urlset	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
	xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc><?php bloginfo_rss('url') ?></loc>
		<lastmod><?php echo mysql2date('Y-m-d\TH:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastmod>
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
?>
	<url>
		<loc><?php the_permalink_rss() ?></loc>
		<lastmod><?php echo mysql2date('Y-m-d\TH:i:s +0000', get_post_modified_time('Y-m-d H:i:s', true), false); ?></lastmod>
		<changefreq>monthly</changefreq>
		<priority>0.6</priority>
	</url>
<?php 		} 
	} 
} ?>
</urlset>
