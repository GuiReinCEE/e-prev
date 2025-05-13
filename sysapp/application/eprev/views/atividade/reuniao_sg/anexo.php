<?php
set_title('Reunião SG - Anexo');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(array(), 'valida_arquivo(form)');
?>

	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/reuniao_sg"); ?>';
    }

    function ir_parecer()
    {
        location.href='<?php echo site_url("atividade/reuniao_sg/parecer/".$row['cd_reuniao_sg']); ?>';
    }
	
	function ir_cadastro()
    {
        location.href='<?php echo site_url("atividade/reuniao_sg/detalhe/".$row['cd_reuniao_sg']); ?>';
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

        $.post( '<?php echo site_url('atividade/reuniao_sg/listar_anexo'); ?>',
		{
			cd_reuniao_sg : $('#cd_reuniao_sg').val()
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
			'Number',
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
        ob_resul.sort(1, true);
    }
	
	function excluir(cd_reuniao_sg_anexo)
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
		{
			location.href='<?php echo site_url("atividade/reuniao_sg/excluir_anexo/".$row['cd_reuniao_sg']); ?>' + "/" + cd_reuniao_sg_anexo;
		}
	}
	
	$(function(){
		load();
	});
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Agendamento', FALSE, 'ir_cadastro();');
$abas[] = array('aba_anexo', 'Anexo', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Parecer', FALSE, 'ir_parecer();');

echo aba_start($abas);
	echo form_open('atividade/reuniao_sg/salvar_anexo');
		echo form_start_box( "default_box", "Reunião SG" );
			echo form_default_text('cd_reuniao_sg', "Nº da Reunião: ", $row['cd_reuniao_sg'], "style='width:100%;border: 0px;' readonly");
			echo form_default_text('dt_inclusao', "Dt. Solicitação: ", $row, "style='width:100%;border: 0px;' readonly");
			echo form_default_text('usuario_cadastro', "Solicitante: ", $row['usuario_cadastro'], "style='width:100%;border: 0px;' readonly");
		echo form_end_box("default_box");

		echo form_start_box("default_box", "Anexo");
			echo form_default_hidden("cd_reuniao_sg", "", $row['cd_reuniao_sg']);
			
			echo form_default_upload_multiplo('arquivo_m', 'Arquivo :*', 'reuniao_sg', 'validaArq');
			
			echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
		echo form_end_box("default_box");
	echo form_close();
echo'<div id="result_div"></div>';

echo br(2);

echo aba_end();
$this->load->view('footer_interna');
?>