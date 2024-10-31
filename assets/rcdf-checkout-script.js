jQuery(document).ready(function ($) {
	var $locHref = window.location.href
	
	if ($locHref == rcdfCheckoutPlugin.href_checkout) {
			getClientData();
		}
		function getClientData() {
		events = ''
		mobile = navigator.maxTouchPoints > 0 && 'orientation' in window
		
		switch (rcdfCheckoutPlugin.selectors.event_el) {
			case 'blur':
				events = 'blur touchend';
				break;
				case 'focus':
					events = 'focus touchstart';
					break;
					default:
						events = 'click touchstart';
						break;
					}
					window.onload = function () {
						$(rcdfCheckoutPlugin.selectors.trigger_element).on(events, function () {
							let firstName = $(rcdfCheckoutPlugin.selectors.first_name).val();
							let lastName = $(rcdfCheckoutPlugin.selectors.last_name).val();
							let phone = $(rcdfCheckoutPlugin.selectors.phone).val();
							let email = $(rcdfCheckoutPlugin.selectors.email).val();
							let productName = $(rcdfCheckoutPlugin.selectors.product_name).text();
							let price = $(rcdfCheckoutPlugin.selectors.price).text();
							let dNow = (new Date()).toISOString().split('.')[0];
							
							var data = {
								action: 'get_client_data',
								"rcdf_checkout_nonce": rcdfCheckoutPlugin.rcdf_checkout_nonce,
								"rcdf-checkout-firstName": firstName,
								"rcdf-checkout-lastName": lastName,
								"rcdf-checkout-phone": phone,
								"rcdf-checkout-email": email,
								"rcdf-checkout-productName": productName,
								"rcdf-checkout-price": price,
								"rcdf-checkout-dNow": dNow,
							}
							
							jQuery.post(rcdfCheckoutPlugin.ajaxurl, data);
							
						})
					}
				}
				
				
			});			

