{include:"/header.tpl"}

<!--BEGIN EXISTS data="hide"-->
<li><span class="green"><b>ADDED</b></span>: asdasd</li>
<li><span class="red"><b>REMOVED</b></span>: asdasd</li>
<li><span class="yellow"><b>CHANGED</b></span>: asdasd</li>

<p><b>DATE</b><br /><ul>
CHANGELINES
</ul></p>

<!--END EXISTS data="hide"-->

<p>Below is a list of changes I make to the Schedule Builder. I will try to keep it current.</p>



<p><b>3/7/07</b><br /><ul>
<li><span class="yellow"><b>CHANGED</b></span>: Parser correctly finds buildings with numbers in them (HOB2..).</li>
<li><span class="yellow"><b>CHANGED</b></span>: Fixed the parsing system to recognize classes with 3 character section codes (A15..).</li>
<li><span class="yellow"><b>CHANGED</b></span>: Fixed problems with some classes with same numbers, but different letters. (21, H21, 31, 31L). The problem has been fixed, and you can now search those classes.</li>
<li><span class="green"><b>ADDED</b></span>: A caching system. Course results are stored on the server instead of quering WebSOC all the time. Each time you click Search, results are cleared.</li>
<li><span class="green"><b>ADDED</b></span>: Per user request, added a button for printer friendly version of the schedule detail.</li>
</ul></p>



<p><b>3/2/07</b><br /><ul>
<li><span class="yellow"><b>CHANGED</b></span>: Now you only need to set the department and course number, all other information is pulled via WebSOC.</li>
<li><span class="red"><b>REMOVED</b></span>: The need to set the main/linked types for a course. They are now pulled automatically.</li>
</ul></p>

<p><b>3/1/07</b><br /><ul>
<li><span class="yellow"><b>CHANGED</b></span>: Fixed the time restrictions to not error out if a class is before that time. Only when all classes don't pass the time restriction.</li>
<li><span class="red"><b>REMOVED</b></span>: The ability to set a specific section. If you want a specific section, just search the results page for it, until I implement it better.</li>

</ul></p>

<p><b>2/13/07</b><br /><ul>
<li><span class="red"><b>REMOVED</b></span>: The ability to specify home location, its really not needed.</li>
</ul></p>

<p><b>2/12/07</b><br /><ul>
<li><span class="green"><b>ADDED</b></span>: The ability to search for dual linked courses (ones with a dis/lab).</li>
<li><span class="green"><b>ADDED</b></span>: Schedules that require you to run across campus in 10 mins are flagged red.</li>
<li><span class="green"><b>ADDED</b></span>: Detail table now shows more information.</li>
<li><span class="green"><b>ADDED</b></span>: The ability for users to send feedback.</li>
<li><span class="yellow"><b>CHANGED</b></span>: Detail page table takes up less space.</li>
<li><span class="yellow"><b>CHANGED</b></span>: Cookie session lasts 120 days now, that way it remembers what you searched for.</li>
</ul></p>

{include:"/footer.tpl"}
