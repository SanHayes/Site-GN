$(document).ready(function (e) {
    // setInterval("yonline()", 20000);
    // acc_data();
});

//用户在线
function yonline() {
    $.post(_online_url, {}, function (rs) {

    });
}
//账号 菜单下的数据
function acc_data() {
    $.post(_acc_data_url, {}, function (rs) {
        var ele = $('#acc_wrap'), _data = rs.data;
        ele.find('._a1').html(_data.money);
        ele.find('._a2').html(_data.invest_money);
        ele.find('._a3').html(_data.yesterday_money);
        ele.find('._a4').html(_data.today_money);
    });
}

let loadIndex = null;
//链接 点击
$('.forJsClick').on('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var url = $(this).data('url');
    if (url) {
        setTimeout(function () {
            window.location.href = url;
        }, 300)
    }
});

//链接 点击 跳出新窗口
$('.forJsClick003').on('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var url = $(this).data('url');
    if (url) {
        setTimeout(function () {
            window.open(url);
        }, 300)
    }
});
function showError(text) {
    layer.msg(text, { icon: 2 });

}
function showSuccess(text) {
    layer.msg(text, { icon: 1 });

}
function showLoading() {
    loadIndex = layer.load();

}
function hiddenLoading() {
    //关闭
    layer.close(loadIndex);
}

function showDialog(title, content) {
    $('#dialogTitle').html(title);
    $('#mcontent').html(content);
    $('.dialog').removeClass('hidden').addClass('show')

}
function closeDialog() {
    //$.post('/home/index/hide');
    $('#dialogTitle').html('');
    $('#mcontent').html('');
    $('.dialog').removeClass('show').addClass('hidden')
}
$('#dialogColseSure').on('click', function () {
    closeDialog()
});
$('.mark').on('click', function () {
    closeDialog()
});
$('.mark').on('wheel', function (e) {
    e.stopPropagation();
    e.preventDefault();
    return false

});
$('#dialogClose').on('click', function () {
    closeDialog()
});
