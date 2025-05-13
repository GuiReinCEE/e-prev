<?php
	set_title('Controle de Certificados');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cpf', 'nome')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/certificado_controle') ?>";
	}

	function busca_participante($cpf)
	{
		$.post("<?= site_url('gestao/certificado_controle/busca_participante') ?>",
		{
			cpf : $cpf.val()
		},
		function(data)
		{
			$("#nome").val(data.nome);
		}, "json");	
	}

	function habilita_posse($cd_certificado_controle_cargo)
	{
		if(
			($cd_certificado_controle_cargo.val() == 1) 
			|| 
			($cd_certificado_controle_cargo.val() == 2) 
			|| 
			($cd_certificado_controle_cargo.val() == 3)
			|| 
			($cd_certificado_controle_cargo.val() == 4)
		  ) 
		{
			$("#dt_posse_row").show();
		}
		else
		{
			$("#dt_posse_row").hide();
		}
	}

	function recertificar($form)
	{
		$form.append('<input type="hidden" id="cd_certificado_controle_pai" name="cd_certificado_controle_pai" value="'+$("#cd_certificado_controle").val()+'" />');

		$("#cd_certificado_controle").val(0);
		$("#dt_certificao").val("");
		$("#dt_expira_certificado").val("");
		$("#arquivo").val("");
		$("#arquivo_nome").val("");

		$form.submit();
	}
	
	$(function(){
		habilita_posse($("#cd_certificado_controle_cargo"));
	});	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	$cargo_db = array('gestao.certificado_controle_cargo', 'cd_certificado_controle_cargo', 'ds_certificado_controle_cargo');
	$tipo_db  = array('gestao.certificado_controle_tipo', 'cd_certificado_controle_tipo', 'ds_certificado_controle_tipo');

	$indicado = array(
		array('value' => 'S', 'text' => 'Sim'),
		array('value' => 'N', 'text' => 'Não')
	);

	echo aba_start($abas);
		echo form_open('gestao/certificado_controle/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_certificado_controle', '', $row);	
				echo form_default_cpf('cpf', 'CPF: (*)', $row, 'onblur="busca_participante($(this));"');
				echo form_default_text('nome', 'Nome: (*)', $row, 'style="width: 350px;"');
				echo form_default_date('dt_nascimento', 'Dt. Nascimento:', $row);
				echo form_default_dropdown_db('cd_certificado_controle_cargo', 'Cargo:', $cargo_db, array($row['cd_certificado_controle_cargo']), 'onchange="habilita_posse($(this))"', '', TRUE);
				echo form_default_date('dt_posse', 'Dt. Posse:', $row);
				echo form_default_row('', '', '');
				echo form_default_date('dt_posse_fim', 'Dt. Fim Posse/Desligamento:', $row);
				echo form_default_row('', '', '<b>Preencher este campo somente quando do encerramento efetivo do mandato.</b>');
				echo form_default_row('', '', '');
				echo form_default_dropdown('fl_indicado', 'Indicado:', $indicado, array($row['fl_indicado']));
				echo form_default_dropdown_db('cd_certificado_controle_tipo', 'Certificado:', $tipo_db, array($row['cd_certificado_controle_tipo']), '', '', TRUE);	
	 			echo form_default_date('dt_certificao', 'Dt. Certificado:', $row);
	 			echo form_default_date('dt_expira_certificado', 'Dt. Termino Certificado:', $row);
	 			echo form_default_upload_iframe('arquivo', 'certificado_controle', 'Anexo:', array($row['arquivo'], $row['arquivo_nome']), 'certificado_controle');
			echo form_end_box('default_box');
			if(trim($row['fl_pontuacao']) == 'S')
 			{
 				echo form_start_box('default_pontuacao_box', 'Pontuação');
 					echo form_default_integer('nr_pontuacao_1', '1º Ano:', $row);
 					echo form_default_integer('nr_pontuacao_2', '2º Ano:', $row);
 					echo form_default_integer('nr_pontuacao_3', '3º Ano:', $row);
 				echo form_end_box('default_pontuacao_box');
 			}
			echo form_command_bar_detail_start();
				if(trim($row['dt_posse_fim']) == '') 
				{
					echo button_save('Salvar');	

					if(
						(intval($row['cd_certificado_controle']) > 0) 
						AND 
						(trim($row['dt_certificao']) != '') 
						AND 
						(trim($row['dt_expira_certificado']) != '') 
						AND 
						(intval($row['nr_filho']) == 0)
					  )
					{
						echo button_save('Recertificar', 'recertificar($(this.form));', 'botao_verde');
					}
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>