<?php
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/jpgraph.php');
   include_once('inc/jpgraph_pie.php');
   include_once('inc/jpgraph_pie3d.php');
   $sql =        " select distinct (count (ip)) as num_acessos, ip  from conta_acessos";
   $sql = $sql . " where (ip <> 'null') and ip in ";
   $sql = $sql . "    ('10.63.255.223', '200.248.156.66', '65.54.164.127', '200.198.107.2', '10.68.255.2', ";
   $sql = $sql . "    '200.194.203.98', '200.198.136.15', '200.162.209.227', '200.248.213.18', '200.174.81.247', '200.203.71.3', '200.196.57.154',";
   $sql = $sql . "    '200.96.117.197', '200.152.225.91', '200.203.19.61', '10.63.255.16', '10.63.255.16', '200.146.152.56', '200.203.2.36')";
   $sql = $sql . "    	group by ip";
   $sql = $sql . "		order by num_acessos desc, ip desc ";
   
   $rs = pg_query($db, $sql);
   $cont = 0;
   while ($reg = pg_fetch_array($rs))
   {
      $valores_os[$cont] = $reg['num_acessos'];
      $titulos_os[$cont] = $reg['ip'];
      $cont = $cont + 1;
   }
   pg_close($db);
   $graph = new PieGraph(600, 400, "auto");
   $graph->title->Set("De onde provêm os acessos");
   $tam = 0.4;
   $pz_o = new PiePlot3D($valores_os);
   $pz_o->SetCenter(0.41, 0.5);
   $pz_o->SetSize($tam);
   $pz_o->SetTheme("earth");
   $pz_o->SetLegends($titulos_os);
	
   $graph->Add($pz_o);
   $graph->Stroke();
?>