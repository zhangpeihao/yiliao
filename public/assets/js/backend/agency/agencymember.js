define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'agency/agencymember/index',
                    add_url: 'agency/agencymember/add',
                    edit_url: 'agency/agencymember/edit',
                    del_url: 'agency/agencymember/del',
                    multi_url: 'agency/agencymember/multi',
                    table: 'agency_member',
                }
            });

            var table = $("#table");
            table.on('post-common-search.bs.table', function (event, table) {
                var form = $("form", table.$commonsearch);
                $("input[name='agency_id']", form).addClass("selectpage").data("source", "agency/agency/index").data("primaryKey", "id").data("field", "name");
                $("input[name='uid']", form).addClass("selectpage").data("source", "user/user/index").data("primaryKey", "id").data("field", "username");
                $("input[name='teacher_id']", form).addClass("selectpage").data("source", "lesson/teacher/index").data("primaryKey", "id").data("field", "username");
                if ($(".selectpage", form).size() > 0) {
                    Form.events.selectpage(form);
                }
            });
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
                        {field: 'type', title: __('Type'), visible:false, searchList: {1:'机构所有者',2:'教务',3:'老师'}},
                        {field: 'type_text', title: __('Type'), operate:false},
                        {field: 'agency_id', title: __('Agency_id'),visible:false},
                        {field: 'agency_name', title: __('Agency_id'), operate:false},
                        {field: 'uid', title: __('Uid'),visible:false},
                        {field: 'username', title: __('Uid'), operate:false},
                        {field: 'teacher_id', title: __('Teacher_id'),visible:false},
                        {field: 'teacher_name', title: __('Teacher_id'), operate:false},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"1":'启用',0:'禁用'}},
                        {field: 'status_text', title: __('Status'), operate:false},
                        // {field: 'creator', title: __('Creator')},
                        // {field: 'updator', title: __('Updator')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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