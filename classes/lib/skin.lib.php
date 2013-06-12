<?php

/**
  * Skin Libraries
  * @package tthouse
  * @subpackage Display
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
function skin_form_dropdown_nk($name, $list=array(), $default="",$multiple=0,$js="")
{
    $multi = $multiple==1 ? " multiple" : "";
    $html = '<select '.$js.' name="'.$name.'" id="'.rtrim(str_replace(array("[","]"),"_",$name),"_").'"'.$multi.'>';
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
function skin_form_dropdown_k($name, $list=array(), $defval=false,$multiple=0,$js="")
{
    $multi = $multiple==1 ? " multiple" : "";
    $html = '<select '.$js.' name="'.$name.'" id="'.rtrim(str_replace(array("[","]"),"_",$name),"_").'"'.$multi.'>';
	foreach($list as $k=>$v)
    {
        $selected = '';

		if(strval($k)==strval($defval))
        {
            $selected = " selected";
        }
        $html .= "<option value=\"".$k."\"".$selected.">".$v."</option>";
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
function skin_date_select($name, $def=0,$type="datetime",$range=false)
{
    if($def==0)
    {
        $def = mktime(0,0,0,date("m"),date("d"));
    }
    
    $yearstart = 1986;
    $yearend = (date("Y")+3);
    if($range)
    {
    	$yearstart = $range[0];
    	$yearend = $range[1];
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
        for($i=$yearstart;$i<=$yearend;$i++)
        {
            $selected = date("Y",$def)==$i ? " selected" : "";
            $text .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
        }
        $text .= "</select>";
    }
    
    if($type=="datetime")
    {
        $text .= " at ";
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
        $text .= "</select> ";
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

?>
