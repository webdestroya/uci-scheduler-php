{include:"/header.tpl"}


<script language="javascript" type="text/javascript">
<!--

var usrTypes = new Array("{$typesa}");
var usrDays = new Array("{$daysa}");
var usrST = "{$sta}";
var usrET = "{$eta}";

function changeTerm()
{
	var data = '';
	data += '<input type="hidden" name="searchid" value="{$searchid}">';
	data += 'Please select another term:';
	data += '<select name="term">';
	<!--BEGIN LOOP data="terms"-->
	data += '<option value="{$term}">{$name}</option>';
	<!--END LOOP data="terms"-->
	data += '</select>';

	(new pop_dialog()).show_form("Change Term",'<p>'+data+'</p>',"Change Term|Cancel", '/ajax/changeterm');
}

function changeTypes()
{
	var realTypes = $w('OPEN Waitl FULL NewOnly');
	var typeClass = $w('green red black blue');
	var ut = $A(usrTypes);
	var data = '';
	data += '<input type="hidden" name="searchid" value="{$searchid}">';
	data += 'Please select the status you wish to allow:';
	data += '<table cellspacing="0" cellpadding="0" border="0">';
	for(i=0;i<realTypes.length;i++)
	{
		data += '<tr><td><input type="checkbox" value="'+realTypes[i]+'" id="etype'+i+'" name="types[]"';
		if( ut.indexOf( realTypes[i] ) != -1 )
		{
			data += ' checked';
		}
		data += '></td><td><label for="etype'+i+'" class="'+typeClass[i]+' bold">'+realTypes[i]+'</label></td></tr>';
	}
	data += '</table>';
	(new pop_dialog()).show_form("Change Status Settings",'<p>'+data+'</p>',"Change Status Settings|Cancel", '/ajax/changestatus');
}
function changeDays()
{
	var realDays = $w('M Tu W Th F Sa Su');
	var realDaysN =$w('Monday Tuesday Wednesday Thursday Friday Saturday Sunday');
	var ud = $A(usrDays);
	var data = '';
	data += '<input type="hidden" name="searchid" value="{$searchid}">';
	data += 'Please select the days you wish to allow:';
	data += '<table cellspacing="0" cellpadding="0" border="0">';
	for(i=0;i<realDays.length;i++)
	{
		data += '<tr><td><input type="checkbox" id="eday'+i+'" value="'+realDays[i]+'" name="days[]"';
		if( ud.indexOf( realDays[i] ) != -1 )
		{
			data += ' checked';
		}
		data += '></td><td><label for="eday'+i+'" class="bold">'+realDaysN[i]+'</label></td></tr>';
	}
	data += '</table>';
	(new pop_dialog()).show_form("Change Days",'<p>'+data+'</p>',"Change Days|Cancel", '/ajax/changedays');
}
function changeStartTime()
{
	var data = '';
	data += '<input type="hidden" name="searchid" value="{$searchid}">';
	data += 'Select Start Time: <select name="starttime">'+makeTimeOpts(usrST)+'</select>';
	(new pop_dialog()).show_form("Change Start Time",'<p>'+data+'</p>',"Change Start Time|Cancel", '/ajax/changest');
}
function changeEndTime()
{
	var data = '';
	data += '<input type="hidden" name="searchid" value="{$searchid}">';
	data += 'Select End Time: <select name="endtime">'+makeTimeOpts(usrET)+'</select>';
	(new pop_dialog()).show_form("Change End Time",'<p>'+data+'</p>',"Change End Time|Cancel", '/ajax/changeet');
}
function makeTimeOpts(defval)
{
	var hrslst = $w('00 01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20 21 22 23');
	var phrslst = $w('12 1 2 3 4 5 6 7 8 9 10 11 12 1 2 3 4 5 6 7 8 9 10 11');
	var minlst = $w('00 30');
	var data = '';
	for(i=0;i<hrslst.length;i++)
	{
		for(j=0;j<minlst.length;j++)
		{
			var tmp = hrslst[i]+":"+minlst[j]+":00";
			data += '<option value="'+tmp+'"';
			if(tmp==defval)
			{
				data += ' selected';
			}
			data += '>';
			data += phrslst[i]+":"+minlst[j];
			if(i>13)
			{
				data += " pm";
			}
			else
			{
				data += " am";
			}
			data += '</option>';
		}
	}
	return data;
}
//-->
</script>

<table class="standard" cellpadding="5" cellspacing="3" border="0">
<tr>
	<th colspan="2">Restrictions</th>
</tr>
<tr>
	<th class="subr">Term:</th>
	<td>Restrict to courses during <b>{$termname}</b>.  (<a href="#" onclick="changeTerm();return false;">Change</a>)</td>
</tr>
<tr>
	<th class="subr">No Earlier:</th>
	<td>No classes will be earlier than <b>{$starttime}</b>.  (<a href="#" onclick="changeStartTime();return false;">Change</a>)</td>
</tr>
<tr>
	<th class="subr">No Later:</th>
	<td>No classes will end after <b>{$endtime}</b>.  (<a href="#" onclick="changeEndTime();return false;">Change</a>)</td>
</tr>
<tr>
	<th class="subr">Days:</th>
	<td>Only classes offered on <b>{$days}</b> will be shown.  (<a href="#" onclick="changeDays();return false;">Change</a>)</td>
</tr>
<tr>
	<th class="subr">Status:</th>
	<td>Only <b>{$types}</b> classes will be shown.  (<a href="#" onclick="changeTypes();return false;">Change</a>)</td>
</tr>

<tr>
	<th colspan="2">Class Information</th>
</tr>
<tr><td colspan="2">

<script language="javascript" type="text/javascript">
<!--
function addCourse()
{
	var handlerFunc = function(t) {
		var jsond = eval( '(' + t.responseText + ')' );

		if( jsond.error=="1")
		{
			(new pop_dialog()).show_message("Invalid Course",'<p>Sorry, but the course you entered does not exist.</p>','Close');
		}
		else if(jsond.error=="2")
		{
			(new pop_dialog()).show_message("Duplicate Course",'<p>You have already added that course to your schedule.</p>','Close');
		}
		else if (jsond.error=="0")
		{
			addCourseToList(jsond.dept, jsond.cnum, jsond.title, jsond.hash);
		}
	};
	
	var data = "searchid={$searchid}";
	data += "&dept="+encodeURIComponent($F('addcrs_dept'));
	data += "&cnum="+encodeURIComponent($F('addcrs_num'));
	new Ajax.Request("/ajax/crsadd", {method:'post',postBody:data,onSuccess:handlerFunc});

}

function removeCourse(dept,cnum)
{
	var handlerFunc = function(t) {
		var jsond = eval( '(' + t.responseText + ')' );
		window.location = '/search/{$searchid}';
		if(jsond.error=="0")
		{
			$('crs_'+jsond.hash).style.display='none';
		}
	};
	
	var data = "searchid={$searchid}";
	data += "&dept="+encodeURIComponent(dept);
	data += "&cnum="+encodeURIComponent(cnum);
	new Ajax.Request("/ajax/crsdel", {method:'post',postBody:data,onSuccess:handlerFunc});
}

function addCourseToList(dept,cnum,title,hash)
{
	$('crsdiv').setStyle({display:''});
	var clist = $('crslist');

	var data = '';
	var listitem = document.createElement('li');
	listitem.id = 'crs_'+hash;
	
	listitem.appendChild(document.createTextNode(dept+" "+cnum+": "+title+" "));
	
	var delbut = document.createElement('span');
	delbut.className = "fakelink";
	$(delbut).observe('click', 
			function (event){
				removeCourse(dept,cnum);
			} );

	var spantxt = document.createTextNode("(x)");
	delbut.appendChild(spantxt);
	listitem.appendChild(delbut);
	clist.appendChild(listitem);
}
//-->
</script>

<div id="crsdiv">
<ul id="crslist">
</ul>
</div>

<div id="addcrs">
<table cellpadding="2" cellspacing="0" border="0">
<tr>
	<td>Department</td>
	<td>Course</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td><select id="addcrs_dept">
	<!--BEGIN LOOP data="depts"-->
	<option value="{$dept}">{$dept} -- {$name}</option>
	<!--END LOOP data="depts"-->
	</select></td>
	<td><input type="text" id="addcrs_num" size="5" maxlength="5"></td>
	<td><input type="button" value="Add Course" onclick="addCourse();return false;">
</tr>
</table>
</div>
<script language="javascript" type="text/javascript">
<!--
<!--BEGIN LOOP data="courses"-->
addCourseToList( '{$dept}','{$cnum}','{$title}','{$hash}'  );
<!--END LOOP data="courses"-->

//-->
</script>
</td>

</tr>
<tr>
	<th colspan="2">Force Sections</th>
</tr>
<tr>
	<td colspan="2">
	<span class="note">In order to <b>FORCE</b> the scheduler to show schedules that contain specific course codes, enter them into the form below:</span>
<script language="javascript" type="text/javascript">
<!--
function addCcode()
{
	var handlerFunc = function(t) {
		var jsond = eval( '(' + t.responseText + ')' );

		if( jsond.error=="1")
		{
			(new pop_dialog()).show_message("Invalid Course",'<p>Sorry, but the course you entered does not exist.</p>','Close');
		}
		else if(jsond.error=="2")
		{
			(new pop_dialog()).show_message("Duplicate Course",'<p>You have already added that course to your schedule.</p>','Close');
		}
		else if (jsond.error=="0")
		{
			addCcodeToList(jsond.dept, jsond.cnum, jsond.title, jsond.type, jsond.ccode);
		}
	};
	
	var data = "searchid={$searchid}";
	data += "&ccode="+$F('add_ccode');
	new Ajax.Request("/ajax/ccodeadd", {method:'post',postBody:data,onSuccess:handlerFunc});
}

function removeCcode(ccode)
{
	var handlerFunc = function(t) {
		var jsond = eval( '(' + t.responseText + ')' );
		window.location = '/search/{$searchid}';
		if(jsond.error=="0")
		{
			$('ccode_'+jsond.ccode).setStyle({display:'none'});
		}
	};
	
	var data = "searchid={$searchid}";
	data += "&ccode="+ccode;
	new Ajax.Request("/ajax/ccodedel", {method:'post',postBody:data,onSuccess:handlerFunc});
}


function addCcodeToList(dept,cnum,title,type,ccode)
{
   	$('ccodesdiv').setStyle({display:''});
	var clist = $('ccodes');

	var data = '';
	var listitem = document.createElement('li');
	listitem.id = 'ccode_'+ccode;
	
	listitem.appendChild(document.createTextNode(dept+" "+cnum+": "+title+", "+type+" ("+ccode+") "));
	
	var delbut = document.createElement('span');
	delbut.className = "fakelink";
	$(delbut).observe('click', 
			function (event){
				removeCcode(ccode);
			});

	var spantxt = document.createTextNode("(x)");
	delbut.appendChild(spantxt);
	listitem.appendChild(delbut);
	clist.appendChild(listitem);
}
//-->
</script>

	<div id="ccodesdiv">
	<ul id="ccodes">
		
	</ul>
	</div>

<script language="javascript" type="text/javascript">
<!--
<!--BEGIN LOOP data="ccodes"-->
addCcodeToList( '{$dept}','{$cnum}','{$title}','{$type}','{$ccode}'  );
<!--END LOOP data="ccodes"-->
//-->
</script>

	<div id="addcode">
	<table cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td>5-digit Course Code:</td>
		<td><input type="text" size="5" maxlength="5" id="add_ccode"></td>
		<td>
		<input type="button" value="Add" onclick="addCcode();return false;">
		</td>
	</tr>
	</table>
	</div>

	</td>
</tr>
<tr>
	<td colspan="2" align="right">
	
	<input type="button" value="Search &raquo;" class="submit" onclick="goURI('/schedules/{$searchid}');">
	
	</td>
</tr>
</table>
</form>


<div class="mb_wrapper"><div class="mb_info"><b>DISCLAIMER:</b>
<p>This site is not endorsed by UC Irvine.
The author assumes no liability for the damages this may cause to your computer or you.
While the author has endeavoured to present accurate information, this program is a work in progress and the data that it presents may be inaccurate.
The program may have inadvertantly modified class schedule information.
Please be sure to consult the official <a href="http://webster.reg.uci.edu/perl/WebSoc" target="_blank">UCI Schedule of Classes</a> for the official schedule.
This site does not modify your class enrollment and does not update your official UCI schedule.
Use this site at your own risk.</p></div></div>


{include:"/footer.tpl"}
