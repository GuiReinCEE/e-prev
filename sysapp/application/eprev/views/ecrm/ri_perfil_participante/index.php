<?php
	set_title('GRI - Perfil Participante');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		plano();
		planoEmpresa();
	}
	
	function plano()
	{
		$("#result_div_plano").html("<?php echo loader_html(); ?>");

		$.post( '<?php echo base_url().index_page(); ?>/ecrm/ri_perfil_participante/planoListar',
			{}
			,
			function(data)
			{
				$("#result_div_plano").html(data);
				plano_result();
			}
		);
	}

	function plano_result()
	{
		var ob_resul = new SortableTable(document.getElementById("tabela_plano"),
		[
			'CaseInsensitiveString',  
			'Number',  
			'Number',  
			'Number',  
			'Number'
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
		ob_resul.sort(0, false);
	}	
	
	function planoEmpresa()
	{
		$("#result_div_plano_empresa").html("<?php echo loader_html(); ?>");

		$.post( '<?php echo base_url().index_page(); ?>/ecrm/ri_perfil_participante/planoEmpresa',
			{}
			,
			function(data)
			{
				$("#result_div_plano_empresa").html(data);
				plano_empresa_result();
			}
		);

	}

	function plano_empresa_result()
	{
		var ob_resul = new SortableTable(document.getElementById("tabela_plano_empresa"),
		[
			'CaseInsensitiveString',  
			'CaseInsensitiveString',  
			'Number',  
			'Number',  
			'Number',  
			'Number'
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
		ob_resul.sort(0, false);
	}	
	
	function ir_sexo()
	{
		location.href='<?php echo site_url("ecrm/ri_perfil_participante/sexo"); ?>';
	}

	function ir_idade()
	{
		location.href='<?php echo site_url("ecrm/ri_perfil_participante/idade"); ?>';
	}	
</script>
<?php
	$abas[] = array('aba_lista', 'Plano', TRUE, 'location.reload();');
	$abas[] = array('aba_lista', 'Sexo', FALSE, 'ir_sexo();');
	$abas[] = array('aba_lista', 'Idade', FALSE, 'ir_idade();');
	echo aba_start( $abas );
	
	echo '<center><span style="color:red; font-weight: bold;">O TOTAL OFICIAL DE PARTICIPANTES � fornecido somente pela GAP ou GA<BR>Dados correspondentes a data atual ('.date("d/m/Y G:i:s").')</span></center>';
	
	echo form_start_box("legenda_box", "Legenda");
		echo form_default_row('', '<b>ATIVOS:</b>', "<span style='font-size: 80%'>s�o todos os participantes em folha na Patrocinadora, Instituidor ou fora de folha como: participante em atividade, apos. CTP (plano �nico), Apos. Ex-Aut�rquico, autopatrocinadados, Vesting/BPD, car�ncia, aguardando documenta��o, contempla, inclusive,  o aux�lio-doen�a, contrato suspenso e o aux�lio-reclus�o;</span>");
		echo form_default_row('', '<b>ASSISTIDOS:</b>', "<span style='font-size: 80%'>s�o os participantes que est�o recebendo complementa��o pela Funda��o como: invalidez,  tempo de servi�o/contribui��o/normal,  antecipada/proporcional, idade, aposentadoria em Vesting/BPD e CTP saldado;</span>");
		echo form_default_row('', '<b>PENS�ES:</b>', "<span style='font-size: 80%'>s�o os <u>RE�s</u> que tem pensionista recebendo pela Funda��o (pens�es por RE);</span>");
		echo form_default_row('', '<b>PENSIONISTAS:</b>', "<span style='font-size: 80%'>s�o <u>os benefici�rios</u> que recebem a pens�o pela Funda��o (total de pensionistas).</span>");
	echo form_end_box("legenda_box");	
	
	echo form_start_box("plano_box", "Resumo", FALSE);
		echo '<div id="result_div_plano"></div>';
	echo form_end_box("plano_box",FALSE);	
	
	echo form_start_box("plano_empresa_box", "Empresa", FALSE);
		echo '<div id="result_div_plano_empresa"></div>';
	echo form_end_box("plano_empresa_box",FALSE);		
	
?>
<br>
<br>
<br>
<script>
	filtrar();
</script>
<?php
	echo aba_end(''); 
	$this->load->view('footer');
?>