<?php 
set_title('Avaliação - Cadastro de Familia');
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array('nome_familia'));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("cadastro/avaliacao_familia"); ?>';
	}
	
	function listar_familias()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");

		$.post('<?php echo site_url('/cadastro/avaliacao_familia/listar_familias'); ?>',
		{
		  cd_familia : $('#cd_familia').val()
		},function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});
	}
	
	$(function(){
		if($('#cd_familia').val() > 0)
		{
			listar_familias();
		}
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start( $abas );
	echo form_open('cadastro/avaliacao_familia/salvar');
		echo form_start_box( "default_box", "Avaliação - Cadastro de Familia" );
			echo form_hidden( 'cd_familia', intval($row['cd_familia']) );
			echo form_default_text("nome_familia", "Família:*", $row['nome_familia'], "style='width:500px;'");
			echo form_default_text("classe", "Classe:", $row['classe']);
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();
		echo form_command_bar_detail_end();
	echo form_close();
    if(intval($row['cd_familia']) > 0)
    {
        echo form_start_box( "default_box", "Escolaridade" );
            echo '<div id="result_div"></div>';
        echo form_end_box("default_box");
    }
	
	echo br(2);
echo aba_end();
$this->load->view('footer_interna');
?>