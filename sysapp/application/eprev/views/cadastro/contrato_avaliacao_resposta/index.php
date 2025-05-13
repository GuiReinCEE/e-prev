<?php
set_title('Contrato Avalia��o');
$this->load->view('header');
?>
<script>
	function salvar_respostas()
	{		
		var arr = new Array();
		
		var bol = true;
	
		$("select[name='resposta[]']").each(function(){
			if(bol)
			{
				if($(this).val() == '')
				{
					alert('ATEN��O\n\nVoc� deve preencher todos os campos antes de salvar.');
					bol = false;

				}
				else
				{
					arr.push($(this).val());
				}
			}
		});
		
		if(bol)
		{
			$('form').submit();
		}
	}
</script>
<?php
$abas[] = array('aba_lista', 'Avalia��o', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_open('cadastro/contrato_avaliacao_resposta/salvar', 'name="filter_bar_form"');
		echo form_start_box("default_avaliacao_box", "Avalia��o de Contrato de Presta��o de Servi�os");
			echo form_default_row('ds_empresa', "Raz�o Social:", "<b>".$row['ds_empresa']."</b>");
			echo form_default_row('ds_servico', "Servi�o Contratado:", $row['ds_servico']);
			echo form_default_row('periodo', "Per�odo Avaliado:", $row['dt_ini'].' � '.$row['dt_fim']);
			echo form_default_row('dt_limite_avaliacao', "Dt Limite para Responder:", '<span class="label label-important">'.$row["dt_limite_avaliacao"].'</span>');
			
		echo form_end_box("default_avaliacao_box");
		foreach($grupos as $item)
		{
			echo form_start_box("default_".$item['cd_contrato_formulario_grupo']."_box", $item['numero'].') '.$item['ds_contrato_formulario_grupo']);
				foreach($item['perguntas'] as $item2)
				{
					echo form_default_row('', '', '<span style="">'.$item2['ds_contrato_formulario_pergunta'].'</span>');
					echo form_default_dropdown('resposta[]', '', $item2['respostas']);
					echo form_default_row('', '', br());
				}
			echo form_end_box("default_".$item['cd_contrato_formulario_grupo']."_box");
		}
		echo form_command_bar_detail_start();  
			echo button_save('Salvar', 'salvar_respostas(form)', 'botao', 'id="btn_salvar"');
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(10);
echo aba_end();
$this->load->view('footer'); 
?>