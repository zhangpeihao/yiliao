define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'mall/product/index',
                    index_url2: 'mall/product/index2',
                    index_url3: 'mall/product/index3',
                    add_url: 'mall/product/add',
                    edit_url: 'mall/product/edit',
                    del_url: 'mall/product/del',
                    multi_url: 'mall/product/multi',
                    table: 'mall_product',
                    self_accept_url:'mall/product/self_accept'
                }
            });

            //绑定事件
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var panel = $($(this).attr("href"));
                // console.log(panel);return;
                if (panel.size() > 0) {
                    Controller.table[panel.attr("id")].call(this);
                    $(this).on('click', function (e) {
                        $($(this).attr("href")).find(".btn-refresh").trigger("click");
                    });
                }
                //移除绑定的事件
                $(this).unbind('shown.bs.tab');
            });

            //必须默认触发shown.bs.tab事件
            $('ul.nav-tabs li.active a[data-toggle="tab"]').trigger("shown.bs.tab");
        },
        table:{
            first:function() {
                var table1 = $("#table1");
                table1.on('post-common-search.bs.table', function (event, table) {
                    var form = $("form", table.$commonsearch);
                    $("input[name='cid']", form).addClass("selectpage").data("source", "mall/category/index").data("primaryKey", "id").data("field", "name");
                    if ($(".selectpage", form).size() > 0) {
                        Form.events.selectpage(form);
                    }
                });
                // 初始化表格
                table1.bootstrapTable({
                    url: $.fn.bootstrapTable.defaults.extend.index_url,
                    toolbar: '#toolbar1',
                    pk: 'id',
                    sortName: 'id',
                    exportTypes: ['csv','excel'],
                    search: false, //是否启用快速搜索
                    searchFormVisible: false, //是否始终显示搜索表单
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id'), operate:false},
                            // {field: 'cid', title: __('Cid'),visible:false},
                            // {field: 'type_text', title: '类型',searchList:{1:'商品',2:'课程',3:'金币商城'}},
                            // {field: 'category', title: __('Cid'),operate:false},
                            {field: 'title', title: __('Title')},
                            // {field: 'summary', title: __('Summary')},
                            {field: 'logo', title: __('Logo'),formatter:Table.api.formatter.image},
                            // {field: 'pics', title: __('Pics')},
                            {field: 'post_fee', title: __('Post_fee'), operate:false},
                            // {field: 'attr_id', title: __('Attr_id')},
                            // {field: 'extra_attr', title: __('Extra_attr')},
                            // {field: 'address', title: __('Address')},
                            {field: 'price', title: __('Price'), operate:false},
                            {field: 'level_discount', title: "折扣",searchList:{1:"会员折扣",0:"会员不折扣"}},
                            // {field: 'origin_price', title: __('Origin_price'), operate:'BETWEEN'},
                            {field: 'store', title: __('Store'),formatter:Table.api.formatter.label, operate:false},
                            {field: 'sale_num', title: __('Sale_num'),formatter:Table.api.formatter.label, operate:false},
                            // {field: 'limit', title: __('Limit')},
                            // {field: 'transfer', title: __('Transfer')},
                            // {field: 'sale_total', title: __('Sale_total')},
                            {field: 'begin_time', title: __('Begin_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'end_time', title: __('End_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            // {field: 'is_recommend', title: __('Is_recommend'), visible:false, searchList: {0:'否',1:'是'}},
                            // {field: 'is_recommend_text', title: __('Is_recommend'), operate:false},
                            {field: 'status', title: __('Status'), visible:false, searchList: {0:'禁用',1:'正常'}},
                            {field: 'status_text', title: __('Status'), operate:false},
                            {field: 'sort', title: __('Sort'),operate:false},
                            // {field: 'remark', title: __('Remark')},
                            // {field: 'kefu', title: __('Kefu')},
                            // {field: 'ctime', title: __('Ctime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'utime', title: __('Utime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'operate', title: __('Operate'), table: table1, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                                buttons: function (value,row,index) {
                                    var add_btn=[];
                                    add_btn.push(
                                        {name: 'detail', text: '商品规格', title: '商品规格', icon: 'fa fa-list', classname: 'btn btn-xs btn-primary btn-dialog', url: 'mall/mallattr/index'}
                                        // {name: 'self-accept', text: '自提', title: '设置自提地点', icon: 'fa fa-recycle', classname: 'btn btn-xs btn-primary btn-dialog', url: 'mall/product/self_accept'}
                                    );
                                    return add_btn;
                                }
                            }
                        ]
                    ]
                });

                // 为表格绑定事件
                Table.api.bindevent(table1);
            },
            secord:function() {
                var table2 = $("#table2");
                table2.on('post-common-search.bs.table', function (event, table) {
                    var form = $("form", table.$commonsearch);
                    $("input[name='cid']", form).addClass("selectpage").data("source", "mall/category/index").data("primaryKey", "id").data("field", "name");
                    if ($(".selectpage", form).size() > 0) {
                        Form.events.selectpage(form);
                    }
                });
                // 初始化表格
                $.fn.bootstrapTable.defaults.extend.add_url='mall/product/add2';
                $.fn.bootstrapTable.defaults.extend.edit_url='mall/product/edit2';
                table2.bootstrapTable({
                    url: $.fn.bootstrapTable.defaults.extend.index_url2,
                    toolbar: '#toolbar2',
                    pk: 'id',
                    sortName: 'id',
                    exportTypes: ['csv','excel'],
                    search: false, //是否启用快速搜索
                    searchFormVisible: false, //是否始终显示搜索表单
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id'), operate:false},
                            // {field: 'cid', title: __('Cid'),visible:false},
                            // {field: 'category', title: __('Cid'),operate:false},
                            {field: 'title', title: __('Title')},
                            {field: 'logo', title: __('Logo'),formatter:Table.api.formatter.image},
                            // {field: 'post_fee', title: __('Post_fee'), operate:false},
                            {field: 'price', title: __('Price'), operate:false},
                            {field: 'level_discount', title: "折扣",searchList:{1:"会员折扣",0:"会员不折扣"}},
                            // {field: 'credit', title: '所需金币', operate:false},
                            {field: 'lesson_count', title: '课节数', operate:false},
                            // {field: 'store', title: __('Store'),formatter:Table.api.formatter.label, operate:false},
                            {field: 'sale_num', title: __('Sale_num'),formatter:Table.api.formatter.label, operate:false},
                            {field: 'begin_time', title: __('Begin_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'end_time', title: __('End_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'status', title: __('Status'), visible:false, searchList: {0:'禁用',1:'正常'}},
                            {field: 'status_text', title: __('Status'), operate:false},
                            {field: 'sort', title: __('Sort'),operate:false},
                            {field: 'utime', title: __('Utime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'operate', title: __('Operate'), table: table2, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                                buttons: function (value,row,index) {
                                    var add_btn=[];
                                    // add_btn.push(
                                        // {name: 'detail', text: '商品规格', title: '商品规格', icon: 'fa fa-list', classname: 'btn btn-xs btn-primary btn-dialog', url: 'mall/mallattr/index'}
                                        // {name: 'self-accept', text: '自提', title: '设置自提地点', icon: 'fa fa-recycle', classname: 'btn btn-xs btn-primary btn-dialog', url: 'mall/product/self_accept'}
                                    // );
                                    return add_btn;
                                }
                            }
                        ]
                    ]
                });

                // 为表格绑定事件
                Table.api.bindevent(table2);
            },
            third:function() {
                var table3 = $("#table3");
                table3.on('post-common-search.bs.table', function (event, table) {
                    var form = $("form", table.$commonsearch);
                    $("input[name='cid']", form).addClass("selectpage").data("source", "mall/category/index").data("primaryKey", "id").data("field", "name");
                    if ($(".selectpage", form).size() > 0) {
                        Form.events.selectpage(form);
                    }
                });
                // 初始化表格
                $.fn.bootstrapTable.defaults.extend.add_url='mall/product/add3';
                $.fn.bootstrapTable.defaults.extend.edit_url='mall/product/edit3';
                table3.bootstrapTable({
                    url: $.fn.bootstrapTable.defaults.extend.index_url3,
                    toolbar: '#toolbar3',
                    pk: 'id',
                    sortName: 'id',
                    exportTypes: ['csv','excel'],
                    search: false, //是否启用快速搜索
                    searchFormVisible: false, //是否始终显示搜索表单
                    columns: [
                        [
                            {checkbox: true},
                            {field: 'id', title: __('Id'), operate:false},
                            // {field: 'cid', title: __('Cid'),visible:false},
                            // {field: 'category', title: __('Cid'),operate:false},
                            {field: 'title', title: __('Title')},
                            {field: 'logo', title: __('Logo'),formatter:Table.api.formatter.image},
                            {field: 'post_fee', title: __('Post_fee'), operate:false},
                            // {field: 'price', title: __('Price'), operate:false},
                            {field: 'credit', title: '所需金币', operate:false},
                            {field: 'store', title: __('Store'),formatter:Table.api.formatter.label, operate:false},
                            {field: 'sale_num', title: __('Sale_num'),formatter:Table.api.formatter.label, operate:false},
                            {field: 'begin_time', title: __('Begin_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'end_time', title: __('End_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'status', title: __('Status'), visible:false, searchList: {0:'禁用',1:'正常'}},
                            {field: 'status_text', title: __('Status'), operate:false},
                            {field: 'sort', title: __('Sort'),operate:false},
                            {field: 'utime', title: __('Utime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'operate', title: __('Operate'), table: table3, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                                buttons: function (value,row,index) {
                                    var add_btn=[];
                                    add_btn.push(
                                        {name: 'detail', text: '商品规格', title: '商品规格', icon: 'fa fa-list', classname: 'btn btn-xs btn-primary btn-dialog', url: 'mall/mallattr/index'}
                                        // {name: 'self-accept', text: '自提', title: '设置自提地点', icon: 'fa fa-recycle', classname: 'btn btn-xs btn-primary btn-dialog', url: 'mall/product/self_accept'}
                                    );
                                    return add_btn;
                                }
                            }
                        ]
                    ]
                });

                // 为表格绑定事件
                Table.api.bindevent(table3);
            }
        },
        add: function () {
            $('input[name="row[cid]"]').data('params',function (obj) {
                return {custom:{type:$('input[name="row[category]"]:checked').val()}};
            });
            Controller.api.bindevent();
        },
        add2:function(){
          Controller.api.bindevent();
        },
        add3:function(){
          Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        edit2: function () {
            Controller.api.bindevent();
        },
        edit3: function () {
            Controller.api.bindevent();
        },
        self_accept:function(){
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});