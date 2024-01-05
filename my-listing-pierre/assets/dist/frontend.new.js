! function(e) {
    "function" == typeof define && define.amd ? define("frontend", e) : e()
}(function() {
    "use strict";

    function yi(e) {
        return (yi = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(e) {
            return typeof e
        } : function(e) {
            return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
        })(e)
    }
    var e, t, i, n, a, s;

    function o() {
        a = Date.now()
    }! function(e, i) {
        if ("function" == typeof define && define.amd) define(["moment", "jquery"], function(e, t) {
            return t.fn || (t.fn = {}), i(e, t)
        });
        else if ("object" === ("undefined" == typeof module ? "undefined" : yi(module)) && module.exports) {
            var t = "undefined" != typeof window ? window.jQuery : void 0;
            t || (t = require("jquery")).fn || (t.fn = {});
            var n = "undefined" != typeof window && void 0 !== window.moment ? window.moment : require("moment");
            module.exports = i(n, t)
        } else e.daterangepicker = i(e.moment, e.jQuery)
    }(window, function(O, F) {
        function n(e, t, i) {
            if (this.parentEl = "body", this.element = F(e), this.startDate = O().startOf("day"), this.endDate = O().endOf("day"), this.minDate = !1, this.maxDate = !1, this.maxSpan = !1, this.autoApply = !1, this.singleDatePicker = !1, this.showDropdowns = !1, this.minYear = O().subtract(100, "year").locale("en").format("YYYY"), this.maxYear = O().add(100, "year").locale("en").format("YYYY"), this.showWeekNumbers = !1, this.showISOWeekNumbers = !1, this.showCustomRangeLabel = !0, this.timePicker = !1, this.timePicker24Hour = !1, this.timePickerIncrement = 1, this.timePickerSeconds = !1, this.linkedCalendars = !0, this.autoUpdateInput = !0, this.alwaysShowCalendars = !1, this.ranges = {}, this.opens = "right", this.element.hasClass("pull-right") && (this.opens = "left"), this.drops = "down", this.element.hasClass("dropup") && (this.drops = "up"), this.buttonClasses = "btn btn-sm", this.applyButtonClasses = "btn-primary", this.cancelButtonClasses = "btn-default", this.locale = {
                    direction: "ltr",
                    format: O.localeData().longDateFormat("L"),
                    separator: " - ",
                    applyLabel: "Apply",
                    cancelLabel: "Cancel",
                    weekLabel: "W",
                    customRangeLabel: "Custom Range",
                    daysOfWeek: O.weekdaysMin(),
                    monthNames: O.monthsShort(),
                    firstDay: O.localeData().firstDayOfWeek()
                }, this.callback = function() {}, this.isShowing = !1, this.leftCalendar = {}, this.rightCalendar = {}, "object" === yi(t) && null !== t || (t = {}), "string" == typeof(t = F.extend(this.element.data(), t)).template || t.template instanceof F || (t.template = '<div class="daterangepicker"><div class="ranges"></div><div class="drp-calendar left"><div class="calendar-table"></div><div class="calendar-time"></div></div><div class="drp-calendar right"><div class="calendar-table"></div><div class="calendar-time"></div></div><div class="drp-buttons"><span class="drp-selected"></span><button class="cancelBtn" type="button"></button><button class="applyBtn" disabled="disabled" type="button"></button> </div></div>'), this.parentEl = t.parentEl && F(t.parentEl).length ? F(t.parentEl) : F(this.parentEl), this.container = F(t.template).appendTo(this.parentEl), "object" === yi(t.locale) && ("string" == typeof t.locale.direction && (this.locale.direction = t.locale.direction), "string" == typeof t.locale.format && (this.locale.format = t.locale.format), "string" == typeof t.locale.separator && (this.locale.separator = t.locale.separator), "object" === yi(t.locale.daysOfWeek) && (this.locale.daysOfWeek = t.locale.daysOfWeek.slice()), "object" === yi(t.locale.monthNames) && (this.locale.monthNames = t.locale.monthNames.slice()), "number" == typeof t.locale.firstDay && (this.locale.firstDay = t.locale.firstDay), "string" == typeof t.locale.applyLabel && (this.locale.applyLabel = t.locale.applyLabel), "string" == typeof t.locale.cancelLabel && (this.locale.cancelLabel = t.locale.cancelLabel), "string" == typeof t.locale.weekLabel && (this.locale.weekLabel = t.locale.weekLabel), "string" == typeof t.locale.customRangeLabel)) {
                (u = document.createElement("textarea")).innerHTML = t.locale.customRangeLabel;
                var n = u.value;
                this.locale.customRangeLabel = n
            }
            if (this.container.addClass(this.locale.direction), "string" == typeof t.startDate && (this.startDate = O(t.startDate, this.locale.format)), "string" == typeof t.endDate && (this.endDate = O(t.endDate, this.locale.format)), "string" == typeof t.minDate && (this.minDate = O(t.minDate, this.locale.format)), "string" == typeof t.maxDate && (this.maxDate = O(t.maxDate, this.locale.format)), "object" === yi(t.startDate) && (this.startDate = O(t.startDate)), "object" === yi(t.endDate) && (this.endDate = O(t.endDate)), "object" === yi(t.minDate) && (this.minDate = O(t.minDate)), "object" === yi(t.maxDate) && (this.maxDate = O(t.maxDate)), this.minDate && this.startDate.isBefore(this.minDate) && (this.startDate = this.minDate.clone()), this.maxDate && this.endDate.isAfter(this.maxDate) && (this.endDate = this.maxDate.clone()), "string" == typeof t.applyButtonClasses && (this.applyButtonClasses = t.applyButtonClasses), "string" == typeof t.applyClass && (this.applyButtonClasses = t.applyClass), "string" == typeof t.cancelButtonClasses && (this.cancelButtonClasses = t.cancelButtonClasses), "string" == typeof t.cancelClass && (this.cancelButtonClasses = t.cancelClass), "object" === yi(t.maxSpan) && (this.maxSpan = t.maxSpan), "object" === yi(t.dateLimit) && (this.maxSpan = t.dateLimit), "string" == typeof t.opens && (this.opens = t.opens), "string" == typeof t.drops && (this.drops = t.drops), "boolean" == typeof t.showWeekNumbers && (this.showWeekNumbers = t.showWeekNumbers), "boolean" == typeof t.showISOWeekNumbers && (this.showISOWeekNumbers = t.showISOWeekNumbers), "string" == typeof t.buttonClasses && (this.buttonClasses = t.buttonClasses), "object" === yi(t.buttonClasses) && (this.buttonClasses = t.buttonClasses.join(" ")), "boolean" == typeof t.showDropdowns && (this.showDropdowns = t.showDropdowns), "number" == typeof t.minYear && (this.minYear = t.minYear), "number" == typeof t.maxYear && (this.maxYear = t.maxYear), "boolean" == typeof t.showCustomRangeLabel && (this.showCustomRangeLabel = t.showCustomRangeLabel), "boolean" == typeof t.singleDatePicker && (this.singleDatePicker = t.singleDatePicker, this.singleDatePicker && (this.endDate = this.startDate.clone())), "boolean" == typeof t.timePicker && (this.timePicker = t.timePicker), "boolean" == typeof t.timePickerSeconds && (this.timePickerSeconds = t.timePickerSeconds), "number" == typeof t.timePickerIncrement && (this.timePickerIncrement = t.timePickerIncrement), "boolean" == typeof t.timePicker24Hour && (this.timePicker24Hour = t.timePicker24Hour), "boolean" == typeof t.autoApply && (this.autoApply = t.autoApply), "boolean" == typeof t.autoUpdateInput && (this.autoUpdateInput = t.autoUpdateInput), "boolean" == typeof t.linkedCalendars && (this.linkedCalendars = t.linkedCalendars), "function" == typeof t.isInvalidDate && (this.isInvalidDate = t.isInvalidDate), "function" == typeof t.isCustomDate && (this.isCustomDate = t.isCustomDate), "boolean" == typeof t.alwaysShowCalendars && (this.alwaysShowCalendars = t.alwaysShowCalendars), 0 != this.locale.firstDay)
                for (var a = this.locale.firstDay; 0 < a;) this.locale.daysOfWeek.push(this.locale.daysOfWeek.shift()), a--;
            var s, o, r;
            if (void 0 === t.startDate && void 0 === t.endDate && F(this.element).is(":text")) {
                var l = F(this.element).val(),
                    c = l.split(this.locale.separator);
                s = o = null, 2 == c.length ? (s = O(c[0], this.locale.format), o = O(c[1], this.locale.format)) : this.singleDatePicker && "" !== l && (s = O(l, this.locale.format), o = O(l, this.locale.format)), null !== s && null !== o && (this.setStartDate(s), this.setEndDate(o))
            }
            if ("object" === yi(t.ranges)) {
                for (r in t.ranges) {
                    s = "string" == typeof t.ranges[r][0] ? O(t.ranges[r][0], this.locale.format) : O(t.ranges[r][0]), o = "string" == typeof t.ranges[r][1] ? O(t.ranges[r][1], this.locale.format) : O(t.ranges[r][1]), this.minDate && s.isBefore(this.minDate) && (s = this.minDate.clone());
                    var d = this.maxDate;
                    if (this.maxSpan && d && s.clone().add(this.maxSpan).isAfter(d) && (d = s.clone().add(this.maxSpan)), d && o.isAfter(d) && (o = d.clone()), !(this.minDate && o.isBefore(this.minDate, this.timepicker ? "minute" : "day") || d && s.isAfter(d, this.timepicker ? "minute" : "day"))) {
                        var u;
                        (u = document.createElement("textarea")).innerHTML = r;
                        n = u.value;
                        this.ranges[n] = [s, o]
                    }
                }
                var h = "<ul>";
                for (r in this.ranges) h += '<li data-range-key="' + r + '">' + r + "</li>";
                this.showCustomRangeLabel && (h += '<li data-range-key="' + this.locale.customRangeLabel + '">' + this.locale.customRangeLabel + "</li>"), h += "</ul>", this.container.find(".ranges").prepend(h)
            }
            "function" == typeof i && (this.callback = i), this.timePicker || (this.startDate = this.startDate.startOf("day"), this.endDate = this.endDate.endOf("day"), this.container.find(".calendar-time").hide()), this.timePicker && this.autoApply && (this.autoApply = !1), this.autoApply && this.container.addClass("auto-apply"), "object" === yi(t.ranges) && this.container.addClass("show-ranges"), this.singleDatePicker && (this.container.addClass("single"), this.container.find(".drp-calendar.left").addClass("single"), this.container.find(".drp-calendar.left").show(), this.container.find(".drp-calendar.right").hide(), this.timePicker || this.container.addClass("auto-apply")), (void 0 === t.ranges && !this.singleDatePicker || this.alwaysShowCalendars) && this.container.addClass("show-calendar"), this.container.addClass("opens" + this.opens), this.container.find(".applyBtn, .cancelBtn").addClass(this.buttonClasses), this.applyButtonClasses.length && this.container.find(".applyBtn").addClass(this.applyButtonClasses), this.cancelButtonClasses.length && this.container.find(".cancelBtn").addClass(this.cancelButtonClasses), this.container.find(".applyBtn").html(this.locale.applyLabel), this.container.find(".cancelBtn").html(this.locale.cancelLabel), this.container.find(".drp-calendar").on("click.daterangepicker", ".prev", F.proxy(this.clickPrev, this)).on("click.daterangepicker", ".next", F.proxy(this.clickNext, this)).on("mousedown.daterangepicker", "td.available", F.proxy(this.clickDate, this)).on("mouseenter.daterangepicker", "td.available", F.proxy(this.hoverDate, this)).on("change.daterangepicker", "select.yearselect", F.proxy(this.monthOrYearChanged, this)).on("change.daterangepicker", "select.monthselect", F.proxy(this.monthOrYearChanged, this)).on("change.daterangepicker", "select.hourselect,select.minuteselect,select.secondselect,select.ampmselect", F.proxy(this.timeChanged, this)), this.container.find(".ranges").on("click.daterangepicker", "li", F.proxy(this.clickRange, this)), this.container.find(".drp-buttons").on("click.daterangepicker", "button.applyBtn", F.proxy(this.clickApply, this)).on("click.daterangepicker", "button.cancelBtn", F.proxy(this.clickCancel, this)), this.element.is("input") || this.element.is("button") ? this.element.on({
                "click.daterangepicker": F.proxy(this.show, this),
                "focus.daterangepicker": F.proxy(this.show, this),
                "keyup.daterangepicker": F.proxy(this.elementChanged, this),
                "keydown.daterangepicker": F.proxy(this.keydown, this)
            }) : (this.element.on("click.daterangepicker", F.proxy(this.toggle, this)), this.element.on("keydown.daterangepicker", F.proxy(this.toggle, this))), this.updateElement()
        }
        return n.prototype = {
            constructor: n,
            setStartDate: function(e) {
                "string" == typeof e && (this.startDate = O(e, this.locale.format)), "object" === yi(e) && (this.startDate = O(e)), this.timePicker || (this.startDate = this.startDate.startOf("day")), this.timePicker && this.timePickerIncrement && this.startDate.minute(Math.round(this.startDate.minute() / this.timePickerIncrement) * this.timePickerIncrement), this.minDate && this.startDate.isBefore(this.minDate) && (this.startDate = this.minDate.clone(), this.timePicker && this.timePickerIncrement && this.startDate.minute(Math.round(this.startDate.minute() / this.timePickerIncrement) * this.timePickerIncrement)), this.maxDate && this.startDate.isAfter(this.maxDate) && (this.startDate = this.maxDate.clone(), this.timePicker && this.timePickerIncrement && this.startDate.minute(Math.floor(this.startDate.minute() / this.timePickerIncrement) * this.timePickerIncrement)), this.isShowing || this.updateElement(), this.updateMonthsInView()
            },
            setEndDate: function(e) {
                "string" == typeof e && (this.endDate = O(e, this.locale.format)), "object" === yi(e) && (this.endDate = O(e)), this.timePicker || (this.endDate = this.endDate.add(1, "d").startOf("day").subtract(1, "second")), this.timePicker && this.timePickerIncrement && this.endDate.minute(Math.round(this.endDate.minute() / this.timePickerIncrement) * this.timePickerIncrement), this.endDate.isBefore(this.startDate) && (this.endDate = this.startDate.clone()), this.maxDate && this.endDate.isAfter(this.maxDate) && (this.endDate = this.maxDate.clone()), this.maxSpan && this.startDate.clone().add(this.maxSpan).isBefore(this.endDate) && (this.endDate = this.startDate.clone().add(this.maxSpan)), this.previousRightTime = this.endDate.clone(), this.container.find(".drp-selected").html(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format)), this.isShowing || this.updateElement(), this.updateMonthsInView()
            },
            isInvalidDate: function() {
                return !1
            },
            isCustomDate: function() {
                return !1
            },
            updateView: function() {
                this.timePicker && (this.renderTimePicker("left"), this.renderTimePicker("right"), this.endDate ? this.container.find(".right .calendar-time select").removeAttr("disabled").removeClass("disabled") : this.container.find(".right .calendar-time select").attr("disabled", "disabled").addClass("disabled")), this.endDate && this.container.find(".drp-selected").html(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format)), this.updateMonthsInView(), this.updateCalendars(), this.updateFormInputs()
            },
            updateMonthsInView: function() {
                if (this.endDate) {
                    if (!this.singleDatePicker && this.leftCalendar.month && this.rightCalendar.month && (this.startDate.format("YYYY-MM") == this.leftCalendar.month.format("YYYY-MM") || this.startDate.format("YYYY-MM") == this.rightCalendar.month.format("YYYY-MM")) && (this.endDate.format("YYYY-MM") == this.leftCalendar.month.format("YYYY-MM") || this.endDate.format("YYYY-MM") == this.rightCalendar.month.format("YYYY-MM"))) return;
                    this.leftCalendar.month = this.startDate.clone().date(2), this.linkedCalendars || this.endDate.month() == this.startDate.month() && this.endDate.year() == this.startDate.year() ? this.rightCalendar.month = this.startDate.clone().date(2).add(1, "month") : this.rightCalendar.month = this.endDate.clone().date(2)
                } else this.leftCalendar.month.format("YYYY-MM") != this.startDate.format("YYYY-MM") && this.rightCalendar.month.format("YYYY-MM") != this.startDate.format("YYYY-MM") && (this.leftCalendar.month = this.startDate.clone().date(2), this.rightCalendar.month = this.startDate.clone().date(2).add(1, "month"));
                this.maxDate && this.linkedCalendars && !this.singleDatePicker && this.rightCalendar.month > this.maxDate && (this.rightCalendar.month = this.maxDate.clone().date(2), this.leftCalendar.month = this.maxDate.clone().date(2).subtract(1, "month"))
            },
            updateCalendars: function() {
                if (this.timePicker) {
                    var e, t, i, n;
                    if (this.endDate) {
                        if (e = parseInt(this.container.find(".left .hourselect").val(), 10), t = parseInt(this.container.find(".left .minuteselect").val(), 10), i = this.timePickerSeconds ? parseInt(this.container.find(".left .secondselect").val(), 10) : 0, !this.timePicker24Hour) "PM" === (n = this.container.find(".left .ampmselect").val()) && e < 12 && (e += 12), "AM" === n && 12 === e && (e = 0)
                    } else if (e = parseInt(this.container.find(".right .hourselect").val(), 10), t = parseInt(this.container.find(".right .minuteselect").val(), 10), i = this.timePickerSeconds ? parseInt(this.container.find(".right .secondselect").val(), 10) : 0, !this.timePicker24Hour) "PM" === (n = this.container.find(".right .ampmselect").val()) && e < 12 && (e += 12), "AM" === n && 12 === e && (e = 0);
                    this.leftCalendar.month.hour(e).minute(t).second(i), this.rightCalendar.month.hour(e).minute(t).second(i)
                }
                this.renderCalendar("left"), this.renderCalendar("right"), this.container.find(".ranges li").removeClass("active"), null != this.endDate && this.calculateChosenLabel()
            },
            renderCalendar: function(e) {
                var t, i = (t = "left" == e ? this.leftCalendar : this.rightCalendar).month.month(),
                    n = t.month.year(),
                    a = t.month.hour(),
                    s = t.month.minute(),
                    o = t.month.second(),
                    r = O([n, i]).daysInMonth(),
                    l = O([n, i, 1]),
                    c = O([n, i, r]),
                    d = O(l).subtract(1, "month").month(),
                    u = O(l).subtract(1, "month").year(),
                    h = O([u, d]).daysInMonth(),
                    p = l.day();
                (t = []).firstDay = l, t.lastDay = c;
                for (var f = 0; f < 6; f++) t[f] = [];
                var m = h - p + this.locale.firstDay + 1;
                h < m && (m -= 7), p == this.locale.firstDay && (m = h - 6);
                for (var g = O([u, d, m, 12, s, o]), y = (f = 0, 0), v = 0; f < 42; f++, y++, g = O(g).add(24, "hour")) 0 < f && y % 7 == 0 && (y = 0, v++), t[v][y] = g.clone().hour(a).minute(s).second(o), g.hour(12), this.minDate && t[v][y].format("YYYY-MM-DD") == this.minDate.format("YYYY-MM-DD") && t[v][y].isBefore(this.minDate) && "left" == e && (t[v][y] = this.minDate.clone()), this.maxDate && t[v][y].format("YYYY-MM-DD") == this.maxDate.format("YYYY-MM-DD") && t[v][y].isAfter(this.maxDate) && "right" == e && (t[v][y] = this.maxDate.clone());
                "left" == e ? this.leftCalendar.calendar = t : this.rightCalendar.calendar = t;
                var w = "left" == e ? this.minDate : this.startDate,
                    b = this.maxDate,
                    D = ("left" == e ? this.startDate : this.endDate, this.locale.direction, '<table class="table-condensed">');
                D += "<thead>", D += "<tr>", (this.showWeekNumbers || this.showISOWeekNumbers) && (D += "<th></th>"), w && !w.isBefore(t.firstDay) || this.linkedCalendars && "left" != e ? D += "<th></th>" : D += '<th class="prev available"><span></span></th>';
                var k = this.locale.monthNames[t[1][1].month()] + t[1][1].format(" YYYY");
                if (this.showDropdowns) {
                    for (var x = t[1][1].month(), C = t[1][1].year(), S = b && b.year() || this.maxYear, M = w && w.year() || this.minYear, _ = C == M, T = C == S, I = '<select class="monthselect">', L = 0; L < 12; L++)(!_ || L >= w.month()) && (!T || L <= b.month()) ? I += "<option value='" + L + "'" + (L === x ? " selected='selected'" : "") + ">" + this.locale.monthNames[L] + "</option>" : I += "<option value='" + L + "'" + (L === x ? " selected='selected'" : "") + " disabled='disabled'>" + this.locale.monthNames[L] + "</option>";
                    I += "</select>";
                    for (var E = '<select class="yearselect">', P = M; P <= S; P++) E += '<option value="' + P + '"' + (P === C ? ' selected="selected"' : "") + ">" + P + "</option>";
                    k = I + (E += "</select>")
                }
                if (D += '<th colspan="5" class="month">' + k + "</th>", b && !b.isAfter(t.lastDay) || this.linkedCalendars && "right" != e && !this.singleDatePicker ? D += "<th></th>" : D += '<th class="next available"><span></span></th>', D += "</tr>", D += "<tr>", (this.showWeekNumbers || this.showISOWeekNumbers) && (D += '<th class="week">' + this.locale.weekLabel + "</th>"), F.each(this.locale.daysOfWeek, function(e, t) {
                        D += "<th>" + t + "</th>"
                    }), D += "</tr>", D += "</thead>", D += "<tbody>", null == this.endDate && this.maxSpan) {
                    var A = this.startDate.clone().add(this.maxSpan).endOf("day");
                    b && !A.isBefore(b) || (b = A)
                }
                for (v = 0; v < 6; v++) {
                    D += "<tr>", this.showWeekNumbers ? D += '<td class="week">' + t[v][0].week() + "</td>" : this.showISOWeekNumbers && (D += '<td class="week">' + t[v][0].isoWeek() + "</td>");
                    for (y = 0; y < 7; y++) {
                        var Y = [];
                        t[v][y].isSame(new Date, "day") && Y.push("today"), 5 < t[v][y].isoWeekday() && Y.push("weekend"), t[v][y].month() != t[1][1].month() && Y.push("off"), this.minDate && t[v][y].isBefore(this.minDate, "day") && Y.push("off", "disabled"), b && t[v][y].isAfter(b, "day") && Y.push("off", "disabled"), this.isInvalidDate(t[v][y]) && Y.push("off", "disabled"), t[v][y].format("YYYY-MM-DD") == this.startDate.format("YYYY-MM-DD") && Y.push("active", "start-date"), null != this.endDate && t[v][y].format("YYYY-MM-DD") == this.endDate.format("YYYY-MM-DD") && Y.push("active", "end-date"), null != this.endDate && t[v][y] > this.startDate && t[v][y] < this.endDate && Y.push("in-range");
                        var j = this.isCustomDate(t[v][y]);
                        !1 !== j && ("string" == typeof j ? Y.push(j) : Array.prototype.push.apply(Y, j));
                        var $ = "",
                            K = !1;
                        for (f = 0; f < Y.length; f++) $ += Y[f] + " ", "disabled" == Y[f] && (K = !0);
                        K || ($ += "available"), D += '<td class="' + $.replace(/^\s+|\s+$/g, "") + '" data-title="r' + v + "c" + y + '">' + t[v][y].date() + "</td>"
                    }
                    D += "</tr>"
                }
                D += "</tbody>", D += "</table>", this.container.find(".drp-calendar." + e + " .calendar-table").html(D)
            },
            renderTimePicker: function(e) {
                if ("right" != e || this.endDate) {
                    var t, i, n, a = this.maxDate;
                    if (!this.maxSpan || this.maxDate && !this.startDate.clone().add(this.maxSpan).isAfter(this.maxDate) || (a = this.startDate.clone().add(this.maxSpan)), "left" == e) i = this.startDate.clone(), n = this.minDate;
                    else if ("right" == e) {
                        i = this.endDate.clone(), n = this.startDate;
                        var s = this.container.find(".drp-calendar.right .calendar-time");
                        if ("" != s.html() && (i.hour(i.hour() || s.find(".hourselect option:selected").val()), i.minute(i.minute() || s.find(".minuteselect option:selected").val()), i.second(i.second() || s.find(".secondselect option:selected").val()), !this.timePicker24Hour)) {
                            var o = s.find(".ampmselect option:selected").val();
                            "PM" === o && i.hour() < 12 && i.hour(i.hour() + 12), "AM" === o && 12 === i.hour() && i.hour(0)
                        }
                        i.isBefore(this.startDate) && (i = this.startDate.clone()), a && i.isAfter(a) && (i = a.clone())
                    }
                    t = '<select class="hourselect">';
                    for (var r = this.timePicker24Hour ? 0 : 1, l = this.timePicker24Hour ? 23 : 12, c = r; c <= l; c++) {
                        var d = c;
                        this.timePicker24Hour || (d = 12 <= i.hour() ? 12 == c ? 12 : c + 12 : 12 == c ? 0 : c);
                        var u = i.clone().hour(d),
                            h = !1;
                        n && u.minute(59).isBefore(n) && (h = !0), a && u.minute(0).isAfter(a) && (h = !0), d != i.hour() || h ? t += h ? '<option value="' + c + '" disabled="disabled" class="disabled">' + c + "</option>" : '<option value="' + c + '">' + c + "</option>" : t += '<option value="' + c + '" selected="selected">' + c + "</option>"
                    }
                    t += "</select> ", t += ': <select class="minuteselect">';
                    for (c = 0; c < 60; c += this.timePickerIncrement) {
                        var p = c < 10 ? "0" + c : c;
                        u = i.clone().minute(c), h = !1;
                        n && u.second(59).isBefore(n) && (h = !0), a && u.second(0).isAfter(a) && (h = !0), i.minute() != c || h ? t += h ? '<option value="' + c + '" disabled="disabled" class="disabled">' + p + "</option>" : '<option value="' + c + '">' + p + "</option>" : t += '<option value="' + c + '" selected="selected">' + p + "</option>"
                    }
                    if (t += "</select> ", this.timePickerSeconds) {
                        t += ': <select class="secondselect">';
                        for (c = 0; c < 60; c++) {
                            p = c < 10 ? "0" + c : c, u = i.clone().second(c), h = !1;
                            n && u.isBefore(n) && (h = !0), a && u.isAfter(a) && (h = !0), i.second() != c || h ? t += h ? '<option value="' + c + '" disabled="disabled" class="disabled">' + p + "</option>" : '<option value="' + c + '">' + p + "</option>" : t += '<option value="' + c + '" selected="selected">' + p + "</option>"
                        }
                        t += "</select> "
                    }
                    if (!this.timePicker24Hour) {
                        t += '<select class="ampmselect">';
                        var f = "",
                            m = "";
                        n && i.clone().hour(12).minute(0).second(0).isBefore(n) && (f = ' disabled="disabled" class="disabled"'), a && i.clone().hour(0).minute(0).second(0).isAfter(a) && (m = ' disabled="disabled" class="disabled"'), 12 <= i.hour() ? t += '<option value="AM"' + f + '>AM</option><option value="PM" selected="selected"' + m + ">PM</option>" : t += '<option value="AM" selected="selected"' + f + '>AM</option><option value="PM"' + m + ">PM</option>", t += "</select>"
                    }
                    this.container.find(".drp-calendar." + e + " .calendar-time").html(t)
                }
            },
            updateFormInputs: function() {
                this.singleDatePicker || this.endDate && (this.startDate.isBefore(this.endDate) || this.startDate.isSame(this.endDate)) ? this.container.find("button.applyBtn").removeAttr("disabled") : this.container.find("button.applyBtn").attr("disabled", "disabled")
            },
            move: function() {
                var e, t = {
                        top: 0,
                        left: 0
                    },
                    i = F(window).width();
                this.parentEl.is("body") || (t = {
                    top: this.parentEl.offset().top - this.parentEl.scrollTop(),
                    left: this.parentEl.offset().left - this.parentEl.scrollLeft()
                }, i = this.parentEl[0].clientWidth + this.parentEl.offset().left), e = "up" == this.drops ? this.element.offset().top - this.container.outerHeight() - t.top : this.element.offset().top + this.element.outerHeight() - t.top, this.container["up" == this.drops ? "addClass" : "removeClass"]("drop-up"), "left" == this.opens ? (this.container.css({
                    top: e,
                    right: i - this.element.offset().left - this.element.outerWidth(),
                    left: "auto"
                }), this.container.offset().left < 0 && this.container.css({
                    right: "auto",
                    left: 9
                })) : "center" == this.opens ? (this.container.css({
                    top: e,
                    left: this.element.offset().left - t.left + this.element.outerWidth() / 2 - this.container.outerWidth() / 2,
                    right: "auto"
                }), this.container.offset().left < 0 && this.container.css({
                    right: "auto",
                    left: 9
                })) : (this.container.css({
                    top: e,
                    left: this.element.offset().left - t.left,
                    right: "auto"
                }), this.container.offset().left + this.container.outerWidth() > F(window).width() && this.container.css({
                    left: "auto",
                    right: 0
                }))
            },
            show: function(e) {
                this.isShowing || (this._outsideClickProxy = F.proxy(function(e) {
                    this.outsideClick(e)
                }, this), F(document).on("mousedown.daterangepicker", this._outsideClickProxy).on("touchend.daterangepicker", this._outsideClickProxy).on("click.daterangepicker", "[data-toggle=dropdown]", this._outsideClickProxy).on("focusin.daterangepicker", this._outsideClickProxy), F(window).on("resize.daterangepicker", F.proxy(function(e) {
                    this.move(e)
                }, this)), this.oldStartDate = this.startDate.clone(), this.oldEndDate = this.endDate.clone(), this.previousRightTime = this.endDate.clone(), this.updateView(), this.container.show(), this.move(), this.element.trigger("show.daterangepicker", this), this.isShowing = !0)
            },
            hide: function(e) {
                this.isShowing && (this.endDate || (this.startDate = this.oldStartDate.clone(), this.endDate = this.oldEndDate.clone()), this.startDate.isSame(this.oldStartDate) && this.endDate.isSame(this.oldEndDate) || this.callback(this.startDate.clone(), this.endDate.clone(), this.chosenLabel), this.updateElement(), F(document).off(".daterangepicker"), F(window).off(".daterangepicker"), this.container.hide(), this.element.trigger("hide.daterangepicker", this), this.isShowing = !1)
            },
            toggle: function(e) {
                this.isShowing ? this.hide() : this.show()
            },
            outsideClick: function(e) {
                var t = F(e.target);
                "focusin" == e.type || t.closest(this.element).length || t.closest(this.container).length || t.closest(".calendar-table").length || (this.hide(), this.element.trigger("outsideClick.daterangepicker", this))
            },
            showCalendars: function() {
                this.container.addClass("show-calendar"), this.move(), this.element.trigger("showCalendar.daterangepicker", this)
            },
            hideCalendars: function() {
                this.container.removeClass("show-calendar"), this.element.trigger("hideCalendar.daterangepicker", this)
            },
            clickRange: function(e) {
                var t = e.target.getAttribute("data-range-key");
                if ((this.chosenLabel = t) == this.locale.customRangeLabel) this.showCalendars();
                else {
                    var i = this.ranges[t];
                    this.startDate = i[0], this.endDate = i[1], this.timePicker || (this.startDate.startOf("day"), this.endDate.endOf("day")), this.alwaysShowCalendars || this.hideCalendars(), this.clickApply()
                }
            },
            clickPrev: function(e) {
                F(e.target).parents(".drp-calendar").hasClass("left") ? (this.leftCalendar.month.subtract(1, "month"), this.linkedCalendars && this.rightCalendar.month.subtract(1, "month")) : this.rightCalendar.month.subtract(1, "month"), this.updateCalendars()
            },
            clickNext: function(e) {
                F(e.target).parents(".drp-calendar").hasClass("left") ? this.leftCalendar.month.add(1, "month") : (this.rightCalendar.month.add(1, "month"), this.linkedCalendars && this.leftCalendar.month.add(1, "month")), this.updateCalendars()
            },
            hoverDate: function(e) {
                if (F(e.target).hasClass("available")) {
                    var t = F(e.target).attr("data-title"),
                        i = t.substr(1, 1),
                        n = t.substr(3, 1),
                        o = F(e.target).parents(".drp-calendar").hasClass("left") ? this.leftCalendar.calendar[i][n] : this.rightCalendar.calendar[i][n],
                        r = this.leftCalendar,
                        l = this.rightCalendar,
                        c = this.startDate;
                    this.endDate || this.container.find(".drp-calendar tbody td").each(function(e, t) {
                        if (!F(t).hasClass("week")) {
                            var i = F(t).attr("data-title"),
                                n = i.substr(1, 1),
                                a = i.substr(3, 1),
                                s = F(t).parents(".drp-calendar").hasClass("left") ? r.calendar[n][a] : l.calendar[n][a];
                            s.isAfter(c) && s.isBefore(o) || s.isSame(o, "day") ? F(t).addClass("in-range") : F(t).removeClass("in-range")
                        }
                    })
                }
            },
            clickDate: function(e) {
                if (F(e.target).hasClass("available")) {
                    var t = F(e.target).attr("data-title"),
                        i = t.substr(1, 1),
                        n = t.substr(3, 1),
                        a = F(e.target).parents(".drp-calendar").hasClass("left") ? this.leftCalendar.calendar[i][n] : this.rightCalendar.calendar[i][n];
                    if (this.endDate || a.isBefore(this.startDate, "day")) {
                        if (this.timePicker) {
                            var s = parseInt(this.container.find(".left .hourselect").val(), 10);
                            if (!this.timePicker24Hour) "PM" === (l = this.container.find(".left .ampmselect").val()) && s < 12 && (s += 12), "AM" === l && 12 === s && (s = 0);
                            var o = parseInt(this.container.find(".left .minuteselect").val(), 10),
                                r = this.timePickerSeconds ? parseInt(this.container.find(".left .secondselect").val(), 10) : 0;
                            a = a.clone().hour(s).minute(o).second(r)
                        }
                        this.endDate = null, this.setStartDate(a.clone())
                    } else if (!this.endDate && a.isBefore(this.startDate)) this.setEndDate(this.startDate.clone());
                    else {
                        if (this.timePicker) {
                            var l;
                            s = parseInt(this.container.find(".right .hourselect").val(), 10);
                            if (!this.timePicker24Hour) "PM" === (l = this.container.find(".right .ampmselect").val()) && s < 12 && (s += 12), "AM" === l && 12 === s && (s = 0);
                            o = parseInt(this.container.find(".right .minuteselect").val(), 10), r = this.timePickerSeconds ? parseInt(this.container.find(".right .secondselect").val(), 10) : 0;
                            a = a.clone().hour(s).minute(o).second(r)
                        }
                        this.setEndDate(a.clone()), this.autoApply && (this.calculateChosenLabel(), this.clickApply())
                    }
                    this.singleDatePicker && (this.setEndDate(this.startDate), this.timePicker || this.clickApply()), this.updateView(), e.stopPropagation()
                }
            },
            calculateChosenLabel: function() {
                var e = !0,
                    t = 0;
                for (var i in this.ranges) {
                    if (this.timePicker) {
                        var n = this.timePickerSeconds ? "YYYY-MM-DD hh:mm:ss" : "YYYY-MM-DD hh:mm";
                        if (this.startDate.format(n) == this.ranges[i][0].format(n) && this.endDate.format(n) == this.ranges[i][1].format(n)) {
                            e = !1, this.chosenLabel = this.container.find(".ranges li:eq(" + t + ")").addClass("active").attr("data-range-key");
                            break
                        }
                    } else if (this.startDate.format("YYYY-MM-DD") == this.ranges[i][0].format("YYYY-MM-DD") && this.endDate.format("YYYY-MM-DD") == this.ranges[i][1].format("YYYY-MM-DD")) {
                        e = !1, this.chosenLabel = this.container.find(".ranges li:eq(" + t + ")").addClass("active").attr("data-range-key");
                        break
                    }
                    t++
                }
                e && (this.showCustomRangeLabel ? this.chosenLabel = this.container.find(".ranges li:last").addClass("active").attr("data-range-key") : this.chosenLabel = null, this.showCalendars())
            },
            clickApply: function(e) {
                this.hide(), this.element.trigger("apply.daterangepicker", this)
            },
            clickCancel: function(e) {
                this.startDate = this.oldStartDate, this.endDate = this.oldEndDate, this.hide(), this.element.trigger("cancel.daterangepicker", this)
            },
            monthOrYearChanged: function(e) {
                var t = F(e.target).closest(".drp-calendar").hasClass("left"),
                    i = t ? "left" : "right",
                    n = this.container.find(".drp-calendar." + i),
                    a = parseInt(n.find(".monthselect").val(), 10),
                    s = n.find(".yearselect").val();
                t || (s < this.startDate.year() || s == this.startDate.year() && a < this.startDate.month()) && (a = this.startDate.month(), s = this.startDate.year()), this.minDate && (s < this.minDate.year() || s == this.minDate.year() && a < this.minDate.month()) && (a = this.minDate.month(), s = this.minDate.year()), this.maxDate && (s > this.maxDate.year() || s == this.maxDate.year() && a > this.maxDate.month()) && (a = this.maxDate.month(), s = this.maxDate.year()), t ? (this.leftCalendar.month.month(a).year(s), this.linkedCalendars && (this.rightCalendar.month = this.leftCalendar.month.clone().add(1, "month"))) : (this.rightCalendar.month.month(a).year(s), this.linkedCalendars && (this.leftCalendar.month = this.rightCalendar.month.clone().subtract(1, "month"))), this.updateCalendars()
            },
            timeChanged: function(e) {
                var t = F(e.target).closest(".drp-calendar"),
                    i = t.hasClass("left"),
                    n = parseInt(t.find(".hourselect").val(), 10),
                    a = parseInt(t.find(".minuteselect").val(), 10),
                    s = this.timePickerSeconds ? parseInt(t.find(".secondselect").val(), 10) : 0;
                if (!this.timePicker24Hour) {
                    var o = t.find(".ampmselect").val();
                    "PM" === o && n < 12 && (n += 12), "AM" === o && 12 === n && (n = 0)
                }
                if (i) {
                    var r = this.startDate.clone();
                    r.hour(n), r.minute(a), r.second(s), this.setStartDate(r), this.singleDatePicker ? this.endDate = this.startDate.clone() : this.endDate && this.endDate.format("YYYY-MM-DD") == r.format("YYYY-MM-DD") && this.endDate.isBefore(r) && this.setEndDate(r.clone())
                } else if (this.endDate) {
                    var l = this.endDate.clone();
                    l.hour(n), l.minute(a), l.second(s), this.setEndDate(l)
                }
                this.updateCalendars(), this.updateFormInputs(), this.renderTimePicker("left"), this.renderTimePicker("right")
            },
            elementChanged: function() {
                if (this.element.is("input") && this.element.val().length) {
                    var e = this.element.val().split(this.locale.separator),
                        t = null,
                        i = null;
                    2 === e.length && (t = O(e[0], this.locale.format), i = O(e[1], this.locale.format)), !this.singleDatePicker && null !== t && null !== i || (i = t = O(this.element.val(), this.locale.format)), t.isValid() && i.isValid() && (this.setStartDate(t), this.setEndDate(i), this.updateView())
                }
            },
            keydown: function(e) {
                9 !== e.keyCode && 13 !== e.keyCode || this.hide(), 27 === e.keyCode && (e.preventDefault(), e.stopPropagation(), this.hide())
            },
            updateElement: function() {
                if (this.element.is("input") && this.autoUpdateInput) {
                    var e = this.startDate.format(this.locale.format);
                    this.singleDatePicker || (e += this.locale.separator + this.endDate.format(this.locale.format)), e !== this.element.val() && this.element.val(e).trigger("change")
                }
            },
            remove: function() {
                this.container.remove(), this.element.off(".daterangepicker"), this.element.removeData()
            }
        }, F.fn.daterangepicker = function(e, t) {
            var i = F.extend(!0, {}, F.fn.daterangepicker.defaultOptions, e);
            return this.each(function() {
                var e = F(this);
                e.data("daterangepicker") && e.data("daterangepicker").remove(), e.data("daterangepicker", new n(e, i, t))
            }), this
        }, n
    }), MyListing.Datepicker = function(e, t) {
            this.el = jQuery(e), this.el.length && this.el.parent().hasClass("datepicker-wrapper") && (jQuery('<input type="text" class="display-value" readonly><i class="mi clear_all c-hide reset-value"></i>').insertAfter(this.el), this.el.attr("autocomplete", "off").attr("readonly", !0).addClass("picker"), this.parent = this.el.parent(), this.value = moment(this.el.val()), this.mask = this.parent.find(".display-value"), this.reset = this.parent.find(".reset-value"), this.args = jQuery.extend({
                timepicker: !1
            }, t), this.format = !0 === this.args.timepicker ? "YYYY-MM-DD HH:mm:ss" : "YYYY-MM-DD", this.displayFormat = !0 === this.args.timepicker ? CASE27.l10n.datepicker.dateTimeFormat : CASE27.l10n.datepicker.format, this.mask.attr("placeholder", this.el.attr("placeholder")), this.picker = this.el.daterangepicker({
                autoUpdateInput: !1,
                showDropdowns: !0,
                singleDatePicker: !0,
                timePicker24Hour: CASE27.l10n.datepicker.timePicker24Hour,
                locale: jQuery.extend({}, CASE27.l10n.datepicker, {
                    format: this.format
                }),
                timePicker: this.args.timepicker
            }), this.drp = this.picker.data("daterangepicker"), this.picker.on("apply.daterangepicker", this.apply.bind(this)), this.el.on("change", this.change.bind(this)), this.updateInputValues(), this.reset.click(function(e) {
                this.value = moment(""), this.el.trigger("change")
            }.bind(this)))
        }, MyListing.Datepicker.prototype.apply = function(e, t) {
            this.value = t.startDate, this.el.trigger("change")
        }, MyListing.Datepicker.prototype.change = function() {
            this.updateInputValues(), this.fireChangeEvent({
                value: this.el.val(),
                mask: this.mask.val()
            })
        }, MyListing.Datepicker.prototype.updateInputValues = function() {
            var e = this.value.isValid() ? this.value.clone().locale("en").format(this.format) : "",
                t = this.value.isValid() ? this.value.format(this.displayFormat) : "";
            this.el.val(e), this.mask.val(t), "" === e ? this.reset.removeClass("c-show").addClass("c-hide") : this.reset.addClass("c-show").removeClass("c-hide")
        }, MyListing.Datepicker.prototype.fireChangeEvent = function(e) {
            var t = document.createEvent("CustomEvent");
            t.initCustomEvent("datepicker:change", !1, !0, e), this.el.get(0).dispatchEvent(t)
        }, MyListing.Datepicker.prototype.setMinDate = function(e) {
            this.drp.minDate = e, this.drp.minDate.isAfter(this.drp.startDate) && (this.value = this.drp.startDate = this.drp.endDate = this.drp.minDate, this.el.trigger("change"))
        }, MyListing.Datepicker.prototype.setValue = function(e) {
            this.value = e, this.el.trigger("change")
        }, MyListing.Datepicker.prototype.do = function(e) {
            e(this)
        }, MyListing.Datepicker.prototype.getValue = function() {
            return this.value
        }, jQuery(function(n) {
            n(".mylisting-datepicker").each(function(e, t) {
                var i = n(t).data("options");
                "object" !== yi(i) && (i = {}), new MyListing.Datepicker(t, i)
            })
        }), e = window, t = function() {
            return function(p, n, e, t) {
                var f = {
                    features: null,
                    bind: function(e, t, i, n) {
                        var a = (n ? "remove" : "add") + "EventListener";
                        t = t.split(" ");
                        for (var s = 0; s < t.length; s++) t[s] && e[a](t[s], i, !1)
                    },
                    isArray: function(e) {
                        return e instanceof Array
                    },
                    createEl: function(e, t) {
                        var i = document.createElement(t || "div");
                        return e && (i.className = e), i
                    },
                    getScrollY: function() {
                        var e = window.pageYOffset;
                        return void 0 !== e ? e : document.documentElement.scrollTop
                    },
                    unbind: function(e, t, i) {
                        f.bind(e, t, i, !0)
                    },
                    removeClass: function(e, t) {
                        var i = new RegExp("(\\s|^)" + t + "(\\s|$)");
                        e.className = e.className.replace(i, " ").replace(/^\s\s*/, "").replace(/\s\s*$/, "")
                    },
                    addClass: function(e, t) {
                        f.hasClass(e, t) || (e.className += (e.className ? " " : "") + t)
                    },
                    hasClass: function(e, t) {
                        return e.className && new RegExp("(^|\\s)" + t + "(\\s|$)").test(e.className)
                    },
                    getChildByClass: function(e, t) {
                        for (var i = e.firstChild; i;) {
                            if (f.hasClass(i, t)) return i;
                            i = i.nextSibling
                        }
                    },
                    arraySearch: function(e, t, i) {
                        for (var n = e.length; n--;)
                            if (e[n][i] === t) return n;
                        return -1
                    },
                    extend: function(e, t, i) {
                        for (var n in t)
                            if (t.hasOwnProperty(n)) {
                                if (i && e.hasOwnProperty(n)) continue;
                                e[n] = t[n]
                            }
                    },
                    easing: {
                        sine: {
                            out: function(e) {
                                return Math.sin(e * (Math.PI / 2))
                            },
                            inOut: function(e) {
                                return -(Math.cos(Math.PI * e) - 1) / 2
                            }
                        },
                        cubic: {
                            out: function(e) {
                                return --e * e * e + 1
                            }
                        }
                    },
                    detectFeatures: function() {
                        if (f.features) return f.features;
                        var e = f.createEl().style,
                            t = "",
                            i = {};
                        if (i.oldIE = document.all && !document.addEventListener, i.touch = "ontouchstart" in window, window.requestAnimationFrame && (i.raf = window.requestAnimationFrame, i.caf = window.cancelAnimationFrame), i.pointerEvent = !!window.PointerEvent || navigator.msPointerEnabled, !i.pointerEvent) {
                            var n = navigator.userAgent;
                            if (/iP(hone|od)/.test(navigator.platform)) {
                                var a = navigator.appVersion.match(/OS (\d+)_(\d+)_?(\d+)?/);
                                a && 0 < a.length && 1 <= (a = parseInt(a[1], 10)) && a < 8 && (i.isOldIOSPhone = !0)
                            }
                            var s = n.match(/Android\s([0-9\.]*)/),
                                o = s ? s[1] : 0;
                            1 <= (o = parseFloat(o)) && (o < 4.4 && (i.isOldAndroid = !0), i.androidVersion = o), i.isMobileOpera = /opera mini|opera mobi/i.test(n)
                        }
                        for (var r, l, c = ["transform", "perspective", "animationName"], d = ["", "webkit", "Moz", "ms", "O"], u = 0; u < 4; u++) {
                            t = d[u];
                            for (var h = 0; h < 3; h++) r = c[h], l = t + (t ? r.charAt(0).toUpperCase() + r.slice(1) : r), !i[r] && l in e && (i[r] = l);
                            t && !i.raf && (t = t.toLowerCase(), i.raf = window[t + "RequestAnimationFrame"], i.raf && (i.caf = window[t + "CancelAnimationFrame"] || window[t + "CancelRequestAnimationFrame"]))
                        }
                        if (!i.raf) {
                            var p = 0;
                            i.raf = function(e) {
                                var t = (new Date).getTime(),
                                    i = Math.max(0, 16 - (t - p)),
                                    n = window.setTimeout(function() {
                                        e(t + i)
                                    }, i);
                                return p = t + i, n
                            }, i.caf = function(e) {
                                clearTimeout(e)
                            }
                        }
                        return i.svg = !!document.createElementNS && !!document.createElementNS("http://www.w3.org/2000/svg", "svg").createSVGRect, f.features = i
                    }
                };
                f.detectFeatures(), f.features.oldIE && (f.bind = function(e, t, i, n) {
                    t = t.split(" ");
                    for (var a, s = (n ? "detach" : "attach") + "Event", o = function() {
                            i.handleEvent.call(i)
                        }, r = 0; r < t.length; r++)
                        if (a = t[r])
                            if ("object" === yi(i) && i.handleEvent) {
                                if (n) {
                                    if (!i["oldIE" + a]) return !1
                                } else i["oldIE" + a] = o;
                                e[s]("on" + a, i["oldIE" + a])
                            } else e[s]("on" + a, i)
                });
                var m = this,
                    g = {
                        allowPanToNext: !0,
                        spacing: .12,
                        bgOpacity: 1,
                        mouseUsed: !1,
                        loop: !0,
                        pinchToClose: !0,
                        closeOnScroll: !0,
                        closeOnVerticalDrag: !0,
                        verticalDragRange: .75,
                        hideAnimationDuration: 333,
                        showAnimationDuration: 333,
                        showHideOpacity: !1,
                        focus: !0,
                        escKey: !0,
                        arrowKeys: !0,
                        mainScrollEndFriction: .35,
                        panEndFriction: .35,
                        isClickableElement: function(e) {
                            return "A" === e.tagName
                        },
                        getDoubleTapZoom: function(e, t) {
                            return e ? 1 : t.initialZoomLevel < .7 ? 1 : 1.33
                        },
                        maxSpreadZoom: 1.33,
                        modal: !0,
                        scaleMode: "fit"
                    };
                f.extend(g, t);

                function i(e, t) {
                    f.extend(m, t.publicMethods), Ge.push(e)
                }

                function l(e) {
                    var t = Nt();
                    return t - 1 < e ? e - t : e < 0 ? t + e : e
                }

                function s(e, t) {
                    return et[e] || (et[e] = []), et[e].push(t)
                }

                function y(e) {
                    var t = et[e];
                    if (t) {
                        var i = Array.prototype.slice.call(arguments);
                        i.shift();
                        for (var n = 0; n < t.length; n++) t[n].apply(m, i)
                    }
                }

                function d() {
                    return (new Date).getTime()
                }

                function v(e) {
                    Fe = e, m.bg.style.opacity = e * g.bgOpacity
                }

                function a(e, t, i, n, a) {
                    (!Je || a && a !== m.currItem) && (n /= a ? a.fitRatio : m.currItem.fitRatio), e[re] = G + t + "px, " + i + "px" + X + " scale(" + n + ")"
                }

                function u(e, t) {
                    if (!g.loop && t) {
                        var i = N + (qe.x * Ve - e) / qe.x,
                            n = Math.round(e - bt.x);
                        (i < 0 && 0 < n || i >= Nt() - 1 && n < 0) && (e = bt.x + n * g.mainScrollEndFriction)
                    }
                    bt.x = e, nt(e, Q)
                }

                function c(e, t) {
                    var i = Dt[e] - We[e];
                    return Ne[e] + He[e] + i - t / z * i
                }

                function w(e, t) {
                    e.x = t.x, e.y = t.y, t.id && (e.id = t.id)
                }

                function h(e) {
                    e.x = Math.round(e.x), e.y = Math.round(e.y)
                }

                function o() {
                    at && (f.unbind(document, "mousemove", o), f.addClass(p, "pswp--has_mouse"), g.mouseUsed = !0, y("mouseUsed")), at = setTimeout(function() {
                        at = null
                    }, 100)
                }

                function b(e, t) {
                    var i = qt(m.currItem, Ue, e);
                    return t && (Ae = i), i
                }

                function D(e) {
                    return (e = e || m.currItem).initialZoomLevel
                }

                function k(e) {
                    return 0 < (e = e || m.currItem).w ? g.maxSpreadZoom : 1
                }

                function x(e, t, i, n) {
                    return n === m.currItem.initialZoomLevel ? (i[e] = m.currItem.initialPosition[e], !0) : (i[e] = c(e, n), i[e] > t.min[e] ? (i[e] = t.min[e], !0) : i[e] < t.max[e] && (i[e] = t.max[e], !0))
                }

                function r(e) {
                    var t = "";
                    g.escKey && 27 === e.keyCode ? t = "close" : g.arrowKeys && (37 === e.keyCode ? t = "prev" : 39 === e.keyCode && (t = "next")), t && (e.ctrlKey || e.altKey || e.shiftKey || e.metaKey || (e.preventDefault ? e.preventDefault() : e.returnValue = !1, m[t]()))
                }

                function C(e) {
                    e && (_e || Me || je || ke) && (e.preventDefault(), e.stopPropagation())
                }

                function S() {
                    m.setScrollOffset(0, f.getScrollY())
                }

                function M(e) {
                    st[e] && (st[e].raf && ue(st[e].raf), ot--, delete st[e])
                }

                function _(e) {
                    st[e] && M(e), st[e] || (ot++, st[e] = {})
                }

                function T() {
                    for (var e in st) st.hasOwnProperty(e) && M(e)
                }

                function I(t, i, n, a, s, o, r) {
                    var l, c = d();
                    _(t),
                        function e() {
                            if (st[t]) {
                                if (l = d() - c, a <= l) return M(t), o(n), void(r && r());
                                o((n - i) * s(l / a) + i), st[t].raf = de(e)
                            }
                        }()
                }

                function L(e, t) {
                    return gt.x = Math.abs(e.x - t.x), gt.y = Math.abs(e.y - t.y), Math.sqrt(gt.x * gt.x + gt.y * gt.y)
                }

                function E(e, t) {
                    return Ct.prevent = ! function e(t, i) {
                        return !(!t || t === document) && (!(t.getAttribute("class") && -1 < t.getAttribute("class").indexOf("pswp__scroll-wrap")) && (i(t) ? t : e(t.parentNode, i)))
                    }(e.target, g.isClickableElement), y("preventDragEvent", e, t, Ct), Ct.prevent
                }

                function P(e, t) {
                    return t.x = e.pageX, t.y = e.pageY, t.id = e.identifier, t
                }

                function A(e, t, i) {
                    i.x = .5 * (e.x + t.x), i.y = .5 * (e.y + t.y)
                }

                function Y() {
                    var e = Qe.y - m.currItem.initialPosition.y;
                    return 1 - Math.abs(e / (Ue.y / 2))
                }

                function j(e) {
                    for (; 0 < _t.length;) _t.pop();
                    return le ? (Be = 0, pt.forEach(function(e) {
                        0 === Be ? _t[0] = e : 1 === Be && (_t[1] = e), Be++
                    })) : -1 < e.type.indexOf("touch") ? e.touches && 0 < e.touches.length && (_t[0] = P(e.touches[0], St), 1 < e.touches.length && (_t[1] = P(e.touches[1], Mt))) : (St.x = e.pageX, St.y = e.pageY, St.id = "", _t[0] = St), _t
                }

                function $(e, t) {
                    var i, n, a, s, o = Qe[e] + t[e],
                        r = 0 < t[e],
                        l = bt.x + t.x,
                        c = bt.x - ft.x;
                    if (i = o > Ae.min[e] || o < Ae.max[e] ? g.panEndFriction : 1, o = Qe[e] + t[e] * i, (g.allowPanToNext || Z === m.currItem.initialZoomLevel) && (Ye ? "h" !== $e || "x" !== e || Me || (r ? (o > Ae.min[e] && (i = g.panEndFriction, Ae.min[e] - o, n = Ae.min[e] - Ne[e]), (n <= 0 || c < 0) && 1 < Nt() ? (s = l, c < 0 && l > ft.x && (s = ft.x)) : Ae.min.x !== Ae.max.x && (a = o)) : (o < Ae.max[e] && (i = g.panEndFriction, o - Ae.max[e], n = Ne[e] - Ae.max[e]), (n <= 0 || 0 < c) && 1 < Nt() ? (s = l, 0 < c && l < ft.x && (s = ft.x)) : Ae.min.x !== Ae.max.x && (a = o))) : s = l, "x" === e)) return void 0 !== s && (u(s, !0), Ie = s !== ft.x), Ae.min.x !== Ae.max.x && (void 0 !== a ? Qe.x = a : Ie || (Qe.x += t.x * i)), void 0 !== s;
                    je || Ie || Z > m.currItem.fitRatio && (Qe[e] += t[e] * i)
                }

                function K(e) {
                    if (!("mousedown" === e.type && 0 < e.button))
                        if (Bt) e.preventDefault();
                        else if (!xe || "mousedown" !== e.type) {
                        if (E(e, !0) && e.preventDefault(), y("pointerDown"), le) {
                            var t = f.arraySearch(pt, e.pointerId, "id");
                            t < 0 && (t = pt.length), pt[t] = {
                                x: e.pageX,
                                y: e.pageY,
                                id: e.pointerId
                            }
                        }
                        var i = j(e),
                            n = i.length;
                        Le = null, T(), Ce && 1 !== n || (Ce = Ke = !0, f.bind(window, V, m), De = Re = Oe = ke = Ie = _e = Se = Me = !1, $e = null, y("firstTouchStart", i), w(Ne, Qe), He.x = He.y = 0, w(ut, i[0]), w(ht, ut), ft.x = qe.x * Ve, mt = [{
                            x: ut.x,
                            y: ut.y
                        }], we = ve = d(), b(Z, !0), xt(), function e() {
                            Ce && (Te = de(e), Tt())
                        }()), !Ee && 1 < n && !je && !Ie && (z = Z, Ee = Se = !(Me = !1), He.y = He.x = 0, w(Ne, Qe), w(lt, i[0]), w(ct, i[1]), A(lt, ct, kt), Dt.x = Math.abs(kt.x) - Qe.x, Dt.y = Math.abs(kt.y) - Qe.y, Pe = L(lt, ct))
                    }
                }

                function O(e) {
                    if (e.preventDefault(), le) {
                        var t = f.arraySearch(pt, e.pointerId, "id");
                        if (-1 < t) {
                            var i = pt[t];
                            i.x = e.pageX, i.y = e.pageY
                        }
                    }
                    if (Ce) {
                        var n = j(e);
                        if ($e || _e || Ee) Le = n;
                        else if (bt.x !== qe.x * Ve) $e = "h";
                        else {
                            var a = Math.abs(n[0].x - ut.x) - Math.abs(n[0].y - ut.y);
                            10 <= Math.abs(a) && ($e = 0 < a ? "h" : "v", Le = n)
                        }
                    }
                }

                function F(e) {
                    if (ge.isOldAndroid) {
                        if (xe && "mouseup" === e.type) return; - 1 < e.type.indexOf("touch") && (clearTimeout(xe), xe = setTimeout(function() {
                            xe = 0
                        }, 600))
                    }
                    var t;
                    if (y("pointerUp"), E(e, !1) && e.preventDefault(), le) {
                        var i = f.arraySearch(pt, e.pointerId, "id");
                        if (-1 < i)
                            if (t = pt.splice(i, 1)[0], navigator.msPointerEnabled) {
                                t.type = {
                                    4: "mouse",
                                    2: "touch",
                                    3: "pen"
                                } [e.pointerType], t.type || (t.type = e.pointerType || "mouse")
                            } else t.type = e.pointerType || "mouse"
                    }
                    var n, a = j(e),
                        s = a.length;
                    if ("mouseup" === e.type && (s = 0), 2 === s) return !(Le = null);
                    1 === s && w(ht, a[0]), 0 !== s || $e || je || (t || ("mouseup" === e.type ? t = {
                        x: e.pageX,
                        y: e.pageY,
                        type: "mouse"
                    } : e.changedTouches && e.changedTouches[0] && (t = {
                        x: e.changedTouches[0].pageX,
                        y: e.changedTouches[0].pageY,
                        type: "touch"
                    })), y("touchRelease", e, t));
                    var o = -1;
                    if (0 === s && (Ce = !1, f.unbind(window, V, m), xt(), Ee ? o = 0 : -1 !== wt && (o = d() - wt)), wt = 1 === s ? d() : -1, n = -1 !== o && o < 150 ? "zoom" : "swipe", Ee && s < 2 && (Ee = !1, 1 === s && (n = "zoomPointerUp"), y("zoomGestureEnded")), Le = null, _e || Me || je || ke)
                        if (T(), (be = be || It()).calculateSwipeSpeed("x"), ke) {
                            if (Y() < g.verticalDragRange) m.close();
                            else {
                                var r = Qe.y,
                                    l = Fe;
                                I("verticalDrag", 0, 1, 300, f.easing.cubic.out, function(e) {
                                    Qe.y = (m.currItem.initialPosition.y - r) * e + r, v((1 - l) * e + l), tt()
                                }), y("onVerticalDrag", 1)
                            }
                        } else {
                            if ((Ie || je) && 0 === s) {
                                if (Et(n, be)) return;
                                n = "zoomPointerUp"
                            }
                            je || ("swipe" === n ? !Ie && Z > m.currItem.fitRatio && Lt(be) : At())
                        }
                }
                var R, B, H, N, Q, U, V, W, q, Z, z, G, X, J, ee, te, ie, ne, ae, se, oe, re, le, ce, de, ue, he, pe, fe, me, ge, ye, ve, we, be, De, ke, xe, Ce, Se, Me, _e, Te, Ie, Le, Ee, Pe, Ae, Ye, je, $e, Ke, Oe, Fe, Re, Be, He = {
                        x: 0,
                        y: 0
                    },
                    Ne = {
                        x: 0,
                        y: 0
                    },
                    Qe = {
                        x: 0,
                        y: 0
                    },
                    Ue = {},
                    Ve = 0,
                    We = {},
                    qe = {
                        x: 0,
                        y: 0
                    },
                    Ze = 0,
                    ze = !0,
                    Ge = [],
                    Xe = {},
                    Je = !1,
                    et = {},
                    tt = function(e) {
                        Ye && (e && (Z > m.currItem.fitRatio ? Je || (Zt(m.currItem, !1, !0), Je = !0) : Je && (Zt(m.currItem), Je = !1)), a(Ye, Qe.x, Qe.y, Z))
                    },
                    it = function(e) {
                        e.container && a(e.container.style, e.initialPosition.x, e.initialPosition.y, e.initialZoomLevel, e)
                    },
                    nt = function(e, t) {
                        t[re] = G + e + "px, 0px" + X
                    },
                    at = null,
                    st = {},
                    ot = 0,
                    rt = {
                        shout: y,
                        listen: s,
                        viewportSize: Ue,
                        options: g,
                        isMainScrollAnimating: function() {
                            return je
                        },
                        getZoomLevel: function() {
                            return Z
                        },
                        getCurrentIndex: function() {
                            return N
                        },
                        isDragging: function() {
                            return Ce
                        },
                        isZooming: function() {
                            return Ee
                        },
                        setScrollOffset: function(e, t) {
                            We.x = e, me = We.y = t, y("updateScrollOffset", We)
                        },
                        applyZoomPan: function(e, t, i, n) {
                            Qe.x = t, Qe.y = i, Z = e, tt(n)
                        },
                        init: function() {
                            if (!R && !B) {
                                var e;
                                m.framework = f, m.template = p, m.bg = f.getChildByClass(p, "pswp__bg"), he = p.className, R = !0, ge = f.detectFeatures(), de = ge.raf, ue = ge.caf, re = ge.transform, fe = ge.oldIE, m.scrollWrap = f.getChildByClass(p, "pswp__scroll-wrap"), m.container = f.getChildByClass(m.scrollWrap, "pswp__container"), Q = m.container.style, m.itemHolders = te = [{
                                        el: m.container.children[0],
                                        wrap: 0,
                                        index: -1
                                    }, {
                                        el: m.container.children[1],
                                        wrap: 0,
                                        index: -1
                                    }, {
                                        el: m.container.children[2],
                                        wrap: 0,
                                        index: -1
                                    }], te[0].el.style.display = te[2].el.style.display = "none",
                                    function() {
                                        if (re) {
                                            var e = ge.perspective && !ce;
                                            return G = "translate" + (e ? "3d(" : "("), X = ge.perspective ? ", 0px)" : ")"
                                        }
                                        re = "left", f.addClass(p, "pswp--ie"), nt = function(e, t) {
                                            t.left = e + "px"
                                        }, it = function(e) {
                                            var t = 1 < e.fitRatio ? 1 : e.fitRatio,
                                                i = e.container.style,
                                                n = t * e.w,
                                                a = t * e.h;
                                            i.width = n + "px", i.height = a + "px", i.left = e.initialPosition.x + "px", i.top = e.initialPosition.y + "px"
                                        }, tt = function() {
                                            if (Ye) {
                                                var e = Ye,
                                                    t = m.currItem,
                                                    i = 1 < t.fitRatio ? 1 : t.fitRatio,
                                                    n = i * t.w,
                                                    a = i * t.h;
                                                e.width = n + "px", e.height = a + "px", e.left = Qe.x + "px", e.top = Qe.y + "px"
                                            }
                                        }
                                    }(), q = {
                                        resize: m.updateSize,
                                        orientationchange: function() {
                                            clearTimeout(ye), ye = setTimeout(function() {
                                                Ue.x !== m.scrollWrap.clientWidth && m.updateSize()
                                            }, 500)
                                        },
                                        scroll: S,
                                        keydown: r,
                                        click: C
                                    };
                                var t = ge.isOldIOSPhone || ge.isOldAndroid || ge.isMobileOpera;
                                for (ge.animationName && ge.transform && !t || (g.showAnimationDuration = g.hideAnimationDuration = 0), e = 0; e < Ge.length; e++) m["init" + Ge[e]]();
                                if (n)(m.ui = new n(m, f)).init();
                                y("firstUpdate"), N = N || g.index || 0, (isNaN(N) || N < 0 || N >= Nt()) && (N = 0), m.currItem = Ht(N), (ge.isOldIOSPhone || ge.isOldAndroid) && (ze = !1), p.setAttribute("aria-hidden", "false"), g.modal && (ze ? p.style.position = "fixed" : (p.style.position = "absolute", p.style.top = f.getScrollY() + "px")), void 0 === me && (y("initialLayout"), me = pe = f.getScrollY());
                                var i = "pswp--open ";
                                for (g.mainClass && (i += g.mainClass + " "), g.showHideOpacity && (i += "pswp--animate_opacity "), i += ce ? "pswp--touch" : "pswp--notouch", i += ge.animationName ? " pswp--css_animation" : "", i += ge.svg ? " pswp--svg" : "", f.addClass(p, i), m.updateSize(), U = -1, Ze = null, e = 0; e < 3; e++) nt((e + U) * qe.x, te[e].el.style);
                                fe || f.bind(m.scrollWrap, W, m), s("initialZoomInEnd", function() {
                                    m.setContent(te[0], N - 1), m.setContent(te[2], N + 1), te[0].el.style.display = te[2].el.style.display = "block", g.focus && p.focus(), f.bind(document, "keydown", m), ge.transform && f.bind(m.scrollWrap, "click", m), g.mouseUsed || f.bind(document, "mousemove", o), f.bind(window, "resize scroll orientationchange", m), y("bindEvents")
                                }), m.setContent(te[1], N), m.updateCurrItem(), y("afterInit"), ze || (J = setInterval(function() {
                                    ot || Ce || Ee || Z !== m.currItem.initialZoomLevel || m.updateSize()
                                }, 1e3)), f.addClass(p, "pswp--visible")
                            }
                        },
                        close: function() {
                            R && (B = !(R = !1), y("close"), f.unbind(window, "resize scroll orientationchange", m), f.unbind(window, "scroll", q.scroll), f.unbind(document, "keydown", m), f.unbind(document, "mousemove", o), ge.transform && f.unbind(m.scrollWrap, "click", m), Ce && f.unbind(window, V, m), clearTimeout(ye), y("unbindEvents"), Qt(m.currItem, null, !0, m.destroy))
                        },
                        destroy: function() {
                            y("destroy"), Ot && clearTimeout(Ot), p.setAttribute("aria-hidden", "true"), p.className = he, J && clearInterval(J), f.unbind(m.scrollWrap, W, m), f.unbind(window, "scroll", m), xt(), T(), et = null
                        },
                        panTo: function(e, t, i) {
                            i || (e > Ae.min.x ? e = Ae.min.x : e < Ae.max.x && (e = Ae.max.x), t > Ae.min.y ? t = Ae.min.y : t < Ae.max.y && (t = Ae.max.y)), Qe.x = e, Qe.y = t, tt()
                        },
                        handleEvent: function(e) {
                            e = e || window.event, q[e.type] && q[e.type](e)
                        },
                        goTo: function(e) {
                            var t = (e = l(e)) - N;
                            Ze = t, N = e, m.currItem = Ht(N), Ve -= t, u(qe.x * Ve), T(), je = !1, m.updateCurrItem()
                        },
                        next: function() {
                            m.goTo(N + 1)
                        },
                        prev: function() {
                            m.goTo(N - 1)
                        },
                        updateCurrZoomItem: function(e) {
                            if (e && y("beforeChange", 0), te[1].el.children.length) {
                                var t = te[1].el.children[0];
                                Ye = f.hasClass(t, "pswp__zoom-wrap") ? t.style : null
                            } else Ye = null;
                            Ae = m.currItem.bounds, z = Z = m.currItem.initialZoomLevel, Qe.x = Ae.center.x, Qe.y = Ae.center.y, e && y("afterChange")
                        },
                        invalidateCurrItems: function() {
                            ee = !0;
                            for (var e = 0; e < 3; e++) te[e].item && (te[e].item.needsUpdate = !0)
                        },
                        updateCurrItem: function(e) {
                            if (0 !== Ze) {
                                var t, i = Math.abs(Ze);
                                if (!(e && i < 2)) {
                                    m.currItem = Ht(N), Je = !1, y("beforeChange", Ze), 3 <= i && (U += Ze + (0 < Ze ? -3 : 3), i = 3);
                                    for (var n = 0; n < i; n++) 0 < Ze ? (t = te.shift(), te[2] = t, nt((++U + 2) * qe.x, t.el.style), m.setContent(t, N - i + n + 1 + 1)) : (t = te.pop(), te.unshift(t), nt(--U * qe.x, t.el.style), m.setContent(t, N + i - n - 1 - 1));
                                    if (Ye && 1 === Math.abs(Ze)) {
                                        var a = Ht(ie);
                                        a.initialZoomLevel !== Z && (qt(a, Ue), Zt(a), it(a))
                                    }
                                    Ze = 0, m.updateCurrZoomItem(), ie = N, y("afterChange")
                                }
                            }
                        },
                        updateSize: function(e) {
                            if (!ze && g.modal) {
                                var t = f.getScrollY();
                                if (me !== t && (p.style.top = t + "px", me = t), !e && Xe.x === window.innerWidth && Xe.y === window.innerHeight) return;
                                Xe.x = window.innerWidth, Xe.y = window.innerHeight, p.style.height = Xe.y + "px"
                            }
                            if (Ue.x = m.scrollWrap.clientWidth, Ue.y = m.scrollWrap.clientHeight, S(), qe.x = Ue.x + Math.round(Ue.x * g.spacing), qe.y = Ue.y, u(qe.x * Ve), y("beforeResize"), void 0 !== U) {
                                for (var i, n, a, s = 0; s < 3; s++) i = te[s], nt((s + U) * qe.x, i.el.style), a = N + s - 1, g.loop && 2 < Nt() && (a = l(a)), (n = Ht(a)) && (ee || n.needsUpdate || !n.bounds) ? (m.cleanSlide(n), m.setContent(i, a), 1 === s && (m.currItem = n, m.updateCurrZoomItem(!0)), n.needsUpdate = !1) : -1 === i.index && 0 <= a && m.setContent(i, a), n && n.container && (qt(n, Ue), Zt(n), it(n));
                                ee = !1
                            }
                            z = Z = m.currItem.initialZoomLevel, (Ae = m.currItem.bounds) && (Qe.x = Ae.center.x, Qe.y = Ae.center.y, tt(!0)), y("resize")
                        },
                        zoomTo: function(t, e, i, n, a) {
                            e && (z = Z, Dt.x = Math.abs(e.x) - Qe.x, Dt.y = Math.abs(e.y) - Qe.y, w(Ne, Qe));
                            var s = b(t, !1),
                                o = {};
                            x("x", s, o, t), x("y", s, o, t);
                            var r = Z,
                                l = Qe.x,
                                c = Qe.y;
                            h(o);

                            function d(e) {
                                1 === e ? (Z = t, Qe.x = o.x, Qe.y = o.y) : (Z = (t - r) * e + r, Qe.x = (o.x - l) * e + l, Qe.y = (o.y - c) * e + c), a && a(e), tt(1 === e)
                            }
                            i ? I("customZoomTo", 0, 1, i, n || f.easing.sine.inOut, d) : d(1)
                        }
                    },
                    lt = {},
                    ct = {},
                    dt = {},
                    ut = {},
                    ht = {},
                    pt = [],
                    ft = {},
                    mt = [],
                    gt = {},
                    yt = 0,
                    vt = {
                        x: 0,
                        y: 0
                    },
                    wt = 0,
                    bt = {
                        x: 0,
                        y: 0
                    },
                    Dt = {
                        x: 0,
                        y: 0
                    },
                    kt = {
                        x: 0,
                        y: 0
                    },
                    xt = function() {
                        Te && (ue(Te), Te = null)
                    },
                    Ct = {},
                    St = {},
                    Mt = {},
                    _t = [],
                    Tt = function() {
                        if (Le) {
                            var e = Le.length;
                            if (0 !== e)
                                if (w(lt, Le[0]), dt.x = lt.x - ut.x, dt.y = lt.y - ut.y, Ee && 1 < e) {
                                    if (ut.x = lt.x, ut.y = lt.y, !dt.x && !dt.y && function(e, t) {
                                            return e.x === t.x && e.y === t.y
                                        }(Le[1], ct)) return;
                                    w(ct, Le[1]), Me || (Me = !0, y("zoomGestureStarted"));
                                    var t = L(lt, ct),
                                        i = Pt(t);
                                    i > m.currItem.initialZoomLevel + m.currItem.initialZoomLevel / 15 && (Re = !0);
                                    var n = 1,
                                        a = D(),
                                        s = k();
                                    if (i < a)
                                        if (g.pinchToClose && !Re && z <= m.currItem.initialZoomLevel) {
                                            var o = 1 - (a - i) / (a / 1.2);
                                            v(o), y("onPinchClose", o), Oe = !0
                                        } else 1 < (n = (a - i) / a) && (n = 1), i = a - n * (a / 3);
                                    else s < i && (1 < (n = (i - s) / (6 * a)) && (n = 1), i = s + n * a);
                                    n < 0 && (n = 0), t, A(lt, ct, vt), He.x += vt.x - kt.x, He.y += vt.y - kt.y, w(kt, vt), Qe.x = c("x", i), Qe.y = c("y", i), De = Z < i, Z = i, tt()
                                } else {
                                    if (!$e) return;
                                    if (Ke && (Ke = !1, 10 <= Math.abs(dt.x) && (dt.x -= Le[0].x - ht.x), 10 <= Math.abs(dt.y) && (dt.y -= Le[0].y - ht.y)), ut.x = lt.x, ut.y = lt.y, 0 === dt.x && 0 === dt.y) return;
                                    if ("v" === $e && g.closeOnVerticalDrag && "fit" === g.scaleMode && Z === m.currItem.initialZoomLevel) {
                                        He.y += dt.y, Qe.y += dt.y;
                                        var r = Y();
                                        return ke = !0, y("onVerticalDrag", r), v(r), void tt()
                                    }! function(e, t, i) {
                                        if (50 < e - we) {
                                            var n = 2 < mt.length ? mt.shift() : {};
                                            n.x = t, n.y = i, mt.push(n), we = e
                                        }
                                    }(d(), lt.x, lt.y), _e = !0, Ae = m.currItem.bounds, $("x", dt) || ($("y", dt), h(Qe), tt())
                                }
                        }
                    },
                    It = function() {
                        var t, i, n = {
                            lastFlickOffset: {},
                            lastFlickDist: {},
                            lastFlickSpeed: {},
                            slowDownRatio: {},
                            slowDownRatioReverse: {},
                            speedDecelerationRatio: {},
                            speedDecelerationRatioAbs: {},
                            distanceOffset: {},
                            backAnimDestination: {},
                            backAnimStarted: {},
                            calculateSwipeSpeed: function(e) {
                                i = 1 < mt.length ? (t = d() - we + 50, mt[mt.length - 2][e]) : (t = d() - ve, ht[e]), n.lastFlickOffset[e] = ut[e] - i, n.lastFlickDist[e] = Math.abs(n.lastFlickOffset[e]), 20 < n.lastFlickDist[e] ? n.lastFlickSpeed[e] = n.lastFlickOffset[e] / t : n.lastFlickSpeed[e] = 0, Math.abs(n.lastFlickSpeed[e]) < .1 && (n.lastFlickSpeed[e] = 0), n.slowDownRatio[e] = .95, n.slowDownRatioReverse[e] = 1 - n.slowDownRatio[e], n.speedDecelerationRatio[e] = 1
                            },
                            calculateOverBoundsAnimOffset: function(t, e) {
                                n.backAnimStarted[t] || (Qe[t] > Ae.min[t] ? n.backAnimDestination[t] = Ae.min[t] : Qe[t] < Ae.max[t] && (n.backAnimDestination[t] = Ae.max[t]), void 0 !== n.backAnimDestination[t] && (n.slowDownRatio[t] = .7, n.slowDownRatioReverse[t] = 1 - n.slowDownRatio[t], n.speedDecelerationRatioAbs[t] < .05 && (n.lastFlickSpeed[t] = 0, n.backAnimStarted[t] = !0, I("bounceZoomPan" + t, Qe[t], n.backAnimDestination[t], e || 300, f.easing.sine.out, function(e) {
                                    Qe[t] = e, tt()
                                }))))
                            },
                            calculateAnimOffset: function(e) {
                                n.backAnimStarted[e] || (n.speedDecelerationRatio[e] = n.speedDecelerationRatio[e] * (n.slowDownRatio[e] + n.slowDownRatioReverse[e] - n.slowDownRatioReverse[e] * n.timeDiff / 10), n.speedDecelerationRatioAbs[e] = Math.abs(n.lastFlickSpeed[e] * n.speedDecelerationRatio[e]), n.distanceOffset[e] = n.lastFlickSpeed[e] * n.speedDecelerationRatio[e] * n.timeDiff, Qe[e] += n.distanceOffset[e])
                            },
                            panAnimLoop: function() {
                                if (st.zoomPan && (st.zoomPan.raf = de(n.panAnimLoop), n.now = d(), n.timeDiff = n.now - n.lastNow, n.lastNow = n.now, n.calculateAnimOffset("x"), n.calculateAnimOffset("y"), tt(), n.calculateOverBoundsAnimOffset("x"), n.calculateOverBoundsAnimOffset("y"), n.speedDecelerationRatioAbs.x < .05 && n.speedDecelerationRatioAbs.y < .05)) return Qe.x = Math.round(Qe.x), Qe.y = Math.round(Qe.y), tt(), void M("zoomPan")
                            }
                        };
                        return n
                    },
                    Lt = function(e) {
                        if (e.calculateSwipeSpeed("y"), Ae = m.currItem.bounds, e.backAnimDestination = {}, e.backAnimStarted = {}, Math.abs(e.lastFlickSpeed.x) <= .05 && Math.abs(e.lastFlickSpeed.y) <= .05) return e.speedDecelerationRatioAbs.x = e.speedDecelerationRatioAbs.y = 0, e.calculateOverBoundsAnimOffset("x"), e.calculateOverBoundsAnimOffset("y"), !0;
                        _("zoomPan"), e.lastNow = d(), e.panAnimLoop()
                    },
                    Et = function(e, t) {
                        var i, n, a;
                        if (je || (yt = N), "swipe" === e) {
                            var s = ut.x - ht.x,
                                o = t.lastFlickDist.x < 10;
                            30 < s && (o || 20 < t.lastFlickOffset.x) ? n = -1 : s < -30 && (o || t.lastFlickOffset.x < -20) && (n = 1)
                        }
                        n && ((N += n) < 0 ? (N = g.loop ? Nt() - 1 : 0, a = !0) : N >= Nt() && (N = g.loop ? 0 : Nt() - 1, a = !0), a && !g.loop || (Ze += n, Ve -= n, i = !0));
                        var r, l = qe.x * Ve,
                            c = Math.abs(l - bt.x);
                        return r = i || l > bt.x == 0 < t.lastFlickSpeed.x ? (r = 0 < Math.abs(t.lastFlickSpeed.x) ? c / Math.abs(t.lastFlickSpeed.x) : 333, r = Math.min(r, 400), Math.max(r, 250)) : 333, yt === N && (i = !1), je = !0, y("mainScrollAnimStart"), I("mainScroll", bt.x, l, r, f.easing.cubic.out, u, function() {
                            T(), je = !1, yt = -1, !i && yt === N || m.updateCurrItem(), y("mainScrollAnimComplete")
                        }), i && m.updateCurrItem(!0), i
                    },
                    Pt = function(e) {
                        return 1 / Pe * e * z
                    },
                    At = function() {
                        var e = Z,
                            t = D(),
                            i = k();
                        Z < t ? e = t : i < Z && (e = i);
                        var n, a = Fe;
                        return Oe && !De && !Re && Z < t ? m.close() : (Oe && (n = function(e) {
                            v((1 - a) * e + a)
                        }), m.zoomTo(e, 0, 200, f.easing.cubic.out, n)), !0
                    };
                i("Gestures", {
                    publicMethods: {
                        initGestures: function() {
                            function e(e, t, i, n, a) {
                                ne = e + t, ae = e + i, se = e + n, oe = a ? e + a : ""
                            }(le = ge.pointerEvent) && ge.touch && (ge.touch = !1), le ? navigator.msPointerEnabled ? e("MSPointer", "Down", "Move", "Up", "Cancel") : e("pointer", "down", "move", "up", "cancel") : ge.touch ? (e("touch", "start", "move", "end", "cancel"), ce = !0) : e("mouse", "down", "move", "up"), V = ae + " " + se + " " + oe, W = ne, le && !ce && (ce = 1 < navigator.maxTouchPoints || 1 < navigator.msMaxTouchPoints), m.likelyTouchDevice = ce, q[ne] = K, q[ae] = O, q[se] = F, oe && (q[oe] = q[se]), ge.touch && (W += " mousedown", V += " mousemove mouseup", q.mousedown = q[ne], q.mousemove = q[ae], q.mouseup = q[se]), ce || (g.allowPanToNext = !1)
                        }
                    }
                });

                function Yt(e, t, i, n, a, s) {
                    t.loadError || n && (t.imageAppended = !0, Zt(t, n, t === m.currItem && Je), i.appendChild(n), s && setTimeout(function() {
                        t && t.loaded && t.placeholder && (t.placeholder.style.display = "none", t.placeholder = null)
                    }, 500))
                }

                function jt(e) {
                    function t() {
                        e.loading = !1, e.loaded = !0, e.loadComplete ? e.loadComplete(e) : e.img = null, i.onload = i.onerror = null, i = null
                    }
                    e.loading = !0, e.loaded = !1;
                    var i = e.img = f.createEl("pswp__img", "img");
                    return i.onload = t, i.onerror = function() {
                        e.loadError = !0, t()
                    }, i.src = e.src, i
                }

                function $t(e, t) {
                    if (e.src && e.loadError && e.container) return t && (e.container.innerHTML = ""), e.container.innerHTML = g.errorMsg.replace("%url%", e.src), !0
                }

                function Kt() {
                    if (Vt.length) {
                        for (var e, t = 0; t < Vt.length; t++)(e = Vt[t]).holder.index === e.index && Yt(e.index, e.item, e.baseDiv, e.img, 0, e.clearPlaceholder);
                        Vt = []
                    }
                }
                var Ot, Ft, Rt, Bt, Ht, Nt, Qt = function(o, e, r, t) {
                        var l;
                        Ot && clearTimeout(Ot), Rt = Bt = !0, o.initialLayout ? (l = o.initialLayout, o.initialLayout = null) : l = g.getThumbBoundsFn && g.getThumbBoundsFn(N);

                        function c() {
                            M("initialZoom"), r ? (m.template.removeAttribute("style"), m.bg.removeAttribute("style")) : (v(1), e && (e.style.display = "block"), f.addClass(p, "pswp--animated-in"), y("initialZoom" + (r ? "OutEnd" : "InEnd"))), t && t(), Bt = !1
                        }
                        var d = r ? g.hideAnimationDuration : g.showAnimationDuration;
                        if (!d || !l || void 0 === l.x) return y("initialZoom" + (r ? "Out" : "In")), Z = o.initialZoomLevel, w(Qe, o.initialPosition), tt(), p.style.opacity = r ? 0 : 1, v(1), void(d ? setTimeout(function() {
                            c()
                        }, d) : c());
                        var u, h;
                        u = H, h = !m.currItem.src || m.currItem.loadError || g.showHideOpacity, o.miniImg && (o.miniImg.style.webkitBackfaceVisibility = "hidden"), r || (Z = l.w / o.w, Qe.x = l.x, Qe.y = l.y - pe, m[h ? "template" : "bg"].style.opacity = .001, tt()), _("initialZoom"), r && !u && f.removeClass(p, "pswp--animated-in"), h && (r ? f[(u ? "remove" : "add") + "Class"](p, "pswp--animate_opacity") : setTimeout(function() {
                            f.addClass(p, "pswp--animate_opacity")
                        }, 30)), Ot = setTimeout(function() {
                            if (y("initialZoom" + (r ? "Out" : "In")), r) {
                                var t = l.w / o.w,
                                    i = Qe.x,
                                    n = Qe.y,
                                    a = Z,
                                    s = Fe,
                                    e = function(e) {
                                        1 === e ? (Z = t, Qe.x = l.x, Qe.y = l.y - me) : (Z = (t - a) * e + a, Qe.x = (l.x - i) * e + i, Qe.y = (l.y - me - n) * e + n), tt(), h ? p.style.opacity = 1 - e : v(s - e * s)
                                    };
                                u ? I("initialZoom", 0, 1, d, f.easing.cubic.out, e, c) : (e(1), Ot = setTimeout(c, d + 20))
                            } else Z = o.initialZoomLevel, w(Qe, o.initialPosition), tt(), v(1), h ? p.style.opacity = 1 : v(1), Ot = setTimeout(c, d + 20)
                        }, r ? 25 : 90)
                    },
                    Ut = {},
                    Vt = [],
                    Wt = {
                        index: 0,
                        errorMsg: '<div class="pswp__error-msg"><a href="%url%" target="_blank">The image</a> could not be loaded.</div>',
                        forceProgressiveLoading: !1,
                        preload: [1, 1],
                        getNumItemsFn: function() {
                            return Ft.length
                        }
                    },
                    qt = function(e, t, i) {
                        if (!e.src || e.loadError) return e.w = e.h = 0, e.initialZoomLevel = e.fitRatio = 1, e.bounds = {
                            center: {
                                x: 0,
                                y: 0
                            },
                            max: {
                                x: 0,
                                y: 0
                            },
                            min: {
                                x: 0,
                                y: 0
                            }
                        }, e.initialPosition = e.bounds.center, e.bounds;
                        var n = !i;
                        if (n && (e.vGap || (e.vGap = {
                                top: 0,
                                bottom: 0
                            }), y("parseVerticalMargin", e)), Ut.x = t.x, Ut.y = t.y - e.vGap.top - e.vGap.bottom, n) {
                            var a = Ut.x / e.w,
                                s = Ut.y / e.h;
                            e.fitRatio = a < s ? a : s;
                            var o = g.scaleMode;
                            "orig" === o ? i = 1 : "fit" === o && (i = e.fitRatio), 1 < i && (i = 1), e.initialZoomLevel = i, e.bounds || (e.bounds = {
                                center: {
                                    x: 0,
                                    y: 0
                                },
                                max: {
                                    x: 0,
                                    y: 0
                                },
                                min: {
                                    x: 0,
                                    y: 0
                                }
                            })
                        }
                        return i ? (function(e, t, i) {
                            var n = e.bounds;
                            n.center.x = Math.round((Ut.x - t) / 2), n.center.y = Math.round((Ut.y - i) / 2) + e.vGap.top, n.max.x = t > Ut.x ? Math.round(Ut.x - t) : n.center.x, n.max.y = i > Ut.y ? Math.round(Ut.y - i) + e.vGap.top : n.center.y, n.min.x = t > Ut.x ? 0 : n.center.x, n.min.y = i > Ut.y ? e.vGap.top : n.center.y
                        }(e, e.w * i, e.h * i), n && i === e.initialZoomLevel && (e.initialPosition = e.bounds.center), e.bounds) : void 0
                    },
                    Zt = function(e, t, i) {
                        if (e.src) {
                            t = t || e.container.lastChild;
                            var n = i ? e.w : Math.round(e.w * e.fitRatio),
                                a = i ? e.h : Math.round(e.h * e.fitRatio);
                            e.placeholder && !e.loaded && (e.placeholder.style.width = n + "px", e.placeholder.style.height = a + "px"), t.style.width = n + "px", t.style.height = a + "px"
                        }
                    };
                i("Controller", {
                    publicMethods: {
                        lazyLoadItem: function(e) {
                            e = l(e);
                            var t = Ht(e);
                            t && (!t.loaded && !t.loading || ee) && (y("gettingData", e, t), t.src && jt(t))
                        },
                        initController: function() {
                            f.extend(g, Wt, !0), m.items = Ft = e, Ht = m.getItemAt, (Nt = g.getNumItemsFn)() < 3 && (g.loop = !1), s("beforeChange", function(e) {
                                var t, i = g.preload,
                                    n = null === e || 0 <= e,
                                    a = Math.min(i[0], Nt()),
                                    s = Math.min(i[1], Nt());
                                for (t = 1; t <= (n ? s : a); t++) m.lazyLoadItem(N + t);
                                for (t = 1; t <= (n ? a : s); t++) m.lazyLoadItem(N - t)
                            }), s("initialLayout", function() {
                                m.currItem.initialLayout = g.getThumbBoundsFn && g.getThumbBoundsFn(N)
                            }), s("mainScrollAnimComplete", Kt), s("initialZoomInEnd", Kt), s("destroy", function() {
                                for (var e, t = 0; t < Ft.length; t++)(e = Ft[t]).container && (e.container = null), e.placeholder && (e.placeholder = null), e.img && (e.img = null), e.preloader && (e.preloader = null), e.loadError && (e.loaded = e.loadError = !1);
                                Vt = null
                            })
                        },
                        getItemAt: function(e) {
                            return 0 <= e && (void 0 !== Ft[e] && Ft[e])
                        },
                        allowProgressiveImg: function() {
                            return g.forceProgressiveLoading || !ce || g.mouseUsed || 1200 < screen.width
                        },
                        setContent: function(t, i) {
                            g.loop && (i = l(i));
                            var e = m.getItemAt(t.index);
                            e && (e.container = null);
                            var n, a = m.getItemAt(i);
                            if (a) {
                                y("gettingData", i, a), t.index = i;
                                var s = (t.item = a).container = f.createEl("pswp__zoom-wrap");
                                if (!a.src && a.html && (a.html.tagName ? s.appendChild(a.html) : s.innerHTML = a.html), $t(a), qt(a, Ue), !a.src || a.loadError || a.loaded) a.src && !a.loadError && ((n = f.createEl("pswp__img", "img")).style.opacity = 1, n.src = a.src, Zt(a, n), Yt(0, a, s, n));
                                else {
                                    if (a.loadComplete = function(e) {
                                            if (R) {
                                                if (t && t.index === i) {
                                                    if ($t(e, !0)) return e.loadComplete = e.img = null, qt(e, Ue), it(e), void(t.index === N && m.updateCurrZoomItem());
                                                    e.imageAppended ? !Bt && e.placeholder && (e.placeholder.style.display = "none", e.placeholder = null) : ge.transform && (je || Bt) ? Vt.push({
                                                        item: e,
                                                        baseDiv: s,
                                                        img: e.img,
                                                        index: i,
                                                        holder: t,
                                                        clearPlaceholder: !0
                                                    }) : Yt(0, e, s, e.img, 0, !0)
                                                }
                                                e.loadComplete = null, e.img = null, y("imageLoadComplete", i, e)
                                            }
                                        }, f.features.transform) {
                                        var o = "pswp__img pswp__img--placeholder";
                                        o += a.msrc ? "" : " pswp__img--placeholder--blank";
                                        var r = f.createEl(o, a.msrc ? "img" : "");
                                        a.msrc && (r.src = a.msrc), Zt(a, r), s.appendChild(r), a.placeholder = r
                                    }
                                    a.loading || jt(a), m.allowProgressiveImg() && (!Rt && ge.transform ? Vt.push({
                                        item: a,
                                        baseDiv: s,
                                        img: a.img,
                                        index: i,
                                        holder: t
                                    }) : Yt(0, a, s, a.img, 0, !0))
                                }
                                Rt || i !== N ? it(a) : (Ye = s.style, Qt(a, n || a.img)), t.el.innerHTML = "", t.el.appendChild(s)
                            } else t.el.innerHTML = ""
                        },
                        cleanSlide: function(e) {
                            e.img && (e.img.onload = e.img.onerror = null), e.loaded = e.loading = e.img = e.imageAppended = !1
                        }
                    }
                });

                function zt(e, t, i) {
                    var n = document.createEvent("CustomEvent"),
                        a = {
                            origEvent: e,
                            target: e.target,
                            releasePoint: t,
                            pointerType: i || "touch"
                        };
                    n.initCustomEvent("pswpTap", !0, !0, a), e.target.dispatchEvent(n)
                }
                var Gt, Xt, Jt = {};
                i("Tap", {
                    publicMethods: {
                        initTap: function() {
                            s("firstTouchStart", m.onTapStart), s("touchRelease", m.onTapRelease), s("destroy", function() {
                                Jt = {}, Gt = null
                            })
                        },
                        onTapStart: function(e) {
                            1 < e.length && (clearTimeout(Gt), Gt = null)
                        },
                        onTapRelease: function(e, t) {
                            if (t && !_e && !Se && !ot) {
                                var i = t;
                                if (Gt && (clearTimeout(Gt), Gt = null, function(e, t) {
                                        return Math.abs(e.x - t.x) < 25 && Math.abs(e.y - t.y) < 25
                                    }(i, Jt))) return void y("doubleTap", i);
                                if ("mouse" === t.type) return void zt(e, t, "mouse");
                                if ("BUTTON" === e.target.tagName.toUpperCase() || f.hasClass(e.target, "pswp__single-tap")) return void zt(e, t);
                                w(Jt, i), Gt = setTimeout(function() {
                                    zt(e, t), Gt = null
                                }, 300)
                            }
                        }
                    }
                }), i("DesktopZoom", {
                    publicMethods: {
                        initDesktopZoom: function() {
                            fe || (ce ? s("mouseUsed", function() {
                                m.setupDesktopZoom()
                            }) : m.setupDesktopZoom(!0))
                        },
                        setupDesktopZoom: function(e) {
                            Xt = {};
                            var t = "wheel mousewheel DOMMouseScroll";
                            s("bindEvents", function() {
                                f.bind(p, t, m.handleMouseWheel)
                            }), s("unbindEvents", function() {
                                Xt && f.unbind(p, t, m.handleMouseWheel)
                            }), m.mouseZoomedIn = !1;

                            function i() {
                                m.mouseZoomedIn && (f.removeClass(p, "pswp--zoomed-in"), m.mouseZoomedIn = !1), Z < 1 ? f.addClass(p, "pswp--zoom-allowed") : f.removeClass(p, "pswp--zoom-allowed"), a()
                            }
                            var n, a = function() {
                                n && (f.removeClass(p, "pswp--dragging"), n = !1)
                            };
                            s("resize", i), s("afterChange", i), s("pointerDown", function() {
                                m.mouseZoomedIn && (n = !0, f.addClass(p, "pswp--dragging"))
                            }), s("pointerUp", a), e || i()
                        },
                        handleMouseWheel: function(e) {
                            if (Z <= m.currItem.fitRatio) return g.modal && (!g.closeOnScroll || ot || Ce ? e.preventDefault() : re && 2 < Math.abs(e.deltaY) && (H = !0, m.close())), !0;
                            if (e.stopPropagation(), Xt.x = 0, "deltaX" in e) 1 === e.deltaMode ? (Xt.x = 18 * e.deltaX, Xt.y = 18 * e.deltaY) : (Xt.x = e.deltaX, Xt.y = e.deltaY);
                            else if ("wheelDelta" in e) e.wheelDeltaX && (Xt.x = -.16 * e.wheelDeltaX), e.wheelDeltaY ? Xt.y = -.16 * e.wheelDeltaY : Xt.y = -.16 * e.wheelDelta;
                            else {
                                if (!("detail" in e)) return;
                                Xt.y = e.detail
                            }
                            b(Z, !0);
                            var t = Qe.x - Xt.x,
                                i = Qe.y - Xt.y;
                            (g.modal || t <= Ae.min.x && t >= Ae.max.x && i <= Ae.min.y && i >= Ae.max.y) && e.preventDefault(), m.panTo(t, i)
                        },
                        toggleDesktopZoom: function(e) {
                            e = e || {
                                x: Ue.x / 2 + We.x,
                                y: Ue.y / 2 + We.y
                            };
                            var t = g.getDoubleTapZoom(!0, m.currItem),
                                i = Z === t;
                            m.mouseZoomedIn = !i, m.zoomTo(i ? m.currItem.initialZoomLevel : t, e, 333), f[(i ? "remove" : "add") + "Class"](p, "pswp--zoomed-in")
                        }
                    }
                });

                function ei() {
                    return fi.hash.substring(1)
                }

                function ti() {
                    ai && clearTimeout(ai), oi && clearTimeout(oi)
                }

                function ii() {
                    var e = ei(),
                        t = {};
                    if (e.length < 5) return t;
                    var i, n = e.split("&");
                    for (i = 0; i < n.length; i++)
                        if (n[i]) {
                            var a = n[i].split("=");
                            a.length < 2 || (t[a[0]] = a[1])
                        } if (g.galleryPIDs) {
                        var s = t.pid;
                        for (i = t.pid = 0; i < Ft.length; i++)
                            if (Ft[i].pid === s) {
                                t.pid = i;
                                break
                            }
                    } else t.pid = parseInt(t.pid, 10) - 1;
                    return t.pid < 0 && (t.pid = 0), t
                }

                function ni() {
                    if (oi && clearTimeout(oi), ot || Ce) oi = setTimeout(ni, 500);
                    else {
                        ri ? clearTimeout(si) : ri = !0;
                        var e = N + 1,
                            t = Ht(N);
                        t.hasOwnProperty("pid") && (e = t.pid);
                        var i = di + "&gid=" + g.galleryUID + "&pid=" + e;
                        ui || -1 === fi.hash.indexOf(i) && (pi = !0);
                        var n = fi.href.split("#")[0] + "#" + i;
                        mi ? "#" + i !== window.location.hash && history[ui ? "replaceState" : "pushState"]("", document.title, n) : ui ? fi.replace(n) : fi.hash = i, ui = !0, si = setTimeout(function() {
                            ri = !1
                        }, 60)
                    }
                }
                var ai, si, oi, ri, li, ci, di, ui, hi, pi, fi, mi, gi = {
                    history: !0,
                    galleryUID: 1
                };
                i("History", {
                    publicMethods: {
                        initHistory: function() {
                            if (f.extend(g, gi, !0), g.history) {
                                fi = window.location, ui = hi = pi = !1, di = ei(), mi = "pushState" in history, -1 < di.indexOf("gid=") && (di = (di = di.split("&gid=")[0]).split("?gid=")[0]), s("afterChange", m.updateURL), s("unbindEvents", function() {
                                    f.unbind(window, "hashchange", m.onHashChange)
                                });
                                var e = function() {
                                    ci = !0, hi || (pi ? history.back() : di ? fi.hash = di : mi ? history.pushState("", document.title, fi.pathname + fi.search) : fi.hash = ""), ti()
                                };
                                s("unbindEvents", function() {
                                    H && e()
                                }), s("destroy", function() {
                                    ci || e()
                                }), s("firstUpdate", function() {
                                    N = ii().pid
                                });
                                var t = di.indexOf("pid="); - 1 < t && "&" === (di = di.substring(0, t)).slice(-1) && (di = di.slice(0, -1)), setTimeout(function() {
                                    R && f.bind(window, "hashchange", m.onHashChange)
                                }, 40)
                            }
                        },
                        onHashChange: function() {
                            if (ei() === di) return hi = !0, void m.close();
                            ri || (li = !0, m.goTo(ii().pid), li = !1)
                        },
                        updateURL: function() {
                            ti(), li || (ui ? ai = setTimeout(ni, 800) : ni())
                        }
                    }
                }), f.extend(m, rt)
            }
        }, "function" == typeof define && define.amd ? define(t) : "object" === ("undefined" == typeof exports ? "undefined" : yi(exports)) ? module.exports = t() : e.PhotoSwipe = t(), i = window, n = function() {
            return function(a, r) {
                function e(e) {
                    if (M) return !0;
                    e = e || window.event, S.timeToIdle && S.mouseUsed && !w && $();
                    for (var t, i, n = (e.target || e.srcElement).getAttribute("class") || "", a = 0; a < O.length; a++)(t = O[a]).onTap && -1 < n.indexOf("pswp__" + t.name) && (t.onTap(), i = !0);
                    if (i) {
                        e.stopPropagation && e.stopPropagation(), M = !0;
                        var s = r.features.isOldAndroid ? 600 : 30;
                        setTimeout(function() {
                            M = !1
                        }, s)
                    }
                }

                function i(e, t, i) {
                    r[(i ? "add" : "remove") + "Class"](e, "pswp__" + t)
                }

                function t() {
                    var e = 1 === S.getNumItemsFn();
                    e !== C && (i(h, "ui--one-slide", e), C = e)
                }

                function n() {
                    i(y, "share-modal--hidden", P)
                }

                function s() {
                    return (P = !P) ? (r.removeClass(y, "pswp__share-modal--fade-in"), setTimeout(function() {
                        P && n()
                    }, 300)) : (n(), setTimeout(function() {
                        P || r.addClass(y, "pswp__share-modal--fade-in")
                    }, 30)), P || Y(), !1
                }

                function o(e) {
                    var t = (e = e || window.event).target || e.srcElement;
                    return a.shout("shareLinkClick", e, t), !!t.href && (!!t.hasAttribute("download") || (window.open(t.href, "pswp_share", "scrollbars=yes,resizable=yes,toolbar=no,location=yes,width=550,height=420,top=100,left=" + (window.screen ? Math.round(screen.width / 2 - 275) : 100)), P || s(), !1))
                }

                function l(e) {
                    for (var t = 0; t < S.closeElClasses.length; t++)
                        if (r.hasClass(e, "pswp__" + S.closeElClasses[t])) return !0
                }

                function c(e) {
                    var t = (e = e || window.event).relatedTarget || e.toElement;
                    t && "HTML" !== t.nodeName || (clearTimeout(T), T = setTimeout(function() {
                        I.setIdle(!0)
                    }, S.timeToIdleOutside))
                }

                function d(e) {
                    var t = e.vGap;
                    if (!a.likelyTouchDevice || S.mouseUsed || screen.width > S.fitControlsWidth) {
                        var i = S.barsSize;
                        if (S.captionEl && "auto" === i.bottom)
                            if (f || ((f = r.createEl("pswp__caption pswp__caption--fake")).appendChild(r.createEl("pswp__caption__center")), h.insertBefore(f, p), r.addClass(h, "pswp__ui--fit")), S.addCaptionHTMLFn(e, f, !0)) {
                                var n = f.clientHeight;
                                t.bottom = parseInt(n, 10) || 44
                            } else t.bottom = i.top;
                        else t.bottom = "auto" === i.bottom ? 0 : i.bottom;
                        t.top = i.top
                    } else t.top = t.bottom = 0
                }
                var u, h, p, f, m, g, y, v, w, b, D, k, x, C, S, M, _, T, I = this,
                    L = !1,
                    E = !0,
                    P = !0,
                    A = {
                        barsSize: {
                            top: 44,
                            bottom: "auto"
                        },
                        closeElClasses: ["item", "caption", "zoom-wrap", "ui", "top-bar"],
                        timeToIdle: 4e3,
                        timeToIdleOutside: 1e3,
                        loadingIndicatorDelay: 1e3,
                        addCaptionHTMLFn: function(e, t) {
                            return e.title ? (t.children[0].innerHTML = e.title, !0) : (t.children[0].innerHTML = "", !1)
                        },
                        closeEl: !0,
                        captionEl: !0,
                        fullscreenEl: !0,
                        zoomEl: !0,
                        shareEl: !0,
                        counterEl: !0,
                        arrowEl: !0,
                        preloaderEl: !0,
                        tapToClose: !1,
                        tapToToggleControls: !0,
                        clickToCloseNonZoomable: !0,
                        shareButtons: [{
                            id: "facebook",
                            label: "Share on Facebook",
                            url: "https://www.facebook.com/sharer/sharer.php?u={{url}}"
                        }, {
                            id: "twitter",
                            label: "Tweet",
                            url: "https://twitter.com/intent/tweet?text={{text}}&url={{url}}"
                        }, {
                            id: "pinterest",
                            label: "Pin it",
                            url: "http://www.pinterest.com/pin/create/button/?url={{url}}&media={{image_url}}&description={{text}}"
                        }, {
                            id: "download",
                            label: "Download image",
                            url: "{{raw_image_url}}",
                            download: !0
                        }],
                        getImageURLForShare: function() {
                            return a.currItem.src || ""
                        },
                        getPageURLForShare: function() {
                            return window.location.href
                        },
                        getTextForShare: function() {
                            return a.currItem.title || ""
                        },
                        indexIndicatorSep: " / ",
                        fitControlsWidth: 1200
                    },
                    Y = function() {
                        for (var e, t, i, n, a = "", s = 0; s < S.shareButtons.length; s++) e = S.shareButtons[s], t = S.getImageURLForShare(e), i = S.getPageURLForShare(e), n = S.getTextForShare(e), a += '<a href="' + e.url.replace("{{url}}", encodeURIComponent(i)).replace("{{image_url}}", encodeURIComponent(t)).replace("{{raw_image_url}}", t).replace("{{text}}", encodeURIComponent(n)) + '" target="_blank" class="pswp__share--' + e.id + '"' + (e.download ? "download" : "") + ">" + e.label + "</a>", S.parseShareButtonOut && (a = S.parseShareButtonOut(e, a));
                        y.children[0].innerHTML = a, y.children[0].onclick = o
                    },
                    j = 0,
                    $ = function() {
                        clearTimeout(T), j = 0, w && I.setIdle(!1)
                    },
                    K = function(e) {
                        k !== e && (i(D, "preloader--active", !e), k = e)
                    },
                    O = [{
                        name: "caption",
                        option: "captionEl",
                        onInit: function(e) {
                            p = e
                        }
                    }, {
                        name: "share-modal",
                        option: "shareEl",
                        onInit: function(e) {
                            y = e
                        },
                        onTap: function() {
                            s()
                        }
                    }, {
                        name: "button--share",
                        option: "shareEl",
                        onInit: function(e) {
                            g = e
                        },
                        onTap: function() {
                            s()
                        }
                    }, {
                        name: "button--zoom",
                        option: "zoomEl",
                        onTap: a.toggleDesktopZoom
                    }, {
                        name: "counter",
                        option: "counterEl",
                        onInit: function(e) {
                            m = e
                        }
                    }, {
                        name: "button--close",
                        option: "closeEl",
                        onTap: a.close
                    }, {
                        name: "button--arrow--left",
                        option: "arrowEl",
                        onTap: a.prev
                    }, {
                        name: "button--arrow--right",
                        option: "arrowEl",
                        onTap: a.next
                    }, {
                        name: "button--fs",
                        option: "fullscreenEl",
                        onTap: function() {
                            u.isFullscreen() ? u.exit() : u.enter()
                        }
                    }, {
                        name: "preloader",
                        option: "preloaderEl",
                        onInit: function(e) {
                            D = e
                        }
                    }];
                I.init = function() {
                    r.extend(a.options, A, !0), S = a.options, h = r.getChildByClass(a.scrollWrap, "pswp__ui"), b = a.listen,
                        function() {
                            var t;
                            b("onVerticalDrag", function(e) {
                                E && e < .95 ? I.hideControls() : !E && .95 <= e && I.showControls()
                            }), b("onPinchClose", function(e) {
                                E && e < .9 ? (I.hideControls(), t = !0) : t && !E && .9 < e && I.showControls()
                            }), b("zoomGestureEnded", function() {
                                (t = !1) && !E && I.showControls()
                            })
                        }(), b("beforeChange", I.update), b("doubleTap", function(e) {
                            var t = a.currItem.initialZoomLevel;
                            a.getZoomLevel() !== t ? a.zoomTo(t, e, 333) : a.zoomTo(S.getDoubleTapZoom(!1, a.currItem), e, 333)
                        }), b("preventDragEvent", function(e, t, i) {
                            var n = e.target || e.srcElement;
                            n && n.getAttribute("class") && -1 < e.type.indexOf("mouse") && (0 < n.getAttribute("class").indexOf("__caption") || /(SMALL|STRONG|EM)/i.test(n.tagName)) && (i.prevent = !1)
                        }), b("bindEvents", function() {
                            r.bind(h, "pswpTap click", e), r.bind(a.scrollWrap, "pswpTap", I.onGlobalTap), a.likelyTouchDevice || r.bind(a.scrollWrap, "mouseover", I.onMouseOver)
                        }), b("unbindEvents", function() {
                            P || s(), _ && clearInterval(_), r.unbind(document, "mouseout", c), r.unbind(document, "mousemove", $), r.unbind(h, "pswpTap click", e), r.unbind(a.scrollWrap, "pswpTap", I.onGlobalTap), r.unbind(a.scrollWrap, "mouseover", I.onMouseOver), u && (r.unbind(document, u.eventK, I.updateFullscreen), u.isFullscreen() && (S.hideAnimationDuration = 0, u.exit()), u = null)
                        }), b("destroy", function() {
                            S.captionEl && (f && h.removeChild(f), r.removeClass(p, "pswp__caption--empty")), y && (y.children[0].onclick = null), r.removeClass(h, "pswp__ui--over-close"), r.addClass(h, "pswp__ui--hidden"), I.setIdle(!1)
                        }), S.showAnimationDuration || r.removeClass(h, "pswp__ui--hidden"), b("initialZoomIn", function() {
                            S.showAnimationDuration && r.removeClass(h, "pswp__ui--hidden")
                        }), b("initialZoomOut", function() {
                            r.addClass(h, "pswp__ui--hidden")
                        }), b("parseVerticalMargin", d),
                        function() {
                            function e(e) {
                                if (e)
                                    for (var t = e.length, i = 0; i < t; i++) {
                                        a = e[i], s = a.className;
                                        for (var n = 0; n < O.length; n++) o = O[n], -1 < s.indexOf("pswp__" + o.name) && (S[o.option] ? (r.removeClass(a, "pswp__element--disabled"), o.onInit && o.onInit(a)) : r.addClass(a, "pswp__element--disabled"))
                                    }
                            }
                            var a, s, o;
                            e(h.children);
                            var t = r.getChildByClass(h, "pswp__top-bar");
                            t && e(t.children)
                        }(), S.shareEl && g && y && (P = !0), t(), S.timeToIdle && b("mouseUsed", function() {
                            r.bind(document, "mousemove", $), r.bind(document, "mouseout", c), _ = setInterval(function() {
                                2 == ++j && I.setIdle(!0)
                            }, S.timeToIdle / 2)
                        }), S.fullscreenEl && !r.features.isOldAndroid && ((u = u || I.getFullscreenAPI()) ? (r.bind(document, u.eventK, I.updateFullscreen), I.updateFullscreen(), r.addClass(a.template, "pswp--supports-fs")) : r.removeClass(a.template, "pswp--supports-fs")), S.preloaderEl && (K(!0), b("beforeChange", function() {
                            clearTimeout(x), x = setTimeout(function() {
                                a.currItem && a.currItem.loading ? a.allowProgressiveImg() && (!a.currItem.img || a.currItem.img.naturalWidth) || K(!1) : K(!0)
                            }, S.loadingIndicatorDelay)
                        }), b("imageLoadComplete", function(e, t) {
                            a.currItem === t && K(!0)
                        }))
                }, I.setIdle = function(e) {
                    i(h, "ui--idle", w = e)
                }, I.update = function() {
                    L = !(!E || !a.currItem) && (I.updateIndexIndicator(), S.captionEl && (S.addCaptionHTMLFn(a.currItem, p), i(p, "caption--empty", !a.currItem.title)), !0), P || s(), t()
                }, I.updateFullscreen = function(e) {
                    e && setTimeout(function() {
                        a.setScrollOffset(0, r.getScrollY())
                    }, 50), r[(u.isFullscreen() ? "add" : "remove") + "Class"](a.template, "pswp--fs")
                }, I.updateIndexIndicator = function() {
                    S.counterEl && (m.innerHTML = a.getCurrentIndex() + 1 + S.indexIndicatorSep + S.getNumItemsFn())
                }, I.onGlobalTap = function(e) {
                    var t = (e = e || window.event).target || e.srcElement;
                    if (!M)
                        if (e.detail && "mouse" === e.detail.pointerType) {
                            if (l(t)) return void a.close();
                            r.hasClass(t, "pswp__img") && (1 === a.getZoomLevel() && a.getZoomLevel() <= a.currItem.fitRatio ? S.clickToCloseNonZoomable && a.close() : a.toggleDesktopZoom(e.detail.releasePoint))
                        } else if (S.tapToToggleControls && (E ? I.hideControls() : I.showControls()), S.tapToClose && (r.hasClass(t, "pswp__img") || l(t))) return void a.close()
                }, I.onMouseOver = function(e) {
                    var t = (e = e || window.event).target || e.srcElement;
                    i(h, "ui--over-close", l(t))
                }, I.hideControls = function() {
                    r.addClass(h, "pswp__ui--hidden"), E = !1
                }, I.showControls = function() {
                    E = !0, L || I.update(), r.removeClass(h, "pswp__ui--hidden")
                }, I.supportsFullscreen = function() {
                    var e = document;
                    return !!(e.exitFullscreen || e.mozCancelFullScreen || e.webkitExitFullscreen || e.msExitFullscreen)
                }, I.getFullscreenAPI = function() {
                    var e, t = document.documentElement,
                        i = "fullscreenchange";
                    return t.requestFullscreen ? e = {
                        enterK: "requestFullscreen",
                        exitK: "exitFullscreen",
                        elementK: "fullscreenElement",
                        eventK: i
                    } : t.mozRequestFullScreen ? e = {
                        enterK: "mozRequestFullScreen",
                        exitK: "mozCancelFullScreen",
                        elementK: "mozFullScreenElement",
                        eventK: "moz" + i
                    } : t.webkitRequestFullscreen ? e = {
                        enterK: "webkitRequestFullscreen",
                        exitK: "webkitExitFullscreen",
                        elementK: "webkitFullscreenElement",
                        eventK: "webkit" + i
                    } : t.msRequestFullscreen && (e = {
                        enterK: "msRequestFullscreen",
                        exitK: "msExitFullscreen",
                        elementK: "msFullscreenElement",
                        eventK: "MSFullscreenChange"
                    }), e && (e.enter = function() {
                        if (v = S.closeOnScroll, S.closeOnScroll = !1, "webkitRequestFullscreen" !== this.enterK) return a.template[this.enterK]();
                        a.template[this.enterK](Element.ALLOW_KEYBOARD_INPUT)
                    }, e.exit = function() {
                        return S.closeOnScroll = v, document[this.exitK]()
                    }, e.isFullscreen = function() {
                        return document[this.elementK]
                    }), e
                }
            }
        }, "function" == typeof define && define.amd ? define(n) : "object" === ("undefined" == typeof exports ? "undefined" : yi(exports)) ? module.exports = n() : i.PhotoSwipeUI_Default = n(), MyListing.PhotoSwipe = function(a, e) {
            var t = document.querySelectorAll(".pswp")[0],
                i = {
                    index: e,
                    showAnimationDuration: 333,
                    hideAnimationDuration: 333,
                    showHideOpacity: !0,
                    history: !1,
                    shareEl: !1,
                    getThumbBoundsFn: function(e) {
                        var t = a[e].el,
                            i = window.pageYOffset || document.documentElement.scrollTop,
                            n = t.getBoundingClientRect();
                        return {
                            x: n.left,
                            y: n.top + i,
                            w: n.width
                        }
                    }
                };
            this.gallery = new PhotoSwipe(t, PhotoSwipeUI_Default, a, i), this.gallery.init(), this.gallery.listen("imageLoadComplete", this.lazyload.bind(this))
        }, MyListing.PhotoSwipe.prototype.lazyload = function(e, t) {
            var i = this;
            if (t.w < 1 || t.h < 1) {
                var n = new Image;
                n.onload = function() {
                    t.w = this.width, t.el.dataset.fullWidth = this.width, t.h = this.height, t.el.dataset.fullHeight = this.height, i.gallery.invalidateCurrItems(), i.gallery.updateSize(!0)
                }, n.src = t.src
            }
        }, jQuery(function(t) {
            t("body").on("click", ".open-photo-swipe", function(e) {
                e.preventDefault(), new MyListing.PhotoSwipe([{
                    src: this.href,
                    w: this.dataset.fullWidth || 0,
                    h: this.dataset.fullHeight || 0,
                    el: this
                }], 0)
            }), t(".photoswipe-gallery .photoswipe-item").on("click", function(e) {
                e.preventDefault();
                var i = [],
                    n = this,
                    a = 0;
                t(this).parents(".photoswipe-gallery").find(".photoswipe-item").each(function(e, t) {
                    i.push({
                        src: t.href || t.dataset.large_image,
                        w: t.dataset.fullWidth || t.dataset.large_image_width || 0,
                        h: t.dataset.fullHeight || t.dataset.large_image_height || 0,
                        el: t
                    }), t == n && (a = e)
                }), new MyListing.PhotoSwipe(i, a)
            })
        }), jQuery(function(n) {
            n(".quick-search-instance").each(function(e, t) {
                var i = {};
                i.el = n(this), i.input = i.el.find('input[name="search_keywords"]'), i.default = i.el.find(".default-results"), i.results = i.el.find(".ajax-results"), i.spinner = i.el.find(".loader-bg"), i.view_all = i.el.find(".all-results"), i.no_results = i.el.find(".no-results"), i.last_request = null, i.input.on("input", MyListing.Helpers.debounce(function(e) {
                    a(i)
                }, 250)).trigger("input"), "always" === i.el.data("focus") ? i.el.find(".header-search").addClass("is-focused") : i.el.on("focusin", function() {
                    i.el.find(".header-search").addClass("is-focused")
                }).on("focusout", function() {
                    i.el.find(".header-search").removeClass("is-focused")
                })
            });
            var a = function(t) {
                if (t.spinner.hide(), t.results.hide(), t.view_all.hide(), t.no_results.hide(), !t.input.val() || !t.input.val().trim()) return t.last_request && t.last_request.abort(), t.last_request = null, void t.default.show();
                t.default.hide(), t.spinner.show();
                var e = n.param({
                    action: "mylisting_quick_search",
                    security: CASE27.ajax_nonce,
                    s: t.input.val().trim()
                });
                n.ajax({
                    url: CASE27.mylisting_ajax_url,
                    type: "GET",
                    dataType: "json",
                    data: e,
                    beforeSend: function(e) {
                        t.last_request && t.last_request.abort(), t.last_request = e
                    },
                    success: function(e) {
                        if (t.spinner.hide(), !e.content.trim().length) return t.no_results.show();
                        t.results.html(e.content).show(), t.view_all.show()
                    }
                })
            }
        }),
        function(m) {
            var e = m.fn.select2.amd.require("select2/defaults");
            m.extend(e.defaults, {
                dropdownPosition: "auto"
            });
            var t = m.fn.select2.amd.require("select2/dropdown/attachBody");
            t.prototype._positionDropdown;
            t.prototype._positionDropdown = function() {
                var e = m(window),
                    t = this.$dropdown.hasClass("select2-dropdown--above"),
                    i = this.$dropdown.hasClass("select2-dropdown--below"),
                    n = null,
                    a = this.$container.offset();
                a.bottom = a.top + this.$container.outerHeight(!1);
                var s = {
                    height: this.$container.outerHeight(!1)
                };
                s.top = a.top, s.bottom = a.top + s.height;
                var o = this.$dropdown.outerHeight(!1),
                    r = e.scrollTop(),
                    l = e.scrollTop() + e.height(),
                    c = r < a.top - o,
                    d = l > a.bottom + o,
                    u = {
                        left: a.left,
                        top: s.bottom
                    },
                    h = this.$dropdownParent;
                "static" === h.css("position") && (h = h.offsetParent());
                var p = h.offset();
                u.top -= p.top, u.left -= p.left;
                var f = this.options.get("dropdownPosition");
                "above" === f || "below" === f ? n = f : (t || i || (n = "below"), d || !c || t ? !c && d && t && (n = "below") : n = "above"), ("above" == n || t && "below" !== n) && (u.top = s.top - p.top - o), null != n && (this.$dropdown.removeClass("select2-dropdown--below select2-dropdown--above").addClass("select2-dropdown--" + n), this.$container.removeClass("select2-container--below select2-container--above").addClass("select2-container--" + n)), this.$dropdownContainer.css(u)
            }
        }(window.jQuery), MyListing.Select_Config = {
            lastSearch: {},
            diacritics: {},
            stripDiacritics: function(e) {
                return e.replace(/[^\u0000-\u007E]/g, function(e) {
                    return MyListing.Select_Config.diacritics[e] || e
                })
            }
        }, MyListing.CustomSelect = function(e, t) {
            var i = this;
            if (this.el = jQuery(e), this.el.length) {
                if (this.el.addClass("mlduo-select"), this.el.data("placeholder")) var n = this.el.data("placeholder");
                else if (this.el.attr("placeholder")) n = this.el.attr("placeholder");
                else n = CASE27.l10n.selectOption;
                if (this.args = jQuery.extend({
                        sortable: !0,
                        selected: [],
                        multiple: this.el.prop("multiple"),
                        required: this.el.prop("required"),
                        placeholder: n,
                        tags: !!this.el.data("create-tags"),
                        ajax: !!this.el.data("mylisting-ajax"),
                        dropdownPosition: this.el.data("dropdown-position") || "auto"
                    }, t), !0 === this.args.ajax) var a = "object" === yi(this.el.data("mylisting-ajax-params")) ? this.el.data("mylisting-ajax-params") : {},
                    s = {
                        url: CASE27.mylisting_ajax_url + "&action=" + this.el.data("mylisting-ajax-url"),
                        dataType: "json",
                        delay: 250,
                        cache: !0,
                        data: function(e) {
                            return a.page = e.page || 1, a.search = e.term, a.security = CASE27.ajax_nonce, a
                        },
                        processResults: function(e, t) {
                            return {
                                results: e.results || [],
                                pagination: {
                                    more: e.more
                                }
                            }
                        }
                    };
                this.select = jQuery(e).select2({
                    width: "100%",
                    minimumResultsForSearch: 10,
                    multiple: this.args.multiple,
                    allowClear: !this.args.required,
                    placeholder: this.args.placeholder,
                    dropdownPosition: this.args.dropdownPosition,
                    ajax: "object" === yi(s) ? s : null,
                    tags: this.args.tags,
                    escapeMarkup: function(e) {
                        return e
                    },
                    createTag: function(e) {
                        var t = jQuery.trim(e.term);
                        return "" === t ? null : {
                            id: t,
                            text: t
                        }
                    },
                    language: {
                        errorLoading: function() {
                            return CASE27.l10n.errorLoading
                        },
                        loadingMore: function() {
                            return CASE27.l10n.loadingMore
                        },
                        noResults: function() {
                            return CASE27.l10n.noResults
                        },
                        removeAllItems: function() {
                            return CASE27.l10n.removeAllItems
                        },
                        searching: function(e) {
                            return MyListing.Select_Config.lastSearch = e, CASE27.l10n.searching
                        }
                    }
                });
                var o = this.el.next(".select2-container").first("ul.select2-selection__rendered");
                jQuery(o).on("click touchstart", function(e) {
                    jQuery(e.target).hasClass("select2-selection__choice__remove") && e.stopImmediatePropagation()
                }), o.sortable({
                    placeholder: "ui-state-highlight",
                    forcePlaceholderSize: !0,
                    items: "li:not(.select2-search__field)",
                    tolerance: "pointer",
                    containment: "parent",
                    stop: function() {
                        jQuery(o.find(".select2-selection__choice").get().reverse()).each(function() {
                            if (jQuery(this).data("data")) {
                                var e = jQuery(this).data("data").id,
                                    t = i.el.find('option[value="' + e + '"]')[0];
                                i.el.prepend(t)
                            }
                        })
                    }
                }), this.select.on("change", this.fireChangeEvent.bind(this))
            }
        }, MyListing.CustomSelect.prototype.fireChangeEvent = function(e) {
            var t = document.createEvent("CustomEvent");
            t.initCustomEvent("select:change", !1, !0, {
                value: jQuery(e.currentTarget).val()
            }), this.el.get(0).dispatchEvent(t)
        }, jQuery(function(i) {
            function e() {
                i([".custom-select, .single-product .variations select", "#buddypress div.item-list-tabs#subnav ul li select", "#buddypress #notification-select", "#wc_bookings_field_resource", "#buddypress #messages-select", "#buddypress form#whats-new-form #whats-new-options select", ".settings.privacy-settings #buddypress #item-body > form > p select", ".woocommerce-ordering select", ".c27-submit-listing-form select:not(.ignore-custom-select)", ".ml-admin-listing-form select:not(.ignore-custom-select)"].join(", ")).each(function(e, t) {
                    new MyListing.CustomSelect(t)
                })
            }
            i.fn.select2.amd.require(["select2/diacritics"], function(e) {
                return MyListing.Select_Config.diacritics = e
            }), i.fn.select2.defaults.defaults && (i.fn.select2.defaults.defaults.sorter = function(e) {
                if ("" === i.trim(MyListing.Select_Config.lastSearch.term)) return e;
                var t = e.slice(0),
                    a = MyListing.Select_Config.lastSearch.term || "";
                return a = MyListing.Select_Config.stripDiacritics(a).toUpperCase(), t.sort(function(e, t) {
                    var i = MyListing.Select_Config.stripDiacritics(e.text).toUpperCase(),
                        n = MyListing.Select_Config.stripDiacritics(t.text).toUpperCase();
                    return i.indexOf(a) - n.indexOf(a)
                }), t
            }), e(), i(document).on("mylisting:refresh-scripts", function() {
                e()
            }), i(".repeater").each(function(e, t) {
                i(t).repeater({
                    initEmpty: !0,
                    show: function() {
                        i(this).show(), i(this).find("select").select2({
                            minimumResultsForSearch: 0
                        })
                    }
                }).setList(i(t).data("list"))
            })
        }), 

        MyListing.CustomSelect2 = function (el, args) {
            var self = this;
            this.el = jQuery(el);

            if (!this.el.length) {
                return;
            }

            this.el.addClass('mlduo-select'); // determine placeholder

            if (this.el.data('placeholder')) {
                var placeholder = this.el.data('placeholder');
            } else if (this.el.attr('placeholder')) {
                var placeholder = this.el.attr('placeholder');
            } else {
                var placeholder = CASE27.l10n.selectOption;
            } // supported args

            let branch_all = [];

            this.args = jQuery.extend({
                sortable: true,
                selected: [],
                multiple: this.el.prop('multiple'),
                required: this.el.prop('required'),
                placeholder: placeholder,
                tags: !!this.el.data('create-tags'),
                ajax: !!this.el.data('mylisting-ajax'),
                dropdownPosition: this.el.data('dropdown-position') || 'auto' // above|below|auto
            }, args);

            if (this.args.ajax === true) {
                var request_params = yi(this.el.data('mylisting-ajax-params')) === 'object' ? this.el.data('mylisting-ajax-params') : {};
                var ajax_config = {
                    url: CASE27.mylisting_ajax_url + '&action=' + this.el.data('mylisting-ajax-url'),
                    dataType: 'json',
                    delay: 250,
                    cache: true,
                    data: function data(params) {
                        request_params.page = params.page || 1;
                        request_params.search = params.term;
                        request_params.security = CASE27.ajax_nonce;
                        return request_params;
                    },
                    processResults: function processResults(data, params) {
                        return {
                            results: data.results || [],
                            pagination: {
                                more: data.more
                            }
                        };
                    }
                };
            }

            this.select = jQuery(el).select2({
                width: '100%',
                minimumResultsForSearch: 10,
                multiple: this.args.multiple,
                allowClear: !this.args.required,
                placeholder: this.args.placeholder,
                dropdownPosition: this.args.dropdownPosition,
                ajax: yi(ajax_config) === 'object' ? ajax_config : null,
                tags: this.args.tags,
                closeOnSelect: false,
                allowHtml: true,
                dropdownParent: jQuery(el).parents('.explore-filter').length ? jQuery(el).parents('.explore-filter') : null,
                templateResult: function formatResult(state) {
                    console.log( state );
                    if (!state.id) {
                        var btn = jQuery('<div class="text-right"><button id="all-branch" style="margin-right: 10px;" class="btn btn-default">Select All</button><button id="clear-branch" class="btn btn-default">Clear All</button></div>')
                        return btn;
                    }
console.log('test');
                    branch_all.push(state.id);
                    var id = 'state' + state.id;
                    var checkbox = jQuery('<div class="checkbox"><input id="'+id+'" type="checkbox" '+(state.selected ? 'checked': '')+'><label for="checkbox1">'+state.text+'</label></div>', { id: id });
                    return checkbox;   
                },
                escapeMarkup: function escapeMarkup(text) {
                    return text;
                },
                createTag: function createTag(params) {
                    var term = jQuery.trim(params.term);

                    if (term === '') {
                        return null;
                    }

                    return {
                        id: term,
                        text: term
                    };
                },
                language: {
                    errorLoading: function errorLoading() {
                        return CASE27.l10n.errorLoading;
                    },
                    loadingMore: function loadingMore() {
                        return CASE27.l10n.loadingMore;
                    },
                    noResults: function noResults() {
                        return CASE27.l10n.noResults;
                    },
                    removeAllItems: function removeAllItems() {
                        return CASE27.l10n.removeAllItems;
                    },
                    searching: function searching(params) {
                        MyListing.Select_Config.lastSearch = params;
                        return CASE27.l10n.searching;
                    }
                }
            });

            var ul = this.el.next('.select2-container').first('ul.select2-selection__rendered');
            jQuery(ul).on('click touchstart', function (e) {
                if (jQuery(e.target).hasClass('select2-selection__choice__remove')) {
                    e.stopImmediatePropagation();
                }
            });

            ul.sortable({
                placeholder: 'ui-state-highlight',
                forcePlaceholderSize: true,
                items: 'li:not(.select2-search__field)',
                tolerance: 'pointer',
                containment: 'parent',
                stop: function stop() {
                    jQuery(ul.find('.select2-selection__choice').get().reverse()).each(function () {
                        if (!jQuery(this).data('data')) {
                            return;
                        }

                        var id = jQuery(this).data('data').id;
                        var option = self.el.find('option[value="' + id + '"]')[0];
                        self.el.prepend(option);
                    });
                }
            }); // Fire native change event.

            this.select.on('change', this.fireChangeEvent.bind(this));
        },

        MyListing.CustomSelect2.prototype.fireChangeEvent = function (e) {
            console.log('event Value');
            var event = document.createEvent('CustomEvent');
            event.initCustomEvent('select:change', false, true, {
                value: jQuery(e.currentTarget).val()
            });

            this.el.get(0).dispatchEvent(event);
        },

        jQuery(function ($) {
            // Get list of diacritics.
            $.fn.select2.amd.require(['select2/diacritics'], function (diacritics) {
                return MyListing.Select_Config.diacritics = diacritics;
            });

            (function () {
                if (!$.fn.select2.defaults.defaults) {
                    return;
                }

                $.fn.select2.defaults.defaults.sorter = function (results) {
                    if ($.trim(MyListing.Select_Config.lastSearch.term) === '') {
                        return results;
                    } // Don't alter the results being passed in, make a copy.

                    var sorted = results.slice(0);
                    var term = MyListing.Select_Config.lastSearch.term || '';
                    term = MyListing.Select_Config.stripDiacritics(term).toUpperCase(); // Array.sort is an in-place sort.

                    sorted.sort(function (firstItem, secondItem) {
                        var first = MyListing.Select_Config.stripDiacritics(firstItem.text).toUpperCase();
                        var second = MyListing.Select_Config.stripDiacritics(secondItem.text).toUpperCase();
                        return first.indexOf(term) - second.indexOf(term);
                    });

                    return sorted;
                };
            })();

            // function initCustomSelect2() {
            //   $(['.mylisting-basic-form .dropdown-filter-double-checkbox, .cts-explore .dropdown-filter-double-checkbox'].join(', ')).each(function (i, el) {
            //     new MyListing.CustomSelect2(el);
            //   });
            // }
            // initCustomSelect2();
            // $(document).on('mylisting:refresh-scripts', function () {
            //   initCustomSelect2();
            // });
        }),

        MyListing.TermHierarchy = function(e) {
            this.input = jQuery(e), this.el = this.input.parent(), this.input.length && this.el.hasClass("cts-term-hierarchy") && (this.ajax_params = this.input.data("mylisting-ajax-params"), this.placeholder = this.input.data("placeholder"), this.selected = this.input.data("selected") || [], this.term_value = "slug" === this.ajax_params["term-value"] ? "slug" : "id", this.label = this.el.find("> label"), this.originalLabel = this.label.length ? this.label.html() : "", this.labelTemplate = '<span class="go-back-btn" data-index="%index%"><i class="mi keyboard_backspace"></i> %label%</span>', this.template = "alternate" === this.input.data("template") ? "alternate" : "default", this.el.addClass("tpl-" + this.template), this.selected.length ? this.handleDefaultValue() : this.addChildSelect({
                index: 0,
                select: null
            }), this.label.on("click", function() {
                var e = this.label.find(".go-back-btn");
                if (e.length) {
                    var t = parseInt(e.data("index"), 10);
                    this.el.find(".select-wrapper.term-select-" + (t - 1) + " .select2-selection__clear").mousedown()
                }
            }.bind(this)), this.input.on("change", this.fireChangeEvent.bind(this)), this.addWrapperClasses())
        }, MyListing.TermHierarchy.prototype.maybeAddChildSelect = function(t) {
            var i = this,
                e = ".term-select.term-select-" + t.index + ", .term-select.term-select-" + t.index + " ~ .term-select";
            if (this.el.find(e).find("select").select2("destroy"), this.el.find(e).remove(), t.select.val()) {
                var n = jQuery.extend({}, t.select.data("mylisting-ajax-params"), {
                    page: 1,
                    security: CASE27.ajax_nonce,
                    search: ""
                });
                n["slug" === this.term_value ? "parent" : "parent_id"] = t.select.val(), this.el.addClass("cts-terms-loading"), jQuery.ajax({
                    url: CASE27.mylisting_ajax_url + "&action=mylisting_list_terms",
                    type: "GET",
                    dataType: "json",
                    data: n,
                    beforeSend: function(e) {
                        t.select.data("last_request") && t.select.data("last_request").abort(), t.select.data("last_request", e)
                    },
                    success: function(e) {
                        i.el.removeClass("cts-terms-loading"), "object" === yi(e) && e.results && e.results.length && i.addChildSelect(t)
                    }
                })
            }
        }, MyListing.TermHierarchy.prototype.addChildSelect = function(i) {
            var e = jQuery('<div class="select-wrapper term-select term-select-' + i.index + '">            <select class="custom-select term-select" data-mylisting-ajax="true" data-mylisting-ajax-url="mylisting_list_terms">                <option></option>            </select>        </div>');
            if ("alternate" === this.template && e.find("select").data("dropdown-position", "below"), 0 === i.index) var n = this.originalLabel,
                t = this.placeholder;
            else {
                var a = i.select.find('option[value="' + i.select.val() + '"]').text();
                t = CASE27.l10n.all_in_category.replace("%s", a), n = this.labelTemplate.replace("%index%", i.index).replace("%label%", a)
            }
            this.updateLabel(n);
            var s = jQuery.extend({}, this.ajax_params);
            return s["slug" === this.term_value ? "parent" : "parent_id"] = 0 === i.index ? 0 : i.select.val(), e.find("select").data("mylisting-ajax-params", s).attr("placeholder", t), this.el.append(e), new MyListing.CustomSelect(e.find("select")), e.find("select").on("select:change", function(e) {
                var t = i.select ? i.select.val() : "";
                this.input.val(e.detail.value || t).trigger("change"), this.updateLabel(n), this.maybeAddChildSelect({
                    index: i.index + 1,
                    select: jQuery(e.target)
                })
            }.bind(this)), e
        }, MyListing.TermHierarchy.prototype.handleDefaultValue = function() {
            var i = 0,
                n = null;
            this.selected.forEach(function(e) {
                var t = this.addChildSelect({
                    index: i,
                    select: n
                });
                (n = t.find("select")).append('<option value="' + e.value + '">' + e.label + "</option>").val(e.value), i++
            }.bind(this)), n.trigger("change");
            var e = this.selected[this.selected.length - 1];
            this.updateLabel(this.labelTemplate.replace("%index%", this.selected.length - 1).replace("%label%", e.label)), this.input.val(e.value).trigger("change")
        }, MyListing.TermHierarchy.prototype.fireChangeEvent = function(e) {
            var t = document.createEvent("CustomEvent");
            t.initCustomEvent("termhierarchy:change", !1, !0, {
                value: this.input.val()
            }), this.input.get(0).dispatchEvent(t), this.addWrapperClasses()
        }, MyListing.TermHierarchy.prototype.updateLabel = function(e) {
            "alternate" === this.template && this.label.length && this.label.html(e + '<div class="spin-box"></div>')
        }, MyListing.TermHierarchy.prototype.addWrapperClasses = function() {
            var e = this.input.val().trim();
            this.el[e ? "addClass" : "removeClass"]("cts-term-filled")
        }, jQuery(function(i) {
            i(".term-hierarchy-input").each(function(e, t) {
                new MyListing.TermHierarchy(i(this))
            })
        }), document.addEventListener("DOMContentLoaded", function() {
            jQuery(".cts-carousel").each(function(e, t) {
                function i() {
                    "rtl" === jQuery("html").attr("dir") ? (10 < t.scrollWidth - t.offsetWidth + t.scrollLeft ? a.classList.add("cts-show") : a.classList.remove("cts-show"), t.scrollLeft < -10 ? n.classList.add("cts-show") : n.classList.remove("cts-show")) : (10 < t.scrollWidth - t.offsetWidth - t.scrollLeft ? a.classList.add("cts-show") : a.classList.remove("cts-show"), 10 < t.scrollLeft ? n.classList.add("cts-show") : n.classList.remove("cts-show"))
                }
                var n = t.querySelector(".cts-prev"),
                    a = t.querySelector(".cts-next");
                t.addEventListener("scroll", MyListing.Helpers.debounce(i, 20)), new ResizeSensor(t, MyListing.Helpers.debounce(i, 100)), i()
            })
        }), a = Date.now(), document.onmousemove = o, document.onkeydown = o, document.onmousedown = o, document.ontouchstart = o, document.onscroll = o, MyListing.Helpers.getLastActivity = function() {
            return Date.now() - a
        }, jQuery.ajaxPrefilter(function(e, t, i) {
            t && t.data && 1 === t.data.no_idle && 1e4 <= MyListing.Helpers.getLastActivity() && i.abort()
        }), MyListing.Handlers.Compare_Button = function(e, t) {
            if (e.preventDefault(), MyListing.Explore) {
                var i = (t = jQuery(t)).parents(".lf-item-container"),
                    n = parseInt(i.data("id").replace("listing-id-", ""), 10);
                if (i.hasClass("compare-chosen")) {
                    var a = MyListing.Explore.compare.indexOf(n); - 1 < a && MyListing.Explore.compare.splice(a, 1), i.removeClass("compare-chosen"), t.find("i").removeClass("remove").addClass("add")
                } else MyListing.Explore.compare.includes(n) || (MyListing.Explore.compare.push(n), i.addClass("compare-chosen"), t.find("i").removeClass("add").addClass("remove"))
            }
        }, MyListing.Handlers.Bookmark_Button = function(e, t) {
            e.preventDefault();
            t = jQuery(t);
            if (!jQuery("body").hasClass("logged-in")) return window.location.href = CASE27.login_url;
            if (!t.hasClass("bookmarking")) {
                var i = CASE27.mylisting_ajax_url + "&action=bookmark_listing";
                t.addClass("bookmarking").toggleClass("bookmarked"), t.find(".action-label").html(t.hasClass("bookmarked") ? t.data("active-label") : t.data("label")), jQuery.get(i, {
                    listing_id: t.data("listing-id")
                }, function(e) {
                    t.removeClass("bookmarking")
                })
            }
        }, MyListing.Helpers.coordinatesToDistance = function(e, t, i, n) {
            function a(e) {
                return e * (Math.PI / 180)
            }
            var s = a(i - e),
                o = a(n - t),
                r = Math.sin(s / 2) * Math.sin(s / 2) + Math.cos(a(e)) * Math.cos(a(i)) * Math.sin(o / 2) * Math.sin(o / 2);
            return 6371 * (2 * Math.atan2(Math.sqrt(r), Math.sqrt(1 - r)))
        }, jQuery(window).on("load", function() {
            jQuery(".galleryPreview, .section-slider.owl-carousel").trigger("refresh.owl.carousel")
        }), jQuery(document).ready(window.case27_ready_script = function(o) {
            o(document).trigger("mylisting:refresh-scripts"), o([".c27-main-header", ".finder-container", ".add-listing-step", ".hide-until-load"].join(", ")).css("opacity", 1), setTimeout(function() {
                    o("#submit-job-form .wp-editor-wrap").css("height", "auto")
                }, 2500), "string" == typeof MyListing_Moment_Locale && MyListing_Moment_Locale.length && moment.locale(MyListing_Moment_Locale),
                function() {
                    if (o("body").hasClass("add-listing-form")) {
                        document.addEventListener("invalid", function(e) {
                            jQuery(e.target).addClass("invalid"), jQuery("html, body").animate({
                                scrollTop: jQuery(jQuery(".invalid")[0]).offset().top - 150
                            }, 0)
                        }, !0), document.addEventListener("change", function(e) {
                            jQuery(e.target).removeClass("invalid")
                        }, !0)
                    }
                }(), jQuery("body").hasClass("elementor-editor-active") && (jQuery.fn.parallax = function() {});
            var e = o("#buddypress form#whats-new-form p.activity-greeting").text();
            if (jQuery("#whats-new-textarea textarea").attr("placeholder", e), o(".woocommerce-MyAccount-navigation ul").length && o(".woocommerce-MyAccount-navigation ul li.is-active, .woocommerce-MyAccount-navigation ul li.current-menu-item").length) {
                var t = o(".woocommerce-MyAccount-navigation ul li.is-active, .woocommerce-MyAccount-navigation ul li.current-menu-item").offset().left,
                    i = o(".woocommerce-MyAccount-navigation ul").offset().left;
                i < t && o(".woocommerce-MyAccount-navigation ul").scrollLeft(t - i)
            }
            o(".ph-details").each(function(e, t) {
                o(t).height() % 2 != 0 && o(t).height(o(t).height() + 1)
            }), o(".cat-card .ac-front-side .hovering-c").each(function(e, t) {
                o(t).height() % 2 != 0 && o(t).height(o(t).height() + 1)
            }), o(".mobile-menu").click(function(e) {
                e.preventDefault(), o(".i-nav").addClass("mobile-menu-open").css("opacity", "1"), o("body").addClass("disable-scroll")
            }), o(".mnh-close-icon").click(function(e) {
                e.preventDefault(), o(".i-nav").removeClass("mobile-menu-open i-nav-fixed"), o("body").removeClass("disable-scroll"), o(window).resize()
            }), o(".i-nav-overlay").click(function() {
                o(this).siblings(".i-nav").removeClass("mobile-menu-open"), o("body").removeClass("disable-scroll")
            }), o(".main-nav li .submenu-toggle").click(function() {
                if (window.matchMedia("(max-width:1200px)").matches) {
                    var e = o(this).siblings(".i-dropdown");
                    e.hasClass("shown-menu") ? e.slideUp(300) : (e.slideDown(300), o(this).parent().parent().find("> li > .shown-menu").slideUp(300).removeClass("shown-menu")), e.toggleClass("shown-menu")
                }
            });
            var n, a, s = o(".pricing-item.featured");
            if (o(".pricing-item").hover(function() {
                    o(s).removeClass("featured"), o(this).addClass("active")
                }, function() {
                    o(this).removeClass("active"), o(s).addClass("featured")
                }), o('[data-toggle="tooltip"]').tooltip({
                    trigger: "hover"
                }), o("body").on("hover", ".listing-feed-2", function(e) {
                    o(this).find('[data-toggle="tooltip"]').tooltip({
                        trigger: "hover"
                    })
                }), o(".fc-type-2 .finder-overlay").on("click", function() {
                    o(".fc-type-2").removeClass("fc-type-2-open")
                }), o(".testimonial-carousel.owl-carousel").owlCarousel({
                    mouseDrag: !1,
                    items: 1,
                    center: !0,
                    autoplay: !0,
                    dotsContainer: "#customDots"
                }), o(".testimonial-image").click(function(e) {
                    e.preventDefault(), o(this).addClass("active").siblings().removeClass("active");
                    var t = o(this).data("slide-no");
                    o(".testimonial-carousel.owl-carousel").trigger("to.owl.carousel", t)
                }), o(".gallery-carousel").each(function(e, t) {
                    var i = o(t).data("items") ? o(t).data("items") : 3,
                        n = o(t).data("items-mobile") ? o(t).data("items-mobile") : 2;
                    o(t).owlCarousel({
                        margin: 10,
                        items: i,
                        mouseDrag: !1,
                        responsive: {
                            0: {
                                items: n
                            },
                            600: {
                                items: 3 < i ? 3 : i
                            },
                            1e3: {
                                items: i
                            }
                        }
                    })
                }), o(".gallery-prev-btn").click(function(e) {
                    e.preventDefault(), o(this).parents(".element").find(".gallery-carousel.owl-carousel").trigger("prev.owl.carousel")
                }), o(".gallery-next-btn").click(function(e) {
                    e.preventDefault(), o(this).parents(".element").find(".gallery-carousel.owl-carousel").trigger("next.owl.carousel")
                }), o(".full-screen-carousel .owl-carousel").owlCarousel({
                    loop: !0,
                    margin: 10,
                    items: 2,
                    center: !0,
                    autoWidth: !0
                }), n = null != navigator.userAgent.match(/Android/i), a = null != navigator.userAgent.match(/iPhone|iPad|iPod/i), n && o("body").addClass("smartphoneuser"), a && o("body").addClass("smartphoneuser iOSUser"), o(".galleryPreview").owlCarousel({
                    items: 1,
                    center: !0,
                    dotsContainer: "#customDots",
                    autoHeight: !0
                }), o(".slide-thumb").click(function(e) {
                    e.preventDefault();
                    var t = o(this).data("slide-no");
                    o(".galleryPreview.owl-carousel").trigger("to.owl.carousel", t)
                }), o(".gallery-thumb").each(function(e, t) {
                    var i = o(t).data("items") ? o(t).data("items") : 4,
                        n = o(t).data("items-mobile") ? o(t).data("items-mobile") : 2;
                    o(t).owlCarousel({
                        margin: 10,
                        items: i,
                        mouseDrag: !1,
                        responsive: {
                            0: {
                                items: n
                            },
                            600: {
                                items: 3 < i ? 3 : i
                            },
                            1e3: {
                                items: i
                            }
                        }
                    })
                }), o(".gallerySlider .gallery-prev-btn").click(function(e) {
                    e.preventDefault(), o(".gallery-thumb.owl-carousel").trigger("prev.owl.carousel")
                }), o(".gallerySlider .gallery-next-btn").click(function(e) {
                    e.preventDefault(), o(".gallery-thumb.owl-carousel").trigger("next.owl.carousel")
                }), o("body").hasClass("rtl")) var r = o(".grid").isotope({
                originLeft: !1
            });
            else r = o(".grid").isotope();
            o(window).bind("load resize", function() {
                r.isotope("reloadItems").isotope()
            }), o(".explore-mobile-nav > ul li").on("click", function() {
                setTimeout(function() {
                    r.isotope("reloadItems").isotope()
                }, 400)
            }), o("body").on("click", ".fc-search .close-filters-27", function() {
                r.isotope("reloadItems").isotope()
            }), o(".tab-switch").click(function(e) {
                e.preventDefault(), o(this).tab("show"), setTimeout(function() {
                    r.isotope("reloadItems").isotope()
                }, 400)
            }), o(".listing-feed-carousel").owlCarousel({
                loop: !0,
                margin: 20,
                items: 3,
                smartSpeed: 500,
                onDrag: function(e) {
                    o(".listing-feed-carousel > .owl-item").css("opacity", "1")
                },
                onDragged: function(e) {
                    o(".listing-feed-carousel > .owl-item").css("opacity", "0.4"), o(".listing-feed-carousel > .owl-item.active").css("opacity", "1")
                },
                responsive: {
                    0: {
                        items: 1,
                        margin: 0
                    },
                    768: {
                        items: 2
                    },
                    1e3: {
                        items: 3
                    }
                }
            }), o(".listing-feed-next-btn").click(function(e) {
                e.preventDefault(), o(this).parents(".container").find(".listing-feed-carousel.owl-carousel").trigger("next.owl.carousel"), o(this).parents(".container").find(".listing-feed-carousel > .owl-item").css("opacity", "0.4"), o(this).parents(".container").find(".listing-feed-carousel > .owl-item.active").css("opacity", "1")
            }), o(".listing-feed-prev-btn").click(function(e) {
                e.preventDefault(), o(this).parents(".container").find(".listing-feed-carousel.owl-carousel").trigger("prev.owl.carousel"), o(this).parents(".container").find(".listing-feed-carousel > .owl-item").css("opacity", "0.4"), o(this).parents(".container").find(".listing-feed-carousel > .owl-item.active").css("opacity", "1")
            }), o(".featured-section-carousel").owlCarousel({
                loop: !0,
                margin: 0,
                items: 1,
                center: !0
            }), o(".listing-feed-next-btn").click(function(e) {
                e.preventDefault(), o(".featured-section-carousel.owl-carousel").trigger("next.owl.carousel")
            }), o(".listing-feed-prev-btn").click(function(e) {
                e.preventDefault(), o(".featured-section-carousel.owl-carousel").trigger("prev.owl.carousel")
            }), o(".lf-background-carousel").owlCarousel({
                margin: 20,
                items: 1,
                loop: !0
            }), o(".lf-background-carousel").each(function() {
                o(this).owlCarousel({
                    margin: 20,
                    items: 1,
                    loop: !0
                }), o(this).on("prev.owl.carousel", function(e) {
                    e.stopPropagation()
                }), o(this).on("next.owl.carousel", function(e) {
                    e.stopPropagation()
                })
            }), o("body").on("click", ".lf-item-next-btn", function(e) {
                e.preventDefault(), o(this).parents(".lf-item").find(".lf-background-carousel.owl-carousel").trigger("next.owl.carousel")
            }), o("body").on("click", ".lf-item-prev-btn", function(e) {
                e.preventDefault(), o(this).parents(".lf-item").find(".lf-background-carousel.owl-carousel").trigger("prev.owl.carousel")
            }), o(".filter-listing-type-select, .filter-listings-select").on("change", function(e) {
                e.preventDefault();
                var t = o(".filter-listing-type-select option:selected").val(),
                    i = o(".filter-listings-select option:selected").val(),
                    n = [];
                if (t) {
                    var a = new URL(t).searchParams.get("filter_by_type");
                    a && n.push("filter_by_type=" + a)
                }
                if (i) {
                    var s = new URL(i).searchParams.get("status");
                    s && n.push("status=" + s)
                }
                if (!n.length) return window.location.href = o(".filter-listing-type-select :first").val();
                window.location.href = o(".filter-listing-type-select :first").val() + "?" + n.join("&")
            }), o(".clients-feed-carousel").owlCarousel({
                loop: !0,
                margin: 20,
                items: 5,
                responsive: {
                    0: {
                        items: 3
                    },
                    600: {
                        items: 3
                    },
                    1e3: {
                        items: 5
                    }
                }
            }), o(".clients-feed-next-btn").click(function(e) {
                e.preventDefault(), o(".clients-feed-carousel.owl-carousel").trigger("next.owl.carousel")
            }), o(".clients-feed-prev-btn").click(function(e) {
                e.preventDefault(), o(".clients-feed-carousel.owl-carousel").trigger("prev.owl.carousel")
            });
            var l = o(".header-gallery-carousel .item").length;
            o(".header-gallery-carousel").owlCarousel({
                    items: Math.min.apply(Math, [3, l]),
                    responsive: {
                        0: {
                            items: Math.min.apply(Math, [1, l])
                        },
                        480: {
                            items: Math.min.apply(Math, [2, l])
                        },
                        992: {
                            items: Math.min.apply(Math, [3, l])
                        }
                    }
                }), o("body.logged-in .comment-info a").click(function(e) {
                    e.preventDefault(), o(this).parents().siblings(".element").toggleClass("element-visible")
                }),
                function() {
                    var e = o("a.back-to-top");
                    if (e.length) {
                        var t = function() {
                            e.css("opacity", "0"), setTimeout(function() {
                                e.css("visibility", "hidden")
                            }, 200)
                        };
                        e.click(function(e) {
                            e.preventDefault(), t(), o("html, body").animate({
                                scrollTop: 0
                            }, 1e3)
                        });
                        var i = function() {
                            800 <= o(window).scrollTop() ? (e.css("visibility", "visible"), e.css("opacity", "1")) : t()
                        };
                        o(window).scroll(MyListing.Helpers.debounce(i, 200)), i()
                    }
                }(), jQuery(".c27-quick-view-modal").on("hidden.bs.modal", function(e) {
                    o(".c27-quick-view-modal .container").css("height", "auto")
                }), o("body").on("click", ".c27-toggle-quick-view-modal", function(e) {
                    e.preventDefault(), o(".c27-quick-view-modal").modal("show"), o(".c27-quick-view-modal").addClass("loading-modal"), o.ajax({
                        url: CASE27.mylisting_ajax_url + "&action=get_listing_quick_view&security=" + CASE27.ajax_nonce,
                        type: "GET",
                        dataType: "json",
                        data: {
                            listing_id: o(this).data("id")
                        },
                        success: function(e) {
                            o(".c27-quick-view-modal").removeClass("loading-modal"), o(".c27-quick-view-modal .modal-content").html(e.html), o(".c27-quick-view-modal .c27-map").css("height", o(".c27-quick-view-modal .modal-content").height()), o(window).trigger("resize"), setTimeout(function() {
                                new MyListing.Maps.Map(o(".c27-quick-view-modal .c27-map").get(0))
                            }, 10), o(".lf-background-carousel").owlCarousel({
                                margin: 20,
                                items: 1,
                                loop: !0
                            }), o(".c27-quick-view-modal .container").each(function(e, t) {
                                o(t).height() % 2 != 0 && o(t).height(o(t).height() + 1)
                            });
                            var t = o(".c27-quick-view-modal .modal-content").height();
                            o(".c27-quick-view-modal .block-map").css("height", t)
                        }
                    })
                }), o(".c27-display-button").each(function(e, t) {
                    var i = jQuery(t);
                    i.on("click", function() {
                        if (!i.hasClass("loading") && !i.hasClass("loaded")) {
                            var e = {
                                listing_id: o(this).data("listing-id"),
                                field_id: o(this).data("field-id")
                            };
                            i.addClass("loading"), o.post(CASE27.mylisting_ajax_url + "&action=display_contact_info&security=" + CASE27.ajax_nonce, e, function(e) {
                                i.removeClass("loading").addClass("loaded"), e.value && i.html(e.value)
                            })
                        }
                    })
                }), o("#ml-messages-modal, #quicksearch-mobile-modal").on("shown.bs.modal", function() {
                    o("body").addClass("disable-scroll")
                }).on("hidden.bs.modal", function() {
                    o("body").removeClass("disable-scroll")
                }), o(".c27-add-product-form input#_virtual").change(function(e) {
                    o(".c27-add-product-form .product_shipping_class_wrapper")["checked" == o(this).attr("checked") ? "hide" : "show"]()
                }).change(), o(".c27-add-product-form input#_sale_price").keyup(function(e) {
                    o(".c27-add-product-form ._sale_price_dates_from__wrapper")[o(this).val() ? "show" : "hide"](), o(".c27-add-product-form ._sale_price_dates_to__wrapper")[o(this).val() ? "show" : "hide"]()
                }).keyup(), o(".c27-add-product-form input#_manage_stock").change(function(e) {
                    o(".c27-add-product-form ._stock__wrapper")["checked" == o(this).attr("checked") ? "show" : "hide"](), o(".c27-add-product-form ._backorders__wrapper")["checked" == o(this).attr("checked") ? "show" : "hide"]()
                }).change(), o(".woocommerce-MyAccount-navigation > ul").each(function() {
                    o(this).children().length <= 6 && o(this).addClass("short")
                })
        }), window.cts_render_captcha = function() {
            jQuery(".g-recaptcha").each(function(e, t) {
                grecaptcha.render(t, {
                    sitekey: t.dataset.sitekey
                }), setTimeout(function() {
                    return t.style.opacity = 1
                }, 1e3)
            })
        }, jQuery(document).ready(function(l) {
            function t(e) {
                var t = e.parents(".pricing-item");
                if (!t.length) return !1;
                if (void 0 === t.data("selected")) return t.find('.owned-product-packages input[name="listing_package"]').first().prop("checked", !0), !0;
                var i = parseInt(t.data("selected"), 10);
                return t.find('.owned-product-packages input[name="listing_package"][value="' + i + '"]').prop("checked", !0), !0
            }
            var e;
            l(".main-loader").addClass("loader-hidden"), setTimeout(function() {
                    l(".main-loader").hide()
                }, 600), l("body").addClass("c27-site-loaded"), l("header.header").parents("section.elementor-element").addClass("c27-header-element"), l(".c27-open-popup-window, .cts-open-popup").click(function(e) {
                    e.preventDefault();
                    var t = screen.height / 2 - 200,
                        i = screen.width / 2 - 300;
                    window.open(this.href, "targetWindow", ["toolbar=no", "location=no", "status=no", "menubar=no", "scrollbars=yes", "resizable=yes", "width=600", "height=400", "top=" + t, "left=" + i].join(","))
                }), l(".c27-add-listing-review, .show-review-form, .pa-below-title .listing-rating").click(function(e) {
                    e.preventDefault(), l(".toggle-tab-type-comments").first().click(), setTimeout(function() {
                        l('#commentform textarea[name="comment"]').focus()
                    }, 250)
                }), l(".c27-book-now").click(function(e) {
                    e.preventDefault(), l(".toggle-tab-type-bookings").first().click()
                }), l(".modal.c27-open-on-load").modal("show"), l(".c27-open-modal").click(function(e) {
                    e.preventDefault();
                    var t = l(this);
                    l(".modal.in").one("hidden.bs.modal", function() {
                        l(t.data("target")).modal("show")
                    }).modal("hide")
                }), l(".featured-search .location-wrapper .geocode-location").click(function(e) {
                    var t = l(this).siblings("input");
                    MyListing.Geocoder.getUserLocation({
                        receivedAddress: function(e) {
                            if (!e) return !1;
                            setTimeout(function() {
                                t.trigger("change")
                            }, 5), t.val(e.address)
                        }
                    })
                }), l("body.single-listing .tab-template-two-columns").each(function(e, t) {
                    function i(e) {
                        var t = window.matchMedia("(max-width: 991.5px)").matches ? "mobile" : "desktop";
                        if (t === r && !e) return !1;
                        "mobile" == t ? s.forEach(function(e, t) {
                            l(e).appendTo(n), o[t] && l(o[t]).appendTo(n)
                        }) : o.forEach(function(e, t) {
                            l(e).appendTo(a)
                        }), r = t
                    }
                    var n = l(this).find(".cts-column-wrapper.cts-main-column"),
                        a = l(this).find(".cts-column-wrapper.cts-side-column"),
                        s = n.find("> div").toArray(),
                        o = a.find("> div").toArray(),
                        r = window.matchMedia("(max-width: 991.5px)").matches ? "mobile" : "desktop";
                    i("mobile" === r), l(window).on("resize", MyListing.Helpers.debounce(function() {
                        i()
                    }, 300))
                }), l('.cts-pricing-item input[name="listing_package"]').change(function(e) {
                    var t = l(this).parents(".pricing-item");
                    if (!t.length) return !0;
                    t.data("selected", l(this).val())
                }), l(".cts-pricing-item .use-package-toggle").click(function(e) {
                    t(l(this))
                }), l(".cts-pricing-item .select-plan:not(.cts-trigger-buy-new)").click(function(e) {
                    e.preventDefault(), t(l(this)) && l("#job_package_selection").submit()
                }), l(".cts-pricing-item .cts-trigger-buy-new").click(function(e) {
                    e.preventDefault();
                    var t = l(this).parents(".pricing-item");
                    if (!t.length) return !1;
                    t.find("input.cts-buy-new").prop("checked", !0), l("#job_package_selection").submit()
                }), l(".cts-wcpl-package a.select-plan").on("click", function(e) {
                    e.preventDefault(), l(this).siblings(".c27-job-package-radio-button").prop("checked", !0), l("#job_package_selection").submit()
                }),
                function() {
                    if (!l("#user-cart-menu").length) return;
                    l(document.body).one("wc_fragments_loaded", function(e) {
                        l("#user-cart-menu").addClass("user-cart-updated")
                    })
                }(), l(document).on("mousedown click", ".c27-copy-link", function(e) {
                    e.preventDefault();
                    var t = l(this);
                    if (!t.hasClass("copying")) {
                        t.addClass("copying");
                        var i = t.find("span"),
                            n = i.html(),
                            a = t.attr("href"),
                            s = l("<input>");
                        l("body").append(s), s.val(a).select(), document.execCommand("copy"), s.remove(), i.html(CASE27.l10n.copied_to_clipboard), setTimeout(function() {
                            i.html(n), t.removeClass("copying")
                        }, 1500)
                    }
                }),
                function() {
                    var e = l(".c27-main-header");
                    if (e.length && e.hasClass("header-fixed")) {
                        var t = null,
                            i = 0,
                            n = e.outerHeight();
                        l(window).on("scroll", MyListing.Helpers.debounce(function() {
                            i = l(window).scrollTop(), t !== i && (n < i || n < i && null === t ? e.addClass("header-scroll") : e.removeClass("header-scroll"), n + 250 < i ? e.addClass("header-scroll-hide") : e.removeClass("header-scroll-hide"), n < i && i < t || null === t ? e.addClass("header-scroll-active") : e.removeClass("header-scroll-active"), t = i)
                        }, 20))
                    }
                }(),
                function() {
                    var e = l(".c27-main-header");
                    if (e.length && e.hasClass("header-menu-center")) {
                        var t = l(".header-left").width(),
                            i = l(".header-right").width(),
                            n = e.find(".header-container").width();
                        l(".header-center > .i-nav").css("max-width", n - t - i - 10)
                    }
                }(), l(".modal-27").on("show.bs.modal", function() {
                    l(this).addClass("show-modal")
                }), l(".modal-27").on("hidden.bs.modal", function() {
                    l(this).removeClass("show-modal")
                }), l(".modal-27").on("hide.bs.modal", function(e) {
                    var t = l(this);
                    t.hasClass("in") ? (e.preventDefault(), t.removeClass("in"), l("body").addClass("modal-closing"), setTimeout(function() {
                        t.modal("hide")
                    }, 200)) : (l("body").removeClass("modal-closing"), l("body").addClass("modal-closed"), setTimeout(function() {
                        return l("body").removeClass("modal-closed")
                    }, 100))
                }), l(".elementor-element[data-mylisting-link-to]").each(function() {
                    var e = l(this).data("mylisting-link-to");
                    if ("object" === yi(e) && "undefined" !== e.url) {
                        var t = l('<a class="mylisting-link-to"></a>');
                        t.attr("href", e.url), e.is_external && t.attr("target", "_blank"), e.nofollow && t.attr("rel", "nofollow");
                        var i = l(this).find(".elementor-column-wrap");
                        i.length ? i.append(t) : l(this).find(".elementor-widget-wrap").append(t)
                    }
                }), l(".cts-open-chat").on("click", function(e) {
                    e.preventDefault();
                    var t = l(this).data("user-id") || null,
                        i = l(this).data("post-data");
                    if (!l("body").hasClass("logged-in")) return window.location.href = CASE27.login_url;
                    MyListing.Messages.open(t, i), setTimeout(function() {
                        l(MyListing.Messages.$el).find("#ml-conv-textarea").focus()
                    }, 150)
                }), l("#quicksearch-mobile-modal").on("shown.bs.modal", function(e) {
                    e.preventDefault(), setTimeout(function() {
                        l('#quicksearch-mobile-modal input[name="search_keywords"]').focus().get(0).click()
                    }, 800)
                }), l(".mobile-nav-head .user-profile-name").on("click", function(e) {
                    e.preventDefault(), l(".mobile-user-menu").slideToggle()
                }), (e = document.getElementById("commentform")) && e.removeAttribute("novalidate")
        }), jQuery(document).ready(function(s) {
            if (!s("#commentform").length) return !1;
            s("#commentform")[0].encoding = "multipart/form-data", s("body").on("click", ".review-gallery-image-remove", function(e) {
                e.preventDefault(), s(this).parents(".review-gallery-image").remove()
            });
            s("#review-gallery-add-input").on("change", function() {
                s("#review-gallery-preview").html(""),
                    function(e, i) {
                        if (e.files)
                            for (var t = e.files.length, n = 0; n < t; n++) {
                                var a = new FileReader;
                                a.onload = function(e) {
                                    var t = s('<div class="review-gallery-image">\n\t\t\t\t\t\t\t<span class="review-gallery-preview-icon">\n\t\t\t\t\t\t\t\t<i class="material-icons file_upload"></i>\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t</div>').css("background-image", "url('" + e.target.result + "')");
                                    s(t).appendTo(i)
                                }, a.readAsDataURL(e.files[n])
                            }
                    }(this, "#review-gallery-preview")
            })
        }), (s = jQuery)(".profile-tab-toggle").on("click", function(e) {
            e.preventDefault(), s(".profile-menu li.active").removeClass("active"), s(e.target).parent().addClass("active");
            var t = s(".listing-tab.tab-active"),
                i = s(e.target).data("section-id"),
                n = s(".listing-tab#profile_tab_" + i);
            if (t.attr("id") === "profile_tab_" + i) return t.addClass("tab-same"), void setTimeout(function() {
                return t.removeClass("tab-same")
            }, 100);
            t.addClass("tab-hiding"), setTimeout(function() {
                t.removeClass("tab-active tab-hiding").addClass("tab-hidden"), n.addClass("tab-showing"), setTimeout(function() {
                    n.removeClass("tab-hidden tab-showing").addClass("tab-active").trigger("mylisting:single:tab-switched"), jQuery(document).trigger("mylisting/single:tab-switched")
                }, 25)
            }, 200)
        }), jQuery(function(t) {
            if (void 0 !== window.MyListing_Switch_Config) {
                var i = "tr.woocommerce-grouped-product-list-item.product-type-job_package_subscription";
                if (t(i).length) {
                    var e = window.MyListing_Switch_Config;
                    t(".single-product " + i + "#product-" + e.current_plan + " label a").append("<span>" + e.current_plan_text + "</span>"), t(".single_add_to_cart_button").hide(), t(i).click(function(e) {
                        e.preventDefault(), t(e.target).find('input[type="checkbox"]').prop("checked", !0), t(i).parents("form").submit()
                    })
                }
            }
        }), MyListing.Dialog = function(e) {
            this.visible = !1, this.args = jQuery.extend({
                message: "",
                status: "info",
                dismissable: !0,
                spinner: !1,
                timeout: 3e3
            }, e), this.show(), this.setTimeout()
        }, MyListing.Dialog.prototype.draw = function() {
            this.template = jQuery(jQuery("#mylisting-dialog-template").text()), this.template.addClass(this.args.status), this.insertContent(), this.template.appendTo("body")
        }, MyListing.Dialog.prototype.refresh = function(e) {
            this.args = jQuery.extend(this.args, e), this.setTimeout(), this.insertContent()
        }, MyListing.Dialog.prototype.insertContent = function() {
            var t = this;
            this.template.find(".mylisting-dialog--message").html(this.args.message), this.template.find(".mylisting-dialog--dismiss")[this.args.dismissable ? "removeClass" : "addClass"]("hide").click(function(e) {
                e.preventDefault(), t.hide()
            }), this.template.find(".mylisting-dialog--loading")[this.args.spinner ? "removeClass" : "addClass"]("hide")
        }, MyListing.Dialog.prototype.setTimeout = function() {
            var e = this;
            e.timeout && clearTimeout(e.timeout), !isNaN(e.args.timeout) && 0 < e.args.timeout && (e.timeout = setTimeout(function() {
                e.hide()
            }, e.args.timeout))
        }, MyListing.Dialog.prototype.show = function() {
            var e = this;
            e.draw(), setTimeout(function() {
                e.template.addClass("slide-in"), e.visible = !0
            }, 15)
        }, MyListing.Dialog.prototype.hide = function() {
            var e = this;
            e.template.removeClass("slide-in").addClass("slide-out"), setTimeout(function() {
                e.template.remove(), e.visible = !1
            }, 250)
        }, 

        Vue.component("wp-search-filter", {
            props: {
                listingType: String,
                filterKey: String,
                location: String,
                label: String
            },
            mounted: function() {
                var e = this;
                this.$nextTick(function() {
                    e.$root.$on("reset-filters:" + e.listingType, function() {
                        e.filters[e.filterKey] = ""
                    })
                })
            },
            methods: {
                updateInput: function() {
                    this.filters[this.filterKey] = this.$refs.input.value, this.$emit("input", this.$refs.input.value, {
                        filterType: this.$options.name,
                        filterKey: this.filterKey,
                        location: this.location
                    })
                }
            },
            computed: {
                filters: function() {
                    return this.$root.types[this.listingType].filters
                }
            }
        }), 

        Vue.component( 'double-checkbox-filter', {
            props: {
                listingType: String,
                filterKey: String,
                location: String,
                ajaxParams: String,
                label: String,
                preSelected: Array,
                multiple: Boolean,
            },

            data() {
                return {
                    selected: this.multiple ? [] : '',
                };
            },

            created() {
                this.selected = this.multiple
                    ? this.filters[ this.filterKey ].split(',')
                    : this.filters[ this.filterKey ];
            },

            mounted() {
                this.$nextTick( () => {
                    new MyListing.CustomSelect( this.$refs.select );
                    this.$root.$on( 'reset-filters:'+this.listingType, () => {
                        this.selected = this.multiple ? [] : '';
                        this.filters[ this.filterKey ] = '';
                        jQuery( this.$refs.select ).val( this.selected )
                            .trigger('change').trigger('select2:close');
                    } );
                } );
            },

            methods: {
                handleChange(e) {
                  console.log( this.multiple );
                    this.selected = this.multiple
                        ? ( Array.isArray(e.detail.value) ? e.detail.value : [] )
                        : ( typeof e.detail.value === 'string' ? e.detail.value : '' );

                    this.updateInput();
                },

                updateInput() {
                  console.log( this );
                    var value = this.multiple ? this.selected.filter(Boolean).join(',') : this.selected;
                    this.filters[ this.filterKey ] = value;
                    this.$emit( 'input', value, {
                        filterType: this.$options.name,
                        filterKey: this.filterKey,
                        location: this.location,
                    } );
                },

                isSelected( choice ) {
                    if ( ! this.multiple ) {
                        return choice === this.selected;
                    }

                    return this.selected.includes( choice );
                },
            },

            computed: {
                filters() {
                    return this.$root.types[ this.listingType ].filters;
                },
            },
        } ),

        Vue.component("text-filter", {
            props: {
                listingType: String,
                filterKey: String,
                location: String,
                label: String
            },
            mounted: function() {
                var e = this;
                this.$nextTick(function() {
                    e.$root.$on("reset-filters:" + e.listingType, function() {
                        e.filters[e.filterKey] = ""
                    })
                })
            },
            methods: {
                updateInput: function() {
                    this.filters[this.filterKey] = this.$refs.input.value, this.$emit("input", this.$refs.input.value, {
                        filterType: this.$options.name,
                        filterKey: this.filterKey,
                        location: this.location
                    })
                }
            },
            computed: {
                filters: function() {
                    return this.$root.types[this.listingType].filters
                }
            }
        }), Vue.component("location-filter", {
            props: {
                listingType: String,
                filterKey: String,
                location: String,
                label: String
            },
            data: function() {
                return {
                    latitudeKey: "lat",
                    longitudeKey: "lng"
                }
            },
            created: function() {
                this.$root.$on("request-location:" + this.listingType, this.requestALocation)
            },
            mounted: function() {
                var e = this;
                this.$nextTick(function() {
                    MyListing.Maps && MyListing.Maps.loaded ? new MyListing.Maps.Autocomplete(e.$refs.input) : jQuery(document).on("maps:loaded", function() {
                        new MyListing.Maps.Autocomplete(e.$refs.input)
                    }), jQuery(e.$root.$el).find(".finder-search").on("scroll", MyListing.Helpers.debounce(function(e) {
                        jQuery(".pac-container").css("display", "none"), jQuery(".cts-autocomplete-dropdown").removeClass("active")
                    }, 100, {
                        leading: !0,
                        trailing: !1
                    })), e.$root.$on("reset-filters:" + e.listingType, function() {
                        e.filters[e.filterKey] = "", e.filters[e.latitudeKey] = !1, e.filters[e.longitudeKey] = !1
                    })
                })
            },
            methods: {
                handleAutocomplete: function(t) {
                    var i = this,
                        e = t.detail.place;
                    t.target.value.length ? e.address && e.latitude && e.longitude ? this.updateInput(e) : MyListing.Geocoder.geocode(t.target.value, function(e) {
                        e && (e.address = t.target.value, i.updateInput(e))
                    }) : this.updateInput({
                        address: "",
                        latitude: !1,
                        longitude: !1
                    })
                },
                updateInput: function(e, t, i) {
                    var n = !(1 < arguments.length && void 0 !== t) || t,
                        a = 2 < arguments.length && void 0 !== i && i;
                    this.filters[this.filterKey] = e.address, this.filters[this.latitudeKey] = e.latitude, this.filters[this.longitudeKey] = e.longitude, this.$emit("input", this.filters[this.filterKey], {
                        filterType: this.$options.name,
                        filterKey: this.filterKey,
                        location: this.location,
                        shouldDebounce: n,
                        forceGet: a
                    })
                },
                updateWithUserLocation: MyListing.Helpers.debounce(function() {
                    var t = this;
                    MyListing.Geocoder.getUserLocation({
                        receivedAddress: function(e) {
                            return t.updateInput(e)
                        },
                        geolocationFailed: function() {
                            new MyListing.Dialog({
                                message: CASE27.l10n.geolocation_failed
                            })
                        }
                    })
                }, 1e3, {
                    leading: !0,
                    trailing: !1
                }),
                requestALocation: function() {
                    var t = this,
                        e = this.currentLocation;
                    if (e.address && e.latitude && e.longitude) return this.updateInput(e, !1, !0);
                    if (!e.address || e.latitude || e.longitude) {
                        var i = new MyListing.Dialog({
                            message: CASE27.l10n.nearby_listings_retrieving_location,
                            timeout: !1,
                            dismissable: !1,
                            spinner: !0
                        });
                        MyListing.Geocoder.getUserLocation({
                            receivedAddress: function(e) {
                                t.updateInput(e, !0, !0), i.refresh({
                                    message: CASE27.l10n.nearby_listings_searching,
                                    timeout: 2e3,
                                    spinner: !0,
                                    dismissable: !1
                                })
                            },
                            geolocationFailed: function() {
                                i.refresh({
                                    message: CASE27.l10n.nearby_listings_location_required,
                                    timeout: 4e3,
                                    dismissable: !0,
                                    spinner: !1
                                }), t.updateInput(t.currentLocation, !0, !0), jQuery(t.$refs.input).focus().one("input", function() {
                                    return i.hide()
                                })
                            }
                        })
                    } else {
                        var n = CASE27_Explore_Settings.Cache;
                        void 0 !== n.defaultLocation ? this.updateInput(n.defaultLocation, !0, !0) : MyListing.Geocoder.geocode(e.address, function(e) {
                            n.defaultLocation = {
                                address: e ? e.address : "",
                                latitude: !!e && e.latitude,
                                longitude: !!e && e.longitude
                            }, t.updateInput(n.defaultLocation, !0, !0)
                        })
                    }
                }
            },
            computed: {
                currentLocation: function() {
                    return {
                        address: this.filters[this.filterKey] ? this.filters[this.filterKey] : "",
                        latitude: this.filters[this.latitudeKey],
                        longitude: this.filters[this.longitudeKey]
                    }
                },
                filters: function() {
                    return this.$root.types[this.listingType].filters
                }
            }
        }), Vue.component("proximity-filter", {
            props: {
                listingType: String,
                filterKey: String,
                location: String,
                label: String,
                units: String,
                max: Number,
                step: Number,
                default: Number
            },
            data: function() {
                return {
                    locked: !1
                }
            },
            mounted: function() {
                var e = this;
                this.$nextTick(function() {
                    jQuery(e.$refs.slider).slider({
                        range: "min",
                        min: 0,
                        max: e.max,
                        step: e.step,
                        slide: e.updateInput,
                        value: e.filters[e.filterKey] ? parseFloat(e.filters[e.filterKey]) : e.default
                    }), e.$root.$on("reset-filters:" + e.listingType, function() {
                        e.filters[e.filterKey] = e.default, e.updateUI()
                    })
                })
            },
            methods: {
                updateInput: function(e, t) {
                    this.locked || (this.filters[this.filterKey] = t.value, this.$emit("input", t.value, {
                        filterType: this.$options.name,
                        filterKey: this.filterKey,
                        location: this.location
                    }))
                },
                updateUI: function() {
                    this.locked = !0;
                    var e = this.filters[this.filterKey] ? parseFloat(this.filters[this.filterKey]) : this.default;
                    jQuery(this.$refs.slider).slider("value", e), this.locked = !1
                }
            },
            computed: {
                displayValue: function() {
                    var e = isNaN(parseFloat(this.filters[this.filterKey])) ? this.filters[this.filterKey] : parseFloat(this.filters[this.filterKey]).toLocaleString();
                    return "".concat(this.label, " ").concat(e).concat(this.units)
                },
                filters: function() {
                    return this.$root.types[this.listingType].filters
                }
            }
        }), Vue.component("date-filter", {
            props: {
                listingType: String,
                filterKey: String,
                location: String,
                label: String,
                type: String,
                l10n: Object
            },
            data: function() {
                return {
                    startDate: "",
                    endDate: "",
                    dateFormat: "YYYY-MM-DD",
                    locked: !1,
                    startPicker: null,
                    endPicker: null
                }
            },
            created: function() {
                var e = this.filters[this.filterKey].split(".."),
                    t = moment(e[0] ? e[0] : ""),
                    i = moment(e[1] ? e[1] : "");
                this.startDate = t.isValid() ? t.clone().locale("en").format(this.dateFormat) : "", this.endDate = i.isValid() ? i.clone().locale("en").format(this.dateFormat) : ""
            },
            mounted: function() {
                var t = this;
                this.$nextTick(function() {
                    t.startPicker = new MyListing.Datepicker(t.$refs.startpicker), t.endPicker = new MyListing.Datepicker(t.$refs.endpicker), jQuery(t.$root.$el).find(".finder-search").on("scroll", MyListing.Helpers.debounce(function(e) {
                        t.startPicker.drp.hide(), t.endPicker.drp && t.endPicker.drp.hide()
                    }, 100, {
                        leading: !0,
                        trailing: !1
                    })), t.$root.$on("reset-filters:" + t.listingType, function() {
                        t.locked = !0, t.startDate = t.endDate = "", t.startPicker.setValue(moment("")), t.endPicker.setValue(moment("")), t.locked = !1
                    })
                })
            },
            methods: {
                updateInput: function() {
                    if ("exact" === this.type) var e = this.startDate;
                    else if (this.startDate || this.endDate) e = "".concat(this.startDate, "..").concat(this.endDate);
                    else e = "";
                    this.filters[this.filterKey] = e, this.locked || this.$emit("input", e, {
                        filterType: this.$options.name,
                        filterKey: this.filterKey,
                        location: this.location,
                        shouldDebounce: !1
                    })
                }
            },
            computed: {
                filters: function() {
                    return this.$root.types[this.listingType].filters
                }
            }
        }), Vue.component("date-year-filter", {
            props: {
                listingType: String,
                filterKey: String,
                location: String,
                choices: Array,
                label: String,
                type: String,
                l10n: Object
            },
            data: function() {
                return {
                    startDate: "",
                    endDate: "",
                    dateFormat: "YYYY",
                    locked: !1
                }
            },
            created: function() {
                var e = this.filters[this.filterKey].split(".."),
                    t = moment(e[0] ? e[0] : ""),
                    i = moment(e[1] ? e[1] : "");
                this.startDate = t.isValid() ? t.clone().locale("en").format(this.dateFormat) : "", this.endDate = i.isValid() ? i.clone().locale("en").format(this.dateFormat) : ""
            },
            mounted: function() {
                var e = this;
                this.$nextTick(function() {
                    new MyListing.CustomSelect(e.$refs.startpicker), new MyListing.CustomSelect(e.$refs.endpicker), e.$root.$on("reset-filters:" + e.listingType, function() {
                        e.locked = !0, e.startDate = e.endDate = "", jQuery(e.$refs.startpicker).val("").trigger("change").trigger("select2:close"), jQuery(e.$refs.endpicker).val("").trigger("change").trigger("select2:close"), e.locked = !1
                    })
                })
            },
            methods: {
                updateInput: function() {
                    if ("exact" === this.type) var e = this.startDate;
                    else if (this.startDate || this.endDate) e = "".concat(this.startDate, "..").concat(this.endDate);
                    else e = "";
                    this.filters[this.filterKey] = e, this.locked || this.$emit("input", e, {
                        filterType: this.$options.name,
                        filterKey: this.filterKey,
                        location: this.location,
                        shouldDebounce: !1
                    })
                }
            },
            computed: {
                filters: function() {
                    return this.$root.types[this.listingType].filters
                }
            }
        }), Vue.component("recurring-date-filter", {
            props: {
                listingType: String,
                filterKey: String,
                location: String,
                label: String,
                presets: Array,
                enableDatepicker: Boolean,
                enableTimepicker: Boolean,
                l10n: Object
            },
            data: function() {
                return {
                    selected: "@custom",
                    startDate: "",
                    endDate: "",
                    dateFormat: this.enableTimepicker ? "YYYY-MM-DD HH:mm" : "YYYY-MM-DD",
                    locked: !1,
                    startPicker: null,
                    endPicker: null
                }
            },
            created: function() {
                var t = this.filters[this.filterKey];
                if (this.enableDatepicker && -1 !== t.indexOf("..")) {
                    var e = moment(t.split("..")[0]),
                        i = moment(t.split("..")[1]);
                    this.selected = "@custom", this.startDate = e.isValid() ? e.clone().locale("en").format(this.dateFormat) : "", this.endDate = i.isValid() ? i.clone().locale("en").format(this.dateFormat) : ""
                } else {
                    var n = this.presets.find(function(e) {
                        return e.key === t
                    });
                    this.selected = n ? n.key : this.presets.length ? this.presets[0].key : "@custom"
                }
            },
            mounted: function() {
                var t = this;
                this.$nextTick(function() {
                    var e = t.enableTimepicker;
                    t.startPicker = new MyListing.Datepicker(t.$refs.start, {
                        timepicker: e
                    }), t.endPicker = new MyListing.Datepicker(t.$refs.end, {
                        timepicker: e
                    }), t.endPicker.do(function(e) {
                        return e.drp.minDate = moment(t.startDate)
                    }), jQuery(t.$root.$el).find(".finder-search").on("scroll", MyListing.Helpers.debounce(function(e) {
                        t.startPicker.drp.hide(), t.endPicker.drp.hide()
                    }, 100, {
                        leading: !0,
                        trailing: !1
                    })), new MyListing.CustomSelect(t.$refs.select), t.$root.$on("reset-filters:" + t.listingType, function() {
                        t.locked = !0, t.selected = t.presets.length ? t.presets[0].key : "@custom", t.startPicker.setValue(moment("")), t.endPicker.setValue(moment("")), t.locked = !1
                    })
                })
            },
            methods: {
                setPreset: function(e) {
                    this.selected !== e && (this.selected = e, this.updateInput())
                },
                updateInput: function() {
                    var t = this;
                    if ("@custom" === this.selected && this.enableDatepicker) {
                        this.endPicker.do(function(e) {
                            return e.drp.minDate = moment(t.startDate)
                        }), this.startDate || (this.endDate = "", this.endPicker.do(function(e) {
                            e.value = moment(""), e.updateInputValues()
                        }));
                        var e = this.startDate ? "".concat(this.startDate, "..").concat(this.endDate) : ""
                    } else if ("@custom" !== this.selected)
                        if (this.presets.length && this.presets[0].key === this.selected) e = "";
                        else e = this.selected;
                    this.filters[this.filterKey] = e, this.locked || this.$emit("input", e, {
                        filterType: this.$options.name,
                        filterKey: this.filterKey,
                        location: this.location,
                        shouldDebounce: !1
                    })
                }
            },
            computed: {
                filters: function() {
                    return this.$root.types[this.listingType].filters
                }
            }
        }), Vue.component("range-filter", {
            props: {
                listingType: String,
                filterKey: String,
                location: String,
                label: String,
                value: String,
                type: String,
                prefix: String,
                suffix: String,
                behavior: String,
                min: Number,
                max: Number,
                step: Number,
                formatValue: Boolean
            },
            data: function() {
                return {
                    defaultValue: "range" === this.type ? "".concat(this.min, "..").concat(this.max) : "upper" === this.behavior ? this.min : this.max,
                    locked: !1,
                    debounceUIUpdate: MyListing.Helpers.debounce(this.updateUI, 200)
                }
            },
            mounted: function() {
                var i = this;
                this.$nextTick(function() {
                    var e = {
                        range: "single" !== i.type || "min",
                        min: i.min,
                        max: i.max,
                        step: i.step,
                        slide: i.updateInput
                    };
                    if ("single" === i.type && (e.value = i.value ? parseFloat(i.value) : "upper" === i.behavior ? i.min : i.max, "upper" === i.behavior && (e.classes = {
                            "ui-slider": "reverse-dir"
                        })), "range" === i.type) {
                        var t = i.value.split("..");
                        e.values = [t[0] ? parseFloat(t[0]) : i.min, t[1] ? parseFloat(t[1]) : i.max]
                    }
                    jQuery(i.$refs.slider).slider(e), i.$root.$on("reset-filters:" + i.listingType, function() {
                        i.filters[i.filterKey] = "", i.updateUI()
                    })
                })
            },
            methods: {
                updateInput: function(e, t) {
                    if (!this.locked) {
                        "single" === this.type ? this.step + t.value > this.max && (this.filters[this.filterKey] = t.value = this.max, this.updateUI()) : this.step + t.values[1] > this.max && (t.values[1] = this.max, this.filters[this.filterKey] = "".concat(t.values[0], "..").concat(t.values[1]), this.updateUI());
                        var i = "single" === this.type ? t.value : "".concat(t.values[0], "..").concat(t.values[1]),
                            n = i !== this.defaultValue ? i : "";
                        this.$set(this.filters, this.filterKey, n), this.$emit("input", n, {
                            filterType: this.$options.name,
                            filterKey: this.filterKey,
                            location: this.location
                        })
                    }
                },
                updateUI: function() {
                    this.locked = !0;
                    var e = this.filters[this.filterKey] ? this.filters[this.filterKey] : this.defaultValue;
                    "single" === this.type ? jQuery(this.$refs.slider).slider("value", e) : jQuery(this.$refs.slider).slider("values", e.split("..")), this.locked = !1
                }
            },
            computed: {
                displayValue: function() {
                    var e = this.filters[this.filterKey] ? this.filters[this.filterKey] : this.defaultValue;
                    if ("single" === this.type) {
                        e = !isNaN(parseFloat(e)) && this.formatValue ? parseFloat(e).toLocaleString() : e;
                        return "".concat(this.prefix).concat(e).concat(this.suffix)
                    }
                    var t = e.split(".."),
                        i = !isNaN(parseFloat(t[0])) && this.formatValue ? parseFloat(t[0]).toLocaleString() : t[0],
                        n = !isNaN(parseFloat(t[1])) && this.formatValue ? parseFloat(t[1]).toLocaleString() : t[1];
                    return jQuery("body").hasClass("rtl") ? "".concat(this.prefix).concat(n).concat(this.suffix, " â€” ").concat(this.prefix).concat(i).concat(this.suffix) : "".concat(this.prefix).concat(i).concat(this.suffix, " â€” ").concat(this.prefix).concat(n).concat(this.suffix)
                },
                targetFilter: function() {
                    return this.filters[this.filterKey]
                },
                filters: function() {
                    return this.$root.types[this.listingType].filters
                }
            },
            watch: {
                targetFilter: function() {
                    this.debounceUIUpdate()
                }
            }
        }), Vue.component("dropdown-filter", {
            props: {
                listingType: String,
                filterKey: String,
                location: String,
                label: String,
                multiple: Boolean,
                choices: Array
            },
            data: function() {
                return {
                    selected: this.multiple ? [] : ""
                }
            },
            created: function() {
                this.selected = this.multiple ? this.filters[this.filterKey].split(",") : this.filters[this.filterKey]
            },
            mounted: function() {
                var e = this;
                this.$nextTick(function() {
                    new MyListing.CustomSelect(e.$refs.select), e.$root.$on("reset-filters:" + e.listingType, function() {
                        e.selected = e.multiple ? [] : "", e.filters[e.filterKey] = "", jQuery(e.$refs.select).val(e.selected).trigger("change").trigger("select2:close")
                    })
                })
            },
            methods: {
                handleChange: function(e) {
                    this.selected = this.multiple ? Array.isArray(e.detail.value) ? e.detail.value : [] : "string" == typeof e.detail.value ? e.detail.value : "", this.updateInput()
                },
                updateInput: function() {
                    var e = this.multiple ? this.selected.filter(Boolean).join(",") : this.selected;
                    this.filters[this.filterKey] = e, this.$emit("input", e, {
                        filterType: this.$options.name,
                        filterKey: this.filterKey,
                        location: this.location
                    })
                },
                isSelected: function(e) {
                    return this.multiple ? this.selected.includes(e) : e === this.selected
                }
            },
            computed: {
                filters: function() {
                    return this.$root.types[this.listingType].filters
                }
            }
        }), Vue.component("dropdown-terms-filter", {
            props: {
                listingType: String,
                filterKey: String,
                location: String,
                ajaxParams: String,
                label: String,
                preSelected: Array
            },
            data: function() {
                return {
                    selected: []
                }
            },
            created: function() {
                this.selected = this.filters[this.filterKey].split(",")
            },
            mounted: function() {
                var e = this;
                this.$nextTick(function() {
                    new MyListing.CustomSelect(e.$refs.select), e.$root.$on("reset-filters:" + e.listingType, function() {
                        e.selected = e.filters[e.filterKey] = [], jQuery(e.$refs.select).val([]).trigger("change").trigger("select2:close")
                    })
                })
            },
            methods: {
                handleChange: function(e) {
                    this.selected = Array.isArray(e.detail.value) ? e.detail.value : [], this.updateInput()
                },
                updateInput: function() {
                    var e = this.selected.join(",");
                    this.filters[this.filterKey] = e, this.$emit("input", e, {
                        filterType: this.$options.name,
                        filterKey: this.filterKey,
                        location: this.location
                    })
                }
            },
            computed: {
                filters: function() {
                    return this.$root.types[this.listingType].filters
                }
            }
        }), Vue.component("dropdown-hierarchy-filter", {
            props: {
                listingType: String,
                filterKey: String,
                location: String,
                label: String,
                ajaxParams: String,
                preSelected: String
            },
            data: function() {
                return {
                    selected: ""
                }
            },
            created: function() {
                this.selected = this.filters[this.filterKey]
            },
            mounted: function() {
                var e = this;
                this.$nextTick(function() {
                    new MyListing.TermHierarchy(e.$refs.input), e.$root.$on("reset-filters:" + e.listingType, function() {
                        e.selected = e.filters[e.filterKey] = "", jQuery(e.$el).find(".term-select-0 select").val("").trigger("change").trigger("select2:close")
                    })
                })
            },
            methods: {
                handleChange: function(e) {
                    this.selected = "string" == typeof e.detail.value ? e.detail.value : "", this.updateInput()
                },
                updateInput: function() {
                    this.filters[this.filterKey] = this.selected, this.$emit("input", this.selected, {
                        filterType: this.$options.name,
                        filterKey: this.filterKey,
                        location: this.location
                    })
                }
            },
            computed: {
                filters: function() {
                    return this.$root.types[this.listingType].filters
                }
            }
        }), Vue.component("checkboxes-filter", {
            props: {
                listingType: String,
                filterKey: String,
                location: String,
                label: String,
                multiple: Boolean,
                choices: Array
            },
            data: function() {
                return {
                    selected: this.multiple ? [] : ""
                }
            },
            created: function() {
                this.selected = this.multiple ? this.filters[this.filterKey].split(",") : this.filters[this.filterKey]
            },
            mounted: function() {
                var e = this;
                this.$nextTick(function() {
                    e.$refs.select && new MyListing.CustomSelect(e.$refs.select), e.$root.$on("reset-filters:" + e.listingType, function() {
                        e.selected = e.multiple ? [] : "", e.filters[e.filterKey] = ""
                    })
                })
            },
            methods: {
                updateInput: function() {
                    var e = this.multiple ? this.selected.filter(Boolean).join(",") : this.selected;
                    this.filters[this.filterKey] = e, this.$emit("input", e, {
                        filterType: this.$options.name,
                        filterKey: this.filterKey,
                        location: this.location,
                        shouldDebounce: !this.$refs.workHourRanges
                    })
                }
            },
            computed: {
                filters: function() {
                    return this.$root.types[this.listingType].filters
                },
                filterId: function() {
                    return "fid:".concat(this.listingType, "-").concat(this.filterKey, "-").concat(this._uid)
                }
            }
        }), Vue.component("related-listing-filter", {
            props: {
                listingType: String,
                filterKey: String,
                location: String,
                ajaxParams: String,
                label: String,
                preSelected: Array,
                multiple: Boolean
            },
            data: function() {
                return {
                    selected: this.multiple ? [] : ""
                }
            },
            created: function() {
                this.selected = this.multiple ? this.filters[this.filterKey].split(",") : this.filters[this.filterKey]
            },
            mounted: function() {
                var e = this;
                this.$nextTick(function() {
                    e.$refs.select.dataset.mylistingAjax = !0, e.$refs.select.dataset.mylistingAjaxUrl = "mylisting_list_posts", e.$refs.select.dataset.mylistingAjaxParams = e.ajaxParams, new MyListing.CustomSelect(e.$refs.select), e.$root.$on("reset-filters:" + e.listingType, function() {
                        e.selected = e.filters[e.filterKey] = e.multiple ? [] : "", jQuery(e.$refs.select).val(e.selected).trigger("change").trigger("select2:close")
                    })
                })
            },
            methods: {
                handleChange: function(e) {
                    this.selected = this.multiple ? Array.isArray(e.detail.value) ? e.detail.value : [] : "string" == typeof e.detail.value ? e.detail.value : "", this.updateInput()
                },
                updateInput: function() {
                    var e = this.multiple ? this.selected.join(",") : this.selected;
                    this.filters[this.filterKey] = e, this.$emit("input", e, {
                        filterType: this.$options.name,
                        filterKey: this.filterKey,
                        location: this.location
                    })
                }
            },
            computed: {
                filters: function() {
                    return this.$root.types[this.listingType].filters
                }
            }
        }), Vue.component("order-filter", {
            props: {
                listingType: String,
                filterKey: String,
                choices: Array,
                location: String,
                label: String
            },
            data: function() {
                return {
                    locked: !1
                }
            },
            mounted: function() {
                var i = this;
                this.$nextTick(function() {
                    new MyListing.CustomSelect(i.$refs.select);
                    var t = i.filters[i.filterKey];
                    t && i.choices.find(function(e) {
                        return e.key === t
                    }) || !i.choices.length || (i.filters[i.filterKey] = i.choices[0].key, i.updateUI()), i.$root.$on("reset-filters:" + i.listingType, function() {
                        var e = i.choices.length ? i.choices[0].key : null;
                        i.filters[i.filterKey] = e, i.updateUI()
                    })
                })
            },
            methods: {
                updateInput: function() {
                    this.locked || (this.filters[this.filterKey] = this.$refs.select.value, this.hasNote(this.filters[this.filterKey], "has-proximity-clause") ? this.$root.$emit("request-location:" + this.listingType) : this.$emit("input", this.$refs.select.value, {
                        filterType: this.$options.name,
                        filterKey: this.filterKey,
                        location: this.location,
                        shouldDebounce: !1
                    }))
                },
                updateUI: function() {
                    this.locked = !0, jQuery(this.$refs.select).val(this.filters[this.filterKey]).trigger("change").trigger("select2:close"), this.locked = !1
                },
                hasNote: function(t, e) {
                    return !(!(t = this.choices.find(function(e) {
                        return e.key === t
                    })) || !t.notes) && -1 !== t.notes.indexOf(e)
                }
            },
            computed: {
                wrapperClasses: function() {
                    var e = this.currentChoice;
                    return e && e.notes ? e.notes : []
                },
                currentChoice: function() {
                    var t = this;
                    return this.choices.find(function(e) {
                        return e.key === t.filters.sort
                    })
                },
                locationDetails: function() {
                    return this.$root.hasValidLocation(this.listingType) ? this.filters.search_location : CASE27.l10n.nearby_listings_location_required
                },
                filters: function() {
                    return this.$root.types[this.listingType].filters
                }
            },
            watch: {
                "filters.sort": function(e) {
                    this.updateUI()
                }
            }
        });

    function r() {
        jQuery(".mylisting-basic-form").each(function(e, t) {
            if (!t.dataset.inited) {
                t.dataset.inited = !0;
                var i = JSON.parse(t.dataset.listingTypes),
                    n = JSON.parse(t.dataset.config);
                new Vue({
                    el: t,
                    data: {
                        activeType: !1,
                        types: i,
                        targetURL: n.target_url,
                        tabMode: n.tabs_mode,
                        typesDisplay: n.types_display,
                        boxShadow: n.box_shadow,
                        formId: n.form_id
                    },
                    created: function() {
                        var e = Object.keys(this.types);
                        e.length && (this.activeType = this.types[e[0]])
                    },
                    methods: {
                        typeDropdownChanged: function(e) {
                            this.activeType !== this.types[e] && (this.activeType = this.types[e], jQuery(this.$refs["types-dropdown-".concat(this.activeType.id)]).val(e).trigger("change").trigger("select2:close"))
                        },
                        filterChanged: function(e, t) {},
                        hasValidLocation: function(e) {},
                        submit: function() {
                            var i = this.activeType.filters,
                                n = {
                                    type: this.activeType.slug,
                                    tab: "search-form"
                                };
                            Object.keys(i).forEach(function(e) {
                                var t = i[e];
                                ("proximity" !== e || i.lat && i.lng) && (t && void 0 !== t.length && t.length ? n[e] = t : "number" == typeof t && t && (n[e] = t))
                            });
                            var e = jQuery.param(n).replace(/%2C/g, ",");
                            window.location.href = "".concat(this.targetURL, "?").concat(e)
                        }
                    }
                })
            }
        })
    }
    r(), document.addEventListener("DOMContentLoaded", r), document.addEventListener("mylisting:refresh-basic-forms", r)
});