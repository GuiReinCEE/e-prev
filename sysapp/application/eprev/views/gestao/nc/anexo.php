<?php
    set_title('Não Conformidade - Anexo');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array(), 'valida_arquivo(form)') ?>

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

    function ir_lista()
    {
        location.href = "<?= site_url('gestao/nc'); ?>";
    }

    function irNC()
    {
        location.href = "<?= site_url('gestao/nc/cadastro/'.$row['cd_nao_conformidade']); ?>";
    }                   

    function irAC()
    {
        location.href = "<?= site_url('gestao/nc/acao_corretiva/'.$row['cd_nao_conformidade']); ?>";
    }             

    function irAcompanha()
    {
         location.href = "<?= site_url('gestao/nc/acompanha/'.$row['cd_nao_conformidade']); ?>";
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

    function excluir(cd_nao_conformidade_anexo)
    {
        var confirmacao = 'Deseja excluir o anexo?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('gestao/nc/excluir_anexo/'.$row['cd_nao_conformidade']) ?>/"+ cd_nao_conformidade_anexo;
        }
    }

    $(function(){
        configure_result_table();
    });
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_nc', 'Não Conformidade', FALSE, "irNC();");
    if($row['fl_apresenta_ac'] == "S")
    {
        $abas[] = array('aba_ac', 'Ação Corretiva', FALSE, "irAC();");
    }
    $abas[] = array('aba_acompanha', 'Acompanhamento', FALSE, "irAcompanha();");
    $abas[] = array('aba_anexo', 'Anexo', TRUE, 'location.reload();');

    $head = array(
      'Dt. Inclusão',
      'Arquivo',
      'Usuário',
      ''
    );

    $body = array();

    foreach ($collection as $item)
    {
        $body[] = array(
            $item['dt_inclusao'],
            array(anchor(base_url().'up/nao_conformidade/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
            array($item['ds_usuario_inclusao'], 'text-align:left'),
            (intval($item['cd_usuario_inclusao']) == $this->session->userdata('codigo') ? '<a href="javascript:void(0);"" onclick="excluir('.$item['cd_nao_conformidade_anexo'].')">[excluir]</a>' : '')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->view_count = false;
    $grid->head = $head;
    $grid->body = $body;
    

    echo aba_start($abas);
        echo form_start_box('default_box', 'Cadastro');
            echo form_default_row('numero_cad_nc', "Número:", '<span class="label label-inverse">'.$row["numero_cad_nc"].'</span>');
            echo form_default_row('dt_cadastro', "Data:", '<span class="label">'.$row["dt_cadastro"].'</span>');
            echo form_default_row('dt_limite_apres_label', "Data limite para apresentação:", '<span class="label label-warning">'.$row["dt_limite_apres"].'</span>');
            echo form_default_hidden('cd_responsavel', 'Responsável:', $row, "style='width:100%;border: 0px;' readonly" );
            echo form_default_row('ds_responsavel', "Responsável:", '<span class="label">'.$row["ds_responsavel"].'</span>'); 
            echo form_default_row('ds_processo', "Processo:", '<span class="label">'.$row["ds_processo"].'</span>');

        echo form_end_box('default_box');
        echo form_open('gestao/nc/salvar_anexo');
            echo form_start_box('default_box', 'Anexo');
            echo form_default_hidden('cd_nao_conformidade', '', $row);
            echo form_default_hidden('cd_processo', '', $row);

                echo form_default_upload_multiplo('arquivo_m', 'Arquivo: (*)', 'nao_conformidade', 'validaArq');
                echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
            echo form_command_bar_detail_end();
        echo form_close();
        echo br();
        echo $grid->render();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>  