/* bacis functions */
function confMsg(msg)
{
	if (!confirm(msg))
	{
		return false;
	}
	return true;
}

function valEmpty(elemname, msg)
{
	var elem = document.getElementById(elemname);
	if (elem.value.length == 0)
	{
		alert(msg)
		elem.focus();
		return false;
	}
	return true;
}

function valEmpty2(elemname1, elemname2, msg)
{
	var elem1 = document.getElementById(elemname1);
	var elem2 = document.getElementById(elemname2);
	if (elem1.value.length == 0 && elem2.value.length == 0)
	{
		alert(msg)
		elem1.focus();
		return false;
	}
	return true;
}

function valOption(elemname, msg)
{
	var elem = document.getElementById(elemname);
	if (elem.options[elem.options.selectedIndex].value == -1)
	{
		alert (msg);
		elem.focus();
		return false;
	}
	return true;
}

function valMultyOption(elemname, msg)
{
	var elem = document.getElementById(elemname);
	if (elem.selectedIndex == -1)
	{
		alert (msg);
		elem.focus();
		return false;
	}
	return true;
}

function valRadio(elemname, msg)
{
	var arrRadios = document.getElementsByName(elemname);
	
	var bChecked = false;
	for(var i=0; i < arrRadios.length; i++)
	{
		bChecked = bChecked | arrRadios[i].checked;
	}
	if(!bChecked)
	{
		alert(msg);
		arrRadios[0].focus();
	}
	return bChecked;
}

function valNumber(elemname, msg)
{
	var elem = document.getElementById(elemname);
	if (isNaN(parseInt(elem.value)))
	{
		alert (msg);
		elem.focus();
		return false;
	}
	return true;
}

function valExpression(elemname, msg, expr)
{
	var elem = document.getElementById(elemname);
	var re = expr;
	
	if (elem.value.search(re)==-1)
	{
		alert (msg);
		elem.focus();
		return false;
	}
	return true;
}

/* specific functions */
function valCharacter(elemname, msg)
{
	if(!valEmpty(elemname, msg)) return false;

	var re=/^[\w]+$/;
	if(!valExpression(elemname, msg, re)) return false;
	
	return true;
}

function valDate(elemname, msg)
{
	if(!valEmpty(elemname, msg)) return false;
	
	var re = /^(19|20)\d\d[-.\/](0[1-9]|1[012])[-.\/](0[1-9]|[12][0-9]|3[01])$/;
	if(!valExpression(elemname, msg, re)) return false;
	
	return true;
}

function valLetter(elemname, msg)
{
	if(!valEmpty(elemname, msg)) return false;
	
	var re=/^[a-zA-Z]+[\w\s]*$/;
	if(!valExpression(elemname, msg, re)) return false;
	
	return true;
}

function valEmail(elemname, msg)
{
	if(!valEmpty(elemname, msg)) return false;
	
	var re=/^[\w\.\_\+-]*@[\w\_\+-]+(\.[\w\_\+-]+)*\.[\w\+-]+$/;
	if(!valExpression(elemname, msg, re)) return false;
	
	return true;
}

function matchValues(elemname1, elemname2, msg)
{
	var elem1 = document.getElementById(elemname1);
	var elem2 = document.getElementById(elemname2);

	if (elem1.value != elem2.value)
	{
		alert(msg);
		elem1.value = "";
		elem2.value = "";
		elem1.focus();
		return false;
	}
	return true;
}