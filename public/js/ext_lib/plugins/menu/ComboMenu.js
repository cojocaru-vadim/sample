Ext.namespace("Ext.ux.menu");
Ext.ux.menu.ComboMenu = function(cfg, filter){
	this.addEvents('checkchanged');
	this.filter = filter;
	Ext.ux.menu.ComboMenu.superclass.constructor.call(this, cfg = cfg || {});

	if(!cfg.store && cfg.options) {
		var options = [];
		for(var i=0, len=cfg.options.length; i<len; i++) {
			var value = cfg.options[i];
			switch(Ext.type(value)){
				case 'array':  options.push(value); break;
				case 'object': options.push([value.id, value[this.displayField]]); break;
				case 'string': options.push([value, value]); break;
			}
		}

		this.store = new Ext.data.Store({
			reader: new Ext.data.ArrayReader({id: 0}, ['id', this.displayField]),
			data:   options,
			listeners: {
				'load': this.onLoad,
				scope:  this
			}
		});
		this.loaded = true;
	} else {
		this.add({text: this.loadingText, iconCls: 'loading-indicator'});
		this.store.on('load', this.onLoad, this);
	}
};

Ext.extend(Ext.ux.menu.ComboMenu, Ext.menu.Menu, {
	displayField:  'name',
	loadingText: 'Loading...',
	loadOnShow:  true,
	selected:    '',
	defaultItem: 'No Filter', // value 0
	emptyText: 'No Filter',
	valueNotFoundText: 'Bad Value',

	show: function() {
		var lastArgs = null;
		return function(){
			if(arguments.length == 0){
				Ext.ux.menu.ComboMenu.superclass.show.apply(this, lastArgs);
			} else {
				lastArgs = arguments;
				if(this.loadOnShow && !this.loaded) this.store.load();
				Ext.ux.menu.ComboMenu.superclass.show.apply(this, arguments);
			}
		};
	}(),

	onLoad: function(store, records) {
		var visible = this.isVisible();
		this.hide(false);

		this.removeAll();

		var storeRecord = Ext.data.Record.create([ { name: 'id' }, { name: this.displayField } ]);
		var rec = new storeRecord({ id: 0, name: this.defaultItem });
		//var rec = new storeRecord();
		//rec.data = new Object();
		//rec.data['id'] = 0;
		//rec.data[this.displayField] = this.defaultItem;
		store.add(rec);

		/* Ext 3 code */
		var combo = new Ext.form.ComboBox({
			xtype: 'combo',
			store: store,
			displayField: this.displayField,
			valueField: 'id',
			emptyText: 'No Filter',
			valueNotFoundText:  'No Filter',
			disableKeyFilter: 'true',
			editable: true,
			triggerAction: 'all',
			selectOnFocus: true,
			typeAhead: true,
			mode: 'local'
		});

		combo.on('select', this.checkChange, this);

		/* Ext 2 code*/
		/*
		var combo = new Ext.menu.Adapter(
			new Ext.form.ComboBox({
				xtype: 'combo',
				store: store,
				displayField: this.displayField,
				valueField: 'id',
				emptyText: 'No Filter',
				valueNotFoundText:  'No Filter',
				disableKeyFilter: 'true',
				editable: true,
				triggerAction: 'all',
				selectOnFocus: true,
				typeAhead: true,
				mode: 'local'
			}),
			{ hideOnClick: false });

		combo.component.on('select', this.checkChange, this);
		*/

		this.combo = this.add(combo);

		this.loaded = true;

		if(visible)
			this.show();

		this.fireEvent('load', this, records);

	},

	setSelected: function(value) {
		//this.combo.component.setValue(value);
		this.combo.setValue(value);
	},

	checkChange: function(item, checked) {
		//this.selected = this.combo.component.getValue();
		this.selected = this.combo.getValue();

		this.fireEvent("checkchange", item, checked);

		this.filter.dt.delay(this.filter.updateBuffer);
	},

	getSelected: function() {
		//return this.combo.component.getValue();
		return this.combo.getValue();
	}
	
});
