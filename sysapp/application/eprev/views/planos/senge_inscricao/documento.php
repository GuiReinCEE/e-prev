<?php
set_title('Inscrições no SENGE');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('opt_tipo_doc', 'dt_entrega'));
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("planos/senge_inscricao"); ?>';
    }
	
	function ir_cadastro()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/cadastro/".$row['cd_registro_empregado']); ?>';
    }
	
	function ir_contato()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/contato/".$row['cd_registro_empregado']); ?>';
    }
	
	function ir_anexo()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/anexo/".$row['cd_registro_empregado']); ?>';
    }
	
	function ir_historico()
    {
        location.href='<?php echo site_url("planos/senge_inscricao/historico/".$row['cd_registro_empregado']); ?>';
    }
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'CaseInsensitiveString',
			'DateBR',
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
$abas[] = array('aba_nc', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_lista', 'Contato', FALSE, 'ir_contato();');
$abas[] = array('aba_lista', 'Documentos', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
$abas[] = array('aba_lista', 'Histórico', FALSE, 'ir_historico();');

$body = array();
$head = array( 
	'Tipo documento',
	'Dt. Entrega',
	'Dt. Inclusão'
);

foreach( $collection as $item )
{
	$body[] = array(
		array($item["documento"],'text-alig:left;'),
		$item["dt_entrega"],
		$item["dt_inclusao"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;


$arr_documento[] = array('value' => '1', 'text' => 'Carteira de Identidade / CIC');
$arr_documento[] = array('value' => '225', 'text' => 'Pedido de Inscrição');

echo aba_start( $abas );
    echo form_open('planos/senge_inscricao/salvar_documento', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_hidden('cd_empresa', '', $row['cd_empresa']);
			echo form_default_hidden('cd_registro_empregado', '', $row['cd_registro_empregado']);
			echo form_default_hidden('seq_dependencia', '', $row['seq_dependencia']);
			echo form_default_row('re', 'RE :', $row['cd_registro_empregado']);
			echo form_default_row('nome', 'Nome :', $row['nome']);
		echo form_end_box("default_box");	
		echo form_start_box( "default_documento_box", "Documento" );
			echo form_default_dropdown('opt_tipo_doc', 'Documento :', $arr_documento, '');
			echo form_default_date('dt_entrega', 'Dt. Entrega :', '');
		echo form_end_box("default_documento_box");
        echo form_command_bar_detail_start();     
			if(trim($row['dt_documentacao_confirmada']) == "")
			{
				echo button_save("Adicionar");
			}
        echo form_command_bar_detail_end();
    echo form_close();
	echo $grid->render();
    echo br();	
	
echo aba_end();

$this->load->view('footer_interna');
?>