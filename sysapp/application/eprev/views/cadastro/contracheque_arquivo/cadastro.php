<?php 
	set_title('Arquivos Contracheque');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nr_mes', 'nr_ano', 'dt_liberacao', 'userfile'), '_valida_file(form)') ?>
	function _valida_file(form)
	{
		var arquivo = $("#userfile").val();

		if((arquivo.substr(arquivo.lastIndexOf("."),arquivo.length)).toLowerCase() != ".txt")
		{
			alert("Tipo de arquivo inválido.\n\nSomente arquivos .TXT");
			return false;
		} 		

		if(confirm("Confirma o envio?"))
		{
			form.submit();
		}
	}

	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/contracheque_arquivo') ?>";
	}

	$(function(){
		$("#nr_mes").mask("99");
	   	$("#nr_ano").mask("9999");
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open_multipart('cadastro/contracheque_arquivo/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_integer('nr_mes', 'Mês competência: (*)'); 
				echo form_default_integer('nr_ano', 'Ano competência (*)'); 
				echo form_default_date('dt_liberacao', 'Dt. Liberação: (*)');
				echo form_default_row('','Arquivo .TXT (*):', '<input type="file" name="userfile" id="userfile">');
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				echo button_save('Salvar');
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();

$this->load->view('footer_interna');
?>