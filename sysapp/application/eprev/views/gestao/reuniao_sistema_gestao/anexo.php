<?php
set_title('Reunião Sistema de Gestão - Anexo');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array(), 'valida_arquivo(form)') ?>
    
    function ir_lista()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao/cadastro/'.$row['cd_reuniao_sistema_gestao']) ?>";
    }

    function ir_indicador()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao/indicador/'.$row['cd_reuniao_sistema_gestao']) ?>";
    }

    function ir_cadastro_ordem()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao/cadastro_ordem/'.$row['cd_reuniao_sistema_gestao']) ?>";
    }

    function ir_processo()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao/processo/'.$row['cd_reuniao_sistema_gestao']) ?>";
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

        $.post("<?= site_url('gestao/reuniao_sistema_gestao/anexo_listar/'.$row['cd_reuniao_sistema_gestao']) ?>",
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
    
    function excluir(cd_reuniao_sistema_gestao_anexo)
    {
        if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
        {
            location.href = "<?= site_url('gestao/reuniao_sistema_gestao/anexo_excluir/'.$row['cd_reuniao_sistema_gestao']) ?>/"+ cd_reuniao_sistema_gestao_anexo;
        }
    }

    $(function(){
        load();
    });
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_nc', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_processo', 'Processo', FALSE, 'ir_processo();');
    $abas[] = array('aba_indicador', 'Indicador', FALSE, 'ir_indicador();');
    $abas[] = array('aba_cadastro_ordem', 'Ordenação dos Processos', FALSE, 'ir_cadastro_ordem();');
    $abas[] = array('aba_anexo', 'Anexo', TRUE, 'location.reload();');
	
    echo aba_start($abas);
        echo form_open('gestao/reuniao_sistema_gestao/anexo_salvar');
            echo form_start_box('default_box', 'Cadastro');			
    			echo form_default_hidden('cd_reuniao_sistema_gestao', '', $row['cd_reuniao_sistema_gestao']);
                echo form_default_row('dt_reuniao_sistema_gestao', 'Data:', $row['dt_reuniao_sistema_gestao']);
                echo form_default_row('ds_reuniao_sistema_gestao_tipo', 'Tipo:', $row['ds_reuniao_sistema_gestao_tipo']);
                echo form_default_row('', '', '');
                echo form_default_upload_multiplo('arquivo_m', 'Arquivo:*', 'reuniao_sistema_gestao', 'validaArq');
                echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();    
                echo button_save('Salvar');
            echo form_command_bar_detail_end();
        echo form_close();
        echo  '<div id="result_div"></div>';
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>