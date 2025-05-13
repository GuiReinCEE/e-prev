<?php
set_title('Comprovante IRPF - Arquivo - Upload');
$this->load->view('header');
?>
<script>
	function salvar(form)
	{
		var arquivo = $("#userfile").val();
	
		if($("#userfile").val() == "")
		{
			alert("Informe o Arquivo para Envio");
			$("#userfile").focus();
			return false;
		}
	
		if($("#nr_ano_exercicio").val() == "")
		{
			alert("Informe o Ano Exercício");
			$("#nr_ano_exercicio").focus();
			return false;
		}
		
		if($("#nr_ano_calendario").val() == "")
		{
			alert("Informe o Ano Calendário");
			$("#nr_ano_calendario").focus();
			return false;
		}		
		
		if((arquivo.substr(arquivo.lastIndexOf('.'),arquivo.length)).toLowerCase()!='.txt')
		{
			alert('Tipo de arquivo inválido.\n\nSomente arquivos .TXT');
			return false;
		} 		

		if(confirm("Enviar arquivo?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			form.submit();
		}
	}

	function ir_lista()
	{
		location.href='<?php echo site_url("cadastro/comprovante_irpf_colaborador_arquivo"); ?>';
	}
	
	
	$(function(){
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Enviar Arquivo', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_open_multipart('cadastro/comprovante_irpf_colaborador_arquivo/salvar');
		echo form_start_box("default_box", "Envio de arquivo");
			echo form_default_row("","Arquivo (.TXT):(*)","<input type='file' name='userfile' id='userfile'>");
			echo form_default_integer('nr_ano_exercicio', 'Ano Exercício:(*)');
			echo form_default_integer('nr_ano_calendario', 'Ano Calendário:(*)');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save("Enviar");
			echo button_save("Cancelar", "location.href='".site_url('cadastro/comprovante_irpf_colaborador_arquivo')."'", "botao_disabled");
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(2);
echo aba_end();
$this->load->view('footer_interna');
?>