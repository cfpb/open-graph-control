<?php
/**
 * Post Meta As Open Graph
 *
 * A plugin that adds meta tags prepared with facebook open graph data pulled from
 * post metadata.
 *
 * @package	Open-Graph-Control
 * @author Greg Boone, CFPB
 * @license Public Domain
 * @link https://cfpb.github.io/
 *
 * @wordpress-plugin
 * Plugin Name:	Open Graph Control
 * Plugin URI: https://github.com/cfpb/open-graph-control
 * Description: Adds meta tags prepared with facebook open graph data
 * Version: 2.0
 * Author: Greg Boone, CFPB
 * Author URI: https://cfpb.github.io/
 * Text Domain: open-graph-control
 * License: Public Domain
 */
namespace CFPB;
Class SimpleOpenGraph {

	public function get_utm_data($medium = 'web', $post) {
  		$utm_data['source'] = defined(UTM_SOURCE) ? UTM_SOURCE : get_site_url('url');
  		$utm_data['campaign'] = get_post_meta( $post->ID, $key = 'utm_campaign', $single = true );
  		$utm_data['term'] = get_post_meta( $post->ID, $key = 'utm_term', $single = true );
  		$utm_data['content'] = get_post_meta( $post->ID, $key = 'utm_content', $single = true );
  		$utm_data['medium'] = $medium;
  		return $utm_data;
	}

	public function utm_url($utm_data) {
		$url = '';
		if ( $utm_data['campaign'] ) {
			$url .= "&utm_campaign=" . \urlencode($utm_data['campaign']);
		}
		if ( $utm_data['term'] ) {
			$url .= "&utm_term=" . \urlencode($utm_data['term']);
		}
		if ( $utm_data['content'] ) {
			$url .= "&utm_content=" . \urlencode($utm_data['content']);
		}
		return $url;
	}

	public function get_og_data($post) {
		$og = array();
		if ( $post ) {
			$og['title'] = get_post_meta( 
	  			$post->ID, 
	  			$key = 'og_title', 
	  			$single = true 
	  		);

	  		$og['image'] = get_post_meta( $post->ID, 'og_image', true);
			$og['description'] = get_post_meta( $post->ID, 'og_desc', true);
		}
		return $og;
	}
	public function open_graph() {
	  	global $post;
  		$utm_data = $this->get_utm_data($medium = 'facebook', $post);
  		$utm_source = '?utm_source=' . $utm_data['source'];
  		$utm_medium = '&utm_medium=' . $utm_data['medium'];
	  	if ( $post ) {
	  		$url = get_permalink( ) . $utm_source . $utm_medium;
	  		$url .= $this->utm_url($utm_data);
		}
		$og = $this->get_og_data($post);

		if ( $og['title'] ) {
			?><meta property="og:title" content="<?php echo htmlspecialchars($og['title']) ?>" /> <?php
		} elseif ( is_front_page() ) {
			?><meta property="og:title" content="<?php bloginfo('sitename') ?>"><?php
		} else {
			?><meta property="og:title" content="<?php wp_title('-',true,'right'); ?><?php bloginfo('name'); ?>" /><?php
		}
		if ( $og['image'] ) {
			?><meta property="og:image" content="<?php echo $og['image']; ?>" /> <?php
		} else {
			?><meta property="og:image" content="<?php bloginfo('template_directory'); ?>/_/img/logo.png" /> <?php
		}
		if ( $og['description'] ) {
			?><meta property="og:description" content="<?php echo htmlspecialchars($og['description']) ?>" /> <?php
		}

		?><meta property="og:url" content="<?php echo $url ?>" /><?php
	}

	public function twitter_data( $post_id ) {
		$tweet['text'] = get_post_meta( $post_id, 'twtr_text', $single = true);
		$tweet['related'] = get_post_meta( $post_id, 'twtr_rel', $single = true );
		$tweet['lang'] = get_post_meta( $post_id, 'twtr_lang', $single = true );
		$tweet['hashtags'] = get_post_meta( $post_id, 'twtr_hash', $single = true);
		return $tweet;
	}

	public function tweet_url($user) {
		global $post;
		$utm = $this->get_utm_data('twitter', $post);
		$utm_url = $this->utm_url($utm);
		$tweet = $this->twitter_data($post->ID);
		$url = get_permalink();
		$share_url = 'http://twitter.com/intent/tweet/';
		$share_url .= "?url={$url}";
		$share_url .= "&via={$user}";
		if ( $tweet['text'] ) {
			$share_url .= '&text=' . urlencode($tweet['text']);
		}
		if ( $tweet['related'] ) {
			$share_url .= '&related=' . $tweet['related'];
		}
		if ( $tweet['lang'] ) {
			$share_url .= '&lang=' . $tweet['lang'];
		}
		if ( $tweet['hashtags'] ) {
			$share_url .= '&hashtags=' . $tweet['hashtags'];
		}
		$share_url .= $utm_url;
		echo $share_url;
	}

	public function build() {
		define('TWITTER_USER', 'CFPB');
		define('UTM_SOURCE', 'consumerfinance.gov');
    	add_action( 'wp_enqueue_scripts', array($this, 'open_graph') );
	}
}
$p = new \CFPB\SimpleOpenGraph();
add_action( 'plugins_loaded', array($p, 'build'));