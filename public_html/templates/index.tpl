{include:"/header_no.tpl"}
<h1>News</h1><div id="news">
<!--BEGIN LOOP data="news"-->
<h3>{$title}</h3><p><span>{$date}</span><br />{$post}</p><hr />
<!--END LOOP-->
</div>
{include:"/footer.tpl"}
