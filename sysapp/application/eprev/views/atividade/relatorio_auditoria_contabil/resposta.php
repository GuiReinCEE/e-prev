<?php
set_title('Relatórios de Auditoria Contábil - Responder');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('cd_relatorio_auditoria_contabil_item'), 'valida_reposta(form)');
	?>
        
    function valida_reposta(form)
    {
        if(($('#arquivo').val() == '' && $('#arquivo_nome').val() == '') && ($('#ds_resposta').val() == ''))
        {
            alert('Informe a resposta ou anexe um arquivo.');
            return false;
        }
        else if(($('#arquivo').val() != '' && $('#arquivo_nome').val() != '') && ($('#arquivo').val().split('.').pop().toLowerCase() != "docx"))
        {
            alert("O arquivo pode ser somente *.docx");
			remover_arquivo_arquivo();
			return false;	
        }
        else
        {
            if( confirm('Salvar?') )
            {
                form.submit();
            }
        }
    }
        
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/minhas"); ?>';
	}
    
	function confirmar()
	{
		var confirmacao = 'Deseja confirmar?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url("atividade/relatorio_auditoria_contabil/confimar/".$row['cd_relatorio_auditoria_contabil_item']); ?>';
		}
	}
    
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Resposta', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_open('atividade/relatorio_auditoria_contabil/salvar_resposta');
		echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden('cd_relatorio_auditoria_contabil', "", $row);	
            echo form_default_hidden('cd_relatorio_auditoria_contabil_item', "", $row);	
            echo form_default_row('ano_numero', 'Ano/Número :', $row['ano_numero']); 
            echo form_default_textarea('ds_relatorio_auditoria_contabil', 'Descrição :', $row['ds_relatorio_auditoria_contabil'], 'style="height:100px;"'); 
            echo form_default_row('arquivo', 'Arquivo :', anchor(base_url().'up/relatorio_auditoria_contabil/'.$row['arquivo_relatorio'], $row['arquivo_nome_relatorio'])); 
            echo form_default_row('nr_numero_item', 'Nr Item :', $row['nr_numero_item']); 
            echo form_default_textarea('ds_relatorio_auditoria_contabil_item', 'Item :', $row['ds_relatorio_auditoria_contabil_item'], 'style="height:100px;"'); 
            echo form_default_row('usuario_responsavel', 'Responsável :', $row['usuario_responsavel']);
            echo form_default_row('usuario_substituto', 'Substituto :', $row['usuario_substituto']);
            echo form_default_row('dt_envio', 'Dt Envio :', $row['dt_envio']); 
            echo form_default_row('usuario_envio', 'Enviado :', $row['usuario_envio']); 
            if(trim($row['dt_resposta']) != '')
            {
                echo form_default_row('dt_resposta', 'Dt Resposta :', $row['dt_resposta']); 
                echo form_default_row('usuario_resposta', 'Respondido :', $row['usuario_resposta']); 
            }
            echo form_default_upload_iframe('arquivo', 'relatorio_auditoria_contabil', 'Arquivo Resposta (.docx):', array($row['arquivo'], $row['arquivo_nome']), 'relatorio_auditoria_contabil', (trim($row['dt_resposta']) == '' ? TRUE : FALSE));
            echo form_default_textarea('ds_resposta', 'Resposta :', $row['ds_resposta'], 'style="height:100px;"'); 
        echo form_end_box("default_box");
		echo form_command_bar_detail_start();
            if(trim($row['dt_resposta']) == '')
            {
                echo button_save("Salvar");	
            }
            
            if((trim($row['dt_resposta']) == '') AND ((trim($row['arquivo_nome']) != '') OR (trim($row['ds_resposta']) != '')))
            {
                echo button_save("Confirmar", 'confirmar();', 'botao_vermelho');	
            }
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>