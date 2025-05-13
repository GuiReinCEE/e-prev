<?php
set_title('Eleições - Solicita Kit');
$this->load->view('header');
?>
<script>
	$(function(){
	   load(); 
	})

	function filtrar()
	{
		load();
	}

	function load()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");

		$.post( '<?php echo base_url() . index_page(); ?>/gestao/solicita_kit/listar',
		{
			cd_empresa : $('#cd_empresa').val(),
			cd_registro_empregado : $('#cd_registro_empregado').val(),
			seq_dependencia : $('#seq_dependencia').val(),
			nome : $('#nome').val(),
			cd_solicita_kit_tipo : $('#cd_solicita_kit_tipo').val(),
			cd_usuario_inclusao : $('#cd_usuario_inclusao').val(),
			cd_usuario_envio : $('#cd_usuario_envio').val(),
			dt_solicitacao_ini : $('#dt_solicitacao_ini').val(),
			dt_solicitacao_fim : $('#dt_solicitacao_fim').val(),
			dt_envio_ini : $('#dt_envio_ini').val(),
			dt_envio_fim : $('#dt_envio_fim').val(),
			fl_enviado : $('#fl_enviado').val()
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
			null,
			'DateTimeBR',
			'DateBR',
			'RE',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
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
	}

	function checkAll()
    {
        var ipts = $("#table-1>tbody").find("input:checkbox");
        var check = document.getElementById("checkboxCheckAll");
	 
        check.checked ?
            jQuery.each(ipts, function(){
            this.checked = true;
        }) :
            jQuery.each(ipts, function(){
            this.checked = false;
        });
    }	

	function novo()
	{
		location.href='<?php echo site_url("gestao/solicita_kit/cadastro"); ?>/'+$('#cd_empresa').val()+'/'+$('#cd_registro_empregado').val()+'/'+$('#seq_dependencia').val();
	}
	
	function enviar_todos()
	{
		var arr = new Array();
	
		$("input[name='solicita_kit[]']").each(function(){
		   arr.push($(this).val());
		});
	
		var confirmacao = 'Deseja enviar a solicitação do kit?\n\n'+
					'Clique [Ok] para Sim\n\n'+
					'Clique [Cancelar] para Não\n\n';
			
		if(confirm(confirmacao))
		{
			$.post( '<?php echo site_url('/gestao/solicita_kit/enviar_todos') ?>',
			{
				'solicita_kit[]' : arr
			},
			function(data)
			{
				load();
			});
		}
		
	}

	function enviar(cd_solicita_kit)
	{
		var confirmacao = 'Deseja enviar a solicitação do kit?\n\n'+
					'Clique [Ok] para Sim\n\n'+
					'Clique [Cancelar] para Não\n\n';
			
		if(confirm(confirmacao))
		{
		   location.href='<?php echo site_url("gestao/solicita_kit/enviar/"); ?>/'+cd_solicita_kit;
		}
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][]=array('Novo', 'novo()');

$arr[] = array('text' => 'Sim', 'value' => 'S');
$arr[] = array('text' => 'Não', 'value' => 'N');

echo aba_start( $abas );
    echo form_list_command_bar($config);
	echo form_start_box_filter();
		echo filter_participante( array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_re'), "RE:", array('cd_empresa' => $cd_empresa, 'cd_registro_empregado' => $cd_registro_empregado, 'seq_dependencia' => $seq_dependencia), TRUE, FALSE );	
		echo filter_text('nome', 'Nome:');
		echo filter_dropdown('cd_usuario_inclusao', 'Solicitante:', $arr_solicitante);
		echo filter_dropdown('fl_enviado', 'Enviado:', $arr);
		echo filter_dropdown('cd_usuario_envio', 'Enviado por:', $arr_enviados);
		echo filter_dropdown('cd_solicita_kit_tipo', 'Tipo:', $arr_tipo);
		echo filter_date_interval('dt_solicitacao_ini', 'dt_solicitacao_fim', 'Período Solicitação:',calcular_data('','1 year'), date('d/m/Y'));
		echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Período Envio:');
    echo form_end_box_filter();
echo aba_end();
?>

<div id="result_div"></div>
<br />

<?php $this->load->view('footer'); ?>.