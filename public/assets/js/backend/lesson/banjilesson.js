define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'lesson/banjilesson/index',
                    add_url: 'lesson/banjilesson/add',
                    edit_url: 'lesson/banjilesson/edit',
                    del_url: 'lesson/banjilesson/forbid',
                    multi_url: 'lesson/banjilesson/multi',
                    table: 'banji_lesson',
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
                        // {field: 'banji_text', title: __('Banji_id')},
                        {field: 'lesson_text', title: __('Lesson_id')},
                        {field: 'teacher_text', title: __('Teacher_id')},
                        {field: 'class_room', title: __('Class_room')},
                        {field: 'startdate', title: __('Startdate'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'enddate', title: "结束日期", operate:'RANGE', addclass:'datetimerange'},
                        {field: 'begin_time', title: __('Begin_time')},
                        {field: 'minute', title: __('Minute')},
                        {field: 'end_time', title: __('End_time')},
                        {field: 'dec_num', title: __('Dec_num')},
                        {field: 'frequency', title: __('Frequency'), visible:false, searchList: {"1":__('Frequency 1')}},
                        {field: 'frequency_text', title: __('Frequency'), operate:false},
                        {field: 'frequency_week', title: __('Frequency_week')},
                        {field: 'lesson_count', title: __('Lesson_count')},
                        {field: 'remark', title: __('Remark')},
                        {field: 'status', title: __('Status'), visible:false, searchList: {0:'已删除',1:'未结课',2:'已结课'}},
                        {field: 'status_text', title: __('Status'), operate:false,formatter:Controller.api.formatter.status},
                        {field: 'creator', title: __('Creator')},
                        // {field: 'updator', title: __('Updator')},
                        // {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons:function (value,row,index) {
                                var add_btn=[];
                                add_btn.push(
                                    // {name: 'detail', text: '查看排课记录', title: '查看学员', icon: 'fa fa-list', classname: 'btn btn-xs btn-primary btn-dialog', url: 'lesson/banjistudent/index/banji_lesson_id/'+row['banji_lesson_id'],extend:'target="_blank"'}
                                    {name: 'detail', text: '', title: '查看学员', icon: 'fa fa-list', classname: 'btn btn-xs btn-primary btn-dialog', url: 'lesson/banjistudent/index/banji_lesson_id/'+row['id']}
                                );
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
                    //0:'已删除',1:'未接课',2:'已结课'
                    var colorArr = {normal: 'success', hidden: 'grey', deleted: 'danger', locked: 'info'};
                    var color;
                    switch(value){
                        case '已删除':color=colorArr.deleted;break;
                        case '未结课':color=colorArr.locked;break;
                        case '已结课':color=colorArr.normal;break;
                    }
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + __(value) + '</span>';
                    return html;
                }
            }
        }
    };
    return Controller;
});