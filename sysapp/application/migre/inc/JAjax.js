/*
 * Autor    : Júlio Corrêa Pereira
 * Data     : 08/03/2006
 * Descrição: Esta classe tem por objetivo facilitar a manipulação de formulários 
 *            utilizando a tecnologia AJAX. Implementa os métodos POST e GET de 
 *            envio de informações e gera, a partir de um objeto especificado,
 *            a string para enviar todos os dados dos objetos de formulário existentes
 *            dentro daquele "container". O retorno é enviado para uma função 
 *            definida pelo usuário, e é um objeto do tipo XMLHttpRequest.    
 *             
 * Obs.: Por questões de compatibilidade, as funções utilizam o ID e não o Name dos objetos 
 *
 * Versão   : 0.34 - 14/03/2006
 * Versão   : 0.36 - 15/03/2006 
 *            - Melhora no código; 
 *            - Correção do envio via POST, 
 *            - Correção do problema de "cacheamento" do XML requisitado no IE;
 *            - Preenchimento dinâmico de Combos/Lists 
 * Versão   : 0.38 - 16/03/2006
 *            - Alterado o método createSendString. Se ele receber como parâmetro um objeto de
 *              formulário, cria a string apenas para este objeto   
 *            - Alterado o método setFormElementsXML para verificar se o objeto que recebe os 
 *              valores de retorno é um objeto de formulário. Se for, preehche o objeto  
 *              
 * Versão   : 0.39 - 17/03/2006
 *            - Informações de Debug 
 *            - Tratamento de NULL (quando vem do cliente), passa para "" no campo do formulário
 *            - Quando vem uma mensagem de erro no campo "ERR" do XML, mostra um Alert com este erro  
 *             
 * Pendências: 
 *            - Listas multi-select tando para envio quanto para retorno
 *            - Campo com auto-preenchimento
 *            - Definir uma estrutura para o div de interação (imagem + texto ?)
 *            - Definir e implementar corretamente a parte de erros 
 *      
 **/

function Jajax() {
   // Constructor
   this.identName             = "Jajax";
   this.version               = "0.39";
   this.autor                 = "Júlio Corrêa Pereira <julio.correa.pereira@gmail.com>"
   this.interactiveLayer      = null;
   this.callback              = null;
   this.errNo                 = 0;
   this.wrnNo                 = 0;
   this.errMsg                = "";
   this.debug                 = false;
   this.interactivityLevel    = 1; // 0 = Nenhum; 1 = Médio (default); 2 = Alto
   this.responseTarget        = null;
   this.lastResponseText      = null;
   this.alertResponses        = true;
   try {
      this.ajax = new XMLHttpRequest();
   } catch(ee) {
      try {
         this.ajax = new ActiveXObject("Msxml2.XMLHTTP");
      } catch(e) {
         try {
            this.ajax = new ActiveXObject("Microsoft.XMLHTTP");
         } catch(E) {
            this.ajax = false;
         }
      }
   }
} 

// Retorna um objeto do documento através do seu ID (independente do navegador)
Jajax.prototype.getEl = function (objName) {
   if (document.getElementById) {
      ret = document.getElementById(objName);
   } else {
      if (document.all) {
         ret = document.all(objName);
      } else {
         ret = 'undefined';
      }
   }
   return ret;
}

Jajax.prototype.setDebugOn = function() {
   this.debug = true;
}

Jajax.prototype.setDebugOff = function() {
   this.debug = false;
}

// --------------------------------
// Obtem informações do navegador
// --------------------------------
// Nome do Browser
Jajax.prototype.browserName = function() {
   var ret = "";
   if (navigator.userAgent.indexOf("Opera")!=-1){
      ret = "OPERA";
   } else {
      if (navigator.appVersion.indexOf("MSIE")!=-1){
         ret = "MSIE";
      } else {
         if (navigator.userAgent.indexOf("Firefox")!=-1) {
            ret = "FF";
         } else {
            if (navigator.appName=="Netscape") { 
               ret = "NS";
            }
         }
      }
   }
   return ret;
}

// Versão do Browser
Jajax.prototype.browserVersion = function() {
   var version = 0;
   if (browserName() == "OPERA") {
      temp=navigator.appVersion.split("Opera");
      version=parseFloat(temp[1].slice(1,temp[1].length-1));
   } else {
      if (browserName == "MSIE") {
         if (navigator.appVersion.indexOf("MSIE")!=-1) {
            temp=navigator.appVersion.split("MSIE");
         }
         version=parseFloat(temp[1])
      } else {
         if (browserName() == "FF") {
            var versionindex=navigator.userAgent.indexOf("Firefox")+8
            version = parseFloat(navigator.userAgent.charAt(versionindex));
         } else {
            if (browserName() = "NS") {
               version = parseFloat(navigator.appVersion);
            }
         }
      }
   }
   return version;
}

Jajax.prototype.setResponseTarget = function(divName) {
   this.responseTarget = divName;
}

// ------------------------------------------
// Div que mostra o andamento do processo
// ------------------------------------------

// Mostra o layer de andamento do processo
Jajax.prototype.showInteractiveLayer = function() {
   if (this.interactiveLayer != null) {
      var el = this.getEl(this.interactiveLayer);
      el.style.visibility = "visible";
   }
}

// Esconde o layer de andamento do processo
Jajax.prototype.hideInteractiveLayer = function() {
   if (this.interactiveLayer != null) {
      var el = this.getEl(this.interactiveLayer);
      el.style.visibility = "hidden";
   }
}

// Define o nome do layer de andamento 
Jajax.prototype.setInteractiveLayer = function(divName) {
   this.interactiveLayer = divName;
}

// Define o nível de interação do layer
// 0 - não mostra nada
// 1 - mostra apenas o início, e meio
// 2 - mostra todos os passos
Jajax.prototype.setInteractivityLevel = function(level) {
   this.interactivityLevel = level;
}

// Função que recebe o retorno
Jajax.prototype.setCallback = function(fncName) {
   this.callback = fncName;
}

Jajax.prototype.setFormElementsJSON = function(rootName, arrValues) {
   // Pressupõe que o arrValues seja um string com sintaxe de array Javascript (padrão JSON), de 2 dimensões, 
   // onde a primeira dimensão é o ID do objeto e a segunda o valor que o mesmo deve receber. 
   // Atualmente funciona com Text, Textarea, CheckBox, Radio, e Select (com apenas 1 elemento selecionável)
   var root = this.getEl(rootName);
   var obj = null;
   var x = null;
   var arrVal = eval(arrValues);
   for (i=0;i<arrVal.length;i++) {
      obj = getEl(arrVal[i][0]);
      switch(obj.type) {
         case 'radio': x = document.getElementsByTagName('input');
                       for (j=0;j<x.length;j++) {
                          if ( (x[j].type=='radio') && (x[j].id == arrVal[i][0]) && (x[j].value == arrVal[i][1]) ) {
                             x[j].checked = true;
                          }
                       }
                       break;
         case 'checkbox':obj.checked=true;break;
         default: obj.value = arrVal[i][1];
      }
   }
}

Jajax.prototype.getFormElements = function(rootName) {
   // Retorna um array contendo os elementos (de formulário) com a estrutura: [id, type, valor/checked]
   var root = this.getEl(rootName);
   var retArr = new Array();
   var els = new Array();
   var retLength = 0;
   var pos = 0;
   els[0] = root.getElementsByTagName('input');
   els[1] = root.getElementsByTagName('textarea');
   els[2] = root.getElementsByTagName('select');
   retArr.length = retLength;
   for (i=0;i<els.length;i++) {
      for (j=0;j<els[i].length;j++) {
         if (els[i][j].type == 'checkbox') {
            retArr[pos] = new Array(els[i][j].id,"'"+els[i][j].type+"'",(els[i][j].checked ? els[i][j].value : ''));
            pos++;
         } else {
            if (els[i][j].type == 'radio') {
               if (els[i][j].checked) {
                  retArr[pos] = new Array(els[i][j].id,"'"+els[i][j].type+"'",els[i][j].value);
                  pos++;
               }
            } else {
               retArr[pos] = new Array(els[i][j].id,"'"+els[i][j].type+"'",els[i][j].value);
               pos++;
            }
         }
      }
   }
   return retArr;
}

Jajax.prototype.createSendString = function(rootName) {
   // Cria uma string para ser enviada como parâmetro (método GET). Utiliza a getFormElements. 
   // Útil para utilização com Ajax
   var obj = this.getEl(rootName)
   var tipo = obj.type;
   if ( (tipo=="text") || (tipo=="textarea") || (tipo=="radio") || (tipo=="ckeckbox") || 
        (tipo=="password") || (tipo=="hidden") || (tipo=="select") || (tipo=="submit") ||
        (tipo=="fileupload") || (tipo=="button") || (tipo=="reset") ) {
      ret = encodeURIComponent(obj.id)+"="+encodeURIComponent(obj.value);
   } else {
      var inp = this.getFormElements(rootName);
      var ret = "";
      for (i=0;i<inp.length;i++) {
         if (ret.length > 0) {
            ret+="&";
         }
         ret+=encodeURIComponent(inp[i][0])+"="+encodeURIComponent(inp[i][2]);
      }
   }
   return ret;
}      

// Retorna o valor do campo cujo ID foi passado por parâmetro. Se não encontrar o campo, retorna null   
Jajax.prototype.getFieldValueXML = function(fldName) {
   elF = this.ajax.responseXML.getElementsByTagName("fld");
   vlr = null;
   for (x=0;x<elF.length;x++) {
//      if (elF[x].getAttribute("id") == fldName) {
      if (elF[x].getAttribute("id").toUpperCase() == fldName.toUpperCase()) {
         if ( (elF[x].getAttribute("tp") == "DAT") && (elF[x].childNodes[0].nodeValue == "NULL") ) {
            vlr = "";
         } else {
            vlr = elF[x].childNodes[0].nodeValue;
         }
         break;
      }
   }
   if (x < elF.length) {
      return vlr;
   } else {
      return null;
   }
}

// Preenche os elementos dentro de elRoot com so valores do XML
Jajax.prototype.setFormElementsXML = function(elRoot) {
   if (this.ajax.responseXML == null) {
      alert("O documento retornado está num formato inválido" + (this.debug == true ? ":\n\n"+this.ajax.getAllResponseHeaders()+"\n\n"+this.ajax.responseText : ""));
   }
   
   // Deu trabalho esta parte, já que os navegadores montam a estrutura da árvore XML de forma diferente... :S 
   // Bazon já dizia: "Browser differences suck!!!"
   
   // Pega a tag response
   if (this.browserName() == "MSIE") {
      var elFields = this.ajax.responseXML.childNodes[1];
   } else {
      var elFields = this.ajax.responseXML.childNodes[0];
   }
   //
   
   // Pega todos os campos retornados
   var elF = elFields.getElementsByTagName("fld");
   var root = this.getEl(elRoot);
//   alert(elRoot + " = " + root);
   if ( (root == null) || (root == 'undefined') ) {
      alert("Conteiner não encontrado na página");
   }
   
   tipo = root.type;
   if ( (tipo=="text")       || (tipo=="textarea") || (tipo=="radio")  || (tipo=="ckeckbox") || 
        (tipo=="password")   || (tipo=="hidden")   || (tipo=="select") || (tipo=="submit") ||
        (tipo=="fileupload") || (tipo=="button")   || (tipo=="reset") ) {
      this.getEl(elRoot).value = this.getFieldValueXML(elRoot);
   } else {
      var pos = 0;
      var els = new Array();
    
      els[0] = root.getElementsByTagName('input');
      els[1] = root.getElementsByTagName('textarea');
      els[2] = root.getElementsByTagName('select');
      
      // Percorre todos os campos retornados
      // Pega os elementos do formulário e busca no XML a informação correspondente a estes elementos (pelo ID)
      for (i=0;i<els.length;i++) { // 1..3
         for (j=0;j<els[i].length;j++) {
            fldValue = this.getFieldValueXML(els[i][j].id);
            if (fldValue != null) { // Se existe o campo no XML...
               if (els[i][j].type == 'checkbox') {
                  if (els[i][j].value == fldValue) {
                     els[i][j].checked = true;
                  }
               } else {
                  if (els[i][j].type == 'radio') {
                     if (els[i][j].value == fldValue) {
                        els[i][j].checked = true;
                     }
                  } else {
                     els[i][j].value = fldValue;
                  }
               }
            }
         }
      }
   }
}   

// Atualiza o estado do div de interação
Jajax.prototype.displayInteractiveState = function(estado) {
   if (this.interactiveLayer != null) {
      var oTxt = this.getEl(this.interactiveLayer);
   }
   if (this.interactiveLayer != null) {
      switch(estado) {
         case 0: (this.interactivityLevel > 1 ? oTxt.innerHTML = 'Inicializando...' : (this.interactivityLevel > 0 ? oTxt.innerHTML = 'Processando...' : oTxt.innerHTML = '')); break;
         case 1: (this.interactivityLevel > 1 ? oTxt.innerHTML = 'Carregando...'    : (this.interactivityLevel > 0 ? oTxt.innerHTML = 'Processando...' : oTxt.innerHTML = '')); break;
         case 2: (this.interactivityLevel > 1 ? oTxt.innerHTML = 'Carregado.'       : (this.interactivityLevel > 0 ? oTxt.innerHTML = 'Processando...' : oTxt.innerHTML = '')); break;
         case 3: (this.interactivityLevel > 1 ? oTxt.innerHTML = 'Processando...'   : (this.interactivityLevel > 0 ? oTxt.innerHTML = 'Processando...' : oTxt.innerHTML = '')); break;
         case 4: (this.interactivityLevel > 1 ? oTxt.innerHTML = 'Encerrado.'       : (this.interactivityLevel > 0 ? oTxt.innerHTML = 'Processando...' : oTxt.innerHTML = '')); break;
      }
   }
}

// Escreve informações na janela de debug
Jajax.prototype.writeDebug = function(origem, obj) {
   w = window.open("", "DEBUG", "toolbar=no,width=500,height=400,statusbar=no,scrollbars=yes,menubar=no,resizable=yes");
   d = w.document;
   dvTitulo = d.createElement('div');
   dv = d.createElement('div');
   if (origem == 'S') { // Server
      dv.setAttribute('style', 'background-color:#CCFFFF;');
      dvTitulo.setAttribute('style', 'background-color:#AACCFF;font-face:tahoma,verdana,arial;font-weight:bold;');
      txt = d.createTextNode('SERVER - Received');
   } else {
      dv.setAttribute('style', 'background-color: #FFFFCC');
      dvTitulo.setAttribute('style', 'background-color:#FFCCAA;font-face:tahoma,verdana,arial;font-weight:bold;');
      txt = d.createTextNode('CLIENT - Sended');
   }
   dvTitulo.appendChild(txt);
   dvConteudo = d.createElement('dv');
   if (typeof(obj) == "object") {
      dvConteudo.appendChild(this.xmlToTable(obj, d));
   } else {
      text = d.createTextNode(obj);
      dvConteudo.appendChild(text);
   }
   dv.appendChild(dvTitulo);
   dv.appendChild(dvConteudo);
   d.documentElement.appendChild(dv);
}   
   
// Transforma os parâmetros de um documento XML em uma tabela
Jajax.prototype.xmlToTable = function(xmlDoc, targetDoc) {
  var wood = xmlDoc.getElementsByTagName('fld');

  var table = targetDoc.createElement('TABLE');
  table.setAttribute('border', '1');  
  table.setAttribute('bgcolor', '#F0F0F0');  
  table.setAttribute('cellspacing', '0');  
  table.setAttribute('cellpadding', '1');  
  var tbody = targetDoc.createElement('TBODY');
  table.appendChild(tbody);

  for (j=0; j<wood.length; j++) {
    var row = targetDoc.createElement('TR');

    // Obtem o nome do nodo
    var td = targetDoc.createElement('TD');
    var text = targetDoc.createTextNode(wood[j].nodeName);
    td.appendChild(text);
    row.appendChild(td);

    // Obtem o tipo do nodo
    td = targetDoc.createElement('TD');
    text = targetDoc.createTextNode(wood[j].getAttribute("tp"));
    td.appendChild(text);
    row.appendChild(td);

    // Obtem o ID do nodo (nome do campo)
    td = targetDoc.createElement('TD');
    text = targetDoc.createTextNode(wood[j].getAttribute("id"));
    td.appendChild(text);
    row.appendChild(td);

    // Obtem o valor do campo
    td = targetDoc.createElement('TD');
    text = targetDoc.createTextNode(wood[j].firstChild.nodeValue);
    td.appendChild(text);
    row.appendChild(td);

    tbody.appendChild(row);
  }
  dv = targetDoc.createElement('DIV');
  dv.appendChild(table);
//  targetDoc.documentElement.appendChild(table);
  return table;
}

Jajax.prototype.trimString = function(s) {
  s = s.replace( /^\s+/g, "" );// strip leading
  return s.replace( /\s+$/g, "" );// strip trailing
}

Jajax.prototype.countOcorrenceChar = function(txt, c) {
   count = 0;
   lastPos = 0;
//   while (s.indexOf(s, lastPos) != -1) {
   while (true) {
      lastPos = txt.indexOf(c, lastPos);
      if (lastPos == -1) {
         break;
      }
      count++;
      lastPos++;
   }
   return count;
}

// Formata uma string com frases separadas por ";" para melhor apresentação em um alert
Jajax.prototype.errorFormat = function(err) 
{
	var vErros = new Array();
	var ret = '';

	if (err != null)
	{
		vAux = err.split(";");
		pos = 0;
	   
		for (i=0;i<vAux.length;i++) 
		{
			vMsg = this.trimString(vAux[i]);
			if (vMsg.substr(0, 16) != 'User-Defined Exc') 
			{
				vErros[pos] = vMsg;
				pos++;
			}
		}
		
		if (vErros.length > 1)
		{ // Todos os erros devem ser apresentados
			ret  = 'ATENÇÃO\n\nOcorreram os seguintes erros:\n\n\n';
			for (i=0;i<vErros.length;i++) 
			{
				vLine = this.trimString(vErros[i]);
				if (vLine.length > 0) 
				{
					ret += '  - ' + vLine + '\n';
				}
			}
			ret += '\n\n\nCorrija-os antes de continuar';
		} 
		else 
		{
			if ((this.trimString(err).length > 0) && (this.trimString(err).length != ""))
			{ // Apenas o último erro deve ser apresentado
				if (this.countOcorrenceChar(err, '.') > 1) 
				{ // Múltiplas frases
					ret  = 'ATENÇÃO\n\nOcorreu o seguinte erro:\n\n\n';
					vErros = err.split(".");

					var pos = vErros.length -1;
					for (i=0;i<vErros.length;i++) 
					{
						vErros[i]= this.trimString(vErros[pos]);
						pos--;
					}

					for(i=0; i<vErros.length; i++) 
					{
						if (vErros[i].substr(0,5) != '(EMP-') 
						{
							ret += "  - " + this.trimString(vErros[i]);
							break;
						}
					}
				} 
				else 
				{
					ret = err;
				}
			}
		}
	}
	return ret;
}

// Processa os eventos da requisição xmlhttprequest nos métodos sendFormGET e sendFormPOST
Jajax.prototype.ajaxChangeState = function(ajax) {
   if (this.interactiveLayer != null) {
      var oTxt = this.getEl(this.interactiveLayer);
   }
   if (ajax.readyState == 4) {
      if (ajax.status == 200) {
         if (this.debug == true) {
//            if (this.browserName == 'MSIE') {
//               this.writeDebug('S', ajax.responseText);
//            } else {
               this.writeDebug('S', ajax.responseXML);
//            }
         }
         // Mostra informação de erro caso tenha ocorrido um erro no lado server.
         var erro = this.getFieldValueXML("ERR");
         if (erro != "NULL") { // Se ocorreu algum erro no lado servidor
            this.hideInteractiveLayer();
//            alert(erro);  // Substituído por algo mais elaborado
//            alert(this.errorFormat(erro) + (this.alertResponses ? '\n\nResposta Servidor(para debug): ' + erro : ''));
//            alert(this.errorFormat(erro)); // Substituido pela função mostraErro 24/10/2006
			this.mostraErro(this.errorFormat(erro));
            this.errNo = 2;
            this.errMsg = erro;
            if (this.callback != null) {
               eval(this.callback+"(ajax)");
            }
//            return null; // Para a execução do programa
         } else { // Se tudo ocorreu corretamente no lado servidor
            if ( (this.responseTarget == null) && (this.callback == null) ) {
               alert("Faltou definir uma função de retorno");
            } else {
               if (this.responseTarget != null) {
                  this.setFormElementsXML(this.responseTarget);
               } else {
                  eval(this.callback+"(ajax)");
               }
            }
         }
      } else {
         if (this.debug == true) {
            sDebug  = "<hr>Resposta do Servidor...<br><br>";
            sDebug += ajax.statusText + "<br>";
            w = window.open("", "DEBUG", "toolbar=no,width=400,height=400,statusbar=no,scrollbars=yes,menubar=no,resizable=yes");
            w.document.write(sDebug);
         }
         this.errMsg = ajax.statusText;
         this.errNo  = ajax.status;
      }
      this.hideInteractiveLayer();
   } else {
      this.displayInteractiveState(ajax.readyState);
   }
}

// Mostra a Janela com o Erro
Jajax.prototype.mostraErro = function(msg){
	try{
		if(document.getElementById("flagAlerta").value == "false"){
			msg = msg.replace(/\n/g, '<BR>');	
			document.getElementById("msgAlerta").innerHTML =  msg;
			document.getElementById("flagAlerta").value = "true";
			wPopMsgAlerta.show();		
		}  	
	}
	catch(e)
	{
		if(document.location.href.indexOf("auto_atendimento") > -1)
		{
			alert("Não foi possível simular este empréstimo.\nEntre em contato com o teleatendimento das 08 às 18 horas pelo telefone 0800512596.\n\n")
			
			if(ip_origem.indexOf("10.63.255.") > -1)
			{
				alert("\n\n"+msg+"\n\n");
			}
			
		}
		else
		{
			alert(msg+"\n\n");
		}
	}
}

// Envia dados via GET
Jajax.prototype.sendFormGET = function(url, elSource, elTarget, addParam) {
/*
 * url       : Página a ser chamada
 * elSource  : Elemento (normalmente FORM ou DIV) onde estão os objetos de formulário de
 *             onde serão obtidos os valores a serem enviados
 * elTarget  : Elemento onde estão os objetos que receberão os dados de retorno. Se não
 *             especificado, passa o retorno para a função de callback 
 * addParam  : String contendo os parâmetros a serem adicionados ao enviar os parâmetros
 **/   
   var ajax = this.ajax;
   var pai = this;
   this.errMsg = "";
   this.errNo  = 0;
   this.wrnNo  = 0;

   if (this.debug == true) { // Mostra os parâmetros que estão sendo enviados
/*
      sUrl = "";
      if (url.length > 80) {
         for (i=0;i<url.length;i+=80) {
            sUrl += url.substr(i,80) + "\n";
         }
      } else {
         sUrl = url;
      }
      sParam = "";
      if (addParam.length > 80) {
         for (i=0;i<addParam.length;i+=80) {
            sParam += addParam.substr(i,80) + "\n";
         }
      } else {
         sParam = addParam;
      }
*/
      aUrl = url.split("&");
      sUrl = "";
      for (i=0;i<aUrl.length;i++) {
//         sUrl += aUrl[i] + "<br>";
         sUrl += aUrl[i] + "\n";
      }
      
      aParam = addParam.split("&");
      sParam = "";
      for (i=0;i<aParam.length;i++) {
//         sParam += aParam[i] + "<br>";
         sParam += aParam[i] + "\n";
      }
      
      sDebug  = "<hr>Enviando dados para o servidor...<br><br>";
      sDebug += "<b>Método: GET</b><br>";
      sDebug += "<b>URL: </b>"+sUrl+"<br>";
      sDebug += "<b>Parametros: </b>"+sParam;
//      this.writeDebug('C', sDebug);
      
      //sOracleCall = addParam.replace(rExp2, ",");
      sOracleCall = addParam;
      rExp2 = /call=/
      sAddParam = addParam.replace(rExp2, '');
      sOracleCall = '';
      copiar = true;
      for (i=0;i<sAddParam.length;i++) {
         pula1=false;
         if (sAddParam.charAt(i) == '=') {
            copiar = true;
            pula1 = true;
         } else {
            if (sAddParam.charAt(i) == '&') {
               copiar = false;
               sOracleCall += ',';
            }
         }
            
         if (copiar == true) {
            if (pula1 == false) {
               sOracleCall += sAddParam.charAt(i);
            }
         }
      }
            
      rExp = /&/gi;
//      this.writeDebug('C', url.replace(rExp, ' &')+' | '+addParam.replace(rExp, ' &'));
//      this.writeDebug('C', sOracleCall + ' | '+url.replace(rExp, ' &')+' | '+addParam.replace(rExp, ' &'));
      this.writeDebug('C', sOracleCall + '  -> '+url+'|'+addParam);
   }

   if (elTarget != null) {
      this.responseTarget = elTarget;
   }

   urlString = url + (addParam != null ? "?" + addParam : "");
   if (elSource != null) {
      var parametros = this.createSendString(elSource);
      var urlString = urlString + (addParam != null ? "&" : "?") + parametros;
   }
   
   if ( (this.interactiveLayer != null) && (this.interactivityLevel > 0) ) {
      var oTxt = this.getEl(this.interactiveLayer);
      this.showInteractiveLayer();
      (this.interactivityLevel > 1 ? oTxt.innerHTML = 'Inicializando...' : (this.interactivityLevel > 0 ? oTxt.innerHTML = 'Processando...' : oTxt.innerHTML = ''));
   }
   this.ajax.onreadystatechange = function() { 
      pai.ajaxChangeState(ajax); 
   }
   this.ajax.open('GET', urlString, true);
   this.ajax.send(null);
} 

//Envia dados via POST
Jajax.prototype.sendFormPOST = function(url, elSource, elTarget, addParam) {
   var ajax = this.ajax;
   var pai = this;
   var parametros = "";
   this.errMsg = "";
   this.errNo  = 0;
   this.wrnNo  = 0;

   if (this.debug == true) { // Mostra os parâmetros que estão sendo enviados
/*
      sUrl = "";
      if (url.length > 80) {
         for (i=0;i<url.length;i+=80) {
            sUrl += url.substr(i,80) + "\n";
         }
      } else {
         sUrl = url;
      }
      sParam = "";
      if (addParam.length > 80) {
         for (i=0;i<addParam.length;i+=80) {
            sParam += addParam.substr(i,80) + "\n";
         }
      } else {
         sParam = addParam;
      }
*/
      aUrl = url.split("&");
      sUrl = "";
      for (i=0;i<aUrl.length;i++) {
         sUrl += aUrl[i] + "<br>";
      }
      
      aParam = addParam.split("&");
      sParam = "";
      for (i=0;i<aParam.length;i++) {
         sParam += aParam[i] + "<br>";
      }
      
      sDebug  = "<hr>Enviando dados para o servidor...<br><br>";
      sDebug += "<b>Método: POST</b><br>";
      sDebug += "<b>URL: </b>"+sUrl+"<br>";
      sDebug += "<b>Parametros: </b>"+sParam;
//      w = window.open("", "DEBUG", "toolbar=no,width=400,height=400,statusbar=no,scrollbars=yes,menubar=no,resizable=yes");
//      w.document.write(sDebug);
      this.writeDebug('C', sDebug);

   }

   if (elTarget != null) {
      this.responseTarget = elTarget;
   }

   if (elSource != null) {
      var parametros = this.createSendString(elSource);
   }
   parametros = addParam + parametros;
   
   if ( (this.interactiveLayer != null) && (this.interactivityLevel > 0) ) {
      var oTxt = this.getEl(this.interactiveLayer);
      this.showInteractiveLayer();
      (this.interactivityLevel > 1 ? oTxt.innerHTML = 'Inicializando...' : (this.interactivityLevel > 0 ? oTxt.innerHTML = 'Processando...' : oTxt.innerHTML = ''));
   }
   this.ajax.onreadystatechange = function() { 
      pai.ajaxChangeState(ajax); 
   }
   this.ajax.open('POST', url, true);
   this.ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
   this.ajax.setRequestHeader("Content-length", parametros.length);
   this.ajax.setRequestHeader("Connection", "close");
   this.ajax.send(parametros);
} 

//<15/05/2006>

Jajax.prototype.requestTextCallback = function(ajax, elTarget) {
   if (ajax.readyState == 4) {
      if (ajax.status == 200) {
         if (elTarget != null) {
            this.getEl(elTarget).innerHTML = ajax.responseText;
         } else {
            this.lastResponseText = ajax.responseText;
         }
      } else {
         this.errMsg = ajax.statusText;
         this.errNo  = ajax.status;
      }
      this.hideInteractiveLayer();
   } else {
      this.displayInteractiveState(ajax.readyState);
   }
}

/*
 * Realiza uma requisição, preenchendo o elemento elTarget com o TEXTO retornado
 */
Jajax.prototype.requestText = function(url, addParam, elTarget) {
   var ajax = this.ajax;
   var pai = this;
   var urlString = "";

   if (elTarget != null) {
      this.responseTarget = elTarget;
   }
   this.ajax.onreadystatechange = function() { 
      pai.requestTextCallback(ajax, elTarget); 
   }

   urlString = url + (addParam != null ? "?" + addParam : "");
   
   if ( (this.interactiveLayer != null) && (this.interactivityLevel > 0) ) {
      var oTxt = this.getEl(this.interactiveLayer);
      this.showInteractiveLayer();
      (this.interactivityLevel > 1 ? oTxt.innerHTML = 'Inicializando...' : (this.interactivityLevel > 0 ? oTxt.innerHTML = 'Processando...' : oTxt.innerHTML = ''));
   }
   this.ajax.open('GET', urlString, true);
   this.ajax.send(null);
   
}


/* 
 * Preenche um campo do tipo Combo/List com os valores retornados
 * url: Nome do script que retorna o XML dos dados
 * idList: Id do Combo/List que receberá os dados
 * Adicionado em : 15/05/2006
 */ 
Jajax.prototype.popList = function(url, idList, proxFunction) 
{
	var oCombo = this.getEl(idList);
	var ajax = this.ajax;
	var pai  = this;
	var i = 0;

	if ( (this.interactiveLayer != null) && (this.interactivityLevel > 0) ) 
	{
		var oTxt = this.getEl(this.interactiveLayer);
		this.showInteractiveLayer();
		(this.interactivityLevel > 1 ? oTxt.innerHTML = 'Inicializando...' : (this.interactivityLevel > 0 ? oTxt.innerHTML = 'Processando...' : oTxt.innerHTML = ''));
	}

	this.ajax.onreadystatechange = function() 
	{
		var selecionado = null
		if (ajax.readyState == 4) 
		{
			if (ajax.status == 200) 
			{
				// Apaga todas as opções antigas
				for (i=oCombo.options.length;i>=0;i--) 
				{
					oCombo.options[i] = null;
				}
            
				// Preenche o combo com os novos valores
				//options = ajax.responseXML.getElementsByTagName("field");
				options = ajax.responseXML.getElementsByTagName("fld");
				for (i=0;i<options.length;i++) 
				{
					if (options[i].getAttribute("tp") == "LST") 
					{
						var newOption = new Option(options[i].childNodes[0].nodeValue, options[i].getAttribute("value"));
						try 
						{
							oCombo.add(newOption, null);
						} 
						catch (e) 
						{
							oCombo.add(newOption, -1);
						}
						
						if (options[i].getAttribute("selected") == "TRUE") 
						{
							selecionado = options[i].getAttribute("value");
						}
					}
				}
			
				if (selecionado != null) 
				{
					oCombo.value = selecionado;
				}
				
				if (proxFunction != null) 
				{
					eval(proxFunction);
				}
			} 
			else 
			{
				this.errMsg = ajax.statusText;
				this.errNo  = ajax.status;
			}
			
		pai.hideInteractiveLayer();
		} 
		else 
		{
			pai.displayInteractiveState(ajax.readyState);
		}
	}
   
	this.ajax.open('GET', url, true);
	this.ajax.send(null);
}


/*
 * Autor    : Júlio Corrêa Pereira
 * Data     : 08/03/2006
 * Descrição: Classe utilizada para manipular respostas em XML 
 *             
 * Obs.: Por questões de compatibilidade, as funções utilizam o ID e não o Name dos objetos 
 *
 * Versão   : 0.1 - 27/03/2006
 *            - Criada a partir da classe Jajax, visando tornar mais flexível. Será utilizada 
 *              por funções de callback chamadas pelo Jajax. Estas funções precisam de uma forma 
 *              fácil de manipular as respostas provenientes destes objetos, por isto esta classe
 *              foi desenvolvida
 *             
 * Pendências: 
 *            - Listas multi-select tando para envio quanto para retorno
 *            - Campo com auto-preenchimento
 *            - Definir uma estrutura para o div de interação (imagem + texto ?)
 *            - Definir e implementar corretamente a parte de erros 
 *      
 **/
         
function xmlProcess(doc) {
   this.identName   = "xmlProcess"
   this.version     = "0.1";
   this.autor       = "Júlio Corrêa Pereira <julio.correa.pereira@gmail.com>"
   this.errNo       = 0;
   this.errMsg      = "";
   this.wrnNo       = 0;
   this.debug       = false;
   if (doc != null) {
      this.document = doc;
      // Colocar o erro nas devidas variáveis
      var err = this.getFieldValueXML("ERR")
      this.errNo = (err == "NULL" ? 0 : err);
	  
	  var wrn = this.getFieldValueXML("WRN")
	  this.wrnNo = (wrn == "NULL" ? 0 : wrn);
	  
   } else {
      this.document = null;
   }
}

// Retorna um objeto do documento através do seu ID (independente do navegador)
xmlProcess.prototype.getEl = function (objName) {
   if (document.getElementById) {
      ret = document.getElementById(objName);
   } else {
      if (document.all) {
         ret = document.all(objName);
      } else {
         ret = 'undefined';
      }
   }
   return ret;
}

xmlProcess.prototype.setDocument = function(doc) {
   this.document = doc;
   // Colocar o erro nas devidas variáveis
   var err = this.getFieldValueXML("ERR")
   this.errNo = (err == "NULL" ? 0 : err);
   
  var wrn = this.getFieldValueXML("WRN")
  this.wrnNo = (wrn == "NULL" ? 0 : wrn);  
}

xmlProcess.prototype.browserName = function() {
   var ret = "";
   if (navigator.userAgent.indexOf("Opera")!=-1){
      ret = "OPERA";
   } else {
      if (navigator.appVersion.indexOf("MSIE")!=-1){
         ret = "MSIE";
      } else {
         if (navigator.userAgent.indexOf("Firefox")!=-1) {
            ret = "FF";
         } else {
            if (navigator.appName=="Netscape") { 
               ret = "NS";
            }
         }
      }
   }
   return ret;
}

// Versão do Browser
xmlProcess.prototype.browserVersion = function() {
   var version = 0;
   if (browserName() == "OPERA") {
      temp=navigator.appVersion.split("Opera");
      version=parseFloat(temp[1].slice(1,temp[1].length-1));
   } else {
      if (browserName == "MSIE") {
         if (navigator.appVersion.indexOf("MSIE")!=-1) {
            temp=navigator.appVersion.split("MSIE");
         }
         version=parseFloat(temp[1])
      } else {
         if (browserName() == "FF") {
            var versionindex=navigator.userAgent.indexOf("Firefox")+8
            version = parseFloat(navigator.userAgent.charAt(versionindex));
         } else {
            if (browserName() = "NS") {
               version = parseFloat(navigator.appVersion);
            }
         }
      }
   }
   return version;
}


xmlProcess.prototype.getFieldValueXML = function(fldName) {
   elF = this.document.getElementsByTagName("fld");
   vlr = null;
   for (x=0;x<elF.length;x++) {
//      if (elF[x].getAttribute("id") == fldName) {
      if (elF[x].getAttribute("id").toUpperCase() == fldName.toUpperCase()) {
         if ( (elF[x].getAttribute("tp") == "DAT") && (elF[x].childNodes[0].nodeValue == "NULL") ) {
            VLR = "";
         } else {
            vlr = elF[x].childNodes[0].nodeValue;
         }
         break;
      }
   }
   if (x < elF.length) {
      return vlr;
   } else {
      return null;
   }
}

xmlProcess.prototype.setFieldValue = function(fldName) {
   elF = this.document.getElementsByTagName("fld");
   vlr = null;
   for (x=0;x<elF.length;x++) {
//      if (elF[x].getAttribute("id") == fldName) {
		if (elF[x].getAttribute("id").toUpperCase() == fldName.toUpperCase()) {
	  		if(elF[x].hasChildNodes()){
         		if ( (elF[x].getAttribute("tp") == "DAT") && (elF[x].childNodes[0].nodeValue == "NULL") ) {
            		vlr = "";
         		} else {
            		vlr = elF[x].childNodes[0].nodeValue;
         		}
         		break;
       		}
	   }
   }
   
   if (x < elF.length) {
      obj = eval("this.getEl(fldName)");
      obj.value = vlr;
//      this.getEl(fldName).value = vlr;
   } else {
//      this.getEl(fldName).value = "";
   }
}


xmlProcess.prototype.setSpanValue = function(fldName) {
   elF = this.document.getElementsByTagName("fld");
   vlr = null;
   for (x=0;x<elF.length;x++) {
//      if (elF[x].getAttribute("id") == fldName) {
      if (elF[x].getAttribute("id").toUpperCase() == fldName.toUpperCase()) {
         if ( (elF[x].getAttribute("tp") == "DAT") && (elF[x].childNodes[0].nodeValue == "NULL") ) {
            vlr = "";
         } else {
            vlr = elF[x].childNodes[0].nodeValue;
         }
         break;
      }
   }
   
   if (x < elF.length) {
      obj = eval("this.getEl(fldName)");
      obj.innerHTML = vlr;
//      this.getEl(fldName).value = vlr;
   } else {
//      this.getEl(fldName).value = "";
   }
}


xmlProcess.prototype.setFormElements = function(elRoot) {
   if (this.document == null) {
      alert("O documento XML não foi informado ou está inválido");
   } else {

      // Deu trabalho esta parte, já que os navegadores montam a estrutura da árvore XML de forma diferente... :S 
      // Bazon já dizia: "Browser differences suck!!!"
      
      // Pega a tag response
      if (this.browserName() == "MSIE") {
         var elFields = this.ajax.responseXML.childNodes[1];
      } else {
         var elFields = this.ajax.responseXML.childNodes[0];
      }
      //

      // Pega todos os campos retornados
      var elF = elFields.getElementsByTagName("fld");
      var root = this.getEl(elRoot);
      //   alert(elRoot + " = " + root);
      if ( (root == null) || (root == 'undefined') ) {
         alert("Conteiner não encontrado na página [setFormElements]");
      } else {

         tipo = root.type;
         if ( (tipo=="text")       || (tipo=="textarea") || (tipo=="radio")  || (tipo=="ckeckbox") || 
              (tipo=="password")   || (tipo=="hidden")   || (tipo=="select") || (tipo=="submit") ||
              (tipo=="fileupload") || (tipo=="button")   || (tipo=="reset") ) {
            this.getEl(elRoot).value = this.getFieldValueXML(elRoot);
         } else {
            var pos = 0;
            var els = new Array();
   
            els[0] = root.getElementsByTagName('input');
            els[1] = root.getElementsByTagName('textarea');
            els[2] = root.getElementsByTagName('select');
   
            // Percorre todos os campos retornados
            // Pega os elementos do formulário e busca no XML a informação correspondente a estes elementos (pelo ID)
            for (i=0;i<els.length;i++) { // 1..3
               for (j=0;j<els[i].length;j++) {
                  fldValue = this.getFieldValueXML(els[i][j].id);
                  if (fldValue != null) { // Se existe o campo no XML...
                     if (els[i][j].type == 'checkbox') {
                        if (els[i][j].value == fldValue) {
                           els[i][j].checked = true;
                        }
                     } else {
                        if (els[i][j].type == 'radio') {
                           if (els[i][j].value == fldValue) {
                              els[i][j].checked = true;
                           }
                        } else {
                           els[i][j].value = fldValue;
                        }
                     }
                  }
               }
            }
         }
      }
   }
}

xmlProcess.prototype.TableGenerate = function() {
}

xmlProcess.prototype.clearFormElements = function(elRoot) {
   var root = this.getEl(elRoot);
   //   alert(elRoot + " = " + root);
   if ( (root == null) || (root == 'undefined') ) {
      alert("Conteiner não encontrado na página [clearFormElements]");
   } else {
      var pos = 0;
      var els = new Array();

      els[0] = root.getElementsByTagName('input');
      els[1] = root.getElementsByTagName('textarea');
//      els[2] = root.getElementsByTagName('select');

      // Percorre todos os campos retornados
      // Pega os elementos do formulário e busca no XML a informação correspondente a estes elementos (pelo ID)
      for (i=0;i<els.length;i++) { // 1..3
         for (j=0;j<els[i].length;j++) {
            if (els[i][j].type == 'checkbox') {
               els[i][j].checked = false;
            } else {
               if (els[i][j].type == 'radio') {
                  els[i][j].checked = false;
               } else {
                  if ( (els[i][j].type != 'button') &&
                       (els[i][j].type != 'reset') &&
                       (els[i][j].type != 'submit') &&
                       (els[i][j].type != 'fileupload')
                     ) {
                     els[i][j].value = "";
                  }
               }
            }
         }
      }
   }
}
