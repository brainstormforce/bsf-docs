<?php
/**
 * Category sort options page
 *
 * @package Category sort options page
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Category order INIT
 */
function bsf_docs_category_order_init() {

	/**
	 * Category order menu
	 */
	function bsf_docs_category_order_menu() {
		if ( function_exists( 'add_submenu_page' ) ) {
			add_submenu_page( 'edit.php?post_type=docs', 'Category Order', 'Category Order', 4, 'bsf_docs_category_order_options', 'bsf_docs_category_order_options' );
		}
		wp_enqueue_script( 'bsf-docs-backend', BSF_DOCS_BASE_URL . 'assets/js/backend.js', array( 'jquery', 'jquery-ui-sortable' ), false, false );
		wp_localize_script( 'bsf-docs-backend', 'BSFDocs', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

	}
	add_action( 'admin_menu', 'bsf_docs_category_order_menu' );

	/**
	 * Order option script
	 */
	function bsf_docs_category_order_options() {
		wp_enqueue_script( 'jquery' );
		bsf_to_plugin_interface();

	}

}

/**
 * Taxonomy Inter
 */
function bsf_to_plugin_interface() {

	global $wpdb, $wp_locale;
	$taxonomy       = isset( $_GET['taxonomy'] ) ? sanitize_key( $_GET['taxonomy'] ) : '';
	$post_type      = isset( $_GET['post_type'] ) ? sanitize_key( $_GET['post_type'] ) : '';
	$post_type_data = get_post_type_object( $post_type );

	if ( ! taxonomy_exists( $taxonomy ) ) {
		$taxonomy = '';
	}

			?>
		<div class="wrap">
			<div class="icon32" id="icon-edit"><br></div>
			<h2><?php _e( 'Docs Category Order', 'bsf-docs' ); ?></h2>
			<p><?php _e( 'Drag each item into the order you prefer, and click the update button.', 'bsf-docs' ); ?></p>
			<div id="ajax-response"></div>
			<noscript>
				   <div class="error message">
					   <p><?php _e( "This plugin can't work without javascript, because it's use drag and drop and AJAX.", 'bsf-docs' ); ?></p>
				</div>
			</noscript>

			   <div class="clear"></div>
				
				<?php

				   $current_section_parent_file = '';
				switch ( $post_type ) {

					case 'attachment':
									$current_section_parent_file = 'upload.php';
						break;

					default:
									$current_section_parent_file = 'edit.php';
						break;
				}

				?>
				
				<form action="<?php echo $current_section_parent_file; ?>" method="get" id="to_form">
				   <input type="hidden" name="page" value="to-interface-<?php echo esc_attr( $post_type ); ?>" />
					<?php

					if ( ! in_array( $post_type, array( 'post', 'attachment' ) ) ) {
						echo '<input type="hidden" name="post_type" value="' . esc_attr( $post_type ) . '" />';
					}

					// output all available taxonomies for this post type.
					$post_type_taxonomies = get_object_taxonomies( $post_type );

					foreach ( $post_type_taxonomies as $key => $taxonomy_name ) {
							$taxonomy_info = get_taxonomy( $taxonomy_name );
						if ( $taxonomy_info->hierarchical !== true ) {
							unset( $post_type_taxonomies[ $key ] );
						}
					}

					// use the first taxonomy if emtpy taxonomy.
					if ( $taxonomy == '' || ! taxonomy_exists( $taxonomy ) ) {
							reset( $post_type_taxonomies );
							$taxonomy = current( $post_type_taxonomies );
					}

					if ( count( $post_type_taxonomies ) > 1 ) {

							?>
							
							<h2 class="subtitle"><?php echo ucfirst( $post_type_data->labels->name ); ?> <?php _e( 'Taxonomies', 'bsf-docs' ); ?></h2>
							<table cellspacing="0" class="wp-list-taxonomy">
								<thead>
								<tr>
									<th style="" class="column-cb check-column" id="cb" scope="col">&nbsp;</th><th style="" class="" id="author" scope="col"><?php _e( 'Taxonomy Title', 'bsf-docs' ); ?></th><th style="" class="manage-column" id="categories" scope="col"><?php _e( 'Total Posts', 'bsf-docs' ); ?></th>    </tr>
								</thead>

   
								<tbody id="the-list">
								<?php

									$alternate = false;
								foreach ( $post_type_taxonomies as $post_type_taxonomy ) {
										$taxonomy_info = get_taxonomy( $post_type_taxonomy );

										$alternate = $alternate === true ? false : true;

										$args           = array(
											'hide_empty' => 0,
											'taxonomy'   => $post_type_taxonomy,
										);
										$taxonomy_terms = get_terms( $args );

										?>
											<tr valign="top" class="
											<?php
											if ( $alternate === true ) {
												echo 'alternate ';}
?>
" id="taxonomy-<?php echo esc_attr( $taxonomy ); ?>">
													<th class="check-column" scope="row"><input type="radio" onclick="to_change_taxonomy(this)" value="<?php echo $post_type_taxonomy; ?>" 
																																									<?php
																																									if ( $post_type_taxonomy == $taxonomy ) {
																																										echo 'checked="checked"';}
?>
 name="taxonomy">&nbsp;</th>
													<td class="categories column-categories"><b><?php echo $taxonomy_info->label; ?></b> (<?php echo  $taxonomy_info->labels->singular_name; ?>)</td>
													<td class="categories column-categories"><?php echo count( $taxonomy_terms ); ?></td>
											</tr>
											
											<?php
								}
								?>
								</tbody>
							</table>
							<br />
							<?php
					}
							?>

				<div id="order-terms">
					<div id="post-body">                    
							<ul class="sortable" id="bsf-to-sortable">
								<?php
								   BSFlistTerms( $taxonomy );
								?>
							</ul>
							<div class="clear"></div>
					</div>
					
					<div class="alignleft actions">
						<p class="submit">
							<a href="javascript:;" class="save-order button-primary"><?php _e( 'Update', 'bsf-docs' ); ?></a>
						</p>
					</div>
					
				</div> 

				</form>
				
			</div>
			<?php

}

/**
 * Taxonomy List
 *
 * @param $taxonomy
 */
function BSFlistTerms( $taxonomy ) {

			// Query pages.
			$args           = array(
				'orderby'    => 'term_order',
				'depth'      => 0,
				'child_of'   => 0,
				'hide_empty' => 0,
			);
			$taxonomy_terms = get_terms( $taxonomy, $args );

			$output = '';
	if ( count( $taxonomy_terms ) > 0 ) {
			$output = bsf_to_walktree( $taxonomy_terms, $args['depth'], $args );
	}

			echo $output;

}

/**
 * Taxonomy List
 *
 * @param int $taxonomy_terms
 * @param int $depth Child taxonomy
 * @param int $r
 */
function bsf_to_walktree( $taxonomy_terms, $depth, $r ) {
		$walker = new BSF_TO_Terms_Walker;
		$args   = array( $taxonomy_terms, $depth, $r );
		return call_user_func_array( array( &$walker, 'walk' ), $args );
}

/**
 * Term sortable
 */
class BSF_TO_Terms_Walker extends Walker {

		   var $db_fields = array(
			   'parent' => 'parent',
			   'id'     => 'term_id',
		   );

	/**
	 * Start of ul
	 *
	 * @param int $output Category List.
	 * @param int $depth children.
	 * @param int $args Sortable arguments.
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
			/* Childern depth*/
			extract( $args, EXTR_SKIP );

			$indent  = str_repeat( "\t", $depth );
			$output .= "\n$indent<ul class='children sortable'>\n";
	}

	/**
	 * End of ul
	 *
	 * @param int $output Category List.
	 * @param int $depth children.
	 * @param int $args Sortable arguments.
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
			extract( $args, EXTR_SKIP );

			$indent  = str_repeat( "\t", $depth );
			$output .= "$indent</ul>\n";
	}

	/**
	 * Start of term LI
	 *
	 * @param int    $args Sortable arguments.
	 * @param int    $output Category List.
	 * @param int    $term Get terms id.
	 * @param int    $depth String repeat.
	 * @param object $current_object_id.
	 */
	function start_el( &$output, $term, $depth = 0, $args = array(), $current_object_id = 0 ) {
		if ( $depth ) {
			$indent = str_repeat( "\t", $depth );
		} else {
			$indent = '';
		}

			$taxonomy = get_taxonomy( $term->term_taxonomy_id );
			$output  .= $indent . '<li class="term_type_li" id="item_' . $term->term_id . '"><div class="item"><span>' . apply_filters( 'to_term_title', $term->name, $term ) . ' </span></div>';
	}

	/**
	 * End of term LI
	 *
	 * @param int    $output Category List.
	 * @param object $object
	 * @param int    $depth
	 * @param int    $args
	 */
	function end_el( &$output, $object, $depth = 0, $args = array() ) {
			$output .= "</li>\n";
	}

}


/**
 * Update order in db.
 *
 * @param int $orderby update db query.
 * @param int $args arguments for query.
 */
function bsf_applyorderfilter( $orderby, $args ) {
	if ( apply_filters( 'to_get_terms_orderby_ignore', false, $orderby, $args ) ) {
		return $orderby;
	}

	if ( ( ! isset( $args['ignore_term_order'] ) || ( isset( $args['ignore_term_order'] ) && $args['ignore_term_order'] !== true ) ) ) {
			return 't.term_order';
	}

		return $orderby;
}

/**
 * Get terms order.
 *
 * @param int $orderby update db query.
 * @param int $args arguments for query.
 */
function bsf_get_terms_orderby( $orderby, $args ) {
	if ( apply_filters( 'to_get_terms_orderby_ignore', false, $orderby, $args ) ) {
		return $orderby;
	}

	if ( isset( $args['orderby'] ) && $args['orderby'] == 'term_order' && $orderby != 'term_order' ) {
		return 't.term_order';
	}

		return $orderby;
}

add_filter( 'get_terms_orderby', 'bsf_applyorderfilter', 10, 2 );
add_filter( 'get_terms_orderby', 'bsf_get_terms_orderby', 1, 2 );
add_action( 'wp_ajax_update-taxonomy-order', 'bsf_save_ajax_order' );

/**
 * Admin Ajax loader.
 */
function bsf_save_ajax_order() {
		global $wpdb;

		$data              = stripslashes( $_POST['order'] );
		$unserialised_data = json_decode( $data, true );

	if ( is_array( $unserialised_data ) ) {
		foreach ( $unserialised_data as $key => $values ) {
				$items = explode( '&', $values );
				unset( $item );
			foreach ( $items as $item_key => $item_ ) {
					$items[ $item_key ] = trim( str_replace( 'item[]=', '', $item_ ) );
			}

			if ( is_array( $items ) && count( $items ) > 0 ) {
				foreach ( $items as $item_key => $term_id ) {
						$wpdb->update( $wpdb->terms, array( 'term_order' => ( $item_key + 1 ) ), array( 'term_id' => $term_id ) );
				}
			}
		}
	}

		do_action( 'tto_update_order' );

		die();
}

add_action( 'init', 'bsf_docs_category_order_init' );
