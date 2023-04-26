'use strict';

$(document).ready(function () {

    //失去聚焦校验
    $('.weui-input').on('keyup', function () {
        var $submit = $('#submit');
        var withdrawalPwd = $.trim($('#withdrawalPwd').val());
        var withdrawalMoney = $.trim($('#withdrawalMoney').val());
        if (withdrawalPwd && withdrawalMoney) {
            $submit.removeClass('bgGery');
            $submit.addClass('bgBlue');
        } else {
            $submit.removeClass('bgBlue');
            $submit.addClass('bgGery');
        }
    });
    //提交
    $('#submit').on('click', function () {
        var withdrawalPwd = $.trim($('#withdrawalPwd').val());
        var withdrawalMoney = $.trim($('#withdrawalMoney').val());
        var bankcardno = $.trim($('#bankcardno').val());
        var remain = $.trim($('#remain').html());
        if ($(this).hasClass('bgGery')) {
            return;
        }
        if (!withdrawalMoney) {
            $.toast("提现金额是必须的", "forbidden");
            return;
        }

        if (!withdrawalPwd) {
            $.toast("提现密码是必须的", "forbidden");
            return;
        }

        if (withdrawalMoney < remain) {
            $.toast("提现金额不能大于余额", "forbidden");
            return;
        }

        var str = 'withdrawalPwd=' + withdrawalPwd + 'withdrawalMoney=' + withdrawalMoney;

        //$.toast(str, "text");

        var _data = {
            price:withdrawalMoney,
            passwd:withdrawalPwd,
            bankcardno: bankcardno,
            bankid: bankid,
        };
        var _loading = $('#loadingNotTuochClose');
        _loading.removeClass('hidden');
        _loading.addClass('show');
        $.post(_withdrawurl, _data, function (res) {
            if(res.type != 1){
                _loading.removeClass('show');
                _loading.addClass('hidden');
                $.toast(res.data, "forbidden");
            }else{
                $.toast(res.data, "text");
                setTimeout(function(){
                    window.location.href = _jump_url;
                },1500);
            }
        });
    });

    //输入金额变化实际到账金额
    $("#withdrawalMoney").bind('input propertychange', function (e) {
        var _money = parseInt($(this).val());
        if(_money){
            var _val = _money - _txfee * _money;
            $('#sjdz').val(_val);
        }

    });
});
