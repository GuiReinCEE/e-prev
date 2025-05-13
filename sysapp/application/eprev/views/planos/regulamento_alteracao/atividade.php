<?php
//echo "<pre>";print_r($cd_regulamento_alteracao);exit;

	set_title('Regulamento de Plano');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_regulamento_alteracao_unidade_basica', 'cd_gerencia')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/index') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/cadastro/'.$cd_regulamento_alteracao) ?>";
    }

    function ir_estrutura()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/estrutura/'.$cd_regulamento_alteracao) ?>";
    }

    function ir_artigo()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/estrutura_artigo/'.$cd_regulamento_alteracao) ?>";
    }

    function ir_quadro_comparativo()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/quadro_comparativo/'.$cd_regulamento_alteracao) ?>";
    }

    function ir_remissao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/remissao/'.$cd_regulamento_alteracao) ?>";
    }

    function ir_revisao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/revisao/'.$cd_regulamento_alteracao) ?>";
    }

    function cancelar()
    {
    	location.href = "<?= site_url('planos/regulamento_alteracao/estrutura_unidade/'.$cd_regulamento_alteracao.'/'.$cd_regulamento_alteracao_unidade_basica) ?>";
    }

    function ir_versao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/versao_anterior/'.$cd_regulamento_alteracao) ?>";
    }

    function ir_glossario()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/glossario/'.$cd_regulamento_alteracao) ?>";
    }

    function ir_atividades()
	{
		location.href = "<?= site_url('planos/regulamento_alteracao/atividades/'.$cd_regulamento_alteracao) ?>";
	}

    function encaminhar_atividade()
    {
    	var confirmacao = "Deseja encaminhar esta atividade?\n\n"+
				    	  "[OK] para Sim\n\n"+
				    	  "[Cancelar] para Não\n\n";

    	if(confirm(confirmacao))
    	{
			location.href = "<?= site_url('planos/regulamento_alteracao/encaminhar_atividade/'.$cd_regulamento_alteracao.'/'.$cd_regulamento_alteracao_unidade_basica.'/'.$row['cd_regulamento_alteracao_atividade']) ?>";
    	}
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
    $abas[] = array('aba_quadro_comparativo', 'Quadro Comparativo', FALSE, 'ir_quadro_comparativo();');
    $abas[] = array('aba_atividades', 'Atividades', FALSE, 'ir_atividades();');
    $abas[] = array('aba_atividade', 'Atividade', TRUE, 'location.reload();');
    $abas[] = array('aba_revisao', 'Revisão', FALSE, 'ir_revisao();');
    $abas[] = array('aba_versao', 'Versão Anterior', FALSE, 'ir_versao();');

    echo aba_start($abas);
        echo form_start_box('default_box', 'Regulamento');
            echo form_default_row('', 'Regulamento:', $regulamento_alteracao['ds_regulamento_tipo']);
            echo form_default_row('', 'CNPB:', $regulamento_alteracao['ds_cnpb']);
        echo form_end_box('default_box');
        echo form_start_box('default_unidade_basica_box', 'Unidade Básica');
        	echo form_default_row('', 'Estrutura:', '<span class="'.$artigo['ds_class_label'].'">'.$artigo['ds_estrutura'].'</span>');
            echo form_default_row('artigo', 'Artigo:', nl2br($artigo['ds_artigo']));
            if(intval($unidade_basica['cd_regulamento_alteracao_unidade_basica_pai']) > 0)
            {
				echo form_default_row('ds_regulamento_alteracao_unidade_basica_pai', 'Unidade Pai:', $unidade_basica['ds_regulamento_alteracao_unidade_basica']);
            }
            echo form_default_row('', 'Tipo:', $unidade_basica['ds_regulamento_alteracao_estrutura_tipo']);
            echo form_default_row('', 'Ordem:', $unidade_basica['nr_ordem']);
            echo form_default_row('ds_regulamento_alteracao_unidade_basica', 'Descrição:', nl2br($unidade_basica['ds_regulamento_alteracao_unidade_basica']));
        echo form_end_box('default_unidade_basica_box');
        echo form_open('planos/regulamento_alteracao/salvar_atividade_unidade_basica');
	        echo form_start_box('default_cadastro_box', 'Cadastro');
	            echo form_default_hidden('cd_regulamento_alteracao', '', $cd_regulamento_alteracao);
	            echo form_default_hidden('cd_regulamento_alteracao_unidade_basica', '', $cd_regulamento_alteracao_unidade_basica);
	            echo form_default_hidden('cd_regulamento_alteracao_atividade', '', $row['cd_regulamento_alteracao_atividade']);
	            echo form_default_checkbox_group('cd_gerencia','Gerência: (*)', $gerencia, $gerencia_atividade, 175);
	        echo form_end_box('default_cadastro_box');
	        echo form_command_bar_detail_start();
	        	if(trim($row['dt_envio']) == '')
	        	{
	        		echo button_save('Salvar');
	                if(intval($row['cd_regulamento_alteracao_atividade']) > 0)
	                {
	                	echo button_save('Encaminhar', 'encaminhar_atividade();', 'botao_verde');
	                }
	        	}
	        echo form_command_bar_detail_end();
        echo form_close(); 
    echo aba_end();
    echo br();
    $this->load->view('footer');
?>