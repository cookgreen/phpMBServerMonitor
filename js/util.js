function getFileExtension(filename)
{
	return filename.substring(filename.lastIndexOf('.')+1, filename.length).toLowerCase();
}
function getUrlParam(name) {  
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");  
    var r = window.location.search.substr(1).match(reg);
    if(r != null) {  
        return decodeURI(r[2]);  
    }  
    return null;  
} 

function getCurrentPathWithoutFile()
{
	var fullPath = window.location.href;
	var currentPathWithoutFile = fullPath.substring(0, fullPath.lastIndexOf("/")+1);
	return currentPathWithoutFile;
}

function getFileName(filepath)
{
	return filepath.substring(filepath.lastIndexOf('/')+1, filepath.length);
}

function getWeishu(digital_number)
{
	var ret = parseInt(digital_number) / 10;
	var weishu = 1;
	while(ret>1)
	{
		ret = getWeishuSub(ret);
		weishu++;
	}
	return weishu;
}
function getWeishuSub(digital_number)
{
	var ret = digital_number / 10;
	return ret;
}
