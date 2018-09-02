define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'custom/topicpost/index',
                    add_url: 'custom/topicpost/add',
                    edit_url: 'custom/topicpost/edit',
                    del_url: 'custom/topicpost/del',
                    multi_url: 'custom/topicpost/multi',
                    table: 'topic_post',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search: false, //是否启用快速搜索
                searchFormVisible: true, //是否始终显示搜索表单
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate:false},
                        // {field: 'tid', title: __('Tid')},
                        {field: 'uid', title: __('Uid'),visible:false},
                        {field: 'username', title: __('Uid')},
                        {field: 'type', title: __('Type'), visible:false, searchList: {0:'公开',1:'仅限老师'}},
                        {field: 'type_text', title: __('Type'), operate:false},
                        {field: 'title', title: __('Title')},
                        // {field: 'pics', title: __('Pics'),formatter:Table.api.formatter.images},
                        // {field: 'is_top', title: __('Is_top'), visible:false, searchList: {"1":'置顶',"0":'不置顶'}},
                        // {field: 'is_top_text', title: __('Is_top'), operate:false},
                        {field: 'cover', title: __('Cover'),formatter:Table.api.formatter.image, operate:false},
                        // {field: 'time', title: __('Time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'atuid', title: __('Atuid')},
                        {field: 'video', title: __('Video'),formatter:Table.api.formatter.url, operate:false},
                        {field: 'readcount', title: __('Readcount'), operate:false},
                        {field: 'pcounts', title: __('Pcounts'), operate:false},
                        // {field: 'storecount', title: __('Storecount')},
                        // {field: 'repeat', title: __('Repeat')},
                        // {field: 'sourceid', title: __('Sourceid')},
                        // {field: 'sort', title: __('Sort')},
                        {field: 'likecount', title: __('Likecount'), operate:false},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"1":'正常',0:'禁用'}},
                        {field: 'status_text', title: __('Status'), operate:false},
                        // {field: 'from', title: __('From')},
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