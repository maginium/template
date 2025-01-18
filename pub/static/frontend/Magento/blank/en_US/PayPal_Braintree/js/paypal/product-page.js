define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'PayPal_Braintree/js/helper/check-guest-checkout',
    'PayPal_Braintree/js/helper/get-cart-line-items-helper',
    'PayPal_Braintree/js/helper/is-cart-virtual',
    'PayPal_Braintree/js/paypal/button',
], function ($, customerData, checkGuestCheckout, getCartLineItems, isCartVirtual, button) {
    'use strict';

    return button.extend({
        defaults: {
            branding: true,
            label: 'buynow',
            productFormSelector: '#product_addtocart_form',
            productAddedToCart: false,
            addToCartPromise: null,
        },

        createOrder: function (paypalCheckoutInstance, currentElement) {
            return this.addToCartPromise.then((cartData) => {
                if (!checkGuestCheckout()) {
                    return false;
                }

                return paypalCheckoutInstance.createPayment({
                    amount: cartData.subtotalAmount,
                    locale: currentElement.data('locale'),
                    currency: currentElement.data('currency'),
                    flow: 'checkout',
                    enableShippingAddress: true,
                    displayName: currentElement.data('displayname'),
                    lineItems: this.buttonConfig.canSendLineItems ? getCartLineItems(cartData) : [],
                    shippingOptions: []
                });
            });
        },

        /**
         * On click add the current product to the quote and proceed with PayPal checkout.
         */
        onClick: function (data, actions) {
            const isAllowed = this._super();

            if (!isAllowed) {
                return actions.reject();
            }

            let $form = $(this.productFormSelector);

            if (!this.productAddedToCart) {
                // Attach cart subscription to listen for the successful add to cart.
                const cart = customerData.get('cart');

                $form.trigger('submit');

                if ($form.validation('isValid')) {
                    $('body').trigger('processStart');

                    this.addToCartPromise = new Promise((resolve) => {
                        cart.subscribe((cartData) => {
                            this.setQuoteId(cartData.braintree_masked_id);
                            this.productAddedToCart = true;
                            $('body').trigger('processStop');
                            actions.resolve();
                            resolve(cartData);
                        });
                    });

                    return;
                }

                return actions.reject();
            }

            return actions.resolve();
        }
    });
});
