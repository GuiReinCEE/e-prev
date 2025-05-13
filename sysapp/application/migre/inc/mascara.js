/********************************************************************** 
CONVERTE PARA MAISCULO STRING DIGITADA
OnKeyDown="mascaraMaiusculo(this,event);"
**********************************************************************/
function mascaraMaiusculo(e) 
{
	this.value = this.value.toUpperCase();
}

/**********************************************************************
Função de formatação de valores numéricos na digitação

Objetivos :
	- Inclusão automática de . e ,

Parametros :
	objeto		-> Nome do campo de formulário (Usar this)
	teclapres	-> Tecla pressionada (Usar event)
	tammax		-> Tamanho máximo de caracteres
	decimais	-> Nº de casas decimais

Exemplo:
	OnKeyDown     mascaraValor(this,event,17,0);
/**********************************************************************/
function mascaraValor(objeto,teclapres,tammax,decimais) 
{
	if (String.fromCharCode(event.keyCode) >=0 || String.fromCharCode(event.keyCode) <=9)
	{
	}
	else
	{
		return false;
	}
	var tecla			= teclapres.keyCode;
	var tamanhoObjeto	= objeto.value.length;

	if ((tecla == 8) && (tamanhoObjeto == tammax))
	{
		tamanhoObjeto = tamanhoObjeto - 1 ;
	}



	if (( tecla == 8 || tecla == 88 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 ) && ((tamanhoObjeto+1) <= tammax))
		{

			vr	= objeto.value;
			vr	= vr.replace( "/", "" );
			vr	= vr.replace( "/", "" );
			vr	= vr.replace( ",", "" );
			vr	= vr.replace( ".", "" );
			vr	= vr.replace( ".", "" );
			vr	= vr.replace( ".", "" );
			vr	= vr.replace( ".", "" );
			vr  = vr.replace( ".", "" );
			vr  = vr.replace( ".", "" );

			tam	= vr.length;

			if (tam < tammax && tecla != 8)
			{
				tam = vr.length + 1 ;
			}

			if ((tecla == 8) && (tam > 1))
			{
				tam = tam - 1 ;
				vr = objeto.value;
				vr = vr.replace( "/", "" );
				vr = vr.replace( "/", "" );
				vr = vr.replace( ",", "" );
				vr = vr.replace( ".", "" );
				vr = vr.replace( ".", "" );
				vr = vr.replace( ".", "" );
				vr = vr.replace( ".", "" );
				vr = vr.replace( ".", "" );
				vr = vr.replace( ".", "" );

		}

		//Cálculo para casas decimais setadas por parametro
		if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 )
		{
			if (decimais > 0)
			{

				if ( (tam <= decimais) )
				{ 
					objeto.value = ("0," + vr) ;
				}
				if( (tam == (decimais + 1)) && (tecla == 8))
				{
					objeto.value = vr.substr( 0, (tam - decimais)) + ',' + vr.substr( tam - (decimais), tam ) ;	
				}
				if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) == "0"))
				{
					objeto.value = vr.substr( 1, (tam - (decimais+1))) + ',' + vr.substr( tam - (decimais), tam ) ;
				}
				if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) != "0"))
				{
				    objeto.value = vr.substr( 0, tam - decimais ) + ',' + vr.substr( tam - decimais, tam ) ; 
				}

				if ( (tam >= (decimais + 4)) && (tam <= (decimais + 6)) )
				{
			 		objeto.value = vr.substr( 0, tam - (decimais + 3) ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
				}
			 	if ( (tam >= (decimais + 7)) && (tam <= (decimais + 9)) )
				{
			 		objeto.value = vr.substr( 0, tam - (decimais + 6) ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
				}
				if ( (tam >= (decimais + 10)) && (tam <= (decimais + 12)) )
				{
			 		objeto.value = vr.substr( 0, tam - (decimais + 9) ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
				}
				if ( (tam >= (decimais + 13)) && (tam <= (decimais + 15)) )
				{
			 		objeto.value = vr.substr( 0, tam - (decimais + 12) ) + '.' + vr.substr( tam - (decimais + 12), 3 ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
				}
				if ( (tam >= (decimais + 16)) && (tam <= (decimais + 18)) )
				{
			 		objeto.value = vr.substr( 0, tam - (decimais + 15) ) + '.' + vr.substr( tam - (decimais + 15), 3 ) + '.' + vr.substr( tam - (decimais + 12), 3 ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
				}
				if ( (tam >= (decimais + 19)) && (tam <= (decimais + 21)) )
				{
			 		objeto.value = vr.substr( 0, tam - (decimais + 18) ) + '.' + vr.substr( tam - (decimais + 18), 3 ) + '.' + vr.substr( tam - (decimais + 15), 3 ) + '.' + vr.substr( tam - (decimais + 12), 3 ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
				}
				if ( (tam >= (decimais + 22)) && (tam <= (decimais + 24)) )
				{
			 		objeto.value = vr.substr( 0, tam - (decimais + 21) ) + '.' + vr.substr( tam - (decimais + 21), 3 ) + '.' + vr.substr( tam - (decimais + 18), 3 ) + '.' + vr.substr( tam - (decimais + 15), 3 ) + '.' + vr.substr( tam - (decimais + 12), 3 ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
				}
				if ( (tam >= (decimais + 25)) && (tam <= (decimais + 27)) )
				{
			 		objeto.value = vr.substr( 0, tam - (decimais + 24) ) + '.' + vr.substr( tam - (decimais + 24), 3 ) + '.' + vr.substr( tam - (decimais + 21), 3 ) + '.' + vr.substr( tam - (decimais + 18), 3 ) + '.' + vr.substr( tam - (decimais + 15), 3 ) + '.' + vr.substr( tam - (decimais + 12), 3 ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
				}
				if ( (tam >= (decimais + 28)) && (tam <= (decimais + 30)) )
				{
			 		objeto.value = vr.substr( 0, tam - (decimais + 27) ) + '.' + vr.substr( tam - (decimais + 27), 3 ) + '.' + vr.substr( tam - (decimais + 24), 3 ) + '.' + vr.substr( tam - (decimais + 21), 3 ) + '.' + vr.substr( tam - (decimais + 18), 3 ) + '.' + vr.substr( tam - (decimais + 15), 3 ) + '.' + vr.substr( tam - (decimais + 12), 3 ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
				}
				if ( (tam >= (decimais + 31)) && (tam <= (decimais + 33)) )
				{
			 		objeto.value = vr.substr( 0, tam - (decimais + 30) ) + '.' + vr.substr( tam - (decimais + 30), 3 ) + '.' + vr.substr( tam - (decimais + 27), 3 ) + '.' + vr.substr( tam - (decimais + 24), 3 ) + '.' + vr.substr( tam - (decimais + 21), 3 ) + '.' + vr.substr( tam - (decimais + 18), 3 ) + '.' + vr.substr( tam - (decimais + 15), 3 ) + '.' + vr.substr( tam - (decimais + 12), 3 ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
				}

			}
			else if(decimais == 0)
			{
				if ( tam <= 3 )
				{ 
			 		objeto.value = vr ;
				}
				if ( (tam >= 4) && (tam <= 6) )
				{
					if(tecla == 8)
					{
						objeto.value = vr.substr(0, tam);
						window.event.cancelBubble = true;
						window.event.returnValue = false;
					}
					objeto.value = vr.substr(0, tam - 3) + '.' + vr.substr( tam - 3, 3 ); 
				}
				if ( (tam >= 7) && (tam <= 9) )
				{
					if(tecla == 8)
					{
						objeto.value = vr.substr(0, tam);
						window.event.cancelBubble = true;
						window.event.returnValue = false;
					}
					objeto.value = vr.substr( 0, tam - 6 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ); 
				}
				if ( (tam >= 10) && (tam <= 12) )
				{
			 		if(tecla == 8)
					{
						objeto.value = vr.substr(0, tam);
						window.event.cancelBubble = true;
						window.event.returnValue = false;
					}
					objeto.value = vr.substr( 0, tam - 9 ) + '.' + vr.substr( tam - 9, 3 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ); 
				}
				if ( (tam >= 13) && (tam <= 15) )
				{
					if(tecla == 8)
					{
						objeto.value = vr.substr(0, tam);
						window.event.cancelBubble = true;
						window.event.returnValue = false;
					}
					objeto.value = vr.substr( 0, tam - 12 ) + '.' + vr.substr( tam - 12, 3 ) + '.' + vr.substr( tam - 9, 3 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ) ;
				}			
			}
		}
	}
	else if((window.event.keyCode != 8) && (window.event.keyCode != 9) && (window.event.keyCode != 13) && (window.event.keyCode != 35) && (window.event.keyCode != 36) && (window.event.keyCode != 46))
		{
			window.event.cancelBubble = true;
			window.event.returnValue = false;
		}
}
/********************************************************************** 
Função de formatação de campos tipo texto durante a digitação

Objetivos :
	- Mudar tudo para maiúsculo
	- Não aceitar acentos nem Ç
	- Especificar um tamanho máximo para o campo

Parâmetros :
	objeto		-> Nome do campo de formulário (Usar this)
	tammax      -> Tamanho maximo que o campo deve conter

Exemplo : 
	OnKeyPress    mascaraTexto(this,10);
/**********************************************************************/
function mascaraTexto(objeto,tammax)
{
	var valor = objeto.value;	
	var LetrasArray = new Array();

	LetrasArray[1] = new Array();
	LetrasArray[2] = new Array();
	
	//Primeira Coluna : Letras Acentuadas e Símbolos
	LetrasArray[1][1]='á';
	LetrasArray[1][2]='ã';
	LetrasArray[1][3]='à';
	LetrasArray[1][4]='â';
	LetrasArray[1][5]='ä';
	LetrasArray[1][6]='é';
	LetrasArray[1][7]='è';
	LetrasArray[1][8]='ê';
	LetrasArray[1][9]='ë';
	LetrasArray[1][10]='í';
	LetrasArray[1][11]='ì';
	LetrasArray[1][12]='î';
	LetrasArray[1][13]='ï';
	LetrasArray[1][14]='ó';
	LetrasArray[1][15]='õ';
	LetrasArray[1][16]='ò';
	LetrasArray[1][17]='ô';
	LetrasArray[1][18]='ö';
	LetrasArray[1][19]='ú';
	LetrasArray[1][20]='ù';
	LetrasArray[1][21]='û';
	LetrasArray[1][22]='ü';
	LetrasArray[1][23]='ç';
	LetrasArray[1][24]='Á';
	LetrasArray[1][25]='Ã';
	LetrasArray[1][26]='À';
	LetrasArray[1][27]='Â';
	LetrasArray[1][28]='Ä';
	LetrasArray[1][29]='É';
	LetrasArray[1][30]='È';
	LetrasArray[1][31]='Ê';
	LetrasArray[1][32]='Ë';
	LetrasArray[1][33]='Í';
	LetrasArray[1][34]='Ì';
	LetrasArray[1][35]='Î';
	LetrasArray[1][36]='Ï';
	LetrasArray[1][37]='Ó';
	LetrasArray[1][38]='Õ';
	LetrasArray[1][39]='Ò';
	LetrasArray[1][40]='Ô';
	LetrasArray[1][41]='Ö';
	LetrasArray[1][42]='Ú';
	LetrasArray[1][43]='Ù';
	LetrasArray[1][44]='Û';
	LetrasArray[1][45]='Ü';
	LetrasArray[1][46]='Ç';
	LetrasArray[1][47]='Ñ';
	LetrasArray[1][48]='ñ';
	LetrasArray[1][49]='~';
	LetrasArray[1][50]='^';
	LetrasArray[1][51]='´';
	LetrasArray[1][52]='`';

	//Segunda Coluna : Letras Equivalentes sem acentos
	LetrasArray[2][1]='a';
	LetrasArray[2][2]='a';
	LetrasArray[2][3]='a';
	LetrasArray[2][4]='a';
	LetrasArray[2][5]='ä';
	LetrasArray[2][6]='e';
	LetrasArray[2][7]='e';
	LetrasArray[2][8]='e';
	LetrasArray[2][9]='e';
	LetrasArray[2][10]='i';
	LetrasArray[2][11]='i';
	LetrasArray[2][12]='i';
	LetrasArray[2][13]='i';
	LetrasArray[2][14]='o';
	LetrasArray[2][15]='o';
	LetrasArray[2][16]='o';
	LetrasArray[2][17]='o';
	LetrasArray[2][18]='o';
	LetrasArray[2][19]='u';
	LetrasArray[2][20]='u';
	LetrasArray[2][21]='u';
	LetrasArray[2][22]='u';
	LetrasArray[2][23]='c';
	LetrasArray[2][24]='A';
	LetrasArray[2][25]='A';
	LetrasArray[2][26]='A';
	LetrasArray[2][27]='A';
	LetrasArray[2][28]='Ä';
	LetrasArray[2][29]='E';
	LetrasArray[2][30]='E';
	LetrasArray[2][31]='E';
	LetrasArray[2][32]='E';
	LetrasArray[2][33]='I';
	LetrasArray[2][34]='I';
	LetrasArray[2][35]='I';
	LetrasArray[2][36]='I';
	LetrasArray[2][37]='O';
	LetrasArray[2][38]='O';
	LetrasArray[2][39]='O';
	LetrasArray[2][40]='O';
	LetrasArray[2][41]='O';
	LetrasArray[2][42]='U';
	LetrasArray[2][43]='U';
	LetrasArray[2][44]='U';
	LetrasArray[2][45]='U';
	LetrasArray[2][46]='C';
	LetrasArray[2][47]='N';
	LetrasArray[2][48]='n';
	LetrasArray[2][49]='';
	LetrasArray[2][50]='';
	LetrasArray[2][51]='';
	LetrasArray[2][52]='';

	temp = "" + valor + (String.fromCharCode(window.event.keyCode));

	for (i=1; i<=52 ; i++ )
	{
		while (temp.indexOf((LetrasArray[1][i]))>-1)
			{
				pos= temp.indexOf((LetrasArray[1][i]));
				temp = "" + (temp.substring(0, pos) + (LetrasArray[2][i]) + temp.substring((pos + (LetrasArray[1][i].length)), temp.length));
			}
	}
	if((window.event.keyCode) != 13)
		{
			objeto.value = (temp.toUpperCase().substring(0,tammax));
			window.event.cancelBubble = true;
			window.event.returnValue = false;
		}

}

/**********************************************************************
Função de formatação de campos tipo texto durante a digitação

Objetivos :
	- Não aceitar caracteres especiais
      Ex.: ", '
    - Especificar um maxlength para esse campo
      Ex.: campo de 10 posicoes

Parâmetros :
	objeto		    -> Nome do campo de formulário (Usar this)
	tamanho maximo  -> Tamanho maximo que o campo irá ter

Exemplo :
	OnKeyPress    mascaraCaracterEspecial(this,10);
/**********************************************************************/
function mascaraCaracterEspecial(objeto,tammax)
{
	var valor = objeto.value;
	var LetrasArray = new Array();

	LetrasArray[1] = new Array();
	LetrasArray[2] = new Array();

	//Primeira Coluna : Símbolos
	LetrasArray[1][1]='"';
	LetrasArray[1][2]='\'';
	LetrasArray[1][3]="\\";

	//Segunda Coluna : Símbolos Equivalentes
	LetrasArray[2][1]='';
	LetrasArray[2][2]='';
	LetrasArray[2][3]='';


	temp = "" + valor + (String.fromCharCode(window.event.keyCode));

	for (i=1; i<=3 ; i++ )
	{
		while (temp.indexOf((LetrasArray[1][i]))>-1)
			{
				pos= temp.indexOf((LetrasArray[1][i]));
				
				temp = "" + (temp.substring(0, pos) + (LetrasArray[2][i]) + temp.substring((pos + (LetrasArray[1][i].length)), temp.length));
				
				
			}
	}
	if((window.event.keyCode) != 13)
		{
			objeto.value = temp.substring(0,tammax).toUpperCase();
			window.event.cancelBubble = true;
			window.event.returnValue = false;
		}

}

/********************************************************************** 
Função de validação de campos do tipo Data

Objetivos :
	- Aceitar somente datas do tipo : dd/mm/aaaa

Parâmetros :
	objeto		-> Nome do campo de formulário (Usar this)

Exemplo : 
	OnChange    validaData(this);
/**********************************************************************/ 
function validaData(objeto) 
{

	var DataString	= objeto.value;
	var DataArray	= DataString.split("/");  
	var Flag=true; 

	if (DataArray.length != 3) 
		Flag=false; 
	else 
		{
			if (DataArray.length==3) 
			{
				var dia = DataArray[0], mes = DataArray[1], ano = DataArray[2]; 

				if (((Flag) && (ano<1000) || ano.length>4)) 
					Flag=false; 
				
				if (Flag) 
				{ 
					verifica_mes = new Date(mes+"/"+dia+"/"+ano); 
					if (verifica_mes.getMonth() != (mes - 1)) 
						Flag=false; 
				} 
			} 
			else 
				Flag=false; 
		} 
return Flag;
} 

/********************************************************************** 
Função de formatação de campos do tipo Data

Objetivos :
	- Mascarar a entrada de dados no formato : dd/mm/aaaa

Parâmetros :
	objeto		-> Nome do campo de formulário (Usar this)
	teclapress	-> Tecla pressionada (Usar event)

Exemplo : 
	OnKeyDown="mascaraData(this,event);" maxlength="10"

Requirido :
	Função validaData
/**********************************************************************/ 
function mascaraData(objeto,event)
{
	var tecla = event.keyCode;
    var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;

	if(((keyCode == 13) || (keyCode == 9))&&objeto.value != "")
	{
		if(!(validaData(objeto)))
			{
				try{
					window.event.cancelBubble = true;
					window.event.returnValue = false;
				}catch(e)
				{
					event.cancelBubble = true;
					event.returnValue = false;
				}
				objeto.value = "";
				objeto.focus();
			}
	}

	if (( tecla == 8 || tecla == 88 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 )&& objeto.value.length < (10))
    {
		vr = objeto.value;
		vr = vr.replace( "/", "" );
		vr = vr.replace( "/", "" );
		tam = vr.length;

		if (tam < 8)
			{
				if (tecla != 8) {tam = vr.length + 1 ;}
			}
		else
			{
				try{
					window.event.cancelBubble = true;
					window.event.returnValue = false;
				}catch(e)
				{
					event.cancelBubble = true;
					event.returnValue = false;
				}				
			}
		
		if ((tecla == 8) && (tam > 1))
			{
				tam = tam - 1 ;
				objeto.value = vr.substr(0,tam);
				try{
					window.event.cancelBubble = true;
					window.event.returnValue = false;
				}catch(e)
				{
					event.cancelBubble = true;
					event.returnValue = false;
				}
			}
				if ( tam <= 4 && tecla != 8){ 
			 		objeto.value = vr ; }

				if ( (tam >= 4) && (tam <= 6) ){
			 		objeto.value = vr.substr(0, tam - 4) + '/' + vr.substr( tam - 4, 4 ); }

				if ( (tam >= 6) && (tam <= 8) ){
					objeto.value = vr.substr(0, tam - 6 ) + '/' + vr.substr( tam - 6, 2 ) + '/' + vr.substr( tam - 4, 4 ); }

				if ((tam == (8)) && tecla != 8)
					{
						if(tecla >=96 && tecla <=105)
							{
								tecla = tecla - 48;
							}

						objeto.value = objeto.value + (String.fromCharCode(tecla));
						
						try
						{
							window.event.cancelBubble = true;
							window.event.returnValue = false;
						}
						catch(e)
						{
							event.cancelBubble = true;
							event.returnValue = false;						
						}

						if (!(validaData(objeto)))
							{
								alert("Data Inválida");
								objeto.value = "";
								objeto.focus();
							}
					}
	}
	else if((keyCode != 8) && (keyCode != 9) && (keyCode != 13) && (keyCode != 35) && (keyCode != 36) && (keyCode != 46))
	{
		try
		{
			
			window.event.returnValue = false;
		}
		catch(e)
		{
			event.returnValue = false;						
		}			
	}
}


/********************************************************************** 
Função de validação de campos do tipo Data Hora

Objetivos :
	- Aceitar somente datas do tipo : dd/mm/aaaa hh:mm:ss

Parâmetros :
	objeto		-> Nome do campo de formulário (Usar this)

Exemplo : 
	OnChange    validaDataHora(this);
/**********************************************************************/ 
function validaDataHora(objeto) 
{        
	var DataHoraString	= objeto.value;
	var Flag			= true;
	
	if(DataHoraString != "" &&((DataHoraString.indexOf("/")!= -1) && (DataHoraString.indexOf(" ")!= -1) && (DataHoraString.indexOf(":")!= -1)))
	{
//		dd/mm/aaaa hh:mm:ss
		var DataArray	= DataHoraString.split("/");  
//		DataArray[0]	= dd;
//		DataArray[1]	= mm;	
//		DataArray[2]	= aaaa hh:mm:ss	;

		var AuxArray	= DataArray[2].split(" ");
//		AuxArray[0]		= aaaa;
//		AuxArray[1]		= hh:mm:ss;
	
		DataArray[2]	= AuxArray[0];
	
		var HoraArray	= AuxArray[1].split(":");
//		HoraArray[0]	= hh;
//		HoraArray[1]	= mm;
//		HoraArray[2]	= ss;

		if(HoraArray[2] >= 0){Flag = true;} 
	}
	else {Flag= false;}

if(Flag)
	{
		//Valida a data
		if (DataArray.length != 3) 
			Flag=false; 
		else 
			{
				if (DataArray.length==3) 
				{
					var dia = DataArray[0], mes = DataArray[1], ano = DataArray[2]; 
	
					if (((Flag) && (ano<1000) || ano.length>4)) 
						Flag=false; 
					
					if (Flag) 
					{ 
						verifica_mes = new Date(mes+"/"+dia+"/"+ano); 
						if (verifica_mes.getMonth() != (mes - 1)) 
							Flag=false; 
					} 
				} 
				else 
					Flag=false; 
			} 
	
		//Valida a hora
		if (HoraArray.length != 3) 
			Flag=false; 
		else 
			{
				if (HoraArray.length==3) 
				{
					var hora = HoraArray[0], min = HoraArray[1], seg = HoraArray[2]; 
					
					if(!((hora>=0 && hora <=23) && (min>=0 && min<=59) && (seg>=0 && seg<=59)))
						Flag=false;
				} 
				else 
					Flag=false; 
			} 
	}		
return Flag;
} 

/********************************************************************** 
Função de formatação de campos do tipo Data Hora

Objetivos :
	- Mascarar a entrada de dados no formato : dd/mm/aaaa hh:mm:ss

Parâmetros :
	objeto		-> Nome do campo de formulário (Usar this)
	teclapress	-> Tecla pressionada (Usar event)

Exemplo : 
	OnKeyPress    mascaraDataHora(this);

Requirido :
	Função validaDataHora(objeto);
/**********************************************************************/ 
function mascaraDataHora(objeto,teclapress)
{
	var tecla = teclapress.keyCode;

	if(((window.event.keyCode == 13) || (window.event.keyCode == 9))&&objeto.value != "")
	{
		if(!(validaDataHora(objeto)))
			{
				window.event.cancelBubble = true;
				window.event.returnValue = false;
				showAlert('aviso', "Data Hora Inválida",'', 200,75);
				objeto.value = "";
				objeto.focus();
			}
	}

	if (( tecla == 8 || tecla == 88 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 )&& objeto.value.length <= (19))
    {
		vr = objeto.value;
		vr = vr.replace( "/", "" );
		vr = vr.replace( "/", "" );
		vr = vr.replace( " ", "" );
		vr = vr.replace( ":", "" );
		vr = vr.replace( ":", "" );
		tam = vr.length;

		if (tam < 14)
			{
				if (tecla != 8) {tam = vr.length + 1 ;}
			}
		else
			{
				window.event.cancelBubble = true;
				window.event.returnValue = false;
			}
		
		if ((tecla == 8) && (tam > 1))
			{
				tam = tam - 1 ;
				objeto.value = vr.substr(0,tam);
				window.event.cancelBubble = true;
				window.event.returnValue = false;
			}
				if ( tam <= 2 && tecla != 8){ 
			 		objeto.value = vr ; }

				if ( (tam >= 2) && (tam <= 4) ){
			 		objeto.value = vr.substr(0, tam - 2) + ':' + vr.substr( tam - 2, 2 ); }

				if ( (tam >= 4) && (tam <= 6) ){
					objeto.value = vr.substr(0, tam - 4 ) + ':' + vr.substr( tam - 4, 2 ) + ':' + vr.substr( tam - 2, 2 ); }

				if ( (tam >= 6) && (tam <= 10) ){
					objeto.value = vr.substr(0, tam - 6 ) + ' ' + vr.substr( tam - 6, 2 ) + ':' + vr.substr( tam - 4, 2 ) + ':' + vr.substr( tam - 2, 2 ); }


				if ( (tam >= 10) && (tam <= 12) ){
					objeto.value = vr.substr(0, tam - 10 ) + '/' + vr.substr( tam - 10, 4 ) + ' ' + vr.substr( tam - 6, 2 ) + ':' + vr.substr( tam - 4, 2 ) + ':' + vr.substr( tam - 2, 2 ) ;}			

				if ( (tam >= 12) && (tam <= 14) ){
					objeto.value = vr.substr(0, tam - 12 ) + '/' + vr.substr( tam - 12, 2 ) + '/' + vr.substr( tam - 10, 4 ) + ' ' + vr.substr( tam - 6, 2 ) + ':' + vr.substr( tam - 4, 2 ) + ':' + vr.substr( tam - 2, 2 ) ;}			

				if ((tam == (14)) && tecla != 8)
					{
						if (!(validaDataHora(objeto)))
							{
								if(tecla >=96 && tecla <=105){tecla = tecla - 48;}
								objeto.value = objeto.value + (String.fromCharCode(tecla));
								window.event.cancelBubble = true;
								window.event.returnValue = false;
								showAlert('aviso', "Data Hora Inválida", "", 200,75);
								objeto.value = "";
								objeto.focus();
							}
					}
	}
	else if((window.event.keyCode != 8) && (window.event.keyCode != 9) && (window.event.keyCode != 13) && (window.event.keyCode != 35) && (window.event.keyCode != 36) && (window.event.keyCode != 46))
		{
			event.returnValue = false;
		}
}


/**********************************************************************
Função de formatação de campos do tipo Hora

Objetivos :
	- Mascarar a entrada de dados no formato : hh:mm

Parâmetros :
	objeto		-> Nome do campo de formulário (Usar this)
	teclapress	-> Tecla pressionada (Usar event)

Exemplo :
	OnKeyPress    mascaraHora(this);

Requirido :
	Função validaHora(objeto);
/**********************************************************************/
function mascaraHora(objeto,event)
{
	var tecla = event.keyCode;
    var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;

	if(((keyCode == 13) || (keyCode == 9))&&objeto.value != "")
	{
		if(!(validaData(objeto)))
			{
				try{
					window.event.cancelBubble = true;
					window.event.returnValue = false;
				}catch(e)
				{
					event.cancelBubble = true;
					event.returnValue = false;
				}
				alert("Hora Inválida");
				objeto.value = "";
				objeto.focus();
			}
	}
	

	if (( tecla == 8 || tecla == 88 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 )&& objeto.value.length <= (19))
    {
		vr = objeto.value;
		vr = vr.replace( " ", "" );
		vr = vr.replace( ":", "" );
		tam = vr.length;

		if (tam < 4)
			{
				if (tecla != 8) {tam = vr.length + 1 ;}
			}
		else
			{
				try{
					window.event.cancelBubble = true;
					window.event.returnValue = false;
				}catch(e)
				{
					event.cancelBubble = true;
					event.returnValue = false;
				}
			}

		if ((tecla == 8) && (tam > 1))
			{
				tam = tam - 1 ;
				objeto.value = vr.substr(0,tam);
				try{
					window.event.cancelBubble = true;
					window.event.returnValue = false;
				}catch(e)
				{
					event.cancelBubble = true;
					event.returnValue = false;
				}
			}
				if ( tam <= 2 && tecla != 8){
			 		objeto.value = vr ; }

				if ( (tam >= 2) && (tam <= 4) ){
			 		objeto.value = vr.substr(0, tam - 2) + ':' + vr.substr( tam - 2, 2 ); }

				if ((tam == (4)) && tecla != 8)
					{
						if (!(validaHora(objeto)))
							{
								if(tecla >=96 && tecla <=105){tecla = tecla - 48;}
								objeto.value = objeto.value + (String.fromCharCode(tecla));
								try{
									window.event.cancelBubble = true;
									window.event.returnValue = false;
								}catch(e)
								{
									event.cancelBubble = true;
									event.returnValue = false;
								}
								alert("Hora Inválida");
								objeto.value = "";
								objeto.focus();
							}
					}
	}
	else if((keyCode != 8) && (keyCode != 9) && (keyCode != 13) && (keyCode != 35) && (keyCode != 36) && (keyCode != 46))
	{
		try
		{
			window.event.returnValue = false;
		}
		catch(e)
		{
			event.returnValue = false;						
		}			
	}		
}

function verificaHora(objeto)
{
	var reTime = /^([0-1]\d|2[0-3]):[0-5]\d$/;
	
	if(trimValue(objeto.value) != "")
	{
		if (!reTime.test(objeto.value)) 
		{
			alert("Horário inválido.\n" + objeto.value);
			objeto.value = "";
			objeto.focus();
		}
	}
}

/**********************************************************************
Função de validação de campos do tipo Hora

Objetivos :
	- Aceitar somente datas do tipo : hh:mm

Parâmetros :
	objeto		-> Nome do campo de formulário (Usar this)

Exemplo :
	OnChange    validaHora(this);
/**********************************************************************/
function validaHora(objeto)
{
	var HoraString	= objeto.value;
	var Flag			= true;

	if(HoraString != "" &&((HoraString.indexOf(":")!= -1)))
	{
		var HoraArray	= HoraString.split(":");

		if(HoraArray[0] >= 0){Flag = true;}
	}
	else {Flag= false;}

	if(Flag)
	{
		//Valida a hora
		if (HoraArray.length != 2)
			Flag=false;
		else
			{
				if (HoraArray.length==2)
				{
					var hora = HoraArray[0], min = HoraArray[1];


					if(!((hora>=0 && hora <=23) && (min>=0 && min<=59)))
						Flag=false;
				}
				else
					Flag=false;
			}
	}
	return Flag;
}


/**********************************************************************
Função de formatação  numérica na digitação

Objetivos :


Parametros :
    Nenhum
    
Exemplo:
    onKeyPress="JavaScript:return mascaraNumero();"
/**********************************************************************/
function mascaraNumero(obj,event)
{
    var charCode = (navigator.appName == "Netscape") ? event.which : event.keyCode;

    if (charCode == 13)
    {
        return true;
    }
    var var_caracter = String.fromCharCode(charCode);


    if ((var_caracter>="0") && (var_caracter<="9"))
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**********************************************************************
Função para não permitir digitação
Objetivos :
Parametros :
    Nenhum
Exemplo:
    onKeyPress="JavaScript:return readonlyObj();"
/**********************************************************************/
function readonlyObj()
{
    var charCode = (navigator.appName == "Netscape") ? event.which : event.keyCode;
    if (charCode == 13)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**********************************************************************
Função de formatação  numérica na digitação

Objetivos :
	- Inclusão automática de .

Parametros :
    Nenhum

Exemplo:
    onKeyPress="JavaScript:return mascaraNumeroPonto(obj);"
/**********************************************************************/
function mascaraNumeroPonto(obj)
{
    var charCode = (navigator.appName == "Netscape") ? event.which : event.keyCode;

    if (charCode == 13)
    {
        return true;
    }
    var var_caracter = String.fromCharCode(charCode);


    if ((var_caracter>="0") && (var_caracter<="9"))
    {
        return true;
    }
    else
    {
        if ((var_caracter==".") && (obj.value.indexOf('.') == -1))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

/**************************************************************************
Função que exclui espaços em branco de string
Exemplo :
         document.formulario.campo.value=trimValue(document.formulario.campo.value);
**************************************************************************/
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

/**********************************************************************
Função de formatação CPF

Parâmetros :
	objeto		-> Nome do campo de formulário (Usar this)
	teclapress	-> Tecla pressionada (Usar event)

Exemplo :
    onKeyPress="JavaScript: return mascaraCPF(this,event);"
/**********************************************************************/
function mascaraCPF(Campo, teclapres)
{
	var tecla = teclapres.keyCode;
    var charCode = (navigator.appName == "Netscape") ? teclapres.which : teclapres.keyCode;
    var var_caracter = String.fromCharCode(charCode);
    if ((var_caracter>="0") && (var_caracter<="9"))
    {
       var vr = new String(Campo.value);
       if (vr.length > 13)
       {
       return false;
       }
	      vr = vr.replace(".", "");
	      vr = vr.replace(".", "");
	      vr = vr.replace("-", "");
          tam = vr.length + 1;
          if (tecla != 9 && tecla != 8)
          {
		     if (tam > 3 && tam < 7)
			    Campo.value = vr.substr(0, 3) + '.' + vr.substr(3, tam);
		     if (tam >= 7 && tam <10)
			    Campo.value = vr.substr(0,3) + '.' + vr.substr(3,3) + '.' + vr.substr(6,tam-6);
		     if (tam >= 10 && tam < 12)
			    Campo.value = vr.substr(0,3) + '.' + vr.substr(3,3) + '.' + vr.substr(6,3) + '-' + vr.substr(9,tam-9);
          }
    }
    else
    {
    return false;
    }
}
/**********************************************************************
Função de Validação de CPF

Parâmetros :
	objeto		-> Nome do campo de formulário

Retorno: true ou false

Exemplo :
    validaCPF(document.form.campo)
/**********************************************************************/
function validaCPF(pcpf)
{
objeto = pcpf
pcpf = pcpf.value

pcpf=pcpf.replace(".","");
pcpf=pcpf.replace(".","");
pcpf=pcpf.replace(".","");
pcpf=pcpf.replace("-","");
  if (pcpf.length != 11)
  {
  sim=false;
  }
  else
  {
  sim=true;
  }
  // valida o primeiro digito
 if (sim)
  {
/*      for (i=0;((i<=(pcpf.length-1))&& sim); i++)
      {
      val = pcpf.charAt(i);
          if ((val!="9")&&(val!="0")&&(val!="1")&&(val!="2")&&(val!="3")&&(val!="4") && (val!="5")&&(val!="6")&&(val!="7")&&(val!="8"))
          {
          sim=false;
          }
      }
*/

  var nulos = new Array();

  nulos[1]="12345678909";
  nulos[2]="11111111111";
  nulos[3]="22222222222";
  nulos[4]="33333333333";
  nulos[5]="44444444444";
  nulos[6]="55555555555";
  nulos[7]="66666666666";
  nulos[8]="77777777777";
  nulos[9]="88888888888";
  nulos[10]="99999999999";
  nulos[11]="00000000000";

      for (i=1; i<=11; i++)
      {

          if (pcpf==nulos[i])
          {
          sim=false;
          }
      }


      if (sim)
      {
      soma = 0;
          for (i=0;i<=8;i++)
          {
          val = eval(pcpf.charAt(i));
          soma = soma + (val*(i+1));
          }
      resto = soma % 11;
          if (resto>9)
          {
          dig = resto - 10;
          }
          else
          {
          dig = resto;
          }
          if (dig != eval(pcpf.charAt(9)))
          {
          sim=false;
          }
          else   // valida o segundo digito
          {
          soma = 0;
               for (i=0;i<=7;i++)
               {
               val = eval(pcpf.charAt(i+1));
               soma = soma + (val*(i+1))
               }
          soma = soma + (dig * 9);
          resto = soma % 11;
          if (resto>9)
          {
          dig = resto -10;
          }
          else
          {
          dig = resto;
          }
          if (dig != eval(pcpf.charAt(10)))
          {
          sim = false;
          }
          else
          {
          sim = true
          }
          }
      }
  }
  if (sim)
  {
  return true;
  }
  else
  {
  showAlert('aviso', '\t\t::: ERRO :::\n\nO CPF não é válido!\n___________________________________________________\nSIPAC - INCRA', '',200,75);
  objeto.focus();
  return false;
  }
}
/**********************************************************************
Função de formatação CNPJ

Parâmetros :
	objeto		-> Nome do campo de formulário (Usar this)
	teclapress	-> Tecla pressionada (Usar event)

Exemplo :
    onKeyPress="JavaScript: return mascaraCNPJ(this,event);"
/**********************************************************************/
function mascaraCNPJ(Campo, teclapres)
{
	var tecla = teclapres.keyCode;
    var charCode = (navigator.appName == "Netscape") ? teclapres.which : teclapres.keyCode;
    var var_caracter = String.fromCharCode(charCode);
    if ((var_caracter>="0") && (var_caracter<="9"))
    {
       var vr = new String(Campo.value);
       if (vr.length > 17)
       {
       return false;
       }
	      vr = vr.replace(".", "");
	      vr = vr.replace(".", "");
  	      vr = vr.replace("-", "");
          tam = vr.length + 1;
          if (tecla != 9 && tecla != 8)
          {
		     if (tam > 2 && tam < 6)
			    Campo.value = vr.substr(0, 2) + '.' + vr.substr(2, tam);

		     if (tam >= 6 && tam <9)
			    Campo.value = vr.substr(0,2) + '.' + vr.substr(2,3) + '.' + vr.substr(5,tam-5);

		     if (tam >= 9 && tam < 13)
			    Campo.value = vr.substr(0,2) + '.' + vr.substr(2,3) + '.' + vr.substr(5,3) + '/' + vr.substr(9,tam-9);
			    
		     if (tam >= 13 && tam <18)
			    Campo.value = vr.substr(0,2) + '.' + vr.substr(2,3) + '.' + vr.substr(5,3) + '/' + vr.substr(9,4)+ '-' + vr.substr(13,tam-13);
          }
    }
    else
    {
    return false;
    }
}
/**********************************************************************
Função de Validação de CNPJ

Parâmetros :
	objeto		-> Nome do campo de formulário

Retorno: true ou false

Exemplo :
    validaCNPJ(document.form.campo)
/**********************************************************************/
function validaCNPJ(CampoCNPJ)
{

go = false;

objeto = CampoCNPJ;


CampoCNPJ = CampoCNPJ.value;
CampoCNPJ=CampoCNPJ.replace(".","");
CampoCNPJ=CampoCNPJ.replace(".","");
CampoCNPJ=CampoCNPJ.replace("/","");
CampoCNPJ=CampoCNPJ.replace("-","");
 // verifica o tamanho
 if (CampoCNPJ.length != 14)
 {
 sim=false;
 showAlert('aviso','\t\t::: ERRO :::\n\nO CNPJ não é válido!\n___________________________________________________\nSIPAC - INCRA','', 200,75);
 objeto.focus();
 return false;
 }
 else
 {
 sim=true;
 }
 // verifica se e numero
 if (sim)
 {
     for (i=0;((i<=(CampoCNPJ.length-1))&& sim); i++)
     {
     val = CampoCNPJ.charAt(i);
     // alert ("Valor do Val: "+val)
          if ((val!="9")&&(val!="0")&&(val!="1")&&(val!="2")&&(val!="3")&&(val!="4") && (val!="5")&&(val!="6")&&(val!="7")&&(val!="8"))
          {
          sim=false;
          showAlert('aviso', '\t\t::: ERRO :::\n\nO CNPJ não é válido!\n___________________________________________________\nSIPAC - INCRA', '', 200,75);
          objeto.focus();
          return false;
          }
     }

     // se for numero continua
     if (sim)
     {
     m2 = 2;
     soma1 = 0;
     soma2 = 0;
          for (i=11;i>=0;i--)
          {
          val = eval(CampoCNPJ.charAt(i));
          // alert ("Valor do Val: "+val)
          m1 = m2;
              if (m2<9)
              {
              m2 = m2+1;
              }
              else
              {
              m2 = 2;
              }
          soma1 = soma1 + (val * m1);
          soma2 = soma2 + (val * m2);
          }
     soma1 = soma1 % 11;
          if (soma1 < 2)
          {
          d1 = 0;
          }
          else
          {
          d1 = 11- soma1;
          }
     soma2 = (soma2 + (2 * d1)) % 11;
          if (soma2 < 2)
          {
          d2 = 0;
          }
          else
          {
          d2 = 11- soma2;
          }
          // alert (d1)
          // alert (d2)
          if ((d1==CampoCNPJ.charAt(12)) && (d2==CampoCNPJ.charAt(13)))
          {
          //alert("Válido")
            go= true;
          }
          else
          {
            go = false;
          }
     }
 }
if (go == true)
{
   return true;
}
else
{
   showAlert('aviso', '\t\t::: ERRO :::\n\nO CNPJ não é válido!\n___________________________________________________\n', '', 200,75);
   objeto.focus();
   return false;

}
}
/**********************************************************************
Função de formatação CEP

Parâmetros :
	objeto		-> Nome do campo de formulário (Usar this)
	teclapress	-> Tecla pressionada (Usar event)

Exemplo :
    onKeyPress="JavaScript: return mascaraCEP(this,event);"
/**********************************************************************/
function mascaraCEP(Campo, teclapres)
{
	var tecla = teclapres.keyCode;
    var charCode = (navigator.appName == "Netscape") ? teclapres.which : teclapres.keyCode;
    var var_caracter = String.fromCharCode(charCode);
    if ((var_caracter>="0") && (var_caracter<="9"))
    {
       var vr = new String(Campo.value);
       if (vr.length > 8)
       {
       return false;
       }
	      vr = vr.replace(".", "");
	      vr = vr.replace(".", "");
  	      vr = vr.replace("-", "");
          tam = vr.length + 1;
          if (tecla != 9 && tecla != 8)
          {
		     if (tam > 5 && tam < 10)
			    Campo.value = vr.substr(0, 5) + '-' + vr.substr(5, tam);

          }
    }
    else
    {
    return false;
    }
}

/**********************************************************************
Função de Tamanho Maximo para um TextArea

Exemplo :
      onkeyup="textAreaMaxLength(this,10)" 
	  onkeypress="textAreaMaxLength(this,10)"
/**********************************************************************/
function textAreaMaxLength(txarea,total)
{
	tam = txarea.value.length;
	if (tam > total)
	{
		aux = txarea.value;
		txarea.value = aux.substring(0,total);
	}
}
/**********************************************************************
Função para validação de Ano
/**********************************************************************/
function validaAno(objeto)
{
	if (objeto.value)
	{
		if (objeto.value.length != 4)
		{
			alert("Ano invalido.")
			objeto.value = "";
			objeto.focus();
		}	
	}
}

/**********************************************************************
Função para validação de Mês / Ano

onblur="validaMesAno(this);"

/**********************************************************************/
function validaMesAno(objeto)
{
	ar_mes_ano = objeto.value.split("/");
	
	if(parseInt(ar_mes_ano[0]) > 12)
	{
		alert("Mês/Ano invalido.")
		objeto.value = "";
		objeto.focus();
	}
	
	if (ar_mes_ano[1] != "")
	{
		if (ar_mes_ano[1].length != 4)
		{
			alert("Mês/Ano invalido.")
			objeto.value = "";
			objeto.focus();
		}	
	}
}


/**********************************************************************
Função de formatação Telefone/Fax

Parâmetros :
	objeto		-> Nome do campo de formulário (Usar this)
	teclapress	-> Tecla pressionada (Usar event)

Exemplo :
    onKeyPress="JavaScript: return mascaraTelefone(this,event);"
/**********************************************************************/
function mascaraTelefone(Campo, e) {
	var key = '';
	var len = 0;
	var strCheck = '0123456789';
	var aux = '';
	var whichCode = (window.Event) ? e.which : e.keyCode;

	if (whichCode == 13 || whichCode == 8 || whichCode == 0)
	{
		return true;  // Enter backspace ou FN qualquer um que não seja alfa numerico
	}
	key = String.fromCharCode(whichCode);
	if (strCheck.indexOf(key) == -1){
		return false;  //NÃO E VALIDO
	}

	aux =  Telefone_Remove_Format(Campo.value);

	len = aux.length;
	if(len>=10)
	{
		return false;	//impede de digitar um telefone maior que 10
	}
	aux += key;

	Campo.value = Telefone_Mont_Format(aux);
	return false;
}


/**********************************************************************
Função de formatação de campos tipo texto durante a digitação

Objetivos :
	- Mudar tudo para minúsculo
	- Não aceitar acentos nem Ç
    - Especificar um tamanho maximo para o campo
    
Parâmetros :
	objeto		-> Nome do campo de formulário (Usar this)
    tammax      -> Tamanho maximo que o campo deve conter
	
Exemplo :
	OnKeyPress    mascaraEmail(this,10);
/**********************************************************************/
function mascaraEmail(objeto,event,tammax)
{
	var valor = objeto.value;
	var LetrasArray = new Array();

	LetrasArray[1] = new Array();
	LetrasArray[2] = new Array();

	//Primeira Coluna : Letras Acentuadas e Símbolos
	LetrasArray[1][1]='á';
	LetrasArray[1][2]='ã';
	LetrasArray[1][3]='à';
	LetrasArray[1][4]='â';
	LetrasArray[1][5]='ä';
	LetrasArray[1][6]='é';
	LetrasArray[1][7]='è';
	LetrasArray[1][8]='ê';
	LetrasArray[1][9]='ë';
	LetrasArray[1][10]='í';
	LetrasArray[1][11]='ì';
	LetrasArray[1][12]='î';
	LetrasArray[1][13]='ï';
	LetrasArray[1][14]='ó';
	LetrasArray[1][15]='õ';
	LetrasArray[1][16]='ò';
	LetrasArray[1][17]='ô';
	LetrasArray[1][18]='ö';
	LetrasArray[1][19]='ú';
	LetrasArray[1][20]='ù';
	LetrasArray[1][21]='û';
	LetrasArray[1][22]='ü';
	LetrasArray[1][23]='ç';
	LetrasArray[1][24]='Á';
	LetrasArray[1][25]='Ã';
	LetrasArray[1][26]='À';
	LetrasArray[1][27]='Â';
	LetrasArray[1][28]='Ä';
	LetrasArray[1][29]='É';
	LetrasArray[1][30]='È';
	LetrasArray[1][31]='Ê';
	LetrasArray[1][32]='Ë';
	LetrasArray[1][33]='Í';
	LetrasArray[1][34]='Ì';
	LetrasArray[1][35]='Î';
	LetrasArray[1][36]='Ï';
	LetrasArray[1][37]='Ó';
	LetrasArray[1][38]='Õ';
	LetrasArray[1][39]='Ò';
	LetrasArray[1][40]='Ô';
	LetrasArray[1][41]='Ö';
	LetrasArray[1][42]='Ú';
	LetrasArray[1][43]='Ù';
	LetrasArray[1][44]='Û';
	LetrasArray[1][45]='Ü';
	LetrasArray[1][46]='Ç';
	LetrasArray[1][47]='Ñ';
	LetrasArray[1][48]='ñ';
	LetrasArray[1][49]='~';
	LetrasArray[1][50]='^';
	LetrasArray[1][51]='´';
	LetrasArray[1][52]='`';

	//Segunda Coluna : Letras Equivalentes sem acentos
	LetrasArray[2][1]='a';
	LetrasArray[2][2]='a';
	LetrasArray[2][3]='a';
	LetrasArray[2][4]='a';
	LetrasArray[2][5]='ä';
	LetrasArray[2][6]='e';
	LetrasArray[2][7]='e';
	LetrasArray[2][8]='e';
	LetrasArray[2][9]='e';
	LetrasArray[2][10]='i';
	LetrasArray[2][11]='i';
	LetrasArray[2][12]='i';
	LetrasArray[2][13]='i';
	LetrasArray[2][14]='o';
	LetrasArray[2][15]='o';
	LetrasArray[2][16]='o';
	LetrasArray[2][17]='o';
	LetrasArray[2][18]='o';
	LetrasArray[2][19]='u';
	LetrasArray[2][20]='u';
	LetrasArray[2][21]='u';
	LetrasArray[2][22]='u';
	LetrasArray[2][23]='c';
	LetrasArray[2][24]='A';
	LetrasArray[2][25]='A';
	LetrasArray[2][26]='A';
	LetrasArray[2][27]='A';
	LetrasArray[2][28]='Ä';
	LetrasArray[2][29]='E';
	LetrasArray[2][30]='E';
	LetrasArray[2][31]='E';
	LetrasArray[2][32]='E';
	LetrasArray[2][33]='I';
	LetrasArray[2][34]='I';
	LetrasArray[2][35]='I';
	LetrasArray[2][36]='I';
	LetrasArray[2][37]='O';
	LetrasArray[2][38]='O';
	LetrasArray[2][39]='O';
	LetrasArray[2][40]='O';
	LetrasArray[2][41]='O';
	LetrasArray[2][42]='U';
	LetrasArray[2][43]='U';
	LetrasArray[2][44]='U';
	LetrasArray[2][45]='U';
	LetrasArray[2][46]='C';
	LetrasArray[2][47]='N';
	LetrasArray[2][48]='n';
	LetrasArray[2][49]='';
	LetrasArray[2][50]='';
	LetrasArray[2][51]='';
	LetrasArray[2][52]='';

	temp = "" + valor + (String.fromCharCode(window.event.keyCode));

	for (i=1; i<=52 ; i++ )
	{
		while (temp.indexOf((LetrasArray[1][i]))>-1)
			{
				pos= temp.indexOf((LetrasArray[1][i]));
				temp = "" + (temp.substring(0, pos) + (LetrasArray[2][i]) + temp.substring((pos + (LetrasArray[1][i].length)), temp.length));
			}
	}
	if((window.event.keyCode) != 13)
		{
			objeto.value = (temp.toLowerCase().substring(0,tammax));
			window.event.cancelBubble = true;
			window.event.returnValue = false;
		}

}



MaskInput = function(f, m){
/*
http://jsfromhell.com/forms/masked-input
Regras Padrões
     	a = A-Z e 0-9
     	A = A-Z, acentos e 0-9
     	9 = 0-9
     	C = A-Z e acentos
     	c = A-Z
     	* = qualquer coisa
Regras Especiais
     	E = (Except) exceção
     	O = (Only) somente
Criação de Máscaras
	Máscara simples:
	nesse tipo de máscara o usuário pode digitar no máximo a mesma
	quantidade de caracteres que a máscara contém.
	
Máscara especial "regra^exceções": 	esse tipo de máscara é composto por 2 partes, separadas por "^", 
o lado esquerdo especifica a regra e o direito as exceções para a regra selecionada.
9^abc = a regra é aceitar somente números "9" e a exceção são os caracteres a, b e c
c^123 = aceita somente caracteres de a-z e a exceção são os números 1, 2 e 3

Uso das regras especiais:
ela é semelhante a máscara especial, porém o lado esquerdo tem um significado diferente, podendo ser "E" (qualquer coisa, exceto...) ou "O" (somente...)
E^abc: aceita qualquer coisa, menos a, b e c
O^123: só permite os caracteres 1, 2 e 3
*/

	function mask(e){
		var patterns = {"1": /[A-Z]/i, "2": /[0-9]/, "4": /[\xC0-\xFF]/i, "8": /./ },
			rules = { "a": 3, "A": 7, "9": 2, "C":5, "c": 1, "*": 8};
		function accept(c, rule){
			for(var i = 1, r = rules[rule] || 0; i <= r; i<<=1)
				if(r & i && patterns[i].test(c))
					break;
				return i <= r || c == rule;
		}
		var k, mC, r, c = String.fromCharCode(k = e.key), l = f.value.length;
		(!k || k == 8 ? 1 : (r = /^(.)\^(.*)$/.exec(m)) && (r[0] = r[2].indexOf(c) + 1) + 1 ?
			r[1] == "O" ? r[0] : r[1] == "E" ? !r[0] : accept(c, r[1]) || r[0]
			: (l = (f.value += m.substr(l, (r = /[A|9|C|\*]/i.exec(m.substr(l))) ?
			r.index : l)).length) < m.length && accept(c, m.charAt(l))) || e.preventDefault();
	}
	for(var i in !/^(.)\^(.*)$/.test(m) && (f.maxLength = m.length), {keypress: 0, keyup: 1})
		addEvent(f, i, mask);
};

/*
**************************************
* Event Listener Function v1.4       *
* Autor: Carlos R. L. Rodrigues      *
**************************************
*/
addEvent = function(o, e, f, s){
    var r = o[r = "_" + (e = "on" + e)] = o[r] || (o[e] ? [[o[e], o]] : []), a, c, d;
    r[r.length] = [f, s || o], o[e] = function(e){
        try{
            (e = e || event).preventDefault || (e.preventDefault = function(){e.returnValue = false;});
            e.stopPropagation || (e.stopPropagation = function(){e.cancelBubble = true;});
            e.target || (e.target = e.srcElement || null);
            e.key = (e.which + 1 || e.keyCode + 1) - 1 || 0;
        }catch(f){}
        for(d = 1, f = r.length; f; r[--f] && (a = r[f][0], o = r[f][1], a.call ? c = a.call(o, e) : (o._ = a, c = o._(e), o._ = null), d &= c !== false));
        return e = null, !!d;
    }
};

removeEvent = function(o, e, f, s){
    for(var i = (e = o["_on" + e] || []).length; i;)
        if(e[--i] && e[i][0] == f && (s || o) == e[i][1])
            return delete e[i];
    return false;
};


/*
**************************************
* formatCurrency Function v1.3       *
* Autor: Carlos R. L. Rodrigues      *
**************************************
formatCurrency(field: HTMLInput, [floatPoint: Integer = 2], [decimalSep: String = ","], [thousandsSep: String = "."]): String
	Formata o input de forma que ele assuma o comportamento de um campo monetário.
    field
        campo que receberá a formatação
    floatPoint
        número de casas decimais
    decimalSep
        string representando o separador decimal
    thousandsSep
        string representando o separador de milhar

    formatCurrency(document.forms.form.a, 2);
    formatCurrency(document.forms.form.b, 3, " ", "-");
    formatCurrency(document.forms.form.c, 6);
*/
function formatCurrency(o, n, dig, dec){
    new function(c, dig, dec, m){
        addEvent(o, "keypress", function(e, _){
            if((_ = e.key == 45) || e.key > 47 && e.key < 58){
                var o = this, d = 0, n, s, h = o.value.charAt(0) == "-" ? "-" : "",
                    l = (s = (o.value.replace(/^(-?)0+/g, "$1") + String.fromCharCode(e.key)).replace(/\D/g, "")).length;
                m + 1 && (o.maxLength = m + (d = o.value.length - l + 1));
                if(m + 1 && l >= m && !_) return false;
                l <= (n = c) && (s = new Array(n - l + 2).join("0") + s);
                for(var i = (l = (s = s.split("")).length) - n; (i -= 3) > 0; s[i - 1] += dig);
                n && n < l && (s[l - ++n] += dec);
                _ ? h ? m + 1 && (o.maxLength = m + d) : s[0] = "-" + s[0] : s[0] = h + s[0];
                o.value = s.join("");
            }
            e.key > 30 && e.preventDefault();
        });
    }(!isNaN(n) ? Math.abs(n) : 2, typeof dig != "string" ? "." : dig, typeof dec != "string" ? "," : dec, o.maxLength);
}


//@ http://jsfromhell.com/number/fmt-money [v1.1]
//FORMATA PARA MONEY
/*
var n = 123456.789;
n = 123456.789
n.formatMoney() = 123.456,79
n.formatMoney(0) = 123.457
n.formatMoney(6) = 123.456,789000
n.formatMoney(2, "*", "#") = 123#456*79
	
Number.fmtMoney([floatPoint: Integer = 2], [decimalSep: String = ","], [thousandsSep: String = "."]): String
Retorna o número no formato monetário.
    floatPoint
        número de casas decimais
    decimalSep
        string que será usada como separador decimal
    thousandsSep
        string que será usada como separador de milhar
*/
Number.prototype.formatMoney = function(c, d, t){
    var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t,
    i = parseInt(n = (+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
    return (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t)
    + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

/*
**************************************
* String.mask Function v1.0          *
* Autor: Carlos R. L. Rodrigues      *
http://jsfromhell.com/pt/string/mask
**************************************
"12345678900".mask("###.###.###,##") = 123.456.789,00
"1234".mask("x:##, y: ##") = x:12, y: 34
"TEST".mask("\#-#*#/#^#") = #-T*E/S^T
*/
String.prototype.mask = function(m) {
    var m, l = (m = m.split("")).length, s = this.split(""), j = 0, h = "";
    for(var i = -1; ++i < l;)
        if(m[i] != "#"){
            if(m[i] == "\\" && (h += m[++i])) continue;
            h += m[i];
            i + 1 == l && (s[j - 1] += h, h = "");
        }
        else{
            if(!s[j] && !(h = "")) break;
            (s[j] = h + s[j++]) && (h = "");
        }
    return s.join("") + h;
};