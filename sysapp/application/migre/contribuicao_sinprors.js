var esta =
{
	normal : false ,
	atraso : false ,
	relatorio : false ,

	init : function(s)
	{
		if(s=='primeiro') 	this.aba_primeiro__Click();
		if(s=='atraso') 	this.aba_atraso__Click();
		if(s=='relatorio') 	this.aba_relatorio__Click()

		if( $F('load_by_url_hidden')=='retornados' )
		{
			//this.change_aba( 'aba_relatorio_email_retornado' );
			this.aba_relatorio_email_retornado__Click();
		}
	},

	aba_atraso__Click : function()
	{
		this.change_aba('aba_atraso');

		new Ajax.Updater
		(
			  'div_content'
			, 'contribuicao_sinprors_partial_atraso.php?' + Math.random()
			, {
				parameters: { mes: $F('mes_competencia__text'), ano: $F('ano_competencia__text') },
				onComplete: configure_table__sem_email
			  }
		);
	},

	aba_primeiro__Click : function()
	{
		this.change_aba('aba_primeiro');

		new Ajax.Updater
		(
			  'div_content'
			, 'contribuicao_sinprors_partial_primeiro_pgto.php?' + Math.random()
			, {
				parameters: { mes: $F('mes_competencia__text'), ano: $F('ano_competencia__text') },
				onComplete:configure_table__sem_email
			  }
		);
	},
	
	aba_mensal__Click : function()
	{
		this.change_aba('aba_mensal');
		
		new Ajax.Updater('div_content', 'contribuicao_sinprors_partial_mensal.php?' + Math.random(), {
		  parameters: { mes: $F('mes_competencia__text'), ano: $F('ano_competencia__text') },
		  onComplete: configure_table__sem_email
		});
		
	},

	aba_relatorio__Click : function()
	{
		this.change_aba('aba_relatorio');
		
		new Ajax.Updater
		(
			  'div_content'
			, 'contribuicao_sinprors_partial_relatorio.php?' + Math.random()
			, {
				parameters: { mes: $F('mes_competencia__text'), ano: $F('ano_competencia__text') },
				onComplete:configure_table__relatorio
			  }
		);
	},

	aba_relatorio_email__Click : function()
	{
		this.change_aba('aba_relatorio_email');

		new Ajax.Updater
		(
			  'div_content'
			, 'contribuicao_sinprors_partial_email.php?' + Math.random()
			, {
				parameters: { tipo:'enviado', mes: $F('mes_competencia__text'), ano: $F('ano_competencia__text') },
				onComplete:configure_table__relatorio_email
			  }
		);
	},

	aba_relatorio_email_retornado__Click : function()
	{
		this.change_aba('aba_relatorio_email_retornado');

		new Ajax.Updater
		(
			  'div_content'
			, 'contribuicao_sinprors_partial_email.php?' + Math.random()
			, {
				parameters: { tipo:'retornado', mes: $F('mes_competencia__text'), ano: $F('ano_competencia__text') },
				onComplete:configure_table__relatorio_email
			  }
		);
	},

	change_aba : function(id)
	{
		this.normal = ( id=='aba_primeiro' || id=='aba_mensal' );
		this.atraso = ( id=='aba_atraso' );
		this.relatorio = ( id=='aba_relatorio' || id=='aba_relatorio_email' || id=='aba_relatorio_email_retornado' );

		if(this.normal)
		{
			$('aba_primeiro').className = '';
			$('aba_mensal').className = '';			
		}
		if(this.relatorio)
		{
			$('aba_relatorio').className = '';
			$('aba_relatorio_email').className = '';
			$('aba_relatorio_email_retornado').className = '';
		}
		if(this.atraso)
		{
			$('aba_atraso').className = '';			
		}

		$(id).className = 'abaSelecionada';
	},

	gerar_cobranca_1ro__click : function()
	{
		if(confirm('Confirmar geração de Primeiro pagamento para competência de ' + $F('mes_competencia__text') + '/'+ $F('ano_competencia__text') + '?' ))
		{
			url = "contribuicao_sinprors_partial_primeiro_pgto.php?" + Math.random();
			new Ajax.Updater( 'result_div', url,
			{
				method: 'post',
				parameters: 
				{ 
					  mes:$F('mes_competencia__text')
					, ano:$F('ano_competencia__text')
					, comando:'gerar'
				},
				onComplete:function()
				{
					if( $('result_div').innerHTML=='true' )
					{
						alert("Gerado com sucesso.\n\nConfira a lista gerada antes de enviar os emails.");
						aux_aba_primeiro__Click();
					}
					else
					{
						alert('Ocorreu uma falha no comando, favor avisar a gerência de informática informando o Erro ID. \n\n (ERRO ID: 2)');
					}
				}
			} );
		}
	},

	gerar_cobranca_mensal__click : function()
	{
		if(confirm('Confirmar geração de Cobrança Mensal para competência de ' + $F('mes_competencia__text') + '/'+ $F('ano_competencia__text') + '?' ))
		{
			url = "contribuicao_sinprors_partial_mensal.php?" + Math.random();
			new Ajax.Updater( 'result_div', url,
			{
				method: 'post',
				parameters: 
				{ 
					  mes:$F('mes_competencia__text')
					, ano:$F('ano_competencia__text')
					, comando:'gerar'
				},
				onComplete:function()
				{
					if( $('result_div').innerHTML=='true' )
					{
						alert("Gerado com sucesso.\n\nConfira a lista gerada antes de enviar os emails.");
						aux_aba_mensal__Click();
					}
					else
					{
						alert('Ocorreu uma falha no comando, favor avisar a gerência de informática informando o Erro ID. \n\n (ERRO ID: 2)');
					}
				}
			} );
		}
	},

	gerar_cobranca_atraso__click : function()
	{
		if(confirm('Confirmar geração de Cobrança em Atraso para competência de ' + $F('mes_competencia__text') + '/'+ $F('ano_competencia__text') + '?' ))
		{
			url = "contribuicao_sinprors_partial_atraso.php?" + Math.random();
			new Ajax.Updater( 'result_div', url,
			{
				method: 'post',
				parameters: 
				{ 
					  mes:$F('mes_competencia__text')
					, ano:$F('ano_competencia__text')
					, comando:'gerar'
				},
				onComplete:function()
				{
					if( $('result_div').innerHTML=='true' )
					{
						alert("Gerado com sucesso.\n\nConfira a lista gerada antes de enviar os emails.");
						aux_aba_atraso__Click();
					}
					else
					{
						alert('Ocorreu uma falha no comando, favor avisar a gerência de informática informando o Erro ID. \n\n (ERRO ID: 2)');
					}
				}
			} );
		}
	},

	listar_gerados_1ro__click : function()
	{
		window.open('contribuicao_sinprors_lista_cobranca.php?tipo=primeiro&mes=' + $F('mes_competencia__text') + '&ano=' + $F('ano_competencia__text') + '', '_blank');
	},

	listar_gerados_mensal__click : function()
	{
		window.open('contribuicao_sinprors_lista_cobranca.php?tipo=mensal&mes=' + $F('mes_competencia__text') + '&ano=' + $F('ano_competencia__text') + '', '_blank');
	},

	listar_gerados_atraso__click : function()
	{
		window.open('contribuicao_sinprors_lista_cobranca.php?tipo=atraso&mes=' + $F('mes_competencia__text') + '&ano=' + $F('ano_competencia__text') + '', '_blank');
	},

	enviar_email_1ro__click : function()
	{
		if(confirm('ATENÇÃO esta ação é irreversível.\n\n' +
				'Confira a lista gerada antes de enviar os emails.\n\n' +
				'' +
				'Confirma o envio de emails de contribuição para a competência ' + $F('mes_competencia__text') + '/'+ $F('ano_competencia__text') + '?' ))
		{
			url="contribuicao_sinprors_enviar_email_primeiro.php?" + Math.random();
			new Ajax.Updater( 'result_div', url,
			{
				method: 'post',
				parameters: 
				{ 
					  mes:$F('mes_competencia__text')
					, ano:$F('ano_competencia__text')
					, tot_bdl_enviado:$F('tot_bdl_enviado__text')
					, vlr_bdl_enviado:$F('vlr_bdl_enviado__text')
					, tot_bco_enviado:$F('tot_bco_enviado__text')
					, vlr_bco_enviado:$F('vlr_bco_enviado__text')
				},
				onComplete:function()
				{
					if( $('result_div').innerHTML=='true' )
					{
						alert('Comando realizado com sucesso.');
						aux_aba_primeiro__Click();
					}
					else
					{
						alert('Ocorreu uma falha no comando, favor avisar a gerência de informática informando o Erro ID. \n\n (ERRO ID: 2)');
					}
				}
			} );
		}
	},

	enviar_email_mensal__click : function()
	{
		if(confirm('ATENÇÃO esta ação é irreversível.\n\n' +
				'Confira a lista gerada antes de enviar os emails.\n\n' +
				'' +
				'Confirma o envio de emails de contribuição para a competência ' + $F('mes_competencia__text') + '/'+ $F('ano_competencia__text') + '?' ))
		{
			url="contribuicao_sinprors_enviar_email_mensal.php?" + Math.random();
			new Ajax.Updater( 'result_div', url,
			{
				method: 'post',
				parameters: 
				{ 
					  mes:$F('mes_competencia__text')
					, ano:$F('ano_competencia__text')
				},
				onComplete:function()
				{
					if( $('result_div').innerHTML=='true' )
					{
						alert('Comando realizado com sucesso.');
						aux_aba_mensal__Click();
					}
					else
					{
						alert('Ocorreu uma falha no comando, favor avisar a gerência de informática informando o Erro ID. \n\n (ERRO ID: 2)');
					}
				}
			} );
		}
	},

	enviar_email_atraso__click : function()
	{
		if(confirm('ATENÇÃO esta ação é irreversível.\n\n' +
				'Confira a lista gerada antes de enviar os emails.\n\n' +
				'' +
				'Confirma o envio de emails de cobrança de contribuição em atraso?' ))
		{
			url="contribuicao_sinprors_enviar_email_atraso.php?" + Math.random();
			new Ajax.Updater( 'result_div', url,
			{
				method: 'post',
				parameters: 
				{ 
					  mes:$F('mes_competencia__text')
					, ano:$F('ano_competencia__text')
				},
				onComplete:function()
				{
					if( $('result_div').innerHTML=='true' )
					{
						alert('Comando realizado com sucesso.');
						aux_aba_atraso__Click();
					}
					else
					{
						alert('Ocorreu uma falha no comando, favor avisar a gerência de informática informando o Erro ID. \n\n (ERRO ID: 2)');
					}
				}
			} );
		}
	},

	send_mail__Click : function(sender)
	{
		if( confirm("Enviar a cobrança aos participantes?") )
		{
			if(sender=="primeiro") url="contribuicao_sinprors_enviar_email_primeiro.php?" + Math.random();
			if(sender=="mensal") url="contribuicao_sinprors_enviar_email_mensal.php?" + Math.random();
			new Ajax.Updater( 'result_div', url,
			{
				method: 'post',
				parameters: 
				{ 
					  mes:$F('mes_competencia__text')
					, ano:$F('ano_competencia__text')
					, tot_bdl_enviado:$F('tot_bdl_enviado__text')
					, vlr_bdl_enviado:$F('vlr_bdl_enviado__text')
				},
				onComplete:function()
				{
					if( $('result_div').innerHTML=='true' )
					{
						alert('Comando realizado com sucesso.');
						if(sender=="primeiro")
						{
							aux_aba_primeiro__Click();
						}
					}
					else
					{
						alert('Ocorreu uma falha no comando, favor avisar a gerência de informática informando o Erro ID. \n\n (ERRO ID: 2)');
					}
				}
			} );
		}
	},
	
	bdl_make__Click : function()
	{
		alert( 'em desenvolvimento!' );
	},
	
	doc_make__Click : function()
	{
		alert( 'em desenvolvimento' );
	},
	
	filtrar__Click : function()
	{
		if( this.normal )
		{
			if( $('aba_primeiro').className=='abaSelecionada' )
			{
				this.aba_primeiro__Click();
			}
			else if($('aba_mensal').className=='abaSelecionada' )
			{
				this.aba_mensal__Click();
			}
		}
		
		if( this.atraso )
		{
			if($('aba_atraso').className=='abaSelecionada' )
			{
				this.aba_atraso__Click();
			}
		}
		
		if( this.relatorio )
		{
			if($('aba_relatorio').className=='abaSelecionada' )
			{
				this.aba_relatorio__Click();
			}
			else if($('aba_relatorio_email').className=='abaSelecionada' )
			{
				this.aba_relatorio_email__Click();
			}
			else if($('aba_relatorio_email_retornado').className=='abaSelecionada' )
			{
				this.aba_relatorio_email_retornado__Click();
			}
		}
	}
}

function aux_aba_primeiro__Click()
{
	esta.aba_primeiro__Click();
}

function aux_aba_mensal__Click()
{
	esta.aba_mensal__Click();
}

function aux_aba_atraso__Click()
{
	esta.aba_atraso__Click();
}


function configure_table__sem_email()
{
	var ob_resul = new SortableTable(document.getElementById("table_lista_sem_email"),["CaseInsensitiveString", "Number", "CaseInsensitiveString"]);
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


function configure_table__relatorio()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),["CaseInsensitiveString", "CaseInsensitiveString", "DateBR", "Number", "Number", "Number", "DateTimeBR"]);
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

function configure_table__relatorio_email()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),["Number", "DateTimeBR", "CaseInsensitiveString", "CaseInsensitiveString", "CaseInsensitiveString", "CaseInsensitiveString", "DateTimeBR", "Number", "Number", "Number"]);
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
	ob_resul.sort(8, false);				
}

function ver_lista_sem_email()
{
	window.open( 'contribuicao_sinprors_lista_sem_email.php', 'mylist' );
}
