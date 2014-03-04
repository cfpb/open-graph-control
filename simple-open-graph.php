<?php
namespace gboone;
Class SimpleOpenGraph {
	public function __construct(){
		add_action( 'plugins_loaded', array($p, 'build'), $priority = 10, $accepted_args = 1 );
	}

	public function open_graph() {
	  	global $post;
  		global $utm_data;
	  	if ( $post ) {
	  		$title = get_post_meta( $post->ID, $key = 'og_title', $single = true );
	  		$url = get_permalink( ) . '?utm_source=CFPB&utm_medium=facebook';
	  		$url .= $this->open_graph_url();
	  		$image = get_post_meta( $post->ID, $key = 'og_image', $single = true);
		}

		if ( $title ) {
			?><meta property="og:title" content="<?php echo htmlspecialchars($title) ?>" /> <?php
		} else {
			?><meta property="og:title" content="<?php wp_title('-',true,'right'); ?><?php bloginfo('name'); ?>" /><?php
		}
		if ( $image ) {
			?><meta property="og:image" content="<?php echo urlencode($image); ?>" /> <?php
		} else {
			?><meta property="og:image" content="<?php bloginfo('template_directory'); echo '/_/img/logo.png'?>" /> <?php
		}

		?><meta property="og:url" content="<?php echo $url ?>" /><?php
	}

	public function open_graph_url() {
		$url = '';
		global $post;
  		$campaign = get_post_meta( $post->ID, $key = 'utm_campaign', $single = true );
  		$term = get_post_meta( $post->ID, $key = 'utm_term', $single = true );
  		$content = get_post_meta( $post->ID, $key = 'utm_content', $single = true );
		if ( $campaign ) {
			$url .= "&utm_campaign=" . \urlencode($campaign);
		}
		if ( $term ) {
			$url .= "&utm_term=" . \urlencode($term);
		}
		if ( $content ) {
			$url .= "&utm_content=" . \urlencode($content);
		}
		return $url;
	}

	public function build() {
    	add_action( 'open_graph_data', array($this, 'open_graph') );
	}
}
$p = new \gboone\SimpleOpenGraph();
?>