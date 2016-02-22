;(function($) {
	rm.namespace('rm.checkout.ergonomic.address');
	//noinspection JSValidateTypes
	/**
	 * @param {jQuery} element
	 */
	rm.checkout.ergonomic.address.Billing = {
		shippingAddressIsTheSame: 'rm.checkout.ergonomic.address.Billing.shippingAddressIsTheSame'
		,construct: function(_config) { var _this = {
			init: function() {
				var onSaveOriginal = this.getBilling().onSave;
				this.getBilling().onSave = function(transport) {
					try {
						_this.getBilling().nextStep(transport);
					}
					catch(e) {
						console.log(e);
					}
					_this.onComplete();
				};
				this.handleShippingAddressHasNoFields();
				this.listenForSelection();
				this.handleUseForShipping();
				this.addFakeRegionFieldsIfNeeded();
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.address.Shipping}
			 */
			save: function() {
				_this.needSave(false);
				var $regionAsText = this.getAddress().getFieldRegionText().getElement();
				var $regionAsSelect = this.getAddress().getFieldRegionSelect().getElement();
				var regionAsText = $regionAsText.get(0);
				var regionAsSelect = $regionAsSelect.get(0);
				if (regionAsText && regionAsSelect) {
					if ('none' === regionAsText.style.display) {
						regionAsText.value = $('option:selected', $regionAsSelect).text();
					}
				}
				_this.getBilling().save();
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.address.Billing}
			 */
			addFakeRegionFieldsIfNeeded: function() {
				rm.checkout.ergonomic.helperSingleton.addFakeInputIfNeeded('billing:region');
				rm.checkout.ergonomic.helperSingleton.addFakeInputIfNeeded('billing:region_id');
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.address.Billing}
			 */
			listenForSelection: function() {
				_this.getAddress().getFields()
					.change(
						function() {
							_this.handleSelection();
						}
					)
				;
				if (document.getElementById('billing-address-select')) {
					this.handleSelection();
				}
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.address.Billing}
			 */
			handleSelection: function() {
				this.getValidator().dfValidateFilledFieldsOnly();
				if (this.getValidator().dfValidateSilent()) {
					if (false === this.getCheckout().loadWaiting) {
						this.save();
					}
					else {
						/**
						 * Вызывать save() пока бесполезно, потому что система занята.
						 * Поэтому вместо прямого вызова save планируем этот вызов на будущее.
						 */
						this.needSave(true);
					}
				}
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.method.Shipping}
			 */
			handleShippingAddressHasNoFields: function() {
				$(window)
					.bind(
						rm.checkout.ergonomic.address.Shipping.hasNoFields
						,/**
						 * @param {jQuery.Event} event
						 */
						function(event) {
							$(_this.getAddress().getField('use_for_shipping').getElement())
								.closest('li.control')
									.hide()
							;
						}
					)
				;
				return this;
			}
			,/**
			 * @public
			 * @param {Object} transport
			 * @returns {rm.checkout.ergonomic.address.Billing}
			 */
			onComplete: function(transport) {
				this.getBilling().resetLoadWaiting(transport);
				rm.checkout.ergonomic.helperSingleton.updateSections(response);
				$(window)
					.trigger(
						{
							/** @type {String} */
							type: rm.checkout.Ergonomic.interfaceUpdated
							,/** @type {String} */
							updateType: 'billingAddress'
						}
					)
				;
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.address.Billing}
			 */
			handleUseForShipping: function() {
				_this.getAddress().getField('use_for_shipping').getElement()
					.change(
						function() {
							_this.onUseForShipping();
						}
					)
				;
				_this.onUseForShipping();
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.address.Billing}
			 */
			onUseForShipping: function() {
				/** @type {Boolean} */
				var shippingAddressIsTheSame = document.getElementById('billing:use_for_shipping_yes').checked;
				$(window)
					.trigger(
						{
							/** @type {String} */
							type: rm.checkout.ergonomic.address.Billing.shippingAddressIsTheSame
							,/** @type {rm.checkout.ergonomic.address.Shipping} */
							value: shippingAddressIsTheSame
						}
					)
				;
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.customer.Address}
			 */
			getAddress: function() {
				if (rm.undefined(this._address)) {
					/**
					 * @type {rm.customer.Address}
					 */
					this._address =
						rm.customer.Address
							.construct(
								{
									element: $('#co-billing-form', _this.getElement())
									,type: 'billing'
								}
							)
					;
				}
				return this._address;
			}
			,/**
			 * @private
			 * @returns {Billing}
			 */
			getBilling: function() {
				return billing;
			}
			,/**
			 * @private
			 * @returns {Checkout}
			 */
			getCheckout: function() {
				return checkout;
			}
			,/**
			 * @public
			 * @param {Boolean}
			 * @returns {rm.checkout.ergonomic.address.Billing}
			 */
			needSave: function(value) {
				if (rm.defined(value)) {
					this._needSave = value;
				}
				return this._needSave;
			}
			,/** @type {Boolean} */
			_needSave: false
			,/**
			 * @private
			 * @returns {jQuery} HTMLElement
			 */
			getElement: function() {
				return _config.element;
			}
			,/**
			 * @private
			 * @returns {Validation}
			 */
			getValidator: function() {
				if (rm.undefined(this._validator)) {
					/**
					 * @type {Validation}
					 */
					this._validator =
						new Validation(
							_this.getBilling().form
						)
					;
				}
				return this._validator;
			}
		}; _this.init(); return _this; }
	};

})(jQuery);