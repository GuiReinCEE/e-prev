<?php
//echo "<pre>";print_r($row);exit;
	set_title('Pauta SG - Integrantes');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('fl_colegiado', 'fl_secretaria', 'ds_pauta_sg_integrante'), 'valida_suplente()'); ?>

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/pauta_sg_integrante') ?>";
	}

	function set_titulares()
	{
        $.post("<?= site_url('gestao/pauta_sg_integrante/set_titulares') ?>",
        {
            fl_colegiado : $("#fl_colegiado").val()
        },
        function(data)
        {
			var select = $('#cd_pauta_sg_integrante_titular'); 
			
			if(select.prop) 
			{
				var options = select.prop('options');
			}
			else
			{
				var options = select.attr('options');
			}
			
			$('option', select).remove();
			
			options[options.length] = new Option('Selecione', '');
			
			$.each(data, function(val, text) {
				options[options.length] = new Option(text.text, text.value);
			});

        }, 'json');
	}

	function mostrar_suplente()
	{
		var fl_tipo = $("#fl_tipo").val();

		if(fl_tipo == 'S')
		{
			set_titulares();

			$("#cd_pauta_sg_integrante_titular_row").show();
		}
		else
		{
			$("#cd_pauta_sg_integrante_titular_row").hide();
			$("#cd_pauta_sg_integrante_titular").val("");
		}
	}

	function mostrar_tipo()
	{
		var fl_secretaria = $("#fl_secretaria").val();

		if(fl_secretaria == 'N')
		{
			$("#fl_tipo_row").show();
			$("#fl_presidente_row").show();
		}
		else if(fl_secretaria == 'S')
		{
			$("#fl_tipo_row").hide();
			$("#fl_tipo").val("");

			$("#cd_pauta_sg_integrante_titular_row").hide();
			$("#cd_pauta_sg_integrante_titular").val("");

			$("#fl_presidente_row").hide();
			$("#fl_presidente").val("");
		}
	}

	function valida_suplente()
	{
		if($("#fl_secretaria").val() == 'N')
		{
			if($("#fl_tipo").val() == '')
			{
				alert('Preencha o campo Tipo.');
			}
			else if($("#fl_tipo").val() == 'S' && $("#cd_pauta_sg_integrante_titular").val() == '')
			{
				alert('Preencha o campo Suplente do Titular.');
			}
			else if($("#fl_presidente").val() == '')
			{
				alert('Preencha o campo Presidente.');
			}
			else
			{
				var confirmacao = "Salvar?\n\n"+
								  "[OK] para Sim\n\n"+
								  "[Cancelar] para Não\n\n";

				if(confirm(confirmacao))
				{
					$("form").submit();
				}
			}
		}
		else
		{
			var confirmacao = "Salvar?\n\n"+
							  "[OK] para Sim\n\n"+
							  "[Cancelar] para Não\n\n";

			if(confirm(confirmacao))
			{
				$("form").submit();
			}
		}
	}

	$(function (){
		if($("#fl_tipo").val() == 'S')
		{
			$("#cd_pauta_sg_integrante_titular_row").show();
		}
		else
		{
			$("#cd_pauta_sg_integrante_titular_row").hide();
		}

		if($("#fl_secretaria").val() == 'S')
		{
			mostrar_tipo();
		}
		
		if($("#cd_pauta_sg_integrante").val() == '')
		{
			$("#fl_presidente").val("N");
			$("#fl_secretaria").val("N");
		}
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_open('gestao/pauta_sg_integrante/salvar');
	        echo form_start_box('default_box', 'Cadastro');
	        	echo form_default_hidden('cd_pauta_sg_integrante', '', $row);
	        	echo form_default_hidden('teste', '', $row['cd_pauta_sg_integrante_titular']);
				echo form_default_dropdown('fl_colegiado', 'Colegiado: (*)', $colegiado, $row['fl_colegiado'], 'onchange="mostrar_suplente()"');
				echo form_default_dropdown('fl_secretaria', 'Secretária: (*)', $drop, $row['fl_secretaria'], 'onchange="mostrar_tipo()";');
				echo form_default_dropdown('fl_tipo', 'Tipo: (*)', $drop_tipo, $row['fl_tipo'], 'onchange="mostrar_suplente()"');

				echo form_default_dropdown('fl_indicado_eleito', 'Indicado/Eleito:', $drop_indicado_eleito, $row['fl_indicado_eleito']);
				echo form_default_dropdown('cd_pauta_sg_integrante_titular', 'Suplente do Titular: (*)', $titulares, $row['cd_pauta_sg_integrante_titular']);
				echo form_default_dropdown('fl_presidente', 'Presidente: (*)', $drop, $row['fl_presidente']);
				echo form_default_text('ds_pauta_sg_integrante', 'Nome: (*)', $row, 'style="width:400px;"');
				echo form_default_text('cargo', 'Cargo:', $row, 'style="width:400px;"');
				echo form_default_text('email', 'E-mail:', $row, 'style="width:400px;"');
				echo form_default_telefone('celular', 'Celular:', $row, 'style="width:400px;"');
			echo form_end_box('default_box');
	        echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
        echo form_close();
		echo br(2);
    echo aba_end();

    $this->load->view('footer');

?>