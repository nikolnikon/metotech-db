Ext.require(['Ext.data.*', 'Ext.grid.*']);

Ext.define('Price', {
    extend: 'Ext.data.Model',
    fields: [{
        name: 'id',
        type: 'int',
        useNull: true
    }, 'alloy_name', 'grade', 'prod_name', 'note', 'diameter', 'length', 'width', 'thickness', 'other_dim', 'quantity', 'mass', 'price']
});

Ext.onReady(function(){

    var store = Ext.create('Ext.data.Store', {
        autoLoad: true,
        autoSync: false,
        model: 'Price',
        proxy: {
            type: 'ajax',
            api: {
                read: 'test_xml_gen.php',
                create: 'test_editor.php',
                update: 'test_editor.php',
                destroy: 'test_editor.php'
            },
            reader: {
                type: 'xml',
                root: 'data',
                record: 'item',
                idProperty: 'id',
                successProperty: 'success',
                messageProperty: 'message'
            },
            writer: { // посылает на сервер POST-запрос, содержащий xml
            	type: 'xml',
            	header: '<?xml version=\'1.0\' encoding=\'utf-8\'?>',
            	documentRoot: 'request',
            	record: 'item',
            	writeAllFields: false
            },
            listeners: {
                exception: function(proxy, response, operation){
                    Ext.MessageBox.show({
                        title: 'REMOTE EXCEPTION',
                        msg: operation.getError(),
                        icon: Ext.MessageBox.ERROR,
                        buttons: Ext.Msg.OK
                    });
                }
            }
        },
        listeners: {
            write: function(store, operation){
                var record = operation.getRecords()[0],
                    name = Ext.String.capitalize(operation.action),
                    verb;
                    
                    
                if (name == 'Destroy') {
                    record = operation.records[0];
                    verb = 'Destroyed';
                } else {
                    verb = name + 'd';
                }
                Ext.example.msg(name, Ext.String.format("{0} user: {1}", verb, record.getId())); // возможно, убрать example
            }
        }
    });
    
    var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
    		clicksToMoveEditor: 1,
            autoCancel: false,
            listeners: {
            	'beforeedit': function(editor, e) {
            		//var r = editor.record.fields['alloy_name'];
            		//r.setDisabled(false);
            		/*for (i = 0; i<=r.length; i++) {
            			r[i].setDisabled(true);
            		}*/
            		
            	},
                'edit': function(editor, e) {
                	editor.store.sync();
                    editor.record.commit();
                }
            }
});
    
    var grid = Ext.create('Ext.grid.Panel', {
        renderTo: 'test_editor',
        plugins: [rowEditing],
        width: 1400,
        height: 400,
        frame: true,
        title: 'Прайс-лист',
        store: store,
        columns: [{
            text: 'ID',
            width: 40,
            sortable: true,
            dataIndex: 'id'
        }, {text: "Материал", width: 110, dataIndex: 'alloy_name', sortable: true, field: {xtype: 'textfield'}},
        {text: "Марка", width: 180, dataIndex: 'grade', sortable: true, field: {xtype: 'textfield'}},
        {text: "Тип проката", width: 115, dataIndex: 'prod_name', sortable: true, field: {xtype: 'textfield'}},
        {text: "Примечание", width: 100, dataIndex: 'note', sortable: true, field: {xtype: 'textfield'}},
        {text: "Диаметр", width: 100, dataIndex: 'diameter', sortable: true, field: {xtype: 'textfield'}},
        {text: "Длина", width: 100, dataIndex: 'length', sortable: true, field: {xtype: 'textfield'}},
        {text: "Ширина", width: 100, dataIndex: 'width', sortable: true, field: {xtype: 'textfield'}},
        {text: "Толщина", width: 100, dataIndex: 'thickness', sortable: true, field: {xtype: 'textfield'}},
        {text: "Другой размер", width: 100, dataIndex: 'other_dim', sortable: true, field: {xtype: 'textfield'}},
        {text: "Количество", width: 100, dataIndex: 'quantity', sortable: true, editor: {xtype: 'numberfield', allowBlank: true, minValue: 1, maxValue: 150000}},
        {text: "Масса", width: 100, dataIndex: 'mass', sortable: true, field: {xtype: 'textfield'}},
        {text: "Цена", width: 100, dataIndex: 'price', sortable: true, field: {xtype: 'textfield'}}
        ],
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text: 'Добавить запись',
                iconCls: 'icon-add',
                handler: function(){
                    // empty record
                    store.insert(0, new Price());
                    rowEditing.startEdit(0, 0);
                }
            }, '-', {
                itemId: 'delete',
                text: 'Удалить запись',
                iconCls: 'icon-delete',
                disabled: true,
                handler: function(){
                    var selection = grid.getView().getSelectionModel().getSelection()[0];
                    if (selection) {
                        store.remove(selection);
                    }
                }
            }]
        }]
    });
    grid.getSelectionModel().on('selectionchange', function(selModel, selections){
        grid.down('#delete').setDisabled(selections.length === 0);
    });
});

