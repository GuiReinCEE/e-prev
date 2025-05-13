<?php
set_title('Programas e Projetos');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(array('ds_projeto', 'objetivo', 'justificativa', 'cd_gerencia_resposanvel'), 'form_valida(form)');
	?>

	function form_valida(form)
	{
		var fl_marcado = false;

		$("input[type='checkbox'][id='gerencia_envolvida']").each( 
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
		location.href = "<?= site_url('gestao/projeto') ?>";
	}

	function ir_indicador()
	{
		location.href = "<?= site_url('gestao/projeto/indicador/'.$row['cd_projeto']) ?>";
	}

	function ir_custo()
	{
		location.href = "<?= site_url('gestao/projeto/custo/'.$row['cd_projeto']) ?>";
	}

	function ir_cronograma()
	{
		location.href = "<?= site_url('gestao/projeto/cronograma/'.$row['cd_projeto']) ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	if(intval($row['cd_projeto']) > 0)
	{
		$abas[] = array('aba_custo', 'Indicador', FALSE, 'ir_indicador();');
		$abas[] = array('aba_indicador', 'Custos Projetados', FALSE, 'ir_custo();');
		$abas[] = array('aba_cronograma', 'Cronograma', FALSE, 'ir_cronograma();');
	}

	echo aba_start($abas);
		echo form_open('gestao/projeto/salvar');
			echo form_start_box('default_box', 'Projeto');
				echo form_default_hidden('cd_projeto', '', $row);	
				echo form_default_text('ds_projeto', 'Projeto :*', $row, 'style="width:350px;"');
				echo form_default_textarea('objetivo', 'Objetivo :*', $row);
				echo form_default_textarea('justificativa', 'Justificativa :*', $row);
				echo form_default_gerencia('cd_gerencia_resposanvel', 'Responsável :*', $row['cd_gerencia_resposanvel']);
				echo form_default_checkbox_group('gerencia_envolvida', 'Envolvidos :*', $gerencia, $gerencia_envolvida, 120);
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				if( (intval($row['cd_projeto']) == 0) OR ((intval($row['cd_projeto']) > 0) AND ($this->session->userdata('divisao') == $row['cd_gerencia_resposanvel'])))
				{
					echo button_save('Salvar');	
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>