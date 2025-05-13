<?php
set_title('Cadastro de Pessoas');
$this->load->view('header');
?>
<script>
	<?php echo form_default_js_submit(array('dt_contato', 'ds_contato')); ?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_pessoa"); ?>';
	}

	function ir_cadastro()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_pessoa/cadastro/".$cd_pessoa); ?>';
	}

	function excluir_contato(id)
	{
		if ( confirm('Excluir contato?') )
		{
			location.href='<?php echo base_url().index_page(); ?>/ecrm/relacionamento_pessoa/excluir_contato/'+id;
			return true;
		}
		else return false;
	}
	
	function listar_contato()
	{		
		$('#result_div').html("<?php echo loader_html(); ?>");
				
		$.post( '<?php echo site_url('/ecrm/relacionamento_pessoa/listar_contato'); ?>',
		{
			cd_pessoa : $('#cd_pessoa').val()
		},
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
			"DateBR", 
			"CaseInsensitiveString", 
			"CaseInsensitiveString", 
			null
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
	
	function adicionar_contato()
	{
		$.post( '<?php echo site_url('/ecrm/relacionamento_pessoa/salvar_contato'); ?>',
		{
			cd_pessoa  : $('#cd_pessoa').val(),
			dt_contato : $('#dt_contato').val(),
			ds_contato : $('#ds_contato').val(),
		},
		function(data)
		{
			$('#dt_contato').val('');
			$('#ds_contato').val('');
			
			listar_contato();
		});
	}
	
	function excluir_contato(cd_pessoa_contato)
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir o contato?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			$.post( '<?php echo site_url('/ecrm/relacionamento_pessoa/excluir_contato'); ?>',
			{
				cd_pessoa_contato : cd_pessoa_contato
			},
			function(data)
			{			
				listar_contato();
			});
		}
	}
	
	$(function(){
		listar_contato();
	});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_pessoa', 'Pessoa', FALSE, 'ir_cadastro();');
$abas[] = array('aba_contato', 'Contato', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_open('ecrm/relacionamento_pessoa/salvar_contato');
		echo form_start_box("contatos_box", "Contatos") ;
			echo form_hidden('cd_pessoa', $cd_pessoa);
			echo form_default_date("dt_contato", "Dt Contato: *");
			echo form_default_textarea("ds_contato", "Contato: *");
		echo form_end_box("contatos_box");
	echo form_close();
	echo form_command_bar_detail_start();
		echo button_save('Adicionar', 'adicionar_contato()');
	echo form_command_bar_detail_end();
	echo br();
	echo '<div id="result_div"></div>';
echo aba_end();	

$this->load->view('footer_interna');
?>