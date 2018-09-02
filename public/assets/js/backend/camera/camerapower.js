define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'camera/camerapower/index',
                    add_url: 'camera/camerapower/add',
                    edit_url: 'camera/camerapower/edit',
                    del_url: 'camera/camerapower/del',
                    multi_url: 'camera/camerapower/multi',
                    table: 'camera_power',
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
                        {field: 'deviceSerial', title: __('Deviceserial')},
                        // {field: 'power_type', title: __('Power_type'), visible:false, searchList: {"1":__('Power_type 1')}},
                        {field: 'power_type_text', title: __('Power_type'), operate:false},
                        {field: 'username', title: __('Uid')},
                        {field: 'agency_id', title: __('Agency_id')},
                        {field: 'creator_name', title: __('Creator')},
                        // {field: 'updator', title: __('Updator')},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"1":__('Status 1')}},
                        {field: 'status_text', title: __('Status'), operate:false},
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