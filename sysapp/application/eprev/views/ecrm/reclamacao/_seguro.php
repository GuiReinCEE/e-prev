<?php
set_title('Controle de Reclamações Seguro - Lista');
$this->load->view('header');
?>
<script>
	
	function filtrar()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");
		
		$.post( '<?php echo site_url('/ecrm/reclamacao/seguro_listar_reclamacao'); ?>',
		$('#filter_bar_form').serialize(),
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
			"Number",
			"RE",
			"CaseInsensitiveString",
			null,
			"CaseInsensitiveString",
			"DateTimeBR",
			"DateTimeBR",
			"DateTimeBR",
			null,
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
		ob_resul.sort(0, true);
	}

	function nova()
	{
		location.href='<?php echo site_url('/ecrm/reclamacao/cadastro'); ?>';
	}
	
	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Nova Reclamação ou Sugestão', 'nova();');

	$ar_tipo_empresa[] = array('value'=> 'I', 'text'=>'Instituidor');
	$ar_tipo_empresa[] = array('value'=> 'P', 'text'=>'Patrociandora');

	$participante['cd_empresa']            = $cd_empresa;
	$participante['cd_registro_empregado'] = $cd_registro_empregado;
	$participante['seq_dependencia']       = $seq_dependencia;

	echo aba_start( $abas );
		echo form_list_command_bar($config);	
		echo form_start_box_filter('filter_bar', 'Filtros', false);
			echo filter_integer('numero', "Número:");
			echo filter_dropdown('cd_empresa_patr', 'Empresa:', $ar_empresas);
			echo filter_dropdown('tipo_cliente', 'Tipo Empresa:', $ar_tipo_empresa);
			echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), "Participante:", $participante, TRUE, TRUE );	
			echo filter_text('nome', "Nome: ", '', "style='width:100%;'");
			echo filter_dropdown('cd_plano', 'Plano:', $ar_planos);	
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Data Cadastro:', calcular_data('','1 year'), date('d/m/Y'));
			echo filter_usuario_ajax(array('cd_divisao','cd_usuario_responsavel'),'','', 'Responsável:', 'Gerência:');
			echo filter_date_interval('dt_prazo_ini', 'dt_prazo_fim', 'Data Prazo:');
			echo filter_date_interval('dt_encerrado_ini', 'dt_encerrado_fim', 'Data Encerrrado:');
			echo filter_dropdown('cd_usuario_inclusao', 'Aberta por:', $arr_usuario_inclusao);
			
		echo form_end_box_filter();
		echo '<div id="result_div"><br><br><span style="color:green;"><b>Realize um filtro para exibir a lista</b></span></div>';
		echo br(5);
	echo aba_end();

$this->load->view('footer');
?>