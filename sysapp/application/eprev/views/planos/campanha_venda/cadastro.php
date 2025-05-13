<?php
set_title('Campanha Venda - Cadastro');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('cd_empresa', 'ds_campanha_venda', 'dt_inicio', 'dt_fim','dt_cadastro','dt_ingresso'));
	?>
	function ir_lista()
	{
		location.href='<?=site_url("planos/campanha_venda")?>';
	}
	
	function excluir()
	{
		if(confirm('Deseja excluir essa campanha?'))
		{
			location.href='<?=site_url("planos/campanha_venda/excluir/".intval($row['cd_campanha_venda']))?>';
		}
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
	
echo aba_start( $abas );
	echo form_open('planos/campanha_venda/salvar');
	echo form_start_box( "default_box", "Cadastro" );
		echo form_default_hidden('cd_campanha_venda', "", $row);	
		echo form_default_dropdown('cd_empresa', 'Empresa:*', $arr_empresa, Array($row['cd_empresa']));		
		echo form_default_text('ds_campanha_venda', 'Descrição:*', $row, 'style="width:500px;"');
		echo form_default_date('dt_inicio', 'Dt Início:*', $row);
		echo form_default_date('dt_final', 'Dt Final:*', $row);
		echo form_default_date('dt_cadastro', 'Dt Cadastro:*', $row);
		echo form_default_date('dt_ingresso', 'Dt Ingresso:*', $row);
	echo form_end_box("default_box");
	echo form_command_bar_detail_start();
		if(intval($row['cd_campanha_venda']) == 0)
		{
			echo button_save("Salvar");
		}
		else
		{
			echo button_save("Excluir", 'excluir();', 'botao_vermelho');
		}
	echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>