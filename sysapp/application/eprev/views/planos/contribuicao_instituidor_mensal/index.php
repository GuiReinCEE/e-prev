<?php
	set_title('Contribui��o instituidor - Contribui��o Mensal');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}

	function load()
	{
		if(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != "") && ($("#nr_mes").val() != "") && ($("#nr_ano").val() != "") && ($("#dt_emissao_eletro").val() != ""))
		{
			$("#result_div").html("<?php echo loader_html(); ?>");

			$.post('<?php echo base_url() . index_page(); ?>/planos/contribuicao_instituidor_mensal/mensal',
				{
					cd_empresa : $("#cd_plano_empresa").val(),
					cd_plano   : $("#cd_plano").val(),
					nr_mes     : $("#nr_mes").val(),
					nr_ano     : $("#nr_ano").val(),
					dt_emissao_eletro : $("#dt_emissao_eletro").val()
				}
				,
				function(data)
				{
					$("#result_div").html(data);
					configure_result_table();
				}
			);
		}
		else
		{
			alert("Informe os campos com (*) e clique em filtrar");
			$("#cd_plano_empresa").focus();
		}
	}
	
	function mensalParticipantes()
	{
		if(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != "") && ($("#nr_mes").val() != "") && ($("#nr_ano").val() != ""))
		{
			$("#result_div").html("<?php echo loader_html(); ?>");

			$.post('<?php echo base_url() . index_page(); ?>/planos/contribuicao_instituidor_mensal/mensalParticipantes',
				{
					cd_empresa : $("#cd_plano_empresa").val(),
					cd_plano   : $("#cd_plano").val(),
					nr_mes     : $("#nr_mes").val(),
					nr_ano     : $("#nr_ano").val(),
					dt_emissao_eletro : $("#dt_emissao_eletro").val()
				}
				,
				function(data)
				{
					$("#result_div").html(data);
					configure_result_table();
				}
			);
		}
		else
		{
			alert("Informe os campos com (*) e clique em filtrar");
			$("#cd_plano_empresa").focus();
		}
	}	
	
	function mensalCadastroParticipantes()
	{
		if(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != "") && ($("#nr_mes").val() != "") && ($("#nr_ano").val() != ""))
		{
			$("#result_div").html("<?php echo loader_html(); ?>");

			$.post('<?php echo base_url() . index_page(); ?>/planos/contribuicao_instituidor_mensal/mensalCadastroParticipantes',
				{
					cd_empresa : $("#cd_plano_empresa").val(),
					cd_plano   : $("#cd_plano").val(),
					nr_mes     : $("#nr_mes").val(),
					nr_ano     : $("#nr_ano").val(),
					dt_emissao_eletro : $("#dt_emissao_eletro").val()
				}
				,
				function(data)
				{
					$("#result_div").html(data);
					configure_result_table();
				}
			);
		}
		else
		{
			alert("Informe os campos com (*) e clique em filtrar");
			$("#cd_plano_empresa").focus();
		}
	}

	function inconsistencias(fl_inconsitencia)
	{
		if(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != "") && ($("#nr_mes").val() != "") && ($("#nr_ano").val() != ""))
		{
			$("#result_div").html("<?= loader_html() ?>");

			$.post("<?= site_url('planos/contribuicao_instituidor_mensal/inconsistencias') ?>",
			{
				cd_empresa       : $("#cd_plano_empresa").val(),
				cd_plano         : $("#cd_plano").val(),
				nr_mes           : $("#nr_mes").val(),
				nr_ano           : $("#nr_ano").val(),
				dt_emissao_eletro : $("#dt_emissao_eletro").val(),
				fl_inconsitencia : fl_inconsitencia
			}
			,
			function(data)
			{
				$("#result_div").html(data);
				configure_result_table();
			});
		}
		else
		{
			alert("Informe os campos com (*) e clique em filtrar");
			$("#cd_plano_empresa").focus();
		}
	}
	
	function gerar()
	{
		if(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != "") && ($("#nr_mes").val() != "") && ($("#nr_ano").val() != ""))
		{
			if($("#dt_emissao_eletro").val() != "")
			{
				var dt_hoje = new Date();
				dt_hoje.zeroTime();	
				
				var dt_emissao_eletro = Date.fromString($('#dt_emissao_eletro').val());
				dt_emissao_eletro.zeroTime();
				

				if(dt_hoje > dt_emissao_eletro)
				{
					var confirmacao = 'Confirma a gera��o de contribui��o para a compet�ncia ' + $("#nr_mes").val() + '/'+ $("#nr_ano").val() + '?\n\n'+
									  'Clique [Ok] para Sim\n\n'+
									  'Clique [Cancelar] para N�o\n\n';
									  
					if(confirm(confirmacao))
					{
						$("#result_div").html("<?php echo loader_html(); ?>");

						$.post('<?php echo base_url() . index_page(); ?>/planos/contribuicao_instituidor_mensal/gerar',
							{
								cd_empresa : $("#cd_plano_empresa").val(),
								cd_plano   : $("#cd_plano").val(),
								nr_mes     : $("#nr_mes").val(),
								nr_ano     : $("#nr_ano").val(),
								dt_emissao_eletro : $("#dt_emissao_eletro").val()
							}
							,
							function(data)
							{
								load();
							}
						);
					}
				}
				else
				{
					alert("ATEN��O\n\nOs e-mails s� podem ser enviados 1 DIA AP�S A DATA DE EMISS�O NO ELETRO")
				}
			}
			else
			{
				alert("Informe a Dt Emiss�o Eletro");
				$("#dt_emissao_eletro").focus();
			}				
		}
		else
		{
			alert("Informe os campos com (*) e clique em filtrar");
			$("#cd_plano_empresa").focus();
		}
	}
	
	function enviarEmail()
	{
		if(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != "") && ($("#nr_mes").val() != "") && ($("#nr_ano").val() != ""))
		{
			var confirmacao = 'ATEN��O esta a��o � irrevers�vel.\n\n' +
				              'Confira a lista gerada antes de enviar os emails.\n\n' +
				              'Confirma o envio de emails de contribui��o para a compet�ncia ' + $("#nr_mes").val() + '/'+ $("#nr_ano").val() + '?\n\n'+
						      'Clique [Ok] para Sim\n\n'+
						      'Clique [Cancelar] para N�o\n\n';
							  
			if(confirm(confirmacao))
			{
				$("#result_div").html("<?php echo loader_html(); ?>");

				$.post('<?php echo base_url() . index_page(); ?>/planos/contribuicao_instituidor_mensal/enviarEmail',
					{
						cd_empresa : $("#cd_plano_empresa").val(),
						cd_plano   : $("#cd_plano").val(),
						nr_mes     : $("#nr_mes").val(),
						nr_ano     : $("#nr_ano").val(),
						dt_emissao_eletro : $("#dt_emissao_eletro").val()
					}
					,
					function(data)
					{
						load();
					}
				);
			}
		}
		else
		{
			alert("Informe os campos com (*) e clique em filtrar");
			$("#cd_plano_empresa").focus();
		}
	}	

	function enviarEmailCadastro()
	{
		if(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != "") && ($("#nr_mes").val() != "") && ($("#nr_ano").val() != ""))
		{
			var confirmacao = 'Confirma o envio da lista para o Cadastro (GCM).\n\n' +
						      'Clique [Ok] para Sim\n\n'+
						      'Clique [Cancelar] para N�o\n\n';
			if(confirm(confirmacao))
			{
				$.post('<?php echo base_url() . index_page(); ?>/planos/contribuicao_instituidor_mensal/enviarEmailCadastro',
					{
						cd_empresa : $("#cd_plano_empresa").val(),
						cd_plano   : $("#cd_plano").val(),
						nr_mes     : $("#nr_mes").val(),
						nr_ano     : $("#nr_ano").val(),
						dt_emissao_eletro : $("#dt_emissao_eletro").val()
					}
					,
					function(data)
					{
						alert("Envio para o Cadastro (GCM) realizado");
					}
				);
			}
		}
		else
		{
			alert("Informe os campos com (*) e clique em filtrar");
			$("#cd_plano_empresa").focus();
		}
	}	
	
	function configure_result_table()
	{
		if(document.getElementById("table-1"))
		{
			var ob_resul = new SortableTable(document.getElementById("table-1"),[
						"RE",
						"CaseInsensitiveString",
						"CaseInsensitiveString",
						"CaseInsensitiveString",
						"DateTimeBR"
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
			ob_resul.sort(1, false);
		}
	}	
	
	function semEmail()
	{
		if(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != "") && ($("#nr_mes").val() != "") && ($("#nr_ano").val() != ""))
		{
			$("#result_div").html("<?php echo loader_html(); ?>");

			$.post('<?php echo base_url() . index_page(); ?>/planos/contribuicao_instituidor_mensal/sem_email',
				{
					cd_empresa : $("#cd_plano_empresa").val(),
					cd_plano   : $("#cd_plano").val(),
					nr_mes     : $("#nr_mes").val(),
					nr_ano     : $("#nr_ano").val(),
					dt_emissao_eletro : $("#dt_emissao_eletro").val()
				}
				,
				function(data)
				{
					$("#result_div").html(data);
					configure_result_table_sem_email();
				}
			);
		}
		else
		{
			alert("Informe os campos com (*) e clique em filtrar");
			$("#cd_plano_empresa").focus();
		}
	}	
	
	function configure_result_table_sem_email()
	{
		if(document.getElementById("table-1"))
		{
			var ob_resul = new SortableTable(document.getElementById("table-1"),[
						"RE",
						"CaseInsensitiveString",
						"CaseInsensitiveString"
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
			ob_resul.sort(1, false);
		}
	}		
	
	function ir_relatorio()
	{
		location.href='<?php echo site_url("planos/contribuicao_instituidor_mensal/relatorio"); ?>';
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Contribui��o Mensal', TRUE, 'location.reload();');
	$abas[] = array('aba_relatorio', 'Relat�rio', FALSE, 'ir_relatorio();');
	echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros', true);
		echo filter_plano_ajax('cd_plano', $cd_plano_empresa, $cd_plano, 'Empresa:(*)', 'Plano:(*)','I');
		echo filter_integer('nr_mes', "M�s:(*)",(intval($nr_mes) > 0 ? intval($nr_mes) : date('m')));
		echo filter_integer('nr_ano', "Ano:(*)",(intval($nr_ano) > 0 ? intval($nr_ano) : date('Y')));
		echo filter_date('dt_emissao_eletro', "Dt Emiss�o Eletro:(*)");
	echo form_end_box_filter();	
?>
<div id="result_div"><br><br><span style='color:green;'><b>Clique no bot�o [Filtrar] para exibir as informa��es</b></span></div>
<br>
<?php
	echo aba_end(''); 
?>
<script type="text/javascript">
	filtrar();
</script>
<?php
	$this->load->view('footer');
?>