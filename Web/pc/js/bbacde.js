'use strict';

var page = {
  init: function init() {

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
        name: {
          message: '姓名验证失败',
          validators: {
            notEmpty: {
              message: '姓名不能为空'
            }
          }
        },
        cp_extra1: {
            message: _cp_extra1_name + '验证失败',
            validators: {
                notEmpty: {
                    message: _cp_extra1_name + '不能为空'
                }
            }
        },
        account: {
          message: '账号验证失败',
          validators: {
            notEmpty: {
              message: '账号不能为空'
            }
          }
        },
        password: {
          message: '密码验证失败',
          validators: {
            notEmpty: {
              message: '密码不能为空'
            }
          },
          stringLength: { //长度限制
            min: 6,
            max: 18,
            message: '密码长度必须在6到18位之间'
          }
        },
        confirmPwd: {
          message: '确认密码验证失败',
          validators: {
            notEmpty: {
              message: '确认密码不能为空'
            },
            identical: { //比较是否相同
              field: 'password', //需要进行比较的input name值
              message: '确认密码跟密码不一致'
            }
          }
        },
        withdrawal: {
          message: '提现密码验证失败',
          validators: {
            notEmpty: {
              message: '提现密码不能为空'
            }
          },
          stringLength: { //长度限制
            min: 6,
            max: 18,
            message: '提现密码长度必须在6到18位之间'
          }
        }

      }
    }); //提交验证

    $("#btn").click(function () {
      //非submit按钮点击后进行验证，如果是submit则无需此句直接验证

      if ($("form").data('bootstrapValidator').isValid()) {
        //获取验证结果，如果成功，执行下面代码
        //   if(!$('#agree').is(':checked')){
        //       return showError('请同意用户协议');
        //   }
        //doajax
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
        if(account.length<5 || account.length>16){
			return showError('用户名为5-16位数字或字母组成');
			return; 
		}
        if (!/(^[A-Za-z0-9]+$)/.test(account)) {
            return showError('会员账号只能包含字母和数字');
          return;
        }
        if(!invitCode && _inviteNeed == '1'){
          return showError(_msg_invite_need);
        }
        if(!scard && _registerIdNeed == '1'){
          return showError(_msg_register_id);
        }
          if (cp_extra1_ele.length > 0) {
              cp_extra1 = $.trim(cp_extra1_ele.val());
              if (!cp_extra1) {
                  showError(cp_extra1_ele.attr('placeholder'));
                  return;
              }
          }
        var _data = {
            nickname:name,
            username:account,
            utel:utel,
            scard:scard,
            upwd:password,
            upwd2:confirmPwd,
            epwd:withdrawal,
            oid:invitCode
        };
        if(cp_extra1){
            _data.cp_extra1 = cp_extra1;
        }
        showLoading();
        $.post(_regurl, _data, function (res) {
            if(res.type != 1){
                hiddenLoading();
                showError(res.data);
            }else{
              showSuccess(_msg_reg_success);
              setTimeout(function(){
                  window.location.href = _loginurl;
              },1500);
            }
        });
      }
    });
  }
};
$(function () {
  page.init();
});
