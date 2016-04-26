<?php
/**
 * From 'Facebook Instant Articles for WP'
 *
 * Used as a standin until we implment a proper oembed wrapper. Thanks to
 * bjornjohansen and diegoquinteiro for this file. Used under GPLv2.
 */


/**
 * Filter the oembed results to see if we should do some extra handling
 *
 * @since 0.1
 * @param string $html     The original HTML returned from the external oembed provider.
 * @param string $url      The URL found in the content.
 * @param mixed  $attr     An array with extra attributes.
 * @param int    $post_id  The post ID.
 * @return string The potentially filtered HTML.
 */
function instant_articles_embed_oembed_html( $html, $url, $attr, $post_id ) {

	if ( ! class_exists( 'WP_oEmbed' ) ) {
		include_once( ABSPATH . WPINC . '/class-oembed.php' );
	}

	// Instead of checking all possible URL variants, use the provider list from WP_oEmbed.
	$wp_oembed = new WP_oEmbed();
	$provider_url = $wp_oembed->get_provider( $url );

	$provider_name = false;
	if ( false !== strpos( $provider_url, 'instagram.com' ) ) {
		$provider_name = 'instagram';
	} elseif ( false !== strpos( $provider_url, 'twitter.com' ) ) {
		$provider_name = 'twitter';
	} elseif ( false !== strpos( $provider_url, 'youtube.com' ) ) {
		$provider_name = 'youtube';
	} elseif ( false !== strpos( $provider_url, 'vine.co' ) ) {
		$provider_name = 'vine';
	}

	$provider_name = apply_filters( 'instant_articles_social_embed_type', $provider_name, $url );

	if ( $provider_name ) {
		$html = instant_articles_embed_get_html( $provider_name, $html, $url, $attr, $post_id );
	} else {
		$html = sprintf( '<iframe class="oembed">%s</iframe>', $html);
	}

	return $html;

}


/**
 * Filter the embed results for embeds.
 *
 * @since 0.1
 * @param string $provider_name  The name of the embed provider. E.g. “instagram” or “youtube”.
 * @param string $html           The original HTML returned from the external oembed/embed provider.
 * @param string $url            The URL found in the content.
 * @param mixed  $attr           An array with extra attributes.
 * @param int    $post_id        The post ID.
 * @return string The filtered HTML.
 */
function instant_articles_embed_get_html( $provider_name, $html, $url, $attr, $post_id ) {

	/**
	 * Filter the HTML that will go into the Instant Article Social Embed markup.
	 *
	 * @since 0.1
	 * @param string $html     The HTML.
	 * @param string $url      The URL found in the content.
	 * @param mixed  $attr     An array with extra attributes.
	 * @param int    $post_id  The post ID.
	 */
	$html = apply_filters( "instant_articles_social_embed_{$provider_name}", $html, $url, $attr, $post_id );

	$html = sprintf( '<figure class="op-social">%s</figure>', $html );

	/**
	 * Filter the Instant Article Social Embed markup.
	 *
	 * @since 0.1
	 * @param string $html     The Social Embed markup.
	 * @param string $url      The URL found in the content.
	 * @param mixed  $attr     An array with extra attributes.
	 * @param int    $post_id  The post ID.
	 */
	$html = apply_filters( 'instant_articles_social_embed', $html, $url, $attr, $post_id );

	return $html;
}