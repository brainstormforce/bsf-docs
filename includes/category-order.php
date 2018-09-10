<?php
function bsf_docs_category_order_init(){
	function bsf_docs_category_order_menu(){
		if (function_exists('add_submenu_page')) {
			add_submenu_page("edit.php?post_type=docs", 'Category Order', 'Category Order', 4, "bsf_docs_category_order_options", 'bsf_docs_category_order_options');
		}
	}

	function bsf_docs_category_order_scriptaculous() {
		if($_GET['page'] == "bsf_docs_category_order_options"){
			wp_enqueue_script('scriptaculous');
		} 
	}
	
	add_action('admin_head', 'bsf_docs_category_order_options_head'); 
	add_action('admin_menu', 'bsf_docs_category_order_menu');
	add_action('admin_menu', 'bsf_docs_category_order_scriptaculous');


	add_action('admin_menu', 'bsf_docs_enqueue_scripts');
	function bsf_docs_enqueue_scripts() {
		wp_enqueue_script( 'bsf-docs-backend', BSF_DOCS_BASE_URL . 'assets/js/backend.js', array( 'jquery', 'jquery-ui-sortable' ), false, false );

		wp_localize_script( 'bsf-docs-backend', 'BSFDocs', array( 'ajaxurl' => admin_url('admin-ajax.php') ) );

		
	}

	
	add_filter('get_terms', 'bsf_docs_category_order_reorder', 10, 3);
	
	// This is the main function. It's called every time the get_terms function is called.
	function bsf_docs_category_order_reorder($terms, $taxonomies, $args){

		// No need for this if we're in the ordering page.
		if(isset($_GET['page']) && $_GET['page'] == "bsf_docs_category_order_options"){ 
			return $terms;
		}

		// Apply to categories only and only if they're ordered by name.
		if($taxonomies[0] == "docs_category" && $args['orderby'] == 'name'){ // You may change this line for: `if($taxonomies[0] == "category" && $args['orderby'] == 'custom'){` if you wish to still be able to order by name.
			$options = get_option("bsf_category_order");
		
			if(!empty($options)){
				
				// Put all the order strings together
				$master = "";
				foreach($options as $id => $option){
					$master .= $option.",";
				}
				
				$ids = explode(",", $master);
				
				// Add an 'order' item to every category
				$i=0;
				foreach($ids as $id){
					if($id != ""){
						foreach($terms as $n => $category){
							if(is_object($category) && $category->term_id == $id){
								$terms[$n]->order = $i;
								$i++;
							}
						}
					}
					
					// Add order 99999 to every category that wasn't manually ordered (so they appear at the end). This just usually happens when you've added a new category but didn't order it.
					foreach($terms as $n => $category){
						if(is_object($category) && !isset($category->order)){
							$terms[$n]->order = 99999;
						}
					}
				
				}
				
				// Sort the array of categories using a callback function
				usort($terms, "bsf_docs_category_order_compare");
			}
		
		}
		
		return $terms;
	}
	
	// Compare function. Used to order the categories array.
	function bsf_docs_category_order_compare($a, $b) {
		
		if ($a->order == $b->order) {
			
			if($a->name == $b->name){
				return 0;
			}else{
				return ($a->name < $b->name) ? -1 : 1;
			}
			
		}
		
	    return ($a->order < $b->order) ? -1 : 1;
	}
	
	function bsf_docs_category_order_options(){
		if(isset($_GET['childrenOf'])){
			$childrenOf = $_GET['childrenOf'];
		}else{
			$childrenOf = 0;
		}
		
		$options = get_option("bsf_category_order");
		$order = $options[$childrenOf];
		
		
		if(isset($_GET['submit'])){
			$options[$childrenOf] = $order = $_GET['category_order'];
			update_option("bsf_category_order", $options);
			$updated = true;
		}
		
		// Get the parent ID of the current category and the name of the current category.
		$allthecategories = get_categories( array(
									    'taxonomy' => 'docs_category',
									    'hide_empty' => false,
									) );
			// echo "<pre>";
			// print_r( $allthecategories );
			// echo "</pre>";

		if($childrenOf != 0){
			foreach($allthecategories as $category){
				if($category->cat_ID == $childrenOf){
					$father = $category->parent;
					$current_name = $category->name;
				}
			}
			
		}
		
		// Get only the categories belonging to the current category
		$categories = get_categories(  array(
									    'taxonomy' => 'docs_category',
									    'hide_empty' => false,
									) );
		// Order the categories.
		if($order){
			$order_array = explode(",", $order);
		
			$i=0;
		
			foreach($order_array as $id){
				foreach($categories as $n => $category){
					if(is_object($category) && $category->term_id == $id){
						$categories[$n]->order = $i;
						$i++;
					}
				}
				
				
				foreach($categories as $n => $category){
					if(is_object($category) && !isset($category->order)){
						$categories[$n]->order = 99999;
					}
				}

			}
			
			usort($categories, "bsf_docs_category_order_compare");
			
			
		}
		
		?>
		
		<div class='wrap'>
			
			<?php if(isset($updated) && $updated == true): ?>
				<div id="message" class="fade updated"><p>Changes Saved.</p></div>
			<?php endif; ?>
			
			<?php // $url = bloginfo("wpurl") . '/wp-admin/edit.php?post_type=docs&page=bsf_docs_category_order_options'; ?>
			<?php $url = admin_url( 'edit.php?post_type=docs&page=bsf_docs_category_order_options' ); ?>
			
			<form action="<?php echo $url; ?>" class="GET">
				<input type="hidden" name="post_type" value="docs" />
				<input type="hidden" name="page" value="bsf_docs_category_order_options" />
				<input type="hidden" id="category_order" name="category_order" size="500" value="<?php echo $order; ?>">
				<input type="hidden" name="childrenOf" value="<?php echo $childrenOf; ?>" />
			<h2>Category Order</h2>
			
			<?php if($childrenOf != 0): ?>
			<p><a href="<?php echo $url; ?>&childrenOf=<?php echo $father; ?>">&laquo; Back</a></p>
			<h3><?php echo $current_name; ?></h3>
			<?php else: ?>
			<?php endif; ?>
			
			<div id="container">
				<div id="order">
					<?php
					foreach($categories as $category){
						
						if($category->parent == $childrenOf){
							//var_dump(get_categories("hide_empty=0&child_of=$category->cat_ID"));
							echo "<div id='item_$category->cat_ID' class='bsf-lineitem'>";
							// if(get_categories("hide_empty=0&child_of=$category->cat_ID")){
							// 	echo "<span class=\"childrenlink\"><a href=\"".get_bloginfo("wpurl")."/wp-admin/edit.php?post_type=docs&page=bsf_docs_category_order_options&childrenOf=$category->cat_ID\">More &raquo;</a></span>";
							// }
							echo "<h4>$category->name</h4>";
							echo "</div>";
							
						}
					}
					?>
				</div>
				<p class="submit" ><input type="submit" name="submit" class="button button-primary" Value="Order Categories"></p>
			</div>
			</form>
		</div>

		<?php


		wp_enqueue_script( 'jquery' );

		TOPluginInterface();


		?>
		<style type="text/css">#tto_sortable li {
    display: block;
    background: #fff;
    padding: .5em 1em;
}

#tto_sortable ul li {
    background: #f1f1f1;
}</style>
<?php













	}
	
	// The necessary CSS and Javascript
	function bsf_docs_category_order_options_head(){
		if(isset($_GET['page']) && $_GET['page'] == "bsf_docs_category_order_options"){
		?>
		<script language="JavaScript">
			window.onload = function(){
				Sortable.create('order',{tag:'div', onChange: function(){ refreshOrder(); }});
			
				function refreshOrder(){
					$("category_order").value = Sortable.sequence('order');
				}
			}
		</script>
		<?php
		}
	}
	
}

add_action('plugins_loaded', 'bsf_docs_category_order_init');


























 function TOPluginInterface()
        {
            global $wpdb, $wp_locale;
            
            $taxonomy = isset($_GET['taxonomy']) ? sanitize_key($_GET['taxonomy']) : '';
            $post_type = isset($_GET['post_type']) ? sanitize_key($_GET['post_type']) : '';
            if(empty($post_type))
                {
                    $screen = get_current_screen();
                    
                    if(isset($screen->post_type)    && !empty($screen->post_type))
                        $post_type  =   $screen->post_type;
                        else
                        {
                            switch($screen->parent_file)
                                {
                                    case "upload.php" :
                                                        $post_type  =   'attachment';
                                                        break;
                                                
                                    default:
                                                        $post_type  =   'post';   
                                }
                        }       
                } 
                                            
            $post_type_data = get_post_type_object($post_type);
            
            if (!taxonomy_exists($taxonomy))
                $taxonomy = '';

            ?>
            <div class="wrap">
                <div class="icon32" id="icon-edit"><br></div>
                <h2><?php _e( "Taxonomy Order", 'taxonomy-terms-order' ) ?></h2>

                <?php // tto_info_box() ?>
                
                <div id="ajax-response"></div>
                
                <noscript>
                    <div class="error message">
                        <p><?php _e( "This plugin can't work without javascript, because it's use drag and drop and AJAX.", 'taxonomy-terms-order' ) ?></p>
                    </div>
                </noscript>

                <div class="clear"></div>
                
                <?php
                
                    $current_section_parent_file    =   '';
                    switch($post_type)
                        {
                            
                            case "attachment" :
                                            $current_section_parent_file    =   "upload.php";
                                            break;
                                            
                            default :
                                            $current_section_parent_file    =    "edit.php";
                                            break;
                        }
                
                
                ?>
                
                <form action="<?php echo $current_section_parent_file ?>" method="get" id="to_form">
                    <input type="hidden" name="page" value="to-interface-<?php echo esc_attr($post_type) ?>" />
                    <?php
                
                     if (!in_array($post_type, array('post', 'attachment'))) 
                        echo '<input type="hidden" name="post_type" value="'. esc_attr($post_type) .'" />';

                    //output all available taxonomies for this post type
                    
                    $post_type_taxonomies = get_object_taxonomies($post_type);
                
                    foreach ($post_type_taxonomies as $key => $taxonomy_name)
                        {
                            $taxonomy_info = get_taxonomy($taxonomy_name);  
                            if ($taxonomy_info->hierarchical !== TRUE) 
                                unset($post_type_taxonomies[$key]);
                        }
                        
                    //use the first taxonomy if emtpy taxonomy
                    if ($taxonomy == '' || !taxonomy_exists($taxonomy))
                        {
                            reset($post_type_taxonomies);   
                            $taxonomy = current($post_type_taxonomies);
                        }
                                            
                    if (count($post_type_taxonomies) > 1)
                        {
                
                            ?>
                            
                            <h2 class="subtitle"><?php echo ucfirst($post_type_data->labels->name) ?> <?php _e( "Taxonomies", 'taxonomy-terms-order' ) ?></h2>
                            <table cellspacing="0" class="wp-list-taxonomy">
                                <thead>
                                <tr>
                                    <th style="" class="column-cb check-column" id="cb" scope="col">&nbsp;</th><th style="" class="" id="author" scope="col"><?php _e( "Taxonomy Title", 'taxonomy-terms-order' ) ?></th><th style="" class="manage-column" id="categories" scope="col"><?php _e( "Total Posts", 'taxonomy-terms-order' ) ?></th>    </tr>
                                </thead>

   
                                <tbody id="the-list">
                                <?php
                                    
                                    $alternate = FALSE;
                                    foreach ($post_type_taxonomies as $post_type_taxonomy)
                                        {
                                            $taxonomy_info = get_taxonomy($post_type_taxonomy);

                                            $alternate = $alternate === TRUE ? FALSE :TRUE;
                                            
                                            $args = array(
                                                        'hide_empty'    =>  0,
                                                        'taxonomy'      =>  $post_type_taxonomy
                                                        );
                                            $taxonomy_terms = get_terms( $args );
                                                             
                                            ?>
                                                <tr valign="top" class="<?php if ($alternate === TRUE) {echo 'alternate ';} ?>" id="taxonomy-<?php echo esc_attr($taxonomy)  ?>">
                                                        <th class="check-column" scope="row"><input type="radio" onclick="to_change_taxonomy(this)" value="<?php echo $post_type_taxonomy ?>" <?php if ($post_type_taxonomy == $taxonomy) {echo 'checked="checked"';} ?> name="taxonomy">&nbsp;</th>
                                                        <td class="categories column-categories"><b><?php echo $taxonomy_info->label ?></b> (<?php echo  $taxonomy_info->labels->singular_name; ?>)</td>
                                                        <td class="categories column-categories"><?php echo count($taxonomy_terms) ?></td>
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
                        
                            <ul class="sortable" id="tto_sortable">
                                <?php 
                                    listTerms($taxonomy); 
                                ?>
                            </ul>
                            
                            <div class="clear"></div>
                    </div>
                    
                    <div class="alignleft actions">
                        <p class="submit">
                            <a href="javascript:;" class="save-order button-primary"><?php _e( "Update", 'taxonomy-terms-order' ) ?></a>
                        </p>
                    </div>
                    
                </div> 

                </form>
                
            </div>
            <?php 
            
            
        }
    
    
    function listTerms($taxonomy) 
            {

                // Query pages.
                $args = array(
                            'orderby'       =>  'term_order',
                            'depth'         =>  0,
                            'child_of'      => 0,
                            'hide_empty'    =>  0
                );
                $taxonomy_terms = get_terms($taxonomy, $args);

                $output = '';
                if (count($taxonomy_terms) > 0)
                    {
                        $output = TOwalkTree($taxonomy_terms, $args['depth'], $args);    
                    }

                echo $output; 
                
            }
        
        function TOwalkTree($taxonomy_terms, $depth, $r) 
            {
                $walker = new TO_Terms_Walker; 
                $args = array($taxonomy_terms, $depth, $r);
                return call_user_func_array(array(&$walker, 'walk'), $args);
            }




 class TO_Terms_Walker extends Walker 
        {

            var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');


            function start_lvl(&$output, $depth = 0, $args = array() )
                {
                    extract($args, EXTR_SKIP);
                    
                    $indent = str_repeat("\t", $depth);
                    $output .= "\n$indent<ul class='children sortable'>\n";
                }


            function end_lvl(&$output, $depth = 0, $args = array())
                {
                    extract($args, EXTR_SKIP);
                        
                    $indent = str_repeat("\t", $depth);
                    $output .= "$indent</ul>\n";
                }


            function start_el(&$output, $term, $depth = 0, $args = array(), $current_object_id = 0) 
                {
                    if ( $depth )
                        $indent = str_repeat("\t", $depth);
                    else
                        $indent = '';

                    //extract($args, EXTR_SKIP);
                    $taxonomy = get_taxonomy($term->term_taxonomy_id);
                    $output .= $indent . '<li class="term_type_li" id="item_'.$term->term_id.'"><div class="item"><span>'.apply_filters( 'to/term_title', $term->name, $term ).' </span></div>';
                }


            function end_el(&$output, $object, $depth = 0, $args = array()) 
                {
                    $output .= "</li>\n";
                }

        }














    function TO_applyorderfilter($orderby, $args)
        {
	        if ( apply_filters('to/get_terms_orderby/ignore', FALSE, $orderby, $args) )
                return $orderby;
            
            
            //if autosort, then force the menu_order
            if (  (!isset($args['ignore_term_order']) ||  (isset($args['ignore_term_order'])  &&  $args['ignore_term_order']  !== TRUE) ))
                {
                    return 't.term_order';
                }
                
            return $orderby; 
        }

    add_filter('get_terms_orderby', 'TO_applyorderfilter', 10, 2);

    add_filter('get_terms_orderby', 'TO_get_terms_orderby', 1, 2);
    function TO_get_terms_orderby($orderby, $args)
        {
            if ( apply_filters('to/get_terms_orderby/ignore', FALSE, $orderby, $args) )
                return $orderby;
                
            if (isset($args['orderby']) && $args['orderby'] == "term_order" && $orderby != "term_order")
                return "t.term_order";
                
            return $orderby;
        }

    add_action( 'wp_ajax_update-taxonomy-order', 'TOsaveAjaxOrder' );
    function TOsaveAjaxOrder()
        {
            global $wpdb;
            
            // if  ( ! wp_verify_nonce( $_POST['nonce'], 'update-taxonomy-order' ) )
            //     die();
             
            $data               = stripslashes($_POST['order']);
            $unserialised_data  = json_decode($data, TRUE);
                    
            if (is_array($unserialised_data))
            foreach($unserialised_data as $key => $values ) 
                {
                    //$key_parent = str_replace("item_", "", $key);
                    $items = explode("&", $values);
                    unset($item);
                    foreach ($items as $item_key => $item_)
                        {
                            $items[$item_key] = trim(str_replace("item[]=", "",$item_));
                        }
                    
                    if (is_array($items) && count($items) > 0)
                    foreach( $items as $item_key => $term_id ) 
                        {
                            $wpdb->update( $wpdb->terms, array('term_order' => ($item_key + 1)), array('term_id' => $term_id) );
                        } 
                }
                
            do_action('tto/update-order');
                
            die();
        }