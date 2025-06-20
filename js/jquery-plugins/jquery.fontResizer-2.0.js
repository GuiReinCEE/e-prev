/*
 * fontResizer - jQuery Plugin
 *
 * Examples and documentation at: <http://paperplanesdesign.com/cubed/Font-Resizer/fontResizer.html>
 *
 * Copyright (c) 2011 Justin Wehrman
 *
 * Version: 2.0.0 (11/11/2010)
 * Requires: jQuery v1.3+
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
(function($) {
	$.fn.fontResizer = function ( options ) {
		var date = new Date();
		var currYear = date.getFullYear();
		var currMonth = date.getMonth();
		var currDay = date.getDate();
		var cExpire = new Date(currYear + 1, currMonth, currDay);
		var cExpireDate = cExpire.toUTCString();
		var defaults = {
			sizeType: 'px',
			minFont: 12,
			maxFont: 16,
			fontSet: 14,
			setFontOn: 'ON',
			increment: 2,
			incrementDisable: 'DISABLED',
			$increaseClickItem: $('.increaseClickItem'),
			$decreaseClickItem: $('.decreaseClickItem'),
			$setFontButton: $('.setFontSize'),
			cookieName: 'fontResizer',
			cookieExpire: cExpireDate,
			cookiePath: '/',
			cookieSet: options.cookieName + '=' + options.fontSet + ';path=' + options.cookiePath + ' ;expires=' + options.cookieExpire,
			endClick: function () {
				blur;
				return false;
			},
			increaseFontSize: function () {
				if (options.fontSet < options.maxFont) {
					options.fontSet += options.increment;
				} else {
					options.endClick();
				}
				$content.css('fontSize',options.fontSet + options.sizeType);
				document.cookie = options.cookieName + '=' + options.fontSet + ';path=' + options.cookiePath + ' ;expires=' + options.cookieExpire;
				options.setCurrentFontButton();
			},
			decreaseFontSize: function () {
				if (options.fontSet > options.minFont) {
					options.fontSet -= options.increment;
				} else {
					options.endClick();
				}
				$content.css('fontSize',options.fontSet + options.sizeType);
				document.cookie = options.cookieName + '=' + options.fontSet + ';path=' + options.cookiePath + ' ;expires=' + options.cookieExpire;
				options.setCurrentFontButton();
			},
			setFontSize: function ( fSize ) {
				fSize = $(this).attr('title');
				fSize = parseInt(fSize);
				options.fontSet = fSize;
				$content.css('fontSize',fSize + options.sizeType);
				document.cookie = options.cookieName + '=' + options.fontSet + ';path=' + options.cookiePath + ' ;expires=' + options.cookieExpire;
			},
			checkfontSetCookie: function () {
				var results = document.cookie.match( '(^|;) ?' + options.cookieName + '=([^;]*)(;|$)' );
				if (results) {
					$content.css('fontSize',unescape(results[2]) + options.sizeType);
					options.setCurrentFontButton();
				}
			},
			setCurrentFontButton: function () {
				if (options.$setFontButton.length !== 0) {
					options.$setFontButton.each(function() {
						$(this).attr('title') == options.fontSet ? $(this).addClass(options.setFontOn) : $(this).removeClass(options.setFontOn);
					});
					options.setIncreaseDecreaseVisibility();
				}
			},
			setIncreaseDecreaseVisibility: function () {
				var maxReached = options.fontSet >= options.maxFont;
				var minReached = options.fontSet <= options.minFont;
				maxReached ? options.$increaseClickItem.addClass(options.incrementDisable) : options.$increaseClickItem.removeClass(options.incrementDisable);
				minReached ? options.$decreaseClickItem.addClass(options.incrementDisable) : options.$decreaseClickItem.removeClass(options.incrementDisable);
			}
		};
		var options = $.extend(defaults, options);
		var $content = this;
		return this.each(function() {
			options.checkfontSetCookie();
			if ($(this).css('fontSize')) {
				options.fontSet = parseInt($(this).css('fontSize').replace(options.sizeType,''));
				options.$increaseClickItem.click(options.increaseFontSize).click(options.setIncreaseDecreaseVisibility).click(options.endClick);
				options.$decreaseClickItem.click(options.decreaseFontSize).click(options.setIncreaseDecreaseVisibility).click(options.endClick);
				options.$setFontButton.click(function(event){
					options.$setFontButton.each(function() {
						if ($(this).hasClass(options.setFontOn)) { $(this).removeClass(options.setFontOn); }
					});
					$(this).addClass(options.setFontOn);
				}).click(options.setFontSize).click(options.setIncreaseDecreaseVisibility).click(options.endClick);
			}
		});
	};
})(jQuery);