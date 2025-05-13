<?php
set_title('Relatório Auditoria');
$this->load->view('header');
?>
<script>
    function ir_lista()
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria"); ?>';
    }
    
    function ir_cadastro(cd_relatorio_auditoria)
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/cadastro"); ?>/'+cd_relatorio_auditoria;
    }
    
    function ir_equipe(cd_relatorio_auditoria)
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/equipe"); ?>/'+cd_relatorio_auditoria;
    }   
	
	function ir_constatacao(cd_relatorio_auditoria)
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/constatacao"); ?>/'+cd_relatorio_auditoria;
    }
	
	function ir_acompanhamento(cd_relatorio_auditoria)
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/acompanhamento"); ?>/'+cd_relatorio_auditoria;
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
    
    function excluir(cd_relatorio_auditoria_anexo)
	{
		var confirmacao = 'Deseja excluir o anexo?\n\n'+
				'Clique [Ok] para Sim\n\n'+
				'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url("gestao/relatorio_auditoria/excluir_anexo/".$cd_relatorio_auditoria); ?>/' + cd_relatorio_auditoria_anexo;
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
$abas[] = array('aba_lista', 'Cadastro', FALSE, 'ir_cadastro('.$cd_relatorio_auditoria.');');
$abas[] = array('aba_lista', 'Equipe', FALSE, 'ir_equipe('.$cd_relatorio_auditoria.');');
$abas[] = array('aba_nc', 'Constatação', FALSE, 'ir_constatacao('.$cd_relatorio_auditoria.');');
$abas[] = array('aba_lista', 'Registros Gerais', FALSE, 'ir_acompanhamento('.$cd_relatorio_auditoria.');');
$abas[] = array('aba_lista', 'Anexo', TRUE, 'location.reload();');

$body = array();
$head = array( 
	'Código',
	'Dt Inclusão',
	'Arquivo',
	'Usuário',
	''
);

foreach( $collection as $item )
{
    $link = '';

    if($fl_permissao)
    {
        $link = '<a href="javascript:void(0);" onclick="excluir('.$item['cd_relatorio_auditoria_anexo'].')">[excluir]</a>';
    }

    $body[] = array(
		$item['cd_relatorio_auditoria_anexo'],
		$item['dt_inclusao'],
		array(anchor(base_url().'up/relatorio_auditoria_anexo/' . $item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), "text-align:left;"),
		$item['nome'],
		$link
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
    if($fl_permissao)
    {
        echo form_open('gestao/relatorio_auditoria/salvar_anexo', 'name="filter_bar_form"');
            echo form_start_box( "default_box", "Anexo" );
                echo form_default_hidden('cd_relatorio_auditoria', "", $cd_relatorio_auditoria );
                echo form_default_upload_multiplo('arquivo_m', 'Arquivo :*', 'relatorio_auditoria_anexo', 'validaArq');
            echo form_end_box("default_box");
        echo form_close();
    }
    echo $grid->render();
	echo br(3);
echo aba_end();

$this->load->view('footer_interna');
?>