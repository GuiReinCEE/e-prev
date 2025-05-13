<?php
set_title('Acompanhamento de Projetos - Previsão');
$this->load->view('header');
?>
<script language="JavaScript">
	var ob_window = "";
	
	function win_previsao(cd_acomp, cd_previsao) 
	{
	    if(ob_window != "")
		{
			ob_window.close();
		}
		 
		if(cd_previsao == 0)
		{
			cd_previsao = "";
		}		 
		 
		var ds_url = "../../../../sysapp/application/migre/registro_previsao_projeto.php";
			ds_url += "?cd_acomp="   + cd_acomp;
			ds_url += "&cd_previsao=" + cd_previsao;		
		
		var nr_width = document.body.clientWidth - 50;;
		var nr_height = 425;
		var nr_left = ((screen.width - 10) - nr_width) / 2;
		var nr_top = ((screen.height - 80) - nr_height) / 2;

		ob_window = window.open(ds_url, "wPrevisao", "left="+nr_left+",top="+nr_top+",width="+nr_width+",height="+nr_height+",scrollbars=yes,resizable=yes,directories=no,location=no,menubar=yes,status=no,titlebar=no,toolbar=yes");		 				
	}	
	
	function win_previsao_relatorio(cd_acomp, cd_previsao) 
	{
	    if(ob_window != "")
		{
			ob_window.close();
		}

		var ds_url = "../../../../sysapp/application/migre/registro_previsao_projeto_rel.php";
			ds_url += "?cd_acomp="  + cd_acomp;
			ds_url += "&cd_previsao=" + cd_previsao;
		
		var nr_width = document.body.clientWidth - 50;
		var nr_height = document.body.clientHeight - 50;
		var nr_left = ((screen.width - 10) - nr_width) / 2;
		var nr_top = ((screen.height - 80) - nr_height) / 2;

		ob_window = window.open(ds_url, "wPrevisaoRel", "left="+nr_left+",top="+nr_top+",width="+nr_width+",height="+nr_height+",scrollbars=yes,resizable=yes,directories=no,location=no,menubar=yes,status=no,titlebar=no,toolbar=yes");		 		
	}		
</script>


<script>
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
			"CaseInsensitiveString",
			"CaseInsensitiveString",
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
		ob_resul.sort(0, true);
	}

	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/acompanhamento"); ?>';
	}
	
	function ir_cadastro()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/cadastro/".intval($row['cd_acomp'])); ?>';
	}	
	
	function ir_etapa()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/etapa/".intval($row['cd_acomp'])); ?>';
	}

	function ir_reuniao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/reuniao/".intval($row['cd_acomp'])); ?>';
	}	
	
	function nova_previsao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/cadastro_previsao/".intval($row['cd_acomp'])); ?>';
	}	
	
	function imprimir(cd_previsao)
    {
        window.open('<?php echo site_url("atividade/acompanhamento/imprimir_previsao/".intval($row['cd_acomp'])); ?>/'+cd_previsao);
    }
	
	$(function(){
		configure_result_table();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Acompanhamento', FALSE, 'ir_cadastro();');
$abas[] = array('aba_reuniao', 'Reuniões', FALSE, 'ir_reuniao();');
$abas[] = array('aba_etapas', 'Etapas', FALSE, 'ir_etapa();');
$abas[] = array('aba_previsao', 'Previsão', TRUE, 'location.reload();');	

$status = "Projeto em andamento";
$cor_status = "blue";

if (trim($row['dt_encerramento']) != '') 
{
	$status = 'Projeto encerrado em: '. $row['dt_encerramento'];
	$cor_status = "red";
}	

if (trim($row['dt_cancelamento']) != '') 
{
	$status = 'Projeto cancelado em: '. $row['dt_cancelamento'];
	$cor_status = "red";
}

$body = array();
$head = array( 
	'Mês/Ano',
	'Previsão',
	'Obs',
	''
);

foreach( $arr_previsao as $item )
{
	$body[] = array(				
        anchor(site_url("atividade/acompanhamento/cadastro_previsao/".intval($row['cd_acomp'])."/".intval($item['cd_previsao'])), $item['mes_ano']),	
		array(nl2br($item["descricao"]),'text-align:left;'),
		array(nl2br($item["obs"]),'text-align:left;'),
		'<a href="javascript:void(0)" onclick="imprimir('.intval($item['cd_previsao']).')">[imprimir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
	
echo aba_start( $abas );
	echo form_start_box( "default_box", "Acompanhamento" );
		echo form_default_hidden('cd_acomp', '', $row);
		echo form_default_text('cd_acomp_h', "Código :", intval($row['cd_acomp']), "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('ds_projeto', "Projeto :", $row, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('status', "Status :", $status, "style='color: ".$cor_status."; font-weight:bold; width:400px;border: 0px;' readonly" );	
	echo form_end_box("default_box");
	echo form_command_bar_detail_start();
		if ((trim($row['dt_encerramento']) == '') and (trim($row['dt_cancelamento']) == ''))
		{
			echo button_save("Nova Previsão", "nova_previsao()");
		}
		echo button_save("Imprimir", "imprimir()", 'botao_disabled');
	echo form_command_bar_detail_end();	
	echo $grid->render();	
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>