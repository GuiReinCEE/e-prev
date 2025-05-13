   function getEl(el) {
      if (typeof(el) == 'object') {
         return el;
      } else {
         if (document.getElementById) {
            return document.getElementById(el);
         } else {
            if (document.all) {
               return document.all(el);
            } else {
               alert('Navegador não suportado');
               return null;
            }
         }
      }
   }

   function valida_data(oData) {
     obj = getEl(oData);
      if (eh_data(oData) == true) {
	     return true;
	  } else {
		 oData.focus();
         oData.select(); 
//		 alert("A data "+oData.value+" não é válida.");         
		 return false;
      }	  
   }

   function mascara_data(dt){ 
      if (dt.value.length == 2){ 
         dt.value = dt.value + '/'; 
      } 
      if (dt.value.length == 5){ 
         dt.value = dt.value + '/'; 
      } 
   }; 

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

   function ano_bissexto(a) 
   {
      if ((a.value % 4) == 0) {  
         return true;
      }
      else {  
         return false;
      }
   };
   
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
      }
      return ret;
   }

   function caracteres_validos2(vlr, caract) {
      var i;
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
      else  {
         if (! caracteres_validos(o, "0123456789/")) {
            o.focus();
            o.select();
            return false;
         }
         else  {
            if (! verifica_data(o)) {
               return false;
            }
            else {
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

   function fltCaracteres(vlr, caracteres) {
      var ret = "";
      var i;
      var v = new String(vlr);
      var tm = v.length;
      for (i=0; i<tm; i++) {
         if (caracteres_validos2(v.substr(i,1), caracteres)==true) {
            ret = ret + v.substr(i,1);
         }
      }
      return ret;
   }

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

