<?php
set_title('Intranet - '. $cd_gerencia);
$this->load->view('header');

$head = array('Arquivos');
$body = array();
foreach( $doc_collection as $item )
{
	$body[] = array(
		array(anchor_file($item['link'],$item['texto_link'], array('target' => '_black') ),'text-align:left')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->view_count = false;
$grid->head = $head;
$grid->body = $body;

?>
<style>
	.menu_intranet_gerencia {
		
		border-collapse: collapse; 
		border: 0px Solid #0046AD;
	}

	.menu_intranet_gerencia th {
		background: #0046AD;
		padding: 10px;
	}
	
	.menu_intranet_gerencia td{
		background: #E7F1FA;
	}

	.menu_intranet_gerencia td p{
		padding: 8px;
	}
	
	.mneu_intranet_gerencia a{
		color: #000000;
		font-family: Verdana,Arial,Helvetica,sans-serif;
		font-size: 10px;
		font-style: normal;
		font-weight: bold;
		line-height: 20px;
		text-decoration: none;
	}
</style>

<script>
	function processos()
	{
		$("#result_processos").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('gestao/processo/mapa/N') ?>",
		function(data)
		{
			$("#result_processos").html(data);
			$(".aba_definicao").css("width", "auto");
			$(".aba_definicao").css("min-width", "80%");
		});
	}

	function regimento_interno()
	{
		$("#result_regimento_interno").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('gestao/regimento_interno/intranet') ?>",
		function(data)
		{
			$("#result_regimento_interno").html(data);
			$(".aba_definicao").css("width", "auto");
			$(".aba_definicao").css("min-width", "80%");
		});
	}

	function regulamento()
	{
		$("#result_regulamento").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('gestao/regulamento/intranet') ?>",
		{
			nr_aba : <?= intval($nr_aba) ?>
		},
		function(data)
		{
			$("#result_regulamento").html(data);
			$(".aba_definicao").css("width", "auto");
			$(".aba_definicao").css("min-width", "80%");
		});
	}

	function politica()
	{
		$("#result_politica").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('gestao/politica/intranet') ?>",
		function(data)
		{
			$("#result_politica").html(data);
			$(".aba_definicao").css("width", "auto");
			$(".aba_definicao").css("min-width", "80%");
		});
	}

	function sumula()
	{
		$("#result_sumula").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('gestao/sumula/intranet') ?>",
		function(data)
		{
			$("#result_sumula").html(data);
			$(".aba_definicao").css("width", "auto");
			$(".aba_definicao").css("min-width", "80%");
		});
	}

	function sumula_conselho()
	{
		$("#result_sumula_conselho").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('gestao/sumula_conselho/intranet') ?>",
		function(data)
		{
			$("#result_sumula_conselho").html(data);
			$(".aba_definicao").css("width", "auto");
			$(".aba_definicao").css("min-width", "80%");
		});
	}

	function sumula_conselho_fiscal()
	{
		$("#result_sumula_conselho_fiscal").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('gestao/sumula_conselho_fiscal/intranet') ?>",
		function(data)
		{
			$("#result_sumula_conselho_fiscal").html(data);
			$(".aba_definicao").css("width", "auto");
			$(".aba_definicao").css("min-width", "80%");
		});
	}

	function planejamento_estrategico(cd_planejamento_estrategico)
	{
		$("#result_planejamento_estrategico").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('gestao/planejamento_estrategico/intranet') ?>",
		{
			nr_aba                      : <?= intval($nr_aba) ?>,
			cd_planejamento_estrategico : cd_planejamento_estrategico
		},
		function(data)
		{
			$("#result_planejamento_estrategico").html(data);
			$(".aba_definicao").css("width", "auto");
			$(".aba_definicao").css("min-width", "80%");
		});
	}

	function convenio_adesao()
	{
		$("#result_convenio_adesao").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('gestao/convenio_adesao/intranet') ?>",
		function(data)
		{
			$("#result_convenio_adesao").html(data);
			$(".aba_definicao").css("width", "auto");
			$(".aba_definicao").css("min-width", "80%");
		});
	}

	function estatuto()
	{
		$("#result_estatuto").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('gestao/estatuto/intranet') ?>",
		function(data)
		{
			$("#result_estatuto").html(data);
			$(".aba_definicao").css("width", "auto");
			$(".aba_definicao").css("min-width", "80%");
		});
	}

	function organograma()
	{
		$("#result_organograma").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('gestao/organograma/get_intranet') ?>",
		function(data)
		{
			$("#result_organograma").html('<img src="https://www.e-prev.com.br/cieprev/up/organograma/'+data+'">');

			$("#result_estatuto").html(data);
			$(".aba_definicao").css("width", "auto");
			$(".aba_definicao").css("min-width", "80%");
		});
	}

	function manual()
	{
		$("#result_manual").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('gestao/manual/intranet') ?>",
		function(data)
		{
			$("#result_manual").html(data);
			$(".aba_definicao").css("width", "auto");
			$(".aba_definicao").css("min-width", "80%");
		});
	}
	
	function codigo_etica()
	{
		$("#result_codigo_etica").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('gestao/codigo_etica/intranet') ?>",
		function(data)
		{
			$("#result_codigo_etica").html(data);
			$(".aba_definicao").css("width", "auto");
			$(".aba_definicao").css("min-width", "80%");
		});
	}

	$(function(){
		<?php
		switch (intval($row['cd_intranet'])) 
		{
			case 10403:
		    #PROCESSOS#
		        echo 'processos();';
		        break;
		    case 10412:
		    #REGIMENTO INTERNO#
		    	echo 'regimento_interno();';
		    	break;
		    case 10413:
		    #REGULAMENTOS#
		    	echo 'regulamento();';
		    	break;
		    case 10414:
		    #ESTATUTO#
		    	echo 'estatuto();';
		    	break;
		    case 10416:
			#POLÍTICAS#
			   	echo 'politica();';
			    break;
			case 10422:
			#SÚMULA DIRETORIA#
			   	echo 'sumula();';
			    break;
			case 10425:
			#SÚMULA CONSELHO DELIBERATIVO#
			   	echo 'sumula_conselho();';
			    break;
			case 10426:
			#SÚMULA CONSELHO FISCAL#
			   	echo 'sumula_conselho_fiscal();';
			    break;
			case 10434:
			#PLANEJAMENTO ESTRATÉGIVO 2017 - 2018#
			   	echo 'planejamento_estrategico(1);';
			    break;
			case 10475:
			#PLANEJAMENTO ESTRATÉGIVO 2019 - 2023#
			   	echo 'planejamento_estrategico(2);';
			    break;
			case 10525:
			#PLANEJAMENTO ESTRATÉGIVO 2019 - 2023 (1ª revisão)#
			   	echo 'planejamento_estrategico(3);';
			    break;
			case 10465:
			#CONVÊNIOS DE ADESÃO#
				echo 'convenio_adesao();';
				break;
			case 10537:
			#PLANEJAMENTO ESTRATÉGIVO 2019 - 2023 (2ª revisão)#
			   	echo 'planejamento_estrategico(4);';
			    break;
			case 10553:
			#PLANEJAMENTO ESTRATÉGIVO 2019 - 2023 (3ª revisão)#
			   	echo 'planejamento_estrategico(5);';
			    break;
			case 10575:
			#PLANEJAMENTO ESTRATÉGIVO 2019 - 2023 (4ª revisão)#
			   	echo 'planejamento_estrategico(6);';
			    break;
			case 10570:
			#ORGANOGRAMA#
			   	echo 'organograma();';
			    break;
			case 10588:
			#POLÍTICAS#
			   	echo 'manual();';
			    break;
			case 10409:
			#POLÍTICAS#
			   	echo 'codigo_etica();';
			    break;
		}
		?>
	});
</script>
<br/>
<table width="98%" border="0" cellspacing="0" cellpadding="0">
	<tr> 
		<td width="10"></td>
		<td valign="top" width="350"> 
			<table  border="0" align="left" class="menu_intranet_gerencia" >
				<tr>
					<td style="width: 350px; padding-top: 40px; padding-left: 10px; background: url(<?php echo base_url().'img/intranet/work_intranet.png'?>) no-repeat;"> 
						<h1 style="font-size: 20pt; font-family: calibri, arial; padding-left: 50px; padding-bottom: 5px; color:#0046AD;">
							<?php echo $cd_gerencia; ?>
						</h1>
							<?php
								function getMenuIntranetItem($cd_intranet = 0)
								{
									$CI = &get_instance();
									
									$sql = "
											SELECT * 
											  FROM projetos.intranet 
											 WHERE dt_exclusao IS NULL
											   AND cd_intranet_pai = ".intval($cd_intranet)." 
											 ORDER BY COALESCE(nr_ordem,0) DESC, titulo
										   ";
									$query = $CI->db->query($sql);
									$collection = $query->result_array();
									$output = '<ul id="browser" class="filetree">';
									foreach( $collection as $item )
									{
										$sql_icon = "
														SELECT COUNT(*) AS quantos 
														  FROM projetos.intranet 
														 WHERE cd_intranet_pai = ".intval($item['cd_intranet'])." 
														   AND dt_exclusao IS NULL
												    ";
										$query = $CI->db->query($sql_icon);
										$row = $query->row_array();
										
										if(intval($row['quantos']) > 0)
										{
											$sql_icon = "
														SELECT COUNT(*) AS quantos 
														  FROM projetos.intranet_link 
														 WHERE cd_intranet = ".intval($item['cd_intranet'])." 
														   AND dt_exclusao IS NULL;
														";
											$query2 = $CI->db->query($sql_icon);
											$row2 = $query2->row_array();											
											
											if((intval($row2['quantos']) == 0) and (trim($item['conteudo']) == "") and (trim($item['arquivo']) == ""))
											{
												$output .= '<li id="'.$item['cd_intranet'].'"><span class="folder"><a href="javascript: void(0);">'.$item['titulo'].'</a></span><ul>'.getMenuIntranetItem($item['cd_intranet'] )."</ul></li>";
											}
											else
											{
												$output .= '<li id="'.$item['cd_intranet'].'"><span class="folder-item">'.anchor(site_url('ecrm/intranet/pagina/'.$item['cd_gerencia'].'/'.$item['cd_intranet']), $item['titulo']).'</span><ul>'.getMenuIntranetItem($item['cd_intranet'] )."</ul></li>";
											}
										}
										else
										{
											$sql_icon = "
														SELECT link, texto_link
														  FROM projetos.intranet_link 
														 WHERE cd_intranet = ".intval($item['cd_intranet'])." 
														   AND dt_exclusao IS NULL
														";
											$query2 = $CI->db->query($sql_icon);
											$row2 = $query2->result_array();											
											
											//
											
											if((count($row2) == 1) and (trim($item['conteudo']) == "") and (trim($item['arquivo']) == ""))
											{
												$output .= '<li id="'.$item['cd_intranet'].'"><span class="file-atalho">'.anchor_file($row2[0]['link'],$row2[0]['texto_link'], array('target' => '_black')).'</span></li>';
											}
											else
											{
												$output .= '<li id="'.$item['cd_intranet'].'"><span class="file">'.anchor(site_url('ecrm/intranet/pagina/'.$item['cd_gerencia'].'/'.$item['cd_intranet']), $item['titulo']).'</span></li>';
											}
										}										
									}
									$output.= "</ul>";
									
									return $output;
								}
								
								function getMenuIntranet($cd_gerencia = "")
								{
									$CI = &get_instance();
									
									$sql = "
											SELECT cd_intranet
											  FROM projetos.intranet 
											 WHERE dt_exclusao IS NULL
											   AND cd_gerencia     = '".$cd_gerencia."'
											   AND cd_intranet_pai = 0
											 LIMIT 1
										   ";
									$query = $CI->db->query($sql);
									$row = $query->row_array();

									return intval($row['cd_intranet']);
								}
								
								echo getMenuIntranetItem(getMenuIntranet($cd_gerencia)) ;
							?>
							<script>
									$(document).ready(function(){
										$("#browser").treeview({
											animated: "fast",
											collapsed: true,
											persist: "cookie",
											cookieId: "treeview-black"
										});
									});
							</script>
						</h1>
					</td>
				</tr> 
			</table>
		</td> 		
		<td valign="top" align="left"> 
			<h2 style="margin: 0px; padding-top: 5px; padding-bottom: 5px; color: #0046AD; font-family: calibri, arial; font-size: 18pt;">
				<?php echo $row['titulo']; ?>
			</h2>
			<ul class="filetree">
			<?php
			foreach($collection as $item)
			{
				echo '<li id="'.$item['cd_intranet'].'"><span class="file">'.anchor(site_url('ecrm/intranet/pagina/'.$cd_gerencia.'/'.$item['cd_intranet']), $item['titulo']).'</span></li>';
			}
			?>
			</ul>
			<div style="padding-right: 15px; padding-bottom: 3px; font-family: Calibri, Arial;">
				<?php
					if(trim($row['arquivo_nome']) != '')
					{
						echo br();
						if($row['tp_imagem'] == "IMG")
						{
							echo img(base_url().'/up/intranet/'.$row['arquivo_nome']);
						}
						else
						{
							echo anchor( base_url().'/up/intranet/'.$row['arquivo_nome'], 'Ver documento', array('target' => '_blank') );
						}
						echo br();
					}
					echo br();
					switch (intval($row['cd_intranet'])) 
					{
					    case 10402:
					    #MANUAL DE GESTÃO#
							 ?>
							 <h2 style="font-size:120%">Gerência Responsável : GC</h2>
					        <h2 style="font-size:120%">Publicado no Site : Não</h2>
					        <a href="https://www.e-prev.com.br/cieprev/index.php/gestao/manual_gestao/get">Download - Manual de Gestão</a>
							<?php
					        break;
					    case 10528:
					    #MANUAL DE EMPRÉSTIMO#
							?>
							<h2 style="font-size:120%">Gerência Responsável : GFC</h2>
					        <h2 style="font-size:120%">Publicado no Site : Não</h2>

							<a href="https://www.e-prev.com.br/cieprev/index.php/gestao/manual_emprestimo/get">Download - Manual de Empréstimo</a>
							<?php
					        break;
					    case 10586:
					    #MANUAL DE INVESTIMENTOS#
							?>
							<h2 style="font-size:120%">Gerência Responsável : GIN</h2>
					        <h2 style="font-size:120%">Publicado no Site : Não</h2>

							<a href="https://www.e-prev.com.br/cieprev/index.php/gestao/manual_investimento/get">Download - Manual de Investimentos</a>
							<?php
					        break;
					    case 10557:
					    #RIG#
							 ?>
							 <h2 style="font-size:120%">Gerência Responsável : GC</h2>
					        <h2 style="font-size:120%">Publicado no Site : Sim</h2>
					        <a href="https://www.e-prev.com.br/cieprev/index.php/gestao/rig/get">Download - RIG</a>
							<?php
					        break;
					    case 10403:
					    #PROCESSOS#
					        echo '<div id="result_processos"></div>';
					        break;
					    case 10409:
					    #CÓDIGO DE ÉTICA#
					        ?>
					        <h2 style="font-size:120%">Gerência Responsável : GC</h2>
					        <h2 style="font-size:120%">Publicado no Site : Sim</h2>
					        <div id="result_codigo_etica"></div>
							<?php
					        break;
					    case 10412:
					    #REGIMENTO INTERNO#
					        echo '<div id="result_regimento_interno"></div>';
					        break;
					    case 10413:
						#REGULAMENTOS#
						   	echo '<div id="result_regulamento"></div>';
						    break;
						case 10414:
						#ESTATUTO#
							echo '<div id="result_estatuto"></div>';
						 	/*
							<iframe src="https://www.e-prev.com.br/cieprev/index.php/gestao/estatuto/get" width="100%" frameborder="0" name="rel" id="iFrameRelatorioId" style="margin-top:2px;height:800px;"></iframe>
							*/
						    break;
						case 10416:
						#POLÍTICAS#
						   	echo '<div id="result_politica"></div>';
						    break;
						case 10417:
					    #CERTIFICADO ISO#
					        ?>

					        <a href="https://www.e-prev.com.br/cieprev/up/certificado_iso/certificado_iso.pdf">Download - Certificado ISO</a>
							<?php

						case 10422:
						#SÚMULA DIRETORIA#
						   	echo '<div id="result_sumula"></div>';
						    break;
						case 10425:
						#SÚMULA CONSELHO DELIBERATIVO#
						   	echo '<div id="result_sumula_conselho"></div>';
						    break;
						case 10426:
						#SÚMULA CONSELHO FISCAL#
						   	echo '<div id="result_sumula_conselho_fiscal"></div>';
						    break;
						case 10434:
						#PLANEJAMENTO ESTRATÉGIVO 2017 - 2018#
						   	echo '<div id="result_planejamento_estrategico"></div>';
						    break;
						case 10475:
						#PLANEJAMENTO ESTRATÉGIVO 2019 - 2023#
						   	echo '<div id="result_planejamento_estrategico"></div>';
						    break;
						case 10465:
						#CONVÊNIOS DE ADESÃO#
							echo '<div id="result_convenio_adesao"></div>';
							break;
						case 10525:
							echo '<div id="result_planejamento_estrategico"></div>';
						#PLANEJAMENTO ESTRATÉGIVO 2019 - 2023 (1ª revisão)#
						/*
						   	echo '<div id="result_planejamento_estrategico">
						   		<center>
						   			<img src="https://www.e-prev.com.br/cieprev/up/planejamento_estrategico/p1e8cba8kmh59uu74mhopi1pq44.png" style="height:562px;">
						   			<br/>
						   			<h1 style="font-size: 14pt;">Os programas e projetos estão no Sistema S.A. (Interact).</h1>
						   		</center>
						   	</div>';
						   	*/
						    break;  
						case 10537:
							echo '<div id="result_planejamento_estrategico"></div>';

						    break; 

						case 10553:
							echo '<div id="result_planejamento_estrategico"></div>';
							break; 

						case 10575:
							echo '<div id="result_planejamento_estrategico"></div>';
							break; 

						case 10560:
					    #Regulamento Viagem dos Sonhos
							?>
							<iframe src="https://www.e-prev.com.br/cieprev/up/regulamento_viagem_sonhos/regulamento_viagem_sonhos_20220225.pdf" width="100%" frameborder="0" name="rel" id="iFrameRelatorioId" style="margin-top:2px;height:800px;"></iframe>
							<?php
					        break;
					    case 10570:
					    #ORGANOGRAMA#
							 ?>
							<h2 style="font-size:120%">Gerência Responsável : GC</h2>
					        <h2 style="font-size:120%">Publicado no Site : Sim</h2>
					        <a href="https://www.e-prev.com.br/cieprev/index.php/gestao/organograma/get">Download - Organograma</a>
							<?php

					        break;
					    case 10583:
					    #RIG#
							 ?>
							 <h2 style="font-size:120%">Gerência Responsável : GC</h2>
					        <h2 style="font-size:120%">Publicado no Site : Não</h2>
					        <a href="https://www.e-prev.com.br/cieprev/index.php/gestao/plano_continuidade_negocios/get">Download - Plano de Continuidade de Negócios</a>
							<?php
					        break;
					    case 10588:
						#POLÍTICAS#
						   	echo '<div id="result_manual"></div>';
						    break;

					    default:
					    	if(trim($row['conteudo']) != '')
							{
   								echo $row['conteudo']; 
   							}
					}

					echo br();

					if(count($doc_collection) > 0)
					{
						echo $grid->render();
					}
				?>
			</div>
			<br/>
			
		</td> 

	</tr>
</table>

<?php
$this->load->view('footer');
?>