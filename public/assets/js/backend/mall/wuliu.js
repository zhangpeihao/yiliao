define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'mall/wuliu/index',
                    add_url: 'mall/wuliu/add',
                    edit_url: 'mall/wuliu/edit',
                    del_url: 'mall/wuliu/del',
                    multi_url: 'mall/wuliu/multi',
                    table: 'mall_wuliu',
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
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'post_num', title: __('Post_num')},
                        {field: 'out_trade_no', title: __('Out_trade_no')},
                        // {field: 'uid', title: __('Uid')},
                        {field: 'post_user', title: __('Post_user')},
                        {field: 'post_mobile', title: __('Post_mobile')},
                        {field: 'wuliu_company', title: __('Post_type')},
                        {field: 'address', title: __('Address'),operate:false},
                        {field: 'accept_user', title: __('Accept_user'),operate:false},
                        {field: 'accept_mobile', title: __('Accept_mobile'),operate:false},
                        {field: 'status', title: __('Status'), visible:false, searchList: {0:'待发货',1:'已发货',2:'运输中',3:'已签收',4:'已退回'}},
                        {field: 'status_text', title: __('Status'), operate:false},
                        {field: 'ctime', title: __('Ctime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'utime', title: __('Utime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: function (value,row,index) {
                                var add_btn=[];
                                add_btn.push(
                                    {name: 'self-accept', text: '', title: '查询物流轨迹', icon: 'fa fa-truck', classname: 'btn btn-xs btn-primary', url: 'https://www.kuaidi100.com/chaxun?com='+row.post_type+'&nu='+row.post_num,extend:'target="_blank"'}
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
            }
        }
    };
    return Controller;
});