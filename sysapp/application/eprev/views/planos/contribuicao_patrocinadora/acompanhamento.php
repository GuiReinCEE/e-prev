<?php
    set_title('Contribuição Patrocinadora');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_descricao')) ?>

    function ir_lista()
    {
        location.href = "<?= site_url('planos/contribuicao_patrocinadora/index') ?>";
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            "DateTimeBR", 
            "CaseInsensitiveString", 
            "CaseInsensitiveString", 
            null
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

    function excluir(cd_contirbuicao_patroc_acompanhamento)
    {
        var confirmacao = 'Deseja excluir o pendencia?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('planos/contribuicao_patrocinadora/excluir_acompanhamento/'.$row['cd_contribuicao_patroc']) ?>/" + cd_contirbuicao_patroc_acompanhamento;
        }
    }

    $(function(){
        configure_result_table();
    });    
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_acompanhamento', 'Acompanhamento', TRUE, 'location.reload();');

    $head = array(
        'Dt. Inclusão',
        'Descrição',
        'Usuário',
        ''
    );

    $body = array();

    foreach ($collection as $item)
    {
        $body[] = array(
            $item['dt_inclusao'],
            array($item['ds_descricao'], 'text-align:left'),
            array($item['ds_usuario_inclusao'], 'text-align:left'),
            (intval($item['cd_usuario_inclusao']) == $this->session->userdata('codigo') ? '<a href="javascript:void(0);"" onclick="excluir('.$item['cd_contribuicao_patroc_acompanhamento'].')">[excluir]</a>' : '')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->view_count = false;
    $grid->head = $head;
    $grid->body = $body;  

    echo aba_start($abas);
        echo form_open('planos/contribuicao_patrocinadora/salvar_acompanhamento');
            echo form_start_box('default_box', 'Cadastro'); 
                echo form_default_hidden('cd_contribuicao_patroc', '', $row['cd_contribuicao_patroc']);
                echo form_default_row('', 'RE:', $row['re']);
                echo form_default_row('', 'Nome:', $row['nome']);
                echo form_default_row('', 'Email:', $row['ds_email']);
                echo form_default_row('', 'Telefone:', $row['ds_telefone']);
            echo form_end_box('default_box');    
            
            echo form_start_box('default_sistema_box', ' Cadastro');
                echo form_default_textarea('ds_descricao', 'Descrição: (*)', '', 'style="height:80px;"');
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
                echo button_save('Salvar'); 
            echo form_command_bar_detail_end();

        echo form_close();
        echo br();
        echo $grid->render();
        echo br(2);     
    echo aba_end();

    $this->load->view('footer_interna');
?>
               