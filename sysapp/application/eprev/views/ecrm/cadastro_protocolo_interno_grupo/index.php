<?php
set_title("Protocolo Interno - Grupo");
$this->load->view("header");
?>
<script>
function filtrar()
{
	$("#result_div").html("<?= loader_html() ?>");
	
    $.post("<?= site_url("ecrm/cadastro_protocolo_interno_grupo/listar") ?>",
	$("#filter_bar_form").serialize(),
    function(data)
    {
		$("#result_div").html(data);
        configure_result_table();
    });
}

function configure_result_table()
{
    var ob_resul = new SortableTable(document.getElementById("table-1"),
    [
		"CaseInsensitiveString",
		"CaseInsensitiveString",
        null,
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

function novo()
{
    location.href="<?= site_url("ecrm/cadastro_protocolo_interno_grupo/cadastro") ?>";
}

function excluir(cd_documento_recebido_grupo)
{
	if(confirm("Deseja excluir o grupo?"))
	{
		location.href="<?= site_url("ecrm/cadastro_protocolo_interno_grupo/excluir") ?>/"+cd_documento_recebido_grupo;
	}
}

$(function(){
	filtrar();
})

</script>
<?php
$abas[] = array("aba_lista", "Lista", TRUE, "location.reload();");

$config["button"][] = array("Novo Grupo", "novo()");

echo aba_start( $abas );
    echo ((gerencia_in(array("GI"))) ? form_list_command_bar($config) : form_list_command_bar());
    echo form_start_box_filter();
		echo filter_dropdown("cd_documento_recebido_grupo", "Grupo Destino:", $arr_grupo);
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view("footer"); 
?>