<?php
set_title('Pendências Gestão');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('gestao/pendencia_gestao/listar') ?>",
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'Number',
	        'CaseInsensitiveString',
	        'CaseInsensitiveString',
	        'DateBR',
	        'DateBR',
	        'CaseInsensitiveString',
	        'CaseInsensitiveString',
	        'CaseInsensitiveString',
	        'CaseInsensitiveString',
	        'CaseInsensitiveString',
	        'CaseInsensitiveString'
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

	function novo()
	{
		location.href = "<?= site_url('gestao/pendencia_gestao/cadastro') ?>";
	}

	$(function() {
        if($("#dt_inicial").val() == "")
        {
        	//$("#dt_inicial").val("01/01/<?= date('Y') ?>");
        	//$("#dt_final").val("<?= date('d/m/Y') ?>");
        }

		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

    $situacao = '';

	foreach($filtros['situacao'] as $item)
	{
		$situacao .= form_checkbox(array(
			'id'   => $item['id'], 
			'name' => $item['id']
		), $item['value'], $item['checked']).'<label for="'.$item['id'].'">'.$item['text'].'</label><br/>';
    }    

	$config['button'][] = array('Nova Pendência', 'novo();');

	echo aba_start($abas);
	    echo form_list_command_bar($config);
	    echo form_start_box_filter();
	        echo filter_dropdown('cd_reuniao_sistema_gestao_grupo', 'Grupo de Reunião:', $grupo_tipo, $cd_reuniao_sistema_gestao_grupo);
	        echo filter_dropdown('cd_reuniao_sistema_gestao_tipo', 'Reunião:', $reuniao);
	        echo filter_date_interval('dt_inicial', 'dt_final', 'Dt. Reunião:');
	        echo filter_date_interval('dt_prazo_inicial', 'dt_prazo_final', 'Prazo:');
	        echo filter_dropdown('cd_superior', 'Superior:', $superior);
	        echo filter_dropdown('cd_responsavel', 'Ger. Responsável:', $responsavel);
            echo filter_dropdown('cd_usuario_responsavel', 'Usu. Responsável:', $usuario);
            echo form_default_row('fl_situacao', 'Situação: ', $situacao);
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer'); 
?>