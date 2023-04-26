'use strict';

var page = {
    init: function init() {

        // page.doRender();
        page.handleCurrentDown();
    },

    doRender: function doRender() {
        //生成用户数据

        $('#table').bootstrapTable({
            method: 'get',
            contentType: "application/x-www-form-urlencoded",
            dataType: "json",
            url: _record_url,
            // height: 500,//高度调整
            // striped: true, //是否显示行间隔色
            dataField: "data",//获取数据的别名，先省略，则为你返回的
            pageNumber: 1, //初始化加载第一页，默认第一页
            pagination: true, //是否分页
            // queryParamsType: 'limit',
            queryParams: function queryParams(params) {
                //这边获取参数
                return params;
            },
            // responseHandler: function responseHandler(res) {
            //     return res.data;
            // },
            onLoadSuccess: function onLoadSuccess() {
                page.handleCurrentDown();
            },
            onLoadError: function onLoadError(status) {
                //注意，，线上请屏蔽下面代码
                //var mock = page.getMockData();
                //$('#table').bootstrapTable('load', mock);
                //page.handleCurrentDown();
            },
            onClickRow: function onClickRow(row, $element, field) {
                window.location.href = "./index.html";
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
                title: '名称',
                field: 'product_name',
                visible: true

            },{
                title: '类型',
                field: 'style_name',
                visible: true

            },{
                title: '金额',
                field: 'money',
                visible: true

            }, {
                title: '时间',
                halign: 'center',
                align: 'center',
                field: 'endprofit',
                visible: true
            }, {
                title: '手续费',
                field: 'fee',
                halign: 'center',
                align: 'center',
                visible: true
            }, {
                title: '建仓点位',
                field: 'buyprice',
                halign: 'center',
                align: 'center',
                visible: true
            }, {
                title: '下单时间',
                field: 'create_time',
                halign: 'center',
                align: 'center',
                visible: true
            }, {
                title: '平仓时间',
                field: 'pingTime',
                halign: 'center',
                align: 'center',
                visible: true,
                formatter: function formatter(value, row, index) {
                    if(row.status == 0){
                        return "<div class='time' data-time=" + row.diff + " >-</div>";
                    }else {
                        return row.selltime;
                    }
                }
            }],
            locale: 'zh-CN' //中文支持,
        });
    },
    getMockData: function getMockData() {
        var mockres = {
            "total": 100,
            "rows": [{
                "name": "中国黄金" + Math.random(),
                "status": "0",
                "money": "100.00",
                "orderTime": "2020-12-17 17:30:45",
                "pingTime": "100"
            }, {
                "name": "中国白金",
                "status": "1",
                "money": "100.00",
                "orderTime": "2020-12-17 17:30:45",
                "pingTime": "300"
            }, {
                "name": "中国黄金",
                "status": "1",
                "money": "100.00",
                "orderTime": "2020-12-17 17:30:45",
                "pingTime": "677"
            }, {
                "name": "中国白金",
                "status": "0",
                "money": "100.00",
                "orderTime": "2020-12-17 17:30:45",
                "pingTime": "666"
            }, {
                "name": "中国黄金",
                "status": "0",
                "money": "100.00",
                "orderTime": "2020-12-17 17:30:45",
                "pingTime": "433"
            }, {
                "name": "中国白金",
                "status": "1",
                "money": "100.00",
                "orderTime": "2020-12-17 17:30:45",
                "pingTime": "444"
            }, {
                "name": "中国黄金",
                "status": "0",
                "money": "100.00",
                "orderTime": "2020-12-17 17:30:45",
                "pingTime": "333"
            }, {
                "name": "中国白金",
                "status": "0",
                "money": "100.00",
                "orderTime": "2020-12-17 17:30:45",
                "pingTime": "205"
            }]
        };
        return mockres;
    },
    handleCurrentDown: function handleCurrentDown() {
        $('.time').each(function (index) {
            var _this = this;
            var time = $(this).data('time');
            if (time) {
                page.countDown(time, function (msg) {
                    $(_this).html(msg);
                });
            }
        });
    },
    countDown: function countDown(maxtime, fn) {
        var timer = setInterval(function () {
            if (!!maxtime) {
                var msg = '';
                var day = Math.floor(maxtime / 86400),
                    hour = Math.floor(maxtime % 86400 / 3600),
                    minutes = Math.floor(maxtime % 3600 / 60),
                    seconds = Math.floor(maxtime % 60);
                if (hour < 10) {
                    hour = '0' + hour;
                }
                if (minutes < 10) {
                    minutes = '0' + minutes;
                }
                if (seconds < 10) {
                    seconds = '0' + seconds;
                }
                msg = hour + ":" + minutes + ":" + seconds;
                fn(msg);
                --maxtime;
            } else {
                clearInterval(timer);
                fn("结束!");
            }
        }, 1000);
    }

};
$(function () {
    page.init();
});
