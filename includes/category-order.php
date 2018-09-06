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
		// var_dump($allthecategories);
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

?>