<?php
/*
Plugin Name: XML Sitemap Feed
Plugin URI: http://4visions.nl/en/index.php?section=57
Description: Creates a dynamic XML feed that complies to the XML Sitemap protocol ready be submitted to Google, Yahoo, MSN, Ask.com and others. Based on the Standard XML Sitemap Generator by Patrick Chia.
Version: 2.0
Author: RavanH
Author URI: http://4visions.nl/
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=XML%20Sitemap%20Feed&item_number=2%2e6%2e2%2e9&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8
*/

/*  Copyright 2009 RavanH  (http://4visions.nl/ email : ravanhagen@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function xml_sitemap_flush_rules() {
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}
add_action('init', 'xml_sitemap_flush_rules');

function xml_sitemap_feed_rewrite($wp_rewrite) {
	$feed_rules = array(
		'^sitemap.xml$' => 'index.php?feed=sitemap',
		'^feed/sitemap$' => 'index.php?feed=sitemap'
	);
	$wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
}
add_filter('generate_rewrite_rules', 'xml_sitemap_feed_rewrite');

function xml_sitemap_do_feed() {
	$dir = dirname(__FILE__);
	if (file_exists($dir.'/xml-sitemap-feed')) // chech if xml-sitemap.php was moved one dir up (for mu-plugins in wpmu)
		load_template( $dir . '/xml-sitemap-feed/template.php' );
	else
		load_template( $dir . '/template.php' );
}
add_action('do_feed_sitemap', 'xml_sitemap_do_feed', 10, 1);

function xml_sitemap_robots() {
	echo "\nSitemap: ".get_option('siteurl')."/sitemap.xml\n\n";
}
add_action('do_robotstxt', 'xml_sitemap_robots');
?>
