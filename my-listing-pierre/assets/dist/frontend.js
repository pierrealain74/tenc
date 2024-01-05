(function (factory) {
  typeof define === 'function' && define.amd ? define('frontend', factory) :
  factory();
}((function () { 'use strict';

  function _typeof(obj) {
    "@babel/helpers - typeof";

    return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) {
      return typeof obj;
    } : function (obj) {
      return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    }, _typeof(obj);
  }

  /**
  * @version: 3.0.3
  * @author: Dan Grossman http://www.dangrossman.info/
  * @copyright: Copyright (c) 2012-2018 Dan Grossman. All rights reserved.
  * @license: Licensed under the MIT license. See http://www.opensource.org/licenses/mit-license.php
  * @website: http://www.daterangepicker.com/
  */
  // Following the UMD template https://github.com/umdjs/umd/blob/master/templates/returnExportsGlobal.js
  (function (root, factory) {
    if (typeof define === 'function' && define.amd) {
      // AMD. Make globaly available as well
      define(['moment', 'jquery'], function (moment, jquery) {
        if (!jquery.fn) jquery.fn = {}; // webpack server rendering

        return factory(moment, jquery);
      });
    } else if ((typeof module === "undefined" ? "undefined" : _typeof(module)) === 'object' && module.exports) {
      // Node / Browserify
      //isomorphic issue
      var jQuery = typeof window != 'undefined' ? window.jQuery : undefined;

      if (!jQuery) {
        jQuery = require('jquery');
        if (!jQuery.fn) jQuery.fn = {};
      }

      var moment = typeof window != 'undefined' && typeof window.moment != 'undefined' ? window.moment : require('moment');
      module.exports = factory(moment, jQuery);
    } else {
      // Browser globals
      root.daterangepicker = factory(root.moment, root.jQuery);
    }
  })(window, function (moment, $) {
    var DateRangePicker = function DateRangePicker(element, options, cb) {
      //default settings for options
      this.parentEl = 'body';
      this.element = $(element);
      this.startDate = moment().startOf('day');
      this.endDate = moment().endOf('day');
      this.minDate = false;
      this.maxDate = false;
      this.maxSpan = false;
      this.autoApply = false;
      this.singleDatePicker = false;
      this.showDropdowns = false;
      this.minYear = moment().subtract(100, 'year').locale('en').format('YYYY');
      this.maxYear = moment().add(100, 'year').locale('en').format('YYYY');
      this.showWeekNumbers = false;
      this.showISOWeekNumbers = false;
      this.showCustomRangeLabel = true;
      this.timePicker = false;
      this.timePicker24Hour = false;
      this.timePickerIncrement = 1;
      this.timePickerSeconds = false;
      this.linkedCalendars = true;
      this.autoUpdateInput = true;
      this.alwaysShowCalendars = false;
      this.ranges = {};
      this.opens = 'right';
      if (this.element.hasClass('pull-right')) this.opens = 'left';
      this.drops = 'down';
      if (this.element.hasClass('dropup')) this.drops = 'up';
      this.buttonClasses = 'btn btn-sm';
      this.applyButtonClasses = 'btn-primary';
      this.cancelButtonClasses = 'btn-default';
      this.locale = {
        direction: 'ltr',
        format: moment.localeData().longDateFormat('L'),
        separator: ' - ',
        applyLabel: 'Apply',
        cancelLabel: 'Cancel',
        weekLabel: 'W',
        customRangeLabel: 'Custom Range',
        daysOfWeek: moment.weekdaysMin(),
        monthNames: moment.monthsShort(),
        firstDay: moment.localeData().firstDayOfWeek()
      };

      this.callback = function () {}; //some state information


      this.isShowing = false;
      this.leftCalendar = {};
      this.rightCalendar = {}; //custom options from user

      if (_typeof(options) !== 'object' || options === null) options = {}; //allow setting options with data attributes
      //data-api options will be overwritten with custom javascript options

      options = $.extend(this.element.data(), options); //html template for the picker UI

      if (typeof options.template !== 'string' && !(options.template instanceof $)) options.template = '<div class="daterangepicker">' + '<div class="ranges"></div>' + '<div class="drp-calendar left">' + '<div class="calendar-table"></div>' + '<div class="calendar-time"></div>' + '</div>' + '<div class="drp-calendar right">' + '<div class="calendar-table"></div>' + '<div class="calendar-time"></div>' + '</div>' + '<div class="drp-buttons">' + '<span class="drp-selected"></span>' + '<button class="cancelBtn" type="button"></button>' + '<button class="applyBtn" disabled="disabled" type="button"></button> ' + '</div>' + '</div>';
      this.parentEl = options.parentEl && $(options.parentEl).length ? $(options.parentEl) : $(this.parentEl);
      this.container = $(options.template).appendTo(this.parentEl); //
      // handle all the possible options overriding defaults
      //

      if (_typeof(options.locale) === 'object') {
        if (typeof options.locale.direction === 'string') this.locale.direction = options.locale.direction;
        if (typeof options.locale.format === 'string') this.locale.format = options.locale.format;
        if (typeof options.locale.separator === 'string') this.locale.separator = options.locale.separator;
        if (_typeof(options.locale.daysOfWeek) === 'object') this.locale.daysOfWeek = options.locale.daysOfWeek.slice();
        if (_typeof(options.locale.monthNames) === 'object') this.locale.monthNames = options.locale.monthNames.slice();
        if (typeof options.locale.firstDay === 'number') this.locale.firstDay = options.locale.firstDay;
        if (typeof options.locale.applyLabel === 'string') this.locale.applyLabel = options.locale.applyLabel;
        if (typeof options.locale.cancelLabel === 'string') this.locale.cancelLabel = options.locale.cancelLabel;
        if (typeof options.locale.weekLabel === 'string') this.locale.weekLabel = options.locale.weekLabel;

        if (typeof options.locale.customRangeLabel === 'string') {
          //Support unicode chars in the custom range name.
          var elem = document.createElement('textarea');
          elem.innerHTML = options.locale.customRangeLabel;
          var rangeHtml = elem.value;
          this.locale.customRangeLabel = rangeHtml;
        }
      }

      this.container.addClass(this.locale.direction);
      if (typeof options.startDate === 'string') this.startDate = moment(options.startDate, this.locale.format);
      if (typeof options.endDate === 'string') this.endDate = moment(options.endDate, this.locale.format);
      if (typeof options.minDate === 'string') this.minDate = moment(options.minDate, this.locale.format);
      if (typeof options.maxDate === 'string') this.maxDate = moment(options.maxDate, this.locale.format);
      if (_typeof(options.startDate) === 'object') this.startDate = moment(options.startDate);
      if (_typeof(options.endDate) === 'object') this.endDate = moment(options.endDate);
      if (_typeof(options.minDate) === 'object') this.minDate = moment(options.minDate);
      if (_typeof(options.maxDate) === 'object') this.maxDate = moment(options.maxDate); // sanity check for bad options

      if (this.minDate && this.startDate.isBefore(this.minDate)) this.startDate = this.minDate.clone(); // sanity check for bad options

      if (this.maxDate && this.endDate.isAfter(this.maxDate)) this.endDate = this.maxDate.clone();
      if (typeof options.applyButtonClasses === 'string') this.applyButtonClasses = options.applyButtonClasses;
      if (typeof options.applyClass === 'string') //backwards compat
        this.applyButtonClasses = options.applyClass;
      if (typeof options.cancelButtonClasses === 'string') this.cancelButtonClasses = options.cancelButtonClasses;
      if (typeof options.cancelClass === 'string') //backwards compat
        this.cancelButtonClasses = options.cancelClass;
      if (_typeof(options.maxSpan) === 'object') this.maxSpan = options.maxSpan;
      if (_typeof(options.dateLimit) === 'object') //backwards compat
        this.maxSpan = options.dateLimit;
      if (typeof options.opens === 'string') this.opens = options.opens;
      if (typeof options.drops === 'string') this.drops = options.drops;
      if (typeof options.showWeekNumbers === 'boolean') this.showWeekNumbers = options.showWeekNumbers;
      if (typeof options.showISOWeekNumbers === 'boolean') this.showISOWeekNumbers = options.showISOWeekNumbers;
      if (typeof options.buttonClasses === 'string') this.buttonClasses = options.buttonClasses;
      if (_typeof(options.buttonClasses) === 'object') this.buttonClasses = options.buttonClasses.join(' ');
      if (typeof options.showDropdowns === 'boolean') this.showDropdowns = options.showDropdowns;
      if (typeof options.minYear === 'number') this.minYear = options.minYear;
      if (typeof options.maxYear === 'number') this.maxYear = options.maxYear;
      if (typeof options.showCustomRangeLabel === 'boolean') this.showCustomRangeLabel = options.showCustomRangeLabel;

      if (typeof options.singleDatePicker === 'boolean') {
        this.singleDatePicker = options.singleDatePicker;
        if (this.singleDatePicker) this.endDate = this.startDate.clone();
      }

      if (typeof options.timePicker === 'boolean') this.timePicker = options.timePicker;
      if (typeof options.timePickerSeconds === 'boolean') this.timePickerSeconds = options.timePickerSeconds;
      if (typeof options.timePickerIncrement === 'number') this.timePickerIncrement = options.timePickerIncrement;
      if (typeof options.timePicker24Hour === 'boolean') this.timePicker24Hour = options.timePicker24Hour;
      if (typeof options.autoApply === 'boolean') this.autoApply = options.autoApply;
      if (typeof options.autoUpdateInput === 'boolean') this.autoUpdateInput = options.autoUpdateInput;
      if (typeof options.linkedCalendars === 'boolean') this.linkedCalendars = options.linkedCalendars;
      if (typeof options.isInvalidDate === 'function') this.isInvalidDate = options.isInvalidDate;
      if (typeof options.isCustomDate === 'function') this.isCustomDate = options.isCustomDate;
      if (typeof options.alwaysShowCalendars === 'boolean') this.alwaysShowCalendars = options.alwaysShowCalendars; // update day names order to firstDay

      if (this.locale.firstDay != 0) {
        var iterator = this.locale.firstDay;

        while (iterator > 0) {
          this.locale.daysOfWeek.push(this.locale.daysOfWeek.shift());
          iterator--;
        }
      }

      var start, end, range; //if no start/end dates set, check if an input element contains initial values

      if (typeof options.startDate === 'undefined' && typeof options.endDate === 'undefined') {
        if ($(this.element).is(':text')) {
          var val = $(this.element).val(),
              split = val.split(this.locale.separator);
          start = end = null;

          if (split.length == 2) {
            start = moment(split[0], this.locale.format);
            end = moment(split[1], this.locale.format);
          } else if (this.singleDatePicker && val !== "") {
            start = moment(val, this.locale.format);
            end = moment(val, this.locale.format);
          }

          if (start !== null && end !== null) {
            this.setStartDate(start);
            this.setEndDate(end);
          }
        }
      }

      if (_typeof(options.ranges) === 'object') {
        for (range in options.ranges) {
          if (typeof options.ranges[range][0] === 'string') start = moment(options.ranges[range][0], this.locale.format);else start = moment(options.ranges[range][0]);
          if (typeof options.ranges[range][1] === 'string') end = moment(options.ranges[range][1], this.locale.format);else end = moment(options.ranges[range][1]); // If the start or end date exceed those allowed by the minDate or maxSpan
          // options, shorten the range to the allowable period.

          if (this.minDate && start.isBefore(this.minDate)) start = this.minDate.clone();
          var maxDate = this.maxDate;
          if (this.maxSpan && maxDate && start.clone().add(this.maxSpan).isAfter(maxDate)) maxDate = start.clone().add(this.maxSpan);
          if (maxDate && end.isAfter(maxDate)) end = maxDate.clone(); // If the end of the range is before the minimum or the start of the range is
          // after the maximum, don't display this range option at all.

          if (this.minDate && end.isBefore(this.minDate, this.timepicker ? 'minute' : 'day') || maxDate && start.isAfter(maxDate, this.timepicker ? 'minute' : 'day')) continue; //Support unicode chars in the range names.

          var elem = document.createElement('textarea');
          elem.innerHTML = range;
          var rangeHtml = elem.value;
          this.ranges[rangeHtml] = [start, end];
        }

        var list = '<ul>';

        for (range in this.ranges) {
          list += '<li data-range-key="' + range + '">' + range + '</li>';
        }

        if (this.showCustomRangeLabel) {
          list += '<li data-range-key="' + this.locale.customRangeLabel + '">' + this.locale.customRangeLabel + '</li>';
        }

        list += '</ul>';
        this.container.find('.ranges').prepend(list);
      }

      if (typeof cb === 'function') {
        this.callback = cb;
      }

      if (!this.timePicker) {
        this.startDate = this.startDate.startOf('day');
        this.endDate = this.endDate.endOf('day');
        this.container.find('.calendar-time').hide();
      } //can't be used together for now


      if (this.timePicker && this.autoApply) this.autoApply = false;

      if (this.autoApply) {
        this.container.addClass('auto-apply');
      }

      if (_typeof(options.ranges) === 'object') this.container.addClass('show-ranges');

      if (this.singleDatePicker) {
        this.container.addClass('single');
        this.container.find('.drp-calendar.left').addClass('single');
        this.container.find('.drp-calendar.left').show();
        this.container.find('.drp-calendar.right').hide();

        if (!this.timePicker) {
          this.container.addClass('auto-apply');
        }
      }

      if (typeof options.ranges === 'undefined' && !this.singleDatePicker || this.alwaysShowCalendars) {
        this.container.addClass('show-calendar');
      }

      this.container.addClass('opens' + this.opens); //apply CSS classes and labels to buttons

      this.container.find('.applyBtn, .cancelBtn').addClass(this.buttonClasses);
      if (this.applyButtonClasses.length) this.container.find('.applyBtn').addClass(this.applyButtonClasses);
      if (this.cancelButtonClasses.length) this.container.find('.cancelBtn').addClass(this.cancelButtonClasses);
      this.container.find('.applyBtn').html(this.locale.applyLabel);
      this.container.find('.cancelBtn').html(this.locale.cancelLabel); //
      // event listeners
      //

      this.container.find('.drp-calendar').on('click.daterangepicker', '.prev', $.proxy(this.clickPrev, this)).on('click.daterangepicker', '.next', $.proxy(this.clickNext, this)).on('mousedown.daterangepicker', 'td.available', $.proxy(this.clickDate, this)).on('mouseenter.daterangepicker', 'td.available', $.proxy(this.hoverDate, this)).on('change.daterangepicker', 'select.yearselect', $.proxy(this.monthOrYearChanged, this)).on('change.daterangepicker', 'select.monthselect', $.proxy(this.monthOrYearChanged, this)).on('change.daterangepicker', 'select.hourselect,select.minuteselect,select.secondselect,select.ampmselect', $.proxy(this.timeChanged, this));
      this.container.find('.ranges').on('click.daterangepicker', 'li', $.proxy(this.clickRange, this));
      this.container.find('.drp-buttons').on('click.daterangepicker', 'button.applyBtn', $.proxy(this.clickApply, this)).on('click.daterangepicker', 'button.cancelBtn', $.proxy(this.clickCancel, this));

      if (this.element.is('input') || this.element.is('button')) {
        this.element.on({
          'click.daterangepicker': $.proxy(this.show, this),
          'focus.daterangepicker': $.proxy(this.show, this),
          'keyup.daterangepicker': $.proxy(this.elementChanged, this),
          'keydown.daterangepicker': $.proxy(this.keydown, this) //IE 11 compatibility

        });
      } else {
        this.element.on('click.daterangepicker', $.proxy(this.toggle, this));
        this.element.on('keydown.daterangepicker', $.proxy(this.toggle, this));
      } //
      // if attached to a text input, set the initial value
      //


      this.updateElement();
    };

    DateRangePicker.prototype = {
      constructor: DateRangePicker,
      setStartDate: function setStartDate(startDate) {
        if (typeof startDate === 'string') this.startDate = moment(startDate, this.locale.format);
        if (_typeof(startDate) === 'object') this.startDate = moment(startDate);
        if (!this.timePicker) this.startDate = this.startDate.startOf('day');
        if (this.timePicker && this.timePickerIncrement) this.startDate.minute(Math.round(this.startDate.minute() / this.timePickerIncrement) * this.timePickerIncrement);

        if (this.minDate && this.startDate.isBefore(this.minDate)) {
          this.startDate = this.minDate.clone();
          if (this.timePicker && this.timePickerIncrement) this.startDate.minute(Math.round(this.startDate.minute() / this.timePickerIncrement) * this.timePickerIncrement);
        }

        if (this.maxDate && this.startDate.isAfter(this.maxDate)) {
          this.startDate = this.maxDate.clone();
          if (this.timePicker && this.timePickerIncrement) this.startDate.minute(Math.floor(this.startDate.minute() / this.timePickerIncrement) * this.timePickerIncrement);
        }

        if (!this.isShowing) this.updateElement();
        this.updateMonthsInView();
      },
      setEndDate: function setEndDate(endDate) {
        if (typeof endDate === 'string') this.endDate = moment(endDate, this.locale.format);
        if (_typeof(endDate) === 'object') this.endDate = moment(endDate);
        if (!this.timePicker) this.endDate = this.endDate.add(1, 'd').startOf('day').subtract(1, 'second');
        if (this.timePicker && this.timePickerIncrement) this.endDate.minute(Math.round(this.endDate.minute() / this.timePickerIncrement) * this.timePickerIncrement);
        if (this.endDate.isBefore(this.startDate)) this.endDate = this.startDate.clone();
        if (this.maxDate && this.endDate.isAfter(this.maxDate)) this.endDate = this.maxDate.clone();
        if (this.maxSpan && this.startDate.clone().add(this.maxSpan).isBefore(this.endDate)) this.endDate = this.startDate.clone().add(this.maxSpan);
        this.previousRightTime = this.endDate.clone();
        this.container.find('.drp-selected').html(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format));
        if (!this.isShowing) this.updateElement();
        this.updateMonthsInView();
      },
      isInvalidDate: function isInvalidDate() {
        return false;
      },
      isCustomDate: function isCustomDate() {
        return false;
      },
      updateView: function updateView() {
        if (this.timePicker) {
          this.renderTimePicker('left');
          this.renderTimePicker('right');

          if (!this.endDate) {
            this.container.find('.right .calendar-time select').attr('disabled', 'disabled').addClass('disabled');
          } else {
            this.container.find('.right .calendar-time select').removeAttr('disabled').removeClass('disabled');
          }
        }

        if (this.endDate) this.container.find('.drp-selected').html(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format));
        this.updateMonthsInView();
        this.updateCalendars();
        this.updateFormInputs();
      },
      updateMonthsInView: function updateMonthsInView() {
        if (this.endDate) {
          //if both dates are visible already, do nothing
          if (!this.singleDatePicker && this.leftCalendar.month && this.rightCalendar.month && (this.startDate.format('YYYY-MM') == this.leftCalendar.month.format('YYYY-MM') || this.startDate.format('YYYY-MM') == this.rightCalendar.month.format('YYYY-MM')) && (this.endDate.format('YYYY-MM') == this.leftCalendar.month.format('YYYY-MM') || this.endDate.format('YYYY-MM') == this.rightCalendar.month.format('YYYY-MM'))) {
            return;
          }

          this.leftCalendar.month = this.startDate.clone().date(2);

          if (!this.linkedCalendars && (this.endDate.month() != this.startDate.month() || this.endDate.year() != this.startDate.year())) {
            this.rightCalendar.month = this.endDate.clone().date(2);
          } else {
            this.rightCalendar.month = this.startDate.clone().date(2).add(1, 'month');
          }
        } else {
          if (this.leftCalendar.month.format('YYYY-MM') != this.startDate.format('YYYY-MM') && this.rightCalendar.month.format('YYYY-MM') != this.startDate.format('YYYY-MM')) {
            this.leftCalendar.month = this.startDate.clone().date(2);
            this.rightCalendar.month = this.startDate.clone().date(2).add(1, 'month');
          }
        }

        if (this.maxDate && this.linkedCalendars && !this.singleDatePicker && this.rightCalendar.month > this.maxDate) {
          this.rightCalendar.month = this.maxDate.clone().date(2);
          this.leftCalendar.month = this.maxDate.clone().date(2).subtract(1, 'month');
        }
      },
      updateCalendars: function updateCalendars() {
        if (this.timePicker) {
          var hour, minute, second;

          if (this.endDate) {
            hour = parseInt(this.container.find('.left .hourselect').val(), 10);
            minute = parseInt(this.container.find('.left .minuteselect').val(), 10);
            second = this.timePickerSeconds ? parseInt(this.container.find('.left .secondselect').val(), 10) : 0;

            if (!this.timePicker24Hour) {
              var ampm = this.container.find('.left .ampmselect').val();
              if (ampm === 'PM' && hour < 12) hour += 12;
              if (ampm === 'AM' && hour === 12) hour = 0;
            }
          } else {
            hour = parseInt(this.container.find('.right .hourselect').val(), 10);
            minute = parseInt(this.container.find('.right .minuteselect').val(), 10);
            second = this.timePickerSeconds ? parseInt(this.container.find('.right .secondselect').val(), 10) : 0;

            if (!this.timePicker24Hour) {
              var ampm = this.container.find('.right .ampmselect').val();
              if (ampm === 'PM' && hour < 12) hour += 12;
              if (ampm === 'AM' && hour === 12) hour = 0;
            }
          }

          this.leftCalendar.month.hour(hour).minute(minute).second(second);
          this.rightCalendar.month.hour(hour).minute(minute).second(second);
        }

        this.renderCalendar('left');
        this.renderCalendar('right'); //highlight any predefined range matching the current start and end dates

        this.container.find('.ranges li').removeClass('active');
        if (this.endDate == null) return;
        this.calculateChosenLabel();
      },
      renderCalendar: function renderCalendar(side) {
        //
        // Build the matrix of dates that will populate the calendar
        //
        var calendar = side == 'left' ? this.leftCalendar : this.rightCalendar;
        var month = calendar.month.month();
        var year = calendar.month.year();
        var hour = calendar.month.hour();
        var minute = calendar.month.minute();
        var second = calendar.month.second();
        var daysInMonth = moment([year, month]).daysInMonth();
        var firstDay = moment([year, month, 1]);
        var lastDay = moment([year, month, daysInMonth]);
        var lastMonth = moment(firstDay).subtract(1, 'month').month();
        var lastYear = moment(firstDay).subtract(1, 'month').year();
        var daysInLastMonth = moment([lastYear, lastMonth]).daysInMonth();
        var dayOfWeek = firstDay.day(); //initialize a 6 rows x 7 columns array for the calendar

        var calendar = [];
        calendar.firstDay = firstDay;
        calendar.lastDay = lastDay;

        for (var i = 0; i < 6; i++) {
          calendar[i] = [];
        } //populate the calendar with date objects


        var startDay = daysInLastMonth - dayOfWeek + this.locale.firstDay + 1;
        if (startDay > daysInLastMonth) startDay -= 7;
        if (dayOfWeek == this.locale.firstDay) startDay = daysInLastMonth - 6;
        var curDate = moment([lastYear, lastMonth, startDay, 12, minute, second]);
        var col, row;

        for (var i = 0, col = 0, row = 0; i < 42; i++, col++, curDate = moment(curDate).add(24, 'hour')) {
          if (i > 0 && col % 7 === 0) {
            col = 0;
            row++;
          }

          calendar[row][col] = curDate.clone().hour(hour).minute(minute).second(second);
          curDate.hour(12);

          if (this.minDate && calendar[row][col].format('YYYY-MM-DD') == this.minDate.format('YYYY-MM-DD') && calendar[row][col].isBefore(this.minDate) && side == 'left') {
            calendar[row][col] = this.minDate.clone();
          }

          if (this.maxDate && calendar[row][col].format('YYYY-MM-DD') == this.maxDate.format('YYYY-MM-DD') && calendar[row][col].isAfter(this.maxDate) && side == 'right') {
            calendar[row][col] = this.maxDate.clone();
          }
        } //make the calendar object available to hoverDate/clickDate


        if (side == 'left') {
          this.leftCalendar.calendar = calendar;
        } else {
          this.rightCalendar.calendar = calendar;
        } //
        // Display the calendar
        //


        var minDate = side == 'left' ? this.minDate : this.startDate;
        var maxDate = this.maxDate;
        var selected = side == 'left' ? this.startDate : this.endDate;
        var arrow = this.locale.direction == 'ltr' ? {
          left: 'chevron-left',
          right: 'chevron-right'
        } : {
          left: 'chevron-right',
          right: 'chevron-left'
        };
        var html = '<table class="table-condensed">';
        html += '<thead>';
        html += '<tr>'; // add empty cell for week number

        if (this.showWeekNumbers || this.showISOWeekNumbers) html += '<th></th>';

        if ((!minDate || minDate.isBefore(calendar.firstDay)) && (!this.linkedCalendars || side == 'left')) {
          html += '<th class="prev available"><span></span></th>';
        } else {
          html += '<th></th>';
        }

        var dateHtml = this.locale.monthNames[calendar[1][1].month()] + calendar[1][1].format(" YYYY");

        if (this.showDropdowns) {
          var currentMonth = calendar[1][1].month();
          var currentYear = calendar[1][1].year();
          var maxYear = maxDate && maxDate.year() || this.maxYear;
          var minYear = minDate && minDate.year() || this.minYear;
          var inMinYear = currentYear == minYear;
          var inMaxYear = currentYear == maxYear;
          var monthHtml = '<select class="monthselect">';

          for (var m = 0; m < 12; m++) {
            if ((!inMinYear || m >= minDate.month()) && (!inMaxYear || m <= maxDate.month())) {
              monthHtml += "<option value='" + m + "'" + (m === currentMonth ? " selected='selected'" : "") + ">" + this.locale.monthNames[m] + "</option>";
            } else {
              monthHtml += "<option value='" + m + "'" + (m === currentMonth ? " selected='selected'" : "") + " disabled='disabled'>" + this.locale.monthNames[m] + "</option>";
            }
          }

          monthHtml += "</select>";
          var yearHtml = '<select class="yearselect">';

          for (var y = minYear; y <= maxYear; y++) {
            yearHtml += '<option value="' + y + '"' + (y === currentYear ? ' selected="selected"' : '') + '>' + y + '</option>';
          }

          yearHtml += '</select>';
          dateHtml = monthHtml + yearHtml;
        }

        html += '<th colspan="5" class="month">' + dateHtml + '</th>';

        if ((!maxDate || maxDate.isAfter(calendar.lastDay)) && (!this.linkedCalendars || side == 'right' || this.singleDatePicker)) {
          html += '<th class="next available"><span></span></th>';
        } else {
          html += '<th></th>';
        }

        html += '</tr>';
        html += '<tr>'; // add week number label

        if (this.showWeekNumbers || this.showISOWeekNumbers) html += '<th class="week">' + this.locale.weekLabel + '</th>';
        $.each(this.locale.daysOfWeek, function (index, dayOfWeek) {
          html += '<th>' + dayOfWeek + '</th>';
        });
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>'; //adjust maxDate to reflect the maxSpan setting in order to
        //grey out end dates beyond the maxSpan

        if (this.endDate == null && this.maxSpan) {
          var maxLimit = this.startDate.clone().add(this.maxSpan).endOf('day');

          if (!maxDate || maxLimit.isBefore(maxDate)) {
            maxDate = maxLimit;
          }
        }

        for (var row = 0; row < 6; row++) {
          html += '<tr>'; // add week number

          if (this.showWeekNumbers) html += '<td class="week">' + calendar[row][0].week() + '</td>';else if (this.showISOWeekNumbers) html += '<td class="week">' + calendar[row][0].isoWeek() + '</td>';

          for (var col = 0; col < 7; col++) {
            var classes = []; //highlight today's date

            if (calendar[row][col].isSame(new Date(), "day")) classes.push('today'); //highlight weekends

            if (calendar[row][col].isoWeekday() > 5) classes.push('weekend'); //grey out the dates in other months displayed at beginning and end of this calendar

            if (calendar[row][col].month() != calendar[1][1].month()) classes.push('off'); //don't allow selection of dates before the minimum date

            if (this.minDate && calendar[row][col].isBefore(this.minDate, 'day')) classes.push('off', 'disabled'); //don't allow selection of dates after the maximum date

            if (maxDate && calendar[row][col].isAfter(maxDate, 'day')) classes.push('off', 'disabled'); //don't allow selection of date if a custom function decides it's invalid

            if (this.isInvalidDate(calendar[row][col])) classes.push('off', 'disabled'); //highlight the currently selected start date

            if (calendar[row][col].format('YYYY-MM-DD') == this.startDate.format('YYYY-MM-DD')) classes.push('active', 'start-date'); //highlight the currently selected end date

            if (this.endDate != null && calendar[row][col].format('YYYY-MM-DD') == this.endDate.format('YYYY-MM-DD')) classes.push('active', 'end-date'); //highlight dates in-between the selected dates

            if (this.endDate != null && calendar[row][col] > this.startDate && calendar[row][col] < this.endDate) classes.push('in-range'); //apply custom classes for this date

            var isCustom = this.isCustomDate(calendar[row][col]);

            if (isCustom !== false) {
              if (typeof isCustom === 'string') classes.push(isCustom);else Array.prototype.push.apply(classes, isCustom);
            }

            var cname = '',
                disabled = false;

            for (var i = 0; i < classes.length; i++) {
              cname += classes[i] + ' ';
              if (classes[i] == 'disabled') disabled = true;
            }

            if (!disabled) cname += 'available';
            html += '<td class="' + cname.replace(/^\s+|\s+$/g, '') + '" data-title="' + 'r' + row + 'c' + col + '">' + calendar[row][col].date() + '</td>';
          }

          html += '</tr>';
        }

        html += '</tbody>';
        html += '</table>';
        this.container.find('.drp-calendar.' + side + ' .calendar-table').html(html);
      },
      renderTimePicker: function renderTimePicker(side) {
        // Don't bother updating the time picker if it's currently disabled
        // because an end date hasn't been clicked yet
        if (side == 'right' && !this.endDate) return;
        var html,
            selected,
            minDate,
            maxDate = this.maxDate;
        if (this.maxSpan && (!this.maxDate || this.startDate.clone().add(this.maxSpan).isAfter(this.maxDate))) maxDate = this.startDate.clone().add(this.maxSpan);

        if (side == 'left') {
          selected = this.startDate.clone();
          minDate = this.minDate;
        } else if (side == 'right') {
          selected = this.endDate.clone();
          minDate = this.startDate; //Preserve the time already selected

          var timeSelector = this.container.find('.drp-calendar.right .calendar-time');

          if (timeSelector.html() != '') {
            selected.hour(selected.hour() || timeSelector.find('.hourselect option:selected').val());
            selected.minute(selected.minute() || timeSelector.find('.minuteselect option:selected').val());
            selected.second(selected.second() || timeSelector.find('.secondselect option:selected').val());

            if (!this.timePicker24Hour) {
              var ampm = timeSelector.find('.ampmselect option:selected').val();
              if (ampm === 'PM' && selected.hour() < 12) selected.hour(selected.hour() + 12);
              if (ampm === 'AM' && selected.hour() === 12) selected.hour(0);
            }
          }

          if (selected.isBefore(this.startDate)) selected = this.startDate.clone();
          if (maxDate && selected.isAfter(maxDate)) selected = maxDate.clone();
        } //
        // hours
        //


        html = '<select class="hourselect">';
        var start = this.timePicker24Hour ? 0 : 1;
        var end = this.timePicker24Hour ? 23 : 12;

        for (var i = start; i <= end; i++) {
          var i_in_24 = i;
          if (!this.timePicker24Hour) i_in_24 = selected.hour() >= 12 ? i == 12 ? 12 : i + 12 : i == 12 ? 0 : i;
          var time = selected.clone().hour(i_in_24);
          var disabled = false;
          if (minDate && time.minute(59).isBefore(minDate)) disabled = true;
          if (maxDate && time.minute(0).isAfter(maxDate)) disabled = true;

          if (i_in_24 == selected.hour() && !disabled) {
            html += '<option value="' + i + '" selected="selected">' + i + '</option>';
          } else if (disabled) {
            html += '<option value="' + i + '" disabled="disabled" class="disabled">' + i + '</option>';
          } else {
            html += '<option value="' + i + '">' + i + '</option>';
          }
        }

        html += '</select> '; //
        // minutes
        //

        html += ': <select class="minuteselect">';

        for (var i = 0; i < 60; i += this.timePickerIncrement) {
          var padded = i < 10 ? '0' + i : i;
          var time = selected.clone().minute(i);
          var disabled = false;
          if (minDate && time.second(59).isBefore(minDate)) disabled = true;
          if (maxDate && time.second(0).isAfter(maxDate)) disabled = true;

          if (selected.minute() == i && !disabled) {
            html += '<option value="' + i + '" selected="selected">' + padded + '</option>';
          } else if (disabled) {
            html += '<option value="' + i + '" disabled="disabled" class="disabled">' + padded + '</option>';
          } else {
            html += '<option value="' + i + '">' + padded + '</option>';
          }
        }

        html += '</select> '; //
        // seconds
        //

        if (this.timePickerSeconds) {
          html += ': <select class="secondselect">';

          for (var i = 0; i < 60; i++) {
            var padded = i < 10 ? '0' + i : i;
            var time = selected.clone().second(i);
            var disabled = false;
            if (minDate && time.isBefore(minDate)) disabled = true;
            if (maxDate && time.isAfter(maxDate)) disabled = true;

            if (selected.second() == i && !disabled) {
              html += '<option value="' + i + '" selected="selected">' + padded + '</option>';
            } else if (disabled) {
              html += '<option value="' + i + '" disabled="disabled" class="disabled">' + padded + '</option>';
            } else {
              html += '<option value="' + i + '">' + padded + '</option>';
            }
          }

          html += '</select> ';
        } //
        // AM/PM
        //


        if (!this.timePicker24Hour) {
          html += '<select class="ampmselect">';
          var am_html = '';
          var pm_html = '';
          if (minDate && selected.clone().hour(12).minute(0).second(0).isBefore(minDate)) am_html = ' disabled="disabled" class="disabled"';
          if (maxDate && selected.clone().hour(0).minute(0).second(0).isAfter(maxDate)) pm_html = ' disabled="disabled" class="disabled"';

          if (selected.hour() >= 12) {
            html += '<option value="AM"' + am_html + '>AM</option><option value="PM" selected="selected"' + pm_html + '>PM</option>';
          } else {
            html += '<option value="AM" selected="selected"' + am_html + '>AM</option><option value="PM"' + pm_html + '>PM</option>';
          }

          html += '</select>';
        }

        this.container.find('.drp-calendar.' + side + ' .calendar-time').html(html);
      },
      updateFormInputs: function updateFormInputs() {
        if (this.singleDatePicker || this.endDate && (this.startDate.isBefore(this.endDate) || this.startDate.isSame(this.endDate))) {
          this.container.find('button.applyBtn').removeAttr('disabled');
        } else {
          this.container.find('button.applyBtn').attr('disabled', 'disabled');
        }
      },
      move: function move() {
        var parentOffset = {
          top: 0,
          left: 0
        },
            containerTop;
        var parentRightEdge = $(window).width();

        if (!this.parentEl.is('body')) {
          parentOffset = {
            top: this.parentEl.offset().top - this.parentEl.scrollTop(),
            left: this.parentEl.offset().left - this.parentEl.scrollLeft()
          };
          parentRightEdge = this.parentEl[0].clientWidth + this.parentEl.offset().left;
        }

        if (this.drops == 'up') containerTop = this.element.offset().top - this.container.outerHeight() - parentOffset.top;else containerTop = this.element.offset().top + this.element.outerHeight() - parentOffset.top;
        this.container[this.drops == 'up' ? 'addClass' : 'removeClass']('drop-up');

        if (this.opens == 'left') {
          this.container.css({
            top: containerTop,
            right: parentRightEdge - this.element.offset().left - this.element.outerWidth(),
            left: 'auto'
          });

          if (this.container.offset().left < 0) {
            this.container.css({
              right: 'auto',
              left: 9
            });
          }
        } else if (this.opens == 'center') {
          this.container.css({
            top: containerTop,
            left: this.element.offset().left - parentOffset.left + this.element.outerWidth() / 2 - this.container.outerWidth() / 2,
            right: 'auto'
          });

          if (this.container.offset().left < 0) {
            this.container.css({
              right: 'auto',
              left: 9
            });
          }
        } else {
          this.container.css({
            top: containerTop,
            left: this.element.offset().left - parentOffset.left,
            right: 'auto'
          });

          if (this.container.offset().left + this.container.outerWidth() > $(window).width()) {
            this.container.css({
              left: 'auto',
              right: 0
            });
          }
        }
      },
      show: function show(e) {
        if (this.isShowing) return; // Create a click proxy that is private to this instance of datepicker, for unbinding

        this._outsideClickProxy = $.proxy(function (e) {
          this.outsideClick(e);
        }, this); // Bind global datepicker mousedown for hiding and

        $(document).on('mousedown.daterangepicker', this._outsideClickProxy) // also support mobile devices
        .on('touchend.daterangepicker', this._outsideClickProxy) // also explicitly play nice with Bootstrap dropdowns, which stopPropagation when clicking them
        .on('click.daterangepicker', '[data-toggle=dropdown]', this._outsideClickProxy) // and also close when focus changes to outside the picker (eg. tabbing between controls)
        .on('focusin.daterangepicker', this._outsideClickProxy); // Reposition the picker if the window is resized while it's open

        $(window).on('resize.daterangepicker', $.proxy(function (e) {
          this.move(e);
        }, this));
        this.oldStartDate = this.startDate.clone();
        this.oldEndDate = this.endDate.clone();
        this.previousRightTime = this.endDate.clone();
        this.updateView();
        this.container.show();
        this.move();
        this.element.trigger('show.daterangepicker', this);
        this.isShowing = true;
      },
      hide: function hide(e) {
        if (!this.isShowing) return; //incomplete date selection, revert to last values

        if (!this.endDate) {
          this.startDate = this.oldStartDate.clone();
          this.endDate = this.oldEndDate.clone();
        } //if a new date range was selected, invoke the user callback function


        if (!this.startDate.isSame(this.oldStartDate) || !this.endDate.isSame(this.oldEndDate)) this.callback(this.startDate.clone(), this.endDate.clone(), this.chosenLabel); //if picker is attached to a text input, update it

        this.updateElement();
        $(document).off('.daterangepicker');
        $(window).off('.daterangepicker');
        this.container.hide();
        this.element.trigger('hide.daterangepicker', this);
        this.isShowing = false;
      },
      toggle: function toggle(e) {
        if (this.isShowing) {
          this.hide();
        } else {
          this.show();
        }
      },
      outsideClick: function outsideClick(e) {
        var target = $(e.target); // if the page is clicked anywhere except within the daterangerpicker/button
        // itself then call this.hide()

        if ( // ie modal dialog fix
        e.type == "focusin" || target.closest(this.element).length || target.closest(this.container).length || target.closest('.calendar-table').length) return;
        this.hide();
        this.element.trigger('outsideClick.daterangepicker', this);
      },
      showCalendars: function showCalendars() {
        this.container.addClass('show-calendar');
        this.move();
        this.element.trigger('showCalendar.daterangepicker', this);
      },
      hideCalendars: function hideCalendars() {
        this.container.removeClass('show-calendar');
        this.element.trigger('hideCalendar.daterangepicker', this);
      },
      clickRange: function clickRange(e) {
        var label = e.target.getAttribute('data-range-key');
        this.chosenLabel = label;

        if (label == this.locale.customRangeLabel) {
          this.showCalendars();
        } else {
          var dates = this.ranges[label];
          this.startDate = dates[0];
          this.endDate = dates[1];

          if (!this.timePicker) {
            this.startDate.startOf('day');
            this.endDate.endOf('day');
          }

          if (!this.alwaysShowCalendars) this.hideCalendars();
          this.clickApply();
        }
      },
      clickPrev: function clickPrev(e) {
        var cal = $(e.target).parents('.drp-calendar');

        if (cal.hasClass('left')) {
          this.leftCalendar.month.subtract(1, 'month');
          if (this.linkedCalendars) this.rightCalendar.month.subtract(1, 'month');
        } else {
          this.rightCalendar.month.subtract(1, 'month');
        }

        this.updateCalendars();
      },
      clickNext: function clickNext(e) {
        var cal = $(e.target).parents('.drp-calendar');

        if (cal.hasClass('left')) {
          this.leftCalendar.month.add(1, 'month');
        } else {
          this.rightCalendar.month.add(1, 'month');
          if (this.linkedCalendars) this.leftCalendar.month.add(1, 'month');
        }

        this.updateCalendars();
      },
      hoverDate: function hoverDate(e) {
        //ignore dates that can't be selected
        if (!$(e.target).hasClass('available')) return;
        var title = $(e.target).attr('data-title');
        var row = title.substr(1, 1);
        var col = title.substr(3, 1);
        var cal = $(e.target).parents('.drp-calendar');
        var date = cal.hasClass('left') ? this.leftCalendar.calendar[row][col] : this.rightCalendar.calendar[row][col]; //highlight the dates between the start date and the date being hovered as a potential end date

        var leftCalendar = this.leftCalendar;
        var rightCalendar = this.rightCalendar;
        var startDate = this.startDate;

        if (!this.endDate) {
          this.container.find('.drp-calendar tbody td').each(function (index, el) {
            //skip week numbers, only look at dates
            if ($(el).hasClass('week')) return;
            var title = $(el).attr('data-title');
            var row = title.substr(1, 1);
            var col = title.substr(3, 1);
            var cal = $(el).parents('.drp-calendar');
            var dt = cal.hasClass('left') ? leftCalendar.calendar[row][col] : rightCalendar.calendar[row][col];

            if (dt.isAfter(startDate) && dt.isBefore(date) || dt.isSame(date, 'day')) {
              $(el).addClass('in-range');
            } else {
              $(el).removeClass('in-range');
            }
          });
        }
      },
      clickDate: function clickDate(e) {
        if (!$(e.target).hasClass('available')) return;
        var title = $(e.target).attr('data-title');
        var row = title.substr(1, 1);
        var col = title.substr(3, 1);
        var cal = $(e.target).parents('.drp-calendar');
        var date = cal.hasClass('left') ? this.leftCalendar.calendar[row][col] : this.rightCalendar.calendar[row][col]; //
        // this function needs to do a few things:
        // * alternate between selecting a start and end date for the range,
        // * if the time picker is enabled, apply the hour/minute/second from the select boxes to the clicked date
        // * if autoapply is enabled, and an end date was chosen, apply the selection
        // * if single date picker mode, and time picker isn't enabled, apply the selection immediately
        // * if one of the inputs above the calendars was focused, cancel that manual input
        //

        if (this.endDate || date.isBefore(this.startDate, 'day')) {
          //picking start
          if (this.timePicker) {
            var hour = parseInt(this.container.find('.left .hourselect').val(), 10);

            if (!this.timePicker24Hour) {
              var ampm = this.container.find('.left .ampmselect').val();
              if (ampm === 'PM' && hour < 12) hour += 12;
              if (ampm === 'AM' && hour === 12) hour = 0;
            }

            var minute = parseInt(this.container.find('.left .minuteselect').val(), 10);
            var second = this.timePickerSeconds ? parseInt(this.container.find('.left .secondselect').val(), 10) : 0;
            date = date.clone().hour(hour).minute(minute).second(second);
          }

          this.endDate = null;
          this.setStartDate(date.clone());
        } else if (!this.endDate && date.isBefore(this.startDate)) {
          //special case: clicking the same date for start/end,
          //but the time of the end date is before the start date
          this.setEndDate(this.startDate.clone());
        } else {
          // picking end
          if (this.timePicker) {
            var hour = parseInt(this.container.find('.right .hourselect').val(), 10);

            if (!this.timePicker24Hour) {
              var ampm = this.container.find('.right .ampmselect').val();
              if (ampm === 'PM' && hour < 12) hour += 12;
              if (ampm === 'AM' && hour === 12) hour = 0;
            }

            var minute = parseInt(this.container.find('.right .minuteselect').val(), 10);
            var second = this.timePickerSeconds ? parseInt(this.container.find('.right .secondselect').val(), 10) : 0;
            date = date.clone().hour(hour).minute(minute).second(second);
          }

          this.setEndDate(date.clone());

          if (this.autoApply) {
            this.calculateChosenLabel();
            this.clickApply();
          }
        }

        if (this.singleDatePicker) {
          this.setEndDate(this.startDate);
          if (!this.timePicker) this.clickApply();
        }

        this.updateView(); //This is to cancel the blur event handler if the mouse was in one of the inputs

        e.stopPropagation();
      },
      calculateChosenLabel: function calculateChosenLabel() {
        var customRange = true;
        var i = 0;

        for (var range in this.ranges) {
          if (this.timePicker) {
            var format = this.timePickerSeconds ? "YYYY-MM-DD hh:mm:ss" : "YYYY-MM-DD hh:mm"; //ignore times when comparing dates if time picker seconds is not enabled

            if (this.startDate.format(format) == this.ranges[range][0].format(format) && this.endDate.format(format) == this.ranges[range][1].format(format)) {
              customRange = false;
              this.chosenLabel = this.container.find('.ranges li:eq(' + i + ')').addClass('active').attr('data-range-key');
              break;
            }
          } else {
            //ignore times when comparing dates if time picker is not enabled
            if (this.startDate.format('YYYY-MM-DD') == this.ranges[range][0].format('YYYY-MM-DD') && this.endDate.format('YYYY-MM-DD') == this.ranges[range][1].format('YYYY-MM-DD')) {
              customRange = false;
              this.chosenLabel = this.container.find('.ranges li:eq(' + i + ')').addClass('active').attr('data-range-key');
              break;
            }
          }

          i++;
        }

        if (customRange) {
          if (this.showCustomRangeLabel) {
            this.chosenLabel = this.container.find('.ranges li:last').addClass('active').attr('data-range-key');
          } else {
            this.chosenLabel = null;
          }

          this.showCalendars();
        }
      },
      clickApply: function clickApply(e) {
        this.hide();
        this.element.trigger('apply.daterangepicker', this);
      },
      clickCancel: function clickCancel(e) {
        this.startDate = this.oldStartDate;
        this.endDate = this.oldEndDate;
        this.hide();
        this.element.trigger('cancel.daterangepicker', this);
      },
      monthOrYearChanged: function monthOrYearChanged(e) {
        var isLeft = $(e.target).closest('.drp-calendar').hasClass('left'),
            leftOrRight = isLeft ? 'left' : 'right',
            cal = this.container.find('.drp-calendar.' + leftOrRight); // Month must be Number for new moment versions

        var month = parseInt(cal.find('.monthselect').val(), 10);
        var year = cal.find('.yearselect').val();

        if (!isLeft) {
          if (year < this.startDate.year() || year == this.startDate.year() && month < this.startDate.month()) {
            month = this.startDate.month();
            year = this.startDate.year();
          }
        }

        if (this.minDate) {
          if (year < this.minDate.year() || year == this.minDate.year() && month < this.minDate.month()) {
            month = this.minDate.month();
            year = this.minDate.year();
          }
        }

        if (this.maxDate) {
          if (year > this.maxDate.year() || year == this.maxDate.year() && month > this.maxDate.month()) {
            month = this.maxDate.month();
            year = this.maxDate.year();
          }
        }

        if (isLeft) {
          this.leftCalendar.month.month(month).year(year);
          if (this.linkedCalendars) this.rightCalendar.month = this.leftCalendar.month.clone().add(1, 'month');
        } else {
          this.rightCalendar.month.month(month).year(year);
          if (this.linkedCalendars) this.leftCalendar.month = this.rightCalendar.month.clone().subtract(1, 'month');
        }

        this.updateCalendars();
      },
      timeChanged: function timeChanged(e) {
        var cal = $(e.target).closest('.drp-calendar'),
            isLeft = cal.hasClass('left');
        var hour = parseInt(cal.find('.hourselect').val(), 10);
        var minute = parseInt(cal.find('.minuteselect').val(), 10);
        var second = this.timePickerSeconds ? parseInt(cal.find('.secondselect').val(), 10) : 0;

        if (!this.timePicker24Hour) {
          var ampm = cal.find('.ampmselect').val();
          if (ampm === 'PM' && hour < 12) hour += 12;
          if (ampm === 'AM' && hour === 12) hour = 0;
        }

        if (isLeft) {
          var start = this.startDate.clone();
          start.hour(hour);
          start.minute(minute);
          start.second(second);
          this.setStartDate(start);

          if (this.singleDatePicker) {
            this.endDate = this.startDate.clone();
          } else if (this.endDate && this.endDate.format('YYYY-MM-DD') == start.format('YYYY-MM-DD') && this.endDate.isBefore(start)) {
            this.setEndDate(start.clone());
          }
        } else if (this.endDate) {
          var end = this.endDate.clone();
          end.hour(hour);
          end.minute(minute);
          end.second(second);
          this.setEndDate(end);
        } //update the calendars so all clickable dates reflect the new time component


        this.updateCalendars(); //update the form inputs above the calendars with the new time

        this.updateFormInputs(); //re-render the time pickers because changing one selection can affect what's enabled in another

        this.renderTimePicker('left');
        this.renderTimePicker('right');
      },
      elementChanged: function elementChanged() {
        if (!this.element.is('input')) return;
        if (!this.element.val().length) return;
        var dateString = this.element.val().split(this.locale.separator),
            start = null,
            end = null;

        if (dateString.length === 2) {
          start = moment(dateString[0], this.locale.format);
          end = moment(dateString[1], this.locale.format);
        }

        if (this.singleDatePicker || start === null || end === null) {
          start = moment(this.element.val(), this.locale.format);
          end = start;
        }

        if (!start.isValid() || !end.isValid()) return;
        this.setStartDate(start);
        this.setEndDate(end);
        this.updateView();
      },
      keydown: function keydown(e) {
        //hide on tab or enter
        if (e.keyCode === 9 || e.keyCode === 13) {
          this.hide();
        } //hide on esc and prevent propagation


        if (e.keyCode === 27) {
          e.preventDefault();
          e.stopPropagation();
          this.hide();
        }
      },
      updateElement: function updateElement() {
        if (this.element.is('input') && this.autoUpdateInput) {
          var newValue = this.startDate.format(this.locale.format);

          if (!this.singleDatePicker) {
            newValue += this.locale.separator + this.endDate.format(this.locale.format);
          }

          if (newValue !== this.element.val()) {
            this.element.val(newValue).trigger('change');
          }
        }
      },
      remove: function remove() {
        this.container.remove();
        this.element.off('.daterangepicker');
        this.element.removeData();
      }
    };

    $.fn.daterangepicker = function (options, callback) {
      var implementOptions = $.extend(true, {}, $.fn.daterangepicker.defaultOptions, options);
      this.each(function () {
        var el = $(this);
        if (el.data('daterangepicker')) el.data('daterangepicker').remove();
        el.data('daterangepicker', new DateRangePicker(el, implementOptions, callback));
      });
      return this;
    };

    return DateRangePicker;
  });

  /**
   * Wrapper for jQuery DateRangePicker plugin.
   *
   * @link  https://github.com/dangrossman/daterangepicker/
   * @since 2.0
   */

  MyListing.Datepicker = function (el, args) {
    this.el = jQuery(el);

    if (!this.el.length || !this.el.parent().hasClass('datepicker-wrapper')) {
      return;
    } // Append mask input and reset value elements.


    jQuery('<input type="text" class="display-value" readonly><i class="mi clear_all c-hide reset-value"></i>').insertAfter(this.el);
    this.el.attr('autocomplete', 'off') // Disable autocomplete suggestions.
    .attr('readonly', true) // Prevent clicks from opening the keyboard on mobile devices.
    .addClass('picker'); // Add helper class.

    this.parent = this.el.parent();
    this.value = moment(this.el.val());
    this.mask = this.parent.find('.display-value');
    this.reset = this.parent.find('.reset-value');
    this.args = jQuery.extend({
      timepicker: false
    }, args); // Determine the format to be used for saving and displaying the value.

    this.format = this.args.timepicker === true ? 'YYYY-MM-DD HH:mm:ss' : 'YYYY-MM-DD';
    this.displayFormat = this.args.timepicker === true ? CASE27.l10n.datepicker.dateTimeFormat : CASE27.l10n.datepicker.format; // Set mask placeholder.

    this.mask.attr('placeholder', this.el.attr('placeholder')); // Initialize daterangepicker.

    this.picker = this.el.daterangepicker({
      autoUpdateInput: false,
      showDropdowns: true,
      singleDatePicker: true,
      timePicker24Hour: CASE27.l10n.datepicker.timePicker24Hour,
      locale: jQuery.extend({}, CASE27.l10n.datepicker, {
        format: this.format
      }),
      timePicker: this.args.timepicker
    }); // daterangepicker instance

    this.drp = this.picker.data('daterangepicker'); // Listen for value change.

    this.picker.on('apply.daterangepicker', this.apply.bind(this)); // On value change, update the input, mask, and trigger a DOM `datepicker:change` event.

    this.el.on('change', this.change.bind(this)); // update the input and mask input values without triggering `datepicker:change` event.

    this.updateInputValues(); // Reset value button.

    this.reset.click(function (e) {
      this.value = moment('');
      this.el.trigger('change');
    }.bind(this));
  };
  /**
   * On daterangepicker apply event, update value prop,
   * and trigger a jQuery change event on the input to handle the rest.
   *
   * @since 2.0
   */


  MyListing.Datepicker.prototype.apply = function (e, picker) {
    this.value = picker.startDate;
    this.el.trigger('change');
  };
  /**
   * On input change event, check if the user has selected
   * a valid date, and update the input and mask accordingly.
   *
   * This also triggers a custom `datepicker:change` DOM event,
   * which can be used to listen for changes in Vue.js
   *
   * @since 2.0
   */


  MyListing.Datepicker.prototype.change = function () {
    this.updateInputValues();
    this.fireChangeEvent({
      value: this.el.val(),
      mask: this.mask.val()
    });
  };
  /**
   * Update the input and mask values.
   *
   * @since 2.1.7
   */


  MyListing.Datepicker.prototype.updateInputValues = function () {
    var value = this.value.isValid() ? this.value.clone().locale('en').format(this.format) : '';
    var mask = this.value.isValid() ? this.value.format(this.displayFormat) : '';
    this.el.val(value);
    this.mask.val(mask);
    value === '' ? this.reset.removeClass('c-show').addClass('c-hide') : this.reset.addClass('c-show').removeClass('c-hide');
  };
  /**
   * Fires a custom DOM event.
   *
   * @since 2.0
   */


  MyListing.Datepicker.prototype.fireChangeEvent = function (data) {
    var event = document.createEvent('CustomEvent');
    event.initCustomEvent('datepicker:change', false, true, data);
    this.el.get(0).dispatchEvent(event);
  };
  /**
   * Update minDate prop and re-apply value.
   *
   * @since 2.4
   * @param date A momentjs date instance
   */


  MyListing.Datepicker.prototype.setMinDate = function (date) {
    this.drp.minDate = date;

    if (this.drp.minDate.isAfter(this.drp.startDate)) {
      this.value = this.drp.startDate = this.drp.endDate = this.drp.minDate;
      this.el.trigger('change');
    }
  };
  /**
   * Update input value.
   *
   * @since 2.4
   * @param date A momentjs date instance
   */


  MyListing.Datepicker.prototype.setValue = function (value) {
    this.value = value;
    this.el.trigger('change');
  };

  MyListing.Datepicker.prototype.do = function (cb) {
    cb(this);
  };
  /**
   * Retrieve input value.
   *
   * @since 2.4
   * @return date A momentjs date instance
   */


  MyListing.Datepicker.prototype.getValue = function () {
    return this.value;
  };
  /**
   * Init datepickers.
   */


  jQuery(function ($) {
    $('.mylisting-datepicker').each(function (i, el) {
      var options = $(el).data('options');

      if (_typeof(options) !== 'object') {
        options = {};
      }

      new MyListing.Datepicker(el, options);
    });
  });

  /*! PhotoSwipe - v4.1.3 - 2019-01-08
  * http://photoswipe.com
  * Copyright (c) 2019 Dmitry Semenov; */
  (function (root, factory) {
    if (typeof define === 'function' && define.amd) {
      define(factory);
    } else if ((typeof exports === "undefined" ? "undefined" : _typeof(exports)) === 'object') {
      module.exports = factory();
    } else {
      root.PhotoSwipe = factory();
    }
  })(window, function () {

    var PhotoSwipe = function PhotoSwipe(template, UiClass, items, options) {
      /*>>framework-bridge*/

      /**
       *
       * Set of generic functions used by gallery.
       * 
       * You're free to modify anything here as long as functionality is kept.
       * 
       */
      var framework = {
        features: null,
        bind: function bind(target, type, listener, unbind) {
          var methodName = (unbind ? 'remove' : 'add') + 'EventListener';
          type = type.split(' ');

          for (var i = 0; i < type.length; i++) {
            if (type[i]) {
              target[methodName](type[i], listener, false);
            }
          }
        },
        isArray: function isArray(obj) {
          return obj instanceof Array;
        },
        createEl: function createEl(classes, tag) {
          var el = document.createElement(tag || 'div');

          if (classes) {
            el.className = classes;
          }

          return el;
        },
        getScrollY: function getScrollY() {
          var yOffset = window.pageYOffset;
          return yOffset !== undefined ? yOffset : document.documentElement.scrollTop;
        },
        unbind: function unbind(target, type, listener) {
          framework.bind(target, type, listener, true);
        },
        removeClass: function removeClass(el, className) {
          var reg = new RegExp('(\\s|^)' + className + '(\\s|$)');
          el.className = el.className.replace(reg, ' ').replace(/^\s\s*/, '').replace(/\s\s*$/, '');
        },
        addClass: function addClass(el, className) {
          if (!framework.hasClass(el, className)) {
            el.className += (el.className ? ' ' : '') + className;
          }
        },
        hasClass: function hasClass(el, className) {
          return el.className && new RegExp('(^|\\s)' + className + '(\\s|$)').test(el.className);
        },
        getChildByClass: function getChildByClass(parentEl, childClassName) {
          var node = parentEl.firstChild;

          while (node) {
            if (framework.hasClass(node, childClassName)) {
              return node;
            }

            node = node.nextSibling;
          }
        },
        arraySearch: function arraySearch(array, value, key) {
          var i = array.length;

          while (i--) {
            if (array[i][key] === value) {
              return i;
            }
          }

          return -1;
        },
        extend: function extend(o1, o2, preventOverwrite) {
          for (var prop in o2) {
            if (o2.hasOwnProperty(prop)) {
              if (preventOverwrite && o1.hasOwnProperty(prop)) {
                continue;
              }

              o1[prop] = o2[prop];
            }
          }
        },
        easing: {
          sine: {
            out: function out(k) {
              return Math.sin(k * (Math.PI / 2));
            },
            inOut: function inOut(k) {
              return -(Math.cos(Math.PI * k) - 1) / 2;
            }
          },
          cubic: {
            out: function out(k) {
              return --k * k * k + 1;
            }
          }
          /*
          	elastic: {
          		out: function ( k ) {
          					var s, a = 0.1, p = 0.4;
          			if ( k === 0 ) return 0;
          			if ( k === 1 ) return 1;
          			if ( !a || a < 1 ) { a = 1; s = p / 4; }
          			else s = p * Math.asin( 1 / a ) / ( 2 * Math.PI );
          			return ( a * Math.pow( 2, - 10 * k) * Math.sin( ( k - s ) * ( 2 * Math.PI ) / p ) + 1 );
          				},
          	},
          	back: {
          		out: function ( k ) {
          			var s = 1.70158;
          			return --k * k * ( ( s + 1 ) * k + s ) + 1;
          		}
          	}
          */

        },

        /**
         * 
         * @return {object}
         * 
         * {
         *  raf : request animation frame function
         *  caf : cancel animation frame function
         *  transfrom : transform property key (with vendor), or null if not supported
         *  oldIE : IE8 or below
         * }
         * 
         */
        detectFeatures: function detectFeatures() {
          if (framework.features) {
            return framework.features;
          }

          var helperEl = framework.createEl(),
              helperStyle = helperEl.style,
              vendor = '',
              features = {}; // IE8 and below

          features.oldIE = document.all && !document.addEventListener;
          features.touch = 'ontouchstart' in window;

          if (window.requestAnimationFrame) {
            features.raf = window.requestAnimationFrame;
            features.caf = window.cancelAnimationFrame;
          }

          features.pointerEvent = !!window.PointerEvent || navigator.msPointerEnabled; // fix false-positive detection of old Android in new IE
          // (IE11 ua string contains "Android 4.0")

          if (!features.pointerEvent) {
            var ua = navigator.userAgent; // Detect if device is iPhone or iPod and if it's older than iOS 8
            // http://stackoverflow.com/a/14223920
            // 
            // This detection is made because of buggy top/bottom toolbars
            // that don't trigger window.resize event.
            // For more info refer to _isFixedPosition variable in core.js

            if (/iP(hone|od)/.test(navigator.platform)) {
              var v = navigator.appVersion.match(/OS (\d+)_(\d+)_?(\d+)?/);

              if (v && v.length > 0) {
                v = parseInt(v[1], 10);

                if (v >= 1 && v < 8) {
                  features.isOldIOSPhone = true;
                }
              }
            } // Detect old Android (before KitKat)
            // due to bugs related to position:fixed
            // http://stackoverflow.com/questions/7184573/pick-up-the-android-version-in-the-browser-by-javascript


            var match = ua.match(/Android\s([0-9\.]*)/);
            var androidversion = match ? match[1] : 0;
            androidversion = parseFloat(androidversion);

            if (androidversion >= 1) {
              if (androidversion < 4.4) {
                features.isOldAndroid = true; // for fixed position bug & performance
              }

              features.androidVersion = androidversion; // for touchend bug
            }

            features.isMobileOpera = /opera mini|opera mobi/i.test(ua); // p.s. yes, yes, UA sniffing is bad, propose your solution for above bugs.
          }

          var styleChecks = ['transform', 'perspective', 'animationName'],
              vendors = ['', 'webkit', 'Moz', 'ms', 'O'],
              styleCheckItem,
              styleName;

          for (var i = 0; i < 4; i++) {
            vendor = vendors[i];

            for (var a = 0; a < 3; a++) {
              styleCheckItem = styleChecks[a]; // uppercase first letter of property name, if vendor is present

              styleName = vendor + (vendor ? styleCheckItem.charAt(0).toUpperCase() + styleCheckItem.slice(1) : styleCheckItem);

              if (!features[styleCheckItem] && styleName in helperStyle) {
                features[styleCheckItem] = styleName;
              }
            }

            if (vendor && !features.raf) {
              vendor = vendor.toLowerCase();
              features.raf = window[vendor + 'RequestAnimationFrame'];

              if (features.raf) {
                features.caf = window[vendor + 'CancelAnimationFrame'] || window[vendor + 'CancelRequestAnimationFrame'];
              }
            }
          }

          if (!features.raf) {
            var lastTime = 0;

            features.raf = function (fn) {
              var currTime = new Date().getTime();
              var timeToCall = Math.max(0, 16 - (currTime - lastTime));
              var id = window.setTimeout(function () {
                fn(currTime + timeToCall);
              }, timeToCall);
              lastTime = currTime + timeToCall;
              return id;
            };

            features.caf = function (id) {
              clearTimeout(id);
            };
          } // Detect SVG support


          features.svg = !!document.createElementNS && !!document.createElementNS('http://www.w3.org/2000/svg', 'svg').createSVGRect;
          framework.features = features;
          return features;
        }
      };
      framework.detectFeatures(); // Override addEventListener for old versions of IE

      if (framework.features.oldIE) {
        framework.bind = function (target, type, listener, unbind) {
          type = type.split(' ');

          var methodName = (unbind ? 'detach' : 'attach') + 'Event',
              evName,
              _handleEv = function _handleEv() {
            listener.handleEvent.call(listener);
          };

          for (var i = 0; i < type.length; i++) {
            evName = type[i];

            if (evName) {
              if (_typeof(listener) === 'object' && listener.handleEvent) {
                if (!unbind) {
                  listener['oldIE' + evName] = _handleEv;
                } else {
                  if (!listener['oldIE' + evName]) {
                    return false;
                  }
                }

                target[methodName]('on' + evName, listener['oldIE' + evName]);
              } else {
                target[methodName]('on' + evName, listener);
              }
            }
          }
        };
      }
      /*>>framework-bridge*/

      /*>>core*/
      //function(template, UiClass, items, options)


      var self = this;
      /**
       * Static vars, don't change unless you know what you're doing.
       */

      var DOUBLE_TAP_RADIUS = 25,
          NUM_HOLDERS = 3;
      /**
       * Options
       */

      var _options = {
        allowPanToNext: true,
        spacing: 0.12,
        bgOpacity: 1,
        mouseUsed: false,
        loop: true,
        pinchToClose: true,
        closeOnScroll: true,
        closeOnVerticalDrag: true,
        verticalDragRange: 0.75,
        hideAnimationDuration: 333,
        showAnimationDuration: 333,
        showHideOpacity: false,
        focus: true,
        escKey: true,
        arrowKeys: true,
        mainScrollEndFriction: 0.35,
        panEndFriction: 0.35,
        isClickableElement: function isClickableElement(el) {
          return el.tagName === 'A';
        },
        getDoubleTapZoom: function getDoubleTapZoom(isMouseClick, item) {
          if (isMouseClick) {
            return 1;
          } else {
            return item.initialZoomLevel < 0.7 ? 1 : 1.33;
          }
        },
        maxSpreadZoom: 1.33,
        modal: true,
        // not fully implemented yet
        scaleMode: 'fit' // TODO

      };
      framework.extend(_options, options);
      /**
       * Private helper variables & functions
       */

      var _getEmptyPoint = function _getEmptyPoint() {
        return {
          x: 0,
          y: 0
        };
      };

      var _isOpen,
          _isDestroying,
          _closedByScroll,
          _currentItemIndex,
          _containerStyle,
          _containerShiftIndex,
          _currPanDist = _getEmptyPoint(),
          _startPanOffset = _getEmptyPoint(),
          _panOffset = _getEmptyPoint(),
          _upMoveEvents,
          // drag move, drag end & drag cancel events array
      _downEvents,
          // drag start events array
      _globalEventHandlers,
          _viewportSize = {},
          _currZoomLevel,
          _startZoomLevel,
          _translatePrefix,
          _translateSufix,
          _updateSizeInterval,
          _itemsNeedUpdate,
          _currPositionIndex = 0,
          _offset = {},
          _slideSize = _getEmptyPoint(),
          // size of slide area, including spacing
      _itemHolders,
          _prevItemIndex,
          _indexDiff = 0,
          // difference of indexes since last content update
      _dragStartEvent,
          _dragMoveEvent,
          _dragEndEvent,
          _dragCancelEvent,
          _transformKey,
          _pointerEventEnabled,
          _isFixedPosition = true,
          _likelyTouchDevice,
          _modules = [],
          _requestAF,
          _cancelAF,
          _initalClassName,
          _initalWindowScrollY,
          _oldIE,
          _currentWindowScrollY,
          _features,
          _windowVisibleSize = {},
          _renderMaxResolution = false,
          _orientationChangeTimeout,
          // Registers PhotoSWipe module (History, Controller ...)
      _registerModule = function _registerModule(name, module) {
        framework.extend(self, module.publicMethods);

        _modules.push(name);
      },
          _getLoopedId = function _getLoopedId(index) {
        var numSlides = _getNumItems();

        if (index > numSlides - 1) {
          return index - numSlides;
        } else if (index < 0) {
          return numSlides + index;
        }

        return index;
      },
          // Micro bind/trigger
      _listeners = {},
          _listen = function _listen(name, fn) {
        if (!_listeners[name]) {
          _listeners[name] = [];
        }

        return _listeners[name].push(fn);
      },
          _shout = function _shout(name) {
        var listeners = _listeners[name];

        if (listeners) {
          var args = Array.prototype.slice.call(arguments);
          args.shift();

          for (var i = 0; i < listeners.length; i++) {
            listeners[i].apply(self, args);
          }
        }
      },
          _getCurrentTime = function _getCurrentTime() {
        return new Date().getTime();
      },
          _applyBgOpacity = function _applyBgOpacity(opacity) {
        _bgOpacity = opacity;
        self.bg.style.opacity = opacity * _options.bgOpacity;
      },
          _applyZoomTransform = function _applyZoomTransform(styleObj, x, y, zoom, item) {
        if (!_renderMaxResolution || item && item !== self.currItem) {
          zoom = zoom / (item ? item.fitRatio : self.currItem.fitRatio);
        }

        styleObj[_transformKey] = _translatePrefix + x + 'px, ' + y + 'px' + _translateSufix + ' scale(' + zoom + ')';
      },
          _applyCurrentZoomPan = function _applyCurrentZoomPan(allowRenderResolution) {
        if (_currZoomElementStyle) {
          if (allowRenderResolution) {
            if (_currZoomLevel > self.currItem.fitRatio) {
              if (!_renderMaxResolution) {
                _setImageSize(self.currItem, false, true);

                _renderMaxResolution = true;
              }
            } else {
              if (_renderMaxResolution) {
                _setImageSize(self.currItem);

                _renderMaxResolution = false;
              }
            }
          }

          _applyZoomTransform(_currZoomElementStyle, _panOffset.x, _panOffset.y, _currZoomLevel);
        }
      },
          _applyZoomPanToItem = function _applyZoomPanToItem(item) {
        if (item.container) {
          _applyZoomTransform(item.container.style, item.initialPosition.x, item.initialPosition.y, item.initialZoomLevel, item);
        }
      },
          _setTranslateX = function _setTranslateX(x, elStyle) {
        elStyle[_transformKey] = _translatePrefix + x + 'px, 0px' + _translateSufix;
      },
          _moveMainScroll = function _moveMainScroll(x, dragging) {
        if (!_options.loop && dragging) {
          var newSlideIndexOffset = _currentItemIndex + (_slideSize.x * _currPositionIndex - x) / _slideSize.x,
              delta = Math.round(x - _mainScrollPos.x);

          if (newSlideIndexOffset < 0 && delta > 0 || newSlideIndexOffset >= _getNumItems() - 1 && delta < 0) {
            x = _mainScrollPos.x + delta * _options.mainScrollEndFriction;
          }
        }

        _mainScrollPos.x = x;

        _setTranslateX(x, _containerStyle);
      },
          _calculatePanOffset = function _calculatePanOffset(axis, zoomLevel) {
        var m = _midZoomPoint[axis] - _offset[axis];
        return _startPanOffset[axis] + _currPanDist[axis] + m - m * (zoomLevel / _startZoomLevel);
      },
          _equalizePoints = function _equalizePoints(p1, p2) {
        p1.x = p2.x;
        p1.y = p2.y;

        if (p2.id) {
          p1.id = p2.id;
        }
      },
          _roundPoint = function _roundPoint(p) {
        p.x = Math.round(p.x);
        p.y = Math.round(p.y);
      },
          _mouseMoveTimeout = null,
          _onFirstMouseMove = function _onFirstMouseMove() {
        // Wait until mouse move event is fired at least twice during 100ms
        // We do this, because some mobile browsers trigger it on touchstart
        if (_mouseMoveTimeout) {
          framework.unbind(document, 'mousemove', _onFirstMouseMove);
          framework.addClass(template, 'pswp--has_mouse');
          _options.mouseUsed = true;

          _shout('mouseUsed');
        }

        _mouseMoveTimeout = setTimeout(function () {
          _mouseMoveTimeout = null;
        }, 100);
      },
          _bindEvents = function _bindEvents() {
        framework.bind(document, 'keydown', self);

        if (_features.transform) {
          // don't bind click event in browsers that don't support transform (mostly IE8)
          framework.bind(self.scrollWrap, 'click', self);
        }

        if (!_options.mouseUsed) {
          framework.bind(document, 'mousemove', _onFirstMouseMove);
        }

        framework.bind(window, 'resize scroll orientationchange', self);

        _shout('bindEvents');
      },
          _unbindEvents = function _unbindEvents() {
        framework.unbind(window, 'resize scroll orientationchange', self);
        framework.unbind(window, 'scroll', _globalEventHandlers.scroll);
        framework.unbind(document, 'keydown', self);
        framework.unbind(document, 'mousemove', _onFirstMouseMove);

        if (_features.transform) {
          framework.unbind(self.scrollWrap, 'click', self);
        }

        if (_isDragging) {
          framework.unbind(window, _upMoveEvents, self);
        }

        clearTimeout(_orientationChangeTimeout);

        _shout('unbindEvents');
      },
          _calculatePanBounds = function _calculatePanBounds(zoomLevel, update) {
        var bounds = _calculateItemSize(self.currItem, _viewportSize, zoomLevel);

        if (update) {
          _currPanBounds = bounds;
        }

        return bounds;
      },
          _getMinZoomLevel = function _getMinZoomLevel(item) {
        if (!item) {
          item = self.currItem;
        }

        return item.initialZoomLevel;
      },
          _getMaxZoomLevel = function _getMaxZoomLevel(item) {
        if (!item) {
          item = self.currItem;
        }

        return item.w > 0 ? _options.maxSpreadZoom : 1;
      },
          // Return true if offset is out of the bounds
      _modifyDestPanOffset = function _modifyDestPanOffset(axis, destPanBounds, destPanOffset, destZoomLevel) {
        if (destZoomLevel === self.currItem.initialZoomLevel) {
          destPanOffset[axis] = self.currItem.initialPosition[axis];
          return true;
        } else {
          destPanOffset[axis] = _calculatePanOffset(axis, destZoomLevel);

          if (destPanOffset[axis] > destPanBounds.min[axis]) {
            destPanOffset[axis] = destPanBounds.min[axis];
            return true;
          } else if (destPanOffset[axis] < destPanBounds.max[axis]) {
            destPanOffset[axis] = destPanBounds.max[axis];
            return true;
          }
        }

        return false;
      },
          _setupTransforms = function _setupTransforms() {
        if (_transformKey) {
          // setup 3d transforms
          var allow3dTransform = _features.perspective && !_likelyTouchDevice;
          _translatePrefix = 'translate' + (allow3dTransform ? '3d(' : '(');
          _translateSufix = _features.perspective ? ', 0px)' : ')';
          return;
        } // Override zoom/pan/move functions in case old browser is used (most likely IE)
        // (so they use left/top/width/height, instead of CSS transform)


        _transformKey = 'left';
        framework.addClass(template, 'pswp--ie');

        _setTranslateX = function _setTranslateX(x, elStyle) {
          elStyle.left = x + 'px';
        };

        _applyZoomPanToItem = function _applyZoomPanToItem(item) {
          var zoomRatio = item.fitRatio > 1 ? 1 : item.fitRatio,
              s = item.container.style,
              w = zoomRatio * item.w,
              h = zoomRatio * item.h;
          s.width = w + 'px';
          s.height = h + 'px';
          s.left = item.initialPosition.x + 'px';
          s.top = item.initialPosition.y + 'px';
        };

        _applyCurrentZoomPan = function _applyCurrentZoomPan() {
          if (_currZoomElementStyle) {
            var s = _currZoomElementStyle,
                item = self.currItem,
                zoomRatio = item.fitRatio > 1 ? 1 : item.fitRatio,
                w = zoomRatio * item.w,
                h = zoomRatio * item.h;
            s.width = w + 'px';
            s.height = h + 'px';
            s.left = _panOffset.x + 'px';
            s.top = _panOffset.y + 'px';
          }
        };
      },
          _onKeyDown = function _onKeyDown(e) {
        var keydownAction = '';

        if (_options.escKey && e.keyCode === 27) {
          keydownAction = 'close';
        } else if (_options.arrowKeys) {
          if (e.keyCode === 37) {
            keydownAction = 'prev';
          } else if (e.keyCode === 39) {
            keydownAction = 'next';
          }
        }

        if (keydownAction) {
          // don't do anything if special key pressed to prevent from overriding default browser actions
          // e.g. in Chrome on Mac cmd+arrow-left returns to previous page
          if (!e.ctrlKey && !e.altKey && !e.shiftKey && !e.metaKey) {
            if (e.preventDefault) {
              e.preventDefault();
            } else {
              e.returnValue = false;
            }

            self[keydownAction]();
          }
        }
      },
          _onGlobalClick = function _onGlobalClick(e) {
        if (!e) {
          return;
        } // don't allow click event to pass through when triggering after drag or some other gesture


        if (_moved || _zoomStarted || _mainScrollAnimating || _verticalDragInitiated) {
          e.preventDefault();
          e.stopPropagation();
        }
      },
          _updatePageScrollOffset = function _updatePageScrollOffset() {
        self.setScrollOffset(0, framework.getScrollY());
      }; // Micro animation engine


      var _animations = {},
          _numAnimations = 0,
          _stopAnimation = function _stopAnimation(name) {
        if (_animations[name]) {
          if (_animations[name].raf) {
            _cancelAF(_animations[name].raf);
          }

          _numAnimations--;
          delete _animations[name];
        }
      },
          _registerStartAnimation = function _registerStartAnimation(name) {
        if (_animations[name]) {
          _stopAnimation(name);
        }

        if (!_animations[name]) {
          _numAnimations++;
          _animations[name] = {};
        }
      },
          _stopAllAnimations = function _stopAllAnimations() {
        for (var prop in _animations) {
          if (_animations.hasOwnProperty(prop)) {
            _stopAnimation(prop);
          }
        }
      },
          _animateProp = function _animateProp(name, b, endProp, d, easingFn, onUpdate, onComplete) {
        var startAnimTime = _getCurrentTime(),
            t;

        _registerStartAnimation(name);

        var animloop = function animloop() {
          if (_animations[name]) {
            t = _getCurrentTime() - startAnimTime; // time diff
            //b - beginning (start prop)
            //d - anim duration

            if (t >= d) {
              _stopAnimation(name);

              onUpdate(endProp);

              if (onComplete) {
                onComplete();
              }

              return;
            }

            onUpdate((endProp - b) * easingFn(t / d) + b);
            _animations[name].raf = _requestAF(animloop);
          }
        };

        animloop();
      };

      var publicMethods = {
        // make a few local variables and functions public
        shout: _shout,
        listen: _listen,
        viewportSize: _viewportSize,
        options: _options,
        isMainScrollAnimating: function isMainScrollAnimating() {
          return _mainScrollAnimating;
        },
        getZoomLevel: function getZoomLevel() {
          return _currZoomLevel;
        },
        getCurrentIndex: function getCurrentIndex() {
          return _currentItemIndex;
        },
        isDragging: function isDragging() {
          return _isDragging;
        },
        isZooming: function isZooming() {
          return _isZooming;
        },
        setScrollOffset: function setScrollOffset(x, y) {
          _offset.x = x;
          _currentWindowScrollY = _offset.y = y;

          _shout('updateScrollOffset', _offset);
        },
        applyZoomPan: function applyZoomPan(zoomLevel, panX, panY, allowRenderResolution) {
          _panOffset.x = panX;
          _panOffset.y = panY;
          _currZoomLevel = zoomLevel;

          _applyCurrentZoomPan(allowRenderResolution);
        },
        init: function init() {
          if (_isOpen || _isDestroying) {
            return;
          }

          var i;
          self.framework = framework; // basic functionality

          self.template = template; // root DOM element of PhotoSwipe

          self.bg = framework.getChildByClass(template, 'pswp__bg');
          _initalClassName = template.className;
          _isOpen = true;
          _features = framework.detectFeatures();
          _requestAF = _features.raf;
          _cancelAF = _features.caf;
          _transformKey = _features.transform;
          _oldIE = _features.oldIE;
          self.scrollWrap = framework.getChildByClass(template, 'pswp__scroll-wrap');
          self.container = framework.getChildByClass(self.scrollWrap, 'pswp__container');
          _containerStyle = self.container.style; // for fast access
          // Objects that hold slides (there are only 3 in DOM)

          self.itemHolders = _itemHolders = [{
            el: self.container.children[0],
            wrap: 0,
            index: -1
          }, {
            el: self.container.children[1],
            wrap: 0,
            index: -1
          }, {
            el: self.container.children[2],
            wrap: 0,
            index: -1
          }]; // hide nearby item holders until initial zoom animation finishes (to avoid extra Paints)

          _itemHolders[0].el.style.display = _itemHolders[2].el.style.display = 'none';

          _setupTransforms(); // Setup global events


          _globalEventHandlers = {
            resize: self.updateSize,
            // Fixes: iOS 10.3 resize event
            // does not update scrollWrap.clientWidth instantly after resize
            // https://github.com/dimsemenov/PhotoSwipe/issues/1315
            orientationchange: function orientationchange() {
              clearTimeout(_orientationChangeTimeout);
              _orientationChangeTimeout = setTimeout(function () {
                if (_viewportSize.x !== self.scrollWrap.clientWidth) {
                  self.updateSize();
                }
              }, 500);
            },
            scroll: _updatePageScrollOffset,
            keydown: _onKeyDown,
            click: _onGlobalClick
          }; // disable show/hide effects on old browsers that don't support CSS animations or transforms, 
          // old IOS, Android and Opera mobile. Blackberry seems to work fine, even older models.

          var oldPhone = _features.isOldIOSPhone || _features.isOldAndroid || _features.isMobileOpera;

          if (!_features.animationName || !_features.transform || oldPhone) {
            _options.showAnimationDuration = _options.hideAnimationDuration = 0;
          } // init modules


          for (i = 0; i < _modules.length; i++) {
            self['init' + _modules[i]]();
          } // init


          if (UiClass) {
            var ui = self.ui = new UiClass(self, framework);
            ui.init();
          }

          _shout('firstUpdate');

          _currentItemIndex = _currentItemIndex || _options.index || 0; // validate index

          if (isNaN(_currentItemIndex) || _currentItemIndex < 0 || _currentItemIndex >= _getNumItems()) {
            _currentItemIndex = 0;
          }

          self.currItem = _getItemAt(_currentItemIndex);

          if (_features.isOldIOSPhone || _features.isOldAndroid) {
            _isFixedPosition = false;
          }

          template.setAttribute('aria-hidden', 'false');

          if (_options.modal) {
            if (!_isFixedPosition) {
              template.style.position = 'absolute';
              template.style.top = framework.getScrollY() + 'px';
            } else {
              template.style.position = 'fixed';
            }
          }

          if (_currentWindowScrollY === undefined) {
            _shout('initialLayout');

            _currentWindowScrollY = _initalWindowScrollY = framework.getScrollY();
          } // add classes to root element of PhotoSwipe


          var rootClasses = 'pswp--open ';

          if (_options.mainClass) {
            rootClasses += _options.mainClass + ' ';
          }

          if (_options.showHideOpacity) {
            rootClasses += 'pswp--animate_opacity ';
          }

          rootClasses += _likelyTouchDevice ? 'pswp--touch' : 'pswp--notouch';
          rootClasses += _features.animationName ? ' pswp--css_animation' : '';
          rootClasses += _features.svg ? ' pswp--svg' : '';
          framework.addClass(template, rootClasses);
          self.updateSize(); // initial update

          _containerShiftIndex = -1;
          _indexDiff = null;

          for (i = 0; i < NUM_HOLDERS; i++) {
            _setTranslateX((i + _containerShiftIndex) * _slideSize.x, _itemHolders[i].el.style);
          }

          if (!_oldIE) {
            framework.bind(self.scrollWrap, _downEvents, self); // no dragging for old IE
          }

          _listen('initialZoomInEnd', function () {
            self.setContent(_itemHolders[0], _currentItemIndex - 1);
            self.setContent(_itemHolders[2], _currentItemIndex + 1);
            _itemHolders[0].el.style.display = _itemHolders[2].el.style.display = 'block';

            if (_options.focus) {
              // focus causes layout, 
              // which causes lag during the animation, 
              // that's why we delay it untill the initial zoom transition ends
              template.focus();
            }

            _bindEvents();
          }); // set content for center slide (first time)


          self.setContent(_itemHolders[1], _currentItemIndex);
          self.updateCurrItem();

          _shout('afterInit');

          if (!_isFixedPosition) {
            // On all versions of iOS lower than 8.0, we check size of viewport every second.
            // 
            // This is done to detect when Safari top & bottom bars appear, 
            // as this action doesn't trigger any events (like resize). 
            // 
            // On iOS8 they fixed this.
            // 
            // 10 Nov 2014: iOS 7 usage ~40%. iOS 8 usage 56%.
            _updateSizeInterval = setInterval(function () {
              if (!_numAnimations && !_isDragging && !_isZooming && _currZoomLevel === self.currItem.initialZoomLevel) {
                self.updateSize();
              }
            }, 1000);
          }

          framework.addClass(template, 'pswp--visible');
        },
        // Close the gallery, then destroy it
        close: function close() {
          if (!_isOpen) {
            return;
          }

          _isOpen = false;
          _isDestroying = true;

          _shout('close');

          _unbindEvents();

          _showOrHide(self.currItem, null, true, self.destroy);
        },
        // destroys the gallery (unbinds events, cleans up intervals and timeouts to avoid memory leaks)
        destroy: function destroy() {
          _shout('destroy');

          if (_showOrHideTimeout) {
            clearTimeout(_showOrHideTimeout);
          }

          template.setAttribute('aria-hidden', 'true');
          template.className = _initalClassName;

          if (_updateSizeInterval) {
            clearInterval(_updateSizeInterval);
          }

          framework.unbind(self.scrollWrap, _downEvents, self); // we unbind scroll event at the end, as closing animation may depend on it

          framework.unbind(window, 'scroll', self);

          _stopDragUpdateLoop();

          _stopAllAnimations();

          _listeners = null;
        },

        /**
         * Pan image to position
         * @param {Number} x     
         * @param {Number} y     
         * @param {Boolean} force Will ignore bounds if set to true.
         */
        panTo: function panTo(x, y, force) {
          if (!force) {
            if (x > _currPanBounds.min.x) {
              x = _currPanBounds.min.x;
            } else if (x < _currPanBounds.max.x) {
              x = _currPanBounds.max.x;
            }

            if (y > _currPanBounds.min.y) {
              y = _currPanBounds.min.y;
            } else if (y < _currPanBounds.max.y) {
              y = _currPanBounds.max.y;
            }
          }

          _panOffset.x = x;
          _panOffset.y = y;

          _applyCurrentZoomPan();
        },
        handleEvent: function handleEvent(e) {
          e = e || window.event;

          if (_globalEventHandlers[e.type]) {
            _globalEventHandlers[e.type](e);
          }
        },
        goTo: function goTo(index) {
          index = _getLoopedId(index);
          var diff = index - _currentItemIndex;
          _indexDiff = diff;
          _currentItemIndex = index;
          self.currItem = _getItemAt(_currentItemIndex);
          _currPositionIndex -= diff;

          _moveMainScroll(_slideSize.x * _currPositionIndex);

          _stopAllAnimations();

          _mainScrollAnimating = false;
          self.updateCurrItem();
        },
        next: function next() {
          self.goTo(_currentItemIndex + 1);
        },
        prev: function prev() {
          self.goTo(_currentItemIndex - 1);
        },
        // update current zoom/pan objects
        updateCurrZoomItem: function updateCurrZoomItem(emulateSetContent) {
          if (emulateSetContent) {
            _shout('beforeChange', 0);
          } // itemHolder[1] is middle (current) item


          if (_itemHolders[1].el.children.length) {
            var zoomElement = _itemHolders[1].el.children[0];

            if (framework.hasClass(zoomElement, 'pswp__zoom-wrap')) {
              _currZoomElementStyle = zoomElement.style;
            } else {
              _currZoomElementStyle = null;
            }
          } else {
            _currZoomElementStyle = null;
          }

          _currPanBounds = self.currItem.bounds;
          _startZoomLevel = _currZoomLevel = self.currItem.initialZoomLevel;
          _panOffset.x = _currPanBounds.center.x;
          _panOffset.y = _currPanBounds.center.y;

          if (emulateSetContent) {
            _shout('afterChange');
          }
        },
        invalidateCurrItems: function invalidateCurrItems() {
          _itemsNeedUpdate = true;

          for (var i = 0; i < NUM_HOLDERS; i++) {
            if (_itemHolders[i].item) {
              _itemHolders[i].item.needsUpdate = true;
            }
          }
        },
        updateCurrItem: function updateCurrItem(beforeAnimation) {
          if (_indexDiff === 0) {
            return;
          }

          var diffAbs = Math.abs(_indexDiff),
              tempHolder;

          if (beforeAnimation && diffAbs < 2) {
            return;
          }

          self.currItem = _getItemAt(_currentItemIndex);
          _renderMaxResolution = false;

          _shout('beforeChange', _indexDiff);

          if (diffAbs >= NUM_HOLDERS) {
            _containerShiftIndex += _indexDiff + (_indexDiff > 0 ? -NUM_HOLDERS : NUM_HOLDERS);
            diffAbs = NUM_HOLDERS;
          }

          for (var i = 0; i < diffAbs; i++) {
            if (_indexDiff > 0) {
              tempHolder = _itemHolders.shift();
              _itemHolders[NUM_HOLDERS - 1] = tempHolder; // move first to last

              _containerShiftIndex++;

              _setTranslateX((_containerShiftIndex + 2) * _slideSize.x, tempHolder.el.style);

              self.setContent(tempHolder, _currentItemIndex - diffAbs + i + 1 + 1);
            } else {
              tempHolder = _itemHolders.pop();

              _itemHolders.unshift(tempHolder); // move last to first


              _containerShiftIndex--;

              _setTranslateX(_containerShiftIndex * _slideSize.x, tempHolder.el.style);

              self.setContent(tempHolder, _currentItemIndex + diffAbs - i - 1 - 1);
            }
          } // reset zoom/pan on previous item


          if (_currZoomElementStyle && Math.abs(_indexDiff) === 1) {
            var prevItem = _getItemAt(_prevItemIndex);

            if (prevItem.initialZoomLevel !== _currZoomLevel) {
              _calculateItemSize(prevItem, _viewportSize);

              _setImageSize(prevItem);

              _applyZoomPanToItem(prevItem);
            }
          } // reset diff after update


          _indexDiff = 0;
          self.updateCurrZoomItem();
          _prevItemIndex = _currentItemIndex;

          _shout('afterChange');
        },
        updateSize: function updateSize(force) {
          if (!_isFixedPosition && _options.modal) {
            var windowScrollY = framework.getScrollY();

            if (_currentWindowScrollY !== windowScrollY) {
              template.style.top = windowScrollY + 'px';
              _currentWindowScrollY = windowScrollY;
            }

            if (!force && _windowVisibleSize.x === window.innerWidth && _windowVisibleSize.y === window.innerHeight) {
              return;
            }

            _windowVisibleSize.x = window.innerWidth;
            _windowVisibleSize.y = window.innerHeight; //template.style.width = _windowVisibleSize.x + 'px';

            template.style.height = _windowVisibleSize.y + 'px';
          }

          _viewportSize.x = self.scrollWrap.clientWidth;
          _viewportSize.y = self.scrollWrap.clientHeight;

          _updatePageScrollOffset();

          _slideSize.x = _viewportSize.x + Math.round(_viewportSize.x * _options.spacing);
          _slideSize.y = _viewportSize.y;

          _moveMainScroll(_slideSize.x * _currPositionIndex);

          _shout('beforeResize'); // even may be used for example to switch image sources
          // don't re-calculate size on inital size update


          if (_containerShiftIndex !== undefined) {
            var holder, item, hIndex;

            for (var i = 0; i < NUM_HOLDERS; i++) {
              holder = _itemHolders[i];

              _setTranslateX((i + _containerShiftIndex) * _slideSize.x, holder.el.style);

              hIndex = _currentItemIndex + i - 1;

              if (_options.loop && _getNumItems() > 2) {
                hIndex = _getLoopedId(hIndex);
              } // update zoom level on items and refresh source (if needsUpdate)


              item = _getItemAt(hIndex); // re-render gallery item if `needsUpdate`,
              // or doesn't have `bounds` (entirely new slide object)

              if (item && (_itemsNeedUpdate || item.needsUpdate || !item.bounds)) {
                self.cleanSlide(item);
                self.setContent(holder, hIndex); // if "center" slide

                if (i === 1) {
                  self.currItem = item;
                  self.updateCurrZoomItem(true);
                }

                item.needsUpdate = false;
              } else if (holder.index === -1 && hIndex >= 0) {
                // add content first time
                self.setContent(holder, hIndex);
              }

              if (item && item.container) {
                _calculateItemSize(item, _viewportSize);

                _setImageSize(item);

                _applyZoomPanToItem(item);
              }
            }

            _itemsNeedUpdate = false;
          }

          _startZoomLevel = _currZoomLevel = self.currItem.initialZoomLevel;
          _currPanBounds = self.currItem.bounds;

          if (_currPanBounds) {
            _panOffset.x = _currPanBounds.center.x;
            _panOffset.y = _currPanBounds.center.y;

            _applyCurrentZoomPan(true);
          }

          _shout('resize');
        },
        // Zoom current item to
        zoomTo: function zoomTo(destZoomLevel, centerPoint, speed, easingFn, updateFn) {
          /*
          	if(destZoomLevel === 'fit') {
          		destZoomLevel = self.currItem.fitRatio;
          	} else if(destZoomLevel === 'fill') {
          		destZoomLevel = self.currItem.fillRatio;
          	}
          */
          if (centerPoint) {
            _startZoomLevel = _currZoomLevel;
            _midZoomPoint.x = Math.abs(centerPoint.x) - _panOffset.x;
            _midZoomPoint.y = Math.abs(centerPoint.y) - _panOffset.y;

            _equalizePoints(_startPanOffset, _panOffset);
          }

          var destPanBounds = _calculatePanBounds(destZoomLevel, false),
              destPanOffset = {};

          _modifyDestPanOffset('x', destPanBounds, destPanOffset, destZoomLevel);

          _modifyDestPanOffset('y', destPanBounds, destPanOffset, destZoomLevel);

          var initialZoomLevel = _currZoomLevel;
          var initialPanOffset = {
            x: _panOffset.x,
            y: _panOffset.y
          };

          _roundPoint(destPanOffset);

          var onUpdate = function onUpdate(now) {
            if (now === 1) {
              _currZoomLevel = destZoomLevel;
              _panOffset.x = destPanOffset.x;
              _panOffset.y = destPanOffset.y;
            } else {
              _currZoomLevel = (destZoomLevel - initialZoomLevel) * now + initialZoomLevel;
              _panOffset.x = (destPanOffset.x - initialPanOffset.x) * now + initialPanOffset.x;
              _panOffset.y = (destPanOffset.y - initialPanOffset.y) * now + initialPanOffset.y;
            }

            if (updateFn) {
              updateFn(now);
            }

            _applyCurrentZoomPan(now === 1);
          };

          if (speed) {
            _animateProp('customZoomTo', 0, 1, speed, easingFn || framework.easing.sine.inOut, onUpdate);
          } else {
            onUpdate(1);
          }
        }
      };
      /*>>core*/

      /*>>gestures*/

      /**
       * Mouse/touch/pointer event handlers.
       * 
       * separated from @core.js for readability
       */

      var MIN_SWIPE_DISTANCE = 30,
          DIRECTION_CHECK_OFFSET = 10; // amount of pixels to drag to determine direction of swipe

      var _gestureStartTime,
          _gestureCheckSpeedTime,
          // pool of objects that are used during dragging of zooming
      p = {},
          // first point
      p2 = {},
          // second point (for zoom gesture)
      delta = {},
          _currPoint = {},
          _startPoint = {},
          _currPointers = [],
          _startMainScrollPos = {},
          _releaseAnimData,
          _posPoints = [],
          // array of points during dragging, used to determine type of gesture
      _tempPoint = {},
          _isZoomingIn,
          _verticalDragInitiated,
          _oldAndroidTouchEndTimeout,
          _currZoomedItemIndex = 0,
          _centerPoint = _getEmptyPoint(),
          _lastReleaseTime = 0,
          _isDragging,
          // at least one pointer is down
      _isMultitouch,
          // at least two _pointers are down
      _zoomStarted,
          // zoom level changed during zoom gesture
      _moved,
          _dragAnimFrame,
          _mainScrollShifted,
          _currentPoints,
          // array of current touch points
      _isZooming,
          _currPointsDistance,
          _startPointsDistance,
          _currPanBounds,
          _mainScrollPos = _getEmptyPoint(),
          _currZoomElementStyle,
          _mainScrollAnimating,
          // true, if animation after swipe gesture is running
      _midZoomPoint = _getEmptyPoint(),
          _currCenterPoint = _getEmptyPoint(),
          _direction,
          _isFirstMove,
          _opacityChanged,
          _bgOpacity,
          _wasOverInitialZoom,
          _isEqualPoints = function _isEqualPoints(p1, p2) {
        return p1.x === p2.x && p1.y === p2.y;
      },
          _isNearbyPoints = function _isNearbyPoints(touch0, touch1) {
        return Math.abs(touch0.x - touch1.x) < DOUBLE_TAP_RADIUS && Math.abs(touch0.y - touch1.y) < DOUBLE_TAP_RADIUS;
      },
          _calculatePointsDistance = function _calculatePointsDistance(p1, p2) {
        _tempPoint.x = Math.abs(p1.x - p2.x);
        _tempPoint.y = Math.abs(p1.y - p2.y);
        return Math.sqrt(_tempPoint.x * _tempPoint.x + _tempPoint.y * _tempPoint.y);
      },
          _stopDragUpdateLoop = function _stopDragUpdateLoop() {
        if (_dragAnimFrame) {
          _cancelAF(_dragAnimFrame);

          _dragAnimFrame = null;
        }
      },
          _dragUpdateLoop = function _dragUpdateLoop() {
        if (_isDragging) {
          _dragAnimFrame = _requestAF(_dragUpdateLoop);

          _renderMovement();
        }
      },
          _canPan = function _canPan() {
        return !(_options.scaleMode === 'fit' && _currZoomLevel === self.currItem.initialZoomLevel);
      },
          // find the closest parent DOM element
      _closestElement = function _closestElement(el, fn) {
        if (!el || el === document) {
          return false;
        } // don't search elements above pswp__scroll-wrap


        if (el.getAttribute('class') && el.getAttribute('class').indexOf('pswp__scroll-wrap') > -1) {
          return false;
        }

        if (fn(el)) {
          return el;
        }

        return _closestElement(el.parentNode, fn);
      },
          _preventObj = {},
          _preventDefaultEventBehaviour = function _preventDefaultEventBehaviour(e, isDown) {
        _preventObj.prevent = !_closestElement(e.target, _options.isClickableElement);

        _shout('preventDragEvent', e, isDown, _preventObj);

        return _preventObj.prevent;
      },
          _convertTouchToPoint = function _convertTouchToPoint(touch, p) {
        p.x = touch.pageX;
        p.y = touch.pageY;
        p.id = touch.identifier;
        return p;
      },
          _findCenterOfPoints = function _findCenterOfPoints(p1, p2, pCenter) {
        pCenter.x = (p1.x + p2.x) * 0.5;
        pCenter.y = (p1.y + p2.y) * 0.5;
      },
          _pushPosPoint = function _pushPosPoint(time, x, y) {
        if (time - _gestureCheckSpeedTime > 50) {
          var o = _posPoints.length > 2 ? _posPoints.shift() : {};
          o.x = x;
          o.y = y;

          _posPoints.push(o);

          _gestureCheckSpeedTime = time;
        }
      },
          _calculateVerticalDragOpacityRatio = function _calculateVerticalDragOpacityRatio() {
        var yOffset = _panOffset.y - self.currItem.initialPosition.y; // difference between initial and current position

        return 1 - Math.abs(yOffset / (_viewportSize.y / 2));
      },
          // points pool, reused during touch events
      _ePoint1 = {},
          _ePoint2 = {},
          _tempPointsArr = [],
          _tempCounter,
          _getTouchPoints = function _getTouchPoints(e) {
        // clean up previous points, without recreating array
        while (_tempPointsArr.length > 0) {
          _tempPointsArr.pop();
        }

        if (!_pointerEventEnabled) {
          if (e.type.indexOf('touch') > -1) {
            if (e.touches && e.touches.length > 0) {
              _tempPointsArr[0] = _convertTouchToPoint(e.touches[0], _ePoint1);

              if (e.touches.length > 1) {
                _tempPointsArr[1] = _convertTouchToPoint(e.touches[1], _ePoint2);
              }
            }
          } else {
            _ePoint1.x = e.pageX;
            _ePoint1.y = e.pageY;
            _ePoint1.id = '';
            _tempPointsArr[0] = _ePoint1; //_ePoint1;
          }
        } else {
          _tempCounter = 0; // we can use forEach, as pointer events are supported only in modern browsers

          _currPointers.forEach(function (p) {
            if (_tempCounter === 0) {
              _tempPointsArr[0] = p;
            } else if (_tempCounter === 1) {
              _tempPointsArr[1] = p;
            }

            _tempCounter++;
          });
        }

        return _tempPointsArr;
      },
          _panOrMoveMainScroll = function _panOrMoveMainScroll(axis, delta) {
        var panFriction,
            overDiff = 0,
            newOffset = _panOffset[axis] + delta[axis],
            startOverDiff,
            dir = delta[axis] > 0,
            newMainScrollPosition = _mainScrollPos.x + delta.x,
            mainScrollDiff = _mainScrollPos.x - _startMainScrollPos.x,
            newPanPos,
            newMainScrollPos; // calculate fdistance over the bounds and friction

        if (newOffset > _currPanBounds.min[axis] || newOffset < _currPanBounds.max[axis]) {
          panFriction = _options.panEndFriction; // Linear increasing of friction, so at 1/4 of viewport it's at max value. 
          // Looks not as nice as was expected. Left for history.
          // panFriction = (1 - (_panOffset[axis] + delta[axis] + panBounds.min[axis]) / (_viewportSize[axis] / 4) );
        } else {
          panFriction = 1;
        }

        newOffset = _panOffset[axis] + delta[axis] * panFriction; // move main scroll or start panning

        if (_options.allowPanToNext || _currZoomLevel === self.currItem.initialZoomLevel) {
          if (!_currZoomElementStyle) {
            newMainScrollPos = newMainScrollPosition;
          } else if (_direction === 'h' && axis === 'x' && !_zoomStarted) {
            if (dir) {
              if (newOffset > _currPanBounds.min[axis]) {
                panFriction = _options.panEndFriction;
                overDiff = _currPanBounds.min[axis] - newOffset;
                startOverDiff = _currPanBounds.min[axis] - _startPanOffset[axis];
              } // drag right


              if ((startOverDiff <= 0 || mainScrollDiff < 0) && _getNumItems() > 1) {
                newMainScrollPos = newMainScrollPosition;

                if (mainScrollDiff < 0 && newMainScrollPosition > _startMainScrollPos.x) {
                  newMainScrollPos = _startMainScrollPos.x;
                }
              } else {
                if (_currPanBounds.min.x !== _currPanBounds.max.x) {
                  newPanPos = newOffset;
                }
              }
            } else {
              if (newOffset < _currPanBounds.max[axis]) {
                panFriction = _options.panEndFriction;
                overDiff = newOffset - _currPanBounds.max[axis];
                startOverDiff = _startPanOffset[axis] - _currPanBounds.max[axis];
              }

              if ((startOverDiff <= 0 || mainScrollDiff > 0) && _getNumItems() > 1) {
                newMainScrollPos = newMainScrollPosition;

                if (mainScrollDiff > 0 && newMainScrollPosition < _startMainScrollPos.x) {
                  newMainScrollPos = _startMainScrollPos.x;
                }
              } else {
                if (_currPanBounds.min.x !== _currPanBounds.max.x) {
                  newPanPos = newOffset;
                }
              }
            } //

          }

          if (axis === 'x') {
            if (newMainScrollPos !== undefined) {
              _moveMainScroll(newMainScrollPos, true);

              if (newMainScrollPos === _startMainScrollPos.x) {
                _mainScrollShifted = false;
              } else {
                _mainScrollShifted = true;
              }
            }

            if (_currPanBounds.min.x !== _currPanBounds.max.x) {
              if (newPanPos !== undefined) {
                _panOffset.x = newPanPos;
              } else if (!_mainScrollShifted) {
                _panOffset.x += delta.x * panFriction;
              }
            }

            return newMainScrollPos !== undefined;
          }
        }

        if (!_mainScrollAnimating) {
          if (!_mainScrollShifted) {
            if (_currZoomLevel > self.currItem.fitRatio) {
              _panOffset[axis] += delta[axis] * panFriction;
            }
          }
        }
      },
          // Pointerdown/touchstart/mousedown handler
      _onDragStart = function _onDragStart(e) {
        // Allow dragging only via left mouse button.
        // As this handler is not added in IE8 - we ignore e.which
        // 
        // http://www.quirksmode.org/js/events_properties.html
        // https://developer.mozilla.org/en-US/docs/Web/API/event.button
        if (e.type === 'mousedown' && e.button > 0) {
          return;
        }

        if (_initialZoomRunning) {
          e.preventDefault();
          return;
        }

        if (_oldAndroidTouchEndTimeout && e.type === 'mousedown') {
          return;
        }

        if (_preventDefaultEventBehaviour(e, true)) {
          e.preventDefault();
        }

        _shout('pointerDown');

        if (_pointerEventEnabled) {
          var pointerIndex = framework.arraySearch(_currPointers, e.pointerId, 'id');

          if (pointerIndex < 0) {
            pointerIndex = _currPointers.length;
          }

          _currPointers[pointerIndex] = {
            x: e.pageX,
            y: e.pageY,
            id: e.pointerId
          };
        }

        var startPointsList = _getTouchPoints(e),
            numPoints = startPointsList.length;

        _currentPoints = null;

        _stopAllAnimations(); // init drag


        if (!_isDragging || numPoints === 1) {
          _isDragging = _isFirstMove = true;
          framework.bind(window, _upMoveEvents, self);
          _isZoomingIn = _wasOverInitialZoom = _opacityChanged = _verticalDragInitiated = _mainScrollShifted = _moved = _isMultitouch = _zoomStarted = false;
          _direction = null;

          _shout('firstTouchStart', startPointsList);

          _equalizePoints(_startPanOffset, _panOffset);

          _currPanDist.x = _currPanDist.y = 0;

          _equalizePoints(_currPoint, startPointsList[0]);

          _equalizePoints(_startPoint, _currPoint); //_equalizePoints(_startMainScrollPos, _mainScrollPos);


          _startMainScrollPos.x = _slideSize.x * _currPositionIndex;
          _posPoints = [{
            x: _currPoint.x,
            y: _currPoint.y
          }];
          _gestureCheckSpeedTime = _gestureStartTime = _getCurrentTime(); //_mainScrollAnimationEnd(true);

          _calculatePanBounds(_currZoomLevel, true); // Start rendering


          _stopDragUpdateLoop();

          _dragUpdateLoop();
        } // init zoom


        if (!_isZooming && numPoints > 1 && !_mainScrollAnimating && !_mainScrollShifted) {
          _startZoomLevel = _currZoomLevel;
          _zoomStarted = false; // true if zoom changed at least once

          _isZooming = _isMultitouch = true;
          _currPanDist.y = _currPanDist.x = 0;

          _equalizePoints(_startPanOffset, _panOffset);

          _equalizePoints(p, startPointsList[0]);

          _equalizePoints(p2, startPointsList[1]);

          _findCenterOfPoints(p, p2, _currCenterPoint);

          _midZoomPoint.x = Math.abs(_currCenterPoint.x) - _panOffset.x;
          _midZoomPoint.y = Math.abs(_currCenterPoint.y) - _panOffset.y;
          _currPointsDistance = _startPointsDistance = _calculatePointsDistance(p, p2);
        }
      },
          // Pointermove/touchmove/mousemove handler
      _onDragMove = function _onDragMove(e) {
        e.preventDefault();

        if (_pointerEventEnabled) {
          var pointerIndex = framework.arraySearch(_currPointers, e.pointerId, 'id');

          if (pointerIndex > -1) {
            var p = _currPointers[pointerIndex];
            p.x = e.pageX;
            p.y = e.pageY;
          }
        }

        if (_isDragging) {
          var touchesList = _getTouchPoints(e);

          if (!_direction && !_moved && !_isZooming) {
            if (_mainScrollPos.x !== _slideSize.x * _currPositionIndex) {
              // if main scroll position is shifted – direction is always horizontal
              _direction = 'h';
            } else {
              var diff = Math.abs(touchesList[0].x - _currPoint.x) - Math.abs(touchesList[0].y - _currPoint.y); // check the direction of movement

              if (Math.abs(diff) >= DIRECTION_CHECK_OFFSET) {
                _direction = diff > 0 ? 'h' : 'v';
                _currentPoints = touchesList;
              }
            }
          } else {
            _currentPoints = touchesList;
          }
        }
      },
          // 
      _renderMovement = function _renderMovement() {
        if (!_currentPoints) {
          return;
        }

        var numPoints = _currentPoints.length;

        if (numPoints === 0) {
          return;
        }

        _equalizePoints(p, _currentPoints[0]);

        delta.x = p.x - _currPoint.x;
        delta.y = p.y - _currPoint.y;

        if (_isZooming && numPoints > 1) {
          // Handle behaviour for more than 1 point
          _currPoint.x = p.x;
          _currPoint.y = p.y; // check if one of two points changed

          if (!delta.x && !delta.y && _isEqualPoints(_currentPoints[1], p2)) {
            return;
          }

          _equalizePoints(p2, _currentPoints[1]);

          if (!_zoomStarted) {
            _zoomStarted = true;

            _shout('zoomGestureStarted');
          } // Distance between two points


          var pointsDistance = _calculatePointsDistance(p, p2);

          var zoomLevel = _calculateZoomLevel(pointsDistance); // slightly over the of initial zoom level


          if (zoomLevel > self.currItem.initialZoomLevel + self.currItem.initialZoomLevel / 15) {
            _wasOverInitialZoom = true;
          } // Apply the friction if zoom level is out of the bounds


          var zoomFriction = 1,
              minZoomLevel = _getMinZoomLevel(),
              maxZoomLevel = _getMaxZoomLevel();

          if (zoomLevel < minZoomLevel) {
            if (_options.pinchToClose && !_wasOverInitialZoom && _startZoomLevel <= self.currItem.initialZoomLevel) {
              // fade out background if zooming out
              var minusDiff = minZoomLevel - zoomLevel;
              var percent = 1 - minusDiff / (minZoomLevel / 1.2);

              _applyBgOpacity(percent);

              _shout('onPinchClose', percent);

              _opacityChanged = true;
            } else {
              zoomFriction = (minZoomLevel - zoomLevel) / minZoomLevel;

              if (zoomFriction > 1) {
                zoomFriction = 1;
              }

              zoomLevel = minZoomLevel - zoomFriction * (minZoomLevel / 3);
            }
          } else if (zoomLevel > maxZoomLevel) {
            // 1.5 - extra zoom level above the max. E.g. if max is x6, real max 6 + 1.5 = 7.5
            zoomFriction = (zoomLevel - maxZoomLevel) / (minZoomLevel * 6);

            if (zoomFriction > 1) {
              zoomFriction = 1;
            }

            zoomLevel = maxZoomLevel + zoomFriction * minZoomLevel;
          }

          if (zoomFriction < 0) {
            zoomFriction = 0;
          } // distance between touch points after friction is applied


          _currPointsDistance = pointsDistance; // _centerPoint - The point in the middle of two pointers

          _findCenterOfPoints(p, p2, _centerPoint); // paning with two pointers pressed


          _currPanDist.x += _centerPoint.x - _currCenterPoint.x;
          _currPanDist.y += _centerPoint.y - _currCenterPoint.y;

          _equalizePoints(_currCenterPoint, _centerPoint);

          _panOffset.x = _calculatePanOffset('x', zoomLevel);
          _panOffset.y = _calculatePanOffset('y', zoomLevel);
          _isZoomingIn = zoomLevel > _currZoomLevel;
          _currZoomLevel = zoomLevel;

          _applyCurrentZoomPan();
        } else {
          // handle behaviour for one point (dragging or panning)
          if (!_direction) {
            return;
          }

          if (_isFirstMove) {
            _isFirstMove = false; // subtract drag distance that was used during the detection direction  

            if (Math.abs(delta.x) >= DIRECTION_CHECK_OFFSET) {
              delta.x -= _currentPoints[0].x - _startPoint.x;
            }

            if (Math.abs(delta.y) >= DIRECTION_CHECK_OFFSET) {
              delta.y -= _currentPoints[0].y - _startPoint.y;
            }
          }

          _currPoint.x = p.x;
          _currPoint.y = p.y; // do nothing if pointers position hasn't changed

          if (delta.x === 0 && delta.y === 0) {
            return;
          }

          if (_direction === 'v' && _options.closeOnVerticalDrag) {
            if (!_canPan()) {
              _currPanDist.y += delta.y;
              _panOffset.y += delta.y;

              var opacityRatio = _calculateVerticalDragOpacityRatio();

              _verticalDragInitiated = true;

              _shout('onVerticalDrag', opacityRatio);

              _applyBgOpacity(opacityRatio);

              _applyCurrentZoomPan();

              return;
            }
          }

          _pushPosPoint(_getCurrentTime(), p.x, p.y);

          _moved = true;
          _currPanBounds = self.currItem.bounds;

          var mainScrollChanged = _panOrMoveMainScroll('x', delta);

          if (!mainScrollChanged) {
            _panOrMoveMainScroll('y', delta);

            _roundPoint(_panOffset);

            _applyCurrentZoomPan();
          }
        }
      },
          // Pointerup/pointercancel/touchend/touchcancel/mouseup event handler
      _onDragRelease = function _onDragRelease(e) {
        if (_features.isOldAndroid) {
          if (_oldAndroidTouchEndTimeout && e.type === 'mouseup') {
            return;
          } // on Android (v4.1, 4.2, 4.3 & possibly older) 
          // ghost mousedown/up event isn't preventable via e.preventDefault,
          // which causes fake mousedown event
          // so we block mousedown/up for 600ms


          if (e.type.indexOf('touch') > -1) {
            clearTimeout(_oldAndroidTouchEndTimeout);
            _oldAndroidTouchEndTimeout = setTimeout(function () {
              _oldAndroidTouchEndTimeout = 0;
            }, 600);
          }
        }

        _shout('pointerUp');

        if (_preventDefaultEventBehaviour(e, false)) {
          e.preventDefault();
        }

        var releasePoint;

        if (_pointerEventEnabled) {
          var pointerIndex = framework.arraySearch(_currPointers, e.pointerId, 'id');

          if (pointerIndex > -1) {
            releasePoint = _currPointers.splice(pointerIndex, 1)[0];

            if (navigator.msPointerEnabled) {
              var MSPOINTER_TYPES = {
                4: 'mouse',
                // event.MSPOINTER_TYPE_MOUSE
                2: 'touch',
                // event.MSPOINTER_TYPE_TOUCH 
                3: 'pen' // event.MSPOINTER_TYPE_PEN

              };
              releasePoint.type = MSPOINTER_TYPES[e.pointerType];

              if (!releasePoint.type) {
                releasePoint.type = e.pointerType || 'mouse';
              }
            } else {
              releasePoint.type = e.pointerType || 'mouse';
            }
          }
        }

        var touchList = _getTouchPoints(e),
            gestureType,
            numPoints = touchList.length;

        if (e.type === 'mouseup') {
          numPoints = 0;
        } // Do nothing if there were 3 touch points or more


        if (numPoints === 2) {
          _currentPoints = null;
          return true;
        } // if second pointer released


        if (numPoints === 1) {
          _equalizePoints(_startPoint, touchList[0]);
        } // pointer hasn't moved, send "tap release" point


        if (numPoints === 0 && !_direction && !_mainScrollAnimating) {
          if (!releasePoint) {
            if (e.type === 'mouseup') {
              releasePoint = {
                x: e.pageX,
                y: e.pageY,
                type: 'mouse'
              };
            } else if (e.changedTouches && e.changedTouches[0]) {
              releasePoint = {
                x: e.changedTouches[0].pageX,
                y: e.changedTouches[0].pageY,
                type: 'touch'
              };
            }
          }

          _shout('touchRelease', e, releasePoint);
        } // Difference in time between releasing of two last touch points (zoom gesture)


        var releaseTimeDiff = -1; // Gesture completed, no pointers left

        if (numPoints === 0) {
          _isDragging = false;
          framework.unbind(window, _upMoveEvents, self);

          _stopDragUpdateLoop();

          if (_isZooming) {
            // Two points released at the same time
            releaseTimeDiff = 0;
          } else if (_lastReleaseTime !== -1) {
            releaseTimeDiff = _getCurrentTime() - _lastReleaseTime;
          }
        }

        _lastReleaseTime = numPoints === 1 ? _getCurrentTime() : -1;

        if (releaseTimeDiff !== -1 && releaseTimeDiff < 150) {
          gestureType = 'zoom';
        } else {
          gestureType = 'swipe';
        }

        if (_isZooming && numPoints < 2) {
          _isZooming = false; // Only second point released

          if (numPoints === 1) {
            gestureType = 'zoomPointerUp';
          }

          _shout('zoomGestureEnded');
        }

        _currentPoints = null;

        if (!_moved && !_zoomStarted && !_mainScrollAnimating && !_verticalDragInitiated) {
          // nothing to animate
          return;
        }

        _stopAllAnimations();

        if (!_releaseAnimData) {
          _releaseAnimData = _initDragReleaseAnimationData();
        }

        _releaseAnimData.calculateSwipeSpeed('x');

        if (_verticalDragInitiated) {
          var opacityRatio = _calculateVerticalDragOpacityRatio();

          if (opacityRatio < _options.verticalDragRange) {
            self.close();
          } else {
            var initalPanY = _panOffset.y,
                initialBgOpacity = _bgOpacity;

            _animateProp('verticalDrag', 0, 1, 300, framework.easing.cubic.out, function (now) {
              _panOffset.y = (self.currItem.initialPosition.y - initalPanY) * now + initalPanY;

              _applyBgOpacity((1 - initialBgOpacity) * now + initialBgOpacity);

              _applyCurrentZoomPan();
            });

            _shout('onVerticalDrag', 1);
          }

          return;
        } // main scroll 


        if ((_mainScrollShifted || _mainScrollAnimating) && numPoints === 0) {
          var itemChanged = _finishSwipeMainScrollGesture(gestureType, _releaseAnimData);

          if (itemChanged) {
            return;
          }

          gestureType = 'zoomPointerUp';
        } // prevent zoom/pan animation when main scroll animation runs


        if (_mainScrollAnimating) {
          return;
        } // Complete simple zoom gesture (reset zoom level if it's out of the bounds)  


        if (gestureType !== 'swipe') {
          _completeZoomGesture();

          return;
        } // Complete pan gesture if main scroll is not shifted, and it's possible to pan current image


        if (!_mainScrollShifted && _currZoomLevel > self.currItem.fitRatio) {
          _completePanGesture(_releaseAnimData);
        }
      },
          // Returns object with data about gesture
      // It's created only once and then reused
      _initDragReleaseAnimationData = function _initDragReleaseAnimationData() {
        // temp local vars
        var lastFlickDuration, tempReleasePos; // s = this

        var s = {
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
          calculateSwipeSpeed: function calculateSwipeSpeed(axis) {
            if (_posPoints.length > 1) {
              lastFlickDuration = _getCurrentTime() - _gestureCheckSpeedTime + 50;
              tempReleasePos = _posPoints[_posPoints.length - 2][axis];
            } else {
              lastFlickDuration = _getCurrentTime() - _gestureStartTime; // total gesture duration

              tempReleasePos = _startPoint[axis];
            }

            s.lastFlickOffset[axis] = _currPoint[axis] - tempReleasePos;
            s.lastFlickDist[axis] = Math.abs(s.lastFlickOffset[axis]);

            if (s.lastFlickDist[axis] > 20) {
              s.lastFlickSpeed[axis] = s.lastFlickOffset[axis] / lastFlickDuration;
            } else {
              s.lastFlickSpeed[axis] = 0;
            }

            if (Math.abs(s.lastFlickSpeed[axis]) < 0.1) {
              s.lastFlickSpeed[axis] = 0;
            }

            s.slowDownRatio[axis] = 0.95;
            s.slowDownRatioReverse[axis] = 1 - s.slowDownRatio[axis];
            s.speedDecelerationRatio[axis] = 1;
          },
          calculateOverBoundsAnimOffset: function calculateOverBoundsAnimOffset(axis, speed) {
            if (!s.backAnimStarted[axis]) {
              if (_panOffset[axis] > _currPanBounds.min[axis]) {
                s.backAnimDestination[axis] = _currPanBounds.min[axis];
              } else if (_panOffset[axis] < _currPanBounds.max[axis]) {
                s.backAnimDestination[axis] = _currPanBounds.max[axis];
              }

              if (s.backAnimDestination[axis] !== undefined) {
                s.slowDownRatio[axis] = 0.7;
                s.slowDownRatioReverse[axis] = 1 - s.slowDownRatio[axis];

                if (s.speedDecelerationRatioAbs[axis] < 0.05) {
                  s.lastFlickSpeed[axis] = 0;
                  s.backAnimStarted[axis] = true;

                  _animateProp('bounceZoomPan' + axis, _panOffset[axis], s.backAnimDestination[axis], speed || 300, framework.easing.sine.out, function (pos) {
                    _panOffset[axis] = pos;

                    _applyCurrentZoomPan();
                  });
                }
              }
            }
          },
          // Reduces the speed by slowDownRatio (per 10ms)
          calculateAnimOffset: function calculateAnimOffset(axis) {
            if (!s.backAnimStarted[axis]) {
              s.speedDecelerationRatio[axis] = s.speedDecelerationRatio[axis] * (s.slowDownRatio[axis] + s.slowDownRatioReverse[axis] - s.slowDownRatioReverse[axis] * s.timeDiff / 10);
              s.speedDecelerationRatioAbs[axis] = Math.abs(s.lastFlickSpeed[axis] * s.speedDecelerationRatio[axis]);
              s.distanceOffset[axis] = s.lastFlickSpeed[axis] * s.speedDecelerationRatio[axis] * s.timeDiff;
              _panOffset[axis] += s.distanceOffset[axis];
            }
          },
          panAnimLoop: function panAnimLoop() {
            if (_animations.zoomPan) {
              _animations.zoomPan.raf = _requestAF(s.panAnimLoop);
              s.now = _getCurrentTime();
              s.timeDiff = s.now - s.lastNow;
              s.lastNow = s.now;
              s.calculateAnimOffset('x');
              s.calculateAnimOffset('y');

              _applyCurrentZoomPan();

              s.calculateOverBoundsAnimOffset('x');
              s.calculateOverBoundsAnimOffset('y');

              if (s.speedDecelerationRatioAbs.x < 0.05 && s.speedDecelerationRatioAbs.y < 0.05) {
                // round pan position
                _panOffset.x = Math.round(_panOffset.x);
                _panOffset.y = Math.round(_panOffset.y);

                _applyCurrentZoomPan();

                _stopAnimation('zoomPan');

                return;
              }
            }
          }
        };
        return s;
      },
          _completePanGesture = function _completePanGesture(animData) {
        // calculate swipe speed for Y axis (paanning)
        animData.calculateSwipeSpeed('y');
        _currPanBounds = self.currItem.bounds;
        animData.backAnimDestination = {};
        animData.backAnimStarted = {}; // Avoid acceleration animation if speed is too low

        if (Math.abs(animData.lastFlickSpeed.x) <= 0.05 && Math.abs(animData.lastFlickSpeed.y) <= 0.05) {
          animData.speedDecelerationRatioAbs.x = animData.speedDecelerationRatioAbs.y = 0; // Run pan drag release animation. E.g. if you drag image and release finger without momentum.

          animData.calculateOverBoundsAnimOffset('x');
          animData.calculateOverBoundsAnimOffset('y');
          return true;
        } // Animation loop that controls the acceleration after pan gesture ends


        _registerStartAnimation('zoomPan');

        animData.lastNow = _getCurrentTime();
        animData.panAnimLoop();
      },
          _finishSwipeMainScrollGesture = function _finishSwipeMainScrollGesture(gestureType, _releaseAnimData) {
        var itemChanged;

        if (!_mainScrollAnimating) {
          _currZoomedItemIndex = _currentItemIndex;
        }

        var itemsDiff;

        if (gestureType === 'swipe') {
          var totalShiftDist = _currPoint.x - _startPoint.x,
              isFastLastFlick = _releaseAnimData.lastFlickDist.x < 10; // if container is shifted for more than MIN_SWIPE_DISTANCE, 
          // and last flick gesture was in right direction

          if (totalShiftDist > MIN_SWIPE_DISTANCE && (isFastLastFlick || _releaseAnimData.lastFlickOffset.x > 20)) {
            // go to prev item
            itemsDiff = -1;
          } else if (totalShiftDist < -MIN_SWIPE_DISTANCE && (isFastLastFlick || _releaseAnimData.lastFlickOffset.x < -20)) {
            // go to next item
            itemsDiff = 1;
          }
        }

        var nextCircle;

        if (itemsDiff) {
          _currentItemIndex += itemsDiff;

          if (_currentItemIndex < 0) {
            _currentItemIndex = _options.loop ? _getNumItems() - 1 : 0;
            nextCircle = true;
          } else if (_currentItemIndex >= _getNumItems()) {
            _currentItemIndex = _options.loop ? 0 : _getNumItems() - 1;
            nextCircle = true;
          }

          if (!nextCircle || _options.loop) {
            _indexDiff += itemsDiff;
            _currPositionIndex -= itemsDiff;
            itemChanged = true;
          }
        }

        var animateToX = _slideSize.x * _currPositionIndex;
        var animateToDist = Math.abs(animateToX - _mainScrollPos.x);
        var finishAnimDuration;

        if (!itemChanged && animateToX > _mainScrollPos.x !== _releaseAnimData.lastFlickSpeed.x > 0) {
          // "return to current" duration, e.g. when dragging from slide 0 to -1
          finishAnimDuration = 333;
        } else {
          finishAnimDuration = Math.abs(_releaseAnimData.lastFlickSpeed.x) > 0 ? animateToDist / Math.abs(_releaseAnimData.lastFlickSpeed.x) : 333;
          finishAnimDuration = Math.min(finishAnimDuration, 400);
          finishAnimDuration = Math.max(finishAnimDuration, 250);
        }

        if (_currZoomedItemIndex === _currentItemIndex) {
          itemChanged = false;
        }

        _mainScrollAnimating = true;

        _shout('mainScrollAnimStart');

        _animateProp('mainScroll', _mainScrollPos.x, animateToX, finishAnimDuration, framework.easing.cubic.out, _moveMainScroll, function () {
          _stopAllAnimations();

          _mainScrollAnimating = false;
          _currZoomedItemIndex = -1;

          if (itemChanged || _currZoomedItemIndex !== _currentItemIndex) {
            self.updateCurrItem();
          }

          _shout('mainScrollAnimComplete');
        });

        if (itemChanged) {
          self.updateCurrItem(true);
        }

        return itemChanged;
      },
          _calculateZoomLevel = function _calculateZoomLevel(touchesDistance) {
        return 1 / _startPointsDistance * touchesDistance * _startZoomLevel;
      },
          // Resets zoom if it's out of bounds
      _completeZoomGesture = function _completeZoomGesture() {
        var destZoomLevel = _currZoomLevel,
            minZoomLevel = _getMinZoomLevel(),
            maxZoomLevel = _getMaxZoomLevel();

        if (_currZoomLevel < minZoomLevel) {
          destZoomLevel = minZoomLevel;
        } else if (_currZoomLevel > maxZoomLevel) {
          destZoomLevel = maxZoomLevel;
        }

        var destOpacity = 1,
            onUpdate,
            initialOpacity = _bgOpacity;

        if (_opacityChanged && !_isZoomingIn && !_wasOverInitialZoom && _currZoomLevel < minZoomLevel) {
          //_closedByScroll = true;
          self.close();
          return true;
        }

        if (_opacityChanged) {
          onUpdate = function onUpdate(now) {
            _applyBgOpacity((destOpacity - initialOpacity) * now + initialOpacity);
          };
        }

        self.zoomTo(destZoomLevel, 0, 200, framework.easing.cubic.out, onUpdate);
        return true;
      };

      _registerModule('Gestures', {
        publicMethods: {
          initGestures: function initGestures() {
            // helper function that builds touch/pointer/mouse events
            var addEventNames = function addEventNames(pref, down, move, up, cancel) {
              _dragStartEvent = pref + down;
              _dragMoveEvent = pref + move;
              _dragEndEvent = pref + up;

              if (cancel) {
                _dragCancelEvent = pref + cancel;
              } else {
                _dragCancelEvent = '';
              }
            };

            _pointerEventEnabled = _features.pointerEvent;

            if (_pointerEventEnabled && _features.touch) {
              // we don't need touch events, if browser supports pointer events
              _features.touch = false;
            }

            if (_pointerEventEnabled) {
              if (navigator.msPointerEnabled) {
                // IE10 pointer events are case-sensitive
                addEventNames('MSPointer', 'Down', 'Move', 'Up', 'Cancel');
              } else {
                addEventNames('pointer', 'down', 'move', 'up', 'cancel');
              }
            } else if (_features.touch) {
              addEventNames('touch', 'start', 'move', 'end', 'cancel');
              _likelyTouchDevice = true;
            } else {
              addEventNames('mouse', 'down', 'move', 'up');
            }

            _upMoveEvents = _dragMoveEvent + ' ' + _dragEndEvent + ' ' + _dragCancelEvent;
            _downEvents = _dragStartEvent;

            if (_pointerEventEnabled && !_likelyTouchDevice) {
              _likelyTouchDevice = navigator.maxTouchPoints > 1 || navigator.msMaxTouchPoints > 1;
            } // make variable public


            self.likelyTouchDevice = _likelyTouchDevice;
            _globalEventHandlers[_dragStartEvent] = _onDragStart;
            _globalEventHandlers[_dragMoveEvent] = _onDragMove;
            _globalEventHandlers[_dragEndEvent] = _onDragRelease; // the Kraken

            if (_dragCancelEvent) {
              _globalEventHandlers[_dragCancelEvent] = _globalEventHandlers[_dragEndEvent];
            } // Bind mouse events on device with detected hardware touch support, in case it supports multiple types of input.


            if (_features.touch) {
              _downEvents += ' mousedown';
              _upMoveEvents += ' mousemove mouseup';
              _globalEventHandlers.mousedown = _globalEventHandlers[_dragStartEvent];
              _globalEventHandlers.mousemove = _globalEventHandlers[_dragMoveEvent];
              _globalEventHandlers.mouseup = _globalEventHandlers[_dragEndEvent];
            }

            if (!_likelyTouchDevice) {
              // don't allow pan to next slide from zoomed state on Desktop
              _options.allowPanToNext = false;
            }
          }
        }
      });
      /*>>gestures*/

      /*>>show-hide-transition*/

      /**
       * show-hide-transition.js:
       *
       * Manages initial opening or closing transition.
       *
       * If you're not planning to use transition for gallery at all,
       * you may set options hideAnimationDuration and showAnimationDuration to 0,
       * and just delete startAnimation function.
       * 
       */


      var _showOrHideTimeout,
          _showOrHide = function _showOrHide(item, img, out, completeFn) {
        if (_showOrHideTimeout) {
          clearTimeout(_showOrHideTimeout);
        }

        _initialZoomRunning = true;
        _initialContentSet = true; // dimensions of small thumbnail {x:,y:,w:}.
        // Height is optional, as calculated based on large image.

        var thumbBounds;

        if (item.initialLayout) {
          thumbBounds = item.initialLayout;
          item.initialLayout = null;
        } else {
          thumbBounds = _options.getThumbBoundsFn && _options.getThumbBoundsFn(_currentItemIndex);
        }

        var duration = out ? _options.hideAnimationDuration : _options.showAnimationDuration;

        var onComplete = function onComplete() {
          _stopAnimation('initialZoom');

          if (!out) {
            _applyBgOpacity(1);

            if (img) {
              img.style.display = 'block';
            }

            framework.addClass(template, 'pswp--animated-in');

            _shout('initialZoom' + (out ? 'OutEnd' : 'InEnd'));
          } else {
            self.template.removeAttribute('style');
            self.bg.removeAttribute('style');
          }

          if (completeFn) {
            completeFn();
          }

          _initialZoomRunning = false;
        }; // if bounds aren't provided, just open gallery without animation


        if (!duration || !thumbBounds || thumbBounds.x === undefined) {
          _shout('initialZoom' + (out ? 'Out' : 'In'));

          _currZoomLevel = item.initialZoomLevel;

          _equalizePoints(_panOffset, item.initialPosition);

          _applyCurrentZoomPan();

          template.style.opacity = out ? 0 : 1;

          _applyBgOpacity(1);

          if (duration) {
            setTimeout(function () {
              onComplete();
            }, duration);
          } else {
            onComplete();
          }

          return;
        }

        var startAnimation = function startAnimation() {
          var closeWithRaf = _closedByScroll,
              fadeEverything = !self.currItem.src || self.currItem.loadError || _options.showHideOpacity; // apply hw-acceleration to image

          if (item.miniImg) {
            item.miniImg.style.webkitBackfaceVisibility = 'hidden';
          }

          if (!out) {
            _currZoomLevel = thumbBounds.w / item.w;
            _panOffset.x = thumbBounds.x;
            _panOffset.y = thumbBounds.y - _initalWindowScrollY;
            self[fadeEverything ? 'template' : 'bg'].style.opacity = 0.001;

            _applyCurrentZoomPan();
          }

          _registerStartAnimation('initialZoom');

          if (out && !closeWithRaf) {
            framework.removeClass(template, 'pswp--animated-in');
          }

          if (fadeEverything) {
            if (out) {
              framework[(closeWithRaf ? 'remove' : 'add') + 'Class'](template, 'pswp--animate_opacity');
            } else {
              setTimeout(function () {
                framework.addClass(template, 'pswp--animate_opacity');
              }, 30);
            }
          }

          _showOrHideTimeout = setTimeout(function () {
            _shout('initialZoom' + (out ? 'Out' : 'In'));

            if (!out) {
              // "in" animation always uses CSS transitions (instead of rAF).
              // CSS transition work faster here, 
              // as developer may also want to animate other things, 
              // like ui on top of sliding area, which can be animated just via CSS
              _currZoomLevel = item.initialZoomLevel;

              _equalizePoints(_panOffset, item.initialPosition);

              _applyCurrentZoomPan();

              _applyBgOpacity(1);

              if (fadeEverything) {
                template.style.opacity = 1;
              } else {
                _applyBgOpacity(1);
              }

              _showOrHideTimeout = setTimeout(onComplete, duration + 20);
            } else {
              // "out" animation uses rAF only when PhotoSwipe is closed by browser scroll, to recalculate position
              var destZoomLevel = thumbBounds.w / item.w,
                  initialPanOffset = {
                x: _panOffset.x,
                y: _panOffset.y
              },
                  initialZoomLevel = _currZoomLevel,
                  initalBgOpacity = _bgOpacity,
                  onUpdate = function onUpdate(now) {
                if (now === 1) {
                  _currZoomLevel = destZoomLevel;
                  _panOffset.x = thumbBounds.x;
                  _panOffset.y = thumbBounds.y - _currentWindowScrollY;
                } else {
                  _currZoomLevel = (destZoomLevel - initialZoomLevel) * now + initialZoomLevel;
                  _panOffset.x = (thumbBounds.x - initialPanOffset.x) * now + initialPanOffset.x;
                  _panOffset.y = (thumbBounds.y - _currentWindowScrollY - initialPanOffset.y) * now + initialPanOffset.y;
                }

                _applyCurrentZoomPan();

                if (fadeEverything) {
                  template.style.opacity = 1 - now;
                } else {
                  _applyBgOpacity(initalBgOpacity - now * initalBgOpacity);
                }
              };

              if (closeWithRaf) {
                _animateProp('initialZoom', 0, 1, duration, framework.easing.cubic.out, onUpdate, onComplete);
              } else {
                onUpdate(1);
                _showOrHideTimeout = setTimeout(onComplete, duration + 20);
              }
            }
          }, out ? 25 : 90); // Main purpose of this delay is to give browser time to paint and
          // create composite layers of PhotoSwipe UI parts (background, controls, caption, arrows).
          // Which avoids lag at the beginning of scale transition.
        };

        startAnimation();
      };
      /*>>show-hide-transition*/

      /*>>items-controller*/

      /**
      *
      * Controller manages gallery items, their dimensions, and their content.
      * 
      */


      var _items,
          _tempPanAreaSize = {},
          _imagesToAppendPool = [],
          _initialContentSet,
          _initialZoomRunning,
          _controllerDefaultOptions = {
        index: 0,
        errorMsg: '<div class="pswp__error-msg"><a href="%url%" target="_blank">The image</a> could not be loaded.</div>',
        forceProgressiveLoading: false,
        // TODO
        preload: [1, 1],
        getNumItemsFn: function getNumItemsFn() {
          return _items.length;
        }
      };

      var _getItemAt,
          _getNumItems,
          _getZeroBounds = function _getZeroBounds() {
        return {
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
        };
      },
          _calculateSingleItemPanBounds = function _calculateSingleItemPanBounds(item, realPanElementW, realPanElementH) {
        var bounds = item.bounds; // position of element when it's centered

        bounds.center.x = Math.round((_tempPanAreaSize.x - realPanElementW) / 2);
        bounds.center.y = Math.round((_tempPanAreaSize.y - realPanElementH) / 2) + item.vGap.top; // maximum pan position

        bounds.max.x = realPanElementW > _tempPanAreaSize.x ? Math.round(_tempPanAreaSize.x - realPanElementW) : bounds.center.x;
        bounds.max.y = realPanElementH > _tempPanAreaSize.y ? Math.round(_tempPanAreaSize.y - realPanElementH) + item.vGap.top : bounds.center.y; // minimum pan position

        bounds.min.x = realPanElementW > _tempPanAreaSize.x ? 0 : bounds.center.x;
        bounds.min.y = realPanElementH > _tempPanAreaSize.y ? item.vGap.top : bounds.center.y;
      },
          _calculateItemSize = function _calculateItemSize(item, viewportSize, zoomLevel) {
        if (item.src && !item.loadError) {
          var isInitial = !zoomLevel;

          if (isInitial) {
            if (!item.vGap) {
              item.vGap = {
                top: 0,
                bottom: 0
              };
            } // allows overriding vertical margin for individual items


            _shout('parseVerticalMargin', item);
          }

          _tempPanAreaSize.x = viewportSize.x;
          _tempPanAreaSize.y = viewportSize.y - item.vGap.top - item.vGap.bottom;

          if (isInitial) {
            var hRatio = _tempPanAreaSize.x / item.w;
            var vRatio = _tempPanAreaSize.y / item.h;
            item.fitRatio = hRatio < vRatio ? hRatio : vRatio; //item.fillRatio = hRatio > vRatio ? hRatio : vRatio;

            var scaleMode = _options.scaleMode;

            if (scaleMode === 'orig') {
              zoomLevel = 1;
            } else if (scaleMode === 'fit') {
              zoomLevel = item.fitRatio;
            }

            if (zoomLevel > 1) {
              zoomLevel = 1;
            }

            item.initialZoomLevel = zoomLevel;

            if (!item.bounds) {
              // reuse bounds object
              item.bounds = _getZeroBounds();
            }
          }

          if (!zoomLevel) {
            return;
          }

          _calculateSingleItemPanBounds(item, item.w * zoomLevel, item.h * zoomLevel);

          if (isInitial && zoomLevel === item.initialZoomLevel) {
            item.initialPosition = item.bounds.center;
          }

          return item.bounds;
        } else {
          item.w = item.h = 0;
          item.initialZoomLevel = item.fitRatio = 1;
          item.bounds = _getZeroBounds();
          item.initialPosition = item.bounds.center; // if it's not image, we return zero bounds (content is not zoomable)

          return item.bounds;
        }
      },
          _appendImage = function _appendImage(index, item, baseDiv, img, preventAnimation, keepPlaceholder) {
        if (item.loadError) {
          return;
        }

        if (img) {
          item.imageAppended = true;

          _setImageSize(item, img, item === self.currItem && _renderMaxResolution);

          baseDiv.appendChild(img);

          if (keepPlaceholder) {
            setTimeout(function () {
              if (item && item.loaded && item.placeholder) {
                item.placeholder.style.display = 'none';
                item.placeholder = null;
              }
            }, 500);
          }
        }
      },
          _preloadImage = function _preloadImage(item) {
        item.loading = true;
        item.loaded = false;
        var img = item.img = framework.createEl('pswp__img', 'img');

        var onComplete = function onComplete() {
          item.loading = false;
          item.loaded = true;

          if (item.loadComplete) {
            item.loadComplete(item);
          } else {
            item.img = null; // no need to store image object
          }

          img.onload = img.onerror = null;
          img = null;
        };

        img.onload = onComplete;

        img.onerror = function () {
          item.loadError = true;
          onComplete();
        };

        img.src = item.src; // + '?a=' + Math.random();

        return img;
      },
          _checkForError = function _checkForError(item, cleanUp) {
        if (item.src && item.loadError && item.container) {
          if (cleanUp) {
            item.container.innerHTML = '';
          }

          item.container.innerHTML = _options.errorMsg.replace('%url%', item.src);
          return true;
        }
      },
          _setImageSize = function _setImageSize(item, img, maxRes) {
        if (!item.src) {
          return;
        }

        if (!img) {
          img = item.container.lastChild;
        }

        var w = maxRes ? item.w : Math.round(item.w * item.fitRatio),
            h = maxRes ? item.h : Math.round(item.h * item.fitRatio);

        if (item.placeholder && !item.loaded) {
          item.placeholder.style.width = w + 'px';
          item.placeholder.style.height = h + 'px';
        }

        img.style.width = w + 'px';
        img.style.height = h + 'px';
      },
          _appendImagesPool = function _appendImagesPool() {
        if (_imagesToAppendPool.length) {
          var poolItem;

          for (var i = 0; i < _imagesToAppendPool.length; i++) {
            poolItem = _imagesToAppendPool[i];

            if (poolItem.holder.index === poolItem.index) {
              _appendImage(poolItem.index, poolItem.item, poolItem.baseDiv, poolItem.img, false, poolItem.clearPlaceholder);
            }
          }

          _imagesToAppendPool = [];
        }
      };

      _registerModule('Controller', {
        publicMethods: {
          lazyLoadItem: function lazyLoadItem(index) {
            index = _getLoopedId(index);

            var item = _getItemAt(index);

            if (!item || (item.loaded || item.loading) && !_itemsNeedUpdate) {
              return;
            }

            _shout('gettingData', index, item);

            if (!item.src) {
              return;
            }

            _preloadImage(item);
          },
          initController: function initController() {
            framework.extend(_options, _controllerDefaultOptions, true);
            self.items = _items = items;
            _getItemAt = self.getItemAt;
            _getNumItems = _options.getNumItemsFn; //self.getNumItems;

            if (_getNumItems() < 3) {
              _options.loop = false; // disable loop if less then 3 items
            }

            _listen('beforeChange', function (diff) {
              var p = _options.preload,
                  isNext = diff === null ? true : diff >= 0,
                  preloadBefore = Math.min(p[0], _getNumItems()),
                  preloadAfter = Math.min(p[1], _getNumItems()),
                  i;

              for (i = 1; i <= (isNext ? preloadAfter : preloadBefore); i++) {
                self.lazyLoadItem(_currentItemIndex + i);
              }

              for (i = 1; i <= (isNext ? preloadBefore : preloadAfter); i++) {
                self.lazyLoadItem(_currentItemIndex - i);
              }
            });

            _listen('initialLayout', function () {
              self.currItem.initialLayout = _options.getThumbBoundsFn && _options.getThumbBoundsFn(_currentItemIndex);
            });

            _listen('mainScrollAnimComplete', _appendImagesPool);

            _listen('initialZoomInEnd', _appendImagesPool);

            _listen('destroy', function () {
              var item;

              for (var i = 0; i < _items.length; i++) {
                item = _items[i]; // remove reference to DOM elements, for GC

                if (item.container) {
                  item.container = null;
                }

                if (item.placeholder) {
                  item.placeholder = null;
                }

                if (item.img) {
                  item.img = null;
                }

                if (item.preloader) {
                  item.preloader = null;
                }

                if (item.loadError) {
                  item.loaded = item.loadError = false;
                }
              }

              _imagesToAppendPool = null;
            });
          },
          getItemAt: function getItemAt(index) {
            if (index >= 0) {
              return _items[index] !== undefined ? _items[index] : false;
            }

            return false;
          },
          allowProgressiveImg: function allowProgressiveImg() {
            // 1. Progressive image loading isn't working on webkit/blink 
            //    when hw-acceleration (e.g. translateZ) is applied to IMG element.
            //    That's why in PhotoSwipe parent element gets zoom transform, not image itself.
            //    
            // 2. Progressive image loading sometimes blinks in webkit/blink when applying animation to parent element.
            //    That's why it's disabled on touch devices (mainly because of swipe transition)
            //    
            // 3. Progressive image loading sometimes doesn't work in IE (up to 11).
            // Don't allow progressive loading on non-large touch devices
            return _options.forceProgressiveLoading || !_likelyTouchDevice || _options.mouseUsed || screen.width > 1200; // 1200 - to eliminate touch devices with large screen (like Chromebook Pixel)
          },
          setContent: function setContent(holder, index) {
            if (_options.loop) {
              index = _getLoopedId(index);
            }

            var prevItem = self.getItemAt(holder.index);

            if (prevItem) {
              prevItem.container = null;
            }

            var item = self.getItemAt(index),
                img;

            if (!item) {
              holder.el.innerHTML = '';
              return;
            } // allow to override data


            _shout('gettingData', index, item);

            holder.index = index;
            holder.item = item; // base container DIV is created only once for each of 3 holders

            var baseDiv = item.container = framework.createEl('pswp__zoom-wrap');

            if (!item.src && item.html) {
              if (item.html.tagName) {
                baseDiv.appendChild(item.html);
              } else {
                baseDiv.innerHTML = item.html;
              }
            }

            _checkForError(item);

            _calculateItemSize(item, _viewportSize);

            if (item.src && !item.loadError && !item.loaded) {
              item.loadComplete = function (item) {
                // gallery closed before image finished loading
                if (!_isOpen) {
                  return;
                } // check if holder hasn't changed while image was loading


                if (holder && holder.index === index) {
                  if (_checkForError(item, true)) {
                    item.loadComplete = item.img = null;

                    _calculateItemSize(item, _viewportSize);

                    _applyZoomPanToItem(item);

                    if (holder.index === _currentItemIndex) {
                      // recalculate dimensions
                      self.updateCurrZoomItem();
                    }

                    return;
                  }

                  if (!item.imageAppended) {
                    if (_features.transform && (_mainScrollAnimating || _initialZoomRunning)) {
                      _imagesToAppendPool.push({
                        item: item,
                        baseDiv: baseDiv,
                        img: item.img,
                        index: index,
                        holder: holder,
                        clearPlaceholder: true
                      });
                    } else {
                      _appendImage(index, item, baseDiv, item.img, _mainScrollAnimating || _initialZoomRunning, true);
                    }
                  } else {
                    // remove preloader & mini-img
                    if (!_initialZoomRunning && item.placeholder) {
                      item.placeholder.style.display = 'none';
                      item.placeholder = null;
                    }
                  }
                }

                item.loadComplete = null;
                item.img = null; // no need to store image element after it's added

                _shout('imageLoadComplete', index, item);
              };

              if (framework.features.transform) {
                var placeholderClassName = 'pswp__img pswp__img--placeholder';
                placeholderClassName += item.msrc ? '' : ' pswp__img--placeholder--blank';
                var placeholder = framework.createEl(placeholderClassName, item.msrc ? 'img' : '');

                if (item.msrc) {
                  placeholder.src = item.msrc;
                }

                _setImageSize(item, placeholder);

                baseDiv.appendChild(placeholder);
                item.placeholder = placeholder;
              }

              if (!item.loading) {
                _preloadImage(item);
              }

              if (self.allowProgressiveImg()) {
                // just append image
                if (!_initialContentSet && _features.transform) {
                  _imagesToAppendPool.push({
                    item: item,
                    baseDiv: baseDiv,
                    img: item.img,
                    index: index,
                    holder: holder
                  });
                } else {
                  _appendImage(index, item, baseDiv, item.img, true, true);
                }
              }
            } else if (item.src && !item.loadError) {
              // image object is created every time, due to bugs of image loading & delay when switching images
              img = framework.createEl('pswp__img', 'img');
              img.style.opacity = 1;
              img.src = item.src;

              _setImageSize(item, img);

              _appendImage(index, item, baseDiv, img);
            }

            if (!_initialContentSet && index === _currentItemIndex) {
              _currZoomElementStyle = baseDiv.style;

              _showOrHide(item, img || item.img);
            } else {
              _applyZoomPanToItem(item);
            }

            holder.el.innerHTML = '';
            holder.el.appendChild(baseDiv);
          },
          cleanSlide: function cleanSlide(item) {
            if (item.img) {
              item.img.onload = item.img.onerror = null;
            }

            item.loaded = item.loading = item.img = item.imageAppended = false;
          }
        }
      });
      /*>>items-controller*/

      /*>>tap*/

      /**
       * tap.js:
       *
       * Displatches tap and double-tap events.
       * 
       */


      var tapTimer,
          tapReleasePoint = {},
          _dispatchTapEvent = function _dispatchTapEvent(origEvent, releasePoint, pointerType) {
        var e = document.createEvent('CustomEvent'),
            eDetail = {
          origEvent: origEvent,
          target: origEvent.target,
          releasePoint: releasePoint,
          pointerType: pointerType || 'touch'
        };
        e.initCustomEvent('pswpTap', true, true, eDetail);
        origEvent.target.dispatchEvent(e);
      };

      _registerModule('Tap', {
        publicMethods: {
          initTap: function initTap() {
            _listen('firstTouchStart', self.onTapStart);

            _listen('touchRelease', self.onTapRelease);

            _listen('destroy', function () {
              tapReleasePoint = {};
              tapTimer = null;
            });
          },
          onTapStart: function onTapStart(touchList) {
            if (touchList.length > 1) {
              clearTimeout(tapTimer);
              tapTimer = null;
            }
          },
          onTapRelease: function onTapRelease(e, releasePoint) {
            if (!releasePoint) {
              return;
            }

            if (!_moved && !_isMultitouch && !_numAnimations) {
              var p0 = releasePoint;

              if (tapTimer) {
                clearTimeout(tapTimer);
                tapTimer = null; // Check if taped on the same place

                if (_isNearbyPoints(p0, tapReleasePoint)) {
                  _shout('doubleTap', p0);

                  return;
                }
              }

              if (releasePoint.type === 'mouse') {
                _dispatchTapEvent(e, releasePoint, 'mouse');

                return;
              }

              var clickedTagName = e.target.tagName.toUpperCase(); // avoid double tap delay on buttons and elements that have class pswp__single-tap

              if (clickedTagName === 'BUTTON' || framework.hasClass(e.target, 'pswp__single-tap')) {
                _dispatchTapEvent(e, releasePoint);

                return;
              }

              _equalizePoints(tapReleasePoint, p0);

              tapTimer = setTimeout(function () {
                _dispatchTapEvent(e, releasePoint);

                tapTimer = null;
              }, 300);
            }
          }
        }
      });
      /*>>tap*/

      /*>>desktop-zoom*/

      /**
       *
       * desktop-zoom.js:
       *
       * - Binds mousewheel event for paning zoomed image.
       * - Manages "dragging", "zoomed-in", "zoom-out" classes.
       *   (which are used for cursors and zoom icon)
       * - Adds toggleDesktopZoom function.
       * 
       */


      var _wheelDelta;

      _registerModule('DesktopZoom', {
        publicMethods: {
          initDesktopZoom: function initDesktopZoom() {
            if (_oldIE) {
              // no zoom for old IE (<=8)
              return;
            }

            if (_likelyTouchDevice) {
              // if detected hardware touch support, we wait until mouse is used,
              // and only then apply desktop-zoom features
              _listen('mouseUsed', function () {
                self.setupDesktopZoom();
              });
            } else {
              self.setupDesktopZoom(true);
            }
          },
          setupDesktopZoom: function setupDesktopZoom(onInit) {
            _wheelDelta = {};
            var events = 'wheel mousewheel DOMMouseScroll';

            _listen('bindEvents', function () {
              framework.bind(template, events, self.handleMouseWheel);
            });

            _listen('unbindEvents', function () {
              if (_wheelDelta) {
                framework.unbind(template, events, self.handleMouseWheel);
              }
            });

            self.mouseZoomedIn = false;

            var hasDraggingClass,
                updateZoomable = function updateZoomable() {
              if (self.mouseZoomedIn) {
                framework.removeClass(template, 'pswp--zoomed-in');
                self.mouseZoomedIn = false;
              }

              if (_currZoomLevel < 1) {
                framework.addClass(template, 'pswp--zoom-allowed');
              } else {
                framework.removeClass(template, 'pswp--zoom-allowed');
              }

              removeDraggingClass();
            },
                removeDraggingClass = function removeDraggingClass() {
              if (hasDraggingClass) {
                framework.removeClass(template, 'pswp--dragging');
                hasDraggingClass = false;
              }
            };

            _listen('resize', updateZoomable);

            _listen('afterChange', updateZoomable);

            _listen('pointerDown', function () {
              if (self.mouseZoomedIn) {
                hasDraggingClass = true;
                framework.addClass(template, 'pswp--dragging');
              }
            });

            _listen('pointerUp', removeDraggingClass);

            if (!onInit) {
              updateZoomable();
            }
          },
          handleMouseWheel: function handleMouseWheel(e) {
            if (_currZoomLevel <= self.currItem.fitRatio) {
              if (_options.modal) {
                if (!_options.closeOnScroll || _numAnimations || _isDragging) {
                  e.preventDefault();
                } else if (_transformKey && Math.abs(e.deltaY) > 2) {
                  // close PhotoSwipe
                  // if browser supports transforms & scroll changed enough
                  _closedByScroll = true;
                  self.close();
                }
              }

              return true;
            } // allow just one event to fire


            e.stopPropagation(); // https://developer.mozilla.org/en-US/docs/Web/Events/wheel

            _wheelDelta.x = 0;

            if ('deltaX' in e) {
              if (e.deltaMode === 1
              /* DOM_DELTA_LINE */
              ) {
                // 18 - average line height
                _wheelDelta.x = e.deltaX * 18;
                _wheelDelta.y = e.deltaY * 18;
              } else {
                _wheelDelta.x = e.deltaX;
                _wheelDelta.y = e.deltaY;
              }
            } else if ('wheelDelta' in e) {
              if (e.wheelDeltaX) {
                _wheelDelta.x = -0.16 * e.wheelDeltaX;
              }

              if (e.wheelDeltaY) {
                _wheelDelta.y = -0.16 * e.wheelDeltaY;
              } else {
                _wheelDelta.y = -0.16 * e.wheelDelta;
              }
            } else if ('detail' in e) {
              _wheelDelta.y = e.detail;
            } else {
              return;
            }

            _calculatePanBounds(_currZoomLevel, true);

            var newPanX = _panOffset.x - _wheelDelta.x,
                newPanY = _panOffset.y - _wheelDelta.y; // only prevent scrolling in nonmodal mode when not at edges

            if (_options.modal || newPanX <= _currPanBounds.min.x && newPanX >= _currPanBounds.max.x && newPanY <= _currPanBounds.min.y && newPanY >= _currPanBounds.max.y) {
              e.preventDefault();
            } // TODO: use rAF instead of mousewheel?


            self.panTo(newPanX, newPanY);
          },
          toggleDesktopZoom: function toggleDesktopZoom(centerPoint) {
            centerPoint = centerPoint || {
              x: _viewportSize.x / 2 + _offset.x,
              y: _viewportSize.y / 2 + _offset.y
            };

            var doubleTapZoomLevel = _options.getDoubleTapZoom(true, self.currItem);

            var zoomOut = _currZoomLevel === doubleTapZoomLevel;
            self.mouseZoomedIn = !zoomOut;
            self.zoomTo(zoomOut ? self.currItem.initialZoomLevel : doubleTapZoomLevel, centerPoint, 333);
            framework[(!zoomOut ? 'add' : 'remove') + 'Class'](template, 'pswp--zoomed-in');
          }
        }
      });
      /*>>desktop-zoom*/

      /*>>history*/

      /**
       *
       * history.js:
       *
       * - Back button to close gallery.
       * 
       * - Unique URL for each slide: example.com/&pid=1&gid=3
       *   (where PID is picture index, and GID and gallery index)
       *   
       * - Switch URL when slides change.
       * 
       */


      var _historyDefaultOptions = {
        history: true,
        galleryUID: 1
      };

      var _historyUpdateTimeout,
          _hashChangeTimeout,
          _hashAnimCheckTimeout,
          _hashChangedByScript,
          _hashChangedByHistory,
          _hashReseted,
          _initialHash,
          _historyChanged,
          _closedFromURL,
          _urlChangedOnce,
          _windowLoc,
          _supportsPushState,
          _getHash = function _getHash() {
        return _windowLoc.hash.substring(1);
      },
          _cleanHistoryTimeouts = function _cleanHistoryTimeouts() {
        if (_historyUpdateTimeout) {
          clearTimeout(_historyUpdateTimeout);
        }

        if (_hashAnimCheckTimeout) {
          clearTimeout(_hashAnimCheckTimeout);
        }
      },
          // pid - Picture index
      // gid - Gallery index
      _parseItemIndexFromURL = function _parseItemIndexFromURL() {
        var hash = _getHash(),
            params = {};

        if (hash.length < 5) {
          // pid=1
          return params;
        }

        var i,
            vars = hash.split('&');

        for (i = 0; i < vars.length; i++) {
          if (!vars[i]) {
            continue;
          }

          var pair = vars[i].split('=');

          if (pair.length < 2) {
            continue;
          }

          params[pair[0]] = pair[1];
        }

        if (_options.galleryPIDs) {
          // detect custom pid in hash and search for it among the items collection
          var searchfor = params.pid;
          params.pid = 0; // if custom pid cannot be found, fallback to the first item

          for (i = 0; i < _items.length; i++) {
            if (_items[i].pid === searchfor) {
              params.pid = i;
              break;
            }
          }
        } else {
          params.pid = parseInt(params.pid, 10) - 1;
        }

        if (params.pid < 0) {
          params.pid = 0;
        }

        return params;
      },
          _updateHash = function _updateHash() {
        if (_hashAnimCheckTimeout) {
          clearTimeout(_hashAnimCheckTimeout);
        }

        if (_numAnimations || _isDragging) {
          // changing browser URL forces layout/paint in some browsers, which causes noticable lag during animation
          // that's why we update hash only when no animations running
          _hashAnimCheckTimeout = setTimeout(_updateHash, 500);
          return;
        }

        if (_hashChangedByScript) {
          clearTimeout(_hashChangeTimeout);
        } else {
          _hashChangedByScript = true;
        }

        var pid = _currentItemIndex + 1;

        var item = _getItemAt(_currentItemIndex);

        if (item.hasOwnProperty('pid')) {
          // carry forward any custom pid assigned to the item
          pid = item.pid;
        }

        var newHash = _initialHash + '&' + 'gid=' + _options.galleryUID + '&' + 'pid=' + pid;

        if (!_historyChanged) {
          if (_windowLoc.hash.indexOf(newHash) === -1) {
            _urlChangedOnce = true;
          } // first time - add new hisory record, then just replace

        }

        var newURL = _windowLoc.href.split('#')[0] + '#' + newHash;

        if (_supportsPushState) {
          if ('#' + newHash !== window.location.hash) {
            history[_historyChanged ? 'replaceState' : 'pushState']('', document.title, newURL);
          }
        } else {
          if (_historyChanged) {
            _windowLoc.replace(newURL);
          } else {
            _windowLoc.hash = newHash;
          }
        }

        _historyChanged = true;
        _hashChangeTimeout = setTimeout(function () {
          _hashChangedByScript = false;
        }, 60);
      };

      _registerModule('History', {
        publicMethods: {
          initHistory: function initHistory() {
            framework.extend(_options, _historyDefaultOptions, true);

            if (!_options.history) {
              return;
            }

            _windowLoc = window.location;
            _urlChangedOnce = false;
            _closedFromURL = false;
            _historyChanged = false;
            _initialHash = _getHash();
            _supportsPushState = 'pushState' in history;

            if (_initialHash.indexOf('gid=') > -1) {
              _initialHash = _initialHash.split('&gid=')[0];
              _initialHash = _initialHash.split('?gid=')[0];
            }

            _listen('afterChange', self.updateURL);

            _listen('unbindEvents', function () {
              framework.unbind(window, 'hashchange', self.onHashChange);
            });

            var returnToOriginal = function returnToOriginal() {
              _hashReseted = true;

              if (!_closedFromURL) {
                if (_urlChangedOnce) {
                  history.back();
                } else {
                  if (_initialHash) {
                    _windowLoc.hash = _initialHash;
                  } else {
                    if (_supportsPushState) {
                      // remove hash from url without refreshing it or scrolling to top
                      history.pushState('', document.title, _windowLoc.pathname + _windowLoc.search);
                    } else {
                      _windowLoc.hash = '';
                    }
                  }
                }
              }

              _cleanHistoryTimeouts();
            };

            _listen('unbindEvents', function () {
              if (_closedByScroll) {
                // if PhotoSwipe is closed by scroll, we go "back" before the closing animation starts
                // this is done to keep the scroll position
                returnToOriginal();
              }
            });

            _listen('destroy', function () {
              if (!_hashReseted) {
                returnToOriginal();
              }
            });

            _listen('firstUpdate', function () {
              _currentItemIndex = _parseItemIndexFromURL().pid;
            });

            var index = _initialHash.indexOf('pid=');

            if (index > -1) {
              _initialHash = _initialHash.substring(0, index);

              if (_initialHash.slice(-1) === '&') {
                _initialHash = _initialHash.slice(0, -1);
              }
            }

            setTimeout(function () {
              if (_isOpen) {
                // hasn't destroyed yet
                framework.bind(window, 'hashchange', self.onHashChange);
              }
            }, 40);
          },
          onHashChange: function onHashChange() {
            if (_getHash() === _initialHash) {
              _closedFromURL = true;
              self.close();
              return;
            }

            if (!_hashChangedByScript) {
              _hashChangedByHistory = true;
              self.goTo(_parseItemIndexFromURL().pid);
              _hashChangedByHistory = false;
            }
          },
          updateURL: function updateURL() {
            // Delay the update of URL, to avoid lag during transition, 
            // and to not to trigger actions like "refresh page sound" or "blinking favicon" to often
            _cleanHistoryTimeouts();

            if (_hashChangedByHistory) {
              return;
            }

            if (!_historyChanged) {
              _updateHash(); // first time

            } else {
              _historyUpdateTimeout = setTimeout(_updateHash, 800);
            }
          }
        }
      });
      /*>>history*/


      framework.extend(self, publicMethods);
    };

    return PhotoSwipe;
  });

  /*! PhotoSwipe Default UI - 4.1.3 - 2019-01-08
  * http://photoswipe.com
  * Copyright (c) 2019 Dmitry Semenov; */

  /**
  *
  * UI on top of main sliding area (caption, arrows, close button, etc.).
  * Built just using public methods/properties of PhotoSwipe.
  * 
  */
  (function (root, factory) {
    if (typeof define === 'function' && define.amd) {
      define(factory);
    } else if ((typeof exports === "undefined" ? "undefined" : _typeof(exports)) === 'object') {
      module.exports = factory();
    } else {
      root.PhotoSwipeUI_Default = factory();
    }
  })(window, function () {

    var PhotoSwipeUI_Default = function PhotoSwipeUI_Default(pswp, framework) {
      var ui = this;

      var _overlayUIUpdated = false,
          _controlsVisible = true,
          _fullscrenAPI,
          _controls,
          _captionContainer,
          _fakeCaptionContainer,
          _indexIndicator,
          _shareButton,
          _shareModal,
          _shareModalHidden = true,
          _initalCloseOnScrollValue,
          _isIdle,
          _listen,
          _loadingIndicator,
          _loadingIndicatorHidden,
          _loadingIndicatorTimeout,
          _galleryHasOneSlide,
          _options,
          _defaultUIOptions = {
        barsSize: {
          top: 44,
          bottom: 'auto'
        },
        closeElClasses: ['item', 'caption', 'zoom-wrap', 'ui', 'top-bar'],
        timeToIdle: 4000,
        timeToIdleOutside: 1000,
        loadingIndicatorDelay: 1000,
        // 2s
        addCaptionHTMLFn: function addCaptionHTMLFn(item, captionEl
        /*, isFake */
        ) {
          if (!item.title) {
            captionEl.children[0].innerHTML = '';
            return false;
          }

          captionEl.children[0].innerHTML = item.title;
          return true;
        },
        closeEl: true,
        captionEl: true,
        fullscreenEl: true,
        zoomEl: true,
        shareEl: true,
        counterEl: true,
        arrowEl: true,
        preloaderEl: true,
        tapToClose: false,
        tapToToggleControls: true,
        clickToCloseNonZoomable: true,
        shareButtons: [{
          id: 'facebook',
          label: 'Share on Facebook',
          url: 'https://www.facebook.com/sharer/sharer.php?u={{url}}'
        }, {
          id: 'twitter',
          label: 'Tweet',
          url: 'https://twitter.com/intent/tweet?text={{text}}&url={{url}}'
        }, {
          id: 'pinterest',
          label: 'Pin it',
          url: 'http://www.pinterest.com/pin/create/button/' + '?url={{url}}&media={{image_url}}&description={{text}}'
        }, {
          id: 'download',
          label: 'Download image',
          url: '{{raw_image_url}}',
          download: true
        }],
        getImageURLForShare: function
          /* shareButtonData */
        getImageURLForShare() {
          return pswp.currItem.src || '';
        },
        getPageURLForShare: function
          /* shareButtonData */
        getPageURLForShare() {
          return window.location.href;
        },
        getTextForShare: function
          /* shareButtonData */
        getTextForShare() {
          return pswp.currItem.title || '';
        },
        indexIndicatorSep: ' / ',
        fitControlsWidth: 1200
      },
          _blockControlsTap,
          _blockControlsTapTimeout;

      var _onControlsTap = function _onControlsTap(e) {
        if (_blockControlsTap) {
          return true;
        }

        e = e || window.event;

        if (_options.timeToIdle && _options.mouseUsed && !_isIdle) {
          // reset idle timer
          _onIdleMouseMove();
        }

        var target = e.target || e.srcElement,
            uiElement,
            clickedClass = target.getAttribute('class') || '',
            found;

        for (var i = 0; i < _uiElements.length; i++) {
          uiElement = _uiElements[i];

          if (uiElement.onTap && clickedClass.indexOf('pswp__' + uiElement.name) > -1) {
            uiElement.onTap();
            found = true;
          }
        }

        if (found) {
          if (e.stopPropagation) {
            e.stopPropagation();
          }

          _blockControlsTap = true; // Some versions of Android don't prevent ghost click event 
          // when preventDefault() was called on touchstart and/or touchend.
          // 
          // This happens on v4.3, 4.2, 4.1, 
          // older versions strangely work correctly, 
          // but just in case we add delay on all of them)	

          var tapDelay = framework.features.isOldAndroid ? 600 : 30;
          _blockControlsTapTimeout = setTimeout(function () {
            _blockControlsTap = false;
          }, tapDelay);
        }
      },
          _fitControlsInViewport = function _fitControlsInViewport() {
        return !pswp.likelyTouchDevice || _options.mouseUsed || screen.width > _options.fitControlsWidth;
      },
          _togglePswpClass = function _togglePswpClass(el, cName, add) {
        framework[(add ? 'add' : 'remove') + 'Class'](el, 'pswp__' + cName);
      },
          // add class when there is just one item in the gallery
      // (by default it hides left/right arrows and 1ofX counter)
      _countNumItems = function _countNumItems() {
        var hasOneSlide = _options.getNumItemsFn() === 1;

        if (hasOneSlide !== _galleryHasOneSlide) {
          _togglePswpClass(_controls, 'ui--one-slide', hasOneSlide);

          _galleryHasOneSlide = hasOneSlide;
        }
      },
          _toggleShareModalClass = function _toggleShareModalClass() {
        _togglePswpClass(_shareModal, 'share-modal--hidden', _shareModalHidden);
      },
          _toggleShareModal = function _toggleShareModal() {
        _shareModalHidden = !_shareModalHidden;

        if (!_shareModalHidden) {
          _toggleShareModalClass();

          setTimeout(function () {
            if (!_shareModalHidden) {
              framework.addClass(_shareModal, 'pswp__share-modal--fade-in');
            }
          }, 30);
        } else {
          framework.removeClass(_shareModal, 'pswp__share-modal--fade-in');
          setTimeout(function () {
            if (_shareModalHidden) {
              _toggleShareModalClass();
            }
          }, 300);
        }

        if (!_shareModalHidden) {
          _updateShareURLs();
        }

        return false;
      },
          _openWindowPopup = function _openWindowPopup(e) {
        e = e || window.event;
        var target = e.target || e.srcElement;
        pswp.shout('shareLinkClick', e, target);

        if (!target.href) {
          return false;
        }

        if (target.hasAttribute('download')) {
          return true;
        }

        window.open(target.href, 'pswp_share', 'scrollbars=yes,resizable=yes,toolbar=no,' + 'location=yes,width=550,height=420,top=100,left=' + (window.screen ? Math.round(screen.width / 2 - 275) : 100));

        if (!_shareModalHidden) {
          _toggleShareModal();
        }

        return false;
      },
          _updateShareURLs = function _updateShareURLs() {
        var shareButtonOut = '',
            shareButtonData,
            shareURL,
            image_url,
            page_url,
            share_text;

        for (var i = 0; i < _options.shareButtons.length; i++) {
          shareButtonData = _options.shareButtons[i];
          image_url = _options.getImageURLForShare(shareButtonData);
          page_url = _options.getPageURLForShare(shareButtonData);
          share_text = _options.getTextForShare(shareButtonData);
          shareURL = shareButtonData.url.replace('{{url}}', encodeURIComponent(page_url)).replace('{{image_url}}', encodeURIComponent(image_url)).replace('{{raw_image_url}}', image_url).replace('{{text}}', encodeURIComponent(share_text));
          shareButtonOut += '<a href="' + shareURL + '" target="_blank" ' + 'class="pswp__share--' + shareButtonData.id + '"' + (shareButtonData.download ? 'download' : '') + '>' + shareButtonData.label + '</a>';

          if (_options.parseShareButtonOut) {
            shareButtonOut = _options.parseShareButtonOut(shareButtonData, shareButtonOut);
          }
        }

        _shareModal.children[0].innerHTML = shareButtonOut;
        _shareModal.children[0].onclick = _openWindowPopup;
      },
          _hasCloseClass = function _hasCloseClass(target) {
        for (var i = 0; i < _options.closeElClasses.length; i++) {
          if (framework.hasClass(target, 'pswp__' + _options.closeElClasses[i])) {
            return true;
          }
        }
      },
          _idleInterval,
          _idleTimer,
          _idleIncrement = 0,
          _onIdleMouseMove = function _onIdleMouseMove() {
        clearTimeout(_idleTimer);
        _idleIncrement = 0;

        if (_isIdle) {
          ui.setIdle(false);
        }
      },
          _onMouseLeaveWindow = function _onMouseLeaveWindow(e) {
        e = e ? e : window.event;
        var from = e.relatedTarget || e.toElement;

        if (!from || from.nodeName === 'HTML') {
          clearTimeout(_idleTimer);
          _idleTimer = setTimeout(function () {
            ui.setIdle(true);
          }, _options.timeToIdleOutside);
        }
      },
          _setupFullscreenAPI = function _setupFullscreenAPI() {
        if (_options.fullscreenEl && !framework.features.isOldAndroid) {
          if (!_fullscrenAPI) {
            _fullscrenAPI = ui.getFullscreenAPI();
          }

          if (_fullscrenAPI) {
            framework.bind(document, _fullscrenAPI.eventK, ui.updateFullscreen);
            ui.updateFullscreen();
            framework.addClass(pswp.template, 'pswp--supports-fs');
          } else {
            framework.removeClass(pswp.template, 'pswp--supports-fs');
          }
        }
      },
          _setupLoadingIndicator = function _setupLoadingIndicator() {
        // Setup loading indicator
        if (_options.preloaderEl) {
          _toggleLoadingIndicator(true);

          _listen('beforeChange', function () {
            clearTimeout(_loadingIndicatorTimeout); // display loading indicator with delay

            _loadingIndicatorTimeout = setTimeout(function () {
              if (pswp.currItem && pswp.currItem.loading) {
                if (!pswp.allowProgressiveImg() || pswp.currItem.img && !pswp.currItem.img.naturalWidth) {
                  // show preloader if progressive loading is not enabled, 
                  // or image width is not defined yet (because of slow connection)
                  _toggleLoadingIndicator(false); // items-controller.js function allowProgressiveImg

                }
              } else {
                _toggleLoadingIndicator(true); // hide preloader

              }
            }, _options.loadingIndicatorDelay);
          });

          _listen('imageLoadComplete', function (index, item) {
            if (pswp.currItem === item) {
              _toggleLoadingIndicator(true);
            }
          });
        }
      },
          _toggleLoadingIndicator = function _toggleLoadingIndicator(hide) {
        if (_loadingIndicatorHidden !== hide) {
          _togglePswpClass(_loadingIndicator, 'preloader--active', !hide);

          _loadingIndicatorHidden = hide;
        }
      },
          _applyNavBarGaps = function _applyNavBarGaps(item) {
        var gap = item.vGap;

        if (_fitControlsInViewport()) {
          var bars = _options.barsSize;

          if (_options.captionEl && bars.bottom === 'auto') {
            if (!_fakeCaptionContainer) {
              _fakeCaptionContainer = framework.createEl('pswp__caption pswp__caption--fake');

              _fakeCaptionContainer.appendChild(framework.createEl('pswp__caption__center'));

              _controls.insertBefore(_fakeCaptionContainer, _captionContainer);

              framework.addClass(_controls, 'pswp__ui--fit');
            }

            if (_options.addCaptionHTMLFn(item, _fakeCaptionContainer, true)) {
              var captionSize = _fakeCaptionContainer.clientHeight;
              gap.bottom = parseInt(captionSize, 10) || 44;
            } else {
              gap.bottom = bars.top; // if no caption, set size of bottom gap to size of top
            }
          } else {
            gap.bottom = bars.bottom === 'auto' ? 0 : bars.bottom;
          } // height of top bar is static, no need to calculate it


          gap.top = bars.top;
        } else {
          gap.top = gap.bottom = 0;
        }
      },
          _setupIdle = function _setupIdle() {
        // Hide controls when mouse is used
        if (_options.timeToIdle) {
          _listen('mouseUsed', function () {
            framework.bind(document, 'mousemove', _onIdleMouseMove);
            framework.bind(document, 'mouseout', _onMouseLeaveWindow);
            _idleInterval = setInterval(function () {
              _idleIncrement++;

              if (_idleIncrement === 2) {
                ui.setIdle(true);
              }
            }, _options.timeToIdle / 2);
          });
        }
      },
          _setupHidingControlsDuringGestures = function _setupHidingControlsDuringGestures() {
        // Hide controls on vertical drag
        _listen('onVerticalDrag', function (now) {
          if (_controlsVisible && now < 0.95) {
            ui.hideControls();
          } else if (!_controlsVisible && now >= 0.95) {
            ui.showControls();
          }
        }); // Hide controls when pinching to close


        var pinchControlsHidden;

        _listen('onPinchClose', function (now) {
          if (_controlsVisible && now < 0.9) {
            ui.hideControls();
            pinchControlsHidden = true;
          } else if (pinchControlsHidden && !_controlsVisible && now > 0.9) {
            ui.showControls();
          }
        });

        _listen('zoomGestureEnded', function () {
          pinchControlsHidden = false;

          if (pinchControlsHidden && !_controlsVisible) {
            ui.showControls();
          }
        });
      };

      var _uiElements = [{
        name: 'caption',
        option: 'captionEl',
        onInit: function onInit(el) {
          _captionContainer = el;
        }
      }, {
        name: 'share-modal',
        option: 'shareEl',
        onInit: function onInit(el) {
          _shareModal = el;
        },
        onTap: function onTap() {
          _toggleShareModal();
        }
      }, {
        name: 'button--share',
        option: 'shareEl',
        onInit: function onInit(el) {
          _shareButton = el;
        },
        onTap: function onTap() {
          _toggleShareModal();
        }
      }, {
        name: 'button--zoom',
        option: 'zoomEl',
        onTap: pswp.toggleDesktopZoom
      }, {
        name: 'counter',
        option: 'counterEl',
        onInit: function onInit(el) {
          _indexIndicator = el;
        }
      }, {
        name: 'button--close',
        option: 'closeEl',
        onTap: pswp.close
      }, {
        name: 'button--arrow--left',
        option: 'arrowEl',
        onTap: pswp.prev
      }, {
        name: 'button--arrow--right',
        option: 'arrowEl',
        onTap: pswp.next
      }, {
        name: 'button--fs',
        option: 'fullscreenEl',
        onTap: function onTap() {
          if (_fullscrenAPI.isFullscreen()) {
            _fullscrenAPI.exit();
          } else {
            _fullscrenAPI.enter();
          }
        }
      }, {
        name: 'preloader',
        option: 'preloaderEl',
        onInit: function onInit(el) {
          _loadingIndicator = el;
        }
      }];

      var _setupUIElements = function _setupUIElements() {
        var item, classAttr, uiElement;

        var loopThroughChildElements = function loopThroughChildElements(sChildren) {
          if (!sChildren) {
            return;
          }

          var l = sChildren.length;

          for (var i = 0; i < l; i++) {
            item = sChildren[i];
            classAttr = item.className;

            for (var a = 0; a < _uiElements.length; a++) {
              uiElement = _uiElements[a];

              if (classAttr.indexOf('pswp__' + uiElement.name) > -1) {
                if (_options[uiElement.option]) {
                  // if element is not disabled from options
                  framework.removeClass(item, 'pswp__element--disabled');

                  if (uiElement.onInit) {
                    uiElement.onInit(item);
                  } //item.style.display = 'block';

                } else {
                  framework.addClass(item, 'pswp__element--disabled'); //item.style.display = 'none';
                }
              }
            }
          }
        };

        loopThroughChildElements(_controls.children);
        var topBar = framework.getChildByClass(_controls, 'pswp__top-bar');

        if (topBar) {
          loopThroughChildElements(topBar.children);
        }
      };

      ui.init = function () {
        // extend options
        framework.extend(pswp.options, _defaultUIOptions, true); // create local link for fast access

        _options = pswp.options; // find pswp__ui element

        _controls = framework.getChildByClass(pswp.scrollWrap, 'pswp__ui'); // create local link

        _listen = pswp.listen;

        _setupHidingControlsDuringGestures(); // update controls when slides change


        _listen('beforeChange', ui.update); // toggle zoom on double-tap


        _listen('doubleTap', function (point) {
          var initialZoomLevel = pswp.currItem.initialZoomLevel;

          if (pswp.getZoomLevel() !== initialZoomLevel) {
            pswp.zoomTo(initialZoomLevel, point, 333);
          } else {
            pswp.zoomTo(_options.getDoubleTapZoom(false, pswp.currItem), point, 333);
          }
        }); // Allow text selection in caption


        _listen('preventDragEvent', function (e, isDown, preventObj) {
          var t = e.target || e.srcElement;

          if (t && t.getAttribute('class') && e.type.indexOf('mouse') > -1 && (t.getAttribute('class').indexOf('__caption') > 0 || /(SMALL|STRONG|EM)/i.test(t.tagName))) {
            preventObj.prevent = false;
          }
        }); // bind events for UI


        _listen('bindEvents', function () {
          framework.bind(_controls, 'pswpTap click', _onControlsTap);
          framework.bind(pswp.scrollWrap, 'pswpTap', ui.onGlobalTap);

          if (!pswp.likelyTouchDevice) {
            framework.bind(pswp.scrollWrap, 'mouseover', ui.onMouseOver);
          }
        }); // unbind events for UI


        _listen('unbindEvents', function () {
          if (!_shareModalHidden) {
            _toggleShareModal();
          }

          if (_idleInterval) {
            clearInterval(_idleInterval);
          }

          framework.unbind(document, 'mouseout', _onMouseLeaveWindow);
          framework.unbind(document, 'mousemove', _onIdleMouseMove);
          framework.unbind(_controls, 'pswpTap click', _onControlsTap);
          framework.unbind(pswp.scrollWrap, 'pswpTap', ui.onGlobalTap);
          framework.unbind(pswp.scrollWrap, 'mouseover', ui.onMouseOver);

          if (_fullscrenAPI) {
            framework.unbind(document, _fullscrenAPI.eventK, ui.updateFullscreen);

            if (_fullscrenAPI.isFullscreen()) {
              _options.hideAnimationDuration = 0;

              _fullscrenAPI.exit();
            }

            _fullscrenAPI = null;
          }
        }); // clean up things when gallery is destroyed


        _listen('destroy', function () {
          if (_options.captionEl) {
            if (_fakeCaptionContainer) {
              _controls.removeChild(_fakeCaptionContainer);
            }

            framework.removeClass(_captionContainer, 'pswp__caption--empty');
          }

          if (_shareModal) {
            _shareModal.children[0].onclick = null;
          }

          framework.removeClass(_controls, 'pswp__ui--over-close');
          framework.addClass(_controls, 'pswp__ui--hidden');
          ui.setIdle(false);
        });

        if (!_options.showAnimationDuration) {
          framework.removeClass(_controls, 'pswp__ui--hidden');
        }

        _listen('initialZoomIn', function () {
          if (_options.showAnimationDuration) {
            framework.removeClass(_controls, 'pswp__ui--hidden');
          }
        });

        _listen('initialZoomOut', function () {
          framework.addClass(_controls, 'pswp__ui--hidden');
        });

        _listen('parseVerticalMargin', _applyNavBarGaps);

        _setupUIElements();

        if (_options.shareEl && _shareButton && _shareModal) {
          _shareModalHidden = true;
        }

        _countNumItems();

        _setupIdle();

        _setupFullscreenAPI();

        _setupLoadingIndicator();
      };

      ui.setIdle = function (isIdle) {
        _isIdle = isIdle;

        _togglePswpClass(_controls, 'ui--idle', isIdle);
      };

      ui.update = function () {
        // Don't update UI if it's hidden
        if (_controlsVisible && pswp.currItem) {
          ui.updateIndexIndicator();

          if (_options.captionEl) {
            _options.addCaptionHTMLFn(pswp.currItem, _captionContainer);

            _togglePswpClass(_captionContainer, 'caption--empty', !pswp.currItem.title);
          }

          _overlayUIUpdated = true;
        } else {
          _overlayUIUpdated = false;
        }

        if (!_shareModalHidden) {
          _toggleShareModal();
        }

        _countNumItems();
      };

      ui.updateFullscreen = function (e) {
        if (e) {
          // some browsers change window scroll position during the fullscreen
          // so PhotoSwipe updates it just in case
          setTimeout(function () {
            pswp.setScrollOffset(0, framework.getScrollY());
          }, 50);
        } // toogle pswp--fs class on root element


        framework[(_fullscrenAPI.isFullscreen() ? 'add' : 'remove') + 'Class'](pswp.template, 'pswp--fs');
      };

      ui.updateIndexIndicator = function () {
        if (_options.counterEl) {
          _indexIndicator.innerHTML = pswp.getCurrentIndex() + 1 + _options.indexIndicatorSep + _options.getNumItemsFn();
        }
      };

      ui.onGlobalTap = function (e) {
        e = e || window.event;
        var target = e.target || e.srcElement;

        if (_blockControlsTap) {
          return;
        }

        if (e.detail && e.detail.pointerType === 'mouse') {
          // close gallery if clicked outside of the image
          if (_hasCloseClass(target)) {
            pswp.close();
            return;
          }

          if (framework.hasClass(target, 'pswp__img')) {
            if (pswp.getZoomLevel() === 1 && pswp.getZoomLevel() <= pswp.currItem.fitRatio) {
              if (_options.clickToCloseNonZoomable) {
                pswp.close();
              }
            } else {
              pswp.toggleDesktopZoom(e.detail.releasePoint);
            }
          }
        } else {
          // tap anywhere (except buttons) to toggle visibility of controls
          if (_options.tapToToggleControls) {
            if (_controlsVisible) {
              ui.hideControls();
            } else {
              ui.showControls();
            }
          } // tap to close gallery


          if (_options.tapToClose && (framework.hasClass(target, 'pswp__img') || _hasCloseClass(target))) {
            pswp.close();
            return;
          }
        }
      };

      ui.onMouseOver = function (e) {
        e = e || window.event;
        var target = e.target || e.srcElement; // add class when mouse is over an element that should close the gallery

        _togglePswpClass(_controls, 'ui--over-close', _hasCloseClass(target));
      };

      ui.hideControls = function () {
        framework.addClass(_controls, 'pswp__ui--hidden');
        _controlsVisible = false;
      };

      ui.showControls = function () {
        _controlsVisible = true;

        if (!_overlayUIUpdated) {
          ui.update();
        }

        framework.removeClass(_controls, 'pswp__ui--hidden');
      };

      ui.supportsFullscreen = function () {
        var d = document;
        return !!(d.exitFullscreen || d.mozCancelFullScreen || d.webkitExitFullscreen || d.msExitFullscreen);
      };

      ui.getFullscreenAPI = function () {
        var dE = document.documentElement,
            api,
            tF = 'fullscreenchange';

        if (dE.requestFullscreen) {
          api = {
            enterK: 'requestFullscreen',
            exitK: 'exitFullscreen',
            elementK: 'fullscreenElement',
            eventK: tF
          };
        } else if (dE.mozRequestFullScreen) {
          api = {
            enterK: 'mozRequestFullScreen',
            exitK: 'mozCancelFullScreen',
            elementK: 'mozFullScreenElement',
            eventK: 'moz' + tF
          };
        } else if (dE.webkitRequestFullscreen) {
          api = {
            enterK: 'webkitRequestFullscreen',
            exitK: 'webkitExitFullscreen',
            elementK: 'webkitFullscreenElement',
            eventK: 'webkit' + tF
          };
        } else if (dE.msRequestFullscreen) {
          api = {
            enterK: 'msRequestFullscreen',
            exitK: 'msExitFullscreen',
            elementK: 'msFullscreenElement',
            eventK: 'MSFullscreenChange'
          };
        }

        if (api) {
          api.enter = function () {
            // disable close-on-scroll in fullscreen
            _initalCloseOnScrollValue = _options.closeOnScroll;
            _options.closeOnScroll = false;

            if (this.enterK === 'webkitRequestFullscreen') {
              pswp.template[this.enterK](Element.ALLOW_KEYBOARD_INPUT);
            } else {
              return pswp.template[this.enterK]();
            }
          };

          api.exit = function () {
            _options.closeOnScroll = _initalCloseOnScrollValue;
            return document[this.exitK]();
          };

          api.isFullscreen = function () {
            return document[this.elementK];
          };
        }

        return api;
      };
    };

    return PhotoSwipeUI_Default;
  });

  /**
   * Wrapper for PhotoSwipe plugin.
   *
   * @since 1.0
   * @link  https://github.com/dimsemenov/PhotoSwipe
   *
   * @param array items List of gallery images.
   * @param int   index Set which gallery image to open initially by index.
   */

  MyListing.PhotoSwipe = function (items, index) {
    var pswpElement = document.querySelectorAll('.pswp')[0];
    var options = {
      index: index,
      showAnimationDuration: 333,
      hideAnimationDuration: 333,
      showHideOpacity: true,
      history: false,
      shareEl: false,
      getThumbBoundsFn: function getThumbBoundsFn(i) {
        var thumbnail = items[i].el,
            pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
            rect = thumbnail.getBoundingClientRect();
        return {
          x: rect.left,
          y: rect.top + pageYScroll,
          w: rect.width
        };
      }
    }; // Init photoswipe.

    this.gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
    this.gallery.init(); // Lazy load images.

    this.gallery.listen('imageLoadComplete', this.lazyload.bind(this));
  };
  /**
   * Lazy load full-size images.
   *
   * @since 1.0
   */


  MyListing.PhotoSwipe.prototype.lazyload = function (i, item) {
    var self = this;

    if (item.w < 1 || item.h < 1) {
      var img = new Image();

      img.onload = function () {
        item.w = this.width;
        item.el.dataset.fullWidth = this.width;
        item.h = this.height;
        item.el.dataset.fullHeight = this.height;
        self.gallery.invalidateCurrItems();
        self.gallery.updateSize(true);
      };

      img.src = item.src;
    }
  };

  jQuery(function ($) {
    /**
     * Initialize PhotoSwipe for single images through HTML,
     * by using the `open-photo-swipe` class.
     *
     * @since 1.0
     */
    $('body').on('click', '.open-photo-swipe', function (e) {
      e.preventDefault();
      new MyListing.PhotoSwipe([{
        src: this.href,
        w: this.dataset.fullWidth || 0,
        h: this.dataset.fullHeight || 0,
        el: this
      }], 0);
    });
    /**
     * PhotoSwipe gallery support for images in OwlCarousel.
     *
     * @since 1.0
     */

    $('.photoswipe-gallery .photoswipe-item').on('click', function (e) {
      e.preventDefault();
      var items = [];
      var curr = this;
      var index = 0;
      $(this).parents('.photoswipe-gallery').find('.photoswipe-item').each(function (i, el) {
        items.push({
          src: el.href || el.dataset.large_image,
          w: el.dataset.fullWidth || el.dataset.large_image_width || 0,
          h: el.dataset.fullHeight || el.dataset.large_image_height || 0,
          el: el
        });

        if (el == curr) {
          index = i;
        }
      });
      new MyListing.PhotoSwipe(items, index);
    });
  });

  /**
   * Handles Quicksearch widget.
   *
   * @since 2.0
   */
  jQuery(function ($) {
    $('.quick-search-instance').each(function (i, el) {
      var instance = {};
      instance.el = $(this);
      instance.input = instance.el.find('input[name="search_keywords"]');
      instance.default = instance.el.find('.default-results');
      instance.results = instance.el.find('.ajax-results');
      instance.spinner = instance.el.find('.loader-bg');
      instance.view_all = instance.el.find('.all-results');
      instance.no_results = instance.el.find('.no-results');
      instance.last_request = null;
      instance.input.on('input', MyListing.Helpers.debounce(function (e) {
        quicksearch(instance);
      }, 250)).trigger('input');

      if (instance.el.data('focus') === 'always') {
        instance.el.find('.header-search').addClass('is-focused');
      } else {
        instance.el.on('focusin', function () {
          instance.el.find('.header-search').addClass('is-focused');
        }).on('focusout', function () {
          instance.el.find('.header-search').removeClass('is-focused');
        });
      }
    });

    var quicksearch = function quicksearch(instance) {
      instance.spinner.hide();
      instance.results.hide();
      instance.view_all.hide();
      instance.no_results.hide(); // Show default categories if no search term is present.

      if (!(instance.input.val() && instance.input.val().trim())) {
        // If a previous ajax request has been sent, abort it.
        if (instance.last_request) {
          instance.last_request.abort();
        }

        instance.last_request = null;
        instance.default.show();
        return;
      } // Show loading animation.


      instance.default.hide();
      instance.spinner.show(); // Prepare query args.

      var params = $.param({
        action: 'mylisting_quick_search',
        security: CASE27.ajax_nonce,
        s: instance.input.val().trim()
      }); // Retrieve search results.

      $.ajax({
        url: CASE27.mylisting_ajax_url,
        type: 'GET',
        dataType: 'json',
        data: params,
        beforeSend: function beforeSend(request) {
          if (instance.last_request) {
            instance.last_request.abort();
          }

          instance.last_request = request;
        },
        success: function success(response) {
          instance.spinner.hide();

          if (!response.content.trim().length) {
            return instance.no_results.show();
          }

          instance.results.html(response.content).show();
          instance.view_all.show();
        }
      });
    };
  });

  /**
   * Select2 extension that adds `dropdownPosition` option.
   * dropdownPosition: auto|below|above
   *
   * @link https://stackoverflow.com/a/47912914/3522553
   */
  (function ($) {
    var Defaults = $.fn.select2.amd.require('select2/defaults');

    $.extend(Defaults.defaults, {
      dropdownPosition: 'auto'
    });

    var AttachBody = $.fn.select2.amd.require('select2/dropdown/attachBody');

    var _positionDropdown = AttachBody.prototype._positionDropdown;

    AttachBody.prototype._positionDropdown = function () {
      var $window = $(window);
      var isCurrentlyAbove = this.$dropdown.hasClass('select2-dropdown--above');
      var isCurrentlyBelow = this.$dropdown.hasClass('select2-dropdown--below');
      var newDirection = null;
      var offset = this.$container.offset();
      offset.bottom = offset.top + this.$container.outerHeight(false);
      var container = {
        height: this.$container.outerHeight(false)
      };
      container.top = offset.top;
      container.bottom = offset.top + container.height;
      var dropdown = {
        height: this.$dropdown.outerHeight(false)
      };
      var viewport = {
        top: $window.scrollTop(),
        bottom: $window.scrollTop() + $window.height()
      };
      var enoughRoomAbove = viewport.top < offset.top - dropdown.height;
      var enoughRoomBelow = viewport.bottom > offset.bottom + dropdown.height;
      var css = {
        left: offset.left,
        top: container.bottom
      }; // Determine what the parent element is to use for calciulating the offset

      var $offsetParent = this.$dropdownParent; // For statically positoned elements, we need to get the element
      // that is determining the offset

      if ($offsetParent.css('position') === 'static') {
        $offsetParent = $offsetParent.offsetParent();
      }

      var parentOffset = $offsetParent.offset();
      css.top -= parentOffset.top;
      css.left -= parentOffset.left;
      var dropdownPositionOption = this.options.get('dropdownPosition');

      if (dropdownPositionOption === 'above' || dropdownPositionOption === 'below') {
        newDirection = dropdownPositionOption;
      } else {
        if (!isCurrentlyAbove && !isCurrentlyBelow) {
          newDirection = 'below';
        }

        if (!enoughRoomBelow && enoughRoomAbove && !isCurrentlyAbove) {
          newDirection = 'above';
        } else if (!enoughRoomAbove && enoughRoomBelow && isCurrentlyAbove) {
          newDirection = 'below';
        }
      }

      if (newDirection == 'above' || isCurrentlyAbove && newDirection !== 'below') {
        css.top = container.top - parentOffset.top - dropdown.height;
      }

      if (newDirection != null) {
        this.$dropdown.removeClass('select2-dropdown--below select2-dropdown--above').addClass('select2-dropdown--' + newDirection);
        this.$container.removeClass('select2-container--below select2-container--above').addClass('select2-container--' + newDirection);
      }

      this.$dropdownContainer.css(css);
    };
  })(window.jQuery);

  MyListing.Select_Config = {
    lastSearch: {},
    diacritics: {},
    stripDiacritics: function stripDiacritics(text) {
      // Used 'uni range + named function' from http://jsperf.com/diacritics/18
      return text.replace(/[^\u0000-\u007E]/g, function (a) {
        return MyListing.Select_Config.diacritics[a] || a;
      });
    }
  };
  /**
   * Select2 support.
   *
   * @link  https://select2.org/
   * @since 1.0
   */

  MyListing.CustomSelect = function (el, args) {
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
      var request_params = _typeof(this.el.data('mylisting-ajax-params')) === 'object' ? this.el.data('mylisting-ajax-params') : {};
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
    } // Init select2.


    this.select = jQuery(el).select2({
      width: '100%',
      minimumResultsForSearch: 10,
      multiple: this.args.multiple,
      allowClear: !this.args.required,
      placeholder: this.args.placeholder,
      dropdownPosition: this.args.dropdownPosition,
      ajax: _typeof(ajax_config) === 'object' ? ajax_config : null,
      tags: this.args.tags,
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
    /**
     * Add support for drag&drop multiselect term reordering.
     *
     * @since 1.0
     */

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
  };
  /**
   * Fires a native custom change event.
   *
   * @since 2.0
   */

  MyListing.CustomSelect.prototype.fireChangeEvent = function (e) {
    var event = document.createEvent('CustomEvent');
    event.initCustomEvent('select:change', false, true, {
      value: jQuery(e.currentTarget).val()
    });
    this.el.get(0).dispatchEvent(event);
  };

  jQuery(function ($) {
    // Get list of diacritics.
    $.fn.select2.amd.require(['select2/diacritics'], function (diacritics) {
      return MyListing.Select_Config.diacritics = diacritics;
    });
    /**
     * Custom sort for select2 search results.
     *
     * @link https://stackoverflow.com/a/32106792/3522553
     */


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
    })(); // List of selectors to apply select2 to.


    function initCustomSelect() {
      $(['.custom-select, .single-product .variations select', '#buddypress div.item-list-tabs#subnav ul li select', '#buddypress #notification-select', '#wc_bookings_field_resource', '#buddypress #messages-select', '#buddypress form#whats-new-form #whats-new-options select', '.settings.privacy-settings #buddypress #item-body > form > p select', '.woocommerce-ordering select', '.c27-submit-listing-form select:not(.ignore-custom-select)', '.ml-admin-listing-form select:not(.ignore-custom-select)'].join(', ')).each(function (i, el) {
        new MyListing.CustomSelect(el);
      });
    }
    initCustomSelect();
    $(document).on('mylisting:refresh-scripts', function () {
      initCustomSelect();
    });
    $('.repeater').each(function (i, el) {
      $(el).repeater({
        initEmpty: true,
        show: function show() {
          $(this).show();
          $(this).find('select').select2({
            minimumResultsForSearch: 0
          });
        }
      }).setList($(el).data('list'));
    });
  });

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
      var request_params = _typeof(this.el.data('mylisting-ajax-params')) === 'object' ? this.el.data('mylisting-ajax-params') : {};
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
    } // Init select2.


    this.select = jQuery(el).select2({
      width: '100%',
      minimumResultsForSearch: 10,
      multiple: this.args.multiple,
      allowClear: !this.args.required,
      placeholder: this.args.placeholder,
      dropdownPosition: this.args.dropdownPosition,
      ajax: _typeof(ajax_config) === 'object' ? ajax_config : null,
      tags: this.args.tags,
      closeOnSelect: false,
      allowHtml: true,
      templateResult: function formatResult(state) {
            if (!state.id) {
                var btn = jQuery('<div class="text-right"><button id="all-branch" style="margin-right: 10px;" class="btn btn-default">Select All</button><button id="clear-branch" class="btn btn-default">Clear All</button></div>')
                return btn;
            }
            
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
    /**
     * Add support for drag&drop multiselect term reordering.
     *
     * @since 1.0
     */

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
  };

  MyListing.CustomSelect2.prototype.test = function (e) {
    console.log('testfunction');
  }

  MyListing.CustomSelect2.prototype.fireChangeEvent = function (e) {
    console.log('event Value');
    var event = document.createEvent('CustomEvent');
    event.initCustomEvent('select:change', false, true, {
      value: jQuery(e.currentTarget).val()
    });
    this.el.get(0).dispatchEvent(event);
  };

  jQuery(function ($) {
    // Get list of diacritics.
    $.fn.select2.amd.require(['select2/diacritics'], function (diacritics) {
      return MyListing.Select_Config.diacritics = diacritics;
    });
    /**
     * Custom sort for select2 search results.
     *
     * @link https://stackoverflow.com/a/32106792/3522553
     */

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
    })(); // List of selectors to apply select2 to.

    function initCustomSelect2() {
      $(['.custom-select, .single-product .variations select', '#buddypress div.item-list-tabs#subnav ul li select', '#buddypress #notification-select', '#wc_bookings_field_resource', '#buddypress #messages-select', '#buddypress form#whats-new-form #whats-new-options select', '.settings.privacy-settings #buddypress #item-body > form > p select', '.woocommerce-ordering select', '.c27-submit-listing-form select:not(.ignore-custom-select)', '.ml-admin-listing-form select:not(.ignore-custom-select)'].join(', ')).each(function (i, el) {
        new MyListing.CustomSelect2(el);
      });
    }
    initCustomSelect2();
    $(document).on('mylisting:refresh-scripts', function () {
      initCustomSelect2();
    });
  });

  /**
   * Handles hierarchical term select fields.
   *
   * @since 2.1
   */
  MyListing.TermHierarchy = function (input) {
    this.input = jQuery(input);
    this.el = this.input.parent();

    if (!(this.input.length && this.el.hasClass('cts-term-hierarchy'))) {
      return;
    }

    this.ajax_params = this.input.data('mylisting-ajax-params');
    this.placeholder = this.input.data('placeholder');
    this.selected = this.input.data('selected') || [];
    this.term_value = this.ajax_params['term-value'] === 'slug' ? 'slug' : 'id';
    this.label = this.el.find('> label');
    this.originalLabel = this.label.length ? this.label.html() : '';
    this.labelTemplate = '<span class="go-back-btn" data-index="%index%"><i class="mi keyboard_backspace"></i> %label%</span>';
    this.template = this.input.data('template') === 'alternate' ? 'alternate' : 'default';
    this.el.addClass('tpl-' + this.template); // handle default value

    if (this.selected.length) {
      this.handleDefaultValue();
    } else {
      // otherwise add empty dropdown
      this.addChildSelect({
        index: 0,
        select: null
      });
    }

    this.label.on('click', function () {
      var backBtn = this.label.find('.go-back-btn');

      if (backBtn.length) {
        var index = parseInt(backBtn.data('index'), 10);
        this.el.find('.select-wrapper.term-select-' + (index - 1) + ' .select2-selection__clear').mousedown();
      }
    }.bind(this)); // Fire native change event.

    this.input.on('change', this.fireChangeEvent.bind(this));
    this.addWrapperClasses();
  };
  /**
   * Determine whether the parent term has any child terms,
   * and add a child term dropdown if so.
   *
   * @since 2.1
   */


  MyListing.TermHierarchy.prototype.maybeAddChildSelect = function (config) {
    var self = this;
    var toRemove = '.term-select.term-select-' + config.index + ', .term-select.term-select-' + config.index + ' ~ .term-select';
    this.el.find(toRemove).find('select').select2('destroy');
    this.el.find(toRemove).remove();

    if (!config.select.val()) {
      return;
    }

    var ajax_params = jQuery.extend({}, config.select.data('mylisting-ajax-params'), {
      page: 1,
      security: CASE27.ajax_nonce,
      search: ''
    }); // some term selects store the term slug as value, others the term id

    ajax_params[this.term_value === 'slug' ? 'parent' : 'parent_id'] = config.select.val();
    this.el.addClass('cts-terms-loading');
    jQuery.ajax({
      url: CASE27.mylisting_ajax_url + '&action=mylisting_list_terms',
      type: 'GET',
      dataType: 'json',
      data: ajax_params,
      beforeSend: function beforeSend(request) {
        if (config.select.data('last_request')) {
          config.select.data('last_request').abort();
        }

        config.select.data('last_request', request);
      },
      success: function success(response) {
        self.el.removeClass('cts-terms-loading');

        if (_typeof(response) === 'object' && response.results && response.results.length) {
          self.addChildSelect(config);
        }
      }
    });
  };
  /**
   * Renders a new dropdown in the hierarchy.
   *
   * @since 2.1
   */


  MyListing.TermHierarchy.prototype.addChildSelect = function (config) {
    var selectWrapper = jQuery('<div class="select-wrapper term-select term-select-' + config.index + '">\
            <select class="custom-select term-select" data-mylisting-ajax="true" data-mylisting-ajax-url="mylisting_list_terms">\
                <option></option>\
            </select>\
        </div>');

    if (this.template === 'alternate') {
      selectWrapper.find('select').data('dropdown-position', 'below');
    }

    if (config.index === 0) {
      var label = this.originalLabel;
      var placeholder = this.placeholder;
    } else {
      var selectedLabel = config.select.find('option[value="' + config.select.val() + '"]').text();
      var placeholder = CASE27.l10n.all_in_category.replace('%s', selectedLabel);
      var label = this.labelTemplate.replace('%index%', config.index).replace('%label%', selectedLabel);
    }

    this.updateLabel(label);
    var ajax_params = jQuery.extend({}, this.ajax_params); // some term selects store the term slug as value, others the term id

    ajax_params[this.term_value === 'slug' ? 'parent' : 'parent_id'] = config.index === 0 ? 0 : config.select.val();
    selectWrapper.find('select').data('mylisting-ajax-params', ajax_params).attr('placeholder', placeholder);
    this.el.append(selectWrapper);
    new MyListing.CustomSelect(selectWrapper.find('select'));
    var parent = this.el.parents('.field-type-term-select');

    if (config.index !== 0 && parent.length) {
      this.input.val('').trigger('change');
    }

    selectWrapper.find('select').on('select:change', function (e) {
      // update the filter value with the selected value, or if empty, with the value of the parent select.
      var parentVal = config.select ? config.select.val() : '';
      this.input.val(e.detail.value || parentVal).trigger('change');
      this.updateLabel(label);
      this.maybeAddChildSelect({
        index: config.index + 1,
        select: jQuery(e.target)
      });
    }.bind(this));
    return selectWrapper;
  };
  /**
   * Apply the default value and render all ancestor terms.
   *
   * @since 2.1
   */


  MyListing.TermHierarchy.prototype.handleDefaultValue = function () {
    var index = 0;
    var select = null;
    this.selected.forEach(function (term) {
      var selectWrapper = this.addChildSelect({
        index: index,
        select: select
      });
      select = selectWrapper.find('select');
      select.append('<option value="' + term.value + '">' + term.label + '</option>').val(term.value);
      index++;
    }.bind(this)); // trigger a change event on the final select element rendered above,
    // to check if it has child terms and display another dropdown if it does.

    select.trigger('change'); // the hidden input must take the value of the last item in the `selected` array.

    var lastItem = this.selected[this.selected.length - 1];
    this.updateLabel(this.labelTemplate.replace('%index%', this.selected.length - 1).replace('%label%', lastItem.label));
    this.input.val(lastItem.value).trigger('change');
  };
  /**
   * Fires a custom change event.
   *
   * @since 2.0
   */


  MyListing.TermHierarchy.prototype.fireChangeEvent = function (e) {
    var event = document.createEvent('CustomEvent');
    event.initCustomEvent('termhierarchy:change', false, true, {
      value: this.input.val()
    });
    this.input.get(0).dispatchEvent(event);
    this.addWrapperClasses();
  };
  /**
   * Update label for alternate template.
   *
   * @since 2.1
   */


  MyListing.TermHierarchy.prototype.updateLabel = function (label) {
    if (!(this.template === 'alternate' && this.label.length)) {
      return;
    }

    this.label.html(label + '<div class="spin-box"></div>');
  };

  MyListing.TermHierarchy.prototype.addWrapperClasses = function () {
    var hasValue = this.input.val().trim();
    this.el[hasValue ? 'addClass' : 'removeClass']('cts-term-filled');
  };
  /**
   * Init term hierarchy fields.
   */


  jQuery(function ($) {
    $('.term-hierarchy-input').each(function (i, el) {
      new MyListing.TermHierarchy($(this));
    });
  });

  document.addEventListener('DOMContentLoaded', function () {
    jQuery('.cts-carousel').each(function (i, el) {
      var prev = el.querySelector('.cts-prev');
      var next = el.querySelector('.cts-next');

      var onScroll = function onScroll() {
        if (jQuery('html').attr('dir') === "rtl") {
          el.scrollWidth - el.offsetWidth + el.scrollLeft > 10 ? next.classList.add('cts-show') : next.classList.remove('cts-show');
          el.scrollLeft < -10 ? prev.classList.add('cts-show') : prev.classList.remove('cts-show');
        } else {
          el.scrollWidth - el.offsetWidth - el.scrollLeft > 10 ? next.classList.add('cts-show') : next.classList.remove('cts-show');
          el.scrollLeft > 10 ? prev.classList.add('cts-show') : prev.classList.remove('cts-show');
        }
      };

      el.addEventListener('scroll', MyListing.Helpers.debounce(onScroll, 20));
      new ResizeSensor(el, MyListing.Helpers.debounce(onScroll, 100));
      onScroll();
    });
  });

  /**
   * Store user last activity on the page to detect idle time.
   * Example usage to detect if window has been idle for 10+ seconds:
   * var isWindowIdle = MyListing.Helpers.getLastActivity() >= 10000;
   *
   * @since 2.2.3
   */
  (function () {
    var lastActivity = Date.now();
    document.onmousemove = setLastActivity;
    document.onkeydown = setLastActivity;
    document.onmousedown = setLastActivity;
    document.ontouchstart = setLastActivity;
    document.onscroll = setLastActivity;

    function setLastActivity() {
      lastActivity = Date.now();
    }

    MyListing.Helpers.getLastActivity = function () {
      return Date.now() - lastActivity;
    };
  })();
  /**
   * If an AJAX request has the `no_idle=1` param, and the window
   * has been idle for 10+ seconds, then abort the request.
   *
   * @since 2.2.3
   */


  jQuery.ajaxPrefilter(function (options, originalOptions, jqXHR) {
    if (originalOptions && originalOptions.data && originalOptions.data.no_idle === 1 && MyListing.Helpers.getLastActivity() >= 10000) {
      jqXHR.abort();
    }
  });
  /**
   * Bookmark listing action.
   *
   * @since 1.0
   */

  MyListing.Handlers.Bookmark_Button = function (event, el) {
    event.preventDefault();
    var el = jQuery(el); // user must be logged in

    if (!jQuery('body').hasClass('logged-in')) {
      return window.location.href = CASE27.login_url;
    } // a bookmark request is already being processed


    if (el.hasClass('bookmarking')) {
      return;
    } // update button ui


    var endpoint = CASE27.mylisting_ajax_url + '&action=bookmark_listing';
    el.addClass('bookmarking').toggleClass('bookmarked');
    el.find('.action-label').html(el.hasClass('bookmarked') ? el.data('active-label') : el.data('label')); // perform ajax bookmark request

    jQuery.get(endpoint, {
      listing_id: el.data('listing-id')
    }, function (response) {
      el.removeClass('bookmarking');
    });
  };
  /**
   * Retrieve the distance between two points in kilometers.
   *
   * @link https://stackoverflow.com/a/27943/3522553
   */


  MyListing.Helpers.coordinatesToDistance = function (lat1, lng1, lat2, lng2) {
    function deg2rad(deg) {
      return deg * (Math.PI / 180);
    }

    var R = 6371;
    var dLat = deg2rad(lat2 - lat1);
    var dLon = deg2rad(lng2 - lng1);
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    var d = R * c;
    return d;
  };

  jQuery(window).on('load', function () {
    jQuery('.galleryPreview, .section-slider.owl-carousel').trigger('refresh.owl.carousel');
  });
  jQuery(document).ready(window.case27_ready_script = function ($) {
    $(document).trigger('mylisting:refresh-scripts');
    /**
     * Temporarily hide some elements until page is fully loaded, to avoid
     * awkward layouts while the JS code handling it hasn't been executed yet.
     *
     * @since 1.7.0
     */

    (function () {
      // Workaround for wp-editor overflow issue on add listing page.
      setTimeout(function () {
        $('#submit-job-form .wp-editor-wrap').css('height', 'auto');
      }, 2500);
    })(); // Localize moment.js


    if (typeof MyListing_Moment_Locale === 'string' && MyListing_Moment_Locale.length) {
      moment.locale(MyListing_Moment_Locale);
    }

    var whatsNewPlaceholder = $('#buddypress form#whats-new-form p.activity-greeting').text();
    jQuery('#whats-new-textarea textarea').attr('placeholder', whatsNewPlaceholder); // mobile menu

    $(".mobile-menu").click(function (e) {
      e.preventDefault();
      $('.i-nav').addClass("mobile-menu-open").css('opacity', '1');
      $('body').addClass('disable-scroll');
    });
    $(".mnh-close-icon").click(function (e) {
      e.preventDefault();
      $('.i-nav').removeClass("mobile-menu-open i-nav-fixed");
      $('body').removeClass('disable-scroll');
      $(window).resize();
    });
    $('.i-nav-overlay').click(function () {
      $(this).siblings('.i-nav').removeClass('mobile-menu-open');
      $('body').removeClass('disable-scroll');
    }); // Sub menu functionality.

    $('.main-nav li .submenu-toggle').click(function () {
      var media_query = window.matchMedia('(max-width:1200px)');

      if (!media_query.matches) {
        return;
      }

      var submenu = $(this).siblings('.i-dropdown');
      var is_open = submenu.hasClass('shown-menu');
      var slideSpeed = 300;

      if (is_open) {
        submenu.slideUp(slideSpeed);
      } else {
        submenu.slideDown(slideSpeed);
        $(this).parent().parent().find('> li > .shown-menu').slideUp(slideSpeed).removeClass('shown-menu');
      }

      submenu.toggleClass('shown-menu');
    }); // Featured class

    var featured = $('.pricing-item.featured');
    $('.pricing-item').hover(function () {
      $(featured).removeClass('featured');
      $(this).addClass('active');
    }, function () {
      $(this).removeClass('active');
      $(featured).addClass('featured');
    }); // Tooltip

    $('[data-toggle="tooltip"]').tooltip({
      trigger: 'hover'
    });
    $('body').on('hover', '.listing-feed-2', function (e) {
      $(this).find('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover'
      });
    });
    $(".fc-type-2 .finder-overlay").on('click', function () {
      $('.fc-type-2').removeClass('fc-type-2-open');
    }); // Owl carousel - Testimonials

    $('.testimonial-carousel.owl-carousel').owlCarousel({
      // loop:true,
      mouseDrag: false,
      items: 1,
      center: true,
      autoplay: true,
      dotsContainer: '#customDots'
    });
    $('.testimonial-image').click(function (e) {
      e.preventDefault();
      $(this).addClass('active').siblings().removeClass('active');
      var slideNo = $(this).data('slide-no');
      $('.testimonial-carousel.owl-carousel').trigger('to.owl.carousel', slideNo);
    }); // Gallery Carousel

    $('.gallery-carousel').each(function (index, el) {
      var items = $(el).data('items') ? $(el).data('items') : 3;
      var items_mobile = $(el).data('items-mobile') ? $(el).data('items-mobile') : 2;
      $(el).owlCarousel({
        margin: 10,
        items: items,
        mouseDrag: false,
        responsive: {
          0: {
            items: items_mobile
          },
          600: {
            items: items > 3 ? 3 : items
          },
          1000: {
            items: items
          }
        }
      });
    });
    $('.gallery-prev-btn').click(function (e) {
      e.preventDefault();
      $(this).parents('.element').find('.gallery-carousel.owl-carousel').trigger('prev.owl.carousel');
    });
    $('.gallery-next-btn').click(function (e) {
      e.preventDefault();
      $(this).parents('.element').find('.gallery-carousel.owl-carousel').trigger('next.owl.carousel');
    });
    $('.full-screen-carousel .owl-carousel').owlCarousel({
      loop: true,
      margin: 10,
      items: 2,
      center: true,
      autoWidth: true
    }); // Section slider
    // $('.section-slider.owl-carousel').owlCarousel({
    //     mouseDrag: false,
    //     loop:true,
    //     // touchDrag: false,
    //     items: 1,
    //     animateOut: 'fadeOut',
    //     callbacks: true,
    //     nav: true,
    //     autoHeight: true,
    //     onInitialized: function() { this.refresh(); },
    // });

    (function () {
      var androidUser = navigator.userAgent.match(/Android/i) != null,
          iOSUser = navigator.userAgent.match(/iPhone|iPad|iPod/i) != null;

      if (androidUser) {
        $('body').addClass('smartphoneuser');
      }

      if (iOSUser) {
        $('body').addClass('smartphoneuser iOSUser');
      }
    })(); // gallerySLider


    $('.galleryPreview').owlCarousel({
      items: 1,
      center: true,
      dotsContainer: '#customDots',
      autoHeight: true
    });
    $('.slide-thumb').click(function (e) {
      e.preventDefault();
      var slideNo = $(this).data('slide-no');
      $('.galleryPreview.owl-carousel').trigger('to.owl.carousel', slideNo);
    }); // Gallery Carousel

    $('.gallery-thumb').each(function (index, el) {
      var items = $(el).data('items') ? $(el).data('items') : 4;
      var items_mobile = $(el).data('items-mobile') ? $(el).data('items-mobile') : 2;
      $(el).owlCarousel({
        margin: 10,
        items: items,
        mouseDrag: false,
        responsive: {
          0: {
            items: items_mobile
          },
          600: {
            items: items > 3 ? 3 : items
          },
          1000: {
            items: items
          }
        }
      });
    });
    $('.gallerySlider .gallery-prev-btn').click(function (e) {
      e.preventDefault();
      $('.gallery-thumb.owl-carousel').trigger('prev.owl.carousel');
    });
    $('.gallerySlider .gallery-next-btn').click(function (e) {
      e.preventDefault();
      $('.gallery-thumb.owl-carousel').trigger('next.owl.carousel');
    }); // Isotope

    var $grid;

    if ($('body').hasClass('rtl')) {
      var $grid = $('.grid').isotope({
        originLeft: false
      });
    } else {
      var $grid = $('.grid').isotope();
    }

    $(window).bind("load resize", function () {
      $grid.isotope('reloadItems').isotope();
    });
    $('.explore-mobile-nav > ul li').on('click', function () {
      setTimeout(function () {
        $grid.isotope('reloadItems').isotope();
      }, 400);
    });
    $('body').on('click', '.fc-search .close-filters-27', function () {
      $grid.isotope('reloadItems').isotope();
    }); // Scroll reveal tabs functionality

    $('.tab-switch').click(function (e) {
      e.preventDefault();
      var self = $(this);
      self.tab('show');
      setTimeout(function () {
        $grid.isotope('reloadItems').isotope();
      }, 400);
    }); // Listing feed carousel

    $('.listing-feed-carousel').owlCarousel({
      loop: true,
      margin: 20,
      items: 3,
      smartSpeed: 500,
      onDrag: slideDragged,
      onDragged: slideChanged,
      responsive: {
        0: {
          items: 1,
          margin: 0
        },
        768: {
          items: 2
        },
        1000: {
          items: 3
        }
      }
    });

    function slideDragged(event) {
      $('.listing-feed-carousel > .owl-item').css('opacity', '1');
    }

    function slideChanged(event) {
      $('.listing-feed-carousel > .owl-item').css('opacity', '0.4');
      $('.listing-feed-carousel > .owl-item.active').css('opacity', '1');
    }

    $('.listing-feed-next-btn').click(function (e) {
      e.preventDefault();
      $(this).parents('.container').find('.listing-feed-carousel.owl-carousel').trigger('next.owl.carousel');
      $(this).parents('.container').find('.listing-feed-carousel > .owl-item').css('opacity', '0.4');
      $(this).parents('.container').find('.listing-feed-carousel > .owl-item.active').css('opacity', '1');
    });
    $('.listing-feed-prev-btn').click(function (e) {
      e.preventDefault();
      $(this).parents('.container').find('.listing-feed-carousel.owl-carousel').trigger('prev.owl.carousel');
      $(this).parents('.container').find('.listing-feed-carousel > .owl-item').css('opacity', '0.4');
      $(this).parents('.container').find('.listing-feed-carousel > .owl-item.active').css('opacity', '1');
    }); // featured section slider

    $('.featured-section-carousel').owlCarousel({
      loop: true,
      margin: 0,
      items: 1,
      center: true
    });
    $('.listing-feed-next-btn').click(function (e) {
      e.preventDefault();
      $('.featured-section-carousel.owl-carousel').trigger('next.owl.carousel');
    });
    $('.listing-feed-prev-btn').click(function (e) {
      e.preventDefault();
      $('.featured-section-carousel.owl-carousel').trigger('prev.owl.carousel');
    }); // Listing feed lf-background carousel

    $('.lf-background-carousel').owlCarousel({
      margin: 20,
      items: 1,
      loop: true
    });
    $('.lf-background-carousel').each(function () {
      $(this).owlCarousel({
        margin: 20,
        items: 1,
        loop: true
      });
      $(this).on('prev.owl.carousel', function (e) {
        e.stopPropagation();
      });
      $(this).on('next.owl.carousel', function (e) {
        e.stopPropagation();
      });
    });
    $('body').on('click', '.lf-item-next-btn', function (e) {
      e.preventDefault();
      var carousel = $(this).parents('.lf-item').find('.lf-background-carousel.owl-carousel');
      carousel.trigger('next.owl.carousel');
    });
    $('body').on('click', '.lf-item-prev-btn', function (e) {
      e.preventDefault();
      var carousel = $(this).parents('.lf-item').find('.lf-background-carousel.owl-carousel');
      carousel.trigger('prev.owl.carousel');
    });
    $('.filter-listing-type-select, .filter-listings-select').on('change', function (e) {
      e.preventDefault();
      var listingTypeUrl = $('.filter-listing-type-select option:selected').val(),
          listingStatusUrl = $('.filter-listings-select option:selected').val(),
          params = [];

      if (listingTypeUrl) {
        var listingType = new URL(listingTypeUrl).searchParams.get('filter_by_type');

        if (listingType) {
          params.push("filter_by_type=" + listingType);
        }
      }

      if (listingStatusUrl) {
        var listingStatus = new URL(listingStatusUrl).searchParams.get('status');

        if (listingStatus) {
          params.push("status=" + listingStatus);
        }
      }

      if (!params.length) {
        return window.location.href = $('.filter-listing-type-select :first').val();
      }

      window.location.href = $('.filter-listing-type-select :first').val() + "?" + params.join('&');
    }); // Clients section slider

    $('.clients-feed-carousel').owlCarousel({
      loop: true,
      margin: 20,
      items: 5,
      responsive: {
        0: {
          items: 3
        },
        600: {
          items: 3
        },
        1000: {
          items: 5
        }
      }
    });
    $('.clients-feed-next-btn').click(function (e) {
      e.preventDefault();
      $('.clients-feed-carousel.owl-carousel').trigger('next.owl.carousel');
    });
    $('.clients-feed-prev-btn').click(function (e) {
      e.preventDefault();
      $('.clients-feed-carousel.owl-carousel').trigger('prev.owl.carousel');
    }); // Header Carousel

    var headerCarouselItems = $('.header-gallery-carousel .item').length;
    $('.header-gallery-carousel').owlCarousel({
      items: Math.min.apply(Math, [3, headerCarouselItems]),
      responsive: {
        0: {
          items: Math.min.apply(Math, [1, headerCarouselItems])
        },
        480: {
          items: Math.min.apply(Math, [2, headerCarouselItems])
        },
        992: {
          items: Math.min.apply(Math, [3, headerCarouselItems])
        }
      }
    }); // Comment reply

    $('body.logged-in .comment-info a').click(function (e) {
      e.preventDefault();
      $(this).parents().siblings('.element').toggleClass('element-visible');
    });
    /**
     * Handles `Back to Top` button in the bottom right corner.
     *
     * @since 2.1
     */

    (function () {
      var scrollTop = $('a.back-to-top');

      if (!scrollTop.length) {
        return;
      }

      var showScrollTop = function showScrollTop() {
        scrollTop.css('visibility', 'visible');
        scrollTop.css('opacity', '1');
      };

      var hideScrollTop = function hideScrollTop() {
        scrollTop.css('opacity', '0');
        setTimeout(function () {
          scrollTop.css('visibility', 'hidden');
        }, 200);
      };

      scrollTop.click(function (e) {
        e.preventDefault();
        hideScrollTop();
        $('html, body').animate({
          scrollTop: 0
        }, 1000);
      });

      var onScroll = function onScroll() {
        $(window).scrollTop() >= 800 ? showScrollTop() : hideScrollTop();
      };

      $(window).scroll(MyListing.Helpers.debounce(onScroll, 200));
      onScroll();
    })();

    jQuery('.c27-quick-view-modal').on('hidden.bs.modal', function (e) {
      $('.c27-quick-view-modal .container').css('height', 'auto');
    });
    $('body').on('click', '.c27-toggle-quick-view-modal', function (e) {
      e.preventDefault();
      $('.c27-quick-view-modal').modal('show');
      $('.c27-quick-view-modal').addClass('loading-modal');
      $.ajax({
        url: CASE27.mylisting_ajax_url + '&action=get_listing_quick_view&security=' + CASE27.ajax_nonce,
        type: 'GET',
        dataType: 'json',
        data: {
          listing_id: $(this).data('id')
        },
        success: function success(response) {
          $('.c27-quick-view-modal').removeClass('loading-modal');
          $('.c27-quick-view-modal .modal-content').html(response.html);
          $('.c27-quick-view-modal .c27-map').css('height', $('.c27-quick-view-modal .modal-content').height());
          $(window).trigger('resize');
          setTimeout(function () {
            new MyListing.Maps.Map($('.c27-quick-view-modal .c27-map').get(0));
          }, 10);
          $('.lf-background-carousel').owlCarousel({
            margin: 20,
            items: 1,
            loop: true
          });
          $('.c27-quick-view-modal .container').each(function (index, el) {
            if ($(el).height() % 2 != 0) {
              $(el).height($(el).height() + 1);
            }
          });
          var mapHeight = $('.c27-quick-view-modal .modal-content').height();
          $('.c27-quick-view-modal .block-map').css('height', mapHeight);
        }
      });
    });
    $('.c27-display-button').each(function (i, el) {
      var container = jQuery(el);
      container.on('click', function () {
        if (container.hasClass('loading') || container.hasClass('loaded')) {
          return;
        }

        var data = {
          listing_id: $(this).data('listing-id'),
          field_id: $(this).data('field-id')
        };
        container.addClass('loading');
        $.post(CASE27.mylisting_ajax_url + '&action=display_contact_info&security=' + CASE27.ajax_nonce, data, function (response) {
          container.removeClass('loading').addClass('loaded');

          if (response.value) {
            container.html(response.value);
          }
        });
      });
    });
    $('#ml-messages-modal, #quicksearch-mobile-modal').on('shown.bs.modal', function () {
      $('body').addClass('disable-scroll');
    }).on('hidden.bs.modal', function () {
      $('body').removeClass('disable-scroll');
    });
    $('.c27-add-product-form input#_virtual').change(function (e) {
      $('.c27-add-product-form .product_shipping_class_wrapper')[$(this).attr('checked') == 'checked' ? 'hide' : 'show']();
    }).change();
    $('.c27-add-product-form input#_sale_price').keyup(function (e) {
      $('.c27-add-product-form ._sale_price_dates_from__wrapper')[$(this).val() ? 'show' : 'hide']();
      $('.c27-add-product-form ._sale_price_dates_to__wrapper')[$(this).val() ? 'show' : 'hide']();
    }).keyup();
    $('.c27-add-product-form input#_manage_stock').change(function (e) {
      $('.c27-add-product-form ._stock__wrapper')[$(this).attr('checked') == 'checked' ? 'show' : 'hide']();
      $('.c27-add-product-form ._backorders__wrapper')[$(this).attr('checked') == 'checked' ? 'show' : 'hide']();
    }).change(); //Find number of items in woocommerce menu

    $('.woocommerce-MyAccount-navigation > ul').each(function () {
      if ($(this).children().length <= 6) $(this).addClass("short");
    });
  });
  /**
   * Render reCAPTCHA. Done manually to fix animation issues on load.
   *
   * @since 2.5.0
   */

  window.cts_render_captcha = function () {
    jQuery('.g-recaptcha').each(function (i, el) {
      grecaptcha.render(el, {
        sitekey: el.dataset.sitekey
      });
      setTimeout(function () {
        return el.style.opacity = 1;
      }, 1000);
    });
  };

  jQuery(document).ready(function ($) {
    $('.main-loader').addClass('loader-hidden');
    setTimeout(function () {
      $('.main-loader').hide();
    }, 600);
    $('body').addClass('c27-site-loaded');
    $('header.header').parents('section.elementor-element').addClass('c27-header-element');
    $('.c27-open-popup-window, .cts-open-popup').click(function (e) {
      e.preventDefault();
      var width = 600;
      var height = 400;
      var top = screen.height / 2 - height / 2;
      var left = screen.width / 2 - width / 2;
      window.open(this.href, 'targetWindow', ['toolbar=no', 'location=no', 'status=no', 'menubar=no', 'scrollbars=yes', 'resizable=yes', 'width=' + width, 'height=' + height, 'top=' + top, 'left=' + left].join(','));
    });
    $('.c27-add-listing-review, .show-review-form, .pa-below-title .listing-rating').click(function (e) {
      e.preventDefault();
      $('.toggle-tab-type-comments').first().click();
      setTimeout(function () {
        $('#commentform textarea[name="comment"]').focus();
      }, 250);
    });
    $('.c27-book-now').click(function (e) {
      e.preventDefault();
      $('.toggle-tab-type-bookings').first().click();
    });
    $('.modal.c27-open-on-load').modal('show');
    $('.c27-open-modal').click(function (e) {
      e.preventDefault();
      var el = $(this);
      $('.modal.in').one('hidden.bs.modal', function () {
        $(el.data('target')).modal('show');
      }).modal('hide'); // $('body').addClass('modal-open');
    });
    $('.featured-search .location-wrapper .geocode-location').click(function (e) {
      var address = $(this).siblings('input');
      MyListing.Geocoder.getUserLocation({
        receivedAddress: function receivedAddress(place) {
          if (!place) {
            return false;
          }

          setTimeout(function () {
            address.trigger('change');
          }, 5);
          address.val(place.address);
        }
      });
    });
    /**
     * Mix content blocks on mobile when two-column layout
     * is used, similar to what happens with masonry layout.
     *
     * @since 1.6.6
     */

    (function () {
      $('body.single-listing .tab-template-two-columns').each(function (i, el) {
        var main_col = $(this).find('.cts-column-wrapper.cts-main-column');
        var side_col = $(this).find('.cts-column-wrapper.cts-side-column');
        var main_blocks = main_col.find('> div').toArray();
        var side_blocks = side_col.find('> div').toArray();
        var view = window.matchMedia('(max-width: 991.5px)').matches ? 'mobile' : 'desktop';

        var relayout = function relayout(force_relayout) {
          var current_view = window.matchMedia('(max-width: 991.5px)').matches ? 'mobile' : 'desktop'; // If it's resized, but view hasn't changed from desktop to mobile or vice-versa,
          // then there's no need to mix content blocks again.

          if (current_view === view && !force_relayout) {
            return false;
          }

          if (current_view === 'mobile') {
            // Combine main and side column content blocks, and output them into the main column container (mobile).
            main_blocks.forEach(function (block, key) {
              $(block).appendTo(main_col);

              if (side_blocks[key]) {
                $(side_blocks[key]).appendTo(main_col);
              }
            });
          } else {
            // Move side column content blocks from the main column to it's original one (for desktop view).
            side_blocks.forEach(function (block, key) {
              $(block).appendTo(side_col);
            });
          } // Save current view;


          view = current_view;
        }; // First time it's run, force relayout if it's mobile view.


        relayout(view === 'mobile'); // Run relayout() on resize.

        $(window).on('resize', MyListing.Helpers.debounce(function () {
          relayout();
        }, 300));
      });
    })();
    /**
     * Handle package selection in Add Listing page.
     *
     * @since 1.6.6
     */


    (function () {
      var select_package = function select_package(el) {
        var pkg = el.parents('.pricing-item');

        if (!pkg.length) {
          return false;
        } // If none selected, automatically select the first package.


        if (pkg.data('selected') === undefined) {
          pkg.find('.owned-product-packages input[name="listing_package"]').first().prop('checked', true);
          return true;
        }

        var selected = parseInt(pkg.data('selected'), 10); // Select package.

        pkg.find('.owned-product-packages input[name="listing_package"][value="' + selected + '"]').prop('checked', true);
        return true;
      };

      $('.cts-pricing-item input[name="listing_package"]').change(function (e) {
        var pkg = $(this).parents('.pricing-item');

        if (!pkg.length) {
          return true;
        }

        pkg.data('selected', $(this).val());
      });
      $('.cts-pricing-item .use-package-toggle').click(function (e) {
        select_package($(this));
      });
      $('.cts-pricing-item .select-plan:not(.cts-trigger-buy-new)').click(function (e) {
        e.preventDefault();

        if (select_package($(this))) {
          // console.log('submitting input ', $('input[name="listing_package"]:checked').val() );
          $('#job_package_selection').submit();
        }
      });
      $('.cts-pricing-item .cts-trigger-buy-new').click(function (e) {
        e.preventDefault();
        var pkg = $(this).parents('.pricing-item');

        if (!pkg.length) {
          return false;
        } // Select buy new package.


        pkg.find('input.cts-buy-new').prop('checked', true); // console.log('buying new ', $('input[name="listing_package"]:checked').val() );

        $('#job_package_selection').submit();
      }); // Maintain compatibility with old paid listings page.

      $('.cts-wcpl-package a.select-plan').on('click', function (e) {
        e.preventDefault();
        $(this).siblings('.c27-job-package-radio-button').prop('checked', true);
        $('#job_package_selection').submit();
      });
    })();
    /**
     * Workaround to avoid the cart item counter pulse
     * animation being triggered on page load.
     *
     * @since 1.7.0
     */


    (function () {
      if (!$('#user-cart-menu').length) {
        return false;
      }

      $(document.body).one('wc_fragments_loaded', function (e) {
        $('#user-cart-menu').addClass('user-cart-updated');
      });
    })();
    /**
     * Copy link to clipboard.
     *
     * @since 1.7.0
     */


    (function () {
      $(document).on('mousedown click', '.c27-copy-link', function (e) {
        e.preventDefault();
        var el = $(this);

        if (el.hasClass('copying')) {
          return;
        }

        el.addClass('copying');
        var content = el.find('span');
        var default_message = content.html();
        var url = el.attr('href'),
            tmp = $('<input>');
        $('body').append(tmp);
        tmp.val(url).select();
        document.execCommand('copy');
        tmp.remove();
        content.html(CASE27.l10n.copied_to_clipboard);
        setTimeout(function () {
          content.html(default_message);
          el.removeClass('copying');
        }, 1500);
      });
    })();

    (function () {
      var header = $('.c27-main-header');

      if (!(header.length && header.hasClass('header-fixed'))) {
        return;
      }

      var lastPosition = null;
      var position = 0;
      var height = header.outerHeight();

      function calc_position() {
        // Get current position.
        position = $(window).scrollTop();

        if (lastPosition === position) {
          return;
        } // Toggle `header-scroll` class.


        position > height || position > height && lastPosition === null ? header.addClass('header-scroll') : header.removeClass('header-scroll'); // Toggle `header-scroll-hide` while scrolling down.

        position > height + 250 ? header.addClass('header-scroll-hide') : header.removeClass('header-scroll-hide'); // Toggle `header-scroll-active` class.

        position > height && position < lastPosition || lastPosition === null ? header.addClass('header-scroll-active') : header.removeClass('header-scroll-active'); // Save current position for use in the next check.

        lastPosition = position;
      }

      $(window).on('scroll', MyListing.Helpers.debounce(function () {
        calc_position();
      }, 20));
    })();

    (function () {
      var header = $('.c27-main-header');

      if (!(header.length && header.hasClass('header-menu-center'))) {
        return;
      }

      var leftHeader = $('.header-left').width();
      var rightHeader = $('.header-right').width();
      var headerWidth = header.find('.header-container').width();
      $('.header-center > .i-nav').css('max-width', headerWidth - leftHeader - rightHeader - 10);
    })();
    /**
     * Modal animations.
     *
     * @since 2.0
     */


    (function () {
      $('.modal-27').on('show.bs.modal', function () {
        $(this).addClass('show-modal');
      });
      $('.modal-27').on('hidden.bs.modal', function () {
        $(this).removeClass('show-modal');
      });
      $('.modal-27').on('hide.bs.modal', function (e) {
        var modal = $(this);

        if (modal.hasClass('in')) {
          e.preventDefault();
          modal.removeClass('in');
          $('body').addClass('modal-closing');
          setTimeout(function () {
            modal.modal('hide');
          }, 200);
        } else {
          $('body').removeClass('modal-closing');
          $('body').addClass('modal-closed');
          setTimeout(function () {
            return $('body').removeClass('modal-closed');
          }, 100);
        }
      });
    })();
    /**
     * Adds support for Elementor columns "Link To" setting.
     *
     * @since 2.0
     */


    (function () {
      $('.elementor-element[data-mylisting-link-to]').each(function () {
        var link_to = $(this).data('mylisting-link-to');

        if (_typeof(link_to) !== 'object' || link_to.url === 'undefined') {
          return;
        }

        var anchor = $('<a class="mylisting-link-to"></a>');
        anchor.attr('href', link_to.url);

        if (link_to.is_external) {
          anchor.attr('target', '_blank');
        }

        if (link_to.nofollow) {
          anchor.attr('rel', 'nofollow');
        }

        var customAttr = link_to.custom_attributes;

        if (customAttr) {
          var attrArray = customAttr.split(",");
          $.each(attrArray, function (index, item) {
            var linkAttr = item.split("|");
            anchor.attr($.trim(linkAttr[0]), $.trim(linkAttr[1]));
          });
        }

        var col_wrap = $(this).find('.elementor-column-wrap');

        if (col_wrap.length) {
          col_wrap.append(anchor);
        } else {
          $(this).find('.elementor-widget-wrap').append(anchor);
        }
      });
    })();
    /**
     * Open the chat conversation from a link.
     *
     * @since 2.1
     */


    (function () {
      $('.cts-open-chat').on('click', function (e) {
        e.preventDefault();
        var user_id = $(this).data('user-id') || null,
            postData = $(this).data('post-data'); // for logged out users, go to login page

        if (!$('body').hasClass('logged-in')) {
          return window.location.href = CASE27.login_url;
        } // open messages


        MyListing.Messages.open(user_id, postData);
        setTimeout(function () {
          $(MyListing.Messages.$el).find('#ml-conv-textarea').focus();
        }, 150);
      });
    })();
    /**
     * Quick search on mobile.
     *
     * @since 2.1
     */


    (function () {
      $('#quicksearch-mobile-modal').on('shown.bs.modal', function (e) {
        e.preventDefault();
        setTimeout(function () {
          $('#quicksearch-mobile-modal input[name="search_keywords"]').focus().get(0).click();
        }, 800);
      });
    })();

    (function () {
      $('.mobile-nav-head .user-profile-name').on('click', function (e) {
        e.preventDefault();
        $('.mobile-user-menu').slideToggle();
      });
    })();

    (function () {
      var commentForm = document.getElementById('commentform');

      if (commentForm) {
        commentForm.removeAttribute('novalidate');
      }
    })();
  });
  /**
   * Listing Reviews JS
   *
   * @since 1.5.0
   */

  jQuery(document).ready(function ($) {
    if (!$('#commentform').length) {
      return false;
    } // Enable upload in comment form.


    $('#commentform')[0].encoding = 'multipart/form-data'; // Remove uploaded gallery image.

    $('body').on('click', '.review-gallery-image-remove', function (e) {
      e.preventDefault();
      $(this).parents('.review-gallery-image').remove();
    }); // Display upload preview.
    // @link https://stackoverflow.com/questions/39439760

    var imagesPreview = function imagesPreview(input, el) {
      if (input.files) {
        var filesAmount = input.files.length;

        for (var i = 0; i < filesAmount; i++) {
          var reader = new FileReader();

          reader.onload = function (event) {
            var img = $("<div class=\"review-gallery-image\">\n\t\t\t\t\t\t\t<span class=\"review-gallery-preview-icon\">\n\t\t\t\t\t\t\t\t<i class=\"material-icons file_upload\"></i>\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t</div>").css('background-image', "url('" + event.target.result + "')");
            $(img).appendTo(el);
          };

          reader.readAsDataURL(input.files[i]);
        }
      }
    };

    $('#review-gallery-add-input').on('change', function () {
      $('#review-gallery-preview').html('');
      imagesPreview(this, '#review-gallery-preview');
    });
  });
  /**
   * Author profile tabs.
   *
   * @since 2.6
   */

  (function ($) {
    var default_tab_id = $('.profile-tab-toggle').first().data('section-id');
    $('.profile-tab-toggle').on('click', function (e) {
      e.preventDefault();
      $('.profile-menu li.active').removeClass('active');
      $(this).parent().addClass('active');
      var currentTab = $('.listing-tab.tab-active');
      var section_id = $(this).data('section-id');
      document.body.dataset.activeTab = section_id;
    });
  })(jQuery);

  jQuery(function ($) {
    if (typeof window.MyListing_Switch_Config === 'undefined') {
      return;
    }

    var target = 'tr.woocommerce-grouped-product-list-item.product-type-job_package_subscription';

    if (!$(target).length) {
      return;
    }

    var config = window.MyListing_Switch_Config;
    $('.single-product ' + target + '#product-' + config.current_plan + ' label a').append('<span>' + config.current_plan_text + '</span>');
    $('.single_add_to_cart_button').hide();
    $(target).click(function (e) {
      e.preventDefault();
      $(e.target).find('input[type="checkbox"]').prop('checked', true);
      $(target).parents('form').submit();
    });
  });

  MyListing.Dialog = function (args) {
    var self = this;
    this.visible = false;
    self.args = jQuery.extend({
      message: '',
      status: 'info',
      // info|warning|success.
      dismissable: true,
      spinner: false,
      timeout: 3000 // close after 3 seconds.

    }, args);
    self.show();
    self.setTimeout();
  };

  MyListing.Dialog.prototype.draw = function () {
    this.template = jQuery(jQuery('#mylisting-dialog-template').text());
    this.template.addClass(this.args.status);
    this.insertContent();
    this.template.appendTo('body');
  };

  MyListing.Dialog.prototype.refresh = function (args) {
    this.args = jQuery.extend(this.args, args);
    this.setTimeout();
    this.insertContent();
  };

  MyListing.Dialog.prototype.insertContent = function () {
    var self = this;
    this.template.find('.mylisting-dialog--message').html(this.args.message);
    this.template.find('.mylisting-dialog--dismiss')[this.args.dismissable ? 'removeClass' : 'addClass']('hide').click(function (e) {
      e.preventDefault();
      self.hide();
    });
    this.template.find('.mylisting-dialog--loading')[this.args.spinner ? 'removeClass' : 'addClass']('hide');
  };
  /**
   * Handle dialog closing timeout.
   *
   * @since 1.7.2
   */


  MyListing.Dialog.prototype.setTimeout = function () {
    var self = this;

    if (self.timeout) {
      clearTimeout(self.timeout);
    }

    if (!isNaN(self.args.timeout) && self.args.timeout > 0) {
      self.timeout = setTimeout(function () {
        self.hide();
      }, self.args.timeout);
    }
  };

  MyListing.Dialog.prototype.show = function () {
    var self = this;
    self.draw();
    setTimeout(function () {
      self.template.addClass('slide-in');
      self.visible = true;
    }, 15);
  };

  MyListing.Dialog.prototype.hide = function () {
    var self = this;
    self.template.removeClass('slide-in').addClass('slide-out');
    setTimeout(function () {
      self.template.remove();
      self.visible = false;
    }, 250);
  };

  /**
   * Component for rendering a `wp-search` filter in Explore page.
   *
   * @since 2.4
   */
  Vue.component('wp-search-filter', {
    props: {
      listingType: String,
      filterKey: String,
      location: String,
      label: String
    },
    mounted: function mounted() {
      var _this = this;

      this.$nextTick(function () {
        _this.$root.$on('reset-filters:' + _this.listingType, function () {
          _this.filters[_this.filterKey] = '';
        });
      });
    },
    methods: {
      updateInput: function updateInput() {
        this.filters[this.filterKey] = this.$refs.input.value;
        this.$emit('input', this.$refs.input.value, {
          filterType: this.$options.name,
          filterKey: this.filterKey,
          location: this.location
        });
      }
    },
    computed: {
      filters: function filters() {
        return this.$root.types[this.listingType].filters;
      }
    }
  });

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
            new MyListing.CustomSelect2( this.$refs.select );
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
} );

  /**
   * Component for rendering a `text` filter in Explore page.
   *
   * @since 2.4
   */
  Vue.component('text-filter', {
    props: {
      listingType: String,
      filterKey: String,
      location: String,
      label: String
    },
    mounted: function mounted() {
      var _this = this;

      this.$nextTick(function () {
        _this.$root.$on('reset-filters:' + _this.listingType, function () {
          _this.filters[_this.filterKey] = '';
        });
      });
    },
    methods: {
      updateInput: function updateInput() {
        this.filters[this.filterKey] = this.$refs.input.value;
        this.$emit('input', this.$refs.input.value, {
          filterType: this.$options.name,
          filterKey: this.filterKey,
          location: this.location
        });
      }
    },
    computed: {
      filters: function filters() {
        return this.$root.types[this.listingType].filters;
      }
    }
  });

  /**
   * Component for rendering a `location` filter in Explore page.
   *
   * @since 2.4
   */
  Vue.component('location-filter', {
    props: {
      listingType: String,
      filterKey: String,
      location: String,
      label: String
    },
    data: function data() {
      return {
        latitudeKey: 'lat',
        longitudeKey: 'lng'
      };
    },
    created: function created() {
      this.$root.$on('request-location:' + this.listingType, this.requestALocation);
    },
    mounted: function mounted() {
      var _this = this;

      this.$nextTick(function () {
        if (MyListing.Maps && MyListing.Maps.loaded) {
          new MyListing.Maps.Autocomplete(_this.$refs.input);
        } else {
          jQuery(document).on('maps:loaded', function () {
            new MyListing.Maps.Autocomplete(_this.$refs.input);
          });
        } // hide on scroll due to dropdown not being positioned relative to the scrolling container


        jQuery(_this.$root.$el).find('.finder-search').on('scroll', MyListing.Helpers.debounce(function (e) {
          jQuery('.pac-container').css('display', 'none');
          jQuery('.cts-autocomplete-dropdown').removeClass('active');
        }, 100, {
          leading: true,
          trailing: false
        }));

        _this.$root.$on('reset-filters:' + _this.listingType, function () {
          _this.filters[_this.filterKey] = '';
          _this.filters[_this.latitudeKey] = false;
          _this.filters[_this.longitudeKey] = false;
        });
      });
    },
    methods: {
      /**
       * Handles the `autocomplete:change` event on the address input.
       *
       * If one of the autocomplete suggestions has been clicked, the
       * input is updated with the provided address and coordinates.
       *
       * Otherwise, if the user has only typed a location but has not
       * clicked any of the suggestions, a custom (debounced) geocode
       * request is performed.
       *
       * @since 2.4
       */
      handleAutocomplete: function handleAutocomplete(e) {
        var _this2 = this;

        var place = e.detail.place;

        if (!e.target.value.length) {
          this.updateInput({
            address: '',
            latitude: false,
            longitude: false
          });
        } else if (place.address && place.latitude && place.longitude) {
          this.updateInput(place);
        } else {
          MyListing.Geocoder.geocode(e.target.value, function (place) {
            if (place) {
              place.address = e.target.value;

              _this2.updateInput(place);
            }
          });
        }
      },

      /**
       * Updates the model values and fires an `input` event.
       *
       * @since 2.4
       */
      updateInput: function updateInput(place) {
        var debounce = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
        var forceGet = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
        this.filters[this.filterKey] = place.address;
        this.filters[this.latitudeKey] = place.latitude;
        this.filters[this.longitudeKey] = place.longitude;
        this.$emit('input', this.filters[this.filterKey], {
          filterType: this.$options.name,
          filterKey: this.filterKey,
          location: this.location,
          shouldDebounce: debounce,
          forceGet: forceGet
        });
      },

      /**
       * Try to retrieve the user address and coordinates; update
       * the filter value if successful.
       *
       * @since 2.4
       */
      updateWithUserLocation: MyListing.Helpers.debounce(function () {
        var _this3 = this;

        MyListing.Geocoder.getUserLocation({
          receivedAddress: function receivedAddress(place) {
            return _this3.updateInput(place);
          },
          geolocationFailed: function geolocationFailed() {
            new MyListing.Dialog({
              message: CASE27.l10n.geolocation_failed
            });
          }
        });
      }, 1000, {
        leading: true,
        trailing: false
      }),

      /**
       * Try to make the filter have a (any) valid address and coordinates.
       *
       * @since 2.4
       */
      requestALocation: function requestALocation() {
        var _this4 = this;

        var place = this.currentLocation; // if an address and coordinates are already provided, use them

        if (place.address && place.latitude && place.longitude) {
          return this.updateInput(place, false, true);
        } // if only an address is provided, perform a geocoding request
        // to retrieve coordinates


        if (place.address && !(place.latitude || place.longitude)) {
          var cache = CASE27_Explore_Settings.Cache;

          if (typeof cache.defaultLocation !== 'undefined') {
            this.updateInput(cache.defaultLocation, true, true);
          } else {
            MyListing.Geocoder.geocode(place.address, function (place) {
              cache.defaultLocation = {
                address: place ? place.address : '',
                latitude: place ? place.latitude : false,
                longitude: place ? place.longitude : false
              };

              _this4.updateInput(cache.defaultLocation, true, true);
            });
          }

          return;
        } // otherwise, try to populate the filter with the user location


        var dialog = new MyListing.Dialog({
          message: CASE27.l10n.nearby_listings_retrieving_location,
          timeout: false,
          dismissable: false,
          spinner: true
        });
        MyListing.Geocoder.getUserLocation({
          receivedAddress: function receivedAddress(place) {
            _this4.updateInput(place, true, true);

            dialog.refresh({
              message: CASE27.l10n.nearby_listings_searching,
              timeout: 2000,
              spinner: true,
              dismissable: false
            });
          },
          geolocationFailed: function geolocationFailed() {
            dialog.refresh({
              message: CASE27.l10n.nearby_listings_location_required,
              timeout: 4000,
              dismissable: true,
              spinner: false
            }); // load a set of results anyway

            _this4.updateInput(_this4.currentLocation, true, true);

            jQuery(_this4.$refs.input).focus().one('input', function () {
              return dialog.hide();
            });
          }
        });
      }
    },
    computed: {
      currentLocation: function currentLocation() {
        return {
          address: this.filters[this.filterKey] ? this.filters[this.filterKey] : '',
          latitude: this.filters[this.latitudeKey],
          longitude: this.filters[this.longitudeKey]
        };
      },
      filters: function filters() {
        return this.$root.types[this.listingType].filters;
      }
    }
  });

  /**
   * Component for rendering a `range` filter in Explore page.
   *
   * @since 2.4
   */
  Vue.component('proximity-filter', {
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
    data: function data() {
      return {
        locked: false
      };
    },
    mounted: function mounted() {
      var _this = this;

      this.$nextTick(function () {
        // init range slider
        jQuery(_this.$refs.slider).slider({
          range: 'min',
          min: 0,
          max: _this.max,
          step: _this.step,
          slide: _this.updateInput,
          value: _this.filters[_this.filterKey] ? parseFloat(_this.filters[_this.filterKey]) : _this.default
        });

        _this.$root.$on('reset-filters:' + _this.listingType, function () {
          _this.filters[_this.filterKey] = _this.default;

          _this.updateUI();
        });
      });
    },
    methods: {
      updateInput: function updateInput(event, ui) {
        if (this.locked) {
          return;
        }

        this.filters[this.filterKey] = ui.value;
        this.$emit('input', ui.value, {
          filterType: this.$options.name,
          filterKey: this.filterKey,
          location: this.location
        });
      },
      updateUI: function updateUI() {
        this.locked = true;
        var value = this.filters[this.filterKey] ? parseFloat(this.filters[this.filterKey]) : this.default;
        jQuery(this.$refs.slider).slider('value', value);
        this.locked = false;
      }
    },
    computed: {
      displayValue: function displayValue() {
        var value = !isNaN(parseFloat(this.filters[this.filterKey])) ? parseFloat(this.filters[this.filterKey]).toLocaleString() : this.filters[this.filterKey];
        return "".concat(this.label, " ").concat(value).concat(this.units);
      },
      filters: function filters() {
        return this.$root.types[this.listingType].filters;
      }
    }
  });

  /**
   * Component for rendering a `date` filter in Explore page.
   *
   * @since 2.4
   */
  Vue.component('date-filter', {
    props: {
      listingType: String,
      filterKey: String,
      location: String,
      label: String,
      type: String,
      l10n: Object
    },
    data: function data() {
      return {
        startDate: '',
        endDate: '',
        dateFormat: 'YYYY-MM-DD',
        locked: false,
        startPicker: null,
        endPicker: null
      };
    },
    created: function created() {
      var values = this.filters[this.filterKey].split('..');
      var startDate = moment(values[0] ? values[0] : '');
      var endDate = moment(values[1] ? values[1] : '');
      this.startDate = startDate.isValid() ? startDate.clone().locale('en').format(this.dateFormat) : '';
      this.endDate = endDate.isValid() ? endDate.clone().locale('en').format(this.dateFormat) : '';
    },
    mounted: function mounted() {
      var _this = this;

      this.$nextTick(function () {
        _this.startPicker = new MyListing.Datepicker(_this.$refs.startpicker);
        _this.endPicker = new MyListing.Datepicker(_this.$refs.endpicker); // hide on scroll due to datepicker not being positioned relative to the scrolling container

        jQuery(_this.$root.$el).find('.finder-search').on('scroll', MyListing.Helpers.debounce(function (e) {
          _this.startPicker.drp.hide();

          if (_this.endPicker.drp) {
            _this.endPicker.drp.hide();
          }
        }, 100, {
          leading: true,
          trailing: false
        }));

        _this.$root.$on('reset-filters:' + _this.listingType, function () {
          _this.locked = true;
          _this.startDate = _this.endDate = '';

          _this.startPicker.setValue(moment(''));

          _this.endPicker.setValue(moment(''));

          _this.locked = false;
        });
      });
    },
    methods: {
      updateInput: function updateInput() {
        if (this.type === 'exact') {
          var value = this.startDate;
        } else if (this.startDate || this.endDate) {
          var value = "".concat(this.startDate, "..").concat(this.endDate);
        } else {
          var value = '';
        }

        this.filters[this.filterKey] = value;

        if (!this.locked) {
          this.$emit('input', value, {
            filterType: this.$options.name,
            filterKey: this.filterKey,
            location: this.location,
            shouldDebounce: false
          });
        }
      }
    },
    computed: {
      filters: function filters() {
        return this.$root.types[this.listingType].filters;
      }
    }
  });

  /**
   * Component for rendering a `date-year` filter in Explore page.
   *
   * @since 2.4
   */
  Vue.component('date-year-filter', {
    props: {
      listingType: String,
      filterKey: String,
      location: String,
      choices: Array,
      label: String,
      type: String,
      l10n: Object
    },
    data: function data() {
      return {
        startDate: '',
        endDate: '',
        dateFormat: 'YYYY',
        locked: false
      };
    },
    created: function created() {
      var values = this.filters[this.filterKey].split('..');
      var startDate = moment(values[0] ? values[0] : '');
      var endDate = moment(values[1] ? values[1] : '');
      this.startDate = startDate.isValid() ? startDate.clone().locale('en').format(this.dateFormat) : '';
      this.endDate = endDate.isValid() ? endDate.clone().locale('en').format(this.dateFormat) : '';
    },
    mounted: function mounted() {
      var _this = this;

      this.$nextTick(function () {
        new MyListing.CustomSelect(_this.$refs.startpicker);
        new MyListing.CustomSelect(_this.$refs.endpicker);

        _this.$root.$on('reset-filters:' + _this.listingType, function () {
          _this.locked = true;
          _this.startDate = _this.endDate = '';
          jQuery(_this.$refs.startpicker).val('').trigger('change').trigger('select2:close');
          jQuery(_this.$refs.endpicker).val('').trigger('change').trigger('select2:close');
          _this.locked = false;
        });
      });
    },
    methods: {
      updateInput: function updateInput() {
        if (this.type === 'exact') {
          var value = this.startDate;
        } else if (this.startDate || this.endDate) {
          var value = "".concat(this.startDate, "..").concat(this.endDate);
        } else {
          var value = '';
        }

        this.filters[this.filterKey] = value;

        if (!this.locked) {
          this.$emit('input', value, {
            filterType: this.$options.name,
            filterKey: this.filterKey,
            location: this.location,
            shouldDebounce: false
          });
        }
      }
    },
    computed: {
      filters: function filters() {
        return this.$root.types[this.listingType].filters;
      }
    }
  });

  /**
   * Component for rendering a `recurring-date` filter in Explore page.
   *
   * @since 2.4
   */
  Vue.component('recurring-date-filter', {
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
    data: function data() {
      return {
        selected: '@custom',
        startDate: '',
        endDate: '',
        dateFormat: this.enableTimepicker ? 'YYYY-MM-DD HH:mm' : 'YYYY-MM-DD',
        locked: false,
        startPicker: null,
        endPicker: null
      };
    },
    created: function created() {
      var value = this.filters[this.filterKey];

      if (this.enableDatepicker && value.indexOf('..') !== -1) {
        var startDate = moment(value.split('..')[0]);
        var endDate = moment(value.split('..')[1]);
        this.selected = '@custom';
        this.startDate = startDate.isValid() ? startDate.clone().locale('en').format(this.dateFormat) : '';
        this.endDate = endDate.isValid() ? endDate.clone().locale('en').format(this.dateFormat) : '';
      } else {
        var preset = this.presets.find(function (p) {
          return p.key === value;
        });
        this.selected = preset ? preset.key : this.presets.length ? this.presets[0].key : '@custom';
      }
    },
    mounted: function mounted() {
      var _this = this;

      this.$nextTick(function () {
        var timepicker = _this.enableTimepicker;
        _this.startPicker = new MyListing.Datepicker(_this.$refs.start, {
          timepicker: timepicker
        });
        _this.endPicker = new MyListing.Datepicker(_this.$refs.end, {
          timepicker: timepicker
        });

        _this.endPicker.do(function (picker) {
          return picker.drp.minDate = moment(_this.startDate);
        }); // hide on scroll due to datepicker not being positioned relative to the scrolling container


        jQuery(_this.$root.$el).find('.finder-search').on('scroll', MyListing.Helpers.debounce(function (e) {
          _this.startPicker.drp.hide();

          _this.endPicker.drp.hide();
        }, 100, {
          leading: true,
          trailing: false
        }));
        new MyListing.CustomSelect(_this.$refs.select);

        _this.$root.$on('reset-filters:' + _this.listingType, function () {
          _this.locked = true;
          _this.selected = _this.presets.length ? _this.presets[0].key : '@custom';

          _this.startPicker.setValue(moment(''));

          _this.endPicker.setValue(moment(''));

          _this.locked = false;
        });
      });
    },
    methods: {
      setPreset: function setPreset(key) {
        if (this.selected !== key) {
          this.selected = key;
          this.updateInput();
        }
      },
      updateInput: function updateInput() {
        var _this2 = this;

        if (this.selected === '@custom' && this.enableDatepicker) {
          this.endPicker.do(function (picker) {
            return picker.drp.minDate = moment(_this2.startDate);
          });

          if (!this.startDate) {
            this.endDate = '';
            this.endPicker.do(function (picker) {
              picker.value = moment('');
              picker.updateInputValues();
            });
          }

          var value = this.startDate ? "".concat(this.startDate, "..").concat(this.endDate) : '';
        } else if (this.selected !== '@custom') {
          // handle preset range selection
          if (this.presets.length && this.presets[0].key === this.selected) {
            // if the default preset is selected, we don't have to pass it to the request
            var value = '';
          } else {
            // otherwise, pass the preset key
            var value = this.selected;
          }
        }

        this.filters[this.filterKey] = value;

        if (!this.locked) {
          this.$emit('input', value, {
            filterType: this.$options.name,
            filterKey: this.filterKey,
            location: this.location,
            shouldDebounce: false
          });
        }
      }
    },
    computed: {
      filters: function filters() {
        return this.$root.types[this.listingType].filters;
      }
    }
  });

  /**
   * Component for rendering a `range` filter in Explore page.
   *
   * @since 2.4
   */
  Vue.component('range-filter', {
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
    data: function data() {
      return {
        defaultValue: this.type === 'range' ? "".concat(this.min, "..").concat(this.max) : this.behavior === 'upper' ? this.min : this.max,
        locked: false,
        debounceUIUpdate: MyListing.Helpers.debounce(this.updateUI, 200)
      };
    },
    mounted: function mounted() {
      var _this = this;

      this.$nextTick(function () {
        // slider options
        var sliderOpts = {
          range: _this.type === 'single' ? 'min' : true,
          min: _this.min,
          max: _this.max,
          step: _this.step,
          slide: _this.updateInput
        }; // set default value for single slider

        if (_this.type === 'single') {
          sliderOpts.value = _this.value ? parseFloat(_this.value) : _this.behavior === 'upper' ? _this.min : _this.max;

          if (_this.behavior === 'upper') {
            sliderOpts.classes = {
              'ui-slider': 'reverse-dir'
            };
          }
        } // set default values for range slider


        if (_this.type === 'range') {
          var values = _this.value.split('..');

          sliderOpts.values = [values[0] ? parseFloat(values[0]) : _this.min, values[1] ? parseFloat(values[1]) : _this.max];
        } // init range slider


        jQuery(_this.$refs.slider).slider(sliderOpts);

        _this.$root.$on('reset-filters:' + _this.listingType, function () {
          _this.filters[_this.filterKey] = '';

          _this.updateUI();
        });
      });
    },
    methods: {
      updateInput: function updateInput(event, ui) {
        if (this.locked) {
          return;
        }

        if (this.type === 'single') {
          if (this.step + ui.value > this.max) {
            this.filters[this.filterKey] = ui.value = this.max;
            this.updateUI();
          }
        } else {
          if (this.step + ui.values[1] > this.max) {
            ui.values[1] = this.max;
            this.filters[this.filterKey] = "".concat(ui.values[0], "..").concat(ui.values[1]);
            this.updateUI();
          }
        }

        var slider_value = this.type === 'single' ? ui.value : "".concat(ui.values[0], "..").concat(ui.values[1]);
        var value = slider_value !== this.defaultValue ? slider_value : '';
        this.$set(this.filters, this.filterKey, value);
        this.$emit('input', value, {
          filterType: this.$options.name,
          filterKey: this.filterKey,
          location: this.location
        });
      },
      updateUI: function updateUI() {
        this.locked = true;
        var value = this.filters[this.filterKey] ? this.filters[this.filterKey] : this.defaultValue;
        this.type === 'single' ? jQuery(this.$refs.slider).slider('value', value) : jQuery(this.$refs.slider).slider('values', value.split('..'));
        this.locked = false;
      }
    },
    computed: {
      displayValue: function displayValue() {
        var value = this.filters[this.filterKey] ? this.filters[this.filterKey] : this.defaultValue;

        if (this.type === 'single') {
          var value = !isNaN(parseFloat(value)) && this.formatValue ? parseFloat(value).toLocaleString() : value;
          return "".concat(this.prefix).concat(value).concat(this.suffix);
        }

        var values = value.split('..');
        var start = !isNaN(parseFloat(values[0])) && this.formatValue ? parseFloat(values[0]).toLocaleString() : values[0];
        var end = !isNaN(parseFloat(values[1])) && this.formatValue ? parseFloat(values[1]).toLocaleString() : values[1];
        return jQuery('body').hasClass('rtl') ? "".concat(this.prefix).concat(end).concat(this.suffix, " \u2014 ").concat(this.prefix).concat(start).concat(this.suffix) : "".concat(this.prefix).concat(start).concat(this.suffix, " \u2014 ").concat(this.prefix).concat(end).concat(this.suffix);
      },
      targetFilter: function targetFilter() {
        return this.filters[this.filterKey];
      },
      filters: function filters() {
        return this.$root.types[this.listingType].filters;
      }
    },
    watch: {
      targetFilter: function targetFilter() {
        this.debounceUIUpdate();
      }
    }
  });

  /**
   * Component for rendering a `dropdown` filter in Explore page.
   *
   * @since 2.4
   */
  Vue.component('dropdown-filter', {
    props: {
      listingType: String,
      filterKey: String,
      location: String,
      label: String,
      multiple: Boolean,
      choices: Array
    },
    data: function data() {
      return {
        selected: this.multiple ? [] : ''
      };
    },
    created: function created() {
      this.selected = this.multiple ? this.filters[this.filterKey].split(',') : this.filters[this.filterKey];
    },
    mounted: function mounted() {
      var _this = this;

      this.$nextTick(function () {
        new MyListing.CustomSelect(_this.$refs.select);

        _this.$root.$on('reset-filters:' + _this.listingType, function () {
          _this.selected = _this.multiple ? [] : '';
          _this.filters[_this.filterKey] = '';
          jQuery(_this.$refs.select).val(_this.selected).trigger('change').trigger('select2:close');
        });
      });
    },
    methods: {
      handleChange: function handleChange(e) {
        this.selected = this.multiple ? Array.isArray(e.detail.value) ? e.detail.value : [] : typeof e.detail.value === 'string' ? e.detail.value : '';
        this.updateInput();
      },
      updateInput: function updateInput() {
        var value = this.multiple ? this.selected.filter(Boolean).join(',') : this.selected;
        this.filters[this.filterKey] = value;
        this.$emit('input', value, {
          filterType: this.$options.name,
          filterKey: this.filterKey,
          location: this.location
        });
      },
      isSelected: function isSelected(choice) {
        if (!this.multiple) {
          return choice === this.selected;
        }

        return this.selected.includes(choice);
      }
    },
    computed: {
      filters: function filters() {
        return this.$root.types[this.listingType].filters;
      }
    }
  });

  /**
   * Component for rendering a `dropdown-terms` filter in Explore page.
   *
   * @since 2.4
   */
  Vue.component('dropdown-terms-filter', {
    props: {
      listingType: String,
      filterKey: String,
      location: String,
      ajaxParams: String,
      label: String,
      preSelected: Array
    },
    data: function data() {
      return {
        selected: []
      };
    },
    created: function created() {
      this.selected = this.filters[this.filterKey].split(',');
    },
    mounted: function mounted() {
      var _this = this;

      this.$nextTick(function () {
        new MyListing.CustomSelect(_this.$refs.select);

        _this.$root.$on('reset-filters:' + _this.listingType, function () {
          _this.selected = _this.filters[_this.filterKey] = [];
          jQuery(_this.$refs.select).val([]).trigger('change').trigger('select2:close');
        });
      });
    },
    methods: {
      handleChange: function handleChange(e) {
        this.selected = Array.isArray(e.detail.value) ? e.detail.value : [];
        this.updateInput();
      },
      updateInput: function updateInput() {
        var value = this.selected.join(',');
        this.filters[this.filterKey] = value;
        this.$emit('input', value, {
          filterType: this.$options.name,
          filterKey: this.filterKey,
          location: this.location
        });
      }
    },
    computed: {
      filters: function filters() {
        return this.$root.types[this.listingType].filters;
      }
    }
  });

  /**
   * Component for rendering a `dropdown-hierarchy` filter in Explore page.
   *
   * @since 2.4
   */
  Vue.component('dropdown-hierarchy-filter', {
    props: {
      listingType: String,
      filterKey: String,
      location: String,
      label: String,
      ajaxParams: String,
      preSelected: String
    },
    data: function data() {
      return {
        selected: ''
      };
    },
    created: function created() {
      this.selected = this.filters[this.filterKey];
    },
    mounted: function mounted() {
      var _this = this;

      this.$nextTick(function () {
        new MyListing.TermHierarchy(_this.$refs.input);

        _this.$root.$on('reset-filters:' + _this.listingType, function () {
          _this.selected = _this.filters[_this.filterKey] = '';
          jQuery(_this.$el).find('.term-select-0 select').val('').trigger('change').trigger('select2:close');
        });
      });
    },
    methods: {
      handleChange: function handleChange(e) {
        this.selected = typeof e.detail.value === 'string' ? e.detail.value : '';
        this.updateInput();
      },
      updateInput: function updateInput() {
        this.filters[this.filterKey] = this.selected;
        this.$emit('input', this.selected, {
          filterType: this.$options.name,
          filterKey: this.filterKey,
          location: this.location
        });
      }
    },
    computed: {
      filters: function filters() {
        return this.$root.types[this.listingType].filters;
      }
    }
  });

  /**
   * Component for rendering a `checkboxes` filter in Explore page.
   *
   * @since 2.4
   */
  Vue.component('checkboxes-filter', {
    props: {
      listingType: String,
      filterKey: String,
      location: String,
      label: String,
      multiple: Boolean,
      choices: Array
    },
    data: function data() {
      return {
        selected: this.multiple ? [] : ''
      };
    },
    created: function created() {
      this.selected = this.multiple ? this.filters[this.filterKey].split(',') : this.filters[this.filterKey];
    },
    mounted: function mounted() {
      var _this = this;

      this.$nextTick(function () {
        if (_this.$refs.select) {
          new MyListing.CustomSelect(_this.$refs.select);
        }

        _this.$root.$on('reset-filters:' + _this.listingType, function () {
          _this.selected = _this.multiple ? [] : '';
          _this.filters[_this.filterKey] = '';
        });
      });
    },
    methods: {
      updateInput: function updateInput() {
        var value = this.multiple ? this.selected.filter(Boolean).join(',') : this.selected;
        this.filters[this.filterKey] = value;
        this.$emit('input', value, {
          filterType: this.$options.name,
          filterKey: this.filterKey,
          location: this.location,
          shouldDebounce: this.$refs.workHourRanges ? false : true
        });
      }
    },
    computed: {
      filters: function filters() {
        return this.$root.types[this.listingType].filters;
      },
      filterId: function filterId() {
        return "fid:".concat(this.listingType, "-").concat(this.filterKey, "-").concat(this._uid);
      }
    }
  });

  /**
   * Component for rendering a `related-listing` filter in Explore page.
   *
   * @since 2.4
   */
  Vue.component('related-listing-filter', {
    props: {
      listingType: String,
      filterKey: String,
      location: String,
      ajaxParams: String,
      label: String,
      preSelected: Array,
      multiple: Boolean
    },
    data: function data() {
      return {
        selected: this.multiple ? [] : ''
      };
    },
    created: function created() {
      this.selected = this.multiple ? this.filters[this.filterKey].split(',') : this.filters[this.filterKey];
    },
    mounted: function mounted() {
      var _this = this;

      this.$nextTick(function () {
        _this.$refs.select.dataset.mylistingAjax = true;
        _this.$refs.select.dataset.mylistingAjaxUrl = 'mylisting_list_posts';
        _this.$refs.select.dataset.mylistingAjaxParams = _this.ajaxParams;
        new MyListing.CustomSelect(_this.$refs.select);

        _this.$root.$on('reset-filters:' + _this.listingType, function () {
          _this.selected = _this.filters[_this.filterKey] = _this.multiple ? [] : '';
          jQuery(_this.$refs.select).val(_this.selected).trigger('change').trigger('select2:close');
        });
      });
    },
    methods: {
      handleChange: function handleChange(e) {
        this.selected = this.multiple ? Array.isArray(e.detail.value) ? e.detail.value : [] : typeof e.detail.value === 'string' ? e.detail.value : '';
        this.updateInput();
      },
      updateInput: function updateInput() {
        var value = this.multiple ? this.selected.join(',') : this.selected;
        this.filters[this.filterKey] = value;
        this.$emit('input', value, {
          filterType: this.$options.name,
          filterKey: this.filterKey,
          location: this.location
        });
      }
    },
    computed: {
      filters: function filters() {
        return this.$root.types[this.listingType].filters;
      }
    }
  });

  /**
   * Component for rendering an `order` filter in Explore page.
   *
   * @since 2.4
   */
  Vue.component('order-filter', {
    props: {
      listingType: String,
      filterKey: String,
      choices: Array,
      location: String,
      label: String
    },
    data: function data() {
      return {
        locked: false
      };
    },
    mounted: function mounted() {
      var _this = this;

      this.$nextTick(function () {
        // init custom select
        new MyListing.CustomSelect(_this.$refs.select); // use the first choice as default sort value, unless another value is passed as a url param

        var defaultValue = _this.filters[_this.filterKey];

        if (!(defaultValue && _this.choices.find(function (a) {
          return a.key === defaultValue;
        })) && _this.choices.length) {
          _this.filters[_this.filterKey] = _this.choices[0].key;

          _this.updateUI();
        }

        _this.$root.$on('reset-filters:' + _this.listingType, function () {
          var defaultValue = _this.choices.length ? _this.choices[0].key : null;
          _this.filters[_this.filterKey] = defaultValue;

          _this.updateUI();
        });
      });
    },
    methods: {
      updateInput: function updateInput() {
        if (this.locked) {
          return;
        }

        this.filters[this.filterKey] = this.$refs.select.value;

        if (this.hasNote(this.filters[this.filterKey], 'has-proximity-clause')) {
          this.$root.$emit('request-location:' + this.listingType);
        } else {
          this.$emit('input', this.$refs.select.value, {
            filterType: this.$options.name,
            filterKey: this.filterKey,
            location: this.location,
            shouldDebounce: false
          });
        }
      },

      /**
       * Update component to reflect it's new value, without
       * re-triggering any change/input events.
       *
       * @since 2.4
       */
      updateUI: function updateUI() {
        this.locked = true;
        jQuery(this.$refs.select).val(this.filters[this.filterKey]).trigger('change').trigger('select2:close');
        this.locked = false;
      },
      hasNote: function hasNote(choice, note) {
        var choice = this.choices.find(function (a) {
          return a.key === choice;
        });

        if (!(choice && choice.notes)) {
          return false;
        }

        return choice.notes.indexOf(note) !== -1;
      }
    },
    computed: {
      wrapperClasses: function wrapperClasses() {
        var choice = this.currentChoice;
        return choice && choice.notes ? choice.notes : [];
      },
      currentChoice: function currentChoice() {
        var _this2 = this;

        return this.choices.find(function (a) {
          return a.key === _this2.filters.sort;
        });
      },
      locationDetails: function locationDetails() {
        if (!this.$root.hasValidLocation(this.listingType)) {
          return CASE27.l10n.nearby_listings_location_required;
        }

        return this.filters.search_location;
      },
      filters: function filters() {
        return this.$root.types[this.listingType].filters;
      }
    },
    watch: {
      'filters.sort': function filtersSort(value) {
        this.updateUI();
      }
    }
  });

  var initBasicForms = function initBasicForms() {
    // document.querySelectorAll(...).forEach not supported in IE
    jQuery('.mylisting-basic-form').each(function (i, el) {
      if (el.dataset.inited) {
        return;
      }

      el.dataset.inited = true;
      var listingTypes = JSON.parse(el.dataset.listingTypes);
      var config = JSON.parse(el.dataset.config);
      new Vue({
        el: el,
        data: {
          activeType: false,
          types: listingTypes,
          targetURL: config.target_url,
          tabMode: config.tabs_mode,
          typesDisplay: config.types_display,
          boxShadow: config.box_shadow,
          formId: config.form_id
        },
        created: function created() {
          var keys = Object.keys(this.types);

          if (keys.length) {
            this.activeType = this.types[keys[0]];
          }
        },
        methods: {
          typeDropdownChanged: function typeDropdownChanged(key) {
            if (this.activeType === this.types[key]) {
              return;
            }

            this.activeType = this.types[key];
            jQuery(this.$refs["types-dropdown-".concat(this.activeType.id)]).val(key).trigger('change').trigger('select2:close');
          },
          filterChanged: function filterChanged(value, event) {//
          },
          hasValidLocation: function hasValidLocation(listingType) {//
          },
          submit: function submit() {
            var filters = this.activeType.filters;
            var params = {
              type: this.activeType.slug,
              tab: 'search-form'
            };
            Object.keys(filters).forEach(function (filter) {
              var value = filters[filter];

              if (filter === 'proximity' && !(filters.lat && filters.lng)) {
                return;
              }

              if (value && typeof value.length !== 'undefined' && value.length) {
                params[filter] = value;
              } else if (typeof value === 'number' && value) {
                params[filter] = value;
              }
            });
            var querystring = jQuery.param(params).replace(/%2C/g, ',');
            window.location.href = "".concat(this.targetURL, "?").concat(querystring);
          }
        }
      });
    });
  };

  initBasicForms();
  document.addEventListener('DOMContentLoaded', initBasicForms);
  document.addEventListener('mylisting:refresh-basic-forms', initBasicForms);

})));

//# sourceMappingURL=frontend.js.map
