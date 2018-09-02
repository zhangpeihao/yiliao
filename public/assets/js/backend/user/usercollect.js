define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/usercollect/index',
                    add_url: 'user/usercollect/add',
                    edit_url: 'user/usercollect/edit',
                    del_url: 'user/usercollect/del',
                    multi_url: 'user/usercollect/multi',
                    table: 'user_collect',
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
                        {field: 'id', title: __('Id')},
                        {field: 'uid', title: __('Uid')},
                        {field: 'username', title: __('Username')},
                        {field: 'mac', title: __('Mac')},
                        {field: 'system', title: __('System')},
                        {field: 'system_version', title: __('System_version')},
                        {field: 'mac_model', title: __('Mac_model')},
                        {field: 'ip', title: __('Ip')},
                        {field: 'date', title: __('Date'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'ctime', title: __('Ctime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'network', title: __('Network')},
                        {field: 'version', title: __('Version')},
                        {field: 'location', title: __('Location')},
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