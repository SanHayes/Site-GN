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
              message: '收款人姓名不能为空'
            }
          }
        },
        bank: {
          message: '银行名称验证失败',
          validators: {
            notEmpty: {
              message: '银行名称不能为空'
            }
          }
        },
        subBranch: {
          message: '支行名称验证失败',
          validators: {
            notEmpty: {
              message: '支行名称不能为空'
            }
          }
        },
        no: {
          message: '银行卡号验证失败',
          validators: {
            notEmpty: {
              message: '银行卡号不能为空'
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
        var remark = $('#remark').val();
        var subBranch = $('#subBranch').html();
        var no = $('#no').html();
        var bank = $('#bank').html();
        var name = $('#name').html();
        var val = null;
        $('.jsForMoneyItem').each(function (item) {
          if ($(this).hasClass('on')) {
            val = $.trim($(this).html());
          }
        });
        if (!val) {
          val = $.trim($('#inputMoney').val());
        }
        if (!val) {
          showError("请选择金额");
          return;
        }

          var _data = {
            truename:remark,
            price:val,
            pay_type: 5,
          };
        showLoading();
        $.post(_payurl, _data, function (res) {
            if(res.status){
                showSuccess(res.msg);
                setTimeout(function(){
                    window.location.href = _recordurl;
                },1500);
            }else {
                hiddenLoading();
                showError(res.msg)
            }
        });
      }
    });
    $('#inputMoney').on('keyup', function (e) {
      var val = $(this).val();
      $('#showMoney').html(val);
    });
    $('#inputMoney').on('focus', function () {
      // $('#inputMoney').val('0')
      $('#showMoney').html('0');
      $('.jsForMoneyItem').removeClass('on');
    });
    //钱item
    $('.jsForMoneyItem').on('click', function () {
      var $this = $(this);
      $('.jsForMoneyItem').removeClass('on');
      $this.addClass('on');
      $('#inputMoney').val('');
      var val = $.trim($(this).html());
      $('#showMoney').html(val);
    });
  }
};
$(function () {
  page.init();
});
