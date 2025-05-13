<?php
    set_title('Atividades - Atendimento - Cenário Legal');
    $this->load->view('header');
    ?>
<script>
    <?= form_default_js_submit(array(), 'valida_form(form)') ?>

    function valida_form(form)
    {
        var pertinencia = $("#pertinencia").val();

        var fl_salvar = true;

        if($("#cd_gerencia_destino_h").val() == $("#cd_gerencia_destino").val())
        {
            if(pertinencia == "")
            {
                alert("Informe a Pertinência");
                fl_salvar = false;
            }
            else if((pertinencia == 0  || pertinencia == 1) && $("#ds_justificativa_cenario").val() == "")
            {
                alert("Informe a Justificativa.");
                fl_salvar = false;
            }
        }
        
        if(fl_salvar)
        {
            if(confirm("Salvar?"))
            {
                form.submit();
            }
        }
    }

    function ir_lista()
    {
        location.href = "<?= site_url('atividade/legal') ?>";
    }
    
    function ir_solicitacao()
    {
        location.href = "<?= site_url('atividade/atividade_solicitacao/index/'.$row['cd_gerencia_destino'].'/'.$row['numero']) ?>";
    }
    
    function ir_historico()
    {
        location.href = "<?= site_url('atividade/atividade_historico/index/'.$row['numero'].'/'.$row['cd_gerencia_destino']) ?>";
    }
    
    function ir_acompanhamento()
    {
        location.href = "<?= site_url('atividade/atividade_acompanhamento/index/'.$row['numero'].'/'.$row['cd_gerencia_destino']) ?>";
    }
    
    function ir_anexo()
    {
        location.href = "<?= site_url('atividade/atividade_anexo/index/'.$row['numero'].'/'.$row['cd_gerencia_destino']) ?>";
    }

    function set_justificativa(pertinencia)
    {
        if(pertinencia == "0" || pertinencia == "1")
        {
            $("#ds_justificativa_cenario_row").show();
        }
        else
        {
            $("#ds_justificativa_cenario_row").hide();
            $("#ds_justificativa_cenario").val("");
        }
    }

    $(function(){
        set_justificativa(<?= $row['pertinencia'] ?>);
    });
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_lista', 'Solicitação', FALSE, 'ir_solicitacao();');
    $abas[] = array('aba_lista', 'Atendimento', TRUE, 'location.reload();');
    $abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
    $abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
    $abas[] = array('aba_lista', 'Histórico', FALSE, 'ir_historico();');

    $pertinencia[] = array('text' => 'Não pertinente', 'value' => '0');
    $pertinencia[] = array('text' => 'Pertinente, mas não altera processo', 'value' => '1');
    $pertinencia[] = array('text' => 'Pertinente e altera processo', 'value' => '2');

    echo aba_start($abas);
        echo form_open('atividade/atividade_atendimento_cenario_legal/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_cenario', '', $row['cd_cenario']);
                echo form_default_hidden('numero', '', $row['numero']);
                echo form_default_hidden('cd_gerencia_destino_h', '', $row['cd_gerencia_destino']);
                
                echo form_default_row('numero', 'Número:', '<span class="label">'.trim($row['numero']).'</span>');
                echo form_default_row('dt_cad', 'Dt Solicitação:', $row['dt_cad']);
                echo form_default_row('texto_pertinencia', 'Pertinência:', '<span class="label '.$row["cor_status"].'">'.wordwrap($row['pertinencia_status'], 50, br(), false).'</span>');
                echo form_default_row('link', 'Link:', '<a href="'.base_url('index.php/ecrm/informativo_cenario_legal/legislacao/'.$row['cd_edicao'].'/'.$row['cd_cenario']).'" target="_blank" style="font-weight:bold;">[Ver o Cenário Legal]</a>');
                echo form_default_row('gerencia_destino', 'Gerência de Destino:', $row['gerencia_destino']);
                echo form_default_text('titulo', 'Título :', $row['titulo'], 'style="width:350px;"');
                echo form_default_textarea('descricao', 'Descrição da Solicitação:', $row['descricao'], 'style="width:450px; height:150px;"');

                if(trim($row['pertinencia']) != '')
                {
                    echo form_default_row('pertinencia', 'Pertinência:', $row['ds_pertinencia']);
                }
                else
                {
                    echo form_default_dropdown('pertinencia', 'Pertinência: (*)', $pertinencia, $row['pertinencia'], 'onchange="set_justificativa($(this).val()); set_data_previsto($(this).val());"');
                }

                if(trim($row['dt_prevista_implementacao_norma_legal']) != '')
                {
                    echo form_default_row('dt_prevista_implementacao_norma_legal', 'Dt. Prazo Previsto:', $row['dt_prevista_implementacao_norma_legal']);
                }

                if(trim($row['dt_implementacao_norma_legal']) != '')
                {
                    echo form_default_row('dt_implementacao_norma_legal', 'Dt. Implantação:', $row['dt_implementacao_norma_legal']);
                    echo form_default_row('', '', '<i>Data em que as mudanças foram efetivamente implementadas '.$row["dt_implementacao"].'</i>');
                }

                echo form_default_textarea('ds_justificativa_cenario', 'Justificativa: (*)', $row['ds_justificativa_cenario'], 'style="width:450px; height:150px;"');

                if(trim($row['pertinencia']) == '')
                {
                    echo form_default_dropdown('cd_gerencia_destino', 'Reencaminhar para:', $gerencia, $row['cd_gerencia_destino']);
                }
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
                
                if((trim($row['pertinencia']) == '') AND ($this->session->userdata('codigo') == intval($row['cod_atendente'])))
                {
                    echo button_save('Salvar');
                }
                echo button_save('Imprimir', 'window.print();', 'botao_disabled');
            echo form_command_bar_detail_end();
        echo form_close();
        echo br(2); 
    echo aba_end();

    $this->load->view('footer_interna');
?>