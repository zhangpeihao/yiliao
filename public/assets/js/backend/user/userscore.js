define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/userscore/index',
                    add_url: 'user/userscore/add',
                    edit_url: 'user/userscore/edit',
                    del_url: 'user/userscore/del',
                    multi_url: 'user/userscore/multi',
                    table: 'user_score',
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
                        // {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'username', title: __('Uid'),operate:false},
                        {field: 'operate', title: __('Operate'), visible:false, searchList: {'1' : '加','-1':'减'}},
                        {field: 'operate_text', title: __('Operate'), operate:false},
                        {field: 'type', title: __('Type'), visible:false, searchList: {1:'发布练习视频',2:'商城消费兑换',3:'商城消费',4:'拉新赠送',5:'新用户注册'}},
                        {field: 'type_text', title: __('Type'), operate:false},
                        {field: 'creator', title: __('Creator'),operate:false},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'last_money', title: __('Last_money'), operate:'BETWEEN'},
                        {field: 'num', title: __('Num'), operate:'BETWEEN'},
                        {field: 'last_num', title: __('Last_num'), operate:'BETWEEN'},
                        {field: 'link_order', title: __('Link_order'),operate:false},
                        {field: 'link_id', title: __('Link_id'),operate:false},
                        {field: 'remark', title: __('Remark'),operate:false},
                        {field: 'ctime', title: __('Ctime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'utime', title: __('Utime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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