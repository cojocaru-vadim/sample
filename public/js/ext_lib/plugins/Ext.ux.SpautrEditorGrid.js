MyJsonReader = Ext.extend(Ext.data.JsonReader, {

	constructor: function (config) {
		MyJsonReader.superclass.constructor.call(this, config);
	},

	createAccessor : function(expr) {
		if(Ext.isEmpty(expr)) {
			return Ext.emptyFn;
		}

		if(Ext.isFunction(expr)) {
			return expr;
		}

		//convert mapping : "obj.sub.sub"
		if(expr.indexOf('.') > 0) {
			return function(obj) {
				var val = obj;
				var a = expr.split(".");
				while(a.length > 0) {
					val = val[a.shift()];
					if(!Ext.isDefined(val)) {
						return null;
					}
				}
				return val;
			};
		}

		//convert mapping : "obj[index]"
		if(expr.indexOf('[') == 0) {
			return new Function('obj', 'return obj' + expr);
		}

		return function(obj) {
			return obj[expr];
		};
	}

});


Ext.grid.TexterSelectionModel = Ext.extend(Ext.grid.RowSelectionModel, {

	header : '',
	/**
         * @cfg {Boolean} sortable <tt>true</tt> if the checkbox column is sortable (defaults to
         * <tt>false</tt>).
         */
	sortable : false,
	editable : false,

	// private
	menuDisabled : true,
	fixed : true,
	hideable : false,
	dataIndex : '',
	id : 'texter',

	constructor : function() {
		Ext.grid.CheckboxSelectionModel.superclass.constructor.apply(this, arguments);

		if(this.checkOnly) {
			this.handleMouseDown = Ext.emptyFn;
		}
	},

	// private
	initEvents : function() {
		Ext.grid.CheckboxSelectionModel.superclass.initEvents.call(this);
		this.grid.on('render', function() {
			var view = this.grid.getView();
			view.mainBody.on('mousedown', this.onMouseDown, this);
			Ext.fly(view.innerHd).on('mousedown', this.onHdMouseDown, this);

		}, this);
	},

	// If handleMouseDown was called from another event (enableDragDrop), set a flag so
	// onMouseDown does not process it a second time
	handleMouseDown : function() {
		Ext.grid.CheckboxSelectionModel.superclass.handleMouseDown.apply(this, arguments);
		this.mouseHandled = true;
	},

	// private
	onMouseDown : function(e, t) {
		if(e.button === 0 && t.className == 'x-grid3-row-checker') { // Only fire if left-click
			e.stopEvent();
			var row = e.getTarget('.x-grid3-row');

			// mouseHandled flag check for a duplicate selection (handleMouseDown) call
			if(!this.mouseHandled && row) {
				var index = row.rowIndex;
				if(this.isSelected(index)) {
					this.deselectRow(index);
				} else {
					this.selectRow(index, true);
					this.grid.getView().focusRow(index);
				}
			}
		}
		this.mouseHandled = false;
	},

	// private
	onHdMouseDown : function(e, t) {
		if(t.className == 'x-grid3-header') {
			e.stopEvent();
			var hd = Ext.fly(t.parentNode);
			var isChecked = hd.hasClass('x-grid3-header-on');
			if(isChecked) {
				hd.removeClass('x-grid3-header-on');
				this.clearSelections();
			} else {
				hd.addClass('x-grid3-header-on');
				this.selectAll();
			}
		}
	},

	// private
	renderer : function(v, p, record) {
		p.css = 'x-grid3-header';
		return record.json.room_name;
	}
});


var dateRenderer = Ext.util.Format.dateRenderer('d/m/Y');

var grid;

var field_renderer = function(value, cell, record, row, column) {
	//var field = grid.getColumnModel().getDataIndex(column);
	var field = this.dataIndex;

	if(record.json[field].manual == 1) {
		cell.style = 'color: navy; background-color: #EEf5FD';
	} else {
		cell.style = 'color: gray';
	}
	return value;
}


/*
        config: {
            // required
            column_definitions: [
                { name: 'name 1', dataIndex: id1},
                { name: 'name 2', dataIndex: id2},
                { name: 'name 3', dataIndex: id3}
            ],

            // required
            data: {
                total: 3,
                items: [
                        {
                            id : 1,
                            row_header: 'Standart',
                            id1 : {price: 0, manual: 0, original: {price: 0, manual: 0}},  // mapped to column 'name 1'
                            id2 : {price: 100, manual: 1, original: {price: 100, manual: 1}}, // mapped to column 'name 2'
                            id3 : {price: 0, manual: 0, original: {price: 0, manual: 0}} // mapped to column 'name 3'
                        },
                        {
                            id : 2,
                            row_header: 'Deluxe',
                            id1 : {price: 0, manual: 0, original: {price: 0, manual: 0}},
                            id2 : {price: 0, manual: 0, original: {price: 0, manual: 0}},
                            id3 : {price: 120, manual: 0, original: {price: 120, manual: 1}}
                        },
                        {
                            id : 3,
                            row_header: 'Sea view',
                            id1 : {price: 0, manual: 0, original: {price: 0, manual: 0}},
                            id2 : {price: 140, manual: 1, original: {price: 140, manual: 1}},
                            id3 : {price: 160, manual: 0, original: {price: 160, manual: 1}}
                        }

                ]
            },

            // required
            submit_url : '/url/to/submit/data/to',

            // optional
            param_mapper : function(field, record, params){
                // params is params object from Ext.Ajax.request
                // see default_mapper in implementation below for hints
            },

            // optional
            submit_callback: function(){}, // see Ext.Ajax.request callback for options
            submit_success:  function(){}, // see Ext.Ajax.request success for options
            submit_failure:  function(){}, // see Ext.Ajax.request failure for options

            // optional
            validator: function(e){}, // see Ext.grid.EditorGridPanel validateedit event for description of e

            // optional
            default_value: 0, // default value to set if value in cell is deleted

            // optional
            button_submit : {   // basic button config, handler will be attached automatically
                xtype: button
            },

            // optional
            button_reset : {   // basic button config, handler will be attached automatically
                xtype: button
            },

            // optional
            toolbar : new Ext.Toolbar(),   // toolbar to render buttons to
,

            // optional
            submit_params : {},   // additional params on save


        }
     */
Ext.ns('Ext.ux.SpautrEditorGrid');

Ext.ux.SpautrEditorGrid = function(config) {
	var id = config.id;
	var column_definitions = config.column_definitions;
	var data = config.data || {items: [], total: 0};
	var grid_recalc = config.grid_recalc || null
	var full_grid_recalc = config.full_grid_recalc || null; // implementations are below
	var default_value = config.default_value || '';
	var submit_params = config.submit_params || {};

	var default_validator = function(){
		return true;
	};

	//debugger;
	var fields = ['id', 'room_name'];

	for(var i = 0; i < column_definitions.length; i++) {
		fields.push(
		{
			name: column_definitions[i].dataIndex,
			mapping: column_definitions[i].dataIndex + '.price'
		}
		);
	}

	var store = new Ext.data.JsonStore({
		reader: MyJsonReader,
		fields: fields,
		root: "items",
		totalProperty: 'total',
		data : data
	});
	// Prepare grid

	var columns = [new Ext.grid.TexterSelectionModel()];

	for(i = 0; i < column_definitions.length; i++) {
		columns.push(
		{
			header: column_definitions[i].name,
			dataIndex: column_definitions[i].dataIndex,
			renderer:  field_renderer
		}
		);
	}

	var columnModel = new Ext.grid.ColumnModel({
		defaults: {
			sortable: false,
			editor: Ext.form.TextField,
			hideable: false
		},
		columns: columns
	});


	var button_submit = config.button_submit || {
		xtype: 'button',
		icon:  '/images/icons/disk_multiple.png',
		text: 'Save'
	};
	var button_reset = config.button_reset || {
		xtype: 'button',
		icon:  '/images/icons/arrow_rotate_anticlockwise.png',
		text: 'Revert'
	};

	button_submit.handler = function() {
		var url = config.submit_url;
		var callback = config.submit_callback || Ext.emptyFn;
		var success = config.submit_success || config.callback || Ext.emptyFn;
		var failure = config.submit_failure || config.callback || Ext.emptyFn;
		var default_param_mapper = function(field, record, params) {
			if(rec.json[field].manual == 1 && rec.json[field].price != rec.json[field].original.price) {
				params['changed_fields[' + rec.json.id + '][' + field + ']'] = rec.data[field];
				rec.beginEdit();
				rec.json[field].original.price = rec.data[field];
				rec.json[field].original.manual = 1;
				rec.endEdit();
				rec.commit();
			}
				

			return params;
		};


		var param_mapper = config.param_mapper || default_param_mapper;

		var params = {};

		var column_count = columnModel.getColumnCount();
		var row_count = store.getCount();
		//debugger;
		//grid.getEl().mask('<?=Translation::tr('common[saving]') ?>', "x-mask-loading");
		grid.getEl().mask('Saving', "x-mask-loading");
		for(var i = 0; i < row_count; i++) {
			var rec = store.getAt(i);
			for(var j = 1; j < column_count; j++) {
				var field = columnModel.getDataIndex(j);
				params = param_mapper(field, rec, params);
			}
		}

		Object.keys = Object.keys || function(o) {
			var result = [];
			for(var name in o) {
				if (o.hasOwnProperty(name))
					result.push(name);
			}
			return result;
		};

		if (Ext.isEmpty(Object.keys(params))) {
			grid.getEl().unmask();
			return true;
		}

		for(var p in submit_params){
			params[p] = submit_params[p];
		}

		Ext.Ajax.request({
			url: url,
			method: 'POST',
			params: params,
			callback: function() {
				grid.getEl().unmask();
				callback.apply(this, arguments);
			},
			success:  success,
			failure:  failure
		});
	}

	button_reset.handler = function() {
		//debugger;
		var count = store.getCount();
		for(var i = 0; i < count; i++) {
			var rec = store.getAt(i);
			rec.beginEdit();
			for(var j = 0; j < store.fields.length; j++) {
				var field = store.fields.keys[j];
				if(!rec.json[field].original) continue;
				var original = rec.json[field].original;
				rec.json[field] = {
					price:    original.price,
					manual:   original.manual,
					original: original
				};
				rec.data[field] = original.price;
			}
			rec.endEdit();
			rec.commit();
		}
		full_grid_recalc();
	};

	var toolbar = config.toolbar || new Ext.Toolbar({

		});

	var grid_config = {
		id: id,
		store: store,
		colModel:  new Ext.grid.ColumnModel({
			defaults: {
				sortable: false,
				editor: Ext.form.TextField,
				hideable: false
			},
			columns: columns
		}),
		viewConfig: {
		},
		loadMask: true,
		border: false,
		enableHdMenu: false,
		stripeRows: true
	};

	if(!config.toolbar) grid_config.bbar = toolbar;

	toolbar.addItem(['->', button_submit, button_reset]);

	var grid = new Ext.grid.EditorGridPanel(grid_config);

	grid.submit_params = submit_params;

	grid.getManualValues = function() {
		var default_param_mapper = function(field, record, params) {
			if(rec.json[field].manual == 1) {
				params['[' + rec.json.id + '][' + field + ']'] = rec.data[field];
			}

			return params;
		};

		var returnParams = {};
		var param_mapper = config.param_mapper || default_param_mapper;

		var column_count = columnModel.getColumnCount();
		var row_count = store.getCount();

		for(var i = 0; i < row_count; i++) {
			var rec = store.getAt(i);
			for(var j = 1; j < column_count; j++) {
				var field = columnModel.getDataIndex(j);
				if(rec.json[field].manual == 1 && rec.json[field].price != rec.json[field].original.price) {
					returnParams = param_mapper(field, rec, returnParams);
				}
			}
		}

		return returnParams;
	};

	grid.updateDefaults = function() {
		var column_count = columnModel.getColumnCount();
		var row_count = store.getCount();
		//debugger;

		for(var i = 0; i < row_count; i++) {
			var rec = store.getAt(i);
			for(var j = 1; j < column_count; j++) {
				var field = columnModel.getDataIndex(j);
				if(rec.json[field].manual == 1) {
					rec.beginEdit();
					rec.json[field].original.price = rec.data[field];
					rec.json[field].original.manual = 1;
					rec.endEdit();
					rec.commit();
				}
			}
		}
	}

	columnModel = grid.getColumnModel();

	if(null != grid_recalc) {
		var local_grid_recalc = grid_recalc;
		grid_recalc = function(r,c,p){local_grid_recalc(r,c,p,grid)};
	}
	else grid_recalc = function(row, column, price) {
		var uppermost = 0;
		var leftmost = 1;
		//debugger;
		for(var i = column; i >= leftmost; i--) {
			var field = columnModel.getDataIndex(i);
			for(var j = row; j >= uppermost; j--) {
				var rec = store.getAt(j);

				if(i == column && j == row) {
					/*rec.beginEdit();
                        rec.json[field].price = rec.data[field];
                        rec.json[field].manual = 1;
                        if(rec.json[field].modified_by)
                            delete rec.json[field].modified_by;
                        rec.endEdit();*/

					continue; // skip current cell
				}


				if(rec.json[field].modified_by && rec.json[field].modified_by.row <= j && rec.json[field].modified_by.column > column) {
					uppermost = j + 1;
				} else if(rec.json[field].manual == 1) {
					uppermost = j + 1;
					if(j == row) {
						leftmost = i + 1;
					}
				} else {
					rec.beginEdit();
					rec.data[field] = price;
					rec.json[field].price = price;
					rec.json[field].modified_by = {
						column: column,
						row: row
					};

					rec.endEdit();
				//rec.commit();
				}
			}
		}
		var row_count = store.getCount();
		for(i = 0; i < row_count; i++) {
			var rec = store.getAt(i);
			if(rec.dirty) {
				rec.commit();
			}
		}
		grid.getView().refresh();
	}

	if(null != full_grid_recalc) {
		var local_full_grid_recalc = full_grid_recalc;
		full_grid_recalc = function(){local_full_grid_recalc(grid)};
	}
	else full_grid_recalc = function() {
		var column_count = columnModel.getColumnCount();
		var row_count = store.getCount();
		//debugger;
		//grid.getEl().mask('<?=Translation::tr('common[recalculating]') ?>', "x-mask-loading");
		grid.getEl().mask('Recalculating', "x-mask-loading");
		for(var i = 0; i < row_count; i++) {
			var rec = store.getAt(i);
			for(var j = 1; j < column_count; j++) {
				var field = columnModel.getDataIndex(j);

				if(rec.json[field].manual == 1) {
					grid_recalc(i, j, rec.json[field].price);
				}
			}
		}
		grid.getEl().unmask();
	};


	var validator = config.validator || default_validator;

	grid.setValidator = function(v) {
		grid.un('validateedit', validator);
		validator = v;
		grid.on('validateedit', validator);
	}

	grid.on('validateedit', validator);

	grid.on('viewready', function(e) {
		full_grid_recalc();
	});

	grid.on('afteredit', function(e) {
		if(e.value == e.originalValue) {
			return true;
		}

		if(e.value == '') e.value = default_value;

		e.record.json[e.field].manual = 1;
		e.record.json[e.field].price = e.value;
		e.record.commit();

		var row = e.row;
		var column = e.column;
		var price = e.value;

		grid_recalc(e.row, e.column, e.value);

		return true;
	});

	return grid;
};