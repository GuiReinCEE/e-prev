<?php
	set_title('Planejamento Estratégico - Cadastro');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('Diretriz Fundamental', 'nr_ano_inicial', 'nr_ano_final')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/index') ?>";
    }
    
    function ir_objetivo()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/objetivo/'.$row['cd_planejamento_estrategico']) ?>";
    }

    function ir_desdobramento()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/desdobramento/'.$row['cd_planejamento_estrategico']) ?>";
    }

    function ir_programa()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/programa_projeto/'.$row['cd_planejamento_estrategico']) ?>";
    }
    
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
    if($row['cd_planejamento_estrategico'] > 0)
    {
        $abas[] = array('aba_objetivo', 'Objetivo', FALSE, 'ir_objetivo();');
        $abas[] = array('aba_desdobramento', 'Desdobramento', FALSE, 'ir_desdobramento();');     
        $abas[] = array('aba_programa', 'Programa/Projeto', FALSE, 'ir_programa();');
    }

    echo aba_start($abas);
    	echo form_open('gestao/planejamento_estrategico/salvar');
    		echo form_start_box('default_box', 'Cadastro'); 
	    		echo form_default_hidden('cd_planejamento_estrategico', '', $row['cd_planejamento_estrategico']);
                echo form_default_textarea('ds_diretriz_fundamental', 'Diretriz Fundamental: (*)', $row['ds_diretriz_fundamental'], 'style="width:450px; height:100px ;"');
                echo form_default_integer('nr_ano_inicial', 'Ano inicial: (*)', $row['nr_ano_inicial']);
                echo form_default_integer('nr_ano_final', 'Ano final: (*)', $row['nr_ano_final']);      
                echo form_default_upload_iframe('arquivo', 'planejamento_estrategico', 'Arquivo: (*)', array($row['arquivo'], $row['arquivo_nome']), 'planejamento_estrategico');                          
                echo form_default_upload_iframe('arquivo_plano_execucao', 'planejamento_estrategico', 'Arquivo Ações:', array($row['arquivo_plano_execucao'], $row['arquivo_plano_execucao_nome']), 'planejamento_estrategico');                          
	    	echo form_end_box('default_box');
	    	echo form_command_bar_detail_start();
                echo button_save('Salvar'); 
            echo form_command_bar_detail_end();		
    	echo form_close();
    echo aba_end();

    $this->load->view('footer_interna');
?>