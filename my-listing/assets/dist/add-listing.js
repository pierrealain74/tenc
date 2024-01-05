!function(e){"function"==typeof define&&define.amd?define("addListing",e):e()}(function(){"use strict";jQuery(function(t){t(".file-upload-field.multiple-uploads .job-manager-uploaded-files").sortable({helper:"clone",appendTo:document.body}),t('.c27-work-hours .day-wrapper .work-hours-type input[type="radio"]').on("change",function(e){t(this).val();t(this).parents(".day-wrapper").removeClass(["day-status-enter-hours","day-status-closed-all-day","day-status-open-all-day","day-status-by-appointment-only"].join(" ")).addClass("day-status-"+t(this).val())})}),jQuery(function(i){var e,d,n={};!function(){var e=i("#submit-job-form .form-section-wrapper");if(!(e.length<=1)){var o=70*window.innerHeight/100,r=5*window.innerHeight/100;i(window).on("scroll",MyListing.Helpers.debounce(function(){var d=[];e.each(function(e,t){var n=t.getBoundingClientRect(),a=o-n.top,i=r-n.top;0<=a&&d.push({el:t,diff:a,max_diff:i})}),e.removeClass("active"),n.Nav.clearAll();var t=!1;d.reverse().forEach(function(e){if(!t)return e.el.classList.add("active"),t=!0,void n.Nav.highlight(e.el.id);e.max_diff<=0&&(e.el.classList.add("active"),n.Nav.highlight(e.el.id))})},20)).scroll()}}(),e=i("#submit-job-form .form-section-wrapper:not(#form-section-submit)"),(d=i(".add-listing-nav")).length&&(n.Nav={clearAll:function(){d.find("li").removeClass("active")},highlight:function(e){var t=d.find("#"+e+"-nav");t.length&&t.addClass("active")}},e.length<=1?d.hide():e.each(function(e,t){var n=i(this).find(".pf-head h5").html();if("string"==typeof n){var a=i('<li id="'+i(this).attr("id")+'-nav"><a href="#"><i><span></span></i>'+n+"</a></li>");a.click(function(e){e.preventDefault(),i("html, body").animate({scrollTop:i(t).offset().top-5*window.innerHeight/100-90})}),d.find("ul").append(a)}}))}),jQuery(function(w){w(".event-picker").each(function(){var t=w(this),e=t.data("dates"),d=t.data("key"),n=t.data("limit"),o="no"!==t.data("timepicker"),M=t.data("l10n"),r=t.find(".dates-list"),a=t.find(".date-add-new"),i=e.length+1,s=t.find(".datetpl").text();function l(){var e=t.find(".single-date").length;n<=e?a.hide():a.show(),e<1&&c()}function c(){f({start:"",end:"",repeat:!1,frequency:2,unit:"weeks",until:moment().add(1,"years").locale("en").format("YYYY-MM-DD"),index:i++})}function f(e){var t=w(s.replace(/{date}/g,d+"["+e.index+"]")),p=t.find(".is-recurring input"),u=t.find(".date-start input"),m=t.find(".date-end input"),g=t.find(".repeat-frequency input"),h=t.find(".repeat-unit"),v=t.find(".repeat-message"),y=t.find(".repeat-end input");function n(){if(p.prop("checked")){var e=u.val(),t=m.val(),n=y.val(),a=parseInt(g.val(),10),i=h.find("input:checked").val();if(e.length&&t.length&&n.length&&a){e=moment(e),t=moment(t),(n=moment(n)).set({hour:23,minute:59,second:59}),"weeks"===i&&(i="days",a*=7),"years"===i&&(i="months",a*=12);for(var d=Math.abs(e.diff(n,i)),o=Math.floor(d/a),r=[],s=1;s<Math.min(o+1,6);s++){var l=e.clone().add(a*s,i),c=t.clone().add(a*s,i);r.push("".concat(l.format(CASE27.l10n.datepicker.format)," - ").concat(c.format(CASE27.l10n.datepicker.format)))}var f=M.next_five.replace("%d",o);o<1?f=M.no_recurrences:o<5&&(f=M.next_recurrences),v.show().html("<span>".concat(f,"</span><ul><li>").concat(r.join("</li><li>"),"</li></ul>"))}else v.hide()}}u.val(e.start),m.val(e.end),p.prop("checked",e.repeat),g.val(e.frequency),h.find('input[value="'.concat(e.unit,'"]')).prop("checked",!0),y.val(e.until),e.repeat&&t.find(".recurrence").addClass("is-open"),p.on("change",function(){n(),w(this).prop("checked")?t.find(".recurrence").addClass("is-open"):t.find(".recurrence").removeClass("is-open")});new MyListing.Datepicker(u,{timepicker:o});var a=new MyListing.Datepicker(m,{timepicker:o}),i=new MyListing.Datepicker(y);e.start&&t.find(".date-start").removeClass("date-empty"),e.end&&t.find(".date-end").removeClass("date-empty"),u.on("datepicker:change",function(e){a.setMinDate(moment(e.detail.value)),i.setMinDate(moment(e.detail.value)),n(),e.detail.value?t.find(".date-start").removeClass("date-empty"):t.find(".date-start").addClass("date-empty")}),m.on("datepicker:change",function(e){n(),e.detail.value?t.find(".date-end").removeClass("date-empty"):t.find(".date-end").addClass("date-empty")}),y.on("datepicker:change",n),g.on("input",n),h.find("input").on("change",n),n(),r.append(t)}e.forEach(function(e,t){f({start:e.start,end:e.end,repeat:e.repeat,frequency:e.repeat?e.frequency:2,unit:e.repeat?e.unit:"weeks",until:e.repeat?e.until:moment(e.start).add(1,"years").locale("en").format("YYYY-MM-DD"),index:t})}),e.length||c(),a.click(function(e){e.preventDefault(),f({start:"",end:"",repeat:!1,frequency:2,unit:"weeks",until:moment().add(1,"years").locale("en").format("YYYY-MM-DD"),index:i++}),l()}),w(this).on("click",".remove-date",function(e){e.preventDefault(),w(this).parents(".single-date").remove(),l()}),l()})}),jQuery(function(M){MyListing.Maps&&MyListing.Maps.loaded&&(M(".repeater-custom").each(function(e,t){var v=parseInt(M(t).data("max"),10),y=M(t).find(".add-location");M(t).repeater({initEmpty:!0,ready:function(e){},hide:function(e){var n=M(this).find(".location-field-wrapper").data("index");MyListing.Maps.instances;MyListing.Maps.instances.forEach(function(e,t){e.id==n&&delete MyListing.Maps.instances[t]}),MyListing.Maps.instances=MyListing.Maps.instances.filter(function(e){return null!==e}),e(),M("div[data-repeater-item] > .location-field-wrapper").length>=v?y.hide():y.show()},show:function(){var e=this;M(e).show();var t=M("div[data-repeater-item] > .location-field-wrapper"),n=M(e).find(".delete-repeater-item");if(t.length>=v?y.hide():y.show(),1==v?n.hide():n.show(),t.eq(-2).length){var a=t.eq(-2).data("index");M(e).find(".location-field-wrapper").attr("data-index",a+1),M(e).find(".location-picker-custom-map").attr("id",a+1)}else M(e).find(".location-field-wrapper").attr("data-index",t.length-1),M(e).find(".location-picker-custom-map").attr("id",t.length-1);new MyListing.Maps.Map(M(e).find(".c27-custom-map").get(0)),new MyListing.Maps.Autocomplete(M(e).find(".address-field").get(0));var i=M(e).find(".location-field-wrapper"),d=M(e).find(".location-picker-custom-map").attr("id"),o=MyListing.Maps.getInstance(d).instance;M(e).find(".cts-custom-get-location").on("click",function(e){e.preventDefault();var t=jQuery(jQuery(this).parents(".repeater-item"));t.find(".cts-custom-get-location").length&&(o&&MyListing.Geocoder.setMap(o.instance),MyListing.Geocoder.getUserLocation({receivedAddress:function(e){if(t.find(".address-field").val(e.address),t.find(".address-field").data("autocomplete"))return t.find(".address-field").data("autocomplete").fireChangeEvent(e)}}))});var r=i.data("options"),s=i.find(".location-coords"),l=i.find(".latitude-input"),c=i.find(".longitude-input"),f=i.find(".address-field"),p=i.find('.lock-pin input[type="checkbox"]'),u=i.find(".enter-coordinates-toggle > span"),m=new MyListing.Maps.Marker({position:h(),map:o,template:{type:"traditional"}});function g(){var e=h();m.setPosition(e),o.panTo(e),""!==l.val().trim()&&""!==c.val().trim()&&(l.val(e.getLatitude()),c.val(e.getLongitude()))}function h(){return l.val().trim()&&c.val().trim()?new MyListing.Maps.LatLng(l.val(),c.val()):new MyListing.Maps.LatLng(r["default-lat"],r["default-lng"])}o.addListener("click",function(e){if(!p.prop("checked")){var t=o.getClickPosition(e);m.setPosition(t),l.val(t.getLatitude()),c.val(t.getLongitude()),MyListing.Geocoder.geocode(t.toGeocoderFormat(),function(e){e&&f.val(e.address)})}}),f.on("autocomplete:change",function(e){if(!p.prop("checked")&&e.detail.place&&e.detail.place.latitude&&e.detail.place.longitude){var t=new MyListing.Maps.LatLng(e.detail.place.latitude,e.detail.place.longitude);m.setPosition(t),l.val(e.detail.place.latitude),c.val(e.detail.place.longitude),o.panTo(t)}}),o.addListenerOnce("idle",function(e){o.setZoom(r["default-zoom"])}),p.on("change",function(e){o.trigger("resize"),o.setCenter(h())}).change(),u.click(function(e){s.toggleClass("hide")}),l.blur(g),c.blur(g)}}).setList(M(t).data("list"))}),jQuery(".field-type-location .address-field").each(function(e,t){new MyListing.Maps.Autocomplete(t)}),jQuery(".cts-custom-get-location").each(function(e,t){jQuery(t).on("click",function(e){e.preventDefault();var t=jQuery(jQuery(this).parent(".repeater-item")),n=null;t.find(".cts-custom-get-location").length&&((n=MyListing.Maps.getInstance(jQuery(this)))&&MyListing.Geocoder.setMap(n.instance),MyListing.Geocoder.getUserLocation({receivedAddress:function(e){if(t.find(".cts-custom-get-location").val(e.address),t.find(".cts-custom-get-location").data("autocomplete"))return t.find(".cts-custom-get-location").data("autocomplete").fireChangeEvent(e)}}))})}))}),jQuery(function(t){t(".file-upload-field").on("click touchstart",".job-manager-remove-uploaded-file",function(){return t(this).closest(".job-manager-uploaded-file").remove(),!1}),t("#submit-job-form").on("submit",function(e){t(".add-listing-loader").show().removeClass("loader-hidden")})})});
