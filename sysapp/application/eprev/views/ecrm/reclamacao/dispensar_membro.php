<?php
	set_title('Controle de Reclamações - Dispensar Comitê');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_usuario_comite', 'ds_justificativa_confirma')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/reclamacao/parecer_comite') ?>"; 
	}
</script>
<style>
    #ds_descricao_item {
        white-space:normal !important;
    }
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE,'ir_lista();');
	$abas[] = array('aba_dispensar_membro', 'Dispensar Membro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('ecrm/reclamacao/atualizar_situacao_membro');
		   	echo form_start_box('default_box','Cadastro');
		   		echo form_default_hidden('numero', '', $row['numero']);
	            echo form_default_hidden('ano', '', $row['ano']);
	            echo form_default_hidden('tipo', '', $row['tipo']);
	            echo form_default_row('', 'Número: ', $row['ano'].'/'.$row['numero'].'/'.$row['tipo']);
	            echo form_default_row('', 'RE: ', $row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia']);
	            echo form_default_row('', 'Nome: ', $row['nome']);
	            echo form_default_row('ds_descricao', 'Descrição: ', $row['descricao']);
	            echo form_default_row('', 'Dt. Classificação: ', $row['dt_classificacao']);
	        echo form_end_box('default_box');

	        echo form_start_box('default_box', 'Comitê');
				echo form_default_dropdown('cd_usuario_comite', 'Membro: (*)', $membros);        	
				echo form_default_textarea('ds_justificativa_confirma', 'Justificativa: (*)', $row['ds_justificativa_confirma']);
	        echo form_end_box('default_box');
	   
			echo form_command_bar_detail_start();            
		        echo button_save('Salvar');
		    echo form_command_bar_detail_end();	
	    echo form_close();
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>