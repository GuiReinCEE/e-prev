<?php
    set_title('Operacionalização de Nova Patrocinadora');
    $this->load->view('header');
?>
<script>
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            "Number",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "Number",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "DatetimeBR",
            "DatetimeBR",
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
        ob_resul.sort(0, false);
    }
					
    function ir_lista()
    {
        location.href = "<?= site_url('planos/nova_patrocinadora/patrocinadora') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('planos/nova_patrocinadora/patrocinadora_cadastro/'.$row['cd_nova_patrocinadora']) ?>";
    }

    $(function(){
        configure_result_table();
    });
</script>
<?php   
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');  
    $abas[] = array('aba_atividade', 'Atividade', TRUE, 'location.reload();');

    $head = array( 
        'Nº',
        'Atividade',
        'Descrição',
        'Ger. Resp.',
        'Responsável',
        'Substituto',
        'Prazo (dias)',
        'Ativ. Dependentes',
        'Observações',
        'Dt. Início',
        'Acompanhamento',
        'Dt. Encerramento',
        'Usuário'
    );

    $body = array();

    foreach($collection as $item)
    {
        $body[] = array(
            $item['nr_nova_patrocinadora_atividade'],
            array($item['ds_nova_patrocinadora_atividade'], 'text-align:left'),
            array(nl2br($item['ds_atividade']), 'text-align:justify'),
            $item['cd_gerencia'],
            $item['ds_usuario_responsavel'],
            $item['ds_usuario_substituto'],
            $item['nr_prazo'],
            array(implode(br(), $item['atividades_dependentes']), 'text-align:justify'),
            array(nl2br($item['ds_observacao']), 'text-align:justify'),
            $item['dt_envio_responsavel_ini'],
            array(nl2br(implode(br(), $item['acompanhamento'])), 'text-align:justify'),
            $item['dt_encerramento_prazo'],
            array($item['ds_usuario_encerramento'], 'text-align:left')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;
    
    echo aba_start($abas);
        echo form_start_box('default_box', 'Cadastro');
            echo form_default_hidden('cd_nova_patrocinadora', '', $row['cd_nova_patrocinadora']);   
            echo form_default_row('ds_nome_patrocinadora', 'Nome:', $row['ds_nome_patrocinadora'], 'style="width:300px;"');
            echo form_default_row('descricao', 'Plano:', $row['descricao']);
            echo form_default_row('dt_limite_aprovacao', 'Dt. Limite Aprovação Previc:', $row['dt_limite_aprovacao']);
            echo form_default_row('dt_inicio', 'Dt. Inicio Atividade:',$row['dt_inicio']);                
        echo form_end_box('default_box');        
        echo br();
    echo $grid->render();
    echo aba_end();

    $this->load->view('footer');
?>