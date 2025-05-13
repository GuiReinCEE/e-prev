<?php
	set_title('Eventos Institucionais - Inscrição via Site');
	$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/evento_institucional_inscricao"); ?>';
	}
</script>
<?php	
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_inscricao', 'Inscrição via Site', TRUE, 'location.reload();');
	echo aba_start( $abas );	

	echo form_start_box('def','Escolha um evento para inscrever',FALSE);
		$body=array();
		$head = array( 
			'#',
			'Data',
			'Evento',
			'Local',
			'Cidade'
		);

		foreach( $eventos as $item )
		{
			$body[] = array(
				anchor("http://www.fundacaoceee.com.br/inscricao_evento.php?id=".$item['cd_evento'], "[Inscrever]",'target="blank"'),
				$item["dt_inicio"],
				array(anchor("http://www.fundacaoceee.com.br/inscricao_evento.php?id=".$item['cd_evento'], $item["nome"],'target="blank"'),"text-align:left;"),
				array($item["local_evento"],"text-align:left;"),
				array($item["nome_cidade"],"text-align:left;")
			);
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;
		echo $grid->render();

	echo form_end_box('def',FALSE);
	
	echo aba_end(''); 
?>
<script>
	jQuery(function($)
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'CaseInsensitiveString',
			'DateBR',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString'
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
		ob_resul.sort(1, true);
	});
</script>
<?php
	$this->load->view('footer');
?>