<div class="wrap">
	<h2>
		<form class="rx-all-reviews-from" action="post">
			<input type="hidden" class="rx-product-id-input" name="productId" value="">
			<?php
				if( $_POST && $_POST["productId"] ){
		            $product_id = intval($_POST['productId']);
		            $product = wc_get_product( $product_id );
					$product_name = get_the_title($product_id);
					esc_html_e( $product_name, 'reviewx' );
		        } else {
		        	esc_html_e( 'All Reviews', 'reviewx' );
		        }
			?>
		</form>		
	</h2>
	<?php 
		wp_enqueue_script( 'admin-comments' );
		enqueue_comment_hotkeys_js();
	?>
	<?php $records->call_analytics_header(); ?>
	<?php $records->review_action_status(); ?>
	
	<form class="rx-all-reviews-from" method="post">
		<?php
			if(! isset($records)) { return; }
			$records->process_bulk_action();
			$records->prepare_items();
			$records->search_box( $searchBoxText, $searchColumn );
			$records->views();
			$records->display();
		?>
	</form>
	<?php wp_comment_trashnotice(); ?>
</div>