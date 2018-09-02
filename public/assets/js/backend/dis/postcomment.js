define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'dis/postcomment/index',
                    add_url: 'dis/postcomment/add',
                    edit_url: 'dis/postcomment/edit',
                    del_url: 'dis/postcomment/del',
                    multi_url: 'dis/postcomment/multi',
                    table: 'dis_post_comment',
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
                        {field: 'pid', title: __('Pid')},
                        // {field: 'puid', title: __('Puid')},
                        // {field: 'pbid', title: __('Pbid')},
                        // {field: 'bid', title: __('Bid')},
                        // {field: 'buid', title: __('Buid')},
                        {field: 'username', title: __('Uid')},
                        {field: 'content', title: __('Content')},
                        {field: 'pics', title: __('Pics'),formatter:Table.api.formatter.picture},
                        // {field: 'likecount', title: __('Likecount')},
                        {field: 'pcounts', title: __('Pcounts')},
                        {field: 'status_text', title: __('Status')},
                        {field: 'ip', title: __('Ip')},
                        {field: 'locate', title: __('Locate')},
                        {field: 'ctime', title: __('Ctime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'utime', title: __('Utime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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