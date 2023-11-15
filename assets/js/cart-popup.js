let woosupercharge_script;

jQuery(document).ready(function ($) {
  woosupercharge_script = {
    
    /**
     * we will hold the reference to our set timeout
     * and clear that before we add a new timeout.
     */
    timer : false,

    showModal: function () {

      clearTimeout( this.timer );
      $('.woosupercharge-modal').show();
      this.timer = setTimeout( ()=>{ $('.woosupercharge-modal').hide() }, woosupercharge.popup_close_after * 1000 );
    },

    hideModal: function () {
      $('.woosupercharge-modal').hide();
    },

    fillModal: function (product) {
      this.hideModal();

      const backgroundImageMarkup = `style='background-image: url(${product.thumbnail})'`;
      const cardImageMarkup       = `<div class="woosupercharge-modal-image"><img src=${product.thumbnail} alt="Product Image" ></div>`;

      const modalContent = 
      `<div class="woosupercharge-modal-content-container ${woosupercharge.position}" 
        ${woosupercharge.layout === 'list' ? backgroundImageMarkup : ''} >
        <h2>Added to Cart</h2>
        <div class="woosupercharge-modal-body">
          ${woosupercharge.layout !== 'list' ? cardImageMarkup : ''}
          <div class="woosupercharge-modal-details">
            <h6>${product.name}</h6>
            <p>$${product.price}</p>
          </div>
        </div>
        <a class="view-cart-btn" href=${woosupercharge.wc_cart_url}>View Cart</a>
      </div>`;
      
      
      $('.woosupercharge-modal-content').html(modalContent);
      setTimeout( this.showModal, 1500 );
    },

    addToCart: function (addToCartButton, formDataArray) {
      $.ajax({
        url: woosupercharge.wc_single_ajax_url,
        type: 'POST',
        data: $.param(formDataArray),
        success: function (response) {

          if (response.fragments) {
            $('.woosupercharge-modal-content').html(response.fragments['div.woosupercharge-modal-content']);
            $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, addToCartButton]);
          } else if (response.error) {
            console.log('Some error occurred');
          } else {
            console.log(response);
          }
        }
      });
    },

    run: function () {
      $(document.body).on('added_to_cart', function () {
        woosupercharge_script.showModal();
      });

      $(document.body).on('click', '.woosupercharge-modal-close', function () {
        woosupercharge_script.hideModal();
      });

      $(document).on('submit', 'form.cart', function (event) {
        const cartForm = $(this);
        const addToCartButton = cartForm.find('button[type="submit"]');
        const formDataArray = cartForm.serializeArray();
        const addToCartName = 'add-to-cart';

        if (addToCartButton.attr('name') === addToCartName && addToCartButton.attr('value')) {
          formDataArray.push({ name: addToCartName, value: addToCartButton.attr('value') });
        }

        const isAddToCartPresent = formDataArray.some(function (data) {
          return data.name === addToCartName;
        });

        if (isAddToCartPresent) {
          event.preventDefault();
        } else {
          return;
        }

        const addAction = 'woosupercharge_add_to_cart';
        formDataArray.push({ name: 'action', value: addAction });

        woosupercharge_script.addToCart(addToCartButton, formDataArray);
      });
    }
  };

  // Initialize woosupercharge_script
  woosupercharge_script.run();
});
