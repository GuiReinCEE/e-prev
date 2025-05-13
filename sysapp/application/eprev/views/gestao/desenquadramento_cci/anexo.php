<?php
set_title('Desenquadramento - Anexo');
$this->load->view('header');
?>
<script>
    <?php
		echo form_default_js_submit(array(), 'valida_arquivo(form)');
    ?>
	function ir_lista()
	{
		location.href='<?php echo site_url("gestao/desenquadramento_cci"); ?>';
	}

	function ir_cadastro()
	{
		location.href='<?php echo site_url("gestao/desenquadramento_cci/cadastro/".$cd_desenquadramento_cci); ?>';
	}
	
	function ir_acompanhamento()
	{
		location.href='<?php echo site_url("gestao/desenquadramento_cci/acompanhamento/".$cd_desenquadramento_cci); ?>';
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
    
    function excluir(cd_desenquadramento_cci_anexo)
	{
		var confirmacao = 'Deseja excluir o anexo?\n\n'+
				'Clique [Ok] para Sim\n\n'+
				'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url("gestao/desenquadramento_cci/excluir_anexo/".$cd_desenquadramento_cci); ?>/' + cd_desenquadramento_cci_anexo;
		}
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
    
    $(function(){
		configure_result_table();
    });
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Cadastro', FALSE, 'ir_cadastro('.$cd_desenquadramento_cci.');');
$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento('.$cd_desenquadramento_cci.');');
$abas[] = array('aba_lista', 'Anexo', TRUE, 'location.reload();');

$body = array();
$head = array('Código','Dt Inclusão','Arquivo','Usuário','');
foreach( $collection as $item )
{

    $body[] = array(
		$item['cd_desenquadramento_cci_anexo'],
		$item['dt_inclusao'],
		array(anchor(base_url().'up/desenquadramento_cci_anexo/' . $item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), "text-align:left;"),
		$item['nome'],
		'<a href="javascript:void(0);" onclick="excluir('.$item['cd_desenquadramento_cci_anexo'].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
    echo form_open('gestao/desenquadramento_cci/salvar_anexo', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Anexo" );
            echo form_default_hidden('cd_desenquadramento_cci', "", $cd_desenquadramento_cci );
            echo form_default_upload_multiplo('arquivo_m', 'Arquivo :*', 'desenquadramento_cci_anexo', 'validaArq');
        echo form_end_box("default_box");
    echo form_close();
	echo '<div id="result_div">'.$grid->render().'</div>';
	echo br(5);
echo aba_end();

$this->load->view('footer_interna');
?>