define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'mall/productorder/index',
                    add_url: 'mall/productorder/add',
                    edit_url: 'mall/productorder/edit',
                    del_url: 'mall/productorder/del',
                    multi_url: 'mall/productorder/multi',
                    table: 'mall_product_order',
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
            first:function () {
                var table1 = $("#table1");
                table1.on('post-common-search.bs.table', function (event, table) {
                    var form = $("form", table.$commonsearch);
                    $("input[name='uid']", form).addClass("selectpage").data("source", "user/user/index").data("primaryKey", "id").data("field", "username");
                    $("input[name='pid']", form).addClass("selectpage").data("source", "mall/product/index").data("primaryKey", "id").data("field", "title").data("params",'{"custom[type]":"1"}');
                    if ($(".selectpage", form).size() > 0) {
                        Form.events.selectpage(form);
                    }
                });
                // 初始化表格
                table1.bootstrapTable({
                    url: $.fn.bootstrapTable.defaults.extend.index_url,
                    pk: 'id',
                    sortName: 'id',
                    exportTypes: ['csv','excel'],
                    search: false, //是否启用快速搜索
                    searchFormVisible: true, //是否始终显示搜索表单
                    toolbar: '#toolbar1',
                    columns: [
                        [
                            // {checkbox: true},
                            {field: 'id', title: __('Id'),operate:false},
                            {field: 'uid', title: __('Uid'),visible:false},
                            {field: 'username', title: '用户',operate:false},
                            {field: 'pid', title: __('Pid'),visible:false},
                            {field: 'product_title', title:'商品',operate:false},
                            // {field: 'price', title: __('Price'), operate:'BETWEEN'},
                            // {field: 'menu', title: __('Menu')},
                            // {field: 'fee', title: __('Fee'), operate:'BETWEEN'},
                            {field: 'total', title: __('Total'), operate:false},
                            {field: 'money', title: __('Money'), operate:false},
                            {field: 'num', title: __('Num'),operate:false},
                            {field: 'attr', title: __('Attr'),operate:false},
                            // {field: 'credit_money', title: __('Credit_money'), operate:'BETWEEN'},
                            {field: 'credit', title: __('Credit'),operate:false},
                            {field: 'pay_type', title: __('Pay_type'),searchList:{1:'积分',2:'支付宝',3:'微信',4:'余额支付',5:'兑换码'}},
                            {field: 'post_type', title: __('Post_type'), visible:false, searchList: {0:'自提',1:'寄送'}},
                            {field: 'post_type_text', title: __('Post_type'), operate:false},
                            // {field: 'post_num', title: __('Post_num')},
                            {field: 'out_trade_no', title: __('Out_trade_no')},
                            // {field: 'tmp_paysn', title: __('Tmp_paysn')},
                            // {field: 'user_coupon_id', title: __('User_coupon_id')},
                            // {field: 'user_duihuan_code', title: __('User_duihuan_code')},
                            // {field: 'share_code', title: __('Share_code')},
                            // {field: 'address', title: __('Address')},
                            // {field: 'form_id', title: __('Form_id')},
                            // {field: 'openid', title: __('Openid')},
                            {field: 'status', title: __('Status'), visible:false, searchList: {"-1":'订单取消',0:'待支付',1:'已支付',2:'已发货',3:'已签收',4:'已退款'}},
                            {field: 'status_text', title: __('Status'), operate:false,formatter:Table.api.formatter.label},
                            // {field: 'remark', title: __('Remark')},
                            {field: 'from', title: __('From'),searchList:{'ios':'ios','android':'android'}},
                            {field: 'ctime', title: __('Ctime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'utime', title: __('Utime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field:'',title:'发货',formatter:Controller.api.formatter.send_post,operate:false},
                            {field: 'operate', title: __('Operate'), table: table1, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                        ]
                    ]
                });

                // 为表格绑定事件
                Table.api.bindevent(table1);
            },
            secord:function () {
                var table2 = $("#table2");
                table2.on('post-common-search.bs.table', function (event, table) {
                    var form = $("form", table.$commonsearch);
                    $("input[name='uid']", form).addClass("selectpage").data("source", "user/user/index").data("primaryKey", "id").data("field", "username");
                    // $("input[name='pid']", form).addClass("selectpage").data("source", "mall/product/index2").data("primaryKey", "id").data("field", "title").data('params','{"custom[type]":"1"}');
                    $("input[name='student_id']", form).addClass("selectpage").data("source", "student/index").data("primaryKey", "id").data("field", "username");
                    $("input[name='lesson_id']", form).addClass("selectpage").data("source", "lesson/lesson/index").data("primaryKey", "id").data("field", "name");
                    if ($(".selectpage", form).size() > 0) {
                        Form.events.selectpage(form);
                    }
                });
                // 初始化表格
                $.fn.bootstrapTable.defaults.extend.index_url='mall/productorder/index2';
                table2.bootstrapTable({
                    url: $.fn.bootstrapTable.defaults.extend.index_url,
                    pk: 'id',
                    sortName: 'id',
                    exportTypes: ['csv','excel'],
                    search: false, //是否启用快速搜索
                    searchFormVisible: true, //是否始终显示搜索表单
                    toolbar: '#toolbar2',
                    columns: [
                        [
                            // {checkbox: true},
                            {field: 'id', title: __('Id'),operate:false},
                            {field: 'uid', title: __('Uid'),visible:false},
                            {field: 'username', title: '用户',operate:false},
                            // {field: 'pid', title: __('Pid'),visible:false},
                            // {field: 'product_title', title:'商品',operate:false},
                            {field: 'lesson_id', title:'课程',visible:false},
                            {field: 'lesson_text', title:'课程',operate:false},
                            // {field: 'price', title: __('Price'), operate:'BETWEEN'},
                            // {field: 'menu', title: __('Menu')},
                            // {field: 'fee', title: __('Fee'), operate:'BETWEEN'},
                            {field: 'total', title: __('Total'), operate:false},
                            {field: 'money', title: __('Money'), operate:false},
                            // {field: 'credit', title: __('Credit'),operate:false},
                            // {field: 'credit_money', title: __('Credit_money'), operate:'BETWEEN'},
                            {field: 'pay_type', title: __('Pay_type'),searchList:{1:'积分',2:'支付宝',3:'微信',4:'余额支付',5:'兑换码'}},
                            {field: 'num', title: __('Num'),operate:false},
                            {field: 'lesson_count', title:'课节数',operate:false},
                            {field: 'already_lesson', title:'已上课节数',operate:false},
                            {field: 'student_id', title:'学员',visible:false},
                            {field: 'student_username', title:'学员',operate:false},
                            // {field: 'post_type', title: __('Post_type'), visible:false, searchList: {0:'自提',1:'寄送'}},
                            // {field: 'post_type_text', title: __('Post_type'), operate:false},
                            // {field: 'post_num', title: __('Post_num')},
                            {field: 'out_trade_no', title: __('Out_trade_no')},
                            // {field: 'tmp_paysn', title: __('Tmp_paysn')},
                            // {field: 'user_coupon_id', title: __('User_coupon_id')},
                            // {field: 'user_duihuan_code', title: __('User_duihuan_code')},
                            // {field: 'share_code', title: __('Share_code')},
                            // {field: 'address', title: __('Address')},
                            // {field: 'form_id', title: __('Form_id')},
                            // {field: 'openid', title: __('Openid')},
                            {field: 'status', title: __('Status'), visible:false,operate:false, searchList: {"-1":'订单取消',0:'待支付',1:'已支付',2:'已发货',3:'已签收',4:'已退款'}},
                            {field: 'status_text', title: __('Status'), operate:false,formatter:Table.api.formatter.label},
                            // {field: 'remark', title: __('Remark')},
                            {field: 'from', title: __('From'),searchList:{'ios':'ios','android':'android'}},
                            // {field: 'ctime', title: __('Ctime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'utime', title: __('Utime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field:'',title:'发货',formatter:Controller.api.formatter.send_post,operate:false},
                            {field: 'operate', title: __('Operate'), table: table2, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                        ]
                    ]
                });

                // 为表格绑定事件
                Table.api.bindevent(table2);
            },
            third:function () {
                var table3 = $("#table3");
                table3.on('post-common-search.bs.table', function (event, table) {
                    var form = $("form", table.$commonsearch);
                    $("input[name='uid']", form).addClass("selectpage").data("source", "user/user/index").data("primaryKey", "id").data("field", "username");
                    $("input[name='pid']", form).addClass("selectpage").data("source", "mall/product/index3").data("primaryKey", "id").data("field", "title");
                    if ($(".selectpage", form).size() > 0) {
                        Form.events.selectpage(form);
                    }
                });
                // 初始化表格
                $.fn.bootstrapTable.defaults.extend.index_url='mall/productorder/index3';
                table3.bootstrapTable({
                    url: $.fn.bootstrapTable.defaults.extend.index_url,
                    pk: 'id',
                    sortName: 'id',
                    exportTypes: ['csv','excel'],
                    search: false, //是否启用快速搜索
                    searchFormVisible: true, //是否始终显示搜索表单
                    toolbar: '#toolbar3',
                    columns: [
                        [
                            // {checkbox: true},
                            {field: 'id', title: __('Id'),operate:false},
                            {field: 'uid', title: __('Uid'),visible:false},
                            {field: 'username', title: '用户',operate:false},
                            {field: 'pid', title: __('Pid'),visible:false},
                            {field: 'product_title', title:'商品',operate:false},
                            // {field: 'price', title: __('Price'), operate:'BETWEEN'},
                            // {field: 'menu', title: __('Menu')},
                            // {field: 'fee', title: __('Fee'), operate:'BETWEEN'},
                            {field: 'total', title: __('Total'), operate:false},
                            {field: 'money', title: __('Money'), operate:false},
                            {field: 'credit', title: __('Credit'),operate:false},
                            // {field: 'credit_money', title: __('Credit_money'), operate:'BETWEEN'},
                            {field: 'pay_type', title: __('Pay_type'),searchList:{1:'积分',2:'支付宝',3:'微信',4:'余额支付',5:'兑换码'},operate:false},
                            {field: 'num', title: __('Num'),operate:false},
                            {field: 'post_type', title: __('Post_type'), visible:false, searchList: {0:'自提',1:'寄送'}},
                            {field: 'post_type_text', title: __('Post_type'), operate:false},
                            {field: 'post_num', title: __('Post_num')},
                            {field: 'out_trade_no', title: __('Out_trade_no')},
                            // {field: 'tmp_paysn', title: __('Tmp_paysn')},
                            // {field: 'user_coupon_id', title: __('User_coupon_id')},
                            // {field: 'user_duihuan_code', title: __('User_duihuan_code')},
                            // {field: 'share_code', title: __('Share_code')},
                            // {field: 'address', title: __('Address')},
                            // {field: 'form_id', title: __('Form_id')},
                            // {field: 'openid', title: __('Openid')},
                            {field: 'status', title: __('Status'), visible:false, searchList: {"-1":'订单取消',0:'待支付',1:'已支付',2:'已发货',3:'已签收',4:'已退款'}},
                            {field: 'status_text', title: __('Status'), operate:false,formatter:Table.api.formatter.label},
                            // {field: 'remark', title: __('Remark')},
                            {field: 'from', title: __('From'),searchList:{'ios':'ios','android':'android'}},
                            {field: 'ctime', title: __('Ctime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field: 'utime', title: __('Utime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                            {field:'',title:'发货',formatter:Controller.api.formatter.send_post,operate:false},
                            {field: 'operate', title: __('Operate'), table: table3, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                        ]
                    ]
                });

                // 为表格绑定事件
                Table.api.bindevent(table3);
            }
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
            formatter: {
                send_post: function (value, row, index) {
                    console.log(row);
                    if (row['status']==1 && row['post_type']==1){
                        return '<a title="订单发货" class="btn btn-xl btn-dialog" href="/admin/mall/wuliu/add?out_trade_no='+row['out_trade_no']+'">发货</a cla>';
                    }else{
                        return "";
                    }
                }
            }
        }
    };
    return Controller;
});