<?php
	set_title('Cenário Plano de Ação');
	$this->load->view('header');
?>
<script>
    <?php
        if(gerencia_in(array('GC')) AND trim($row['dt_envio_responsavel'] == ''))
        {
            $validacao = array('ds_cenario_plano_acao', 'dt_prazo_previsto');
        }
        else if(trim($row['dt_envio_responsavel']) != '')
        {
            if(trim($row['dt_envio_auditoria']) == '' AND gerencia_in(array($row['cd_gerencia_responsavel'])))
            {
                $validacao = array('dt_verificacao_eficacia');
            }
            else
            {   
                $validacao = array('dt_validacao_eficacia');
            }
        }

        echo form_default_js_submit($validacao);
    ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/cenario_plano_acao/index') ?>";
    }

    function ir_acomapnhamento()
    {
        location.href = "<?= site_url('gestao/cenario_plano_acao/acompanhamento/'.$row['cd_cenario_plano_acao']) ?>";
    }

    function ir_anexo()
    {
        location.href = "<?= site_url('gestao/cenario_plano_acao/anexo/'.$row['cd_cenario_plano_acao']) ?>";
    }

    function envio_responsavel()
    {
        var confirmacao = 'Deseja enviar para o responsável?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/cenario_plano_acao/envio_responsavel/'.$row['cd_cenario_plano_acao']) ?>";
        }
    }

    function envio_auditoria()
    {
        var confirmacao = 'Deseja enviar para o responsável?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/cenario_plano_acao/envio_auditoria/'.$row['cd_cenario_plano_acao']) ?>";
        }
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
    $abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acomapnhamento();');
    $abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

    $fl_salvar = FALSE;

    echo aba_start($abas);
    	echo form_open('gestao/cenario_plano_acao/salvar');
            echo form_start_box('default_cenario_box', 'Cenário');   
                echo form_default_hidden('cd_cenario_plano_acao', '', $row['cd_cenario_plano_acao']);         
                echo form_default_row('', 'Cénario:', $row['cd_cenario'].'-'.$row['titulo']);
                echo form_default_row('link', 'Link:', '<a href="'.base_url('index.php/ecrm/informativo_cenario_legal/legislacao/'.$row['cd_edicao'].'/'.$row['cd_cenario']).'" target="_blank" style="font-weight:bold;">[Ver o Cenário Legal]</a>');
            echo form_end_box('default_cenario_box');
    		echo form_start_box('default_box', 'Plano de Ação'); 
	    		echo form_default_hidden('cd_cenario_plano_acao', '', $row['cd_cenario_plano_acao']);
                
                if(gerencia_in(array('GC')) AND trim($row['dt_envio_responsavel'] == ''))
                {
                    //GC REGISTRANDO O PLANO DE AÇÃO
                    $fl_salvar = TRUE;

                    echo form_default_textarea('ds_cenario_plano_acao', 'Plano de Ação: (*)', $row['ds_cenario_plano_acao'], 'style="height:80px"');
                    echo form_default_date('dt_prazo_previsto', 'Dt. Prazo Previsto: (*)', $row['dt_prazo_previsto']);
                }
                else
                {
                    echo form_default_hidden('ds_cenario_plano_acao', '', $row['ds_cenario_plano_acao']);
                    echo form_default_hidden('dt_prazo_previsto', '', $row['dt_prazo_previsto']);

                    echo form_default_textarea('', 'Plano de Ação:', $row['ds_cenario_plano_acao'], 'style="height:80px;" readonly=""');
                    echo form_default_row('', 'Dt. Prazo Previsto:', $row['dt_prazo_previsto']);

                    if(trim($row['dt_envio_responsavel']) != '')
                    {
                        if(
                            trim($row['dt_envio_auditoria']) == '' 
                            AND 
                            gerencia_in(array($row['cd_gerencia_responsavel'])) 
                            AND 
                            $this->session->userdata('indic_03') == '*'
                        )
                        {
                            //GÊRENCIA RESP. REGISTRANDO A DATA PARA VERIFICAÇÃO DA EFICÁCIA
                            $fl_salvar = TRUE;

                            echo form_default_date('dt_verificacao_eficacia', 'Dt. Verificação Eficácia: (*)', $row['dt_verificacao_eficacia']);
                        }
                        else
                        {
                            echo form_default_hidden('dt_verificacao_eficacia', '', $row['dt_verificacao_eficacia']);

                            echo form_default_row('', 'Dt. Verificação Eficácia:', $row['dt_verificacao_eficacia']);

                            if(trim($row['dt_envio_auditoria']) != '' AND gerencia_in(array('AI')))
                            {
                                if(trim($row['dt_validacao_eficacia']) == '')
                                {
                                    //AI VALIDANDO A EFICÁCIA
                                    $fl_salvar = TRUE;

                                    echo form_default_date('dt_validacao_eficacia', 'Dt. Validação Eficácia: (*)', $row['dt_validacao_eficacia']);
                                }
                                else
                                {
                                    echo form_default_row('', 'Dt. Validação Eficácia:', $row['dt_validacao_eficacia']);
                                }  
                            }
                            else
                            {
                                echo form_default_row('', 'Dt. Validação Eficácia:', $row['dt_validacao_eficacia']);
                            }
                        }
                    }
                }

	    	echo form_end_box('default_box');
	    	echo form_command_bar_detail_start();

                if($fl_salvar)
                {
                    echo button_save('Salvar'); 
                }

                if(trim($row['dt_prazo_previsto']) != '' AND trim($row['dt_envio_responsavel']) == '' AND (gerencia_in(array('GC'))))
                {
                    echo button_save('Enviar Responsável', 'envio_responsavel()', 'botao_verde');
                } 
                else if(
                    trim($row['dt_envio_responsavel']) != '' 
                    AND 
                    trim($row['dt_verificacao_eficacia']) != '' 
                    AND 
                    trim($row['dt_envio_auditoria']) == '' 
                    AND 
                    gerencia_in(array($row['cd_gerencia_responsavel']))
                )
                {
                    echo button_save('Enviar Auditoria', 'envio_auditoria()', 'botao_verde');
                }

            echo form_command_bar_detail_end();		
    	echo form_close();
    echo aba_end();

    $this->load->view('footer_interna');
?>