<?php
	set_title('Pauta SG');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nr_ata', 'fl_sumula', 'fl_tipo_reuniao', 'dt_pauta', 'hr_pauta'), 'validacao(form);'); ?>

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/pauta_sg') ?>";
	}

	function ir_assunto()
	{
		location.href = "<?= site_url('gestao/pauta_sg/assunto/'.$row['cd_pauta_sg']) ?>";
	}

	function ir_presentes()
	{
		location.href = "<?= site_url('gestao/pauta_sg/presentes/'.$row['cd_pauta_sg']) ?>";
	}

	function validacao(form)
    {
        $.post("<?= site_url('gestao/pauta_sg/valida_numero_ata') ?>",
        {
        	cd_pauta_sg : <?= intval($row['cd_pauta_sg']) ?>,
            nr_ata      : $("#nr_ata").val(),
            fl_sumula   : $("#fl_sumula").val()
        },
        function(data)
        {   
            if(data["valida"] > 0) 
            {
                alert("Número de Ata já existe");
                return false; 
            }
            else
            {
            	if(confirm("Salvar?"))
                {
                    form.submit();  
                }
            }
        }, "json", true);
    }

	function publicar(form)
	{
        var confirmacao = 'Confirma a publicação?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        {		
			form.submit();            
        }		
	}
	

	function getPautaAssinatura()
	{
		$('#result_div_assinatura').html("<?php echo loader_html(); ?>");

		$.post("<?php echo site_url('gestao/pauta_sg/getPautaAssinatura'); ?>/",
		{
			cd_pauta_sg : $('#cd_pauta_sg').val()
		},
		function(data)
		{
			$('#result_div_assinatura').html(data);
		});
	}	
	
	$(function(){
		 getPautaAssinatura();
	})			
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	if(intval($row['cd_pauta_sg']) > 0)
	{
		$abas[] = array('aba_assunto', 'Assuntos', FALSE, 'ir_assunto();');
		$abas[] = array('aba_presentes', 'Presentes', FALSE, 'ir_presentes();');
	}

	echo aba_start($abas);
		echo form_open('gestao/pauta_sg/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_pauta_sg', '', $row);	
				echo form_default_integer('nr_ata', 'Nº da Ata: (*)', $row);
				
				if(intval($row['cd_pauta_sg']) > 0)
				{
					echo form_default_row('fl_sumula_row', 'Colegiado:', '<span class="'.$row['class_sumula'].'">'.$row['fl_sumula'].'</span>');
					if($row['fl_sumula'] == "CD")
					{
						echo form_default_row('link_pauta', 'Link para envio:', 'https://www.fundacaoceee.com.br/link/?p='.$row['cd_pauta_sg_md5']);
					}
					
					echo form_default_hidden('fl_sumula', '', $row);	
				}
				else
				{
					echo form_default_dropdown('fl_sumula', 'Colegiado: (*)', $sumula);
				}

				echo form_default_dropdown('fl_tipo_reuniao', 'Tipo Reunião: (*)', $tipo_reuniao, $row['fl_tipo_reuniao']);
				echo form_default_text('local', 'Local:', $row, 'style="width:350px;"');
				echo form_default_date('dt_pauta', 'Dt. Reunião: (*)', $row);
				echo form_default_time('hr_pauta', 'Hr. Reunião: (*)', $row);
				echo form_default_date('dt_pauta_sg_fim', 'Dt. Reunião Encerramento:', $row);
				echo form_default_time('hr_pauta_sg_fim', 'Hr. Reunião Encerramento:', $row);;
			echo form_end_box('default_box');

			echo form_command_bar_detail_start();
				if(trim($row['dt_aprovacao']) == '')
				{
					echo button_save('Salvar');	
				}
			echo form_command_bar_detail_end();
		echo form_close();
		
		if(intval($row['cd_pauta_sg']) > 0)
		{
			echo form_start_box("default_ass_box", "Assinatura" );
				echo form_default_row('', '', '<div id="result_div_assinatura"></div>');
			echo form_end_box("default_ass_box");			
			
			echo form_open('gestao/pauta_sg/publicar');
				echo form_start_box('default_box', 'Publicação');
					echo form_default_hidden('cd_pauta_sg', '', $row);
					echo form_default_date('dt_publicacao_libera', 'Dt Autoatendimento:', $row);

					if(trim($row['dt_publicacao_libera']) != '')
					{
						echo form_default_row('', 'Dt Publicação:', $row['dt_publicacao']);
						echo form_default_row('', 'Usuário Publicação:', $row['ds_usuario_publicacao']);
					}

				echo form_end_box('default_box');
				echo form_command_bar_detail_start();     
					echo button_save('Salvar', 'publicar(this.form)', 'botao_vermelho');
				echo form_command_bar_detail_end();
			echo form_close();
		} 
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>