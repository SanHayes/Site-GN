'use strict';

$(document).ready(function () {


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
    //复制
    $('.iconCopy').on('click', function () {
        var val = $(this).data('value');
        copy(val);
        $.toast("复制成功:" + val, "text");
    });
    var allow_recharge = true;
    //提交
    $('#submit').on('click', function () {
        if(!allow_recharge){
            return false;
        }
        var remark = $('.remarkForJs').val();
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
            $.toast("请选择金额", "forbidden");
            return;
        }
        //$.toast("金额" + val + "remark" + remark + "subBranch" + subBranch + "bank" + bank + "name" + name + "no" + no, "text");
        var _data = {
            truename:remark,
            price:val,
            pay_type: 5,
        };
        allow_recharge = false;
        //var index = layer.load();
        var _loading = $('#loadingNotTuochClose');
        _loading.removeClass('hidden');
        _loading.addClass('show');
        $.post(_payurl, _data, function (res) {
            console.log(res);
            //layer.close(index);
            if(res.status){
                $.toast(res.msg, "text");
                setTimeout(function(){
                    window.location.href = _recordurl;
                },1500);
            }else {
                _loading.removeClass('show');
                _loading.addClass('hidden');
                allow_recharge = true;
                $.toast(res.msg, "forbidden");
            }
        });
    });

    function copy(str) {
        var save = function save(e) {

            e.clipboardData.setData('text/plain', str); //clipboardData对象
            e.preventDefault(); //阻止默认行为
        };
        document.addEventListener('copy', save);
        return document.execCommand("copy"); //使文档处于可编辑状态，否则无效
    }
});
