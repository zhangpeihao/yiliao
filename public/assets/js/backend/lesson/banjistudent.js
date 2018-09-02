define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'lesson/banjistudent/index/banji_lesson_id/'+banji_lesson_id+'/shedule_id/'+shedule_id,
                    add_url: 'lesson/banjistudent/add',
                    edit_url: 'lesson/banjistudent/edit',
                    del_url: 'lesson/banjistudent/forbid',
                    multi_url: 'lesson/banjistudent/multi',
                    table: 'banji_student',
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
                        // {field: 'id', title: __('Id')},
                        // {field: 'banji_text', title: __('Banji_id')},
                        {field: 'username', title: __('Student_id')},
                        // {field: 'banji_lesson.lesson', title: __('Banji_lesson_id')},
                        // {field: 'shedule_id', title: __('Shedule_id')},
                        {field: 'status', title: __('Status'), visible:false, searchList: {'1':'正常',0:'禁用'}},
                        {field: 'status_text', title: __('Status'), operate:false,formatter:Controller.api.formatter.status},
                        // {field: 'remark', title: __('Remark')},
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
            },
            formatter:{
                'status':function (value, row, index) {
                    var colorArr = {normal: 'success', hidden: 'grey', deleted: 'danger', locked: 'info'};
                    //value:"1":'正常',"0":'禁用'
                    var color;
                    switch(value){
                        case '正常':color=colorArr.normal;break;
                        case '禁用':color=colorArr.deleted;break;
                    }
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + __(value) + '</span>';
                    return html;
                }
            }
        }
    };
    return Controller;
});