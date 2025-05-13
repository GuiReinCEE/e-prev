<?php
set_title('Cálculo IRRF');
$this->load->view('header');
?>
<script>
	<?php
	echo form_default_js_submit(array('ds_calculo_irrf_rejeitado'));
	?>
	
	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/calculo_irrf"); ?>';
    }
	
	function ir_cadastro()
    {
        location.href='<?php echo site_url("atividade/calculo_irrf/cadastro/".intval($row['cd_calculo_irrf'])); ?>';
    }
	
	function ir_anexo()
    {
        location.href='<?php echo site_url("atividade/calculo_irrf/anexo/".intval($row['cd_calculo_irrf'])); ?>';
    }
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');
$abas[] = array('aba_nc', 'Anexos', FALSE, 'ir_anexo();');

echo aba_start( $abas );
	echo form_open('atividade/calculo_irrf/salvar_rejeitar', 'name="filter_bar_form"');
		echo form_start_box("default_box", "Motivo");
			echo form_default_hidden('cd_calculo_irrf', '', $row['cd_calculo_irrf']);
			echo form_default_textarea('ds_calculo_irrf_rejeitado', "Informe o motivo:* ", '', "style='width:500px; height:100px;'");
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();     
            echo button_save("Salvar");
			echo button_save("Cancelar", 'ir_cadastro()', 'botao_disabled');
        echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>