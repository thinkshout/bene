!function e(t,i,s){function n(c,l){if(!i[c]){if(!t[c]){var a="function"==typeof require&&require;if(!l&&a)return a(c,!0);if(o)return o(c,!0);var r=new Error("Cannot find module '"+c+"'");throw r.code="MODULE_NOT_FOUND",r}var d=i[c]={exports:{}};t[c][0].call(d.exports,function(e){var i=t[c][1][e];return n(i?i:e)},d,d.exports,e,t,i,s)}return i[c].exports}for(var o="function"==typeof require&&require,c=0;c<s.length;c++)n(s[c]);return n}({1:[function(e,t,i){!function(e){Drupal.behaviors.tsBENE={attach:function(t,i){var s={isMobile:function(){var t=e(".mobile-menu-toggle").css("display");return"none"!=t},beneMobile:function(){this.isMobile()&&e(".mobile-menu-toggle").once().click(function(){e(this).toggleClass("active"),e(".mobile-nav .region-primary-nav").fadeToggle(500).css({display:"flex"})})},formCleanup:function(){e(".region-content form").find("input").each(function(){var t=e(this),i=e('label[for="'+this.id+'"]').text().trim();t.attr("placeholder",i)})},messageDismiss:function(){e(".messages").once().click(function(){e(this).fadeOut()})},showHideTabs:function(){e(".user-logged-in .block-local-tasks-block").once().prepend('<div class="show-hide"><span></span></div>'),e(".block-local-tasks-block .show-hide").once().click(function(){e(this).parent().toggleClass("active")})},beneHero:function(){"none"==e(".block-hero").css("background-image")?e(".block-hero").addClass("no-bgimage").css("visibility","visible"):e(".block-hero").addClass("bgimage").css("visibility","visible")},beneNiceSelect:function(){e.fn.niceSelect=function(t){function i(t){t.after(e("<div></div>").addClass("nice-select").addClass(t.attr("class")||"").addClass(t.attr("disabled")?"disabled":"").attr("tabindex",t.attr("disabled")?null:"0").html('<span class="current"></span><ul class="list"></ul>'));var i=t.next(),s=t.find("option"),n=t.find("option:selected");i.find(".current").html(n.data("display")||n.text()),s.each(function(t){var s=e(this),n=s.data("display");i.find("ul").append(e("<li></li>").attr("data-value",s.val()).attr("data-display",n||null).addClass("option"+(s.is(":selected")?" selected":"")+(s.is(":disabled")?" disabled":"")).html(s.text()))})}if("string"==typeof t)return"update"==t?this.each(function(){var t=e(this),s=e(this).next(".nice-select"),n=s.hasClass("open");s.length&&(s.remove(),i(t),n&&t.next().trigger("click"))}):"destroy"==t?(this.each(function(){var t=e(this),i=e(this).next(".nice-select");i.length&&(i.remove(),t.css("display",""))}),0==e(".nice-select").length&&e(document).off(".nice_select")):console.log('Method "'+t+'" does not exist.'),this;this.hide(),this.each(function(){var t=e(this);t.next().hasClass("nice-select")||i(t)}),e(document).off(".nice_select"),e(document).on("click.nice_select",".nice-select",function(t){var i=e(this);e(".nice-select").not(i).removeClass("open"),i.toggleClass("open"),i.hasClass("open")?(i.find(".option"),i.find(".focus").removeClass("focus"),i.find(".selected").addClass("focus")):i.focus()}),e(document).on("click.nice_select",function(t){0===e(t.target).closest(".nice-select").length&&e(".nice-select").removeClass("open").find(".option")}),e(document).on("click.nice_select",".nice-select .option:not(.disabled)",function(t){var i=e(this),s=i.closest(".nice-select");s.find(".selected").removeClass("selected"),i.addClass("selected");var n=i.data("display")||i.text();s.find(".current").text(n),s.prev("select").val(i.data("value")).trigger("change")}),e(document).on("keydown.nice_select",".nice-select",function(t){var i=e(this),s=e(i.find(".focus")||i.find(".list .option.selected"));if(32==t.keyCode||13==t.keyCode)return i.hasClass("open")?s.trigger("click"):i.trigger("click"),!1;if(40==t.keyCode){if(i.hasClass("open")){var n=s.nextAll(".option:not(.disabled)").first();n.length>0&&(i.find(".focus").removeClass("focus"),n.addClass("focus"))}else i.trigger("click");return!1}if(38==t.keyCode){if(i.hasClass("open")){var o=s.prevAll(".option:not(.disabled)").first();o.length>0&&(i.find(".focus").removeClass("focus"),o.addClass("focus"))}else i.trigger("click");return!1}if(27==t.keyCode)i.hasClass("open")&&i.trigger("click");else if(9==t.keyCode&&i.hasClass("open"))return!1});var s=document.createElement("a").style;return s.cssText="pointer-events:auto","auto"!==s.pointerEvents&&e("html").addClass("no-csspointerevents"),this},e(".region-content select:not([multiple])").niceSelect()},init:function(){this.beneMobile(),this.formCleanup(),this.messageDismiss(),this.showHideTabs(),this.beneHero(),this.beneNiceSelect()}};s.init(),e(window).resize(function(){clearTimeout(window.resizedFinished),window.resizedFinished=setTimeout(function(){s.beneMobile()},250)})}}}(jQuery,Drupal,window)},{}]},{},[1]);
