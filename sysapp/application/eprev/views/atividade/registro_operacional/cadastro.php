<?php
set_title('Registro Operacional');
$this->load->view('header');
?>
<script>
	<?php echo form_default_js_submit(array('cd_acomp', 'ds_nome'));	?>
	
	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/registro_operacional"); ?>';
    }
	
	function finalizar()
	{
		if(confirm('Deseja realmente finalizar o registro operacional?\n\n'))
		{
			location.href='<?php echo site_url("atividade/registro_operacional/finalizar/".intval($row['cd_acompanhamento_registro_operacional'])); ?>';
		}
	}
	
	function imprimir()
    {
        window.open('<?php echo site_url("atividade/registro_operacional/imprimir/".intval($row['cd_acompanhamento_registro_operacional'])); ?>');
    }
</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_lista', 'Cadastro', true, 'location.reload();');

echo aba_start( $abas );
	echo form_open('atividade/registro_operacional/salvar', 'name="filter_bar_form"');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_acompanhamento_registro_operacional', '', $row['cd_acompanhamento_registro_operacional']);
			echo form_default_dropdown('cd_acomp', 'Projeto :*', $arr_projeto, array($row['cd_acomp']));
			echo form_default_text('ds_nome', 'Nome Processo :*', $row['ds_nome'], 'style="width:400px;"');
			echo form_default_textarea('ds_processo_faz', '1) O que o processo faz : ', $row, 'style="height:100px;"');
			echo form_default_textarea('ds_processo_executado', '2) De que maneira é executado o processo : ', $row, 'style="height:100px;"');
			echo form_default_textarea('ds_calculo', '3) Cálculos : ', $row, 'style="height:100px;"');
			echo form_default_textarea('ds_responsaveis', '4) Responsáveis : ', $row, 'style="height:100px;"');
			echo form_default_textarea('ds_requesito', '5) O que é necessário para que este processo possa ocontecer : ', $row, 'style="height:100px;"');
			echo form_default_textarea('ds_necessario', '6) Este processo é necessário para qual(is) outro(s) processo(s) : ', $row, 'style="height:100px;"');
			echo form_default_textarea('ds_integridade', '7) Integração com outros sistemas : ', $row, 'style="height:100px;"');
			echo form_default_textarea('ds_resultado', '8) Resultados : ', $row, 'style="height:100px;"');
			echo form_default_textarea('ds_local', '9) Telas / Relatórios / Planilhas : ', $row, 'style="height:100px;"');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();   
			if(trim($row['dt_finalizado']) == '')
			{
				echo button_save("Salvar");
				if(intval($row['cd_acompanhamento_registro_operacional']) > 0)
				{
					echo button_save("Finalizar e Enchaminhar", 'finalizar()', 'botao_verde');
				}
			}
			
			if(intval($row['cd_acompanhamento_registro_operacional']) > 0)
			{
				echo button_save("Imprimir", 'imprimir()', 'botao_disabled');
			}
		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end(); 

$this->load->view('footer');
?>