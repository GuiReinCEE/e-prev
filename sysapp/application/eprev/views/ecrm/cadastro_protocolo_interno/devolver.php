<?php
set_title('Protocolo Interno - Devolução');
$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/cadastro_protocolo_interno"); ?>';
	}
	
	function ir_relatorio()
	{
		location.href="<?php echo site_url('ecrm/cadastro_protocolo_interno/relatorio'); ?>";
	}
	
	function ir_cadastro(cd_protocolo)
	{
		location.href='<?php echo site_url("ecrm/cadastro_protocolo_interno/detalhe"); ?>' + "/" + cd_protocolo;
	}	
	
	function ir_resumo()
    {
        location.href="<?php echo site_url('ecrm/cadastro_protocolo_interno/resumo'); ?>";
    }
	
	function devolver()
	{
		if(jQuery.trim($("#descricao").val()) == "")
		{
			alert("Informe o campo DESCRIÇÃO.");
			$("#descricao").focus();
		}
		else
		{
			var confirmacao = 'Deseja DEVOLVER o Protocolo (' + $("#numero_protocolo").val() + ') para ' + $("#devolver_para").val() + '?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';
						  
			if(confirm(confirmacao))
			{				
				$("#devolverForm").submit();
			}
		}		
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_relatorio', 'Relatório', false, 'ir_relatorio();');
$abas[] = array('aba_resumo', 'Resumo', false, 'ir_resumo();');
$abas[] = array('aba_detalhe', 'Cadastro', false, 'ir_cadastro('.$ar_protocolo['cd_documento_recebido'].');');
$abas[] = array('aba_devolver', 'Devolução', true, 'location.reload();');

echo aba_start( $abas );
	echo form_open('ecrm/cadastro_protocolo_interno/salvar_devolucao',Array("id" => "devolverForm"));
	echo form_start_box( "default_box", "Justificativa" );
		echo form_default_hidden('cd_documento_recebido', "Código:", $ar_protocolo['cd_documento_recebido'], "style='width:100%;border: 0px;' readonly");
		echo form_default_text('numero_protocolo', "Número:", $ar_protocolo['nr_documento_recebido'], "style='font-weight: bold;width:100%;border: 0px;' readonly" );	
		echo form_default_text("devolver_para", "Devolver para:", $ar_protocolo["nome_usuario_cadastro"], 'style="border: 0px; width: 500px;" readonly' );		
		echo form_default_textarea('descricao', "Descrição:*", '', "style='width:500px; height: 100px;'");
	echo form_end_box("default_box");
	echo form_command_bar_detail_start();
		echo button_save("Devolver","devolver();","botao_vermelho");
		echo button_save("Cancelar",'ir_cadastro('.$ar_protocolo['cd_documento_recebido'].');',"botao_disabled");
	echo form_command_bar_detail_end();
	echo form_close();
	echo br();	
echo aba_end();
$this->load->view('footer_interna');
?>