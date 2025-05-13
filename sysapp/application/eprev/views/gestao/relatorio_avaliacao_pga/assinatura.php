<?php
	set_title('Indicadores de Gestão do PGA – Avaliação da Diretoria Executiva');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit() ?>
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateTimeBR",
			"CaseInsensitiveString"			
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		ob_resul.sort(2, true);
	}
	
	function ir_lista()
    {
        location.href = "<?= site_url('gestao/relatorio_avaliacao_pga/index') ?>";
    } 
	
	function visualizar_apresentacao()
    {
		window.open("<?= site_url('gestao/relatorio_avaliacao_pga/apresentacao/'.$row['cd_relatorio_avaliacao_pga']) ?>", '_blank');
    }

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_assinatura', 'Assinatura', TRUE, 'location.reload();');
	
	$head = array(
		'Indicador',
		'Usuário',
		'Dt. Atualização',
		'Avaliação'
	);

	$body = array();

	foreach($collection as $item)
	{	
		$body[] = array(
			array($item['ds_indicador'], 'text-align: left'),
			array($item['cd_usuario_alteracao'], 'text-align: left'),
			$item['dt_alteracao'],
			array(nl2br($item['ds_avaliacao']), 'text-align: justify')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count = false;
	
	echo aba_start($abas); 
		echo form_open('gestao/relatorio_avaliacao_pga/assinar');
			echo form_start_box('default_box', 'Cadastro'); 
				echo form_default_hidden('cd_relatorio_avaliacao_pga', '', $row['cd_relatorio_avaliacao_pga']);
				echo form_default_hidden('cd_relatorio_avaliacao_pga_diretoria', '', $cd_relatorio_avaliacao_pga_diretoria);
				echo form_default_row('trimestre', 'Trimestre: ', $row['nr_ano'].'/'.sprintf('%02d', $row['nr_trimestre']));
				echo form_default_row('dt_atualizacao_apresentacao', 'Dt. Atualização Apresentação: ', $row['dt_alteracao']);
				echo form_default_row('dt_encerramento', 'Dt. Encerramento: ', $row['dt_encerramento']);
				echo form_default_row('usuario_encerramento', 'Usuário Encerramento: ', $row['cd_usuario_encerramento']);
				if($diretor['dt_assinatura'] != '')
				{
					echo form_default_row('dt_assinatura', 'Dt. Assinatura: ', $diretor['dt_assinatura']);
				}
			echo form_end_box('default_box');
			echo form_command_bar_detail_start(); 	
				echo button_save('Abrir Apresentação', 'visualizar_apresentacao();');	
				if($diretor['dt_assinatura'] == '')
				{
					echo button_save('Assinar', '', 'botao_verde');
				}
			echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
		echo $grid->render();
        echo br(2);
    echo aba_end();

	$this->load->view('footer');
?>