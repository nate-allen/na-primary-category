<input type="hidden" class="na-primary-category-id" name="na_primary_category_id" value="<?php echo esc_attr( $primary_category ); ?>">
<?php wp_nonce_field( "set_primary_category_{$post_id}", 'na_primary_category_nonce', false ); ?>
