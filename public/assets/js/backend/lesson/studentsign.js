define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'lesson/student_sign/index',
                    add_url: 'lesson/student_sign/add',
                    edit_url: 'lesson/student_sign/edit',
                    del_url: 'lesson/student_sign/del',
                    multi_url: 'lesson/student_sign/multi',
                    table: 'student_sign',
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
                        {field: 'lesson_text', title: __('Lesson')},
                        {field: 'class_room', title: __('Class_room')},
                        {field: 'dec_lesson', title: __('Dec_lesson'), visible:false, searchList: {'1':'扣课时','0':'不扣课时'}},
                        {field: 'dec_lesson_text', title: __('Dec_lesson'), operate:false},
                        {field: 'status', title: __('Status'), visible:false, searchList: {'0':'未确认','1':'已到达','2':'请假','3':'迟到','4':'早退','5':'旷课'}},
                        {field: 'status_text', title: __('Status'), operate:false,formatter:Controller.api.formatter.status},
                        {field: 'creator_text', title: __('Creator')},
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
                    var colorArr = {arrive: 'success', relay: 'grey', deleted: 'danger', vacation: 'info',early:'primary',unconifrm:'warning'};
                    //value:'0':'未确认','1':'已到达','2':'请假','3':'迟到','4':'早退','5':'旷课'
                    var color;
                    switch(value){
                        case '未确认':color=colorArr.unconifrm;break;
                        case '已到达':color=colorArr.arrive;break;
                        case '请假':color=colorArr.vacation;break;
                        case '迟到':color=colorArr.relay;break;
                        case '早退':color=colorArr.early;break;
                        case '旷课':color=colorArr.deleted;break;
                    }
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + __(value) + '</span>';
                    return html;
                }
            }
        }
    };
    return Controller;
});