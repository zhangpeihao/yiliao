define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'student/index',
                    add_url: 'student/add',
                    edit_url: 'student/edit',
                    del_url: 'student/forbid',
                    import_url:'student/import',
                    multi_url: 'student/multi',
                    table: 'student',
                }
            });

            var table = $("#table");
            table.on('post-common-search.bs.table', function (event, table) {
                var form = $("form", table.$commonsearch);
                $("input[name='agency_id']", form).addClass("selectpage").data("source", "agency/agency/index").data("primaryKey", "id").data("field", "name");
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
                        {checkbox:true},
                        {field: 'id', title: __('Id'), operate:false},
                        {field: 'agency_id', title: '所属机构',visible:false},
                        {field: 'agency_text', title: '所属机构', operate:false},
                        {field: 'username', title: __('Username')},
                        {field: 'mobile', title: __('Mobile')},
                        {field: 'rest_lesson', title:'剩余课时数',sortable:true},
                        {field: 'gender', title: __('Gender'), visible:false, searchList: {"1":'男',"2":'女',0:'未知'}},
                        {field: 'gender_text', title: __('Gender'), operate:false},
                        {field: 'birthday', title: __('Birthday'), operate:false, type: 'datetime', addclass:'datetimepicker',data: 'data-date-format="YYYY-MM-DD"'},
                        {field: 'learn_status', title: __('Learn_status'), visible:false, searchList: {'1':'在读','2':'试听','3':'过期'}},
                        {field: 'learn_status_text', title: __('Learn_status'), operate:false,formatter:Controller.api.formatter.learn_status},
                        {field: 'status', title: __('Status'), visible:false, searchList: {'0':'禁用','1':'未签约','2':'未排课'}},
                        {field: 'status_text', title: __('Status'), operate:false,formatter:Controller.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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
                    //value:'0':'禁用','1':'未签约','2':'未排课'
                    var color;
                    switch(value){
                        case '禁用':color=colorArr.deleted;break;
                        case '未签约':color=colorArr.locked;break;
                        case '未排课':color=colorArr.normal;break;
                    }
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + __(value) + '</span>';
                    return html;
                },
                'learn_status':function (value, row, index) {
                    var colorArr = {normal: 'success', hidden: 'grey', deleted: 'danger', locked: 'info'};
                    //value:'1':'在读','2':'试听','3':'过期'
                    var color;
                    switch(value){
                        case '在读':color=colorArr.normal;break;
                        case '试听':color=colorArr.locked;break;
                        case '过期':color=colorArr.hidden;break;
                    }
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + __(value) + '</span>';
                    return html;
                }
            }
        }
    };
    return Controller;
});