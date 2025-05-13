<?php
set_title('Limites Telefone');
$this->load->view('header');
?>
<script>
	function atualizaLimite(cd_limite)
	{
		$.post( '<?php echo base_url() . index_page(); ?>/gestao/limite_voip/atualizar'
			,{
				cd_limite  : cd_limite,
				qt_chamada : $('#qt_chamada_' + cd_limite).val(),
				vl_chamada : ajustaValor('vl_chamada_' + cd_limite),
				hr_chamada : $('#hr_chamada_' + cd_limite).val()
			}
			,
		function(data)
			{
				alert("Atualizado.")
				$('#dt_atualizacao_' + cd_limite).html(data);
			}
		);
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
					"Number",
					"CaseInsensitiveString",
					"Number",
					"NumberFloat",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"DateTimeBR"
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
		ob_resul.sort(1, false);
	}
	
	function ajustaValor(id)
	{
		$('#vl_temp').val($('#' + id).val());
		$('#vl_temp').priceFormat({prefix: '',centsSeparator: '.',thousandsSeparator: ''});	
		
		return $('#vl_temp').val();
	}
</script>
<input type="hidden" name="vl_temp" id="vl_temp" value="0">


<?php
	$abas[0] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start( $abas );
	echo "<b>* Informe zero para sem limite.</b>";
?>
<BR>
<div id="result_div">
<?php
$body=array();
$head = array( 
	'Ramal',
	'Nome',
	'Quantidade *',
	'Valor *',
	'Duração *',
	'',
	'Dt Atualização'
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["nr_ramal"],
		array($item["nome"],"text-align:left;"),
		'<input type="input" name="qt_chamada_'.$item["cd_limite"].'" id="qt_chamada_'.$item["cd_limite"].'" value="'.$item["qt_chamada"].'" style="width: 60px; text-align:right;">',
		'<input type="input" name="vl_chamada_'.$item["cd_limite"].'" id="vl_chamada_'.$item["cd_limite"].'" value="'.$item["vl_chamada"].'" style="width: 60px; text-align:right;">',
		'<input type="input" name="hr_chamada_'.$item["cd_limite"].'" id="hr_chamada_'.$item["cd_limite"].'" value="'.$item["hr_chamada"].'" style="width: 60px; text-align:center;">',
		'
		<input type="button" value="Salvar" onclick="atualizaLimite('.$item["cd_limite"].')" class="botao">
		<script>
			jQuery(function($){
				$("#qt_chamada_'.$item["cd_limite"].'").numeric();
				$("#hr_chamada_'.$item["cd_limite"].'").mask("99:99:99");
				$("#vl_chamada_'.$item["cd_limite"].'").priceFormat({
					prefix: "",
					centsSeparator: ",",
					thousandsSeparator: "."
				});			   
			});		
		</script>
		',
		'<span id="dt_atualizacao_'.$item["cd_limite"].'">'.$item["dt_atualizacao"].'</span>'
		
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>
</div>
<br>
<?php 
	echo aba_end(''); 
?>
<script>
	configure_result_table();
</script>
<?php
	$this->load->view('footer');
?>