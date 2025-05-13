<?php if ( ! defined('PCHART_FONTPATH')) exit('No direct script access allowed');

// Standard inclusions      
include("pChart/pChart/pData.class");   
include("pChart/pChart/pChart.class");   
	

/*
	linechart($tickLabels, $values, $legenda, $width=300, $height=200, $referencia=Array())
	echo "<br><PRE><b>REFERENCIA</b> <br>".print_r($referencia,true)."</PRE><BR>";
	echo "<br><PRE><b>TICKS</b> <br>".print_r($tickLabels,true)."</PRE><BR>";
	echo "<br><PRE><b>VALORES</b> <br>".print_r($values,true)."</PRE><BR>";
	echo "<br><PRE><b>LEGENDA</b> <br>".print_r($legenda,true)."</PRE><BR>";
	#exit;
*/

/*
	piechart($tickLabels, $values, $legenda, $width=300, $height=200)
	echo "<br><PRE><b>TICKS</b> <br>".print_r($tickLabels,true)."</PRE><BR>";
	echo "<br><PRE><b>VALORES</b> <br>".print_r($values,true)."</PRE><BR>";
	echo "<br><PRE><b>LEGENDA</b> <br>".print_r($legenda,true)."</PRE><BR>";
	exit;
*/

/*
	group_barchart($tickLabels, $values, $tipo, $legenda, $width=300, $height=200, $referencia=Array())
	echo "<br><PRE><b>TICKS</b> <br>".print_r($tickLabels,true)."</PRE><BR>";
	echo "<br><PRE><b>VALORES</b> <br>".print_r($values,true)."</PRE><BR>";
	echo "<br><PRE><b>TIPO</b> <br>".print_r($tipo,true)."</PRE><BR>";
	echo "<br><PRE><b>LEGENDA</b> <br>".print_r($legenda,true)."</PRE><BR>";
	exit;
*/

/*
	accumulate_barchart($tickLabels, $values, $tipo, $legenda, $width=300, $height=200, $referencia=Array())
	echo "<br><PRE><b>TICKS</b> <br>".print_r($tickLabels,true)."</PRE><BR>";
	echo "<br><PRE><b>VALORES</b> <br>".print_r($values,true)."</PRE><BR>";
	echo "<br><PRE><b>TIPO</b> <br>".print_r($tipo,true)."</PRE><BR>";
	echo "<br><PRE><b>LEGENDA</b> <br>".print_r($legenda,true)."</PRE><BR>";
	exit;
*/

function linechart($tickLabels, $values, $legenda, $width=300, $height=200, $referencia=Array())
{
	$dir_fonte = PCHART_FONTPATH;
	$dir_img = $_SERVER['DOCUMENT_ROOT']."/cieprev/charts/";
	
	$DataSet = new pData;
	
	#### VALORES ####
	$nr_conta = 0;
	$nr_fim = count($values);
	while($nr_conta < $nr_fim)
	{
		$DataSet->AddPoint($values[$nr_conta],"Serie".$nr_conta);
		$nr_conta++;
	}
	$DataSet->AddAllSeries();
	
	$DataSet->AddPoint($tickLabels,"tickLabels");
	$DataSet->SetAbsciseLabelSerie("tickLabels");
	
	#### LEGENDA ####
	$nr_legenda = 0;
	$nr_fim = count($legenda);
	while($nr_legenda < $nr_fim)
	{
		$DataSet->SetSerieName($legenda[$nr_legenda],"Serie".$nr_legenda);
		$nr_legenda++;
	}	
	
	$fl_inteiro = true;
	foreach($values as $ar_val)
	{
		foreach($ar_val as $item)
		{
			$x = substr(strrchr($item, "."), 1);
			if($x > 0)
			{
				$fl_inteiro = false;
			}
		}
	}
	
	if($fl_inteiro)
	{
		$formatY = "integer";
	}
	else
	{
		$formatY = "float";
	}
	
	
	
	$DataSet->SetYAxisFormat($formatY);

	#### POSICIONA LEGENDAS ####
	$nr_tick = tickTam($tickLabels);
	$nr_height_graph = ($height - $nr_tick);
	$nr_pos_legenda = $height;
	$height = $height + 50;//($nr_legenda * 22);
	
	// Initialise the graph   
	$graph = new pChart($width,$height);
	$graph->setFontProperties($dir_fonte."tahoma.ttf",8);   
	$graph->setGraphArea(70,30,($width - 30), $nr_height_graph);   
	$graph->drawFilledRoundedRectangle(7,7,($width - 7),($height - 7),5,240,240,240);   
	$graph->drawRoundedRectangle(5,5,($width - 5),($height - 5),5,240,240,240);   
	$graph->drawGraphArea(254,254,254,TRUE);
	$graph->removeZeroInicio();
	$graph->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,0,0,0,TRUE,90,2,TRUE);   
	#$graph->drawGrid(4,TRUE,240,240,240,50);
	$graph->drawGrid(4,TRUE,210,210,210,90);

	#### DEFINE COR PARAS AS REFERENCIAS ####
	if(is_array($referencia))
	{
		if(array_key_exists("M", $referencia))
		{
			if(is_array($referencia))
			{
				$graph->setMetas($referencia["M"]);
				$graph->setMeta('-1');
			}
			else
			{
				$graph->setMeta($referencia["M"]);
			}
		}
		
		if(array_key_exists("T", $referencia))
		{
			$graph->setTendencia($referencia["T"]);
		}
		
		if(array_key_exists("R", $referencia))
		{
			$graph->setReferencia($referencia["R"]);
		}		
	}
	
	// Espessura da linha 
	$graph->setLineStyle(1.1); 

	// Draw the 0 line   
	$graph->setFontProperties($dir_fonte."tahoma.ttf",8);   
	$graph->drawTreshold(0,150,150,150,TRUE,TRUE,4);   

	// Draw the line graph
	$graph->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());   
	#$graph->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);   
	$graph->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),4,2);   
	
	/*
	// Write values
	$graph->setFontProperties($dir_fonte."verdana.ttf",8);     
	$nr_legenda = 0;
	$nr_fim = count($legenda);
	while($nr_legenda < $nr_fim)
	{
		$graph->writeValues($DataSet->GetData(),$DataSet->GetDataDescription(),"Serie".$nr_legenda,"FLOAT");  
		$nr_legenda++;
	}
	*/
	
	// Finish the graph   
	$graph->setFontProperties($dir_fonte."tahoma.ttf",8);   
	$graph->drawLegend(40,$nr_pos_legenda,$DataSet->GetDataDescription(),254,254,254); 

	
	$graph_file_name = random_string().'.png';
	$graph->Render($dir_img.$graph_file_name);
	return "charts/".$graph_file_name;
}

function piechart($tickLabels, $values, $legenda, $width=300, $height=200)
{
	$dir_fonte = PCHART_FONTPATH;
	$dir_img = $_SERVER['DOCUMENT_ROOT']."/cieprev/charts/";
	
	$DataSet = new pData;
	
	$DataSet->AddPoint($values,"Valor");
	$DataSet->AddPoint($legenda,"Legenda");
	$DataSet->AddAllSeries();
	$DataSet->SetAbsciseLabelSerie("Legenda");

	// Initialise the graph
	$graph = new pChart($width,$height);
	$graph->setFontProperties($dir_fonte."tahoma.ttf",8);
	$graph->drawFilledRoundedRectangle(7,7,($width - 7),($height - 7),5,240,240,240);  
	$graph->drawRoundedRectangle(5,5,($width - 5),($height - 5),5,240,240,240);  

	// Draw the pie chart
	$graph->AntialiasQuality = 0;
	$graph->setShadowProperties(2,2,200,200,200);
	$graph->drawFlatPieGraphWithShadow($DataSet->GetData(),$DataSet->GetDataDescription(),($width / 2) - ((10 * $width)/100),($height / 2),((20 * $width)/100),PIE_PERCENTAGE,8);
	$graph->clearShadow();

	$graph->setFontProperties($dir_fonte."tahoma.ttf",8); 
	$graph->drawPieLegend(($width - 180),30,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);

	$graph_file_name = random_string().'.png';
	$graph->Render($dir_img.$graph_file_name);
	return "charts/".$graph_file_name;
}

function group_barchart($tickLabels, $values, $tipo, $legenda, $width=300, $height=200, $referencia=Array())
{
	$dir_fonte = PCHART_FONTPATH;
	$dir_img = $_SERVER['DOCUMENT_ROOT']."/cieprev/charts/";
	
	$DataSet = new pData;
	
	
	#### VALORES BARRA ####
	$nr_barra = 0;
	$nr_conta = 0;
	$nr_fim = count($values);
	while($nr_conta < $nr_fim)
	{
		if($tipo[$nr_conta] != "linha")
		{
			$DataSet->AddPoint($values[$nr_conta],"Serie".$nr_barra);
			$nr_barra++;
		}
		$nr_conta++;
	}
	$nr_linha = $nr_barra;
	
	#### VALORES LINHA ####
	$nr_conta = 0;
	$nr_fim = count($values);
	
	while($nr_conta < $nr_fim)
	{
		if($tipo[$nr_conta] == "linha")
		{
			$DataSet->AddPoint($values[$nr_conta],"Serie".$nr_linha);
			$nr_linha++;
		}
		$nr_conta++;
	}
	
	$DataSet->AddAllSeries();
	
	$DataSet->AddPoint($tickLabels,"tickLabels");
	$DataSet->SetAbsciseLabelSerie("tickLabels");
	
	#### LEGENDA BARRA ####
	$nr_leg = legendaTam($legenda);
	$nr_legenda = 0;
	$nr_fim = count($legenda);
	while($nr_legenda < $nr_fim)
	{
		$DataSet->SetSerieName($legenda[$nr_legenda],"Serie".$nr_legenda);
		$nr_legenda++;
	}	
	
	$DataSet->SetYAxisFormat("float");

	#### POSICIONA LEGENDAS ####
	$nr_tick = tickTam($tickLabels);
	$nr_height_graph = ($height - $nr_tick);
	$nr_pos_legenda = $height + 50;
	$height = $height + 100;//($nr_legenda * 22);	
	
	// Initialise the graph   
	$graph = new pChart($width,$height);
	$graph->setFontProperties($dir_fonte."tahoma.ttf",8);   
	$graph->setGraphArea(100,30,($width - 30), $nr_height_graph);    
	$graph->drawFilledRoundedRectangle(7,7,($width - 7),($height - 7),5,240,240,240);   
	$graph->drawRoundedRectangle(5,5,($width - 5),($height - 5),5,240,240,240);   
	$graph->drawGraphArea(254,254,254,TRUE);
    $graph->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,0,0,0,TRUE,90,2,TRUE); 
	#$graph->drawGrid(4,TRUE,240,240,240,50);
	$graph->drawGrid(4,TRUE,210,210,210,90);
	

	#### DEFINE COR PARAS AS REFERENCIAS ####
	if(is_array($referencia))
	{
		if(array_key_exists("M", $referencia))
		{
			if(is_array($referencia))
			{
				$graph->setMetas($referencia["M"]);
				$graph->setMeta('-1');
			}
			else
			{
				$graph->setMeta($referencia["M"]);
			}
		}
		
		if(array_key_exists("T", $referencia))
		{
			$graph->setTendencia($referencia["T"]);
		}
		
		if(array_key_exists("R", $referencia))
		{
			$graph->setReferencia($referencia["R"]);
		}		
	}	
	

	// Draw the 0 line   
	$graph->setFontProperties($dir_fonte."tahoma.ttf",8);   
	$graph->drawTreshold(0,150,150,150,TRUE,TRUE,4);   

	// Draw the BAR
	$nr_conta = $nr_barra;
	$nr_fim = $nr_linha;
	while($nr_conta < $nr_fim)
	{
		$DataSet->RemoveSerie("Serie".$nr_conta);
		$nr_conta++;
	}
	$graph->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE,50);
	

	// Draw the LINE
	$nr_conta = 0;
	$nr_fim = count($values);
	while($nr_conta < $nr_fim)
	{
		if($nr_conta < $nr_barra)
		{
			$DataSet->RemoveSerie("Serie".$nr_conta);
		}
		else
		{
			$DataSet->AddSerie("Serie".$nr_conta);
		}
		
		$nr_conta++;
	}

	// Espessura da linha
	$graph->setLineStyle(1.1); 	
	
	$graph->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
	$graph->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2);   	

	// Finish the graph   
	$graph->setFontProperties($dir_fonte."tahoma.ttf",8);   
	$graph->drawLegend(50,$nr_pos_legenda,$DataSet->GetDataDescription(),254,254,254); 

	
	$graph_file_name = random_string().'.png';
	$graph->Render($dir_img.$graph_file_name);
	return "charts/".$graph_file_name;
}

function accumulate_barchart($tickLabels, $values, $tipo, $legenda, $width=300, $height=200, $referencia=Array())
{
	$dir_fonte = PCHART_FONTPATH;
	$dir_img = $_SERVER['DOCUMENT_ROOT']."/cieprev/charts/";

	$DataSet = new pData;
	
	#### VALORES BARRA ####
	$nr_barra = 0;
	$nr_conta = 0;
	$nr_fim = count($values);
	while($nr_conta < $nr_fim)
	{
		if($tipo[$nr_conta] != "linha")
		{
			$DataSet->AddPoint($values[$nr_conta],"Serie".$nr_barra);
			$nr_barra++;
		}
		$nr_conta++;
	}
	$nr_linha = $nr_barra;
	
	$DataSet->AddAllSeries();
	
	$DataSet->AddPoint($tickLabels,"tickLabels");
	$DataSet->SetAbsciseLabelSerie("tickLabels");
	
	#### LEGENDA ####
	$nr_leg = legendaTam($legenda);
	$nr_legenda = 0;
	$nr_fim = count($legenda);
	while($nr_legenda < $nr_fim)
	{
		$DataSet->SetSerieName($legenda[$nr_legenda],"Serie".$nr_legenda);
		$nr_legenda++;
	}	
	
	$DataSet->SetYAxisFormat("float");

	#### POSICIONA LEGENDAS ####
	$nr_tick = tickTam($tickLabels);
	$nr_height_graph = ($height - $nr_tick);
	$nr_pos_legenda = $height;
	$height = $height + 50;//($nr_legenda * 22);	
	
	// Initialise the graph   
	$graph = new pChart($width,$height);
	$graph->setFontProperties($dir_fonte."tahoma.ttf",8);   
	$graph->setGraphArea(70,30,($width - 30), $nr_height_graph); 
	$graph->drawFilledRoundedRectangle(7,7,($width - 7),($height - 7),5,240,240,240);   
	$graph->drawRoundedRectangle(5,5,($width - 5),($height - 5),5,240,240,240);   
	$graph->drawGraphArea(254,254,254,TRUE);
	$graph->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_ADDALLSTART0,0,0,0,TRUE,90,2,TRUE); 
	#$graph->drawGrid(4,TRUE,240,240,240,50);
	$graph->drawGrid(4,TRUE,210,210,210,90);
	
	#### DEFINE COR PARAS AS REFERENCIAS ####
	if(is_array($referencia))
	{
		if(array_key_exists("M", $referencia))
		{
			if(is_array($referencia))
			{
				$graph->setMetas($referencia["M"]);
				$graph->setMeta('-1');
			}
			else
			{
				$graph->setMeta($referencia["M"]);
			}
		}
		
		if(array_key_exists("T", $referencia))
		{
			$graph->setTendencia($referencia["T"]);
		}
		
		if(array_key_exists("R", $referencia))
		{
			$graph->setReferencia($referencia["R"]);
		}		
	}	

	// Draw the 0 line   
	$graph->setFontProperties($dir_fonte."tahoma.ttf",8);   
	$graph->drawTreshold(0,150,150,150,TRUE,TRUE,4);   

	// Draw the bar graph
	$graph->drawStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),50); 

	#### VALORES LINHA ####
	$nr_conta = 0;
	$nr_fim = count($values);
	$ar_cor = $graph->Palette;
	while($nr_conta < $nr_fim)
	{
		if($tipo[$nr_conta] == "linha")
		{
			#$DataSet->AddPoint($values[$nr_conta],"Serie".$nr_linha);
			#print_r($values[$nr_conta][0])."<BR>";
			#drawTreshold($Value,$R,$G,$B,$ShowLabel=FALSE,$ShowOnRight=FALSE,$TickWidth=4,$FreeText=NULL)
			#$MyData->getSeriePalette("Probe 1")
			#$Palette
			#print_r( $ar_cor[$nr_conta]); #Array ( [R] => 102 [G] => 205 [B] => 170 )  #exit;

			if(($graph->meta > -1))
			{
				$R = $graph->refer_cor["M"]["R"];
				$G = $graph->refer_cor["M"]["G"];
				$B = $graph->refer_cor["M"]["B"];					
			}
			else
			{
				$R = $ar_cor[$nr_conta]["R"];
				$G = $ar_cor[$nr_conta]["G"];
				$B = $ar_cor[$nr_conta]["B"];			
			}
			
			
			$graph->drawTreshold($values[$nr_conta][0],$R,$G,$B,TRUE,TRUE,0,NULL);
			
			$nr_linha++;
		}
		$nr_conta++;
	}	
	
	
	// Finish the graph   
	$graph->setFontProperties($dir_fonte."tahoma.ttf",8);   
	$graph->drawLegend(40,$nr_pos_legenda,$DataSet->GetDataDescription(),254,254,254); 
	
	
	
	$graph_file_name = random_string().'.png';
	$graph->Render($dir_img.$graph_file_name);
	return "charts/".$graph_file_name;
}

function legendaTam($ar_legenda)
{
	$ar_tmp = Array();
	$ar_tmp = array_map("tamStringItem", $ar_legenda);
	$nr_max = max($ar_tmp);
	$nr_leg = 0;
	if ($nr_max > 18)
	{
		$nr_leg = ($nr_max - 18) * 7;
	}
	return $nr_leg;
}

function tickTam($ar_tick)
{
	$ar_tmp = Array();
	$ar_tmp = array_map("tamStringItem", $ar_tick);
	$nr_max = max($ar_tmp);
	$nr_tick = $nr_max * (14/(ceil($nr_max/10)));
	
	return $nr_tick;
}

function tamStringItem($valor) 
{
   return strlen($valor);
}


?>