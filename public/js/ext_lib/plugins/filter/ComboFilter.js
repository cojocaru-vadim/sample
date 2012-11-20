Ext.ux.grid.filter.ComboFilter = Ext.extend(Ext.ux.grid.filter.Filter, {
	phpMode:     false,

	init: function(config) {
		this.dt = new Ext.util.DelayedTask(this.fireUpdate, this);

		this.menu = new Ext.ux.menu.ComboMenu(config, this);
		this.menu.on('onCheckChage', this.onCheckChange, this);
	},

	onCheckChange: function() {
		this.dt.delay(this.updateBuffer);
	},

	setValue: function(value) {
		this.menu.setSelected(value);

		this.fireEvent("update", this);
	},

	getValue: function() {
		return this.menu.getSelected();
	},

	serialize: function() {
		var args = {type: 'list', value: this.getValue()};
	    this.fireEvent('serialize', args, this);

		return args;
	}
	
});
