define([
    'jquery',
    'underscore',
    'mage/url',
    'PayPal_Braintree/js/form-builder',
    'PayPal_Braintree/js/helper/remove-non-digit-characters',
    'PayPal_Braintree/js/helper/replace-single-quote-character'
], function (
    $,
    _,
    url,
    formBuilder,
    removeNonDigitCharacters,
    replaceSingleQuoteCharacter
) {
    'use strict';

    return function (payload, currentElement) {
        $('#maincontent').trigger('processStart');

        /* Set variables & default values for shipping/recipient name to billing */
        let accountFirstName = replaceSingleQuoteCharacter(payload.details.firstName),
            accountLastName = replaceSingleQuoteCharacter(payload.details.lastName),
            accountEmail = replaceSingleQuoteCharacter(payload.details.email),
            recipientFirstName = accountFirstName,
            recipientLastName = accountLastName,
            address = payload.details.shippingAddress,
            recipientName = null,
            actionSuccess = url.build('braintree/paypal/review'),
            isRequiredBillingAddress,
            phone = _.get(payload, ['details', 'phone'], '');

        // Map the shipping address correctly
        if (!_.isUndefined(address.recipientName) && _.isString(address.recipientName)) {
            /*
                * Trim leading/ending spaces before splitting,
                * filter to remove array keys with empty values
                * & set to variable.
                */
            recipientName = address.recipientName.trim().split(' ').filter(n => n);
        }

        /*
            * If the recipientName is not null, and it is an array with
            * first/last name, use it. Otherwise, keep the default billing first/last name.
            * This is to avoid cases of old accounts where spaces were allowed to first or
            * last name in PayPal and the result was an array with empty fields
            * resulting in empty names in the system.
            */
        if (!_.isNull(recipientName) && !_.isUndefined(recipientName[1])) {
            recipientFirstName = replaceSingleQuoteCharacter(recipientName[0]);
            recipientLastName = replaceSingleQuoteCharacter(recipientName[1]);
        }

        payload.details.shippingAddress = {
            streetAddress: typeof address.line2 !== 'undefined' && _.isString(address.line2)
                ? replaceSingleQuoteCharacter(address.line1)
                        + ' ' + replaceSingleQuoteCharacter(address.line2)
                : replaceSingleQuoteCharacter(address.line1),
            locality: replaceSingleQuoteCharacter(address.city),
            postalCode: address.postalCode,
            countryCodeAlpha2: address.countryCode,
            email: accountEmail,
            recipientFirstName: recipientFirstName,
            recipientLastName: recipientLastName,
            telephone: removeNonDigitCharacters(phone),
            region: typeof address.state !== 'undefined'
                ? replaceSingleQuoteCharacter(address.state)
                : ''
        };

        payload.details.email = accountEmail;
        payload.details.firstName = accountFirstName;
        payload.details.lastName = accountLastName;
        if (typeof payload.details.businessName !== 'undefined'
                && _.isString(payload.details.businessName)) {
            payload.details.businessName
                    = replaceSingleQuoteCharacter(payload.details.businessName);
        }

        // Map the billing address correctly
        isRequiredBillingAddress = currentElement.data('requiredbillingaddress');

        if (isRequiredBillingAddress
                    && typeof payload.details.billingAddress !== 'undefined') {
            let billingAddress = payload.details?.billingAddress?.streetAddress
                ? payload.details.billingAddress
                : address;

            payload.details.billingAddress = {
                streetAddress: typeof billingAddress.line2 !== 'undefined'
                        && _.isString(billingAddress.line2)
                    ? replaceSingleQuoteCharacter(billingAddress.line1)
                            + ' ' + replaceSingleQuoteCharacter(billingAddress.line2)
                    : replaceSingleQuoteCharacter(billingAddress.line1),
                locality: replaceSingleQuoteCharacter(billingAddress.city),
                postalCode: billingAddress.postalCode,
                countryCodeAlpha2: billingAddress.countryCode,
                telephone: removeNonDigitCharacters(phone),
                region: typeof billingAddress.state !== 'undefined'
                    ? replaceSingleQuoteCharacter(billingAddress.state)
                    : ''
            };
        }

        if (currentElement.data('location') === 'productpage') {
            let form = $('#product_addtocart_form');

            payload.additionalData = form.serialize();
        }

        formBuilder.build(
            {
                action: actionSuccess,
                fields: {
                    result: JSON.stringify(payload)
                }
            }
        ).submit();
    }
});
