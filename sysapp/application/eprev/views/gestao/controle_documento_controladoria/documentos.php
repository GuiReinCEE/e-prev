<?php
set_title('Controle Documentos - Lista');
$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href = '<?= site_url('gestao/controle_documento_controladoria/minhas') ?>';
	}

	function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
        	"CaseInsensitiveString",
			"DateTimeBR",
			"DateTimeBR",
			"DateBR",
			"CaseInsensitiveString"
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
$abas[] = array('aba_documentos', 'Documentos', TRUE , 'location.reload();');

$head = array( 
	'Arquivo',
	'Dt. Atualização',
	'Dt. Divulgação ',
	'Dt. Referência',
	'Descrição'
);

$body = array();

foreach($collection as $item )
{	
    $body[] = array(
		array(anchor(base_url().'up/controle_documento_controladoria/' . $item['arquivo'], $item['arquivo_nome'] , array('target' => '_blank')), "text-align:left;"),
		$item['dt_inclusao'],
		$item['dt_envio'],
		$item['dt_referencia'],
		array(nl2br($item['ds_controle_documento_controladoria']),'text-align:left;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start($abas);
	echo form_start_box('default_box', 'Documento');
			echo form_default_hidden('cd_controle_documento_controladoria_tipo', '', $row['cd_controle_documento_controladoria_tipo'] );	
			echo form_default_row('ds_controle_documento_controladoria_tipo', 'Tipo Documento:', $row['ds_controle_documento_controladoria_tipo'] );
	echo form_end_box('default_box');
	echo form_close();
	 echo $grid->render();
echo aba_end();
$this->load->view('footer');
?>