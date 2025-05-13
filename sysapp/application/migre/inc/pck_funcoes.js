   function getElem(objId) {
      return (document.all ? document.all(objId) : document.getElementById(objId) );
   }

   function getEl(objId) {
      return (document.all ? document.all(objId) : document.getElementById(objId) );
   }

   function mascara_data(dt){ 
      if (dt.value.length == 2){ 
         dt.value = dt.value + '/'; 
      } 
      if (dt.value.length == 5){ 
         dt.value = dt.value + '/'; 
      } 
   }; 

   function mascara_datahora(dt){ 
      if (dt.value.length == 2){ 
         dt.value = dt.value + '/'; 
      } 
      if (dt.value.length == 5){ 
         dt.value = dt.value + '/'; 
      } 
      if (dt.value.length == 10){ 
         dt.value = dt.value + ' '; 
      } 
      if (dt.value.length == 13){ 
         dt.value = dt.value + ':'; 
      } 
   }; 

   function compara_datas(dt1, dt2) {
		if (dt1.value == "") {  
			return true;  
		}
		else
		{
			dia1 = (dt1.value.substring(0,2)); 
			mes1 = (dt1.value.substring(3,5)); 
			ano1 = (dt1.value.substring(6,10)); 
			dia2 = (dt2.value.substring(0,2)); 
			mes2 = (dt2.value.substring(3,5)); 
			ano2 = (dt2.value.substring(6,10)); 
			var Data1 = new Date(Date.UTC(ano1, mes1, dia1, 1, 0, 0));
			var Data2 = new Date(Date.UTC(ano2, mes2, dia2, 1, 0, 0));
			if (Data1 < Data2) {
				dt1.focus();
				dt1.select();
				alert("Data não pode ser menor do que " + dia2 + "/" + mes2 + "/" + ano2 + ".");
				return false;
			}
			else 
			{
				return true;
			}	
		}
	};

   // Data: 04/10/2005
   // Autor: Júlio C. Pereira           
   function valida_data(oData) {
      obj = getElem(oData.id);
      if (eh_data(oData) == true) {
	     return true;
	  } else {
         alert("A data "+oData.value+" não é válida.");
         obj.focus();
         obj.select();
		 return false;
      }	  
   }

   function verifica_data(dt) { 
      dia = (dt.value.substring(0,2)); 
      mes = (dt.value.substring(3,5)); 
      ano = (dt.value.substring(6,10)); 
      ret = false;
	  if (dt.value == "") {  
		  ret = true;  
	  }
	  else
	  {
         if ( (! caracteres_validos2(dia,"0123456789")) || (! caracteres_validos2(mes,"0123456789")) || (! caracteres_validos2(ano,"0123456789")) )
         {
	        ret = false;
         }
         else
         {
            if (mes < 1 || mes > 12 || dia < 1) 
            {  ret = false;  }
            else
            {
               if (dia < 1) 
               {  ret = false;  }
               else 
               {
                  if (mes==1 || mes==3 || mes==5 || mes==7 || mes==8 || mes==10 || mes==12) 
                  {
                     if (dia > 31) 
                     { ret = false;  }
                     else 
                     { ret = true;  }
                  }
                  else 
                  {
                     if (mes==4 || mes==6 || mes==9 || mes==11) 
                     {
                        if (dia > 30) 
                        {  ret = false;  }
                        else 
                        {  ret = true;  }
                     }
                     else 
                     {
                        if ((ano % 4) > 0)
                        {  
		                   if (dia > 28) 	 
                           {  ret = false;  }
                           else 
                           {  ret = true;  }
                        }
                        else
                        {
                           if (dia > 29) 
                           {  ret = false; }
                            else 
                           {  ret = true;  }
                        }
                     }
                  }
               }
            }
         }
      }
	  if (ret == false) 
	  { 
	  	 dt.value = "";
	     dt.focus();
		 dt.select();
	     alert("Data inválida");
	  }
   }; 
   
   function eh_data(dt) { 
      dia = (dt.value.substring(0,2)); 
      mes = (dt.value.substring(3,5)); 
      ano = (dt.value.substring(6,10)); 
      ret = false;
	  if (dt.value == "") {  
		  ret = true;  
	  }
	  else
	  {
         if ( (! caracteres_validos2(dia,"0123456789")) || (! caracteres_validos2(mes,"0123456789")) || (! caracteres_validos2(ano,"0123456789")) )
         {
	        ret = false;
         }
         else
         {
            if (mes < 1 || mes > 12 || dia < 1) 
            {  ret = false;  }
            else
            {
               if (dia < 1) 
               {  ret = false;  }
               else 
               {
                  if (mes==1 || mes==3 || mes==5 || mes==7 || mes==8 || mes==10 || mes==12) 
                  {
                     if (dia > 31) 
                     { ret = false;  }
                     else 
                     { ret = true;  }
                  }
                  else 
                  {
                     if (mes==4 || mes==6 || mes==9 || mes==11) 
                     {
                        if (dia > 30) 
                        {  ret = false;  }
                        else 
                        {  ret = true;  }
                     }
                     else 
                     {
                        if ((ano % 4) > 0)
                        {  
		                   if (dia > 28) 	 
                           {  ret = false;  }
                           else 
                           {  ret = true;  }
                        }
                        else
                        {
                           if (dia > 29) 
                           {  ret = false; }
                            else 
                           {  ret = true;  }
                        }
                     }
                  }
               }
            }
         }
      }
	  return ret;
   }; 

   function verifica_datahora(dt) {
      if (eh_datahora(dt) == false) {
	     alert("Data/hora inválida");
		 dt.focus();
      }
   }
   
   function eh_datahora(dt) { 
      dia = (dt.value.substring(0,2)); 
      mes = (dt.value.substring(3,5)); 
      ano = (dt.value.substring(6,10)); 
	  hora   = (dt.value.substring(11,13)); 
	  minuto = (dt.value.substring(14,16)); 
//	  alert('Dia: '+dia+'\nMes: '+mes+' Ano: '+ano+' Hora: '+hora+' Minuto: '+minuto);
      ret = true;

      if (dt.value == "") {  
         ret = true;  
      }
      else
      {
         if ( (! caracteres_validos2(dia,"0123456789")) || (! caracteres_validos2(mes,"0123456789")) || (! caracteres_validos2(ano,"0123456789")) ||  (! caracteres_validos2(hora,"0123456789")) || (! caracteres_validos2(minuto,"0123456789")) )
         {
            ret = false;
         }
         else
         {
		    // Testa Hora
		    if (hora < 0 || hora > 23) 
			{  
			   ret = false; 
			}
			// Testa Minuto
            if (minuto < 0 || minuto > 59) 
            { 
               ret = false; 
            }
			// Testa Mes
            if (mes < 1 || mes > 12) 
            {  
               ret = false;  
            }
			// Testa Dia
            if (dia < 1 || dia > dias_mes(mes,ano) )
            {  
               ret = false;  
            }
         }
      }
	  return ret;
   }; 

   function ano_bissexto(a) 
   {
      if ((a.value % 4) == 0)
      {  
         return true;
      }
      else
      {  
         return false;
      }
   };
   
   function dias_mes(m, a) {
      ret = 0;
      if (m==1 || m==3 || m==5 || m==7 || m==8 || m==10 || m==12) {
	     ret = 31;
	  }
      if (m==4 || m==6 || m==9 || m==11) {
	     ret = 30;
	  }
	  if (m==2) {
	     if (ano_bissexto(a) == true) {
		    ret = 29;
		 } else {
		    ret = 28;
		 }
	  }
	  return ret;
   }
   
   function valida_limites_vlr(obj, min, max) {
      if (obj.value != "") 
      {
         if (obj.value < min || obj.value > max) 
         {
            alert("O valor deve estar entre " + min + "% e " + max + "%");
            obj.focus();
         }
      }
   };

   function caracteres_validos(obj, caract) {
      ret = true;
      for (i=0; i < obj.value.length; i++) {
         if (caract.indexOf(obj.value.charAt(i)) < 0) { 
			ret = false; 
		 }
      }
      if (ret == false) {
         alert("Existe(m) caracter(es) inválido(s)");
	  	 obj.value = "";
	     obj.focus();
		 obj.select();

      }
	  return ret;
   }

   function caracteres_validos2(vlr, caract) {
      ret = true;
      for (i=0; i < vlr.length; i++) {
         if (caract.indexOf(vlr.charAt(i)) < 0) { 
			ret = false; 
		 }
      }
	  return ret;
   }

   function format_number(num, decimais) {
      n = Math.round(num * Math.pow(10,decimais)) / Math.pow(10,decimais);
      return n
   };

   function multiplica(obj, qtd, vlr) {
	  var s = new String();
      v = format_number((qtd.value * vlr),2);
	  s = v.toString();
      obj.value = s.replace(".",",");
   };

   function data_obrigatoria(o) {
      if (o.value == "") {
         o.focus();
         o.select();
	     return false;
	  }
	  else
	  {
	     if (! caracteres_validos(o, "0123456789/")) {
			o.focus();
		    o.select();
		    return false;
		 }
         else
		 {
		    if (! verifica_data(o)) {
			   return false;
			}
			else
			{
			   return true;
			}
	     }
	  }
   }

   function formata_numero(cmp) {
      v = new String(cmp.value);
      v = v.replace(",", "");
      v = v.replace(".", "");
      if (caracteres_validos2(v, "0123456789") == true) {
         tm = v.length;
         if (tm > 2) {v = v.substr(0, tm-2) + "," + v.substr(tm-2, tm);}
         tm = v.length;
         if (tm > 6) {v = v.substr(0, tm-6) + "." + v.substr(tm-6, tm);} 
         cmp.value = v;
         return true;
      }
      else {
         return false;
      }
   }

   function valor_entre(v,i,f) {
      if ((v >= i) && (v <= f)) {
	     return true;
	  }
	  else {
	     return false;
      }
   }
   
   function envia_mensagem(msg) {
            alert(msg);
   };

   function lpad(valor,tam,caracter) {
      var ret = new String(valor);
	  while (ret.length < tam) {
	     ret = caracter + ret;
      }
	  return ret;
   }

   function rpad(valor,tam,caracter) {
      var ret = new String(valor);
	  while (ret.length < tam) {
	     ret = ret + caracter;
      }
	  return ret;
   }

   function now(mask) {
      /*
		 W - Dia da Semana
	     DD - Dia
		 MM - Mes
		 YYYY - Ano
		 HH - Hora
		 MI - Minuto
		 SS - Segundo
	  */
      var dt = new Date();
	  var ret = new String(mask);
	  // Obtem Dados
	  dia_semana = dt.getDay() + 1;
	  dia = lpad(dt.getDate(), 2, "0");
	  mes = lpad(dt.getMonth() + 1, 2, "0");
	  ano = lpad(dt.getFullYear(), 4, "0");
	  hora = lpad(dt.getHours(), 2, "0");
	  minuto = lpad(dt.getMinutes(), 2, "0");
	  segundo = lpad(dt.getSeconds(), 2, "0");
	  // Efetua Substituições
	  ret = ret.replace("W",dia_semana);
	  ret = ret.replace("DD",dia);
	  ret = ret.replace("MM",mes);
	  ret = ret.replace("YYYY",ano);
	  ret = ret.replace("HH",hora);
	  ret = ret.replace("MI",minuto);
	  ret = ret.replace("SS",segundo);
	  //
	  return ret;
   }
   
function valida_etiqueta(numero){ 
	if (numero.value.length == 20){ 
		w = window.open("etiqueta.php?r="+numero.value+"&h=", "wskin2", "menubar=no,location=no,scrollbars=no,resizable=no,width=500,height=300");
	}
//	if (numero.value.length == 2){ 
//		numero.value = numero.value + '/'; 
//	} 
//	if (numero.value.length == 5){ 
//		numero.value = numero.value + '/'; 
//	} 
}; 
   function fmtNumber(cmp, fmt) {
      /****
        Autor....: Júlio Corrêa Pereira
        Data.....: 09/09/2003
        Evento...: onKeyUp
        Descrição: Formata valores num campo do tipo text conforme a máscara especificada
        Obs......: Caracteres válidos para a formatação (fmt): 9,.
                   Pode-se utilizar css para alinhar os dados à direita dentro do campo. 
                   Ex.: ... style="text-align: right" ...
        Ex.: <input type="text" size=15 maxlength=12 onKeyUp="return fmtNumber(this, '99.999.999,99')" style="text-align: right">
      **/
      f = new String(fmt);
      v = new String(cmp.value);
      v = v.replace(",", "");
      v = v.replace(".", "");
      aux = new String("");
      if (!caracteres_validos2(v, "0123456789") == true) {
         v = fltCaracteres(v, "0123456789");
      }
      tm = v.length-1;
      j = f.length-1;
      for (i=tm;i>-1;i--) {
         if (f.substr(j,1) == 9) {
            aux = v.substr(i,1) + aux; 
         }
         else {
            if ((v.substr(i,1) >= "0") && (v.substr(i,1) <= "9")) {
               aux = v.substr(i,1) + f.substr(j,1) + aux;
               j--;
            }
         }
         j--;
      }
      cmp.value = aux;
      return true;
   }
     
function faixa(v, menor, maior) {
      if ((v >= menor) && (v <= maior)) {
         return true;
      }
      else {
         alert("Valor fora da faixa aceitável. Deve estar entre " + menor + " e " + maior + ".");
         return false;
      }
   } 
   
function trimValue(str)
{
    while (str.charAt(0) == " ")
    {
        str = str.substr(1,str.length -1);
    }

    while (str.charAt(str.length-1) == " ")
    {
        str = str.substr(0,str.length-1);
    }
    str = str.replace(/\r|\n|\r\n/g,"");
    return str;
}   