/**
 * 持仓&历史明细
 */
var resorderlist = {};
var proprice = {};
var page = 1;
var ispage = 1;
var is_ajax_list = 0;
var timer_get_price = '';
var timer_orderlist = '';
var listionhajax = '';

hold_order_list();
change_category(0);
var _sell_time = 0;
var _ftime = 0;
var html_type = 1;


/**
 * 订单列表
 * @author lukui  2017-07-01
 * @return {[type]} [description]
 */
function hold_order_list() {
    var url = "/index/order/ajaxorder_list";
    $.get(url,function(resdata){
        if(resdata){
             resdata = jQuery.parseJSON(Base64.decode(resdata));
        }
        resorderlist = resdata;
       if(resorderlist){
             if(resorderlist.length >= 1){
                _ftime = resorderlist[0]['time'];
            }else{
                var timestamp = Date.parse(new Date());
                _ftime = timestamp/1000;
            }
            timer_get_price = setInterval("get_price()",1000);
            timer_orderlist = setInterval("show_order_list()",1000);
        }
    })
}


function get_price() {    
    var url = "/index/order/get_price";
    $.get(url,function(resdata){
        if(!resdata){
            proprice = '';
        }else{
            proprice = jQuery.parseJSON(Base64.decode(resdata));
        }        
    })
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
        if(typeof(v.selltime)=="undefined"){v.selltime = 0}
        var  _end_time = (v.selltime*1 - _ftime*1);
        var baifenbi = (_end_time/(v.selltime-v.buytime))*100;
        var newprice = proprice[v.pid];
        var limit = 0;
        var endprofit = parseFloat(v.endprofit), endloss = parseFloat(v.endloss);
        if(!_end_time){
            _end_time = 0;
        }
        
        if(_end_time >= 0){            
            var chaprice = newprice-v.buyprice;
            var closeprice = 0;
            var closeprice_class = '';
            if(v.ostyle == 0){
                var ostyle_class = "buytop";
                var ostyle_class2 = 'in_money';
                var ostyle_name = "买涨";
                if(chaprice >0){
                    proloss = (chaprice/v.point).toFixed(3);
                    limit = proloss>endprofit ? 1 : 0;
                    proloss = proloss>endprofit ? endprofit : proloss;
                    closeprice = '+'+(v.fee*proloss/100).toFixed(3);
                    closeprice_class = 'in_money';
                }else if(chaprice <0){
                    proloss = (-chaprice/v.point).toFixed(3);
                    limit = proloss>endloss ? 1 : 0;
                    proloss = proloss>endloss ? endloss : proloss;
                    closeprice = '-'+(v.fee*proloss/100).toFixed(3);
                    closeprice_class = 'out_money';
                }else{
                    closeprice = 0;
                    closeprice_class = '';
                }
            }else{
                var ostyle_class = "buydown";
                var ostyle_name = "买跌";
                var ostyle_class2 = 'out_money';
                if(chaprice <0){
                    proloss = (-chaprice/v.point).toFixed(3);
                    limit = proloss>endprofit ? 1 : 0;
                    proloss = proloss>endprofit ? endprofit : proloss;
                    closeprice = '+'+(v.fee*proloss/100).toFixed(3);
                    closeprice_class = 'in_money';
                }else if(chaprice >0){
                    proloss = (chaprice/v.point).toFixed(3);
                    limit = proloss>endloss ? 1 : 0;
                    proloss = proloss>endloss ? endloss : proloss;
                    closeprice = '-'+(v.fee*proloss/100).toFixed(3);
                    closeprice_class = 'out_money';
                }else{
                    closeprice = 0;
                    closeprice_class = '';
                }
            }

            html += '<li ng-repeat="o" class="">\
                        <section>\
                            <p style="margin: 0">\
                                <span class="ng-binding">'+v.ptitle+'</span>\
                                <span class="ng-binding '+ostyle_class2+'"><i class="'+ostyle_class+'"></i>'+ostyle_name+'</span>\
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
            
            $('.trade_history_list .slider-left ul').html(html);
            if(limit==1 && sys_limit==1){// 超过警戒线
                flash_order(v.oid);
                _ftime = v.selltime;
            }
        }else{
            resorderlist.splice(k,1);
        }
    })
}

/**
 * 切换按钮
 * @param  {[type]} type [description]
 * @return {[type]}      [description]
 */
function change_category(type){

  $('.slider-right').css('transition-duration','300ms');
  $('.slider-left').css('transition-duration','300ms');
  if(type == 0){
    page = 1;
    $('.uls').html(' ');
    $('.slider-left').css('transform','translate(0px, 0px) translateZ(0px)');
    $('.slider-right').css('transform','translate(100%, 0px) translateZ(0px)');
    $('.left-table').addClass('active');
    $('.right-table').removeClass('active');
  }

  if(type == 1){

    clearInterval(timer_get_price);
    clearInterval(timer_orderlist);
    listionhajax = setInterval("listionh()",1000);
    is_ajax_list = 0;
    orderedlist();
    $('.slider-left').css('transform','translate(-100%, 0px) translateZ(0px)');
    $('.slider-right').css('transform','translate(0px, 0px) translateZ(0px)');
    $('.right-table').addClass('active');
    $('.left-table').removeClass('active');
  }

}



function orderedlist() {
    if(ispage != 1){
        return;
    }

    setolist(html_type);
    html_type = 1;
}



function setolist(types) {
    var url = "/index/order/orderlist?page="+page;
    var html = '';
    if(is_ajax_list == 1){
        return ;
    }
    is_ajax_list = 1;
    $.get(url,function(resdata){
        
        resdata = jQuery.parseJSON(Base64.decode(resdata));

        var res_list = resdata.data;
        if(res_list.length == 0){
            clearInterval(listionhajax);
            is_ajax_list = 1;
            return;
        }
        $.each(res_list,function(k,v){
            var closeprice = 0;
            var closeprice_class = '';

            if(v.ostyle == 0){
                var ostyle_class = "buytop";
                var ostyle_name = "买涨";
            }else{
                var ostyle_class = "buydown";
                var ostyle_name = "买跌";
            }

            if(v.is_win == 1){
                closeprice = '+'+v.ploss;
                closeprice_class = 'in_money';
            }else if(v.is_win == 2){
                closeprice = v.ploss;
                closeprice_class = 'out_money';
            }else{
                closeprice = 0;
                closeprice_class = '';
            }

            html += '<li ng-repeat="o" onclick="get_hold_order('+v.oid+')" >\
                        <section>\
                            <p>\
                                <span class="ng-binding">'+v.ptitle+'</span>\
                                <span  class="ng-binding '+closeprice_class+'">\
                                <i  class="'+ostyle_class+'"></i>'+ostyle_name+'</span>\
                            </p>\
                            <p class="ng-binding">\
                                '+v.buyprice+'-<span  class="ng-binding '+closeprice_class+'">'+v.sellprice+'</span>\
                            </p>\
                            <p class="ng-binding">'+getLocalTime(v.buytime)+'</p>\
                        </section><section>\
                            <p class="ng-binding '+closeprice_class+'">'+closeprice+'</p>\
                            <p class="ng-binding">'+getLocalTime(v.selltime)+'</p>\
                        </section>\
                    </li>';        
        })
        if(types == 0){
            $('.trade_history_list .slider-right .uls').html(html);
        }else{
            $('.trade_history_list .slider-right .uls').append(html);
        }
        html = '';
        page++;
        is_ajax_list = 0;

    })

}


listionhajax = setInterval("listionh()",1000);

/**
 * 监听高度
 * @author lukui  2017-07-05
 * @return {[type]} [description]
 */
function listionh() {
    if($(".uls li:last").attr('ng-repeat')){
        var ScrollTop = $(".uls li:last").offset().top; 

        if(ScrollTop <1000 ){
            setolist(1);
        }
    }
    
}

function itemback(){
    $('.modal-backdrop').addClass('ng-hide');
    $('.modal-backdrop').removeClass('active');
    $('.tab-nav').show();
}

function get_hold_order(oid) {
    var url = "/index/order/get_hold_order/oid/"+oid;
    $.get(url,function(data){
        data = jQuery.parseJSON(Base64.decode(data));
        var fudong = (parseFloat(data.sellprice)-parseFloat(data.buyprice)).toFixed(3);
        var ptime = parseFloat(data.selltime)-parseFloat(data.buytime);
        var direction = data.ostyle==0 ? '买涨' : '买跌';
        $('.order-modal-content .ptitle').html(data.ptitle);
        $('.order-modal-content .direction').html(direction+' / '+ptime+'秒');
        $('.order-modal-content .buyprice').html(data.buyprice);
        $('.order-modal-content .sellprice').html(data.sellprice);
        $('.order-modal-content .fudong').html(fudong);
        $('.order-modal-content .ploss').html(data.ploss+' / '+data.sx_fee);
        $('.order-modal-content .yingsun').html(data.endprofit+'% / '+data.endloss+'%');
        $('.order-modal-content .buytime').html(getLocalTime(data.buytime));
        $('.order-modal-content .selltime').html(getLocalTime(data.selltime));
        $('.order-modal-content .sx_fee').html(data.sx_fee);
        if(data.ploss < 0){
            $('.order-modal-content .ploss').addClass('fall');
            $('.order-modal-content .ploss').removeClass('rise');
        }else{
            $('.order-modal-content .ploss').removeClass('fall');
            $('.order-modal-content .ploss').addClass('rise');
        }
        $('.modal-backdrop').removeClass('ng-hide');
        $('.modal-backdrop').addClass('active');
        $('.tab-nav').hide();
    })
}

function close_order_modal() {
    $('.modal-backdrop').addClass('ng-hide');
    $('.modal-backdrop').removeClass('active');
    $('.tab-nav').show();
}


function flash_order(oid) {
    var flag = false;
    $.get("/index/api/order", {oid:oid}, function(res) {
        flag = res;
        return flag;
    });
}