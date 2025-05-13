<?
	set_title('Planejamento Estratégico - Programas/Projetos');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nr_ordem', 'ds_programa_projeto', 'cd_gerencia_responsavel', 'cd_planejamento_estrategico_desdobramento')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/index') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/cadastro/'.$planejamento['cd_planejamento_estrategico']) ?>";
    }
    
    function ir_objetivo()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/objetivo/'.$planejamento['cd_planejamento_estrategico']) ?>";
    } 

    function ir_desdobramento()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/desdobramento/'.$planejamento['cd_planejamento_estrategico']) ?>";
    }   
    
    function cancelar()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/programa_projeto/'. $planejamento['cd_planejamento_estrategico'])?>";
    }

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro()');
    $abas[] = array('aba_objetivo', 'Objetivo', FALSE, 'ir_objetivo();');
    $abas[] = array('aba_desdobramento', 'Desdobramento', FALSE, 'ir_desdobramento();');
    $abas[] = array('aba_programa', 'Programa/Projeto', TRUE, 'location.reload();');
  
    $head = array(
        'Ordem',
        'Descrição',
        'Gerência Resp',
        'Desdobramento',
        'Dt. Inclusão',
        ''        
    );

    $body = array();

    foreach ($collection as $item)
    {   
        $body[] = array(
            array($item['nr_ordem_desdobramento'].'.'.$item['nr_ordem'], 'text-align:left'),
            array(anchor('gestao/planejamento_estrategico/programa_projeto/'.$item['cd_planejamento_estrategico'].'/'.$item['cd_programa_projeto'],$item['ds_programa_projeto']), 'text-align:left;'),
            $item['cd_gerencia_responsavel'],
            array($item['ds_planejamento_estrategico_desdobramento'], 'text-align:left'),
            $item['dt_inclusao'],
            anchor('gestao/planejamento_estrategico/cronograma/'.$item['cd_programa_projeto'], '[cronograma]')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->view_count = false;
    $grid->head = $head;
    $grid->body = $body;

    echo aba_start($abas);

        echo form_start_box('default_box','Planejamento'); 
            echo form_default_row('', 'Diretriz Fundamental:', $planejamento['ds_diretriz_fundamental']);
            echo form_default_row('', 'Ano inicial:', $planejamento['nr_ano_inicial']);
            echo form_default_row('', 'Ano final:', $planejamento['nr_ano_final']);
        echo form_end_box('default_box');

        echo form_open('gestao/planejamento_estrategico/salvar_projeto');
            echo form_start_box('default_box', 'Programa');
            echo form_default_hidden('cd_programa_projeto', '', $row['cd_programa_projeto']);
            echo form_default_hidden('cd_planejamento_estrategico', '', $row['cd_planejamento_estrategico']);
            echo form_default_integer('nr_ordem', 'Ordem: (*)', $row['nr_ordem']);
            echo form_default_text('ds_programa_projeto', 'Descrição: (*)', $row['ds_programa_projeto'], 'style="width:450px;"');
            echo form_default_dropdown('cd_gerencia_responsavel', 'Gerência Resp.: (*)', $gerencia, $row['cd_gerencia_responsavel']);
            echo form_default_dropdown('cd_planejamento_estrategico_desdobramento', 'Desdobramento: (*)', $desdobramento, $row['cd_planejamento_estrategico_desdobramento']);
        echo form_end_box('default_box');

	    	echo form_command_bar_detail_start();
                echo button_save('Salvar'); 
                if(intval($row['cd_programa_projeto']) > 0)
                {
                    echo button_save('Cancelar', 'cancelar()', 'botao_disabled');
                }
            echo form_command_bar_detail_end();		
    	echo form_close();
        echo br();
        echo $grid->render();
    echo aba_end();

    $this->load->view('footer_interna');
?>