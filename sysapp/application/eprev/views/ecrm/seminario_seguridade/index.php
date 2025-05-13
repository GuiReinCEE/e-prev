<?php
set_title('Seminário Seguridade - Lista');
$this->load->view('header');
?>
<script>
function filtrar()
{
	load();
}
function load()
{
	document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";
	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/seminario_seguridade/listar'
		,{
			nr_ano_edicao: $('#nr_ano_edicao').val()
		}
		,
	function(data)
		{
			document.getElementById("result_div").innerHTML = data;
			configure_result_table();
		}
	);
}
function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),[
				"DateTimeBR"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "CaseInsensitiveString"
				, "RE"
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
	ob_resul.sort(0, true);
}


function setPresente(fl_presente,cd_inscricao)
{
	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/seminario_seguridade/presente'
		,{
			cd_inscricao: cd_inscricao,
			fl_presente: fl_presente
		}
		,
	function(data)
		{
			if(data != "")
			{
				alert(data);
			}
		}
	);
}

function enviaCertificado(cd_inscricao)
{
	$.post( '<?php echo site_url('ecrm/seminario_seguridade/certificado'); ?>'
		,{
			cd_inscricao: cd_inscricao
		}
		,
	function(data)
		{
			if(data != "")
			{
				alert(data);
			}
			else
			{
				alert("Certificado enviado.")
			}
		}
	);
}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start( $abas );
	$config[]= array();
	echo form_list_command_bar($config);	
	echo form_start_box_filter('filter_bar', 'Filtros', true);
		$ar_ano = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 2009, 'value' => 2009),Array('text' => 2010, 'value' => 2010),Array('text' => 2011, 'value' => 2011)) ;
		echo filter_dropdown("nr_ano_edicao", "Ano edição:", $ar_ano,2011);
	echo form_end_box_filter();
?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />
<?php echo aba_end(''); ?>
<script>
	filtrar();
</script>
<?php
$this->load->view('footer');
?>

