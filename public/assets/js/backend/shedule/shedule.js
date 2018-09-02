define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'shedule/shedule/index',
                    add_url: 'shedule/shedule/add',
                    edit_url: 'shedule/shedule/edit',
                    del_url: 'shedule/shedule/del',
                    multi_url: 'shedule/shedule/multi',
                    table: 'shedule',
                }
            });

            var table = $("#table");
            table.on('post-common-search.bs.table', function (event, table) {
                var form = $("form", table.$commonsearch);
                $("input[name='agency_id']", form).addClass("selectpage").data("source", "agency/agency/index").data("primaryKey", "id").data("field", "name");
                $("input[name='teacher_id']", form).addClass("selectpage").data("source", "lesson/teacher/index").data("primaryKey", "id").data("field", "username");
                $("input[name='lesson_id']", form).addClass("selectpage").data("source", "lesson/lesson/index").data("primaryKey", "id").data("field", "name");
                $("input[name='banji_id']", form).addClass("selectpage").data("source", "lesson/banji/index").data("primaryKey", "id").data("field", "name");
                $("input[name='class_room']", form).addClass("selectpage").data("source", "lesson/class_room/index").data("primaryKey", "id").data("field", "name");
                if ($(".selectpage", form).size() > 0) {
                    Form.events.selectpage(form);
                }
            });
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
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'agency_id', title: '机构',visible:false},
                        {field: 'student_name', title: '学员',operate:false},
                        {field: 'teacher_id', title:__('Teacher_id'),visible:false},
                        {field: 'teacher_text', title: __('Teacher_id'),operate:false},
                        // {field: 'banji_lesson_id', title: __('Banji_lesson_id')},
                        {field: 'lesson_id', title: __('Lesson_id'),visible:false},
                        {field: 'lesson_text', title: __('Lesson_id'),operate:false},
                        {field: 'date', title: __('Date'), operate:'RANGE',sortable:true, addclass:'datetimerange'},
                        {field: 'week', title: __('Week'), visible:false, searchList: {0:'星期日',1:'星期一',2:'星期二',3:'星期三',4:'星期四',5:'星期五',6:'星期六'}},
                        {field: 'week_text', title: __('Week'), operate:false,formatter:Table.api.formatter.label},
                        // {field: 'banji_id', title: __('Banji_id'),visible:false},
                        // {field: 'banji_text', title: __('Banji_id'),operate:false},
                        {field: 'begin_time', title: __('Begin_time'), sortable:true,operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.text},
                        {field: 'end_time', title: __('End_time'), sortable:true,operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.text},
                        {field: 'dec_num', title: __('Dec_num'), sortable:true,operate:false},
                        {field: 'class_room', title: __('Class_room'),operate:false},
                        {field: 'status', title: __('Status'), visible:false, searchList: {0:'禁用',1:'未结课',2:'已结课',3:'已调课'},formatter:Table.api.formatter.status},
                        {field: 'status_text', title: __('Status'), operate:false,formatter:Controller.api.formatter.status},
                        {field: 'dispatch_id', title: __('Dispatch_id'),operate:false},
                        {field: 'creator', title: __('Creator')},
                        // {field: 'updator', title: __('Updator')},
                        {field: 'remark', title: __('Remark')},
                        // {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            /*buttons: [
                                {name: 'detail', text: '查看学员', title: '查看学员', icon: 'fa fa-list', classname: 'btn btn-xs btn-primary btn-dialog', url: 'mall/mallattr/index'}
                            ]*/
                            buttons:function (value,row,index) {
                                var add_btn=[];
                                // add_btn.push(
                                //     {name: 'detail', text: '查看学员', title: '查看学员', icon: 'fa fa-list', classname: 'btn btn-xs btn-primary btn-dialog', url: 'lesson/banjistudent/index/banji_lesson_id/'+row['banji_lesson_id']}
                                // );
                                return add_btn;
                            }
                        }
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
                    //0:'禁用',1:'未结课',2:'已结课',3:'已调课'
                    var colorArr = {normal: 'success', hidden: 'grey', deleted: 'danger', locked: 'info'};
                    var color;
                    switch(value){
                        case '禁用':color=colorArr.locked;break;
                        case '未结课':color=colorArr.deleted;break;
                        case '已结课':color=colorArr.normal;break;
                        case '已调课':color=colorArr.hidden;break;
                    }
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + __(value) + '</span>';
                    return html;
                }
            }
        }
    };
    return Controller;
});