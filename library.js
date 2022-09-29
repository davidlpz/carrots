;(function() {
	function $(el) { return document.querySelectorAll(el); }

	function addListener(element, event, handler) {
		if (element.addEventListener) {
			element.addEventListener(event, handler, false);
		} else if (element.attachEvent) {
			element.attachEvent('on' + event, handler);
		}
	}

	function resize() {
		var sections = $('section'),
			h = window.innerHeight || document.documentElement.clientHeight,
			margin = 50,
			max, wrapper, wH;

		for (var i = 0; i < sections.length; i++) {
			if (sections[i].height > h) return;
			wrapper = $('.wrapper')[i];
			wH = wrapper.clientHeight;
			max = (h < wH) ? wH+margin*2 : h;
			sections[i].style.height = max + 'px';
			wrapper.style.marginTop = '-' + wH/2 + 'px';
		}
	}

	addListener(window, 'load', resize);
	addListener(window, 'resize', resize);

	var Scroller = {
		// Extract all the links and attach the event to the ones that validate
		init: function() {
			var links = $('a');

			for (var i = 0; i < links.length; i++) {
				if (links[i].href.indexOf('#') !== -1) {
					Scroller.add(links[i], 'click', Scroller.find);
				}
			}
		},

		// Attach an event for an element
		add: function(elem, event, handler) {
			if (elem.addEventListener) {
                return elem.addEventListener(event, handler, false);
            }
			if (elem.attachEvent) {
                return elem.attachEvent('on' + event, handler);
            }
		},

		// Find the anchor
		find: function(e) {
			var anchor,
				item = e.target || e.srcElement,
				top;

			e.preventDefault();
			anchor = $('#' + item.href.split('#')[1]);
			top = (anchor[0].id == 'home') ? 0 : anchor[0].offsetTop;
			Scroller.scroll(top);
			return false;
		},

		// Return the current Y position of the document
		currentPosition: function() {
			return  window.pageYOffset || // Firefox, Chrome, Opera, Safari
					document.documentElement.scrollTop || // IE 6 (standards mode)
					document.body.scrollTop; // IE 6, 7 and 8
		},

		// Scroll to the anchor
		scroll: function(end) {
			var start = Scroller.currentPosition(),
				distance = Math.abs(end - start),
				speed = Math.round(distance/100),
				step = Math.round(distance/25),
				leap = end > start ? start + step : start - step,
				timer = 0;

		    if (distance < 100) {
		        scrollTo(0, distance);
                return false;
		    }

			if (speed >= 20) speed = 20;

			if (end > start) {
				for (var i = start; i < end; i += step ) {
					setTimeout('window.scrollTo(0, ' + leap + ')', timer * speed);
					leap += step;
					if (leap > end) {
                        leap = end;
                    }
					timer++;
				}
			} else{
				for (var i = start; i > end; i -= step ) {
					setTimeout('window.scrollTo(0, ' + leap + ')', timer * speed);
					leap -= step;
					if (leap < end) {
                        leap = end;
                    }
					timer++;
				}
			}
			return false;
		}
	}

	window.onload = function() { Scroller.init(); }

	function changeImage(e) {
		var self = this;
		$('#images')[0].src = 'img/' + self.id + '.jpg';
		for (var i = 0; i < $('.btn').length; i++) {
			buttonUpdate($('.btn')[i], 'btn');
		}
		buttonUpdate(self, 'btn active');
	}

	for (var i = 0; i < $('.btn').length; i++) {
		addListener($('.btn')[i], 'click', changeImage);
	}

	function buttonUpdate(btn, name) { btn.className = name; }

	function nextImage(e) {
		var self = this,
			length = $('.btn').length,
			i = (self.src.slice(-5,-4)),
			next = (i % length) + 1;

		self.src = 'img/image' + next + '.jpg';
		buttonUpdate($('#image' + i)[0], 'btn');
		buttonUpdate($('#image' + next)[0], 'btn active');
	}

	addListener($('#images')[0],'click',nextImage);

	function scrollTopControl() {
		var control = $('#backTop')[0],
			y = window.scrollY || document.body.parentNode.scrollTop,
			h = window.innerHeight || document.documentElement.clientHeight;

		control.className = (y < h) ? 'hidden' : '';
	}
	addListener(window, 'scroll', scrollTopControl);

})();