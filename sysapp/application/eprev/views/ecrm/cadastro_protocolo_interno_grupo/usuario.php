<?php
set_title("Protocolo Interno - Grupo");
$this->load->view('header');
?>
<script>

<?php echo form_default_js_submit(array("cd_usuario"));?>

function ir_lista()
{
    location.href="<?= site_url("ecrm/cadastro_protocolo_interno_grupo") ?>";
}

function ir_cadastro()
{
    location.href="<?= site_url("ecrm/cadastro_protocolo_interno_grupo/cadastro/".intval($row["cd_documento_recebido_grupo"])) ?>";
}

function excluir(cd_documento_recebido_grupo_usuario)
{
	if(confirm("Deseja excluir o grupo?"))
	{
		location.href="<?= site_url("ecrm/cadastro_protocolo_interno_grupo/excluir_usuario/".intval($row["cd_documento_recebido_grupo"])) ?>/"+cd_documento_recebido_grupo_usuario;
	}
}

$(function(){
    var ob_resul = new SortableTable(document.getElementById("table-1"),
    [
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
    ob_resul.sort(0, true);
});

</script>
<?php
$abas[] = array("aba_lista", "Lista", FALSE, "ir_lista();");
$abas[] = array("aba_lista", "Cadastro", FALSE, "ir_cadastro();");
$abas[] = array("aba_lista", "Usuários", TRUE, "location.reload();");

$body = array();
$head = array(
	"Usuário",
	""
);

foreach ($collection as $item)
{	
	$body[] = array(
		array($item["usuario"], "text-align:left"),
		'<a href="javascript:void(0);" onclick="excluir('.$item["cd_documento_recebido_grupo_usuario"].')">[excluir]</a>'
	);
}

$this->load->helper("grid");
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
	echo form_open("ecrm/cadastro_protocolo_interno_grupo/salvar_usuario");
		echo form_start_box("default_box", "Cadastro");
			echo form_hidden("cd_documento_recebido_grupo", $row["cd_documento_recebido_grupo"]);
			echo form_default_row("ds_nome", "Grupo :", $row["ds_nome"]);
			echo form_default_dropdown("cd_usuario", "Usuário :*", $arr_usuario);
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();     
			echo button_save("Salvar");
        echo form_command_bar_detail_end();
	echo form_close();
	echo $grid->render();
	echo br();
echo aba_end();

$this->load->view("footer_interna");
?>