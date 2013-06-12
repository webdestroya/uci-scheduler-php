function show()
{for(var i=0;i<arguments.length;i++){var element=ge(arguments[i]);if(element&&element.style)element.style.display='';}
return false;}

function hide()
{for(var i=0;i<arguments.length;i++){var element=ge(arguments[i]);if(element&&element.style)element.style.display='none';}
return false;}

function shown(el){el=ge(el);return(el.style.display!='none');}

function toggle()
{for(var i=0;i<arguments.length;i++){var element=ge(arguments[i]);element.style.display=(element.style.display=='block')?'none':'block';}
return false;}

function linkbuster()
{
	return false;
}

function noenter()
{
	return !(window.event && window.event.keyCode == 13); 
}
function changeMonth(mo,yr)
{
	var handlerFunc = function(t) {
		$('ajaxcalendar').innerHTML = t.responseText;
	}
		
	var data = "";
	data += "mo="+mo;
	data += "&yr="+yr;
	data += "&hour="+$('ajax_cal_hour').value;
	data += "&min="+$('ajax_cal_min').value;
	data += "&offset="+$('ajax_cal_off').value;
	new Ajax.Request("/ajax/calendar.php", {method:'post', postBody:data, onSuccess:handlerFunc});
}
function calendarToDate(timestamp,prefix)
{
	var date = new Date();
	date.setTime(timestamp*1000);
	
	var month=new Array(12)
	month[0]="January";
	month[1]="February";
	month[2]="March";
	month[3]="April";
	month[4]="May";
	month[5]="June";
	month[6]="July";
	month[7]="August";
	month[8]="September";
	month[9]="October";
	month[10]="November";
	month[11]="December";
	var weekday=new Array(7)
	weekday[0]="Sunday";
	weekday[1]="Monday";
	weekday[2]="Tuesday";
	weekday[3]="Wednesday";
	weekday[4]="Thursday";
	weekday[5]="Friday";
	weekday[6]="Saturday";
	
	$(prefix+'datetxt').innerHTML = weekday[ date.getUTCDay() ];
	$(prefix+'datetxt').innerHTML += ", ";
	$(prefix+'datetxt').innerHTML += month[ date.getUTCMonth() ];
	$(prefix+'datetxt').innerHTML += " ";
	$(prefix+'datetxt').innerHTML += date.getUTCDate();
	$(prefix+'datetxt').innerHTML += ", ";
	$(prefix+'datetxt').innerHTML += date.getUTCFullYear();
	$(prefix+'datetxt').innerHTML += " at ";
	if(date.getUTCHours()==0)
	{
		$(prefix+'datetxt').innerHTML += "12";
	}
	else if(date.getUTCHours()>12)
	{
		$(prefix+'datetxt').innerHTML += (date.getUTCHours()-12);
	}
	else
	{
		$(prefix+'datetxt').innerHTML += date.getUTCHours();
	}
	
	// minutes
	if(date.getUTCMinutes()==0)
	{
		$(prefix+'datetxt').innerHTML += ":00";
	}
	else
	{
		$(prefix+'datetxt').innerHTML += ":" + date.getUTCMinutes();
	}
	
	if(date.getUTCHours()==0)
	{
		$(prefix+'datetxt').innerHTML += " AM";
	}
	else if(date.getUTCHours()>=12)
	{
		$(prefix+'datetxt').innerHTML += " PM";
	}
	else
	{
		$(prefix+'datetxt').innerHTML += " AM";
	}
}
function saveCalendar(prefix)
{
	var tsdate = parseInt($('ajax_cal_ts_date').value);
	var tshour = parseInt($('ajax_cal_hour').value);
	var tsmin = parseInt($('ajax_cal_min').value);
	var tsoff = parseInt($('ajax_cal_off').value);
	
	if(tsoff==0 && tshour==12)
	{
		tshour = 0;
	}
	
	var timestamp = (tsdate + (tshour*3600) + (tsmin*60) + (tsoff*3600));
	$(prefix+'date').value = timestamp;
	calendarToDate(timestamp,prefix);
}
function hideNotice(notice,uid)
{
	Effect.SlideUp(notice,{duration:0.5});
	var data = "notice="+notice+"&userid="+uid;
	new Ajax.Request('/ajax/notice.php', {method:'post', postBody:data});
}

function setCheckedValue(radioObj, newValue) {
	if(!radioObj)
		return;
	var radioLength = radioObj.length;
	if(radioLength == undefined) {
		radioObj.checked = (radioObj.value == newValue.toString());
		return;
	}
	for(var i = 0; i < radioLength; i++) {
		radioObj[i].checked = false;
		if(radioObj[i].value == newValue.toString()) {
			radioObj[i].checked = true;
		}
	}
}
function remove_node(node)
{
	if(node.removeNode)
		node.removeNode(true);
	else
	{
		for(var i=node.childNodes.length-1;i>=0;i--)
			remove_node(node.childNodes[i]);
		node.parentNode.removeChild(node);
	}
	return null;
}

function findX(obj)
{
	var curleft=0;
	if(obj.offsetParent)
	{
		while(obj.offsetParent)
		{
			curleft+=obj.offsetLeft;
			obj=obj.offsetParent;
		}
	}
	else if(obj.x)
		curleft+=obj.x;
	return curleft;
}

function findY(obj)
{
	var curtop=0;
	if(obj.offsetParent)
	{
		while(obj.offsetParent)
		{
			curtop+=obj.offsetTop;
			obj=obj.offsetParent;
		}
	}
	else if(obj.y)
		curtop+=obj.y;
	return curtop;
}

function mousePosX(e)
{
	var posx=0;
	if(!e)
		var e=window.event;
	if(e.pageX)
		posx=e.pageX;
	else if(e.clientX&&document.body.scrollLeft)
		posx=e.clientX+document.body.scrollLeft;
	else if(e.clientX&&document.documentElement.scrollLeft)
		posx=e.clientX+document.documentElement.scrollLeft;
	else if(e.clientX)
		posx=e.clientX;
	return posx;
}

function mousePosY(e)
{
	var posy=0;
	if(!e)
		var e=window.event;
	if(e.pageY)
		posy=e.pageY;
	else if(e.clientY&&document.body.scrollTop)
		posy=e.clientY+document.body.scrollTop;
	else if(e.clientY&&document.documentElement.scrollTop)
		posy=e.clientY+document.documentElement.scrollTop;
	else if(e.clientY)
		posy=e.clientY;
	return posy;
}

function elementX(obj)
{
	var curleft=0;
	if(obj.offsetParent)
	{
		while(obj.offsetParent)
		{
			curleft+=obj.offsetLeft;
			obj=obj.offsetParent;
		}
	}
	else if(obj.x)
		curleft+=obj.x;
	return curleft;
}

function elementY(obj)
{
	var curtop=0;
	if(obj.offsetParent)
	{
		while(obj.offsetParent)
		{
			curtop+=obj.offsetTop;
			obj=obj.offsetParent;
		}
	}
	else if(obj.y)
		curtop+=obj.y;
		return curtop;
}

function onloadRegister(handler)
{
	if(window.onload)
	{
		var old=window.onload;
		window.onload=function()
			{
				old();
				handler();
			};
	}
	else
	{
		window.onload=handler;
	}
}

function placeholderSetup(id)
{
	var el=$(id);
	if(!el)
		return;
	var ph=el.getAttribute("placeholder");
	if(ph&&ph!="")
	{
		el.value=ph;
		el.style.color='#777';
		el.is_focused=0;
		el.onfocus=placeholderFocus;
		el.onblur=placeholderBlur;
	}
}

function placeholderFocus()
{
	if(!this.is_focused)
	{
		this.is_focused=1;
		this.value='';
		this.style.color='#000';
		var rs=this.getAttribute("radioselect");
		if(rs&&rs!="")
		{
			var re=document.getElementById(rs);
			if(!re)
			{
				return;
			}
			if(re.type!='radio')
				return;
			re.checked=true;
		}
	}
}

function placeholderBlur()
{
	var ph=this.getAttribute("placeholder");
	if(this.is_focused&&ph&&this.value=="")
	{
		this.is_focused=0;
		this.value=ph;
		this.style.color='#777';
	}
}

function htmlspecialchars(text)
{
	return text?text.toString().replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#039;').replace(/</g,'&lt;').replace(/>/g,'&gt;'):'';
}
/*"*/

function escape_js_quotes(text)
{
	if(!text)
	{
		return;
	}
	return text.replace(/\\/g,'\\\\').replace(/\n/g,'\\n').replace(/\r/g,'\\r').replace(/"/g,'\\x22').replace(/'/g,'\\\'').replace(/</g,'\\x3c').replace(/>/g,'\\x3e').replace(/&/g,'\\x26');
}

function trim(str)
{
	var delim=arguments.length>1 ? arguments[1] : ' ';
	for(var i=0,c=str.length-delim.length;i<=c;i+=delim.length)
	{
		if(str.substring(i,i+delim.length)!=delim)
		{
			break;
		}
	}
	for(var j=str.length,c=Math.max(i,delim.length-1);j>c;j-=delim.length)
	{
		if(str.substring(j-delim.length,j)!=delim)
		{
			break;
		}
	}
	return str.substring(i,j);
}

function escapeURI(u)
{
	if(encodeURIComponent)
	{
		return encodeURIComponent(u);
	}
	if(escape)
	{
		return escape(u);
	}
}

function goURI(href)
{
	window.location.href=href;
}

function is_email(email)
{
	return/^[\w!.%+]+@[\w]+(?:\.[\w]+)+$/.test(email);
}

function getViewportWidth()
{
	var width=0;
	if(document.documentElement&&document.documentElement.clientWidth)
	{
		width=document.documentElement.clientWidth;
	}
	else if(document.body&&document.body.clientWidth)
	{
		width=document.body.clientWidth;
	}
	else if(window.innerWidth)
	{
		width=window.innerWidth-18;
	}
	return width;
};

function getViewportHeight()
{
	var height=0;
	if(document.documentElement&&document.documentElement.clientHeight)
	{
		height=document.documentElement.clientHeight;
	}
	else if(document.body&&document.body.clientHeight)
	{
		height=document.body.clientHeight;
	}
	else if(window.innerHeight)
	{
		height=window.innerHeight-18;
	}
	return height;
};

function getRadioFormValue(obj)
{
	for(i=0;i<obj.length;i++)
	{
		if(obj[i].checked)
		{
			return obj[i].value;
		}
	}
	return null;
}
function checkAgree()
{
	if(document.frm.pic.value)
	{
		if(document.frm.agree.checked)
		{
			document.frm.submit();
		}
		else
		{
			show("error");
		}
	}
}

function isIE()
{
	return(navigator.userAgent.toLowerCase().indexOf("msie")!=-1);
}

function getTableRowShownDisplayProperty()
{
	if(isIE())
	{
		return'inline';
	}
	else
	{
		return'table-row';
	}
}


function showTableRow()
{
	for(var i=0;i<arguments.length;i++)
	{
		var element=$(arguments[i]);
		if(element&&element.style)
			element.style.display=getTableRowShownDisplayProperty();
	}
	return false;
}

function getParentRow(el)
{
	el=$(el);
	while(el.tagName&&el.tagName!="TR")
	{
		el=el.parentNode;
	}
	return el;
}

function stopPropagation(e)
{
	if(!e)
		var e=window.event;
	e.cancelBubble=true;
	if(e.stopPropagation)
	{
		e.stopPropagation();
	}
}

function show_standard_status(status)
{
	s=$('standard_status');
	if(s)
	{
		var header=s.firstChild;
		header.innerHTML=status;
		show('standard_status');
	}
}

function hide_standard_status()
{
	s=$('standard_status');
	if(s)
	{
		hide('standard_status');
	}
}

function remove_node(node)
{
	if(node.removeNode)
		node.removeNode(true);
	else
	{
		for(var i=node.childNodes.length-1;i>=0;i--)
			remove_node(node.childNodes[i]);
		node.parentNode.removeChild(node);
	}
	return null;
}

function adjustImage(obj,stop_word,max)
{
	var pn=obj.parentNode;
	if(stop_word==null)
		stop_word='note_content';
	if(max==null)
	{
		while(pn.className.indexOf(stop_word)==-1)
			pn=pn.parentNode;
		if(pn.offsetWidth)
			max=pn.offsetWidth;
		else
			max=400;
	}
	obj.className=obj.className.replace('img_loading','img_ready');
	if(obj.width>max)
	{
		if(window.ActiveXObject)
		{
			try
			{
				var img_div=document.createElement('div');
				img_div.style.filter='progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'
									+obj.src.replace('"','%22')+'", sizingMethod="scale")';
				img_div.style.width=max+'px';
				img_div.style.height=((max/obj.width)*obj.height)+'px';
				if(obj.parentNode.tagName=='A')
					img_div.style.cursor='pointer';
				obj.parentNode.insertBefore(img_div,obj);
				obj.removeNode(true);
			}
			catch(e)
			{
				obj.style.width=max+'px';
			}
		}
		else
			obj.style.width=max+'px';
	}
}
function getmousexy(event,image)
{
	pos_x = mousePosX(event)-findX(image);
	pos_y = mousePosY(event)-findY(image);

	temp = Array();
	temp[0] = pos_x;
	temp[1] = pos_y;
	return temp;
}


function set_opacity(obj,opacity)
{
	try
	{
		obj.style.opacity=(opacity==1 ? '' : opacity);
		obj.style.filter=(opacity==1 ? '' : 'alpha(opacity='+opacity*100+')');
	}
	catch(e)
	{
	
	}
	obj.setAttribute('opacity',opacity);
}

function get_opacity(obj)
{
	return obj.opacity ? obj.opacity : 1;
}

function focus_login()
{
	var email=$("email");
	var pass=$("pass");
	var dologin=$("doquicklogin");
	if(email&&pass)
	{
		if(email.value!=""&&pass.value=="")
		{
			pass.focus();
		}
		else if(email.value=="")
		{
			email.focus();
		}
		else if(email.value!=""&&pass.value!="")
		{
			dologin.focus();
		}
	}
}

function array_indexOf(arr,val,index)
{
	if(!index)
	{
		index=0;
	}
	for(var i=index;i<arr.length;i++)
	{
		if(arr[i]==val)
		{
			return i;
		}
	}
	return-1;
}

function showLoadingOverlay()
{
	var loadOverlayHTML = '<p><div style="text-align:center;padding:10px;">';
	loadOverlayHTML += '<img src="http://static.picmember.com/images/whiteloader.gif" width="48" height="48" border="0" alt="Loading...">';
	loadOverlayHTML += '<br>';
	loadOverlayHTML += 'Loading...';
	loadOverlayHTML += '<br>';
	loadOverlayHTML += '</div></p>';
	new Effect.Opacity('pagecontainer',{duration:0.5, from: 1.0, to: 0.3});
	return (new pop_dialog()).show_prompt('Loading...',loadOverlayHTML);
}
function showloader()
{
	showLoadingOverlay();
}
