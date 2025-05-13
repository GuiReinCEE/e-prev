<?php
echo $this->load->view( "header" );
echo form_open( "dev/showmenu/submit" );
?>
<script>
function esconder()
{
	$('#detalhe').hide();
}

function preencher(cd,pai,ds,ln,or,rs)
{
	$('#detalhe').show();

	$('#cd_menu').val( cd );
	$('#cd_menu_pai').val( pai );
	$('#ds_menu').val( ds );
	$('#ds_href').val( ln );
	$('#nr_ordem').val( or );
	$('#ds_resumo').val( rs );

	listar_ordenacao(cd);

	$('#ds_menu').focus();
}

function salvar()
{
	url = "<?php echo site_url( 'dev/showmenu/salvar' ); ?>";
	$.post( 
		url,
		{
			cd_menu: $('#cd_menu').val(),
			cd_menu_pai: $('#cd_menu_pai').val(),
			ds_menu: $('#ds_menu').val(),
			ds_href: $('#ds_href').val(),
			nr_ordem: $('#nr_ordem').val(),
			ds_resumo: $('#ds_resumo').val()
		}, 
		function(data)
		{ 
			$('#output').html(data);
		}
	);
}

function desativar()
{
	if(confirm("Desativar?"))
	{
		url = "<?php echo site_url( 'dev/showmenu/desativar' ); ?>";
		$.post(
			url,
			{
				cd_menu: $('#cd_menu').val()
			},
			function(data)
			{ 
				$('#output').html(data);
			}
		);
	}
}

function listar_ordenacao(cd)
{
	url="<?php echo site_url('dev/showmenu/listar_ordenacao'); ?>";

	$.post(url,{cd_menu_pai:cd},function(data){ $('#ordenacao').html(data); });
}
</script>

<script src="<?php echo base_url();?>js/jquery-plugins/iutil.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>js/jquery-plugins/idrag.js" type="text/javascript"></script>
<script>
$(document).ready(
	function()
	{
		$('#detalhe').Draggable(
			{
				handle:	'#detalhe_title'
			}
		);
	}
);
</script>

<?php
echo form_start_box("detalhe", "Detalhe", FALSE, TRUE, " style='width:500px;position:absolute;' ");

	echo form_start_box("menu","Menu");
	echo form_default_text("cd_menu_pai", "Pai");
	echo form_default_text("cd_menu", "Código");
	echo form_default_text("ds_menu", "Menu", "", " style='width:300px;' ");
	echo form_default_text("ds_href", "Link", "", " style='width:300px;' ");
	echo form_default_integer("nr_ordem", "Ordem", "", " style='width:30px;' ");
	echo form_default_textarea("ds_resumo", "Resumo", "", " style='height:100px;width:300px;' ");
	echo form_default_row("", "", "<input type='button' value='Salvar' onclick='salvar();' /><input type='button' value='Desativar' onclick='desativar();' />&nbsp&nbsp&nbsp&nbsp<input value='Fechar' onclick='esconder();' type='button' />");
	echo form_end_box("menu");

	echo form_start_box("ordenacao_box","Ordenação",FALSE,TRUE);
	echo "<div id='ordenacao'></div>";
	echo form_end_box("ordenacao_box",FALSE);

	echo form_start_box("output_box","Output",FALSE);
	echo "<div id='output'></div>";
	echo form_end_box("output_box",FALSE);

echo form_end_box("detalhe", FALSE);

echo $menu;

echo form_close();
echo $this->load->view('footer');
