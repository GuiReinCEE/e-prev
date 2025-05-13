var bRedirecionar=false;

function documento_recebido()
{
	this.version = "1.0";
	this.autor = "cjunior";
	this.required = "minimo prototype 1.6.0";
}

documento_recebido.prototype.input_Blur = 
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

documento_recebido.prototype.tipo_documento_Blur =
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

documento_recebido.prototype.confirmar_indexacao_Click =
function(o)
{
	if ( confirm("Atenção\n\nConfirmar a indexação dos itens desse protocolo de documentos?\n\n") )
	{
		url = "documento_recebido_partial_lista.php";
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

documento_recebido.prototype.enviar_Click =
function(o)
{
	if ( confirm("Atenção\n\nConfirmar o envio do protocolo de documentos?\n\n") ) {
		url = 'documento_recebido_partial_form_send.php';
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
						if(o.id!="enviar_protocolo_button")
						{
							auxLoadLista();
						}
						else
						{
							auxLoadDetails( $("cd_documento_recebido_text").value, "editar" );
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

documento_recebido.prototype.receber_Click =
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

documento_recebido.prototype.cancelar_Click =
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

documento_recebido.prototype.loadPartial_AJAX = 
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

documento_recebido.prototype.initialize = 
function()
{
	this.loadPartial_AJAX( $("div_content"), $("div_content").getAttribute("urlInitial"), $("div_content").getAttribute("args") );
}

documento_recebido.prototype.filtrar_Click =
function(o)
{
	$("filtrar_hidden").value = "true";
	this.loadLista(o)
}

documento_recebido.prototype.loadLista =
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

documento_recebido.prototype.loadInserir =
function(o)
{
	this.loadPartial_AJAX( $(o.getAttribute("contentPartial")), o.getAttribute("urlPartial"), o.getAttribute("args") );
}

documento_recebido.prototype.loadReport =
function(o)
{
	var url = "documento_recebido_partial_relatorio.php";
	new Ajax.Updater( 'div_content', url, { onComplete:configureReport } );
}

documento_recebido.prototype.save =
function(o)
{
	valid = true;

	if( $("nome_participante_text").value=="" )
	{
		$("item_cd_empresa_text").className = "failed";
		$("item_cd_registro_empregado_text").className = "failed";
		$("item_seq_dependencia_text").className = "failed";
		$("participante_message").innerHTML = "Informe Empresa, RE e Sequencial corretamente";
		$("item_cd_empresa_text").focus();
		valid = false;
	}
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

documento_recebido.prototype.showHide_Click =
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

documento_recebido.prototype.change_aba =
function(id_object)
{
	$("abaMovimento").className = "";
	$("abaIncluir").className = "";
	$("abaRelatorio").className = "";

	$(id_object).className = "abaSelecionada";
}

documento_recebido.prototype.abaIncluir_Click =
function(o)
{
	this.change_aba("abaIncluir");
	
	this.loadInserir(o);
	
	return true;
}

documento_recebido.prototype.abaMovimento_Click =
function(o)
{
	this.change_aba("abaMovimento");

	this.loadLista(o);

	return true;
}

documento_recebido.prototype.abaRelatorio_Click =
function(o)
{
	this.change_aba(o.id)

	this.loadReport(o);

	return true;
}

documento_recebido.prototype.validateForm = 
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

documento_recebido.prototype.reComplete_Blur = 
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
}

documento_recebido.prototype.details_Click = 
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

documento_recebido.prototype.load_details =
function( cd_documento_recebido, command )
{
	$("abaMovimento").className = "";
	$("abaIncluir").className = "abaSelecionada";

	url = "documento_recebido_partial_form.php";
	args = "id=" + cd_documento_recebido 
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
			},
			onFailure: function (request)
			{
				$( "message_panel" ).innerHTML = "Sorry. There was an error.";
			}
		}
	);
}

documento_recebido.prototype.load_list_only =
function()
{
		$("command").value = "load_list_only";
		$("cd_comando_text").value = $F('cd_documento_recebido_text');
		$("only_form").action = 'documento_recebido_partial_form.php';
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

documento_recebido.prototype.excluir_item_Click =
function(o)
{
	if (confirm("Atenção\n\nExcluir registro?\n\n"))
	{
		$("command").value = "";
		url = "documento_recebido_partial_form_item_excluir.php";
		$("cd_documento_recebido_item_selected").value = o.getAttribute("registroId");
	
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
						auxLoadDetails( $("cd_documento_recebido_text").value, "editar" );
						
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

documento_recebido.prototype.insert_protocolo =
function(o, insert_only)
{
	$("command").action = "insert_protocolo";
	$("only_form").action = "documento_recebido_partial_form_save.php";
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
					$('cd_documento_recebido_text').value = ajaxReturn[1];
					$('ano_text').value = ajaxReturn[2];
					$('contador_text').value = ajaxReturn[3];
					//$('message_panel').innerHTML = "Protocolo de documentos adicionado com sucesso"; 
					
					if( !insert_only ){
					
						auxAddItem(o);
					
					}
					else
					{
						auxLoadDetails( $("cd_documento_recebido_text").value, "editar" );
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

documento_recebido.prototype.add_item =
function(o)
{
	$("command").action = "add_item";
	$("only_form").action = "documento_recebido_partial_form_add_item.php";
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
					$('message_panel').innerHTML = "";
					auxLoadListOnly(); 
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

documento_recebido.prototype.adicionar_item_Click =
function(o)
{
	valid = true;

	if( $('arquivo').value!='' && $('item_arquivo').value=='' )
	{
		alert( 'Antes de adicionar o documento, clique no botão ANEXAR para que o arquivo selecionado seja enviado.' );
		valid=false;
	}
	
	if( ( $("item_cd_empresa_text").value=='' || $("item_cd_registro_empregado_text").value=='' || $("item_seq_dependencia_text").value==''  )  && $("nome_participante_text").value=="" )
	{
		alert( "Você deve preencher o EMP/RE/SEQ ou Nome do Participante" );
		$("item_cd_empresa_text").focus();
		valid = false;
	}

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
			if ( $("cd_documento_recebido_text").value=="" ) 
			{
				// Insert documento_recebido and documento_recebido_item
				this.insert_protocolo(o, false);
			} 
			else 
			{
				// Insert documento_recebido_item only
				this.add_item(o);
				if( $('tipo_doc_radio').checked )
				{
					// -------------------------------------------
					$('nome_participante_text').value='';
					$('item_seq_dependencia_text').value='';
					$('item_cd_registro_empregado_text').value='';
					$('item_cd_empresa_text').value='';
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
					$('item_cd_tipo_doc_text').focus();
					// -------------------------------------------
				}
				$('item_observacao').value = '';
				$('item_nr_folha').value = '1';

				$('item_arquivo').value='';
				$('item_arquivo_nome').value='';
				//$('arquivo').value='';
				$('resetar_file').innerHTML='<INPUT TYPE="file" NAME="arquivo" id="arquivo" SIZE="40">';
				$('arquivo_upload_div').show();
				$('arquivo_div').innerHTML = '';
			}
		}
	}
}

documento_recebido.prototype.td_show_hide_Click =
function( id_show_hide )
{
	//$(id_show_hide).show( );
	$(id_show_hide).toggle( id_show_hide, 'BLIND' );
}

documento_recebido.prototype.novo_protocolo_Click = 
function(o)
{
	if( $('cd_documento_recebido_tipo').value=='' )
	{
		alert('Informe o tipo de protocolo.');
		return false;
	}
	this.insert_protocolo(o, true);
}

documento_recebido.prototype.select_documento =
function(v1, v2)
{
	$("item_cd_tipo_doc_text").value = v1;
	$("nome_documento_text").value = v2;
	Effect.Fade( "lista_documentos_div" );
}

documento_recebido.prototype.consultar_documentos_Click =
function(o)
{
	if (o.getAttribute("extra")=="show_panel")
	{
		Effect.Appear('lista_documentos_div');
	}
	$("command").value = "load_filtro_documentos";
	$("only_form").action = "documento_recebido_partial_form.php";
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

documento_recebido.prototype.ordem_itens_Change =
function(o)
{
	$("cd_comando_text").value = $("cd_documento_recebido_text").value;
	$("command").value = "update_ordem_itens";
	$("only_form").action = "documento_recebido_partial_form.php";
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
				auxLoadDetails( $("cd_documento_recebido_text").value, "editar" );
			},
			onFailure:function (request)
			{
				$('message_panel').innerHTML = "Sorry. There was an error.";
			}
		}
	);
}

documento_recebido.prototype.handleEnter =
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

documento_recebido.prototype.test_enter =
function (field, event)
{
	var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	if (keyCode == 13)
	{
		if (field.id == 'item_observacao')
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
					if( $('item_seq_dependencia_text').value=='' )
					{
						$('nome_participante_text').focus();
					}
					else
					{
						$('item_observacao').focus();
					}
				}
			}
			else
			{
				if( $('item_seq_dependencia_text').value=='' )
				{
					$('nome_participante_text').focus();
				}
				else
				{
					$('item_observacao').focus();
				}
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

documento_recebido.prototype.relatorio_filtrar = 
function()
{
	var url = "documento_recebido_partial_relatorio.php";
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
		  	, dt_envio_inicio: $F('dt_envio_inicio')
		  	, dt_envio_fim: $F('dt_envio_fim')
		  	, dt_ok_inicio: $F('dt_ok_inicio')
		  	, dt_ok_fim: $F('dt_ok_fim')
		  	, cd_usuario_envio: $F('cd_usuario_envio')
		  	, cd_usuario_destino: $F('cd_usuario_destino')
		},
		onComplete:configureReport
	} );
}


//////////////////////////////////////////////////////////

thisPage = new documento_recebido();

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

function auxLoadDetails( cd_documento_recebido, command )
{
	thisPage.load_details( cd_documento_recebido, command );
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
		, "CaseInsensitiveString"
		, "DateBR"
		, "Number"
		, "Number"
		, "Number"
		, "CaseInsensitiveString"
		, "Number"
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

/**
 * Esconde o formulário de inclusão de documentos e exibe
 * o formulário com a divisão e usuários de destino
 * para envio do protocolo
 */
function escolher_usuario()
{
	if( ! existe_documento() ) { return false; }
	
	bRedirecionar = false;
	$('documento_table').hide();
	$('usuario_div').show();
}

/**
 * Esconde o formulário de inclusão de documentos e exibe
 * o formulário com a divisão e usuários de destino
 * para redirecionamento do protocolo
 */
function redirecionar()
{
	if( ! existe_documento() ) return false;
	
	bRedirecionar = true;
	$('documento_table').hide();
	$('usuario_div').show();
}

function existe_documento()
{
	if($('count_docs').value=="0")
	{
		alert( 'Adicione algum documento antes de enviar o protocolo!' );
		return false;
	}
	else
	{
		return true;
	}
}

/**
 * Cria o combo de usuário de destino
 * baseado na divisão escolhida 
 * para envio ou redirecionamento do protocolo
 */
function carregar_usuarios()
{
	url = "documento_recebido_partial_form.php";

	var myAjax = new Ajax.Request
	(
		url + '?command=carregar_usuario',
		{
			method: 'post',
			parameters: 'gerencia='+$('gerencia_select').value,
			onLoading: function()
			{
				// nada
			},
			onComplete: function (originalRequest)
			{
				$("usuario_select_div").innerHTML = originalRequest.responseText;
				if(!bRedirecionar) $("enviar_protocolo_button").show();
				if(bRedirecionar) $("redirecionar_protocolo_button").show();
			},
			onFailure: function (request)
			{
				// nada
			}
		}
	);
}

function receber_protocolo( cd_documento_recebido )
{
	if (confirm('Receber o protocolo?'))
	{
		observacao = prompt("Observações");
		url = "documento_recebido_partial_form.php";

		var myAjax = new Ajax.Request
		(
			url + '?command=receber_protocolo',
			{
				method: 'post',
				parameters: 'cd_documento_recebido='+cd_documento_recebido+'&observacao_recebimento='+observacao,
				onLoading: function()
				{
					// nada
				},
				onComplete: function (originalRequest)
				{
					auxLoadDetails(cd_documento_recebido, 'editar');

					if(originalRequest.responseText=="1")
					{
						alert('Protocolo recebido.');
					}
				},
				onFailure: function (request)
				{
					// nada
				}
			}
		);
	}
	else
	{
		// nada
	}
}

function redirecionar_protocolo( cd_documento_recebido )
{
	if($('cd_usuario_destino').value=="")
	{
		alert('Informe um usuário');
		return false;
	}
	if (confirm('Redirecionar o protocolo?'))
	{
		url = "documento_recebido_partial_form.php";

		var myAjax = new Ajax.Request
		(
			url + '?command=redirecionar_protocolo',
			{
				method: 'post',
				parameters: 'cd_usuario_destino='+$('cd_usuario_destino').value+'&cd_documento_recebido='+cd_documento_recebido,
				onLoading: function()
				{
					// nada
				},
				onComplete: function (originalRequest)
				{
					auxLoadDetails(cd_documento_recebido, 'editar');

					if(originalRequest.responseText=="1")
					{
						alert('Documentos redirecionados.');
					}
				},
				onFailure: function (request)
				{
					// nada
				}
			}
		);
	}
	else
	{
		// nada
	}
}

/**
 * Item - envio de arquivo ao servidor
 */
function enviar_arquivo(f,act)
{
	enc=f.encoding;
	f.target='upload_iframe';
	f.encoding='multipart/form-data';
	f.action=act;
	f.submit();
	f.encoding=enc;
}
function sucesso(request)
{
	ar_request = request.toString().split('|');
	$('item_arquivo').value=ar_request[0];
	$('item_arquivo_nome').value=ar_request[1];

	// TODO: ARRUMAR CAMINHO PARA DOWNLOAD
	$('arquivo_div').innerHTML = '<a href="'+$('root').value+'up/documento_recebido/'+ar_request[0]+'" target="_blank">ver arquivo</a>&nbsp&nbsp&nbsp&nbsp<a href="javascript:void(0);" onclick="remover_arquivo();">remover arquivo</a>';

	//$('arquivo').value='';
	$('resetar_file').innerHTML='<INPUT TYPE="file" NAME="arquivo" id="arquivo" SIZE="40">';
	$('arquivo_upload_div').hide();
}
function falha(request)
{
	alert(request)
}
function remover_arquivo()
{
	$('item_arquivo').value='';
	$('item_arquivo_nome').value='';
	$('arquivo_div').innerHTML='';
	$('arquivo_upload_div').show();
}