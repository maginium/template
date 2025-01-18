/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'uiComponent',
        'underscore',
        'jquery',
        'Magento_Customer/js/customer-data',
        'mage/translate',
        'braintree',
        'braintreeCheckoutPayPalAdapter',
        'braintreeDataCollector',
        'braintreePayPalCheckout',
        'PayPal_Braintree/js/helper/check-guest-checkout',
        'PayPal_Braintree/js/helper/create-payment',
        'PayPal_Braintree/js/helper/get-cart-line-items-helper',
        'PayPal_Braintree/js/helper/is-cart-virtual',
        'PayPal_Braintree/js/helper/is-logged-in',
        'PayPal_Braintree/js/helper/submit-review-page',
        'mage/storage',
        'domReady!'
    ],
    function (
        Component,
        _,
        $,
        customerData,
        $t,
        braintree,
        Braintree,
        dataCollector,
        paypalCheckout,
        checkGuestCheckout,
        createPayment,
        getCartLineItems,
        isCartVirtual,
        isLoggedIn,
        submitReviewPage,
        storage
    ) {
        'use strict';

        return Component.extend({
            events: {
                onClick: null,
                onCancel: null,
                onError: null
            },
            currencyCode: null,
            amount: 0,
            quoteId: 0,
            storeCode: 'default',
            shippingAddress: {},
            countryDirectory: null,
            countryList: null,
            shippingMethods: {},
            shippingMethodCode: null,
            lineItems: {},
            buttonIds: [],
            skipReview: null,
            buttonConfig: {},

            /**
             * Initialize button
             *
             * @param config
             * @param element
             */
            initialize: function (config, element) {
                this._super(config);

                $(document).on('priceUpdated', (event, displayPrices) => {
                    $('.action-braintree-paypal-message[data-pp-type="product"]')
                        .attr('data-pp-amount', displayPrices.finalPrice.amount);
                });

                this.buttonConfig = config.buttonConfig;
                this.buttonIds = config.buttonIds;
                this.lineItems = this.buttonConfig.lineItems;
                this.loadSDK(this.buttonConfig, this.lineItems);

                window.addEventListener('hashchange', function () {
                    const step = window.location.hash.replace('#', '');

                    if (step === 'shipping') {
                        Braintree.getPayPalInstance()?.teardown(function () {
                            this.loadSDK(this.buttonConfig, this.lineItems);
                        }.bind(this));
                    }

                }.bind(this));

                window.addEventListener('paypal:reinit-express', function () {
                    this.loadSDK(this.buttonConfig, this.lineItems);
                }.bind(this));

                const cart = customerData.get('cart');

                cart.subscribe(({ braintree_masked_id }) => {
                    this.setQuoteId(braintree_masked_id);
                });

                if (cart()?.braintree_masked_id) {
                    this.setQuoteId(cart().braintree_masked_id);
                }
            },

            /**
             * Get Region ID
             *
             * @param countryCode
             * @param regionName
             * @returns {number|*|null}
             */
            getRegionId: function (countryCode, regionName) {
                if (typeof regionName !== 'string') {
                    return null;
                }

                regionName = regionName.toLowerCase().replace(/[^A-Z0-9]/ig, '');

                if (typeof this.countryDirectory[countryCode] !== 'undefined'
                    && typeof this.countryDirectory[countryCode][regionName] !== 'undefined')
                {
                    return this.countryDirectory[countryCode][regionName];
                }

                return 0;
            },

            /**
             * Get Region ID by region code
             *
             * @param countryCode
             * @param regionCode
             * @returns {number|*|null}
             */
            getRegionIdByCode: function (countryCode, regionCode) {
                if (typeof regionCode !== 'string') {
                    return null;
                }

                if (typeof this.countryList[countryCode] !== 'undefined'
                    && typeof this.countryList[countryCode][regionCode] !== 'undefined')
                {
                    return this.countryList[countryCode][regionCode];
                }

                return 0;
            },

            /**
             * Set and get quote id
             */
            setQuoteId: function (value) {
                this.quoteId = value;
            },
            getQuoteId: function () {
                return this.quoteId;
            },

            /**
             * Set and get success redirection url
             */
            setActionSuccess: function (value) {
                this.actionSuccess = value;
            },
            getActionSuccess: function () {
                return this.actionSuccess;
            },

            /**
             * Set and get success redirection url
             */
            setSkipReview: function (value) {
                this.skipReview = value;
            },
            getSkipReview: function () {
                return this.skipReview;
            },

            /**
             * Set and get amount
             */
            setAmount: function (value) {
                this.amount = parseFloat(value).toFixed(2);
            },
            getAmount: function () {
                return parseFloat(this.amount).toFixed(2);
            },

            /**
             * Set and get store code
             */
            setStoreCode: function (value) {
                this.storeCode = value;
            },
            getStoreCode: function () {
                return this.storeCode;
            },

            /**
             * Set and get store code
             */
            setCurrencyCode: function (value) {
                this.currencyCode = value;
            },
            getCurrencyCode: function () {
                return this.currencyCode;
            },

            /**
             * API Urls for logged in / guest
             */
            getApiUrl: function (uri) {
                if (isLoggedIn()) {
                    return "rest/" + this.getStoreCode() + "/V1/carts/mine/" + uri;
                } else {
                    return "rest/" + this.getStoreCode() + "/V1/guest-carts/" + this.getQuoteId() + "/" + uri;
                }
            },

            /**
             * Load Braintree PayPal SDK
             *
             * @param buttonConfig
             * @param lineItems
             */
            loadSDK: function (buttonConfig, lineItems, shippingPage = false) {
                // Get list of countries
                if (!this.countryDirectory) {
                    storage.get("rest/V1/directory/countries").done(function (result) {
                        this.countryDirectory = {};
                        this.countryList = {};
                        let i, data, x, region;
                        for (i = 0; i < result.length; ++i) {
                            data = result[i];
                            this.countryDirectory[data.two_letter_abbreviation] = {};
                            this.countryList[data.two_letter_abbreviation] = {};
                            if (typeof data.available_regions !== 'undefined') {
                                for (x = 0; x < data.available_regions.length; ++x) {
                                    region = data.available_regions[x];
                                    this.countryDirectory[data.two_letter_abbreviation][region.name.toLowerCase().replace(/[^A-Z0-9]/ig, '')] = region.id;
                                    this.countryList[data.two_letter_abbreviation][region.code] = region.id;
                                }
                            }
                        }
                    }.bind(this));
                }

                // Load SDK
                braintree.create({
                    authorization: buttonConfig.clientToken
                }, function (clientErr, clientInstance) {
                    if (clientErr) {
                        console.error('paypalCheckout error', clientErr);
                        let error = 'PayPal Checkout could not be initialized. Please contact the store owner.';

                        return Braintree.showError(error);
                    }
                    dataCollector.create({
                        client: clientInstance,
                        paypal: true
                    }, function (err) {
                        if (err) {
                            return console.log(err);
                        }
                    });
                    paypalCheckout.create({
                        client: clientInstance
                    }, function (err, paypalCheckoutInstance) {
                        if (typeof paypal !== 'undefined' ) {
                            this.renderPayPalButtons(paypalCheckoutInstance, lineItems, shippingPage);
                            this.renderPayPalMessages();
                        } else {
                            let configSDK = {
                                    components: 'buttons,messages,funding-eligibility',
                                    'enable-funding': this.isCreditActive(buttonConfig) ? 'credit' : 'paylater',
                                    currency: buttonConfig.currency,
                                    commit: buttonConfig.skipOrderReviewStep && !isCartVirtual(),
                                },

                                buyerCountry = this.getMerchantCountry(buttonConfig);

                            if (buttonConfig.environment === 'sandbox'
                                && (buyerCountry !== '' || buyerCountry !== 'undefined')) {
                                configSDK['buyer-country'] = buyerCountry;
                            }
                            paypalCheckoutInstance.loadPayPalSDK(configSDK, function () {
                                this.renderPayPalButtons(paypalCheckoutInstance, lineItems, shippingPage);
                                this.renderPayPalMessages();
                            }.bind(this));
                        }
                    }.bind(this));
                }.bind(this));
            },

            /**
             * Is Credit enabled
             *
             * @param buttonConfig
             * @returns {boolean}
             */
            isCreditActive: function (buttonConfig) {
                return buttonConfig.isCreditActive;
            },

            /**
             * Get merchant country
             *
             * @param buttonConfig
             * @returns {string}
             */
            getMerchantCountry: function (buttonConfig) {
                return buttonConfig.merchantCountry;
            },

            /**
             * Render PayPal buttons
             *
             * @param paypalCheckoutInstance
             * @param lineItems
             * @param shippingPage
             */
            renderPayPalButtons: function (paypalCheckoutInstance, lineItems, shippingPage = false) {
                this.payPalButton(paypalCheckoutInstance, lineItems, shippingPage);
            },

            /**
             * Render PayPal messages
             */
            renderPayPalMessages: function () {
                $('.action-braintree-paypal-message').each(function () {
                    window.paypal.Messages({
                        amount: $(this).data('pp-amount'),
                        pageType: $(this).data('pp-type'),
                        style: {
                            layout: $(this).data('messaging-layout'),
                            text: {
                                color:   $(this).data('messaging-text-color')
                            },
                            logo: {
                                type: $(this).data('messaging-logo'),
                                position: $(this).data('messaging-logo-position')
                            }
                        }
                    }).render('#' + $(this).attr('id'));
                });
            },

            /**
             * @param paypalCheckoutInstance
             * @param lineItems
             * @param shippingPage
             */
            payPalButton: function (paypalCheckoutInstance, lineItems, shippingPage = false) {
                let self = this;
                $(this.buttonIds.join(',')).each(function (index, element) {
                    $(element).html('');

                    let currentElement = $(element),
                        style = {
                            label: currentElement.data('label'),
                            color: currentElement.data('color'),
                            shape: currentElement.data('shape')
                        },
                        button;

                    if (currentElement.data('fundingicons')) {
                        style.fundingicons = currentElement.data('fundingicons');
                    }

                    // set values
                    self.setCurrencyCode(currentElement.data('currency'));
                    self.setAmount(currentElement.data('amount'));
                    self.setStoreCode(currentElement.data('storecode'));
                    self.setActionSuccess(currentElement.data('actionsuccess'));

                    self.setSkipReview(currentElement.data('skiporderreviewstep'));

                    // Render
                    const config = {
                        fundingSource: currentElement.data('funding'),
                        style: style,

                        createOrder: () => self.createOrder(paypalCheckoutInstance, currentElement, lineItems),

                        validate: function (actions) {
                            let cart = customerData.get('cart'),
                                customer = customerData.get('customer'),
                                declinePayment = false,
                                isGuestCheckoutAllowed;

                            isGuestCheckoutAllowed = cart().isGuestCheckoutAllowed;
                            declinePayment = !customer().firstname && !isGuestCheckoutAllowed
                                && typeof isGuestCheckoutAllowed !== 'undefined';

                            if (declinePayment) {
                                actions.disable();
                            }
                        },

                        onCancel: function () {
                            $('#maincontent').trigger('processStop');
                        },

                        onError: function (errorData) {
                            console.error('paypalCheckout button render error', errorData);
                            $('#maincontent').trigger('processStop');
                        },

                        onClick: self.onClick.bind(self),

                        onApprove: function (approveData) {
                            return paypalCheckoutInstance.tokenizePayment(approveData, function (err, payload) {
                                if (!self.getSkipReview() || isCartVirtual()) {
                                    return submitReviewPage(payload, currentElement);
                                }

                                return createPayment(payload, currentElement, self);
                            });
                        }
                    };

                    if (self.getSkipReview()) {
                        config.onShippingChange = async function (data) {
                            // Create a payload to get estimated shipping methods
                            let payload = {
                                address: {
                                    city: data.shipping_address.city,
                                    region: data.shipping_address.state,
                                    country_id: data.shipping_address.country_code,
                                    postcode: data.shipping_address.postal_code,
                                    save_in_address_book: 0
                                }
                            };

                            this.shippingAddress = payload.address;

                            // POST to endpoint for shipping methods.
                            const result = await storage.post(
                                self.getApiUrl("estimate-shipping-methods"),
                                JSON.stringify(payload)
                            );

                            // Stop if no shipping methods.
                            let virtualFlag = false;
                            if (result.length === 0) {
                                let productItems = customerData.get('cart')().items;
                                _.each(productItems,
                                    function (item) {
                                        if (item.is_virtual || item.product_type === 'bundle') {
                                            virtualFlag = true;
                                        } else {
                                            virtualFlag = false;
                                        }
                                    }
                                );
                                if (!virtualFlag) {
                                    alert($t("There are no shipping methods available for you right now. Please try again or use an alternative payment method."));
                                    return false;
                                }
                            }

                            let shippingMethods = [];
                            // Format shipping methods array.
                            for (let i = 0; i < result.length; i++) {
                                if (typeof result[i].method_code !== 'string') {
                                    continue;
                                }

                                let selected = false;
                                if (!data.selected_shipping_option) {
                                    if (i === 0) {
                                        selected = true;
                                        this.shippingMethodCode = result[i].method_code;
                                    }
                                } else {
                                    if (data.selected_shipping_option.id === result[i].method_code) {
                                        selected = true;
                                        this.shippingMethodCode = result[i].method_code;
                                    }
                                }

                                // get shipping type
                                let shippingType = 'SHIPPING';
                                if (result[i].method_code === 'pickup') {
                                    shippingType = 'PICKUP';
                                }

                                let method = {
                                    id: result[i].method_code,
                                    type: shippingType,
                                    label: result[i].method_title,
                                    selected: selected,
                                    amount: {
                                        value: parseFloat(result[i].price_incl_tax).toFixed(2),
                                        currency: self.getCurrencyCode()
                                    },
                                };

                                // Add method object to array.
                                shippingMethods.push(method);

                                self.shippingMethods[result[i].method_code] = result[i];
                            }

                            // Create payload to get totals
                            let totalsPayload = {
                                "addressInformation": {
                                    "address": {
                                        "countryId": this.shippingAddress.country_id,
                                        "region": this.shippingAddress.region,
                                        "regionId": self.getRegionId(this.shippingAddress.country_id, this.shippingAddress.region),
                                        "postcode": this.shippingAddress.postcode
                                    },
                                    "shipping_method_code": virtualFlag ? null : self.shippingMethods[this.shippingMethodCode].method_code,
                                    "shipping_carrier_code": virtualFlag ? null : self.shippingMethods[this.shippingMethodCode].carrier_code
                                }
                            };

                            // POST to endpoint to get totals, using 1st shipping method
                            const totals = await storage.post(
                                self.getApiUrl("totals-information"),
                                JSON.stringify(totalsPayload)
                            )
                            // Set total
                            self.setAmount(totals.base_grand_total);

                            // update payments to PayPal
                            return paypalCheckoutInstance.updatePayment({
                                paymentId: data.paymentId,
                                amount: self.getAmount(),
                                currency: self.getCurrencyCode(),
                                lineItems: self.buttonConfig.canSendLineItems ? getCartLineItems(totals, false) : [],
                                shippingOptions: shippingMethods
                            });
                        };
                    }

                    button = window.paypal.Buttons(config);

                    if (!button.isEligible()) {
                        console.log('PayPal button is not elligible');
                        currentElement.parent().remove();
                        return;
                    }
                    if (button.isEligible() && $('#' + currentElement.attr('id')).length) {
                        button.render('#' + currentElement.attr('id'));
                    }
                });
            },

            createOrder: function (paypalCheckoutInstance, currentElement, lineItems) {
                return paypalCheckoutInstance.createPayment({
                    amount: currentElement.data('amount'),
                    locale: currentElement.data('locale'),
                    currency: currentElement.data('currency'),
                    flow: 'checkout',
                    enableShippingAddress: true,
                    displayName: currentElement.data('displayname'),
                    lineItems: lineItems,
                    shippingOptions: []
                });
            },

            onClick: function (data, actions) {
                if (!checkGuestCheckout()) {
                    return false;
                }

                return true;
            }
        });
    }
);
