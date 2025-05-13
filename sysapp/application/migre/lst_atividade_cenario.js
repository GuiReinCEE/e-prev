var eu =
{
	filtrar : function()
	{
		new Ajax.Updater( 'result_div', 'lst_atividade_cenario_partial_lista.php', 
		{
			parameters: 
			{ 
				  data_inicial: $F('data_inicial')
				, data_final: $F('data_final')
				, nao_pertinente: ($('nao_pertinente').checked)?'S':'N'
				, pertinente_com_reflexo: ($('pertinente_com_reflexo').checked)?'S':'N'
				, pertinente_sem_reflexo: ($('pertinente_sem_reflexo').checked)?'S':'N'
				, nao_verificado: ($('nao_verificado').checked)?'S':'N'
			},
			onComplete:configure_result_table
		});
	}
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),["Number", "DateBR", "CaseInsensitiveString", "CaseInsensitiveString", "CaseInsensitiveString", "CaseInsensitiveString"]);
	ob_resul.onsort = function ()
	{
		var rows = ob_resul.tBody.rows;
		var l = rows.length;
		for (var i = 0; i < l; i++)
		{
			removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
			addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
		}
	};
	ob_resul.sort(0, false);
	
	var sep = '';
	$('filtros_span').innerHTML = '';
	
	if($('nao_pertinente').checked)
	{
		$('filtros_span').innerHTML += sep + 'Não pertinente';
		sep = ', ';
	}
	if($('pertinente_com_reflexo').checked)
	{
		$('filtros_span').innerHTML += sep + 'Pertinente com reflexo no processo';
		sep = ', ';
	}
	if($('pertinente_sem_reflexo').checked)
	{
		$('filtros_span').innerHTML += sep + 'Pertinente sem reflexo no processo';
		sep = ', ';
	}
	if($('nao_verificado').checked)
	{
		$('filtros_span').innerHTML += sep + 'Aguardando verificação';
		sep = ', ';
	}
	if($F('data_inicial')!='')
	{
		$('filtros_span').innerHTML += sep + 'De ' + $F('data_inicial');
		sep = ', ';
	}
	if($F('data_final')!='')
	{
		$('filtros_span').innerHTML += ' até ' + $F('data_final');
		sep = ', ';
	}
	
	if($('filtros_span').innerHTML=='') $('filtros_span').innerHTML='Nenhum filtro informado.';
	
}

function exibir_filtro()
{
	$('filter_bar').show();	
}

function handleEnter(field, event)
{
	var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	if (keyCode == 13)
	{
		var i;
		for (i = 0; i < field.form.elements.length; i++)
		if (field == field.form.elements[i])
			break;
		i = (i + 1) % field.form.elements.length;
		field.form.elements[i].focus();
		return false;
	}
	else
		return true;
}
