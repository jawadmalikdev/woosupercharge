<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

$cart = WC()->cart->get_cart();

$item = $cart[ $cart_item_key ];

$product = $item['data'];

$thumbnail = $product->get_image();

$thumbnail_url = esc_url( get_the_post_thumbnail_url( $product->id ) );

$background_image = 'style=background-image:url("' . $thumbnail_url . '")';

$product_name = $product->get_title();

$product_price = WC()->cart->get_product_price( $product );

?>

<div class="woosupercharge-modal-content-container" <?php echo $listview === 'list' ? $background_image : ''; ?> >
  <h2><?php __( 'Added to Cart', 'woosupercharge') ?>Added to Cart</h2>
  <div class="woosupercharge-modal-body">
	<?php if ( $listview !== 'list' ) { ?>
	<div class="woosupercharge-modal-image">
		<?php echo $thumbnail; ?>
	</div>
	<?php } ?>

	<div class="woosupercharge-modal-details">
	  <h6> <?php echo esc_html( $product_name ); ?> </h6>
	  <p> <?php  echo $product_price; ?> </p>
	</div>
  </div>
  <a class="view-cart-btn" href="<?php echo wc_get_cart_url(); ?>">View Cart</a>
</div>







