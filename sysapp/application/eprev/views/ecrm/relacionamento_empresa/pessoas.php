<?php
set_title('Relacionamento - Empresas');
$this->load->view('header');
?>

<script>
	function ir_lista()
	{
		location.href='<?php echo site_url( "ecrm/relacionamento_empresa"); ?>';
	}

	function ir_cadastro()
	{
		location.href="<?php echo site_url('ecrm/relacionamento_empresa/cadastro/'.$cd_empresa); ?>";
	}

	function ir_contato()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa/contato/".intval($cd_empresa)); ?>';
	}
	
	function ir_anexo()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa/anexo/".$cd_empresa); ?>';
	}
	
	function novo()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_pessoa/cadastro/0/".$cd_empresa); ?>';
	}
	
	function ir_agenda()
	{
		location.href='<?php echo site_url(  "ecrm/relacionamento_empresa/agenda/".intval($cd_empresa)); ?>';
	}

	function listarPessoas()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");
	
		$.post( '<?php echo site_url('/ecrm/relacionamento_empresa/pessoasListar'); ?>',
		{
			cd_empresa    : $('#cd_empresa').val(),
			fl_count_grid : "S"
		},
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});
	}	
	
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'CaseInsensitiveString', 
			'CaseInsensitiveString', 
			'CaseInsensitiveString', 
			'CaseInsensitiveString', 
			'CaseInsensitiveString'
		]);
		ob_resul.onsort = function()
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
	
	$(function(){
		listarPessoas()
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_empresa', 'Empresa', FALSE, 'ir_cadastro();');
$abas[] = array('aba_contato', 'Contato', FALSE, 'ir_contato();');
$abas[] = array('aba_agenda', 'Agenda', FALSE, 'ir_agenda();');
$abas[] = array('aba_pessoa', 'Pessoa', TRUE, 'location.reload();');
$abas[] = array('aba_pessoa', 'Anexo', FALSE, 'ir_anexo();');


echo aba_start( $abas );
	echo form_hidden("cd_empresa", $cd_empresa);
	echo button_save('Nova Pessoa', 'novo()');
	echo '<div id="result_div"></div>';
echo aba_end();

$this->load->view('footer_interna');
?>