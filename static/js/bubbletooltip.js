/*javascript for Bubble Tooltips by Alessandro Fulciniti
- http://pro.html.it - http://web-graphics.com */

function enableTooltips(id)
{
	var links,i,h;
	if(!document.getElementById || !document.getElementsByTagName)
	{
		return;
	}
	h=document.createElement("span");
	h.id="btc";
	h.setAttribute("id","btc");
	h.style.position="absolute";
	document.getElementsByTagName("body")[0].appendChild(h);
	if(id==null)
	{
		links=document.getElementsByTagName("a");
	}
	else if(document.getElementById(id))
	{
		links=document.getElementById(id).getElementsByTagName("a");
	}
	if(links)
	{
		for(i=0;i<links.length;i++)
		{
    		Prepare(links[i]);
		}
	}
}

function Prepare(el)
{
	var tooltip,t,b,s,l;
	tooltip=CreateEl("div","tooltip");

	t=el.getAttribute("title");
	d=CreateEl("div","desc");
	d.appendChild(document.createTextNode(t));
	t=el.getAttribute("ebuild");
	e=CreateEl("div","ebuild");
	e.appendChild(document.createTextNode(t));
	t=el.getAttribute("keywords");
	k=CreateEl("div","keywords");
	k.appendChild(document.createTextNode(t));

	el.removeAttribute("title");
	tooltip.appendChild(e);
	tooltip.appendChild(d);
	tooltip.appendChild(k);
	
	t=el.getAttribute("image");
	if(t==null || t.length==0)
	{

	}
	else
	{
		i1=CreateEl("div","latest_image");
		i2=document.createElement('img');
		i2.setAttribute('t_src', t);
	
		i1.appendChild(i2);
		tooltip.appendChild(i1);
		el.image_div = i2;
	}

	setOpacity(tooltip);
	el.tooltip=tooltip;
	el.onmouseover=showTooltip;
	el.onmouseout=hideTooltip;
	el.onmousemove=Locate;
}

function showTooltip(e)
{
	document.getElementById("btc").appendChild(this.tooltip);
	if(this.image_div)
	{
		this.image_div.setAttribute('src', this.image_div.getAttribute('t_src'));
	}
	Locate(e);
}

function hideTooltip(e)
{
	var d=document.getElementById("btc");
	if(d.childNodes.length>0)
	{
		d.removeChild(d.firstChild);
	}
}

function setOpacity(el)
{
	el.style.filter="alpha(opacity:90)";
	el.style.KHTMLOpacity="0.90";
	el.style.MozOpacity="0.90";
	el.style.opacity="0.90";
}

function CreateEl(t,c)
{
	var x=document.createElement(t);
	x.className=c;
	x.style.display="block";
	return(x);
}

function Locate(e)
{
	var posx=0,posy=0;
	if(e==null)
	{
		e=window.event;
	}
	if(e.pageX || e.pageY)
	{
    	posx=e.pageX; posy=e.pageY;
    }
	else if(e.clientX || e.clientY)
	{
    	if(document.documentElement.scrollTop)
		{
        	posx=e.clientX+document.documentElement.scrollLeft;
        	posy=e.clientY+document.documentElement.scrollTop;
        }
    	else
		{
        	posx=e.clientX+document.body.scrollLeft;
        	posy=e.clientY+document.body.scrollTop;
        }
    }
	document.getElementById("btc").style.top=(posy+10)+"px";
	document.getElementById("btc").style.left=(posx-90)+"px";
}
