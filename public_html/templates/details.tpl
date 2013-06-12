{include:"/header.tpl"}

<div class="noprint">
	<input type="button" class="submit" value="&laquo; Return to Schedule List" onclick="goURI('/schedules/{$searchid}');return false;">
	&nbsp;
	<input type="button" class="submit" value="Print" onclick="window.print();">
	<br><br>

	<table cellpadding="4" cellspacing="2" border="0" class="standard">
	<tr>
		<th colspan="5">Course Information</th>	
	</tr>
	<tr>
		<th>Course</th>
		<th>Status</th>
		<th>Teacher</th>
		<th>Time</th>
		<th>Days</th>
	</tr>
	<!--BEGIN LOOP data="crsinfo"-->
	<tr>
		<td><b>{$dept} {$cnum} ({$type})</b></td>
		<td>{$status}</td>
		<td>{$teacher}</td>
		<td>{$start} - {$end}</td>
		<td>{$days}</td>
	</tr>
	<!--END LOOP data="crsinfo"-->
	</table>

	<br>

</div>

<table cellpadding="0" cellspacing="0" border="0" class="timetable">
<tr>
	<th>Time</th>
	<!--BEGIN LOOP data="daysh"-->
	<th class="day{$class} daytop">{$day}</th>
	<!--END LOOP data="daysh"-->
	<th>Time</th>
</tr>


<!--BEGIN LOOP data="calendar"-->
<tr>
	<th class="timel">{$time}</th>
	
	{$com1s}<td align="center" rowspan="{$rowspan1}" class="{$class1}">{$crs1}</td>{$com1e}
	{$com2s}<td align="center" rowspan="{$rowspan2}" class="{$class2}">{$crs2}</td>{$com2e}
	{$com3s}<td align="center" rowspan="{$rowspan3}" class="{$class3}">{$crs3}</td>{$com3e}
	{$com4s}<td align="center" rowspan="{$rowspan4}" class="{$class4}">{$crs4}</td>{$com4e}
	{$com5s}<td align="center" rowspan="{$rowspan5}" class="{$class5}">{$crs5}</td>{$com5e}
	{$com6s}<td align="center" rowspan="{$rowspan6}" class="{$class6}">{$crs6}</td>{$com6e}
	{$com7s}<td align="center" rowspan="{$rowspan7}" class="{$class7}">{$crs7}</td>{$com7e}

	<th class="timer">{$time}</th>
</tr>
<!--END LOOP data="calendar"-->

<tr>
	<th>Time</th>
	<!--BEGIN LOOP data="daysh"-->
	<th class="day{$class}">{$day}</th>
	<!--END LOOP data="daysh"-->
	<th>Time</th>
</tr>

</table>

<div class="noprint">
<br><input type="button" class="submit" value="&laquo; Return to Schedule List" onclick="goURI('/schedules/{$searchid}');return false;">
</div>

{include:"/footer.tpl"}
