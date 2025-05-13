<?php
	set_title('Reclamações e Sugestões - Acompanhamento');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_acompanhamento')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/reclamacao') ?>";
	}
	
	function ir_cadastro()
	{
		location.href = "<?= site_url('ecrm/reclamacao/cadastro/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_anexo()
	{
		location.href = "<?= site_url('ecrm/reclamacao/anexo/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_reencaminhamento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/reencaminhamento/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_acao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/acao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_atendimento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/atendimento/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_prorrogacao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/prorrogacao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_retorno()
	{
		location.href = "<?= site_url('ecrm/reclamacao/retorno/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_validacao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/validacao_comite/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_parecer_final()
	{
		location.href = "<?= site_url('ecrm/reclamacao/parecer_comite_avaliacao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"DateTimeBR",
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

	$(function(){
		configure_result_table();

		default_conceito_box_box_recolher();
	})
</script>
<style>
    #conceito_item {
        white-space:normal !important;
    }
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_reclamacao', 'Cadastro', FALSE, 'ir_cadastro();');

	if($permissao['fl_aba_atendimento'])
	{	
	//	$abas[] = array('aba_atendimento', 'Atendimento', FALSE, 'ir_atendimento();');
	}

	if($permissao['fl_aba_prorrogacao'])
	{	
		$abas[] = array('aba_reencaminahemnto', 'Reencaminhamento', FALSE, 'ir_reencaminhamento();');
		$abas[] = array('aba_prorrogacao', 'Prorrogação', FALSE, 'ir_prorrogacao();');
	}

	$abas[] = array('aba_acompanhamento', 'Acompanhamento', TRUE, 'location.reload();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

	if($permissao['fl_aba_acao'])
	{
		$abas[] = array('aba_acao', 'Ação', FALSE, "ir_acao();");
	}

	if($permissao['fl_aba_retorno'])
	{
		$abas[] = array('aba_retorno', 'Retorno', FALSE, 'ir_retorno();');
	}

	if($permissao['fl_aba_comite'])
	{
		$abas[] = array('aba_validacao_comite', 'Validação Comitê', FALSE, 'ir_validacao();');
	}

	if($permissao['fl_aba_parecer_final'])
	{
		$abas[] = array('aba_parecer_final', 'Parecer Final', FALSE, 'ir_parecer_final();');
	}	

	$head = array( 
		'Dt. Inclusão',
		'Usuário',
		'Descrição'
	);

	$body = array();

	foreach($acompanhamento as $item)
	{
		$body[] = array(
			$item['dt_inclusao'],
			array($item['ds_usuario_inclusao'], 'text-align:left;'),
			array(nl2br($item['ds_acompanhamento']), 'text-align:justify;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_start_box('default_conceito_box', 'Coneceito da Tela');
			echo form_default_row('conceito', 'Acompanhamento:', 'Registro de tudo o que for relevante sobre a situação apresentada.');
		echo form_end_box('default_conceito_box');
		echo form_start_box('default_reclamacao_box', 'Reclamação');
			echo form_default_row('numero', 'Número:', $row['cd_reclamacao']);

			if(intval($row['cd_usuario_responsavel']) > 0)
			{
				echo form_default_row('dt_prazo_acao', 'Dt. Prazo Ação:', '<span class="label label-inverse">'.$row['dt_prazo_acao'].'</span>');
				
				if(trim($row['dt_prorrogacao_acao']) != '')
				{
					echo form_default_row('dt_prorrogacao_acao', 'Dt. Prorrogação Ação:', '<span class="label label-info">'.$row['dt_prorrogacao_acao'].'</span>');
				}

				echo form_default_row('dt_prazo', 'Dt. Prazo Classificação:', '<span class="label label-inverse">'.$row['dt_prazo'].'</span>');
				
				if(trim($row['dt_prorrogacao']) != '')
				{
					echo form_default_row('dt_prorrogacao', 'Dt. Prorrogação Classificação:', '<span class="label label-info">'.$row['dt_prorrogacao'].'</span>');
				}
			}
			
		echo form_end_box('default_reclamacao_box');
		echo form_open('ecrm/reclamacao/salvar_acompanhamento');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('numero', '', $row['numero']);
				echo form_default_hidden('ano', '', $row['ano']);
				echo form_default_hidden('tipo', '', $row['tipo']);
				
				echo form_default_textarea('ds_acompanhamento', 'Descrição: (*)', '', 'style="width:500px; height: 80px;"');
			echo form_end_box("default_box");
			echo form_command_bar_detail_start();
				echo button_save('Salvar');
			echo form_command_bar_detail_end();
		echo form_close();
		echo $grid->render();
	echo aba_end();
	$this->load->view('footer_interna');
?>