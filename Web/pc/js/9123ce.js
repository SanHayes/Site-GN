'use strict';

var page = {
    init: function init() {
        var remain = $.trim($('#remain').val());
        $('form').bootstrapValidator({
            //默认提示
            message: 'This value is not valid', // 表单框里右侧的icon
            submitHandler: function submitHandler(validator, form, submitButton) {
                // 表单提交成功时会调用此方法
                // validator: 表单验证实例对象
                // form jq对象 指定表单对象
                // submitButton jq对象 指定提交按钮的对象
            },
            fields: {
                withdrawalMoney: {
                    message: '提现金额验证失败',
                    validators: {
                        notEmpty: {
                            message: '提现金额不能为空',
                        },
                        lessThan: {
                            value: remain,
                            message: '提现金额不能大于余额',
                        },
                    },
                },
                withdrawalPwd: {
                    message: '提现密码验证失败',
                    validators: {
                        notEmpty: {
                            message: '提现密码不能为空',
                        },
                    },
                },

            },
        }); //提交验证
        //输入金额变化实际到账金额
        $('#withdrawalMoney').bind('input propertychange', function (e) {
            var _money = parseInt($(this).val());
            if (_money) {
                var _val = _money - _txfee * _money;
                $('#sjdz').val(_val);
            }

        });
        $('#btn').click(function () {
            //非submit按钮点击后进行验证，如果是submit则无需此句直接验证
            if ($('form').data('bootstrapValidator').isValid()) {
                //获取验证结果，如果成功，执行下面代码
                var withdrawalPwd = $.trim($('#withdrawalPwd').val());
                var withdrawalMoney = $.trim($('#withdrawalMoney').val());
                var bankcardno = $.trim($('#bankcardno').val());
                var _data = {
                    price: withdrawalMoney,
                    passwd: withdrawalPwd,
                    bankcardno: bankcardno,
                    bankid: bankid,
                };
                showLoading();
                $.post(_withdrawurl, _data, function (res) {
                    if (res.type != 1) {
                        hiddenLoading();
                        showError(res.data);
                    } else {
                        showSuccess(res.data);
                        setTimeout(function () {
                            window.location.href = _jump_url;
                        }, 1500);
                    }
                });
            }
        });
    },
};
$(function () {
    page.init();
});
