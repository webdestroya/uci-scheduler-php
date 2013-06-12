function AJAX_AC(elem,divname,script){
	var me = this;
	this.elem = document.getElementById(elem);
	this.highlighted = -1;
	this.arrItens = new Array();
	this.ajaxTarget = '/ajax/'+script+'.php';
	this.chooseFunc = null;
	this.div = document.getElementById(divname);
	var TAB = 9;
	var ESC = 27;
	var KEYUP = 38;
	var KEYDN = 40;
	var ENTER = 13;
	var DELETE = 46;
	var BACKSPACE = 8;
	
	/*this.div.style.width = this.elem.style.width;*/
	me.elem.setAttribute("autocomplete","off");
	
	me.elem.onkeydown = function(ev){
		var key = me.getKeyCode(ev);
		switch(key){
			/*case TAB:*/
			case ENTER:
				if (me.highlighted.id != undefined){
					me.acChoose(me.highlighted.id);
				}
				me.hideDiv();
				return false;
				break;
			case ESC:
				me.hideDiv();
				return false;
				break;
			case KEYUP:
				me.changeHighlight('up');
				return false;
				break;
			case KEYDN:
				me.changeHighlight('down');
				return false;
				break;
		}
	};
	
	this.setElemValue = function()
	{
		var a = me.highlighted.firstChild;
		me.elem.value = me.highlighted.id;
	};
	
	this.highlightThis = function(obj,yn){
		if (yn = 'y'){
			me.highlighted.className = '';
			me.highlighted = obj;
			me.highlighted.className = 'selected';
			me.setElemValue(obj);
		}else{
			obj.className = '';
			me.highlighted = '';
		}
	};
	
	this.changeHighlight = function(way){
		if (me.highlighted != '' && me.highlighted != null ){
			me.highlighted.className = '';
			switch(way){
				case 'up':
					if(me.highlighted.parentNode.firstChild == me.highlighted){
						me.highlighted = me.highlighted.parentNode.lastChild;
					}else{
						me.highlighted = me.highlighted.previousSibling;
					}
				break;
				case 'down':
					if(me.highlighted.parentNode.lastChild == me.highlighted){
						me.highlighted = me.highlighted.parentNode.firstChild;
					}else{
						me.highlighted = me.highlighted.nextSibling;
					}
				break;
			}
			me.highlighted.className = 'selected';
			me.setElemValue();
		}else{
			switch(way){
				case 'up':
					me.highlighted = me.div.firstChild.lastChild;
				break;
				case 'down':
					me.highlighted = me.div.firstChild.firstChild;
				break;
			}
			me.highlighted.className = 'selected';
			me.setElemValue();
		}
	};
	
	me.elem.onkeyup = function(ev) {
		var key = me.getKeyCode(ev);
		switch(key){
			case TAB:
			case ESC:
			case KEYUP:
			case KEYDN:
				return;
			case ENTER:
				return false;
				break;
			default:
				me.ajaxReq = createRequest();
				if (me.ajaxReq != undefined){
					me.ajaxReq.open("POST", me.ajaxTarget, true);
					me.ajaxReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
					me.ajaxReq.onreadystatechange = me.acResult;
					me.ajaxReq.send('q=' + me.elem.value);
				}
				me.highlighted = '';
		}
	};
	
	me.elem.onblur = function() {
		me.hideDiv();
	};

	this.acResult = function(){
		if (me.ajaxReq.readyState == 4){
			me.showDiv();
			var xmlRes = me.ajaxReq.responseXML;
			var itens = xmlRes.getElementsByTagName('item');
			var itCnt = itens.length;
			me.div.innerHTML = '';
			var ul = document.createElement('ul');
			me.div.appendChild(ul);
			var li = null;
			
			if (itCnt > 0){
				for (i=0; i<itCnt; i++){
					me.arrItens[itens[i].getAttribute("id")] = new Array(); 
					me.arrItens[itens[i].getAttribute("id")]['label'] = itens[i].getAttribute("label");
					
					li = document.createElement('li');
					li.id = itens[i].getAttribute("id");
					li.onmouseover = function(){this.className='selected';me.highlightThis(this,'y');};
					li.onmouseout  = function(){this.className='';me.highlightThis(this,'n');};
					li.onmousedown = function(){me.acChoose(this.id);me.hideDiv();return false;};
					
					var a = document.createElement('a');
					a.href = '#';
					a.onclick = function() { return false; };
					a.innerHTML = unescape(itens[i].getAttribute("label"));

					li.appendChild(a);
					ul.appendChild(li);	
				}
			}else{
				me.hideDiv();	
			}
		}
	};
	
	this.acChoose = function (id){
		if (id != ''){
			if (me.chooseFunc != null){
				me.chooseFunc(id,unescape(me.arrItens[id]['label']));
			}
		}
		me.hideDiv();
		me.elem.value = id;
	};

	this.positionDiv = function(){
		var el = this.elem;
		var x = 0;
		var y = el.offsetHeight;
		while (el.offsetParent && el.tagName.toUpperCase() != 'BODY'){
			x += el.offsetLeft;
			y += el.offsetTop;
			el = el.offsetParent;
		}

		x += el.offsetLeft;
		y += el.offsetTop;

		this.div.style.left = x + 'px';
		this.div.style.top = y + 'px';
	};

	this.hideDiv = function(){
		me.highlighted = '';
		me.div.style.display = 'none';
	};

	this.showDiv = function(){
		me.highlighted = '';
		me.positionDiv();
		me.div.style.display = 'block';
	};
	
	this.getKeyCode = function(ev){
		if(ev){
			return ev.keyCode;
		}
		if(window.event){
			return window.event.keyCode;
		}
	};

	this.getEventSource = function(ev){
		if(ev){
			return ev.target;
		}
		if(window.event){
			return window.event.srcElement;
		}
	};

	this.cancelEvent = function(ev){
		if(ev){
			ev.preventDefault();
			ev.stopPropagation();
		}
		if(window.event){
			window.event.returnValue = false;
		}
	};
}

function createRequest() {
	request = new XMLHttpRequest();
	if (!request){alert("Error initializing XMLHttpRequest!");}
	else{return request;}
};