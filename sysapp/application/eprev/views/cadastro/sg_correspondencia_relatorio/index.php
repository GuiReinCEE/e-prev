<?php
	set_title('Relat�rio de Correspond�cias');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('date_ini', 'date_fim')) ?>

	$(function(){
		$("#date_ini_date_fim_shortcut").val("currentMonth");
		$("#date_ini_date_fim_shortcut").change();
	})

	function gerar(form)
	{
		$('form').submit();
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Relat�rio', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('cadastro/sg_correspondencia_relatorio/relatorio');
			echo form_start_box('default_box', 'Relat�rio') ;
				echo form_default_date_interval('date_ini', 'date_fim', 'Per�odo: (*)');
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				echo button_save('Gerar', 'gerar(form)');
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>