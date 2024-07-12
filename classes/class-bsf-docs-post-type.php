<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies.
 *
 * @class     BSF_Docs_post_Type
 * @category  Class
 * @author    Pratik Chaskar
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
				'hierarchical' => true,
				'label'        => __( 'Categories', 'bsf-docs' ),
				'labels'       => array(
					'name'              => __( 'Docs categories', 'bsf-docs' ),
					'singular_name'     => __( 'Category', 'bsf-docs' ),
					'menu_name'         => _x( 'Categories', 'Admin menu name', 'bsf-docs' ),
					'search_items'      => __( 'Search categories', 'bsf-docs' ),
					'all_items'         => __( 'All categories', 'bsf-docs' ),
					'parent_item'       => __( 'Parent category', 'bsf-docs' ),
					'parent_item_colon' => __( 'Parent category:', 'bsf-docs' ),
					'edit_item'         => __( 'Edit category', 'bsf-docs' ),
					'update_item'       => __( 'Update category', 'bsf-docs' ),
					'add_new_item'      => __( 'Add new category', 'bsf-docs' ),
					'new_item_name'     => __( 'New category name', 'bsf-docs' ),
					'not_found'         => __( 'No categories found', 'bsf-docs' ),
				),
				'show_ui'      => true,
				'query_var'    => true,
				'show_in_rest' => true,
				'rewrite'      => array(
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
				'bsf_taxonomy_args_docs_tag',
				array(
					'hierarchical' => false,
					'label'        => __( 'Docs tags', 'bsf-docs' ),
					'labels'       => array(
						'name'                       => __( 'Docs tags', 'bsf-docs' ),
						'singular_name'              => __( 'Tag', 'bsf-docs' ),
						'menu_name'                  => _x( 'Tags', 'Admin menu name', 'bsf-docs' ),
						'search_items'               => __( 'Search tags', 'bsf-docs' ),
						'all_items'                  => __( 'All tags', 'bsf-docs' ),
						'edit_item'                  => __( 'Edit tag', 'bsf-docs' ),
						'update_item'                => __( 'Update tag', 'bsf-docs' ),
						'add_new_item'               => __( 'Add new tag', 'bsf-docs' ),
						'new_item_name'              => __( 'New tag name', 'bsf-docs' ),
						'popular_items'              => __( 'Popular tags', 'bsf-docs' ),
						'separate_items_with_commas' => __( 'Separate tags with commas', 'bsf-docs' ),
						'add_or_remove_items'        => __( 'Add or remove tags', 'bsf-docs' ),
						'choose_from_most_used'      => __( 'Choose from the most used tags', 'bsf-docs' ),
						'not_found'                  => __( 'No tags found', 'bsf-docs' ),
					),
					'show_ui'      => true,
					'query_var'    => true,
					'show_in_rest' => true,
					'rewrite'      => array(
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
			'author',
			'revisions',
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
						'name'                  => __( 'Docs', 'bsf-docs' ),
						'singular_name'         => __( 'Doc', 'bsf-docs' ),
						'menu_name'             => _x( 'Docs', 'Admin menu name', 'bsf-docs' ),
						'add_new'               => __( 'Add Doc', 'bsf-docs' ),
						'add_new_item'          => __( 'Add New Doc', 'bsf-docs' ),
						'edit'                  => __( 'Edit', 'bsf-docs' ),
						'edit_item'             => __( 'Edit Doc', 'bsf-docs' ),
						'new_item'              => __( 'New Doc', 'bsf-docs' ),
						'view'                  => __( 'View Doc', 'bsf-docs' ),
						'view_item'             => __( 'View Doc', 'bsf-docs' ),
						'search_items'          => __( 'Search Docs', 'bsf-docs' ),
						'not_found'             => __( 'No Docs found', 'bsf-docs' ),
						'not_found_in_trash'    => __( 'No Docs found in trash', 'bsf-docs' ),
						'parent'                => __( 'Parent Doc', 'bsf-docs' ),
						'featured_image'        => __( 'Docs image', 'bsf-docs' ),
						'set_featured_image'    => __( 'Set Docs image', 'bsf-docs' ),
						'remove_featured_image' => __( 'Remove Docs image', 'bsf-docs' ),
						'use_featured_image'    => __( 'Use as Docs image', 'bsf-docs' ),
						'items_list'            => __( 'Docs list', 'bsf-docs' ),
					),
					'description'         => __( 'This is where you can add new docs to your site.', 'bsf-docs' ),
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


