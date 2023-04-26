webpackJsonp([1], {
    489: function (t, e, o) {
        "use strict";

        function i(t) {
            o(557)
        }

        Object.defineProperty(e, "__esModule", {value: !0});
        var n = o(520), s = o.n(n);
        for (var r in n) "default" !== r && function (t) {
            o.d(e, t, function () {
                return n[t]
            })
        }(r);
        var a = o(567), l = o(47), c = i, u = l(s.a, a.a, !1, c, null, null);
        e.default = u.exports
    }, 514: function (t, e) {
        t.exports = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAdCAMAAACUsxyNAAAAUVBMVEVMaXH////////////////////////////////////////////////////////////////////////////////////////////////////////JzkR1AAAAGnRSTlMADr/Ga8ARamkLvsgMDWfDt0xoEsr1bAmgIGFaF2gAAABpSURBVBjTfdFHEoAgDEBREVHB3jX3P6hZwseR5ZuQWshxFskTkSqBXaWJ4QoqayybVWlj8SXFOGY2luIDa9XdZ+YxiZn4q3eUYVZZYnkU7p8I5mAV9sFOOQun5T64Me4023p2l+xyvO0LF6sLfF4IPUQAAAAASUVORK5CYII="
    }, 520: function (t, e, o) {
        "use strict";
        (function (t) {
            function i(t) {
                return t && t.__esModule ? t : {default: t}
            }

            Object.defineProperty(e, "__esModule", {value: !0}), o(559);
            var n, s = o(48), r = i(s), a = o(85), l = i(a), c = o(63), u = i(c), d = o(184), h = i(d), f = o(561),
                p = i(f), m = o(566), v = i(m);
            o(188);
            e.default = {
                data: function () {
                    return {
                        baseurl: this.junImgUrl,
                        dataset: [],
                        dbRoom: "",
                        changeData: "",
                        loadingShow: !1,
                        title: "",
                        kfQQ: ""
                    }
                }, methods: {
                    prev: function (t, e) {
                    }, skipStep: function () {
                        t(".l_NoviceStepBox").remove(), t(".l_NoviceStepShowBox").remove(), t(".skip").remove();
                        var e = this;
                        u.default.get({url: "Index/novice", params: {type: null}}).then(function (t) {
                            if (1 != t.code) return e.$message({
                                message: t.msg,
                                type: "error"
                            }), "请登录" == t.msg && (window.location.href = "#/", !1);
                            e.$message({message: "成功", type: "success"}), e.$store.state.noviceStep = 0
                        })
                    }, applicant: function () {
                        var t = this.$store.state.isBroker;
                        1 == t ? r.default.push("broker") : -1 == t ? this.$confirm("正在审核中!", "提示", {
                            confirmButtonText: "确定",
                            showCancelButton: !1,
                            showClose: !1,
                            roundButton: !0,
                            center: !0,
                            type: ""
                        }) : 0 == t && r.default.push("applicant")
                    }, pay: function () {
                    }, wallet: function () {
                    }, connect: function (t) {
                    }, dbroom: function () {
                        var t = this;
                        u.default.get({url: "Index/tvQQ"}).then(function (e) {
                            1 == e.code ? t.dbRoom = e.data : e.code
                        })
                    }
                }, mounted: function () {
                    this.dbroom();
                    var e = this;
                    u.default.get({url: "Index/getNewPrice", vm: this}).then(function (t) {
                        e.changeData = t.data
                    }), setTimeout(function () {
                        u.default.get({url: "Index/index"}).then(function (o) {
                            1 == o.code && (e.dataset = o.data.product, e.title = o.data.title, e.$store.state.closeResult = o.data.closeResult, e.$store.state.kfQQ = "http://wpa.qq.com/msgrd?v=3&uin=" + o.data.kfQQ + "&site=qq&menu=yes", e.$store.state.noviceStep = o.data.novice, 1 == o.data.novice && 0 == e.$store.state.step && (t(".cover_box").hide(), t(".step0").show(), e.$store.state.step = 1, t(".skip").show()), 7 == e.$store.state.step && t(".step2").show())
                        })
                    }, 100), n = setInterval(function () {
                        u.default.get({url: "Index/getNewPrice"}).then(function (t) {
                            e.changeData = t.data
                        })
                    }, 2e3), document.addEventListener("touchstart", function () {
                        return !1
                    }, !0), h.default.slideLeft(".t_table", "/home/pay")
                }, updated: function () {
                    var e = this;
                    t(this.$refs.ab).click(function () {
                        if ("休市" == t(this).find(".t_status").text()) e.$message({
                            message: "休市",
                            center: !0,
                            type: ""
                        }); else {
                            var o = t(this).find(".identifying").text();
                            r.default.push({name: "am", params: {am: o}})
                        }
                    })
                }, destroyed: function () {
                    clearInterval(n)
                }, components: {loading: l.default, announcement: p.default, Carousel: v.default}
            }
        }).call(e, o(49))
    }, 521: function (t, e, o) {
        "use strict";
        Object.defineProperty(e, "__esModule", {value: !0}), o(562);
        var i = o(63), n = function (t) {
            return t && t.__esModule ? t : {default: t}
        }(i);
        e.default = {
            data: function () {
                return {announcement: !1, info: ""}
            }, methods: {
                data: function () {
                    var t = this;
                    n.default.get({url: "Index/topNotices", vm: this}).then(function (e) {
                        0 == e.code ? t.announcement = !1 : 1 == e.code && (t.info = e.data, t.announcement = !0)
                    })
                }, announcementShow: function () {
                    this.announcement = !1, this.$store.state.announcement = !1
                }
            }, mounted: function () {
                this.data()
            }
        }
    }, 557: function (t, e, o) {
        var i = o(558);
        "string" == typeof i && (i = [[t.i, i, ""]]), i.locals && (t.exports = i.locals);
        o(121)("b242d83c", i, !0)
    }, 558: function (t, e, o) {
        e = t.exports = o(29)(void 0), e.push([t.i, "", ""])
    }, 559: function (t, e, o) {
        var i = o(560);
        "string" == typeof i && (i = [[t.i, i, ""]]);
        var n = {hmr: !0};
        n.transform = void 0;
        o(34)(i, n);
        i.locals && (t.exports = i.locals)
    }, 560: function (t, e, o) {
        e = t.exports = o(29)(void 0), e.push([t.i, ".t_box{flex:1;display:flex;flex-direction:column;background-color:#222629;position:relative}.t_box .header{height:1.4rem;background-color:#181f2f;display:flex;flex-direction:row;justify-content:center;align-items:center}.t_box .header span{color:#fff;font-size:.48rem}.t_box .jun-carousel{height:3.466667rem;overflow:hidden}.t_box .jun-carousel .carousel-item{height:3.466667rem;width:100%;background:#fff;font-size:.8rem;color:red}.t_box .jun-carousel .carousel-item .jun-son-img{height:100%;width:100%}.t_box .jun-carousel .el-carousel__container{width:90%;margin:0 auto}.t_box .jun-carousel .el-carousel__item h3{color:#475669;font-size:14px;opacity:.75;line-height:200px;margin:0}.t_box .jun-carousel .el-carousel__item:nth-child(2n){background-color:#99a9bf}.t_box .jun-carousel .el-carousel__item:nth-child(odd){background-color:#d3dce6}.t_box .t_table{flex:1;display:flex;flex-direction:column;position:relative}.t_box .t_table .t_lineher{height:1.066667rem;display:flex;flex-direction:row}.t_box .t_table .t_lineher li{font-size:.373333rem;color:#fff;text-align:center;line-height:1.066667rem}.t_box .t_table .t_lineher li:first-child{flex:1;text-align:left;text-indent:.8rem}.t_box .t_table .t_lineher li:nth-child(2){width:2.4rem}.t_box .t_table .t_lineher li:nth-child(3){width:2.6rem;text-align:left;text-indent:.533333rem}.t_box .t_table .t_con_home{flex:1;overflow:scroll;-webkit-overflow-scrolling:touch;justify-content:center;align-items:center}.t_box .t_table .t_con_home .jun-empty{height:1.55rem}.t_box .t_table .t_con_home ul{height:1.55rem;width:94%;margin:0 auto;display:flex;flex-direction:row;background-size:100% 100%;margin-bottom:.106667rem;border-radius:.266667rem}.t_box .t_table .t_con_home ul li:first-child{flex:1;display:flex;flex-direction:row}.t_box .t_table .t_con_home ul li:first-child span{width:1.866667rem;position:relative}.t_box .t_table .t_con_home ul li:first-child span img{display:block;width:1.333333rem;height:1.333333rem;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%)}.t_box .t_table .t_con_home ul li:first-child div{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center}.t_box .t_table .t_con_home ul li:first-child div span{width:100%;color:#fff;font-size:.32rem;text-align:left;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.t_box .t_table .t_con_home ul li:first-child div span:nth-child(2){line-height:.6rem;color:#fff}.t_box .t_table .t_con_home ul li:first-child div span:last-child{font-size:.266667rem;color:#9b8ea5}.t_box .t_table .t_con_home ul li:nth-child(2){width:2.4rem;display:flex;justify-content:center;align-items:center}.t_box .t_table .t_con_home ul li:nth-child(2) span{display:block;height:.8rem;width:100%;background-color:#26a848;border-radius:.133333rem;line-height:.8rem;text-align:center;color:#fff}.t_box .t_table .t_con_home ul li:nth-child(3){width:1.66rem;display:flex;justify-content:center;align-items:center}.t_box .t_table .t_con_home ul li:nth-child(3) span{display:block;width:1.2rem;height:.533333rem;background-color:#d73741;font-size:.3rem;line-height:.533333rem;text-align:center;color:#fff;border-radius:.133333rem}.t_box .t_table .t_con_home ul li:nth-child(4){width:.6rem;display:flex;justify-content:center;align-items:center}.t_box .t_table .t_con_home ul li:nth-child(4) i{display:block;width:.213333rem;height:.386667rem;background:url(" + o(514) + ") no-repeat;background-size:cover}.l_NoviceStepBox{height:100%;background:rgba(0,0,0,.8)}.l_NoviceStepBox,.l_NoviceStepShowBox{width:100%;position:absolute;top:0;left:0}.l_NoviceStepShowBox{height:98%;border:5px solid #fff;background:rgba(0,0,0,.1)}.l_NoviceStepShowBox .l_NoviceStepShowBoxTip{width:100%;position:absolute;top:-1.3rem;text-indent:.5rem;color:#fff;font-size:.5rem}.l_NoviceStepShowBox .l_nextButton button{width:1.5rem;height:.8rem;border:1px solid #fff;border-radius:5rem;background:#009dff;color:#fff;font-size:.3rem;position:absolute;top:-2.5rem;right:10%}.l_NoviceStepShowBox .l_nextButton span{width:1.5rem;height:4px;border-top:4px dashed #fff;transform:rotate(90deg);position:absolute;top:-.9rem;right:10%}div.l_NoviceStepBox.step0{position:absolute!important;display:none}.l_NoviceStepBox{display:none}.l_NoviceStepBox.step1{height:93%;display:none}.l_NoviceStepBox.step1.active,.l_NoviceStepBox.step2.active{background:none;border:3px solid #fff}.l_NoviceStepBox.step1.active p,.l_NoviceStepBox.step2.active p{width:500px;color:#fff;position:absolute;top:-120px;left:100%}.l_NoviceStepBox.step1.active p:first-of-type,.l_NoviceStepBox.step2.active p:first-of-type{top:-120px}.l_NoviceStepBox.step1.active p:nth-of-type(2),.l_NoviceStepBox.step2.active p:nth-of-type(2){top:-80px}.l_NoviceStepBox.step1.active p:nth-of-type(3),.l_NoviceStepBox.step2.active p:nth-of-type(3){top:-40px}.l_NoviceStepBox.step4,.l_NoviceStepBox.step6{z-index:10}.l_NoviceStepBox.l_NoviceStepShowBox.step7{height:33.3%}.l_NoviceStepBox.l_NoviceStepShowBox.step8{height:66.6%;top:33.3%}.skip,.step0,.step1,.step2,.step4,.step5,.step6,.step7,.step8{display:none}.skip{position:absolute;top:1%;right:1%;background:#000;color:#fff;padding:20px 40px;border:1px solid #fff;border-radius:100px;font-size:.3rem;z-index:1000}", ""])
    }, 561: function (t, e, o) {
        "use strict";
        Object.defineProperty(e, "__esModule", {value: !0});
        var i = o(521), n = o.n(i);
        for (var s in i) "default" !== s && function (t) {
            o.d(e, t, function () {
                return i[t]
            })
        }(s);
        var r = o(564), a = o(47), l = a(n.a, r.a, !1, null, null, null);
        e.default = l.exports
    }, 562: function (t, e, o) {
        var i = o(563);
        "string" == typeof i && (i = [[t.i, i, ""]]);
        var n = {hmr: !0};
        n.transform = void 0;
        o(34)(i, n);
        i.locals && (t.exports = i.locals)
    }, 563: function (t, e, o) {
        e = t.exports = o(29)(void 0), e.push([t.i, ".cover_box{position:fixed;height:100%;width:100%;z-index:3009}.cover_box .t_cover{position:absolute;height:100%;width:100%;background-color:#666;opacity:.5}.cover_box .t_announcement{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);height:5.093333rem;width:8.026667rem;border:20px solid #fd6e77;border-radius:20px;background-color:#fff}.cover_box .t_announcement .t_header_box .t_header{position:absolute;left:20%;top:-.733333rem;height:.933333rem;width:4.32rem;line-height:.933333rem;text-align:center;text-indent:1.626667rem;background:linear-gradient(#fd6e77,#d73741);border-radius:.133333rem;transform:skew(-30deg);-webkit-transform:skew(-30deg);-moz-transform:skew(-30deg);-o-transform:skew(-30deg);-ms-transform:skew(-30deg)}.cover_box .t_announcement .t_header_box h1{position:absolute;top:-.16rem;left:50%;transform:translate(-50%,-50%);color:#fff;height:.933333rem;text-align:center;line-height:.933333rem;font-size:.48rem;font-weight:500;letter-spacing:.133333rem;z-index:3100}.cover_box .t_announcement .t_con{height:3.466667rem;margin:0 auto;width:90%;margin-top:1.066667rem;overflow:scroll;-webkit-overflow-scrolling:touch}.cover_box .t_announcement .t_con p{word-wrap:break-word;font-size:.4rem;margin-bottom:.133333rem;text-align:center}.cover_box .t_announcement .t_con p i{display:block;float:left;background-color:#fd6e77;width:.533333rem;height:.533333rem;line-height:.533333rem;text-align:center;border-radius:50%;color:#fff;font-size:.373333rem;margin-right:.133333rem;margin-top:.026667rem}.cover_box img{position:absolute;top:70.5%;left:50%;transform:translate(-50%,-73%);width:.986667rem;height:2.36rem}", ""])
    }, 564: function (t, e, o) {
        "use strict";
        var i = function () {
            var t = this, e = t.$createElement, i = t._self._c || e;
            return i("div", {
                directives: [{
                    name: "show",
                    rawName: "v-show",
                    value: t.announcement,
                    expression: "announcement"
                }], staticClass: "cover_box"
            }, [i("div", {staticClass: "t_cover"}), t._v(" "), i("div", {staticClass: "t_announcement"}, [i("div", {staticClass: "t_header_box"}, [i("div", {staticClass: "t_header"}), t._v(" "), i("h1", [t._v("公告")]), t._v(" "), i("div", {staticClass: "t_con"}, [i("p", [t._v(t._s(t.info.contents))])])])]), t._v(" "), i("img", {
                attrs: {
                    src: o(565),
                    alt: ""
                }, on: {click: t.announcementShow}
            })])
        }, n = [], s = {render: i, staticRenderFns: n};
        e.a = s
    }, 565: function (t, e) {
        t.exports = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEoAAACxCAMAAABUW9uZAAABvFBMVEVMaXH////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VmifYAAAAk3RSTlMAQ4v89VRE+Ur6mpV3xYYBeAcoBV3PdT1mapYIBslClO9F0DBpa0/TbwJuF8TO1Bzz1WX0mwmEJ+MYv152GRD9cD7GhYhJDxFo8PuJWv6cWybBqLHAMVgqUKMs+DiiNEhZ6+3pclZgqr2dmDPugGI/18J/jjfWyIygLiXmY20pno9X5DaSO3Pi7J+pX7ASmcM1OS/cGsDCAAAERUlEQVRo3u3Y6V8TRxjA8ceEkpCE2yD3EQ4REAUUQQ5BROVQKRa02ta73rVq78se2vv+/cN9sdndbNiZ2eXjm34+s6+ys5NvksnMM888IsYLQF7PZSlLWcpSlrKUpSxlKUtZylKWspSlLGUpS1nKUpaylKUsZSlLWcpSlvo/UPW93zUBtK41bffW7565NtlB4OqYvLYLbnrwJKHXycHpWNC+mm6UV3fNvshQZZf3tlTh10SuZWamJZfYLKS85q7KSNBwotg/OfL4SWBo6p88/itZfJgYNkvjt5y++8PHZHpwv/P81rhJyjgdq9O1qh616WqnT0YLNRwH4OAe/eftOQjA8QZ1lws/AUwsHDB99wMLEwA/71V1+ACAQkOUP6chD8DZ8KdnnRFoizZj2jJqay/AuVz0efx1EiDkN250A8n346yJ9SSwsrGj/R6QXI+3UNeTwL3y1iMALXEXfR/AkWBbM8D5+AHkPEBzacuZKaBwND51tABMnSlpmQQOH3PvXn12/z3d+7cevPjbfX3sMDDpP+sEmHXv/lmFq2m19EYVpLzPmgXo9B52AWONgRWdSuskaHVvG8eALi9mAvR4fVvRWY5E1mvoAXDjag1Q7Xd+mNJYRaniit9UDdQ4L+dvAqXvS6stVyqNQmng5ryIiPQDY4FIp7TCJKkdA/q9L1i2ihVWqCSScwfoNHDikESwFJIcOgGcFpEB4M0dgxJiqSSRt4DLIpIA6sRsqSWpAz4VkU+Ad8RoaST5COgQaQSq2sVk6SRprwIapQH4MHxil1haSeRj4JIMABfFYBkkuQgMSBOQEINlkCQBNMmp8D8waBkkqQNOSas+pvuWRpJ+ICtvh+9lO6wKXUo1A7xrpIojromFHmX4ga6kt1qAVsOw+5LWcoZdOxnc+ZQyWM5kuAz8bpAq0wbLmaKaheNJ2hjtLxz1ci6RDFZxOSuDTEDSW8Ugowp9ZZLWckNfeEDeIeksNyCHbhMhktrytomwzStUUlre5iX9wGhtBElh1Y56W+r8o7KNXimFW2ng0byffoz4j3pTmqjiWr1+04iffpQnRRltfCpaflJUWZoUSRYY9VK1X/SRzrE8qnE0AHcCNLl3P6T0MTN9FSo+DySQJbF6KZDWPsy+vKJLa3u/zf4bSGuXSh7+8T2Q302ynS9Ptp0jwJ/xqQc7jgDFg0nf6ziYOEsyeT2edD0ZGgq+WQFWYx3iflsFVi6pjpaz0aUfz6HaQ/t2c+Dt0x3D8xtRpC+cY/hXyuLAFMDEkLk4MDQB8OUFc8miUy91mksW7rETqpfVhZTlEafPfWN550axvDMUWgsaHiqWd26Mmwd0zis6PX22GNhr2xefPfWKTnOR/ueeklJYfnPtdvOdO8231zbzfjKZ7Yk8+xb1BbrFWMtrbkFVNlyYix9A2paXyouZS8ttsturfms79zxz927meW57y1AT/Q+GiYMqtUbuuQAAAABJRU5ErkJggg=="
    }, 566: function (t, e, o) {
        !function (e, o) {
            t.exports = o()
        }(0, function () {
            return function (t) {
                function e(i) {
                    if (o[i]) return o[i].exports;
                    var n = o[i] = {exports: {}, id: i, loaded: !1};
                    return t[i].call(n.exports, n, n.exports, e), n.loaded = !0, n.exports
                }

                var o = {};
                return e.m = t, e.c = o, e.p = "", e(0)
            }([function (t, e, o) {
                "use strict";
                Object.defineProperty(e, "__esModule", {value: !0}), o(4);
                e.default = {
                    props: {
                        loop: {type: Boolean, default: !0},
                        auto: {type: Number, default: 3e3},
                        indicators: {type: Boolean, default: !1},
                        responsive: {type: Number, default: 40},
                        flickThreshold: {type: Number, default: .6},
                        delta: {type: Number, default: 100},
                        onSlidEnd: {
                            type: Function, default: function (t) {
                                return 0
                            }
                        },
                        preventDefault: {type: Boolean, default: !1}
                    }, data: function () {
                        return {
                            childrenCount: 0,
                            touch: !1,
                            timer: 0,
                            activeIndex: 0,
                            trackStyle: {transform: "translate(0px, 0px) translateZ(0px)", transitionDuration: 0},
                            transitionDuration: 0,
                            width: "100vw"
                        }
                    }, computed: {
                        computedAuto: function () {
                            return this.auto && this.childrenCount > 1
                        }, computedLoop: function () {
                            return this.$slots.default.length > 1 && this.loop
                        }
                    }, methods: {
                        setHelperDOM: function () {
                            var t = this.$slots.default.length;
                            t > 1 && this.loop && (this.addonBefore = this.list[t - 1].$el.outerHTML, this.addonAfter = this.list[0].$el.outerHTML)
                        }, slid: function (t, e) {
                            var o = this.computedLoop, i = this.width, n = this.transitionDuration, s = this.$slots,
                                r = s.default.length;
                            if (0 !== r) {
                                o || (t = (t + r) % r);
                                var a = -i * (t + (o ? 1 : 0)) - e;
                                this.trackStyle = {
                                    transform: "translate(" + a + "px, 0px) translateZ(0px) translate3d(0, 0, 0)",
                                    transitionDuration: n + "ms",
                                    "-webkit-backface-visibility": "hidden"
                                }, this.activeIndex = (t + r) % r, n > 0 && o && (0 === this.activeIndex || this.activeIndex === r - 1) && setTimeout(this.correctIndex, n), n > 0 && this.onSlidEnd(this.activeIndex)
                            }
                        }, correctIndex: function () {
                            this.transitionDuration = 0, this.slid(this.activeIndex, 0)
                        }, calculatePos: function (t) {
                            var e = t.changedTouches[0].clientX, o = t.changedTouches[0].clientY, i = this.x - e,
                                n = this.y - o;
                            return {deltaX: i, deltaY: n, absX: Math.abs(i), absY: Math.abs(n)}
                        }, setTimer: function () {
                            var t = this, e = this.auto, o = this.$slots, i = o.default.length;
                            e && i > 1 && (this.timer = setInterval(function () {
                                t.transitionTo(t.activeIndex + 1)
                            }, e))
                        }, clearTimer: function () {
                            this.timer && clearInterval(this.timer)
                        }, transitionTo: function (t, e) {
                            this.clearTimer(), this.transitionDuration = e || 300, this.slid(t, 0), this.setTimer()
                        }, onTouchstart: function (t) {
                            if (!(t.touches.length > 1)) {
                                if (1 === this.$slots.default.length) return void(this.touch = !1);
                                this.touch = !0, this.transitionDuration = 0, this.start = Date.now(), this.x = t.touches[0].clientX, this.y = t.touches[0].clientY, this.clearTimer()
                            }
                        }, onTouchmove: function (t) {
                            if (this.preventDefault && t.preventDefault(), this.touch) {
                                var e = this.calculatePos(t);
                                e.absX > e.absY && (t.preventDefault(), this.slid(this.activeIndex, e.deltaX))
                            }
                        }, onTouchend: function (t) {
                            if (this.touch) {
                                var e = this.loop, o = this.$slots, i = this.start, n = this.flickThreshold,
                                    s = this.delta, r = this.activeIndex, a = this.calculatePos(t), l = Date.now() - i,
                                    c = Math.sqrt(a.absX * a.absX + a.absY * a.absY) / l, u = c > n, d = r;
                                (u || a.absX > s) && (d += a.absX / a.deltaX, e || (d = Math.max(Math.min(d, o.default.length - 1), 0))), this.transitionTo(d), this.cleanTouch()
                            }
                        }, onTouchcancel: function (t) {
                            this.touch && (this.transitionTo(this.activeIndex), this.cleanTouch())
                        }, cleanTouch: function () {
                            this.touch = !1
                        }, resize: function () {
                            this.$nextTick(function () {
                                this.width = this.$el.clientWidth, this.slid(this.activeIndex, 0)
                            }, this)
                        }
                    }, watch: {
                        computedAuto: function () {
                            this.setTimer()
                        }
                    }, render: function (t) {
                        var e = this, o = this.computedLoop, i = this.$slots, n = i.default.length - 1,
                            s = this.indicators && t("div", {class: "carousel-indicators"}, [this.$slots.default.map(function (o, i) {
                                var n = {"carousel-dot": !0, active: i === e.activeIndex};
                                return t("div", {class: n, on: {click: e.transitionTo.bind(e, i, 300)}}, [i])
                            })]), r = {};
                        0 !== this.responsive && (r.height = "0", r.paddingBottom = this.responsive + "%");
                        var a = {width: this.width + ("number" == typeof this.width ? "px" : "")};
                        return t("div", {class: "carousel", style: r}, [t("div", {
                            class: "carousel-track",
                            style: this.trackStyle,
                            on: {
                                touchstart: this.onTouchstart,
                                touchmove: this.onTouchmove,
                                touchend: this.onTouchend,
                                touchcancel: this.onTouchcancel
                            }
                        }, [o ? t("div", {
                            class: "carousel-item",
                            style: a
                        }, [this.$slots.default[n]]) : null, this.$slots.default.map(function (e) {
                            return t("div", {class: "carousel-item", style: a}, [e])
                        }), o ? t("div", {class: "carousel-item", style: a}, [this.$slots.default[0]]) : null]), s])
                    }, mounted: function () {
                        var t = this;
                        this.childrenCount = this.$slots.default.length, this.$nextTick(function () {
                            t.resize(), window.addEventListener("resize", t.resize), t.setTimer()
                        }), document.addEventListener("visibilitychange", function (e) {
                            (document.hidden ? t.clearTimer : t.setTimer)()
                        })
                    }, updated: function () {
                        this.childrenCount = this.$slots.default.length
                    }, beforeDestroy: function () {
                        window.removeEventListener("resize", this.resize), this.clearTimer()
                    }
                }
            }, function (t, e, o) {
                e = t.exports = o(2)(), e.push([t.id, ".carousel{box-sizing:content-box;width:100%;overflow:hidden;position:relative}.carousel-track{height:100%;min-width:100%;position:absolute;top:0;left:0;white-space:nowrap;display:table-cell;font-size:0}.carousel-item{width:100vw;height:100%;overflow:hidden;display:inline-block;font-size:16px}.carousel-indicators{position:absolute;height:0;bottom:40px;left:0;width:100%;text-align:center}.carousel-dot{display:inline-block;width:20px;height:20px;margin:10px;background:rgba(0,0,0,.5)}.carousel-dot.active{background:red}", ""])
            }, function (t, e) {
                t.exports = function () {
                    var t = [];
                    return t.toString = function () {
                        for (var t = [], e = 0; e < this.length; e++) {
                            var o = this[e];
                            o[2] ? t.push("@media " + o[2] + "{" + o[1] + "}") : t.push(o[1])
                        }
                        return t.join("")
                    }, t.i = function (e, o) {
                        "string" == typeof e && (e = [[null, e, ""]]);
                        for (var i = {}, n = 0; n < this.length; n++) {
                            var s = this[n][0];
                            "number" == typeof s && (i[s] = !0)
                        }
                        for (n = 0; n < e.length; n++) {
                            var r = e[n];
                            "number" == typeof r[0] && i[r[0]] || (o && !r[2] ? r[2] = o : o && (r[2] = "(" + r[2] + ") and (" + o + ")"), t.push(r))
                        }
                    }, t
                }
            }, function (t, e, o) {
                function i(t, e) {
                    for (var o = 0; o < t.length; o++) {
                        var i = t[o], n = f[i.id];
                        if (n) {
                            n.refs++;
                            for (var s = 0; s < n.parts.length; s++) n.parts[s](i.parts[s]);
                            for (; s < i.parts.length; s++) n.parts.push(c(i.parts[s], e))
                        } else {
                            for (var r = [], s = 0; s < i.parts.length; s++) r.push(c(i.parts[s], e));
                            f[i.id] = {id: i.id, refs: 1, parts: r}
                        }
                    }
                }

                function n(t) {
                    for (var e = [], o = {}, i = 0; i < t.length; i++) {
                        var n = t[i], s = n[0], r = n[1], a = n[2], l = n[3], c = {css: r, media: a, sourceMap: l};
                        o[s] ? o[s].parts.push(c) : e.push(o[s] = {id: s, parts: [c]})
                    }
                    return e
                }

                function s(t, e) {
                    var o = v(), i = b[b.length - 1];
                    if ("top" === t.insertAt) i ? i.nextSibling ? o.insertBefore(e, i.nextSibling) : o.appendChild(e) : o.insertBefore(e, o.firstChild), b.push(e); else {
                        if ("bottom" !== t.insertAt) throw new Error("Invalid value for parameter 'insertAt'. Must be 'top' or 'bottom'.");
                        o.appendChild(e)
                    }
                }

                function r(t) {
                    t.parentNode.removeChild(t);
                    var e = b.indexOf(t);
                    e >= 0 && b.splice(e, 1)
                }

                function a(t) {
                    var e = document.createElement("style");
                    return e.type = "text/css", s(t, e), e
                }

                function l(t) {
                    var e = document.createElement("link");
                    return e.rel = "stylesheet", s(t, e), e
                }

                function c(t, e) {
                    var o, i, n;
                    if (e.singleton) {
                        var s = g++;
                        o = x || (x = a(e)), i = u.bind(null, o, s, !1), n = u.bind(null, o, s, !0)
                    } else t.sourceMap && "function" == typeof URL && "function" == typeof URL.createObjectURL && "function" == typeof URL.revokeObjectURL && "function" == typeof Blob && "function" == typeof btoa ? (o = l(e), i = h.bind(null, o), n = function () {
                        r(o), o.href && URL.revokeObjectURL(o.href)
                    }) : (o = a(e), i = d.bind(null, o), n = function () {
                        r(o)
                    });
                    return i(t), function (e) {
                        if (e) {
                            if (e.css === t.css && e.media === t.media && e.sourceMap === t.sourceMap) return;
                            i(t = e)
                        } else n()
                    }
                }

                function u(t, e, o, i) {
                    var n = o ? "" : i.css;
                    if (t.styleSheet) t.styleSheet.cssText = _(e, n); else {
                        var s = document.createTextNode(n), r = t.childNodes;
                        r[e] && t.removeChild(r[e]), r.length ? t.insertBefore(s, r[e]) : t.appendChild(s)
                    }
                }

                function d(t, e) {
                    var o = e.css, i = e.media;
                    if (i && t.setAttribute("media", i), t.styleSheet) t.styleSheet.cssText = o; else {
                        for (; t.firstChild;) t.removeChild(t.firstChild);
                        t.appendChild(document.createTextNode(o))
                    }
                }

                function h(t, e) {
                    var o = e.css, i = e.sourceMap;
                    i && (o += "\n/*# sourceMappingURL=data:application/json;base64," + btoa(unescape(encodeURIComponent(JSON.stringify(i)))) + " */");
                    var n = new Blob([o], {type: "text/css"}), s = t.href;
                    t.href = URL.createObjectURL(n), s && URL.revokeObjectURL(s)
                }

                var f = {}, p = function (t) {
                    var e;
                    return function () {
                        return void 0 === e && (e = t.apply(this, arguments)), e
                    }
                }, m = p(function () {
                    return /msie [6-9]\b/.test(self.navigator.userAgent.toLowerCase())
                }), v = p(function () {
                    return document.head || document.getElementsByTagName("head")[0]
                }), x = null, g = 0, b = [];
                t.exports = function (t, e) {
                    e = e || {}, void 0 === e.singleton && (e.singleton = m()), void 0 === e.insertAt && (e.insertAt = "bottom");
                    var o = n(t);
                    return i(o, e), function (t) {
                        for (var s = [], r = 0; r < o.length; r++) {
                            var a = o[r], l = f[a.id];
                            l.refs--, s.push(l)
                        }
                        if (t) {
                            i(n(t), e)
                        }
                        for (var r = 0; r < s.length; r++) {
                            var l = s[r];
                            if (0 === l.refs) {
                                for (var c = 0; c < l.parts.length; c++) l.parts[c]();
                                delete f[l.id]
                            }
                        }
                    }
                };
                var _ = function () {
                    var t = [];
                    return function (e, o) {
                        return t[e] = o, t.filter(Boolean).join("\n")
                    }
                }()
            }, function (t, e, o) {
                var i = o(1);
                "string" == typeof i && (i = [[t.id, i, ""]]), o(3)(i, {}), i.locals && (t.exports = i.locals)
            }])
        })
    }, 567: function (t, e, o) {
        "use strict";
        var i = function () {
            var t = this, e = t.$createElement, o = t._self._c || e;
            return o("div", {staticClass: "t_box slide"}, [o("div", {staticClass: "jun-carousel"}, [o("carousel", {attrs: {auto: 4e3}}, t._l(6, function (e) {
                return o("img", {
                    directives: [{
                        name: "lazy",
                        rawName: "v-lazy",
                        value: t.junImgUrl + "t_imgs/home/" + e + ".png",
                        expression: "junImgUrl+'t_imgs/home/'+i+'.png'"
                    }], staticClass: "jun-son-img", attrs: {src: "", alt: ""}
                })
            }))], 1), t._v(" "), o("div", {staticClass: "t_table"}, [t._m(0), t._v(" "), o("div", {staticClass: "t_con_home"}, [t._l(t.dataset, function (e, i) {
                return o("ul", {
                    ref: "ab",
                    refInFor: !0,
                    style: {
                        "background-image": "url(" + t.baseurl + "t_imgs/home/bg.png)",
                        "background-repeat": "no-repeat"
                    },
                    on: {click: t.connect}
                }, [o("li", [o("span", [o("img", {
                    directives: [{
                        name: "lazy",
                        rawName: "v-lazy",
                        value: e.imgname,
                        expression: "obj.imgname"
                    }], attrs: {src: "", alt: ""}
                })]), t._v(" "), o("div", [o("i", {
                    staticClass: "identifying",
                    staticStyle: {display: "none"}
                }, [t._v(t._s(e.name))]), t._v(" "), o("span", [t._v(t._s(e.cname))]), t._v(" "), o("span", [t._v(t._s(e.sname))])])]), t._v(" "), o("li", [o("span", {style: {backgroundColor: t.changeData[i].color}}, [t._v(t._s(t.changeData[i].nums))])]), t._v(" "), o("li", [o("span", {
                    staticClass: "t_status",
                    style: "休市" == e.closes ? "background:#3a8ee6" : ""
                }, [t._v(t._s(e.closes))])]), t._v(" "), t._m(1, !0)])
            }), t._v(" "), o("div", {staticClass: "jun-empty"})], 2)]), t._v(" "), this.$store.state.announcement ? o("announcement") : t._e(), t._v(" "), o("loading", {
                directives: [{
                    name: "show",
                    rawName: "v-show",
                    value: t.loadingShow,
                    expression: "loadingShow"
                }]
            }), t._v(" "), o("div", {staticClass: "skip", on: {click: t.skipStep}}, [t._v("跳过")])], 1)
        }, n = [function () {
            var t = this, e = t.$createElement, o = t._self._c || e;
            return o("ul", {staticClass: "t_lineher"}, [o("li", [t._v("热门产品")]), t._v(" "), o("li", [t._v("最新")]), t._v(" "), o("li", [t._v("状态")])])
        }, function () {
            var t = this, e = t.$createElement, o = t._self._c || e;
            return o("li", [o("i")])
        }], s = {render: i, staticRenderFns: n};
        e.a = s
    }
});
//# sourceMappingURL=1.build.js.map