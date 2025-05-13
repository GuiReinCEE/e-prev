<?php
	set_title('Pré-venda - Relatório');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		relatorioListar();
	}
	
	function relatorioListar()
	{
		if($("#nr_ano").val() != "")
		{
			$("#result_div").html("<?php echo loader_html(); ?>");

			$.post('<?php echo base_url().index_page(); ?>/ecrm/prevenda/relatorioListar',
				{
					nr_ano     : $("#nr_ano").val(),
					cd_empresa : $("#cd_empresa").val(),
					tp_empresa : $("#tp_empresa").val()
				}
				,
				function(data)
				{
					$("#result_div").html(data);
					//table_result();
				}
			);
		}
		else
		{
			alert("Informe os campos com (*).");
			$("#nr_ano").focus();
		}
	}
	
	function table_result()
	{
		var ob_resul = new SortableTable(document.getElementById("tabela_certificado"),
		[
			null,
			'RE',  
			'CaseInsensitiveString',  
			'DateBR'
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
		ob_resul.sort(2, false);
	}	

	function ir_protocolo_interno()
	{
		location.href='<?php echo site_url('/ecrm/prevenda/protocolo_interno'); ?>';
	}	
		
</script>
<?php
	$link_lista = site_url( 'ecrm/prevenda' );
	$abas[] = array('aba_lista', 'Lista', false, "redir('', '$link_lista')");
	$abas[] = array('aba_relatorio', 'Relatório', TRUE, 'location.reload();');
	$abas[] = array('aba_protocolo', 'Encaminhar Ped. Inscrição', FALSE, 'ir_protocolo_interno();');
	echo aba_start( $abas );	

	echo form_list_command_bar();	
	echo form_start_box_filter('filter_bar', 'Filtros');

		echo filter_integer('nr_ano',"Ano:(*)", date('Y'));
		
		$ar_tipo = Array(Array('text' => 'Instituidor', 'value' => 'I'),Array('text' => 'Patrocinadora', 'value' => 'P')) ;
		echo filter_dropdown('tp_empresa', 'Tipo Empresa:', $ar_tipo);		
		
		echo filter_dropdown('cd_empresa', 'Empresa:', $ar_empresa);		
		
	echo form_end_box_filter();	

?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br>
<br>
<br>
<script>
$(document).ready(function() {
	filtrar();
});
</script>
<?php
	echo aba_end(''); 
	$this->load->view('footer');
?>
















<?php
/*
set_title('Pré-venda - Relatório');
$this->load->view('header');

$link_lista = site_url( 'ecrm/prevenda' );
$link_relatorio = site_url( 'ecrm/prevenda/relatorio' );
$abas[0] = array('aba_lista', 'Lista', false, "redir('', '$link_lista')");
$abas[1] = array('aba_relatorio', 'Relatório', true, "redir('', '$link_relatorio')");
echo aba_start( $abas );

echo form_open("ecrm/prevenda/relatorio");

echo form_list_command_bar();

echo form_start_box_filter();

echo form_default_text('ano', 'Ano',$ano);
//echo form_default_dropdown('tipo', 'Tipo Empresa:', $ar_tipo_empresa);

echo form_end_box_filter();

echo form_close();

echo aba_end( 'relatorio');
$this->load->view('footer');
*/
?>