var cal = null;

var isFocus=false; //是否为焦�?
function SelectDate(obj,strFormat) {
	if( !document.getElementById('calendarPanel') ) createCale();
	var date = new Date();
	var by = date.getFullYear()-50;  //最小�?�?50 年前
	var ey = date.getFullYear()+50;  //最大�?�?50 年后
	if( !checkCal() ) {
		cal = new Calendar(by, ey, 0, strFormat);
	}
	else {
		cal.dateFormatStyle = strFormat;
	}
	if( obj.value ) {
		obj.value = obj.value.toDate(strFormat).format(strFormat);
		if( (startTime = obj.value.split('-') ) && startTime.length > 2 ) {
			cal.year = startTime[0];
			cal.month = startTime[1];
			cal.changeSelect();
			cal.bindData();
		}
	}
	else {
		obj.value = (new Date()).format(strFormat);
	}
	cal.show(obj);
}
function checkCal() {
	var d = document.getElementById('calendarTable');
	return !(cal==null||d==null||typeof d=='undefined');
}
String.prototype.toDate = function(style) {
	var y = this.substring(style.indexOf('y'),style.lastIndexOf('y')+1);//�?
	var m = this.substring(style.indexOf('M'),style.lastIndexOf('M')+1);//�?
	var d = this.substring(style.indexOf('d'),style.lastIndexOf('d')+1);//�?
	if(isNaN(y)) y = new Date().getFullYear();
	if(isNaN(m)) m = new Date().getMonth();
	if(isNaN(d)) d = new Date().getDate();
	var dt ;
	eval ("dt = new Date('"+ y+"', '"+(m-1)+"','"+ d +"')");
	return dt;
};
Date.prototype.format = function(style) {
	var o = {
		"M+" : this.getMonth() + 1, //month
		"d+" : this.getDate(),		//day
		"h+" : this.getHours(),	 //hour
		"m+" : this.getMinutes(),   //minute
		"s+" : this.getSeconds(),   //second
		"w+" : "日一二三四五六".charAt(this.getDay()),   //week
		"q+" : Math.floor((this.getMonth() + 3) / 3),  //quarter
		"S"  : this.getMilliseconds() //millisecond
	};
	if(/(y+)/.test(style)) {
		style = style.replace(RegExp.$1,(this.getFullYear() + "").substr(4 - RegExp.$1.length));
	}
	for(var k in o) {
		if(new RegExp("("+ k +")").test(style)) {
			style = style.replace(RegExp.$1,
			RegExp.$1.length == 1 ? o[k] :
			("00" + o[k]).substr(("" + o[k]).length));
		}
	}
	return style;
};
function Calendar(beginYear, endYear, lang, dateFormatStyle) {
	this.beginYear = 1990;
	this.endYear = 2010;
	this.lang = 0;			//0(中文) | 1(英文)
	this.dateFormatStyle = "yyyy-MM-dd";
	if (beginYear != null && endYear != null) {
		this.beginYear = beginYear;
		this.endYear = endYear;
	}
	if (lang != null) {
		this.lang = lang
	}
	if (dateFormatStyle != null) {
		this.dateFormatStyle = dateFormatStyle
	}
	this.dateControl = null;
	this.panel = this.getElementById("calendarPanel");
	this.container = this.getElementById("ContainerPanel");
	this.form  = null;
	this.date = new Date();
	this.year = this.date.getFullYear();
	this.month = this.date.getMonth();
	this.colors = {
		"cur_word"		: "#FFFFFF",  //当日日期文字颜色
		"cur_bg"		: "#00FF00",  //当日日期单元格背影色
		"sel_bg"		: "#FFCCCC",  //已被选择的日期单元格背影�?2006-12-03 寒羽枫添�?
		"sun_word"		: "#FF0000",  //星期天文字颜�?
		"sat_word"		: "#0000FF",  //星期六文字颜�?
		"td_word_light" : "#333333",  //单元格文字颜�?
		"td_word_dark"  : "#CCCCCC",  //单元格文字暗�?
		"td_bg_out"	 : "#EFEFEF",  //单元格背影色
		"td_bg_over"	: "#FFCC00",  //单元格背影色
		"tr_word"		 : "#FFFFFF",  //日历头文字颜�?
		"tr_bg"		 : "#666666",  //日历头背影色
		"input_border"  : "#CCCCCC",  //input控件的边框颜�?
		"input_bg"		: "#EFEFEF"   //input控件的背影色
	};
	this.draw();
	this.bindYear();
	this.bindMonth();
	this.changeSelect();
	this.bindData();
}
Calendar.language = {
	"year"   : [
		[""], [""]
	],
	"months" : [
		["一月","二月","三月","四月","五月","六月","七月","八月","九月","十月","十一月","十二月"],
		["JAN","FEB","MAR","APR","MAY","JUN","JUL","AUG","SEP","OCT","NOV","DEC"]
	],
	"weeks"  : [
		["日","一","二","三","四","五","六"],
		["SUN","MON","TUR","WED","THU","FRI","SAT"]
	],
	"clear"  : [
		["清空"], 
		["CLS"]
	],
	"today"  : [
		["今天"], 
		["TODAY"]
	],
	"close"  : [
		["关闭"],
		["CLOSE"]
	]
};
Calendar.prototype.draw = function() {
	calendar = this;
	var mvAry = [];
	mvAry[mvAry.length]  = '<div name="calendarForm" style="margin: 0px;">';
	mvAry[mvAry.length]  = '<table width="100%" border="0" cellpadding="0" cellspacing="1">';
	mvAry[mvAry.length]  = '<tr>';
	mvAry[mvAry.length]  = '<th align="left" width="1%"><input style="border: 1px solid ' + calendar.colors["input_border"] + ';background-color:' + calendar.colors["input_bg"] + ';width:16px;height:20px;" name="prevMonth" type="button" id="prevMonth" value="&lt;" /></th>';
	mvAry[mvAry.length]  = '<th align="center" width="98%" nowrap="nowrap"><select id="calendarYear" style="font-size:12px;"></select><select id="calendarMonth" style="font-size:12px;"></select></th>';
	mvAry[mvAry.length]  = '<th align="right" width="1%"><input style="border: 1px solid ' + calendar.colors["input_border"] + ';background-color:' + calendar.colors["input_bg"] + ';width:16px;height:20px;" name="nextMonth" type="button" id="nextMonth" value="&gt;" /></th>';
	mvAry[mvAry.length]  = '</tr>';
	mvAry[mvAry.length]  = '</table>';
	mvAry[mvAry.length]  = '<table id="calendarTable" width="100%" style="border:0 solid #ccc;background-color:#fff" border="0" cellpadding="3" cellspacing="1">';
	mvAry[mvAry.length]  = '<tr>';
	for(var i = 0; i< 7; i++) {
		mvAry[mvAry.length]  = '<th style="font-weight:normal;background-color:' + calendar.colors["tr_bg"] + ';color:' + calendar.colors["tr_word"] + ';">' + Calendar.language["weeks"][this.lang][i] + '</th>';
	}
	mvAry[mvAry.length]  = '</tr>';
	for(var i = 0; i< 6;i++) {
		mvAry[mvAry.length]  = '<tr align="center">';
		for(var j = 0; j< 7; j++) {
			if (j == 0) {
			mvAry[mvAry.length]  = '<td style="cursor:pointer;color:' + calendar.colors["sun_word"] + ';"></td>';
			} else if(j == 6) {
			mvAry[mvAry.length]  = '<td style="cursor:pointer;color:' + calendar.colors["sat_word"] + ';"></td>';
			} else {
			mvAry[mvAry.length]  = '<td style="cursor:pointer;"></td>';
			}
		}
		mvAry[mvAry.length]  = '</tr>';
	}
	mvAry[mvAry.length]  = '<tr style="background-color:' + calendar.colors["input_bg"] + ';">';
	mvAry[mvAry.length]  = '<th colspan="2"><input name="calendarClear" type="button" id="calendarClear" value="' + Calendar.language["clear"][this.lang] + '" style="cursor:pointer;border: 1px solid ' + calendar.colors["input_border"] + ';background-color:' + calendar.colors["input_bg"] + ';width:100%;height:20px;font-size:12px;"/></th>';
	mvAry[mvAry.length]  = '<th colspan="3"><input name="calendarToday" type="button" id="calendarToday" value="' + Calendar.language["today"][this.lang] + '" style="cursor:pointer;border: 1px solid ' + calendar.colors["input_border"] + ';background-color:' + calendar.colors["input_bg"] + ';width:100%;height:20px;font-size:12px;"/></th>';
	mvAry[mvAry.length]  = '<th colspan="2"><input name="calendarClose" type="button" id="calendarClose" value="' + Calendar.language["close"][this.lang] + '" style="cursor:pointer;border: 1px solid ' + calendar.colors["input_border"] + ';background-color:' + calendar.colors["input_bg"] + ';width:100%;height:20px;font-size:12px;"/></th>';
	mvAry[mvAry.length]  = '</tr>';
	mvAry[mvAry.length]  = '</table>';
	mvAry[mvAry.length]  = '</div>';
	this.panel.innerHTML = mvAry.join("");
	var obj = this.getElementById("prevMonth");
	obj.onclick = function() {
		calendar.goPrevMonth(calendar);
	};
	obj.onblur = function() {
		calendar.onblur();
	};
	this.prevMonth= obj;
	obj = this.getElementById("nextMonth");
	obj.onclick = function() {
		calendar.goNextMonth(calendar);
	};
	obj.onblur = function() {
		calendar.onblur();
	};
	this.nextMonth= obj;
	obj = this.getElementById("calendarClear");
	obj.onclick = function() {
		calendar.dateControl.value = "";
		calendar.hide();
	};
	obj.onblur = function() {
		calendar.onblur();
	};
	this.calendarClear = obj;
	obj = this.getElementById("calendarClose");
	obj.onclick = function() {
		calendar.hide();
	};
	this.calendarClose = obj;
	obj = this.getElementById("calendarYear");
	obj.onchange = function() {
		calendar.update(calendar);
	};
	obj.onblur = function() {
		calendar.onblur();
	};
	this.calendarYear = obj;
	obj = this.getElementById("calendarMonth");
	obj.onchange = function() {
		calendar.update(calendar);
	};
	obj.onblur = function() {
		calendar.onblur();
	};
	this.calendarMonth = obj;
	obj = this.getElementById("calendarToday");
	obj.onclick = function() {
		var today = new Date();
		calendar.date = today;
		calendar.year = today.getFullYear();
		calendar.month = today.getMonth();
		calendar.changeSelect();
		calendar.bindData();
		calendar.dateControl.value = today.format(calendar.dateFormatStyle);
		calendar.hide();
	};
	this.calendarToday = obj;
};
Calendar.prototype.bindYear = function() {
	var cy = this.calendarYear;//2006-12-01 由寒羽枫修改
	cy.length = 0;
	for (var i = this.beginYear; i<= this.endYear; i++) {
		cy.options[cy.length] = new Option(i + Calendar.language["year"][this.lang], i);
	}
};
//月份下拉框绑定数�?
Calendar.prototype.bindMonth = function() {
	var cm = this.calendarMonth;//2006-12-01 由寒羽枫修改
	cm.length = 0;
	for (var i = 0; i< 12; i++) {
		cm.options[cm.length] = new Option(Calendar.language["months"][this.lang][i], i);
	}
};
//向前一�?
Calendar.prototype.goPrevMonth = function(e) {
	if (this.year == this.beginYear && this.month == 0) {
		return;
	}
	this.month--;
	if (this.month == -1) {
		this.year--;
		this.month = 11;
	}
	this.date = new Date(this.year, this.month, 1);
	this.changeSelect();
	this.bindData();
};
//向后一�?
Calendar.prototype.goNextMonth = function(e) {
	if (this.year == this.endYear && this.month == 11) {
		return;
	}
	this.month++;
	if (this.month == 12) {
		this.year++;
		this.month = 0;
	}
	this.date = new Date(this.year, this.month, 1);
	this.changeSelect();
	this.bindData();
};
//改变SELECT选中状�?
Calendar.prototype.changeSelect = function() {
	var cy = this.calendarYear;//2006-12-01 由寒羽枫修改
	var cm = this.calendarMonth;
	for (var i= 0; i< cy.length; i++) {
		if (cy.options[i].value == this.date.getFullYear()) {
			cy[i].selected = true;
			break;
		}
	}
	for (var i= 0; i< cm.length; i++) {
		if (cm.options[i].value == this.date.getMonth()) {
			cm[i].selected = true;
			break;
		}
	}
};
//更新年、月
Calendar.prototype.update = function(e) {
	this.year  = e.calendarYear.options[e.calendarYear.selectedIndex].value;//2006-12-01 由寒羽枫修改
	this.month = e.calendarMonth.options[e.calendarMonth.selectedIndex].value;
	this.date = new Date(this.year, this.month, 1);
	this.changeSelect();
	this.bindData();
};
//绑定数据到月视图
Calendar.prototype.bindData = function() {
	var calendar = this;
	var dateArray = this.getMonthViewArray(this.date.getFullYear(), this.date.getMonth());
	var tds = this.getElementById("calendarTable").getElementsByTagName("td");
	for(var i = 0; i< tds.length; i++) {
		tds[i].style.backgroundColor = calendar.colors["td_bg_out"];
		tds[i].onclick = function() {return;};
		tds[i].onmouseover = function() {return;};
		tds[i].onmouseout = function() {return;};
		if (i > dateArray.length - 1) break;
		if( tds[i].innerText ) {
			tds[i].innerText = dateArray[i]=='&nbsp;'?' ':dateArray[i];
		}
		else {
			tds[i].innerHTML = dateArray[i];
		}
		if (dateArray[i] != "&nbsp;") {
			tds[i].onclick = function() {
				if (calendar.dateControl != null) {
					calendar.dateControl.value = new Date(calendar.date.getFullYear(),calendar.date.getMonth(),this.innerHTML).format(calendar.dateFormatStyle);
				}
				calendar.hide();
			};
			tds[i].onmouseover = function() {
				this.style.backgroundColor = calendar.colors["td_bg_over"];
			};
			tds[i].onmouseout = function() {
				this.style.backgroundColor = calendar.colors["td_bg_out"];
			};
			if (new Date().format(calendar.dateFormatStyle) == new Date(calendar.date.getFullYear(),calendar.date.getMonth(),dateArray[i]).format(calendar.dateFormatStyle)) {
				tds[i].style.backgroundColor = calendar.colors["cur_bg"];
				tds[i].onmouseover = function() {
					this.style.backgroundColor = calendar.colors["td_bg_over"];
				};
				tds[i].onmouseout = function() {
					this.style.backgroundColor = calendar.colors["cur_bg"];
				};
			//continue; //若不想当天单元格的背景被下面的覆盖，请取消注�?�? 2006-12-03 寒羽枫添�?
			}
			if (calendar.dateControl != null && calendar.dateControl.value == new Date(calendar.date.getFullYear(),calendar.date.getMonth(),dateArray[i]).format(calendar.dateFormatStyle)) {
				tds[i].style.backgroundColor = calendar.colors["sel_bg"];
				tds[i].onmouseover = function() {
					this.style.backgroundColor = calendar.colors["td_bg_over"];
				};
				tds[i].onmouseout = function() {
					this.style.backgroundColor = calendar.colors["sel_bg"];
				};
			}
		}
	}
};
//根据年、月得到月视图数�?数组形式)
Calendar.prototype.getMonthViewArray = function(y, m) {
	var mvArray = [];
	var dayOfFirstDay = new Date(y, m, 1).getDay();
	var daysOfMonth = new Date(y, m + 1, 0).getDate();
	for (var i = 0; i< 42; i++) {
		mvArray[i] = "&nbsp;";
	}
	for (var i = 0; i< daysOfMonth; i++) {
		mvArray[i + dayOfFirstDay] = i + 1;
	}
	return mvArray;
};
//扩展 document.getElementById(id) 多浏览器兼容�?from meizz tree source
Calendar.prototype.getElementById = function(id) {
	if (typeof(id) != "string" || id == "") return null;
	if (document.getElementById) return document.getElementById(id);
	if (document.all) return document.all(id);
	try {return eval(id);} catch(e) { return null;}
};
//扩展 object.getElementsByTagName(tagName)
Calendar.prototype.getElementsByTagName = function(object, tagName) {
	if (document.getElementsByTagName) return document.getElementsByTagName(tagName);
	if (document.all) return document.all.tags(tagName);
};
//取得HTML控件绝对位置
Calendar.prototype.getAbsPoint = function(e) {
	var x = e.offsetLeft;
	var y = e.offsetTop;
	while(e = e.offsetParent) {
		x += e.offsetLeft;
		y += e.offsetTop;
	}
	return {"x": x, "y": y};
};
//显示日历
Calendar.prototype.show = function(dateObj, popControl) {
	if (dateObj == null) {
		throw new Error("arguments[0] is necessary")
	}
	this.dateControl = dateObj;
	this.date = (dateObj.value.length > 0) ? new Date(dateObj.value.toDate(this.dateFormatStyle)) : new Date() ;//2006-12-03 寒羽枫添�?�?若为空则显示当前月份
	this.year = this.date.getFullYear();
	this.month = this.date.getMonth();
	this.changeSelect();
	this.bindData();
	if (popControl == null) {
		popControl = dateObj;
	}
	/*var xy = this.getAbsPoint(popControl);
	this.panel.style.left = xy.x -25 + "px";
	this.panel.style.top = (xy.y + dateObj.offsetHeight) + "px";*/
	var xy = this.getAbsPoint(popControl);
	this.panel.style.left = xy.x + "px";
	//this.panel.style.top = xy.y + "px";
	this.panel.style.marginTop = dateObj.offsetHeight + "px";
	this.panel.style.display = "";
	this.container.style.display = "";
	this.dateControl.parentNode.insertBefore(this.container,this.dateControl);
	var onblurfun = dateObj.onblur;
	dateObj.onblur = function() {
		if(onblurfun) onblurfun();
		calendar.onblur();
	};
	this.container.onmouseover = function() {isFocus=true;};
	this.container.onmouseout = function() {isFocus=false;};
};
//隐藏日历
Calendar.prototype.hide = function() {
	this.panel.style.display = "none";
	this.container.style.display = "none";
	isFocus=false;
};
//焦点转移时隐藏日�?�?由寒羽枫 2006-06-25 添加
Calendar.prototype.onblur = function() {
	if(!isFocus) {
		this.hide();
	}
};
function createCale() {
	if( document.getElementById('ContainerPanel') ) return;
	var d = document.createElement('div');
	d.setAttribute('id','ContainerPanel');
	d.style.display = 'none';
	t = '<div id="calendarPanel" style="position: absolute;display: none;z-index: 9999;background-color: #fff;border: 1px solid #ccc;width:175px;font-size:12px;">';
	if(document.all) {
		t += '<iframe style="position:absolute;z-index:2000;width:expression(this.previousSibling.offsetWidth);height:expression(this.previousSibling.offsetHeight);left:expression(this.previousSibling.offsetLeft);top:expression(this.previousSibling.offsetTop);display:expression(this.previousSibling.style.display);" scrolling="no" frameborder="no"></iframe>';
	}
	t += '</div>';
	d.innerHTML = t;
	document.body.insertBefore(d, document.body.firstChild);
}
window.onload = createCale;