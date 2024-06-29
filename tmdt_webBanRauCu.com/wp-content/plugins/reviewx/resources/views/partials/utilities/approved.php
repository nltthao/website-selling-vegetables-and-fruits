<div class="rx_comment_approve"><?php esc_html_e( 'Approved', 'reviewx' ); ?></div>

<?php 
    $meta_value = get_comment_meta($review_id);
    if( ! empty ($meta_value['reviewx_review_type'][0])) {
        if( $meta_value['reviewx_review_type'][0] === 'imported' ) :
?>
    <div class="rx_comment_imported"><?php esc_html_e( 'Imported', 'reviewx' ); ?></div>
<?php endif; } ?>