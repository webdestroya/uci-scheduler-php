

function __super_class(obj)
{
	this.__super=obj;
	this.__parent=obj.prototype.parent;
}

__super_class.prototype.__super_method=function(method,pointer)
{
	var __pointer=pointer;
	this[method]=function()
		{
			var __parent=this.__context.parent;
			this.__context.__parents.push(__parent);
			this.__context.parent=__parent ? __parent.parent : null;
			var __ret=__pointer.apply(this.__context,arguments);
			this.__context.parent=__parent;
			this.__context.__parents.pop();
			__parent=null;
			return __ret;
		};
}

__super_class.prototype.__overridden_method=function(method,pointer)
{
	var __pointer=pointer;
	return function()
		{
			if((typeof this.__parents=='undefined')||!this.__parents.length)
			{
				return __pointer.apply(this,arguments);
			}
			else
			{
				var __parent=this.parent;
				this.parent=this.__parents[0];
				var __ret=__pointer.apply(this,arguments);
				this.parent=__parent;
				__parent=null;
				return __ret;
			}
		}
}

__super_class.prototype.construct=function(context)
{
	this.__context=context;
	this.__context.__parents=[];
	if(typeof this.__context.__prototype.__overridden=='undefined')
	{
		this.__context.__prototype.__overridden=true;
		for(var i in this.__context.__prototype)
		{
			if((typeof this.__context.__prototype[i]=='function') && 
				this.__context.__prototype[i]!=this.__super.prototype[i])
			{
				this.__context.__prototype[i]=this.__overridden_method(i,this.__context.__prototype[i]);
			}
		}
	}
	var a=new Array();
	for(var i=1;i<arguments.length;i++)
	{
		a.push(arguments[i]);
	}
	this.__context.parent=this.__parent;
	var __ret=this.__super.apply(context,a);
	this.__context.parent=this;
	return __ret;
}

Function.prototype.bind=function(context)
{
	var __method=this;
	var __context=context;
	return function()
	{
		return __method.apply(context,arguments);
	}
}

Function.prototype.extend=function(obj)
{
	this.prototype.parent=new __super_class(obj);
	this.prototype.__prototype=this.prototype;
	for(var i in obj.prototype)
	{
		if(typeof obj.prototype[i]=='function')
		{
			this.prototype[i]=obj.prototype[i];
			this.prototype.parent.__super_method(i,obj.prototype[i]);
		}
		else if(i!='parent')
		{
			this.prototype[i]=obj.prototype[i];
		}
	}
}

function dp(object)
{
	var descString="";
	for(var value in object)
		descString+=(value+" => "+object[value]+"\n");
	if(descString!="")
		alert(descString);
	else
		alert(object);
}


function generic_dialog()
{
	this.className="wallpro_dialog";
	this.content=null;
	this.obj=null;
	this.popup=null;
	this.iframe=null;
	this.hidden_objects=new Array();
}
generic_dialog.prototype.should_hide_objects=navigator.userAgent.indexOf('Mac OS X')!=-1;

generic_dialog.prototype.set_content=function(html)
{
	this.content.innerHTML=html;
	if(generic_dialog.prototype.should_hide_objects)
	{
		var imgs=this.content.getElementsByTagName('img');
		for(var i=0;i<imgs.length;i++)
		{
			imgs[i].onload=imgs[i].onload 
				? function(){this.img.onload();this.dialog.hide_objects()}.bind({img:imgs[i],dialog:this}) 
				: this.hide_objects.bind(this);
		}
	}
}

generic_dialog.prototype.show_dialog=function(html)
{
	if(!this.obj)
	{
		this.build_dialog();
	}
	this.set_content(html);
	this.show();
}

generic_dialog.prototype.show_ajax_dialog=function(src)
{
	this.show_dialog('<div class="dialog_loading">Loading...</div>');
	var myself=this;
	var ajax=new Ajax(function(obj,text)
	{
		myself.show_dialog(text);
	});
	ajax.get(src);
}

generic_dialog.prototype.show_prompt=function(title,content)
{
	this.show_dialog('<h2><span>'+title+'</span></h2><div class="dialog_content">'+content+'</div>');
}

generic_dialog.prototype.show_message=function(title,content,button)
{
	if(button==null)
	{
		button='OK';
	}
	this.show_choice(title,content,button,function()
		{
			generic_dialog.get_dialog(this).fade_out(100)
		});
}

generic_dialog.prototype.show_choice=function(title,content,button1,button1js,button2,button2js)
{
	var buttons='<div class="dialog_buttons"><input class="submit" type="button" value="'+button1+'">';
	if(button2)
	{
		buttons+='<input class="submit" type="button" value="'+button2+'">';
	}
	this.show_prompt(title,this.content_to_markup(content)+buttons);
	var inputs=this.obj.getElementsByTagName('input');
	if(button2)
	{
		button1obj=inputs[inputs.length-2];
		button2obj=inputs[inputs.length-1];
	}
	else
	{
		button1obj=inputs[inputs.length-1];
	}
	if(button1js)
	{
		if(typeof button1js=='string')
		{
			eval('button1js = function(){'+button1js+'}');
		}
		button1obj.onclick=button1js;
	}
	
	if(button2js=="close")
	{
		eval('button2js = function(){generic_dialog.get_dialog(this).fade_out(100)}');
		button2obj.onclick=button2js;
	}
	else if(button2js)
	{
		if(typeof button2js=='string')
		{
			eval('button2js = function(){'+button2js+'}');
		}
		button2obj.onclick=button2js;
	}
	document.onkeyup=function(e)
	{
		var keycode=(e&&e.which)?e.which:event.keyCode;
		var btn2_exists=(typeof button2obj!='undefined');
		var is_webkit=(navigator.userAgent.indexOf('WebKit')>0);
		if(is_webkit&&keycode==13)
		{
			button1obj.click();
		}
		if(keycode==27)
		{
			if(btn2_exists)
			{
				button2obj.click();
			}
			else
			{
				button1obj.click();
			}
		}
		document.onkeyup=function(){}
	}
	button1obj.focus();
}

generic_dialog.prototype.show_form=function(title,content,button,target)
{
	content='<form action="'+target+'" method="post">'+this.content_to_markup(content);
	var post_form_id=$('post_form_id');
	if(post_form_id)
	{
		content+='<input type="hidden" name="post_form_id" value="'+post_form_id.value+'">';
	}
	
	var button1 = button;
	var button2 = "Cancel";
	if(button.indexOf('|')!=-1)
	{
		var temp = button.split('|');
		button1 = temp[0];
		button2 = temp[1];
	}
	
	content+='<div class="dialog_buttons"><input class="submit" name="confirm" type="submit" value="'+button1+'">';
	content+='<input type="hidden" name="next" value="'+htmlspecialchars(document.location)+'">';
	content+='<input class="submit" type="button" value="'+button2+'" onclick="generic_dialog.get_dialog(this).fade_out(100)"></form>';
	this.show_prompt(title,content);
	return false;
}


generic_dialog.prototype.submit_ajax_form=function(url)
{
	var handlerFunc = function(t) {
	    $('ajaxpopupformdiv').innerHTML = t.responseText;
		$('gendiag_clbutton').value = "Close";
		generic_dialog.get_dialog($('ajaxpopupform')).fade_out(100,2500);
	}
	
	var errFunc = function(t) {
	    $('ajaxpopupformdiv').innerHTML = "<p>Sorry, but an error occurred while processing your request.</p>";
		$('gendiag_clbutton').value = "Close";
		generic_dialog.get_dialog($('ajaxpopupform')).fade_out(100,2500);
	}
		
	var elms = Form.getElements('ajaxpopupform');
	var data = "ajax=1";

	for(i=0;i<elms.length;i++)
	{
		data += "&"+elms[i].name+"="+escapeURI(elms[i].value);
	}
	
	// Set the loader for the form..
	var loader = "";
	loader += '<p><div align="center">';
	loader += '<img src="http://static.picmember.com/images/whiteloader.gif" width="48" height="48" border="0"><br>';
	loader += "<b>Loading...</b><br>";
	loader += '</div></p>';
	$('ajaxpopupformdiv').innerHTML = loader;
	$('ajaxpopupform_button').style.display='none';
	new Ajax.Request(url, {method:'post', postBody:data, onSuccess:handlerFunc, onFailure:errFunc});
}
generic_dialog.prototype.show_ajax_form=function(title,content,button,target)
{
	content='<form id="ajaxpopupform"><div id="ajaxpopupformdiv">'+this.content_to_markup(content)+'</div>';
	
	var button1 = button;
	var button2 = "Cancel";
	if(button.indexOf('|')!=-1)
	{
		var temp = button.split('|');
		button1 = temp[0];
		button2 = temp[1];
	}
	
	content+='<div class="dialog_buttons"><input class="submit" name="confirm" type="button" value="'+button1+'" id="ajaxpopupform_button" onclick="generic_dialog.get_dialog(this).submit_ajax_form(\''+target+'\');">';
	content+='<input type="hidden" name="next" value="'+htmlspecialchars(document.location)+'">';
	content+='<input class="submit" id="gendiag_clbutton" type="button" value="'+button2+'" onclick="generic_dialog.get_dialog(this).fade_out(100)"></form>';
	this.show_prompt(title,content);
	return false;
}



generic_dialog.prototype.content_to_markup=function(content)
{
	return(typeof content=='string') 
		? '<div class="dialog_body" id="generic_dialog_content">'+content+'</div>'
		:'<div class="dialog_summary">'+content.summary+'</div><div class="dialog_body">'+content.body+'</div>';
}

generic_dialog.prototype.hide=function()
{
	if(this.obj)
	{
		this.obj.style.display='none';
	}
	if(this.timeout)
	{
		clearTimeout(this.timeout);
		this.timeout=null;
		return;
	}
	if(this.hidden_objects.length)
	{
		for(var i in this.hidden_objects)
		{
			this.hidden_objects[i].style.visibility='';
		}
		this.hidden_objects=new Array();
	}
}

generic_dialog.prototype.anim_res=5;
generic_dialog.prototype.fade_out=function(interval,timeout,first_call)
{
	if(timeout)
	{
		this.timeout=setTimeout(function(){this.fade_out(interval)}.bind(this),timeout);
		return;
	}
	else if(this.timeout)
	{
		clearTimeout(this.timeout);
		this.timeout=null;
	}
	if(!interval)
		interval=350;
	if(!first_call)
		first_call=(new Date).getTime()-this.anim_res;
	
	var fade=1.0-(((new Date).getTime()-first_call)/interval)*1.0;
	if(fade>0)
	{
		set_opacity(this.obj,fade);
		setTimeout(function(){this.fade_out(interval,0,first_call)}.bind(this),this.anim_res);
	}
	else
	{
		this.hide();
		set_opacity(this.obj,1);
	}
}

generic_dialog.prototype.show=function()
{
	if(this.obj && this.obj.style.display)
	{
		this.obj.style.visibility='hidden';
		this.obj.style.display='';
		this.reset_dialog();
		this.obj.style.visibility='';
		this.obj.dialog=this;
	}
	else
	{
		this.reset_dialog();
	}
	this.hide_objects();
}

generic_dialog.prototype.hide_objects=function()
{
	if(!this.should_hide_objects)
	{
		return;
	}
	var rect={ 
			x:elementX(this.content),
			y:elementY(this.content),
			w:this.content.offsetWidth,
			h:this.content.offsetHeight
			};
	var objects=new Array();
	var iframes=document.getElementsByTagName('iframe');
	for(var i=0;i<iframes.length;i++)
	{
		if(iframes[i].className.indexOf('share_hide_on_dialog')!=-1)
		{
			objects.push(iframes[i]);
		}
	}
	var swfs=document.getElementsByTagName('embed');
	for(var i=0;i<swfs.length;i++)
	{
		objects.push(swfs[i]);
	}
	for(var i=0;i<objects.length;i++)
	{
		var pn=false;
		var node=objects[i].offsetHeight?objects[i]:objects[i].parentNode;
		swf_rect={x:elementX(node),y:elementY(node),w:node.offsetWidth,h:node.offsetHeight};
		if(rect.y+rect.h>swf_rect.y&&swf_rect.y+swf_rect.h>rect.y&&rect.x+rect.w>swf_rect.x
			&&swf_rect.x+swf_rect.w>rect.w&&array_indexOf(this.hidden_objects,node)==-1)
		{
			this.hidden_objects.push(node);
			node.style.visibility='hidden';
			node.style.visibility='hidden';
		}
	}
}
function hidePopupDiag()
{
	Effect.Fade('generic_dialog');
}
generic_dialog.prototype.build_dialog=function()
{
	if(!this.obj && !(this.obj=$('generic_dialog')))
	{
		this.obj=document.createElement('div');
		this.obj.id='generic_dialog';
	}
	this.obj.className='generic_dialog'+(this.className ? ' '+this.className : '');
	this.obj.style.display='none';
	document.body.appendChild(this.obj);
	if(!this.iframe && !(this.iframe=$('generic_dialog_iframe')))
	{
		this.iframe=document.createElement('iframe');
		this.iframe.id='generic_dialog_iframe';
	}
	this.iframe.frameBorder='0';
	this.obj.appendChild(this.iframe);
	if(!this.popup && !(this.popup=$('generic_dialog_popup')))
	{
		this.popup=document.createElement('div');
		this.popup.id='generic_dialog_popup';
	}
	this.obj.appendChild(this.popup);
	
	// Remove this comment to have draggable popup boxes
	//new Draggable('generic_dialog');
}

generic_dialog.prototype.reset_dialog=function()
{
	if(!this.popup || !this.iframe)
		return;
	this.reset_dialog_obj();
	this.iframe.style.width=this.popup.offsetWidth+'px';
	this.iframe.style.height=this.popup.offsetHeight+'px';
}

generic_dialog.prototype.reset_dialog_obj=function(){}

generic_dialog.prototype.set_width=function(w)
{
	this.obj.style.width=w ? w+'px' : '';
}

generic_dialog.get_dialog=function(obj)
{
	while(!obj.dialog && obj.parentNode)
	{
		obj=obj.parentNode;
	}
	return obj.dialog ? obj.dialog : false;
}


// Popup dialog
function pop_dialog(className)
{
	this.parent.construct(this,className);
}
pop_dialog.extend(generic_dialog);
pop_dialog.prototype.build_dialog=function()
{
	this.parent.build_dialog();
	this.obj.className+=' pop_dialog';
	this.popup.innerHTML='<table class="pop_dialog_table">'
	+'<tr><td class="pop_topleft"></td><td class="pop_border"></td><td class="pop_topright"></td></tr>'
	+'<tr><td class="pop_border"></td><td class="pop_content" id="pop_content"></td><td class="pop_border"></td></tr>'
	+'<tr><td class="pop_bottomleft"></td><td class="pop_border"></td><td class="pop_bottomright"></td></tr>'
	+'</table>';
	this.content=document.getElementById('pop_content');
}
pop_dialog.prototype.reset_dialog_obj=function()
{
	this.obj.style.top=(document.documentElement.scrollTop 
		? document.documentElement.scrollTop 
		: document.body.scrollTop)
		+'px';
	this.obj.style.left=(document.body.offsetWidth-this.popup.offsetWidth)/2+'px';
}

// Contextual Dialog
function contextual_dialog(className)
{
	this.parent.construct(this,className);
}
contextual_dialog.extend(generic_dialog);
contextual_dialog.prototype.set_context=function(obj)
{
	this.context=obj;
}
contextual_dialog.prototype.build_dialog=function()
{
	this.parent.build_dialog();
	this.obj.className+=' contextual_dialog';
	this.popup.innerHTML='<div class="contextual_arrow"><span>^_^keke1</span></div>'
						+'<div class="contextual_dialog_content"></div>';
	this.content=this.popup.getElementsByTagName('div')[1];
}
contextual_dialog.prototype.reset_dialog_obj=function()
{
	this.obj.style.top=(elementY(this.context)+7)+'px';
	this.obj.style.left=elementX(this.context)-this.obj.offsetWidth+12+'px';
}


function showFeedbackForm()
{
	var content="<b>Feedback Message:</b><br><textarea style='border:1px solid #000;' cols='40' rows='5' name='feedback'></textarea><br>Please note that if you are having ";
	content += "trouble logging into PicMember.com, you must provide us with the email address you used to sign up so that we can help you ";
	content += "with your account.";
	return (new pop_dialog()).show_ajax_form('Feedback','<p>'+content+'</p>','Send Feedback','/ajax/feedback.php');
}

/*
onclick="return (new pop_dialog('wallpro_dialog')).show_form('TITLE','<p>areyousure</p>','OK','/the/url/to/send');"
*/