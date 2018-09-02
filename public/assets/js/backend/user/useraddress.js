define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/useraddress/index',
                    add_url: 'user/useraddress/add',
                    edit_url: 'user/useraddress/edit',
                    del_url: 'user/useraddress/del',
                    multi_url: 'user/useraddress/multi',
                    table: 'user_address',
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
                        {field: 'mobile', title: __('Mobile')},
                        {field: 'province', title: __('Province')},
                        {field: 'city', title: __('City')},
                        {field: 'district', title: __('District')},
                        {field: 'address', title: __('Address')},
                        {field: 'lat', title: __('Lat')},
                        {field: 'long', title: __('Long')},
                        {field: 'isdefault', title: __('Isdefault'), visible:false, searchList: {"1) unsigne":__('1) unsigne')}},
                        {field: 'isdefault_text', title: __('Isdefault'), operate:false},
                        {field: 'isdelete', title: __('Isdelete'), visible:false, searchList: {"1) unsigne":__('1) unsigne')}},
                        {field: 'isdelete_text', title: __('Isdelete'), operate:false},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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