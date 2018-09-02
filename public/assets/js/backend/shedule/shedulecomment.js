define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'shedule/shedulecomment/index',
                    add_url: 'shedule/shedulecomment/add',
                    edit_url: 'shedule/shedulecomment/edit',
                    del_url: 'shedule/shedulecomment/del',
                    multi_url: 'shedule/shedulecomment/multi',
                    table: 'shedule_comment',
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
                        // {checkbox: true},
                        // {field: 'id', title: __('Id')},
                        // {field: 'banji_id', title: __('Banji_id')},
                        // {field: 'banji_lesson_id', title: __('Banji_lesson_id')},
                        {field: 'shedule_id', title: __('Shedule_id')},
                        // {field: 'student_id', title: __('Student_id')},
                        {field: 'student_name', title: __('Student_id')},
                        // {field: 'lesson_id', title: __('Lesson_id')},
                        {field: 'lesson_text', title: __('Lesson_id')},
                        {field: 'date', title: __('Date'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'week', title: __('Week'), visible:false, searchList: {0:'星期日',1:'星期一',2:'星期二',3:'星期三',4:'星期四',5:'星期五',6:'星期六'}},
                        {field: 'week_text', title: __('Week'), operate:false,formatter:Table.api.formatter.label},
                        {field: 'begin_time', title: __('Begin_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.text},
                        {field: 'end_time', title: __('End_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.text},
                        {field: 'content', title: __('Content')},
                        // {field: 'pics_view', title: __('Pics')},
                        // {field: 'video_view', title: __('Video')},
                        {field: 'status', title: __('Status'), visible:false, searchList: {'1':'正常',0:'禁用'}},
                        {field: 'status_text', title: __('Status'), operate:false,formatter:Controller.api.formatter.status},
                        {field: 'creator', title: __('Creator')},
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
                'status':function (value,row,index) {
                    //'1':'正常',0:'禁用'
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
        },
    };
    return Controller;
});