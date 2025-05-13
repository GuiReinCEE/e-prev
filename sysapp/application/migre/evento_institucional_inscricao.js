var eu =
{
	filtrar : function()
	{
		new Ajax.Updater( 'result_div', 'evento_institucional_inscricao_partial_lista.php', 
		{
			parameters: 
			{ 
				  data_inicial: $F('data_inicial')
				, data_final: $F('data_final')
				, cd_eventos_institucionais: $F('cd_eventos_institucionais')
				, desclassificado: $F("desclassificado")
				, selecionado: $F("selecionado")
				, com_foto: $F("com_foto")
			},
			onComplete: configure_result_table
		});
	},

	nova : function()
	{
		location.href = "evento_institucional_inscrito.php";
	},

	salvar : function()
	{
		if($F('nome')=='')
		{
			alert('Informe o nome');
			$('nome').focus();
			return false;
		}
		
		if( $F('email')!="" && ! checkMail($F('email')) )
		{
			alert('Informe um email válido');
			$('email').focus();
			return false;
		}
		
		if(confirm('Salvar?'))
		{
			document.only_form.action = "evento_institucional_inscrito_save.php";
			document.only_form.submit();
		}
	},
	
	cancelar_edicao : function()
	{
		if(confirm('Voltar para lista?'))
		{
			location.href='evento_institucional_inscricao.php';
		}
	},

	secao_select : function(v)
	{
		var LEGISLACAO_NA_INTEGRA = 5; 
		if(v==LEGISLACAO_NA_INTEGRA)
		{
			$('legislacao_integra_div').show();
			location.href = "#legint";
		}
		else
		{
			$('legislacao_integra_div').hide();
		}
	},

	consultar_participante : function()
	{
		new Ajax.Updater( 'result_div', 'evento_institucional_inscrito.php', 
			{
				method:'post',
				parameters: 
				{ 
					  cd_empresa: $F('cd_empresa')
					, cd_registro_empregado: $F('cd_registro_empregado')
					, seq_dependencia: $F('seq_dependencia')
					, comando: 'participante'
				},
				onComplete: function(response)
				{
					ret = response.responseText.toString().split('|');
					$("participante_msg").innerHTML = "";
					if( response.responseText!="" )
					{
						$("nome").value=ret[0];
						$("email").value=ret[1];
						$("telefone").value=ret[2];
						$("endereco").value=ret[3];
						$("cidade").value=ret[4];
						$("uf").value=ret[5];
						$("cep").value=ret[6];
						document.getElementById("empresa").value=ret[7];
						if(ret[8] != "")
						{
							document.getElementById("empresa").value+= " (" + ret[8] + ")";
						}
						
						document.getElementById("observacao").value += "\rDt Nascimento: " + ret[9] + " (" + ret[10] + " anos)";

						
					}
					else
					{
						$('participante_msg').innerHTML = ' Não é participante';
					}
					$('tipo').focus();
				}
			});
	},

	consultar_evento : function(v)
	{
		if(v!='')
		{
			new Ajax.Updater( 'result_div', 'evento_institucional_inscrito.php', 
			{
				method:'post',
				parameters: 
				{ 
					  cd_evento: v
					, comando: 'evento_consultar'
				},
				onComplete: function(response)
				{
					$('evento_info_div').innerHTML='';
					if( response.responseText!="" )
					{
						$('evento_info_div').innerHTML=response.responseText.toString();
					}
					else
					{
						$('evento_info_div').innerHTML='';
					}
				}
			});
		}
		else
		{
			$('evento_info_div').innerHTML='';
		}
	},

	excluir : function()
	{
		if(confirm('Excluir?'))
		{
			document.only_form.action = "evento_institucional_inscrito_excluir.php";
			document.only_form.submit();
		}
	}
}

function configure_result_table()
{
	configure_table();

	var sep = '';
	$('filtros_span').innerHTML = '';

	if($F('cd_eventos_institucionais')!="")
	{
		$('filtros_span').innerHTML += sep + 'Evento: ' + document.filtro_form.cd_eventos_institucionais.options[document.filtro_form.cd_eventos_institucionais.selectedIndex].text;
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
	if($F('desclassificado')=="S")
	{
		$('filtros_span').innerHTML += sep + ' apenas desclassificados ';
		sep = ', ';
	}
	if($F('desclassificado')=="N")
	{
		$('filtros_span').innerHTML += sep + ' apenas não desclassificados ';
		sep = ', ';
	}
	if($F('selecionado')=="S")
	{
		$('filtros_span').innerHTML += sep + ' apenas selecionados ';
		sep = ', ';
	}
	if($F('selecionado')=="N")
	{
		$('filtros_span').innerHTML += sep + ' apenas não selecionados ';
		sep = ', ';
	}
	if($F('com_foto')=="S")
	{
		$('filtros_span').innerHTML += sep + ' apenas com foto ';
		sep = ', ';
	}
	if($F('com_foto')=="N")
	{
		$('filtros_span').innerHTML += sep + ' apenas sem foto ';
		sep = ', ';
	}

	if($('filtros_span').innerHTML=='') $('filtros_span').innerHTML='Nenhum filtro informado.';
}

function configure_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		null,'Number','CaseInsensitiveString', 'RE','DateBR', 'CaseInsensitiveString', 'CaseInsensitiveString', 'CaseInsensitiveString'
	]);
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
}

function selecionar_participante(cd,fl)
{
	new Ajax.Updater( 'message_div', 'evento_institucional_inscrito_selecionar.php', 
	{
		parameters: 
		{ 
			  cd_eventos_institucionais_inscricao: cd
			, fl_selecionado: (fl)?"S":"N"
		},
		onComplete: function(resp)
		{
			// alert("Salvo com sucesso!");
		}
	});
}

function configure_result_publicacao()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),["CaseInsensitiveString", "DateTimeBR", "CaseInsensitiveString"]);
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
	ob_resul.sort(1, false);
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