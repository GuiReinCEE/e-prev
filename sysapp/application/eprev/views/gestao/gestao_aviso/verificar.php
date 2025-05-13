<?php
set_title('Aviso - Verificar');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit();
	?>

	function verificarSalvar(form)
	{
		if(confirm("Deseja Verificar a Pendência?"))
		{
			form.submit();
		}
	}
</script>
<?php
	$abas[] = array('aba_verificar', 'Verificar', TRUE, 'location.reload();');

	$head = array(
		'Acompanhamento',
	   	'Dt Inclusão',
	   	'Usuário'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			array(nl2br($item["ds_gestao_aviso_verificacao_acompanhamento"]), "text-align:justify;"),
			$item["dt_inclusao"],
			array($item["ds_usuario_inclusao"], "text-align:left;")
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	

	echo aba_start($abas);
		echo form_open('gestao/gestao_aviso/verificarSalvar');
			echo form_start_box("default_box", "Verificar");
				echo form_default_hidden('cd_gestao_aviso_verificacao', "Código:", intval($row['cd_gestao_aviso_verificacao']));
				echo form_default_row('', "Descrição:", '<span class="label label-inverse">'.$row['ds_descricao'].'</span>');
				echo form_default_row('', 'Periodicidade:', '<span class="label label-info">'.$row["periodicidade"].'</span>');
				echo form_default_row('', "Dt Referência:",  '<span class="label '.((trim($row['dt_verificacao']) == "") ? "label-important" : "").'">'.$row['dt_referencia'].'</span>');
				echo form_default_row('', '', '<span class="label label-success">'.$row["verificado"].'</span>');
			echo form_end_box("default_box");

			echo form_command_bar_detail_start();
				if (trim($row['dt_verificacao']) == "")
				{
					echo button_save("Verificar", 'verificarSalvar(this.form);');
				}
			echo form_command_bar_detail_end();
		
		echo form_close();

		echo form_open('gestao/gestao_aviso/acompanhamento_salvar');
			echo form_start_box("default_box", "Acompanhamento");
				echo form_default_hidden('cd_gestao_aviso_verificacao', '', intval($row['cd_gestao_aviso_verificacao']));
				echo form_default_textarea('ds_gestao_aviso_verificacao_acompanhamento', "Acompanhamento:*", '', "style='width:500px; height:90px;'");
			echo form_end_box("default_box");

			echo form_command_bar_detail_start();
				if (trim($row['dt_verificacao']) == "")
				{
					echo button_save("Salvar");
				}
			echo form_command_bar_detail_end();

		echo form_close();
		echo $grid->render();
		echo br(5);
	echo aba_end();
$this->load->view('footer_interna');
?>