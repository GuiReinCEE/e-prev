<?php
	set_title('Indicadores de Gestão do PGA – Avaliação da Diretoria Executiva');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_presidente')) ?>
	
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
		
		var ob_resul2 = new SortableTable(document.getElementById("table-2"),
		[
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateTimeBR"		
		]);
		ob_resul2.onsort = function ()
		{
			var rows2 = ob_resul2.tBody.rows;
			var l2 = rows2.length;
			for (var i = 0; i < l2; i++)
			{
				removeClassName(rows2[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows2[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		ob_resul2.sort(1, true);
	}
	
	function ir_lista()
    {
        location.href = "<?= site_url('gestao/relatorio_avaliacao_pga/index') ?>";
    } 
	
    function atualizar_apresentacao()
    {
        var confirmacao = "Deseja ATUALIZAR a Apresentação?\n\n"+
                          "*A atualização vai ser feita a partir dos indicadores do momento atual.\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('gestao/relatorio_avaliacao_pga/atualiza_indicador/'.$row['cd_relatorio_avaliacao_pga']) ?>";
        }
    }
	
	function visualizar_apresentacao()
    {
		window.open("<?= site_url('gestao/relatorio_avaliacao_pga/apresentacao/'.$row['cd_relatorio_avaliacao_pga']) ?>", '_blank');
    }

    function reenviar_email_diretoria()
    {
    	location.href = '<?= site_url('gestao/relatorio_avaliacao_pga/reenviar_email_diretoria/'.$row['cd_relatorio_avaliacao_pga']) ?>';
    }

    function encerrar()
    {
        var confirmacao = "Deseja ENCERRAR a Reunião e Encaminhar para Diretoria?\n\n"+
                          "*A apresentação não vai poder ser mais atualizada.\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('gestao/relatorio_avaliacao_pga/encerrar/'.$row['cd_relatorio_avaliacao_pga']) ?>";
        }
    }

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_indicador', 'Indicador', TRUE, 'location.reload();');
	
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
			(trim($row['dt_encerramento']) == '' ? array(anchor('gestao/relatorio_avaliacao_pga/cadastro_avaliacao/'.$cd_relatorio_avaliacao_pga.'/'.$item['cd_indicador'].'/'.$item['cd_relatorio_avaliacao_pga_indicador'], $item['ds_indicador']), 'text-align: left') : array($item['ds_indicador'], 'text-align: left')),
			array($item['cd_usuario_alteracao'], 'text-align: left'),
			$item['dt_alteracao'],
			array(nl2br($item['ds_avaliacao']), 'text-align: justify')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->id_tabela = "table-1";
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count = false;
	
	echo aba_start($abas); 
		echo form_open('gestao/relatorio_avaliacao_pga/salvar_diretoria');
			echo form_start_box('default_box', 'Cadastro'); 
				echo form_default_hidden('cd_relatorio_avaliacao_pga', '', $row['cd_relatorio_avaliacao_pga']);
				echo form_default_hidden('cd_relatorio_avaliacao_pga_diretoria_fin', '', $row['cd_relatorio_avaliacao_pga_diretoria_fin']);
				//echo form_default_hidden('cd_relatorio_avaliacao_pga_diretoria_adm', '', $row['cd_relatorio_avaliacao_pga_diretoria_adm']);
				echo form_default_hidden('cd_relatorio_avaliacao_pga_diretoria_seg', '', $row['cd_relatorio_avaliacao_pga_diretoria_seg']);
				echo form_default_hidden('cd_relatorio_avaliacao_pga_diretoria_pre', '', $row['cd_relatorio_avaliacao_pga_diretoria_pre']);
				echo form_default_row('trimestre', 'Trimestre: ', $row['nr_ano'].'/'.sprintf('%02d', $row['nr_trimestre']));
				echo form_default_row('dt_atualizacao_apresentacao', 'Dt. Atualização Apresentação: ', $row['dt_alteracao']);
				if(trim($row['dt_encerramento']) != '')
				{	
					echo form_default_row('dt_encerramento', 'Dt. Encerramento: ', $row['dt_encerramento']);
					echo form_default_row('usuario_encerramento', 'Usuário Encerramento: ', $row['cd_usuario_encerramento']);
				}	
			echo form_end_box('default_box');
			echo form_command_bar_detail_start(); 	
				echo button_save('Abrir Apresentação', 'visualizar_apresentacao();');	
				if(trim($row['dt_encerramento']) == '')
				{
					echo button_save('Atualizar Apresentação', 'atualizar_apresentacao();', 'botao_verde');
					if(intval($row['avaliados']) == count($collection))
					{
						echo button_save('Encaminhar Diretoria', 'encerrar();', 'botao_vermelho');
					}						
				}	

				if(intval($assinaturas['qt_assinaturas']) > 0 AND trim($row['dt_encerramento']) != '')
				{
					echo button_save('Reencaminhar Diretoria', 'reenviar_email_diretoria();', 'botao_verde');
				}	
			echo form_command_bar_detail_end();
			if((trim($row['dt_encerramento']) == '') AND (intval($row['avaliados']) == count($collection)))
			{
				echo form_start_box('assinatura_box', 'Assinatura');
					echo form_default_dropdown('cd_presidente', 'Presidente: (*)', $diretoria, $row['cd_presidente']);

					echo form_default_dropdown('cd_dir_financeiro', 'Diretor Financeiro:', $diretoria, $row['cd_dir_financeiro']);
					//echo form_default_dropdown('cd_dir_administrativo', 'Diretor Administrativo:', $diretoria, $row['cd_dir_administrativo']);
					echo form_default_dropdown('cd_dir_seguridade', 'Diretor de Seguridade:', $diretoria, $row['cd_dir_seguridade']);
					
				echo form_end_box('assinatura_box');
				echo form_command_bar_detail_start();
					echo button_save('Salvar');
				echo form_command_bar_detail_end();
			}
			else if(trim($row['dt_encerramento']) != '')
			{
				$head2 = array(
					'Cargo',
					'Nome',
					'Dt. Assinatura'
				);

				$body2 = array();

				foreach($diretores_assinatura as $item)
				{	
					$body2[] = array(
						array($item['diretoria'], 'text-align: left'),
						array($item['cd_usuario_diretoria'], 'text-align: left'),
						$item['dt_assinatura']
					);
				}

				$this->load->helper('grid');
				$grid2 = new grid();
				$grid2->id_tabela = "table-2";
				$grid2->head = $head2;
				$grid2->body = $body2;
				$grid2->view_count = false;
				
				echo br(2);
				echo $grid2->render();
			}
        echo form_close();
        echo br(2);
		echo $grid->render();
        echo br(2);
    echo aba_end();

	$this->load->view('footer');
?>