<?php
set_title('Parecer Enquadramento');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(array(), 'valida_arquivo(form)');
?>

	function ir_lista()
    {
        location.href='<?php echo site_url("gestao/parecer_enquadramento_cci"); ?>';
    }
	
	function ir_cadastro()
	{
		location.href='<?php echo site_url("gestao/parecer_enquadramento_cci/cadastro/".$row['cd_parecer_enquadramento_cci']); ?>';
	}
	
	function valida_arquivo(form)
    {
        if($('#arquivo').val() == '' && $('#arquivo_nome').val() == '')
        {
            alert('Nenhum arquivo foi anexado.');
            return false;
        }
        else
        {
            if( confirm('Salvar?') )
            {
                form.submit();
            }
        }
    }
	
	function validaArq(enviado, nao_enviado, arquivo)
	{
		$("form").submit();
	}	
	
	function load()
    {
		$('#result_div').html("<?php echo loader_html(); ?>");

        $.post( '<?php echo site_url('gestao/parecer_enquadramento_cci/listar_anexo'); ?>',
		{
			cd_parecer_enquadramento_cci : $('#cd_parecer_enquadramento_cci').val()
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
			'DateTimeBR', 
			'CaseInsensitiveString', 
			'CaseInsensitiveString', 
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
        ob_resul.sort(0, true);
    }
	
	function excluir(cd_parecer_enquadramento_cci_anexo)
	{
		var confirmacao = 'Deseja excluir o anexo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url("gestao/parecer_enquadramento_cci/excluir_anexo/".$row['cd_parecer_enquadramento_cci']); ?>/' + cd_parecer_enquadramento_cci_anexo;
		}
	}
	
	$(function(){
		load();
	});
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_cadastro', 'Anexo', TRUE, 'location.reload();');

echo aba_start($abas);
	echo form_open('gestao/parecer_enquadramento_cci/salvar_anexo');
		echo form_start_box( "default_box", "Parecer" );
			echo form_default_hidden("cd_parecer_enquadramento_cci", "", $row['cd_parecer_enquadramento_cci']);
			echo form_default_row('nr_ano_numero', 'Ano/Número :', $row['nr_ano_numero']);
			echo form_default_row('dt_inclusao', 'Dt Cadastro :', $row['dt_inclusao']);
			echo form_default_row('usuario_cadastro', 'Usuário Cadastrado :', $row['usuario_cadastro']);
			if(trim($row['dt_envio']) != '')
			{
				echo form_default_row('dt_envio', 'Dt Envio :', $row['dt_envio']);
				echo form_default_row('usuario_envio', 'Usuário Envio :', $row['usuario_envio']);
			}
			
			if(trim($row['dt_encerrado']) != '')
			{
				echo form_default_row('dt_encerrado', 'Dt Encerrado :', $row['dt_encerrado']);
				echo form_default_row('usuario_encerrado', 'Usuário Encerrado :', $row['usuario_encerrado']);
			}
				
		echo form_end_box("default_box");
		echo form_start_box("default_anexo_box", "Anexo");
			echo form_default_upload_multiplo('arquivo_m', 'Arquivo :*', 'parecer_enquadramento_cci', 'validaArq');
		echo form_end_box("default_box");
	echo form_close();
echo'<div id="result_div"></div>';

echo br(2);

echo aba_end();
$this->load->view('footer_interna');
?>