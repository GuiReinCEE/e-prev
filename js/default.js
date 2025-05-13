function exportarpdf_fncdef()
{
	alert('exportar para PDF');
}

function imprimir_fncdef()
{
	window.print();
}

function change_page(page)
{
	page = ( page==undefined )?0:page;
	document.getElementById("current_page").value = page;
	load();
}

function box_click(box, content)
{
	$("#"+content).toggle();
}

function enter2tab()
{
	$("input, select").live("keydown", function(e) 
	{    
		var inputs = $("body").find("input:visible:enabled, select:visible:enabled, textarea:visible:enabled").not("[readonly]"); 
		var idx = inputs.index(this); 

		if (e.keyCode == 13) 
		{
			if(inputs[idx + 1])
			{
				inputs[idx + 1].focus();
				inputs[idx + 1].select();
			}
			return false;
			
			//if(!((inputs[idx].type == "button") || (inputs[idx].type == "submit") || (inputs[idx].type == "reset")))
			//{	
			//	return false;
			//}
		}
	});
}

 function removeAccents_table(s){
	var r = s.toLowerCase();
	non_asciis = {"a": "[àáâãäå]", "ae": "æ", "c": "ç", "e": "[èéêë]", "i": "[ìíîï]", "n": "ñ", "o": "[òóôõö]", "oe": "œ", "u": "[ùúûuü]", "y": "[ýÿ]"};
	for (i in non_asciis) 
	{ 
	    r = r.replace(new RegExp(non_asciis[i], "g"), i); 
	}
	return r;
};

function setCorFocus()
{
	$("input:visible:enabled, select:visible:enabled, textarea:visible:enabled").not("[readonly], input:checkbox, input:button, input:reset, input:submit").focus(function() {
		$(this).addClass('highlight');
	});

	$("input:visible:enabled, select:visible:enabled, textarea:visible:enabled").not("[readonly], input:checkbox, input:button, input:reset, input:submit").blur(function(){
		$(this).removeClass('highlight');
	});	

	$("input:checkbox, input:radio, input:button, input:reset, input:submit").focus(function() {
		$(this).removeClass('highlight');
		$(this).removeClass('highlight_readonly');
		$(this).addClass('highlight_borda');
	});

	$("input:checkbox, input:radio, input:button, input:reset, input:submit").blur(function(){
		$(this).removeClass('highlight');
		$(this).removeClass('highlight_borda');
		$(this).removeClass('highlight_readonly');
	});	

	$("input:[readonly]").addClass('highlight_readonly');
}

function handleEnter(field, event)
{
	/*
	//DESATIVADO PELA FUNÇÃO enter2tab()
	var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	if (keyCode == 13)
	{
		if(field.form)
		{
			var i;
			for (i = 0; i < field.form.elements.length; i++)
			if (field == field.form.elements[i])
				break;
			i = (i + 1) % field.form.elements.length;
			
			while(field.form.elements[i].type == 'hidden')
			{
				i = (i + 1) % field.form.elements.length;

				if(i == 100)
				{
				   break;
				}
			}

			field.form.elements[i].focus();
		}
		return false;
	}
	else
	{
		return true;
	}
	*/
}

/**
 * Função chamada no form_helper.php
 */
function excluir(link)
{
	if(confirm('Excluir?'))
	{
		location.href=link;
	}
	else
	{
		return false;
	}
}

function redir(question, url)
{
	if(question!="")
	{
		if(confirm(question))
		{
			location.href=url;
		}
		else
		{
			return false;
		}
	}
	else
	{
		location.href=url;
	}
}

function hora_valida(hora)
{
	if(hora.toString().length<5)
	{
		return false;
	}

	sp_hora = hora.toString().split(':');

	if( 
			(parseInt(sp_hora[0])<0 || parseInt(sp_hora[0])>23)  
		||  (parseInt(sp_hora[1])<0 || parseInt(sp_hora[1])>59)
	)
	{
		return false;
	}

	return true;
}

/**
 * Função de validação de campos do tipo Data
 * Aceitar somente datas do tipo : dd/mm/aaaa
 * 
 * @param	value	Nome do campo de formulário (Usar this)
 * @return 	boolean
 */ 
function data_valida(data)
{
	var DataString	= data;
	var DataArray	= DataString.split("/");  
	var Flag=true; 

	if (DataArray.length != 3)
	{ 
		Flag=false;
	} 
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
		{ 
			Flag=false;
		} 
	} 
	return Flag;
}

// TODO: IMPLEMENTAR FUNÇÃO
// usada originalmente nos indicadores, no IGP
function decimal_valido( numero )
{
	//alert(numero);
	return true;
}
