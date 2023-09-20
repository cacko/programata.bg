function isTop()
{
	if (this.location != top.location)
	{
		top.location = this.location;
	}
}
//isTop();

function reloadPage()
{
	// reload IE to reload css - concerns abs positioning
	if (document.all)
		document.location.href=document.location.href;
}

function writeYear()
{
	var startYear = 2002;
	var curYear = (new Date()).getFullYear();
	document.write(startYear == curYear ? startYear : startYear + "-" + curYear);
}

function lt (elem, selectedColor)
{
	var aColors = new Array();
	aColors[0]="";
	aColors[1]="#93b3d2";
	elem.style.backgroundColor = aColors[selectedColor];
}

function fixHeights()
{
	var sourceNode = document.getElementById('content');
	var targetNode = document.getElementById('context');
	
	//alert(sourceNode.clientHeight);
	targetNode.style.height = (sourceNode.clientHeight - 50);
}

function showHide(elem, b_show)
{
	
	var gNode = document.getElementById(elem);
	if (gNode)
	{
		if (b_show == true)
			gNode.style.display = "block";
		else
			gNode.style.display = "none";
	}
}

function showImgLt(imgid)
{
	
	var gNodes = document.getElementById('gallery').childNodes;
	for (var i = 0; gNodes.length > i; i ++)
	{
		if (gNodes[i].tagName == "IMG")
		{
			if (gNodes[i].attributes['id'].value == 'i' +imgid)
				gNodes[i].style.display = "block";
			else
				gNodes[i].style.display = "none";
		}
	}
	var gNodes = document.getElementById('thumbs').childNodes;
	for (var i = 0; gNodes.length > i; i ++)
	{
		if (gNodes[i].tagName == "LI")
		{
			if (gNodes[i].attributes['id'].value == 'a' +imgid)
				gNodes[i].className = "on";
			else
				gNodes[i].className = "off";
		}
	}
}

function showImg(imgid)
{
	var gNodes = document.getElementById('gallery').childNodes;
	for (var i = 0; gNodes.length > i; i ++)
	{
		if (gNodes[i].tagName == "IMG")
		{
			if (gNodes[i].attributes['id'].value == 'i' +imgid)
				gNodes[i].style.display = "block";
			else
				gNodes[i].style.display = "none";
		}
	}
	var gNodes = document.getElementById('nums').childNodes;
	for (var i = 0; gNodes.length > i; i ++)
	{
		if (gNodes[i].tagName == "LI")
		{
			if (gNodes[i].attributes['id'].value == 'a' +imgid)
				gNodes[i].className = "on";
			else
				gNodes[i].className = "off";
		}
	}
	var gNodes = document.getElementById('info').childNodes;
	for (var i = 0; gNodes.length > i; i ++)
	{
		if (gNodes[i].tagName == "SPAN")
		{
			if (gNodes[i].attributes['id'].value == 'tt' +imgid)
				gNodes[i].style.display = "block";
			else
				gNodes[i].style.display = "none";
		}
	}
	var gNodes = document.getElementById('auth').childNodes;
	for (var i = 0; gNodes.length > i; i ++)
	{
		if (gNodes[i].tagName == "SPAN")
		{
			if (gNodes[i].attributes['id'].value == 'au' +imgid)
				gNodes[i].style.display = "block";
			else
				gNodes[i].style.display = "none";
		}
	}
}