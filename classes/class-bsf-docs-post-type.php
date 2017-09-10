<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies.
 *
 * @class     BSF_Docs_post_Type
 * @category  Class
 * @author    Brainstormforce
 * @package   Documentation/PostType
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BSF_Docs_Post_Type Class.
 */
class BSF_Docs_Post_Type {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		add_filter( 'rest_api_allowed_post_types', array( __CLASS__, 'rest_api_allowed_post_types' ) );
	}

	/**
	 * Register core taxonomies.
	 */
	public static function register_taxonomies() {

		if ( ! is_blog_installed() ) {
			return;
		}

		do_action( 'bsf_docs_before_register_taxonomy' );

		register_taxonomy(
			'docs_category',
			apply_filters( 'docs_category', array( BSF_DOCS_POST_TYPE ) ),
			array(
				'hierarchical'          => true,
				'label'                 => __( 'Categories', 'wp-docs' ),
				'labels' => array(
					'name'              => __( 'Docs categories', 'wp-docs' ),
					'singular_name'     => __( 'Category', 'wp-docs' ),
					'menu_name'         => _x( 'Categories', 'Admin menu name', 'wp-docs' ),
					'search_items'      => __( 'Search categories', 'wp-docs' ),
					'all_items'         => __( 'All categories', 'wp-docs' ),
					'parent_item'       => __( 'Parent category', 'wp-docs' ),
					'parent_item_colon' => __( 'Parent category:', 'wp-docs' ),
					'edit_item'         => __( 'Edit category', 'wp-docs' ),
					'update_item'       => __( 'Update category', 'wp-docs' ),
					'add_new_item'      => __( 'Add new category', 'wp-docs' ),
					'new_item_name'     => __( 'New category name', 'wp-docs' ),
					'not_found'         => __( 'No categories found', 'wp-docs' ),
				),
				'show_ui'               => true,
				'query_var'             => true,
				'rewrite'          => array(
					'slug'         => 'docs-category',
					'with_front'   => false,
					'hierarchical' => true,
				),
			)
		);

		register_taxonomy(
			'docs_tag',
			apply_filters( 'bsf_taxonomy_objects_docs_tag', array( BSF_DOCS_POST_TYPE ) ),
			apply_filters(
				'bsf_taxonomy_args_docs_tag', array(
					'hierarchical'          => false,
					'label'                 => __( 'Docs tags', 'wp-docs' ),
					'labels'                => array(
						'name'                       => __( 'Docs tags', 'wp-docs' ),
						'singular_name'              => __( 'Tag', 'wp-docs' ),
						'menu_name'                  => _x( 'Tags', 'Admin menu name', 'wp-docs' ),
						'search_items'               => __( 'Search tags', 'wp-docs' ),
						'all_items'                  => __( 'All tags', 'wp-docs' ),
						'edit_item'                  => __( 'Edit tag', 'wp-docs' ),
						'update_item'                => __( 'Update tag', 'wp-docs' ),
						'add_new_item'               => __( 'Add new tag', 'wp-docs' ),
						'new_item_name'              => __( 'New tag name', 'wp-docs' ),
						'popular_items'              => __( 'Popular tags', 'wp-docs' ),
						'separate_items_with_commas' => __( 'Separate tags with commas', 'wp-docs' ),
						'add_or_remove_items'        => __( 'Add or remove tags', 'wp-docs' ),
						'choose_from_most_used'      => __( 'Choose from the most used tags', 'wp-docs' ),
						'not_found'                  => __( 'No tags found', 'wp-docs' ),
					),
					'show_ui'     => true,
					'query_var'   => true,
					'rewrite'     => array(
						'slug'       => 'docs-tag',
						'with_front' => false,
					),
				)
			)
		);

		do_action( 'bsf_docs_after_register_taxonomy' );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( ! is_blog_installed() || post_type_exists( 'bsf_docs' ) ) {
			return;
		}

		do_action( 'bsf_docs_register_post_type' );

		$supports = array(
			'title',
			'editor',
			'excerpt',
			'thumbnail',
			'custom-fields',
		);

		$has_comments = get_option( 'bsf_search_has_comments' );
		$has_comments = ! $has_comments ? false : $has_comments;

		if ( ! $has_comments ) {
			$supports[] = 'comments';
		}

		register_post_type(
			BSF_DOCS_POST_TYPE,
			apply_filters(
				'bsf_register_post_type_docs',
				array(
					'labels'              => array(
							'name'                  => __( 'Docs', 'wp-docs' ),
							'singular_name'         => __( 'Doc', 'wp-docs' ),
							'menu_name'             => _x( 'Docs', 'Admin menu name', 'wp-docs' ),
							'add_new'               => __( 'Add Doc', 'wp-docs' ),
							'add_new_item'          => __( 'Add New Doc', 'wp-docs' ),
							'edit'                  => __( 'Edit', 'wp-docs' ),
							'edit_item'             => __( 'Edit Doc', 'wp-docs' ),
							'new_item'              => __( 'New Doc', 'wp-docs' ),
							'view'                  => __( 'View Doc', 'wp-docs' ),
							'view_item'             => __( 'View Doc', 'wp-docs' ),
							'search_items'          => __( 'Search Docs', 'wp-docs' ),
							'not_found'             => __( 'No Docs found', 'wp-docs' ),
							'not_found_in_trash'    => __( 'No Docs found in trash', 'wp-docs' ),
							'parent'                => __( 'Parent Doc', 'wp-docs' ),
							'featured_image'        => __( 'Docs image', 'wp-docs' ),
							'set_featured_image'    => __( 'Set Docs image', 'wp-docs' ),
							'remove_featured_image' => __( 'Remove Docs image', 'wp-docs' ),
							'use_featured_image'    => __( 'Use as Docs image', 'wp-docs' ),
							'items_list'            => __( 'Docs list', 'wp-docs' ),
						),
					'description'         => __( 'This is where you can add new docs to your site.', 'wp-docs' ),
					'public'              => true,
					'show_ui'             => true,
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
					'query_var'           => true,
					'supports'            => $supports,
					'has_archive'         => true,
					'show_in_nav_menus'   => true,
					'show_in_rest'        => true,
				)
			)
		);

		do_action( 'bsf_docs_after_register_post_type' );
	}

	/**
	 * Added post type to allowed for rest api
	 *
	 * @param  array $post_types Get the docs post types.
	 * @return array
	 */
	public static function rest_api_allowed_post_types( $post_types ) {
		$post_types[] = 'docs';

		return $post_types;
	}
}

BSF_Docs_post_Type::init();


