<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

$cart          = WC()->cart->get_cart();

// Ensure $cart_item_key is set
$cart_item_key = isset( $cart_item_key ) ? $cart_item_key : '';

$item          = isset( $cart[ $cart_item_key ] ) ? $cart[ $cart_item_key ] : array();
$product_id    = isset( $item['product_id'] ) ? $item['product_id'] : 0;

$product       = wc_get_product( $product_id );

// Check if the product exist

if( $product ) {

	$thumbnail        = $product->get_image();

	$thumbnail_url    = esc_url( get_the_post_thumbnail_url( $product->id ) );

	$background_image =  'style=background-image:url("' . $thumbnail_url . '")';

	$product_name     =  $product->get_title();

	$product_price = strip_tags( wc_price( $product->get_price() ) );
} else {
	$thumbnail = $thumbnail_url = $background_image = $product_name = $product_price = '';
}
?>

<div class="woosupercharge-modal-content-container" <?php echo $listview === 'list' ? esc_attr( $background_image ) : ''; ?>>
  <h2><?php _e( 'Added to Cart', 'woosupercharge' ); ?></h2>
  <div class="woosupercharge-modal-body">
    <?php if ( $listview !== 'list' && $thumbnail ) { ?>
    <div class="woosupercharge-modal-image">
      <?php echo $thumbnail; ?>
    </div>
    <?php } ?>

    <div class="woosupercharge-modal-details">
      <h6><?php echo esc_html( $product_name ); ?></h6>
      <p><?php  echo  esc_html( $product_price ); ?></p>
    </div>
  </div>
  <a class="view-cart-btn" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'View Cart', 'woosupercharge' ); ?></a>
</div>
