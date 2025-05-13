var eu =
{
	filtrar : function()
	{
		new Ajax.Updater( 'result_div', 'mensagem_estacao_partial_lista.php', 
		{
			parameters: 
			{ 
				  data_inicial: $F('data_inicial')
				, data_final: $F('data_final')
			},
			onComplete: configure_result_table
		});
	},

	nova : function()
	{
		location.href = "mensagem_estacao_detalhe.php";
	},

	salvar : function()
	{
		if($F('nome')=='')
		{
			alert('Informe o nome.');
			$('nome').focus();
			return false;
		}
		if( $F('cd_mensagem_estacao')=='' && $F('arquivo')=='')
		{
			alert('Escolha um arquivo.');
			$('arquivo').focus();
			return false;
		}

		if( $F('dt_inicial')=="" )
		{
			alert('Informe uma data.');
			$('dt_inicial').focus();
			return false;
		}
		
		new Ajax.Updater( 'result_div', 'mensagem_estacao_detalhe.php', 
		{
			parameters: 
			{ 
				  dt_inicial: $F('dt_inicial')
				, cd_mensagem_estacao: $F('cd_mensagem_estacao')
				, comando: 'data_existe'
			},
			onComplete: function(t){
				if(t.responseText=="false")
				{
					if(confirm('Salvar?'))
					{
						document.only_form.action = "mensagem_estacao_detalhe_save.php";
						document.only_form.submit();
					}
				}
				else
				{
					alert('Já existe uma mensagem agendada para essa data. \n\nEscolha outra data para agendar a mensagem.');
					$('dt_inicial').focus();
				}
			}
		});
	},

	cancelar_edicao : function()
	{
		if(confirm('Voltar para lista?'))
		{
			location.href='mensagem_estacao.php';
		}
	},
	
	excluir : function()
	{
		if(confirm('Excluir?'))
		{
			document.only_form.action = "mensagem_estacao_detalhe_excluir.php";
			document.only_form.submit();
		}
	},
	
	verificar_data_existe : function(v)
	{
		if(v!="")
		{
			new Ajax.Updater( 'result_div', 'mensagem_estacao_detalhe.php', 
			{
				parameters: 
				{ 
					  dt_inicial: v
					, comando: 'data_existe'
				},
				onComplete: function(t){
					if(t.responseText=="true")
					{
						alert('Já existe uma mensagem agendada para essa data. \n\nEscolha outra data para agendar a mensagem.');
					}
				}
			});
		}
	}
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),["CaseInsensitiveString", null, "DateBR", "CaseInsensitiveString"]);
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
	ob_resul.sort(2, false);

	var sep = '';
	$('filtros_span').innerHTML = '';

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

function checkMail(mail)
{
    var er = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
    if(typeof(mail) == "string")
    {
        if(er.test(mail))
        { 
        	return true; 
        }
    }
    else if(typeof(mail) == "object")
    {
        if(er.test(mail.value))
        {
        	return true;
        }
    }
    else
    {
        return false;
    }
}