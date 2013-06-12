
Util._deprecatedBecause={extend:'extend() has been renamed copy_properties() to avoid confusion with '+'Function.extend(). Use Function.extend() or subclass() to establish class'+'inheritence, and copy_properties() to copy properties between objects.',ajaxrequest:'AjaxRequest has been renamed AsyncRequest. The interface has not '+'changed.',ajaxresponse:'AjaxResponse has been renamed AsyncResponse. The interface has not '+'changed.',ajax:'The `Ajax\' class has been deprecated for sucking. Use AsyncRequest '+'and AsyncResponse to make remote HTTP requests. Prefer JSON to XML as '+'a transport encoding, but never say "AJAJ". AND WRITE ERROR HANDLERS! ',ajaxloadindicator:'No ajaxLoadIndicator element is ever generated, so this code is '+'apparently never used.',mediaheaderpictureload:'This function is deprecated because it solves a problem that should be '+'solved (and can more easily be solved) with CSS.',toggleinlineflyer:'This function is not used anywhere.',checkagree:'This function is marked as deprecated and not used anywhere.',dynamicdialog:'Dynamicdialog is deprecated in favor of dialogpro.'}
function extend(u, v) 
{
	Util.deprecated('extend');
	return copy_properties(u, v);
}
function checkAgree() 
{
	Util.deprecated('checkagree');
	if(document.frm.pic.value) {
	  if(document.frm.agree.checked) {
	     document.frm.submit();
	     }
	  else {
	     show("error");
	     }
	  }
}
function mediaHeaderPictureLoad(image, size) {
Util.deprecated('mediaheaderpictureload');
var w = image.offsetWidth;
var h = image.offsetHeight;
var ratio = size / h;
image.style.height = size + 'px';
image.style.width = (ratio * w) + 'px';
image.style.visibility = 'visible';
}
function toggleInlineFlyer(toggler) {
Util.deprecated('toggleinlineflyer');
if(toggler.innerHTML == 'hide flyer') {
  toggler.innerHTML = 'show flyer';
  }
else {
  toggler.innerHTML = 'hide flyer';
  }
toggle('inline_flyer_content');
}
var ajaxLoadIndicatorRefCount = 0;
function ajaxShowLoadIndicator() {
Util.deprecated('ajaxloadindicator');
indicatorDiv = ge('ajaxLoadIndicator');
if(!indicatorDiv) {
  indicatorDiv = document.createElement("div");
  indicatorDiv.id = 'ajaxLoadIndicator';
  indicatorDiv.innerHTML = 'Loading';
  indicatorDiv.className = 'ajaxLoadIndicator';
  document.body.appendChild(indicatorDiv);
  }
indicatorDiv.style.top = (5 + pageScrollY()) + 'px';
indicatorDiv.style.left = (5 + pageScrollX()) + 'px';
indicatorDiv.style.display = 'block';
ajaxLoadIndicatorRefCount++;
}
function ajaxHideLoadIndicator() {
ajaxLoadIndicatorRefCount--;
if(ajaxLoadIndicatorRefCount == 0)ge('ajaxLoadIndicator').style.display = '';
}

function Ajax(doneHandler,failHandler)
{Util.deprecated('ajax');newAjax=this;this.onDone=doneHandler;this.onFail=failHandler;this.transport=this.getTransport();this.transport.onreadystatechange=ajaxTrampoline(this);}

Ajax.prototype.get=function(uri,query,force_sync)
{force_sync=force_sync||false;if(typeof query!='string')
query=ajaxArrayToQueryString(query);fullURI=uri+(query?('?'+query):'');this.transport.open('GET',fullURI,!force_sync);this.transport.send('');}

Ajax.prototype.post=function(uri,data,force_sync,no_post_form_id)
{force_sync=force_sync||false;no_post_form_id=no_post_form_id||false;if(typeof data!='string'){data=ajaxArrayToQueryString(data);}
if(!no_post_form_id){var post_form_id=ge('post_form_id');if(post_form_id){data+='&post_form_id='+post_form_id.value;}}
this.transport.open('POST',uri,!force_sync);this.transport.setRequestHeader("Content-Type","application/x-www-form-urlencoded");this.transport.send(data);}

Ajax.prototype.stateDispatch=function()
{try{if(this.transport.readyState==1&&this.showLoad)
ajaxShowLoadIndicator();if(this.transport.readyState==4){if(this.showLoad)
ajaxHideLoadIndicator();if(this.transport.status>=200&&this.transport.status<300&&this.transport.responseText.length>0){try{if(this.onDone)this.onDone(this,this.transport.responseText);}catch(tempError){console?console.error(tempError):false;}}else{try{if(this.onFail)this.onFail(this);}catch(tempError){console?console.error(tempError):false;}}}}catch(error){if(this.onFail)this.onFail(this);}}

Ajax.prototype.getTransport=function()
{var ajax=null;try{ajax=new XMLHttpRequest();}
catch(e){ajax=null;}
try{if(!ajax)ajax=new ActiveXObject("Msxml2.XMLHTTP");}
catch(e){ajax=null;}
try{if(!ajax)ajax=new ActiveXObject("Microsoft.XMLHTTP");}
catch(e){ajax=null;}
return ajax;}
function ajaxTrampoline(ajaxObject)
{return function(){ajaxObject.stateDispatch();};}
function AjaxRequest(){Util.deprecated('AjaxRequest');return arguments.callee.parent.apply(this,arguments);};subclass(AjaxRequest,AsyncRequest);function AjaxResponse(){Util.deprecated('AjaxResponse');return arguments.callee.parent.apply(this,arguments);};subclass(AjaxRequest,AsyncRequest);function toggle_dynamic_dialog_custom(rootEl,innerHTML){Util.deprecated('dialogpro');var ieHTML;ieHTML='<div id="ie_iframe_holder"></div>';ieHTML+='<div style="position: absolute; z-index: 100;">';innerHTML=ieHTML+innerHTML+'</div>';var dynamic_dialog=ge('dynamic_dialog');if(dynamic_dialog){if(shown(dynamic_dialog)&&same_place(rootEl,dynamic_dialog)){hide(dynamic_dialog);}else{move_here(rootEl,dynamic_dialog);dynamic_dialog.innerHTML=innerHTML;show('dynamic_dialog');}}else{var dynamic_dialog=document.createElement("div");dynamic_dialog.id='dynamic_dialog';dynamic_dialog.innerHTML=innerHTML;move_here(rootEl,dynamic_dialog);ge('content').appendChild(dynamic_dialog);}
var height,width,ieIframeHTML;height=ge('dialog').offsetHeight;width=ge('dialog').offsetWidth;ieIframeHTML='<iframe width="'+width+' "height='+height+'" ';ieIframeHTML+='style="position: absolute; z-index: 99; border: none;"></iframe>';ge('ie_iframe_holder').innerHTML=ieIframeHTML;return false;}
function same_place(rootEl,dynamic_dialog){Util.deprecated('dialogpro');if(rootEl=ge(rootEl)){if(elementY(rootEl)+20==elementY(dynamic_dialog))
return true;}
return false;}
function move_here(rootEl,el){Util.deprecated('dialogpro');var x=getViewportWidth()/2-120;var y=elementY(rootEl)+20;el.style.left=x+"px";el.style.top=y+"px";}
function toggle_dynamic_dialog_post(rootEl,headingText,contentText,confirmText,confirmLocation,confirmParams){Util.deprecated('dialogpro');var form_check_string=(ge('post_form_id')?('<input type="hidden" name="post_form_id" value="'+ge('post_form_id').value+'"/>'):'');var formParams='';for(var param in confirmParams){formParams+='<input type="hidden" name="'+param+'" value="'+
confirmParams[param]+'"/>'}
var innerHTML='<table id="dialog" border="0" cellspacing="0" width="360">'+'<tr>'+'<td class="dialog">'+'<h4>'+headingText+'</h4>'+'<p>'+contentText+'</p>'+'<div class="buttons">'+'<form action="'+confirmLocation+'" method="post">'+
form_check_string+
formParams+'<input type="hidden" name="next" value="'+window.location+'"/>'+'<input type="submit" id="confirm" name="confirm" class="inputsubmit" '+'value="'+confirmText+'"/>&nbsp;<input type="button" id="cancel" '+'name="cancel" onclick="hide(\'dynamic_dialog\');" class="inputbutton" '+'value="Cancel" />'+'</form>'+'</div>'+'</td>'+'</tr>'+'</table>';return toggle_dynamic_dialog_custom(rootEl,innerHTML);}
function toggle_dynamic_dialog(rootEl,headingText,contentText,confirmText,confirmLocation){Util.deprecated('dialogpro');var form_check_string=(ge('post_form_id')?('<input type="hidden" name="post_form_id" value="'+ge('post_form_id').value+'"/>'):'');var innerHTML="<form action=\""+confirmLocation+"\" method=\"post\">\n"+"<table id=\"dialog\" border=\"0\" cellspacing=\"0\" width=\"360\">"+"<tr>\n"+"<td class=\"dialog\">\n"+"<h4>"+headingText+"</h4>\n"+"<p>"+contentText+"</p>"+"<div class=\"buttons\">\n"+
form_check_string+"<input type=\"hidden\" name=\"next\" value=\""+window.location+"\"/>\n"+"<input type=\"submit\" id=\"confirm\" name=\"confirm\" class=\"inputsubmit\" value=\""+confirmText+"\"/>&nbsp;<input type=\"button\" id=\"cancel\" name=\"cancel\" onclick=\"hide('dynamic_dialog');\" class=\"inputbutton\" value=\"Cancel\" />\n"+"</div>\n"+"</td>\n"+"</tr>\n"+"</table>\n"+"</form>\n";return toggle_dynamic_dialog_custom(rootEl,innerHTML);}
function toggle_dynamic_dialog_js(rootEl,headingText,contentText,confirmText,confirmJS,remove_cancel_option){Util.deprecated('dialogpro');var innerHTML="<table id=\"dialog\" border=\"0\" cellspacing=\"0\" width=\"360\">"+"<tr>\n"+"<td class=\"dialog\">\n"+"<h4>"+headingText+"</h4>\n"+"<p>"+contentText+"</p>"+"<div class=\"buttons\">\n"+"<input type=\"button\" id=\"confirm\" name=\"confirm\" class=\"inputsubmit\"  value=\""+confirmText+"\" onclick=\""+confirmJS+"\"/>&nbsp;";if(!remove_cancel_option){innerHTML+="<input type=\"button\" id=\"cancel\" name=\"cancel\" onclick=\"hide('dynamic_dialog');\" class=\"inputbutton\" value=\"Cancel\" />\n";}
innerHTML+="</div>\n"+"</td>\n"+"</tr>\n"+"</table>\n";return toggle_dynamic_dialog_custom(rootEl,innerHTML);}