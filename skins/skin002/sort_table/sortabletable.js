/*----------------------------------------------------------------------------\
|                            Sortable Table 1.12                              |
|-----------------------------------------------------------------------------|
|                         Created by Erik Arvidsson                           |
|                  (http://webfx.eae.net/contact.html#erik)                   |
|                      For WebFX (http://webfx.eae.net/)                      |
|-----------------------------------------------------------------------------|
| A DOM 1 based script that allows an ordinary HTML table to be sortable.     |
|-----------------------------------------------------------------------------|
|                  Copyright (c) 1998 - 2006 Erik Arvidsson                   |
|-----------------------------------------------------------------------------|
| Licensed under the Apache License, Version 2.0 (the "License"); you may not |
| use this file except in compliance with the License.  You may obtain a copy |
| of the License at http://www.apache.org/licenses/LICENSE-2.0                |
| - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - |
| Unless  required  by  applicable law or  agreed  to  in  writing,  software |
| distributed under the License is distributed on an  "AS IS" BASIS,  WITHOUT |
| WARRANTIES OR  CONDITIONS OF ANY KIND,  either express or implied.  See the |
| License  for the  specific language  governing permissions  and limitations |
| under the License.                                                          |
|-----------------------------------------------------------------------------|
| 2003-01-10 | First version                                                  |
| 2003-01-19 | Minor changes to the date parsing                              |
| 2003-01-28 | JScript 5.0 fixes (no support for 'in' operator)               |
| 2003-02-01 | Sloppy typo like error fixed in getInnerText                   |
| 2003-07-04 | Added workaround for IE cellIndex bug.                         |
| 2003-11-09 | The bDescending argument to sort was not correctly working     |
|            | Using onclick DOM0 event if no support for addEventListener    |
|            | or attachEvent                                                 |
| 2004-01-13 | Adding addSortType and removeSortType which makes it a lot     |
|            | easier to add new, custom sort types.                          |
| 2004-01-27 | Switch to use descending = false as the default sort order.    |
|            | Change defaultDescending to suit your needs.                   |
| 2004-03-14 | Improved sort type None look and feel a bit                    |
| 2004-08-26 | Made the handling of tBody and tHead more flexible. Now you    |
|            | can use another tHead or no tHead, and you can chose some      |
|            | other tBody.                                                   |
| 2006-04-25 | Changed license to Apache Software License 2.0                 |  
|-----------------------------------------------------------------------------|
| Created 2003-01-10 | All changes are in the log above. | Updated 2006-04-25 |
\----------------------------------------------------------------------------*/


function SortableTable(oTable, oSortTypes) {

	this.sortTypes = oSortTypes || [];

	this.sortColumn = null;
	this.descending = null;

	var oThis = this;
	this._headerOnclick = function (e) {
		oThis.headerOnclick(e);
	};

	if (oTable) {
		this.setTable( oTable );
		this.document = oTable.ownerDocument || oTable.document;
	}
	else {
		this.document = document;
	}


	// only IE needs this
	var win = this.document.defaultView || this.document.parentWindow;
	this._onunload = function () {
		oThis.destroy();
	};
	if (win && typeof win.attachEvent != "undefined") {
		win.attachEvent("onunload", this._onunload);
	}
	
	setCorFocus(); //função está no arquivo default.js
}

SortableTable.gecko = navigator.product == "Gecko";
SortableTable.msie = /msie/i.test(navigator.userAgent);
// Mozilla is faster when doing the DOM manipulations on
// an orphaned element. MSIE is not
SortableTable.removeBeforeSort = SortableTable.gecko;

SortableTable.prototype.onsort = function () {};

// default sort order. true -> descending, false -> ascending
SortableTable.prototype.defaultDescending = false;

// shared between all instances. This is intentional to allow external files
// to modify the prototype
SortableTable.prototype._sortTypeInfo = {};

SortableTable.prototype.setTable = function (oTable) {
	if ( this.tHead )
		this.uninitHeader();
	this.element = oTable;
	this.setTHead( oTable.tHead );
	this.setTBody( oTable.tBodies[0] );
};

SortableTable.prototype.setTHead = function (oTHead) {
	if (this.tHead && this.tHead != oTHead )
		this.uninitHeader();
	this.tHead = oTHead;
	this.initHeader( this.sortTypes );
};

SortableTable.prototype.setTBody = function (oTBody) {
	this.tBody = oTBody;
};

SortableTable.prototype.setSortTypes = function ( oSortTypes ) {
	if ( this.tHead )
		this.uninitHeader();
	this.sortTypes = oSortTypes || [];
	if ( this.tHead )
		this.initHeader( this.sortTypes );
};

// adds arrow containers and events
// also binds sort type to the header cells so that reordering columns does
// not break the sort types
SortableTable.prototype.initHeader = function (oSortTypes) {
	if (!this.tHead) return;
	var cells = this.tHead.rows[0].cells;
	var doc = this.tHead.ownerDocument || this.tHead.document;
	this.sortTypes = oSortTypes || [];
	var l = cells.length;
	var img, c;
	for (var i = 0; i < l; i++) {
		c = cells[i];
		if (this.sortTypes[i] != null && this.sortTypes[i] != "None") {
			img = doc.createElement("IMG");
			img.src = document.getElementById('root').value + "skins/skin001/sort_table/images/blank.png";
			c.appendChild(img);
			if (this.sortTypes[i] != null)
				c._sortType = this.sortTypes[i];
			if (typeof c.addEventListener != "undefined")
				c.addEventListener("click", this._headerOnclick, false);
			else if (typeof c.attachEvent != "undefined")
				c.attachEvent("onclick", this._headerOnclick);
			else
				c.onclick = this._headerOnclick;
		}
		else
		{
			c.setAttribute( "_sortType", oSortTypes[i] );
			c._sortType = "None";
		}
	}
	this.updateHeaderArrows();
};

// remove arrows and events
SortableTable.prototype.uninitHeader = function () {
	if (!this.tHead) return;
	var cells = this.tHead.rows[0].cells;
	var l = cells.length;
	var c;
	for (var i = 0; i < l; i++) {
		c = cells[i];
		if (c._sortType != null && c._sortType != "None") {
			c.removeChild(c.lastChild);
			if (typeof c.removeEventListener != "undefined")
				c.removeEventListener("click", this._headerOnclick, false);
			else if (typeof c.detachEvent != "undefined")
				c.detachEvent("onclick", this._headerOnclick);
			c._sortType = null;
			c.removeAttribute( "_sortType" );
		}
	}
};

SortableTable.prototype.updateHeaderArrows = function () {
	if (!this.tHead) return;
	var cells = this.tHead.rows[0].cells;
	var l = cells.length;
	var img;
	for (var i = 0; i < l; i++) {
		if (cells[i]._sortType != null && cells[i]._sortType != "None") {
			img = cells[i].lastChild;
			if (i == this.sortColumn)
				img.className = "sort-arrow " + (this.descending ? "descending" : "ascending");
			else
				img.className = "sort-arrow";
		}
	}
};

SortableTable.prototype.headerOnclick = function (e) {
	// find TH element
	var el = e.target || e.srcElement;

	while (el.tagName != "TH")
	{
		el = el.parentNode;
	}

	this.sort(SortableTable.msie ? SortableTable.getCellIndex(el) : el.cellIndex);
};

// IE returns wrong cellIndex when columns are hidden
SortableTable.getCellIndex = function (oTd) {
	var cells = oTd.parentNode.childNodes
	var l = cells.length;
	var i;
	for (i = 0; cells[i] != oTd && i < l; i++)
		;
	return i;
};

SortableTable.prototype.getSortType = function (nColumn) {
	return this.sortTypes[nColumn] || "String";
};

// only nColumn is required
// if bDescending is left out the old value is taken into account
// if sSortType is left out the sort type is found from the sortTypes array

SortableTable.prototype.sort = function (nColumn, bDescending, sSortType) {
	if (!this.tBody) return;
	if (sSortType == null)
		sSortType = this.getSortType(nColumn);

	// exit if None
	if (sSortType == "None")
		return;

	if (bDescending == null) {
		if (this.sortColumn != nColumn)
			this.descending = this.defaultDescending;
		else
			this.descending = !this.descending;
	}
	else
		this.descending = bDescending;

	this.sortColumn = nColumn;

	if (typeof this.onbeforesort == "function")
		this.onbeforesort();

	var f = this.getSortFunction(sSortType, nColumn);
	var a = this.getCache(sSortType, nColumn);
	var tBody = this.tBody;

	a.sort(f);

	if (this.descending)
		a.reverse();

	if (SortableTable.removeBeforeSort) {
		// remove from doc
		var nextSibling = tBody.nextSibling;
		var p = tBody.parentNode;
		p.removeChild(tBody);
	}

	// insert in the new order
	var l = a.length;
	for (var i = 0; i < l; i++)
		tBody.appendChild(a[i].element);

	if (SortableTable.removeBeforeSort) {
		// insert into doc
		p.insertBefore(tBody, nextSibling);
	}

	this.updateHeaderArrows();

	this.destroyCache(a);

	if (typeof this.onsort == "function")
		this.onsort();
		
		
	//$(this.element).stickyTableHeaders();	
};

SortableTable.prototype.asyncSort = function (nColumn, bDescending, sSortType) {
	var oThis = this;
	this._asyncsort = function () {
		oThis.sort(nColumn, bDescending, sSortType);
	};
	window.setTimeout(this._asyncsort, 1);
};

SortableTable.prototype.getCache = function (sType, nColumn) {
	if (!this.tBody) return [];
	var rows = this.tBody.rows;
	var l = rows.length;
	var a = new Array(l);
	var r;
	for (var i = 0; i < l; i++) {
		r = rows[i];
		a[i] = {
			value:		this.getRowValue(r, sType, nColumn),
			element:	r
		};
	};
	return a;
};

SortableTable.prototype.destroyCache = function (oArray) {
	var l = oArray.length;
	for (var i = 0; i < l; i++) {
		oArray[i].value = null;
		oArray[i].element = null;
		oArray[i] = null;
	}
};

SortableTable.prototype.getRowValue = function (oRow, sType, nColumn) {
	// if we have defined a custom getRowValue use that
	if (this._sortTypeInfo[sType] && this._sortTypeInfo[sType].getRowValue)
		return this._sortTypeInfo[sType].getRowValue(oRow, nColumn);

	var s;
	var c = oRow.cells[nColumn];
	if (typeof c.innerText != "undefined")
		s = c.innerText;
	else
		s = SortableTable.getInnerText(c);
	return this.getValueFromString(s, sType);
};

SortableTable.getInnerText = function (oNode) {
	var s = "";
	var cs = oNode.childNodes;
	var l = cs.length;
	for (var i = 0; i < l; i++) {
		switch (cs[i].nodeType) {
			case 1: //ELEMENT_NODE
				s += SortableTable.getInnerText(cs[i]);
				break;
			case 3:	//TEXT_NODE
				s += cs[i].nodeValue;
				break;
		}
	}
	return s;
};

SortableTable.prototype.getValueFromString = function (sText, sType) {
	if (this._sortTypeInfo[sType])
		return this._sortTypeInfo[sType].getValueFromString( sText );
	return sText;
	/*
	switch (sType) {
		case "Number":
			return Number(sText);
		case "CaseInsensitiveString":
			return sText.toUpperCase();
		case "Date":
			var parts = sText.split("-");
			var d = new Date(0);
			d.setFullYear(parts[0]);
			d.setDate(parts[2]);
			d.setMonth(parts[1] - 1);
			return d.valueOf();
	}
	return sText;
	*/
	};

SortableTable.prototype.getSortFunction = function (sType, nColumn) {
	if (this._sortTypeInfo[sType])
		return this._sortTypeInfo[sType].compare;
	return SortableTable.basicCompare;
};

SortableTable.prototype.destroy = function () {
	this.uninitHeader();
	var win = this.document.parentWindow;
	if (win && typeof win.detachEvent != "undefined") {	// only IE needs this
		win.detachEvent("onunload", this._onunload);
	}
	this._onunload = null;
	this.element = null;
	this.tHead = null;
	this.tBody = null;
	this.document = null;
	this._headerOnclick = null;
	this.sortTypes = null;
	this._asyncsort = null;
	this.onsort = null;
};

// Adds a sort type to all instance of SortableTable
// sType : String - the identifier of the sort type
// fGetValueFromString : function ( s : string ) : T - A function that takes a
//    string and casts it to a desired format. If left out the string is just
//    returned
// fCompareFunction : function ( n1 : T, n2 : T ) : Number - A normal JS sort
//    compare function. Takes two values and compares them. If left out less than,
//    <, compare is used
// fGetRowValue : function( oRow : HTMLTRElement, nColumn : int ) : T - A function
//    that takes the row and the column index and returns the value used to compare.
//    If left out then the innerText is first taken for the cell and then the
//    fGetValueFromString is used to convert that string the desired value and type

SortableTable.prototype.addSortType = function (sType, fGetValueFromString, fCompareFunction, fGetRowValue) {
	this._sortTypeInfo[sType] = {
		type:				sType,
		getValueFromString:	fGetValueFromString || SortableTable.idFunction,
		compare:			fCompareFunction || SortableTable.basicCompare,
		getRowValue:		fGetRowValue
	};
};

// this removes the sort type from all instances of SortableTable
SortableTable.prototype.removeSortType = function (sType) {
	delete this._sortTypeInfo[sType];
};

SortableTable.basicCompare = function compare(n1, n2) {
	if (n1.value < n2.value)
		return -1;
	if (n2.value < n1.value)
		return 1;
	return 0;
};

SortableTable.idFunction = function (x) {
	return x;
};

SortableTable.toUpperCase = function (s) {
	return s.toUpperCase();
};

SortableTable.toDate = function (s) {
	// FORMATO YYY-MM-DD
	if((s.length == "") || (s.length == 13))
	{
		s = "1900-12-31";
	}	

	var parts = s.split("-");
	var d = new Date(0);
	d.setFullYear(parts[0]);
	d.setDate(parts[2]);
	d.setMonth(parts[1] - 1);
	return d.valueOf();
};

SortableTable.toDateBR = function (s) {
	// FORMATO DD/MM/YYYY
	if((s.length == "") || (s.length == 13))
	{
		s = "31/12/1900";
	}	
	
	var parts = s.split("/");
	var d = new Date(0);
	d.setFullYear(parts[2]);
	d.setDate(parts[0]);
	d.setMonth(parts[1] - 1);
	return d.valueOf();
};

SortableTable.toDateTimeBR = function (s) {
	// FORMATO DD/MM/YYYY HH:MI:SS
	if((s.length == "") || (s.length == 13))
	{
		s = "31/12/1900 00:00:00";
	}	
	
	var ar_tmp = s.split(" ");
	var ar_data = ar_tmp[0].split("/");
	if(ar_tmp.length > 1)
	{
		var ar_time = ar_tmp[1].split(":");
	}
	else
	{
		var ar_time = new Array();
	}
		
	var d = new Date(0);
	d.setFullYear(ar_data[2]);
	d.setDate(ar_data[0]);
	d.setMonth(ar_data[1] - 1);
	
	if(ar_time.length == 3)
	{
		d.setHours(ar_time[0],ar_time[1],ar_time[2],0);
	}
	else if(ar_time.length == 2)
	{
		d.setHours(ar_time[0],ar_time[1],0,0);
	}
		
	return d.valueOf();
};

SortableTable.toTimeBR = function (s) {
	// FORMATO  HH:MI:SS
	
	if((s.length == "") || (s.length < 8))
	{
		s = "00:00:00";
	}	

	var ar_time = s.split(":");
	
	var d = new Date(0);
	d.setHours(ar_time[0],ar_time[1],ar_time[2],0);

	return d.valueOf();
};

SortableTable.toRE = function (s) {
	// FORMATO 00/000000/00
	if(s.length == "")
	{
		s = "0/0/0";
	}	
	
	var ar_tmp = s.split("/");
	var cd_empresa            = ar_tmp[0];
	var cd_registro_empregado = ar_tmp[1];
	var seq_dependencia       = ar_tmp[2];
	
	var qt_zero = (2 - cd_empresa.length);
	if (qt_zero > 0)
	{
		for(i=0; i < qt_zero; i++)
		{
			cd_empresa = "0" + cd_empresa;
		}
	}
	
	var qt_zero = (6 - cd_registro_empregado.length);
	if (qt_zero > 0)
	{
		for(i=0; i < qt_zero; i++)
		{
			cd_registro_empregado = "0" + cd_registro_empregado;
		}
	}	

	var qt_zero = (2 - seq_dependencia.length);
	if (qt_zero > 0)
	{
		for(i=0; i < qt_zero; i++)
		{
			seq_dependencia = "0" + seq_dependencia;
		}
	}
	
	s = cd_empresa + cd_registro_empregado + seq_dependencia;
	
	return s;
};

SortableTable.toValorBR = function (s)
{
	// FORMATO 1.000.000.000,00
	if(s.length == "")
	{
		s = "9999999999999999999999999999999";
	}
	
	var n = s.replace(".","");
	var fl_achou = n.indexOf(".");
	while (fl_achou != -1)
	{
		n = n.replace(".","");
		fl_achou = n.indexOf(".");
	}	
	
    n = n.replace(",", ".");

	return parseFloat( n );
}


SortableTable.toNumber = function (s)
{
	if(s.length == "")
	{
		s = "9999999999999999999999999999999";
	}
	
	return parseInt( s );
}

SortableTable.toPercentual = function (s)
{
	// FORMATO 1.000.000.000,00
	if(s.length == "")
	{
		s = "9999999999999999999999999999999";
	}
	
	s = s.replace(" ","");
	s = s.replace("%","");
	
	var n = s.replace(".","");
	var fl_achou = n.indexOf(".");
	while (fl_achou != -1)
	{
		n = n.replace(".","");
		fl_achou = n.indexOf(".");
	}	
	
    n = n.replace(",", ".");

	return parseFloat( n );
}

SortableTable.toMesAno = function (s)
{
	if(s.length == "")
	{
		s = "99/9999";
	}
	
	var ar_tmp = s.split("/");
	var n = ar_tmp[1] + "/" + ar_tmp[0];
	return n;
}

/*
Number:                Somente números inteiros
NumberFloatBR:         Números de ponto flutuante (1.000.000,00)
String:                String considerarando maiúscula e minúscula
CaseInsensitiveString: String sem considerar maiúscula e minúscula
Date:                  Data no formato americano (2009-10-01)
DateBR:                Data no formato brasileiro (01/10/2009)
DateTimeBR:            Data e Tempo formato brasileiro (01/10/2009 10:30:20)
TimeBR:                Tempo no formato brasileiro (10:30:20)
RE:                    Empresa + Registro empregado + Sequência (00/000000/00)
Percentual:            Percentual (100%)
MesAno:                Campos com Mês/Ano (05/2012)
*/
// add sort types
//SortableTable.prototype.addSortType("Number", Number);
SortableTable.prototype.addSortType("Number", SortableTable.toNumber);
SortableTable.prototype.addSortType("NumberFloatBR", SortableTable.toValorBR);
SortableTable.prototype.addSortType("String");
SortableTable.prototype.addSortType("CaseInsensitiveString", SortableTable.toUpperCase);
SortableTable.prototype.addSortType("Date", SortableTable.toDate);
SortableTable.prototype.addSortType("DateBR", SortableTable.toDateBR);
SortableTable.prototype.addSortType("DateTimeBR", SortableTable.toDateTimeBR);
SortableTable.prototype.addSortType("TimeBR", SortableTable.toTimeBR);
SortableTable.prototype.addSortType("RE", SortableTable.toRE);
SortableTable.prototype.addSortType("Percentual", SortableTable.toPercentual);
SortableTable.prototype.addSortType("MesAno", SortableTable.toMesAno);
// None is a special case




// ZEBRAR LINHAS
	function addClassName(el, sClassName) {
		var s = el.className;
		var p = s.split(" ");
		var l = p.length;
		for (var i = 0; i < l; i++) {
			if (p[i] == sClassName)
				return;
		}
		p[p.length] = sClassName;
		el.className = p.join(" ").replace( /(^\s+)|(\s+$)/g, "" );
	}

	function removeClassName(el, sClassName) {
		var s = el.className;
		var p = s.split(" ");
		var np = [];
		var l = p.length;
		var j = 0;
		for (var i = 0; i < l; i++) {
			if (p[i] != sClassName)
				np[j++] = p[i];
		}
		el.className = np.join(" ").replace( /(^\s+)|(\s+$)/g, "" );
	}
	
//SELECIONA
var sort_class_anterior = "";
function sortSetClassOver(obj)
{
	sort_class_anterior = obj.className;
	obj.className = 'sort-selecionado';
}
function sortSetClassOut(obj)
{
	obj.className = sort_class_anterior;
}	