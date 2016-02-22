;(function($) {
	rm.namespace('rm.checkout.ergonomic.address');
	//noinspection JSValidateTypes
	rm.checkout.ergonomic.address.Shipping = {
		hasNoFields: 'rm.checkout.ergonomic.address.Shipping.hasNoFields'
		,construct: function(_config) { var _this = {
			init: function() {
				this.getShipping().onSave = function(transport) {
					try {
						_this.getShipping().nextStep(transport);
					}
					catch(e) {
						console.log(e);
					}
					_this.onComplete();
				};
				/**
				 * Важно вызывать этот метод ранее других
				 */
				this.addFakeRegionFieldsIfNeeded();
				this.listenForShippingAddressTheSameAsBilling();
				this.disableShippingAddressTheSameSwitcherIfNeeded();
				this.handleShippingAddressHasNoFields();
				this.listenForSelection();
				$(window)
					.bind(
						rm.checkout.Ergonomic.interfaceUpdated
						,/**
						 * @param {jQuery.Event} event
						 */
						function(event) {
							if (
									_this.needSave()
								||
									(
											('billingAddress' === event.updateType)
										&&
											_this.hasNoFields()
									)
							) {
								_this.save();
							}
						}
					)
				;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.address.Shipping}
			 */
			save: function() {
				this.needSave(false);
				var $regionAsText = this.getAddress().getFieldRegionText().getElement();
				var $regionAsSelect = this.getAddress().getFieldRegionSelect().getElement();
				var regionAsText = $regionAsText.get(0);
				var regionAsSelect = $regionAsSelect.get(0);
				if (regionAsText && regionAsSelect) {
					if ('none' === regionAsText.style.display) {
						regionAsText.value = $('option:selected', $regionAsSelect).text();
					}
				}
				this.getShipping().save();
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.address.Shipping}
			 */
			addFakeRegionFieldsIfNeeded: function() {
				rm.checkout.ergonomic.helperSingleton.addFakeInputIfNeeded('shipping:region');
				rm.checkout.ergonomic.helperSingleton.addFakeInputIfNeeded('shipping:region_id');
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.address.Shipping}
			 */
			disableShippingAddressTheSameSwitcherIfNeeded: function() {
				/**
				 * Если форма адреса доставки содержит обязательное для заполнения поле,
				 * которое в то же время не является обязательным для заполнения в адресе плательщика,
				 * то переключатель "Доставить на этот адрес" / "Доставить по другому адресу"
				 * надо скрыть и сразу выбрать значение "Доставить по другому адресу".
				 */
				/** @type {Boolean} */
				var needDisableSwitcher = false;
				this.getAddress().getFields()
					.each(
						function() {
							/** @type {rm.customer.address.Field} */
							var shippingField =
								rm.customer.address.Field
									.construct({
										element: $(this)
									})
							;
							if (shippingField.isRequired()) {
								/** @type {rm.customer.address.Field} */
								var billingField =
									rm.checkout.ergonomic.billingAddressSingleton.getAddress().getField(
										shippingField.getShortName()
									)
								;
								if (!billingField.isExist() || !billingField.isRequired()) {
									needDisableSwitcher = true;
									return false;
								}
							}
						}
					)
				;
				if (needDisableSwitcher) {
					_this.handleShippingAddressTheSameAsBilling(false);
					$(
						rm.checkout.ergonomic.billingAddressSingleton.getAddress()
							.getField('use_for_shipping').getElement()
					)
						.closest('li.control')
							.hide()
					;
					$('#billing\\:use_for_shipping_yes')
						.removeAttr('checked')
					;
					$('#billing\\:use_for_shipping_no')
						.attr('checked', 'checked')
					;
					$('#shipping\\:same_as_billing').val(0);
					$('#shipping\\:same_as_billing').get(0).checked = false;
				}
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.address.Shipping}
			 */
			listenForSelection: function() {
				_this.getAddress().getFields()
					.change(
						function() {
							_this.handleSelection();
						}
					)
				;
				if (document.getElementById('shipping-address-select')) {
					this.handleSelection();
				}
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.address.Shipping}
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
				/**
				 * Один невидимый элемента у нас всегда есть: shipping:address_id
				 */
				if (this.hasNoFields()) {
					this.getElement().hide();
					$(window)
						.trigger(
							{
								/** @type {String} */
								type: rm.checkout.ergonomic.address.Shipping.hasNoFields
							}
						)
					;
				}
				return this;
			}
			,/**
			 * @public
			 * @returns {rm.checkout.ergonomic.address.Shipping}
			 */
			listenForShippingAddressTheSameAsBilling: function() {
				$(window)
					.bind(
						rm.checkout.ergonomic.address.Billing.shippingAddressIsTheSame
						,/**
						 * @param {jQuery.Event} event
						 */
						function(event) {
							_this.handleShippingAddressTheSameAsBilling(event.value);
						}
					)
				;
				/**
				 * Явно вызываем метод handleShippingAddressTheSameAsBilling в первый раз,
				 * потому что rm.checkout.ergonomic.address.Billing инициализируется до
				 * rm.checkout.ergonomic.address.Shipping, и первое оповещение от
				 * rm.checkout.ergonomic.address.Billing не доходит до
				 * rm.checkout.ergonomic.address.Shipping.
				 */
				_this.handleShippingAddressTheSameAsBilling(
					document.getElementById('billing:use_for_shipping_yes').checked
				);
				return this;
			}
			,/**
			 * @public
			 * @param {Boolean} value
			 * @returns {rm.checkout.ergonomic.address.Shipping}
			 */
			handleShippingAddressTheSameAsBilling: function(value) {
				_this.getShipping().setSameAsBilling(value);
				_this.getElement().toggle(!value);
				return this;
			}
			,/**
			 * @public
			 * @param {Object} transport
			 * @returns {rm.checkout.ergonomic.address.Shipping}
			 */
			onComplete: function(transport) {
				this.getShipping().resetLoadWaiting(transport);
				rm.checkout.ergonomic.helperSingleton.updateSections(response);
				$(window)
					.trigger(
						{
							/** @type {String} */
							type: rm.checkout.Ergonomic.interfaceUpdated
							,/** @type {String} */
							updateType: 'shippingAddress'
						}
					)
				;
				return this;
			}
			,/**
			 * @public
			 * @returns {Boolean}
			 */
			hasNoFields: function() {
				if (rm.undefined(this._hasNoFields)) {
					/** @type {jQuery} HTMLInputElement */
					var $fields = $('#shipping-new-address-form fieldset :input', _this.getElement());
					/**
					 * @type {Boolean}
					 */
					this._hasNoFields = (2 > $fields.length);
				}
				return this._hasNoFields;
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
									element: $('#co-shipping-form', _this.getElement())
									,type: 'shipping'
								}
							)
					;
				}
				return this._address;
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
			 * @returns {rm.checkout.ergonomic.method.Shipping}
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
			 * @returns {Shipping}
			 */
			getShipping: function() {
				return shipping;
			}
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
					this._validator = new Validation(_this.getShipping().form);
				}
				return this._validator;
			}
		}; _this.init(); return _this; }
	};

})(jQuery);