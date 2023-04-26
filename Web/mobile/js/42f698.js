'use strict';

$(document).ready(function (e) {
    var imgs = $('#imgSwriper').find('.swiper-slide').length;
    var flag = true;
    if (imgs == 1) {
        flag = false;
    } else {
        flag = true;
    }
    // 图片轮播
    var mySwiper = new Swiper('#imgSwriper', {
        // direction: 'vertical', // 垂直切换选项
        loop: true, // 循环模式选项
        autoplay: flag,
        // 如果需要分页器
        pagination: {
            el: '.swiper-pagination'
        }
    });
    var infos = $('#infoPanel').find('.swiper-slide').length;
    var infoFlag = true;
    if (infos == 1) {
        infoFlag = false;
    } else {
        infoFlag = true;
    }
    //信息面板轮播
    var mySwiper2 = new Swiper('#infoPanel', {
        // direction: 'vertical', // 垂直切换选项
        loop: true, // 循环模式选项
        autoplay: infoFlag
        // 如果需要分页器
        // pagination: {
        //   el: '.swiper-pagination',
        // },

    });


  //弹窗关闭
  $('.close').on('click', function (e) {
      hideShow();
    $("#dialog").addClass('hidden');
  });

  $('.glass').on('click', function (e) {
      //hideShow();
    $("#dialog").addClass('hidden');
  });

  $('.dfonter').on('click', function (e) {
      hideShow();
    $("#dialog").addClass('hidden');
  });

  //公告弹窗
  //   get_ad();
  //   window.setInterval("get_ad()",1000*60);

    window.setInterval("getdt()",5000);
});

function hideShow() {
    $.post('99/index/index/hide');
}

/**
 * 公告弹窗 || 私信
 */
// function get_ad() {
//     $.post(_adurl, {}, function (rs) {
//         if(rs.code != 0){
//           return false;
//         }
//         var _data = rs.data;
//         $('#dialog .p_ad_tl').text(_data.title);
//         $('#dialog .p_ad_ct').html(_data.content);
//         $("#dialog").removeClass('hidden');
//
//     });
// }

//获取列表数据
function getdt() {
    $.post(_index_url, {}, function (data) {
        var  pro = JSON.parse(data);
        
        $.each(pro,function(k,vv){
            var _class1 = "procode_price_"+ vv.pid;
            var _class2 = "procode_status_name_"+ vv.pid;
            var _class3 = "procode_"+ vv.pid;
            //console.log(_class3,vv.status);
            var old_price = $("."+_class1).data('price');
            //console.log(old_price);
            if(old_price*10 < vv.price*10){
                $("."+_class1).css('background-color', '#e9506c');//涨
            }else if(old_price*10 > vv.price*10){
                $("."+_class1).css('background-color', '#02ac8f');//跌
            }
            //console.log($("."+_class1).html(), vv.price);
            $("."+_class1).html(vv.price);
            $("."+_class1).data('price',vv.price);
            $("."+_class2).html(vv.is_rise == 1 ? '交易中' : '休市');
            $("."+_class3).attr('data-status', vv.status);
        });
    });
}
