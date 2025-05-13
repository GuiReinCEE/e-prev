function avaliacao()
{
	this.version = "1.0";
	this.autor = "cjunior";
	this.required = "minimo prototype 1.6.0";
}

avaliacao.prototype.initialize =
function()
{
	if( $("load_by_url_tipo_hidden").value=="" )
	{
		this.loadPartial_AJAX( $("div_content")
	                     , $("div_content").getAttribute("urlInitial")
	                     , $("div_content").getAttribute("args") );
	}
	else
	{
		this.load_by_url( $("load_by_url_tipo_hidden").value, $("load_by_url_cd_capa_hidden").value );
	}
}

avaliacao.prototype.input_Blur = 
function(o)
{
	if (o.value=="") 
	{
		Effect.Appear( o.id + "_message" );
		o.className = "failed";
	} 
	else 
	{
		Effect.Fade( o.id + "_message" );
		o.className = "passed";
	}

	var s = "";
}

avaliacao.prototype.loadPartial_AJAX =
function( o, url, args )
{
	var myAjax = new Ajax.Request
	(
		url + '?' + args + '&ano=' + $('filtro_ano').value,
		{
			method: 'get',
			onLoading : function()
			{
				// do nothing
			},
			onComplete : function (originalRequest)
			{
				// put returned HTML partial in the innerHTML
				o.innerHTML = originalRequest.responseText;
				
				if( url=='avaliacao_partial_lista.php' )
				{
					$("div_filtro").show();
					configure_table_lista();
				}
			},
			onFailure : function (request)
			{
				// do nothing
			}
		}
	);
}

avaliacao.prototype.filtrar_Click =
function(o)
{
	$("filtrar_hidden").value = "true";
	this.loadLista()
}

avaliacao.prototype.loadLista =
function()
{
	$("only_form").action = "avaliacao_partial_lista.php";
	$("only_form").request(
		{
			onLoading:function (){
				$('message_panel').innerHTML = "";
			},
			onComplete:function (originalRequest)
			{
				$( o.getAttribute("contentPartial") ).innerHTML = originalRequest.responseText;
				$('message_panel').innerHTML = "";
				configure_table_lista(); 
			},
			onFailure:function (request)
			{
				alert('Sorry. There was an error.');
			}
		}
	);
}

avaliacao.prototype.loadInserir =
function(o)
{
	this.loadPartial_AJAX( $( o.getAttribute("contentPartial") )
						 , o.getAttribute("urlPartial")
						 , o.getAttribute("args") );
}

avaliacao.prototype.save =
function(o)
{
	valid = true;

	if( $("nome_participante_text").value == "" )
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

avaliacao.prototype.showHide_Click =
function(o)
{
	if ($('tr_filtro_form').style.display=="none") {
		$('tr_filtro_form').show();
	}
	else 
	{
		$('tr_filtro_form').hide();
	}
}

avaliacao.prototype.show_hide_aba =
function()
{
	//$('div_filtro').hide();
	if ($("cd_avaliacao_selected_hidden").value!="")
	{
		$("aba_identificacao").show();
		
		if($F('tipo_promocao_hidden')=='H') {
			$("aba_quadro_resumo").show();
		}

		// Aba Avaliador, abre para o superior se superior já avaliou ou está avaliando
		// Abre para comite se o superior já avaliou
		// Não abre nunca para o avaliado
		if (
			    (      
			    	   $("usuario_logado_is_avaliado_hidden").value=="N" 	// LOGADO NÃO É O AVALIADO
	    	    )
			&& 
				(
					   $("status_selected_hidden").value=="S" 				//    JÁ AVALIOU 
					|| $("tem_avaliacao_hidden").value=="S" 				// OU ESTÁ AVALIANDO 
					|| $("usuario_logado_is_comite_hidden").value=="S"		// OU É INTEGRANTE DO COMITE
				)
			)
		{
			$("aba_avaliacao_superior").show();
		}
		else
		{
			// Ainda assim pode abrir essa aba se o tipo de avaliação for VERTICAL
			// E o usuário logado não for o Avaliado
			// Nesse caso não será aberta aba do Avaliado (visto em outra parte)
			if( $F('tipo_promocao_hidden')=='V' && $F('usuario_logado_is_avaliado_hidden')!='S' )
			{
				$("aba_avaliacao_superior").show();
			}
			else
			{
				$("aba_avaliacao_superior").hide();
			}			
		}
		// Aba Superior, abre se superior já avaliou, abre para comite

		// Aba Comite, abre para integrante do comite
		if ($("usuario_logado_is_comite_hidden").value=="S")
		{
			$("aba_avaliacao_comite").show();
			$("aba_avaliacao_resultado").show();
		}
		else
		{
			$("aba_avaliacao_comite").hide();
			$("aba_avaliacao_resultado").hide();
		}
		// Aba Comite, abre para integrante do comite
		
		// Aba Resultado, abre para comite e abre para o superior se avaliação já finaliza
		// também abre para o Avaliado se não for avaliação do tipo Vertical
		// Avaliação Vertical o avaliado não ve o resultado pois depende de aprovação
		// da diretoria executiva
		if ($("avaliacao_publicada_hidden").value=="S")
		{
			if($F("usuario_logado_is_avaliado_hidden")=="S" && $F('tipo_promocao_hidden')=='V')
			{
				$("aba_avaliacao_resultado").hide();
			}
			else
			{
				$("aba_avaliacao_resultado").show();
			}
		}
		// Aba Resultado, abre para comite e abre para avaliado e superior se avaliação já finaliza

		// Aba Resultado abre se o usuário logado é o superior da avaliação.
		if ($("usuario_logado_is_avaliado_hidden").value=="N" && $("usuario_logado_is_comite_hidden").value=="N")
		{
			$("aba_avaliacao_resultado").show();
		}

		// Aba Comite, abre para integrante do comite que já realizou avaliação
		if ($("tem_avaliacao_hidden").value=="S" && $("usuario_logado_is_comite_hidden").value=="S")
		{
			$("aba_avaliacao_comite").show();
		}
		else
		{
			$("aba_avaliacao_comite").hide();
		}
		// Aba Comite, abre para integrante do comite que já realizou avaliação
	}
}

avaliacao.prototype.change_aba =
function(id_object)
{
	this.show_hide_aba();
	
	$("aba_movimento").className = "";
	$("aba_identificacao").className = "";
	$("aba_quadro_resumo").className = "";
	$("aba_avaliacao_superior").className = "";
	$("aba_avaliacao_comite").className = "";
	$("aba_avaliacao_resultado").className = "";

	$(id_object).className = "abaSelecionada";
}

avaliacao.prototype.aba_movimento_Click =
function(o)
{
	this.change_aba("aba_movimento");
	$("aba_identificacao").hide();
	$("aba_quadro_resumo").hide();
	$("aba_avaliacao_superior").hide();
	$("aba_avaliacao_comite").hide();
	$("aba_avaliacao_resultado").hide();

	url = "avaliacao_partial_lista.php";
	this.loadPartial_AJAX( $("div_content"), url, "" );

	return true;
}
avaliacao.prototype.aba_identificacao_Click =
function(o)
{
	this.change_aba("aba_identificacao");

	var url = "avaliacao_partial_form.php";
	if ($("cd_avaliacao_selected_hidden").value =="")
	{
		$("ajax_command_hidden").value = "new";
		this.loadDetails_AJAX(url);
	}
	else
	{
		$("ajax_command_hidden").value = "edit";
		this.loadDetails_AJAX(url);
	}

	return true;
}

avaliacao.prototype.aba_quadro_resumo_Click =
function(o)
{
	this.change_aba("aba_quadro_resumo");
	this.load_quadro_resumo();
	return true;
}
avaliacao.prototype.aba_avaliacao_superior_Click =
function(o)
{
	this.change_aba("aba_avaliacao_superior");
	this.load_avaliacao_superior();
	return true;
}
avaliacao.prototype.aba_avaliacao_comite_Click =
function(o)
{
	this.change_aba("aba_avaliacao_comite");
	this.load_avaliacao_comite();
	return true;
}
avaliacao.prototype.aba_avaliacao_resultado_Click =
function(o)
{
	this.change_aba("aba_avaliacao_resultado");
	this.load_avaliacao_resultado();
	return true;
}

avaliacao.prototype.validateForm = 
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

avaliacao.prototype.edit_Click =
function(o)
{
	$("cd_avaliacao_selected_hidden").value = o.getAttribute("registroId");
	$("tipo_promocao_hidden").value = o.getAttribute("tipoPromocao");
	$("status_selected_hidden").value = o.getAttribute("status");
	$("usuario_logado_is_comite_hidden").value = o.getAttribute("isComite");
	$("tem_avaliacao_hidden").value = o.getAttribute("temAvaliacao");
	$("usuario_logado_is_avaliado_hidden").value = o.getAttribute("isAvaliado");
	
	this.show_hide_aba();
	this.change_aba( "aba_identificacao" );

	var url = "avaliacao_partial_form.php";

	$("ajax_command_hidden").value = "edit";
	this.loadDetails_AJAX(url);
}

avaliacao.prototype.view_Click =
function(o)
{
	$("cd_avaliacao_selected_hidden").value = o.getAttribute("registroId");
	$("tipo_promocao_hidden").value = o.getAttribute("tipoPromocao");
	$("status_selected_hidden").value = o.getAttribute("status");
	$("usuario_logado_is_comite_hidden").value = o.getAttribute("isComite");
	$("avaliacao_publicada_hidden").value = o.getAttribute("publicada");
	$("tem_avaliacao_hidden").value = o.getAttribute("temAvaliacao");
	$("usuario_logado_is_avaliado_hidden").value = o.getAttribute("isAvaliado");

	this.show_hide_aba();
	this.change_aba("aba_identificacao");

	url = "avaliacao_partial_form.php";
	$("ajax_command_hidden").value = "edit";
	this.loadDetails_AJAX(url);

}

avaliacao.prototype.new_Click =
function()
{
	$("cd_avaliacao_selected_hidden").value = "";
	$("tipo_promocao_hidden").value = 'H';

	$("aba_identificacao").show();
	$("aba_quadro_resumo").hide();
	$("aba_avaliacao_superior").hide();
	$("aba_avaliacao_comite").hide();
	
	this.change_aba("aba_identificacao");

	var url = "avaliacao_partial_form.php";
	$("ajax_command_hidden").value = "new";
	this.loadDetails_AJAX(url);
}

avaliacao.prototype.new_vertical_Click =
function(o)
{
	$("cd_avaliacao_selected_hidden").value = o.getAttribute("registroId");
	$("tipo_promocao_hidden").value = 'V';

	$("aba_identificacao").show();
	$("aba_quadro_resumo").hide();
	$("aba_avaliacao_superior").hide();
	$("aba_avaliacao_comite").hide();

	this.change_aba("aba_identificacao");

	var url = "avaliacao_partial_nova_vertical.php";
	$("ajax_command_hidden").value = "new";
	this.loadDetails_AJAX(url);
}

avaliacao.prototype.loadDetails_AJAX =
function(url)
{
	$("only_form").action = url;
	$("only_form").request(
		{
			onLoading:function (){
				// do nothing
			},
			onComplete:function (originalRequest)
			{
				$( "div_content" ).innerHTML = originalRequest.responseText;
				$("div_filtro").hide();
			},
			onFailure:function (request)
			{
				alert('Sorry. There was an error.');
			}
		}
	);	
}

avaliacao.prototype.continue_Click =
function(o)
{
	this.change_aba("aba_quadro_resumo");
	this.load_quadro_resumo();
}

avaliacao.prototype.start_Click =
function(o)
{
	var r = this.validateForm( $("only_form") );
	if(r)
	{
		$("only_form").action = "avaliacao_partial_form_save.php";
		$("only_form").request(
			{
				onLoading:function ()
				{
					$('message_panel').innerHTML = "";
				},
				onComplete:function (originalRequest)
				{
					// Códigos de retorno customizados
					// alert(originalRequest.responseText);
					var ajaxReturn = originalRequest.responseText.split("|"); 

					// Código "1": Insert ou Update concluído com sucesso,
					// retorna a sequence gerada em caso de insert
					if (ajaxReturn[0]=="1") 
					{
						$('cd_avaliacao_selected_hidden').value = ajaxReturn[1];
						$('cd_avaliacao_text').value = ajaxReturn[1];

						$("div_content").innerHTML = "";
						
						if( $F('tipo_promocao_hidden')=='H' )
						{
							$("aba_quadro_resumo").show();
							auxLoadQuadroResumo();
						}
						else if( $F('tipo_promocao_hidden')=='V' )
						{
							location.reload();
						}
					}
					else 
					{
						if (ajaxReturn[1]!="")
						{
							alert("Não foi possível incluir esta avaliação.\n\n" + ajaxReturn[1]);
							auxLoadLista();
						}
					}
				},
				onFailure:function (request)
				{
					alert('Sorry. There was an error.');
				}
			}
		);

	}
}

avaliacao.prototype.load_quadro_resumo =
function()
{
	url = "avaliacao_partial_avaliacao_avaliado.php";
	$("ajax_command_hidden").value = "view";
	$("only_form").action = url;
	$("only_form").request(
		{
			onLoading:function ()
			{
				// do nothing
			},
			onComplete:function (originalRequest)
			{
				$("div_content").innerHTML = originalRequest.responseText;
				espectativa_carregar(url);
				espectativas__configure_table();
				
				$("div_filtro").hide();
			},
			onFailure:function (request)
			{
				alert('Sorry. There was an error.');
			}
		}
	);
}
avaliacao.prototype.load_avaliacao_superior =
function()
{
	// Para avaliação VERTICAL envia para página de avaliação 
	// onde o usuário logado é tratado como avaliador
	if( $F('tipo_promocao_hidden')=='V' )// && $("usuario_logado_is_comite_hidden").value!='S' )
	{
		url = "avaliacao_partial_vertical_avaliador.php";
	}
	// Para avaliação HORIZONTAL envia para página de avaliação 
	// onde o usuário logado é tratado como avaliado
	// OU
	// se avaliação é vertical e o usuário logado é tratado como comitê
	else
	{
		url = "avaliacao_partial_avaliacao_superior.php";
	}
	
	$("ajax_command_hidden").value = "view";
	$("only_form").action = url;
	$("only_form").request(
		{
			onLoading:function ()
			{
				// do nothing
			},
			onComplete:function (originalRequest)
			{
				$("div_content").innerHTML = originalRequest.responseText;
				espectativas__configure_table();
				
				$("div_filtro").hide();
			},
			onFailure:function (request)
			{
				alert('Sorry. There was an error.');
			}
		}
	);
}

avaliacao.prototype.load_avaliacao_comite =
function()
{
	url = "avaliacao_partial_avaliacao_comite.php";
	$("ajax_command_hidden").value = "view";
	$("only_form").action = url;
	$("only_form").request(
		{
			onLoading:function ()
			{
				// do nothing
			},
			onComplete:function (originalRequest)
			{
				$("div_content").innerHTML = originalRequest.responseText;
				
				$("div_filtro").hide();
			},
			onFailure:function (request)
			{
				alert('Sorry. There was an error.');
			}
		}
	);
}
avaliacao.prototype.load_avaliacao_resultado =
function()
{
	url = "avaliacao_partial_resultado.php";
	$("ajax_command_hidden").value = "view";
	$("only_form").action = url;
	$("only_form").request(
		{
			onLoading:function ()
			{
				// do nothing
			},
			onComplete:function (originalRequest)
			{
				$("div_content").innerHTML = originalRequest.responseText;
				
				$("div_filtro").hide();
			},
			onFailure:function (request)
			{
				alert('Sorry. There was an error.');
			}
		}
	);
}

avaliacao.prototype.load_by_url =
function(tipo, cd_capa)
{
	if(cd_capa!='0') $("cd_avaliacao_selected_hidden").value = cd_capa;
	$("cd_avaliacao_selected_hidden").value = cd_capa;
	$("tem_avaliacao_hidden").value = "N";
	
	if(tipo=="A")
	{
		$("usuario_logado_is_avaliado_hidden").value = "S";
		$("tipo_promocao_hidden").value = "H";
		$("tem_avaliacao_hidden").value = "N";
		$("usuario_logado_is_comite_hidden").value = "N"; // não
		$("status_selected_hidden").value = "A"; // iniciado
		this.aba_quadro_resumo_Click( $("aba_quadro_resumo") );
	}	
	if(tipo=="S")
	{
		$("usuario_logado_is_avaliado_hidden").value = "N";
		$("tipo_promocao_hidden").value = "H";
		$("tem_avaliacao_hidden").value = "S";
		$("usuario_logado_is_comite_hidden").value = "N"; // não
		$("status_selected_hidden").value = "F"; // encaminhado ao superior (fechado pelo avaliado)
		// this.aba_quadro_resumo_Click( $("aba_quadro_resumo") );
		this.aba_avaliacao_superior_Click( $("aba_avaliacao_superior") );
	}
	if(tipo=="Sv")
	{
		$("usuario_logado_is_avaliado_hidden").value = "N";
		$("tipo_promocao_hidden").value = "V";
		$("tem_avaliacao_hidden").value = "S";
		$("usuario_logado_is_comite_hidden").value = "N"; // não
		$("status_selected_hidden").value = "F"; // encaminhado ao superior (fechado pelo avaliado)
		// this.aba_quadro_resumo_Click( $("aba_quadro_resumo") );

		this.aba_avaliacao_superior_Click( $("aba_avaliacao_superior") );
	}
	if(tipo=="C")
	{
		$("usuario_logado_is_avaliado_hidden").value = "N"; // não
		$("usuario_logado_is_comite_hidden").value = "S"; // sim
		$("status_selected_hidden").value = "S"; // encaminhado ao comite (fechado pelo superior)
		$("tem_avaliacao_hidden").value = "S";
		
		this.aba_avaliacao_comite_Click( $("aba_avaliacao_comite") )
		// this.aba_avaliacao_superior_Click( $("aba_avaliacao_superior") );
	}
	if(tipo=="Cv") /* tipo de avaliação COMITE VERTICAL */
	{
		$("tipo_promocao_hidden").value = "V";
		$("usuario_logado_is_avaliado_hidden").value = "N"; // não
		$("usuario_logado_is_comite_hidden").value = "S"; // sim
		$("status_selected_hidden").value = "S"; // encaminhado ao comite (fechado pelo superior)
		$("tem_avaliacao_hidden").value = "S";
		
		this.aba_avaliacao_comite_Click( $("aba_avaliacao_comite") )
		// this.aba_avaliacao_superior_Click( $("aba_avaliacao_superior") );
	}
	if(tipo=="R")
	{
		$("usuario_logado_is_comite_hidden").value = "S"; // sim
		$("status_selected_hidden").value = "S"; // encaminhado ao comite (fechado pelo superior)
		this.aba_avaliacao_resultado_Click( $("aba_avaliacao_resultado") );
	}
	if(tipo=="N")
	{
		this.new_Click();
	}
	if(tipo=="F")
	{
		$("tipo_promocao_hidden").value = "{tipo_promocao_hidden}";
		$("avaliacao_publicada_hidden").value = "S";
		$("usuario_logado_is_avaliado_hidden").value = "S";
		$("tem_avaliacao_hidden").value = "S";
		$("usuario_logado_is_comite_hidden").value = "N";
		$("status_selected_hidden").value = "C";
		this.aba_avaliacao_resultado_Click( $("aba_avaliacao_resultado") );
	}
}

avaliacao.prototype.save_close_and_send_Click =
function(o)
{
	if( ! this.validar_avaliacao()) return false;	
	
	if (confirm("Atenção:\n\nDeseja Fechar e Encaminhar a avaliação para seu avaliador?"))
	{
		$("status_hidden").value = "F";

		url = "avaliacao_partial_avaliacao_avaliado_save.php";
		$("only_form").action = url;
		$("only_form").request(
			{
				onLoading:function ()
				{
					// Do nothing
				},
				onComplete:function (originalRequest)
				{
					// Códigos de retorno customizados
					//$('message_panel').innerHTML = originalRequest.responseText;
					var ajaxReturn = originalRequest.responseText.split("|");

					// Código "1": Update concluído com sucesso,
					// Código "2": Update concluído com sucesso em alguma tabela e falha em outra,
					if (ajaxReturn[0]=="1" || ajaxReturn[0]=="2") 
					{
						alert( "Atenção\n\nSua avaliação foi enviada para o seu avaliador." );
						auxLoadLista(); 
					} 
					else 
					{
						alert("Falha<br><br>" + ajaxReturn[1]);
					}
				},
				onFailure:function (request)
				{
					alert('Sorry. There was an error.');
				}
			}
		);
	}
}

avaliacao.prototype.close_and_send_Click =
function(o)
{
	if( ! this.validar_avaliacao()) return false;
	
	if (confirm("Atenção:\n\nPara Fechar e Encaminhar clique em OK."))
	{
		$("status_hidden").value = "F";
		
		url = "avaliacao_partial_form_close_and_send.php";
		$("only_form").action = url;
		$("only_form").request(
			{
				onLoading:function ()
				{
					// do nothing
				},
				onComplete:function (originalRequest)
				{
					// Códigos de retorno customizados
					var ajaxReturn = originalRequest.responseText.split("|");
					
					// Código "1": Update concluído com sucesso,
					// Código "2": Update concluído com sucesso em alguma tabela e falha em outra,
					if (ajaxReturn[0]=="1" || ajaxReturn[0]=="2") 
					{
						//$('message_panel').innerHTML = "Sucesso<br><br>" + ajaxReturn[1];
						auxLoadLista(); 
					} 
					else 
					{
						//$('message_panel').innerHTML = "Falha<br><br>" + ajaxReturn[1];
						alert("Falha<br><br>" + ajaxReturn[1]);
					}
				},
				onFailure:function (request)
				{
					alert('Sorry. There was an error.');
				}
			}
		);
	}
}
avaliacao.prototype.save_and_continue_Click =
function(o)
{
	url = "avaliacao_partial_avaliacao_avaliado_save.php";
	$("only_form").action = url;
	$("only_form").request(
		{
			onLoading:function ()
			{
				// do nothing
			},
			onComplete:function (originalRequest)
			{
				// Códigos de retorno customizados
				var ajaxReturn = originalRequest.responseText.split("|"); 

				// Código "1": Update concluído com sucesso,
				// Código "2": Update concluído com sucesso em alguma tabela e falha em outra,
				//$('message_panel').innerHTML = originalRequest.responseText;
				if (ajaxReturn[0]=="1" || ajaxReturn[0]=="2") 
				{
					// $('message_panel').innerHTML = "Sucesso<br><br>" + ajaxReturn[1];
					alert( "Atenção\n\nSua avaliação foi salva com sucesso, porém ainda não foi encaminhada para seu avaliador.\nPara encaminhar clique no botão Fechar e Encaminhar!" );
					auxLoadQuadroResumo(); 
				} 
				else 
				{
					// $('message_panel').innerHTML = "Falha<br><br>" + ajaxReturn[1];
					alert("Falha<br><br>" + ajaxReturn[1]);
				}
			},
			onFailure:function (request)
			{
				alert('Sorry. There was an error.');
			}
		}
	);
}

avaliacao.prototype.insert_and_continue_by_avaliador_Click =
function(o)
{
	$("ajax_command_hidden").value = "insert_and_continue";
	url = "avaliacao_partial_avaliacao_avaliado_save_by_avaliador.php";
	$("only_form").action = url;
	$("only_form").request(
		{
			onLoading:function ()
			{
				// do nothing
			},
			onComplete:function (originalRequest)
			{
				// Códigos de retorno customizados
				//$('message_panel').innerHTML = originalRequest.responseText;
				var ajaxReturn = originalRequest.responseText.split("|"); 

				// Código "1": Update concluído com sucesso,
				// Código "2": Update concluído com sucesso em alguma tabela e falha em outra,
				if (ajaxReturn[0]=="1" || ajaxReturn[0]=="2") 
				{
					alert( "Atenção\n\nAvaliação salva com sucesso." );
					$("status_selected_hidden").value = "S";
					auxLoadAvaliacaoSuperior();
				} 
				else 
				{
					alert("Falha<br><br>" + ajaxReturn[1]);
				}
			},
			onFailure:function (request)
			{
				alert('Sorry. There was an error.');
			}
		}
	);
}
avaliacao.prototype.save_and_continue_by_avaliador_Click =
function(o)
{
	$("ajax_command_hidden").value = "save_and_continue";
	url = "avaliacao_partial_avaliacao_avaliado_save_by_avaliador.php";
	$("only_form").action = url;
	$("only_form").request(
		{
			onLoading:function ()
			{
				// do nothing
			},
			onComplete:function (originalRequest)
			{
				// Códigos de retorno customizados
				var ajaxReturn = originalRequest.responseText.split("|"); 

				// Código "1": Update concluído com sucesso,
				// Código "2": Update concluído com sucesso em alguma tabela e falha em outra,
				if (ajaxReturn[0]=="1" || ajaxReturn[0]=="2") 
				{
					alert( "Atenção\n\nAvaliação salva com sucesso." );
				} 
				else 
				{
					alert("Falha<br><br>" + ajaxReturn[1]);
				}
			},
			onFailure:function (request)
			{
				alert('Sorry. There was an error.');
			}
		}
	);
}

avaliacao.prototype.save_by_avaliador_Click =
function(o)
{
	if( ! this.validar_avaliacao()) return false;
	
	if( confirm("Atenção:\n\nPara confirmar a avaliação clique em OK.") )
	{
		$("ajax_command_hidden").value = o.getAttribute('command');
		url = "avaliacao_partial_avaliacao_avaliado_save_by_avaliador.php";
		$("only_form").action = url;
		$("only_form").request(
			{
				onLoading:function ()
				{
					// Do nothing
				},
				onComplete:function (originalRequest)
				{
					// Códigos de retorno customizados
					// $('message_panel').innerHTML = originalRequest.responseText;
					var ajaxReturn = originalRequest.responseText.split("|"); 

					// Código "1": Update concluído com sucesso,
					// Código "2": Update concluído com sucesso em alguma tabela e falha em outra,
					if (ajaxReturn[0]=="1" || ajaxReturn[0]=="2") 
					{
						alert( "Atenção\n\nEssa avaliação foi confirmada com sucesso." );
						$("status_selected_hidden").value = "S";
						auxLoadLista();
					}
					else 
					{
						// $('message_panel').innerHTML = "Falha<br><br>" + ajaxReturn[1];
						alert("Falha<br><br>" + ajaxReturn[1]);
					}
				},
				onFailure:function (request)
				{
					alert('Sorry. There was an error.');
				}
			}
		);
	}
}

avaliacao.prototype.save_by_comite_Click =
function(o)
{
	continua = false;
	if(o.getAttribute("command")=="insert_and_send" || o.getAttribute("command")=="save_and_send")
	{
		if( ! this.validar_avaliacao()) return false;
		
		continua = confirm("Atenção:\n\nPara confirmar e encaminhar a avaliação para publicação clique em OK.");
	}
	else
	{
		continua = true;
	}
	
	if ( continua )
	{
		$("ajax_command_hidden").value = o.getAttribute("command");
		url = "avaliacao_partial_save_by_comite.php";
		$("only_form").action = url;
		$("only_form").request(
			{
				onLoading:function ()
				{
					// do nothing
				},
				onComplete:function (originalRequest)
				{
					//$("message_panel").innerHTML = originalRequest.responseText;
					// Códigos de retorno customizados
					var ajaxReturn = originalRequest.responseText.split("|"); 

					// Código "1": Update concluído com sucesso,
					// Código "2": Update concluído com sucesso em alguma tabela e falha em outra,
					if (ajaxReturn[0]=="1" || ajaxReturn[0]=="2")
					{
						//$( 'message_panel' ).innerHTML = "Sucesso<br><br>" + ajaxReturn[1];
						alert( "Atenção\n\nEssa avaliação foi salva com sucesso." );
						$("status_selected_hidden").value = "C";
						if($("ajax_command_hidden").value=="insert_and_continue" || $("ajax_command_hidden").value=="save_and_continue")
						{
							$("tem_avaliacao_hidden").value="S";
							auxLoadAvaliacaoComite();
						}
						else
						{
							auxLoadLista();
						} 
					} 
					else 
					{
						// $('message_panel').innerHTML = "Falha<br><br>" + ajaxReturn[1];
						alert( "Falha<br><br>" + ajaxReturn[1] );
					}
				},
				onFailure:function (request)
				{
					alert('Sorry. There was an error.');
				}
			}
		);
	}
}

avaliacao.prototype.load_mensagem =
function(r, h)
{
	Dialog.alert
	(
		  { url: "avaliacao_partial_mensagem.php?r=" + r + "&h=" + h + "" , options: {method: 'get'} }
	    , { className: "alphacube", width:540, okLabel: "Fechar" }
	);
}

avaliacao.prototype.load_mensagem_conceito = 
function(mensagem)
{
	Dialog.alert
	(
			   mensagem
			 , { className: "alphacube", width:540,  okLabel: "Fechar"}
    );
}


avaliacao.prototype.publicar_Click =
function()
{
	if( confirm("ATENÇÃO:\n\nDeseja finalizar a avaliação?\n\n\nApós finalizar o o resultado será visualizado pelo avaliado.\n") )
	{
		$("ajax_command_hidden").value = "publicar_avaliacao";
		url = "avaliacao_partial_resultado.php";
		$("only_form").action = url;
		$("only_form").request(
			{
				onLoading:function ()
				{
					// do nothing
				},
				onComplete:function (originalRequest)
				{
					// $("message_panel").innerHTML = originalRequest.responseText;
					// Códigos de retorno customizados
					var ajaxReturn = originalRequest.responseText; 
	
					// Código "1": Update concluído com sucesso,
					// Código "0": Falha
					if (ajaxReturn=="1")
					{
						$("avaliacao_publicada_hidden").value = "S";
						alert( "Resultado publicado com sucesso!" );
						auxLoadResultado();
					} 
					else 
					{
						alert( "Falha ao tentar finalizar a avaliação!" );
					}
				},
				onFailure:function (request)
				{
					alert('Sorry. There was an error.');
				}
			}
		);
	}
}

avaliacao.prototype.aspecto_editar__Click =
function(ob)
{
	var url = ob.getAttribute('url');
	new Ajax.Updater( 'aspecto_div', url, { parameters: { ajax_command_hidden: 'ajax_espectativas', id: ob.getAttribute('registroId') } } );
	$("avaliacao__div").hide();
	$("aspecto_div").show();
}

avaliacao.prototype.aspecto_deletar__Click =
function(ob)
{
	if(confirm('Atenção\n\nDeseja realmente excluir a expectativa?'))
	{
		var url = ob.getAttribute('url');
		new Ajax.Updater( 'aspecto_div', url, 
		{ 
			parameters: 
			{ 
			  	ajax_command_hidden: 'ajax_espectativas__delete'
			  	, id: ob.getAttribute('registroId')
			},
			onComplete: function()
			{
				if($('aspecto_div').innerHTML=='true')
				{
					alert('A expectativa foi excluída com sucesso.');
					espectativa_carregar( url );
				}
				else
				{
					alert('Não foi possível excluir a expectativa.');
				}
			}
		});
	}
}

avaliacao.prototype.nova_espectativa__Click =
function( ob )
{
	var url = ob.getAttribute('url');
	new Ajax.Updater( 'aspecto_div', url, { parameters: { ajax_command_hidden: 'ajax_espectativas', cd_avaliacao: $F('cd_avaliacao_hidden') } } );
	$("avaliacao__div").hide();
	$("aspecto_div").show();
}

avaliacao.prototype.espectativa__Hide =
function(ob)
{
	$("aspecto_div").innerHTML = '';
	$("avaliacao__div").show();
	$("aspecto_div").hide();
	location.href = "#expectativas";
}

avaliacao.prototype.espectativa_salvar__Click =
function( ob )
{
	var url = ob.getAttribute('url');
	new Ajax.Updater( 'aspecto_div', url, 
	{ 
		parameters: 
		{ 
		  	  ajax_command_hidden: 'ajax_espectativas__save'
		  	, id: $F('cd_avaliacao_aspecto__hidden') 
		  	, cd_avaliacao: $F('cd_avaliacao__hidden') 
		  	, aspecto: $F('aspecto__text') 
		  	, resultado_esperado: $F('resultado__text') 
		  	, acao: $F('acao__text')
		},
		onComplete:function()
		{
			thisPage.espectativa__Hide();
			espectativa_carregar(url);
			//espectativa_carregar('avaliacao_partial_avaliacao_superior.php');
		}
	});
}

avaliacao.prototype.validar_avaliacao = 
function()
{
	var old_name='';
	var new_name='';
	var valid = true;
	retorno = true;
	for( var index=0; index<document.onlyForm.elements.length; index++ )
	{
        //ALTERAÇÃO FEITA PARA UM CASO ESPECIFICO EM QUE A PESSOA QUE FOI FAZER A AVALIAÇÃO NÃO TINHA A ESCOLARIDAD MINIMA PARA O SEU CARGO
		ob = document.onlyForm.elements[index];
		if( ob.name.toString().indexOf('comp_inst')=='' || (ob.name.toString().indexOf('grau_escolaridade')=='' && ob.name.toString().indexOf('grau_ce_zero')=='1') || ob.name.toString().indexOf('comp_espec')=='' || ob.name.toString().indexOf('responsabilidade')=='' || ob.name.toString().indexOf('end_objects')=='' )
		{
			if(ob.name!=old_name)
			{
				if( ! valid )
				{
					alert('Atenção\n\nVocê deve preencher todos os campos antes de confirmar.');
					retorno = false;
					break;
				}
				old_name = ob.name;
				valid = false;
			}
			if( ! valid && ob.checked )
			{
				valid = true;
			}
		}
	}

	return retorno;
}

avaliacao.prototype.cd_usuario_avaliador__Change =
function(ob) 
{
	new Ajax.Updater('combo_avaliado__div', 'avaliacao_partial_nova_vertical.php',{ 
	
		parameters: { 
			
			  ajax_command_hidden:'carregar_combo_avaliado'
			, cd_superior:$F('cd_usuario_avaliador_select')
			
		 }
	
	});
}

//////////////////////////////////////////////////////////

thisPage = new avaliacao();

function auxLoadLista()
{
	thisPage.aba_movimento_Click( $("aba_movimento") );
}
function auxLoadQuadroResumo()
{
	thisPage.change_aba("aba_quadro_resumo");
	thisPage.load_quadro_resumo();
}
function auxLoadAvaliacaoSuperior()
{
	thisPage.aba_avaliacao_superior_Click( $("aba_avaliacao_superior") );
}
function auxLoadAvaliacaoComite()
{
	thisPage.aba_avaliacao_comite_Click( $("aba_avaliacao_comite") );
	//thisPage.load_avaliacao_superior();
}
function auxLoadResultado()
{
	thisPage.aba_avaliacao_resultado_Click( $("aba_avaliacao_resultado") );
}

function espectativas__configure_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),["CaseInsensitiveString"]);
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
}

function espectativa_carregar( url )
{
	$("ajax_command_hidden").value = "ajax_espectativas__lista";
	$("only_form").action = url;
	$("only_form").request(
		{
			onLoading:function ()
			{
				$('espectativa_lista__div').innerHTML = 'carregando ...';
			},
			onComplete:function (originalRequest)
			{
				$('espectativa_lista__div').innerHTML = originalRequest.responseText;
				espectativas__configure_table();
			},
			onFailure:function (request)
			{
				location.reload();
			}
		}
	);
}

function configure_table_lista()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),["", "Number", "CaseInsensitiveString", "CaseInsensitiveString", "CaseInsensitiveString", "CaseInsensitiveString", "CaseInsensitiveString"]);
	ob_resul.onsort = function ()
	{
		var rows = ob_resul.tBody.rows;
		var l = rows.length;
		for (var i = 0; i < l; i++)
		{
			if(rows[i].getAttribute("marcar")=="N")
			{
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		}
	};
	ob_resul.sort(2, false);
}

function filtrar()
{
	thisPage.loadPartial_AJAX( $("div_content"), "avaliacao_partial_lista.php", "" );
}






function validaFormAcordo(cd_avaliacao_capa)
{
	var fl_acordo = ((document.getElementById("fl_acordo_A").checked) ? "A" : ((document.getElementById("fl_acordo_C").checked) ? "C" : ""));
	
	if((document.getElementById("fl_acordo_A").checked) || (document.getElementById("fl_acordo_C").checked))
	{
		var confirmacao = 'ATENÇÃO.\n\n' +
						  'Confirma a sua opção?\n\n' +
						  'Clique [Ok] para Sim\n\n'+
						  'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{
			location.href="avaliacao_acordo_grava.php?cd_avaliacao_capa=" + cd_avaliacao_capa + "&fl_acordo=" + fl_acordo; 
		}
	}
	else
	{
		alert(document.getElementById("aval_acordo_pergunta").innerHTML);
	}
}


function setRespAnterior(id,valor)
{
	console.log(id);
	
	document.getElementById("lb" + id).className = "label";
	
	if(document.getElementById("ed" + id).value != valor)
	{
		document.getElementById("lb" + id).className = "label label-important";
	}
}
