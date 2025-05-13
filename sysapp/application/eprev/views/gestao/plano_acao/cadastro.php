<?php
set_title('Plano de Ação - Cadastro');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('nr_ano', 'nr_plano_acao'),'valida_destino(form)') ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('gestao/plano_acao') ?>";
    }

    function ir_itens()
    {
        location.href = "<?= site_url('gestao/plano_acao/itens/'.$row['cd_plano_acao']) ?>";
    }

    function valida_destino(form)
    {
        var cd_processo = $('#cd_processo').val(); 
        var ds_situacao = $('#ds_situacao').val();
        
        if((cd_processo == '') && (ds_situacao == ''))
        {
            alert('Informe o Processo ou a Situação.');
            
            return false;
        }
        else
        {
            $(form).submit();
        }

    }   
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    if(intval($row['cd_plano_acao']) > 0)
    {
        $abas[] = array('aba_itens', 'Itens', FALSE, 'ir_itens();');
    }

    echo aba_start($abas);
        echo form_open('gestao/plano_acao/salvar');
            echo form_start_box('default_box', 'Cadastro');			
    			echo form_default_hidden('cd_plano_acao', '', $row['cd_plano_acao']);

                if(intval($row['cd_plano_acao']) > 0)
                {
                    echo form_default_row('', 'Ano/Número:', '<label class="label label-inverse">'.$row['ds_ano_numero'].'</label>');
                }
                else
                {
                    echo form_default_integer_ano('nr_ano', 'nr_plano_acao', 'Ano/Número: (*)', $row['nr_ano'], $row['nr_plano_acao']);
                }
                
                echo form_default_row('', '', '<i>Informe o Processo ou a Situação.</i>');
                echo form_default_dropdown('cd_processo', 'Processo: (*)', $processo, $row['cd_processo']);
                echo form_default_textarea('ds_situacao', 'Situação: (*)', $row['ds_situacao'], 'style="height:80px;"');
                echo form_default_textarea('ds_relatorio_auditoria', 'Relatório de Auditoria:', $row['ds_relatorio_auditoria'], 'style="height:80px;"');
                
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();   
                echo button_save('Salvar');
            echo form_command_bar_detail_end();
        echo form_close();

        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>