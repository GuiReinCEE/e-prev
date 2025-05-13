<?php
set_title('Fotos');
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

		$.post( '<?php echo base_url() . index_page(); ?>/ecrm/multimidia/listar_foto'
			,{}
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
					"Number",
					"CaseInsensitiveString",
					"DateBR"
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
	
	
	var ob_window = "";
	function visualizarFotos(cd_foto)
	{
		if(ob_window != "")
		{
			ob_window.close();
		}

		var ds_url = '<?php echo base_url() . index_page(); ?>/intranet/biblioteca_multimidia/foto_ver/' + cd_foto;
			
		var nr_width  = 1000;
		var nr_height = 800;
		var nr_left = ((screen.width - 10) - nr_width) / 2;
		var nr_top = ((screen.height - 80) - nr_height) / 2;

		ob_window = window.open(ds_url, "wVisualizarVideo", "left="+nr_left+",top="+nr_top+",width="+nr_width+",height="+nr_height+",scrollbars=yes,resizable=yes,directories=no,location=no,menubar=no,status=yes,titlebar=no,toolbar=no");		 					
	}

	function novo()
	{
		location.href='<?php echo site_url("ecrm/multimidia/foto_cadastro"); ?>';
	}


	function ir_aba_video()
	{
		location.href='<?php echo site_url("ecrm/multimidia"); ?>';
	}
</script>

<?php
$abas[] = array('aba_video', 'Vídeos', FALSE, 'ir_aba_video();');
$abas[] = array('aba_foto', 'Fotos', TRUE, 'location.reload();');
echo aba_start( $abas );

$config['button'][]=array('Nova foto', 'novo()');
$config['filter']=false;
echo form_list_command_bar($config);
?>

<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end(''); 
?>

<script type="text/javascript">
	filtrar();
</script>

<?php
$this->load->view('footer');
?>