/*
(c) Copyright David Rochwerger 2001-2003
----------------------------------------
Use is limited to orginal purpose ONLY in the project(s)
implemented and under license for Pracom, Ltd.
David Rochwerger maintains ownership of this file.
Modifications made for 433-351 project (May, 2003)
*/
// validates the provided field is not empty.
// TEXT fields only.
function validate_notempty(field, fieldtxt)
{
	if (field.value.length > 0)  
		return true;
       	alert("<"+fieldtxt+">: This is a mandatory field.");
	field.focus();
   	return false;
}

//validates the provided field has at least one radio/checkbox option selected.
// RADIO fields only.
function validate_radio(field, fieldtxt)
{
	rvar = false;
	for (i=0; i < field.length; i++) {
		if ( field[i].checked == true ) {
			rvar = true;
			break;
		}
	}
	if ( !rvar )
		alert("<"+fieldtxt+">: Please select one of these options.");
	return rvar;
}

//validates the provided field has at least one radio/checkbox option selected.
// CHECKBOX fields only.
function validate_checkbox(field, fieldtxt)
{
	rvar = false;
	for (i=0; i < field.length; i++) {
		if ( field[i].checked == true ) {
			rvar = true;
			break;
		}
	}
	if ( !rvar )
		alert("<"+fieldtxt+">: Please select at least one of these options.");
	return rvar;
}

//validates the provided field has at least one select option selected.
// This assumes that all options have *explicit* values in the option tags,
// except for the first one which would be "Please select" (etc)
// SELECT fields only.
function validate_select(field, fieldtxt)
{
	rvar = false;
	if ( field.value == "" )
		alert("<"+fieldtxt+">: Please select one of these options.");
	else
		rvar = true;
	return rvar;
}

//validates the provided field contains a positive number (integer or float)
// TEXT fields only.
function validate_number(numberfield, fieldtxt) {
    number = parseFloat(numberfield.value).toString();
	if ((number == "NaN") || (number < 0))
	{
    		alert("<"+fieldtxt+">: Please enter a valid (and positive) number");
		numberfield.focus();
    		return false;
  	} 
	return true;
}

//validates the provided field contains a valid email address
// TEXT fields only.
function validate_email(emailfield, fieldtxt) {
	atpos = emailfield.value.indexOf('@');
	if ( atpos < 1 ) //the @ has to be at least one position in.
	{
		alert("<"+fieldtxt+">: Please enter a valid email address");
		return false;
	}
	return true;
}

//validates the provided field is not empty (if specified with the EMPTY parameter)
// and is no longer than MAXCHARS parameter.
// TEXT or TEXTAREA fields only.
function validate_details(txtfield, fieldtxt, maxchars, empty)
{
	if ( empty && (!validate_notempty(txtfield, fieldtxt)) )	//if specified, check if empty.
		return false;
	if (txtfield.value.length > maxchars)
	{
	    	alert("<"+fieldtxt+">: This field can not exceed " + maxchars + " characters.");
		txtfield.focus();
    		return false;
  	} 
	return true;
}

//validates the provided field contains a valid Australian Date.
// TEXT fields only.
function validate_date(datefield, fieldtxt)
{
	datefield.value = datefield.value.replace(/\D/g,'/'); //replace all non-numerals with fwd-slash.
	slash1 = datefield.value.indexOf('/');
	rest = datefield.value.substring(slash1+1);
	slash2 = rest.indexOf('/');
	day = datefield.value.substring(0,slash1);
	month = rest.substring(0,slash2);
	year = rest.substring(slash2+1);
	if ( (year.length != 4) || (month > 12) || (month < 1) 
		|| !_checkday(day, month, year) )
	{
		alert("<"+fieldtxt+">: Please enter a valid date (dd/mm/yyyy)");
		datefield.focus();
		return false;
	}
	else
		return true;
}

//validates the provided field contains a valid Short date (mm/yy) Australian Date.
// TEXT fields only.
function validate_shortdate(datefield, fieldtxt)
{
	datefield.value = datefield.value.replace(/\D/g,'/'); //replace all non-numerals with fwd-slash.
	slash1 = datefield.value.indexOf('/');
	rest = datefield.value.substring(slash1+1);
	month = datefield.value.substring(0,slash1);
	year = rest; 
	if ( (year.length != 2) || (month > 12) || (month < 1) )
	{
		alert("<"+fieldtxt+">: Please enter a valid date (mm/yy)");
		datefield.focus();
		return false;
	}
	else
		return true;
}

function _checkday(day, month, year)
{
	var leapyear = _check_leapyear(year);
	if ( leapyear )
		var maxfeb = 29;
	else
		var maxfeb = 28;
	month = parseInt(month);
	switch(month)
	{
	case 1:
	case 3:
	case 5:
	case 7:
	case 8:
	case 10:
	case 12:
		var maxdays = 31;
		break;
	case 2:
		var maxdays = maxfeb;
		break;
	default:
		var maxdays = 30;
		break;
	}
	return ( (day >= 1) && (day <= maxdays) );
}

function _check_leapyear(year) 
{
	var div4 = (year % 4 == 0);
	var div100 = (year % 100 == 0);
	var div1000 = (year % 1000 == 0);
	
	if ( (div4 && !div100) || (div4 && div100 && div1000) )
		return true;
	else
		return false;
}

