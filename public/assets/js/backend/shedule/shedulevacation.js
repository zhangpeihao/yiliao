define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'shedule/shedulevacation/index',
                    add_url: 'shedule/shedulevacation/add',
                    edit_url: 'shedule/shedulevacation/edit',
                    del_url: 'shedule/shedulevacation/del',
                    multi_url: 'shedule/shedulevacation/multi',
                    table: 'shedule_vacation',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                exportTypes: ['csv','excel'],
                search: false, //是否启用快速搜索
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'student_text', title: __('Student_id')},
                        {field: 'date', title: __('Date'), operate:'RANGE', addclass:'datetimerange'},
                        // {field: 'banji_lesson_id', title: __('Banji_lesson_id')},
                        {field: 'lesson_text', title: __('Lesson_id')},
                        {field: 'teacher_text', title: __('Teacher_id')},
                        // {field: 'shedule_id', title: __('Shedule_id')},
                        {field: 'reason', title: __('Reason')},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"1":"正常","0":"禁用"}},
                        {field: 'status_text', title: __('Status'), operate:false,formatter:Controller.api.formatter.status},
                        {field: 'from', title: __('From'), visible:false, searchList: {"1":'家长端',2:'教师端'}},
                        {field: 'from_text', title: __('From'), operate:false},
                        {field: 'creator', title: __('Creator')},
                        // {field: 'updator', title: __('Updator')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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
            },
            formatter:{
                'status':function (value,row,index) {
                    //"1":'正常',"2":'禁用'
                    var colorArr = {normal: 'success', hidden: 'grey', deleted: 'danger', locked: 'info'};
                    var color;
                    switch(value){
                        case '正常':color=colorArr.normal;break;
                        case '禁用':color=colorArr.locked;break;
                    }
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + __(value) + '</span>';
                    return html;
                }
            }
        }
    };
    return Controller;
});