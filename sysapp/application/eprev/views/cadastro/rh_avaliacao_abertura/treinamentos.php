<?php
	set_title('Sistema de Avaliação - Treinamentos');
	$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura/cadastro/'.$row['cd_avaliacao']) ?>";
	}	

	function ir_avaliacao()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura/avaliacao/'.$row['cd_avaliacao']) ?>";
	}	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_avaliacao', 'Avaliações', FALSE, 'ir_avaliacao();');
	$abas[] = array('aba_treinamentos', 'Treinamentos', TRUE, 'location.reload();');

	 $head = array( 
        'Número',
        'Nome',
        'Promotor',
        'Dt. Início',
        'Dt. Final',
        'Tipo',
        'Carga<br/>Horária(h)'
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            $item['numero'],
            array($item['nome'], 'text-align:left'),
            array($item['promotor'],'text-align:left'),
            $item['dt_inicio'],
            $item['dt_final'],
            array($item['ds_treinamento_colaborador_tipo'],'text-align:left'),
            $item['carga_horaria']
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
    $grid->view_count = TRUE;

	echo aba_start($abas);
		echo form_start_box('default_pdi_box', 'Cadastro PDI');
			echo form_default_hidden('cd_avaliacao', '', $row);	
			echo form_default_hidden('cd_avaliacao_usuario', '', $row);	
			echo form_default_hidden('cd_avaliacao_usuario_avaliacao', '', $row);	
			echo form_default_hidden('cd_usuario_avaliador', '', $row);	
			echo form_default_hidden('tp_avaliacao', '', $row);	
			echo form_default_hidden('fl_encerramento', '', 'N');	
			echo form_default_row('', 'Período:', $row['nr_ano_avaliacao']);
			echo form_default_row('', 'Avaliado:', $row['ds_avaliado']);
			echo form_default_row('', 'Admissão:', $row['dt_admissao']);
			echo form_default_row('', 'Cargo/Área de Atuação:', $row['ds_cargo_area_atuacao']);
			echo form_default_row('', 'Avaliador:', $row['ds_avaliador']);
		echo form_end_box('default_pdi_box');
		echo br();
		echo $grid->render();
		echo br();
	echo aba_end();
	$this->load->view('footer');
?>