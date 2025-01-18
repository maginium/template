define([
    'jquery',
    'underscore',
    'mage/storage',
    'mage/translate',
    'PayPal_Braintree/js/helper/is-cart-virtual'
], function (
    $,
    _,
    storage,
    $t,
    isCartVirtual
) {
    'use strict';

    return function (payload, currentElement, self) {
        $('body').trigger('processStart');

        let address = payload.details.shippingAddress,
            recipientFirstName,
            recipientLastName,
            methodCode = '',
            shippingResponse = Promise.resolve(),
            isRequiredBillingAddress = currentElement.data('requiredbillingaddress'),
            billingAddress = isRequiredBillingAddress && payload.details?.billingAddress?.line1
                ? payload.details.billingAddress
                : address;

        // get recipient first and last name
        if (typeof address.recipientName !== 'undefined') {
            let recipientName = address.recipientName.split(' ');
            recipientFirstName = recipientName[0].replace(/'/g, '&apos;');
            recipientLastName = recipientName[1].replace(/'/g, '&apos;');
        } else {
            recipientFirstName = payload.details.firstName.replace(/'/g, '&apos;');
            recipientLastName = payload.details.lastName.replace(/'/g, '&apos;');
        }

        // get shipping method code
        if (payload.shippingOptionId != null) {
            methodCode = payload.shippingOptionId;
        } else {
            methodCode = self.shippingMethodCode;
        }

        if (!isCartVirtual()) {
            const shippingInformation = {
                addressInformation: {
                    'shipping_method_code': self.shippingMethods[methodCode].method_code,
                    'shipping_carrier_code': self.shippingMethods[methodCode].carrier_code,
                    'shipping_address': {
                        'email': payload.details.email.replace(/'/g, '&apos;'),
                        'telephone': typeof payload.details.phone !== 'undefined' ? payload.details.phone : '00000000000',
                        'firstname': recipientFirstName,
                        'lastname': recipientLastName,
                        'street': typeof address.line2 !== 'undefined' ? [address.line1.replace(/'/g, '&apos;'), address.line2.replace(/'/g, '&apos;')] : [address.line1.replace(/'/g, '&apos;')],
                        'city': address.city.replace(/'/g, '&apos;'),
                        'region': address?.state?.replace(/'/g, '&apos;') || '',
                        'region_id': self.getRegionIdByCode(address.countryCode, address?.state?.replace(/'/g, '&apos;') || ''),
                        'region_code': null,
                        'country_id': address.countryCode,
                        'postcode': address.postalCode,
                        'same_as_billing': 0,
                        'customer_address_id': 0,
                        'save_in_address_book': 0
                    }
                }
            };

            shippingResponse = storage.post(
                self.getApiUrl('shipping-information'),
                JSON.stringify(shippingInformation)
            );
        }

        shippingResponse.then(function () {
            // Submit payment information
            let paymentInformation = {
                'email': payload.details.email.replace(/'/g, '&apos;'),
                'paymentMethod': {
                    'method': 'braintree_paypal',
                    'additional_data': {
                        'payment_method_nonce': payload.nonce
                    }
                },
                'billing_address': {
                    'email': payload.details.email.replace(/'/g, '&apos;'),
                    'telephone': typeof payload.details.phone !== 'undefined' ? payload.details.phone : '00000000000',
                    'firstname': recipientFirstName,
                    'lastname': recipientLastName,
                    'street': typeof billingAddress.line2 !== 'undefined' ? [billingAddress.line1.replace(/'/g, '&apos;'), billingAddress.line2.replace(/'/g, '&apos;')] : [billingAddress.line1.replace(/'/g, '&apos;')],
                    'city': billingAddress.city.replace(/'/g, '&apos;'),
                    'region': billingAddress?.state?.replace(/'/g, '&apos;') || '',
                    'region_id': self.getRegionIdByCode(billingAddress.countryCode, billingAddress?.state?.replace(/'/g, '&apos;') || ''),
                    'region_code': null,
                    'country_id': billingAddress.countryCode,
                    'postcode': billingAddress.postalCode,
                    'same_as_billing': 0,
                    'customer_address_id': 0,
                    'save_in_address_book': 0
                }
            };
            if (window.checkout || window.checkoutConfig) {
                let agreementIds = [];
                if (window?.checkout?.agreementIds) {
                    agreementIds = window.checkout.agreementIds;
                }
                if (window?.checkoutConfig?.checkoutAgreements
                    && window?.checkoutConfig?.checkoutAgreements?.isEnabled) {
                    let agreements = window.checkoutConfig.checkoutAgreements.agreements;
                    _.each(agreements, function (item) {
                        agreementIds.push(item.agreementId);
                    });
                }
                if (agreementIds.length) {
                    paymentInformation.paymentMethod.extension_attributes = {
                        'agreement_ids': agreementIds
                    };
                }
            }
            storage.post(
                self.getApiUrl('payment-information'),
                JSON.stringify(paymentInformation)
            ).done(function (r) {
                document.location = self.getActionSuccess();
            }.bind(this)).fail(function (r) {
                $('body').trigger('processStop');

                alert($t('We\'re unable to take payment through PayPal. Please try with different payment method.'));
                console.error('Braintree PayPal Unable to take payment', r);
                return false;
            });
        }.bind(this)).fail(function (r) {
            $('body').trigger('processStop');

            alert($t('Braintree PayPal unable to set shipping information.'));
            console.error('Braintree PayPal unable to set shipping information', r);
        });
    };
});
