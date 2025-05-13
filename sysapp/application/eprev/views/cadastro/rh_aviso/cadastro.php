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
			alert("Informe os campos obrigat�rios! \n\n(os campos obrigat�rios tem um * logo ap�s a identifica��o.)");
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
				echo form_default_text('ds_descricao', 'Descri��o: (*)', '','style="width: 500px;"');
				echo form_default_dropdown('cd_periodicidade', 'Periodicidade: (*)', $periodicidade);
				echo form_default_integer('qt_dia', 'Qt dia(s): (*)', 0);
				echo form_default_row('', '', '<i>Quantidade de dias �teis antes da data ser� enviado o aviso por e-mail.<br/>ZERO envia no dia</i>');
				echo form_default_date('dt_referencia', 'Dt Refer�ncia: (*)');
				echo form_default_row('','','
					<i>
						A Dt Refer�ncia ser� usada em conjunto com a Periodicidade, obedecendo as regras abaixo:
						<br/>- Eventual: � a data que o item dever� ser verificado
						<br/>- Di�rio: � a data que indica a partir de quando o item dever� ser verificado
						<br/>- Semanal: � a data que indica o dia da semana que o item dever� ser verificado
						<br/>- Mensal: � a data que indica o dia do m�s que o item dever� ser verificado
						<br/>- Anual: � a data que indica o dia/m�s do ano que o item dever� ser verificado
					</i>');
				echo form_default_checkbox_group('usuario', 'Quem ser� avisado: (*)', $usuario, array(), 80);
				echo form_default_dropdown('cd_usuario_conferencia', 'Usu�rio que vai Conferir: (*)', $usuario);
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				echo button_save("Salvar");
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>