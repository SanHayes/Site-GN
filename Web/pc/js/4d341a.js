// 'use strict';
function accMul(arg1, arg2) {
    var m = 0, s1 = arg1.toString(), s2 = arg2.toString();
    try {
        m += s1.split(".")[1].length;
    }
    catch (e) {
    }
    try {
        m += s2.split(".")[1].length;
    }
    catch (e) {
    }
    return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m);
}
var Base64 = new Base64(), ctype = "k", interval = "1";
var minQiya, maxQiya, ccout, cldata, data0, K2line;
var obj={
    start:0,
    end:100
};
var timer = null;
var second = 0;

var page = {
  init: function init() {

    //nav切换-k线切换
    $('.kItem').on('click', function (e) {
      e.stopPropagation();
      var $this = $(this);
      var index = $this.data('index');
        if(index == 'stock'){
            ctype = 'k';
        }else {
            ctype = '1';
        }
      $this.siblings().removeClass('on');
      $this.addClass('on');
      getMaindata(ctype);
    });
    //nav切换
    $('.optionItem').on('click', function (e) {
      e.stopPropagation();
      var $this = $(this);
      var index = $this.data('index');
      Vtype = index;
      $this.siblings().removeClass('on');
      $this.addClass('on');
      getMaindata(ctype);
    });
    //切换买入/卖出
    $('.navBtn').on('click', function (e) {
      var $this = $(this);
      var type = $this.data('type');
      $('.dealContent').attr('type', type);
      if (type == 2) {
          order_type = 0;
        $('#buyType').html('买涨');
      } else {
          order_type = 1;
        $('#buyType').html('买跌');
      }
    });

    $('#inputMoney').on('keyup', function (e) {
      var val = $(this).val(), _vlp = accMul(val, web_poundage).toFixed(2);
      $('#showMoney').html(val);
        // $('.charge .vlp').html(_vlp);
    });
    $('#inputMoney').on('focus', function () {
      $('.jsForMoneyItem').removeClass('on');
      $('#showMoney').html('0');
      // $('.charge .vlp').html('0');
    });
    //time-item
    $('.jsForTimeItem').on('click', function () {
      var $this = $(this);
      $('.jsForTimeItem').removeClass('on');
      $this.addClass('on');
    });
    //钱item
    $('.jsForMoneyItem').on('click', function () {
      var $this = $(this);
      $('.jsForMoneyItem').removeClass('on');
      $this.addClass('on');
      $('#inputMoney').val();
      var val = $.trim($(this).attr('data-value')), _vlp = accMul(val, web_poundage).toFixed(2);
      $('#showMoney').html(val);
      // $('.charge .vlp').html(_vlp);
    });
    //提交
    $('#submit').on('click', function () {
      var val = null;
      var time = null;
      var order_shouyi = null;
      var order_price = null;
      $('.jsForTimeItem').each(function (item) {
        if ($(this).hasClass('on')) {
          time = $.trim($(this).data('value'));
          order_shouyi = $.trim($(this).data('shouyi'))
        }
      });
      $('.jsForMoneyItem').each(function (item) {
        if ($(this).hasClass('on')) {
          val = $.trim($(this).attr('data-value'));
        }
      });
      if (!val) {
        val = $.trim($('#inputMoney').val());
      }
      if (!val) {
        showError("请选择金额");
        return;
      }
      order_price = val;
      if(order_price > my_money){
          showError("资金不足，请先充值");
          return;
      }
      if(order_price < order_min_price){
          showError('最小下注金额为'+order_min_price);
          return;
      }
      if(order_price > order_max_price){
          showError('最大下注金额为'+order_max_price);
          return;
      }
      var postdata = {
          newprice:newprice,
          order_pid:procode,
          order_type:order_type,
          order_price:order_price,
          order_sen:time,
          order_shouyi:order_shouyi,
          profit: 10,
          loss: 10,
      };
      setTimeout(function () {
        hiddenLoading();
      }, 3000);
      showLoading();
      $.post(_addorderurl, postdata,function(resdata){
          hiddenLoading();
          if(resdata.type == 1){
              resdata.data = jQuery.parseJSON(Base64.decode(resdata.data));
              showSuccess('交易成功');
              $('.allmoney').attr('data-value', 0);
              $('.pay_mymoney').html($('.pay_mymoney').html() - resdata.data.fee);
              $('.jsForMoneyItem').attr('data-value', $('.pay_mymoney').html());
          }else{
              showError(resdata.data);
          }
      });
    });
  }
};
$(function () {
  page.init();
});
// window.setInterval("getdt()",1000*3);
//获取列表数据
function getdt() {
    $.post(_detail_url, {}, function (data) {
        var  pro = data.data;
        $.each(pro,function(k,vv){
            var _class1 = "procode_price_"+ vv.procode;
            var _class2 = "procode_status_name_"+ vv.procode;
            var _class3 = "procode_"+ vv.procode;
            var old_price = $("."+_class1).data('price');
            if(old_price*10 < vv.price*10){
                $("."+_class1).css('color', '#0BBA9F');//涨
            }else if(old_price*10 > vv.price*10){
                $("."+_class1).css('color', '#E8506C');//跌
            }
            $("."+_class1).html(vv.price);
            $("."+_class2).html(vv.status_name);
            $("."+_class3).attr('data-status', vv.status);
        });
    });
}

function mcountDown(maxtime, fn) {
    timer = setInterval(function () {
        if (!!maxtime) {
            var msg = '';
            var day = Math.floor(maxtime / 86400),
                hour = Math.floor(maxtime % 86400 / 3600),
                minutes = Math.floor(maxtime % 3600 / 60),
                seconds = Math.floor(maxtime % 60);
            if (hour < 10) {
                hour = '0' + hour;
            }
            if (minutes < 10) {
                minutes = '0' + minutes;
            }
            if (seconds < 10) {
                seconds = '0' + seconds;
            }
            msg = hour + ":" + minutes + ":" + seconds;
            fn(msg);
            --maxtime;
        } else {
            clearInterval(timer);
            fn("结束!");
        }
    }, 1000);
}
$(window).resize(function (e) {
    autoHeight();
});
autoHeight();
//自动高度
function autoHeight() {
    var detailTopPart = $(".detailTopPart").height();
    var nav = $(".nav-wrap").height();
    var WinHss = $(window).height();
    $("#ecKx").height(WinHss - detailTopPart - nav - 70);
}
//倒计时
countDown(555);
function countDown(no) {
    second = no;
    timer = setInterval(function () {
        second--;
        $('#no').html(second);
    }, 1000);
}


ccout=setTimeout("getonedata()",1000);
getMaindata(ctype);
//实时更新面板信息
getdata();
if (isopen == 1) {
    setInterval('getdata()', 5000);
}
//5分钟刷新页面
setInterval("window.location.reload();",1000*60*5);

function getMaindata(ctype) {
    switch (Vtype){
        case "1M":
            interval="1";
            break;
        case "5M":
            interval="5";
            break;
        case "15M":
            interval="15";
            break;
        case "30M":
            interval="30";
            break;
        case "1H":
            interval="60";
            break;
        case "1D":
            interval="d";
            break;
        default:return false;
    }
    //ajax
    clearTimeout(ccout);
    $.ajax({
        url:kdata_url,
        type: "get",
        dataType: "json",
        async:true,
        data:{
            "pid":procode,
            "num":60,
            "interval":interval
        },
        success: function(_jdatadata) {
            var jdatadata = jQuery.parseJSON(Base64.decode(_jdatadata));
            localStorage.setItem("data", '');
            localStorage.setItem("data", JSON.stringify(jdatadata));
            gotoecharts(jdatadata);
            if (isopen == 1) {
                ccout=setTimeout("getMaindata()",5000);
            }
            // getonedata();
            minQiya = jdatadata.items[jdatadata.items.length-1][2];
            maxQiya = jdatadata.items[jdatadata.items.length-1][3];
        },
        error:function(data){

        }
    })
}

var build_diff_data = function (m_short, m_long, data) {
    var result = [];
    var pre_emashort = 0;
    var pre_emalong = 0;
    for (var i = 0, len = data.length; i < len; i++) {
        var ema_short = data[i][1];
        var ema_long = data[i][1];

        if (i != 0) {
            ema_short = (1.0 / m_short) * data[i][1] + (1 - 1.0 / m_short) * pre_emashort;
            ema_long = (1.0 / m_long) * data[i][1] + (1 - 1.0 / m_long) * pre_emalong;
        }

        pre_emashort = ema_short;
        pre_emalong = ema_long;
        var diff = ema_short - ema_long;

        result.push(diff);
    }

    return result;
}

var build_dea_data = function (m, diff) {
    var result = [];
    var pre_ema_diff = 0;
    for (var i = 0, len = diff.length; i < len; i++) {
        var ema_diff = diff[i];

        if (i != 0) {
            ema_diff = (1.0 / m) * diff[i] + (1 - 1.0 / m) * pre_ema_diff;
        }
        pre_ema_diff = ema_diff;

        result.push(ema_diff);
    }

    return result;
}

var build_macd_data = function (data, diff, dea) {
    var result = [];

    for (var i = 0, len = data.length; i < len; i++) {
        var macd = 2 * (diff[i] - dea[i]);
        result.push(macd);
    }

    return result;
}

//时间戳转成时：分：00形式   1700以来的秒数（非毫秒）
function getDateHM(tm){
    var NWh, NWm;
    NWh=new Date(parseInt(tm) * 1000).getHours(tm);
    NWm=new Date(parseInt(tm) * 1000).getMinutes(tm);
    if(NWh<10){
        NWh="0"+NWh;
    }
    if(NWm<10){
        NWm="0"+NWm;
    }
    var tt=NWh+":"+NWm
    return tt;
}

function splitData(rawData) {
    var categoryData = [];
    var values = []
    for (var i = 0; i < rawData.length; i++) {
        categoryData.push(getDateHM(rawData[i].splice(0, 1)[0]));
        values.push(rawData[i])
    }
    return {
        categoryData: categoryData,
        values: values
    };
}

function kTl(KDS){
    K2line=new Array();
    for(p=0;p<KDS.length;p++){
        K2line.push(KDS[p][3])
        if (p == KDS.length-1) {
            K2line[p] = KDS[p][1];
        }
    }
    //alert(JSON.stringify(K2line))
    return K2line;
}

function calculateMA(dayCount) {
    var result = [];
    for (var i = 0, len = data0.values.length; i < len; i++) {
        if (i < dayCount) {
            result.push('-');
            continue;
        }
        var sum = 0;
        for (var j = 0; j < dayCount; j++) {
            sum += Number(data0.values[i - j][1]);
        }
        result.push(sum / dayCount);
    }
    //alert(result)
    return result;
}


//加载走势图
function gotoecharts(data){
    var ecKxId = document.getElementById("ecKx");
    var ecKx = echarts.getInstanceByDom(ecKxId);
    if (ecKx === undefined) {
        ecKx = echarts.init(ecKxId);
    }

    cldata=data.topdata.now;

    $("#container .txt1 span.a").text(getDateHM(data.topdata.topdata)+":00");
    $("#container .txt1 span.b").text(data.topdata.now);
    $("#container .txt1 span.c").text(data.topdata.open);
    $("#container .txt1 span.d").text(data.topdata.lowest);
    $("#container .txt1 span.e").text(data.topdata.highest);



    var diff =build_diff_data(12, 26, data.items);
    var dea =build_dea_data(9, diff);
    var macd =build_macd_data(data.items, diff, dea);
    var diffL=diff.length-1;
    var deaL=dea.length-1;
    var macdL=macd.length-1;
    $("#container .txt2 span.a i").text(diff[diffL].toFixed(4));
    $("#container .txt2 span.b i").text(dea[deaL].toFixed(4));
    $("#container .txt2 span.c i").text(macd[macdL].toFixed(4));

    data0 = splitData(data.items);
    var ecKdata = {
        //animation: false,
        backgroundColor: 'rgb(25, 25, 26)',
        legend: {
            show: false,
        },
        tooltip: {
            show: false,
        },
        grid: [
            {
                top: 5+'%',
                bottom: 30+'%',
                left: 4+'%',
                right: 0+'%',
                height: 55+'%',
                containLabel:true,
            },
            {
                top: 75+'%',
                bottom: 0+'%',
                left: 2+'%',
                right: 0+'%',
                height: 25+'%',
                containLabel:true,
            },
        ],
        xAxis: [
            {
                gridIndex: 0,
                type: 'category',
                data: data0.categoryData,
                axisLine: {
                    show: false,
                },
                axisTick: {
                    show: false,
                },
                /*axisLabel: {
                    textStyle: { color: 'rgb(100, 100, 100)' },
                    formatter: function (value, index) {
                        if (value>0) {
                            var time = value.split(" ")[0];
                            var split = time.split("-");
                            return split[1] + "/" + split[2];
                        }
                        else {
                            var time = value.split(" ")[1];
                            var split = time.split(":");
                            return split[0] + ":" + split[1];
                        }
                    }
                },*/

            },
            {
                gridIndex: 1,
                type: 'category',
                data: data0.categoryData,
                axisLine: {
                    show: false,
                },
                axisTick: {
                    show: false,
                },
                axisLabel: {
                    show: false,
                },
            },
        ],
        yAxis: [
            {
                gridIndex: 0,
                position: "right",
                scale: true,
                axisLabel: {
                    textStyle: { color: 'rgb(100, 100, 100)' },//右边竖下来文字颜色
                    formatter: function (value, index) {
                        return value.toFixed(5);
                    }
                },
                axisLine: {
                    show: false,
                },
                axisTick: {
                    show: false,
                },
                splitLine: {
                    show: true,
                    lineStyle: {
                        color: 'rgb(35, 34, 38)',//走势横线的颜色
                    }
                }
            },
            {
                gridIndex: 1,
                position: "right",
                scale: true,

                axisLabel: {
                    textStyle: { color: 'rgb(100, 100, 100)' },//右下角竖下来文字颜色
                    formatter: function (value, index) {
                        if (value >= 0) {
                            return "+" + value.toFixed(4);
                        }
                        return value.toFixed(4);
                    }
                },
                axisLine: {
                    show: false,
                },
                axisTick: {
                    show: false,
                },
                splitLine: { show: false }
            },
        ],
        dataZoom: [
            {
                xAxisIndex: [0, 1],
                type : 'inside' },
        ],
        series: [
            {
                name: 'line',
                type: 'line',
                xAxisIndex: 0,
                yAxisIndex: 0,
                data: data0.categoryData,
                showSymbol: false,
                lineStyle: {
                    normal: {
                        width: 1,
                        color: 'rgb(253, 209, 42)',
                    }
                },
                animationDuration:0,


                markPoint: {
                    symbol: "rect",
                    animation: false,
                    symbolSize: [60, 18],
                    symbolOffset: [-20, 0],
                    animationDuration:0,
                    data: [
                        {
                            name: '最新价',
                            x: '100%',
                            yAxis: data.topdata.now,
                            value: data.topdata.now,
                            label: {
                                normal: {
                                    show:true,
                                    position: [ 1, 4],
                                    textStyle: {
                                        color: "#FFf",//最新价跳动价格
                                    }
                                }
                            },
                            formatter: function (value, index) {
                                return value.toFixed(5);
                            },
                        }
                    ]
                },
                markLine: {//跳动的横线
                    symbolSize: 0,
                    animationDuration:0,
                    symbol:'',
                    label: {
                        normal: {
                            show: false,
                        }
                    },
                    lineStyle: {
                        normal: {
                            type: 'dashed',
                            width: 0,// 1
                        },
                    },
                    data: [{yAxis:data.topdata.now},]
                },
            },
            {
                name: 'stick',
                xAxisIndex: 0,
                yAxisIndex: 0,
                type: 'candlestick',
                data: data0.values,
                animationDuration:0,
                itemStyle: {
                    normal: {
                        color: '#E8506C',//上升蜡烛 25, 25, 26  #1aad19  #E8506C
                        color0: '#0BBA9F',//下降蜡烛 19, 233, 236  #e64340  #0BBA9F
                        borderColor: '#E8506C',//上升蜡烛边框 250, 46, 66
                        borderColor0: '#0BBA9F',//下降蜡烛边框 19, 233, 236
                        barGap:'100%',
                    }
                }
            },
            {
                name: 'ma5',
                type: 'line',
                xAxisIndex: 0,
                yAxisIndex: 0,
                data: calculateMA(5),
                smooth: true,
                showSymbol: false,
                animationDuration:0,
                lineStyle: {
                    normal: {
                        width: 1
                    }
                }
            },
            {
                name: 'ma10',
                type: 'line',
                xAxisIndex: 0,
                yAxisIndex: 0,
                data: calculateMA(10),
                smooth: true,
                showSymbol: false,
                animationDuration:0,
                lineStyle: {
                    normal: {
                        width: 1,
                        color: '#86da2b'
                    }
                }
            },
            {
                name: 'ma20',
                type: 'line',
                xAxisIndex: 0,
                yAxisIndex: 0,
                data: calculateMA(20),
                smooth: true,
                showSymbol: false,
                animationDuration:0,
                lineStyle: {
                    normal: {
                        width: 1,
                        color: '#ff5382'
                    }
                }
            },
            {
                name: 'ma30',
                type: 'line',
                xAxisIndex: 0,
                yAxisIndex: 0,
                data: calculateMA(30),
                smooth: true,
                showSymbol: false,
                animationDuration:0,
                lineStyle: {
                    normal: {
                        width: 1,
                        color: '#3d8ef6'
                    }
                }
            },
            {
                name: 'diff',
                type: 'line',
                data: diff,
                smooth: true,
                showSymbol: false,
                xAxisIndex: 1,
                yAxisIndex: 1,
                animationDuration:0,
                lineStyle: {
                    normal: {
                        width: 1,
                        color: '#00ffff'
                    }
                }
            },
            {
                name: 'dea',
                type: 'line',
                data: dea,
                smooth: true,
                showSymbol: false,
                xAxisIndex: 1,
                yAxisIndex: 1,
                animationDuration:0,
                lineStyle: {
                    normal: {
                        width: 1,
                        color: '#fe337f'
                    }
                }
            },
            {
                name: 'macd',
                type: 'bar',
                xAxisIndex: 1,
                yAxisIndex: 1,
                animationDuration:0,
                itemStyle: {
                    normal: {
                        color: '#0BBA9F',//下面的交易量树形图
                        borderColor: 'black',
                    }
                },
                data: macd,
            },
        ]
    };
    var ecKdata2 = {
        backgroundColor: 'rgb(25, 25, 26)',
        legend: {
            //data: ['日K', 'MA5', 'MA10', 'MA20', 'MA30']
        },
        /*
        tooltip: {
            trigger: 'axis'
        },
*/
        grid: [
            {
                left: 20,
                right:70,
                top:'5%',
                bottom:180
            },
            {
                left: 20,
                right:70,
                bottom:60,
                height: 60
            }
        ],
        xAxis:[{
            type: 'category',
            data: data0.categoryData,
            scale: true,
            boundaryGap : true,
            splitLine: {show: false},
            axisTick: {show: false},
            splitLine: {show: false},
            axisLine:{
                show:false,
                lineStyle:{
                    color:'#5f5f5f'
                }
            },
            min: 'dataMin',
            max: 'dataMax',
            //show:false
        },
            {   gridIndex: 1,
                type: 'category',
                data: data0.categoryData,
                scale: true,
                boundaryGap : true,
                //axisLine: {onZero: false},
                axisTick: {show: false},
                splitLine: {show: false},
                axisLabel: {show: false},
                min: 'dataMin',
                max: 'dataMax',
                show:false
            }
        ],
        yAxis:[
            {   type:'value',
                position:"right",
                scale: true,
                splitNumber:5,
                boundaryGap : false,
                splitLine: {
                    show: true,
                    lineStyle:{
                        color:'#292929'
                    }
                },
                axisLine:{
                    show:false,
                    lineStyle:{
                        color:'#5f5f5f'
                    }
                },
                axisTick:{
                    show:false
                },
                axisLabel:{
                    show:true,
                    formatter: function (value, index) {
                        return value.toFixed(5)
                    }
                },
                max:'dataMax',
                min:'dataMin'

            },
            {   gridIndex: 1,
                position:"right",
                scale: true,
                splitNumber:3,
                boundaryGap : false,
                splitLine: {show: false},
                axisLine:{
                    show:false,
                    onZero: true,
                    lineStyle:{
                        color:'#5f5f5f'
                    }
                },
                axisTick:{
                    show:false
                },
                axisLabel:{
                    show:true,
                    formatter: function (value, index) {

                        if(value>0){
                            return "+"+value.toFixed(5)
                        }
                        if(value<0){
                            return value.toFixed(5)
                        }
                        if(value==0){
                            return '-'+value.toFixed(5)
                        }
                    }
                },
                max:'dataMax',
                min:'dataMin'
            }
        ],
        dataZoom:[
            {
                type: 'inside',
                xAxisIndex: [0, 1],
                start: obj.start,
                end: obj.end
            },
            {
                show: false,
                xAxisIndex: [0, 1],
                type: 'slider',
                top: '1%',
                start: 30,
                end: 50
            }
        ],
        series: [
            {
                name: '日K',
                type: 'line',
                data: kTl(data.items),
                markLine: {
                    data: [
                        {yAxis:data.topdata.now}
                    ],
                    symbol:'',
                    lineStyle:{
                        normal:{
                            color:'#c23531',
                        }

                    },
                    label:{
                        normal:{
                            formatter: '{c}'
                        }
                    },
                    animationDuration:0
                },
                smooth:false,
                symbol: 'none',
                sampling: 'average',
                itemStyle: {
                    normal: {
                        color: 'rgb(255, 70, 131)'
                    }
                },
                lineStyle:{
                    normal:{
                        width:2,
                        color:"#d2c01e"
                    }
                },
                areaStyle: {
                    normal: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                            offset: 0,
                            color: '#474019'
                        }, {
                            offset: 1,
                            color: '#262922'
                        }])
                    }
                },
                animationDuration:0

            },
            {
                xAxisIndex: 1,
                yAxisIndex: 1,
                name: 'MACD',
                type: 'bar',
                data:macd,//
                smooth: true,
                symbolSize:1,
                animationDuration:0,
                itemStyle:{
                    normal:{
                        color:'rgba(31,198,98,1)'
                    }
                }
            },
            {
                xAxisIndex: 1,
                yAxisIndex: 1,
                name: 'diff',//快
                type: 'line',
                data: diff,
                smooth: true,
                animationDuration:0,
                symbolSize:1,
                lineStyle:{
                    normal:{
                        color:"#13E9EC"
                    }
                }
            },
            {
                xAxisIndex: 1,
                yAxisIndex: 1,
                name: 'dea',
                type: 'line',
                data: dea,//慢
                smooth: true,
                animationDuration:0,
                symbolSize:1,
                lineStyle:{
                    normal:{
                        color:"#FA2E42"
                    }
                }
            }
        ]
    };
    ecKx.clear();
    if(ctype=="k"){
        ecKx.setOption(ecKdata, true);
    }else{
        ecKx.setOption(ecKdata2, true);
    }

    ecKx.on("datazoom",function(param){
        obj = param.batch[0];
        // ecKx.setOption(ecKdata);
    })
    ecKxId = null;

    //console.log('渲染结束');

}

function getonedata(){
    var data=JSON.parse(localStorage.getItem("data"));

    clearTimeout(ccout);
    var temp = $.ajax({
        url:prodata_url,
        type: "get",
        cache:false,
        dataType: "json",
        async:true,

        data:{
            "pid":procode
        },
        success: function(_onedata) {
            var onedata = jQuery.parseJSON(Base64.decode(_onedata));
            var a = tempmy(onedata,data);
            a = null;

        },
        error:function(XHR){
            XHR = null
        },
        complete: function (jqXHR , TS) { jqXHR=null;
        }

    })
    //temp.destroy();
    //delete temp;
    ccout=setTimeout("getonedata()",1000);
}


/**
 * 实时更新面板信息
 */
function getdata() {
    $.get(_getdataurl + procode, function(rs){
        var data = rs;
        var old_price = $('#jk').data('price');
        if(old_price*10 < data.Price*10){
            $('#jk .title').css('color', 'red');//#1aad19
            $('#jk .dirc').html('↑');
        }else if(old_price*10 > data.Price*10){
            $('#jk .dirc').html('↓');
            $('#jk .title').css('color', '#1aad19');//red
        }
        $('#jk .price .price_ct').html(data.Price);
        $('#jk').data('price',data.Price)
        $('.vol').html(data.vol);
        newprice = data.Price;
        $('.newprice').html(newprice);
        $('.currentinfo div:first-child span:last-child').html(data.High);
        $('.currentinfo div:nth-child(2) span:last-child').html(data.Low);
    });
}

function tempmy(onedata,data){
    if(onedata.now>data.topdata.now){
        data.topdata=onedata;
        data.topdata.state="up"
    }

    if(onedata.now<data.topdata.now){
        data.topdata=onedata;
        data.topdata.state="down"
    }
//			data.items[59] = [data.items[59][0],onedata.now,onedata.now,data.items[59][3],data.items[59][4]];
    data.items[data.items.length-1][2] = onedata.now;
    maxQiya = maxQiya>onedata.now?maxQiya:onedata.now;
    minQiya = minQiya<onedata.now?minQiya:onedata.now;
    data.items[data.items.length-1][3] = minQiya;
    data.items[data.items.length-1][4] = maxQiya;
    if(ctype=="l"){
        K2line[data.items.length-1]=data.topdata.now;
    }

    var gotoechartsNew = new gotoecharts(JSON.parse(JSON.stringify(data)));
    // gotoechartsNew();
    gotoechartsNew = null;


    newprice = data.topdata.now;
    var old_price = $('#jk').data('price');
    if(old_price*10 < newprice*10){
        $('#jk .dirc').html('↑');
    }else if(old_price*10 > newprice*10){
        $('#jk .dirc').html('↓');
    }
    $('#jk .price').html(data.price);
    $('.newprice').html(newprice);
    onedata = null ;
    data = null;
}
