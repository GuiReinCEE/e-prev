<?php
	set_title('Recursos Humanos - Cadastro');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_descricao', 'cd_periodicidade', 'nr_dia', 'dt_referencia', 'cd_usuario_conferencia'), 'form_valida(form)') ?>

	function form_valida(form)
	{
		var fl_marcado = false;

		$("input[type='checkbox'][id='usuario']").each( 
			function() 
			{ 
				if (this.checked) 
				{ 
					fl_marcado = true;
				} 
			}
		);	
		
		if(!fl_marcado)
		{
			alert("Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação.)");
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
	
	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_aviso') ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('cadastro/rh_aviso/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_text('ds_descricao', 'Descrição: (*)', '','style="width: 500px;"');
				echo form_default_dropdown('cd_periodicidade', 'Periodicidade: (*)', $periodicidade);
				echo form_default_integer('qt_dia', 'Qt dia(s): (*)', 0);
				echo form_default_row('', '', '<i>Quantidade de dias úteis antes da data será enviado o aviso por e-mail.<br/>ZERO envia no dia</i>');
				echo form_default_date('dt_referencia', 'Dt Referência: (*)');
				echo form_default_row('','','
					<i>
						A Dt Referência será usada em conjunto com a Periodicidade, obedecendo as regras abaixo:
						<br/>- Eventual: é a data que o item deverá ser verificado
						<br/>- Diário: é a data que indica a partir de quando o item deverá ser verificado
						<br/>- Semanal: é a data que indica o dia da semana que o item deverá ser verificado
						<br/>- Mensal: é a data que indica o dia do mês que o item deverá ser verificado
						<br/>- Anual: é a data que indica o dia/mês do ano que o item deverá ser verificado
					</i>');
				echo form_default_checkbox_group('usuario', 'Quem será avisado: (*)', $usuario, array(), 80);
				echo form_default_dropdown('cd_usuario_conferencia', 'Usuário que vai Conferir: (*)', $usuario);
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				echo button_save("Salvar");
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>