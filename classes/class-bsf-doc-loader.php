<?php
/**
 * Responsible for setting up constants, classes and includes.
 *
 * @author Pratik Chaskar
 * @package Documentation/Loader
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! class_exists( 'Bsf_Doc_Loader' ) ) {

	/**
	 * Responsible for setting up constants, classes and includes.
	 *
	 * @since 1.0
	 */
	final class Bsf_Doc_Loader {

		/**
		 * The unique instance of the plugin.
		 *
		 * @var Instance variable
		 */
		private static $instance;

		/**
		 * Gets an instance of our plugin.
		 */
		public static function get_instance() {

			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 */
		private function __construct() {

			$this->define_constants();
			$this->load_files();
			$this->init_hooks();
			add_action( 'init', array( $this, 'bsf_callback_init' ) );

			do_action( 'bsf_docs_loaded' );
		}
		/**
		 * Callback function for overide templates.
		 *
		 * @category InitCallBack
		 */
		function bsf_callback_init() {

			$is_single_template_on = get_option( 'bsf_override_single_template' );
			$is_cat_template_on    = get_option( 'bsf_override_category_template' );

			if ( '1' == $is_single_template_on || false === $is_single_template_on ) {// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison

				add_filter( 'single_template', array( $this, 'get_bsf_docs_single_template' ), 99 );
				add_filter( 'body_class', array( $this, 'bsf_docs_body_single_class' ) );
			}

			if ( '1' == $is_cat_template_on || false === $is_cat_template_on ) {// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				add_filter( 'template_include', array( $this, 'category_template' ), 99 );
				add_filter( 'template_include', array( $this, 'tag_template' ), 99 );
				add_filter( 'body_class', array( $this, 'bsf_docs_body_tax_class' ) );
				add_filter( 'body_class', array( $this, 'bsf_docs_body_sidebar_class' ) );
			}

		}

		/**
		 * Initialization hooks
		 *
		 * @category Hooks
		 */
		function init_hooks() {
			register_activation_hook( BSF_DOCS_BASE_FILE, array( $this, 'activation' ) );
			add_action( 'admin_menu', array( $this, 'register_options_menu' ), 99 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_scripts' ), 10 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 10 );
			// Use this filter to overwrite archive page for bsf docs post type.
			add_filter( 'archive_template', array( $this, 'get_bsf_docs_archive_template' ) );
			// Call register settings function.
			add_action( 'admin_init', array( $this, 'register_bsf_docs_plugin_settings' ) );
		}

		/**
		 * Taxonomy Callback Function.
		 *
		 * @param array $template Overide taxonomy template.
		 */
		function category_template( $template ) {
			if ( is_tax( 'docs_category' ) ) {
				return BSF_DOCS_BASE_DIR . 'includes/taxonomy-bsf-docs-cat.php';
			}
			return $template;
		}

		/**
		 * Taxonomy Callback Function.
		 *
		 * @param array $template Overide taxonomy template.
		 */
		function tag_template( $template ) {
			if ( is_tax( 'docs_tag' ) ) {
				return BSF_DOCS_BASE_DIR . 'includes/taxonomy-bsf-docs-tag.php';
			}
			return $template;
		}

		/**
		 * Plugin activation hook.
		 *
		 * @author Pratik Chaskar
		 */
		function activation() {
			// Register post types.
			BSF_Docs_post_Type::register_post_types();
			BSF_Docs_post_Type::register_taxonomies();
			flush_rewrite_rules();

		}

		/**
		 * Add Class to body hooks
		 *
		 * @param array $classes It will add class to the body doc post.
		 * @category Hooks
		 * @return $classed
		 */
		function bsf_docs_body_single_class( $classes ) {

			if ( is_post_type_archive( 'docs' ) || is_singular( 'docs' ) && is_array( $classes ) ) {
					$cls = array_merge( $classes, array( 'docs-single-templates-enabled' ) );
				return $cls;
			}
			return $classes;
		}

		/**
		 * Processes this test, when one of its tokens is encountered.
		 *
		 * @param Class-bsf-docs-loader $classes load.
		 * @return $classes
		 */
		function bsf_docs_body_tax_class( $classes ) {
			if ( is_post_type_archive( 'docs' ) || is_tax( 'docs_category' ) || is_tax( 'docs_tag' ) && is_array( $classes ) ) {
				// Add clss to body.
				$cls = array_merge( $classes, array( 'docs-tax-templates-enabled' ) );
				return $cls;
			}
			return $classes;
		}

		/**
		 * Processes this test, when one of its tokens is encountered.
		 *
		 * @param Class-bsf-docs-loader $classes load.
		 * @return $classes
		 */
		function bsf_docs_body_sidebar_class( $classes ) {
			if ( is_post_type_archive( 'docs' ) || is_tax( 'docs_category' ) || is_tax( 'docs_tag' ) || is_singular( 'docs' ) && is_array( $classes ) ) {

				if ( is_active_sidebar( 'docs-sidebar-1' ) ) {
					// Add clss to body.
					$cls = array_merge( $classes, array( 'docs-sidebar-active' ) );
					return $cls;
				}
			}
			return $classes;
		}

		/**
		 * Register setting option variables.
		 */
		function register_bsf_docs_plugin_settings() {
			// Register our settings.
			register_setting( 'bsf-docs-settings-group', 'bsf_ls_enabled' );
			register_setting( 'bsf-docs-settings-group', 'bsf_search_post_types' );
			register_setting( 'bsf-docs-settings-group', 'bsf_search_has_comments' );
			register_setting( 'bsf-docs-settings-group', 'bsf_override_single_template' );
			register_setting( 'bsf-docs-settings-group', 'bsf_override_category_template' );
			register_setting( 'bsf-docs-settings-group', 'bsf_doc_title' );
		}

		/**
		 * Regsiter option menu
		 *
		 * @category Filter
		 */
		function register_options_menu() {
			add_submenu_page(
				'edit.php?post_type=docs',
				__( 'Settings', 'bsf-docs' ),
				__( 'Settings', 'bsf-docs' ),
				'manage_options',
				'bsf_docs_settings',
				array( $this, 'render_options_page' )
			);
		}

		/**
		 * Includes options page
		 */
		function render_options_page() {
			require_once BSF_DOCS_BASE_DIR . 'includes/bsf-options-page.php';
		}

		/**
		 * Get Archive Template for the docs base directory.
		 *
		 * @param int $archive_template Overirde archive templates.
		 * @author Pratik Chaskar
		 */
		function get_bsf_docs_archive_template( $archive_template ) {

			if ( is_post_type_archive( BSF_DOCS_POST_TYPE ) ) {
				$archive_template = BSF_DOCS_BASE_DIR . 'includes/bsf-archive-template.php';
			}
			return $archive_template;
		}

		/**
		 * Get Single Page Template for docs base directory.
		 *
		 * @param int $single_template Overirde single templates.
		 * @author Pratik Chaskar
		 */
		function get_bsf_docs_single_template( $single_template ) {

			if ( is_singular( 'docs' ) ) {
				$single_template = BSF_DOCS_BASE_DIR . 'includes/bsf-single-template.php';
			}
			return $single_template;
		}

		/**
		 * Renders an admin notice.
		 *
		 * @since 1.0
		 * @param string $message Error message.
		 * @param string $type Check type of user.
		 * @return void
		 */
		private function render_admin_notice( $message, $type = 'update' ) {

			if ( ! is_admin() ) {
				return;
			} elseif ( ! is_user_logged_in() ) {
				return;
			} elseif ( ! current_user_can( 'update_core' ) ) {
				return;
			}

			echo '<div class="' . $type . '">';
			echo '<p>' . $message . '</p>';
			echo '</div>';
		}

		/**
		 * Define constants.
		 *
		 * @since 1.0
		 * @return void
		 */
		private function define_constants() {

			$file = dirname( dirname( __FILE__ ) );

			define( 'BSF_DOCS_VERSION', '1.0.7' );
			define( 'BSF_DOCS_DIR_NAME', plugin_basename( $file ) );
			define( 'BSF_DOCS_BASE_FILE', trailingslashit( $file ) . BSF_DOCS_DIR_NAME . '.php' );
			define( 'BSF_DOCS_BASE_DIR', plugin_dir_path( BSF_DOCS_BASE_FILE ) );
			define( 'BSF_DOCS_BASE_URL', plugins_url( '/', BSF_DOCS_BASE_FILE ) );
			define( 'BSF_DOCS_POST_TYPE', 'docs' );
		}

		/**
		 * Loads classes and includes.
		 *
		 * @since 1.0
		 * @return void
		 */
		static private function load_files() {

			require_once BSF_DOCS_BASE_DIR . 'classes/class-bsf-docs-post-type.php';
			require_once BSF_DOCS_BASE_DIR . 'includes/bsf-docs-shortcode.php';
			require_once BSF_DOCS_BASE_DIR . 'classes/class-bsf-docs-widget.php';
			require_once BSF_DOCS_BASE_DIR . 'classes/class-bsf-docs-cat-widget.php';
		}

		/**
		 * Enqueue frontend scripts
		 *
		 * @since 1.0
		 */
		function enqueue_front_scripts() {
			wp_register_style( 'bsf-frontend-style', BSF_DOCS_BASE_URL . 'assets/css/frontend.css' );

			$is_live_search = get_option( 'bsf_ls_enabled' );

			if ( '1' == $is_live_search || false === $is_live_search ) {// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison

				wp_register_script( 'bsf-live-search', BSF_DOCS_BASE_URL . 'assets/js/jquery.livesearch.js', array( 'jquery' ), BSF_DOCS_VERSION, true );
				wp_register_script( 'bsf-searchbox-script', BSF_DOCS_BASE_URL . 'assets/js/searchbox-script.js', array( 'bsf-live-search' ), BSF_DOCS_VERSION, true );

				wp_localize_script(
					'bsf-searchbox-script',
					'bsf_ajax_url',
					array(
						'url' => admin_url( 'admin-ajax.php' ),
					)
				);
			}
		}

		/**
		 * Enqueue admin scripts.
		 *
		 * @since 1.0
		 */
		function enqueue_admin_scripts() {
			wp_register_style( 'bsf-options-style', BSF_DOCS_BASE_URL . 'assets/css/admin.css' );
		}
	}

	$bsf_doc_loader = Bsf_Doc_Loader::get_instance();
}

