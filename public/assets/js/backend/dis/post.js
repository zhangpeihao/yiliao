define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'dis/post/index',
                    add_url: 'dis/post/add',
                    edit_url: 'dis/post/edit',
                    del_url: 'dis/post/del',
                    multi_url: 'dis/post/multi',
                    table: 'dis_post',
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
                        // {field: 'tid', title: __('Tid')},
                        {field: 'username', title: __('Uid')},
                        // {field: 'type', title: __('Type'), visible:false, searchList: {"1":__('Type 1')}},
                        // {field: 'type_text', title: __('Type'), operate:false},
                        {field: 'title', title: __('Title')},
                        {field: 'pics', title: __('Pics'),formatter:Table.api.formatter.picture},
                        // {field: 'is_top', title: __('Is_top'), visible:false, searchList: {"1":__('Is_top 1')}},
                        // {field: 'is_top_text', title: __('Is_top'), operate:false},
                        {field: 'video', title: __('Video'),formatter:Table.api.formatter.url},
                        // {field: 'time', title: __('Time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'atuid', title: __('Atuid')},
                        {field: 'readcount', title: __('Readcount')},
                        {field: 'pcounts', title: __('Pcounts')},
                        // {field: 'likecount', title: __('Likecount')},
                        // {field: 'storecount', title: __('Storecount')},
                        // {field: 'repeat', title: __('Repeat')},
                        // {field: 'sourceid', title: __('Sourceid')},
                        {field: 'sort', title: __('Sort')},
                        // {field: 'cover', title: __('Cover')},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"1":__('Status 1')}},
                        {field: 'status_text', title: __('Status'), operate:false},
                        // {field: 'from', title: __('From')},
                        // {field: 'ctime', title: __('Ctime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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