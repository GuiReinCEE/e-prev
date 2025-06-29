

YAHOO.widget.AutoComplete=function(inputEl,containerEl,oDataSource,oConfigs){if(inputEl&&containerEl&&oDataSource){if(oDataSource.getResults){this.dataSource=oDataSource;}

else{return;}

if(YAHOO.util.Dom.inDocument(inputEl)){if(typeof inputEl=="string"){this._sName=inputEl+YAHOO.widget.AutoComplete._nIndex;this._oTextbox=document.getElementById(inputEl);}

else{this._sName=(inputEl.id)?inputEl.id+YAHOO.widget.AutoComplete._nIndex:"yac_inputEl"+YAHOO.widget.AutoComplete._nIndex;this._oTextbox=inputEl;}}

else{return;}

if(YAHOO.util.Dom.inDocument(containerEl)){if(typeof containerEl=="string"){this._oContainer=document.getElementById(containerEl);}

else{this._oContainer=containerEl;}}

else{return;}

if(typeof oConfigs=="object"){for(var sConfig in oConfigs){if(sConfig){this[sConfig]=oConfigs[sConfig];}}}

var oSelf=this;var oTextbox=this._oTextbox;var oContainer=this._oContainer;YAHOO.util.Event.addListener(oTextbox,'keyup',oSelf._onTextboxKeyUp,oSelf);YAHOO.util.Event.addListener(oTextbox,'keydown',oSelf._onTextboxKeyDown,oSelf);YAHOO.util.Event.addListener(oTextbox,'keypress',oSelf._onTextboxKeyPress,oSelf);YAHOO.util.Event.addListener(oTextbox,'focus',oSelf._onTextboxFocus,oSelf);YAHOO.util.Event.addListener(oTextbox,'blur',oSelf._onTextboxBlur,oSelf);YAHOO.util.Event.addListener(oContainer,'mouseover',oSelf._onContainerMouseover,oSelf);YAHOO.util.Event.addListener(oContainer,'mouseout',oSelf._onContainerMouseout,oSelf);YAHOO.util.Event.addListener(oContainer,'scroll',oSelf._onContainerScroll,oSelf);if(oTextbox.form&&this.allowBrowserAutocomplete){YAHOO.util.Event.addListener(oTextbox.form,'submit',oSelf._onFormSubmit,oSelf);}

this.textboxFocusEvent=new YAHOO.util.CustomEvent("textboxFocus",this);this.textboxKeyEvent=new YAHOO.util.CustomEvent("textboxKey",this);this.dataRequestEvent=new YAHOO.util.CustomEvent("dataRequest",this);this.dataReturnEvent=new YAHOO.util.CustomEvent("dataReturn",this);this.dataErrorEvent=new YAHOO.util.CustomEvent("dataError",this);this.containerExpandEvent=new YAHOO.util.CustomEvent("containerExpand",this);this.typeAheadEvent=new YAHOO.util.CustomEvent("typeAhead",this);this.itemMouseOverEvent=new YAHOO.util.CustomEvent("itemMouseOver",this);this.itemMouseOutEvent=new YAHOO.util.CustomEvent("itemMouseOut",this);this.itemArrowToEvent=new YAHOO.util.CustomEvent("itemArrowTo",this);this.itemArrowFromEvent=new YAHOO.util.CustomEvent("itemArrowFrom",this);this.itemSelectEvent=new YAHOO.util.CustomEvent("itemSelect",this);this.selectionEnforceEvent=new YAHOO.util.CustomEvent("selectionEnforce",this);this.containerCollapseEvent=new YAHOO.util.CustomEvent("containerCollapse",this);this.textboxBlurEvent=new YAHOO.util.CustomEvent("textboxBlur",this);oTextbox.setAttribute("autocomplete","off");this._initProps();}

else{}};YAHOO.widget.AutoComplete.prototype.dataSource=null;YAHOO.widget.AutoComplete.prototype.minQueryLength=1;YAHOO.widget.AutoComplete.prototype.maxResultsDisplayed=10;YAHOO.widget.AutoComplete.prototype.queryDelay=0.5;YAHOO.widget.AutoComplete.prototype.highlightClassName="highlight";YAHOO.widget.AutoComplete.prototype.delimChar=null;YAHOO.widget.AutoComplete.prototype.typeAhead=false;YAHOO.widget.AutoComplete.prototype.animHoriz=false;YAHOO.widget.AutoComplete.prototype.animVert=true;YAHOO.widget.AutoComplete.prototype.animSpeed=0.3;YAHOO.widget.AutoComplete.prototype.forceSelection=false;YAHOO.widget.AutoComplete.prototype.allowBrowserAutocomplete=true;YAHOO.widget.AutoComplete.prototype.getName=function(){return this._sName;};YAHOO.widget.AutoComplete.prototype.getListIds=function(){return this._aListIds;};YAHOO.widget.AutoComplete.prototype.setHeader=function(sHeader){if(sHeader){this._oHeader.innerHTML=sHeader;this._oHeader.style.display="block";}};YAHOO.widget.AutoComplete.prototype.setFooter=function(sFooter){if(sFooter){this._oFooter.innerHTML=sFooter;this._oFooter.style.display="block";}};YAHOO.widget.AutoComplete.prototype.useIFrame=false;YAHOO.widget.AutoComplete.prototype.formatResult=function(oResultItem,sQuery){var sResult=oResultItem[0];if(sResult){return sResult;}

else{return"";}};YAHOO.widget.AutoComplete.prototype.textboxFocusEvent=null;YAHOO.widget.AutoComplete.prototype.textboxKeyEvent=null;YAHOO.widget.AutoComplete.prototype.dataRequestEvent=null;YAHOO.widget.AutoComplete.prototype.dataReturnEvent=null;YAHOO.widget.AutoComplete.prototype.dataErrorEvent=null;YAHOO.widget.AutoComplete.prototype.containerExpandEvent=null;YAHOO.widget.AutoComplete.prototype.typeAheadEvent=null;YAHOO.widget.AutoComplete.prototype.itemMouseOverEvent=null;YAHOO.widget.AutoComplete.prototype.itemMouseOutEvent=null;YAHOO.widget.AutoComplete.prototype.itemArrowToEvent=null;YAHOO.widget.AutoComplete.prototype.itemArrowFromEvent=null;YAHOO.widget.AutoComplete.prototype.itemSelectEvent=null;YAHOO.widget.AutoComplete.prototype.selectionEnforceEvent=null;YAHOO.widget.AutoComplete.prototype.containerCollapseEvent=null;YAHOO.widget.AutoComplete.prototype.textboxBlurEvent=null;YAHOO.widget.AutoComplete._nIndex=0;YAHOO.widget.AutoComplete.prototype._sName=null;YAHOO.widget.AutoComplete.prototype._oTextbox=null;YAHOO.widget.AutoComplete.prototype._bFocused=true;YAHOO.widget.AutoComplete.prototype._oAnim=null;YAHOO.widget.AutoComplete.prototype._oContainer=null;YAHOO.widget.AutoComplete.prototype._bContainerOpen=false;YAHOO.widget.AutoComplete.prototype._bOverContainer=false;YAHOO.widget.AutoComplete.prototype._oIFrame=null;YAHOO.widget.AutoComplete.prototype._oContent=null;YAHOO.widget.AutoComplete.prototype._oHeader=null;YAHOO.widget.AutoComplete.prototype._oFooter=null;YAHOO.widget.AutoComplete.prototype._aListIds=null;YAHOO.widget.AutoComplete.prototype._nDisplayedItems=0;YAHOO.widget.AutoComplete.prototype._sCurQuery=null;YAHOO.widget.AutoComplete.prototype._sSavedQuery=null;YAHOO.widget.AutoComplete.prototype._oCurItem=null;YAHOO.widget.AutoComplete.prototype._bItemSelected=false;YAHOO.widget.AutoComplete.prototype._nKeyCode=null;YAHOO.widget.AutoComplete.prototype._nDelayID=-1;YAHOO.widget.AutoComplete.prototype._initProps=function(){var minQueryLength=this.minQueryLength;if(isNaN(minQueryLength)||(minQueryLength<1)){minQueryLength=1;}

var maxResultsDisplayed=this.maxResultsDisplayed;if(isNaN(this.maxResultsDisplayed)||(this.maxResultsDisplayed<1)){this.maxResultsDisplayed=10;}

var queryDelay=this.queryDelay;if(isNaN(this.queryDelay)||(this.queryDelay<0)){this.queryDelay=0.5;}

var aDelimChar=(this.delimChar)?this.delimChar:null;if(aDelimChar){if(typeof aDelimChar=="string"){this.delimChar=[aDelimChar];}

else if(aDelimChar.constructor!=Array){this.delimChar=null;}}

var animSpeed=this.animSpeed;if(this.animHoriz||this.animVert){if(isNaN(animSpeed)||(animSpeed<0)){animSpeed=0.3;}

if(!this._oAnim&&YAHOO.util.Anim){this._oAnim=new YAHOO.util.Anim(this._oContainer,{},animSpeed);}

else if(this._oAnim){this._oAnim.duration=animSpeed;}}

if(this.forceSelection&&this.delimChar){}

if(!this._aListIds){this._aListIds=[];}

if(!this._aListIds||(this.maxResultsDisplayed!=this._aListIds.length)){this._initContainer();}};YAHOO.widget.AutoComplete.prototype._initContainer=function(){this._aListIds=[];var aItemsMarkup=[];var sName=this._sName;var sPrefix=sName+"item";var sHeaderID=sName+"header";var sFooterID=sName+"footer";for(var i=this.maxResultsDisplayed-1;i>=0;i--){var sItemID=sPrefix+i;this._aListIds[i]=sItemID;aItemsMarkup.unshift("<li id='"+sItemID+"'></li>\n");}

var sList="<ul id='"+sName+"list'>"+

aItemsMarkup.join("")+"</ul>";var sContent=(this.useIFrame)?["<div id='",sName,"content'>","<div id='",sHeaderID,"' class='ac_hd'></div><div class='ac_bd'>",sList,"</div><div id='",sFooterID,"' class='ac_ft'></div>","</div><iframe id='",sName,"iframe' src='about:blank' frameborder='0' scrolling='no'>","</iframe>"]:["<div id='",sHeaderID,"' class='ac_hd'></div><div class='ac_bd'>",sList,"</div><div id='",sFooterID,"' class='ac_ft'></div>"];sContent=sContent.join("");this._oContainer.innerHTML=sContent;this._oHeader=document.getElementById(sHeaderID);this._oFooter=document.getElementById(sFooterID);if(this.useIFrame){this._oContent=document.getElementById(sName+"content");this._oIFrame=document.getElementById(sName+"iframe");this._oContent.style.position="relative";this._oIFrame.style.position="relative";this._oContent.style.zIndex=9050;}

this._oContainer.style.display="none";this._oHeader.style.display="none";this._oFooter.style.display="none";this._initItems();};YAHOO.widget.AutoComplete.prototype._initItems=function(){for(var i=this.maxResultsDisplayed-1;i>=0;i--){var oItem=document.getElementById(this._aListIds[i]);this._initItem(oItem,i);}};YAHOO.widget.AutoComplete.prototype._initItem=function(oItem,nItemIndex){var oSelf=this;oItem.style.display="none";oItem._nItemIndex=nItemIndex;oItem.mouseover=oItem.mouseout=oItem.onclick=null;YAHOO.util.Event.addListener(oItem,'mouseover',oSelf._onItemMouseover,oSelf);YAHOO.util.Event.addListener(oItem,'mouseout',oSelf._onItemMouseout,oSelf);YAHOO.util.Event.addListener(oItem,'click',oSelf._onItemMouseclick,oSelf);};YAHOO.widget.AutoComplete.prototype._onItemMouseover=function(v,oSelf){oSelf._toggleHighlight(this,'mouseover');oSelf.itemMouseOverEvent.fire(oSelf,this);};YAHOO.widget.AutoComplete.prototype._onItemMouseout=function(v,oSelf){oSelf._toggleHighlight(this,'mouseout');oSelf.itemMouseOutEvent.fire(oSelf,this);};YAHOO.widget.AutoComplete.prototype._onItemMouseclick=function(v,oSelf){oSelf._toggleHighlight(this,'mouseover');oSelf._selectItem(this);};YAHOO.widget.AutoComplete.prototype._onContainerMouseover=function(v,oSelf){oSelf._bOverContainer=true;};YAHOO.widget.AutoComplete.prototype._onContainerMouseout=function(v,oSelf){oSelf._bOverContainer=false;if(oSelf._oCurItem){oSelf._toggleHighlight(oSelf._oCurItem,'mouseover');}};YAHOO.widget.AutoComplete.prototype._onContainerScroll=function(v,oSelf){oSelf._oTextbox.focus();};YAHOO.widget.AutoComplete.prototype._onTextboxKeyDown=function(v,oSelf){var nKeyCode=v.keyCode;switch(nKeyCode){case 9:if(oSelf.delimChar&&(oSelf._nKeyCode!=nKeyCode)){if(oSelf._bContainerOpen){YAHOO.util.Event.stopEvent(v);}}

if(oSelf._oCurItem){oSelf._selectItem(oSelf._oCurItem);}

else{oSelf._clearList();}

break;case 13:if(oSelf._nKeyCode!=nKeyCode){if(oSelf._bContainerOpen){YAHOO.util.Event.stopEvent(v);}}

if(oSelf._oCurItem){oSelf._selectItem(oSelf._oCurItem);}

else{oSelf._clearList();}

break;case 27:oSelf._clearList();return;case 39:oSelf._jumpSelection();break;case 38:YAHOO.util.Event.stopEvent(v);oSelf._moveSelection(nKeyCode);break;case 40:YAHOO.util.Event.stopEvent(v);oSelf._moveSelection(nKeyCode);break;default:break;}};YAHOO.widget.AutoComplete.prototype._onTextboxKeyPress=function(v,oSelf){var nKeyCode=v.keyCode;switch(nKeyCode){case 9:case 13:if(oSelf.delimChar&&(oSelf._nKeyCode!=nKeyCode)){if(oSelf._bContainerOpen){YAHOO.util.Event.stopEvent(v);}}

break;case 38:case 40:YAHOO.util.Event.stopEvent(v);break;default:break;}};YAHOO.widget.AutoComplete.prototype._onTextboxKeyUp=function(v,oSelf){oSelf._initProps();var nKeyCode=v.keyCode;oSelf._nKeyCode=nKeyCode;var sChar=String.fromCharCode(nKeyCode);var sText=this.value;if(oSelf._isIgnoreKey(nKeyCode)||(sText.toLowerCase()==this._sCurQuery)){return;}

else{oSelf.textboxKeyEvent.fire(oSelf,nKeyCode);}

if(oSelf.queryDelay>0){var nDelayID=setTimeout(function(){oSelf._sendQuery(sText);},(oSelf.queryDelay*1000));if(oSelf._nDelayID!=-1){clearTimeout(oSelf._nDelayID);}

oSelf._nDelayID=nDelayID;}

else{oSelf._sendQuery(sText);}};YAHOO.widget.AutoComplete.prototype._isIgnoreKey=function(nKeyCode){if(this.typeAhead){if((nKeyCode==8)||(nKeyCode==39)||(nKeyCode==46)){return true;}}

if((nKeyCode==9)||(nKeyCode==13)||(nKeyCode==16)||(nKeyCode==17)||(nKeyCode>=18&&nKeyCode<=20)||(nKeyCode==27)||(nKeyCode>=33&&nKeyCode<=35)||(nKeyCode>=36&&nKeyCode<=38)||(nKeyCode==40)||(nKeyCode>=44&&nKeyCode<=45)){return true;}

return false;};YAHOO.widget.AutoComplete.prototype._onTextboxFocus=function(v,oSelf){oSelf._oTextbox.setAttribute("autocomplete","off");oSelf._bFocused=true;oSelf.textboxFocusEvent.fire(oSelf);};YAHOO.widget.AutoComplete.prototype._onTextboxBlur=function(v,oSelf){if(!oSelf._bOverContainer||(oSelf._nKeyCode==9)){if(oSelf.forceSelection&&!oSelf._bItemSelected){if(!oSelf._bContainerOpen||(oSelf._bContainerOpen&&!oSelf._textMatchesOption())){oSelf._clearSelection();}}

if(oSelf._bContainerOpen){oSelf._clearList();}

oSelf._bFocused=false;oSelf.textboxBlurEvent.fire(oSelf);}};YAHOO.widget.AutoComplete.prototype._onFormSubmit=function(v,oSelf){oSelf._oTextbox.setAttribute("autocomplete","on");};YAHOO.widget.AutoComplete.prototype._sendQuery=function(sQuery){var aDelimChar=(this.delimChar)?this.delimChar:null;if(aDelimChar){var nDelimIndex=-1;for(var i=aDelimChar.length-1;i>=0;i--){var nNewIndex=sQuery.lastIndexOf(aDelimChar[i]);if(nNewIndex>nDelimIndex){nDelimIndex=nNewIndex;}}

if(aDelimChar[i]==" "){for(var j=aDelimChar.length-1;j>=0;j--){if(sQuery[nDelimIndex-1]==aDelimChar[j]){nDelimIndex--;break;}}}

if(nDelimIndex>-1){var nQueryStart=nDelimIndex+1;while(sQuery.charAt(nQueryStart)==" "){nQueryStart+=1;}

this._sSavedQuery=sQuery.substring(0,nQueryStart);sQuery=sQuery.substr(nQueryStart);}

else if(sQuery.indexOf(this._sSavedQuery)<0){this._sSavedQuery=null;}}

if(sQuery.length<this.minQueryLength){if(this._nDelayID!=-1){clearTimeout(this._nDelayID);}

this._clearList();return;}

sQuery=encodeURI(sQuery);this._nDelayID=-1;this.dataRequestEvent.fire(this,sQuery);this.dataSource.getResults(this._populateList,sQuery,this);};YAHOO.widget.AutoComplete.prototype._clearList=function(){this._oContainer.scrollTop=0;var aItems=this._aListIds;for(var i=aItems.length-1;i>=0;i--){document.getElementById(aItems[i]).style.display="none";}

if(this._oCurItem){this._toggleHighlight(this._oCurItem,'mouseout');}

this._oCurItem=null;this._nDisplayedItems=0;this._sCurQuery=null;this._toggleContainer(false);};YAHOO.widget.AutoComplete.prototype._populateList=function(sQuery,aResults,oSelf){if(aResults===null){oSelf.dataErrorEvent.fire(oSelf,sQuery);}

else{oSelf.dataReturnEvent.fire(oSelf,sQuery,aResults);}

if(!oSelf._bFocused||!aResults){return;}

var isOpera=(navigator.userAgent.toLowerCase().indexOf("opera")!=-1);oSelf._oContainer.style.width=(!isOpera)?null:"";oSelf._oContainer.style.height=(!isOpera)?null:"";var sCurQuery=decodeURI(sQuery);oSelf._sCurQuery=sCurQuery;var aItems=oSelf._aListIds;oSelf._bItemSelected=false;var nItems=Math.min(aResults.length,oSelf.maxResultsDisplayed);oSelf._nDisplayedItems=nItems;if(nItems>0){for(var i=nItems-1;i>=0;i--){var oItemi=document.getElementById(aItems[i]);var oResultItemi=aResults[i];oItemi.innerHTML=oSelf.formatResult(oResultItemi,sCurQuery);oItemi.style.display="list-item";oItemi._sResultKey=oResultItemi[0];oItemi._oResultData=oResultItemi;}

for(var j=aItems.length-1;j>=nItems;j--){var oItemj=document.getElementById(aItems[j]);oItemj.innerHTML=null;oItemj.style.display="none";oItemj._sResultKey=null;oItemj._oResultData=null;}

var oFirstItem=document.getElementById(aItems[0]);oSelf._toggleHighlight(oFirstItem,'mouseover');oSelf._toggleContainer(true);oSelf.itemArrowToEvent.fire(oSelf,oFirstItem);oSelf._typeAhead(oFirstItem,sQuery);oSelf._oCurItem=oFirstItem;}

else{oSelf._clearList();}};YAHOO.widget.AutoComplete.prototype._clearSelection=function(){var sValue=this._oTextbox.value;var sChar=(this.delimChar)?this.delimChar[0]:null;var nIndex=(sChar)?sValue.lastIndexOf(sChar,sValue.length-2):-1;if(nIndex>-1){this._oTextbox.value=sValue.substring(0,nIndex);}

else{this._oTextbox.value="";}

this._sSavedQuery=this._oTextbox.value;this.selectionEnforceEvent.fire(this);};YAHOO.widget.AutoComplete.prototype._textMatchesOption=function(){var foundMatch=false;for(var i=this._nDisplayedItems-1;i>=0;i--){var oItem=document.getElementById(this._aListIds[i]);var sMatch=oItem._sResultKey.toLowerCase();if(sMatch==this._sCurQuery.toLowerCase()){foundMatch=true;break;}}

return(foundMatch);};YAHOO.widget.AutoComplete.prototype._typeAhead=function(oItem,sQuery){var oTextbox=this._oTextbox;var sValue=this._oTextbox.value;if(!this.typeAhead){return;}

if(!oTextbox.setSelectionRange&&!oTextbox.createTextRange){return;}

var nStart=sValue.length;this._updateValue(oItem);var nEnd=oTextbox.value.length;this._selectText(oTextbox,nStart,nEnd);var sPrefill=oTextbox.value.substr(nStart,nEnd);this.typeAheadEvent.fire(this,sQuery,sPrefill);};YAHOO.widget.AutoComplete.prototype._selectText=function(oTextbox,nStart,nEnd){if(oTextbox.setSelectionRange){oTextbox.setSelectionRange(nStart,nEnd);}

else if(oTextbox.createTextRange){var oTextRange=oTextbox.createTextRange();oTextRange.moveStart("character",nStart);oTextRange.moveEnd("character",nEnd-oTextbox.value.length);oTextRange.select();}

else{oTextbox.select();}};YAHOO.widget.AutoComplete.prototype._toggleContainer=function(bShow){var oContainer=this._oContainer;if(!bShow&&!this._bContainerOpen){oContainer.style.display="none";return;}

var oContent=this._oContent;var oIFrame=this._oIFrame;if(bShow&&oContent&&oIFrame){var sDisplay=oContainer.style.display;oContainer.style.display="block";oIFrame.style.width=oContent.offsetWidth+"px";oIFrame.style.height=oContent.offsetHeight+"px";oIFrame.style.marginTop="-"+oContent.offsetHeight+"px";oContainer.style.display=sDisplay;}

var oAnim=this._oAnim;if(oAnim&&oAnim.getEl()&&(this.animHoriz||this.animVert)){if(oAnim.isAnimated()){oAnim.stop();}

var oClone=oContainer.cloneNode(true);oContainer.parentNode.appendChild(oClone);oClone.style.top="-9000px";oClone.style.display="block";var wExp=oClone.offsetWidth;var hExp=oClone.offsetHeight;var wColl=(this.animHoriz)?0:wExp;var hColl=(this.animVert)?0:hExp;oAnim.attributes=(bShow)?{width:{to:wExp},height:{to:hExp}}:{width:{to:wColl},height:{to:hColl}};if(bShow&&!this._bContainerOpen){oContainer.style.width=wColl+"px";oContainer.style.height=hColl+"px";}

else{oContainer.style.width=wExp+"px";oContainer.style.height=hExp+"px";}

oContainer.parentNode.removeChild(oClone);oClone=null;var oSelf=this;var onAnimComplete=function(){if(!bShow){oContainer.style.display="none";}

oAnim.onComplete.unsubscribeAll();if(bShow){oSelf.containerExpandEvent.fire(oSelf);}

else{oSelf.containerCollapseEvent.fire(oSelf);}};oContainer.style.display="block";oAnim.onComplete.subscribe(onAnimComplete);oAnim.animate();this._bContainerOpen=bShow;}

else{this._bContainerOpen=bShow;oContainer.style.display=(bShow)?"block":"none";if(bShow){this.containerExpandEvent.fire(this);}

else{this.containerCollapseEvent.fire(this);}}};YAHOO.widget.AutoComplete.prototype._toggleHighlight=function(oNewItem,sType){oNewItem.className=oNewItem.className.replace(this.highlightClassName,"");if(this._oCurItem){this._oCurItem.className=this._oCurItem.className.replace(this.highlightClassName,"");}

if(sType=='mouseover'){oNewItem.className+=" "+this.highlightClassName;this._oCurItem=oNewItem;}};YAHOO.widget.AutoComplete.prototype._updateValue=function(oItem){var oTextbox=this._oTextbox;var sDelimChar=(this.delimChar)?this.delimChar[0]:null;var sSavedQuery=this._sSavedQuery;var sResultKey=oItem._sResultKey;oTextbox.focus();oTextbox.value="";if(sDelimChar){if(sSavedQuery){oTextbox.value=sSavedQuery;}

oTextbox.value+=sResultKey+sDelimChar;if(sDelimChar!=" "){oTextbox.value+=" ";}}

else{oTextbox.value=sResultKey;}

if(oTextbox.type=="textarea"){oTextbox.scrollTop=oTextbox.scrollHeight;}

var end=oTextbox.value.length;this._selectText(oTextbox,end,end);this._oCurItem=oItem;};YAHOO.widget.AutoComplete.prototype._selectItem=function(oItem){this._bItemSelected=true;this._updateValue(oItem);this.itemSelectEvent.fire(this,oItem);this._clearList();};YAHOO.widget.AutoComplete.prototype._jumpSelection=function(){if(!this.typeAhead){return;}

else{this._clearList();}};YAHOO.widget.AutoComplete.prototype._moveSelection=function(nKeyCode){if(this._bContainerOpen){var oCurItem=this._oCurItem;var nCurItemIndex=-1;if(oCurItem){nCurItemIndex=oCurItem._nItemIndex;}

var nNewItemIndex=(nKeyCode==40)?(nCurItemIndex+1):(nCurItemIndex-1);if(nNewItemIndex<-2||nNewItemIndex>=this._nDisplayedItems){return;}

if(oCurItem){this._toggleHighlight(oCurItem,'mouseout');this.itemArrowFromEvent.fire(this,oCurItem);}

if(nNewItemIndex==-1){if(this.delimChar&&this._sSavedQuery){if(!this._textMatchesOption()){this._oTextbox.value=this._sSavedQuery;}

else{this._oTextbox.value=this._sSavedQuery+this._sCurQuery;}}

else{this._oTextbox.value=this._sCurQuery;}

this._oCurItem=null;return;}

if(nNewItemIndex==-2){this._clearList();return;}

var oNewItem=document.getElementById(this._sName+"item"+nNewItemIndex);if((YAHOO.util.Dom.getStyle(this._oContainer,"overflow")=="auto")&&(nNewItemIndex>-1)&&(nNewItemIndex<this._nDisplayedItems)){if(nKeyCode==40){if((oNewItem.offsetTop+oNewItem.offsetHeight)>(this._oContainer.scrollTop+this._oContainer.offsetHeight)){this._oContainer.scrollTop=(oNewItem.offsetTop+oNewItem.offsetHeight)-this._oContainer.offsetHeight;}

else if((oNewItem.offsetTop+oNewItem.offsetHeight)<this._oContainer.scrollTop){this._oContainer.scrollTop=oNewItem.offsetTop;}}

else{if(oNewItem.offsetTop<this._oContainer.scrollTop){this._oContainer.scrollTop=oNewItem.offsetTop;}

else if(oNewItem.offsetTop>(this._oContainer.scrollTop+this._oContainer.offsetHeight)){this._oContainer.scrollTop=(oNewItem.offsetTop+oNewItem.offsetHeight)-this._oContainer.offsetHeight;}}}

this._toggleHighlight(oNewItem,'mouseover');this.itemArrowToEvent.fire(this,oNewItem);if(this.typeAhead){this._updateValue(oNewItem);}}};YAHOO.widget.DataSource=function(){};YAHOO.widget.DataSource.prototype.ERROR_DATANULL="Response data was null";YAHOO.widget.DataSource.prototype.ERROR_DATAPARSE="Response data could not be parsed";YAHOO.widget.DataSource.prototype.maxCacheEntries=15;YAHOO.widget.DataSource.prototype.queryMatchContains=false;YAHOO.widget.DataSource.prototype.queryMatchSubset=false;YAHOO.widget.DataSource.prototype.queryMatchCase=false;YAHOO.widget.DataSource.prototype.getResults=function(oCallbackFn,sQuery,oParent){var aResults=this._doQueryCache(oCallbackFn,sQuery,oParent);if(aResults.length===0){this.queryEvent.fire(this,oParent,sQuery);this.doQuery(oCallbackFn,sQuery,oParent);}};YAHOO.widget.DataSource.prototype.doQuery=function(oCallbackFn,sQuery,oParent){};YAHOO.widget.DataSource.prototype.flushCache=function(){if(this._aCache){this._aCache=[];}

if(this._aCacheHelper){this._aCacheHelper=[];}

this.cacheFlushEvent.fire(this);};YAHOO.widget.DataSource.prototype.queryEvent=null;YAHOO.widget.DataSource.prototype.cacheQueryEvent=null;YAHOO.widget.DataSource.prototype.getResultsEvent=null;YAHOO.widget.DataSource.prototype.getCachedResultsEvent=null;YAHOO.widget.DataSource.prototype.dataErrorEvent=null;YAHOO.widget.DataSource.prototype.cacheFlushEvent=null;YAHOO.widget.DataSource.prototype._aCache=null;YAHOO.widget.DataSource.prototype._init=function(){var maxCacheEntries=this.maxCacheEntries;if(isNaN(maxCacheEntries)||(maxCacheEntries<0)){maxCacheEntries=0;}

if(maxCacheEntries>0&&!this._aCache){this._aCache=[];}

this.queryEvent=new YAHOO.util.CustomEvent("query",this);this.cacheQueryEvent=new YAHOO.util.CustomEvent("cacheQuery",this);this.getResultsEvent=new YAHOO.util.CustomEvent("getResults",this);this.getCachedResultsEvent=new YAHOO.util.CustomEvent("getCachedResults",this);this.dataErrorEvent=new YAHOO.util.CustomEvent("dataError",this);this.cacheFlushEvent=new YAHOO.util.CustomEvent("cacheFlush",this);};YAHOO.widget.DataSource.prototype._addCacheElem=function(resultObj){var aCache=this._aCache;if(!aCache||!resultObj||!resultObj.query||!resultObj.results){return;}

if(aCache.length>=this.maxCacheEntries){aCache.shift();}

aCache.push(resultObj);};YAHOO.widget.DataSource.prototype._doQueryCache=function(oCallbackFn,sQuery,oParent){var aResults=[];var bMatchFound=false;var aCache=this._aCache;var nCacheLength=(aCache)?aCache.length:0;var bMatchContains=this.queryMatchContains;if((this.maxCacheEntries>0)&&aCache&&(nCacheLength>0)){this.cacheQueryEvent.fire(this,oParent,sQuery);if(!this.queryMatchCase){var sOrigQuery=sQuery;sQuery=sQuery.toLowerCase();}

for(var i=nCacheLength-1;i>=0;i--){var resultObj=aCache[i];var aAllResultItems=resultObj.results;var matchKey=(!this.queryMatchCase)?encodeURI(resultObj.query.toLowerCase()):encodeURI(resultObj.query);if(matchKey==sQuery){bMatchFound=true;aResults=aAllResultItems;if(i!=nCacheLength-1){aCache.splice(i,1);this._addCacheElem(resultObj);}

break;}

else if(this.queryMatchSubset){for(var j=sQuery.length-1;j>=0;j--){var subQuery=sQuery.substr(0,j);if(matchKey==subQuery){bMatchFound=true;for(var k=aAllResultItems.length-1;k>=0;k--){var aRecord=aAllResultItems[k];var sKeyIndex=(this.queryMatchCase)?encodeURI(aRecord[0]).indexOf(sQuery):encodeURI(aRecord[0]).toLowerCase().indexOf(sQuery);if((!bMatchContains&&(sKeyIndex===0))||(bMatchContains&&(sKeyIndex>-1))){aResults.unshift(aRecord);}}

resultObj={};resultObj.query=sQuery;resultObj.results=aResults;this._addCacheElem(resultObj);break;}}

if(bMatchFound){break;}}}

if(bMatchFound){this.getCachedResultsEvent.fire(this,oParent,sOrigQuery,aResults);oCallbackFn(sOrigQuery,aResults,oParent);}}

return aResults;};YAHOO.widget.DS_XHR=function(sScriptURI,aSchema,oConfigs){if(typeof oConfigs=="object"){for(var sConfig in oConfigs){this[sConfig]=oConfigs[sConfig];}}

if(!aSchema||(aSchema.constructor!=Array)){}

else{this.schema=aSchema;}

this.scriptURI=sScriptURI;this._init();};YAHOO.widget.DS_XHR.prototype=new YAHOO.widget.DataSource();YAHOO.widget.DS_XHR.prototype.TYPE_JSON=0;YAHOO.widget.DS_XHR.prototype.TYPE_XML=1;YAHOO.widget.DS_XHR.prototype.TYPE_FLAT=2;YAHOO.widget.DS_XHR.prototype.ERROR_DATAXHR="XHR response failed";YAHOO.widget.DS_XHR.prototype.scriptURI=null;YAHOO.widget.DS_XHR.prototype.scriptQueryParam="query";YAHOO.widget.DS_XHR.prototype.scriptQueryAppend="";YAHOO.widget.DS_XHR.prototype.responseType=YAHOO.widget.DS_XHR.prototype.TYPE_JSON;YAHOO.widget.DS_XHR.prototype.responseStripAfter="\n<!--";YAHOO.widget.DS_XHR.prototype.doQuery=function(oCallbackFn,sQuery,oParent){var isXML=(this.responseType==this.TYPE_XML);var sUri=this.scriptURI+"?"+this.scriptQueryParam+"="+sQuery;if(this.scriptQueryAppend.length>0){sUri+="&"+this.scriptQueryAppend;}

var oResponse=null;var oSelf=this;var responseSuccess=function(oResp){if(!isXML){oResp=oResp.responseText;}

else{oResp=oResp.responseXML;}

if(oResp===null){oSelf.dataErrorEvent.fire(oSelf,oParent,sQuery,oSelf.ERROR_DATANULL);oCallbackFn(sQuery,null,oParent);return;}

var resultObj={};resultObj.query=decodeURI(sQuery);resultObj.results=oSelf.parseResponse(sQuery,oResp,oParent);oSelf._addCacheElem(resultObj);oCallbackFn(sQuery,resultObj.results,oParent);};var responseFailure=function(oResp){oSelf.dataErrorEvent.fire(oSelf,oParent,sQuery,oSelf.ERROR_DATAXHR);oCallbackFn(sQuery,null,oParent);return;};var oCallback={success:responseSuccess,failure:responseFailure};YAHOO.util.Connect.asyncRequest("GET",sUri,oCallback,null);};YAHOO.widget.DS_XHR.prototype.parseResponse=function(sQuery,oResponse,oParent){var aSchema=this.schema;var aResults=[];var bError=false;var nEnd=((this.responseStripAfter!=="")&&(oResponse.indexOf))?oResponse.indexOf(this.responseStripAfter):-1;if(nEnd!=-1){oResponse=oResponse.substring(0,nEnd);}

switch(this.responseType){case this.TYPE_JSON:if(window.JSON){var jsonObjParsed=JSON.parse(oResponse);if(!jsonObjParsed){bError=true;break;}

else{var jsonListParsed=eval("jsonObjParsed."+aSchema[0]);for(var i=jsonListParsed.length-1;i>=0;i--){jsonListParsed[i][0]=eval("jsonListParsed[i]."+aSchema[1]);aResults[i]=jsonListParsed[i];}

break;}}

else{try{while(oResponse.substring(0,1)==" "){oResponse=oResponse.substring(1,oResponse.length);}

if((oResponse.indexOf("{}")===0)||(oResponse.indexOf("{")<0)){break;}

var jsonObjRaw=eval('('+oResponse+')');var jsonListRaw=eval("jsonObjRaw."+aSchema[0]);for(var j=jsonListRaw.length-1;j>=0;j--){jsonListRaw[j][0]=jsonListRaw[j][aSchema[1]];aResults[j]=jsonListRaw[j];}

break;}

catch(e){bError=true;break;}}

break;case this.TYPE_XML:var xmlList=oResponse.getElementsByTagName(aSchema[0]);for(var k=xmlList.length-1;k>=0;k--){var result=xmlList.item(k);var aFieldSet=[];for(var m=aSchema.length-1;m>=1;m--){var sValue=null;var xmlAttr=result.attributes.getNamedItem(aSchema[m]);if(xmlAttr){sValue=xmlAttr.value;}

else{var xmlNode=result.getElementsByTagName(aSchema[m]);if(xmlNode){sValue=xmlNode.item(0).firstChild.nodeValue;}}

aFieldSet.unshift(sValue);}

aResults.unshift(aFieldSet);}

break;case this.TYPE_FLAT:if(oResponse.length>0){var newLength=oResponse.length-aSchema[0].length;if(oResponse.substr(newLength)==aSchema[0]){oResponse=oResponse.substr(0,newLength);}

var aRecords=oResponse.split(aSchema[0]);for(var n=aRecords.length-1;n>=0;n--){aResults[n]=aRecords[n].split(aSchema[1]);}}

break;default:break;}

if(bError){this.dataErrorEvent.fire(this,oParent,sQuery,this.ERROR_DATAPARSE);return null;}

else{this.getResultsEvent.fire(this,oParent,sQuery,aResults);return aResults;}};YAHOO.widget.DS_XHR.prototype._oConn=null;YAHOO.widget.DS_JSFunction=function(oFunction,oConfigs){if(typeof oConfigs=="object"){for(var sConfig in oConfigs){this[sConfig]=oConfigs[sConfig];}}

this.dataFunction=oFunction;this._init();};YAHOO.widget.DS_JSFunction.prototype=new YAHOO.widget.DataSource();YAHOO.widget.DS_JSFunction.prototype.dataFunction=null;YAHOO.widget.DS_JSFunction.prototype.doQuery=function(oCallbackFn,sQuery,oParent){var oFunction=this.dataFunction;var aResults=[];aResults=oFunction(sQuery);if(aResults===null){this.dataErrorEvent.fire(this,oParent,sQuery,this.ERROR_DATANULL);oCallbackFn(sQuery,null,oParent);return;}

var resultObj={};resultObj.query=decodeURI(sQuery);resultObj.results=aResults;this._addCacheElem(resultObj);this.getResultsEvent.fire(this,oParent,sQuery,aResults);oCallbackFn(sQuery,aResults,oParent);return;};YAHOO.widget.DS_JSArray=function(aData,oConfigs){if(typeof oConfigs=="object"){for(var sConfig in oConfigs){this[sConfig]=oConfigs[sConfig];}}

this.data=aData;this._init();};YAHOO.widget.DS_JSArray.prototype=new YAHOO.widget.DataSource();YAHOO.widget.DS_JSArray.prototype.data=null;YAHOO.widget.DS_JSArray.prototype.doQuery=function(oCallbackFn,sQuery,oParent){var aData=this.data;var aResults=[];var bMatchFound=false;var bMatchContains=this.queryMatchContains;if(!this.queryMatchCase){sQuery=sQuery.toLowerCase();}

for(var i=aData.length-1;i>=0;i--){var aDataset=[];if(typeof aData[i]=="string"){aDataset[0]=aData[i];}

else{aDataset=aData[i];}

var sKeyIndex=(this.queryMatchCase)?encodeURI(aDataset[0]).indexOf(sQuery):encodeURI(aDataset[0]).toLowerCase().indexOf(sQuery);if((!bMatchContains&&(sKeyIndex===0))||(bMatchContains&&(sKeyIndex>-1))){aResults.unshift(aDataset);}}

this.getResultsEvent.fire(this,oParent,sQuery,aResults);oCallbackFn(sQuery,aResults,oParent);};

