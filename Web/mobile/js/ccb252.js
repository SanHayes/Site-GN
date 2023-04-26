'use strict';

$(document).ready(function (e) {

    //失去聚焦校验
    $('.weui-input').on('keyup', function () {
        var oldpwd = $.trim($('#oldpwd').val());
        var newpwd = $.trim($('#newpwd').val());
        var confirmpwd = $.trim($('#confirmpwd').val());
        var $submit = $("#submit");
        if (oldpwd && newpwd && confirmpwd) {
            $submit.removeClass('bgGery');
            $submit.addClass('bgBlue');
        } else {
            $submit.removeClass('bgBlue');
            $submit.addClass('bgGery');
        }
    });
    //提交
    $('#submit').on('click', function () {
        var oldpwd = $.trim($('#oldpwd').val());
        var newpwd = $.trim($('#newpwd').val());
        var confirmpwd = $.trim($('#confirmpwd').val());

        if ($(this).hasClass('bgGery')) {
            return;
        }
        if (!oldpwd) {
            $.toast("登录密码是必须的", "forbidden");
            return;
        }

        if (!newpwd) {
            $.toast("新提现密码是必须的", "forbidden");
            return;
        }
        if (!confirmpwd) {
            $.toast("确认密码是必须的", "forbidden");
            return;
        }
        //$.toast(str, "text");

        var _data = {
            loginpwd:oldpwd,
            password:newpwd,
            cpassword:confirmpwd
        };
        var _loading = $('#loadingNotTuochClose');
        _loading.removeClass('hidden');
        _loading.addClass('show');
        $.post(_url, _data, function (res) {
            console.log(res);
            _loading.removeClass('show');
            _loading.addClass('hidden');
            if(res.type != 1){
                $.toast(res.data, "forbidden");
            }else{
                $.toast(res.msg, "text");
                setTimeout(function(){
                    window.location.href = _jump_url;
                },1500);
            }
        });
    });
});
