var esta = 
{	
	init : function()
	{
		if( $F('load_by_url_hidden')=='conceito' )
		{
			$('aba_conceitos').show();
			this.aba_conceitos_Click();
		}
		if( $F('load_by_url_hidden')=='matriz' )
		{
			$('aba_matriz').show();
			$('aba_classificacao_usuario').show();
			this.aba_matriz_Click();
		}
		if( $F('load_by_url_hidden')=='nomearcomite' )
		{
			$('aba_promocao').show();
			this.aba_promocao_Click();
		}
		if( $F('load_by_url_hidden')=='relatorio' )
		{
			$('aba_relatorio').show();
			$('aba_relatorio_comite').show();
			this.aba_relatorio_Click();
		}
		if( $F('load_by_url_hidden')=='manutencao' )
		{
			$('aba_manutencao').show();
			this.aba_manutencao_Click();
		}
	},

	aba_classificacao_usuario_Click : function()
	{
		this.change_aba( 'aba_classificacao_usuario' );
		var myAjax = new Ajax.Request
		(
			'avaliacao_config_partial_classificacao_usuario.php',
			{
				method : 'get',
				onLoading : function()
				{
					$("div_content").innerHTML = 'Aguarde alguns instantes enquanto a lista de usuários é carregada ...';
				},
				onComplete : function (originalRequest)
				{
					// put returned HTML partial in the innerHTML
					$("div_content").innerHTML = originalRequest.responseText;
					apply_masks();
				},
				onFailure : function (request)
				{
					$("div_content").innerHTML = "Falha ao tentar carregar conteúdo";
				}
			}
		);
	},

	aba_conceitos_Click : function()
	{
		this.change_aba( 'aba_conceitos' );
		var myAjax = new Ajax.Request
		(
			'avaliacao_config_partial_conceitos.php',
			{
				method : 'get',
				onLoading : function()
				{
					// do nothing
				},
				onComplete : function (originalRequest)
				{
					// put returned HTML partial in the innerHTML
					$("div_content").innerHTML = originalRequest.responseText;
				},
				onFailure : function (request)
				{
					$("div_content").innerHTML = "Falha ao tentar carregar conteúdo";
				}
			}
		);
	},

	aba_manutencao_Click : function()
	{
		this.change_aba( 'aba_manutencao' );
		var myAjax = new Ajax.Request
		(
			'avaliacao_config_partial_manutencao.php',
			{
				method : 'get',
				onLoading : function()
				{
					// do nothing
				},
				onComplete : function (originalRequest)
				{
					// put returned HTML partial in the innerHTML
					$("div_content").innerHTML = originalRequest.responseText;
					configure_table_manutencao();
				},
				onFailure : function (request)
				{
					$("div_content").innerHTML = "Falha ao tentar carregar conteúdo";
				}
			}
		);
	},

	salvar_Click : function()
	{
		$("only_form").action = "avaliacao_config_partial_conceitos.php";
		$("ajax_command_hidden").value = "ajax_salvar_conceitos";
		$("only_form").request(
			{
				onLoading:function()
				{
					// do nothing
				},
				onComplete:function(originalRequest)
				{
					$("message_panel").innerHTML = originalRequest.responseText;
					//alert( "Sucesso ao salvar alterações." );
				},
				onFailure:function(request)
				{
					alert('Sorry. There was an error.');
				}
			}
		);
	},

	change_aba : function(id)
	{
		$('aba_conceitos').className = '';
		$('aba_matriz').className = '';
		$('aba_promocao').className = '';
		$('aba_classificacao_usuario').className = '';
		$('aba_relatorio').className = '';
		$('aba_relatorio_comite').className = '';
		$('aba_manutencao').className = '';

		$(id).className = 'abaSelecionada';
	},

	aba_matriz_Click : function()
	{
		this.change_aba( 'aba_matriz' );
		var myAjax = new Ajax.Request
		(
			'avaliacao_config_partial_matriz_salarial.php',
			{
				method: 'get',
				onLoading : function()
				{
					// Do nothing
				},
				onComplete : function (originalRequest)
				{
					// Put returned HTML partial in the innerHTML
					$("div_content").innerHTML = originalRequest.responseText;
					new Mask('only_form');
				},
				onFailure : function (request)
				{
					$("div_content").innerHTML = "Falha ao tentar carregar conteúdo";
				}
			}
		);
	},

	aba_relatorio_Click : function()
	{
		this.change_aba( 'aba_relatorio' );
		var myAjax = new Ajax.Request
		(
			'avaliacao_config_partial_relatorio.php',
			{
				method: 'get',
				onLoading : function()
				{
					// Do nothing
				},
				onComplete : function ( originalRequest )
				{
					// Put returned HTML partial in the innerHTML
					$("div_content").innerHTML = originalRequest.responseText;
					configure_table();
				},
				onFailure : function (request)
				{
					$("div_content").innerHTML = "Falha ao tentar carregar conteúdo";
				}
			}
		);
	},

	aba_relatorio_comite_Click : function()
	{
		this.change_aba( 'aba_relatorio_comite' );
		var myAjax = new Ajax.Request
		(
			'avaliacao_config_partial_relatorio_comite.php',
			{
				method: 'get',
				onLoading : function()
				{
					// Do nothing
				},
				onComplete : function ( originalRequest )
				{
					// Put returned HTML partial in the innerHTML
					$("div_content").innerHTML = originalRequest.responseText;
					configure_table_relatorio_comite();
				},
				onFailure : function (request)
				{
					$("div_content").innerHTML = "Falha ao tentar carregar conteúdo";
				}
			}
		);
	},

	aba_promocao_Click : function()
	{
		this.change_aba( 'aba_promocao' );
		var myAjax = new Ajax.Request
		(
			'avaliacao_config_partial_promocao.php?cd_divisao='+$('cd_divisao').value,
			{
				method: 'get',
				onLoading : function()
				{
					// Do nothing
				},
				onComplete : function (originalRequest)
				{
					// Put returned HTML partial in the innerHTML
					$("div_content").innerHTML = originalRequest.responseText;
				},
				onFailure : function (request)
				{
					$("div_content").innerHTML = "Falha ao tentar carregar conteúdo";
				}
			}
		);
	},

	consultar_usuario_Click : function(o)
	{
		if (o.getAttribute("extra")=="show_panel")
		{
			Effect.Appear('lista_usuario_div');
		}

		if( o.getAttribute('registroId')!='' )
		{
			$('cd_avaliacao_capa_hidden').value = o.getAttribute('registroId');
		}
		$("ajax_command_hidden").value = "load_filtro_usuario";
		$("only_form").action = "avaliacao_config_partial_promocao.php";
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
					$('lista_usuario_grid_div').innerHTML = ajaxReturn;
				},
				onFailure:function (request)
				{
					$('message_panel').innerHTML = "Sorry. There was an error.";
				}
			}
		);
	},

	adicionar_usuario_Click : function(o)
	{
		$("ajax_command_hidden").value = "adicionar_usuario_ao_comite";
		$("cd_usuario_avaliador_hidden").value = o.getAttribute('registroId');
		$("only_form").action = "avaliacao_config_partial_promocao.php";
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
				},
				onFailure:function (request)
				{
					$('message_panel').innerHTML = "Sorry. There was an error.";
				}
			}
		);
	},
	
	deletar_integrante_Click : function(o)
	{
		if( confirm('Excluir integrante do comitê?') )
		{
			$("ajax_command_hidden").value = "remover_usuario_do_comite";
			$("cd_avaliacao_comite_hidden").value = o.getAttribute('registroId');
			$("only_form").action = "avaliacao_config_partial_promocao.php";
			$("only_form").request(
				{
					onLoading : function ()
					{
						// Do nothing
					},
					onComplete : function (originalRequest)
					{
						// Códigos de retorno customizados
						var ajaxReturn = originalRequest.responseText;
						alert( 'Integrante excluído com sucesso.' );
						aux_aba_promocao_Click();
					},
					onFailure : function (request)
					{
						$('message_panel').innerHTML = "Sorry. There was an error.";
					}
				}
			);
		}
	},

	encaminhar_Click : function(o)
	{
		var valid = true;
		
		if( $( o.getAttribute('responsavelId') ).value=='0' || $( o.getAttribute('responsavelId') ).value=='' )
		{
			alert( 'Escolha o responsável pelo comitê antes de encaminhar!' );
			valid=false;
		}

		if(valid)
			if( confirm('Encaminhar para o comitê?') )
			{
				$("ajax_command_hidden").value = "encaminhar_comite";
				$("cd_avaliacao_capa_hidden").value = o.getAttribute('registroId');
				$("only_form").action = "avaliacao_config_partial_promocao.php";
				$("only_form").request(
					{
						onLoading : function ()
						{
							// Do nothing
						},
						onComplete : function (originalRequest)
						{
							// Códigos de retorno customizados
							var ajaxReturn = originalRequest.responseText;
							alert( 'Encaminhado ao comitê!' );
							aux_aba_promocao_Click();
						},
						onFailure : function(request)
						{
							$('message_panel').innerHTML = "Sorry. There was an error.";
						}
					}
				);
			}
	},

	classe_Click : function()
	{
		// alert( 'teste' );
	},
	
	salvar_matriz_Click : function()
	{
		var msg = '';
		valid = true;
		if($('classe_select').value=='')
		{
			msg = "Selecione uma classe!\n";
		}

		if($('faixa_select').value=='')
		{
			msg += "Selecione uma faixa!";
		}

		if(msg!='')
		{
			alert(msg)
			valid = false;
		}

		if(valid)
		{
			$("ajax_command_hidden").value = "ajax_salvar_matriz";
			$("only_form").action = "avaliacao_config_partial_matriz_salarial.php";
			$("only_form").request(
				{
					onLoading : function()
					{
						// Do nothing
					},
					onComplete : function( originalRequest )
					{
						// Códigos de retorno customizados
						var ajaxReturn = originalRequest.responseText;
						if(ajaxReturn=='true')
						{
							alert( 'Atualizado com sucesso!' );
							$('faixa_select').selectedIndex = 0;
							$('valor_inicial_text').value = '0';
							$('valor_final_text').value = '0';
						}
						else
						{
							alert(ajaxReturn);
							alert('Ocorreu algum problema ao tentar salvar, verifique se os dados informados estão corretos, se o erro persistir favor informar o analista!');
						}
					},
					onFailure : function(request)
					{
						// $('message_panel').innerHTML = "Sorry. There was an error.";
					}
				}
			);
		}
	},

	consultar_matriz : function()
	{
		if( $F('faixa_select')!='' && $F('faixa_select') )
		{
			$("ajax_command_hidden").value = "ajax_consultar_matriz";
			$("only_form").action = "avaliacao_config_partial_matriz_salarial.php";
			$("only_form").request(
				{
					onLoading : function()
					{
						// Do nothing
					},
					onComplete : function( originalRequest )
					{
						// Códigos de retorno customizados
						var ajaxReturn = originalRequest.responseText;

						valores = ajaxReturn.split( '|' );
						$('cd_matriz_salarial_hidden').value = valores[0];
						$('valor_inicial_text').value = valores[1];
						$('valor_final_text').value = valores[2];
					},
					onFailure : function( request )
					{
						// $('message_panel').innerHTML = "Sorry. There was an error.";
					}
				}
			);
		}
	},
	
	salvar_usuario_Click : function(o)
	{
		valid = true;
		var msg = '';
		if( $(o.getAttribute( 'matrizId' )).selectedIndex==0 )
		{
			msg = 'Selecione o enquadramento.\n';
		}
		if( $(o.getAttribute( 'admissaoId' )).value=='' )
		{
			msg += 'Informe a data de admissão.\n';
		}
		if( $(o.getAttribute( 'promocaoId' )).value=='' )
		{
			msg += 'Informe a data de promoção.\n';
		}
		if( $(o.getAttribute( 'tipoPromocaoId' )).value=='' )
		{
			msg += 'Informe o tipo de promoção.\n';
		}
		
		if( msg!='' )
		{
			alert( msg );
			valid = false;
		}
				
		if(valid)
		{
			$('cd_matriz_salarial_hidden').value = $F(o.getAttribute( 'matrizId' ));
			$('cd_usuario_hidden').value = o.getAttribute( 'usuarioId' );
			$('dt_admissao_hidden').value = $F(o.getAttribute( 'admissaoId' ));;
			$('dt_promocao_hidden').value = $F(o.getAttribute( 'promocaoId' ));
			$('tipo_promocao_hidden').value = $F(o.getAttribute( 'tipoPromocaoId' ));
			
			$('cd_escolaridade_hidden').value = $F(o.getAttribute( 'escolaridadeId' ));
			$("ajax_command_hidden").value = "ajax_salvar_classificacao";
			$("only_form").action = "avaliacao_config_partial_classificacao_usuario.php";
			$("only_form").request(
				{
					onLoading : function ()
					{
						// Do nothing
					},
					onComplete : function( originalRequest )
					{
						// Códigos de retorno customizados
						var ajaxReturn = originalRequest.responseText;
						if( ajaxReturn=='true' )
						{
							alert( 'Funcionário atualizado.' );
						}
						else
						{
							alert( 'Ocorreu algum erro ao tentar salvar, favor avise a equipe de informática.' );
							alert( ajaxReturn );
						}
	
						
					},
					onFailure : function( request )
					{
						// $('message_panel').innerHTML = "Sorry. There was an error.";
					}
				}
			);
		}
	},
	
	responsavel_Click : function(o, s, c)
	{
		$('origem_hidden').value = s;
		$('cd_avaliacao_capa_hidden').value = c;
		$('cd_avaliacao_comite_hidden').value = o.getAttribute( 'registroId' );
		$("ajax_command_hidden").value = "definir_responsavel";
		$("only_form").action = "avaliacao_config_partial_promocao.php";
		$("only_form").request(
			{
				onLoading : function()
				{
					// Do nothing
				},
				onComplete : function( originalRequest )
				{
					// Códigos de retorno customizados
					var ajaxReturn = originalRequest.responseText;
					if( ajaxReturn=='true' )
					{
						$( o.getAttribute('responsavelId') ).value = o.getAttribute('registroId');
						// alert('Sucesso!');
					}
					else
					{
						alert( 'Ocorreu algum erro ao tentar salvar, favor avise a equipe de informática.' );
						alert( ajaxReturn );
					}

					
				},
				onFailure : function( request )
				{
					// $('message_panel').innerHTML = "Sorry. There was an error.";
				}
			}
		);
	},

	print_report__Click : function()
	{
		this.filter_report__Click();
		
		$('only_form').action = 'avaliacao_config_partial_relatorio_pdf.php';
		$('only_form').target = '_blank';
		$('only_form').submit();
	},

	filter_report__Click : function()
	{
		$('ajax_command_hidden').value = 'filtrar';
		$('only_form').action = 'avaliacao_config_partial_relatorio.php';
		$('only_form').target = '';
		
		$("only_form").request(
			{
				onLoading:function (){
					// loading ...
				},
				onComplete:function (originalRequest)
				{
					$( "lista_div" ).innerHTML = originalRequest.responseText;
					configure_table();
					// loaded
				},
				onFailure:function (request)
				{
					alert('Sorry. There was an error.');
				}
			}
		);
	},

	filter_report_comite__Click : function()
	{
		$('ajax_command_hidden').value = 'filtrar';
		$('only_form').action = 'avaliacao_config_partial_relatorio_comite.php';
		$('only_form').target = '';
		
		$("only_form").request(
			{
				onLoading:function (){
					// loading ...
				},
				onComplete:function (originalRequest)
				{
					$( "lista_div" ).innerHTML = originalRequest.responseText;
					configure_table();
					// loaded
				},
				onFailure:function (request)
				{
					alert('Sorry. There was an error.');
				}
			}
		);
	},

	gerencia__Change : function()
	{
		$('ajax_command_hidden').value = 'carregar_combo_avaliado';
		$('only_form').action = 'avaliacao_config_partial_relatorio.php';
		$('only_form').target = '';
		
		$("only_form").request(
			{
				onLoading:function (){
					// loading ...
				},
				onComplete:function (originalRequest)
				{
					$( "avaliados_div" ).innerHTML = originalRequest.responseText;
					// loaded
				},
				onFailure:function (request)
				{
					alert('Sorry. There was an error.');
				}
			}
		);
	},

	reabrir_avaliacao__Click : function(ob)
	{
		if(confirm('Você tem certeza que deseja reabrir a avaliação?'))
		{
			$('pk__hidden').value = ob.getAttribute('registroId');
			$('ajax_command_hidden').value = 'reabrir_avaliacao';
			$('only_form').action = 'avaliacao_config_partial_manutencao.php';
			$('only_form').target = '';
	
			$("only_form").request(
			{
				onLoading:function ()
				{
					// loading ...
				},
				onComplete:function (originalRequest)
				{
					if(originalRequest.responseText=="true")
					{
						alert("Avaliação reaberta com sucesso.");
					}
					else
					{
						alert("Falha ao tentar reabrir avaliação.");
					}
					aux_aba_manutencao_Click();
					// loaded!
				},
				onFailure:function (request)
				{
					alert('Sorry. There was an error.');
				}
			}
			);
		}
	},
	
	encerrar_avaliacao__Click : function(ob)
	{
		if(confirm('Você tem certeza que deseja encerrar essa avaliação?'))
		{
			$('pk__hidden').value = ob.getAttribute('registroId');
			$('ajax_command_hidden').value = 'encerrar_avaliacao';
			$('only_form').action = 'avaliacao_config_partial_manutencao.php';
			$('only_form').target = '';
	
			$("only_form").request(
			{
				onLoading:function ()
				{
					// loading ...
				},
				onComplete:function (originalRequest)
				{
					if(originalRequest.responseText=="true")
					{
						alert("Avaliação encerrada com sucesso.");
					}
					else
					{
						alert("Falha ao tentar encerrar avaliação.");
					}
					aux_aba_manutencao_Click();
					// loaded!
				},
				onFailure:function (request)
				{
					alert('Sorry. There was an error.');
				}
			}
			);
		}
	},
	
	excluir_avaliacao__Click : function(ob)
	{
		if(confirm('Você tem certeza que deseja excluir essa avaliação?'))
		{
			$('pk__hidden').value = ob.getAttribute('registroId');
			$('ajax_command_hidden').value = 'excluir_avaliacao';
			$('only_form').action = 'avaliacao_config_partial_manutencao.php';
			$('only_form').target = '';
	
			$("only_form").request(
			{
				onLoading:function ()
				{
					// loading ...
				},
				onComplete:function (originalRequest)
				{
					if(originalRequest.responseText=="true")
					{
						alert("Avaliação excluída com sucesso.");
					}
					else
					{
						alert("Falha ao tentar excluir avaliação.");
					}
					aux_aba_manutencao_Click();
					// loaded!
				},
				onFailure:function (request)
				{
					alert('Sorry. There was an error.');
				}
			}
			);
		}
	}
}

function aux_aba_promocao_Click()
{
	esta.aba_promocao_Click();
}
function aux_aba_matriz_Click()
{
	esta.aba_matriz_Click();
}
function aux_aba_manutencao_Click()
{
	esta.aba_manutencao_Click();
}

function apply_masks()
{
	for( var index=0; index<$('only_form').elements.length; index++ )
	{
		if( $('only_form').elements[index].id.toString().indexOf( 'dt_admissao_do_usuario_' )>-1 )
		{
			MaskInput( $('only_form').elements[index] , "99/99/9999");
		}
		if( $('only_form').elements[index].id.toString().indexOf( 'dt_promocao_do_usuario_' )>-1 )
		{
			MaskInput( $('only_form').elements[index] , "99/99/9999");
		}
	}
}

function configure_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),["CaseInsensitiveString", "CaseInsensitiveString", "Number", "Number", "CaseInsensitiveString"]);
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

function configure_table_relatorio_comite()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),["Number", "CaseInsensitiveString", "CaseInsensitiveString"]);
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

function configure_table_manutencao()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),["CaseInsensitiveString", "CaseInsensitiveString", "CaseInsensitiveString", "Number", "Number"]);
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


function filtro_gerencia(cd_divisao)
{
	location.href = "avaliacao_config.php?lbu=nomearcomite&cd_divisao="+cd_divisao;
}
									