/*
    WebSnapr - Preview Bubble Javascript
    Written by Juan Xavier Larrea 
    http://www.websnapr.com - xavier@websnapr.com   
    
*/


// Point this variable to the correct location of the bg.png file
var imgbubbleImagePath = '/images/bubblebg.png';
var imgBubbleImgs = Array();


// DO NOT EDIT BENEATH THIS

function bindImgBubbles(e){
lbActions=WSR_getElementsByClassName(document,"a","imglink");
for(i=0;i<lbActions.length;i++){
if(window.addEventListener){
lbActions[i].addEventListener("mouseover",attachImgBubble,false);
lbActions[i].addEventListener("mouseout",detachImgBubble,false);
}else{
lbActions[i].attachEvent("onmouseover",attachImgBubble);
lbActions[i].attachEvent("onmouseout",detachImgBubble);
}
}
}
function attachImgBubble(_b){
var _c;

if(_b["srcElement"]){
	_c=_b["srcElement"];
}else{
	_c=_b["target"];
}
if (_c.href == undefined){
	_c=_c.parentNode;
}

var _d=_c.href;
var _im=_c.plink;
var _e=findPos(_c)[0]+5;
var _f=findPos(_c)[1]+17;
var _10=document.createElement("div");
document.getElementsByTagName("body")[0].appendChild(_10);
_10.className="imgbubble";
if (BrowserDetect.browser == 'Explorer') {
_10.style.width="240px";
_10.style.position="absolute";
_10.style.top=_f;
_10.style.zIndex=99999;
_10.style.left=_e;
_10.style.textAlign="left";
_10.style.height="190px";
_10.style.paddingTop="0";
_10.style.paddingLeft="0";
_10.style.paddingBottom="0";
_10.style.paddingRight="0";
_10.style.marginTop="0";
_10.style.marginLeft="0";
_10.style.marginBottom="0";
_10.style.marginRight="0";
_10.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + imgbubbleImagePath + "',sizingMethod='image')";
} else {
_10.setAttribute("style","text-align: center; z-index: 99999; position: absolute; top: "+_f+"px ; left: "+_e+"px ; background: url("+ imgbubbleImagePath +") no-repeat; width: 240px; height: 190px; padding: 0; margin: 0;");
}
if (BrowserDetect.browser == 'Safari' || BrowserDetect.browser == 'Konqueror' ) {

var _height = _f;
    
_10.setAttribute("style","text-align: center; z-index: 99999; position: absolute; top: "+ _height +"px ; left: "+_e+"px ; background: url("+ imgbubbleImagePath +") no-repeat; width: 240px; height: 190px; padding: 0; margin: 0;");
    
}
var img=document.createElement("img");
_10.appendChild(img);

if (BrowserDetect.browser == 'Explorer') {
img.style.paddingTop="0";
img.style.paddingLeft="0";
img.style.paddingBottom="0";
img.style.paddingRight="0";
img.style.margin="auto";
img.style.marginTop="27px";
img.style.marginLeft="25px";
img.style.marginBottom="0";
img.style.marginRight="0";
img.style.borderTop="0";
img.style.borderLeft="0";
img.style.borderBottom="0";
img.style.borderRight="0";
} else {
img.setAttribute("style","padding-top: 0; padding-left: 0; padding-right: 0; padding-bottom: 0; margin-top: 27px; margin-left: 12px; margin-bottom: 0; margin-right: 0; border: 0");
}
img.id=(_c.id)+"img";
img.setAttribute("src",imgBubbleImgs[_c.id].src);
img.setAttribute("width",imgBubbleImgs[_c.id].width);
img.setAttribute("height",imgBubbleImgs[_c.id].height);
img.setAttribute("alt","Preview");



}
function detachImgBubble(_12){
lbActions=WSR_getElementsByClassName(document,"div","imgbubble");
for(i=0;i<lbActions.length;i++){
lbActions[i].parentNode.removeChild(lbActions[i]);
}
}
if(window.addEventListener){
addEventListener("load",bindImgBubbles,false);
}else{
attachEvent("onload",bindImgBubbles);
}