<?php
	set_title('Meus Treinamentos - Documento');
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
        location.href = "<?= site_url('servico/meus_treinamentos_diretoria/index') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('servico/meus_treinamento_diretoria_conselhos/anexo/'.$row['cd_treinamento_diretoria_conselhos_item']) ?>"
    }

    function excluir(cd_treinamento_diretoria_conselhos_documento)
        {
            var confirmacao = 'Deseja excluir o documento?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';

            if(confirm(confirmacao))
            {
                location.href = "<?= site_url('servico/meus_treinamentos_diretoria/excluir_documento/'.$row['cd_treinamento_diretoria_conselhos_item']) ?>/"+ cd_treinamento_diretoria_conselhos_documento;
            }
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

    $(function(){
        configure_result_table();
    });

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	//$abas[] = array('aba_anexo', 'Anexo', FALSE , 'ir_cadastro();'); 
    $abas[] = array('aba_documento', 'Documento', TRUE, 'location.reload();');

    $head = array(
      'Dt. Inclusão',
      'Documento',
      'Usuário',
      ''
    );

    $body = array();

    foreach ($collection as $item)
    {
        $body[] = array(
            $item['dt_inclusao'],
            array(anchor(base_url().'up/meus_treinamentos/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
            array($item['ds_usuario_inclusao'], 'text-align:left'),
            (intval($item['cd_usuario_inclusao']) == $this->session->userdata('codigo') ? '<a href="javascript:void(0);"" onclick="excluir('.$item['cd_treinamento_diretoria_conselhos_documento'].')">[excluir]</a>' : '')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->view_count = false;
    $grid->head = $head;
    $grid->body = $body;

    echo aba_start($abas);
        echo form_open('servico/meus_treinamentos_diretoria/salvar_documento');
            echo form_start_box('default_box', 'Cadastro'); 
                echo form_default_hidden('cd_treinamento_diretoria_conselhos_item', '', $row['cd_treinamento_diretoria_conselhos_item']);
                echo form_default_row('', 'Numero:', $row['numero']);
                echo form_default_row('', 'Nome:', $row['ds_nome']);
                echo form_default_row('', 'Promotor:', $row['ds_promotor']);
                /*echo form_default_row('', 'Tipo:', $row['ds_treinamento_diretoria_conselhos_tipo']);*/
                echo form_default_row('', 'Dt. Inicio:', $row['dt_inicio']);
                echo form_default_row('', 'Dt. Final:', $row['dt_final']);
            echo form_end_box('default_box');    

        echo form_open('servico/meus_treinamentos_diretoria/salvar_documento');
            echo form_start_box('default_box', 'Documento');
            echo form_default_hidden('cd_treinamento_diretoria_conselhos_item', '', $row['cd_treinamento_diretoria_conselhos_item']);
            echo form_default_hidden('cd_treinamento_diretoria_conselhos', '', $row['cd_treinamento_diretoria_conselhos']);

                echo form_default_upload_multiplo('arquivo_m', 'Arquivo: (*)', 'meus_treinamentos', 'validaArq');
                echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
            echo form_command_bar_detail_end();
        echo form_close();
        echo br();
        echo $grid->render();
        echo br(2);
    echo aba_end();

	$this->load->view('footer');
?>