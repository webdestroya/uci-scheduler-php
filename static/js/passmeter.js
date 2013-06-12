function testPassword(passwd)
{
var intScore=0;
if(passwd.length<5){intScore=(intScore+3);}
else if(passwd.length>4&&passwd.length<8){intScore=(intScore+6);}
else if(passwd.length>7&&passwd.length<16){intScore=(intScore+12);}
else if(passwd.length>15){intScore=(intScore+18);}
if(passwd.match(/[a-z]/)){intScore=(intScore+1);}
if(passwd.match(/[A-Z]/)){intScore=(intScore+5);}
if(passwd.match(/\d+/)){intScore=(intScore+5);}
if(passwd.match(/(.*[0-9].*[0-9].*[0-9])/)){intScore=(intScore+5);}
if(passwd.match(/.[!,@,#,$,%,^,&,*,?,_,~]/)){intScore=(intScore+5);}
if(passwd.match(/(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/)){intScore=(intScore+5);}
if(passwd.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)){intScore=(intScore+2);}
if(passwd.match(/([a-zA-Z])/)&&passwd.match(/([0-9])/)){intScore=(intScore+2);}
if(passwd.match(/([a-zA-Z0-9].*[!,@,#,$,%,^,&,*,?,_,~])|([!,@,#,$,%,^,&,*,?,_,~].*[a-zA-Z0-9])/)){intScore=(intScore+2);}
return intScore;
}

function showPassHard(pass,field)
{
if(pass==''){$(field).innerHTML= "";}
else{
var temp=testPassword(pass);var passfield="<b>Password Stength:</b> ";
if(temp<16){strVerdict="<b class='weak'>Weak</b>";}
else if(temp>15&&temp<25){strVerdict="<b class='good'>Good</b>";}
else if(temp>24&&temp<35){strVerdict="<b class='strong'>Strong</b>";}
else if(temp>34&&temp<45){strVerdict="<b class='stronger'>Stronger</b>";}
else{strVerdict="<b class='para'>Are you going to remember this?</b>";}
$(field).innerHTML=passfield+strVerdict;}
}