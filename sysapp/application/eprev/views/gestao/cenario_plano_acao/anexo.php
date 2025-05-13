<?php
    set_title('Cenário Plano de Ação');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array(), 'valida_arquivo(form)') ?>
    
    function ir_lista()
    {
        location.href = "<?= site_url('gestao/cenario_plano_acao') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('gestao/cenario_plano_acao/cadastro/'.$row['cd_cenario_plano_acao']) ?>";
    }

    function ir_acompanhamento()
    {
        location.href = "<?= site_url('gestao/cenario_plano_acao/acompanhamento/'.$row['cd_cenario_plano_acao']) ?>";
    }

    function valida_arquivo(form)
    {
        if($("#arquivo").val() == "" && $("#arquivo_nome").val() == "")
        {
            alert("Nenhum arquivo foi anexado.");
            return false;
        }
        else
        {
            if(confirm("Salvar?"))
            {
                form.submit();
            }
        }
    }
    
    function validaArq(enviado, nao_enviado, arquivo)
    {
        $("form").submit();
    }   
    
    function load()
    {
        $("#result_div").html("<?= loader_html() ?>");

        $.post("<?= site_url('gestao/cenario_plano_acao/anexo_listar/'.$row['cd_cenario_plano_acao']) ?>",
        {},
        function(data)
        {
            $("#result_div").html(data);
            configure_result_table();
        });
    }
    
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'DateTimeBR', 
            'CaseInsensitiveString', 
            'CaseInsensitiveString', 
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
    
    function excluir_anexo(cd_cenario_plano_acao_anexo)
    {
        var confirmacao = 'Deseja excluir o anexo?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('gestao/cenario_plano_acao/anexo_excluir/'.$row['cd_cenario_plano_acao']) ?>/"+ cd_cenario_plano_acao_anexo;
        }
    }

    $(function(){
        load();
    });
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
    $abas[] = array('aba_anexo', 'Anexo', TRUE, 'location.reload();');

    $head = array( 
        'Dt Inclusão',
        'Arquivo',
        'Usuário',
        ''
    );

    $body = array();

    foreach($collection as $item )
    {   
        $body[] = array(
            $item['dt_inclusao'],
            array(anchor(base_url().'up/cenario_plano_acao/'.$item['arquivo'], $item['arquivo_nome'] , array('target' => '_blank')), 'text-align:left;'),
            array($item['ds_usuario_inclusao'], 'text-align:left'),
            (intval($this->session->userdata('codigo')) == intval($item['cd_usuario_inclusao']) ? '<a href="javascript:void(0);" onclick="excluir_anexo('.$item['cd_cenario_plano_acao_anexo'].')">[excluir]</a>' : '')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;
    $grid->view_count = false;

    echo aba_start($abas);
        echo form_open('gestao/cenario_plano_acao/anexo_salvar');
            echo form_start_box('default_box', 'Cenário Plano de Ação');			
                echo form_default_hidden('cd_cenario_plano_acao', '', $row['cd_cenario_plano_acao']);
                echo form_default_row('', 'Cénario:', $row['cd_cenario'].'-'.$row['titulo']);
                echo form_default_row('link', 'Link:', '<a href="'.base_url('index.php/ecrm/informativo_cenario_legal/legislacao/'.$row['cd_edicao'].'/'.$row['cd_cenario']).'" target="_blank" style="font-weight:bold;">[Ver o Cenário Legal]</a>');
                echo form_default_row('', 'Dt. Verificação da Eficácia: ', $row['dt_verificacao_eficacia']);
                echo form_default_row('', 'Dt. Validação da Eficácia: ', $row['dt_validacao_eficacia']);
                echo form_default_row('', 'Dt. Prazo Previsto: ', $row['dt_prazo_previsto']);
            echo form_end_box('default_box');

            echo form_start_box('default_anexo_box', 'Cadastro'); 
                echo form_default_upload_multiplo('arquivo_m', 'Arquivo:*', 'cenario_plano_acao', 'validaArq');
                echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
            echo form_end_box('default_anexo_box');
            echo form_command_bar_detail_start();    
            echo form_command_bar_detail_end();
        echo form_close();
        echo br();
        echo $grid->render();   
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?> 