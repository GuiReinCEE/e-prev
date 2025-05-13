<?php
set_title('Rescisão');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('descricao'));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/rescisao_controle"); ?>';
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'DateTimeBR'
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(2, true);
	}
	
	$(function(){
		configure_result_table();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Acompanhamento', TRUE, 'location.reload();');
	
$body = array();
$head = array(
	'Descrição',
	'Usuário',
	'Dt Inclusão'
);

foreach ($collection as $item)
{			
	$body[] = array(
		array($item['descricao'], 'text-align:justify'),
		$item['nome'],
		$item['dt_inclusao']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

	
echo aba_start( $abas );
	echo form_open('atividade/rescisao_controle/salvar_acompanhamento');
		echo form_start_box( "default_box", "Acompanhamento" );
			echo form_default_hidden('cd_registro_empregado', "", $cd_registro_empregado);		
			echo form_default_hidden('cd_empresa', "", $cd_empresa);		
			echo form_default_hidden('seq_dependencia', "", $seq_dependencia);		
			echo form_default_textarea('descricao', 'Descrição :', '');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save("Salvar");
		echo form_command_bar_detail_end();
	echo form_close();
	echo $grid->render();
	echo br(2);
echo aba_end();
$this->load->view('footer_interna');
?>