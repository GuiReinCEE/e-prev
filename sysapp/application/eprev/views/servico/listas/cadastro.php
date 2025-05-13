<?php
set_title('Par�metros do Sistema');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('categoria', 'codigo_new'));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("servico/listas/index/".$row['categoria']); ?>';
	}
	
	function excluir()
	{	
		confirmacao = "Deseja excluir?";
		
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url("servico/listas/excluir/".$row['categoria']."/".$row['codigo'] ); ?>';
		}
	}

</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
echo aba_start( $abas );
	echo form_open('servico/listas/salvar');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('codigo', "", $row);	
			
			if(trim($row['categoria']) != '')
			{
				echo form_default_row('', 'Categoria :', $row['categoria']);
				echo form_default_hidden('categoria', "", $row);
			}
			else
			{
				echo form_default_text('categoria', 'Categoria :*', $row);
			}
			
			echo form_default_text('codigo_new', 'C�digo :*', $row['codigo']);
			echo form_default_text('descricao', 'Descri��o :', $row, 'style="width:300px;"');
			echo form_default_dropdown('divisao', 'Ger�ncia :', $arr_divisao, array($row['divisao']));
			echo form_default_text('valor', 'Valor :', $row);
			if(trim($row['dt_exclusao']) != '')
			{
				echo form_default_row('', 'Dt Exclus�o :', $row['dt_exclusao']);
			}
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			if(trim($row['dt_exclusao']) == '')
			{
				echo button_save("Salvar");	
			}
			
			if((trim($row['codigo']) != '') AND (trim($row['dt_exclusao']) == ''))
			{
				echo button_save("Excluir", "excluir();", "botao_vermelho");	
			}
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>