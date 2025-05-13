<?php
	set_title('Regulamento de Plano');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_regulamento_alteracao_atividade_gerencia', 'ds_regulamento_alteracao_atividade_acompanhamento')) ?>

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

    function ir_atividades()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/atividades/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function cancelar()
    {
    	 location.href = "<?= site_url('planos/regulamento_alteracao/acompanhamento/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$atividade_gerencia['cd_regulamento_alteracao_atividade']) ?>";
    }
</script>
<style>
    #ds_regulamento_alteracao_unidade_basica_item, #ds_regulamento_alteracao_unidade_basica_pai_item, #artigo_item
    {
        white-space:normal !important;
    }
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_glossario', 'Glossário', FALSE, 'ir_glossario();');
    $abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
    $abas[] = array('aba_artigo', 'Artigos', FALSE, 'ir_artigo();');
    $abas[] = array('aba_remissao', 'Remissão', FALSE, 'ir_remissao();');
    $abas[] = array('aba_quadro_comparativo', 'Quadro Comparativo', FALSE, 'ir_quadro();');
    $abas[] = array('aba_atividades', 'Atividades', FALSE, 'ir_atividades();');
    $abas[] = array('aba_acompanhamento', 'Acompanhamentos', TRUE, 'location.reload();');
    $abas[] = array('aba_revisao', 'Revisão', FALSE, 'ir_revisao();');
    $abas[] = array('aba_versao', 'Versão Anterior', FALSE, 'ir_versao();');

    $head = array(
        'Descrição',
        'Dt. Inclusão',
        'Usuário'
    );

    $body = array();

    foreach ($collection as $key => $item)
    {
    	$link = $item['ds_regulamento_alteracao_atividade_acompanhamento'];

    	if(trim($atividade_gerencia['dt_implementacao']) == '' AND intval($item['cd_usuario_inclusao']) == intval($cd_usuario))
    	{
    		$link = anchor('planos/regulamento_alteracao/acompanhamento/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$atividade_gerencia['cd_regulamento_alteracao_atividade_gerencia'].'/'.$item['cd_regulamento_alteracao_atividade_acompanhamento'], $item['ds_regulamento_alteracao_atividade_acompanhamento']);
    	}

    	$body[] = array(
    		array($link, 'text-align:justify'),
	    	$item['dt_inclusao'],
	    	$item['ds_usuario']
    	);
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

	echo aba_start($abas);
        echo form_start_box('default_box', 'Regulamento');
            echo form_default_row('', 'Regulamento:', $regulamento_alteracao['ds_regulamento_tipo']);
            echo form_default_row('', 'CNPB:', $regulamento_alteracao['ds_cnpb']);
        echo form_end_box('default_box');
        echo form_start_box('default_unidade_basica_box', 'Unidade Básica');
        	echo form_default_row('', 'Estrutura:', '<span class="'.$unidade_basica['ds_class_label'].'">'.$unidade_basica['ds_estrutura'].'</span>');
            echo form_default_row('artigo', 'Artigo:', nl2br($unidade_basica['ds_artigo']));
            if(intval($unidade_basica['cd_regulamento_alteracao_unidade_basica_pai']) > 0)
            {
				echo form_default_row('ds_regulamento_alteracao_unidade_basica_pai', 'Unidade Pai:', $unidade_basica_pai['ds_regulamento_alteracao_unidade_basica']);
            }
            echo form_default_row('ds_regulamento_alteracao_unidade_basica', 'Descrição:', nl2br($unidade_basica['ds_regulamento_alteracao_unidade_basica']));
        echo form_end_box('default_unidade_basica_box');
    	if(trim($atividade_gerencia['dt_implementacao']) == '')
        {
		    echo form_open('planos/regulamento_alteracao/salvar_acompanhamento');
		    	echo form_start_box('default_cadastro_box', 'Cadastro');
		    		echo form_default_hidden('cd_regulamento_alteracao', '', $regulamento_alteracao['cd_regulamento_alteracao']);
		    		echo form_default_hidden('cd_regulamento_alteracao_atividade_gerencia', '', $atividade_gerencia['cd_regulamento_alteracao_atividade_gerencia']);
		    		echo form_default_hidden('cd_regulamento_alteracao_atividade_acompanhamento', '', $row['cd_regulamento_alteracao_atividade_acompanhamento']);
		    		echo form_default_textarea('ds_regulamento_alteracao_atividade_acompanhamento', 'Descrição: (*)', $row);
		        echo form_end_box('default_cadastro_box');
					echo form_command_bar_detail_start();
						echo button_save('Salvar');
						if(intval($row['cd_regulamento_alteracao_atividade_acompanhamento']) > 0)
						{
							echo button_save('Cancelar', 'cancelar();', 'botao_disabled');
						}
					echo form_command_bar_detail_end();
			echo form_close(); 
		}
        echo br();
        echo $grid->render();
        echo br(2);
    echo aba_end();
    $this->load->view('footer');
?>