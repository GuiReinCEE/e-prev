<?php
	set_title('Treinamento - Diretoria e Conselhos');
	$this->load->view('header');
?>
<script type="text/javascript">

	function novo()
	{
		location.href = "<?= site_url('cadastro/treinamento_diretoria_conselhos/cadastro') ?>";
	}
	
	function pdf()
	{
		filter_bar_form.method = "post";
		filter_bar_form.action = '<?= base_url() . index_page(); ?>/cadastro/treinamento_diretoria_conselhos/pdf';
		filter_bar_form.target = "_blank";
		filter_bar_form.submit();
	}

	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('cadastro/treinamento_diretoria_conselhos/listar') ?>",
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
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'DateBR',
			'DateBR',
			'CaseInsensitiveString',
			'NumberFloatBR',
			'Number'
			
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
		ob_resul.sort(6, true);
	}

	$(function(){
		filtrar();
	});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', true, 'location.reload();');

echo aba_start($abas);

    $config['button'][] = array('Novo Treinamento', 'novo()');
    $config['button'][] = array('Gerar PDF', 'pdf()');

    echo form_list_command_bar($config);

    echo form_start_box_filter();
        echo filter_integer('nr_numero', 'Número:');
        echo filter_integer('nr_ano', 'Ano:', date('Y'));
        echo filter_text('ds_nome', 'Nome do Evento:', '', 'style="width:300px;"');
        echo filter_date_interval('dt_inicio_ini', 'dt_inicio_fim', 'Dt Inicio:');
        echo filter_date_interval('dt_final_ini', 'dt_final_fim', 'Dt Final:');
        echo filter_dropdown('cd_treinamento_colaborador_tipo', 'Tipo: ', $tipo);
		echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'ds_nome_colaborador'), 'RE:', '', TRUE, TRUE);
		echo filter_text('ds_nome_colaborador', 'Nome:', '', 'style="width:300px;"');
    echo form_end_box_filter();

	echo '<div id="result_div"></div>';
	echo br(2);	
echo aba_end();

$this->load->view('footer');
?>