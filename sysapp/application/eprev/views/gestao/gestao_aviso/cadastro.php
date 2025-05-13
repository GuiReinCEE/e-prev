<?php
set_title('Aviso - Cadastro');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit
			 (
				Array
					(
						'ds_descricao',
						'cd_periodicidade',
						'nr_dia',
						'dt_referencia'
					),
				'formValida(form)'
			 );
	?>

	function formValida(form)
	{
		var fl_marcado = false;
		$("input[type='checkbox'][id='ar_usuario']").each( 
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
			if(confirm('Salvar?'))
			{
				form.submit();
			}
		}	
	}
	
	function ir_lista()
	{
		location.href='<?php echo site_url("gestao/gestao_aviso"); ?>';
	}

	function excluir()
	{
		var confirmacao = 'Deseja excluir?\n\n'+
				'Clique [Ok] para Sim\n\n'+
				'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
		   location.href='<?php echo site_url("gestao/gestao_aviso/excluir/".intval($row['cd_gestao_aviso'])); ?>';
		}
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('gestao/gestao_aviso/salvar');
			echo form_start_box("default_box", "Cadastro");
				echo form_default_hidden('cd_gestao_aviso', '', $row['cd_gestao_aviso']);
				echo form_default_text('ds_descricao', "Descrição:*", $row['ds_descricao'],'style="width: 500px;"');
				echo form_default_dropdown('cd_periodicidade', 'Periodicidade:*', $ar_periodicidade, $row['cd_periodicidade']);
				echo form_default_integer('qt_dia', "Qt dia(s):*", $row['qt_dia']);
				echo form_default_row("","", "<i>Quantidade de dias úteis antes da data será enviado o aviso por e-mail.<BR>ZERO envia no dia</i>");
				echo form_default_date('dt_referencia', "Dt Referência:*", $row['dt_referencia']);
				echo form_default_row("","", "
												<i>
												A Dt Referência será usada em conjunto com a Periodicidade, obedecendo as regras abaixo:
												<BR>- Eventual: é a data que o item deverá ser verificado
												<BR>- Diário: é a data que indica a partir de quando o item deverá ser verificado
												<BR>- Semanal: é a data que indica o dia da semana que o item deverá ser verificado
												<BR>- Mensal: é a data que indica o dia do mês que o item deverá ser verificado
												<BR>- Anual: é a data que indica o dia/mês do ano que o item deverá ser verificado
												</i>
				                             ");
											 
				echo form_default_checkbox_group("ar_usuario", "Quem será avisado:", $ar_usuario, $ar_usuario_checked, 120);
			echo form_end_box("default_box");
			echo form_command_bar_detail_start();
				echo button_save("Salvar");
				if(($this->session->userdata("divisao") == 'GC' OR $this->session->userdata("codigo") == 251) AND intval($row['tl_gestao_aviso_controle']) > 0)
				{
					echo button_save("Excluir", "excluir()", "botao_vermelho");
				}
			echo form_command_bar_detail_end();
		
		echo form_close();
		
		echo br(5);
	echo aba_end();
$this->load->view('footer_interna');
?>