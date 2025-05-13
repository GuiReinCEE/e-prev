<?php
	set_title('Reclamatória - Anexo');
	$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/atendimento_reclamatoria"); ?>';
	}
	
	function ir_retorno()
	{
		location.href='<?php echo site_url("ecrm/atendimento_reclamatoria/retorno/0/".$cd_atendimento_reclamatoria); ?>';
	}
	
	function ir_detalhe()
	{
		location.href='<?php echo site_url("ecrm/atendimento_reclamatoria/detalhe/".$cd_atendimento_reclamatoria); ?>';
	}	

	function ir_acompanhamento()
	{
		location.href='<?php echo site_url("ecrm/atendimento_reclamatoria/acompanhamento/".$cd_atendimento_reclamatoria); ?>';
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
        ob_resul.sort(1, true);
    }
	
	function excluir(cd_atendimento_reclamatoria_arquivo)
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
		{
			location.href='<?php echo site_url("ecrm/atendimento_reclamatoria/excluir_anexo/".$cd_atendimento_reclamatoria); ?>' + "/" + cd_atendimento_reclamatoria_arquivo;
		}
	}
	
	$(function(){
		configure_result_table();
	});	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Reclamatória', FALSE, 'ir_detalhe();');
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	$abas[] = array('aba_anexo', 'Anexo', TRUE, 'location.reload();');

	echo aba_start($abas);
	echo form_open('ecrm/atendimento_reclamatoria/salvar_anexo');
		echo form_start_box("default_box", "Anexo");
			echo form_default_hidden("cd_atendimento_reclamatoria", "", $cd_atendimento_reclamatoria);
			echo form_default_row('', '', '');
			echo form_default_upload_multiplo('arquivo_m', 'Arquivo :*', 'atendimento_reclamatoria', 'validaArq');
			echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
		echo form_end_box("default_box");
	echo form_close();

$body=array();
$head = array( 
	'Dt Inclusão',
	'Arquivo',
	'Usuário',
	''
);

foreach( $collection as $item )
{
    $body[] = array(
		$item['dt_inclusao'],
		array(anchor(base_url().'up/atendimento_reclamatoria/' . $item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), "text-align:left;"),
		$item['nome'],
		'<a href="javascript:void(0);" onclick="excluir('.$item['cd_atendimento_reclamatoria_arquivo'].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();

echo br(2);

echo aba_end();
$this->load->view('footer_interna');