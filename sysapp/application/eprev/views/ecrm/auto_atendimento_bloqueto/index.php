<?php
set_title('Bloqueto (Autoatendimento)');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		document.getElementById("current_page").value = 0;
		load();
	}

	function load()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");
		
		$.post('<?php echo site_url('ecrm/auto_atendimento_bloqueto/listar_arquivo'); ?>',
		{
			current_page: $('#current_page').val()
		},
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
					  "DateTimeBR"				
					, "CaseInsensitiveString"
					, "Number"
					, "Number"
					, "NumberFloatBR"
					, "DateBR"
					, "DateBR"
					, "DateBR"
					, "DateBR"
					, "DateBR"
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
		ob_resul.sort(0, true);
	}

	function deletaArquivo(cd_arquivo)
	{
		if(confirm("ATENÇÃO!\nEste processo BLOQUEARÁ os bloquetos deste arquivo.\n\nConfirma?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			$('#result_div').html("<?php echo loader_html(); ?>");
			$.post('<?php echo site_url('ecrm/auto_atendimento_bloqueto/deleta_arquivo'); ?>',
			{
				current_page: $('#current_page').val(),
				cd_arquivo : cd_arquivo
			},
			function(data)
			{
				if(data)
				{
					$('#result_div').html(data);
				}
				else
				{
					filtrar();
				}
			});
		}
	}
	
	function enviaEmail(cd_arquivo)
	{
		if(confirm("ATENÇÃO!\nEste processo ENVIARÁ EMAIL(S) aos PARTICIPANTES deste arquivo.\n\nConfirma?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			location.href='<?php echo site_url("ecrm/auto_atendimento_bloqueto/enviar_email"); ?>/' + cd_arquivo;
		}
	}	

	function ir_lista_bloqueto()
	{
		location.href='<?php echo site_url("ecrm/auto_atendimento_bloqueto/bloqueto"); ?>';
	}

	function ir_enviar_arquivo()
	{
		location.href='<?php echo site_url("ecrm/auto_atendimento_bloqueto/detalhe"); ?>';
	}

</script>
<?php
	$abas[] = array('aba_lista', 'Lista',    TRUE, 'location.reload();');
	$abas[] = array('aba_lista', 'Bloquetos Disponível',    FALSE, 'ir_lista_bloqueto();');
	echo aba_start( $abas );

	$config['filter'] = FALSE;
	$config['new'] = array('Enviar Arquivo', 'ir_enviar_arquivo()');

	echo form_list_command_bar($config);
?>
<div id="result_div"></div>
<br />
<?php echo aba_end( ''); ?>
<script type="text/javascript">
	filtrar();
</script>
<?php
$this->load->view('footer');
?>