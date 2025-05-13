<?
	set_title('Planejamento Estratégico - Cronograma');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nr_ano', 'cd_pendencia_gestao')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/index') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/cadastro/'. $programa['cd_planejamento_estrategico']) ?>";
    }
    
    function ir_objetivo()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/objetivo/'.$programa['cd_planejamento_estrategico']) ?>";
    }
    
    function ir_desdobramento()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/desdobramento/'.$programa['cd_planejamento_estrategico']) ?>";
    }

    function ir_programa()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/programa_projeto/'.$programa['cd_planejamento_estrategico']) ?>";
    }

    function salvar_cronograma()
    {
        location.href = "<?= site_url('gestao/planejamento_estrategico/salvar_cronograma') ?>";
    }

    function cancelar()
    {
        if(confirm('Deseja CANCELAR o cronograma?'))
        {
            location.href = "<?= site_url('gestao/planejamento_estrategico/cronograma/'.$row['cd_programa_projeto'])?>";
        }
    }


    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'NumberFloatBR',
            'DateTimeBR'

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
        ob_resul.sort(1, true);
    } 

    $(function(){
        configure_result_table();
    });
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro()');
    $abas[] = array('aba_objetivo', 'Objetivo', FALSE, 'ir_objetivo();');
    $abas[] = array('aba_desdobramento', 'Desdobramento', FALSE, 'ir_desdobramento();');
    $abas[] = array('aba_programa', 'Programa/Projeto', FALSE, 'ir_programa();');
    $abas[] = array('aba_cronograma', 'Cronograma', TRUE, 'location.reload();');
    
    $head = array(
        'Descrição',
        'Arquivo',
        'Ano',
        'Dt. Inclusão'
    );

    $body = array();

    foreach ($collection as $item)
    {   
        $body[] = array(
            array(anchor('gestao/planejamento_estrategico/cronograma/'.$item['cd_programa_projeto'].'/'.$item['cd_programa_projeto_arquivo'], $item['ds_programa_projeto_arquivo']), 'text-align:left;'),
            array(anchor(base_url().'up/planejamento_estrategico/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'), 
            $item['nr_ano'],         
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
            echo form_default_row('', 'Diretriz Fundamental:', $programa['ds_diretriz_fundamental']);
            echo form_default_row('', 'Ano inicial:', $programa['nr_ano_inicial']);
            echo form_default_row('', 'Ano final:', $programa['nr_ano_final']);
        echo form_end_box('default_box');

        echo form_start_box('default_box','Programa/Projeto'); 
            echo form_default_row('', 'Programa/Projeto:', $programa['ds_programa_projeto']);
            echo form_default_row('', 'Desdobramento:', $programa['ds_planejamento_estrategico_desdobramento']);
            echo form_default_row('', 'Objetivo:', $programa['ds_planejamento_estrategico_objetivo']);
        echo form_end_box('default_box');

        echo form_open('gestao/planejamento_estrategico/salvar_cronograma');
            echo form_start_box('default_box', 'Cronograma');
                echo form_default_hidden('cd_programa_projeto_arquivo', '', $row['cd_programa_projeto_arquivo']);
                echo form_default_hidden('cd_programa_projeto', '', $row['cd_programa_projeto']);

                if(intval($row['cd_programa_projeto_arquivo']) == 0 AND $drop != null)
                {
                    echo form_default_dropdown('nr_ano', 'Ano: (*)', $drop, $row['nr_ano']);  
                }
                else
                {
                    echo form_default_hidden('nr_ano', '', $row['nr_ano']);
                    if(intval($row['nr_ano']) > 0)
                    {
                          echo form_default_row('', 'Ano: (*)', $row['nr_ano']);
                    }
                }
				
				echo form_default_integer('cd_pendencia_gestao', 'Cód Pendência: (*)', $row['cd_pendencia_gestao']);
				
                #echo form_default_text('ds_programa_projeto_arquivo', 'Descrição: (*)', $row['ds_programa_projeto_arquivo'], 'style="width:450px;"'); 
                #echo form_default_upload_iframe('arquivo', 'planejamento_estrategico', 'Arquivo: ', array($row['arquivo'], $row['arquivo_nome']), 'planejamento_estrategico');                          
				
				
            echo form_end_box('default_box');

        	echo form_command_bar_detail_start();
                echo button_save('Salvar'); 
                if(intval($row['cd_programa_projeto_arquivo']) > 0)
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