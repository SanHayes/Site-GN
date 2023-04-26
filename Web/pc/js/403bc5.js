'use strict';

function operateFormatter(value, row, index) {
    return Number(value === 0) ? '已回款' : '未回款';
}

var page = {

    init: function init() {

        page.doRender();
    },

    doRender: function doRender() {
        //生成用户数据

        $('#table').bootstrapTable({
            method: 'get',
            contentType: "application/x-www-form-urlencoded",
            dataType: "json",
            url: _record_url,
            // url: "/static/mock/businessListRes.json",
            // height: 500,//高度调整
            // striped: true, //是否显示行间隔色
            dataField: "list",//获取数据的别名，先省略，则为你返回的
            totalField: 'total',
            pageNumber: 1, //初始化加载第一页，默认第一页
            pagination: true, //是否分页
            // queryParamsType: 'limit',
            queryParams: function queryParams(params) {
                //这边获取参数
                return params;
            },
            // responseHandler: function responseHandler(res) {
            //     //res.rows[0].name = Math.random();
            //     console.log(res);
            //     return res.list;
            // },
            onLoadError: function onLoadError(status) {
                //注意，，线上请屏蔽下面代码
                //var mock = page.getMockData();
                //$('#table').bootstrapTable('load', mock);
            },
            onClickRow: function onClickRow(row, $element, field) {
                //window.location.href = "./index.html";
            },
            rowStyle: function rowStyle(row, index) {

                return {
                    classes: 'hoverPointer'
                };
            },

            sidePagination: 'server', //在服务器分页
            pageSize: 10, //单页记录数
            // pageList:[5,10,20,30],//分页步进值
            showRefresh: true, //刷新按钮
            // showColumns:true,
            clickToSelect: true, //是否启用点击选中行
            toolbarAlign: 'right',
            buttonsAlign: 'right', //按钮对齐方式
            toolbar: '#toolbar', //指定工作栏
            columns: [{
                title: '编号',
                field: 'id',
                visible: true,
                halign: 'center',
                align: 'center'
            }, {
                title: '投资期限',
                field: 'days',
                visible: true,
                halign: 'center',
                align: 'center',
                formatter: function formatter(value, row, index) {
                    return row.days + '天';
                }
            }, {
                title: '本金',
                field: 'money',
                visible: true,
                halign: 'center',
                align: 'center'
            }, {
                title: '利息',
                field: 'interest',
                visible: true,
                halign: 'center',
                align: 'center',
                formatter: function formatter(value, row, index) {
                    return row.interest + '元';
                }
            }, {
                title: '投资时间',
                field: 'time',
                visible: true,
                halign: 'center',
                align: 'center',
            }, {
                title: '回款时间',
                field: 'totime',
                visible: true,
                halign: 'center',
                align: 'center'
            }, {
                title: '状态',
                field: 'status_name',
                visible: true,
                halign: 'center',
                align: 'center',
                formatter:  function formatter(value, row, index) {
                    return row.state == 2 ? '已回款' : '待回款';
                }
            }],
            locale: 'zh-CN' //中文支持,
        });
    }

};
$(function () {
    page.init();
});
