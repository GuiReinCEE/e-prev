function documento_protocolo()
{
	this.version = "1.0";
	this.autor = "cjunior";
	this.required = "minimo prototype 1.6.0";
}

documento_protocolo.prototype.input_Blur = 
function(o)
{
	if (o.value=="") 
	{
		Effect.Appear(o.id+"_message");
		o.className = "failed";
	} 
	else 
	{
		Effect.Fade(o.id+"_message");
		o.className = "passed";
	}
}

documento_protocolo.prototype.tipo_documento_Blur =
function(o)
{
	// DEIXOU DE SER OBRIGATÓRIO PARA ATENDER DEMANDA DA GB
	// if ( $("item_cd_tipo_doc_text").className=="failed_custom" && $("item_cd_tipo_doc_text").value!="" )
	// {
		// $("item_cd_tipo_doc_text").className = "normal";
		// Effect.Fade( "item_cd_tipo_doc_text_message" );
	// }
	$( "nome_documento_text" ).value = "";

	cd_tipo_doc = $( "item_cd_tipo_doc_text" ).value;
	if (cd_tipo_doc!="")
	{
		url = o.getAttribute("urlPartial");
		args = o.getAttribute("args");
		args += "&cd_tipo_doc="+cd_tipo_doc+"";
		var myAjax = new Ajax.Request
		(
			url+'?'+args+'',
			{
				method: 'get',
				onLoading: function()
				{
					// do nothing
				},
				onComplete: function (originalRequest)
				{
					$( o.getAttribute("loadContent") ).value = originalRequest.responseText;
				},
				onFailure: function (request)
				{
					$( "nome_documento_text" ).value = "";
				}
			}
		);
	}
	else
	{
		$( "nome_documento_text" ).value = "";
	}
}

documento_protocolo.prototype.confirmar_indexacao_Click =
function(o)
{
	if ( confirm("Atenção\n\nConfirmar a indexação dos itens desse protocolo de documentos?\n\n") )
	{
		url = "documento_protocolo_partial_lista.php";
		$("command").value = "confirmar_indexacao";
		$("cd_comando_text").value = o.getAttribute("registroId");
		$("only_form").action = url;
		$("only_form").request(
			{
				onLoading:function()
				{
					$('message_panel').innerHTML = "";
				},
				onComplete:function (originalRequest)
				{
					// Códigos de retorno customizados
					var ajaxReturn = originalRequest.responseText.split("|"); 
	
					// Código "1": Insert ou Update concluído com sucesso,
					// retorna a sequence gerada em caso de insert
					if (ajaxReturn[0]=="1") 
					{
						auxLoadLista();
					} 
					else 
					{
						$('message_panel').innerHTML = originalRequest.responseText;
					}
				},
				onFailure:function( request )
				{
					$('message_panel').innerHTML = 'Sorry. There was an error.';
				}
			}
		);
	}
}

documento_protocolo.prototype.enviar_Click =
function(o)
{
	if ( confirm("Atenção\n\nConfirmar o envio do protocolo de documentos?\n\n") ) {
		url = o.getAttribute("urlPartial");
		$("command").value = "";
		$("cd_comando_text").value = o.getAttribute("registroId");
		$("only_form").action = url;
		$("only_form").request(
			{
				onLoading:function (){
					$('message_panel').innerHTML = "";
				},
				onComplete:function (originalRequest)
				{
					// Códigos de retorno customizados
					var ajaxReturn = originalRequest.responseText.split("|"); 
	
					// Código "1": Insert ou Update concluído com sucesso,
					// retorna a sequence gerada em caso de insert
					if (ajaxReturn[0]=="1") 
					{
						if(o.id!="enviar_protocolo_interna_img")
						{
							auxLoadLista();
						}
						else
						{
							auxLoadDetails( $("cd_documento_protocolo_text").value, "editar" );
						}
					} 
					else 
					{
						$('message_panel').innerHTML = originalRequest.responseText;
					}
				},
				onFailure:function (request)
				{
					$('message_panel').innerHTML = 'Sorry. There was an error.';
				}
			}
		);
	}
}

documento_protocolo.prototype.receber_Click =
function(o)
{
	if ( confirm("Atenção\n\nConfirmar recebimento do protocolo de documentos?\n\n") ) {
		url = o.getAttribute("urlPartial");
		$("command").value = "";
		$("cd_comando_text").value = o.getAttribute("registroId");
		$("only_form").action = url;
		$("only_form").request(
			{
				onLoading:function (){
					$('message_panel').innerHTML = "";
				},
				onComplete:function (originalRequest)
				{
					// Códigos de retorno customizados
					var ajaxReturn = originalRequest.responseText.split("|");
					//alert( originalRequest.responseText ); 
	
					// Código "1": Insert ou Update concluído com sucesso,
					// retorna a sequence gerada em caso de insert
					if (ajaxReturn[0]=="1") 
					{
						auxLoadLista();
					} 
					else 
					{
						$('message_panel').innerHTML = originalRequest.responseText;
					}
				},
				onFailure:function (request)
				{
					$('message_panel').innerHTML = 'Sorry. There was an error.';
				}
			}
		);
	}
}

documento_protocolo.prototype.load_cancelar =
function(o)
{
	$('confirm_cancel').show();
	$("cd_comando_text").value = o.getAttribute("registroId");
	$("motivo_cancelamento_text").focus();
}

documento_protocolo.prototype.cancelar_Click =
function(o)
{
	if(confirm("Atenção:\n\nPara excluir o protocolo clique em OK.\n\n"))
	{
		$("command").value = "";
		$("cd_comando_text").value = o.getAttribute("registroId");
		url = o.getAttribute("urlPartial");

		$("only_form").action = url;
		$("only_form").request
		(
			{
				onLoading:function ()
				{
					$('message_panel').innerHTML = "";
				},
				onComplete:function (originalRequest)
				{
					// Códigos de retorno customizados
					var ajaxReturn = originalRequest.responseText.split( "|" ); 
	
					// Código "1": Insert ou Update concluído com sucesso,
					// retorna a sequence gerada em caso de insert
					if ( ajaxReturn[0]=="1" ) 
					{
						$("abaMovimento").className = "abaSelecionada";
						$("abaIncluir").className = "";
						auxCancelar_Click( $("abaMovimento") );
					} 
					else 
					{
						$('message_panel').innerHTML = originalRequest.responseText;
					}
				},
				onFailure:function(request)
				{
					$('message_panel').innerHTML = 'Sorry. There was an error.';
				}
			}
		);
	}
}

documento_protocolo.prototype.loadPartial_AJAX = 
function( o, url, args )
{
	var myAjax = new Ajax.Request
	(
		url+'?'+args+'',
		{
			method: 'get',
			onLoading: function()
			{
				// do nothing
			},
			onComplete: function (originalRequest)
			{
				// put returned HTML partial in the innerHTML
				o.innerHTML = originalRequest.responseText;
			},
			onFailure: function (request)
			{
				// do nothing
			}
		}
	);
}

documento_protocolo.prototype.initialize = 
function()
{
	this.loadPartial_AJAX( $("div_content"), $("div_content").getAttribute("urlInitial"), $("div_content").getAttribute("args") );
}

documento_protocolo.prototype.filtrar_Click =
function(o)
{
	$("filtrar_hidden").value = "true";
	this.loadLista(o)
}

documento_protocolo.prototype.loadLista =
function(o)
{
	$("command").value = "";
	$("only_form").action = o.getAttribute("urlPartial");
	$("only_form").request(
		{
			onLoading:function (){
				$('message_panel').innerHTML = "";
			},
			onComplete:function (originalRequest)
			{
				$( o.getAttribute("contentPartial") ).innerHTML = originalRequest.responseText;
				$('message_panel').innerHTML = ""; 
			},
			onFailure:function (request)
			{
				alert('Sorry. There was an error.');
			}
		}
	);
}

documento_protocolo.prototype.loadInserir =
function(o)
{
	this.loadPartial_AJAX( $(o.getAttribute("contentPartial")), o.getAttribute("urlPartial"), o.getAttribute("args") );
}

documento_protocolo.prototype.loadReport =
function(o)
{
	var url = "documento_protocolo_partial_relatorio.php";
	new Ajax.Updater( 'div_content', url, { onComplete:configureReport } );
}

documento_protocolo.prototype.save =
function(o)
{
	valid = true;

	/*if( $("nome_participante_text").value=="" )
	{
		$("item_cd_empresa_text").className = "failed";
		$("item_cd_registro_empregado_text").className = "failed";
		$("item_seq_dependencia_text").className = "failed";
		$("participante_message").innerHTML = "Informe Empresa, RE e Sequencial corretamente";
		$("item_cd_empresa_text").focus();
		valid = false;
	}*/
	if ( $("nome_documento_text").value=="" )
	{
		$("item_cd_tipo_doc_text").className = "failed";
		$("item_cd_tipo_doc_text_message").innerHTML = "Informe o código de um tipo de documento válido";
		$("item_cd_tipo_doc_text").focus();
		valid = false;
	}

	if(valid)
	{
		var r = this.validateForm( $("only_form") );
	
		if (r)
		{
			$("command").value = "";
			$("only_form").action = o.getAttribute("urlPartial");
			$("only_form").request(
				{
					onLoading:function (){
						$('message_panel').innerHTML = "Aguarde ...";
					},
					onComplete:function (originalRequest)
					{
						// Códigos de retorno customizados
						var ajaxReturn = originalRequest.responseText.split("|"); 
	
						// Código "1": Insert ou Update concluído com sucesso,
						// retorna a sequence gerada em caso de insert
						if (ajaxReturn[0]=="1") 
						{
							$('cd_atendimento_protocolo_text').value = ajaxReturn[1];
							$('message_panel').innerHTML = "Correspondência atualizada com sucesso"; 
						} 
						else 
						{
							$('message_panel').innerHTML = originalRequest.responseText;
						}
					},
					onFailure:function (request)
					{
						$('message_panel').innerHTML = "Sorry. There was an error.";
					}
				}
			);
		}
	}
}

documento_protocolo.prototype.showHide_Click =
function(o)
{
	if ($('tr_filtro_form').style.display=="none")
	{
		$('tr_filtro_form').show();
	}
	else
	{
		$('tr_filtro_form').hide();
	}
}

documento_protocolo.prototype.change_aba =
function(id_object)
{
	$("abaMovimento").className = "";
	$("abaIncluir").className = "";
	$("abaRelatorio").className = "";

	$(id_object).className = "abaSelecionada";
}

documento_protocolo.prototype.abaIncluir_Click =
function(o)
{
	this.change_aba("abaIncluir");
	
	this.loadInserir(o);
	
	return true;
}

documento_protocolo.prototype.abaMovimento_Click =
function(o)
{
	this.change_aba("abaMovimento");

	this.loadLista(o);

	return true;
}

documento_protocolo.prototype.abaRelatorio_Click =
function(o)
{
	this.change_aba(o.id)

	this.loadReport(o);

	return true;
}

documento_protocolo.prototype.validateForm = 
function(f)
{
	var result = true;
	var sFirstObjectFailed = "";
	for(var index=0; index<f.elements.length; index++) 
	{
		if (f.elements[index].className=="required") 
		{
			if(f.elements[index].value == "")
			{
				Effect.Appear(f.elements[index].id+"_message");
				f.elements[index].className = "failed";
				if (sFirstObjectFailed=="") 
				{
					sFirstObjectFailed = f.elements[index].id;
				}

				result = false;
			}
		}
		else if(f.elements[index].className=="failed")
		{
			if(f.elements[index].value != "")
			{
				Effect.Fade(f.elements[index].id+"_message");
				f.elements[index].className = "passed";
			}
			else
			{
				if (sFirstObjectFailed=="") 
				{
					sFirstObjectFailed = f.elements[index].id;
				}
				result = false;
			}
			
		}
	}
	if (sFirstObjectFailed!="")
	{
		$(sFirstObjectFailed).focus();
	}
	return result;
}

documento_protocolo.prototype.reComplete_Blur = 
function( o )
{
	emp = $( o.getAttribute("emp") ).value;
	re = $( o.getAttribute("re") ).value;
	seq = $( o.getAttribute("seq") ).value;
	if (emp!="" && re!="" && seq!="") {
		url = o.getAttribute("urlPartial");
		args = o.getAttribute("args");
		args += "&emp="+emp+"&re="+re+"&seq="+seq+"";
		var myAjax = new Ajax.Request
		(
			url+'?'+args+'',
			{
				method: 'get',
				onLoading: function()
				{
					$( o.getAttribute("loadContent") ).value = "";
				},
				onComplete: function (originalRequest)
				{
					$( o.getAttribute("loadContent") ).value = originalRequest.responseText;
				},
				onFailure: function (request)
				{
					$( o.getAttribute("loadContent") ).value = "Sorry. There was an error.";
				}
			}
		);
	}
	else
	{
		$( o.getAttribute("loadContent") ).value = '';
	}
}

documento_protocolo.prototype.details_Click = 
function( o )
{
	$("abaMovimento").className = "";
	$("abaIncluir").className = "abaSelecionada";

	url = o.getAttribute("urlPartial");
	args = "id=" + o.getAttribute("registroId") + "&command=" + o.getAttribute("command");
	var myAjax = new Ajax.Request
	(
		url+'?'+args+'',
		{
			method: 'get',
			onLoading: function()
			{
				$( "message_panel" ).innerHTML = "";
			},
			onComplete: function (originalRequest)
			{
				$( "div_content" ).innerHTML = originalRequest.responseText;
			},
			onFailure: function (request)
			{
				$( "message_panel" ).innerHTML = "Sorry. There was an error.";
			}
		}
	);
}

documento_protocolo.prototype.load_details =
function( cd_documento_protocolo, command )
{
	$("abaMovimento").className = "";
	$("abaIncluir").className = "abaSelecionada";

	url = "documento_protocolo_partial_form.php";
	args = "id=" + cd_documento_protocolo 
		+ "&command=" + command 
		+ "&cd_tipo_doc=" + $("item_cd_tipo_doc_text").value 
		+ "&nome_documento=" + $("nome_documento_text").value 
		+ "&emp=" + $("item_cd_empresa_text").value + ""
		+ "&re=" + $("item_cd_registro_empregado_text").value + ""
		+ "&seq=" + $("item_seq_dependencia_text").value + ""
		+ "&nome_participante=" + $("nome_participante_text").value + ""
		;

	var myAjax = new Ajax.Request
	(
		url+'?'+args+'',
		{
			method: 'get',
			onLoading: function()
			{
				$( "message_panel" ).innerHTML = "";
			},
			onComplete: function (originalRequest)
			{
				$( "div_content" ).innerHTML = originalRequest.responseText;
				// $("item_cd_tipo_doc_text").focus();
				
			},
			onFailure: function (request)
			{
				$( "message_panel" ).innerHTML = "Sorry. There was an error.";
			}
		}
	);
}

documento_protocolo.prototype.load_list_only =
function()
{
		$("command").value = "load_list_only";
		$("cd_comando_text").value = $F('cd_documento_protocolo_text');
		$("only_form").action = 'documento_protocolo_partial_form.php';
		$("only_form").request(
			{
				onLoading:function (){
					$('message_panel').innerHTML = "";
				},
				onComplete:function (originalRequest)
				{
					$('lista_documentos_incluidos_div').innerHTML = originalRequest.responseText; 
				},
				onFailure:function (request)
				{
					$('message_panel').innerHTML = 'Sorry. There was an error.';
				}
			}
		);
}

documento_protocolo.prototype.excluir_item_Click =
function(o)
{
	if (confirm("Atenção\n\nExcluir registro?\n\n"))
	{
		$("command").value = "";
		url = "documento_protocolo_partial_form_item_excluir.php";
		$("cd_documento_protocolo_item_selected").value = o.getAttribute("registroId");
	
		$("only_form").action = url;
		$("only_form").request
		(
			{
				onLoading:function (){
					$('message_panel').innerHTML = "";
				},
				onComplete:function (originalRequest)
				{
					// Códigos de retorno customizados
					var ajaxReturn = originalRequest.responseText.split("|"); 
	
					// Código "1": Insert ou Update concluído com sucesso,
					// retorna a sequence gerada em caso de insert
					if (ajaxReturn[0]=="1") 
					{
						// alert( "0" );
						auxLoadDetails( $("cd_documento_protocolo_text").value, "editar" );
						
					} 
					else 
					{
						$('message_panel').innerHTML = originalRequest.responseText;
					}
				},
				onFailure:function(request)
				{
					$('message_panel').innerHTML = 'Sorry. There was an error.';
				}
			}
		);
	}

}

documento_protocolo.prototype.insert_protocolo =
function(o, insert_only)
{
	$("command").action = "insert_protocolo";
	$("only_form").action = "documento_protocolo_partial_form_save.php";
	$("only_form").request(
		{
			onLoading:function (){
				$('message_panel').innerHTML = "";
			},
			onComplete:function (originalRequest)
			{
				// Códigos de retorno customizados
				var ajaxReturn = originalRequest.responseText.split("|"); 

				// Código "1": Insert ou Update concluído com sucesso,
				// retorna a sequence gerada em caso de insert
				if (ajaxReturn[0]=="1") 
				{
					$('cd_documento_protocolo_text').value = ajaxReturn[1];
					$('ano_text').value = ajaxReturn[2];
					$('contador_text').value = ajaxReturn[3];
					//$('message_panel').innerHTML = "Protocolo de documentos adicionado com sucesso"; 
					
					if( !insert_only ){
					
						auxAddItem(o);
					
					}
					else
					{
						auxLoadDetails( $("cd_documento_protocolo_text").value, "editar" );
					}
				} 
				else 
				{
					$('message_panel').innerHTML = originalRequest.responseText;
				}
			},
			onFailure:function (request)
			{
				$('message_panel').innerHTML = "Sorry. There was an error.";
			}
		}
	);
}

documento_protocolo.prototype.add_item =
function(o)
{
	$("command").action = "add_item";
	$("only_form").action = o.getAttribute("urlPartial");
	$("only_form").request(
		{
			onLoading:function ()
			{
				$('message_panel').innerHTML = "";
			},
			onComplete:function (originalRequest)
			{
				// Códigos de retorno customizados
				var ajaxReturn = originalRequest.responseText.split("|"); 

				// Código "1": Insert ou Update concluído com sucesso,
				// retorna a sequence gerada em caso de insert
				if (ajaxReturn[0]=="1") 
				{
					$('message_panel').innerHTML = "";
					auxLoadListOnly(); 
				} 
				else 
				{
					//$('message_panel').innerHTML = originalRequest.responseText;
				}
			},
			onFailure:function (request)
			{
				$('message_panel').innerHTML = "Sorry. There was an error.";
			}
		}
	);
}

documento_protocolo.prototype.adicionar_item_Click =
function(o)
{
	valid = true;
	
	/*if( $("nome_participante_text").value=="" )
	{
		$("item_cd_empresa_text").className = "failed_custom";
		$("item_cd_registro_empregado_text").className = "failed_custom";
		$("item_seq_dependencia_text").className = "failed_custom";
		$("item_cd_empresa_text_message").innerHTML = "Informe Empresa, RE e Sequencial corretamente";
		Effect.Appear("item_cd_empresa_text_message");
		$("item_cd_empresa_text").focus();
		valid = false;
	}
	else
	{
		$("item_cd_empresa_text").className = "normal";
		$("item_cd_registro_empregado_text").className = "normal";
		$("item_seq_dependencia_text").className = "normal";
		Effect.Fade("item_cd_empresa_text_message");
	}*/
	
	if( $F('item_nr_folha')=='0' || $F('item_nr_folha')=='' )
	{
		alert("Informe o número de folhas.");
		$('item_nr_folha').focus();
		valid = false;
	}

	if(valid)
	{
		var valid = this.validateForm( $("only_form") );
		if ( valid )
		{
			if( $('item_ds_processo').value=="" && $('nome_participante_text').value=="" )
			{
				alert("Informe o Participante ou o número do processo antes de adicionar!");
				return false;
			}
			else
			{
				if ( $("cd_documento_protocolo_text").value=="" ) 
				{
					// Insert documento_protocolo and documento_protocolo_item
					this.insert_protocolo(o, false);
				} 
				else 
				{
					// Insert documento_protocolo_item only
					this.add_item(o);
					if( $('tipo_doc_radio').checked )
					{
						// -------------------------------------------
						$('nome_participante_text').value='';
						$('item_seq_dependencia_text').value='';
						$('item_cd_registro_empregado_text').value='';
						$('item_cd_empresa_text').value='';
						// -------------------------------------------
						$('item_ds_processo').value='';
						// -------------------------------------------
						$('item_cd_empresa_text').focus();
						// -------------------------------------------
					}
					else if( $('re_radio').checked )
					{
						// -------------------------------------------
						$('nome_documento_text').value='';
						$('item_cd_tipo_doc_text').value='';
						// -------------------------------------------
						$('item_ds_processo').value='';
						// -------------------------------------------
						$('item_cd_tipo_doc_text').focus();
						// -------------------------------------------
					}
					else if( $('processo_radio').checked )
					{
						// -------------------------------------------
						$('nome_participante_text').value='';
						$('item_seq_dependencia_text').value='';
						$('item_cd_registro_empregado_text').value='';
						$('item_cd_empresa_text').value='';
						// -------------------------------------------
						$('nome_documento_text').value='';
						$('item_cd_tipo_doc_text').value='';
						// -------------------------------------------
						$("item_cd_empresa_text").focus();
						// -------------------------------------------
					}
					$('item_observacao').value = '';
					$('item_nr_folha').value = '1';
				}
			}
		}
	}

}

documento_protocolo.prototype.td_show_hide_Click =
function( id_show_hide )
{
	//$(id_show_hide).show( );
	$(id_show_hide).toggle( id_show_hide, 'BLIND' );
}

documento_protocolo.prototype.exibe_documentos =
function(cd)
{
	Effect.Appear("tr_row_documentos_" + cd + "");
	
	$("ob_max_" + cd + "").hide();
	$("ob_min_" + cd + "").show();
	$("tr_row_protocolo_" + cd + "").className = "exibeRevisao";
	
}

documento_protocolo.prototype.oculta_documentos =
function(cd)
{
	Effect.Fade("tr_row_documentos_" + cd + "");
	
	$("ob_max_" + cd + "").show();
	$("ob_min_" + cd + "").hide();
	$("tr_row_protocolo_" + cd + "").className = "";
	
}		

documento_protocolo.prototype.novo_protocolo_Click = 
function(o)
{
	this.insert_protocolo(o, true);
	
	/*$('novo_protocolo_div').hide();
	Effect.Appear('documentos_table');*/
}

documento_protocolo.prototype.select_documento =
function(v1, v2)
{
	$("item_cd_tipo_doc_text").value = v1;
	$("nome_documento_text").value = v2;
	Effect.Fade( "lista_documentos_div" );
}

documento_protocolo.prototype.consultar_documentos_Click =
function(o)
{
	if (o.getAttribute("extra")=="show_panel")
	{
		Effect.Appear('lista_documentos_div');
	}
	$("command").value = "load_filtro_documentos";
	$("only_form").action = "documento_protocolo_partial_form.php";
	$("only_form").request(
		{
			onLoading:function ()
			{
				// Do nothing
			},
			onComplete:function (originalRequest)
			{
				// Códigos de retorno customizados
				var ajaxReturn = originalRequest.responseText; 
				$('lista_documentos_grid_div').innerHTML = ajaxReturn;
			},
			onFailure:function (request)
			{
				$('message_panel').innerHTML = "Sorry. There was an error.";
			}
		}
	);
}

documento_protocolo.prototype.ordem_itens_Change =
function(o)
{
	$("cd_comando_text").value = $("cd_documento_protocolo_text").value;
	$("command").value = "update_ordem_itens";
	$("only_form").action = "documento_protocolo_partial_form.php";
	$("only_form").request(
		{
			onLoading:function ()
			{
				// Do nothing
			},
			onComplete:function (originalRequest)
			{
				// Códigos de retorno customizados
				var ajaxReturn = originalRequest.responseText; 
				//$("message_panel").innerHTML = ajaxReturn;
				//$("lista_documentos_grid_div").innerHTML = ajaxReturn;
				auxLoadDetails( $("cd_documento_protocolo_text").value, "editar" );
			},
			onFailure:function (request)
			{
				$('message_panel').innerHTML = "Sorry. There was an error.";
			}
		}
	);
}

documento_protocolo.prototype.handleEnter =
function (field, event)
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

documento_protocolo.prototype.test_enter =
function (field, event)
{
	var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	if (keyCode == 13)
	{
		if(field.id == 'item_ds_processo')
		{
			$('item_observacao').focus();
		}
		else if (field.id == 'item_observacao')
		{
			$('item_nr_folha').focus();
		}
		else if (field.id == 'item_cd_tipo_doc_text')
		{
			if( $('re_radio').checked )
			{
				if($F('item_cd_empresa_text')=='')
				{
					$('item_cd_empresa_text').focus();
				}
				else
				{
					$('item_ds_processo').focus();
				}
			}
			else
			{
				$('item_cd_empresa_text').focus();
			}
		}
		else if (field.id == 'item_seq_dependencia_text')
		{
			if( $('tipo_doc_radio').checked )
			{
				if($F('item_cd_tipo_doc_text')=='')
				{
					$('item_cd_tipo_doc_text').focus();
				}
				else
				{
					$('item_ds_processo').focus();
				}
			}
			else
			{
				$('item_observacao').focus();
			}
		}
		else if (field.id == 'item_nr_folha')
		{
			if(confirm('Adicionar?'))
			{
				this.adicionar_item_Click( $('adicionar_item_button') );
			}
		}
	}
	else
	{
		return true;
	}
}

documento_protocolo.prototype.relatorio_filtrar = 
function()
{
	var url = "documento_protocolo_partial_relatorio.php";
	new Ajax.Updater( 'relatorio', url, 
	{
		onLoading: function()
		{
			$('relatorio').innerHTML = 'carregando ...';
		},
		parameters: 
		{ 
		  	comando: 'relatorio_listar'
		  	, filtrar: 'true'
		  	, ano: $F('ano')
		  	, contador: $F('contador')
		  	, cd_empresa: $F('cd_empresa')
		  	, cd_registro_empregado: $F('cd_registro_empregado')
		  	, seq_dependencia: $F('seq_dependencia')
		  	, cd_tipo_doc: $F('cd_tipo_doc')
		  	, ds_processo: $F('ds_processo')
		  	, dt_envio_inicio: $F('dt_envio_inicio')
		  	, dt_envio_fim: $F('dt_envio_fim')
		  	, dt_indexacao_inicio: $F('dt_indexacao_inicio')
		  	, dt_indexacao_fim: $F('dt_indexacao_fim')
		  	, dt_ok_inicio: $F('dt_ok_inicio')
		  	, dt_ok_fim: $F('dt_ok_fim')
		  	, apenas_devolvidos: ( $('apenas_devolvidos').checked )?"S":""
		},
		onComplete:configureReport
	} );
}


//////////////////////////////////////////////////////////

thisPage = new documento_protocolo();

function auxLoadLista()
{
	if($("filtrar_hidden").value=="true")
	{
		thisPage.filtrar_Click( $("filtrar_button") );
	}
	else
	{
		thisPage.initialize();
	}
}
function auxLoadDetails( cd_documento_protocolo, command )
{
	thisPage.load_details( cd_documento_protocolo, command );
}
function auxLoadListOnly()
{
	thisPage.load_list_only();
}
function auxAddItem( o )
{
	thisPage.add_item(o);
}
function auxCancelar_Click( o )
{
	thisPage.loadLista( o );
}

function configureReport()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		  "CaseInsensitiveString"
		, "DateBR"
		, "DateBR"
		, "DateBR"
		, "DateBR"
		, "CaseInsensitiveString"
		, "Number"
		, "Number"
		, "Number"
		, "CaseInsensitiveString"
		, "Number"
		, "CaseInsensitiveString"
		, "CaseInsensitiveString"
		, "Number"
		]);
	ob_resul.onsort = function ()
	{
		var rows = ob_resul.tBody.rows;
		var l = rows.length;
		for (var i = 0; i < l; i++)
		{
			removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
			addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
		}
	};
	ob_resul.sort(0, false);
	
	// Configurar filtros
	MaskInput( document.getElementById('ano'), "9999" );
	MaskInput( document.getElementById('contador'), "999999" );
	MaskInput( document.getElementById('cd_empresa'), "999999" );
	MaskInput( document.getElementById('cd_registro_empregado'), "999999" );
	MaskInput( document.getElementById('seq_dependencia'), "999999" );
	MaskInput( document.getElementById('dt_envio_inicio'), "99/99/9999" );
	MaskInput( document.getElementById('dt_envio_fim'), "99/99/9999" );
	MaskInput( document.getElementById('dt_indexacao_inicio'), "99/99/9999" );
	MaskInput( document.getElementById('dt_indexacao_fim'), "99/99/9999" );
	MaskInput( document.getElementById('dt_ok_inicio'), "99/99/9999" );
	MaskInput( document.getElementById('dt_ok_fim'), "99/99/9999" );
}