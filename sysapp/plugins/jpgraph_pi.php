<?php
function linechart( $tickLabels, $values, $legends, $width=350, $height=250 )
{
    require_once("jpgraph/jpgraph.php");
    require_once("jpgraph/jpgraph_line.php");
	
	/*
	echo "<PRE>TICK <br>".print_r($tickLabels,true)."</PRE>";
	echo "<PRE>LEGENDA <br>".print_r($legends,true)."</PRE>";
	echo "<PRE>VALOR <br>".print_r($values,true)."</PRE>";
	exit;	
	*/

    $graph = new Graph($width,$height,"auto");
	$graph->SetShadow();
    $graph->SetScale("textlin");
    $graph->SetMargin(60,30,20,150);
	$graph->SetMarginColor('white');

	$graph->xaxis->SetFont(FF_VERDANA,FS_NORMAL,8);
	$graph->yaxis->SetFont(FF_VERDANA,FS_NORMAL,8);	
	
	$graph->yaxis->scale->SetGrace(10); 

    $graph->xaxis->SetTickLabels($tickLabels);
	$graph->xaxis->SetLabelAngle(90);
	$graph->xaxis->SetPos('min');

	// Adjust the legend position
	$graph->legend->SetFont(FF_VERDANA, FS_NORMAL, 8);
	$graph->legend->SetLayout(LEGEND_HOR);
	$graph->legend->Pos(0.4, 0.98, "center", "bottom");

	$config[] = array( 'color'=>'red', 'mark-type'=>MARK_SQUARE, 'mark-color'=>'red', 'mark-fillcolor'=>'red', 'mark-size' => 5 );
	$config[] = array( 'color'=>'orange', 'mark-type'=>MARK_DIAMOND, 'mark-color'=>'orange', 'mark-fillcolor'=>'orange', 'mark-size' => 8 );
	$config[] = array( 'color'=>'blue', 'mark-type'=>MARK_UTRIANGLE, 'mark-color'=>'blue', 'mark-fillcolor'=>'blue', 'mark-size' => 5 );
	$config[] = array( 'color'=>'olivedrab', 'mark-type'=>MARK_FILLEDCIRCLE, 'mark-color'=>'olivedrab', 'mark-fillcolor'=>'olivedrab', 'mark-size' => 3 );
	$config[] = array( 'color'=>'green', 'mark-type'=>MARK_DTRIANGLE, 'mark-color'=>'green', 'mark-fillcolor'=>'green', 'mark-size' => 5 );
	$config[] = array( 'color'=>'gold', 'mark-type'=>MARK_STAR, 'mark-color'=>'gold', 'mark-fillcolor'=>'gold', 'mark-size' => 5 );
	$config[] = array( 'color'=>'deeppink', 'mark-type'=>MARK_CROSS, 'mark-color'=>'deeppink', 'mark-fillcolor'=>'deeppink', 'mark-size' => 5 );
	$config[] = array( 'color'=>'chocolate', 'mark-type'=>MARK_X, 'mark-color'=>'chocolate', 'mark-fillcolor'=>'chocolate', 'mark-size' => 5 );
	$config[] = array( 'color'=>'brown', 'mark-type'=>MARK_SQUARE, 'mark-color'=>'brown', 'mark-fillcolor'=>'brown', 'mark-size' => 5 );
	$config[] = array( 'color'=>'cadetblue', 'mark-type'=>MARK_UTRIANGLE, 'mark-color'=>'cadetblue', 'mark-fillcolor'=>'cadetblue', 'mark-size' => 5 );

	for( $i=0;$i<sizeof($values);$i++ )
	{
	    $lineplot=new LinePlot( $values[$i] );
		//$lineplot->SetBarCenter(); 
	    $lineplot->mark->SetType( $config[$i]['mark-type'] );
		$lineplot->value->SetFont(FF_ARIAL, FS_BOLD, 10);
		$lineplot->value->SetFormat('(%d)');
		$lineplot->SetLegend( $legends[$i] );
		$lineplot->SetWeight( 2 );
	    $graph->Add( $lineplot );

	    $lineplot->SetColor( $config[$i]['color'] );
	    $lineplot->mark->SetColor($config[$i]['mark-color']);
		$lineplot->mark->SetFillColor($config[$i]['mark-fillcolor']);
		$lineplot->mark->SetSize($config[$i]['mark-size']);

		// $lineplot->value->SetColor('darkred');
		// $lineplot->value->show();
	}

    $graph_temp_directory = 'up/indicador_grafico';
    $graph_file_name = random_string().'.png';

    $graph_file_location = $graph_temp_directory . '/' . $graph_file_name;

    $graph->Stroke('./'.$graph_file_location);

    return $graph_file_location;
}

function group_barchart($tickLabels, $values, $tipo, $legenda, $width=300, $height=200)
{
	require_once( 'jpgraph/jpgraph.php' );
	
	require_once( 'jpgraph/jpgraph_bar.php' );
    require_once("jpgraph/jpgraph_line.php");

	$graph = new Graph($width,$height);
	$graph->SetScale('textlin');
	$graph->SetShadow();
	$graph->SetMargin(60,30,20,150);
	$graph->SetMarginColor('white');
	$graph->xaxis->SetTickLabels($tickLabels);
	
	$graph->xaxis->SetFont(FF_VERDANA,FS_NORMAL,8);
	$graph->yaxis->SetFont(FF_VERDANA,FS_NORMAL,8);		

	$graph->legend->SetFont(FF_VERDANA, FS_NORMAL, 8);
	$graph->legend->SetLayout(LEGEND_HOR);
	$graph->legend->Pos(0.4, 0.98, "center", "bottom");
	
	$graph->xaxis->SetLabelAngle(90);

	$fillGradient[] = 'olivedrab1';
	$fillGradient[] = 'brown1';
	$fillGradient[] = 'cadetblue1';
	$fillGradient[] = 'chocolate1';
	$fillGradient[] = 'gold1';
	$fillGradient[] = 'deeppink';
	$fillGradient[] = 'cadetblue';
	
	if(count($fillGradient) < count($values))
	{
		$nr_conta = 0;
		$nr_fim   = count($values) - count($fillGradient);
	
		while($nr_conta < $nr_fim )
		{
			$cor = getCor();
			while(in_array($cor, $fillGradient))
			{
				$cor = getCor();
			}
			$fillGradient[] = $cor;
			$nr_conta++;
		}
	}
	
	$color='darkred';

	$config[] = array( 'color'=>'red', 'mark-type'=>MARK_SQUARE, 'mark-color'=>'red', 'mark-fillcolor'=>'red' );
	$config[] = array( 'color'=>'orange', 'mark-type'=>MARK_DIAMOND, 'mark-color'=>'orange', 'mark-fillcolor'=>'orange' );
	$config[] = array( 'color'=>'blue', 'mark-type'=>MARK_UTRIANGLE, 'mark-color'=>'blue', 'mark-fillcolor'=>'blue' );
	$config[] = array( 'color'=>'olivedrab', 'mark-type'=>MARK_FILLEDCIRCLE, 'mark-color'=>'olivedrab', 'mark-fillcolor'=>'olivedrab' );
	$config[] = array( 'color'=>'green', 'mark-type'=>MARK_DTRIANGLE, 'mark-color'=>'green', 'mark-fillcolor'=>'green' );
	$config[] = array( 'color'=>'gold', 'mark-type'=>MARK_STAR, 'mark-color'=>'gold', 'mark-fillcolor'=>'gold' );
	$config[] = array( 'color'=>'deeppink', 'mark-type'=>MARK_CROSS, 'mark-color'=>'deeppink', 'mark-fillcolor'=>'deeppink' );
	$config[] = array( 'color'=>'chocolate', 'mark-type'=>MARK_X, 'mark-color'=>'chocolate', 'mark-fillcolor'=>'chocolate' );
	$config[] = array( 'color'=>'brown', 'mark-type'=>MARK_SQUARE, 'mark-color'=>'brown', 'mark-fillcolor'=>'brown' );
	$config[] = array( 'color'=>'cadetblue', 'mark-type'=>MARK_UTRIANGLE, 'mark-color'=>'cadetblue', 'mark-fillcolor'=>'cadetblue' );

	$aLineplot=array();
	for($i=0;$i<sizeof($values);$i++ )
	{
		if($tipo[$i]=='linha')
		{
			$lineplot = new LinePlot( $values[$i] );
			$lineplot->SetBarCenter(); 
		    $lineplot->mark->SetType( $config[$i]['mark-type'] );
			$lineplot->value->SetFont( FF_ARIAL, FS_BOLD, 10 );
			$lineplot->value->SetFormat('(%d)');
			$lineplot->SetLegend( $legenda[$i] );
			$lineplot->SetWeight(2);

			$aLineplot[] = $lineplot;
		}
		else//if($tipo[$i]=='barra')
		{
			// Create the first bar
			$bplot = new BarPlot($values[$i]);
			$bplot->SetFillColor($fillGradient[$i]);
			#$bplot->SetWidth(0.5);
			#$bplot->SetShadow();
			#$bplot->SetFillGradient($fillGradient[$i], $fillGradient[$i], GRAD_VERT);
			#$bplot->SetFillGradient($fillGradient[$i][0], $fillGradient[$i][0], GRAD_VERT);
			#$bplot->SetColor( $color );
			#$bplot->SetColor($fillGradient[$i],"darkred");
			$bplot->SetLegend($legenda[$i]);

			$aBplot[] = $bplot;
		}
	}

	$accbplot = new GroupBarPlot($aBplot);
	$graph->Add($accbplot);

	if(sizeof($aLineplot)>0)
	{
		$graph->Add($aLineplot);
	}

	// Display the graph
	$graph_temp_directory = 'up/indicador_grafico';
	$graph_file_name = random_string().'.png';

	$graph_file_location = $graph_temp_directory . '/' . $graph_file_name;

	$graph->Stroke('./'.$graph_file_location);

	return $graph_file_location;
}

function accumulate_barchart($tickLabels, $values, $legenda, $width=300, $height=200)
{
	require_once( 'jpgraph/jpgraph.php' );
	require_once( 'jpgraph/jpgraph_bar.php' );

	$graph = new Graph($width,$height);
	$graph->SetScale('textlin');
	$graph->SetShadow();
	$graph->SetMargin(40,30,20,130);
	$graph->SetMarginColor('white');
	$graph->xaxis->SetTickLabels($tickLabels);
	$graph->xaxis->SetLabelAngle(90);

	$graph->xaxis->SetFont(FF_VERDANA,FS_NORMAL,8);
	$graph->yaxis->SetFont(FF_VERDANA,FS_NORMAL,8);		
	
	$graph->legend->SetFont(FF_VERDANA, FS_NORMAL, 8);
	$graph->legend->SetLayout(LEGEND_HOR);
	$graph->legend->Pos(0.4, 0.98, "center", "bottom");

	$fillGradient[] = 'olivedrab1';
	$fillGradient[] = 'brown1';
	$fillGradient[] = 'cadetblue1';
	$fillGradient[] = 'chocolate1';
	$fillGradient[] = 'gold1';
	$fillGradient[] = 'deeppink';
	$fillGradient[] = 'cadetblue';
	
	if(count($fillGradient) < count($values))
	{
		$nr_conta = 0;
		$nr_fim   = count($values) - count($fillGradient);
	
		while($nr_conta < $nr_fim )
		{
			$cor = getCor();
			while(in_array($cor, $fillGradient))
			{
				$cor = getCor();
			}
			$fillGradient[] = $cor;
			$nr_conta++;
		}
	}
	
	$color='darkred';

	for( $i=0;$i<sizeof($values);$i++ )
	{
		$bplot = new BarPlot($values[$i]);
		$bplot->SetFillColor($fillGradient[$i]);
		$bplot->SetLegend($legenda[$i]);

		$aBplot[] = $bplot;
	}

	$accbplot = new AccBarPlot( $aBplot );
	$graph->Add($accbplot);

	// Display the graph
	$graph_temp_directory = 'up/indicador_grafico';
	$graph_file_name = random_string().'.png';

	$graph_file_location = $graph_temp_directory . '/' . $graph_file_name;

	$graph->Stroke('./'.$graph_file_location);

	return $graph_file_location;
}

function piechart($tickLabels, $values, $legenda, $width=300, $height=200)
{
	require_once ('jpgraph/jpgraph.php');
	require_once ('jpgraph/jpgraph_pie.php');
	require_once ('jpgraph/jpgraph_pie3d.php');	

	/*
	echo "<PRE>TICK <br>".print_r($tickLabels,true)."</PRE>";
	echo "<PRE>LEGENDA <br>".print_r($legenda,true)."</PRE>";
	echo "<PRE>VALOR <br>".print_r($values,true)."</PRE>";
	exit;
	*/
	
	// Create the Pie Graph.
	$graph = new PieGraph($width,$height,"auto");
	$graph->SetShadow();
	$graph->legend->Pos(0.1,0.2);
	$graph->legend->SetFont(FF_VERDANA, FS_NORMAL, 8);

	// Create pie plot
	$p1 = new PiePlot3d($values);
	$p1->SetTheme("earth");
	$p1->SetCenter(0.4);
	$p1->SetAngle(60);
	$p1->SetLegends($legenda);
	$p1->value->SetFont(FF_VERDANA, FS_NORMAL, 10);
	$p1->value->SetColor('black');

	$graph->Add($p1);
	
	$graph_temp_directory = 'up/indicador_grafico';
    $graph_file_name = random_string().'.png';

    $graph_file_location = $graph_temp_directory . '/' . $graph_file_name;

    $graph->Stroke('./'.$graph_file_location);

    return $graph_file_location;	
}

function getCor()
{
	$ar_cor = Array();
	$ar_cor[] = 'aqua';
	$ar_cor[] = 'lime';
	$ar_cor[] = 'teal';
	$ar_cor[] = 'whitesmoke';
	$ar_cor[] = 'gainsboro';
	$ar_cor[] = 'oldlace';
	$ar_cor[] = 'linen';
	$ar_cor[] = 'antiquewhite';
	$ar_cor[] = 'papayawhip';
	$ar_cor[] = 'blanchedalmond';
	$ar_cor[] = 'bisque';
	$ar_cor[] = 'peachpuff';
	$ar_cor[] = 'navajowhite';
	$ar_cor[] = 'moccasin';
	$ar_cor[] = 'cornsilk';
	$ar_cor[] = 'ivory';
	$ar_cor[] = 'lemonchiffon';
	$ar_cor[] = 'seashell';
	$ar_cor[] = 'mintcream';
	$ar_cor[] = 'azure';
	$ar_cor[] = 'aliceblue';
	$ar_cor[] = 'lavender';
	$ar_cor[] = 'lavenderblush';
	$ar_cor[] = 'mistyrose';
	$ar_cor[] = 'white';
	$ar_cor[] = 'black';
	$ar_cor[] = 'darkslategray';
	$ar_cor[] = 'dimgray';
	$ar_cor[] = 'slategray';
	$ar_cor[] = 'lightslategray';
	$ar_cor[] = 'gray';
	$ar_cor[] = 'lightgray';
	$ar_cor[] = 'midnightblue';
	$ar_cor[] = 'navy';
	$ar_cor[] = 'indigo';
	$ar_cor[] = 'electricindigo';
	$ar_cor[] = 'deepindigo';
	$ar_cor[] = 'pigmentindigo';
	$ar_cor[] = 'indigodye';
	$ar_cor[] = 'cornflowerblue';
	$ar_cor[] = 'darkslateblue';
	$ar_cor[] = 'slateblue';
	$ar_cor[] = 'mediumslateblue';
	$ar_cor[] = 'lightslateblue';
	$ar_cor[] = 'mediumblue';
	$ar_cor[] = 'royalblue';
	$ar_cor[] = 'blue';
	$ar_cor[] = 'dodgerblue';
	$ar_cor[] = 'deepskyblue';
	$ar_cor[] = 'skyblue';
	$ar_cor[] = 'lightskyblue';
	$ar_cor[] = 'steelblue';
	$ar_cor[] = 'lightred';
	$ar_cor[] = 'lightsteelblue';
	$ar_cor[] = 'lightblue';
	$ar_cor[] = 'powderblue';
	$ar_cor[] = 'paleturquoise';
	$ar_cor[] = 'darkturquoise';
	$ar_cor[] = 'mediumturquoise';
	$ar_cor[] = 'turquoise';
	$ar_cor[] = 'cyan';
	$ar_cor[] = 'lightcyan';
	$ar_cor[] = 'cadetblue';
	$ar_cor[] = 'mediumaquamarin';
	$ar_cor[] = 'aquamarine';
	$ar_cor[] = 'darkgreen';
	$ar_cor[] = 'darkolivegreen';
	$ar_cor[] = 'darkseagreen';
	$ar_cor[] = 'seagreen';
	$ar_cor[] = 'mediumseagreen';
	$ar_cor[] = 'lightseagreen';
	$ar_cor[] = 'palegreen';
	$ar_cor[] = 'springgreen';
	$ar_cor[] = 'lawngreen';
	$ar_cor[] = 'green';
	$ar_cor[] = 'chartreuse';
	$ar_cor[] = 'mediumspringgre';
	$ar_cor[] = 'greenyellow';
	$ar_cor[] = 'limegreen';
	$ar_cor[] = 'yellowgreen';
	$ar_cor[] = 'forestgreen';
	$ar_cor[] = 'olivedrab';
	$ar_cor[] = 'darkkhaki';
	$ar_cor[] = 'khaki';
	$ar_cor[] = 'palegoldenrod';
	$ar_cor[] = 'lightgoldenrody';
	$ar_cor[] = 'lightyellow';
	$ar_cor[] = 'yellow';
	$ar_cor[] = 'gold';
	$ar_cor[] = 'lightgoldenrod';
	$ar_cor[] = 'goldenrod';
	$ar_cor[] = 'darkgoldenrod';
	$ar_cor[] = 'rosybrown';
	$ar_cor[] = 'indianred';
	$ar_cor[] = 'saddlebrown';
	$ar_cor[] = 'sienna';
	$ar_cor[] = 'peru';
	$ar_cor[] = 'burlywood';
	$ar_cor[] = 'beige';
	$ar_cor[] = 'wheat';
	$ar_cor[] = 'sandybrown';
	$ar_cor[] = 'tan';
	$ar_cor[] = 'chocolate';
	$ar_cor[] = 'firebrick';
	$ar_cor[] = 'brown';
	$ar_cor[] = 'darksalmon';
	$ar_cor[] = 'salmon';
	$ar_cor[] = 'lightsalmon';
	$ar_cor[] = 'orange';
	$ar_cor[] = 'darkorange';
	$ar_cor[] = 'coral';
	$ar_cor[] = 'lightcoral';
	$ar_cor[] = 'tomato';
	$ar_cor[] = 'orangered';
	$ar_cor[] = 'red';
	$ar_cor[] = 'hotpink';
	$ar_cor[] = 'deeppink';
	$ar_cor[] = 'pink';
	$ar_cor[] = 'lightpink';
	$ar_cor[] = 'palevioletred';
	$ar_cor[] = 'maroon';
	$ar_cor[] = 'mediumvioletred';
	$ar_cor[] = 'violetred';
	$ar_cor[] = 'magenta';
	$ar_cor[] = 'violet';
	$ar_cor[] = 'plum';
	$ar_cor[] = 'orchid';
	$ar_cor[] = 'mediumorchid';
	$ar_cor[] = 'darkorchid';
	$ar_cor[] = 'darkviolet';
	$ar_cor[] = 'blueviolet';
	$ar_cor[] = 'purple';
	$ar_cor[] = 'mediumpurple';
	$ar_cor[] = 'thistle';
	$ar_cor[] = 'snow1';
	$ar_cor[] = 'snow2';
	$ar_cor[] = 'snow3';
	$ar_cor[] = 'snow4';
	$ar_cor[] = 'seashell1';
	$ar_cor[] = 'seashell2';
	$ar_cor[] = 'seashell3';
	$ar_cor[] = 'seashell4';
	$ar_cor[] = 'AntiqueWhite1';
	$ar_cor[] = 'AntiqueWhite2';
	$ar_cor[] = 'AntiqueWhite3';
	$ar_cor[] = 'AntiqueWhite4';
	$ar_cor[] = 'bisque1';
	$ar_cor[] = 'bisque2';
	$ar_cor[] = 'bisque3';
	$ar_cor[] = 'bisque4';
	$ar_cor[] = 'peachPuff1';
	$ar_cor[] = 'peachpuff2';
	$ar_cor[] = 'peachpuff3';
	$ar_cor[] = 'peachpuff4';
	$ar_cor[] = 'navajowhite1';
	$ar_cor[] = 'navajowhite2';
	$ar_cor[] = 'navajowhite3';
	$ar_cor[] = 'navajowhite4';
	$ar_cor[] = 'lemonchiffon1';
	$ar_cor[] = 'lemonchiffon2';
	$ar_cor[] = 'lemonchiffon3';
	$ar_cor[] = 'lemonchiffon4';
	$ar_cor[] = 'ivory1';
	$ar_cor[] = 'ivory2';
	$ar_cor[] = 'ivory3';
	$ar_cor[] = 'ivory4';
	$ar_cor[] = 'honeydew';
	$ar_cor[] = 'lavenderblush1';
	$ar_cor[] = 'lavenderblush2';
	$ar_cor[] = 'lavenderblush3';
	$ar_cor[] = 'lavenderblush4';
	$ar_cor[] = 'mistyrose1';
	$ar_cor[] = 'mistyrose2';
	$ar_cor[] = 'mistyrose3';
	$ar_cor[] = 'mistyrose4';
	$ar_cor[] = 'azure1';
	$ar_cor[] = 'azure2';
	$ar_cor[] = 'azure3';
	$ar_cor[] = 'azure4';
	$ar_cor[] = 'slateblue1';
	$ar_cor[] = 'slateblue2';
	$ar_cor[] = 'slateblue3';
	$ar_cor[] = 'slateblue4';
	$ar_cor[] = 'royalblue1';
	$ar_cor[] = 'royalblue2';
	$ar_cor[] = 'royalblue3';
	$ar_cor[] = 'royalblue4';
	$ar_cor[] = 'dodgerblue1';
	$ar_cor[] = 'dodgerblue2';
	$ar_cor[] = 'dodgerblue3';
	$ar_cor[] = 'dodgerblue4';
	$ar_cor[] = 'steelblue1';
	$ar_cor[] = 'steelblue2';
	$ar_cor[] = 'steelblue3';
	$ar_cor[] = 'steelblue4';
	$ar_cor[] = 'deepskyblue1';
	$ar_cor[] = 'deepskyblue2';
	$ar_cor[] = 'deepskyblue3';
	$ar_cor[] = 'deepskyblue4';
	$ar_cor[] = 'skyblue1';
	$ar_cor[] = 'skyblue2';
	$ar_cor[] = 'skyblue3';
	$ar_cor[] = 'skyblue4';
	$ar_cor[] = 'lightskyblue1';
	$ar_cor[] = 'lightskyblue2';
	$ar_cor[] = 'lightskyblue3';
	$ar_cor[] = 'lightskyblue4';
	$ar_cor[] = 'slategray1';
	$ar_cor[] = 'slategray2';
	$ar_cor[] = 'slategray3';
	$ar_cor[] = 'slategray4';
	$ar_cor[] = 'lightsteelblue1';
	$ar_cor[] = 'lightsteelblue2';
	$ar_cor[] = 'lightsteelblue3';
	$ar_cor[] = 'lightsteelblue4';
	$ar_cor[] = 'lightblue1';
	$ar_cor[] = 'lightblue2';
	$ar_cor[] = 'lightblue3';
	$ar_cor[] = 'lightblue4';
	$ar_cor[] = 'lightcyan1';
	$ar_cor[] = 'lightcyan2';
	$ar_cor[] = 'lightcyan3';
	$ar_cor[] = 'lightcyan4';
	$ar_cor[] = 'paleturquoise1';
	$ar_cor[] = 'paleturquoise2';
	$ar_cor[] = 'paleturquoise3';
	$ar_cor[] = 'paleturquoise4';
	$ar_cor[] = 'cadetblue1';
	$ar_cor[] = 'cadetblue2';
	$ar_cor[] = 'cadetblue3';
	$ar_cor[] = 'cadetblue4';
	$ar_cor[] = 'turquoise1';
	$ar_cor[] = 'turquoise2';
	$ar_cor[] = 'turquoise3';
	$ar_cor[] = 'turquoise4';
	$ar_cor[] = 'cyan1';
	$ar_cor[] = 'cyan2';
	$ar_cor[] = 'cyan3';
	$ar_cor[] = 'cyan4';
	$ar_cor[] = 'darkslategray1';
	$ar_cor[] = 'darkslategray2';
	$ar_cor[] = 'darkslategray3';
	$ar_cor[] = 'darkslategray4';
	$ar_cor[] = 'aquamarine1';
	$ar_cor[] = 'aquamarine2';
	$ar_cor[] = 'aquamarine3';
	$ar_cor[] = 'aquamarine4';
	$ar_cor[] = 'darkseagreen1';
	$ar_cor[] = 'darkseagreen2';
	$ar_cor[] = 'darkseagreen3';
	$ar_cor[] = 'darkseagreen4';
	$ar_cor[] = 'seagreen1';
	$ar_cor[] = 'seagreen2';
	$ar_cor[] = 'seagreen3';
	$ar_cor[] = 'seagreen4';
	$ar_cor[] = 'palegreen1';
	$ar_cor[] = 'palegreen2';
	$ar_cor[] = 'palegreen3';
	$ar_cor[] = 'palegreen4';
	$ar_cor[] = 'springgreen1';
	$ar_cor[] = 'springgreen2';
	$ar_cor[] = 'springgreen3';
	$ar_cor[] = 'springgreen4';
	$ar_cor[] = 'chartreuse1';
	$ar_cor[] = 'chartreuse2';
	$ar_cor[] = 'chartreuse3';
	$ar_cor[] = 'chartreuse4';
	$ar_cor[] = 'olivedrab1';
	$ar_cor[] = 'olivedrab2';
	$ar_cor[] = 'olivedrab3';
	$ar_cor[] = 'olivedrab4';
	$ar_cor[] = 'darkolivegreen1';
	$ar_cor[] = 'darkolivegreen2';
	$ar_cor[] = 'darkolivegreen3';
	$ar_cor[] = 'darkolivegreen4';
	$ar_cor[] = 'khaki1';
	$ar_cor[] = 'khaki2';
	$ar_cor[] = 'khaki3';
	$ar_cor[] = 'khaki4';
	$ar_cor[] = 'lightgoldenrod1';
	$ar_cor[] = 'lightgoldenrod2';
	$ar_cor[] = 'lightgoldenrod3';
	$ar_cor[] = 'lightgoldenrod4';
	$ar_cor[] = 'yellow1';
	$ar_cor[] = 'yellow2';
	$ar_cor[] = 'yellow3';
	$ar_cor[] = 'yellow4';
	$ar_cor[] = 'gold1';
	$ar_cor[] = 'gold2';
	$ar_cor[] = 'gold3';
	$ar_cor[] = 'gold4';
	$ar_cor[] = 'goldenrod1';
	$ar_cor[] = 'goldenrod2';
	$ar_cor[] = 'goldenrod3';
	$ar_cor[] = 'goldenrod4';
	$ar_cor[] = 'darkgoldenrod1';
	$ar_cor[] = 'darkgoldenrod2';
	$ar_cor[] = 'darkgoldenrod3';
	$ar_cor[] = 'darkgoldenrod4';
	$ar_cor[] = 'rosybrown1';
	$ar_cor[] = 'rosybrown2';
	$ar_cor[] = 'rosybrown3';
	$ar_cor[] = 'rosybrown4';
	$ar_cor[] = 'indianred1';
	$ar_cor[] = 'indianred2';
	$ar_cor[] = 'indianred3';
	$ar_cor[] = 'indianred4';
	$ar_cor[] = 'sienna1';
	$ar_cor[] = 'sienna2';
	$ar_cor[] = 'sienna3';
	$ar_cor[] = 'sienna4';
	$ar_cor[] = 'burlywood1';
	$ar_cor[] = 'burlywood2';
	$ar_cor[] = 'burlywood3';
	$ar_cor[] = 'burlywood4';
	$ar_cor[] = 'wheat1';
	$ar_cor[] = 'wheat2';
	$ar_cor[] = 'wheat3';
	$ar_cor[] = 'wheat4';
	$ar_cor[] = 'tan1';
	$ar_cor[] = 'tan2';
	$ar_cor[] = 'tan3';
	$ar_cor[] = 'tan4';
	$ar_cor[] = 'chocolate1';
	$ar_cor[] = 'chocolate2';
	$ar_cor[] = 'chocolate3';
	$ar_cor[] = 'chocolate4';
	$ar_cor[] = 'firebrick1';
	$ar_cor[] = 'firebrick2';
	$ar_cor[] = 'firebrick3';
	$ar_cor[] = 'firebrick4';
	$ar_cor[] = 'brown1';
	$ar_cor[] = 'brown2';
	$ar_cor[] = 'brown3';
	$ar_cor[] = 'brown4';
	$ar_cor[] = 'salmon1';
	$ar_cor[] = 'salmon2';
	$ar_cor[] = 'salmon3';
	$ar_cor[] = 'salmon4';
	$ar_cor[] = 'lightsalmon1';
	$ar_cor[] = 'lightsalmon2';
	$ar_cor[] = 'lightsalmon3';
	$ar_cor[] = 'lightsalmon4';
	$ar_cor[] = 'orange1';
	$ar_cor[] = 'orange2';
	$ar_cor[] = 'orange3';
	$ar_cor[] = 'orange4';
	$ar_cor[] = 'darkorange1';
	$ar_cor[] = 'darkorange2';
	$ar_cor[] = 'darkorange3';
	$ar_cor[] = 'darkorange4';
	$ar_cor[] = 'coral1';
	$ar_cor[] = 'coral2';
	$ar_cor[] = 'coral3';
	$ar_cor[] = 'coral4';
	$ar_cor[] = 'tomato1';
	$ar_cor[] = 'tomato2';
	$ar_cor[] = 'tomato3';
	$ar_cor[] = 'tomato4';
	$ar_cor[] = 'orangered1';
	$ar_cor[] = 'orangered2';
	$ar_cor[] = 'orangered3';
	$ar_cor[] = 'orangered4';
	$ar_cor[] = 'deeppink1';
	$ar_cor[] = 'deeppink2';
	$ar_cor[] = 'deeppink3';
	$ar_cor[] = 'deeppink4';
	$ar_cor[] = 'hotpink1';
	$ar_cor[] = 'hotpink2';
	$ar_cor[] = 'hotpink3';
	$ar_cor[] = 'hotpink4';
	$ar_cor[] = 'pink1';
	$ar_cor[] = 'pink2';
	$ar_cor[] = 'pink3';
	$ar_cor[] = 'pink4';
	$ar_cor[] = 'lightpink1';
	$ar_cor[] = 'lightpink2';
	$ar_cor[] = 'lightpink3';
	$ar_cor[] = 'lightpink4';
	$ar_cor[] = 'palevioletred1';
	$ar_cor[] = 'palevioletred2';
	$ar_cor[] = 'palevioletred3';
	$ar_cor[] = 'palevioletred4';
	$ar_cor[] = 'maroon1';
	$ar_cor[] = 'maroon2';
	$ar_cor[] = 'maroon3';
	$ar_cor[] = 'maroon4';
	$ar_cor[] = 'violetred1';
	$ar_cor[] = 'violetred2';
	$ar_cor[] = 'violetred3';
	$ar_cor[] = 'violetred4';
	$ar_cor[] = 'magenta1';
	$ar_cor[] = 'magenta2';
	$ar_cor[] = 'magenta3';
	$ar_cor[] = 'magenta4';
	$ar_cor[] = 'mediumred';
	$ar_cor[] = 'orchid1';
	$ar_cor[] = 'orchid2';
	$ar_cor[] = 'orchid3';
	$ar_cor[] = 'orchid4';
	$ar_cor[] = 'plum1';
	$ar_cor[] = 'plum2';
	$ar_cor[] = 'plum3';
	$ar_cor[] = 'plum4';
	$ar_cor[] = 'mediumorchid1';
	$ar_cor[] = 'mediumorchid2';
	$ar_cor[] = 'mediumorchid3';
	$ar_cor[] = 'mediumorchid4';
	$ar_cor[] = 'darkorchid1';
	$ar_cor[] = 'darkorchid2';
	$ar_cor[] = 'darkorchid3';
	$ar_cor[] = 'darkorchid4';
	$ar_cor[] = 'purple1';
	$ar_cor[] = 'purple2';
	$ar_cor[] = 'purple3';
	$ar_cor[] = 'purple4';
	$ar_cor[] = 'mediumpurple1';
	$ar_cor[] = 'mediumpurple2';
	$ar_cor[] = 'mediumpurple3';
	$ar_cor[] = 'mediumpurple4';
	$ar_cor[] = 'thistle1';
	$ar_cor[] = 'thistle2';
	$ar_cor[] = 'thistle3';
	$ar_cor[] = 'thistle4';
	$ar_cor[] = 'gray1';
	$ar_cor[] = 'gray2';
	$ar_cor[] = 'gray3';
	$ar_cor[] = 'gray4';
	$ar_cor[] = 'gray5';
	$ar_cor[] = 'gray6';
	$ar_cor[] = 'gray7';
	$ar_cor[] = 'gray8';
	$ar_cor[] = 'gray9';
	$ar_cor[] = 'darkgray';
	$ar_cor[] = 'darkblue';
	$ar_cor[] = 'darkcyan';
	$ar_cor[] = 'darkmagenta';
	$ar_cor[] = 'darkred';
	$ar_cor[] = 'silver';
	$ar_cor[] = 'eggplant';
	$ar_cor[] = 'lightgreen';
	
	return $ar_cor[rand(0,count($ar_cor))];
}

    function in_multiarray($elem, $array)
    {
        $top = sizeof($array) - 1;
        $bottom = 0;
        while($bottom <= $top)
        {
            if($array[$bottom] == $elem)
                return true;
            else
                if(is_array($array[$bottom]))
                    if(in_multiarray($elem, ($array[$bottom])))
                        return true;
                   
            $bottom++;
        }       
        return false;
    }
?>