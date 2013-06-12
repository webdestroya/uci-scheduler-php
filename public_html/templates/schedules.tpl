{include:"/header.tpl"}

<script language="javascript" type="text/javascript">
<!--

function getScheduleList(searchid)
{
	var handlerFunc = function(t) {
		var jsond = eval( '(' + t.responseText + ')' );
        if(jsond.error!="0")
        {
			$('schedlist').innerHTML = "";
			
			(new pop_dialog()).show_choice("Search Error",
			'<p>'+jsond.errorstr+'</p>',
			'OK',
			"goURI('/search/{$searchid}');"
			);

        }
		else
		{
			if( jsond.schedules.length==0)
			{
				(new pop_dialog()).show_choice("No Schedules Found",
				'<p>Sorry, but your search was too restrictive, and did not return any results.</p>',
				'OK',
				"goURI('/search/{$searchid}');"
			);

			}
			else
			{
			var content = '<table cellpadding="5" cellspacing="3" class="standard" border="0">';
			content += '<tr>';
			content += '<th>&nbsp;</th>';
			content += '<th>CCode List</th>';
			content += '<th>Details</th>';
			content += '</tr>';
			for(i=0;i<jsond.schedules.length;i++)
			{
				content += "<tr>";
				content += '<td>'+(i+1)+'</td>';
				content += '<td>'+jsond.schedules[i].ccodes+'</td>';
				content += '<td><a href="/details/'+jsond.searchid+'/'+jsond.schedules[i].hash+'">Details</a></td>';

				content += "</tr>";
			}
			content += '</table>';
			$('schedlist').innerHTML = content;
			}
		}
    };

	var errorFunc = function(t) {
		(new pop_dialog()).show_choice("Search Error",
		'<p>Sorry, but there was an error with the schedule generator. Please try again.</p>',
		'OK',
		"goURI('/search/{$searchid}');"
		);

	};

	var data = "searchid="+searchid;
    new Ajax.Request("/ajax/getschedules", {
		method:'post',
		postBody:data,
		onSuccess:handlerFunc,
		onFailure:errorFunc
		});
}
//-->
</script>

<input type="button" class="submit" value="&laquo; Change Search Options" onclick="goURI('/search/{$searchid}');return false;"><br><br>

<div id="schedlist" style="margin-top:10px;margin-bottom:10px;">
	<div align="center" style="margin:25px;">
	<span style="font-size:24px;font-color:#666;">Searching for schedules...</span>
	<br>
	<img src="/static/images/whiteloader.gif" width="48" height="48" border="0" alt="Loading..." title="Loading...">
	</div>
</div>

<script language="javascript" type="text/javascript">
<!--
getScheduleList('{$searchid}');
//-->
</script>

{include:"/footer.tpl"}
