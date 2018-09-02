define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'mall/pay/index',
                    add_url: 'mall/pay/add',
                    edit_url: 'mall/pay/edit',
                    del_url: 'mall/pay/del',
                    multi_url: 'mall/pay/multi',
                    table: 'mall_pay',
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
                searchFormVisible: true, //是否始终显示搜索表单
                columns: [
                    [
                        // {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        // {field: 'pay_sn', title: __('Pay_sn')},
                        {field: 'out_trade_no', title: __('Out_trade_no')},
                        // {field: 'uid', title: __('Uid')},
                        {field: 'username', title:"用户",operate:false},
                        {field: 'total', title: "订单总额", operate:false},
                        {field: 'money', title: "实际支付", operate:false},
                        // {field: 'ratio', title: __('Ratio'), operate:'BETWEEN'},
                        {field: 'credit', title: "金币", operate:false},
                        // {field: 'coupon', title: __('Coupon'), operate:'BETWEEN'},
                        {field: 'status', title: '状态', visible:false, searchList: {'-1':'订单取消',0:'待支付',1:'已支付',2:'已发货',3:'已签收',4:'已退款'}},
                        {field: 'status_text', title: __('Status'), operate:false},
                        {field: 'type', title: __('Type'),searchList:{1:'在线支付',2:'余额支付',3:'线下付款',4:'兑换'},operate:false},
                        {field: 'ctype', title: __('Ctype'), visible:false, searchList: {0:'消费',1:'充值'}},
                        {field: 'ctype_text', title: __('Ctype'), operate:false},
                        {field: 'onlinetype', title: __('Onlinetype'),searchList:{1:'支付宝',2:'微信',3:'现金',4:'团购券',5:'赠送',6:'会员'}},
                        // {field: 'outlinetype', title: __('Outlinetype')},
                        // {field: 'outlinesn', title: __('Outlinesn')},
                        // {field: 'ratioscore', title: __('Ratioscore')},
                        // {field: 'couponcode', title: __('Couponcode')},
                        // {field: 'remark', title: __('Remark')},
                        // {field: 'image', title: __('Image'), formatter: Table.api.formatter.image},
                        {field: 'creator', title: __('Creator')},
                        // {field: 'update_creator', title: __('Update_creator')},
                        {field: 'ctime', title: __('Ctime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'utime', title: __('Utime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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
            }
        }
    };
    return Controller;
});