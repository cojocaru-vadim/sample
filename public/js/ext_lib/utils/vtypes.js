/**
 * Custom vtypes
 */

Ext.onReady(function() {
	// phone vtype
	Ext.apply(Ext.form.VTypes, {
		alphaMinusText: "characters a-z and - allowed.",
		alphaMinusMask: /[a-zA-Z\-]/,
		alphaMinusRe: /^(.*)$/,
		alphaMinus: function (v) {
			return this.alphaMinusRe.test(v);
		}
	});

	// contacts login
	Ext.apply(Ext.form.VTypes, {
		alphaDigitText: "characters 0-9, a-z and _ allowed.",
		alphaDigitMask: /[0-9a-zA-Z\_]/,
		alphaDigitRe: /^(.*)$/,
		alphaDigit: function (v) {
			return this.alphaDigitRe.test(v);
		}
	});

	// 0-9 vtype
	Ext.apply(Ext.form.VTypes, {
		digitText: "Not a valid number.  Must be in the format 123...",
		digitMask: /[0-9]/,
		digitRe: /^(.*)$/,
		digit: function (v) {
			return this.digitRe.test(v);
		}
	});

	// digits, '.', ','
	Ext.apply(Ext.form.VTypes, {
		intFloatText: "Must be in the format 12, 12.21, 1.11, 12.1 ...",
		intFloatMask: /[0-9\.\,]/,
		intFloatRe: /^(.*)$/,
		intFloat: function (v) {
			return this.intFloatRe.test(v);
		}
	});

	// dates, '.', ',', '/', '-'
	Ext.apply(Ext.form.VTypes, {
		'chkDate': function(){
			var re = /\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}/;
			return function(v){
				return re.test(v);
			}
		}(),
		'chkDateText' : 'The format is wrong, ie: 12.12.2010 or 12/12/2010 ...'
	});

	// date`s vtype
	Ext.apply(Ext.form.VTypes, {
		daterange : function(val, field) {
			var date = field.parseDate(val);

			if(!date) {
				return;
			}

			if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
				var start = Ext.getCmp(field.startDateField);
				start.setMaxValue(date);
				start.validate();
				this.dateRangeMax = date;
			}
			else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
				var end = Ext.getCmp(field.endDateField);
				end.setMinValue(date);
				end.validate();
				this.dateRangeMin = date;
			}

			return true;
		}
	});

})
