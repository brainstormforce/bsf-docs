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
				'label'                 => __( 'Categories', 'doc-wp' ),
				'labels' => array(
					'name'              => __( 'Docs categories', 'doc-wp' ),
					'singular_name'     => __( 'Category', 'doc-wp' ),
					'menu_name'         => _x( 'Categories', 'Admin menu name', 'doc-wp' ),
					'search_items'      => __( 'Search categories', 'doc-wp' ),
					'all_items'         => __( 'All categories', 'doc-wp' ),
					'parent_item'       => __( 'Parent category', 'doc-wp' ),
					'parent_item_colon' => __( 'Parent category:', 'doc-wp' ),
					'edit_item'         => __( 'Edit category', 'doc-wp' ),
					'update_item'       => __( 'Update category', 'doc-wp' ),
					'add_new_item'      => __( 'Add new category', 'doc-wp' ),
					'new_item_name'     => __( 'New category name', 'doc-wp' ),
					'not_found'         => __( 'No categories found', 'doc-wp' ),
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
					'label'                 => __( 'Docs tags', 'doc-wp' ),
					'labels'                => array(
						'name'                       => __( 'Docs tags', 'doc-wp' ),
						'singular_name'              => __( 'Tag', 'doc-wp' ),
						'menu_name'                  => _x( 'Tags', 'Admin menu name', 'doc-wp' ),
						'search_items'               => __( 'Search tags', 'doc-wp' ),
						'all_items'                  => __( 'All tags', 'doc-wp' ),
						'edit_item'                  => __( 'Edit tag', 'doc-wp' ),
						'update_item'                => __( 'Update tag', 'doc-wp' ),
						'add_new_item'               => __( 'Add new tag', 'doc-wp' ),
						'new_item_name'              => __( 'New tag name', 'doc-wp' ),
						'popular_items'              => __( 'Popular tags', 'doc-wp' ),
						'separate_items_with_commas' => __( 'Separate tags with commas', 'doc-wp' ),
						'add_or_remove_items'        => __( 'Add or remove tags', 'doc-wp' ),
						'choose_from_most_used'      => __( 'Choose from the most used tags', 'doc-wp' ),
						'not_found'                  => __( 'No tags found', 'doc-wp' ),
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
							'name'                  => __( 'Docs', 'doc-wp' ),
							'singular_name'         => __( 'Doc', 'doc-wp' ),
							'menu_name'             => _x( 'Docs', 'Admin menu name', 'doc-wp' ),
							'add_new'               => __( 'Add Doc', 'doc-wp' ),
							'add_new_item'          => __( 'Add New Doc', 'doc-wp' ),
							'edit'                  => __( 'Edit', 'doc-wp' ),
							'edit_item'             => __( 'Edit Doc', 'doc-wp' ),
							'new_item'              => __( 'New Doc', 'doc-wp' ),
							'view'                  => __( 'View Doc', 'doc-wp' ),
							'view_item'             => __( 'View Doc', 'doc-wp' ),
							'search_items'          => __( 'Search Docs', 'doc-wp' ),
							'not_found'             => __( 'No Docs found', 'doc-wp' ),
							'not_found_in_trash'    => __( 'No Docs found in trash', 'doc-wp' ),
							'parent'                => __( 'Parent Doc', 'doc-wp' ),
							'featured_image'        => __( 'Docs image', 'doc-wp' ),
							'set_featured_image'    => __( 'Set Docs image', 'doc-wp' ),
							'remove_featured_image' => __( 'Remove Docs image', 'doc-wp' ),
							'use_featured_image'    => __( 'Use as Docs image', 'doc-wp' ),
							'items_list'            => __( 'Docs list', 'doc-wp' ),
						),
					'description'         => __( 'This is where you can add new docs to your site.', 'doc-wp' ),
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


