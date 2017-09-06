<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies.
 *
 * @class     BSF_Docs_post_Type
 * @category  Class
 * @author    Brainstormforce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BSF_Docs_post_Type Class.
 */
class BSF_Docs_post_Type {

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

		register_taxonomy( 'docs_category',
			apply_filters( 'docs_category', array( BSF_DOCS_POST_TYPE ) ),
			array(
				'hierarchical'          => true,
				'label'                 => __( 'Categories', 'documentation-wordpress' ),
				'labels' => array(
						'name'              => __( 'Docs categories', 'documentation-wordpress' ),
						'singular_name'     => __( 'Category', 'documentation-wordpress' ),
						'menu_name'         => _x( 'Categories', 'Admin menu name', 'documentation-wordpress' ),
						'search_items'      => __( 'Search categories', 'documentation-wordpress' ),
						'all_items'         => __( 'All categories', 'documentation-wordpress' ),
						'parent_item'       => __( 'Parent category', 'documentation-wordpress' ),
						'parent_item_colon' => __( 'Parent category:', 'documentation-wordpress' ),
						'edit_item'         => __( 'Edit category', 'documentation-wordpress' ),
						'update_item'       => __( 'Update category', 'documentation-wordpress' ),
						'add_new_item'      => __( 'Add new category', 'documentation-wordpress' ),
						'new_item_name'     => __( 'New category name', 'documentation-wordpress' ),
						'not_found'         => __( 'No categories found', 'documentation-wordpress' ),
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

		register_taxonomy( 'docs_tag',
			apply_filters( 'bsf_taxonomy_objects_docs_tag', array( BSF_DOCS_POST_TYPE ) ),
			apply_filters( 'bsf_taxonomy_args_docs_tag', array(
				'hierarchical'          => false,
				'label'                 => __( 'Docs tags', 'documentation-wordpress' ),
				'labels'                => array(
						'name'                       => __( 'Docs tags', 'documentation-wordpress' ),
						'singular_name'              => __( 'Tag', 'documentation-wordpress' ),
						'menu_name'                  => _x( 'Tags', 'Admin menu name', 'documentation-wordpress' ),
						'search_items'               => __( 'Search tags', 'documentation-wordpress' ),
						'all_items'                  => __( 'All tags', 'documentation-wordpress' ),
						'edit_item'                  => __( 'Edit tag', 'documentation-wordpress' ),
						'update_item'                => __( 'Update tag', 'documentation-wordpress' ),
						'add_new_item'               => __( 'Add new tag', 'documentation-wordpress' ),
						'new_item_name'              => __( 'New tag name', 'documentation-wordpress' ),
						'popular_items'              => __( 'Popular tags', 'documentation-wordpress' ),
						'separate_items_with_commas' => __( 'Separate tags with commas', 'documentation-wordpress' ),
						'add_or_remove_items'        => __( 'Add or remove tags', 'documentation-wordpress' ),
						'choose_from_most_used'      => __( 'Choose from the most used tags', 'documentation-wordpress' ),
						'not_found'                  => __( 'No tags found', 'documentation-wordpress' ),
					),
				'show_ui'     => true,
				'query_var'   => true,
				'rewrite'     => array(
					'slug'       => 'docs-tag',
					'with_front' => false,
				),
			) )
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
			'custom-fields' 
		);

		$has_comments = get_option( 'bsf_search_has_comments' );
		$has_comments = !$has_comments ? false : $has_comments; 

		if( !$has_comments ) {
			$supports[] = 'comments';
		}

		register_post_type( BSF_DOCS_POST_TYPE,
			apply_filters( 'bsf_register_post_type_docs',
				array(
					'labels'              => array(
							'name'                  => __( 'Docs', 'documentation-wordpress' ),
							'singular_name'         => __( 'Doc', 'documentation-wordpress' ),
							'menu_name'             => _x( 'Docs', 'Admin menu name', 'documentation-wordpress' ),
							'add_new'               => __( 'Add Doc', 'documentation-wordpress' ),
							'add_new_item'          => __( 'Add New Doc', 'documentation-wordpress' ),
							'edit'                  => __( 'Edit', 'documentation-wordpress' ),
							'edit_item'             => __( 'Edit Doc', 'documentation-wordpress' ),
							'new_item'              => __( 'New Doc', 'documentation-wordpress' ),
							'view'                  => __( 'View Doc', 'documentation-wordpress' ),
							'view_item'             => __( 'View Doc', 'documentation-wordpress' ),
							'search_items'          => __( 'Search Docs', 'documentation-wordpress' ),
							'not_found'             => __( 'No Docs found', 'documentation-wordpress' ),
							'not_found_in_trash'    => __( 'No Docs found in trash', 'documentation-wordpress' ),
							'parent'                => __( 'Parent Doc', 'documentation-wordpress' ),
							'featured_image'        => __( 'Docs image', 'documentation-wordpress' ),
							'set_featured_image'    => __( 'Set Docs image', 'documentation-wordpress' ),
							'remove_featured_image' => __( 'Remove Docs image', 'documentation-wordpress' ),
							'use_featured_image'    => __( 'Use as Docs image', 'documentation-wordpress' ),
							'items_list'            => __( 'Docs list', 'documentation-wordpress' ),
						),
					'description'         => __( 'This is where you can add new docs to your site.', 'documentation-wordpress' ),
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
	 * @param  array $post_types
	 * @return array
	 */
	public static function rest_api_allowed_post_types( $post_types ) {
		$post_types[] = 'docs';

		return $post_types;
	}
}

BSF_Docs_post_Type::init();


