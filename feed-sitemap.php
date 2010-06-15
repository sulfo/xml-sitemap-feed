<?php
/* ---------------------------
    XML Sitemap Feed Template
   --------------------------- */

// presets
$frontpage_priority = 1.0;
$min_priority = 0.1;
$max_priority = 0.9;
$maxURLS = 9999; // 10,000 (including 1 for front page) of maximum 50,000 URLs allowed in a sitemap.xml
                 // should be more than enough for any blog...
$comment_weight = 0.1;
$age_weight = 0.1;
$level_weight = 0.1;

// site variables
$_post_count = wp_count_posts('post');
$_page_count = wp_count_posts('page');
$_totalcommentcount = wp_count_comments();

$lastpostmodified_GMT = get_lastpostmodified('GMT'); // last posts modified date
$lastpostmodified = mysql2date('U',$lastpostmodified_GMT); // last posts modified date in Unix seconds
$firstpostmodified = mysql2date('U',get_firstpostmodified('GMT')); // get_firstpostmodified() function defined in xml-sitemap.php !
$average_commentcount = $_totalcommentcount->approved/($_post_count->publish + $_page_count->publish);

// calculated presets
if ($_totalcommentcount->approved > 0)
	$comment_weight =  ($max_priority - $min_priority) / $_totalcommentcount->approved;

if ($_post_count->publish > $_page_count->publish) { // site emphasis on posts
	$post_priority = 0.7;
	$page_priority = 0.4;
} else { // site emphasis on pages
	$post_priority = 0.4;
	$page_priority = 0.7;
}

if ( $lastpostmodified > $firstpostmodified )
	$age_weight = ($max_priority - $min_priority) / ($lastpostmodified - $firstpostmodified);

// reset the query
global $post;
query_posts( array(
	'posts_per_page' => $maxURLS, 
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
		<lastmod><?php echo mysql2date('Y-m-d\TH:i:s+00:00', $lastpostmodified_GMT, false); ?></lastmod>
		<changefreq>daily</changefreq>
		<priority>1.0</priority>
	</url>
<?php
// and loop away!
while ( have_posts() ) : the_post();

	if($post->post_type == "page") {
		if ($post->ID == get_option('page_on_front')) // check if we are not doing the front page twice
			continue;
		
		if (!is_array($post->ancestors)) { // $post->ancestors seems always empty. something to do with http://core.trac.wordpress.org/ticket/10381 ??
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
		$priority = $page_priority + round($offset,1);
	} else {
		$offset = (($post->comment_count - $average_commentcount) * $comment_weight) - (($lastpostmodified - mysql2date('U',$post->post_modified_gmt)) * $age_weight);
		$priority = $post_priority + round($offset,1);
	}
	$priority = ($priority > $max_priority) ? $max_priority : $priority;
	$priority = ($priority < $min_priority) ? $min_priority : $priority;
?>
	<url>
		<loc><?php the_permalink_rss() ?></loc>
		<lastmod><?php echo mysql2date('Y-m-d\TH:i:s+00:00', $post->post_modified_gmt, false) ?></lastmod>
<?php 	if($post->comment_count > ($_totalcommentcount->approved / 2)) { ?>
		<changefreq>daily</changefreq>
<?php	} else if($post->comment_count > 0 ) { ?>
		<changefreq>weekly</changefreq>
<?php 	} else { ?>
		<changefreq>monthly</changefreq>
<?php	} ?>
		<priority><?php echo $priority ?></priority>
	</url>
<?php endwhile; ?>
</urlset>
