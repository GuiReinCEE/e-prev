<?php
set_title('Protocolo Correspondência Recebida');
$this->load->view('header');
?>
<script>

<?php echo form_default_js_submit(array('cd_usuario'));?>

function ir_lista()
{
    location.href='<?php echo site_url('ecrm/correspondencia_recebida_grupo');?>';
}

function ir_cadastro()
{
    location.href='<?php echo site_url('ecrm/correspondencia_recebida_grupo/cadastro/'.intval($row['cd_correspondencia_recebida_grupo']));?>';
}

function excluir(cd_correspondencia_recebida_grupo_usuario)
{
	if(confirm("Deseja excluir o grupo?"))
	{
		location.href='<?php echo site_url("ecrm/correspondencia_recebida_grupo/excluir_usuario/".intval($row['cd_correspondencia_recebida_grupo'])); ?>/'+cd_correspondencia_recebida_grupo_usuario;
	}
}

$(function(){
    var ob_resul = new SortableTable(document.getElementById("table-1"),
    [
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
});

</script>
<?php
$abas[] = array( 'aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', false, 'ir_cadastro();');
$abas[] = array('aba_lista', 'Usuários', true, 'location.reload();');

$body = array();
$head = array(
	'Usuário',
	''
);

foreach ($collection as $item)
{	
	$body[] = array(
		array($item['usuario'], 'text-align:left'),
		'<a href="javascript:void(0);" onclick="excluir('.$item['cd_correspondencia_recebida_grupo_usuario'].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
	echo form_open('ecrm/correspondencia_recebida_grupo/salvar_usuario');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_hidden('cd_correspondencia_recebida_grupo', $row['cd_correspondencia_recebida_grupo']);
			echo form_default_row('ds_nome', 'Descrição :', $row['ds_nome']);
			echo form_default_dropdown('cd_usuario', 'Usuário :*', $arr_usuario);
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();     
			echo button_save("Salvar");
        echo form_command_bar_detail_end();
	echo form_close();
	echo $grid->render();
	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>