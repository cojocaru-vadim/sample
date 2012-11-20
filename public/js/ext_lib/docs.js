if (typeof console == "undefined" || typeof console.log == "undefined")
var console = { log: function(e) { if(typeof UI != "undefined" && typeof UI.notify != "undefined") ;/* UI.notify(e) */ } };

Ext.BLANK_IMAGE_URL = 'images/ext/s.gif';

Ext.Updater.defaults.loadScripts = true;
Ext.Updater.defaults.timeout = 300000;

Ext.Ajax.timeout = 300000;

// When we load a new page, we destroy all windows
// Because of this we extend the standard messagebox with a x-special-type: 'messagebox'
// and account for it when destroying windows
Ext.MessageBox.get_dialog_original = Ext.MessageBox.getDialog;
Ext.MessageBox.getDialog = function(){
	var d = this.get_dialog_original();
	d.xspecial = true;
	return d;
}

//if(!window.console) window.console = {log:function(){return;}};

/**
 * ????? ? ??????????? ?????? ????? ??????????? ?????????
 **/
Ext.override(Ext.form.ComboBox, {triggerAction: 'all'});
Ext.override(Ext.TabPanel, {tabPosition:'top',enableTabScroll: true});


// fucking hate ExtJS
// http://www.sencha.com/forum/showthread.php?93403-Problem-setting-hidden-on-form-fields&p=479231#post479231

Ext.layout.FormLayout.prototype.trackLabels = true;


/*
 * http://www.extjs.com/forum/showthread.php?p=297123#post297123
 **/


Ext.namespace('Ext.ux');

Ext.ux.DelayedTask = function(fn, scope, args) {
    var id = null;
    var call = function() {
        id = null;
        fn.apply(scope, args || []);
    };
    this.delay = function(delay, newFn, newScope, newArgs) {
        if(id) {
            this.cancel();
        }
        fn = newFn || fn;
        scope = newScope || scope;
        args = newArgs || args;
        if(!id) {
            id = setTimeout(call, delay);
        }
    };
    this.cancel = function() {
        if(id) {
            clearTimeout(id);
            id = null;
        }
    };
};

Ext.StatusBar = Ext.ux.StatusBar = Ext.extend(Ext.Toolbar, {
    textId: '',
    defaultText: '',
    autoClear: 5000,
    task: null,
    initComponent: function() {
        this.textId = Ext.id();
        this.defaultText = this.initialConfig.defaultText || '';
        var text = this.initialConfig.text || this.defaultText;

        var config = {
            items: [
                '<span id="' + this.textId + '">' + text + '</span>',    // status text
                '->'                            // make it greedy
            ]
        };
        if(this.initialConfig.items) {
            config.items = config.items.concat(this.initialConfig.items);
            delete this.initialConfig.items;
        }
        Ext.apply(this, Ext.apply(this.initialConfig, config));
        Ext.ux.StatusBar.superclass.initComponent.apply(this, arguments);
        this.task = new Ext.ux.DelayedTask(function() {
            var el = Ext.get(this.textId);
            var defaultText = this.defaultText;
            el.fadeOut({
                callback: function() {
                    el.update(defaultText);
                    el.show();
                },
                duration: 1
            });
        }, this);
    },
    onRender: function() {
        Ext.ux.StatusBar.superclass.onRender.apply(this, arguments);
    },
    setText: function(text) {
        var el = Ext.get(this.textId);
        el.update(text);
    },
    setStatus: function(config) {
        var defaults = {
            clear: {
                wait: this.autoClear,
                anim: true,
                useDefaults: true
            }
        };

        if(config.clear === true) {
            delete config.clear;
        }
        if(!Ext.isArray(config)) {
            config = {
                text: config.text || ''
            }
        }
        Ext.apply(config, defaults);
        var el = Ext.get(this.textId);
        el.update(config.text);
        var clear = config.clear;
        var defaultText = this.defaultText;
        if(clear.wait) {
            this.task.delay(clear.wait);
        }
        else {
            this.task.cancel();
        }
    },
    clearStatus: function() {
        this.setText(this.defaultText);
        this.task.cancel();
    },
    showBusy: function(msg) {
        // stub for now
    }
});


Ext.override(Ext.grid.RowSelectionModel, {
    selectRow : function(index, keepExisting, preventViewNotify) {
        if(this.isLocked() || (index < 0 || index >= this.grid.store.getCount()) ||
                (keepExisting && this.isSelected(index))) {
            return;
        }
        var r = this.grid.store.getAt(index);
        if(r && this.fireEvent("beforerowselect", this, index, keepExisting, r) !== false) {
            if(!keepExisting || this.singleSelect) {
                this.clearSelections();
            }
            this.selections.add(r);
            this.last = this.lastActive = index;
            if(!preventViewNotify) {
                this.grid.getView().onRowSelect(index);
            }
            this.fireEvent("rowselect", this, index, r);
            this.fireEvent("selectionchange", this);
        }
    }
});


/* ??. http://extjs.com/forum/showthread.php?p=152336#post152336  */
/**
 * Ext.ux.ToastWindow
 *
 * @author  Edouard Fattal
 * @date    March 14, 2008
 *
 * @class Ext.ux.ToastWindow
 * @extends Ext.Window
 */

Ext.namespace("Ext.ux");


Ext.ux.NotificationMgr = {
    positions: []
};

Ext.ux.Notification = Ext.extend(Ext.Window, {
    initComponent: function() {
        Ext.apply(this, {
            iconCls: this.iconCls || 'x-icon-information',
            cls: 'x-notification',
            width: 200,
            autoHeight: true,
            plain: false,
            draggable: false,
            bodyStyle: 'text-align: left; padding: 5px;'
        });
        if(this.autoDestroy) {
            this.task = new Ext.util.DelayedTask(this.hide, this);
        } else {
            this.closable = true;
        }
        Ext.ux.Notification.superclass.initComponent.call(this);
    },
    setMessage: function(msg) {
        this.body.update(msg);
    },
    setTitle: function(title, iconCls) {
        Ext.ux.Notification.superclass.setTitle.call(this, title, iconCls || this.iconCls);
    },
    onRender:function(ct, position) {
        Ext.ux.Notification.superclass.onRender.call(this, ct, position);
    },
    onDestroy: function() {
        Ext.ux.NotificationMgr.positions.remove(this.pos);
        Ext.ux.Notification.superclass.onDestroy.call(this);
    },
    cancelHiding: function() {
        this.addClass('fixed');
        if(this.autoDestroy) {
            this.task.cancel();
        }
    },
    afterShow: function() {
        Ext.ux.Notification.superclass.afterShow.call(this);
        Ext.fly(this.body.dom).on('click', this.cancelHiding, this);
        if(this.autoDestroy) {
            this.task.delay(this.hideDelay || 4000);
        }
    },
    animShow: function() {
        this.pos = 0;
        while(Ext.ux.NotificationMgr.positions.indexOf(this.pos) > -1) {
            this.pos++;
        }
        Ext.ux.NotificationMgr.positions.push(this.pos);
        this.setSize(200, 100);
        this.el.alignTo(document, "br-br", [ -20, -20 - ((this.getSize().height + 10) * this.pos) ]);
        this.el.slideIn('b', {
            duration: 1,
            callback: this.afterShow,
            scope: this
        });
    },
    animHide: function() {
        Ext.ux.NotificationMgr.positions.remove(this.pos);
        this.el.ghost("b", {
            duration: 1,
            remove: true
        });
    },

    focus: Ext.emptyFn

});


SPAUTR = {
    defaults : {
    }
};

Docs = {};

Ext.namespace('Spautr');

Spautr.windowMgr = new Ext.WindowGroup();


Spautr.jsonEncodeStore = function(store) {
    var data_array = [];
    store.each(function(store) {
        data_array.push(store.data);
    });
    return Ext.util.JSON.encode(data_array);
};


/**
 * Ext.ux.form.XDateField - Date field that supports submitFormat
 * author  Ing. Jozef Sakalos
 * see http://extjs.com/forum/showthread.php?t=25900
 */

Ext.ns('Ext.ux.form');

/**
 * @class Ext.ux.form.XDateField
 * @extends Ext.form.DateField
 */
Ext.ux.form.XDateField = Ext.extend(Ext.form.DateField, {
    //submitFormat:'Y-m-d',
	submitFormat:'d/m/Y',
    submitFormatSuffix: '-format',
	format: 'd/m/Y'
    ,onRender:function() {

        // call parent
        Ext.ux.form.XDateField.superclass.onRender.apply(this, arguments);
        var name = this.name || this.el.dom.name;

        // IE doesn't like duplicate names. See http://extjs.com/forum/showthread.php?p=148800#post148800
        this.name = (typeof this.name == "undefined" ? this.id + this.submitFormatSuffix : (this.name == this.id ? this.name + this.submitFormatSuffix : this.name));

        this.hiddenField = this.el.insertSibling({
            tag:'input'
            ,type:'hidden'
            ,name:name
            ,value:this.formatHiddenDate(this.parseDate(this.value))
        });
        this.hiddenName = this.name; // otherwise field is not found by BasicForm::findField
        this.el.dom.removeAttribute('name');
        this.el.on({
            keyup:{scope:this, fn:this.updateHidden}
            ,blur:{scope:this, fn:this.updateHidden}
        }, Ext.isIE ? 'after' : 'before');

        this.setValue = this.setValue.createSequence(this.updateHidden);

    } // eo function onRender

    ,onDisable: function() {
        // call parent
        Ext.ux.form.XDateField.superclass.onDisable.apply(this, arguments);
        if(this.hiddenField) {
            this.hiddenField.dom.setAttribute('disabled', 'disabled');
        }
    } // of function onDisable

    ,onEnable: function() {
        // call parent
        Ext.ux.form.XDateField.superclass.onEnable.apply(this, arguments);
        if(this.hiddenField) {
            this.hiddenField.dom.removeAttribute('disabled');
        }
    } // eo function onEnable

    ,formatHiddenDate : function(date) {
        if(!Ext.isDate(date)) {
            return date;
        }
        if('timestamp' === this.submitFormat) {
            return date.getTime() / 1000;
        }
        else {
            return Ext.util.Format.date(date, this.submitFormat);
        }
    }

    ,updateHidden:function() {
        this.hiddenField.dom.value = this.formatHiddenDate(this.getValue());
    } // eo function updateHidden

}); // end of extend

// register xtype
Ext.reg('xdatefield', Ext.ux.form.XDateField);

// eof


UI = {
    defaults : {},
    tabs : [],
    showMessage : function(title, msg, callback, icon) {
        if(!msg) {
            msg = title;
            title = 'Error';
        }
        if(!callback) {
            callback = Ext.emtyFn;
        }
        Ext.MessageBox.show({
            title: title,
            msg: msg,
            buttons: Ext.MessageBox.OK,
            callback: callback,
            icon: icon
        });
    },
    alert : function(t/*itle*/, m/*sg*/, c/*allback*/) {
        if(m) {
            this.notify(t, m, 'error');
        }
        else {
            this.notify(t, undefined, 'error');
        }
        //this.showMessage(title, msg, callback, Ext.MessageBox.ERROR);
    },
    info: function(t/*itle*/, m/*sg*/, c/*allback*/) {
        if(m) {
            this.notify(t, m, 'information');
        }
        else {
            this.notify(t, undefined, 'information');
        }
        //this.showMessage(title, msg, callback, Ext.MessageBox.INFO);
    },
    translations : {
        /*
         *	UI.translations.add([
         *		{hello : 'Hello, {0}'}
         *	]);
         *
         *	UI.tr('hello', 'world');
         *
         **/
        add : function(strs) {
            if(!strs) {
                return;
            }

            if(strs.constructor.toString().indexOf("Array") != -1) {
                for(str in strs) {
                    for(p in strs[str]) {
                        this.strings[p] = strs[str][p];
                    }
                }
            } else if(strs.constructor.toString().indexOf("Object") != -1) {
                for(p in strs) {
                    this.strings[p] = strs[p];
                }
            } else if(arguments.length == 2) {
                this.strings[arguments[0]] = arguments[1];
            }
        },
        strings : []
    },
    tr: function(text) {
        if(this.translations.strings[text]) {
            arguments[0] = this.translations.strings[text];
        }

        return String.format.apply(this, arguments);
    },
    notify: function(t/*itle*/, h/*tml*/, ty/*pe*/) {
        var title = h === undefined ? null : t;
        var html = h === undefined ? t : h;
        var type = ty === undefined ? 'information' : ty;
        //console.log(ty);

        title = title || (type == 'information' ? 'Info' : 'error');
        //this.alert(title + '\n' + html + '\n' + typeof(t));
        var coords = Ext.get('notification-area').getXY();
        /*var notification = new Ext.ToolTip({
         html: html,
         targetXY: coords,
         title: title,
         dismissDelay: 15000,
         autoHide: true,
         closable: true
         });

         notification.show();*/
        new Ext.ux.Notification({
            title: title,
            html: html,
            iconCls: 'x-icon-' + type
        }).show(document);
    },
    setTitle : function(t/*ab*/, ti/*itle*/) {
        var title = ti || tab;
        var tab = this.mainPanel;
        for(var i = 0; i < this.tabs.length; i++) {
            if(this.tabs[i].id == t) {
                tab = this.tabs[i].tab;
            }
        }

        tab.setTitle(title);
    },
    setTabs : function(tabs, c/*allback*/) {
        var callback = c || function() {
            return;
        };
        this.mainPanel.clear();
        for(var i = 0; i < tabs.length; i++) {
            /*var tab = this.mainPanel.add({
             id: tabs[i].id,
             title: 'Loading...',
             html: 'Loading...',
             xtype: 'panel',
             layout: 'fit'
             });*/

            //this.mainPanel.activate(tab);
            this.tabs.push({id:tabs[i].id, /*tab: tab, */url: tabs[i].url});

            if(i == tabs.length - 1) {
                this.mainPanel.loadToTab(tabs[i].id, tabs[i].url, null, null, false, callback);
            }
            else {
                this.mainPanel.loadToTab(tabs[i].id, tabs[i].url, null, null, false);
            }

            //this.mainPanel.activate(this.tabs[0].id);
        }
    },
    getTab : function(id) {
        var tab = Ext.getCmp(id) || this.activeTab;
        return tab;
    },
    getToolbar : function(id) {
        var tab = this.getTab(id);
        return Ext.getCmp((tab ? tab.id : this.activeTab.id) + 'my-tbar');
    },
    getStatusbar : function(id) {
        var tab = this.getTab(id);
        return Ext.getCmp((tab ? tab.id : this.activeTab.id) + 'my-status');
    }
};

Spautr.Breadcrumbs = function(config) {
    this.toolbar = config.toolbar || null;
    var self = this;

    this.back = new Ext.Toolbar.Button({
        icon: 'images/icons/arrow_left.png', cls:"x-btn-icon",
        handler: function() {
            self.click(-1);
        },
        disabled: true,
        id: 'spautr-breadcrumb-back'
    });
    this.fwd = new Ext.Toolbar.Button({
        icon: 'images/icons/arrow_right.png', cls:"x-btn-icon",
        handler: function() {
            self.click(1);
        },
        disabled: true,
        id: 'spautr-breadcrumb-fwd'
    });
    if(this.toolbar) {
        this.toolbar.add(
                this.back,
                this.fwd,
        {
            xtype: 'button',
            itemId: 'spautr-breadcrumb-0',
            visible: true,
            handler: function() {
                self.click(-2);
            }
        }, /*{
             xtype: 'tbtext',
             id: 'spautr-breadcrumb-sep-1',
             text: '/',
             visible: false
             },*/{
            xtype: 'button',
            id: 'spautr-breadcrumb-1',
            visible: true,
            handler: function() {
                self.click(-1);
            }
        }, {
            xtype: 'button',
            id: 'spautr-breadcrumb-2',
            icon: 'images/icons/mini/action_refresh.gif',
			cls:"x-btn-text-icon",
            handler: function() {
                self.click(0);
            }
        }, {
            xtype: 'button',
            id: 'spautr-breadcrumb-3',
            visible: true,
            handler: function() {
                self.click(1);
            }
        }, {
            xtype: 'button',
            id: 'spautr-breadcrumb-4',
            visible: true,
            handler: function() {
                self.click(2);
            }
        });
    }
    for(var i = 0; i <= 5; i ++) {
        var b = this.toolbar.items.get('spautr-breadcrumb-' + i);
        var s = this.toolbar.items.get('spautr-breadcrumb-sep-' + i);
        if(b) {
            b.setVisible(false);
            if(s) {
                s.setVisible(false);
            }
        }
    }

    this.maxDepth = config.maxDepth || 20;
    this.currentIndex = 0;
    this.store = config.store || new Ext.data.SimpleStore({
        fields : ['href', 'params', 'title'],
        autoLoad : true,
        data: []
    });
    this.record = new Ext.data.Record.create(['href','params','title']);

    Spautr.Breadcrumbs.superclass.constructor.call(this);
    this.toolbar.doLayout();
};

Ext.extend(Spautr.Breadcrumbs, Ext.Element, {
    add : function(h/*ref*/, p/*arams*/, t/*itle*/) {
        var href = h;
        var params = {};
        var title = '';
        if(p && p.constructor.toString().indexOf("String") != -1) {
            title = p;
        } else {
            params = p;
        }
        if(t) {
            title = t;
        }

        // ???? ?? ???????? ???-?? ? ???????? ? ????????? ????? ????,
        // ??????? ??? ???? ? ???????? ???? ????????
        // ? ????????? ?????
        for(var i = this.currentIndex + 1; i < this.store.getCount(); i++) {
            this.store.remove(this.store.getAt(i));
        }

        // ???? ????? ??????, ??? maxDepth, ??????? ????? ??????
        if(this.store.getCount() == this.store.maxDepth) {
            this.store.remove(this.store.getAt(0));
        }

        // ????????? ???? ? ????????? ???????????
        this.store.add(new this.record({href: href, params: params, title:title}));

        this.currentIndex = this.store.getCount() - 1;

        this.updateDisplay();
    },

    updateDisplay : function() {
        if(!this.toolbar) {
            return;
        }

        // ??????????? ????????? ???????:
        // *?????? ????* / *?????? ????* / ... / _??????? ????_ / _??????? ????_
        // *????* � ????? ??????, _????_ � ?????? ??????
        // ... � ??????? ??????? (UI.activeTab)

        // ?????: ????????? ??? ?????????? ??????? ? ??? ??????? ???????
        for(var i = this.currentIndex - 2, localIndex = 0; i <= this.currentIndex + 2; i ++,localIndex++) {
            var item = this.store.getAt(i);
            var button = this.toolbar.items.get('spautr-breadcrumb-' + localIndex);
            var sep = this.toolbar.items.get('spautr-breadcrumb-sep-' + localIndex);
            if(item) {
                if(button) {
                    button.setText(item.data.title);
                    button.show();
                    if(sep) {
                        sep.show();
                    }
                }
            } else {
                if(button) {
                    button.hide();
                    button.setText('');
                    if(sep) {
                        sep.hide();
                    }
                }
            }
        }

        // ?????? ?????-??????
        if(this.currentIndex === 0) {
            this.back.disable();
        }
        else {
            this.back.enable();
        }
        if(this.currentIndex == this.store.getCount() - 1) {
            this.fwd.disable();
        }
        else {
            this.fwd.enable();
        }
    },
    click : function(index) {
        this.currentIndex += index;
        var item = this.store.getAt(this.currentIndex);
        this.updateDisplay();
        if(item) {
            UI.mainPanel.load(item.data.href, item.data.params, item.data.title, false);
        }
    },
    setCurrentTitle : function(title) {
        var rec = this.store.getAt(this.currentIndex);
        if(rec) {
            rec.data.title = title;
            this.store.commitChanges();
        }
    }
});

SpautrToolBar = {};
SpautrToolBar = Ext.extend(Ext.Toolbar, {
    /*remove : function(item){
     if(this.rendered && item){

     if(item.destroy)item.destroy();
     if(item.removeListeners)item.removeListeners();

     //all items are contained by a <TD> tag, so might need:
     if(item.dom && item.dom.parentNode){
     var td = item.dom.parentNode;
     td.removeChild(item.dom);
     td.parentNode.removeChild(td);
     }

     this.items.remove(item);
     }
     },

     clear : function(){
     if(this.items){
     var myself = this;
     this.items.each(function(item){myself.remove(item);});
     }
     }*/
});


ApiPanel = function() {
    ApiPanel.superclass.constructor.call(this, {
        id:'api-tree',
        region:'west',
        split:true,
        width: 280,
        minSize: 175,
        maxSize: 500,
        collapsible: true,
        margins:'0 0 5 5',
        cmargins:'0 0 0 0',
        rootVisible:false,
        lines:false,
        autoScroll:true,
        animCollapse:false,
        animate: false,
        collapseMode:'mini',
        loader: new Ext.tree.TreeLoader({
            preloadChildren: true,
            clearOnLoad: false
        }),
        root: new Ext.tree.AsyncTreeNode({
            text:'Ext JS',
            id:'root',
            expanded:true,
            children: Docs.classData
        }),
        collapseFirst:false
    });
    // no longer needed!
    //new Ext.tree.TreeSorter(this, {folderSort:true,leafAttr:'isClass'});

    this.getSelectionModel().on('beforeselect', function(sm, node) {
        return node.isLeaf();
    });
};

Ext.extend(ApiPanel, Ext.tree.TreePanel, {
    selectClass : function(cls) {
        if(cls) {
            var parts = cls.split('.');
            var last = parts.length - 1;
            for(var i = 0; i < last; i++) { // things get nasty - static classes can have .
                var p = parts[i];
                var fc = p.charAt(0);
                var staticCls = fc.toUpperCase() == fc;
                if(p == 'Ext' || !staticCls) {
                    parts[i] = 'pkg-' + p;
                } else if(staticCls) {
                    --last;
                    parts.splice(i, 1);
                }
            }
            parts[last] = cls;

            this.selectPath('/root/apidocs/' + parts.join('/'));
        }
    }
});


DocPanel = Ext.extend(Ext.Panel, {
    autoScroll:true,
    remove : function(item) {
        if(this.rendered && item) {

            if(item.destroy) {
                item.destroy();
            }
            if(item.removeListeners) {
                item.removeListeners();
            }

            //all items are contained by a <TD> tag, so might need:
            if(item.dom && item.dom.parentNode) {
                var td = item.dom.parentNode;
                td.removeChild(item.dom);
                td.parentNode.removeChild(td);
            }

            this.items.remove(item);
        }
    },

    clear : function() {
        if(this.items) {
            var myself = this;
            this.items.each(function(item) {
                myself.remove(item);
            });
        }
    }
});


MainPanel = function(panel_id, panel_title) {
    var id = panel_id || 'main-tab-panel';
    var title = panel_title || 'Welcome';
    var bbar_id = id == id + 'my-status';
    var tbar_id = id == id + 'my-tbar';

    MainPanel.superclass.constructor.call(this, {
        id:'doc-body',
        region:'center',
        margins:'0 5 5 0',
        resizeTabs: true,
        minTabWidth: 135,
        tabWidth: 135,
        plugins: new Ext.ux.TabCloseMenu(),
        enableTabScroll: true,
        activeTab: 0,
        centralUpdater: null,
        deferredRender : false,
        layoutOnTabChange:true,


        items: {
            bbar: new Ext.StatusBar({
                defaultText: 'Ready',
                id: bbar_id,
                items: [
                    {text: '', id: 'notification-area', disabled: true}
                ]

            }),


            id:id,
            title: 'Welcome',
            iconCls:'icon-docs',

            layout: 'fit',
            autoScroll: false,
            tbar: new SpautrToolBar({id:tbar_id, items: ['']})
        }
    });
};

Ext.extend(MainPanel, Ext.TabPanel, {
    initEvents : function() {
        MainPanel.superclass.initEvents.call(this);
        this.body.on('click', this.onClick, this);
    },

    onClick: function(e, target) {

        // ????? ?? ???? ?????? ???????????? ??? ?????? ??
        // ????? (????? ???????) ??? ?? ???????????/datefield'?? (IE)
        if(target.href && !/.*\-(tab|form|no-link)\-.*/.test(target.className)) {
			e.stopEvent();
            this.load/*ToTab*/(/*this.activeTab.id, */target.href);
        }
        //		if(target = e.getTarget('a.exi', 3)){
        //			this.load(target.href);
        //		}
        /*		if(target = e.getTarget('a:not(.exi)', 3)){
         var cls = Ext.fly(target).getAttributeNS('ext', 'cls');
         e.stopEvent();
         if(cls){
         var member = Ext.fly(target).getAttributeNS('ext', 'member');
         this.load(target.href, cls);
         }else if(target.className == 'inner-link'){
         this.getActiveTab().scrollToSection(target.href.split('#')[1]);
         }else{
         window.open(target.href);
         }
         }else if(target = e.getTarget('.micon', 2)){
         e.stopEvent();
         var tr = Ext.fly(target.parentNode);
         if(tr.hasClass('expandable')){
         tr.toggleClass('expanded');
         }
         }*/
    },
    load : function(h/*ref*/, p/*arams*/, t/*itle*/, s/*ave_breadcrumb*/, c/*allback*/) {
        this.clear();
        this.loadToTab('main-tab-panel', h/*ref*/, p/*arams*/, t/*itle*/, s/*ave_breadcrumb*/, c/*allback*/);
    },
    loadToTab : function(id, h/*ref*/, p/*arams*/, t/*itle*/, s/*ave_breadcrumb*/, c/*allback*/) {
        var href = h.href || h;
        var params = h.params || {};
        var title = h.title || 'Loading...';
        var save_breadcrumb = h.save_breadcrumb || true;
        var callback = c || h.callback || null;

        if(p && p.constructor.toString().indexOf("Object") != -1) {
            params = p;
        } else if(p && p.constructor.toString().indexOf("String") != -1) {
            title = p;
        } else if(p !== undefined && p !== null && (p.constructor.toString().indexOf("Boolean") != -1 || p.constructor.toString().indexOf("Number") != -1)) {
            save_breadcrumb = p;
        } else if(p) {
            callback = p;
        }

        if(t && t.constructor.toString().indexOf("String") != -1) {
            title = t;
        } else if(t !== undefined && t !== null && (t.constructor.toString().indexOf("Boolean") != -1 || t.constructor.toString().indexOf("Number") != -1)) {
            save_breadcrumb = t;
        } else if(t) {
            callback = t;
        }

        if(s !== undefined && s !== null && (s.constructor.toString().indexOf("Boolean") != -1 || s.constructor.toString().indexOf("Number") != -1)) {
            save_breadcrumb = s;
        } else if(s) {
            callback = s;
        }

        //var id = 'main-tab-panel';
        var tab = this.findById(id);
        var updateManager;
        var updater;

        var myself = this;
        var do_update = false;
        //while(this.get(0)) this.remove(this.get(0));
        var item_index = this.items.length;
        if(tab) {
            item_index = this.items.indexOf(tab);
            this.remove(tab);
            tab = false;
            //do_update = true;
            //tab.items.each(function(item){myself.remove(item);});
            //tab.add();
        }

        UI.data = {};

	UI.data.markupMenu = {};
	UI.data.markupMenu.value = {};
	UI.data.markupMenu.currency = {};
	UI.data.markupMenu.menu = {};
	UI.data.markupMenu.button = {};

        if(true/*!do_update*/) {
            var bbar_id = id + 'my-status';
            var tbar_id = id + 'my-tbar';

	    var statusBar = new Ext.StatusBar({
		defaultText: 'Ready',
		id: bbar_id,
		items: [
		    {text: ' ', id: 'notification-area'}
		]
	    });

	    var path = href.split('/');

	    if (path[1] == 'search' && path[2] != 'sale-orders' && path[2] != 'sub-agencies') {
		UI.data.markupMenu.value = new Ext.form.TextField({
			xtype: 'textfield',
			id: 'markup',
			fieldLabel: '+',
			width: 30,
			labelSeparator: '',
			value: UI.data.selectedMarkup.value
		});

		UI.data.markupMenu.currency = new Ext.form.ComboBox({
			xtype: 'combo',
			id: 'markup-currency-combo',
			width: 45,
			editable: false,
			store: UI.data.currencyStore,
			valueField: 'id',
			displayField: 'name',
			mode: 'local',
			lazyInit: true,
			value: UI.data.selectedMarkup.currency,
			listeners: {
				focus: function() {
					UI.data.markupMenu.menu.canHide = false;
				},
				blur: function() {
					UI.data.markupMenu.menu.canHide = true;
				},
				select: function() {
					UI.data.markupMenu.menu.canHide = true;
				}
			}
		});

		UI.data.markupMenu.menu = new Ext.menu.Menu({
			id: 'markup-currency-menu',
			canHide: true,
			items: [new Ext.FormPanel({
				layout: 'column',
				//autoWidth: true,
				width: 120,
				frame: true,
				border: false,
				style: 'padding: 1px; margin: 0px;',
				items: [{
					columnWidth: .5,
					layout: 'form',
					labelWidth: 10,
					items: [UI.data.markupMenu.value]
				}, {
					columnWidth: .5,
					layout: 'form',
					hideLabels: true,
					items: [UI.data.markupMenu.currency]
				}]
			})],
			listeners: {
				beforehide: function(c) {
					if(false == c.canHide) {
						return false;
					}
				}
			}
		});

		UI.data.markupMenu.button = new Ext.Button({
			xtype: 'button',
			text: '%',
			id: 'markup-button',
			hidden: true,
			handler: function() {
				//if (true == UI.data.markupMenu.menu.canHide) {
				//	UI.data.markupMenu.menu.canHide = false;
				//} else {
					//UI.data.markupMenu.menu.canHide = true;
					//UI.data.markupMenu.menu.hide();
					UI.data.selectedMarkup.value = UI.data.markupMenu.value.getValue();
					UI.data.selectedMarkup.currency = UI.data.markupMenu.currency.getValue();
				//}
			},
			menu: UI.data.markupMenu.menu
		})

		statusBar.add(UI.data.markupMenu.button);
	    }

            tab = this.insert(item_index, {
                bbar: statusBar,
                id:id,
                title: 'Welcome',
                iconCls:'icon-docs',

                layout: 'fit',
                autoScroll: false,
                tbar: new SpautrToolBar({id: tbar_id, items: ['']})
            });
            this.activate(tab);
            //tab.doLayout();
            //this.doLayout();
        }
        //this.beginUpdate();

        if(tab) {
            var self = this;
            /*if(Docs.icons && Docs.icons[cls])
             tab.setTitle(ps[ps.length-1], Docs.icons[cls]);
             else*/
            if(!this.centralUpdater) {
                updater = tab.getUpdater();
                updater.on('failure', function(el, response) {
                    tab.add({
                        xtype: 'panel',
                        layout: 'fit',
                        autoScroll: true,
                        html: '<p style="padding: 15px"><img src="images/icons/error.png" />&nbsp;<strong>Page <a href="' + updater.defaultUrl + '">' + updater.defaultUrl + '</a> could not be loaded</strong><br /><br /><br />' +
                                '<em>Failure reason:</em><br /><br />' +
                                response.responseText + "</p>",
                        padding: '15px'
                    });
                    tab.doLayout();
                    tab.setTitle('Error: {0}', updater.defaultUrl);
                    Ext.each(Ext.query('.loading-indicator'), function(i) {
                        Ext.fly(i).remove();
                    });
                });
                //updater.on('update', function() {
                    //self.endUpdate();
                    //console.log(UI);
                //})
                updater.loadScripts = true;
            }
        }

        if(updater) {
            if(save_breadcrumb) {
                UI.breadcrumbs.add(href, params, title);
            }

            tab.setTitle(title);

            // ??????? ??????? ???????? ?????????? ????
            Ext.WindowMgr.each(function(w) {
				if(!w.xspecial){
	                w.close();
	                w.destroy();
				}
            });
			Spautr.windowMgr.each(function(w) {
                w.close();
                w.destroy();
            });

            updater.update({url:href, params:params, callback:callback});
        } else {
            //this.endUpdate();
        }

    },
    loadClass : function(href, cls, member) {
        var ps = cls.split('.');
        this.load(href, {}, ps[ps.length - 1]);
    },

    initSearch : function() {

    },
    /*remove : function(item){
     if(this.rendered && item){

     if(item.destroy)item.destroy();
     if(item.removeListeners)item.removeListeners();

     //all items are contained by a <TD> tag, so might need:
     if(item.dom && item.dom.parentNode){
     var td = item.dom.parentNode;
     td.removeChild(item.dom);
     td.parentNode.removeChild(td);
     }

     this.items.remove(item);
     }
     },*/

    clear : function() {
        var myself = this;
        //while(this.get(0)) this.remove(this.get(0));
        this.items.each(function(item) {
            myself.remove(item);
        });

        /*
         if(this.activeTab.items){
         var myself = this;
         this.activeTab.items.each(function(item){myself.remove(item);});
         }*/
    },

    listeners: {
        tabchange : function() {
            UI.activeTab = UI.mainPanel.getActiveTab();
            if(!UI.activeTab) return;
            if(!UI.activeTab.setTitleOriginal) {
                UI.activeTab.setTitleOriginal = UI.activeTab.setTitle;
                UI.activeTab.setTitle = function() {
                    var title = String.format.apply(String, Array.prototype.slice.call(arguments, 0));
                    UI.breadcrumbs.setCurrentTitle(title);
                    UI.activeTab.setTitleOriginal(title);
                };
            }

            UI.toolbar = Ext.getCmp(UI.activeTab.id + 'my-tbar');
            UI.statusbar = Ext.getCmp(UI.activeTab.id + 'my-status');
            if(UI.mainPanel.lastSize && UI.mainPanel.lastSize.width) {
                UI.mainPanel.setSize(UI.mainPanel.lastSize.width, UI.mainPanel.lastSize.height + 1);
                UI.mainPanel.setSize(UI.mainPanel.lastSize.width, UI.mainPanel.lastSize.height - 1);
            }
        },
        activate : function() {
            UI.activeTab = UI.mainPanel.getActiveTab();
            if(!UI.activeTab.setTitleOriginal) {
                UI.activeTab.setTitleOriginal = UI.activeTab.setTitle;
                UI.activeTab.setTitle = function() {
                    var title = String.format.apply(String, Array.prototype.slice.call(arguments, 0));
                    UI.breadcrumbs.setCurrentTitle(title);
                    UI.activeTab.setTitleOriginal(title);
                };
            }

            UI.toolbar = Ext.getCmp(UI.activeTab.id + 'my-tbar');
            UI.statusbar = Ext.getCmp(UI.activeTab.id + 'my-status');
            if(UI.mainPanel.lastSize && UI.mainPanel.lastSize.width) {
                UI.mainPanel.setSize(UI.mainPanel.lastSize.width, UI.mainPanel.lastSize.height + 1);
                UI.mainPanel.setSize(UI.mainPanel.lastSize.width, UI.mainPanel.lastSize.height - 1);
            }
        }
    }
});


Ext.onReady(function() {
    var url = window.location.href;
    var parts = url ? String(url).split('#') : ['', ''];

    Ext.QuickTips.init();

    var api = new ApiPanel();

    UI.mainPanel = new MainPanel();

    api.on('click', function(node, e) {
        if(node.isLeaf()) {
            e.stopEvent();
            UI.mainPanel.clear();
            UI.mainPanel.load(node.attributes.href, node.attributes.text);
        }
    });

    //UI.mainPanel.on('tabchange', function(tp, tab){
    //	api.selectClass(tab.cclass);
    //});

    var topToolbar = new Ext.Toolbar({
        cls:'top-toolbar',
        items:[ ' ',
            new Ext.form.TextField({
                width: 200,
                emptyText:'Quick menu search',
                listeners:{
                    render: function(f) {
                        f.el.on('keydown', filterTree, f, {
                            buffer: 350
                        });
                    }
                }
            }), ' ', ' ',
            {
                iconCls: 'icon-expand-all',
                tooltip: 'Expand All',
                handler: function() {
                    api.root.expand(true);
                }
            }, '-', {
                iconCls: 'icon-collapse-all',
                tooltip: 'Collapse All',
                handler: function() {
                    api.root.collapse(true);
                }
            }, '->'/*, {
             tooltip:'Hide Inherited Members',
             iconCls: 'icon-hide-inherited',
             enableToggle: true,
             toggleHandler : function(b, pressed){
             mainPanel[pressed ? 'addClass' : 'removeClass']('hide-inherited');
             }
             }, '-', {
             tooltip:'Expand All Members',
             iconCls: 'icon-expand-members',
             enableToggle: true,
             toggleHandler : function(b, pressed){
             mainPanel[pressed ? 'addClass' : 'removeClass']('full-details');
             }
             }*/]
    });

    var hd = new Ext.Panel({
        border: false,
        layout:'anchor',
        region:'north',
        cls: 'docs-header',
        height:60,
        items: [
            {
                xtype:'box',
                el:'header',
                border:false,
                anchor: 'none -25'
            },
            topToolbar
        ]
    });

    var viewport = new Ext.Viewport({
        layout:'border',
        items:[ hd, api, UI.mainPanel ]
    });

    api.expandPath('/root');

    // allow for link in
    /*	var page = window.location.href.split('?')[1];
     if(page){
     var ps = Ext.urlDecode(page);
     var cls = ps['class'];
     UI.mainPanel.load('output/' + cls + '.html', cls);
     }
     */
    viewport.doLayout();

    UI.activeTab = UI.mainPanel.activeTab;
    UI.toolbar = UI.mainPanel.activeTab.topToolbar;
    UI.statusbar = UI.mainPanel.activeTab.bottomToolbar;
    UI.activeTab = UI.mainPanel.activeTab;
    UI.menu = api;

    UI.breadcrumbs = new Spautr.Breadcrumbs({toolbar: topToolbar});

    UI.activeTab.setTitleOriginal = UI.activeTab.setTitle;
    UI.activeTab.setTitle = function() {
        var title = String.format.apply(String, Array.prototype.slice.call(arguments, 0));
        UI.breadcrumbs.setCurrentTitle(title);
        UI.activeTab.setTitleOriginal(title);
    };

    setTimeout(function() {
        Ext.get('loading').remove();
        Ext.get('loading-mask').fadeOut({
            remove:true
        });
    }, 250);

    var filter = new Ext.tree.TreeFilter(api, {
        clearBlank: true,
        autoClear: true
    });

    //console.log(parts);

    if(parts[1] && parts[1] != '') {
        UI.mainPanel.load(parts[1]);
    }


    var hiddenPkgs = [];

    function filterTree(e) {
        var text = e.target.value;
        Ext.each(hiddenPkgs, function(n) {
            n.ui.show();
        });
        if(!text) {
            filter.clear();
            return;
        }
        api.expandAll();

        var re = new RegExp('^' + Ext.escapeRe(text), 'i');
        filter.filterBy(function(n) {
            return !n.attributes.isClass || re.test(n.text);
        });

        // hide empty packages that weren't filtered
        hiddenPkgs = [];
        api.root.cascade(function(n) {
            if(!n.attributes.isClass && n.ui.ctNode.offsetHeight < 3) {
                n.ui.hide();
                hiddenPkgs.push(n);
            }
        });
    }
});


Ext.app.SearchField = Ext.extend(Ext.form.TwinTriggerField, {
    initComponent : function() {
        if(!this.store.baseParams) {
            this.store.baseParams = {};
        }
        Ext.app.SearchField.superclass.initComponent.call(this);
        this.on('specialkey', function(f, e) {
            if(e.getKey() == e.ENTER) {
                this.onTrigger2Click();
            }
        }, this);
    },

    validationEvent:false,
    validateOnBlur:false,
    trigger1Class:'x-form-clear-trigger',
    trigger2Class:'x-form-search-trigger',
    hideTrigger1:true,
    width:180,
    hasSearch : false,
    paramName : 'query',

    onTrigger1Click : function() {
        if(this.hasSearch) {
            this.store.baseParams[this.paramName] = '';
            this.store.removeAll();
            this.el.dom.value = '';
            this.triggers[0].hide();
            this.hasSearch = false;
            this.focus();
        }
    },

    onTrigger2Click : function() {
        var v = this.getRawValue();
        if(v.length < 1) {
            this.onTrigger1Click();
            return;
        }
        if(v.length < 2) {
            Ext.Msg.alert('Invalid Search', 'You must enter a minimum of 2 characters to search the API');
            return;
        }
        this.store.baseParams[this.paramName] = v;
        var o = {
            start: 0
        };
        this.store.reload({
            params:o
        });
        this.hasSearch = true;
        this.triggers[0].show();
        this.focus();
    }
});


/**
 * Makes a ComboBox more closely mimic an HTML SELECT.  Supports clicking and dragging
 * through the list, with item selection occurring when the mouse button is released.
 * When used will automatically set {@link #editable} to false and call {@link Ext.Element#unselectable}
 * on inner elements.  Re-enabling editable after calling this will NOT work.
 *
 * @author Corey Gilmore
 * http://extjs.com/forum/showthread.php?t=6392
 *
 * @history 2007-07-08 jvs
 * Slight mods for Ext 2.0
 */
Ext.ux.SelectBox = function(config) {
    this.searchResetDelay = 1000;
    config = config || {};
    config = Ext.apply(config || {}, {
        editable: false,
        forceSelection: true,
        rowHeight: false,
        lastSearchTerm: false,
        triggerAction: 'all',
        mode: 'local'
    });

    Ext.ux.SelectBox.superclass.constructor.apply(this, arguments);

    this.lastSelectedIndex = this.selectedIndex || 0;
};

Ext.extend(Ext.ux.SelectBox, Ext.form.ComboBox, {
    lazyInit: false,
    initEvents : function() {
        Ext.ux.SelectBox.superclass.initEvents.apply(this, arguments);
        // you need to use keypress to capture upper/lower case and shift+key, but it doesn't work in IE
        this.el.on('keydown', this.keySearch, this, true);
        this.cshTask = new Ext.util.DelayedTask(this.clearSearchHistory, this);
    },

    keySearch : function(e, target, options) {
        var raw = e.getKey();
        var key = String.fromCharCode(raw);
        var startIndex = 0;

        if(!this.store.getCount()) {
            return;
        }

        switch(raw) {
            case Ext.EventObject.HOME:
                e.stopEvent();
                this.selectFirst();
                return;

            case Ext.EventObject.END:
                e.stopEvent();
                this.selectLast();
                return;

            case Ext.EventObject.PAGEDOWN:
                this.selectNextPage();
                e.stopEvent();
                return;

            case Ext.EventObject.PAGEUP:
                this.selectPrevPage();
                e.stopEvent();
                return;
        }

        // skip special keys other than the shift key
        if((e.hasModifier() && !e.shiftKey) || e.isNavKeyPress() || e.isSpecialKey()) {
            return;
        }
        if(this.lastSearchTerm == key) {
            startIndex = this.lastSelectedIndex;
        }
        this.search(this.displayField, key, startIndex);
        this.cshTask.delay(this.searchResetDelay);
    },

    onRender : function(ct, position) {
        this.store.on('load', this.calcRowsPerPage, this);
        Ext.ux.SelectBox.superclass.onRender.apply(this, arguments);
        if(this.mode == 'local') {
            this.calcRowsPerPage();
        }
    },

    onSelect : function(record, index, skipCollapse) {
        if(this.fireEvent('beforeselect', this, record, index) !== false) {
            this.setValue(record.data[this.valueField || this.displayField]);
            if(!skipCollapse) {
                this.collapse();
            }
            this.lastSelectedIndex = index + 1;
            this.fireEvent('select', this, record, index);
        }
    },

    render : function(ct) {
        Ext.ux.SelectBox.superclass.render.apply(this, arguments);
        if(Ext.isSafari) {
            this.el.swallowEvent('mousedown', true);
        }
        this.el.unselectable();
        this.innerList.unselectable();
        this.trigger.unselectable();
        this.innerList.on('mouseup', function(e, target, options) {
            if(target.id && target.id == this.innerList.id) {
                return;
            }
            this.onViewClick();
        }, this);

        this.innerList.on('mouseover', function(e, target, options) {
            if(target.id && target.id == this.innerList.id) {
                return;
            }
            this.lastSelectedIndex = this.view.getSelectedIndexes()[0] + 1;
            this.cshTask.delay(this.searchResetDelay);
        }, this);

        this.trigger.un('click', this.onTriggerClick, this);
        this.trigger.on('mousedown', function(e, target, options) {
            e.preventDefault();
            this.onTriggerClick();
        }, this);

        this.on('collapse', function(e, target, options) {
            Ext.getDoc().un('mouseup', this.collapseIf, this);
        }, this, true);

        this.on('expand', function(e, target, options) {
            Ext.getDoc().on('mouseup', this.collapseIf, this);
        }, this, true);
    },

    clearSearchHistory : function() {
        this.lastSelectedIndex = 0;
        this.lastSearchTerm = false;
    },

    selectFirst : function() {
        this.focusAndSelect(this.store.data.first());
    },

    selectLast : function() {
        this.focusAndSelect(this.store.data.last());
    },

    selectPrevPage : function() {
        if(!this.rowHeight) {
            return;
        }
        var index = Math.max(this.selectedIndex - this.rowsPerPage, 0);
        this.focusAndSelect(this.store.getAt(index));
    },

    selectNextPage : function() {
        if(!this.rowHeight) {
            return;
        }
        var index = Math.min(this.selectedIndex + this.rowsPerPage, this.store.getCount() - 1);
        this.focusAndSelect(this.store.getAt(index));
    },

    search : function(field, value, startIndex) {
        field = field || this.displayField;
        this.lastSearchTerm = value;
        var index = this.store.find.apply(this.store, arguments);
        if(index !== -1) {
            this.focusAndSelect(index);
        }
    },

    focusAndSelect : function(record) {
        var index = typeof record === 'number' ? record : this.store.indexOf(record);
        this.select(index, this.isExpanded());
        this.onSelect(this.store.getAt(record), index, this.isExpanded());
    },

    calcRowsPerPage : function() {
        if(this.store.getCount()) {
            this.rowHeight = Ext.fly(this.view.getNode(0)).getHeight();
            this.rowsPerPage = this.maxHeight / this.rowHeight;
        } else {
            this.rowHeight = false;
        }
    }

});
/*
 Ext.Ajax.on('requestcomplete', function(ajax, xhr, o){
 if(typeof urchinTracker == 'function' && o && o.url){
 urchinTracker(o.url);
 }
 });
 */


           /* var Main_Admin_Panel = new Ext.Panel({
                title: "Admin Panel",
                region: 'center',
                id: 'Main-Admin-Panel',
                layout: 'fit',
                margins:'10 5 5 0',
                frame: true,
                border: true
            });

            var Navigation_Tree = new Ext.tree.TreePanel({
                useArrows:true,
                autoScroll:true,
                animate:true,
                enableDD:true,
                containerScroll: true,
                rootVisible: false,
                root: {
                    nodeType: 'async'
                },
                dataUrl: 'admin/index/get-tree',
                listeners: {
                    'click': function(node){
                        if(node.attributes.url)
                        {
                            Main_Admin_Panel.removeAll(true);
                            Main_Admin_Panel.load({
                                url: node.attributes.url,
                                discardUrl: false,
                                nocache: false,
                                text: 'Loading...',
                                timeout: 30,
                                scripts: true
                            });
                        }
                    }
                }
            });

            Navigation_Tree.getRootNode().expand(true);


            var viewport = new Ext.Viewport({
                layout:'border',
                items:[{
                    region:'west',
                    id:'west-panel',
                    title:'Navigation',
                    split:true,
                    width: 200,
                    minSize: 175,
                    maxSize: 400,
                    collapsible: true,
                    margins:'10 0 5 5',
                    cmargins:'10 5 5 5',
                    layout:'fit',
                    layoutConfig:{
                        animate:true
                    },
                    items: [{
                        autoScroll:true,
                        border:false,
                        iconCls:'nav',
                        layout:'fit',
                        items: [Navigation_Tree]
                    }]
                },Main_Admin_Panel]
            });*/