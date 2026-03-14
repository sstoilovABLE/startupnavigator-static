<?php
/**
 * Adds query control. This control will fetch different type of data e.g post, author, term, taxonomy based on query param.
 *
 * @package Raven
 * @since 1.9.4
 */

namespace Raven\Controls;

use Elementor\Control_Select2;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Query extends Control_Select2 {

	public function get_type() {
		return 'raven_query';
	}

	/**
	 *  Control default settings.
	 *
	 * @since 1.9.4
	 *
	 * @return array
	 */
	protected function get_default_settings() {
		return array_merge( parent::get_default_settings(), [
			'query' => [
				'source' => 'post', // post, author, term, taxonomy.
				'post_type' => 'post',
				'post_type_control' => 'query_post_type', // Mention control whose value is a post_type.
				'numberposts' => 10,
				'no_found_rows' => true,
				'orderby' => 'title',
			],
		] );
	}

	/**
	 * Get query results.
	 *
	 * @since 1.9.4
	 * @access public
	 *
	 * @param array $data Ajax params.
	 *
	 * @return array
	 */
	public static function get_query_results( $data ) {
		switch ( $data['source'] ) {
			case 'post':
				$results = self::get_posts( $data );
				break;
		}

		return [
			'results' => $results,
		];
	}

	/**
	 * Get posts.
	 *
	 * @since 1.9.4
	 * @access public
	 * @static
	 *
	 * @param array $data Query.
	 *
	 * @return array
	 */
	private static function get_posts( $data ) {
		if ( ! empty( $data['editor_post_id'] ) ) {
			$exclude         = empty( $data['exclude'] ) ? [] : $data['exclude'];
			$exclude[]       = $data['editor_post_id'];
			$data['exclude'] = $exclude;

			unset( $data['editor_post_id'] );
		}

		$posts = get_posts( $data );

		$posts = array_reduce( $posts, function ( $value, $post ) {
			$value[] = [
				'id' => $post->ID,
				'text' => $post->post_title,
			];

			return $value;
		}, [] );

		return $posts;
	}
}
