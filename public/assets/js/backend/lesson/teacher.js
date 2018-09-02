define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'jstree'], function ($, undefined, Backend, Table, Form,undefined) {
    //读取选中的条目
    $.jstree.core.prototype.get_all_checked = function (full) {
        var obj = this.get_selected(), i, j;
        for (i = 0, j = obj.length; i < j; i++) {
            obj = obj.concat(this.get_node(obj[i]).parents);
        }
        obj = $.grep(obj, function (v, i, a) {
            return v != '#';
        });
        obj = obj.filter(function (itm, i, a) {
            return i == a.indexOf(itm);
        });
        return full ? $.map(obj, $.proxy(function (i) {
            return this.get_node(i);
        }, this)) : obj;
    };
    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'lesson/teacher/index',
                    add_url: 'lesson/teacher/add',
                    edit_url: 'lesson/teacher/edit',
                    del_url: 'lesson/teacher/del',
                    multi_url: 'lesson/teacher/multi',
                    table: 'teacher',
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
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'agency_id', title: '机构',visible:false},
                        {field: 'username', title: __('Username')},
                        {field: 'mobile', title: __('Mobile')},
                        {field: 'power_text', title: __('Power'),operate:false,visible:true,formatter: Table.api.formatter.flag},
                        {field: 'is_bind', title: __('Is_bind'), visible:false, searchList: {"1":'已注册绑定',0:'未注册绑定'}},
                        {field: 'is_bind_text', title: __('Is_bind'), operate:false,formatter: Controller.api.formatter.is_bind},
                        {field: 'bind_uid', title: '绑定uid', operate:false},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"1":'正常',"0":'禁用'}},
                        {field: 'status_text', title: __('Status'), operate:false,formatter: Controller.api.formatter.status},
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
                Form.api.bindevent($("form[role=form]"),null,null,function () {
                    if ($("#treeview").size() > 0) {
                        var r = $("#treeview").jstree("get_all_checked");
                        $("input[name='row[power]']").val(r.join(','));
                    }
                    return true;
                });
                //渲染权限节点树
                //销毁已有的节点树
                $("#treeview").jstree("destroy");
                Controller.api.rendertree(nodeData);
                $("select[name='row[power]']").trigger("change");
            },
            rendertree: function (content) {
                $("#treeview")
                    .on('redraw.jstree', function (e) {
                        $(".layer-footer").attr("domrefresh", Math.random());
                    })
                    .jstree({
                        "themes": {"stripes": true},
                        "checkbox": {
                            "keep_selected_style": false,
                        },
                        "types": {
                            "root": {
                                "icon": "fa fa-folder-open",
                            },
                            "menu": {
                                "icon": "fa fa-folder-open",
                            },
                            "file": {
                                "icon": "fa fa-file-o",
                            }
                        },
                        "plugins": ["checkbox", "types"],
                        "core": {
                            'check_callback': true,
                            "data": content
                        }
                    });
            },
            formatter:{
                is_bind:function (value, row, index) {
                    var colorArr = {normal: 'success', hidden: 'grey', deleted: 'danger', locked: 'info'};
                    //value:"1":'已注册绑定',0:'未注册绑定'
                    var color;
                    switch(value){
                        case '已注册绑定':color=colorArr.normal;break;
                        case '未注册绑定':color=colorArr.deleted;break;
                    }
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + __(value) + '</span>';
                    return html;
                },
                show_power:function (value, row, index) {
                    console.log(value);
                    return "<label class='label label-success'>"+value+"</label>";
                },
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