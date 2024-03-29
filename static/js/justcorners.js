/**
 * justcorners.js 1.4 (22-Jul-2007)
 * (c) by Christian Effenberger 
 * All Rights Reserved
 * Source: corner.netzgesta.de
 * Distributed under NSL
 * License permits free of charge
 * use on non-commercial and 
 * private web sites only 
**/

var tmp = navigator.appName == 'Microsoft Internet Explorer' && navigator.userAgent.indexOf('Opera') < 1 ? 1 : 0;
if(tmp) var isIE = document.namespaces ? 1 : 0;

if(isIE) {
	if(document.namespaces['v'] == null) {
		var stl = document.createStyleSheet();
		stl.addRule("v\\:*", "behavior: url(#default#VML);"); 
		document.namespaces.add("v", "urn:schemas-microsoft-com:vml"); 
	}
}

function roundedCorners(ctx,width,height,radius,lt,rt,lb,rb){
	ctx.beginPath(); ctx.moveTo(0,radius);
	if(lb==1) {ctx.lineTo(0,height-radius); ctx.quadraticCurveTo(0,height,radius,height); }else {ctx.lineTo(0,height); }
	if(rb==1) {ctx.lineTo(width-radius,height); ctx.quadraticCurveTo(width,height,width,height-radius); }else {ctx.lineTo(width,height); }
	if(rt==1) {ctx.lineTo(width,radius); ctx.quadraticCurveTo(width,0,width-radius,0); }else {ctx.lineTo(width,0); }		
	if(lt==1) {ctx.lineTo(radius,0); ctx.quadraticCurveTo(0,0,0,radius); }else {ctx.lineTo(0,0); }		
	ctx.closePath();
}

function addRoundedIECorners() {
	var image; var object; var vml; var classNames; var data;
	var iradius = 0; var classes = ''; var newClasses = ''; 
	var maxdim = 0; var mindim = 0; var i; var j; var tmp = '';
	var radius = null; var display = ""; var flt = "";
	var tl = 1; var tr = 1; var bl = 1; var br = 1; 
	var children = document.getElementsByTagName('img'); 
	var theimages = new Array(); var child; 
	for (i=0;i<children.length;i++) {
		child = children[i];
		classNames = child.className.split(' ');
		for (j=0;j<classNames.length; j++) {
			if (classNames[j] == 'corners') {theimages.push(child); break; }
		}
	}	
	for (i=0;i<theimages.length;i++) {	
		image = theimages[i]; iradius = 16;
		tl = 1;	tr = 1; bl = 1; br = 1; 
		object = image.parentNode; 
		classes = image.className.split(' ');
		for (j=0;j<classes.length;j++) {
			if (classes[j].indexOf("iradius") == 0) {
				iradius = classes[j].substring(7);
			}else if (classes[j].indexOf("iradiae") == 0) {
				tmp = classes[j].substring(7);
				tl = (tmp.substr(0,1)!="1"?0:1);
				tr = (tmp.substr(1,1)!="1"?0:1);
				bl = (tmp.substr(2,1)!="1"?0:1);
				br = (tmp.substr(3,1)!="1"?0:1);
			}
		}
		for (j=0;j<classes.length;j++) {
			if (classes[j] != "corners") {
				if (newClasses) { newClasses += ' '; }
				newClasses += classes[j];
			}
		}
		maxdim = Math.min(image.width,image.height)/2;
		mindim = (iradius==0?16:iradius);
		radius = Math.min(maxdim,mindim);
		display = (image.currentStyle.display.toLowerCase()=='block')?'block':'inline-block';        
		vml = document.createElement(['<var style="zoom:1;overflow:hidden;display:' + display + ';width:' + image.width + 'px;height:' + image.height + 'px;padding:0;">'].join(''));
		flt =  image.currentStyle.styleFloat.toLowerCase();
		display = (flt=='left'||flt=='right')?'inline':display;
		var path = "m 0," + radius; 
		if(bl==1) {path += " l 0," + (image.height-radius) + " qy " + radius + "," + image.height;}else {path += " l 0," + image.height;}
		if(br==1) {path += " l " + (image.width-radius) + "," + image.height + " qx " + image.width + "," + (image.height-radius);}else {path += " l " + image.width + "," + image.height;}
		if(tr==1) {path += " l " + image.width + "," + radius + " qy " + (image.width-radius) + ",0";}else {path += " l " + image.width + ",0";}	
		if(tl==1) {path += " l " + radius + ",0 qx 0," + radius;}else {path += + " l 0,0";}	
		path += " x e"; data = '<v:shape strokeweight="0" filled="t" stroked="f" fillcolor="#ffffff" coordorigin="0,0" coordsize="' + image.width + ',' + image.height + '" path="' + path + '" style="zoom:1;margin:-1px 0 0 -1px;padding: 0;display:' + display + ';width:' + image.width + 'px;height:' + image.height + 'px;"><v:fill src="' + image.src + '" type="frame" /></v:shape>';
		vml.innerHTML = [data ].join('');
		vml.className = newClasses;
		vml.style.cssText = image.style.cssText;
		vml.style.visibility = 'visible';
		vml.src = image.src; vml.alt = image.alt;
		if(image.id!='') vml.id = image.id;
		if(image.title!='') vml.title = image.title;
		if(image.getAttribute('onclick')!='') vml.setAttribute('onclick',image.getAttribute('onclick'));
		image.insertAdjacentElement('BeforeBegin',vml); 
		if(image.getAttribute("usemap")) {
			object.style.position = 'relative';
			object.style.height = image.height+'px';
			object.style.width = image.width+'px';
			image.left = 0; image.top = 0;
			image.style.position = 'absolute';
			image.style.height = image.height+'px';
			image.style.width = image.width+'px';
			image.style.left = 0 + 'px';
			image.style.top = 0 + 'px';
			image.style.filter = "Alpha(opacity=0)";
		}else {
			image.style.display = 'none'; 
		}
	}
}

function addRoundedCorners() {
	var image; var object; var canvas; var context; 
	var iradius = 0; var classes = ''; var newClasses = ''; 
	var maxdim = 0; var mindim = 0; var i; var j; var tmp = '';
	var tl = 1; var tr = 1; var bl = 1; var br = 1; 
	var children = document.getElementsByTagName('img'); 
	var theimages = new Array(); var child; var classNames;
	for (i=0;i<children.length;i++) {
		child = children[i];
		classNames = child.className.split(' ');
		for (j=0;j<classNames.length; j++) {
			if (classNames[j] == 'corners') {theimages.push(child); break; }
		}
	}	
	for (i=0;i<theimages.length;i++) {	
		image = theimages[i]; iradius = 16;
		tl = 1;	tr = 1; bl = 1; br = 1; 
		object = image.parentNode; 
		canvas = document.createElement('canvas');
		if (canvas.getContext) {
			classes = image.className.split(' ');
			for (j=0;j<classes.length;j++) {
				if (classes[j].indexOf("iradius") == 0) {
					iradius = classes[j].substring(7);
				}else if (classes[j].indexOf("iradiae") == 0) {
					tmp = classes[j].substring(7);
					tl = (tmp.substr(0,1)!="1"?0:1);
					tr = (tmp.substr(1,1)!="1"?0:1);
					bl = (tmp.substr(2,1)!="1"?0:1);
					br = (tmp.substr(3,1)!="1"?0:1);
				}
			}
			for (j=0;j<classes.length;j++) {
				if (classes[j] != "corners") {
					if (newClasses) { newClasses += ' '; }
					newClasses += classes[j];
				}
			}
			canvas.className = newClasses;
			canvas.style.cssText = image.style.cssText;
			canvas.style.height = image.height+'px';
			canvas.style.width = image.width+'px';
			canvas.height = image.height;
			canvas.width = image.width;
			canvas.src = image.src; canvas.alt = image.alt;
			if(image.id!='') canvas.id = image.id;
			if(image.title!='') canvas.title = image.title;
			if(image.getAttribute('onclick')!='') canvas.setAttribute('onclick',image.getAttribute('onclick'));
			maxdim = Math.min(canvas.width,canvas.height)/2;
			mindim = (iradius==0?16:iradius);
			iradius = Math.min(maxdim,mindim);
			context = canvas.getContext("2d");
			if(image.getAttribute("usemap")) {
				object.style.position = 'relative';
				object.style.height = image.height+'px';
				object.style.width = image.width+'px';
				canvas.left = 0; canvas.top = 0;
				canvas.style.position = 'absolute';
				canvas.style.left = 0 + 'px';
				canvas.style.top = 0 + 'px';
				image.left = 0; image.top = 0;
				image.style.position = 'absolute';
				image.style.height = image.height+'px';
				image.style.width = image.width+'px';
				image.style.left = 0 + 'px';
				image.style.top = 0 + 'px';
				image.style.opacity = 0;
				object.insertBefore(canvas,image);
			}else {
				object.replaceChild(canvas,image);
			}
			context.clearRect(0,0,canvas.width,canvas.height);
			globalCompositeOperation = "source-in";
			roundedCorners(context,canvas.width,canvas.height,iradius,tl,tr,bl,br);
			context.clip();
			context.fillStyle = 'rgba(0,0,0,0)';
			context.fillRect(0,0,canvas.width,canvas.height);
			context.drawImage(image,0,0,canvas.width,canvas.height);
			canvas.style.visibility = 'visible';
		}
	}
}

window.onload = function () { if(isIE){addRoundedIECorners(); }else {addRoundedCorners();}}