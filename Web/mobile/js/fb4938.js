'use strict';

$(document).ready(function (e) {
    $('#submit').on('click', function () {
        var money = $('#money').val();
        if (!money) {
            $.toast('请输入金额', "forbidden");
            return;
        }
        //$.toast('money:' + money, "text");
        var _data = {
            money: money,
            pid:_id
        };
        var _loading = $('#loadingNotTuochClose');
        _loading.removeClass('hidden');
        _loading.addClass('show');
        $.post(_investurl, _data, function (res) {
            if(res.type == 1){
                $.toast(res.data, "text");
                setTimeout(function(){
                    window.location.href = _detailurl;
                },1500);
            }else {
                _loading.removeClass('show');
                _loading.addClass('hidden');
                $.toast(res.data, "forbidden");
            }
        });
    });
});
