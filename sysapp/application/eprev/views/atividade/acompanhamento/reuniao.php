<?php
set_title('Acompanhamento de Projetos - Reuniões');
$this->load->view('header');
?>
<script>
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
			"DateBR",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateTimeBR",
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

	function ir_previsao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/previsao/".intval($row['cd_acomp'])); ?>';
	}	
	
	function nova_reuniao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/cadastro_reuniao/".intval($row['cd_acomp'])); ?>';
	}
	
	function imprimir_reuniao(cd_reuniao)
    {
        window.open('<?php echo site_url("atividade/acompanhamento/imprimir_reuniao/".intval($row['cd_acomp'])); ?>/'+cd_reuniao);
    }
	
	function imprimir()
    {
        window.open('<?php echo site_url("atividade/acompanhamento/imprimir_reuniao/".intval($row['cd_acomp'])); ?>');
    }
	
	function enviar_email(cd_reuniao)
	{
		if(confirm('Deseja enviar a reunião para os envolvidos?\n\n'))
		{
			location.href='<?php echo site_url("atividade/acompanhamento/email_reuniao/".intval($row['cd_acomp'])); ?>/' + cd_reuniao;
		}
	}
	
	$(function(){
		configure_result_table();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Acompanhamento', FALSE, 'ir_cadastro();');
$abas[] = array('aba_reuniao', 'Reuniões', TRUE, 'location.reload();');
$abas[] = array('aba_etapas', 'Etapas', FALSE, 'ir_etapa();');
$abas[] = array('aba_previsao', 'Previsão', FALSE, 'ir_previsao();');	
	
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
			echo button_save("Nova Reunião", "nova_reuniao()");
		}
		echo button_save("Imprimir", 'imprimir();', 'botao_disabled');
	echo form_command_bar_detail_end();	
	
	$body = array();
	$head = array( 
		'Data',
		'Resumo',
		'Presentes',
		'Motivo não ocorrência',
		'Dt Email',
		'Anexo', 
		''
	);
		
	foreach( $arr_reuniao as $item )
	{
		if (array_key_exists($item["cd_reuniao"], $arr_reuniao_envolvido))
		{
			$envolvidos = $item["envolvidos"].implode(", ",$arr_reuniao_envolvido[$item["cd_reuniao"]]);
		}
		else
		{
			$envolvidos = $item["envolvidos"];
		}

		$body[] = array(					 
			anchor('atividade/acompanhamento/cadastro_reuniao/'.$row['cd_acomp'].'/'.$item['cd_reuniao'], $item["dt_reuniao"]),
			array(nl2br($item["descricao"].br(2).$item["assunto"]),'text-align:justify;'),
			$envolvidos,
			array($item["motivo"],'text-align:justify;'),
			$item["dt_email"],
			(((trim($item["ds_arquivo_fisico"]) != "") OR (intval($item["tl_anexo"]) > 0)) ? "Sim" : "Não"),
			'<a href="javascript:void(0)" onclick="imprimir_reuniao('.$item['cd_reuniao'].');">[imprimir]</a> '.
			'<a href="javascript:void(0)" onclick="enviar_email('.$item['cd_reuniao'].');">[enviar email]</a>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();		
echo aba_end();
$this->load->view('footer_interna');
?>