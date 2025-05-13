<?php
$ar_browser = getNavegador();

if(strtoupper(trim($ar_browser['browser'])) == "INTERNET EXPLORER")
{
	#echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	echo "<!DOCTYPE HTML>";
}

$data['topo_titulo'] = 'Apresentação dos Indicadores de Desempenho';
$this->load->view(get_header_sem_topo(), $data);
?>
<style>
	body{ background:#FFFFFF; }
	
	.indicador_topo {
		height: 85px; 
		border-bottom: 1px solid #A9BFD3;
		background-image: url('<?php echo base_url(); ?>skins/skin002/img/header/fundo.png');
		background-position: bottom left;
		background-repeat: repeat-x;
	}
	.div-apr{
		border-style:solid;
		border-width:1px;
		margin:5px;
		padding:5px;
		background:white;
		
	}

	.barra_opcoes {
		text-align:center;
		width: 100%;
		height: 32px; 
		border-color: #FAFAFA #DDDDDD #D2D2D2;
		border-left: 1px solid #DDDDDD;
		border-right: 1px solid #DDDDDD;
		border-style: solid;
		border-width: 1px;
		background: url( '<?php echo base_url(); ?>skins/skin002/img/indicador_barra.gif' );
	}
	
	.barra_opcoes td {
		padding-left: 20px;
		padding-right: 20px;
	}	
	
	ul.barra_menu li {
		background-image: url('<?php echo base_url(); ?>skins/skin002/img/indicador_menu_divisor.png');
		background-position: center right;
		background-repeat: no-repeat;		
		float: left;
		margin: 0;
		padding: 2px 13px 0 13px;
	}
	
	@media print {
		#indicador-bar {
			display: none;
		}
	}
	
</style>
<script type="text/javascript">
	var CODIGO_INDICADOR_TABELA = <?php echo $codigo; ?>;

	var tamanho_grafico=0;

	function geraApresentacao(ob)
	{
		$('#ds_range_tick').val('');
		$('#ds_range_legenda').val('');
		$('#ds_range_valor').val('');

		selecionado='';

		if( CODIGO_INDICADOR_TABELA!='' )
		{
			url = "<?php echo site_url('indicador/grafico_config/carregar_grafico_config_ajax'); ?>";
			$.post( url, {cd: CODIGO_INDICADOR_TABELA }, function(data)
			{ 
				if(data.erro=='')
				{
					gerar_grafico(0,0);

					exibir_tabela();
					exibir_tabela_anos_anteriores();
				}
				else
				{
					alert( 'erro aqui' );
					alert( data.erro );
				}
			}, 'json' );
		}
		else
		{
		}
	}

	function gerar_grafico(nr_largura,nr_altura)
	{
		$("#img_gra_div").html("<?php echo loader_html(); ?>");
		gerar_grafico_analise();
		$.post('<?php echo site_url('indicador/grafico_config/grafico_ajax'); ?>',
			{
				cd         : CODIGO_INDICADOR_TABELA,
			    nr_largura : nr_largura,
			    nr_altura  : nr_altura
			},
			function(data)
			{
				$('#img_gra_div').html("<img src='" + data + "' id='img_gra' border='0'>");
			}
		);		
		
	}

	function gerar_grafico_analise()
	{
			$.post('<?php echo site_url('indicador/grafico_config/gerar_grafico_analise'); ?>',
			{
				cd_indicador_tabela : CODIGO_INDICADOR_TABELA
			},
			function(data)
			{
				$('#img_gra_div_analise').html(data);
			}
		);		
		
	}	
	
	function exibir_tabela()
	{
		$("#tabela_div").html("<?php echo loader_html(); ?>");
		$.post('<?php echo site_url('indicador/grafico_config/tabela'); ?>',
			{
				cd_indicador_tabela : CODIGO_INDICADOR_TABELA 
			},
			function(data)
			{
				// im anos anteriores
				im = '';
				$('#tabela_div').html( im+''+data );
				$('#tabela_div td').fontResizer({minFont:10,maxFont:200,increment:2});
			}
		);			
	}

	function exibir_tabela_anos_anteriores()
	{
		/*
		url="<?php echo site_url('igp/igp/exibir_historico_ajax'); ?>";
		$.post( url, { cd_indicador_tabela: CODIGO_INDICADOR_TABELA }, 
		function(data)
		{
			$('#historico_div').html( data );
		} );
		*/
	}

	// compatibilidade
	function escolher_celula_na_tabela(v)
	{
	}

	function dimi()
	{
		if($('#img_gra'))
		{
			var nr_x = $('#img_gra').width() - parseInt((10 * $('#img_gra').width()) / 100);
			var nr_y = $('#img_gra').height() - parseInt((10 * $('#img_gra').height()) / 100);
			
			if(nr_x > 250)
			{
				gerar_grafico(nr_x,nr_y);
			}
		}
	}

	function aumen()
	{
		if($('#img_gra'))
		{
			var nr_x = $('#img_gra').width() + parseInt((10 * $('#img_gra').width()) / 100);
			var nr_y = $('#img_gra').height() + parseInt((10 * $('#img_gra').height()) / 100);
			gerar_grafico(nr_x,nr_y);
		}
	}

	function tamnorm()
	{
		if($('#img_gra'))
		{
			gerar_grafico(0,0);
		}
	}

	function exibir_lista_indicador()
	{
		$('#lista_indicador_table').show();
	}

	function maximizeJanela() 
	{
		window.moveTo(0,0)
		window.resizeTo(screen.availWidth, screen.availHeight)
	}	
	
	function getPPT(cd_indicador_tabela)
	{
		var ob_win = window.open('<?php echo base_url().index_page(); ?>/indicador/grafico_config/geraPPT/' + cd_indicador_tabela, 
								 '_blank', 
								 'width=10,height=10,scrollbars=no,status=yes,resizable=no,screenx=0,screeny=0');	
		ob_win.moveTo(0,0);
	}
	
	function getEXCEL(cd_indicador_tabela)
	{
		var ob_win = window.open('<?php echo base_url().index_page(); ?>/indicador/grafico_config/geraEXCEL/' + cd_indicador_tabela, 
								 '_blank', 
								 'width=10,height=10,scrollbars=no,status=yes,resizable=no,screenx=0,screeny=0');	
		ob_win.moveTo(0,0);
	}	
	
    function irIndicador(cd_indicador_tabela)
	{
		location.href='<?= site_url("indicador/apresentacao/detalhe") ?>'+"/"+cd_indicador_tabela;
	}	
	
	function imprimir()
	{
		window.print();
		return false;	
	}
	
	$(document).ready(function() {
		maximizeJanela();
		geraApresentacao();
		$("#indicador-bar").show();
		$("#indicador-bar").jixedbar();
	});
</script>

<?php 
	#echo "<PRE>".print_r($row,true)."</PRE>";
?>

<table cellpadding='0' cellspacing='0' border='0' width='100%' class="indicador_topo">
	<tr>
		<td width="100px" valign="top" style="padding-top: 5px; padding-left: 5px;">
			<img src="<?php echo base_url(); ?>/img/logo-eprev-transp.png"/></td>
		<td valign="top">
			<div class='div-apr' style="width:95%; font-family: calibri, arial, verdana; font-size: 12pt;">
			<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><?php echo $row['ds_indicador_grupo']; ?> - <?php echo $row['ds_processo']; ?></td>
				</tr>
				<tr>
					<td ><?php echo $row['ds_periodo']; ?></td>
				</tr>					
				<tr>
					<td align="center" style='font-size: 130%; font-weight:bold;'><?php echo $row['ds_indicador_tabela']; ?></td>
				</tr>
				
				<tr>
					<td>
						<a id="obBtExibir" href='javascript:void(0);' onclick='$("#obBtExibir").hide();$("#obDescricaoIndicador").show();$("#obBtOcultar").show();' title="Exibir descrição"><img src="<?php echo base_url(); ?>skins/skin002/img/indicador_exibir.gif" border="0"></a>
						<a id="obBtOcultar" href='javascript:void(0);' onclick='$("#obDescricaoIndicador").hide();$("#obBtOcultar").hide();$("#obBtExibir").show();' style="display:none;" title="Ocultar descrição"><img src="<?php echo base_url(); ?>skins/skin002/img/indicador_ocultar.gif" border="0"></a>
						<table id="obDescricaoIndicador" style="display:none; font-size: 80%;" border="0">
							<?php
								if(trim($row['ds_missao']) != "")
								{
									echo "
											<tr>
												<td>Missão e Objetivo: ".$row['ds_missao']."</td>
											</tr>
									";
								}
							?>
							<tr>
								<td>Controle: <?php echo $row['ds_indicador_controle'];?></td>
							</tr>							
							<tr>
								<td>Fórmula: <?php echo nl2br($row['ds_formula']);?></td>
							</tr>
							<tr>
								<td>Unidade de Medida: <?php echo $row['ds_indicador_unidade_medida'];?></td>
							</tr>
						</table>
					</td>
				</tr>					
			</table>
			</div>
		</td>
	</tr>
</table>

<?php 
/*
if( sizeof($apresentacao)>1 )
{
	for( $i=0;$i<sizeof($apresentacao);$i++ )
	{
		if( $apresentacao[$i]['cd_indicador_tabela']==$codigo )
		{
			break;
		}
	}

	if( $i==0 ){ $anterior=0; }else{ $anterior =  $apresentacao[$i-1]['cd_indicador_tabela']; }
	if(	sizeof($apresentacao)==($i+1) ){ $proximo=0; }else{ $proximo = $apresentacao[$i+1]['cd_indicador_tabela']; }

	echo "<CENTER>";
	if( $anterior>0 )
	{
		echo anchor( "indicador/apresentacao/detalhe/$anterior/$cd_apresentacao", "<< ANTERIOR" );
	}
	else
	{
		echo "<< ANTERIOR";
	}
	echo " | ";
	if($proximo>0)
	{
		echo anchor( "indicador/apresentacao/detalhe/$proximo/$cd_apresentacao", "PRÓXIMO >>" );
	}
	else
	{
		echo "PROXIMO >>";
	}
	echo "</CENTER>";
}
*/
?>
<BR>
<center>


<table border="0" align="center">
	<tr>
		<td align="right">
			<div id='img_gra_div_analise'></div>
	</tr>
	<tr>
		<td align="center">
			<div id='img_gra_div'></div>
		</td>
	</tr>
</table>
<BR>
<div id="tabela_div"></div>
<BR>
<div id='historico_div'></div>
<BR>
<div id='output-div'></div><br>


<?php
echo form_open(); ?>
<?php if(sizeof($apresentacao)>0): ?>

	<table id='lista_indicador_table' class='div-apr' style='display:none;'><tr><td>
	<ul>
	<?php
		foreach($apresentacao as $item)
		{
			$st="";if($item['cd_indicador_tabela']==$codigo){ $st="background-color:#efefef;"; }
			echo "<li style='width:150px;float:left;font-size:10;$st;padding:5px;'>".anchor("indicador/apresentacao/detalhe/".$item['cd_indicador_tabela']."/".$item['cd_indicador_apresentacao'],"<img src='".base_url()."/img/ico_grafico_apresentacao_indicador.jpg' border='0' />".br().$item['ds_indicador_grupo'].br().$item['ds_indicador'].br().$item['ds_periodo'])."</li>";
			echo "";
		}
	?>
	</ul>
	</td></tr></table>

<a href='javascript:void(0);' onclick='exibir_lista_indicador();'>Exibir lista de indicadores da apresentação</a>

<?php endif;?>

<?php echo form_close(); ?>
</center>

<div id="indicador-bar" style="display:none;">
	<ul>        
		<li title="Mais indicadores"><a href="#"><img src="<?php echo base_url(); ?>skins/skin002/img/indicador_menu_mais.png" alt="Indicadores" /></a>
			<ul id="obListaIndicador">
				<?php
					$cd_indicador_next = 0;
					$cd_indicador_prev = 0;

					foreach($ar_indicador as $key => $ar_item)
					{
						if($ar_item['cd_indicador_tabela'] == intval($codigo))
						{
							if(isset($ar_indicador[($key-1)]))
							{
								$cd_indicador_prev = $ar_indicador[($key-1)]['cd_indicador_tabela'];
							}

							if(isset($ar_indicador[($key+1)]))
							{
								$cd_indicador_next = $ar_indicador[($key+1)]['cd_indicador_tabela'];
							}
						}

						echo '<li><a href="javascript:void(0);" onclick="irIndicador('.$ar_item['cd_indicador_tabela'].');">'.$ar_item['ds_indicador'].'</a></li>';
					}
				?>				
			</ul>
		</li>
	</ul>
	<span class="jx-separator-left"></span>
	<ul>
		<!--<li title="Download PPT"><a href='javascript:void(0);' onclick="getPPT(<?php echo intval($codigo);?>);" ><img src="<?php echo base_url(); ?>skins/skin002/img/indicador_ppt.png" border="0" alt=""></a></li>-->
		<? if(intval($cd_indicador_prev) > 0): ?>
		<li title="Apresentação Anterior">
			<a href="<?= site_url('indicador/apresentacao/detalhe/'.$cd_indicador_prev) ?>">
				<<
			</a>
		</li>
		<? endif; ?>

		<? if(intval($cd_indicador_next) > 0): ?>
		<li title="Próxima Apresentação">
			<a href="<?= site_url('indicador/apresentacao/detalhe/'.$cd_indicador_next) ?>">
				>>
			</a>
		</li>
		<? endif; ?>

		<li title="Download EXCEL"><a href='javascript:void(0);' onclick="getEXCEL(<?php echo intval($codigo);?>);" ><img src="<?php echo base_url(); ?>skins/skin002/img/indicador_xls.png" border="0" alt=""></a></li>
		<li title="(-) Diminuir Gráfico"><a href='javascript:void(0);' onclick='dimi();'><img src="<?php echo base_url(); ?>skins/skin002/img/indicador_zoom_out.png" border="0" alt=""></a></li>
		<li title="(+) Aumentar Gráfico"><a href='javascript:void(0);' onclick='aumen();'><img src="<?php echo base_url(); ?>skins/skin002/img/indicador_zoom_in.png" border="0" alt=""></a></li>
		<li title="Gráfico Normal"><a href='javascript:void(0);' onclick='tamnorm();'><img src="<?php echo base_url(); ?>skins/skin002/img/indicador_zoom_fit.png" border="0" alt=""></a></li>
		<li title="Diminuir Fonte"><a class="decreaseClickItem" href="javascript:void(0);"><img src="<?php echo base_url(); ?>skins/skin002/img/indicador_font_menos.png" border="0" alt=""></a></li>
		<li title="Aumentar Fonte"><a class="increaseClickItem" href="javascript:void(0);"><img src="<?php echo base_url(); ?>skins/skin002/img/indicador_font_mais.png" border="0" alt=""></a></li>
		<li title="Fonte Normal"><a class="setFontSize" title="12" href="javascript:void(0);"><img src="<?php echo base_url(); ?>skins/skin002/img/indicador_font.png" border="0" alt=""></a></li>
		<li title="Atualizar Apresentação"><a href='javascript:void(0);' onclick='geraApresentacao();'><img src="<?php echo base_url(); ?>skins/skin002/img/indicador_refresh.png" border="0" alt=""></a></li>		
		<li title="Imprimir"><a href='javascript:void(0);' onclick="imprimir();" ><img src="<?php echo base_url(); ?>skins/skin002/img/indicador_imprimir.png" border="0" alt=""></a></li>
	</ul>
	<span class="jx-separator-left"></span>        
	<span class="jx-separator-right"></span>
</div>

<?
$this->load->view('footer.php');
