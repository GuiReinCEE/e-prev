var esta =
{
	init : function()
	{
		this.aba_primeiro__Click();
	},

	aba_primeiro__Click : function()
	{
		this.change_aba('aba_primeiro');

		new Ajax.Updater
		(
			  'div_content'
			, 'contribuicao_senge_partial_primeiro_pgto.php?' + Math.random()
			, {
				parameters: { mes: $F('mes_competencia__text'), ano: $F('ano_competencia__text') }
			  }
		);
	},

	aba_mensal__Click : function()
	{
		this.change_aba('aba_mensal');

		new Ajax.Updater('div_content', 'contribuicao_senge_partial_mensal.php?' + Math.random(), {
		  parameters: { mes: $F('mes_competencia__text'), ano: $F('ano_competencia__text') }
		});
	},

	aba_relatorio__Click : function()
	{
		this.change_aba('aba_relatorio');

		new Ajax.Updater
		(
			  'div_content'
			, 'contribuicao_senge_partial_relatorio.php?' + Math.random()
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
			, 'contribuicao_senge_partial_email.php?' + Math.random()
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
			, 'contribuicao_senge_partial_email.php?' + Math.random()
			, {
				parameters: { tipo:'retornado', mes: $F('mes_competencia__text'), ano: $F('ano_competencia__text') },
				onComplete:configure_table__relatorio_email
			  }
		);
	},

	change_aba : function(id)
	{
		$('aba_primeiro').className = '';
		$('aba_mensal').className = '';
		$('aba_relatorio').className = '';
		$('aba_relatorio_email').className = '';
		$('aba_relatorio_email_retornado').className = '';

		$(id).className = 'abaSelecionada';
	},

	send_mail__Click : function(sender)
	{
		if(confirm('Atenção\n\nConfirma o envio de emails de cobrança?'))
		{
			if(sender=="primeiro") url="contribuicao_senge_enviar_email_primeiro.php?" + Math.random();
			if(sender=="mensal") url="contribuicao_senge_enviar_email_mensal.php?" + Math.random();
			new Ajax.Updater( 'result_div', url,
			{
				method: 'post',
				parameters: 
				{ 
					  mes:$F('mes_competencia__text')
					, ano:$F('ano_competencia__text')
					, tot_internet_enviado:$F('tot_internet_enviado__text')
					, vlr_internet_enviado:$F('vlr_internet_enviado__text')
					, tot_bdl_enviado:$F('tot_bdl_enviado__text')
					, vlr_bdl_enviado:$F('vlr_bdl_enviado__text')
					, tot_arrec_enviado:$F('tot_arrec_enviado__text')
					, vlr_arrec_enviado:$F('vlr_arrec_enviado__text')
				},
				onComplete:function()
				{
					if( $('result_div').innerHTML=='true' )
					{
						alert('Comando realizado com sucesso.');
						aux_filtrar__Click();
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
		if( $('aba_primeiro').className=='abaSelecionada' )
			this.aba_primeiro__Click();
		else if($('aba_mensal').className=='abaSelecionada' )
			this.aba_mensal__Click();
		else if($('aba_relatorio').className=='abaSelecionada' )
			this.aba_relatorio__Click();
		else if($('aba_relatorio_email').className=='abaSelecionada' )
			this.aba_relatorio_email__Click();
		else if($('aba_relatorio_email_retornado').className=='abaSelecionada' )
			this.aba_relatorio_email_retornado__Click();
	}
}

function aux_filtrar__Click()
{
	esta.filtrar__Click()
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
	var ob_resul = new SortableTable(document.getElementById("table-1"),["Number", "DateTimeBR", "CaseInsensitiveString", "CaseInsensitiveString", "CaseInsensitiveString", "CaseInsensitiveString", "DateTimeBR", "CaseInsensitiveString"]);
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
	ob_resul.sort(1, true);				
}