define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'mall/mallattr/index?ids='+ids,
                    add_url: 'mall/mallattr/add?ids='+ids,
                    edit_url: 'mall/mallattr/edit',
                    del_url: 'mall/mallattr/del',
                    multi_url: 'mall/mallattr/multi',
                    table: 'mall_product_attr',
                }
            });

            var table = $("#table");
            table.on('post-common-search.bs.table', function (event, table) {
                var form = $("form", table.$commonsearch);
                $("input[name='pid']", form).addClass("selectpage").data("source", "mall/product/index").data("primaryKey", "id").data("field", "title");
                if ($(".selectpage", form).size() > 0) {
                    Form.events.selectpage(form);
                }
            });
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'pid', title: __('Pid'),visible:false},
                        {field: 'product_title', title: __('Pid'),operate:false},
                        {field: 'attr_name', title: __('Attr_name')},
                        // {field: 'input_type', title: __('Input_type')},
                        {field: 'attr_value', title: __('Attr_value')},
                        {field: 'remark', title: __('Remark')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});