<?php
/**
 * Get terms walker
 *
 * @package Documentation/Order
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creating the sortable order.
 */
class BSF_TO_Terms_Walker extends Walker {

	/** Base ID of Terms.
	 *
	 * @var Array of terms.
	 **/
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
			/* Children depth. */

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
			/* Children depth. */

			$indent  = str_repeat( "\t", $depth );
			$output .= "$indent</ul>\n";
	}

	/**
	 * Start of term LI
	 *
	 * @param int $output Object list.
	 * @param int $term Get terms id.
	 * @param int $depth Child category.
	 * @param int $args number of arguments.
	 * @param int $current_object_id Current object id.
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
	 * @param int $output Category List.
	 * @param int $object Object list.
	 * @param int $depth Child category.
	 * @param int $args number of arguments.
	 */
	function end_el( &$output, $object, $depth = 0, $args = array() ) {
			$output .= "</li>\n";
	}

}
