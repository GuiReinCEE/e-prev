<?php
set_title('Avaliação - Cadastro de Familia');
$this->load->view('header');
?>
<script>

	<?php echo form_default_js_submit(array());	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("cadastro/avaliacao_familia"); ?>';
	}

    function ir_cadastro()
	{
		location.href='<?php echo site_url("cadastro/avaliacao_familia/detalhe/".$cd_familia); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', false, 'ir_cadastro()');
$abas[] = array('aba_detalhe', 'Escolaridade', true, 'location.reload();');

$nivel[] = array('text'=> 'Básico',    'value'=> 'B');
$nivel[] = array('text'=> 'Pleno',     'value'=> 'P');
$nivel[] = array('text'=> 'Excelente', 'value'=> 'E');

echo aba_start( $abas );
	echo form_open('cadastro/avaliacao_familia/salva_familia');
		echo form_hidden( 'cd_familia', intval($cd_familia) );
		echo form_hidden( 'cd_escolaridade', intval($cd_escolaridade) );
		echo form_hidden( 'tipo', intval($tipo) );
		echo form_start_box( "default_box", "Avaliação - Cadastro de Familia" );
			echo form_default_text("nome_familia", "Família:", $nome_familia, "style='width:200%;border: 0px;' readonly");
			echo form_default_text("nome_escolaridade", "Escolaridade:", $nome_escolaridade, "style='width:200%;border: 0px;' readonly");
			echo form_default_integer("grau_percentual", "Grau Percentual:", $row['grau_percentual']);
			echo form_default_dropdown('nivel', 'Nível escolaridade:', $nivel, array($row['nivel']));
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();

$this->load->view('footer_interna');
?>