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
		$("only_form").action = "documento_protocolo_partial_item_salvar.php";
		$("only_form").submit();
	}
}

function valido()
{
	var f = document.only_form;
	var iMarcado = 0;
	for( i=0; i<f.elements.length; i++ )
	{
		if( f.elements[i].name.toString().indexOf("marcar_check_")>-1 )
		{
			if( f.elements[i].checked ) iMarcado += 1;
			//$("painel").innerHTML += "<br />" + f.elements[i].name.toString() + " : " + f.elements[i].checked + "";
		}
		if( f.elements[i].name.toString().indexOf("devolver_check_")>-1 )
		{
			if( f.elements[i].checked ) iMarcado += 1;
			//$("painel").innerHTML += "<br />" + f.elements[i].name.toString() + " : " + f.elements[i].checked + "";
		}
	}
	return (iMarcado==$("total").innerHTML);
}

function salvar_e_confirmar()
{
	if( valido() )
	{
		if( confirm("Deseja salvar e confirmar o recebimento?") )
		{
			$("command").value = "confirmar";
			$("only_form").action = "documento_protocolo_partial_item_salvar.php";
			$("only_form").submit();
		}
	}
	else
	{
		alert("Atenção:\n\nAlgum ítem não está marcado.\nAntes de 'Salvar e Confirmar', todos os ítens devem estar marcados com 'Visto' ou 'Devolução'.\n\nPara salvar sem confirmar, clique no botão 'Salvar'.");
	}
}

function visto(o, v)
{
	if(o.checked)
	{
		if( $F("dt_indexacao")!="" )
		{
			$("dt_indexacao_"+v).value = $F("dt_indexacao");
			$("total_indexados").innerHTML = parseInt($("total_indexados").innerHTML)+1
		}
	}

	informar_indexados();
}

function desmarcar(v)
{
	$("visto_check_"+v).checked=false;
	$("devolver_check_"+v).checked=false;
	$("dt_indexacao_"+v).value="";
}

function devolver(o, v)
{
	$("dt_indexacao_"+v).value = "";
	if(o.checked)
	{
		if( $F("dt_indexacao")!="" )
		{
			if( $F("dt_indexacao_"+v)==$F("dt_indexacao") )
			{
				$("total_indexados").innerHTML = parseInt($("total_indexados").innerHTML)-1
			}
		}
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