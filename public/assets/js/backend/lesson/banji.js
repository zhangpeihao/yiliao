define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'lesson/banji/index',
                    add_url: 'lesson/banji/add',
                    edit_url: 'lesson/banji/edit',
                    del_url: 'lesson/banji/forbid',
                    multi_url: 'lesson/banji/multi',
                    table: 'banji',
                }
            });

            var table = $("#table");
            table.on('post-common-search.bs.table', function (event, table) {
                var form = $("form", table.$commonsearch);
                $("input[name='agency_id']", form).addClass("selectpage").data("source", "agency/agency/index").data("primaryKey", "id").data("field", "name");
                $("input[name='lesson_id']", form).addClass("selectpage").data("source", "lesson/lesson/index").data("primaryKey", "id").data("field", "name");
                $("input[name='header_uid']", form).addClass("selectpage").data("source", "lesson/teacher/index").data("primaryKey", "id").data('params',{"custom[type]":"3"}).data("field", "username");
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
                        {field: 'type', title: __('Type'), visible:false, searchList: {"1":'建班课',"2":'一对一'}},
                        {field: 'type_text', title: __('Type'), operate:false},
                        {field: 'name', title: __('Name')},
                        {field: 'lesson_id', title: __('Lesson_id'),visible:false},
                        {field: 'lesson', title: __('Lesson_id'),operate:false},
                        {field: 'max_member', title: __('Max_member'),operate:false},
                        {field: 'header_name', title: __('Header_uid')},
                        {field: 'remark', title: __('Remark'),operate:false},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"1":"正常","0":"禁用",'2':'已完结'}},
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
                    //value:"1":'正常',"0":'禁用',2:已完结
                    var color;
                    switch(value){
                        case '正常':color=colorArr.normal;break;
                        case '禁用':color=colorArr.deleted;break;
                        case '已完结':color=colorArr.locked;break;
                    }
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + __(value) + '</span>';
                    return html;
                }
            }
        }
    };
    return Controller;
});