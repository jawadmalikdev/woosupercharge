<?php
/**
 * Cart view template.
 *
 * @since 2.0
 * @package woosupercharge
 */

namespace JawadMalik\Woosupercharge\Templates;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<div class="woosupercharge-modal-content-container" <?php echo esc_attr( $modal_style ); ?>>
	<h2><?php esc_html_e( 'Added to Cart', 'woosupercharge' ); ?></h2>
	<div class="woosupercharge-modal-body">
	<?php if ( $show_thumbnail ) : ?>
		<div class="woosupercharge-modal-image">
		<?php echo wp_kses( $thumbnail, $allowed_thumbnail_html ); ?>
		</div>
	<?php endif; ?>
	
	<div class="woosupercharge-modal-details">
		<h6><?php echo esc_html( $product_name ); ?></h6>
		<p><?php echo esc_html( $product_price ); ?></p>
	</div>
	</div>
	<a class="view-cart-btn" href="<?php echo esc_url( $cart_url ); ?>">
	<?php esc_html_e( 'View Cart', 'woosupercharge' ); ?>
	</a>
</div>
