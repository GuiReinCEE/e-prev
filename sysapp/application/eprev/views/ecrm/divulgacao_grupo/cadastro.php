<?php
set_title('Divulgação Grupo - Cadastro');
$this->load->view('header');
?>
<script>
	<?php echo form_default_js_submit(Array(
											'cd_divulgacao_grupo', 
											'ds_divulgacao_grupo')
									); ?>    
	
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/divulgacao_grupo"); ?>';
    }
	
    function total_registro()
    {
        location.href='<?php echo site_url("ecrm/divulgacao_grupo/total_registro/".intval($row['cd_divulgacao_grupo'])); ?>';
    }	

    function excluir()
    {
        var confirmacao = 'Deseja excluir este item?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('ecrm/divulgacao_grupo/excluir/'.intval($row['cd_divulgacao_grupo'])) ?>";
        }
    }
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');
	
echo aba_start( $abas );
    echo form_open('ecrm/divulgacao_grupo/salvar');
        echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_divulgacao_grupo', '', $row);
			echo form_default_text('ds_divulgacao_grupo', 'Descrição:(*)', $row, "style='width:800px;'");
			echo form_default_text('cd_lista', 'Cód Lista:', $row);
			echo form_default_row('qt_registro', 'Qt Total Registro(s):', '<span class="label label-success">'.$row['qt_registro'].'</span>');
			echo form_default_editor_code('qr_sql', 'SQL:(*)', $row, "style='width:800px; height: 300px;'");
			
			echo form_default_row('usuario_inclusao', 'Incluído por:', $row['usuario_inclusao']);
			echo form_default_row('dt_inclusao', 'Dt Inclusão:', $row['dt_inclusao']);
			echo form_default_row('usuario_alteracao', 'Alteração por:', $row['usuario_alteracao']);
			echo form_default_row('dt_alteracao', 'Dt Alteração:', $row['dt_alteracao']);
			echo form_default_row('usuario_exclusao', 'Excluído por:', $row['usuario_exclusao']);
			echo form_default_row('dt_exclusao', 'Dt Exclusão:', $row['dt_exclusao']);
		echo form_end_box("default_box");

        echo form_command_bar_detail_start();   
        	echo button_save("Salvar");
        	
			if(intval($row['cd_divulgacao_grupo']) > 0)
			{
                echo button_save("Atualizar Total Registro(s)","total_registro();","botao_amarelo");
                
				echo button_save("Excluir", "excluir();", "botao_vermelho");
			}
			
        echo form_command_bar_detail_end();

	echo form_close();
    echo br(5);	
echo aba_end();

$this->load->view('footer_interna');
?>