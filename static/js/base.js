
function ge()
{var ea;for(var i=0;i<arguments.length;i++){var e=arguments[i];if(typeof e=='string')
e=document.getElementById(e);if(arguments.length==1)
return e;if(!ea)
ea=new Array();ea[ea.length]=e;}
return ea;}
function $(){var el=ge.apply(null,arguments);if(!el){Util.warn('Tried to get element %q, but it is not present in the page. (Use ge() '+'to test for the presence of an element.)',arguments[0]);}
return el;}
function show()
{for(var i=0;i<arguments.length;i++){var element=ge(arguments[i]);if(element&&element.style)element.style.display='';}
return false;}
function hide()
{for(var i=0;i<arguments.length;i++){var element=ge(arguments[i]);if(element&&element.style)element.style.display='none';}
return false;}
function shown(el){el=ge(el);return(el.style.display!='none');}
function toggle()
{for(var i=0;i<arguments.length;i++){var element=ge(arguments[i]);element.style.display=(element.style.display=='block'||element.style.display=='')?'none':'block';}
return false;}
function is_descendent(base_obj,target_id){var target_obj=ge(target_id);if(base_obj==null)return;while(base_obj!=target_obj){if(base_obj.parentNode){base_obj=base_obj.parentNode;}else{return false;}}
return true;}
function close_more_list(){var list_expander=ge('expandable_more');if(list_expander){list_expander.style.display='none';removeEventBase(document,'click',list_expander.offclick,list_expander.id);}
var sponsor=ge('ssponsor');if(sponsor){sponsor.style.position='';}
var link_obj=ge('more_link');if(link_obj){link_obj.innerHTML='more';link_obj.className='expand_link more_apps';}}
function expand_more_list(){var ajax_ping=new Ajax();ajax_ping.onDone=function(ajaxObj,responseText){}
ajax_ping.onFail=function(ajaxObj){}
ajax_ping.post('/ajax/more_click.php');var list_expander=ge('expandable_more');var more_link=ge('more_section');if(more_link){remove_css_class_name(more_link,'highlight_more_link');}
if(list_expander){list_expander.style.display='block';list_expander.offclick=function(e){if(!is_descendent(e.target,'sidebar_content')){close_more_list();}}.bind(list_expander);addEventBase(document,'click',list_expander.offclick,list_expander.id);}
var sponsor=ge('ssponsor');if(sponsor){sponsor.style.position='static';}
var link_obj=ge('more_link');if(link_obj){link_obj.innerHTML='less';link_obj.className='expand_link less_apps';}}
function toggle_more_list(){var list_expander=ge('expandable_more');var ajax_ping=new Ajax();ajax_ping.onDone=function(ajaxObj,responseText){}
ajax_ping.onFail=function(ajaxObj){}
ajax_ping.post('/ajax/more_click.php');if(!list_expander){return false;}
if(list_expander.style.display=='none'){expand_more_list();}else{close_more_list();}}
function remove_node(node)
{if(node.removeNode)
node.removeNode(true);else{for(var i=node.childNodes.length-1;i>=0;i--)
remove_node(node.childNodes[i]);node.parentNode.removeChild(node);}
return null;}
function create_hidden_input(name,value){var new_input=document.createElement('input');new_input.name=name;new_input.value=value;new_input.type='hidden';return new_input;}
function has_css_class_name(elem,cname){return(elem&&cname)?new RegExp('\\b'+trim(cname)+'\\b').test(elem.className):false;}
function add_css_class_name(elem,cname){if(elem&&cname){if(elem.className){if(has_css_class_name(elem,cname)){return false;}else{elem.className+=' '+trim(cname);return true;}}else{elem.className=cname;return true;}}else{return false;}}
function remove_css_class_name(elem,cname){if(elem&&cname&&elem.className){cname=trim(cname);var old=elem.className;elem.className=elem.className.replace(new RegExp('\\b'+cname+'\\b'),'');return elem.className!=old;}else{return false;}}
function set_inner_html(obj,html){obj.innerHTML=html;var scripts=obj.getElementsByTagName('script');for(var i=0;i<scripts.length;i++){if(scripts[i].src){var script=document.createElement('script');script.type='text/javascript';script.src=scripts[i].src;document.body.appendChild(script);}else{try{eval(scripts[i].innerHTML);}catch(e){if(typeof console!='undefined'){console.error(e);}}}}}
var KEYS={BACKSPACE:8,TAB:9,RETURN:13,ESC:27,SPACE:32,LEFT:37,UP:38,RIGHT:39,DOWN:40,DELETE:46};function mouseX(event)
{return event.pageX||(event.clientX+
(document.documentElement.scrollLeft||document.body.scrollLeft));}
function mouseY(event)
{return event.pageY||(event.clientY+
(document.documentElement.scrollTop||document.body.scrollTop));}
function pageScrollX()
{return document.body.scrollLeft||document.documentElement.scrollLeft;}
function pageScrollY()
{return document.body.scrollTop||document.documentElement.scrollTop;}
function elementX(obj)
{var curleft=0;if(obj.offsetParent){while(obj.offsetParent){curleft+=obj.offsetLeft;obj=obj.offsetParent;}}
else if(obj.x)
curleft+=obj.x;return curleft;}
function elementY(obj)
{var curtop=0;if(obj.offsetParent){while(obj.offsetParent){curtop+=obj.offsetTop;obj=obj.offsetParent;}}
else if(obj.y)
curtop+=obj.y;return curtop;}
function onloadRegister(handler){if(window.onload){var old=window.onload;window.onload=function(){old();handler();};}
else{window.onload=handler;}}
function onbeforeunloadRegister(handler){if(window.onbeforeunload){var old=window.onbeforeunload;window.onbeforeunload=function(){var ret=old();if(ret){return ret;}
return handler();};}
else{window.onbeforeunload=handler;}}
function warn_if_unsaved(form_id){onloadRegister(function(){var form_state=[];var form=ge(form_id);var inputs=get_all_form_inputs(form);for(var i=0;i<inputs.length;++i){if(is_button(inputs[i])){inputs[i].onclick=bind(null,function(old,e){document.unsaved_warning_disabled=true;return old&&old(e);},inputs[i].onclick);}else if(is_descendent(inputs[i],form)){form_state.push({'element':inputs[i],'value':inputs[i].value});}}
(function(original_form_state){onbeforeunloadRegister(function(){if(!document.unsaved_warning_disabled){for(var i=0;i<original_form_state.length;++i){var input_element=original_form_state[i].element;var original_value=original_form_state[i].value;if(input_element.value!=original_value){return'You have unsaved changes.  Continue?'}}}});})(form_state);});}
function get_all_form_inputs(){var ret=[];var tag_names={'input':1,'select':1,'textarea':1,'button':1};for(var tag_name in tag_names){var elements=document.getElementsByTagName(tag_name);for(var i=0;i<elements.length;++i){ret.push(elements[i]);}}
return ret;}
function is_button(element){var tagName=element.tagName.toUpperCase();if(tagName=='BUTTON'){return true;}
if(tagName=='INPUT'&&element.type){var type=element.type.toUpperCase();return type=='BUTTON'||type=='SUBMIT';}
return false;}
function addEventBase(obj,type,fn,name_hash)
{if(obj.addEventListener)
obj.addEventListener(type,fn,false);else if(obj.attachEvent)
{obj["e"+type+fn+name_hash]=fn;obj[type+fn+name_hash]=function(){obj["e"+type+fn+name_hash](window.event);}
obj.attachEvent("on"+type,obj[type+fn+name_hash]);}}
function removeEventBase(obj,type,fn,name_hash)
{if(obj.removeEventListener)
obj.removeEventListener(type,fn,false);else if(obj.detachEvent)
{obj.detachEvent("on"+type,obj[type+fn+name_hash]);obj[type+fn+name_hash]=null;obj["e"+type+fn+name_hash]=null;}}
function placeholderSetup(id){var el=ge(id);if(!el)return;var ph=el.getAttribute("placeholder");if(ph&&ph!=""){el.value=ph;el.style.color='#777';el.is_focused=0;addEventBase(el,'focus',placeholderFocus);addEventBase(el,'blur',placeholderBlur);}}
function placeholderFocus(){if(!this.is_focused){this.is_focused=1;this.value='';this.style.color='#000';var rs=this.getAttribute("radioselect");if(rs&&rs!=""){var re=document.getElementById(rs);if(!re){return;}
if(re.type!='radio')return;re.checked=true;}}}
function placeholderBlur(){var ph=this.getAttribute("placeholder")
if(this.is_focused&&ph&&this.value==""){this.is_focused=0;this.value=ph;this.style.color='#777';}}
function optional_drop_down_menu(arrow,link,menu,event,arrow_class,arrow_old_class)
{if(menu.style.display=='none'){menu.style.display='block';var old_arrow_classname=arrow_old_class?arrow_old_class:arrow.className;if(link){link.className='active';}
arrow.className=arrow_class?arrow_class:'global_menu_arrow_active';var justChanged=true;var shim=ge(menu.id+'_iframe');if(shim){shim.style.top=menu.style.top;shim.style.right=menu.style.right;shim.style.display='block';shim.style.width=(menu.offsetWidth+2)+'px';shim.style.height=(menu.offsetHeight+2)+'px';}
menu.offclick=function(e){if(!justChanged){hide(this);if(link){link.className='';}
arrow.className=old_arrow_classname;var shim=ge(menu.id+'_iframe');if(shim){shim.style.display='none';shim.style.width=menu.offsetWidth+'px';shim.style.height=menu.offsetHeight+'px';}
removeEventBase(document,'click',this.offclick,menu.id);}else{justChanged=false;}}.bind(menu);addEventBase(document,'click',menu.offclick,menu.id);}
return false;}
function position_app_switcher(){var switcher=ge('app_switcher');var menu=ge('app_switcher_menu');menu.style.top=(switcher.offsetHeight-1)+'px';menu.style.right='0px';}
function escapeURI(u)
{if(encodeURIComponent){return encodeURIComponent(u);}
if(escape){return escape(u);}}
function goURI(href){window.location.href=href;}
function is_email(email){return/^[\w!.%+]+@[\w]+(?:\.[\w]+)+$/.test(email);}
function getViewportWidth(){var width=0;if(document.documentElement&&document.documentElement.clientWidth){width=document.documentElement.clientWidth;}
else if(document.body&&document.body.clientWidth){width=document.body.clientWidth;}
else if(window.innerWidth){width=window.innerWidth-18;}
return width;};function getViewportHeight(){var height=0;if(window.innerHeight){height=window.innerHeight-18;}
else if(document.documentElement&&document.documentElement.clientHeight){height=document.documentElement.clientHeight;}
else if(document.body&&document.body.clientHeight){height=document.body.clientHeight;}
return height;};function getPageScrollHeight(){var height;if(typeof(window.pageYOffset)=='number'){height=window.pageYOffset;}else if(document.body&&document.body.scrollTop){height=document.body.scrollTop;}else if(document.documentElement&&document.documentElement.scrollTop){height=document.documentElement.scrollTop;}
return height;};function getRadioFormValue(obj){for(i=0;i<obj.length;i++){if(obj[i].checked){return obj[i].value;}}
return null;}
function getTableRowShownDisplayProperty(){if(ua.ie()){return'inline';}else{return'table-row';}}
function showTableRow()
{for(var i=0;i<arguments.length;i++){var element=ge(arguments[i]);if(element&&element.style)element.style.display=getTableRowShownDisplayProperty();}
return false;}
function getParentRow(el){el=ge(el);while(el.tagName&&el.tagName!="TR"){el=el.parentNode;}
return el;}
function stopPropagation(e){if(!e)var e=window.event;e.cancelBubble=true;if(e.stopPropagation){e.stopPropagation();}}
function show_standard_status(status){s=ge('standard_status');if(s){var header=s.firstChild;header.innerHTML=status;show('standard_status');}}
function hide_standard_status(){s=ge('standard_status');if(s){hide('standard_status');}}
function remove_node(node){if(node.removeNode)
node.removeNode(true);else{for(var i=node.childNodes.length-1;i>=0;i--)
remove_node(node.childNodes[i]);node.parentNode.removeChild(node);}
return null;}
function adjustImage(obj,stop_word,max){var pn=obj.parentNode;if(stop_word==null)
stop_word='note_content';if(max==null){while(pn.className.indexOf(stop_word)==-1)
pn=pn.parentNode;if(pn.offsetWidth)
max=pn.offsetWidth;else
max=400;}
if(navigator.userAgent.indexOf('AppleWebKit/4')==-1){obj.style.position='absolute';obj.style.left=obj.style.top='-32000px';}
obj.className=obj.className.replace('img_loading','img_ready');if(obj.width>max){if(window.ActiveXObject){try{var img_div=document.createElement('div');img_div.style.filter='progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'+obj.src.replace('"','%22')+'", sizingMethod="scale")';img_div.style.width=max+'px';img_div.style.height=((max/obj.width)*obj.height)+'px';if(obj.parentNode.tagName=='A')
img_div.style.cursor='pointer';obj.parentNode.insertBefore(img_div,obj);obj.removeNode(true);}
catch(e){obj.style.width=max+'px';}}
else
obj.style.width=max+'px';}
obj.style.left=obj.style.top=obj.style.position='';}
function imageConstrainSize(src,maxX,maxY,placeholderid){var image=new Image();image.onload=function(){if(image.width>0&&image.height>0){var width=image.width;var height=image.height;if(width>maxX||height>maxY){var desired_ratio=maxY/maxX;var actual_ratio=height/width;if(actual_ratio>desired_ratio){width=width*(maxY/height);height=maxY;}else{height=height*(maxX/width);width=maxX;}}
var placeholder=ge(placeholderid);var newimage=document.createElement('img');newimage.src=src;newimage.width=width;newimage.height=height;placeholder.parentNode.insertBefore(newimage,placeholder);placeholder.parentNode.removeChild(placeholder);}}
image.src=src;}
function set_opacity(obj,opacity){try{obj.style.opacity=(opacity==1?'':opacity);obj.style.filter=(opacity==1?'':'alpha(opacity='+opacity*100+')');}
catch(e){}
obj.setAttribute('opacity',opacity);}
function get_opacity(obj){return obj.opacity?obj.opacity:1;}
function focus_login(){var email=ge("email");var pass=ge("pass");var dologin=ge("doquicklogin");if(email&&pass){if(email.value!=""&&pass.value==""){pass.focus();}else if(email.value==""){email.focus();}else if(email.value!=""&&pass.value!=""){dologin.focus();}}}
function array_indexOf(arr,val,index){if(!index){index=0;}
for(var i=index;i<arr.length;i++){if(arr[i]==val){return i;}}
return-1;}
var ua={populate:function(){var agent=/(?:MSIE.(\d+\.\d+))|(?:Firefox.(\d+\.\d+))|(?:Opera.(\d+\.\d+))|(?:AppleWebKit.(\d+.\d+))/.exec(navigator.userAgent);if(!agent){this._ie=this._firefox=this._opera=this._safari=0;}
this._ie=parseFloat(agent[1]?agent[1]:0);this._firefox=parseFloat(agent[2]?agent[2]:0);this._opera=parseFloat(agent[3]?agent[3]:0);this._safari=parseFloat(agent[4]?agent[4]:0);this.populated=true;},populated:false,ie:function(){if(!this.populated)this.populate();return this._ie;},firefox:function(){if(!this.populated)this.populate();return this._firefox;},opera:function(){if(!this.populated)this.populate();return this._opera;},safari:function(){if(!this.populated)this.populate();return this._safari;},matches:function(str){return(navigator.userAgent.indexOf(str)!=-1);}}
function subclass(sub,parent){sub.prototype=sub.prototype||{};sub.prototype.prototype=parent.prototype;sub.prototype.parent=parent;return sub;}
Function.prototype.extend=function(superclass){var superprototype=__metaprototype(superclass,0);var subprototype=__metaprototype(this,superprototype.prototype.__level+1);subprototype.parent=superprototype;}
function __metaprototype(obj,level){if(obj.__metaprototype){return obj.__metaprototype;}
var metaprototype=new Function();metaprototype.construct=__metaprototype_construct;metaprototype.prototype.construct=__metaprototype_wrap(obj,level);metaprototype.prototype.__level=level;metaprototype.base=obj;obj.prototype.parent=metaprototype;obj.__metaprototype=metaprototype;return metaprototype;}
function __metaprototype_construct(instance){__metaprototype_init(instance.parent);var parents=[];var obj=instance;while(obj.parent){parents.push(new_obj=new obj.parent());new_obj.__instance=instance;obj=obj.parent;}
instance.parent=parents[1];parents.reverse();parents.pop();instance.__parents=parents;instance.__instance=instance;var args=[];for(var i=1;i<arguments.length;i++){args.push(arguments[i]);}
return instance.parent.construct.apply(instance.parent,args);}
function __metaprototype_init(metaprototype){if(metaprototype.initialized)return;var base=metaprototype.base.prototype;if(metaprototype.parent){__metaprototype_init(metaprototype.parent);var parent_prototype=metaprototype.parent.prototype;for(i in parent_prototype){if(i!='__level'&&i!='construct'&&base[i]===undefined){base[i]=metaprototype.prototype[i]=parent_prototype[i]}}}
metaprototype.initialized=true;var level=metaprototype.prototype.__level;for(i in base){if(i!='parent'){base[i]=metaprototype.prototype[i]=__metaprototype_wrap(base[i],level);}}}
function __metaprototype_wrap(method,level){if(typeof method!='function'||method.__prototyped){return method;}
var func=function(){var instance=this.__instance;if(instance){var old_parent=instance.parent;instance.parent=level?instance.__parents[level-1]:null;var ret=method.apply(instance,arguments);instance.parent=old_parent;return ret;}else{return method.apply(this,arguments);}}
func.__prototyped=true;return func;}
function dp(object)
{var descString="";for(var value in object){try{descString+=(value+" => "+object[value]+"\n");}catch(exception){descString+=(value+" => "+exception+"\n");}}
if(descString!="")
alert(descString);else
alert(object);}
function adClick(id)
{ajax=new Ajax();ajax.get('ajax/redirect.php',{'id':id},true);return true;}
function abTest(data,inline)
{ajax=new Ajax();ajax.get('/ajax/abtest.php',{'data':data},true);if(!inline){return true;}}
function ajaxArrayToQueryString(query,name){if(typeof query=='object'){var params=[];for(var i in query){params.push(ajaxArrayToQueryString(query[i],name?name+'['+i+']':i));}
return params.join('&');}else{return name?encodeURIComponent(name)+'='+(query!=null?encodeURIComponent(query):''):query;}}
function setCookie(cookieName,cookieValue,nDays){var today=new Date();var expire=new Date();if(nDays==null||nDays==0)nDays=1;expire.setTime(today.getTime()+3600000*24*nDays);document.cookie=cookieName+"="+escape(cookieValue)+"; expires="+expire.toGMTString()+"; path=/; domain=.facebook.com";}
function clearCookie(cookieName){document.cookie=cookieName+"=; expires=Mon, 26 Jul 1997 05:00:00 GMT; path=/; domain=.facebook.com";}
function getCookie(name){var nameEQ=name+"=";var ca=document.cookie.split(';');for(i=0;i<ca.length;i++){var c=ca[i];while(c.charAt(0)==' ')c=c.substring(1,c.length);if(c.indexOf(nameEQ)==0){return unescape(c.substring(nameEQ.length,c.length));}}
return null;}
function do_post(url){var pieces=/(^([^?])+)\??(.*)$/.exec(url);var form=document.createElement('form');form.action=pieces[1];form.method='post';form.style.display='none';var sparam=/([\w]+)(?:=([^&]+)|&|$)/g;var param=null;if(ge('post_form_id'))
pieces[3]+='&post_form_id='+ge('post_form_id').value;while(param=sparam.exec(pieces[3])){var input=document.createElement('input');input.type='hidden';input.name=param[1];input.value=param[2];form.appendChild(input);}
document.body.appendChild(form);form.submit();return false;}
function anchor_set(anchor){window.location=window.location.href.split('#')[0]+'#'+anchor;}
function anchor_get(){return window.location.href.split('#')[1]||null;}
function get_event(e){return e||window.event;}