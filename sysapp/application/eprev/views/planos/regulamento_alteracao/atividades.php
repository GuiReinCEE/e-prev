<?php
	set_title('Regulamento de Plano');
	$this->load->view('header');
?>
<script>
	function ir_lista()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/index') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/cadastro/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_estrutura()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/estrutura/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_artigo()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/estrutura_artigo/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_remissao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/remissao/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_quadro()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/quadro_comparativo/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_versao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/versao_anterior/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_revisao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/revisao/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_glossario()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/glossario/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_glossario', 'Glossário', FALSE, 'ir_glossario();');
    $abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
    $abas[] = array('aba_artigo', 'Artigos', FALSE, 'ir_artigo();');
    $abas[] = array('aba_remissao', 'Remissão', FALSE, 'ir_remissao();');
    $abas[] = array('aba_quadro_comparativo', 'Quadro Comparativo', FALSE, 'ir_quadro();');
    $abas[] = array('aba_atividades', 'Atividades', TRUE, 'location.reload();');
    $abas[] = array('aba_revisao', 'Revisão', FALSE, 'ir_revisao();');
    $abas[] = array('aba_versao', 'Versão Anterior', FALSE, 'ir_versao();');

    $head = array(
    	'Atividade',
    	'Datas',
    	'Controle'
    );

    $head_2 = array(
        'Gerência',
        'Respondente',
        'Dt. Prevista',
        'Dt. Implementação',
        'Pertinência',
        ''
    );

    $body = array();

    $this->load->helper('grid');
    $grid_2 = new grid();
    $grid_2->view_count = FALSE;
    $grid_2->view_data  = FALSE;

    foreach ($collection as $key => $item) 
    {
    	$body_2 = array();

    	foreach ($item['atividade'] as $key2 => $item2) 
    	{
    		$body_2[] = array(
    			$item2['cd_gerencia'],
    			$item2['ds_usuario_respondente'],
    			$item2['dt_prevista'],
    			$item2['dt_implementacao'],
    			'<span class="'.$item2['ds_class_tipo'].'">'.$item2['ds_regulamento_alteracao_atividade_tipo'].'</span>',
    			anchor('planos/regulamento_alteracao/acompanhamento/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$item2['cd_regulamento_alteracao_atividade_gerencia'], '[acompanhamentos]')
    		);
    	}

    	$grid_2->head = $head_2;
    	$grid_2->body = $body_2;

    	$dt_referencia = '';

    	if(trim($item['dt_prevista_ref']) != '')
    	{
    		$dt_referencia = '<span class="label label-inverse"> Dt. Prevista: '.$item['dt_prevista_ref'].'</span>';
    	}

    	if(trim($item['dt_implementecao_ref']) != '')
    	{
    		$dt_referencia .= '<br><br><span class="label label-warning"> Dt. Implementação: '.$item['dt_implementecao_ref'].'</span>';
    	}

    	$body[] = array(
    		array(nl2br($item['ds_artigo']), 'text-align:justify;'),
    		$dt_referencia,
    		(count($item['atividade']) > 0 ? $grid_2->render() : '')
    	);
    }

    $grid = new grid();
    $grid->view_count = TRUE;
    $grid->head = $head;
    $grid->body = $body;

    echo aba_start($abas);
    	echo form_start_box('default_box', 'Regulamento');
			echo form_default_row('', 'Regulamento:', $regulamento_alteracao['ds_regulamento_tipo']);
			echo form_default_row('', 'CNPB:', $regulamento_alteracao['ds_cnpb']);
        echo form_end_box('default_box');
    	echo br();
        echo $grid->render();
        echo br();
    echo aba_end();
    $this->load->view('footer');
?>