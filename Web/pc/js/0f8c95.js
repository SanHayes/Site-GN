'use strict';

function operateFormatter(value, row, index) {
    return ['<span class="touzhi" href="javascript:void(0)" >', '投资', '</a>  '].join('');
}

window.operateEvents = {
    'click .touzhi': function clickTouzhi(e, value, row, index) {
        showLoading();
        $.post(_invest_info_url, {pid:row.pid},function (rs) {
            hiddenLoading();
            console.log(rs);
            if(rs.code == 0){
                return showError(rs.data);
            }
            $('#formId').val(rs.data.pid);
            $('#formAccMoney').val(rs.user.usermoney);
            $('#formLimit').val(rs.data.days);
            $('#formPrecent').val(rs.data.rates + '%');
            $('#formMoney').val(rs.data.min);
            // $('#formBackTime').val(rs.data.back_time);
            $('#touDialog').modal('show');
        });

    }

};

var page = {

    init: function init() {

        page.doRender();

        $("form").bootstrapValidator({
            //默认提示
            message: 'This value is not valid',
            // 表单框里右侧的icon
            submitHandler: function submitHandler(validator, form, submitButton) {
                // 表单提交成功时会调用此方法
                // validator: 表单验证实例对象
                // form jq对象 指定表单对象
                // submitButton jq对象 指定提交按钮的对象
            },
            fields: {
                money: {
                    message: '投资金额验证失败',
                    validators: {
                        notEmpty: {
                            message: '投资金额不能为空'
                        }
                    }
                }

            }
        }); //提交验证

        $("#btn").click(function () {
            //非submit按钮点击后进行验证，如果是submit则无需此句直接验证

            if ($("form").data('bootstrapValidator').isValid()) {
                //获取验证结果，如果成功，执行下面代码
                //doajax
                var money = $('#money').val();
                var _id = $('#formId').val();

                var _data = {
                    money: money,
                    pid:_id
                };
                showLoading();
                $.post(_invest_url, _data, function (res) {
                    if(res.type == 1){
                        showSuccess(res.data);
                        setTimeout(function(){
                            window.location.href = _record_url;
                        },1500);
                    }else {
                        hiddenLoading();
                        showError(res.data);
                    }
                });
            }
        });
    },

    doRender: function doRender() {
        //生成用户数据

        $('#table').bootstrapTable({
            method: 'get',
            contentType: "application/x-www-form-urlencoded",
            dataType: "json",
            url: _product_url,
            // url: "/static/mock/businessListRes.json",
            // height: 500,//高度调整
            // striped: true, //是否显示行间隔色
            dataField: "list",//获取数据的别名，先省略，则为你返回的
            pageNumber: 1, //初始化加载第一页，默认第一页
            pagination: true, //是否分页
            // queryParamsType: 'limit',
            queryParams: function queryParams(params) {
                //这边获取参数
                return params;
            },
            // responseHandler: function responseHandler(res) {
            //     //res.rows[0].name = Math.random();
            //     return res.list;
            // },
            onLoadError: function onLoadError(status) {
                //注意，，线上请屏蔽下面代码
                //var mock = page.getMockData();
                //$('#table').bootstrapTable('load', mock);
            },
            // onClickRow: function (row, $element, field) {
            //     window.location.href = "./index.html"
            // },
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
                title: '投资期限',
                field: 'days',
                visible: true,
                halign: 'center',
                align: 'center'

            }, {
                title: '投资效益',
                field: 'rates',
                visible: true,
                halign: 'center',
                align: 'center',
                formatter(value, row) {
                    return row.rates + '%';
                },
            }, {
                title: '起投金额',
                field: 'min',
                visible: true,
                halign: 'center',
                align: 'center'
            }, {
                field: 'operate',
                title: '操作',
                align: 'center',
                clickToSelect: false,
                events: window.operateEvents,
                formatter: operateFormatter
            }],
            locale: 'zh-CN' //中文支持,
        });
    }


};
$(function () {
    page.init();
});
