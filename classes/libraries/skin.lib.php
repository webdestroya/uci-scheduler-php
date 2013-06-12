<?php

/**
  * Skin Libraries
  * @package ANHSLibraries
  * @subpackage Skin
  * @filesource
  */

/**
 * Creates a group of radio buttons
 * @param string $name name of the textarea 
 * @param array $list to put in the text area
 * @param string $default  number of columns in the textarea
 * @param string $sep  number of rows in the textarea
 * @return string 
 */	
function skin_form_radiogroup($name, $list=array(), $default="", $sep="<br />") 
{
	$text = "";
	$keys = array_keys($list);
	foreach( $keys as $k )
	{
		$checked = "";
		if( $k == $default )
		{
			$checked = " checked";
		}
		$text .= "<input type='radio' name='$name'$checked value='$k' /> ".$list[ $k ].$sep;
		
	}
	$text = substr($text,0, (strlen($sep)*(-1) ));
	return $text;
}

/**
 * Creates a dropdown where the value and the label is the array value
 * @param string $name name of the field
 * @param array $list array of the options
 * @param string $default the default selected value
 * @param string $js optional javascript
 */
function skin_form_dropdown_nk($name, $list=array(), $default="",$multiple=0)
{
	$multi = $multiple==1 ? " multiple" : "";
	$html = '<select name="'.$name.'"'.$multi.'>';
	for( $i=0; $i < count($list); $i++ )
	{
		$selected = '';
		if( $list[ $i ] == $default )
		{
			$selected = " selected";
		}
		$html .= "<option value=\"".$list[$i]."\"".$selected.">".$list[$i]."</option>";
	}//
	$html .= "</select>";
	return $html;
}

/**
 * Creates a dropdown where the value is the key, and the label is the value
 * @param string $name name of the field
 * @param array $list array of the options
 * @param string $default the default selected value
 * @param string $js optional javascript
 */
function skin_form_dropdown_k($name, $list=array(), $default="",$multiple=0)
{
	$multi = $multiple==1 ? " multiple" : "";
	$html = '<select name="'.$name.'"'.$multi.'>';
	$keys = array_keys($list);
	for( $i=0; $i < count($keys); $i++ )
	{
		$selected = '';
		if( $keys[$i] == $default || $list[ $keys[$i] ] == $default )
		{
			$selected = " selected";
		}
		$html .= "<option value=\"".$keys[$i]."\"".$selected.">".$list[ $keys[$i] ]."</option>";
	}//
	$html .= "</select>";
	return $html;
}

/**
 * Adds a pair of Yes/No Radio Buttons
 * @param string $name name of the buttons
 * @param string $default_val default value ("true"/"false")
 * @param string $sep the default separator between the fields
 * @return string 
 */	
function skin_form_yes_no( $name, $default_val="", $sep="&nbsp;&nbsp;&nbsp;"  ) 
{
	$yes = "<input type='radio' name='$name' value='1' />&nbsp;Yes";
	$no  = "<input type='radio' name='$name' value='0' />&nbsp;No";
	if ($default_val == 'true' || $default_val == '1')
	{
		$yes = "<input type='radio' name='$name' value='1' checked />&nbsp;Yes";
	}
	else
	{
		$no  = "<input type='radio' name='$name' value='0' checked />&nbsp;No";
	}
	return $yes.$sep.$no;
}

/**
 * Adds a checkbox field
 * @param string $name the name of the checkbox
 * @param string $val the value of the checkbox
 * @param bool $checked whether or not it is checked
 * @return string 
 */
function skin_form_checkbox( $name, $val, $checked=false ) 
{
	if ($checked)
	{
		return "<input type='checkbox' name='$name' value='$val' checked='checked' />";
	}
	else
	{
		return "<input type='checkbox' name='$name' value='$val' />";
	}
}


// THESE ARE NEW
function skin_date_select($name, $def=0,$type="datetime")
{
	if($def==0)
	{
		$def = mktime(0,0,0,date("m"),date("d"));
	}
	// type = date,datetime,time
	$text = "";
	if(in_array($type,array("datetime","date","month")) )
	{
		// Month
		$text .= '<select name="'.$name.'[m]">';
		for($i=1;$i<=12;$i++)
		{
			$selected = date("n",$def)==$i ? " selected" : "";
			$text .= '<option value="'.$i.'"'.$selected.'>'.date("F",mktime(0,0,0,$i,1)).'</option>';
		}
		$text .= "</select> ";
	}
	if(in_array($type,array("datetime","date","day")) )
	{	
		// Day
		$text .= '<select name="'.$name.'[d]">';
		for($i=1;$i<=31;$i++)
		{
			$selected = date("j",$def)==$i ? " selected" : "";
			$text .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		}
		$text .= "</select> ";
	}
	if(in_array($type,array("datetime","date","year")) )
	{
		// Year
		$text .= '<select name="'.$name.'[y]">';
		for($i=1986;$i<=(date("Y")+3);$i++)
		{
			$selected = date("Y",$def)==$i ? " selected" : "";
			$text .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		}
		$text .= "</select>";
	}
	
	if($type=="datetime")
	{
		$text .= " @ ";
	}
	
	if(in_array($type,array("datetime","time","hour")) )
	{
		// Hour
		$text .= '<select name="'.$name.'[h]">';
		for($i=1;$i<=12;$i++)
		{
			$selected = date("g",$def)==$i ? " selected" : "";
			if($i==12)
			{
				$text .= '<option value="0"'.$selected.'>12</option>';
			}
			else 
			{
				$text .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
			}
		}
		$text .= "</select> : ";
	}
	if(in_array($type,array("datetime","time","minute")) )
	{
		// Minute
		$text .= '<select name="'.$name.'[i]">';
		for($i=0;$i<=59;$i++)
		{
			$selected = date("i",$def)==$i ? " selected" : "";
			$text .= '<option value="'.$i.'"'.$selected.'>'.str_pad($i,2,"0",STR_PAD_LEFT).'</option>';
		}
		$text .= "</select> : ";
	}
	if(in_array($type,array("datetime","time","ampm")) )
	{
		//ampm
		$text .= '<select name="'.$name.'[a]">';
		$text .= date("a",$def)=="am" ? '<option value="0" selected>am</option>' : '<option value="0">am</option>';
		$text .= date("a",$def)=="pm" ? '<option value="12" selected>pm</option>' : '<option value="12">pm</option>';
		$text .= "</select>";
	}
	
	return $text;
}
$COUNTRIES = array('AL'=>"Albania",'DZ'=>"Algeria",'AS'=>"American Samoa",'AD'=>"Andorra",'AI'=>"Anguilla",'AQ'=>"Antartica",'AG'=>"Antigua &amp; Barbuda",'AR'=>"Argentina",'AM'=>"Armenia",'AW'=>"Aruba",'AU'=>"Australia",'AT'=>"Austria",'AZ'=>"Azerbaijan",'BS'=>"Bahamas",'BH'=>"Bahrain",'BD'=>"Bangladesh",'BB'=>"Barbados",'BY'=>"Belarus",'BE'=>"Belgium",'BZ'=>"Belize",'BJ'=>"Benin",'BM'=>"Bermuda",'BT'=>"Bhutan",'BO'=>"Bolivia",'BW'=>"Botswana",'BV'=>"Bouvet Island",'BR'=>"Brazil",'IO'=>"British Indian Ocean Terr.",'BN'=>"Brunei Darussalam",'BG'=>"Bulgaria",'BF'=>"Burkina Faso",'BI'=>"Burundi",'KH'=>"Cambodia",'CM'=>"Cameroon",'CA'=>"Canada",'CV'=>"Cape Verde",'KY'=>"Cayman Islands",'CF'=>"Central African Republic",'TD'=>"Chad",'CL'=>"Chile",'CN'=>"China",'CX'=>"Christmas Island",'CC'=>"Cocos (Keeling) Isl",'CO'=>"Colombia",'KM'=>"Comoros",'CG'=>"Congo",'CK'=>"Cook Isl",'CR'=>"Costa Rica",'CI'=>"Cote D'Ivoire",'HR'=>"Croatia",'CY'=>"Cyprus",'CZ'=>"Czech Republic",'DK'=>"Denmark",'DJ'=>"Djibouti",'DM'=>"Dominica",'DO'=>"Dominican Republic",'TP'=>"East Timor",'EC'=>"Ecuador",'EG'=>"Egypt",'SV'=>"El Salvador",'GQ'=>"Equatorial Guinea",'EE'=>"Estonia",'ET'=>"Ethiopia",'FO'=>"Faeroe Islands",'FK'=>"Falkland Isl. (Malvinas)",'FJ'=>"Fiji",'FI'=>"Finland",'FR'=>"France",'GF'=>"French Guiana",'PF'=>"French Polynesia",'TF'=>"French Southern Terr.", 'GA'=>"Gabon",'GM'=>"Gambia",'GE'=>"Georgia",'DE'=>"Germany",'GH'=>"Ghana",'GI'=>"Gibraltar",'GR'=>"Greece",'GL'=>"Greenland",'GD'=>"Grenada",'GP'=>"Guadeloupe",'GU'=>"Guam",'GT'=>"Guatemala",'GG'=>"Guernsey, C.I.",'GN'=>"Guinea",'GW'=>"Guinea-Bissau",'GY'=>"Guyana",'HT'=>"Haiti",'HM'=>"Heard and McDonald Isl",'HN'=>"Honduras",'HK'=>"Hong Kong",'HU'=>"Hungary",'IS'=>"Iceland",'IN'=>"India",'ID'=>"Indonesia",'IE'=>"Ireland",'IM'=>"Isle of Man",'IL'=>"Israel",'IT'=>"Italy",'JM'=>"Jamaica",'JP'=>"Japan",'JE'=>"Jersey, C.I.",'JO'=>"Jordan",'KZ'=>"Kazakhstan",'KE'=>"Kenya",'KI'=>"Kiribati",'KR'=>"Korea, Republic of",'KW'=>"Kuwait",'KG'=>"Kyrgyzstan",'LA'=>"Laos",'LV'=>"Latvia",'LB'=>"Lebanon",'LS'=>"Lesotho",'LR'=>"Liberia",'LI'=>"Liechtenstein",'LT'=>"Lithuania",'LU'=>"Luxemborg",'MO'=>"Macau",'MG'=>"Madagascar",'MW'=>"Malawi",'MY'=>"Malaysia",'MV'=>"Maldives",'ML'=>"Mali",'MT'=>"Malta",'MH'=>"Marshall Isl",'MQ'=>"Martinique",'MR'=>"Mauritania",'MU'=>"Mauritius",'MX'=>"Mexico",'FM'=>"Micronesia",'MD'=>"Moldova, Republic of",'MC'=>"Monaco",'MN'=>"Mongolia",'MS'=>"Montserrat",'MA'=>"Morocco",'MZ'=>"Mozambique",'MM'=>"Myanmar",'NA'=>"Namibia",'NR'=>"Nauru",'NP'=>"Nepal",'AN'=>"Netherland Antilles",'NL'=>"Netherlands",'NC'=>"New Caledonia",'NZ'=>"New Zealand",'NI'=>"Nicaragua",'NE'=>"Niger",'NG'=>"Nigeria",'NU'=>"Niue",'NF'=>"Norfolk Isl",'MP'=>"Northern Mariana Isl",'NO'=>"Norway",'OM'=>"Oman",'PK'=>"Pakistan",'PW'=>"Palau",'PA'=>"Panama",'PZ'=>"Panama Canal Zone",'PG'=>"Papua New Guinea",'PY'=>"Paraguay",'PE'=>"Peru",'PH'=>"Philippines",'PN'=>"Pitcairn",'PL'=>"Poland",'PT'=>"Portugal",'PR'=>"Puerto Rico",'QA'=>"Qatar",'RE'=>"Reunion",'RO'=>"Romania",'RU'=>"Russian Federation",'RW'=>"Rwanda",'KN'=>"Saint Kitts &amp; Nevis",'LC'=>"Saint Lucia",'WS'=>"Samoa",'SM'=>"San Marino",'ST'=>"Sao Tome &amp; Principe",'SA'=>"Saudi Arabia",'SN'=>"Senegal",'SC'=>"Seychelles",'SL'=>"Sierra Leone",'SG'=>"Singapore",'SK'=>"Slovakia",'SI'=>"Slovenia",'SB'=>"Solomon Islands",'SO'=>"Somalia",'ZA'=>"South Africa",'ES'=>"Spain",'LK'=>"Sri Lanka",'SH'=>"St. Helena",'PM'=>"St. Pierre and Miquelon",'VC'=>"St. Vincent &amp; Grenadines",'SR'=>"Suriname",'SJ'=>"Svalbard &amp; Jan Mayen Isl",'SZ'=>"Swaziland",'SE'=>"Sweden",'CH'=>"Switzerland",'TW'=>"Taiwan",'TJ'=>"Tajikistan",'TZ'=>"Tanzania, United Republic",'TH'=>"Thailand",'TG'=>"Togo",'TK'=>"Tokelau",'TO'=>"Tonga",'TT'=>"Trinidad &amp; Tobago",'TN'=>"Tunisia",'TR'=>"Turkey",'TM'=>"Turkmenistan",'TC'=>"Turks and Caicos Isl",'TV'=>"Tuvalu",'AE'=>"U.A.E.",'UM'=>"U.S.Minor Outlying Isl",'UG'=>"Uganda",'UA'=>"Ukraine",'GB'=>"United Kingdom",'US'=>"United States",'UY'=>"Uruguay",'UZ'=>"Uzbekistan",'VU'=>"Vanuatu",'VA'=>"Vatican City State",'VE'=>"Venezuela",'VN'=>"Viet Nam",'VG'=>"Virgin Isl (British)",'VI'=>"Virgin Isl, (U.S.)",'WF'=>"Wallis &amp; Futuna Islands",'EH'=>"Western Sahara",'YE'=>"Yemen, Republic of",'ZR'=>"Zaire",'ZM'=>"Zambia",'ZW'=>"Zimbabwe",);


$STATES = array(
'AL'=>"Alabama",'AK'=>"Alaska",'AZ'=>"Arizona",'AR'=>"Arkansas",
'CA'=>"California",'CO'=>"Colorado",'CT'=>"Connecticut",
'DE'=>"Delaware",'DC'=>"District of Columbia",
'FL'=>"Florida",
'GA'=>"Georgia",
'HI'=>"Hawaii",
'ID'=>"Idaho",'IL'=>"Illinois",'IN'=>"Indiana",'IA'=>"Iowa",
'KS'=>"Kansas",'KY'=>"Kentucky",
'LA'=>"Louisiana",
'ME'=>"Maine",'MD'=>"Maryland",'MA'=>"Massachusetts",'MI'=>"Michigan",'MN'=>"Minnesota",'MS'=>"Mississippi",'MO'=>"Missouri",'MT'=>"Montana",
'NE'=>"Nebraska",'NV'=>"Nevada",'NH'=>"New Hampshire",'NJ'=>"New Jersey",'NM'=>"New Mexico",'NY'=>"New York",'NC'=>"North Carolina",'ND'=>"North Dakota",
'OH'=>"Ohio",'OK'=>"Oklahoma",'OR'=>"Oregon",
'PA'=>"Pennsylvania",
'RI'=>"Rhode Island",
'SC'=>"South Carolina",'SD'=>"South Dakota",
'TN'=>"Tennessee",'TX'=>"Texas",
'UT'=>"Utah",
'VT'=>"Vermont",'VA'=>"Virginia",
'WA'=>"Washington",'WV'=>"West Virginia",'WI'=>"Wisconsin",'WY'=>"Wyoming"
);


?>
