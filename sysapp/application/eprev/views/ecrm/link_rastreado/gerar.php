<?php
	set_title('Link Rastreado - Gerar');
	$this->load->view('header');
?>
<script>
<?php
		echo form_default_js_submit(Array('ds_divulgacao_link','ds_url'));
?>

function carregar_dados_participante(data)
{
	$('#nome').val(data.nome);
}

function ir_lista()
{
	location.href='<?php echo site_url("ecrm/link_rastreado/gerar_index"); ?>';
}
</script>

<?php
$abas[] = array('aba_lista', 'Lista', False, 'ir_lista();');
$abas[] = array('aba_lista', 'Gerar', TRUE, 'location.reload();');

$c['emp']['id'] = 'cd_empresa';
$c['re']['id'] = 'cd_registro_empregado';
$c['seq']['id'] = 'seq_dependencia';
$c['emp']['value'] = $row['cd_empresa'];
$c['re']['value'] = $row['cd_registro_empregado'];
$c['seq']['value'] = $row['seq_dependencia'];
$c['caption'] = 'Participante:';
$c['callback'] = 'carregar_dados_participante';

$re = '';

echo aba_start( $abas );
	echo form_open('ecrm/link_rastreado/salvar_link');
		echo form_start_box( "default_box", "Gerar" );
			if(trim($row['cd_link']) == '')
			{
				echo form_default_text("ds_divulgacao_link", "Descrição:*", '', "style='width:500px;'");
				echo form_default_text("ds_url", "Url:*", '', "style='width:500px;'");
				echo form_default_participante_trigger($c);
			}
			else
			{
				echo form_default_text("ds_divulgacao_link", "Descrição:", $row, "style='width:100%;border: 0px;' readonly");
				echo form_default_text("ds_url", "Url:", $row, "style='width:100%;border: 0px;' readonly");
				
				if(trim($row['cd_empresa']) != '' AND trim($row['cd_registro_empregado']) != '' AND trim($row['seq_dependencia']) != '')
				{
					$re = $row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia'];
				}
				
				echo form_default_text("participante", "Participante:", $re, "style='width:100%;border: 0px;' readonly");
				
			}
			echo form_default_text("nome", "Nome:", $row, "style='width:100%;border: 0px;' readonly");
			
			if(trim($row['cd_link']) != '')
			{
				echo form_default_text("link", "URL Rastreada:", $row, "style='width:100%;border: 0px;' readonly");
			}
			
		echo form_end_box("default_box");
		
		echo form_command_bar_detail_start();
			if(trim($row['cd_link']) == '')
			{
				echo button_save("Gerar");
			}
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer');
?>