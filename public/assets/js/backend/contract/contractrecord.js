define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'contract/contractrecord/index',
                    add_url: 'contract/contractrecord/add',
                    edit_url: 'contract/contractrecord/edit',
                    del_url: 'contract/contractrecord/del',
                    multi_url: 'contract/contractrecord/multi',
                    table: 'contract_record',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search: false, //是否启用快速搜索
                columns: [
                    [
                        {field: 'id', title: __('Id')},
                        {field: 'is_old', title: __('Is_old'), visible:false, searchList: {"1":"是",0:'否'}},
                        {field: 'is_old_text', title: __('Is_old'), operate:false},
                        {field: 'type', title: __('Type'), visible:false, searchList: {0:'默认',1:'新签',2:'赠送',3:'续费',4:'退款'}},
                        {field: 'type_text', title: __('Type'), operate:false},
                        {field: 'sno', title: __('Sno')},
                        {field: 'student_text', title: __('Student_id')},
                        {field: 'startdate', title: __('Startdate'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'enddate', title: __('Enddate'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'lesson_text', title: __('Lesson_id')},
                        {field: 'lesson_count', title: __('Lesson_count'), operate:'BETWEEN'},
                        {field: 'price', title: __('Price'), operate:'BETWEEN'},
                        {field: 'other_price', title: __('Other_price'), operate:'BETWEEN'},
                        {field: 'lesson_money', title: __('Lesson_money'), operate:'BETWEEN'},
                        {field: 'total_fee', title: __('Total_fee'), operate:'BETWEEN'},
                        {field: 'remark', title: __('Remark')},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"1":__('Status 1')}},
                        {field: 'status_text', title: __('Status'), operate:false,formatter:Controller.api.formatter.status},
                        {field: 'creator_text', title: __('Creator')},
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
                'type':function (value,row,index) {
                    //0:'默认',1:'新签',2:'赠送',3:'续费',4:'退款'
                    var colorArr = {normal: 'success', hidden: 'grey', deleted: 'danger', locked: 'info',default:'primary',refund:'warning'};
                    var color;
                    switch(value){
                        case '默认':color=colorArr.default;break;
                        case '新签':color=colorArr.locked;break;
                        case '赠送':color=colorArr.deleted;break;
                        case '续费':color=colorArr.normal;break;
                        case '退款':color=colorArr.hidden;break;
                    }
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + __(value) + '</span>';
                    return html;
                },
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