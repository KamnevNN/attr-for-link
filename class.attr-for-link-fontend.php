<?php

class AFL_Front
{
	public function __construct()
	{
		add_filter('the_content', array($this, 'afl_attr_content'));
		add_filter('get_post_metadata', array($this, 'afl_attr_metadata'), 10, 4);
	}

	public function afl_attr_content($content)
	{
		return preg_replace_callback('/<a[^>]+/', array($this, 'afl_attr_content_callback'), $content);
	}

	public function afl_attr_metadata($metadata, $object_id, $meta_key, $single)
	{
		global $wpdb;
		$value = $wpdb->get_var("SELECT meta_value FROM $wpdb->postmeta WHERE post_id = $object_id AND  meta_key = '" . $meta_key . "'");
		preg_match_all('/<a[^>]+/', $value, $matches);
		$site_url = site_url();
		$afl_options = get_option('afl_options');
		if (in_array(get_post_type(), $afl_options['field_afl_post_type']) && !empty($matches[0])) {
			if (in_array('nofollow', $afl_options['field_afl_attrs'])) {
				if (strpos($matches[0][0], 'rel') === false) {
					$value = preg_replace("%(href=\S(?!$site_url))%i", 'rel="nofollow" $1', $value);
				} elseif (preg_match("%href=\S(?!$site_url)%i", $value)) {
					$value = preg_replace('/rel=S(?!nofollow)\S*/i', 'rel="nofollow"', $value);
				}
			}
			if (in_array('blank', $afl_options['field_afl_attrs'])) {
				if (strpos($matches[0][0], '_blank') === false) {
					$value = preg_replace("%(href=\S(?!$site_url))%i", 'target="_blank" $1', $value);
				} elseif (preg_match("%href=\S(?!$site_url)%i", $value)) {
					$value = preg_replace('/target=S(?!\_blank)\S*/i', 'target="_blank"', $value);
				}
			}
		}
		return $value;
	}

	public function afl_attr_content_callback($matches)
	{
		$afl_options = get_option('afl_options');

		$anchor = $matches[0];
		$site_url = site_url();
		if (in_array(get_post_type(), $afl_options['field_afl_post_type'])) {
			if (in_array('nofollow', $afl_options['field_afl_attrs'])) {
				if (strpos($anchor, 'rel') === false) {
					$anchor = preg_replace("%(href=\S(?!$site_url))%i", 'rel="nofollow" $1', $anchor);
				} elseif (preg_match("%href=\S(?!$site_url)%i", $anchor)) {
					$anchor = preg_replace('/rel=S(?!nofollow)\S*/i', 'rel="nofollow"', $anchor);
				}
			}
			if (in_array('blank', $afl_options['field_afl_attrs'])) {
				if (strpos($anchor, '_blank') === false) {
					$anchor = preg_replace("%(href=\S(?!$site_url))%i", 'target="_blank" $1', $anchor);
				} elseif (preg_match("%href=\S(?!$site_url)%i", $anchor)) {
					$anchor = preg_replace('/target=S(?!\_blank)\S*/i', 'target="_blank"', $anchor);
				}
			}
		}
		return $anchor;
	}
}
