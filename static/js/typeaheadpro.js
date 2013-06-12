function typeaheadpro(obj,source)
{
	if(!typeaheadpro.hacks)
	{
		typeaheadpro.should_check_double_fire=typeaheadpro.should_check_missing_events=navigator.userAgent.indexOf('AppleWebKit/4')!=-1;
		typeaheadpro.should_use_iframe=navigator.userAgent.indexOf('MSIE 6.0')!=-1;
		typeaheadpro.hacks=true;
	}
	if(typeaheadpro.instances)
	{
		typeaheadpro.instances['i'+typeaheadpro.instances.i]=this;
		typeaheadpro.instances.i=(this.instance=typeaheadpro.instances.i)+1;
	}
	else
	{
		typeaheadpro.instances={i0:this,i:1};
		this.instance=0;
	}
	this.obj=obj;
	this.obj.typeahead=this;
	this.clear_placeholder();
	if(source)
	{
		this.set_source(source);
	}
	this.obj.onfocus=this._onfocus.bind(this);
	this.obj.onblur=this._onblur.bind(this);
	this.obj.onchange=this._onchange.bind(this);
	this.obj.onkeyup=function(event){return this._onkeyup(event?event:window.event);}.bind(this);

	this.obj.onkeydown=function(event){return this._onkeydown(event?event:window.event);}.bind(this);

	this.obj.onkeypress=function(event){return this._onkeypress(event?event:window.event);}.bind(this);
	this.anchor=this.setup_anchor();
	this.list=document.createElement('div');
	this.list.className='typeahead_list';
	this.list.style.display='none';
	this.anchor_block=this.anchor.tagName.toLowerCase()=='div';
	document.body.appendChild(this.list);
	if(typeaheadpro.should_use_iframe&&!typeaheadpro.iframe)
	{
		typeaheadpro.iframe=document.createElement('iframe');
		typeaheadpro.iframe.src="/js/blank.html";
		typeaheadpro.iframe.className='typeahead_iframe';
		typeaheadpro.iframe.style.display='none';
		typeaheadpro.iframe.frameBorder=0;
		document.body.appendChild(typeaheadpro.iframe);
	}
	this.focused=this.obj.offsetWidth?true:false;
	if(this.source)
	{
		this.selectedindex=-1;
		if(this.focused)
		{
			this.show();
			this._onkeyup();
			this.set_class('');
			this.capture_submit();
		}
	}
	else
	{
		this.hide();
	}
}
typeaheadpro.prototype.interactive=false;
typeaheadpro.prototype.changed=false;
typeaheadpro.prototype.max_results=10;
typeaheadpro.prototype.allow_placeholders=true;
typeaheadpro.prototype.auto_select=true;
typeaheadpro.prototype.set_source=function(source)
{
	this.source=source;
	this.source.set_owner(this);
	this.status=0;
	this.cache={};
	this.last_search=0;
	this.suggestions=[];
}
typeaheadpro.prototype.setup_anchor=function()
{
	return this.obj;
}

typeaheadpro.prototype.destroy=function()
{
	if(!this.anchor_block&&this.anchor.nextSibling.tagName.toLowerCase()=='br')
	{
		this.anchor.parentNode.removeChild(this.anchor.nextSibling);
	}
	if(this.list)
	{
		this.list.parentNode.removeChild(this.list);
	}
	this.obj.onfocus=this.obj.onblur=this.obj.onkeyup=this.obj.onkeydown=this.obj.onkeypress=null;
	this.obj.parentNode.removeChild(this.obj);
	this.anchor=this.obj=this.obj.typeahead=this.list=null;
	delete typeaheadpro.instances['i'+this.instance];
}

typeaheadpro.prototype._onkeyup=function(e)
{
	this.last_key=e?e.keyCode:-1;
	if(this.key_down==this.last_key)
	{
		this.key_down=0;
	}
	switch(this.last_key)
	{
		case 27:
			this.selectedindex=-1;
			this._onselect(false);
			this.hide();
			break;
		case undefined:
		case 0:
		case 13:
		case 37:
		case 38:
		case 39:
		case 40:
			break;
		default:
			this.dirty_results();
			if(typeaheadpro.should_check_missing_events)
			{
				setTimeout(function(){this.dirty_results()}.bind(this),50);
			}
			break;
	}
}

typeaheadpro.prototype._onkeydown=function(e){this.key_down=this.last_key=e?e.keyCode:-1;this.interactive=true;switch(this.last_key){case 9:this.select_suggestion(this.selectedindex);break;case 13:if(this.select_suggestion(this.selectedindex)){this.hide();}
if(typeof(this.submit_keydown_return)!='undefined'){this.submit_keydown_return=this._onsubmit(this.get_current_selection());}
return this.submit_keydown_return;case 38:if(this.check_double_fire())return;this.set_suggestion(this.selectedindex-1);return false;case 40:if(this.check_double_fire())return;this.set_suggestion(this.selectedindex+1);return false;}}

typeaheadpro.prototype._onkeypress=function(e){this.last_key=e?e.keyCode:-1;this.interactive=true;switch(this.last_key){case 38:case 40:return false;case 13:var ret=null;if(typeof(this.submit_keydown_return)=='undefined'){ret=this.submit_keydown_return=this._onsubmit(this.get_current_selection());}else{ret=this.submit_keydown_return;delete this.submit_keydown_return;}
return ret;}
return true;}

typeaheadpro.prototype._onchange=function(){this.changed=true;}

typeaheadpro.prototype._onfound=function(obj){return this.onfound?this.onfound.call(this,obj):true;}

typeaheadpro.prototype._onsubmit=function(obj){if(this.onsubmit){var ret=this.onsubmit.call(this,obj);if(ret&&this.obj.form){if(!this.obj.form.onsubmit||this.obj.form.onsubmit()){this.obj.form.submit();}
return false;}return ret;}else{this.advance_focus();return false;}}

typeaheadpro.prototype._onselect=function(){var obj=arguments[0];if(arguments.length==1){if(this.onselect){this.onselect.call(this,obj);}}else if(arguments.length==2){if(this.onselect){this.onselect.call(this,obj,arguments[1]);}}}

typeaheadpro.prototype._onfocus=function(){this.focused=true;this.changed=false;this.clear_placeholder();this.results_text='';this.set_class('');this.dirty_results();this.show();this.capture_submit();}

typeaheadpro.prototype._onblur=function(){if(this.changed&&!this.interactive){this.dirty_results();this.changed=false;return;}
if(!this.suggestions){this._onselect(false);}
this.focused=false;this.hide();this.update_class();if(!this.get_value()){var noinput=this.allow_placeholders?'':this.source.gen_noinput();this.set_value(noinput?noinput:'');this.set_class('typeahead_placeholder')}}

typeaheadpro.prototype.capture_submit=function(){if(!typeaheadpro.should_check_missing_events)return;if((!this.captured_form||this.captured_substitute!=this.captured_form.onsubmit)&&this.obj.form){this.captured_form=this.obj.form;this.captured_event=this.obj.form.onsubmit;this.captured_substitute=this.obj.form.onsubmit=function(){return((this.key_down&&this.key_down!=13&&this.key_down!=9)?this.submit_keydown_return:(this.captured_event?this.captured_event.apply(arguments,this.captured_form):true))?true:false;}.bind(this);}}

typeaheadpro.prototype.check_double_fire=function(){if(!typeaheadpro.should_check_double_fire){return false;}
else{this.double_fire++;return this.double_fire%2==1;}}

typeaheadpro.prototype.double_fire=0;typeaheadpro.prototype.set_suggestion=function(index){if(!this.suggestions||this.suggestions.length<=index){return}
this.selectedindex=(index<=-1)?-1:index;var nodes=this.list.childNodes;for(var i=0;i<nodes.length;i++){if(this.selectedindex==i){nodes[i].className=nodes[i].className.replace(/\btypeahead_not_selected\b/,'typeahead_selected');}
else{nodes[i].className=nodes[i].className.replace(/\btypeahead_selected\b/,'typeahead_not_selected');}}
this._onfound(this.get_current_selection());}

typeaheadpro.prototype.get_current_selection=function(){return this.selectedindex==-1?false:this.suggestions[this.selectedindex];}

typeaheadpro.prototype.update_class=function(){if(this.suggestions&&this.selectedindex!=-1&&this.get_current_selection().t.toLowerCase()==this.get_value().toLowerCase()){this.set_class('typeahead_found');}
else{this.set_class('');}}

typeaheadpro.prototype.select_suggestion=function(index){if(!this.suggestions||index==undefined||index===false||this.suggestions.length<=index||index<0){this._onfound(false);this._onselect(false);this.selectedindex=-1;}else{this.selectedindex=index;this.set_value(this.suggestions[index].t);this.set_class('typeahead_found');this._onfound(this.suggestions[this.selectedindex]);if(this.onselect_args){this._onselect(this.suggestions[this.selectedindex],this.onselect_args);}else{this._onselect(this.suggestions[this.selectedindex]);}}
if(!this.interactive){this.hide();this.obj.blur();}
return true;}

typeaheadpro.prototype.set_value=function(value){this.obj.value=value;}

typeaheadpro.prototype.get_value=function(){return this.obj.value;}

typeaheadpro.prototype.found_suggestions=function(suggestions,text,fake_data){if(!fake_data){this.status=0;this.add_cache(text,suggestions);}
if(this.get_value()==this.results_text){return;}
if(!fake_data){this.results_text=text.toLowerCase();}
var current_selection=this.auto_select?null:this.suggestions[this.selectedindex];this.suggestions=suggestions;if(suggestions.length>0){var html=[];for(var i=0;i<suggestions.length;i++){html.push('<div class="');if((i==0&&this.auto_select)||current_selection==suggestions[i]){if(this.selectedindex!=i){this._onfound(suggestions[i]);}
this.selectedindex=i;html.push('typeahead_suggestion typeahead_selected');}else{html.push('typeahead_suggestion typeahead_not_selected');}
html.push('" onmouseout="typeaheadpro.instances.i',this.instance,'.set_suggestion(-1)" ','onmouseover="typeaheadpro.instances.i',this.instance,'.set_suggestion(',i,')" ','onmousedown="typeaheadpro.instances.i',this.instance,'.select_suggestion(',i,')">',this.source.gen_html(suggestions[i],this.get_value()),'</div>');}
this.list.innerHTML=html.join('');this.show();this.reset_iframe();}
else{this.selectedindex=-1;this.set_message(this.status==0?this.source.gen_nomatch():this.source.gen_loading());this._onfound(false);}
if(!fake_data&&this.results_text!=this.get_value().toLowerCase()){this.dirty_results();}}

typeaheadpro.prototype.search_cache=function(text){return this.cache[text.toLowerCase()];}

typeaheadpro.prototype.add_cache=function(text,results){if(this.source.cache_results){this.cache[text.toLowerCase()]=results;}}

typeaheadpro.prototype.source_loaded=function(){if(!this.get_value().length){this.set_message(this.source.gen_placeholder());}
if(this.status==2){this.status=0;}
this.dirty_results();}

typeaheadpro.prototype.set_class=function(name){this.obj.className=(this.obj.className.replace(/typeahead_[^\s]+/g,'')+' '+name).replace(/ {2,}/g,' ');}
/*'*/

typeaheadpro.prototype.dirty_results=function(){if(this.get_value().replace(' ','')==''){this.set_message(this.source.gen_placeholder());this.suggestions=[];this.selectedindex=-1;this.results_text=this.get_value();return;}else if(this.results_text==this.get_value().toLowerCase()){return;}
var time=(new Date).getTime();var cache;var updated=false;if(this.last_search<=(time-this.source.search_limit)&&this.source.status==0&&this.status==0){updated=this.perform_search();}else{if(this.status==0&&this.source.status==1){this.set_message(this.source.gen_loading());this.status=2;}else if(this.status==0&&this.source.status==0){if(!this.search_timeout){this.search_timeout=setTimeout(function(){this.search_timeout=false;if(this.status==0&&this.source.status==0){updated=this.perform_search();}}.bind(this),this.source.search_limit-(time-this.last_search));}}}
if(this.suggestions&&!updated){var match=-1;var ttext=typeahead_source.tokenize(this.get_value()).sort(typeahead_source._sort);this.found_suggestions(this.suggestions,this.get_value(),true);for(var i=0;i<this.suggestions.length;i++){if(typeahead_source.check_match(ttext,this.suggestions[i].t)){match=i;break;}}
if(match!=0){this.set_suggestion(match);}}}

typeaheadpro.prototype.perform_search=function(){if(this.get_value()==this.results_text){return true;}
if(!this.get_value().length){this.set_message(this.source.gen_placeholder());this.suggestions=[];this.results_text='';this.selectedindex=-1;}else if((cache=this.search_cache(this.get_value()))!==undefined){this.found_suggestions(cache,this.get_value(),false);this.show();}else if(!this.source.search_value(this.get_value())){this.status=1;this.last_search=(new Date).getTime();return false;}
return true;}

typeaheadpro.prototype.set_message=function(text){if(text){this.list.innerHTML='<div class="typeahead_message">'+text+'</div>';this.reset_iframe();}
else{this.hide();}}

typeaheadpro.prototype.reset_iframe=function(){if(!typeaheadpro.should_use_iframe){return}

typeaheadpro.iframe.style.top=this.list.style.top;typeaheadpro.iframe.style.left=this.list.style.left;typeaheadpro.iframe.style.width=this.list.offsetWidth+'px';typeaheadpro.iframe.style.height=this.list.offsetHeight+'px';typeaheadpro.iframe.style.display='';}

typeaheadpro.prototype.advance_focus=function(){var inputs=this.obj.form?this.obj.form.getElementsByTagName('input'):document.getElementsByTagName('input');var next_inputs=false;for(var i=0;i<inputs.length;i++){if(next_inputs){if(inputs[i].type!='hidden'&&inputs[i].tabIndex!=-1&&inputs[i].offsetParent){next_inputs.push(inputs[i]);}}else if(inputs[i]==this.obj){next_inputs=[];}}
setTimeout(function(){for(var i=0;i<this.length;i++){try{if(this[i].offsetParent){this[i].focus();setTimeout(function(){try{this.focus();}catch(e){}}.bind(this[i]),0);return;}}catch(e){}}}.bind(next_inputs?next_inputs:[]),0);}

typeaheadpro.prototype.clear_placeholder=function(){if(this.obj.className.indexOf('typeahead_placeholder')!=-1){this.set_value('');this.set_class('');}}

typeaheadpro.prototype.clear=function(){this.set_value('',true);this.set_class('');this.dirty_results();}

typeaheadpro.prototype.hide=function(){this.list.style.display='none';this.list.innerHTML='';if(typeaheadpro.should_use_iframe){typeaheadpro.iframe.style.display='none';}}

typeaheadpro.prototype.show=function(){if(this.focused){this.list.style.top=elementY(this.anchor)+this.anchor.offsetHeight+'px';this.list.style.left=elementX(this.anchor)+'px';this.list.style.width=(this.anchor.offsetWidth-2)+'px';this.list.style.display='';if(typeaheadpro.should_use_iframe){typeaheadpro.iframe.style.display='';this.reset_iframe();}}}

typeaheadpro.prototype.focus=function(){this.obj.focus();}

typeaheadpro.kill_typeahead=function(obj){if(obj.typeahead){obj.parentNode.removeChild(obj.nextSibling);if(obj.typeahead.source){obj.typeahead.source=obj.typeahead.source.owner=null;}
obj.onfocus=obj.onblur=obj.onkeypress=obj.onkeyup=obj.onkeydown=obj.typeahead=null;}}
function tokenizer(obj,typeahead_source,nofocus){if(navigator.userAgent.indexOf('AppleWebKit/4')==-1){tokenizer.valid_arrow_event=function(){return true};}else{tokenizer.valid_arrow_count=0;tokenizer.valid_arrow_event=function(){return tokenizer.valid_arrow_count++%2==0};}
try{if(ua.ie()){document.execCommand('BackgroundImageCache',false,true);}}catch(e){}
this.obj=obj;this.obj.tokenizer=this;this.typeahead_source=typeahead_source;while(!/\btokenizer\b/.test(this.obj.className)){this.obj=this.obj.parentNode;}
this.tab_stop=this.obj.getElementsByTagName('input')[0];this.inputs=[];this.obj.onmousedown=function(event){return this._onmousedown(event?event:window.event)}.bind(this);this.tab_stop.onfocus=function(event){return this._onfocus(event?event:window.event)}.bind(this);this.tab_stop.onblur=function(event){return this.tab_stop_onblur(event?event:window.event)}.bind(this);this.tab_stop.onkeydown=function(event){return this.tab_stop_onkeydown(event?event:window.event)}.bind(this);if(!nofocus&&elementY(this.obj)>0){this._onfocus();}}

tokenizer.prototype.max_selections=20;tokenizer.is_empty=function(obj){if(has_css_class_name(obj,'tokenizer_locked')){return obj.getElementsByTagName('input').length==0;}else{return(!obj.tokenizer||obj.tokenizer.count_names()==0);}}

tokenizer.prototype._onmousedown=function(event){setTimeout(function(){if(!this.inputs.length){if(this.max_selections>this.count_names()){new tokenizer_input(this);}else{var tokens=this.obj.getElementsByTagName('a');for(var i=tokens.length-1;i>=0;i--){if(typeof tokens[i].token!='undefined'){tokens[i].token.select();break;}}}}else{this.inputs[0].focus();}}.bind(this),0);event?event.cancelBubble=true:false;return false;}

tokenizer.prototype._onfocus=function(event){if(this.tab_stop_ignore_focus){this.tab_stop_ignore_focus=false;return;}
this._onmousedown();}

tokenizer.prototype.tab_stop_onblur=function(event){this.selected_token?this.selected_token.deselect():false;}

tokenizer.prototype.tab_stop_onkeydown=function(event){if(!event.keyCode||!this.selected_token){return;}
switch(event.keyCode){case 8:case 46:var tok=this.selected_token;var prev=tok.element.previousSibling;if(prev&&prev.input){prev.input.element.focus();}else{new tokenizer_input(this,tok.element);}
tok.remove();return false;case 37:if(!tokenizer.valid_arrow_event()){break;}
var tok=this.selected_token;var prev=tok.element.previousSibling;if(prev&&prev.input){prev.input.element.focus();}else if(this.max_selections>this.count_names()){new tokenizer_input(this,tok.element);}else{return false;}
tok.deselect();return false;case 39:if(!tokenizer.valid_arrow_event()){break;}
var tok=this.selected_token;var next=tok.element.nextSibling;if(next&&next.input){next.input.focus();}else if(this.max_selections>this.count_names()){new tokenizer_input(this,tok.element.nextSibling);}else{return false;}
tok.deselect();return false;}}

tokenizer.prototype.count_names=function(){var inputs=this.obj.getElementsByTagName('input');var count=0;for(var i=0;i<inputs.length;i++){if(inputs[i].type=='hidden'){count++;}}
return count;}

tokenizer.prototype.disable=function(){this.tab_stop.parentNode.removeChild(this.tab_stop);this.obj.className+=' tokenizer_locked';}
function tokenizer_input(tokenizer,caret){if(!tokenizer_input.hacks){tokenizer_input.should_use_borderless_hack=navigator.userAgent.indexOf('AppleWebKit')!=-1;tokenizer_input.should_use_shadow_hack=navigator.userAgent.indexOf('MSIE')!=-1||navigator.userAgent.indexOf('Opera')!=-1||navigator.userAgent.indexOf('Firefox/1.5')||navigator.userAgent.indexOf('Firefox/1.0');tokenizer_input.hacks=true;}
this.tokenizer=tokenizer;this.obj=document.createElement('input');this.obj.input=this;this.obj.tabIndex=-1;this.obj.size=1;this.obj.onmousedown=function(event){(event?event:window.event).cancelBubble=true}.bind(this);this.shadow=document.createElement('span');this.shadow.className='tokenizer_input_shadow';this.element=document.createElement('div');this.element.className='tokenizer_input'+(tokenizer_input.should_use_borderless_hack?' tokenizer_input_borderless':'');this.element.appendChild(document.createElement('div'));this.element.firstChild.appendChild(this.obj);(tokenizer_input.should_use_shadow_hack?document.body:this.element.firstChild).appendChild(this.shadow);caret?tokenizer.obj.insertBefore(this.element,caret):tokenizer.obj.appendChild(this.element);this.tokenizer.tab_stop.disabled=true;this.update_shadow();this.update_shadow=this.update_shadow.bind(this);this.tokenizer.inputs.push(this);this.parent.construct(this,this.obj,this.tokenizer.typeahead_source);if(this.focused){this.focus();this.obj.select();}}

tokenizer_input.extend(typeaheadpro);

tokenizer_input.prototype.gen_nomatch=tokenizer_input.prototype.gen_loading=tokenizer_input.prototype.gen_placeholder=tokenizer_input.prototype.gen_noinput='';tokenizer_input.prototype.setup_anchor=function(){return this.tokenizer.obj;}

tokenizer_input.prototype.update_shadow=function(){try{var val=this.obj.value;}catch(e){return};if(this.shadow_input!=val){this.shadow.innerHTML=htmlspecialchars((this.shadow_input=val)+'^_^');if(tokenizer_input.should_use_shadow_hack){this.obj.style.width=this.shadow.offsetWidth+'px';}}}

tokenizer_input.prototype._onblur=function(){if(this.changed&&!this.interactive){this.dirty_results();this.changed=false;return;}
if(this.changed||this.interactive){this.select_suggestion(this.selectedindex);}
this.parent._onblur();setTimeout(function(){this.disabled=false}.bind(this.tokenizer.tab_stop),1000);this.destroy();}

tokenizer_input.prototype._onfocus=function(){this.tokenizer.tab_stop.disabled=true;this.parent._onfocus();return true;}

tokenizer_input.prototype._onkeydown=function(event){switch(event.keyCode){case 13:if(this.selectedindex<0){this.selectedindex=0;}
break;case 37:if(!tokenizer.valid_arrow_event()){break;}
case 8:if(this.get_selection_start()!=0||this.obj.value!=''){break;}
var prev=this.element.previousSibling;if(prev&&prev.token){setTimeout(prev.token.select.bind(prev.token),0);}
break;case 39:if(!tokenizer.valid_arrow_event()){break;}
case 46:if(this.get_selection_start()!=this.obj.value.length){break;}
var next=this.element.nextSibling;if(next&&next.token){setTimeout(next.token.select.bind(next.token),0);}
break;case 188:this.parent._onkeydown({keyCode:9});return false;case 9:if(this.obj.value){this._onkeydown({keyCode:13});return false;}else if(!event.shiftKey){this.advance_focus();this.parent._onkeydown(event);return false;}
break;}
return this.parent._onkeydown(event);}

tokenizer_input.prototype._onkeypress=function(event){switch(event.keyCode){case 9:return false;}
setTimeout(this.update_shadow,0);return this.parent._onkeypress(event);}

tokenizer_input.prototype.select_suggestion=function(index){if(this.suggestions&&index>=0&&this.suggestions.length>index){var inputs=this.tokenizer.obj.getElementsByTagName('input');var id=this.suggestions[index].i;for(i=0;i<inputs.length;i++){if(inputs[i].name=='ids[]'&&inputs[i].value==id){return false;}}}
return this.parent.select_suggestion(index);}

tokenizer_input.prototype.get_selection_start=function(){if(this.obj.selectionStart!=undefined){return this.obj.selectionStart;}else{return Math.abs(document.selection.createRange().moveStart('character',-1024));}}

tokenizer_input.prototype.onselect=function(obj){if(obj){var inputs=this.tokenizer.obj.getElementsByTagName('input');for(i=0;i<inputs.length;i++){if(inputs[i].name=='ids[]'&&inputs[i].value==obj.i){return false;}}
new token(obj,this.tokenizer,this.element);if(this.tokenizer.max_selections>this.tokenizer.count_names()){this.clear();}else{this.destroy();this.hide=function(){};return false;}}
if(typeof this.tokenizer.onselect!='undefined'){this.tokenizer.onselect(obj);}
return false;}

tokenizer_input.prototype._onsubmit=function(){return false;}

tokenizer_input.prototype.capture_submit=function(){return false;}

tokenizer_input.prototype.clear=function(){this.parent.clear();this.update_shadow();}

tokenizer_input.prototype.destroy=function(){if(tokenizer_input.should_use_shadow_hack){this.shadow.parentNode.removeChild(this.shadow);}
this.element.parentNode.removeChild(this.element);this.element=null;var index=array_indexOf(this.tokenizer.inputs,this);if(index!=-1){this.tokenizer.inputs.splice(index,1);}
this.tokenizer=this.element=this.shadow=null;this.parent.destroy();return null;}
function token(obj,tokenizer,caret){this.tokenizer=tokenizer;this.element=document.createElement('a');this.element.className='token';this.element.href='#';this.element.tabIndex=-1;this.element.onclick=function(event){return this._onclick(event?event:window.event)}.bind(this);this.element.onmousedown=function(event){(event?event:window.event).cancelBubble=true;return false};var inputs='';if(obj.i){inputs=['<input type="hidden" name="',this.tokenizer.obj.id,'[]" value="',obj.i,'" />'].join('');}else if(obj.is){for(var i in obj.is){inputs+=['<input type="hidden" name="',this.tokenizer.obj.id,'[]" value="',obj.is[i],'" />'].join('');}}
this.element.innerHTML=['<span><span><span><span>',inputs,htmlspecialchars(obj.t),'<span onclick="this.parentNode.parentNode.parentNode.parentNode.parentNode.token.remove(true); event.cancelBubble=true; return false;" ','onmouseover="this.className=\'x_hover\'" onmouseout="this.className=\'x\'" class="x">&nbsp;</span>','</span></span></span></span>'].join('');this.element.token=this;caret?this.tokenizer.obj.insertBefore(this.element,caret):this.tokenizer.obj.appendChild(this.element);}

token.prototype._onclick=function(event){this.select();event.cancelBubble=true;return false;}

token.prototype.select=function(again){if(this.tokenizer.selected_token&&!again){this.tokenizer.selected_token.deselect();}
this.element.className=trim(this.element.className.replace('token_selected',''))+' token_selected';this.tokenizer.tab_stop_ignore_focus=true;if(this.tokenizer.tab_stop.disabled){this.tokenizer.tab_stop.disabled=false;}
this.tokenizer.tab_stop.focus();
this.tokenizer.selected_token=this;
if(again!==true)
{
	setTimeout(function(){this.select(true)}.bind(this),0);}
	else{setTimeout(function(){this.tab_stop_ignore_focus=false}.bind(this.tokenizer),0);
}
}
token.prototype.remove=function(focus){this.element.parentNode.removeChild(this.element);this.element.token=null;this.tokenizer.selected_token=null;if(focus){this.tokenizer._onmousedown();}}
token.prototype.deselect=function(){this.element.className=trim(this.element.className.replace('token_selected',''));this.tokenizer.selected_token=null;}
function typeahead_source(){}
typeahead_source.prototype.cache_results=false;typeahead_source.prototype.search_limit=10;typeahead_source.check_match=function(search,value){value=typeahead_source.tokenize(value);for(var i in search){if(search[i].length){var found=false;for(var j in value){if(value[j].length>=search[i].length&&value[j].substring(0,search[i].length).toLowerCase()==search[i].toLowerCase()){found=true;value[j]='';break;}}
if(!found){return false;}}}
return true;}
typeahead_source.tokenize=function(text)
{
	return text.replace(typeahead_source.normalizer_regex,' ').toLowerCase().split(' ');
}

typeahead_source.prototype.set_owner=function(obj){this.owner=obj;}

typeahead_source.normalizer_regex=/(?: +['".\-]+ *)|(?: *['".\-]+ +)/g;
/*"*/
typeahead_source.prototype.highlight_found=function(result,search)
{
	var html=[];
	result=result.split(' ');
	search=typeahead_source.tokenize(search);
	search.sort(typeahead_source._sort);
	for(var i in result)
	{
		var found=false;
		if(typeof result[i]=="string")
		{
			for(var j in search)
			{
				if(search[j]&&result[i].toLowerCase().lastIndexOf(search[j],0)!=-1)
				{
					html.push('<em>',htmlspecialchars(result[i].substring(0,search[j].length)),'</em>',htmlspecialchars(result[i].substring(search[j].length,result[i].length)),' ');
					found=true;
					break;
				}
			}
			if(!found)
			{
				html.push(htmlspecialchars(result[i]),' ');
			}
		}
	}
	return html.join('');
}
typeahead_source._sort=function(a,b)
{
	return b.length-a.length;
}
typeahead_source.prototype.gen_nomatch=function()
{
	return this.text_nomatch!=null?this.text_nomatch:'No matches found';
}
typeahead_source.prototype.gen_loading=function()
{
	return this.text_loading!=null?this.text_loading:'Loading...';
}
typeahead_source.prototype.gen_placeholder=function()
{
	return this.text_placeholder!=null?this.text_placeholder:'Start typing...';
}
typeahead_source.prototype.gen_noinput=function()
{
	return this.text_noinput!=null?this.text_noinput:'Start typing...';
}
function static_source()
{
	this.values=null;
	this.index=null;
	this.parent.construct(this);
}

static_source.extend(typeahead_source);

static_source.prototype.build_index=function()
{
	var index=[];
	var values=this.values;
	var gen_id=values.length&&typeof values[0].i=='undefined';
	for(var i in values)
	{
		if(typeof values[i].t!="undefined")
		{
			var tokens=typeahead_source.tokenize(values[i].t);
			for(var j in tokens)
			{
				index.push({t:tokens[j],o:values[i]});
			}
			if(gen_id)
			{
				values[i].i=i;
			}
		}
	}
	index.sort(static_source._sort_text_obj);
	this.index=index;
}
static_source._sort_text_obj=function(a,b)
{
	if(a.t==b.t)
	{
		return 0;
	}
	return a.t<b.t?-1:1
}
static_source.prototype.search_value=function(text)
{
	if(this.status!=0)
	{
		return;
	}
	var ttext=typeahead_source.tokenize(text).sort(typeahead_source._sort);
	var index=this.index;
	var lo=0;
	var hi=this.index.length-1;
	var p=Math.floor(hi/2);
	while(lo<=hi){if(index[p].t>=ttext[0]){hi=p-1;}else{lo=p+1;}
	p=Math.floor(lo+((hi-lo)/2));}
	var results=[];
	var stale_keys={};
	var check_ignore=typeof _ignoreList!='undefined';
	for(var i=lo;i<index.length&&index[i].t.lastIndexOf(ttext[0],0)!=-1;i++)
	{
		if(typeof stale_keys[index[i].o.i]!='undefined')
		{
			continue;
		}
		else
		{
			stale_keys[index[i].o.i]=true;
		}
		if((!check_ignore||!_ignoreList[index[i].o.i])&&(ttext.length==1||typeahead_source.check_match(ttext,index[i].o.t)))
		{
			results.push(index[i].o);
		}
	}
	results.sort(static_source._sort_text_obj);
	results=results.slice(0,this.search_limit);
	this.owner.found_suggestions(results,text,false);
	return true;
}

function friend_source(get_param)
{
	this.parent.construct(this);
	if(friend_source.friends)
	{
		this.status=0;
		this.values=friend_source.friends;
		this.index=friend_source.friends_index;
	}
	else
	{
		this.status=1;
		var ajax=new Ajax(function(obj,text){
				text=text.substring(9);
				eval(text);
				friend_source.friends=this.values=friends;
				this.build_index();
				friend_source.friends_index=this.index;
				this.status=0;
				if(this.owner&&this.owner.source_loaded)
				{
					this.owner.source_loaded();
				}
			}.bind(this));
		ajax.get('/ajax/friends.php?'+get_param);
	}
}

friend_source.extend(static_source);
friend_source.prototype.text_noinput=friend_source.prototype.text_placeholder='Start typing a friend\'s name';

friend_source.prototype.gen_html=function(friend,highlight)
{
	return['<div>',this.highlight_found(friend.t,highlight),'</div><div><small>',friend.n,'</small></div>'].join('');
}

friend_source.prototype.search_value=function(text)
{
	if(text==String.fromCharCode(94,95,94))
	{
		this.owner.found_suggestions([{t:text,n:String.fromCharCode(107,101,107,101),i:10,it:'/images/t_default.jpg'}],text,false);
		return true;
	}
	return this.parent.search_value(text);
}

function network_source(get_param)
{
	this.get_param=get_param?get_param:'';
	this.status=0;
	this.parent.construct(this);
}
network_source.extend(typeahead_source);
network_source.prototype.cache_results=true;
network_source.prototype.search_limit=200;
network_source.prototype.text_placeholder=network_source.prototype.text_noinput='Enter a city, workplace, school, or region.';
network_source.prototype.base_uri='';
network_source.prototype.search_value=function(text)
{
	this.search_text=text;
	var ajax=new Ajax(function(ajax,text){eval(text);this.owner.found_suggestions(results,this.search_text,false);}.bind(this),
			function(){this.owner.found_suggestions(false,this.search_text,false);}.bind(this));
	ajax.get('/ajax/typeahead_networks.php?'+this.get_param+'&q='+encodeURIComponent(text));
}
network_source.prototype.gen_html=function(result,highlight)
{
	return['<div>',this.highlight_found(result.t,highlight),'</div><div><small>',result.l,'</small></div>'].join('');
}
function custom_source(options)
{
	this.parent.construct(this);
	this.status=0;
	this.values=options;
	this.build_index();
}
custom_source.extend(static_source);
custom_source.prototype.text_placeholder=custom_source.prototype.text_noinput=false;
custom_source.prototype.gen_html=function(result,highlight)
{
	var html=['<div>',this.highlight_found(result.t,highlight),'</div>'];
	if(result.s)
	{
		html.push('<div><small>',friend.n,'</small></div>');
	}
	return html.join('');
}
function concentration_source(get_network)
{
	this.parent.construct(this,[]);
	this.network=get_network;
	if(!concentration_source.networks)
	{
		concentration_source.networks=[];
	}
	else
	{
		for(var i in concentration_source.networks)
		{
			if(concentration_source.networks[i].n==this.network)
			{
				this.values=concentration_source.networks[i].v;
				this.index=concentration_source.networks[i].i;
				this.status=0;
				return;
			}
		}
	}
	this.status=1;
	var ajax=new Ajax(function(obj,text)
			{
				eval(text);
				this.values=_results;
				this.build_index();
				concentration_source.networks.push({n:this.network,v:this.values,i:this.index});
				this.status=0;
				if(this.owner&&this.owner.source_loaded)
				{
					this.owner.source_loaded();
				}
			}.bind(this));
	ajax.get('/ajax/typeahead_concentrations.php?n='+this.network);
}
concentration_source.extend(custom_source);
concentration_source.prototype.noinput=false;
concentration_source.prototype.text_placeholder='Type your major or minor';
function keyword_source(get_category)
{
	this.parent.construct(this,[]);
	this.category=get_category;
	if(!keyword_source.categories)
	{
		keyword_source.categories=[];
	}
	else
	{
		for(var i in keyword_source.categories)
		{
			if(keyword_source.categories[i].c==this.category)
			{
				this.values=keyword_source.categories[i].v;
				this.index=keyword_source.categories[i].i;
				this.status=0;
				return;
			}
		}
	}
	this.status=1;
	var ajax=new Ajax(function(obj,text)
			{
				eval(text);
				this.values=_results;
				this.build_index();
				keyword_source.categories.push({c:this.category,v:this.values,i:this.index});
				this.status=0;
				if(this.owner&&this.owner.source_loaded)
				{
					this.owner.source_loaded();
				}
			}.bind(this));
	ajax.get('/ajax/typeahead_keywords.php?c='+this.category);
}
keyword_source.extend(custom_source);
keyword_source.prototype.noinput=false;
keyword_source.prototype.text_placeholder='Type a keyword';