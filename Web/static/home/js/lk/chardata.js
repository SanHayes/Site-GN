



var selltime = 0;
option = null;
var order_data = {};
var timer = '';
var resorderlist = {};
var timer_orderlist= '';
var _sell_time = 0;
var _ftime = 0;



function getdata(pid) {
    var url = "/index/goods/ajaxpro/pid/"+pid;
    $.get(url,function(data){
        var old_price = $('#jk').html();
        if(old_price*10 < data.Price*10){
            $('#jk').removeClass('fall');
            $('#jk').addClass('rise');
        }else if(old_price*10 > data.Price*10){
            $('#jk').addClass('fall');
            $('#jk').removeClass('rise');
        }
        $('#jk').html(data.Price);
        newprice = data.Price;
    });
}


//选择时间
$('.period-widget').click(function(){
    $('.period-widget').removeClass('active');
    $(this).addClass('active');
    order_sen = $(this).attr('data-sen');    
});

//选择金额
$('.amount-box').click(function(){
    $('.amount-box').removeClass('active');
    $('.other-amount').removeClass('active');
    $(this).addClass('active');
    order_price = $(this).attr('data-price');
    $('#money').html(' ￥'+order_price);
    my_money = parseFloat($('.pay_mymoney').html());
    if(my_money < order_price){
        $('.no-money').removeClass('ng-hide');
    }else{
        $('.no-money').addClass('ng-hide');
    }
    $('.no-min').addClass('ng-hide');
    $('.no-max').addClass('ng-hide');
});

$('.other-amount').click(function(){
    $('.amount-box').removeClass('active');
    $(this).addClass('active');

});
$('.other-amount input').bind('input propertychange', function() {
    var inputdata = $('.other-amount input').val();
    if(inputdata*10 < order_min_price*10){
        $('.no-min').removeClass('ng-hide');
        $('.no-money').addClass('ng-hide');
        $('.button').attr('disabled','disabled');
    }else if(inputdata*10 > order_max_price*10){
        $('.no-max').removeClass('ng-hide');
        $('.no-money').addClass('ng-hide');
        $('.button').attr('disabled','disabled');
    }else{

        $('.no-min').addClass('ng-hide');
        $('.no-max').addClass('ng-hide');
        $('.button').removeAttr('disabled');
    }
    order_price = inputdata;
    $('#money').html(' ￥'+order_price);
   
});
function toggle_order_confirm_panel(type) {

  if(type == 'lookup'){
    var typename = '买涨';
    order_type = 0;
    
    $('.order_type').removeClass('fall');
    $('.order_type').addClass('rise');
  }else{
    var typename = '买跌';
    order_type = 1;

    $('.order_type').addClass('fall');
    $('.order_type').removeClass('rise');
  }

  $('.order_type').html(typename);
  $('.pro_mengban').addClass('glass_mask');
  $('.order-confirm-panel').addClass('open');
  

}

/**
 * 下单
 * @author lukui  2017-06-30
 * @return {[type]} [description]
 */
function addorder(){
    var profit = $('#profit').val();
    var loss = $('#loss').val();
    var postdata = "order_type="+order_type+"&order_pid="+order_pid+"&order_price="+order_price+"&order_sen="+order_sen+"&newprice="+newprice+"&profit="+profit+"&loss="+loss;
    var posturl = "/index/order/addorder";
    
    toggle_order_close_panel();

    $('.order_mengban').addClass('glass_mask');
    $('.paysuccess').hide();

    $('.order-state-panel').show();
    $('.order-state-panel .wait').removeClass('ng-hide');
    
    my_money = parseFloat($('.pay_mymoney').html());
    if(order_price > my_money){
        err_info('资金不足，请先充值');
        return;
    }
    if(order_price < order_min_price){
        err_info('最小下注金额为'+order_min_price);
        return;
    }
    if(order_price > order_max_price){
        err_info('最大下注金额为'+order_max_price);
        return;
    }


    $.post(posturl,postdata,function(resdata){
   
        if(resdata.type == 1){
            resdata.data = jQuery.parseJSON(Base64.decode(resdata.data));
            console.log(resdata.data);

            //倒计时
            var lefttime = resdata.data.selltime-resdata.data.buytime;
            $('.pay_order_sen').html(lefttime);
            $('.img_circle_right').attr('style','-webkit-animation: run '+lefttime+'s linear;')
            $('.img_circle_lift').attr('style','-webkit-animation: runaway '+lefttime+'s linear;')

            //下方提示
            if(resdata.data.ostyle == 0){
                $('.pay_order_type').html('买涨');
                $('.pay_order_type').addClass('rise');
                $('.pay_order_type').removeClass('fall');
            }else{
                $('.pay_order_type').html('买跌');
                $('.pay_order_type').addClass('fall');
                $('.pay_order_type').removeClass('rise');
            }
			$('.info_list .oid').val(resdata.data.oid);
            //$('.order-state-panel').hide();
            $('.order-state-panel .wait').addClass('ng-hide');

            $('.pay_order_price').html(resdata.data.fee);
            $('.pay_order_buypricee').html(resdata.data.buyprice);

            $('.order-state-panel .wait').addClass('ng-hide');
            $('.order-state-panel .paysuccess').removeClass('ng-hide');
            $('.order-state-panel .paysuccess').addClass('success');
            //余额
            $('.pay_mymoney').html(resdata.data.commission);
            //转盘倒计时
            
            
            selltime = resdata.data.selltime;
            order_data = resdata.data;
            $('.paysuccess').show();
            _sell_time = order_data.selltime - order_data.buytime;
            timer = setInterval("endtimes()",1000);
        }else{
            err_info(resdata.data);
        }
        
    });
}

// 手动平仓
function pcorder() {
	var oid = $('.info_list .oid').val();
	$.post('/index/order/pcorder','oid='+oid,function(resdata){
		if(resdata.type == 1){
			newprice = resdata.price;
			_sell_time = 1;
			//close_order();
			//get_this_order();
		}else{
			err_info(resdata.data);
			return;
		}
	});
}


//转盘倒计时
function endtimes() {    
    var timestamp = Date.parse(new Date());
    timestamp = timestamp / 1000;
    _sell_time--;
    var newsen = _sell_time;
    $('.pay_order_sen').html(newsen);

    var old_price = order_data.buyprice;
    var point = parseFloat(newprice)-parseFloat(old_price);
    var endprofit = parseFloat(order_data.endprofit), endloss = parseFloat(order_data.endloss);
    var yuce_case = 0;
    var proloss = 0;
    var limit = 0;
    if(point>0){
        $('.data-price').removeClass('fall');
        $('.data-price').addClass('rise');

        proloss = (point/order_data.point).toFixed(3);
        if(order_data.ostyle == 0){ //买涨
            proloss = proloss>endprofit ? endprofit : proloss;
            limit = proloss>=endprofit ? 1 : 0;
            yuce_case = '+'+(order_data.fee*proloss/100).toFixed(3);
            $('.yuce').removeClass('fall');
            $('.yuce').addClass('rise');
        }else{
            proloss = proloss>endloss ? endloss : proloss;
            limit = proloss>=endloss ? 1 : 0;
            yuce_case = '-'+(order_data.fee*proloss/100).toFixed(3);
            $('.yuce').removeClass('rise');
            $('.yuce').addClass('fall');
        }

    }else if(point<0){
        $('.data-price').addClass('fall');
        $('.data-price').removeClass('rise');

        proloss = (-point/order_data.point).toFixed(3);
        if(order_data.ostyle == 0){     //买涨
            proloss = proloss>endloss ? endloss : proloss;
            limit = proloss>=endloss ? 1 : 0;
            yuce_case = '-'+(order_data.fee*proloss/100).toFixed(3);
            $('.yuce').removeClass('rise');
            $('.yuce').addClass('fall');
        }else{
            proloss = proloss>endprofit ? endprofit : proloss;
            limit = proloss>=endprofit ? 1 : 0;
            yuce_case = '+'+(order_data.fee*proloss/100).toFixed(3);
            $('.yuce').removeClass('fall');
            $('.yuce').addClass('rise');
        }
    }else{
        yuce_case = 0;
    }

    $('.yuce').html('￥'+yuce_case);

    if(limit==1 && sys_limit==1){// 超过警戒线
        flash_order(order_data.oid);
        _sell_time = 0;
    }

    if(newsen <= 0){ 
        $('.paysuccess').addClass('ng-hide');
        $('.paysuccess').removeClass('success');
        $('.order-state-panel .wait').removeClass('ng-hide');
        //请求检测订单
        //get_this_order();
        clearInterval(timer)
        //停止ajax
        clearTimeout(ccout);
        //go order
        goorder();
     }
}

function goorder() {    
    var posturl = '/index/order/goorder';
    var postdata = 'price='+newprice+"&oid="+order_data.oid+"&order_rand="+order_data.order_rand;

    $.post(posturl,postdata,function(res){
        if(res == 1 || res == 3){
            get_this_order();
        }else if(res == 2){
            setTimeout('goorder()',100);
        }else{
			get_this_order();
		}
        
    });
}


function get_this_order() {
    var tourl = "/index/order/get_this_order/oid/"+order_data.oid;
    $.get(tourl,function(resdata){
        if(resdata && resdata!='W10='){
            resdata = jQuery.parseJSON(Base64.decode(resdata));
			$('.order-state-panel .wait').addClass('ng-hide');
            $('.ordersuccess').removeClass('ng-hide');
            $('.ordersuccess').addClass('success');            

            if(resdata.is_win == 1){
                $('.result_profit').addClass('rise')
                $('.result_profit').removeClass('fall')
                $('.result_profit').html('￥'+resdata.ploss);
                $('.endprice').addClass('rise')
                $('.endprice').removeClass('fall')
            }else if(resdata.is_win == 2){
                $('.result_profit').addClass('fall')
                $('.result_profit').removeClass('rise')
                $('.result_profit').html('￥'+resdata.ploss);
                $('.endprice').addClass('fall')
                $('.endprice').removeClass('rise')
            }else{
                $('.result_profit').removeClass('rise')
                $('.result_profit').removeClass('fall')
                $('.result_profit').html('￥'+resdata.ploss);
                $('.endprice').removeClass('rise')
                $('.endprice').removeClass('fall')
            }
            $('.endprice').html('￥'+resdata.sellprice);
            //余额
            $('.pay_mymoney').html(resdata.yue);
            ccout=setTimeout("getonedata()",300);
        }else{
            get_this_order();
        }
    });
}
/**
 * 继续下单
 * @author lukui  2017-06-30
 * @return {[type]} [description]
 */
function continue_order() {
    
    close_order();
    if(order_type == 0){
        var _type = 'lookup';
    }else{
        var _type = 'lookdown';
    }
    toggle_order_confirm_panel(_type);
}

/**
 * 关闭窗口
 * @author lukui  2017-06-30
 * @return {[type]} [description]
 */
function close_order() {
    clearInterval(timer)
    $('.order_mengban').removeClass('glass_mask');
    $('.order-state-panel').hide();
    $('.ordersuccess').removeClass('success');
    $('.ordersuccess').addClass('ng-hide');
    $('.order_fail').addClass('ng-hide');
    

}

/**
 * 持仓明细
 */
function toggle_history_order_panel() {
    var type =  $('.history-panel').attr('ng-include');
    if(type == 1){

      //ajax order
      var ajaxorderurl = "/index/order/ajaxorder/pid/"+order_pid;
      $.get(ajaxorderurl,function(resdata){

        resdata = jQuery.parseJSON(Base64.decode(resdata));

        resorderlist = resdata;
        
        if(resorderlist.length >= 1){
            _ftime = resorderlist[0]['time'];
        }else{
            var timestamp = Date.parse(new Date());
            _ftime = timestamp/1000;
        }

        timer_orderlist = setInterval("show_order_list()",1000);
            $('.history-panel').css('top','50%')
            $('.history-panel').css('bottom','10%')
            $('.history-panel').attr('ng-include',0);
      })

    }else{
      $('.history-panel').css('top','500%')
      $('.history-panel').css('bottom','100%')
      $('.history-panel').attr('ng-include',1);
      clearInterval(timer_orderlist)
    }


    
}
/**
 * 订单列表
 * @return {[type]} [description]
 */
function show_order_list() {
    var  html = '';
    
    if(resorderlist.length == 0){
        $('.trade_history_list ul').html(' ');
        return false;
    }
    
    _ftime++;
    $.each(resorderlist,function(k,v){        
        var timestamp = Date.parse(new Date());
        var  _end_time = (v.selltime - _ftime);
        var baifenbi = (_end_time/v.endprofit)*100;
        if(_end_time >0){            
            var chaprice = newprice-v.buyprice;
            var closeprice = 0;
            var closeprice_class = '';
            var proloss = 0;
            if(v.ostyle == 0){
                var ostyle_class = "buytop";
                var ostyle_class2 = 'in_money';
                var ostyle_name = "买涨";
                if(chaprice >0){
                    proloss = (chaprice/v.point).toFixed(3);
                    proloss = proloss>v.endprofit ? v.endprofit : proloss;
                    closeprice = '+'+(v.fee*v.proloss/100);
                    closeprice_class = 'in_money';
                }else if(chaprice <0){
                    proloss = (-chaprice/v.point).toFixed(3);
                    proloss = proloss>v.endloss ? v.endloss : proloss;
                    closeprice = '-'+(v.fee*v.proloss/100);
                    closeprice_class = 'out_money';
                }else{
                    closeprice = v.fee;
                    closeprice_class = '';
                }
            }else{
                var ostyle_class = "buydown";
                var ostyle_name = "买跌";
                var ostyle_class2 = 'out_money';
                if(chaprice <0){
                    proloss = (-chaprice/v.point).toFixed(3);
                    proloss = proloss>v.endprofit ? v.endprofit : proloss;
                    closeprice = '+'+(v.fee*v.endprofit/100);
                    closeprice_class = 'in_money';
                }else if(chaprice >0){
                    proloss = (chaprice/v.point).toFixed(3);
                    proloss = proloss>v.endloss ? v.endloss : proloss;
                    closeprice = '-'+(v.fee*v.endloss/100);
                    closeprice_class = 'out_money';
                }else{
                    closeprice = v.fee;
                    closeprice_class = '';
                }
            }

            html += '<li ng-repeat="o in trade_order_list" class="">\
                        <section>\
                            <p style="margin: 0">\
                                <span class="ng-binding">'+v.ptitle+'</span>\
                                <span class="ng-binding '+ostyle_class2+'"><i class="'+ostyle_class+'"></i>'+ostyle_name+'（￥'+v.fee+'）</span>\
                            </p>\
                            <p style="margin: 0" class="ng-binding">\
                                '+v.buyprice+'-<span  class="ng-binding '+closeprice_class+'">'+newprice+'</span>\
                            </p>\
                            <p style="margin: 0" class="ng-binding">'+getLocalTime(v.buytime)+'</p>\
                        </section><section>\
                            <p style="margin: 0px;" class="ng-binding '+closeprice_class+'">'+closeprice+'</p>\
                            <p style="margin: 0" class="ng-binding">'+formatSeconds2(_end_time)+'</p>\
                        </section>\
                        <article class="">\
                        <span class="move_width" style="width: '+baifenbi+'%; transition-duration: 1s;">\
                        </span>\
                        <i>\
                            <em></em>\
                        </i>\
                        </article>\
                    </li>';            
            $('.trade_history_list ul').html(html);
        }else{
            resorderlist.splice(k,1);
        }
    })
}

/**
 * 订单错误提示
 * @param  {[type]} data 错误信息
 * @return {[type]}      [description]
 */
function err_info(data) {
    $('.order-state-panel .paysuccess').addClass('ng-hide');
    $('.order-state-panel .paysuccess').removeClass('success');
    $('.order-state-panel .ordersuccess').addClass('ng-hide');
    $('.order-state-panel .ordersuccess').removeClass('success');
    
    $('.order-state-panel .wait').addClass('ng-hide');
    $('.fail-info').html(data);
    $('.order_fail').removeClass('ng-hide');
}

function flash_order(oid) {
    var flag = false;
    $.get("/index/api/order", {oid:oid}, function(res) {
        flag = res;
        return flag;
    });
}