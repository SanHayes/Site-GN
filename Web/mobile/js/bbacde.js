'use strict';

$(document).ready(function (e) {
    var register = {
        data: {
            canLogin: false
        }
    };
    $('.weui-input').on('keyup', function () {
        var withdrawal = $.trim($('#withdrawal').val());
        var confirmPwd = $.trim($('#confirmPwd').val());
        var password = $.trim($('#password').val());
        var account = $.trim($('#account').val());
        var name = $.trim($('#name').val());
        if (name && account && password && confirmPwd && withdrawal) {
            $('#subBtn').removeClass('bgDisable').addClass('bgUseable');
            register.data.canLogin = true;
        } else {
            $('#subBtn').removeClass('bgUseable').addClass('bgDisable');
            register.data.canLogin = false;
        }
    });
    $('.forJsClick').on('click', function (e) {
        var url = $(this).data('url');
        if (url) {
            setTimeout(function () {
                window.location.href = url;
            }, 300);
        }
    });
    //遮罩层可以关闭
    $('.loadingWrap').on('click', function () {
        $('#loading').addClass('hidden');
    });
    //登录按钮
    $('#submit').on('click', function () {
        var invitCode = $.trim($('#invitCode').val());
        var withdrawal = $.trim($('#withdrawal').val());
        var confirmPwd = $.trim($('#confirmPwd').val());
        var password = $.trim($('#password').val());
        var account = $.trim($('#account').val());
        var name = $.trim($('#name').val());
        var utel = $.trim($('#utel').val());
        var scard = $.trim($('#scard').val());
        var cp_extra1_ele = $('#cp_extra1');
        var cp_extra1 = '';
        if (!name && _truenameNeed == 1) {
            $.toast(_msg_name, "forbidden");
            return;
        }
        if(cp_extra1_ele.length > 0){
            cp_extra1 = $.trim(cp_extra1_ele.val());
            if(!cp_extra1){
                $.toast(cp_extra1_ele.attr('placeholder'), "forbidden");
                return;
            }
        }

        if (!account) {
            $.toast(_msg_account, "forbidden");
            return;
        }
        if(account.length<5 || account.length>16){
			$.toast('用户名为5-16位数字或字母组成', "forbidden");
			return; 
		}
        if (!/(^[A-Za-z0-9]+$)/.test(account)) {
            $.toast('会员账号只能包含字母和数字', "forbidden");
            return;
        }
        if (!password) {
            $.toast(_msg_password, "forbidden");
            return;
        }
        if (password.length < 6) {
            $.toast(_msg_password_length, "forbidden");
            return;
        }

        if (!confirmPwd) {
            $.toast(_msg_confirm, "forbidden");
            return;
        }
        if (!(password === confirmPwd)) {
            $.toast(_msg_password_disagree, "forbidden");
            return;
        }
        if (!withdrawal) {
            $.toast(_msg_tx_password, "forbidden");
            return;
        }
        if (withdrawal.length < 6) {
            $.toast(_msg_tx_password_length, "forbidden");
            return;
        }
        if (!invitCode && _inviteNeed == '1') {
            $.toast(_msg_invite_need, "forbidden");
            return;
        }
        if (!scard && _registerIdNeed == '1') {
            $.toast(_msg_register_id, "forbidden");
            return;
        }

        //var str = 'name=' + name + 'account=' + account + 'password=' + password + 'confirmPwd=' + confirmPwd + 'withdrawal=' + withdrawal + 'invitCode=' + invitCode;
        var _data = {
            nickname:name,
            username:account,
            upwd:password,
            upwd2:confirmPwd,
            epwd:withdrawal,
            oid:invitCode,
            utel:utel,
            scard:scard,
        };
        if(cp_extra1){
            _data.cp_extra1 = cp_extra1;
        }
        var _loading = $('#loadingNotTuochClose');
        _loading.removeClass('hidden');
        _loading.addClass('show');
        $.post(_regurl, _data, function (res) {
            if(res.type != 1){
                _loading.removeClass('show');
                _loading.addClass('hidden');
                $.toast(res.data, "forbidden");
            }else{
                $.toast(_msg_reg_success, "text");
                setTimeout(function(){
                    window.location.href = _loginurl;
                },1500);
            }
        });

    });
    var browser = {
        versions: function () {
            var u = navigator.userAgent,
                app = navigator.appVersion;
            return { //移动终端浏览器版本信息
                mobile: !!u.match(/AppleWebKit.*Mobile.*/) //是否为移动终端
            };
        }()
    };

    if (!browser.versions.mobile) {
        //pc
        $('body').addClass('plantPc');
    }
});
