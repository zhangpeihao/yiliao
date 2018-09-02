define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/userlevel/index',
                    add_url: 'user/userlevel/add',
                    edit_url: 'user/userlevel/edit',
                    del_url: 'user/userlevel/del',
                    multi_url: 'user/userlevel/multi',
                    table: 'user_level',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'level', title: __('Level')},
                        {field: 'levelname', title: __('Levelname')},
                        // {field: 'ordermoney', title: __('Ordermoney'), operate:'BETWEEN'},
                        // {field: 'ordercount', title: __('Ordercount')},
                        // {field: 'score', title: __('Score')},
                        {field: 'discount', title: __('Discount'), operate:'BETWEEN'},
                        {field: 'creator_text', title: __('Creator'),operate:false},
                        {field: 'ctime', title: __('Ctime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'utime', title: __('Utime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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