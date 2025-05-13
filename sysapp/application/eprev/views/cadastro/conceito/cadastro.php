<?php
set_title('Avaliação - Conceitos para Avaliações de Desempenho ');
$this->load->view('header');
?>
<script>
	<?php echo form_default_js_submit(/*array('nome_cargo', 'cd_familia')*/);	?>
</script>
<?php
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start( $abas );
	echo form_open('cadastro/conceito/salvar');
		echo form_start_box( "default_box", "Competências Institucionais" );
			echo form_default_textarea('CACI', "A:", $row[0]['descricao']);
			echo form_default_textarea('CBCI', "B:", $row[1]['descricao']);
			echo form_default_textarea('CCCI', "C:", $row[2]['descricao']);
			echo form_default_textarea('CDCI', "D:", $row[3]['descricao']);
			echo form_default_textarea('CECI', "E:", $row[4]['descricao']);
			echo form_default_textarea('CFCI', "F:", $row[5]['descricao']);
		echo form_end_box("default_box");

		echo form_start_box( "default_box", "Competências Específicas" );
			echo form_default_textarea('CACE', "A:", $row[6]['descricao']);
			echo form_default_textarea('CBCE', "B:", $row[7]['descricao']);
			echo form_default_textarea('CCCE', "C:", $row[8]['descricao']);
			echo form_default_textarea('CDCE', "D:", $row[9]['descricao']);
			echo form_default_textarea('CECE', "E:", $row[10]['descricao']);
			echo form_default_textarea('CFCE', "F:", $row[11]['descricao']);
		echo form_end_box("default_box");

		echo form_start_box( "default_box", "Responsabilidades" );
			echo form_default_textarea('CARE', "A:", $row[12]['descricao']);
			echo form_default_textarea('CBRE', "B:", $row[13]['descricao']);
			echo form_default_textarea('CCRE', "C:", $row[14]['descricao']);
			echo form_default_textarea('CDRE', "D:", $row[15]['descricao']);
			echo form_default_textarea('CERE', "E:", $row[16]['descricao']);
			echo form_default_textarea('CFRE', "F:", $row[17]['descricao']);
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(2);
echo aba_end();


$this->load->view('footer_interna');
?>