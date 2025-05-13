<?php
set_title('Pré-venda - Lista');
$this->load->view('header');
?>
<script>

	function filtrar()
	{
		if($('#fl_tela').val() == "T")
		{
			//if($('#cd_empresa').val() != "")
			//{
				$('#result_div').html("<?php echo loader_html(); ?>");
				$.post( '<?php echo site_url('ecrm/prevenda/listar');?>',
				{
					cd_empresa: $('#cd_empresa').val(),
					cd_registro_empregado: $('#cd_registro_empregado').val(),
					seq_dependencia: $('#seq_dependencia').val(),
					nome: $('#nome').val(),
					dt_contato_ini: $('#dt_contato_ini').val(),
					dt_contato_fim: $('#dt_contato_fim').val(),			
					dt_envio_ini: $('#dt_envio_ini').val(),
					dt_envio_fim: $('#dt_envio_fim').val(),
					dt_opcao_ini: $('#dt_opcao_ini').val(),
					dt_opcao_fim: $('#dt_opcao_fim').val(),
					dt_ingresso_ini: $('#dt_ingresso_ini').val(),
					dt_ingresso_fim: $('#dt_ingresso_fim').val(),
					cd_pre_venda_local: $('#cd_pre_venda_local').val(),
					fl_inscricao: $('#fl_inscricao').val(),
					cd_usuario_contato: $('#cd_usuario_contato').val()
				},
				function(data)
				{
					$('#result_div').html(data);
					configure_result_table();
				});
			//}
			//else
			//{
			//	alert("Informe a Empresa");
			//	$('#cd_empresa').focus();
			//}
		}
		else if($('#fl_tela').val() == "E")
		{
			excel();
		}
		else
		{
			alert('Informe como mostrar o filtro.');
		}
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
			[
				"Number", 
				"RE", 
				"CaseInsensitiveString", 
				"DateBR", 
				"CaseInsensitiveString", 
				"CaseInsensitiveString", 
				"DateBR", 
				"DateBR", 
				"DateBR"/*, 
				"DateBR", 
				"DateBR", 
				"CaseInsensitiveString"*/
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
		ob_resul.sort(2, false);
	}

	function novo()
	{
		location.href='<?php echo base_url().index_page(); ?>/ecrm/prevenda/abrir';
	}
	
	function relatorio()
	{
		location.href='<?php echo base_url().index_page(); ?>/ecrm/prevenda/relatorio';
	}	
	
	function excel()
    {
		if($('#cd_empresa').val() != "")
		{
			filter_bar_form.method = "post";
			filter_bar_form.action = '<?php echo site_url('/ecrm/prevenda/excel')?>';
			filter_bar_form.target = "_self";
			filter_bar_form.submit();
		}
		else
		{
			alert("Informe a Empresa");
			$('#cd_empresa').focus();
		}
    }
	
	function ir_protocolo_interno()
	{
		location.href='<?php echo site_url('/ecrm/prevenda/protocolo_interno'); ?>';
	}	
	
	$(function(){
		filtrar();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
$abas[] = array('aba_relatorio', 'Relatório', FALSE, 'relatorio();');
$abas[] = array('aba_protocolo', 'Encaminhar Ped. Inscrição', FALSE, 'ir_protocolo_interno();');

$config['button'][]=array('Novo', 'novo();');
	
echo aba_start( $abas );
	echo form_list_command_bar($config);	
	echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
		echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), "Participante:", Array(), TRUE, FALSE );
		echo filter_text('nome', 'Nome:','','style="width: 350px;"');
		echo filter_date_interval('dt_contato_ini', 'dt_contato_fim', 'Data do Contato:',calcular_data('','2 month'), date('d/m/Y'));
		echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Data de Envio GAP:');
		echo filter_date_interval('dt_opcao_ini', 'dt_opcao_fim', 'Data de Opção:');
		echo filter_date_interval('dt_ingresso_ini', 'dt_ingresso_fim', 'Data de Ingresso:');
		echo filter_dropdown('cd_usuario_contato', 'Responsável Contato:', $ar_usuario_contato);
		echo filter_dropdown('cd_pre_venda_local', 'Local:', $ar_local);
		echo filter_dropdown("fl_inscricao", "Inscrição preenchida:", array( array('value'=>'S', 'text'=>'Sim'), array('value'=>'N', 'text'=>'Não') ));
		echo filter_dropdown("fl_tela", "Mostrar:", array( array('value'=>'T', 'text'=>'Tela'), array('value'=>'E', 'text'=>'Excel') ), array('T'));
	echo form_end_box_filter();
	echo '<div id="result_div"><br><br><span style="color:green;"><b>Realize um filtro para exibir a lista</b></span></div>';
	echo br();
echo aba_end();


$this->load->view('footer');
?>