function $(id) { return document.getElementById(id); }

function addListener(element,event,handler){
	if (element.addEventListener) {
		element.addEventListener(event,handler,false); 
	} else if (element.attachEvent) {
		element.attachEvent('on'+event,handler);
	}
}

function resize() {
	var sections = document.getElementsByTagName('section'),
		h = window.innerHeight || document.documentElement.clientHeight,
		margin = 50,
		max, wrapper, wH,
		i = 0;

	for (i; i < sections.length; i++) {
		wrapper = sections[i].children[0];
		wH = wrapper.clientHeight;
		max = (h < wH) ? wH+margin : h;
		sections[i].style.height = max + 'px';
		wrapper.style.marginTop = '-' + wH/2 + 'px';
	}
}

addListener(window,'load',resize);
addListener(window,'resize',resize);

var Scroller = {
	// Extract all the links and attach the event to the ones that validate
	init: function() {
		var links = document.getElementsByTagName('a'),
			i = 0;

		for (; i < links.length; i++) {
			if (links[i].href.indexOf('#') !== -1) {
				Scroller.add(links[i],'click',Scroller.find);
			}
		}
	},

	// Attach an event for an element
	add: function(elem,event,handler){
		if (elem.addEventListener) return elem.addEventListener(event,handler,false); 
		if (elem.attachEvent) return elem.attachEvent('on'+event,handler);
	},

	// Find the anchor
	find: function(e) {
		var anchor,
			item = e.target || e.srcElement;

		anchor = document.getElementById(item.href.split('#')[1]);
		Scroller.scroll(anchor.offsetTop);
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
	        scrollTo(0, distance); return false;
	    }

		if (speed >= 20) speed = 20;

		if (end > start) {
			for (var i=start; i<end; i+=step ) {
				setTimeout('window.scrollTo(0, '+leap+')', timer * speed);
				leap += step;
				if (leap > end) leap = end;
				timer++;
			}
		} else {
			for (var i=start; i>end; i-=step ) {
				setTimeout('window.scrollTo(0, '+leap+')', timer * speed);
				leap -= step;
				if (leap < end) leap = end;
				timer++;
			}
		}
		return false;
	}
}

window.onload = function() { Scroller.init(); }

function changeImage(button) {
	var menu = $('menu-circ'),
		i, item;

	buttonOn(button);
	$('images').src = 'img/' + button.id + '.jpg';
	for (i = 0; i < menu.children.length; i++) {
		item = menu.children[i];
		if (item.id !== button.id) buttonOff(item);
	}
}

function buttonOn(btn) {
	btn.style.backgroundImage = 'url(img/btn-orange.png)';
}

function buttonOff(btn) {
	btn.style.backgroundImage = 'url(img/btn-gray.png)';
}

function nextImage(e) {
	var item = e.target || e.srcElement;
	var length = $('menu-circ').children.length,
		i = (item.src.slice(-5,-4)),
		next = (i % length) + 1;
	item.src = 'img/image' + next + '.jpg';
	buttonOff($('image' + i));
	buttonOn($('image' + next));
}

addListener($('images'),'click',nextImage);

function scrollTopControl(){
	var control = $('backTop'),
		y = window.scrollY || document.body.parentNode.scrollTop,
		h = window.innerHeight || document.documentElement.clientHeight;

	if (y < h) control.className = 'hidden';
	else control.className = '';
}

addListener(window,'scroll',scrollTopControl);

function ajax(){
	var xmlhttp;
	if (window.XMLHttpRequest) //code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	else //code for IE6, IE5
		xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
	return xmlhttp;
}

function contact(){
	var xmlhttp = ajax(),
		parent = document.getElementById('contactForm').parentNode;

	if (document.getElementById('email').value === '' ||
		document.getElementById('message').value === '') return false;

	xmlhttp.open('POST','contact.php',true);
	xmlhttp.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	xmlhttp.send('email='+document.getElementById('email').value+'&message='+document.getElementById('message').value);
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			parent.removeChild(document.getElementById('contactForm'));
			parent.innerHTML = xmlhttp.responseText;
		}
	}
	return false;
}

addListener($('contactForm'),'submit',contact);