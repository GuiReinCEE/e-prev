   var waitingResponse = false;
   var waitingResponse = false;
   var ie  = document.all;
   var nn6 = document.getElementById&&!document.all;
   
   YAHOO.namespace("aqua");
   
	function init() {
      wPopDecomposicaoSalEmp = new YAHOO.widget.Panel("divDecomposicaoSalEmp", { effect:{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.25}, width:"400px", height:"284px", fixedcenter: true, constraintoviewport: true, underlay:"none", close:true, visible:false, draggable:true, modal:true } );
      wPopDecomposicaoSalEmp.render();
      wPopReforma13 = new YAHOO.widget.Panel("divReforma13", { effect:{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.25}, width:"380px", height:"250px", fixedcenter: true, constraintoviewport: true, underlay:"none", close:true, visible:false, draggable:true, modal:true } );
      wPopReforma13.render();
      wPopDebitos = new YAHOO.widget.Panel("divDebitos", { effect:{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.25}, width:"700px", height:"400px",  fixedcenter: true, constraintoviewport: true, underlay:"none", close:true, visible:false, draggable:true, modal:true } );
      wPopDebitos.render();
      wPopImprimir = new YAHOO.widget.Panel("divImprimir", { effect:{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.25}, width:"217px", fixedcenter: true, constraintoviewport: true, underlay:"none", close:true, visible:false, draggable:true, modal:true } );
      wPopImprimir.render();
  	  wPopMsgAlerta = new YAHOO.widget.Panel("divMsgAlerta", { effect:{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.25}, width:"20em", fixedcenter: true, constraintoviewport: true, underlay:"none", close:true, visible:false, draggable:true, modal:true } );
      wPopMsgAlerta.render();

      wPopMotivoConcEspecial = new YAHOO.widget.Panel("divMotivoConcEspecial", { effect:{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.25}, width:"420px", fixedcenter: true, constraintoviewport: true, underlay:"none", close:false, visible:false, draggable:true, modal:true } );
      wPopMotivoConcEspecial.render();

      wPopAdequacaoSalarial = new YAHOO.widget.Panel("divAdequacaoSalarial", { effect:{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.25}, width:"500px", height:"400px", fixedcenter: true, constraintoviewport: true, underlay:"none", close:true, visible:false, draggable:true, modal:true } );
      wPopAdequacaoSalarial.render();

      wPopDetalhes = new YAHOO.widget.Panel("divDet", { effect:{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.25}, width:"700px", height:"400px",   fixedcenter: true, constraintoviewport: true, underlay:"none", close:true, visible:false, draggable:true, modal:true } );
      wPopDetalhes.render();
	  
      wPopEmprestimoAnterior = new YAHOO.widget.Panel("divEmprestimoAnterior", { effect:{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.25}, width:"700px", height:"400px",   fixedcenter: true, constraintoviewport: true, underlay:"none", close:true, visible:false, draggable:true, modal:true } );
      wPopEmprestimoAnterior.render();

	  // Alterado modo de demonstrar o AGUARDE (da outra forma a imagem ficava deformada)
 	  wPopAguarde = new YAHOO.widget.Panel("divAguarde", { effect:{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.25}, width:"20em", fixedcenter: true, constraintoviewport: true, underlay:"none", close:true, visible:false, draggable:false, modal:true } );
      wPopAguarde.render();
	  
	  
		// evolucao posfixado 20/01/2010
		wPopEvolucaoPosFixado = new YAHOO.widget.Panel("divEvolucaoPosFixado", { effect:{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.25}, width:"730px", height:"400px",  fixedcenter: true, constraintoviewport: true, underlay:"none", close:true, visible:false, draggable:true, modal:true } );
		wPopEvolucaoPosFixado.render();	  
	}

	YAHOO.util.Event.addListener(window, "load", init);

   function maximize() {
      javascript:moveTo(0,0);
      window.resizeTo(screen.availWidth,screen.availHeight);
   }   

   function showAguarde() {
      wPopAguarde.show();
   }
   
   function hideAguarde() {
      wPopAguarde.hide();
   }

   function validaKeyPress(e) {
      var ev  = nn6 ? e : event;
      var obj = nn6 ? ev.target : ev.srcElement;
      var k   = ev.keyCode ? ev.keyCode : ev.which ? ev.which : ev.charcode;

      if ( (k == 8) || (k == 9) || ((k > 31) && (k < 41)) || (k == 45) || (k == 46) ) { // Teclas de navegação
         return true;
      } else {
         if (obj.type == 'text') {
            if (obj.getAttribute('datatype')) {
               switch (obj.getAttribute('datatype')) {
                  case 'integer' :if ( (k > 47) && (k < 58) ) {
                                     return true;
                                  } else {
                                     return false;
                                  } 
                                  break;
                  case 'decimal' :if ( ((k > 47) && (k < 58)) || (k == 44) || (k == 46) ) {
                                     return true;
                                  } else {
                                     return false;
                                  } 
                                  break;
                  case 'date'    :if ( ((k > 47) && (k < 58)) || (k == 47) ) {
                                     return true;
                                  } else {
                                     return false;
                                  } 
                                  break;
                  case 'datetime':if ( ((k > 47) && (k < 58)) || (k == 47) || (k == 32) || (k == 58) ) {
                                     return true;
                                  } else {
                                     return false;
                                  } 
                                  break;
                  case 'email'   :if ( ((k > 47) && (k < 58)) || ((k > 43) && (k < 47)) || ((k > 64) && (k < 91)) || ((k > 60) && (k < 123)) || (k == 95) || (k == 64) ) {
                                     return true;
                                  } else {
                                     return false;
                                  } 
                                  break;
                  case 'telefone':if ( ((k > 47) && (k < 58)) || (k == 45) || (k == 40) || (k == 41) ) {
                                     return true;
                                  } else {
                                     return false;
                                  } 
                                  break;
                  default        :return true;
                                  break;
               }
            } else {
               return true;
            }
         } else {
            return true;
         }
      }
   }

   function setFocus(obj) {
      globalvar = obj;
      setTimeout("globalvar.focus()",100);
//      globalvar.focus();
   }
//FUNÇÃO INCLUIDA EM 04/12/2006   
   function calculaSimulacao(){
   		if ((getEl('dt_pagamento').value)==(getEl('dt_pagamento_anterior').value)){
  			realizaSimulacao(false, getEl('opTipoCalculo').value);
   		} else {
			realizaSimulacao(false);
		}   
   	}
   
   function toNull(vlr) {
      if ((vlr == null) ||(vlr == '')) {
         return 'NULL';
      } else {
         return vlr;
      }
   }

   function centerElement(elName) {
      obj = getEl(elName);
      var pageWidth = document.body.offsetWidth ? document.body.offsetWidth : window.innerWidth;
      var pageHeight = document.body.offsetHeight ? document.body.offsetHeight : window.innerHeight;
      obj.style.left = ((pageWidth - obj.offsetWidth) /2)+'px';
      obj.style.top = ((pageHeight - obj.offsetHeight) /2)+'px';
   }

   function chkValue(id, vTrue, vFalse) {
      obj = getEl(id);
      if (obj.type == 'checkbox') {
         if (obj.checked) {
            return vTrue;
         } else {
            return vFalse;
         }
      }
   }

   function highlightTipoCalculo(obj, val) {
      if (val == 'on') {
         obj.className = "highlightOn";
      } else {
         sel = getEl('opSimulacao').value;
         if ( (! ((obj.id=='opTCL') && (getEl('opTipoCalculo').value=='L')) ) &&
              (! ((obj.id=='opTCS') && (getEl('opTipoCalculo').value=='S')) ) &&
              (! ((obj.id=='opTCP') && (getEl('opTipoCalculo').value=='P')) )
            ) {
               obj.className = "highlightOff";
         }
      }
   }

	function atualizaHighlight() 
	{
		var tb = document.getElementById('tabPrestacoes');
		var linhas = tb.getElementsByTagName('tr');

		for (i = 1; i < linhas.length;i ++) 
		{
			highlight(linhas[i], 'off');
		}
	}

	function highlight(obj, val) 
	{
		if (val == 'on') 
		{
			obj.className = "tabDetHighlight";
		} 
		else 
		{
			var nr_parc = obj.id.replace("opSim_","");
			
			if(nr_parc != document.getElementById('num_prestacoes').value)
			{
				obj.className = "tabDetalhe";
			}
		}
	}

   function showMessageIOF(v) {
      if (v == true) {
         getEl('divMsgIOF').style.display='';
      } else {
         getEl('divMsgIOF').style.display='none';
      }
   }
   
   function troca_div_emp_ferias(tipo_emp) {
      if (tipo_emp == 2) {
         getEl('divParametrosCalculo_np').style.display='';
		 getEl('divParametrosCalculo_dp').style.display='none';
      } else {
	  	 getEl('divParametrosCalculo_np').style.display='none';
         getEl('divParametrosCalculo_dp').style.display='';
      }
   }
   
   function setTipoCalculo(op) {
      getEl('opTipoCalculo').value = op;
      getEl('num_prestacoes').value = '';

      highlightTipoCalculo(getEl('opTCL'), 'off');
      highlightTipoCalculo(getEl('opTCS'), 'off');
      highlightTipoCalculo(getEl('opTCP'), 'off');

      switch(op) {
         case 'P':highlightTipoCalculo(getEl('opTCP'), 'on'); 
                  getEl('valor').value = ''; 
                  showMessageIOF(false); 
//                  realizaSimulacao(true);
                  realizaSimulacao(false);
                  break;
         case 'S':highlightTipoCalculo(getEl('opTCS'), 'on'); 
                  getEl('valor').value = getEl('vlr_max_concessao').value; 
                  showMessageIOF(false); 
//                  realizaSimulacao(true);
                  realizaSimulacao(false);
                  break;
         case 'L':highlightTipoCalculo(getEl('opTCL'), 'on'); 
                  getEl('valor').value = ''; 
                  showMessageIOF(true); 
//                  realizaSimulacao(true, 'L');
//                  realizaSimulacao(true);
                  realizaSimulacao(false);
//                  realizaSimulacao(false, 'L');
                  break;
      }
   }

   function callbackUpdateOrigem(ret) {
      var x = new xmlProcess(ret.responseXML);
      ret = x.getFieldValueXML('sucesso');
      if (ret == 'FALSE') {
//         alert("Ocorreu um erro ao tentar alterar a origem");
         mostraAlerta("Erro","Ocorreu um erro ao tentar alterar a origem");
      }
   }
   
   function updateOrigem() {
      // Atualiza o campo Origem no BD, evitando a necessidade de 
      // um recálculo das prestações apenas pelo fato de alterar este campo
      var param = '';
      param  = "&sessao="+getEl("session_id").value;
      param += "&cd_empresa="+getEl('cd_empresa').value;
      param += "&cd_registro_empregado="+getEl('cd_registro_empregado').value;
      param += "&seq_dependencia="+getEl('seq_dependencia').value;		 
      param += "&o="+getEl('origem').value;
      xOrigem = new Jajax();
      xOrigem.setInteractiveLayer('divAndamento');
      xOrigem.setCallback('callbackUpdateOrigem');
      xOrigem.sendFormGET('proxyEmprestimo.php', null, null, 'call=fnc_atualiza_origem'+param);
   }

   function callbackUpdateFormaPgtoFceee(ret) {
      var x = new xmlProcess(ret.responseXML);
      ret = x.getFieldValueXML('sucesso');
      if (ret == 'FALSE') {
//         alert("Ocorreu um erro ao tentar alterar a origem");
         mostraAlerta("Erro","Ocorreu um erro ao tentar alterar a origem");
      }
   }
   
   function updateFormaPgtoFceee() {
      // Atualiza o campo Origem no BD, evitando a necessidade de 
      // um recálculo das prestações apenas pelo fato de alterar este campo
      var param = '';
      param  = "&sessao="+getEl("session_id").value;
      param += "&cd_empresa="+getEl('cd_empresa').value;
      param += "&cd_registro_empregado="+getEl('cd_registro_empregado').value;
      param += "&seq_dependencia="+getEl('seq_dependencia').value;		 
      param += "&f="+getEl('forma_pgto_fundacao').value;
      xOrigem = new Jajax();
      xOrigem.setInteractiveLayer('divAndamento');
      xOrigem.setCallback('callbackUpdateFormaPgtoFceee');
      xOrigem.sendFormGET('proxyEmprestimo.php', null, null, 'call=fnc_atualiza_pgto_fceee'+param);
   }

   function ExCtpReduc() 
   {
   //Garcia - 28/09/2006		
   		if (getEl('ex_ctp_reduc').checked) 
		{
			getEl('considera_ex_ctp_reduc').value = getEl('ex_ctp_reduc').value;
		}	
		else 
		{	
			getEl('considera_ex_ctp_reduc').value = 'N';
		}
	     realizaSimulacao(true);
   }
   
	function adequacaoSalarial() 
	{
		if (getEl('adequacao_salarial').checked) 
		{
			setRadioValue('seMotivo', getEl('motivo_adeq_salarial').value, 'divAdequacaoSalarial');
			getEl('seObs').value = getEl('observacao_adeq_salarial').value;
			getEl('seAtendente').value = getEl('atendente_adeq_salarial').value;
			getEl('seSalarioAdeqSalarial').value = getEl('salario_adeq_salarial').value;
			wPopAdequacaoSalarial.show();
		} 
		else 
		{
			realizaSimulacao(true);
		}
	}

   function alteraAdequacaoSalarial() {
      getEl('motivo_adeq_salarial').value = getRadioValue('seMotivo', 'divAdequacaoSalarial');
      getEl('observacao_adeq_salarial').value = getEl('seObs').value;
      getEl('atendente_adeq_salarial').value = getEl('seAtendente').value;
      getEl('salario_adeq_salarial').value = getEl('seSalarioAdeqSalarial').value;
      obj = getEl('divAdequacaoSalarial');
//      realizaSimulacao(true);
      wPopAdequacaoSalarial.hide();
      realizaSimulacao(false);
   }
   
	function alteraMotivoConcEspecial() 
	{
		
		if (getEl('mceMotivo').value != '') 
		{
			getEl('motivo_conc_especial').value = getEl('mceMotivo').value;
			wPopMotivoConcEspecial.hide();
			realizaSimulacao(false);
		} 
		else 
		{
			getEl('concessao_especial').checked = false;
			getEl('mceMotivo').value = '';
			alert("ATENÇÃO\n\nInforme o motivo da Concessão Especial");
		}
	}

	function cancAlteraMotivoConcEspecial() 
	{
		getEl('concessao_especial').checked = false;
		getEl('mceMotivo').value = '';
		wPopMotivoConcEspecial.hide();
	}
   
   function concessaoEspecial(v) {
      if (v.checked) {
         getEl('tipo_risco').disabled = false;
      } else {
         getEl('tipo_risco').disabled = true;
      }
      if (getEl('concessao_especial').checked) {
         wPopMotivoConcEspecial.show();
/*
         setRadioValue('seMotivo', getEl('motivo_adeq_salarial').value, 'divAdequacaoSalarial');
         getEl('seObs').value = getEl('observacao_adeq_salarial').value;
         getEl('seAtendente').value = getEl('atendente_adeq_salarial').value;
         getEl('seSalarioAdeqSalarial').value = getEl('salario_adeq_salarial').value;
         wPopMotivoConcEspecial.show();
*/
      } else {
		 buscaParticipante();
//         realizaSimulacao(false); Alterado dia 16/11/2006 Cleisson
      }
   }

/*
   function callbackEmprestimoAnterior(ret) {
      getEl('divPrestEmpAnterior').innerHTML = ret.responseText;
      wPopEmprestimoAnterior.show();
   }
*/
   
   function popEmprestimoAnterior() {
      var param = "sessao="+getEl("session_id").value;
      xPopAnt = new Jajax();
      xPopAnt.setInteractiveLayer('divAndamento');
      xPopAnt.requestText("popEmprestimoAnterior.php", param, 'divPrestEmpAnterior');
      wPopEmprestimoAnterior.show();
   }
   
   function popDecomposicaoSalEmp() {
      xPopDec = new Jajax();
      xPopDec.setInteractiveLayer('divAndamento');
      xPopDec.sendFormGET('proxyEmprestimo.php', null, 'divDecomposicaoSalEmp', 'call=fnc_pop_decomposicao_sal_emp'+'&sessao='+getEl('session_id').value);
      wPopDecomposicaoSalEmp.show();
   }

   function popReforma13() {
      xPopDec = new Jajax();
      xPopDec.setInteractiveLayer('divAndamento');
      xPopDec.sendFormGET('proxyEmprestimo.php', null, 'divReforma13', 'call=fnc_pop_reforma13'+'&sessao='+getEl('session_id').value);
      wPopReforma13.show();
   }
   
   function popImprimir(d) {
      getEl('e').value = getEl('cd_empresa').value;
      getEl('r').value = getEl('cd_registro_empregado').value;
      getEl('s').value = getEl('seq_dependencia').value;
      getEl('d').value = d;
      if ( (getEl('origem').value == 'C') || (getEl('origem').value == 'A') ) {
         getEl('call').value = 'S';
      } else {
         getEl('call').value = 'N';
      }
      wPopImprimir.show();
   }

   
	function imprimeProposta() 
	{
		if(getEl('pro').checked)
		{		
			var ds_url = "contrato_emprestimo.php";
				ds_url += "?cd_emp=" + getEl('cd_empresa').value;
				ds_url += "&cd_re=" + getEl('cd_registro_empregado').value;
				ds_url += "&cd_seq=" + getEl('seq_dependencia').value;
				
				if(getEl('t').checked)
				{
					ds_url += "&fl_cad=S&fl_ass=S&nr_via=2&tp_imp=1";
				}
				else
				{
					ds_url += "&fl_cad=S&fl_ass=S&nr_via=2&tp_imp=0";
				}
				
//cd_emp=9&cd_re=7536&cd_seq=0&fl_cad=S&fl_ass=S&tp_imp=1&nr_via=2				
				
			var nr_width  = (screen.width - 10);
			var nr_height = (screen.height - 80);
			var nr_left = ((screen.width - 10) - nr_width) / 2;
			var nr_top = ((screen.height - 80) - nr_height) / 2;

			window.open(ds_url, "wViewContrato", "left="+nr_left+",top="+nr_top+",width="+nr_width+",height="+nr_height+",scrollbars=yes,resizable=yes,directories=yes,location=yes,menubar=yes,status=no,titlebar=no,toolbar=yes");		 									
		}
	}   
   
	function imprimeNotaPromissoria() 
	{
		if(getEl('np').checked)
		{
			//gera_proposta_emprestimo.php?np=S&e=9&r=7536&s=0&d=N&a=N&call=N&Submit11=Imprimir		
			ds_url = 'gera_proposta_emprestimo.php?c=&np=S';
			ds_url += "&e=" + getEl('cd_empresa').value;
			ds_url += "&r=" + getEl('cd_registro_empregado').value;
			ds_url += "&s=" + getEl('seq_dependencia').value;		
			ds_url += "&d=" + getEl('d').value;		
			ds_url += "&a=" + getEl('a').value;		
			ds_url += "&call=" + getEl('call').value;		

				
			window.open(ds_url);
		}
	}   
   
/*
   // Alterar a origem do empréstimo
   function updateOrigem() {
      xOrigem = new Jajax();
      xOrigem.setInteractiveLayer('divAndamento');
      xOrigem.sendFormGET('proxyEmprestimo.php', null, 'divDecomposicaoSalEmp', 'call=fnc_pop_decomposicao_sal_emp'+'&sessao='+getEl('session_id').value);
   }
*/

   // Obtem o valor do radiobutton selecionado de uma lista. Para agrupá-los,  a propriedade "name" deve ser igual 
   // em todos os radios que compõem a lista. O ID deve ser diferente
   function getRadioValue(vName, vRoot) {
      if (vRoot == null) {
         lObj = document.getElementsByTagName('input');
      } else {
         lObj = getEl(vRoot).getElementsByTagName('input');
      }
      var ret = null;
      for (i=0;i<lObj.length;i++) {
         if ( (lObj[i].type == 'radio') && (lObj[i].name == vName) && (lObj[i].checked) ) {
            ret = lObj[i].value;
         }
      }
      return ret;
   }
   
   // Seleciona o radiobutton selecionado de uma lista pelo valor. Para agrupar radiobuttons, a propriedade "name" 
   // deve ser igual em todos os radios que compõem a lista. O ID deve ser diferente.
   function setRadioValue(vName, vValue, vRoot) {
      if (vRoot == null) {
         lObj = document.getElementsByTagName('input');
      } else {
         lObj = getEl(vRoot).getElementsByTagName('input');
      }
      var ret = null;
      for (i=0;i<lObj.length;i++) {
         if ( (lObj[i].type == 'radio') && (lObj[i].name == vName) && (lObj[i].value == vValue) ) {
            lObj[i].checked = true;
         }
      }
      return ret;
   }
   
	function mostraErro(txt) 
	{
		if(txt != "")
		{
			var o = getEl('divErro');
			o.innerHTML = txt;
			o.style.display="show";
		}
	}

   function limpaErro() {
      o.innerHTML = "";
      o.style.display="none";
   }
   function limpaErros() {
	 document.getElementById("flagAlerta").value = "false";
   }

   function getEl(o) {
      if (document.getElementById) { 
         return document.getElementById(o);
      } else {
         return document.documentall(o);
      }
   }

   function isMinimized(o) {
      if (o.style.height == '0px') {
         return true;
      } else {
         return false;
      }
   }

   function setHeight(o, h) {
      o.style.height = h;
   }

   function setVisibility(o, valor) {
      if (valor == true) {
         o.style.visibility = "visible";
         o.style.display = "";
      } else {
         o.style.visibility = "hidden"; // Visibility mantem o espaço, apenas esconde o elemento
         o.style.display = "none"; // Display esconde o elemento e desocupa o espaço
      }
   }

   function alternaHeight(o, h) {
      obj = getEl(o);
      if (isMinimized(obj)) {
         setHeight(obj, h+'px');
         setVisibility(obj, true);
      } else {
         setHeight(obj,'0px');
         setVisibility(obj, false);
      }
   }

   function prestMouseOver(e) {
      if (!e) var e = window.event;
      if (e.target) {
         obj = e.target;
      } else {
         if (e.srcElement) {
            obj = e.srcElement;
         }
      }
      highlight(obj.parentNode, 'on');
   }
   
   function prestMouseOut(e) {
      if (!e) var e = window.event;
      if (e.target) {
         obj = e.target;
      } else {
         if (e.srcElement) {
            obj = e.srcElement;
         }
      }
      highlight(obj.parentNode, 'off');
   }

   
	function selecionaParcelamento(nr_opcao,obj) 
	{
		getEl('valor').value = '';
		getEl('num_prestacoes').value = nr_opcao;
		getEl('opSimulacao').value = nr_opcao;
		atualizaHighlight();
		highlight(obj, "on");
	}   


	function getDetalhes(nro_prestacoes)
	{
		var param = 'sessao=' + document.getElementById('session_id').value +'&nro_prestacoes=' + nro_prestacoes;
        var xDet = new Jajax();
			xDet.setInteractiveLayer('divAndamento');
			xDet.sendFormGET('proxyEmprestimo.php', null, 'divDet', 'call=fnc_busca_detalhes_prest_sim'+'&'+param);
        wPopDetalhes.show();	
	}
	
   
   function numPrestacoesChange() {
      getEl('valor').value = '';
   } 
   
   function valorChange() {
      getEl('num_prestacoes').value = '';
   }
   
   function troca_dt_pagamento() {
      getEl('dt_pagamento_oculta').value = getEl('dt_pagamento').value;
   }

   function mostraDetalhesPrest(ret) {
      xml = new xmlProcess(ret.responseXML);
      if (xml.errNo != 0) {
         getEl('divErro').innerHTML = xml.errMsg;
      } else {
         xml.setFieldValue('juros');
         xml.setFieldValue('vlr_concedido');
         wPopDetalhes.show();
      }
   }


   // ------------------ FUNÇÕES DO EMPRÉSTIMO ---------------------
/*
   function testaRetorno(res) {
      alert(res.responseText);
   }
*/
	function mostraAlerta(titulo,msg){
		if (msg == null)
		{
			msg = "Erro desconhecido";
		}
		
		msg = msg.replace(/\n/g, '<BR>');
		document.getElementById("tituloAlerta").innerHTML = titulo;
		document.getElementById("msgAlerta").innerHTML = msg;
  		wPopMsgAlerta.show();
	}

   function buscaNomeEmpresa(obj) {
      if (obj.value == '') {
         //alert("A empresa deve ser informada");
 		 mostraAlerta("Erro","A empresa deve ser informada");
         setFocus(obj);
      } else {
         x = new Jajax();
         x.setInteractiveLayer('divAndamento');
         x.sendFormGET('proxyEmprestimo.php', obj.name, 'nome_empresa', 'call=fnc_busca_empresa'); 
      }
   }

/*
   function informacoesEmprestimo(ret) {
      alert(ret.responseText);
   }
*/

   function montaParametrosEmprestimo(vDefault, vTipoSim) {
  
      // Se vDefault == true, assume que é a entrada no sistema e utiliza os valores default na simulação
/*
      var vMotivo = '';
      if (getEl('seMotivoO').checked) {
         vMotivo = 'O';
      }
      if (getEl('seMotivoM').checked) {
         vMotivo = 'M';
      }
      if (getEl('seMotivoV').checked) {
         vMotivo = 'V';
      }
*/
      var paramEmp = "";
      paramEmp  = "sessao="+getEl("session_id").value;
      paramEmp += "&cd_empresa="+getEl('cd_empresa').value;
      paramEmp += "&cd_registro_empregado="+getEl('cd_registro_empregado').value;
      paramEmp += "&seq_dependencia="+getEl('seq_dependencia').value;		 
//      paramEmp += "&ip="+getEl('ip').value;
//      paramEmp += "&tipo_emprestimo="+getEl("tipo_emprestimo").value;
      paramEmp += "&tipo_emprestimo=2";
//      paramEmp += "&origem="+(vDefault == true ? "N" : getEl("origem").value);
      paramEmp += "&origem="+getEl("origem").value;
      paramEmp += "&data_simulacao="+getEl("data_simulacao").value;        // ????????
      paramEmp += "&forma_pgto_fundacao="+getEl("forma_pgto_fundacao").value;
      paramEmp += "&dt_deposito="+(vDefault == true ? "NULL" : getEl("dt_deposito").value);
      paramEmp += "&tipo_risco="+(vDefault == true ? "NULL" : getEl("tipo_risco").value);
      paramEmp += "&conc_especial="+chkValue('concessao_especial','S','N');
      paramEmp += "&renegociacao="+chkValue('renegociacao','S','N');
      paramEmp += "&adiantamento_13="+chkValue('adiantamento13', 'S', 'N');
      
	  //paramEmp += "&adeq_salarial="+chkValue('adequacao_salarial','S','N');
	  paramEmp += "&adeq_salarial="+chkValue('adequacao_salarial','S','N');
	  
      paramEmp += "&motivo_adeq_salarial="+(vDefault == true ? "NULL" : getEl("motivo_adeq_salarial").value);
      paramEmp += "&pagar_emp_anterior="+chkValue('sim_pagar_emp_anterior','S','N');
      paramEmp += "&pagar_1_3="+chkValue('sim_pagar_1_3', 'S', 'N');
      paramEmp += "&pagar_prest_atraso="+chkValue('sim_pagar_prestacoes_atrasadas','S','N');
//      paramEmp += "&tipo_simulacao="+(vDefault == true ? "M" : (vTipoSim == "M" ? "M" : getEl("opTipoCalculo").value));
      paramEmp += "&tipo_simulacao="+(((vDefault == true)&&(vTipoSim==null)) ? "M" : (vTipoSim == "M" ? "M" : getEl("opTipoCalculo").value));
//      paramEmp += "&tipo_simulacao="+getEl("opTipoCalculo").value;
      paramEmp += "&num_prestacoes="+(vDefault == true ? "NULL" : (vTipoSim == "M" ? "NULL" : getEl("num_prestacoes").value));
      paramEmp += "&valor="+(vDefault == true ? "NULL" : (vTipoSim == "M" ? "NULL" : swap_pv(getEl("valor").value)));
      paramEmp += "&retorno_ferias="+chkValue('retorno_ferias','S','N');
//      paramEmp += "&motivo_conc_especial="+(vDefault == true ? "NULL" : vMotivo);
      /*
	  paramEmp += "&motivo_conc_especial="+(vDefault == true ? "NULL" : getEl("motivo_conc_especial").value);
      paramEmp += "&observacao_adeq_salarial="+(vDefault == true ? "NULL" : getEl("observacao_adeq_salarial").value);
      paramEmp += "&atendente_adeq_salarial="+(vDefault == true ? "NULL" : getEl("atendente_adeq_salarial").value);
      paramEmp += "&salario_adeq_salarial="+(vDefault == true ? "NULL" : swap_pv(getEl("salario_adeq_salarial").value));
	  */

	  
	if(document.getElementById('concessao_especial').checked)
	{
		paramEmp += "&motivo_conc_especial="+document.getElementById('motivo_conc_especial').value;
	}
	else
	{
		paramEmp += "&motivo_conc_especial=NULL"
	}
	
	//paramEmp += "&motivo_conc_especial="     +(chkValue('adequacao_salarial','S','N') == 'N' ? "NULL" : getEl("motivo_conc_especial").value);
      paramEmp += "&observacao_adeq_salarial=" +(chkValue('adequacao_salarial','S','N') == 'N' ? "NULL" : getEl("observacao_adeq_salarial").value);
      paramEmp += "&atendente_adeq_salarial="  +(chkValue('adequacao_salarial','S','N') == 'N' ? "NULL" : getEl("atendente_adeq_salarial").value);
      paramEmp += "&salario_adeq_salarial="    +(chkValue('adequacao_salarial','S','N') == 'N' ? "NULL" : swap_pv(getEl("salario_adeq_salarial").value));
	  
	if(getEl("tipo_emprestimo"))
	{
		paramEmp += "&seq_emprestimo_patroc="+getEl("tipo_emprestimo").value;
	}
	else
	{
		paramEmp += "&seq_emprestimo_patroc=2";
	}
      
	  
	  
	  
	  //	  paramEmp += "&considera_ex_ctp_reduc="+(vDefault == true ? "N" : getEl("considera_ex_ctp_reduc").value);	//Garcia - 28/09/2006
		if (getEl('considera_ex_ctp_reduc').value == 'S') {
			paramEmp += "&considera_ex_ctp_reduc="+(getEl('considera_ex_ctp_reduc').value);
		} else {
			paramEmp += "&considera_ex_ctp_reduc="+'N';
		}
		
		
		paramEmp += "&dt_pagamento="+getEl("dt_pagamento_oculta").value;	// 03/10/2006
		
		paramEmp += "&pagar_1="+chkValue('sim_pagar_1', 'S', 'N');
		
		paramEmp += "&pi_prc="+chkValue('fl_recupera_credito', 'S', 'N');
		
//	  paramEmp += "&dt_pagamento="+(vDefault == true ? "NULL" : getEl("dt_pagamento").value);
//      alert(getEl('tipo_risco').value);

      return paramEmp;
   }

   function cmbTipoEmprestimo(emp, plano) {
      xcmb = new Jajax();
      xcmb.setInteractiveLayer('divAndamento');
//      xcmb.popList('proxyEmprestimo.php?call=fnc_php_lst_tipos_emprestimos&emp='+emp+'&plano='+plano, 'tipo_emprestimo'); 19/10/2006
	  xcmb.popList('proxyEmprestimo.php?call=popList&fncLst=fnc_php_lst_tipos_emprestimos&emp='+emp+'&plano='+plano, 'tipo_emprestimo');
   }

   var titulo_pagina = "";
   //--- Busca informações e realiza simulações
   function buscaParticipante() 
   {
		if(titulo_pagina == "")
		{
			titulo_pagina = document.title;
		}
      // Obs.: Feito em várias funções para garantir que as informações sejam obtidas de forma sequencial, 
      // já que uma das características fundamentais do ajax é o fato de trabalhar de forma assíncrona
      //
      if ( (getEl('cd_empresa').value != '') && (getEl('cd_registro_empregado').value != '') && (getEl('seq_dependencia').value != '') ) {
         // Limpa dados anteriores
         getEl('opTipoCalculo').value = 'M'; // 12/07/2006
         limpaPrestacoesAntigas();
         limpaEndereco();
         limpaValoresNovoEmprestimo();
         limpaParcelasInformadas();
		 limpaErros();
         getEl('tipo_risco').disabled = true;
   
//         getEl('forma_pgto_fundacao').value = 'BCO'; 20/10/2006
         

         var param = "cd_empresa="+getEl('cd_empresa').value+"&cd_registro_empregado="+getEl('cd_registro_empregado').value+"&seq_dependencia="+getEl('seq_dependencia').value;
         
		 document.title = titulo_pagina + " EMP_001 ---- " + getEl('cd_empresa').value + "/" + getEl('cd_registro_empregado').value + "/" + getEl('seq_dependencia').value;
		 
		 var x = new Jajax();
         // Busca nome do participante e chama buscaInformacoes
         x.setInteractiveLayer('divAndamento');
         x.setCallback("buscaInformacoes");
         x.sendFormGET('proxyEmprestimo.php', null, null, 'call=fnc_busca_nome_part'+'&'+param);
         highlightTipoCalculo(getEl('opTCS'), 'on');

		 var x = new Jajax();
         // Busca tipo de contrato participante (senha fraca / forte)
         x.setInteractiveLayer('divAndamento');
         x.setCallback("tipoSenhaParticipante");
		 x.sendFormGET('proxyEmprestimo.php', null, null, 'call=fnc_tp_senha_callcenter'+'&'+param);
	 } else {
//         alert('Você precisa informar a Empresa, o RE e a Sequencia');
		 mostraAlerta('Erro','Você precisa informar a Empresa, o RE e a Sequência');
      }
   }

   function buscaInformacoes(ret) {
      var paramEmp = null;
      var xml = new xmlProcess(ret.responseXML);
	  
	  
      if (xml.errNo != 0) {
         getEl('divErro').innerHTML = xml.errMsg;
         xml.clearFormElements('divEndereco');
      } else {
         // Gera nova sessão, destruindo a anterior (caso exista)
         // Como a sessão é essencial para todas as funções, força a funcionar de forma síncrona nesta parte, 
         // por isto a função foi dividida
         getEl("nome_participante").value = xml.getFieldValueXML("nome_participante");
         getEl("cd_plano").value = xml.getFieldValueXML("cd_plano");
         populaCombo();
     }
   }

   function tipoSenhaParticipante(ret) {
      var paramEmp = null;
      var xml = new xmlProcess(ret.responseXML);
	  
      if (xml.errNo != 0) {
         getEl('divErro').innerHTML = xml.errMsg;
      } else {
        //alert(xml.getFieldValueXML("tp_senha_callcenter"));
        
		if (xml.getFieldValueXML("tp_senha_callcenter") == 1)
		{
			//mostraAlerta('Aviso','Com contrato Consulta');
			alert('Com contrato Consulta');
		}
		else if (xml.getFieldValueXML("tp_senha_callcenter") == 2)
		{
			//mostraAlerta('Aviso','Com contrato Completo');
			alert('Com contrato Completo');
		}
		else
		{
			//mostraAlerta('Aviso','Sem contrato Callcenter');
			alert('Sem contrato Callcenter');
		}
		//mostraAlerta('Erro','Você precisa informar a Empresa, o RE e a Sequência');
     }
	 
   }
   
   function comboOk() {
//      alert(getEl('tipo_emprestimo').options.length);
      if (getEl('tipo_emprestimo').options.length < 0) {
         waitingResponse = false;
      } else { 
         waitingResponse = true;
      } 
   }

   function clearTipoEmprestimo() {
      obj = getEl("tipo_emprestimo");
      for (i=obj.options.length;i>=0;i--) {
         obj.options[i] = null;
      }
   }

   function updaterComboOk(fncCall) {
      if (getEl('tipo_emprestimo').options.length < 1) {
         waitingResponse = true;
         self.setTimeout("updaterComboOk('"+fncCall+"')", 250);
      } else { 
         waitingResponse = false;
         eval(fncCall);
      } 
   }

   function erroUpdater() {
//      alert("Ocorreu um erro tentando atualizar o combo de Tipos de Empréstimos");
        mostraAlerta("Erro","Ocorreu um erro tentando atualizar o combo de Tipos de Empréstimos");
   }

   function populaCombo() {
      var param = "cd_empresa="+getEl('cd_empresa').value+"&cd_registro_empregado="+getEl('cd_registro_empregado').value+"&seq_dependencia="+getEl('seq_dependencia').value;
      // Preenche o combo de tipos de empréstimos
      cmbTipoEmprestimo(getEl('cd_empresa').value, getEl('cd_plano').value);

      // Cria sessão e chama BuscaInformacoes2
      getEl('opTipoCalculo').value = 'S';
//      setTipoCalculo('S');
      cmb = getEl("tipo_emprestimo");
      
      // Aguarda até que o combo esteja preenchido
      clearTipoEmprestimo();
      waitingResponse = true;
      updaterComboOk("buscaInf2()");
//      setTipoCalculo('S');
   }

   function atualizaSessao(ret) {
      var xml = new xmlProcess(ret.responseXML);
      if (xml.errNo != 0) {
//         alert('Ocorreu um erro ao tentar gerar uma nova sessão: '+xml.errMsg);
	       mostraAlerta('Erro','Ocorreu um erro ao tentar gerar uma nova sessão: '+xml.errMsg);
      } else {
         getEl('session_id').value = xml.getFieldValueXML("session_id");
      }
      ajaxWaiter("getEl('session_id').value != ''", "buscaInformacoes2(ret)", 100);
   }

   function buscaInf2() {
      var param = "cd_empresa="+getEl('cd_empresa').value+"&cd_registro_empregado="+getEl('cd_registro_empregado').value+"&seq_dependencia="+getEl('seq_dependencia').value;
      var xSessao = new Jajax()
//      xSessao.setCallback('buscaInformacoes2');
      getEl('session_id').value = '';
      xSessao.setCallback('atualizaSessao');
      xSessao.sendFormGET('proxyEmprestimo.php', null, null, 'call=novaSessao&'+param);
   }

   function buscaInformacoes2(ret) {
      var param = "cd_empresa="+getEl('cd_empresa').value+"&cd_registro_empregado="+getEl('cd_registro_empregado').value+"&seq_dependencia="+getEl('seq_dependencia').value;
      var paramEmp = null;
      var xml = new xmlProcess(ret.responseXML);
      if (xml.errNo != 0) {
         getEl('divErro').innerHTML = xml.errMsg;
         xml.clearFormElements('divEndereco');
         hideAguarde();
      } else {

//         getEl('session_id').value = xml.getFieldValueXML("session_id");
         
         var xIP = new Jajax();
         xIP.setInteractiveLayer('divAndamento');
         xIP.sendFormGET('proxyEmprestimo.php', null, 'ip_cliente', 'call=mostraIP');

         // Busca informações de endereço
		 //comentado em 2010
         //var xEnder = new Jajax()
         //xEnder.setInteractiveLayer('divAndamento');
         //xEnder.sendFormGET('proxyEmprestimo.php', null, 'divEndereco', 'call=fnc_consulta_endereco_partic&'+param);

        realizaSimulacao(true);
      }
   }

	function realizaSimulacao(vPrimeira, vTipo) 
	{
		document.getElementById("flagAlerta").value = "false";
		showAguarde();
		// Informações para o novo empréstimo
		var xNovoEmp = new Jajax();
		if (vTipo == null) 
		{
			vTipo = 'M';
		}
		paramEmp = montaParametrosEmprestimo(vPrimeira, vTipo);
		
		//xNovoEmp.debug = true;
		xNovoEmp.setInteractiveLayer('divAndamento');
		xNovoEmp.setCallback('buscaInformacoesSimulacao');
		xNovoEmp.setInteractivityLevel(2);

		xNovoEmp.sendFormGET('proxyEmprestimo.php', null, null, 'call=fnc_busca_informacoes_emp&'+paramEmp);
	  
	}

   function buscaInformacoesSimulacao(ret) {
      var xml = new xmlProcess(ret.responseXML);
      var sessao = getEl("session_id").value;
	  //alert(xml.errNo + " | "+xml.wrnNo)
      if (xml.errNo != 0) {
         getEl('divErro').innerHTML = xml.errMsg;
         xml.clearFormElements('divEndereco');
         limpaPrestacoesAntigas();
         hideAguarde();
		 //return 0;
      }
	  
      if (xml.wrnNo != 0) {
		 hideAguarde();
		 mostraAlerta("Aviso",xml.wrnNo);
      }
	  
//      else {     //  Comentado em 21/07/2006
         // mostra informações da simulação
         getEl('opSimulacao').value = '';
         xBuscaSim  = new Jajax();
         xBuscaSim.setInteractiveLayer('divAndamento');
         xBuscaSim.setCallback('mostraInformacoesSimulacao');
         xBuscaSim.setInteractivityLevel(2);

		 xBuscaSim.sendFormGET('proxyEmprestimo.php', null, null, 'call=fnc_busca_tmp_sim_emp&sessao='+sessao);
		 
		 if (xml.errNo != 0) {
      	   hideAguarde();
    	 }else{			 
        	// Mostra informações sobre as parcelas da simulação
         	xBuscaPar = new Jajax();
         	xBuscaPar.setCallback('mostraInformacoesPrestacoes');
         	xBuscaPar.sendFormGET('proxyEmprestimo.php', null, null, 'call=fnc_busca_tmp_sim_prestacoes&sessao='+sessao);
		 }         
 
//      }          //  Comentado em 21/07/2006
   }
   
   function simulacaoValoresMaximos() {
      showAguarde();
      // Realiza simulação quando o usuário clica no botão "Valores Máximos"
      var xNovoEmp = new Jajax();
      paramEmp = montaParametrosEmprestimo(false, "M"); // Simulação pelos valores máximos, considerando outras parametrizações da tela
      getEl('num_prestacoes').value = '';
      getEl('valor').value = '';
//      xNovoEmp.debug = true;
      xNovoEmp.setInteractiveLayer('divAndamento');
      xNovoEmp.setCallback('buscaInformacoesSimulacao');
      xNovoEmp.setInteractivityLevel(2);
      xNovoEmp.sendFormGET('proxyEmprestimo.php', null, null, 'call=fnc_busca_informacoes_emp&'+paramEmp);
   }


   function mostraInformacoesSimulacao(ret) {
      var xml = new xmlProcess(ret.responseXML);
      xml.setFieldValue('vlr_saldo_devedor');
      xml.setFieldValue('vlr_prest_atrasadas');
      xml.setFieldValue('vlr_emp_ferias');
      xml.setFieldValue('salario_emprestimo');
      xml.setFieldValue('vlr_debitos');
      var tipoRisco = xml.getFieldValueXML('tipo_risco')
      var oTipoRisco = getEl('tipo_risco');
      for (i=0;i<oTipoRisco.options.length;i++) {
         if (oTipoRisco.options[i].value == tipoRisco) {
            oTipoRisco.options[i].selected = true;
         }
      }
      if (xml.getFieldValueXML('renegociacao') == 'S') {
         getEl('renegociacao').checked = true;
      } else {
         getEl('renegociacao').checked = false;
      }
      xml.setFieldValue('vlr_max_concessao');
      xml.setFieldValue('dt_deposito');
	  xml.setFieldValue('dt_pagamento');	//03/10/2006
	  getEl('dt_pagamento_anterior').value = getEl('dt_pagamento').value; //04/12/2006
      
      var op = getEl('opTipoCalculo').value;
      var tipoSimRealizada = xml.getFieldValueXML('tipo_simulacao');
      switch(op) {
         case 'P':getEl('valor').value = (tipoSimRealizada == 'M' ? xml.getFieldValueXML('limite_comprometimento') : xml.getFieldValueXML('valor'));
                  break;
         case 'S':xml.setFieldValue('valor');
                  break;
         case 'L':getEl('valor').value = (tipoSimRealizada == 'M' ? xml.getFieldValueXML('vlr_max_liquido') : xml.getFieldValueXML('valor'));
                  break;
      }
   }

   function limpaPrestacoesAntigas() {
      // Remove a tabela anterior, caso exista
      oldTable = document.getElementById('tabPrestacoes');
      if (oldTable != null) {
         pai = oldTable.parentNode;
         pai.removeChild(oldTable);
      }
   }
   
   function limpaEndereco() {
      var xF = new xmlProcess;
      xF.clearFormElements('divEndereco');
   }
   
   function limpaValoresNovoEmprestimo() {
      var xF = new xmlProcess;
      xF.clearFormElements('divNovoEmprestimo');
   }
   
   function limpaParcelasInformadas() {
      getEl('dt_deposito').value = '';
      getEl('num_prestacoes').value = '';
      getEl('valor').value = '';
   }
   
   
	function mostraInformacoesPrestacoes(ret) 
	{
		var divPrest = document.getElementById('divListaSimulacoes');
		limpaPrestacoesAntigas();
		
		if(getTipoEmprestimo() == "PRE")
		{
			// Cria a tabela
			table = document.createElement('table');
			table.style.width = "100%";
			table.setAttribute('id', 'tabPrestacoes');
			table.className = 'tabParcelas';
			
			tbody = document.createElement('tbody');
			table.appendChild(tbody);
			

			// Header da tabela
			tr = document.createElement('tr');
			tr.className = 'tabHeader';
			
			td = document.createElement('td');
			td.innerHTML = 'Valor<br>Dep&oacute;sito';
			tr.appendChild(td);
			
			td = document.createElement('td');
			td.innerHTML = 'Prest';
			tr.appendChild(td);
			
			td = document.createElement('td');
			td.innerHTML = 'Valor<br>Prest';
			tr.appendChild(td);
			
			td = document.createElement('td');
			td.innerHTML = 'IOF';
			tr.appendChild(td);
			
			td = document.createElement('td');
			td.innerHTML = 'Primeiro<br>Pgto';
			tr.appendChild(td);
			
			td = document.createElement('td');
			td.innerHTML = '%<BR>Compro.';
			td.title = '% de Compromentimento';
			tr.appendChild(td);
			
			td = document.createElement('td');
			td.innerHTML = '#';
			
			tr.appendChild(td);

			tbody.appendChild(tr);

			xml = ret.responseXML;
			var linhas = xml.getElementsByTagName('record');

			// Linha Detalhe
			for (i=0;i<linhas.length;i++) 
			{
				var valores = linhas[i].getElementsByTagName('fld');
				
				
				tr = document.createElement('tr');
				tr.className = 'tabDetalhe';
				tr.setAttribute('id', 'opSim_' + getValorRegistroXML(valores, "NRO_PRESTACOES"));
				tr.onmouseover = function (event){prestMouseOver(event)};
				tr.onmouseout = function (event){prestMouseOut(event)};
				tr.onclick = function (){ 
											var nr_opcao = this.id.replace("opSim_","");
											selecionaParcelamento(nr_opcao,this);
										};
				
				td = document.createElement('td');
				td.innerHTML = getValorRegistroXML(valores, "VLR_DEPOSITO");
				td.style.textAlign = "right";
				tr.appendChild(td);
				
				td = document.createElement('td');
				td.innerHTML = getValorRegistroXML(valores, "NRO_PRESTACOES");
				td.style.textAlign = "center";
				tr.appendChild(td);
				
				td = document.createElement('td');
				td.innerHTML = getValorRegistroXML(valores, "VLR_PRESTACAO");
				td.style.textAlign = "right";
				tr.appendChild(td);
				
				td = document.createElement('td');
				td.innerHTML = getValorRegistroXML(valores, "VLR_IOF");
				td.style.textAlign = "right";
				tr.appendChild(td);
				
				td = document.createElement('td');
				td.innerHTML = getValorRegistroXML(valores, "DT_PRIMEIRA_PRESTACAO");
				td.style.textAlign = "center";
				tr.appendChild(td);
				
				td = document.createElement('td');
				td.innerHTML = getValorRegistroXML(valores, "PERC_COMPROMETIMENTO");
				td.style.textAlign = "right";
				tr.appendChild(td);

				td = document.createElement('td');
				td.innerHTML = '<a href="javascript: getDetalhes('+ getValorRegistroXML(valores, "NRO_PRESTACOES") +');" id="getDet_'+ getValorRegistroXML(valores, "NRO_PRESTACOES") +'" title="Clique aqui para saber os detalhes da opção">Det</a>';
				td.style.textAlign = "center";
				tr.appendChild(td);					
				
				tbody.appendChild(tr);
			} 

			// Adiciona a tabela na div correspondente às prestacoes
			divPrest.appendChild(table);
		}
		
		if(getTipoEmprestimo() == "POS")
		{
			// Cria a tabela
			table = document.createElement('table');
			table.style.width = "100%";
			table.setAttribute('id', 'tabPrestacoes');
			table.className = 'tabParcelas';
			
			tbody = document.createElement('tbody');
			table.appendChild(tbody);
			

			// Header da tabela
			tr = document.createElement('tr');
			tr.className = 'tabHeader';
			
			td = document.createElement('td');
			td.innerHTML = 'Valor<br>Dep&oacute;sito';
			tr.appendChild(td);
			td = document.createElement('td');
			td.innerHTML = 'Número de<br>Prestações';
			tr.appendChild(td);
			td = document.createElement('td');
			td.innerHTML = 'Valor da 1ª<br>Prest. Básica';
			tr.appendChild(td);
			td = document.createElement('td');
			td.innerHTML = 'Valor do INPC<br>Projetado<BR>1º Pgto';
			tr.appendChild(td);
			td = document.createElement('td');
			td.innerHTML = 'Valor da 1ª<br>Prest. Projetada';
			tr.appendChild(td);
			td = document.createElement('td');
			td.innerHTML = '#';
			tr.appendChild(td);	  
			td = document.createElement('td');
			td.innerHTML = '#';
			tr.appendChild(td);	
			
			tr.appendChild(td);

			tbody.appendChild(tr);

			xml = ret.responseXML;
			var linhas = xml.getElementsByTagName('record');

			// Linha Detalhe
			for (i=0;i<linhas.length;i++) 
			{
				var valores = linhas[i].getElementsByTagName('fld');
				
				
				tr = document.createElement('tr');
				tr.className = 'tabDetalhe';
				tr.setAttribute('id', 'opSim_' + getValorRegistroXML(valores, "NRO_PRESTACOES"));
				tr.onmouseover = function (event){prestMouseOver(event)};
				tr.onmouseout = function (event){prestMouseOut(event)};
				tr.onclick = function (){ 
											var nr_opcao = this.id.replace("opSim_","");
											selecionaParcelamento(nr_opcao,this);
										};
				
				td = document.createElement('td');
				td.innerHTML = getValorRegistroXML(valores, "VLR_DEPOSITO");
				td.style.textAlign = "right";
				tr.appendChild(td);
				
				td = document.createElement('td');
				td.innerHTML = getValorRegistroXML(valores, "NRO_PRESTACOES");
				td.style.textAlign = "center";
				tr.appendChild(td);
				
				td = document.createElement('td');
				td.innerHTML = getValorRegistroXML(valores, "VLR_PRESTACAO_BASICA");
				td.style.textAlign = "right";
				tr.appendChild(td);
				
				td = document.createElement('td');
				td.innerHTML = getValorRegistroXML(valores, "VLR_INPC");
				td.style.textAlign = "right";
				tr.appendChild(td);
				
				td = document.createElement('td');
				td.innerHTML = getValorRegistroXML(valores, "VLR_PRESTACAO");
				td.style.textAlign = "right";
				tr.appendChild(td);
				
				td = document.createElement('td');
				td.innerHTML = '<a href="javascript: getDetalhes('+ getValorRegistroXML(valores, "NRO_PRESTACOES") +');" id="getDet_'+ getValorRegistroXML(valores, "NRO_PRESTACOES") +'" title="Clique aqui para saber os detalhes da opção">Det</a>';
				td.style.textAlign = "center";
				tr.appendChild(td);	 
				
				td = document.createElement('td');
				td.innerHTML = '<a href="javascript: getEvolucao('+ getValorRegistroXML(valores, "NRO_PRESTACOES") +');" id="getEvo_'+ getValorRegistroXML(valores, "NRO_PRESTACOES") +'" title="Clique aqui para saber a evolução das prestações da opção">Evo</a>';
				td.style.textAlign = "center";
				tr.appendChild(td);				
				
				tbody.appendChild(tr);
			} 

			// Adiciona a tabela na div correspondente às prestacoes
			divPrest.appendChild(table);
		}		
		
		
		hideAguarde();
	}   
   
   
	function mostraInformacoesPrestacoesOLD(ret) 
	{
		var divPrest = document.getElementById('divListaSimulacoes');

		limpaPrestacoesAntigas();

		// Cria a tabela
		table = document.createElement('table');
		table.style.width = "100%";
		table.setAttribute('id', 'tabPrestacoes');
		tbody = document.createElement('tbody');
		table.appendChild(tbody);
		table.className = 'tabParcelas';

		// Header da tabela
		tr = document.createElement('tr');
		tr.className = 'tabHeader';
		td = document.createElement('td');
		td.innerHTML = 'Valor<br>Dep&oacute;sito';
		tr.appendChild(td);
		td = document.createElement('td');
		td.innerHTML = 'Prest';
		tr.appendChild(td);
		
		var parc_pos = "";
		if(getTipoEmprestimo() == "POS")
		{
			parc_pos = " 1ª ";
		}
		
		td = document.createElement('td');
		td.innerHTML = 'Valor'+parc_pos+'<br>Prest';
		tr.appendChild(td);
		td = document.createElement('td');
		td.innerHTML = 'IOF';
		tr.appendChild(td);
		td = document.createElement('td');
		td.innerHTML = 'Primeiro<br>Pgto';
		tr.appendChild(td);
		td = document.createElement('td');
		td.innerHTML = '%<BR>Compro.';
		td.title = '% de Compromentimento';
		tr.appendChild(td);
		td = document.createElement('td');
		td.innerHTML = 'Det';
		tr.appendChild(td);

		//coluna evolução pos-fixado 20/10/2010
		if(getTipoEmprestimo() == "POS")
		{
			td = document.createElement('td');
			td.innerHTML = 'Evo';
			tr.appendChild(td);	  
		}

		tbody.appendChild(tr);

		xml = ret.responseXML;
		var linhas = xml.getElementsByTagName('record');

		// Linha Detalhe
		for (i=0;i<linhas.length;i++) 
		{
			tr = document.createElement('tr');
			tr.setAttribute('id', 'opSim'+i+1);
			tr.onmouseover = prestMouseOver;
			if (tr.captureEvents) { element.captureEvents(Event.MOUSEOVER) };
			tr.onmouseout = prestMouseOut;
			if (tr.captureEvents) { element.captureEvents(Event.MOUSEOUT) };
			tr.onclick = selecionaParcelamento;
			if (tr.captureEvents) { element.captureEvents(Event.CLICK) };

			valores = linhas[i].getElementsByTagName('fld');
			for (j = 0; j < (valores.length -1); j++) 
			{
				td = document.createElement('td');
				td.innerHTML = valores[j].childNodes[0].nodeValue;
				if ((j==0) || (j==2) || (j==3) || (j==5)) 
				{
					td.style.textAlign = "right";
				} 
				else
				{
					td.style.textAlign = "center";
				}
				tr.appendChild(td);
			}

			//coluna detalhes
			td = document.createElement('td');
			attr = document.createAttribute('det');
			td.setAttributeNode(attr);
			txt = document.createTextNode('Det');
			td.style.textAlign = "center";
			td.appendChild(txt);
			tr.appendChild(td);
		 

			//coluna evolução pos-fixado 20/10/2010
			if(getTipoEmprestimo() == "POS")
			{
				td = document.createElement('td');
				attr = document.createAttribute('evo');
				td.setAttributeNode(attr);
				txt = document.createTextNode('Evo');
				td.style.textAlign = "center";
				td.appendChild(txt);	 
				tr.appendChild(td);
			}

			tr.className = 'tabDetalhe';
			var attr = document.createAttribute('numPrestacoes');
			attr.value = valores[1].childNodes[0].nodeValue;
			tr.setAttributeNode(attr);
			tbody.appendChild(tr);
		} 

		// Adiciona a tabela na div correspondente às prestacoes
		divPrest.appendChild(table);
		hideAguarde();
	}

   
   
	function getValorRegistroXML(registro, campo)
	{
		for (j=0;j < registro.length; j++) 
		{		
			var qt_atrib = registro[j].attributes.length;
			var ob_atrib = registro[j].attributes;
			
			for(x = 0; x < qt_atrib; x++)
			{
				if(ob_atrib[x].nodeName.toLowerCase() == "id")
				{
					if(ob_atrib[x].nodeValue.toLowerCase() == campo.toLowerCase())
					{
						return registro[j].childNodes[0].nodeValue;
					}
				}
			}
		}
	}    
   
   
	// BLOQUEIA CALCULO A PARTIR DO VALOR DA PARCELA
	// PROVISORIO ATÉ CONCLUIR TELA FINAL 19/01/2010
	function setTipoEmprestimo()
	{
		var texto = getTipoEmprestimo()
		if(texto == "POS")
		{
			getEl("opTCP").style.display = "none";
			getEl("tr_perc_tx_preservacao_patrimonia").style.display = "none";
			getEl("tr_cm").style.display = "none";
		}
		else
		{
			getEl("opTCP").style.display = "";
			getEl("tr_perc_tx_preservacao_patrimonia").style.display = "";
			getEl("tr_cm").style.display = "";			
		}
	}
	
	// PROVISORIO ATÉ CONCLUIR TELA FINAL 19/01/2010
	function getTipoEmprestimo()
	{
		if(getEl("tipo_emprestimo"))
		{
			var id = getEl("tipo_emprestimo").selectedIndex;
			var texto = getEl("tipo_emprestimo").options[id].text;
				texto = texto.substr(0,3);
			if(texto == "POS")
			{
				return "POS";
			}
			else
			{
				return "PRE";
			}
		}
		else
		{
			return "PRE";
		}		
	}   
   
   
	// Busca a evolução das parcelas de acordo com a opção escolhida 20/01/2010
	function getEvolucao(nr_parcela) 
	{
		showAguarde();
		var sessao = getEl("session_id").value;
       	xBuscaPar = new Jajax();
       	xBuscaPar.setCallback('mostraEvolucaoPrestacoes');
       	xBuscaPar.sendFormGET('proxyEmprestimo.php', null, null, 'call=fnc_busca_tmp_sim_evolucao&sessao='+sessao+';'+nr_parcela);
		
	}   
   
	// Mostra a evolução das parcelas de acordo com a opção escolhida 20/01/2010
	function mostraEvolucaoPrestacoes(ret) 
	{
		
		var xml = new xmlProcess(ret.responseXML);
		if (xml.errNo != 0) 
		{
			alert(xml.errNo);
			hideAguarde();
		}
		else
		{
			var xml = ret.responseXML;
			var linhas = xml.getElementsByTagName('record');
		
			var divEvolucao = document.getElementById('divEvolucaoPosFixadoLista');
				divEvolucao.innerHTML = "";
			
			// Cria a tabela
			table = document.createElement('table');
			table.style.width = "100%";
			table.setAttribute('id', 'tabEvolucaoPosFixado');
			table.className = 'sort-table';
			
			// Header da tabela
			thead = document.createElement('thead');
			table.appendChild(thead);		
			tr = document.createElement('tr');
			td = document.createElement('td');
			td.innerHTML = 'Parcela';
			tr.appendChild(td);
			td = document.createElement('td');
			td.innerHTML = 'Vl Juro';
			tr.appendChild(td);
			td = document.createElement('td');
			td.innerHTML = 'Vl Amortização';
			tr.appendChild(td);
			td = document.createElement('td');
			td.innerHTML = 'Vl Prest Básica';
			tr.appendChild(td);
			td = document.createElement('td');
			td.innerHTML = 'Vl INPC';
			tr.appendChild(td);
			td = document.createElement('td');
			td.innerHTML = 'Vl Seguro';
			tr.appendChild(td);			
			td = document.createElement('td');
			td.innerHTML = 'Vl Prest Projetada';
			tr.appendChild(td);
			thead.appendChild(tr);
			
			tbody = document.createElement('tbody');
			table.appendChild(tbody);		
			
			
		for (i = 0; i < linhas.length; i++) 
		{
			var valores = linhas[i].getElementsByTagName('fld');
			
			tr = document.createElement('tr');

			td = document.createElement('td');
			td.innerHTML = getValorRegistroXML(valores, "seq_prestacao");
			td.style.textAlign = "center";
			tr.appendChild(td);
			
			td = document.createElement('td');
			td.innerHTML = getValorRegistroXML(valores, "vl_juros");
			td.style.textAlign = "right";
			tr.appendChild(td);
			
			td = document.createElement('td');
			td.innerHTML = getValorRegistroXML(valores, "vl_amortizacao");
			td.style.textAlign = "right";
			tr.appendChild(td);
			
			td = document.createElement('td');
			td.innerHTML = getValorRegistroXML(valores, "vl_prestacao_basica");
			td.style.textAlign = "right";
			tr.appendChild(td);
			
			td = document.createElement('td');
			td.innerHTML = getValorRegistroXML(valores, "vl_inpc");
			td.style.textAlign = "right";
			tr.appendChild(td);
			
			td = document.createElement('td');
			td.innerHTML = getValorRegistroXML(valores, "vl_seguro");
			td.style.textAlign = "right";
			tr.appendChild(td);

			td = document.createElement('td');
			td.innerHTML = getValorRegistroXML(valores, "vl_prestacao_projetada");
			td.style.textAlign = "right";
			tr.appendChild(td);			
			
			tbody.appendChild(tr);
		}
			
			divEvolucao.appendChild(table);
			hideAguarde();	
			
	
			wPopEvolucaoPosFixado.show();
		}
	}     
   
   
  
   
   // ---

   function retAlteraEndereco(ret) {
      var xml = new xmlProcess(ret.responseXML);
      if (xml.errNo == 0) {
//         alert('Endereço alterado');
         mostraAlerta('Aviso','Endereço alterado');
      }
   }

   function fncAlteraEndereco() {
      var param = "cd_empresa="+getEl('cd_empresa').value+"&cd_registro_empregado="+getEl('cd_registro_empregado').value+"&seq_dependencia="+getEl('seq_dependencia').value;
      x = new Jajax();
      x.setInteractiveLayer('divAndamento');
//      x.setDebugOn();
      x.setCallback("retAlteraEndereco");
      x.sendFormGET('proxyEmprestimo.php', 'divEndereco', null, 'call=fnc_altera_endereco&'+param); 
   }

   function debito() {
      var param = "";
      param += "cd_empresa="+getEl('cd_empresa').value;
      param += "&cd_registro_empregado="+getEl('cd_registro_empregado').value;
      param += "&seq_dependencia="+getEl('seq_dependencia').value;
      param += "&id_renegociacao="+chkValue('renegociacao','1','0');
      param += "&dt_deposito="+getEl('dt_deposito').value;
      var x = new Jajax();
      x.setInteractiveLayer('divAndamento');
      x.requestText("pop_debitos.php", param, 'divDetalhesDebito');
      wPopDebitos.show();
   }

	function callConfirmaEmprestimo(ret) 
	{
		window.location = "confirma_emprestimo_dap.php?call=fnc_busca_inf_concessao&session_id="+getEl('session_id').value+"&num_prestacoes="+getEl('num_prestacoes').value+"&usuario_emp="+getEl('usuario_emp').value+"&MOSTRAR_BANNER="+getEl('MOSTRAR_BANNER').value;
	}

	function confirmaEmprestimo() 
	{
		if (getEl('opSimulacao').value == '') 
		{
			alert('ERRO\n\nVocê não selecionou o número de prestações'); 
		} 
		else 
		{
			if (getEl('sim_pagar_emp_anterior').checked || getEl('sim_pagar_1_3').checked || getEl('sim_pagar_1').checked || getEl('sim_pagar_prestacoes_atrasadas').checked) 
			{
				alert('ATENÇÃO\n\nVocê está realizando uma simulação.\n\nNeste tipo de operação, a concessão não está disponível.');
			} 
			else 
			{
				if (getEl('concessao_especial').checked && getEl('motivo_conc_especial').value == '') 
				{
					alert('ERRO\n\nVocê não especificou o motivo da Concessão Especial.');
				} 
				else 
				{
					
					var url_confirma = "confirma_emprestimo_dap.php?call=fnc_busca_inf_concessao&session_id="+getEl('session_id').value+"&num_prestacoes="+getEl('num_prestacoes').value+"&usuario_emp="+getEl('usuario_emp').value+"&MOSTRAR_BANNER="+getEl('MOSTRAR_BANNER').value;
					//fl_recupera_credito
					if(chkValue('fl_recupera_credito', 'S', 'N') == 'S')
					{
						if(confirm("ATENÇÃO\n\nConfirma a opção: RECUPERAÇÃO DE CRÉDITO?\n\n"))
						{
							window.location = url_confirma;
						}
					}
					else
					{
						window.location = url_confirma;
					}
				}
			}
		}
	}
   
/*
   function liberaTipoRisco(v) {
      if (v.checked) {
         getEl('tipo_risco').disabled = false;
      } else {
         getEl('tipo_risco').disabled = true;
      }
   }
*/

   function swap_pv(txt) {
      var sTxt = new String(txt);
      if (sTxt.length > 0) {
         var ret = new String();
         for (i=0;i<sTxt.length;i++) {
            switch(sTxt.charAt(i)) {
               case ',': ret += '.'; break;
               case '.': ret += ','; break;
               default : ret += sTxt.charAt(i);
            }
         }
         return ret;
      } else {
         return txt;
      }
   }

   function executeQuery() {
      if ( (getEl('cd_empresa').value != '') &&
           (getEl('cd_registro_empregado').value != '') &&
           (getEl('seq_dependencia').value != '') )
      {
         oEmp = getEl('cd_empresa');
         buscaNomeEmpresa(oEmp);
         oCmbOrigem = getEl('origem');
         vOrigem = getEl('getOrigem').value;
         if (vOrigem != '') {
            vOrigem = getEl('getOrigem').value
            for(i=0;i<oCmbOrigem.options.length;i++) {
               if (oCmbOrigem.options[i].value == vOrigem) {
                  oCmbOrigem.options[i].selected = true;
                  break;
               }
            }
         }
         buscaParticipante();
      }
   }
   
   function ajaxWaiter(condition, destination, interval) {
      if (eval(condition) == true) {
         eval(destination);
      } else {
         setInterval(ajaxWaiter(condition, destination, interval));
      }
   }
   
   // Registra captura de eventos
   document.onkeypress = validaKeyPress;

