define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'custom/practice/index',
                    // add_url: 'custom/practice/add',
                    edit_url: 'custom/practice/edit',
                    del_url: 'custom/practice/del',
                    multi_url: 'custom/practice/multi',
                    table: 'practice',
                }
            });

            var table = $("#table");
            table.on('post-common-search.bs.table', function (event, table) {
                var form = $("form", table.$commonsearch);
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
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'teacher_id', title: __('Teacher_id')},
                        {field: 'teacher_name', title: __('Teacher_id'),operate:false},
                        {field: 'title', title: __('Title')},
                        // {field: 'type', title: __('Type'), visible:false, searchList: {"1":__('Type 1')}},
                        // {field: 'type_text', title: __('Type'), operate:false},
                        {field: 'summary', title: __('Summary'),operate:false},
                        {field: 'cover', title: __('Cover'),formatter:Table.api.formatter.images,operate:false},
                        // {field: 'audio', title: __('Audio'),formatter:Table.api.formatter.url},
                        {field: 'video', title: __('Video'),formatter:Table.api.formatter.url,operate:false},
                        {field: 'status', title: __('Status'), visible:false, searchList: {0:'已结束',1:'未开始',2:'正在进行'}},
                        {field: 'status_text', title: __('Status'), operate:false},
                        {field: 'remark', title: __('Remark'),operate:false},
                        {field: 'begin_time', title: __('Begin_time'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'end_time', title: __('End_time'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'read_count', title: __('Read_count'),operate:false},
                        {field: 'pcounts', title: __('Pcounts'),operate:false},
                        {field: 'creator', title: __('Creator'),operate:false},
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