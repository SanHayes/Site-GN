"use strict";

var page = {
   init: function init() {
       window.setInterval("getdt()",1000*3);
        //公告弹窗
       // get_ad();
       // window.setInterval("get_ad()",1000*60);
   }
};
$(function () {
   page.init();
});

//获取列表数据
function getdt() {
    $.post(_index_url, {}, function (data) {
        var  pro = data;
        $.each(pro,function(k,vv){
            var _class1 = "procode_price_"+ vv.pid;
            var _class2 = "procode_status_name_"+ vv.pid;
            var _class3 = "procode_"+ vv.pid;
            var _class4 = "procode_high_"+ vv.pid;
            var _class5 = "procode_low_"+ vv.pid;
            var old_price = $("."+_class1).data('price');
            if(old_price*10 > vv.price*10){
                $("."+_class1).css('color', '#0BBA9F');
            }else if(old_price*10 < vv.price*10){
                $("."+_class1).css('color', '#E8506C');
            }
            $("."+_class1).html(vv.price);
            $("."+_class1).data('price',vv.price);
            $("."+_class2).html(vv.status_name);
            $("."+_class3).attr('data-status', vv.status);
            $("."+_class4).html(vv.high);
            $("."+_class5).html(vv.low);
        });
    });
}

/**
 * 公告弹窗 || 私信
 */
function get_ad() {
    $.post(_adurl, {}, function (rs) {
        if(rs.code != 0){
            return false;
        }
        var _data = rs.data;
        showDialog(_data.title, _data.content);
    });
}
