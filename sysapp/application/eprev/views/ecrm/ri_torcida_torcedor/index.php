<?php
set_title('Torcida - Torcedor');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}

	function liberar( cd_md5 )
	{
		if( confirm('Liberar?') )
		{
			url="<?php echo site_url('ecrm/ri_torcida_torcedor/liberar'); ?>";
			$.post( url, {cd:cd_md5}, function(data){ if(data=='true'){ load(); } else { alert(data); } } );
		}
	}

	function bloquear( cd_md5 )
	{
		if( confirm('Bloquear?') )
		{
			url="<?php echo site_url('ecrm/ri_torcida_torcedor/bloquear'); ?>";
			$.post( url, {cd:cd_md5}, function(data){ if(data=='true'){ load(); } else { alert(data); } } );
		}
	}

	function load()
	{
		document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

		$.post( '<?php echo base_url() . index_page(); ?>/ecrm/ri_torcida_torcedor/listar',
			{    
				fl_brinde       : $('#fl_brinde').val(),
				fl_liberado     : $('#fl_liberado').val(),
				dt_inclusao_ini : $('#dt_inclusao_ini').val(),			
				dt_inclusao_fim : $('#dt_inclusao_fim').val()		
			},
			function(data)
			{ 
				$("#result_div").html(data);
				configure_result_table(); 
			} 
		);
	}
	
	function etiqueta()
	{
		filter_bar_form.method = "post";
		filter_bar_form.action = '<?php echo base_url() . index_page(); ?>/ecrm/ri_torcida_torcedor/etiqueta';
		filter_bar_form.target = "_blank";
		filter_bar_form.submit();
	}	
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'Number','CaseInsensitiveString','CaseInsensitiveString','RE','CaseInsensitiveString','DateTimeBR','CaseInsensitiveString','CaseInsensitiveString', 'DateTimeBR'
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

	function novo()
	{
		location.href='<?php echo site_url("ecrm/ri_torcida_torcedor/detalhe/0"); ?>';
	}

	$(document).ready( function () {
		filtrar(); 
	});


	function setBrinde(fl_brinde, cd_torcedor)
	{
		url="<?php echo site_url('ecrm/ri_torcida_torcedor/brinde'); ?>";
		$.post( url, { fl_brinde:fl_brinde, cd_torcedor:cd_torcedor }, function(data){ });		
	}
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start( $abas );

	$config['button'][]=array('Etiquetas', 'etiqueta();');
	echo form_list_command_bar($config);
	
	echo form_start_box_filter('filter_bar', 'Filtros', true);
		echo form_default_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt Inclusão:');
		
		$ar_brinde = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
		echo form_default_dropdown('fl_brinde', 'Brinde:', $ar_brinde, "style='width:100%;" );
		
		$ar_liberado = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
		echo form_default_dropdown('fl_liberado', 'Liberado:', $ar_liberado, "style='width:100%;" );
				
	echo form_end_box_filter();
?>

<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end(''); 
$this->load->view('footer');
?>