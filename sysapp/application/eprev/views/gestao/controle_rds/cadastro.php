<?php
	set_title('Controle de RDS - Cadastro');
	$this->load->view('header');
?>

<script>
	<?= form_default_js_submit(array('nr_ano', 'nr_rds', 'ds_controle_rds', 'nr_ata', 'dt_reuniao', 'fl_restrito')); ?>
    
	function ir_lista()
    {
        location.href = "<?= site_url('gestao/controle_rds') ?>";
    }
	
	function excluir()
	{
		var confirmacao = 'Deseja EXCLUIR a RDS?\n\n'+
						  'Clique [Ok] para Sim\n\n'+
						  'Clique [Cancelar] para Não\n\n';	

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('gestao/controle_rds/excluir/'.intval($row['cd_controle_rds'])) ?>"
		}
	}
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	$restrito = array(
		array('value' => 'N', 'text' => 'Não'),
		array('value' => 'S', 'text' => 'Sim')
	);

	echo aba_start($abas);
	    echo form_open('gestao/controle_rds/salvar');
	        echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_controle_rds', '', $row['cd_controle_rds']);
				
				if(intval($row['cd_controle_rds']) > 0)
				{
					echo form_default_row('', 'Ano/Número:','<span class="label label-success">'.$row['nr_ano_numero'].'</span>');
					echo form_default_row('', 'Dt RDS:', $row['dt_rds']);
				}

				echo form_default_integer('nr_ano', 'Ano: (*)', $row);
				echo form_default_integer('nr_rds', 'Nº RDS: (*)', $row);
				
				echo form_default_textarea('ds_controle_rds', 'Assunto: (*)', $row, 'style="width:500px; height:60px;"');
				echo form_default_upload_iframe('arquivo', 'controle_rds', 'Arquivo:', array($row['arquivo'], $row['arquivo_nome']), 'controle_rds', (gerencia_in(array('GRC'))) ? TRUE : FALSE);
				
				echo form_default_integer('nr_ata', 'Nr Ata: (*)', $row);
				echo form_default_date('dt_reuniao', 'Dt Reunião: (*)', $row);

				echo form_default_dropdown('fl_restrito', 'Restrito: (*)', $restrito, $row['fl_restrito']);

				if(intval($row['cd_controle_rds']) > 0)
				{	
					echo form_default_row('', 'Dt Inclusão:', $row['dt_inclusao']);
					echo form_default_row('', 'Inclusão:', $row['ds_usuario_inclusao']);
				}
				
	        echo form_end_box('default_box');
	        echo form_command_bar_detail_start();     
	            if(gerencia_in(array('GC')))
	            {
	            	echo button_save('Salvar');

					if(intval($row['cd_controle_rds']) > 0)
					{
						echo button_save('Excluir', 'excluir();', 'botao_vermelho');
					}
	            }
	        echo form_command_bar_detail_end();
	    echo form_close();
	    echo br(2);	
	echo aba_end();

	$this->load->view('footer_interna');
?>