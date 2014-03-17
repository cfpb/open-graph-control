<?php
/**
 * Post Meta As Open Graph
 *
 * A plugin that adds meta tags prepared with facebook open graph data pulled from
 * post metadata.
 *
 * @package	Open-Graph-Control
 * @author Greg Boone <boone.greg@gmail.com>
 * @license Public Domain
 * @link http://greg.harmsboone.org
 * @copyright 2014 Greg Boone
 *
 * @wordpress-plugin
 * Plugin Name:	Open Graph Control
 * Plugin URI: https://github.com/gboone/open-graph-control
 * Description: Adds meta tags prepared with facebook open graph data
 * Version: 0.1
 * Author: Greg Boone
 * Author URI: http://greg.harmsboone.org
 * Text Domain: open-graph-control
 * License: Public Domain
 * GitHub Plugin URI: https://github.com/gboone/open-graph-control
 */
namespace gboone;
Class SimpleOpenGraph {

	function __construct(){
		define('TWITTER_USER', 'CFPB');
		define('UTM_SOURCE', 'consumerfinance.gov');
	}

	public function get_utm_data($medium = 'web') {
  		$utm_data['source'] = UTM_SOURCE;
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

	  		$og['image'] = get_post_meta( $post->ID, 'og_image', $single = true);
		}
		return($og);
	}
	public function open_graph() {
	  	global $post;
  		$utm_data = get_utm_data($medium = 'facebook');
  		$utm_source = '?utm_source=' . $utm_data['source'];
  		$utm_medium = '&utm_medium=' . $utm_data['medium'];
	  	if ( $post ) {
	  		$url = get_permalink( ) . $utm_source . $utm_medium;
	  		$url .= $this->utm_url($utm_data);
		}
		$og = get_og_data($post);

		if ( $title ) {
			?><meta property="og:title" content="<?php echo htmlspecialchars($title) ?>" /> <?php
		} elseif ( is_front_page() ) {
			?><meta property="og:title" content="<?php bloginfo('sitename') ?>"><?php
		} else {
			?><meta property="og:title" content="<?php wp_title('-',true,'right'); ?><?php bloginfo('name'); ?>" /><?php
		}
		if ( $image ) {
			?><meta property="og:image" content="<?php echo urlencode($image); ?>" /> <?php
		} else {
			?><meta property="og:image" content="<?php bloginfo('template_directory'); ?>/_/img/logo.png" /> <?php
		}

		?><meta property="og:url" content="<?php echo $url ?>" /><?php
	}

	public function twitter_data( $post_id ) {
		$tweet['text'] = get_post_meta( $post_id, 'twtr_text', $single = true);
		$tweet['related'] = get_post_meta( $post_id, 'twtr_rel', $single = true );
		$tweet['lang'] = get_post_meta( $post_id, 'twtr_lang', $single = true );
		$tweet['hastags'] = get_post_meta( $post_id, 'twtr_hash', $single = true);
		return $tweet;
	}

	public function tweet_url() {
		global $post;
		$utm = get_utm_data('twitter');
		$utm_url = utm_url($utm);
		$user = TWITTER_USER;
		$tweet = twitter_data($post->ID);
		$count_url = the_permalink();
		$share_url = 'http://twitter.com/share/?via=' . $user . '&counturl=' . $count_url;
		if ( $tweet['text'] ) {
			$share_url .= '&text=' . $tweet['text'];
		}
		if ( $tweet['related'] ) {
			$share_url .= '&via=' . $tweet['related'];
		}
		if ( $tweet['lang'] ) {
			$share_url .= '&lang=' . $tweet['lang'];
		}
		if ( $tweet['hashtags'] ) {
			$share_url .= '&hashtags=' . $tweet['hashtags'];
		}
		$share_url .= $utm_url;
		return $share_url;
	}

	public function build() {
    	add_action( 'wp_enqueue_scripts', array($this, 'open_graph') );
    	add_action( 'tweet_url', array($this, 'tweet_button' ) );
	}
}
$p = new \gboone\SimpleOpenGraph();
add_action( 'plugins_loaded', array($p, 'build'));
?>