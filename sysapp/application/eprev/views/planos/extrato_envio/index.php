<?php
	set_title('Extrato - Enviar e-mail');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}

	function load()
	{
		if(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != "") && ($("#nr_mes").val() != "") && ($("#nr_ano").val() != "") && ($("#dt_envio").val() != ""))
		{
			$("#result_div").html("<?php echo loader_html(); ?>");

			$.post('<?php echo base_url() . index_page(); ?>/planos/extrato_envio/listar',
				$("#filter_bar_form").serialize(),
				function(data)
				{
					$("#result_div").html(data);
				}
			);
		}
		else
		{
			alert("Informe os campos com (*) e clique em filtrar");
			$("#cd_plano_empresa").focus();
		}
	}
	
	function enviarEmail()
	{
		if(($("#r_cd_empresa").val() != "") && ($("#r_cd_plano").val() != "") && ($("#r_nr_mes").val() != "") && ($("#r_nr_ano").val() != "") && ($("#r_dt_envio").val() != ""))
		{
			var confirmacao = 'Confirma o envio do Extrato para\n\n'+
			                  'Plano: ' + $("#r_cd_plano").val() +'\n'+
			                  'Empresa: ' + $("#r_cd_empresa").val() +'\n'+
			                  'Mês/Ano: ' + $("#r_nr_mes").val() + '/'+ $("#r_nr_ano").val() +'\n'+
			                  'Dt Envio: ' + $("#r_dt_envio").val() +'\n\n\n'+
						      'Clique [Ok] para Sim\n\n'+
						      'Clique [Cancelar] para Não\n\n';
							  
			if(confirm(confirmacao))
			{
				$("#formParticipanteExtrato").submit();
			}
		}
		else
		{
			alert("Informe os campos com (*) e clique em filtrar");
			$("#cd_plano_empresa").focus();
		}	
	}

	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Extrato', TRUE, 'location.reload();');
	echo aba_start($abas);

		echo form_list_command_bar();
		echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
			echo filter_plano_ajax('cd_plano', $cd_plano_empresa, $cd_plano, 'Empresa:(*)', 'Plano:(*)','',' AND cd_empresa NOT IN (4,5)');
			echo filter_integer('nr_mes', "Mês:(*)",(intval($nr_mes) > 0 ? intval($nr_mes) : date('m')));
			echo filter_integer('nr_ano', "Ano:(*)",(intval($nr_ano) > 0 ? intval($nr_ano) : date('Y')));
			echo filter_integer('nr_extrato', "Nr Extrato:(*)",(intval($nr_extrato) > 0 ? intval($nr_extrato) : ""));
			echo filter_date('dt_envio', "Dt Envio E-mail:(*)", $dt_envio);
		echo form_end_box_filter();
		echo '<div id="result_div" align="center"><BR><BR><span class="label label-success">Realize um filtro para exibir a lista</span></div>';
		echo br(5);
	echo aba_end(); 

$this->load->view('footer');
?>
