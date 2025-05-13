<?php
	set_title('Demonstrativo Estatístico');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nr_mes', 'nr_ano'), 'valida_arquivo(form)') ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/demonstrativo_estatistico/index') ?>";
    }

    function valida_arquivo(form)
    {
        if($("#arquivo").val() == "" && $("#arquivo_nome").val() == "")
        {
            alert("Nenhum arquivo foi anexado.");
            return false;
        }
        else if($("#arquivo_planilha").val() == "" && $("#arquivo_planilha_nome").val() == "")
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

    function excluir()
    {
        var confirmacao = 'Deseja excluir?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
           location.href='<?php echo site_url("gestao/demonstrativo_estatistico/excluir/".$row['cd_demonstrativo_estatistico']); ?>';
        }

    }

    function enviar()
    {
        var confirmacao = 'Deseja enviar e-mail?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/demonstrativo_estatistico/enviar/'.$row['cd_demonstrativo_estatistico']) ?>";
        }
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('gestao/demonstrativo_estatistico/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_demonstrativo_estatistico', '', $row['cd_demonstrativo_estatistico']);
                if(intval($row['cd_demonstrativo_estatistico']) > 0)
                {
                    echo form_default_text('mes_ano', "Mês/Ano:", $row['mes_ano'], "style='width:100%;border: 0px;' readonly");
                    echo form_default_hidden('nr_mes', '', $row['nr_mes']);
                    echo form_default_hidden('nr_ano', '', $row['nr_ano']);
                }
                else
                {
                    echo form_default_mes_ano('nr_mes', 'nr_ano', 'Mês/Ano: (*)', $row['mes_ano']);
                }
            
                echo form_default_upload_iframe('arquivo', 'demonstrativo_estatistico', 'Arquivo PDF: (*)', array($row['arquivo'], $row['arquivo_nome']), 'demonstrativo_estatistico', true);
		        echo form_default_upload_iframe('arquivo_planilha', 'demonstrativo_estatistico', 'Arquivo Planilha: (*)', array($row['arquivo_planilha'], $row['arquivo_planilha_nome']), 'demonstrativo_estatistico', true);

                echo form_default_upload_iframe('arquivo_ceeeprev', 'demonstrativo_estatistico', 'Arquivo PDF CEEEPrev: (*)', array($row['arquivo_ceeeprev'], $row['arquivo_ceeeprev_nome']), 'demonstrativo_estatistico', true);
                echo form_default_upload_iframe('arquivo_ceeeprev_planilha', 'demonstrativo_estatistico', 'Arquivo Planilha CEEEPrev: (*)', array($row['arquivo_ceeeprev_planilha'], $row['arquivo_ceeeprev_planilha_nome']), 'demonstrativo_estatistico', true);
	         	if(trim($row['dt_envio']) != '')
                {
                    echo form_default_row('', 'Dt. Envio:', $row['dt_envio']);
                    echo form_default_row('', 'Usuário:', $row['ds_usuario_envio']);
                }
			echo form_end_box('default_box');	
			echo form_command_bar_detail_start();
				echo button_save('Salvar');    
                if(intval($row['cd_demonstrativo_estatistico']) > 0 AND trim($row['dt_envio']) == '')
                {
                    echo button_save('Excluir', 'excluir()', 'botao_vermelho');   
                    echo button_save('Enviar E-mail', 'enviar()', 'botao_verde'); 
                }
		    echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>