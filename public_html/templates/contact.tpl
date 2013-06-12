{include:"/header.tpl"}

<input type="button" value="&laquo; Return to schedule builder" onclick="goURI('/');" class="submit">

<p>Got any questions? Comments? Bug reports? Don't hesitate to let me know!</p>

<h3>Send A Message</h3>
<p>You can send me a message if you want to using the form below. 
If you want a response, then include your email or AIM/Y!M or something.</p>

<form name="message" action="/contact" method="post">
<input type="hidden" name="action" value="feedback">
<textarea cols="50" rows="8" name="msg"></textarea>

<br><input type="submit" value="Send Message" class="submit">

</form>

{include:"/footer.tpl"}
