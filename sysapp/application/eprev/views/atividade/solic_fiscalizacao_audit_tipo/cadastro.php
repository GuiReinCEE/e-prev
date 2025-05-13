<?php
	set_title('Registro de Solicitações de Fiscalizações e Auditorias - Tipo');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_solic_fiscalizacao_audit_tipo_agrupamento', 'ds_solic_fiscalizacao_audit_tipo', 'fl_especificar')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('atividade/solic_fiscalizacao_audit_tipo') ?>";
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    $especificar = array(
        array('value' => 'N', 'text' => 'Não'),
        array('value' => 'S', 'text' => 'Sim')
    );

	echo aba_start($abas);
		echo form_open('atividade/solic_fiscalizacao_audit_tipo/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_solic_fiscalizacao_audit_tipo', '', $row['cd_solic_fiscalizacao_audit_tipo']);
				echo form_default_dropdown_db('cd_solic_fiscalizacao_audit_tipo_agrupamento', 'Agrupamento: (*)', array('projetos.solic_fiscalizacao_audit_tipo_agrupamento', 'cd_solic_fiscalizacao_audit_tipo_agrupamento', 'ds_solic_fiscalizacao_audit_tipo_agrupamento'), array($row['cd_solic_fiscalizacao_audit_tipo_agrupamento']), '', '', TRUE);
                echo form_default_text('ds_solic_fiscalizacao_audit_tipo', 'Tipo: (*)', $row['ds_solic_fiscalizacao_audit_tipo'], 'style="width:350px;"');			
				echo form_default_dropdown('fl_especificar', 'Especificar: (*)', $especificar, $row['fl_especificar']);
                echo form_default_checkbox_group('tipo_gerencia', 'Gestão:', $gerencia, $tipo_gerencia, 150, 350);
                echo form_default_dropdown('cd_gerencia', 'Área consolidadora:', $area_consolidadora, $row['cd_gerencia']);                               
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				echo button_save('Salvar');         	            
		    echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>