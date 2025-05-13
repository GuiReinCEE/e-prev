<?php
set_title('Informativo do Cen�rio Legal');
$this->load->view('header');
?>
<script>
	<?php echo form_default_js_submit(array('titulo', 'cd_secao', 'pertinencia'));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/informativo_cenario_legal"); ?>';
	}
	
	function ir_conteudo()
	{
		location.href='<?php echo site_url("ecrm/informativo_cenario_legal/conteudo/".$row['cd_edicao']); ?>';
	}
	
	function ir_anexo()
	{
		location.href='<?php echo site_url("ecrm/informativo_cenario_legal/anexo/".$row['cd_edicao'].'/'.intval($row['cd_cenario'])); ?>';
	}
	
	function excluir()
	{
		var confirmacao = 'Deseja excluir?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para N�o\n\n';

		if(confirm(confirmacao))
		{
			location.href='<?php  echo site_url("ecrm/informativo_cenario_legal/excluir_conteudo/".$row['cd_edicao'].'/'.intval($row['cd_cenario'])); ?>';
		}
	}

</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', $tit_capa , FALSE, 'ir_conteudo();');
$abas[] = array('aba_lista', 'Conte�do', TRUE, 'location.reload();');
if(intval($row['cd_cenario']) > 0)
{
	$abas[] = array('aba_lista', 'Anexos' , FALSE, 'ir_anexo();');
}

$arr_pertinencia[] = array('value' => '0', 'text' => 'N�o Pertinente');
$arr_pertinencia[] = array('value' => '1', 'text' => 'Pertinente, mas n�o altera processo');
$arr_pertinencia[] = array('value' => '2', 'text' => 'Pertinente e altera processo');

echo aba_start( $abas );
	echo form_open('ecrm/informativo_cenario_legal/salvar_conteudo');
		echo form_start_box("cenario_box", "Cen�rio legal" );

			echo form_default_hidden('cd_cenario', "", intval($row['cd_cenario']));
			echo form_default_hidden('cd_edicao', "", intval($row['cd_edicao']));
			if(intval($row['cd_cenario']) > 0)
			{
				echo form_default_text('cd_acao', "Edi��o / Item:", $row['cd_edicao'].'/'.$row['cd_cenario'], "style='width:100%;border: 0px;' readonly");
				echo form_default_text('dt_inclusao', "Dt Inclus�o:", $row['dt_inclusao'], "style='width:100%;border: 0px;' readonly");
				echo form_default_text('usuario_inclusao', "Usu�rio Inclus�o:", $row['usuario_inclusao'], "style='width:100%;border: 0px;' readonly");
			}
			else
			{
				echo form_default_text('cd_acao', "Edi��o:", $row['cd_edicao'], "style='width:100%;border: 0px;' readonly");
			}			
			echo form_default_text('titulo', "T�tulo: *", $row, 'style="width:500px;"');
			echo form_default_textarea('referencia', "Refer�ncia:", $row, 'style="width:500px; height: 100px;"');
			echo form_default_text('fonte', "Fonte:", $row, 'style="width:500px;"');
			echo form_default_dropdown('cd_secao', 'Se��o: *', $arr_secao, array($row['cd_secao']));
			echo form_default_checkbox_group('divisao[]', '�reas indicadas:', $arr_divisao, $arr_divisao_checked, 190);
		echo form_end_box("cenario_box");
		echo form_start_box("pagina_box", "P�gina" );
			echo form_default_editor_html('conteudo_pagina', "", $row['conteudo'], 'style="height: 400px;"');
		echo form_end_box("pagina_box");
		echo form_start_box( "link_box", "Links" );
			echo form_default_text('link1', "Link 1:", $row, 'style="width:500px;"');
			echo form_default_text('link2', "Link 2:", $row, 'style="width:500px;"');
			echo form_default_text('link3', "Link 3:", $row, 'style="width:500px;"');
			echo form_default_text('link4', "Link 4:", $row, 'style="width:500px;"');
		echo form_end_box("link_box");
		echo form_start_box("implementacao_box", "Implementa��o" );
			echo form_default_dropdown('pertinencia', 'Pertin�ncia:', $arr_pertinencia, array($row['pertinencia']));
			echo form_default_date('dt_prevista', 'Prazo Previsto:', $row);
			echo form_default_row('','','<i>Prazo previsto para a implementa��o das mudan�as</i>');
			echo form_default_date('dt_legal', 'Prazo Legal:', $row);
			echo form_default_date('dt_implementacao', 'Data Implanta��o:', $row);
			echo form_default_row('','','<i> Data em que as mudan�as foram efetivamente implementadas</i>');
			echo form_default_date('dt_exclusao', 'Data de Exclus�o:', $row);
		echo form_end_box("implementacao_box");
		echo form_command_bar_detail_start();

			if($row['dt_exclusao'] == "" AND $dt_envio_email == '')
			{
				echo button_save();
			}

			if((intval($row['cd_cenario']) > 0) AND ($row['dt_exclusao'] == "") AND ($dt_envio_email == ''))
			{
				echo button_save("Excluir", "excluir();", 'botao_vermelho');
			}
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(2);
echo aba_end();
$this->load->view('footer');
?>