Ext.require([
    'Ext.data.*',
    'Ext.grid.*'
]);

Ext.onReady(function(){
    Ext.define('PriceList',{
        extend: 'Ext.data.Model',
        fields: [
            // set up the fields mapping into the xml doc
            // The first needs mapping, the others are very basic
            'alloy_name', 'grade', 'prod_name', 'note', 'diameter', 'length',
            'width', 'thickness', 'other_dim', 'quantity', 'mass', 'price', 'order'
        ]
    });

    // create the Data Store
    var store = Ext.create('Ext.data.Store', {
        model: 'PriceList',
        autoLoad: true,
        proxy: {
            // load using HTTP
            type: 'ajax',
            url: 'test_xml_gen.php',
            //url: 'price_list_items.xml',
            //url: 'test.xml',
            // the return will be XML, so lets set up a reader
            reader: {
                type: 'xml',
                root: 'items',
                record: 'item'
            }
        }
    });
    
    var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToMoveEditor: 1,
        autoCancel: false
    });

    // create the grid
    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        columns: [
            {text: "Материал", width: 110, dataIndex: 'alloy_name', sortable: true, field: {xtype: 'textfield'}},
            {text: "Марка", width: 180, dataIndex: 'grade', sortable: true, field: {xtype: 'textfield'}},
            {text: "Тип проката", width: 115, dataIndex: 'prod_name', sortable: true},
            {text: "Примечание", width: 100, dataIndex: 'note', sortable: true},
            {text: "Диаметр", width: 100, dataIndex: 'diameter', sortable: true},
            {text: "Длина", width: 100, dataIndex: 'length', sortable: true},
            {text: "Ширина", width: 100, dataIndex: 'width', sortable: true},
            {text: "Толщина", width: 100, dataIndex: 'thickness', sortable: true},
            {text: "Другой размер", width: 100, dataIndex: 'other_dim', sortable: true},
            {text: "Количество", width: 100, dataIndex: 'quantity', sortable: true},
            {text: "Масса", width: 100, dataIndex: 'mass', sortable: true},
            {text: "Цена", width: 100, dataIndex: 'price', sortable: true}
        ],
        selType: 'rowmodel',
        renderTo: 'table_editor',
        width: 1000,
        height: 400,
        selType: 'rowmodel',
        plugins: [rowEditing],
        listeners: {
            'selectionchange': function(view, records) {
                grid.down('#removeEmployee').setDisabled(!records.length);
            }
        }
    });
});