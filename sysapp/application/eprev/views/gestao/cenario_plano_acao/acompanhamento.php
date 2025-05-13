<?php
    set_title('Cenário Plano de Ação');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_cenario_plano_acao_acompanhamento')) ?>

    function ir_lista()
    {
        location.href = "<?= site_url('gestao/cenario_plano_acao/index/') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('gestao/cenario_plano_acao/cadastro/'.$row['cd_cenario_plano_acao']) ?>";
    }

    function ir_anexo()
    {
        location.href = "<?= site_url('gestao/cenario_plano_acao/anexo/'.$row['cd_cenario_plano_acao']) ?>";
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

    function excluir_acompanhamento(cd_cenario_plano_acao_acompanhamento)
    {
        var confirmacao = 'Deseja excluir o acompanhamento?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/cenario_plano_acao/excluir_acompanhamento/'.$row['cd_cenario_plano_acao']) ?>/" + cd_cenario_plano_acao_acompanhamento;
        }
    }

    $(function(){
        configure_result_table();
    });    
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_acompanhamento', 'Acompanhamento', TRUE, 'location.reload();');
    $abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

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
            array(nl2br($item['ds_cenario_plano_acao_acompanhamento']), 'text-align:justify'),
            array($item['ds_usuario_inclusao'], 'text-align:left'),
            (intval($this->session->userdata('codigo')) == intval($item['cd_usuario_inclusao']) ? '<a href="javascript:void(0);" onclick="excluir_acompanhamento('.$item['cd_cenario_plano_acao_acompanhamento'].')">[excluir]</a>' : '')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->view_count = false;
    $grid->head = $head;
    $grid->body = $body;  

    echo aba_start($abas);
        echo form_open('gestao/cenario_plano_acao/salvar_acompanhamento');
            echo form_start_box('default_box', 'Cenário Plano de Ação'); 
                echo form_default_hidden('cd_cenario_plano_acao', '', $row['cd_cenario_plano_acao']);
                echo form_default_row('', 'Cénario:', $row['cd_cenario'].'-'.$row['titulo']);
                echo form_default_row('link', 'Link:', '<a href="'.base_url('index.php/ecrm/informativo_cenario_legal/legislacao/'.$row['cd_edicao'].'/'.$row['cd_cenario']).'" target="_blank" style="font-weight:bold;">[Ver o Cenário Legal]</a>');
                echo form_default_row('', 'Dt. Verificação da Eficácia: ', $row['dt_verificacao_eficacia']);
                echo form_default_row('', 'Dt. Validação da Eficácia: ', $row['dt_validacao_eficacia']);
                echo form_default_row('', 'Dt. Prazo Previsto: ', $row['dt_prazo_previsto']);
            echo form_end_box('default_box');    
            
            echo form_start_box('default_sistema_box', ' Cadastro');
                echo form_default_textarea('ds_cenario_plano_acao_acompanhamento', 'Descrição: (*)', '', 'style="height:80px;"');
            echo form_end_box('default_sistema_box');
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