<?
	set_title('Planejamento Estratégico - Desdobramento');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nr_ordem', 'ds_planejamento_estrategico_desdobramento')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/index') ?>";
    }

    function ir_cadastro()
    {
        location = "<?= site_url('gestao/planejamento_estrategico/cadastro/'. $planejamento['cd_planejamento_estrategico']) ?>";
    }
    
    function ir_objetivo()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/objetivo/'. $planejamento['cd_planejamento_estrategico']) ?>";
    }

    function ir_programa()
    {
        location.href = "<?=  site_url('gestao/planejamento_estrategico/programa_projeto/'.$planejamento['cd_planejamento_estrategico'])?>";
    }    

    function salvar_desdobramento()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/salvar_desdobramento') ?>";
    }

    function cancelar()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/desdobramento/'. $planejamento['cd_planejamento_estrategico'])?>";
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "DateTimeBR"
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
        ob_resul.sort(0, false);
    }

    $(function(){ 
        configure_result_table();
    });
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro()');
    $abas[] = array('aba_objetivo', 'Objetivo', FALSE, 'ir_objetivo();');    
    $abas[] = array('aba_desdobramento', 'Desdobramento', TRUE, 'location.reload();');
    $abas[] = array('aba_programa', 'Programa/Projeto', FALSE, 'ir_programa();');
  
    $head = array(
        'Ordem',
        'Descrição',
        'Dt. Inclusão'
    );

    $body = array();

    foreach ($collection as $item)
    {   
        $body[] = array(
            array($item['nr_ordem'],'text-align:left'),
            array(anchor('gestao/planejamento_estrategico/desdobramento/'.$item['cd_planejamento_estrategico'].'/'.$item['value'],$item['text']), 'text-align:left;'),
            $item['dt_inclusao']
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

        echo form_open('gestao/planejamento_estrategico/salvar_desdobramento');
            echo form_start_box('default_box', 'Desdobramento');
            echo form_default_hidden('cd_planejamento_estrategico_desdobramento', '', $row['cd_planejamento_estrategico_desdobramento']);
            echo form_default_hidden('cd_planejamento_estrategico', '', $planejamento['cd_planejamento_estrategico']);
            echo form_default_integer('nr_ordem', 'Ordem: (*)', $row['nr_ordem']);
            echo form_default_text('ds_planejamento_estrategico_desdobramento', 'Descrição: (*)', $row['ds_planejamento_estrategico_desdobramento'], 'style="width:450px; "');
            echo form_default_checkbox_group('objetivo', 'Objetivo:', $objetivo, $objetivo_checked, 100, 350);
        echo form_end_box('default_box');

	    	echo form_command_bar_detail_start();
                echo button_save('Salvar'); 
                if(intval($row['cd_planejamento_estrategico_desdobramento']) > 0)
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