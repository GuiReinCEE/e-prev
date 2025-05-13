<?php
set_title('Reunião Sistema de Gestão - Cadastro');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('dt_reuniao_sistema_gestao', 'cd_reuniao_sistema_gestao_tipo')) ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao') ?>";
    }

    function ir_cadastro_ordem()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao/cadastro_ordem/'.$row['cd_reuniao_sistema_gestao']) ?>";
    }

    function ir_anexo()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao/anexo/'.$row['cd_reuniao_sistema_gestao']) ?>";
    }

    function ir_indicador()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao/indicador/'.$row['cd_reuniao_sistema_gestao']) ?>";
    }

    function ir_processo()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao/processo/'.$row['cd_reuniao_sistema_gestao']) ?>";
    }

    function ir_pendencia()
    {
        window.open("<?= site_url('gestao/pendencia_gestao/cadastro/0/'.$row['cd_reuniao_sistema_gestao']) ?>");
    }

    function atualiar_apresentacao()
    {
        var confirmacao = "Deseja ATUALIZAR a Apresentação?\n\n"+
                          "*A atualização vai ser feita a partir dos indicadores do momento atual.\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('gestao/reuniao_sistema_gestao/atualiza_indicador/'.$row['cd_reuniao_sistema_gestao']) ?>";
        }
    }

    function encerrar()
    {
        var confirmacao = "Deseja ENCERRAR a Reunião?\n\n"+
                          "*A apresentação não vai poder ser mais atualizada.\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('gestao/reuniao_sistema_gestao/encerrar/'.$row['cd_reuniao_sistema_gestao']) ?>";
        }
    }

    function excluir()
    {
        var confirmacao = "Deseja EXCLUIR a Reunião?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('gestao/reuniao_sistema_gestao/excluir/'.$row['cd_reuniao_sistema_gestao']) ?>";
        }
    }

    function apresentacao()
    {
        window.open("<?= site_url('gestao/reuniao_sistema_gestao/apresentacao/'.$row['cd_reuniao_sistema_gestao']) ?>");
    }

    function enviar_todos()
    {
        var confirmacao = "Deseja ENVIAR PARA TODOS o e-mail de Divulgação?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('gestao/reuniao_sistema_gestao/enviar_todos/'.$row['cd_reuniao_sistema_gestao']) ?>";
        }
    }

    $(function(){
        $("#FilterTextBox_table").keypress(function(event){
            if (event.keyCode == 13) 
            {
                event.preventDefault();
                return false;
            }
        });  

        $("#processo_checked_table tbody tr:has(td)").each(function(){
            var t = $(this).text().toLowerCase();
            $("<td class=\'indexColumn\' style=\'display:none;\'></td>").hide().text(removeAccents_table(t)).appendTo(this);
        });

        $("#FilterTextBox_table").keyup(function(event){
            if (event.keyCode == 27) 
            {
                $("#FilterTextBox_table").val("").keyup();
            }
            else
            {
                var s = $(this).val();
                    s = removeAccents_table(s);
                    s = s.toLowerCase().split(" ");
                    

                $("#processo_checked_table tbody tr:hidden").show();
                $.each(s, function(){
                    $("#processo_checked_table tbody tr:visible .indexColumn:not(:contains(\'"+ this + "\'))").parent().hide();
                });
                
                var rowCount = $("#processo_checked_table tbody tr:visible").length;
                $("#gridCount'.$this->id_tabela.'").html(rowCount);
            }
        });
    });
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    if(intval($row['cd_reuniao_sistema_gestao']) > 0)
    {
        $abas[] = array('aba_processo', 'Processo', FALSE, 'ir_processo();');
        $abas[] = array('aba_indicador', 'Indicador', FALSE, 'ir_indicador();');
        $abas[] = array('aba_cadastro_ordem', 'Ordenação dos Processos', FALSE, 'ir_cadastro_ordem();');
        $abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');	
    }

    echo aba_start($abas);
        echo form_open('gestao/reuniao_sistema_gestao/salvar');
            echo form_start_box('default_box', 'Cadastro');			
    			echo form_default_hidden('cd_reuniao_sistema_gestao', '', $row['cd_reuniao_sistema_gestao']);
                echo form_default_date('dt_reuniao_sistema_gestao', 'Data: (*)', $row);
                echo form_default_dropdown('cd_reuniao_sistema_gestao_tipo', 'Tipo: (*)', $tipo, $row['cd_reuniao_sistema_gestao_tipo']);
               
                if(intval($row['cd_reuniao_sistema_gestao']) == 0)
                {
                    echo form_default_row('', '', '<div>Procurar: <input type="text" id="FilterTextBox_table" name="FilterTextBox" style="width: 400px;"></div>');
                
                    echo form_default_checkbox_group('processo_checked', 'Processo:', $processo, array(), 120);
                }
 
                echo form_default_upload_iframe('arquivo', 'reuniao_sistema_gestao', 'Ata:', array($row['arquivo'], $row['arquivo_nome']), 'reuniao_sistema_gestao', true);
                
                if(intval($row['cd_reuniao_sistema_gestao']) > 0)
                {
                    echo form_default_row('', 'Dt. Atualização Apresentação:', $row['dt_apresentacao']);
                }

                if(trim($row['dt_encerramento']) != '')
                {
                    echo form_default_row('', 'Usuário Encerramento:', $row['usuario_encerramento']);
                    echo form_default_row('', 'Dt. Encerramento:', $row['dt_encerramento']);    
                }

                if((intval($row['cd_reuniao_sistema_gestao']) > 0) AND (count($processo_checked) > 0))
                {
                    echo form_default_row('', 'Processo:', implode(br(), $processo_checked));

                    if(count($indicador_checked) > 0)
                    {
                         echo form_default_row('', 'Indicador:', implode(br(), $indicador_checked));
                    }
                }

            echo form_end_box('default_box');
            echo form_command_bar_detail_start();   
                if(trim($row['dt_encerramento']) == '')
                { 
                    echo button_save('Salvar');
                }

                if(intval($row['cd_reuniao_sistema_gestao']) > 0)
                {
                    echo button_save('Abrir Apresentação', 'apresentacao();');

                    if(trim($row['dt_encerramento']) == '')
                    {
                        echo button_save('Atualizar Apresentação', 'atualiar_apresentacao();', 'botao_verde');

                        if(trim($row['arquivo']) != '')
                        {
                            echo button_save('Encerrar', 'encerrar();', 'botao_vermelho');
                        }

                        echo button_save('Excluir', 'excluir();', 'botao_vermelho');
                    }
                    else
                    {
                        echo button_save('Enviar para Todos', 'enviar_todos();', 'botao_verde');
                    }

                    echo button_save('Pendência de Gestão', 'ir_pendencia();');
                }
            echo form_command_bar_detail_end();
        echo form_close();

        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>