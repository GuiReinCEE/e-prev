function init()
{
	configure_table();
}

function configure_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"), [null,null,null,null,null,null] );
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
}

function salvar()
{
	if( confirm("Deseja salvar?") )
	{
		$("command").value = "";
		$("only_form").action = "documento_protocolo_partial_indexar_salvar.php";
		$("only_form").submit();
	}
}

function valido()
{
	var f = document.only_form;
	var iMarcado = 0;
	for( i=0; i<f.elements.length; i++ )
	{
		if( f.elements[i].name.toString().indexOf("dt_indexacao_")>-1 )
		{
			if( f.elements[i].value=="" ) return false;
		}
	}
	return true;
}

function salvar_e_confirmar()
{
	if( valido() )
	{
		if( confirm("Deseja salvar e confirmar o recebimento?") )
		{
			$("command").value = "confirmar";
			$("only_form").action = "documento_protocolo_partial_indexar_salvar.php";
			$("only_form").submit();
		}
	}
	else
	{
		alert("Atenção:\n\nAlgum ítem não está marcado.\nAntes de 'Salvar e Confirmar', todos os ítens devem estar marcados com 'Visto' ou 'Devolução'.\n\nPara salvar sem confirmar, clique no botão 'Salvar'.");
	}
}

function marcar(o,v)
{
	if(o.checked)
	{
		if( $F("dt_indexacao")=="" )
		{
			alert( "Informe a data de indexação." );
			$("dt_indexacao").focus();
			o.checked = false;
			return false;
		}
		else
		{
			$("dt_indexacao_"+v).value = $F('dt_indexacao');
			$("total_indexados").innerHTML = parseInt($("total_indexados").innerHTML)+1
		}
	}
	else
	{
		if( $F("dt_indexacao_"+v)==$F("dt_indexacao") )
		{
			$("total_indexados").innerHTML = parseInt($("total_indexados").innerHTML)-1
		}
		$("dt_indexacao_"+v).value = "";
	}
	informar_indexados();
}

function informar_indexados()
{
	if($F("dt_indexacao")!="")
	{
		document.title = $F("dt_indexacao") + " - " + $("total_indexados").innerHTML;
	}
	else
	{
		document.title = "Lista de Itens protocolados para digitalização";
	}
}

function carregar_total_indexados_na_data()
{
	if( $F("dt_indexacao")=="" )
	{
		$("total_indexados").innerHTML="0";
	}
	else
	{
		var url = "documento_protocolo_partial_item.php";
		new Ajax.Updater( 'total_indexados', url, 
		{ 
			onLoading:function()
			{ 
				$("total_indexados").innerHTML="carregando...";
				$("lista").hide(); 
			}
			, parameters: 
			{ 
				command: 'atualizar_total_indexacao'
				, dt_indexacao: $F("dt_indexacao") 
			}
			, onComplete:function()
			{
				$("lista").show();
			} 
		} 
		);
	}
}

function limpar_data()
{
	$('dt_indexacao').value=''
	$("total_indexados").innerHTML="0";
	informar_indexados();
}