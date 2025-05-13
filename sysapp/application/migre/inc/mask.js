/*
 * Copyright (c) 2007 Rafael Zanetti <rafael@webdev.com.br>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */


/* mask.js - Masks for Form Fields - Based on Prototype 
 * by Rafael Zanetti <rafael@webdev.com.br> distributed under the Creative Commons license. 
 */
var Mask = Class.create();
Mask.prototype = {
  initialize: function(form_id) {
    this.form_id = form_id;
    this.builtin_masks = ['mask-date', 'mask-hour', 'mask-numeric', 'mask-cpf', 'mask-cnpj', 'mask-cpf_cnpj', 'mask-cep'];
    this.updateElements();
  },
  
  updateElements: function() {
    elements = $(this.form_id).getInputs('text');
	  // Para cada elemento, faça verificações
	  for (i = 0; i < elements.length; i++) {
	    e = elements[i];
      mask = null;
      this.builtin_masks.each(
        function(sMask) {
          if (e.hasClassName(sMask)) mask = sMask.replace(/mask\-/, '');
        }
      );
      if (mask) {
        oBfx = this.buildmask.bindAsEventListener(this);
        e.stopObserving('keypress', oBfx);
        e.observe('keypress', oBfx);
        
        switch (mask) {
          case 'date' : 
            oBfx = this.date_mask.bindAsEventListener(this);
            oBfx1 = this.year_fill.bindAsEventListener(this);
            e.stopObserving('keyup', oBfx);
            e.stopObserving('blur', oBfx1);
            e.observe('keyup', oBfx);
            e.observe('blur', oBfx1);
            e.size = 12;
            e.maxLength = 10;
            break;
           case 'numeric' :
             e.style.textAlign = 'right';
             e.onfocus = function() {
               this.activate();
             }
             break;
           case 'cpf' :
             oBfx = this.cpf_mask.bindAsEventListener(this);
             e.stopObserving('keyup', oBfx);
             e.stopObserving('change', oBfx);
             e.observe('keyup', oBfx);
             e.observe('change', oBfx);
             e.maxLength = 14;
             e.size = 20;
             break;
             
           case 'cnpj' :
             oBfx = this.cnpj_mask.bindAsEventListener(this);
             e.stopObserving('keyup', oBfx);
             e.stopObserving('change', oBfx);
             e.observe('keyup', oBfx);
             e.observe('change', oBfx);
             e.maxLength = 18;
             e.size = 22;
             break;
             
           case 'cpf_cnpj' :
             oBfx = this.cpf_cnpj_mask.bindAsEventListener(this);
             e.stopObserving('keyup', oBfx);
             e.stopObserving('change', oBfx);
             e.observe('keyup', oBfx);
             e.observe('change', oBfx);
             e.maxLength = 18;
             e.size = 22;
             break;
           case 'cep' :
             oBfx = this.cep_mask.bindAsEventListener(this);
             e.stopObserving('keyup', oBfx);
             e.stopObserving('change', oBfx);
             e.observe('keyup', oBfx);
             e.observe('change', oBfx);
             e.maxLength = 8;
             e.size = 10;
             break;
           case 'hour' :
             oBfx = this.hour_mask.bindAsEventListener(this);
             e.stopObserving('keyup', oBfx);
             e.stopObserving('change', oBfx);
             e.observe('keyup', oBfx);
             e.observe('change', oBfx);
             
             e.maxLength = 4;
             e.size = 6;
             break;
        }
      }
    }
  },
  
  buildmask: function(event) {
	  var elem = Event.element(event);
    var mask = null;
    this.builtin_masks.each(
      function(sMask) {
        if (elem.hasClassName(sMask)) mask = sMask.replace(/mask\-/, '');
      }
    );
	  window.setTimeout(
      function(obj) {
		    switch (mask) {
		      case 'numeric' :
		        elem.value = elem.value.replace(/[^\d\.,]*/gi, '');
		        break;
		      case 'date' :
		        elem.value = elem.value.replace(/[^\d,\/]/g, '');
		        break;
		      case 'int' :
		      case 'integer' :
		        elem.value = elem.value.replace(/[^\d]/g, '');
		        break;
		      case 'cpf' :
          case 'cnpj' :
          case 'cpf_cnpj' :
            elem.value = elem.value.replace(/[^\d\.\-\/]*/gi, '');
            break;
          case 'cep' :
            elem.value = elem.value.replace(/[^\d\-]*/g, '');
            break;
          case 'hour' :
            elem.value = elem.value.replace(/[^\d\:]*/g, '');
            break;
		    }
		  }, 1);
	},
	
	/***********************************************************************\
	|*          Funções para manutenção de campos de DATA                  *|
	\***********************************************************************/
	// Datas em formato dd/mm/aaaa
	// Para utilizá-las, devem ser associadas com os seguintes eventos:
	//   onKeyUp    - mascara_data(this, event)
	//   onBlur     - completa_ano(this)
	date_mask: function(event) {
	  dataField = Event.element(event);
	  data = dataField.value;
	  mydata = '';
	  if (data.length > 0) {
	    mydata = data.slice(0, 2);
	    if (data.length > 1) {
	      mydata += '/';
	      if (data.length > 2) {
	        mydata += data.slice(2, 4);
	        if (data.length > 3) {
	          mydata += '/';
	          if (data.length > 4) {
	            mydata += data.slice(4, 8);
	          }
	        }
	      }
	    }
	  }
	  dataField.value = mydata;
	},
	
	// Completa o ano no campo
	year_fill: function(event) {
	  dataField = Event.element(event);
	  data = dataField.value;
	  mydata = '';
	  
	  mydata += data;
	  diames = (dataField.value.substring(0,6));
	  //alert(mydata.length);
	  if (mydata.length == 8) {
	    ano = (dataField.value.substring(6,8));
	    if (ano > 20) {
	      dataField.value = diames + "19" + ano;
	    } else {
	      dataField.value = diames + "20" + ano;
	    }
	  } else if (mydata.length == 6) {
	    ano = (dataField.value.substring(6,8));
	    Today = new Date();
	    dataField.value = diames + Today.getFullYear();
	  }
	
	  if (mydata.length != 0) {
	    this.check_date(dataField);
	  }
	},
	
	// Verifica se a data está correta
	check_date: function(data, sMessage) {
	  if (sMessage == undefined || sMessage == null) {
	    sMessage = '';
	  }
	
	  mydata = '';
	  mydata = data.value.replace(' ', '');
	  tam = mydata.length;
	
	  situacao = true;
	  
	  if (tam == 7) {
	    dia = "01";
	    mes = (data.value.substring(0,2));
	    ano = (data.value.substring(3,7));
	  } else {
	    if (tam < 8) { situacao = "falsa"; }
	    dia = (data.value.substring(0,2));
	    mes = (data.value.substring(3,5));
	    ano = (data.value.substring(6,10));
	  }
	  
	  // verifica o dia valido para cada mes
	  if ((dia < 1) || (dia < 1 || dia > 30) && (mes == 4 || mes == 6 || mes == 9 || mes == 11 ) || dia > 31) {
	    if (dia > 30) { // Supoe q o usuario quer digitar o ultimo dia do mes
	      if (mes == 4 || mes == 6 || mes == 9 || mes == 11 ) {
	        dia = 30;
	      } else {
	        dia = 31;
	      }
	      data.value = dia + '/' + mes + '/' + ano;
	      this.check_date(data, sMessage);
	      return;
	    }
	    situacao = false;
	  }
	
	  // verifica se o mes e valido
	  if (mes < 1 || mes > 12 ) {
	    if (mes > 12) { // Se mes maior q 12, setar pra 12 ou setar pra 01
	      mes = '12';
	    } else {
	      mes = '01';
	    }
	    if (tam == 7) {
	      data.value = mes + '/' + ano;
	    } else {
	      data.value = dia + '/' + mes + '/' + ano;
	    }
	    this.check_date(data, sMessage);
	    return;
	  }
	
	  // verifica se e ano bissexto
	  if (mes == 2 && ( dia < 1 || dia > 29 || (dia > 28 && (parseInt(ano / 4) != ano / 4)))) {
	    if (parseInt(ano / 4) != ano / 4) {// Forcando a data a ser valida
	      dia = 28;
	    } else {
	      dia = 29;
	    }
	    if (tam == 7) {
	      data.value = mes + '/' + ano;
	    } else {
	      data.value = dia + '/' + mes + '/' + ano;
	    }
	    this.check_date(data, sMessage);
	    return;
	  }
	  
	  if (data.value == "") {
	    situacao = false;
	  }
	
	  if (!situacao) {
	    if (sMessage != '') {
	      alert(sMessage);
	    } else {
	      alert("Data inválida!");
	    }
	    data.focus();
	    data.select();
	  }
	  return;
	},
	
	/* Fim das funções de DATA */
	
	// Máscara de CNPJ
	cnpj_mask: function(event) {
	  var campo = Event.element(event);
	  var tammax = 14;
    var vr = campo.value;
    vr = vr.replace("-", "");
    vr = vr.replace("/", "");
    vr = vr.replace(".", "");
    vr = vr.replace(".", "");
    var tam = vr.length;
    
    if (tam < tammax) { tam = vr.length + 1 ; }
    
    tam = tam - 1;
    
    if ( (tam > 2) && (tam <= 5) ) {
      vr = vr.substr( 0, tam - 1 ) + '-' + vr.substr( tam - 1, tam );
    }
    if ( (tam >= 6) && (tam <= 8) ) {
      vr = vr.substr( 0, tam - 5 ) + '/' + vr.substr( tam - 5, 4 ) + '-' + vr.substr( tam - 1, tam );
    }
    
    if ( (tam >= 9) && (tam <= 11) ) {
      vr = vr.substr( 0, tam - 8 ) + '.' + vr.substr( tam - 8, 3 ) + '/' + vr.substr( tam - 5, 4 ) + '-' + vr.substr( tam - 1, tam ); 
    }
    
    if ( (tam >= 12) && (tam < 14) ) {
      vr = vr.substr( 0, tam - 11 ) + '.' + vr.substr( tam - 11, 3 ) + '.' + vr.substr( tam - 8, 3 ) + '/' + vr.substr( tam - 5, 4 ) + '-' + vr.substr( tam - 1, tam ); 
    }

    campo.value = vr;
  },
  
  // Máscara de CPF
  cpf_mask: function(event) {
    var campo = Event.element(event);
    var tammax = 11;
    var vr = campo.value;
    vr = vr.replace( "-", "" );
    vr = vr.replace( ".", "" );
    vr = vr.replace( ".", "" );
    var tam = vr.length;
    
    if (tam < tammax) { tam = vr.length + 1; }
    tam = tam - 1;
    if ( (tam > 2) && (tam <= 11) ) {
      vr = vr.substr( 0, tam - 1 ) + '-' + vr.substr( tam - 1, tam ); 
    }
    if ( (tam == 10) ) {
      vr = vr.substr( 0, tam - 7 ) + '.' + vr.substr( tam - 7, 3 ) + '.' + vr.substr( tam - 4, tam ); 
    }
    campo.value = vr;
  },
  
  // Máscara de cpf ou CNPJ, baseado na quatidade de 
  // caracteres dentro do campo
  cpf_cnpj_mask: function(event) {
    var campo = Event.element(event);
    
    var vr = campo.value;
    vr = vr.replace( "-", "" );
    vr = vr.replace( ".", "" );
    vr = vr.replace( ".", "" );
    vr = vr.replace( "/", "" );
    var tam = vr.length;
    
    if (tam <= 11) {
      var tammax = 11;
      if (tam < tammax) { tam = vr.length + 1; }
      tam = tam - 1;
      if ( (tam > 2) && (tam <= 11) ) {
        vr = vr.substr( 0, tam - 1 ) + '-' + vr.substr( tam - 1, tam ); 
      }
      if ( (tam == 10) ) {
        vr = vr.substr( 0, tam - 7 ) + '.' + vr.substr( tam - 7, 3 ) + '.' + vr.substr( tam - 4, tam ); 
      }
    } else {
      var tammax = 14;
      if (tam < tammax) { tam = vr.length + 1 ; }
    
      tam = tam - 1;
      
      if ( (tam > 2) && (tam <= 5) ) {
        vr = vr.substr( 0, tam - 1 ) + '-' + vr.substr( tam - 1, tam );
      }
      if ( (tam >= 6) && (tam <= 8) ) {
        vr = vr.substr( 0, tam - 5 ) + '/' + vr.substr( tam - 5, 4 ) + '-' + vr.substr( tam - 1, tam );
      }
      
      if ( (tam >= 9) && (tam <= 11) ) {
        vr = vr.substr( 0, tam - 8 ) + '.' + vr.substr( tam - 8, 3 ) + '/' + vr.substr( tam - 5, 4 ) + '-' + vr.substr( tam - 1, tam ); 
      }
      
      if ( (tam >= 12) && (tam < 14) ) {
        vr = vr.substr( 0, tam - 11 ) + '.' + vr.substr( tam - 11, 3 ) + '.' + vr.substr( tam - 8, 3 ) + '/' + vr.substr( tam - 5, 4 ) + '-' + vr.substr( tam - 1, tam ); 
      }
    }
    
    campo.value = vr;
  },
  
  // Máscara de CEP
  cep_mask: function(event) {
    var campo = Event.element(event);
    var tammax = 8;
    var vr = campo.value;
    vr = vr.replace( "-", "" );
    vr = vr.replace( ".", "" );
    var tam = vr.length;
    if (tam < tammax) { tam = vr.length + 1; }
    
    tam = tam - 1;
    if ( (tam > 2) && (tam <= 8) ) {
      vr = vr.substr( 0, tam - 2 ) + '-' + vr.substr( tam - 2, tam ); 
    }
    if ( (tam == 7) ) {
      vr = vr.substr( 0, tam - 5 ) + '.' + vr.substr( tam - 5, tam ); 
    }
    campo.value = vr;
  },
  
  // Máscara de hora
  hour_mask: function(event) {
    var tecla = event;
    var codigo = (tecla.which ? tecla.which : tecla.keyCode ? tecla.keyCode : tecla.charCode);
  
    var campo = Event.element(event);
    var tammax = 4;   
    var vr = campo.value;
    vr = vr.replace( ":", "" );
    vr = vr.replace( ":", "" );
    var tam = vr.length;
    if (tam < tammax) { tam = vr.length + 1; }
    if (codigo == 8) { tam = tam - 1; }

    tam = tam - 1;
    if ( (tam >= 2) && (tam < 3) ) {
      vr = vr.substr( 0, tam - 0 ) + ':' + vr.substr( tam - 0, 2 ); 
    }
    if ( (tam >= 3) && (tam < 4) ) {
      vr = vr.substr( 0, tam - 1 ) + ':' + vr.substr( tam - 1, 2 ); 
    }
    if (tam == 4) {
      vr = vr.substr( 0, tam - 2 ) + ':' + vr.substr( tam - 2, 2 ) + ':' + vr.substr( tam - 0, 5 ); 
    }
    if (tam == 5) {
      vr = vr.substr( 0, tam - 3 ) + ':' + vr.substr( tam - 3, 2 ) + ':' + vr.substr( tam - 1, 6 ); 
    }
    campo.value = vr;
  },
  
  unformatNumber: function(iNum) {
    return String(iNum).replace(/\D/g, "").replace(/^0+/, "");
  }
  
};
