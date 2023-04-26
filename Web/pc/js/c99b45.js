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
        userName: {
          message: '登录名验证失败',
          validators: {
            notEmpty: {
              message: '登录名不能为空'
            }
          }
        },
        password: {
          message: '密码验证失败',
          validators: {
            notEmpty: {
              message: '密码不能为空'
            },
            stringLength: { //长度限制
              min: 6,
              max: 18,
              message: '密码长度必须在6到18位之间'
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
        var userName = $.trim($('#userName').val());
        var password = $.trim($('#password').val());

        showLoading();
        $.post(_loginurl, {username:userName, password:password}, function (res) {
            if(res.code != 0){
                hiddenLoading();
                showError(res.msg);
            }else{
                showSuccess(_msg_login_suc);
                setTimeout(function(){
                    window.location.href = _indexurl;
                },1500);
            }
        });
          return false;
      }
    });
  }
};
$(function () {
  page.init();
});