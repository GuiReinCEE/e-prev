<?php
	set_title('Calendário');
	$this->load->view('header');
	
	$cor_F = "#CC0000";
	$cor_C = "#FF6600";
	$cor_T = "#999900";

	$cor_DE = "#FE9A2E";
	$cor_CF = "#0B610B";
	$cor_CD = "#0174DF";

	$cor_EN = "#831d1c";
?>
<style>
	.calendario
	{
		margin: 0px;
		width: 730px;
		background: #FFFFFF;
		border: #A0A0A0 1px solid;
	}

	.calendario_ano
	{
		text-align:center;
		font-family: FrancoisOneRegular, Calibri, Arial;
		font-size: 22pt;
		font-weight: bold;
	}

	.calendario_ano_ant
	{
		height: 25px;
		background: no-repeat url("<?php echo base_url(); ?>img/calendario/back.png");
		background-position: bottom left;
	}
	
	.calendario_ano_ant a
	{
		padding-left: 20px;
		text-align:left;
		text-decoration: none;
		font-family: FrancoisOneRegular, Calibri, Arial;
		font-size: 13pt;
		font-weight: bold;
	}

	.calendario_ano_pro
	{
		height: 25px;
		background: no-repeat url("<?php echo base_url(); ?>img/calendario/next.png");
		background-position: bottom right;
	}
	
	.calendario_ano_pro a
	{
		padding-right: 20px;
		text-align: right;
		text-decoration: none;
		font-family: FrancoisOneRegular, Calibri, Arial;
		font-size: 13pt;
		font-weight: bold;
	}
	
	.calendario_mes
	{
		text-align:center;
		font-family: YanoneKaffeesatzRegular, Calibri, Arial;
		font-size: 12pt;
		font-weight: bold;
		background: no-repeat url("<?php echo base_url(); ?>img/calendario/mes.png");
		width: 163px;
		height: 18px;	
		color: #555555;			
	}	
	
	.calendario_dias
	{
		text-align:center;
		font-family: YanoneKaffeesatzRegular, Calibri, Arial;
		font-size: 12pt;
		font-weight: bold;
		color: #4E8CCF;
		
		width: 24px;
	}
	
	.calendario_sabado 
	{
		text-align:center;
		font-family: FrancoisOneRegular, Calibri, Arial;
		font-size: 12pt;
		color: #A0A0A0;
		width: 22px;
		height: 22px;
	}
	
	.calendario_domingo 
	{
		text-align:center;
		font-family: FrancoisOneRegular, Calibri, Arial;
		font-size: 12pt;
		color: #A0A0A0;
		width: 22px;
		height: 22px;		
	}	
	
	.calendario_semana 
	{
		text-align:center;
		font-family: FrancoisOneRegular,Calibri, Arial;
		font-size: 12pt;
		width: 22px;
		height: 22px;	
		color: #777777;		
	}	

	.calendario_noticia
	{
		width: 22px;
		height: 22px;		
	}
	
	.calendario_noticia_F
	{
		cursor: pointer;
		width: 22px;
		height: 22px;			
		text-align:center;
		text-decoration: none;
		font-family: FrancoisOneRegular, Calibri, Arial;
		font-size: 12pt;
		font-weight: bold;
		color: <? echo $cor_F;?>;
	}

	.calendario_noticia_C
	{
		cursor: pointer;
		width: 22px;
		height: 22px;			
		text-align:center;
		text-decoration: none;
		font-family: FrancoisOneRegular, Calibri, Arial;
		font-size: 12pt;
		font-weight: bold;
		color: <? echo $cor_C;?>;
	}
	
	.calendario_noticia_T
	{
		cursor: pointer;
		width: 22px;
		height: 22px;			
		text-align:center;
		text-decoration: none;
		font-family: FrancoisOneRegular, Calibri, Arial;
		font-size: 12pt;
		font-weight: bold;
		color: <? echo $cor_T;?>;
	}	

	.calendario_noticia_DE
	{
		cursor: pointer;
		width: 22px;
		height: 22px;			
		text-align:center;
		text-decoration: none;
		font-family: FrancoisOneRegular, Calibri, Arial;
		font-size: 12pt;
		font-weight: bold;
		color: <? echo $cor_DE;?>;
	}

	.calendario_noticia_CF
	{
		cursor: pointer;
		width: 22px;
		height: 22px;			
		text-align:center;
		text-decoration: none;
		font-family: FrancoisOneRegular, Calibri, Arial;
		font-size: 12pt;
		font-weight: bold;
		color: <? echo $cor_CF;?>;
	}
	
	.calendario_noticia_CD
	{
		cursor: pointer;
		width: 22px;
		height: 22px;			
		text-align:center;
		text-decoration: none;
		font-family: FrancoisOneRegular, Calibri, Arial;
		font-size: 12pt;
		font-weight: bold;
		color: <? echo $cor_CD;?>;
	}	
	
	.calendario_noticia_E
	{
		cursor: pointer;
		width: 22px;
		height: 22px;			
		text-align:center;
		text-decoration: none;
		font-family: FrancoisOneRegular, Calibri, Arial;
		font-size: 12pt;
		font-weight: bold;
		color: green;
	}	

	.calendario_noticia_EN
	{
		cursor: pointer;
		width: 22px;
		height: 22px;			
		text-align:center;
		text-decoration: none;
		font-family: FrancoisOneRegular, Calibri, Arial;
		font-size: 12pt;
		font-weight: bold;
		color: <? echo $cor_EN;?>;
	}

	.calendario_noticia_EN a
	{
		cursor: pointer;
		width: 22px;
		height: 22px;			
		text-align:center;
		text-decoration: none;
		font-family: FrancoisOneRegular, Calibri, Arial;
		font-size: 12pt;
		font-weight: bold;
		color: <? echo $cor_EN;?>;
		font-style: italic;
	}
	
	.calendario_noticia_P
	{
		cursor: pointer;
		width: 22px;
		height: 22px;			
		text-align:center;
		text-decoration: none;
		font-family: FrancoisOneRegular, Calibri, Arial;
		font-size: 12pt;
		font-weight: bold;
		color: blue;
	}	
	
	#boxCalendarioToolTip {
		display: none;
		position: absolute;
		top: 0px;
		left: 0px;
		width: 209px;
		height: 122px;
		background: no-repeat url("<?php echo base_url(); ?>img/calendario/calendario_tooltip.png");
	}
	
	#boxCalendarioToolTipText {
		margin-top: 35px;
		margin-left: 20px;
		margin-right: 20px;
		margin-bottom: 20px;
		font-size: 110%;
		font-weight: bold;
		text-align: center;
	}
</style>

<script>
	function exibeTip() 
	{
		var offset = $(this).offset();
		$("#boxCalendarioToolTipText").html($(this).attr("title"));
		
		$(this).attr("title", "");
		
		$("#boxCalendarioToolTip").css( { 
						position: 'absolute',
						zIndex: 5000,
						left: (offset.left - 98 - ($(this).width() < 10 ? Math.ceil($(this).width() / 2) : 0)), 
						top: (offset.top - 203)
				} );			
		$("#boxCalendarioToolTip").show();	
	}

	function ocultaTip()
	{
		$(this).attr("title", $("#boxCalendarioToolTipText").html());
		$("#boxCalendarioToolTipText").empty();
		$("#boxCalendarioToolTip").hide();
	}
	
	function ir_feriado()
	{
		location.href='<?php echo site_url("calendario/index/0/F"); ?>';
	}	
	
	function ir_pagamento()
	{
		location.href='<?php echo site_url("calendario/index/0/P"); ?>';
	}

	function ir_evento()
	{
		location.href='<?php echo site_url("calendario/index/0/E"); ?>';
	}

	function ir_reuniao()
	{
		location.href='<?php echo site_url("calendario/index/0/R"); ?>';
	}	
	
	$(document).ready(function() {
		$(".calendario_noticia_F").mouseover(exibeTip);			
		$(".calendario_noticia_F").mouseout(ocultaTip);			

		$(".calendario_noticia_C").mouseover(exibeTip);			
		$(".calendario_noticia_C").mouseout(ocultaTip);	
		
		$(".calendario_noticia_T").mouseover(exibeTip);			
		$(".calendario_noticia_T").mouseout(ocultaTip);			

		$(".calendario_noticia_E").mouseover(exibeTip);			
		$(".calendario_noticia_E").mouseout(ocultaTip);
		
		$(".calendario_noticia_P").mouseover(exibeTip);			
		$(".calendario_noticia_P").mouseout(ocultaTip);		

		$(".calendario_noticia_DE").mouseover(exibeTip);			
		$(".calendario_noticia_DE").mouseout(ocultaTip);	

		$(".calendario_noticia_CF").mouseover(exibeTip);			
		$(".calendario_noticia_CF").mouseout(ocultaTip);	

		$(".calendario_noticia_CD").mouseover(exibeTip);			
		$(".calendario_noticia_CD").mouseout(ocultaTip);

		$(".calendario_noticia_EN").mouseover(exibeTip);			
		$(".calendario_noticia_EN").mouseout(ocultaTip);			
	});
</script>

<?php

	$tabela_calendario = '
							<BR>
							<span id="debugCalendario"> </span>
							<div id="boxCalendarioToolTip">
								<div id="boxCalendarioToolTipText">
								</div>
							</div>							
							<center>
							<div class="calendario">
							<table border="0" cellspacing="7">
								<tr>
									<td align="left">
										<div class="calendario_ano_ant">'.anchor("calendario/index/".($ano - 1).'/'.$tipo, ($ano - 1)).'</div>
									</td>
									<td colspan="2" class="calendario_ano">
										'.$ano.'
									</td>				
									<td align="right">
										<div class="calendario_ano_pro">'.anchor("calendario/index/".($ano + 1).'/'.$tipo, ($ano + 1)).'</div>
									</td>
								</tr>
						 ';
	for($i=0; $i<12; $i+=4)
	{
		$tabela_calendario.='
		<tr> 
			<td valign="top">
			  '.cria(1,1+$i,$ano,$ar_data,$ar_tipo,$ar_desc,$ar_url).'
			</td>
			  
			<td valign="top">
			  '.cria(1,2+$i,$ano,$ar_data,$ar_tipo,$ar_desc,$ar_url).'
			</td>
			  
			<td valign="top">
			  '.cria(1,3+$i,$ano,$ar_data,$ar_tipo,$ar_desc,$ar_url).'
			</td>
			  
			<td valign="top">
			  '.cria(1,4+$i,$ano,$ar_data,$ar_tipo,$ar_desc,$ar_url).'
			</td>
		</tr>';
	}
	
	if(trim($tipo) == "F")
	{
		$tabela_calendario.= '
								</table>
								<table align="left" border="0">
									<tr>
										<td valign="middle" style="color: '.$cor_F.'; font-family: YanoneKaffeesatzRegular, Calibri, Arial; font-weight: normal; font-size: 12pt;">
											Feriado
										</td>
									</tr>
									<tr>
										<td valign="middle" style="color: '.$cor_C.'; font-family: YanoneKaffeesatzRegular, Calibri, Arial; font-weight: normal; font-size: 12pt;">
											Feriado - Fundação
										</td>
									</tr>								
									<tr>
										<td valign="middle" style="color: '.$cor_T.'; font-family: YanoneKaffeesatzRegular, Calibri, Arial; font-weight: normal; font-size: 12pt;">
											Feriado - Fundação - Meio Turno
										</td>
									</tr>							
								</table>
								</div>
								</center>
							 ';	
	}
	else if(trim($tipo) == "P")
	{
		$tabela_calendario.= '
								</table>
								<table align="left" border="0">
									<tr>
										<td valign="middle" style="color: blue; font-family: YanoneKaffeesatzRegular, Calibri, Arial; font-weight: normal; font-size: 12pt;">
											Pagamento
										</td>
									</tr>
								</table>
								</div>
								</center>
							 ';		
	}
	else if(trim($tipo) == "E")
	{
		$tabela_calendario.= '
								</table>
								<table align="left" border="0">
									<tr>
										<td valign="middle" style="color: green; font-family: YanoneKaffeesatzRegular, Calibri, Arial; font-weight: normal; font-size: 12pt;">
											Eventos
										</td>
									</tr>
									<tr>
										<td valign="middle" style="color: '.$cor_EN.'; font-family: YanoneKaffeesatzRegular, Calibri, Arial; font-weight: normal; font-size: 12pt;">
											Evento Endomarketing
										</td>
									</tr>
								</table>
								</div>
								</center>
							 ';		
	}	
	else if(trim($tipo) == "R")
	{
		$tabela_calendario.= '
								</table>
								<table align="left" border="0">
									<tr>
										<td valign="middle" style="color: '.$cor_DE.'; font-family: YanoneKaffeesatzRegular, Calibri, Arial; font-weight: normal; font-size: 12pt;">
											Reunião Diretoria Executiva
										</td>
									</tr>
									<tr>
										<td valign="middle" style="color: '.$cor_CF.'; font-family: YanoneKaffeesatzRegular, Calibri, Arial; font-weight: normal; font-size: 12pt;">
											Reunião Conselho Fiscal
										</td>
									</tr>
									<tr>
										<td valign="middle" style="color: '.$cor_CD.'; font-family: YanoneKaffeesatzRegular, Calibri, Arial; font-weight: normal; font-size: 12pt;">
											Reunião Conselho Deliberativo
										</td>
									</tr>	
								</table>
								</div>
								</center>
							 ';		
	}	
	else
	{
		$tabela_calendario.= '
								</table>
								</div>
								</center>
							 ';		
	}
	
	$abas[] = array('aba_listaC', 'Feriados e Folgas', (trim($tipo) == "F" ? TRUE : FALSE), 'ir_feriado();');
	$abas[] = array('aba_listaP', 'Pagamentos Colaboradores', (trim($tipo) == "P" ? TRUE : FALSE), 'ir_pagamento();');
	$abas[] = array('aba_listaE', 'Eventos', (trim($tipo) == "E" ? TRUE : FALSE), 'ir_evento();');
	$abas[] = array('aba_listaR', 'Reuniões dos Colegiados', (trim($tipo) == "R" ? TRUE : FALSE), 'ir_reuniao();');
	
	echo aba_start($abas);	
		echo $tabela_calendario;
	echo aba_end('');


   
   
	function cria($dia,$mes,$ano,$ar_data,$ar_tipo,$ar_desc,$ar_url)
	{
		$ar_mes = Array('1'=>'Janeiro','2'=>'Fevereiro','3'=>'Março','4'=>'Abril','5'=>'Maio','6'=>'Junho','7'=>'Julho','8'=>'Agosto','9'=>'Setembro','10'=>'Outubro','11'=>'Novembro','12'=>'Dezembro');
		$verf = date("d/n/Y", mktime (0,0,0,$mes,$dia,$ano));//Corrige qualquer data invalida
		$pieces=explode("/",$verf);
		$dia=$pieces[0];
		$mes=$pieces[1];
		$ano=$pieces[2];
		$last=date ("d", mktime (0,0,0,$mes+1,0,$ano));//Inteiro do ultimo dia do mês
		$diasem=date ("w", mktime (0,0,0,$mes,1,$ano));//Numero de dias na primeira semana do mês
		$numt=$last+$diasem;//Total de linhas na tabela
		$numt=($numt%7 != 0)?($numt+7-$numt%7):$numt;
		for($i=0;$i < $numt;$i++)
		{
			$data=$i-$diasem+1;
			if($i >= $diasem and $i < ($diasem+$last))
			{
				if($i%7 == 6)
				{
					#### SABADO ####
					if(checkFeriado($data,$mes,$ano,$ar_data))
					{					
						$aux[$i] = '
									<td  
										   class="calendario_noticia_'.$ar_tipo[str_pad($data, 2, "0", STR_PAD_LEFT)."/".str_pad($mes, 2, "0", STR_PAD_LEFT)."/".$ano].'"
										   title="'.$ar_desc[str_pad($data, 2, "0", STR_PAD_LEFT)."/".str_pad($mes, 2, "0", STR_PAD_LEFT)."/".$ano].'">
											'.$data.'
										
									</td>
								   ';
					}
					else
					{					
						$aux[$i]='<td class="calendario_sabado">'.$data.'</td>';
					}
				}
				elseif($i%7 == 0)
				{
					#### DOMINGO ####
					if(checkFeriado($data,$mes,$ano,$ar_data))
					{					
						$aux[$i] = '
									<td 
										   class="calendario_noticia_'.$ar_tipo[str_pad($data, 2, "0", STR_PAD_LEFT)."/".str_pad($mes, 2, "0", STR_PAD_LEFT)."/".$ano].'"
										   title="'.$ar_desc[str_pad($data, 2, "0", STR_PAD_LEFT)."/".str_pad($mes, 2, "0", STR_PAD_LEFT)."/".$ano].'">
											'.$data.'
										
									</td>
									';
					}
					else
					{						
						$aux[$i]='<td class="calendario_domingo">'.$data.'</td>'; //Marca os domingos
					}
				}
				else
				{
					if(checkFeriado($data,$mes,$ano,$ar_data))
					{					
						
						if(isset($ar_url[str_pad($data, 2, "0", STR_PAD_LEFT)."/".str_pad($mes, 2, "0", STR_PAD_LEFT)."/".$ano]) AND trim($ar_url[str_pad($data, 2, "0", STR_PAD_LEFT)."/".str_pad($mes, 2, "0", STR_PAD_LEFT)."/".$ano]) != '')
						{
							$aux[$i] = '
									<td  
										   class="calendario_noticia_'.$ar_tipo[str_pad($data, 2, "0", STR_PAD_LEFT)."/".str_pad($mes, 2, "0", STR_PAD_LEFT)."/".$ano].'"
										   title="'.$ar_desc[str_pad($data, 2, "0", STR_PAD_LEFT)."/".str_pad($mes, 2, "0", STR_PAD_LEFT)."/".$ano].'<br/> Clique no dia para ver mais.">
											<a href="'.$ar_url[str_pad($data, 2, "0", STR_PAD_LEFT)."/".str_pad($mes, 2, "0", STR_PAD_LEFT)."/".$ano].'" target="_blank">'.$data.'</a>
										
									</td>
									';
						}
						else
						{
							$aux[$i] = '
									<td  
										   class="calendario_noticia_'.$ar_tipo[str_pad($data, 2, "0", STR_PAD_LEFT)."/".str_pad($mes, 2, "0", STR_PAD_LEFT)."/".$ano].'"
										   title="'.$ar_desc[str_pad($data, 2, "0", STR_PAD_LEFT)."/".str_pad($mes, 2, "0", STR_PAD_LEFT)."/".$ano].'">
											'.$data.'
										
									</td>
									';

						}
					}
					else
					{
						$aux[$i] = '
							<td class="calendario_semana">
								'.$data.'
							</td>
								';
											
					}
					

				}
			}
			else
			{
				$aux[$i]='<td width="20">&nbsp;</td>';
			}
			if($i%7 == 0)
			{
				$aux[$i]='<tr align="center" >'.$aux[$i];
			}
			
			if($i%7 == 6)
			{
				$aux[$i].="</tr>";
			}
		}


		$calendario_mes = '
				<table border="0"  cellspacing="0" cellpadding="0">
				<tr>
					<td>
                    <table width="170" border="0" cellspacing="2" cellpadding="0">
                        <tr>
                           <td class="calendario_mes" colspan="7">
							'.$ar_mes[$mes].'
						   </td>
                        </tr>
                        <tr>
                           <td class="calendario_dias">D</td>
                           <td class="calendario_dias">S</td>
                           <td class="calendario_dias">T</td>
                           <td class="calendario_dias">Q</td>
                           <td class="calendario_dias">Q</td>
                           <td class="calendario_dias">S</td>
                           <td class="calendario_dias">S</td>
                       </tr>';
		$calendario_mes.= implode(" ",$aux);
		$calendario_mes.= '
					</table>
					</td>
				</tr>
				</table>';
                  
		return $calendario_mes;
	}

	
	function checkFeriado($dia,$mes,$ano,$ar_data)
	{
		if(intval($dia) < 10)
		{
			$dia = "0".intval($dia);
		}

		if(intval($mes) < 10)
		{
			$mes = "0".intval($mes);
		}		
		
		if (in_array(($dia."/".$mes."/".$ano), $ar_data)) 
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	

$this->load->view('footer_interna');
?>