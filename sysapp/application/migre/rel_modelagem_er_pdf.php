<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	require_once('inc/fpdf153/draw.php');

	#### TAMANHO DA PAGINA ####
	$qr_select = "
					SELECT MAX(mt.nr_x + mt.nr_largura) AS nr_max_x,
                           MAX(mt.nr_y + mt.nr_altura) AS nr_max_y,
						   MIN(mt.nr_x) AS nr_min_x,
                           MIN(mt.nr_y) AS nr_min_y   						   
					  FROM modelagem.modelos_tabelas mt
					 WHERE mt.cd_modelo = ".$_REQUEST['cd_modelo']."
	             ";
	$ob_result = pg_query($db, $qr_select);
	$ar_reg = pg_fetch_array($ob_result);

	$nr_max_x = ceil($ar_reg['nr_max_x']/(72/2.54)) + ceil($ar_reg['nr_min_x']/(72/2.54));
	$nr_max_y = ceil($ar_reg['nr_max_y']/(72/2.54)) + ceil($ar_reg['nr_min_y']/(72/2.54));
	$ob_pdf = new PDF_Draw("P","cm",array($nr_max_x,$nr_max_y ));

	$ob_pdf->AddPage();

	#### DESCRIÇÃO DO MODELO ####
	$qr_select = "
					SELECT m.ds_modelo,
					       m.ds_cor
                      FROM modelagem.modelos m
                     WHERE m.cd_modelo = ".$_REQUEST['cd_modelo']."
				 ";
	$ob_result = pg_query($db, $qr_select);	
	$ar_reg = pg_fetch_array($ob_result);	

	$nr_len = (strlen($ar_reg['ds_modelo'])/3)/2;
	$ob_pdf->SetFont('courier', '', 18);
	$ob_pdf->Text(((($nr_max_x - 1.5)/2) + 0.05) - $nr_len, 0.9, $ar_reg['ds_modelo']);	

	$ob_pdf->SetFont('courier', '', 8);

	#### LISTA MODELOS ####
	$qr_select = "
					SELECT DISTINCT(m.ds_modelo) AS ds_modelo,
                           m.ds_cor
                      FROM modelagem.modelos_tabelas mt,
                           (SELECT DISTINCT(m1.ds_modelo),
                                   m1.ds_cor,
                                   mt1.ds_esquema,
                                   mt1.ds_tabela
						      FROM modelagem.modelos_tabelas mt1,
						 	       modelagem.modelos m1
						     WHERE m1.cd_modelo     = mt1.cd_modelo
						       AND mt1.fl_principal = 'S') AS m
                     WHERE mt.cd_modelo = ".$_REQUEST['cd_modelo']."
					   AND m.ds_esquema = mt.ds_esquema
					   AND m.ds_tabela  = mt.ds_tabela
				 ";
	$ob_result = pg_query($db, $qr_select);	
	$nr_prox_y = addTabela(0.5,1.1,7.5, "LEGENDA",'');
	while ($ar_reg = pg_fetch_array($ob_result)) 
	{
		addTabela(0.5,$nr_prox_y,0.5, "",$ar_reg['ds_cor']);
		$nr_prox_y = addTabela(1,$nr_prox_y,7, $ar_reg['ds_modelo'],'');
	}	

	#### LISTA TABELAS DO MODELO ####
	$ar_tabelas = array();
	$qr_select = "
					SELECT mt.ds_esquema,
						   mt.ds_tabela,
						   mt.nr_x,
						   mt.nr_y,
						   mt.nr_largura,
						   (SELECT m1.ds_cor 
							  FROM modelagem.modelos_tabelas mt1,
								   modelagem.modelos m1
							 WHERE m1.cd_modelo     = mt1.cd_modelo
							   AND mt1.ds_esquema   = mt.ds_esquema
							   AND mt1.ds_tabela    = mt.ds_tabela
							   AND mt1.fl_principal = 'S') AS ds_cor,
						   a.attnum AS nr_ordem, 
						   a.attname AS ds_campo, 
						   REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(pg_catalog.format_type(a.atttypid, a.atttypmod)
                                                   ,'character varying','varchar') 
                                                   ,'timestamp(0) without time zone','timestamp') 
                                                   ,'timestamp without time zone','timestamp') 
                                                   ,'timestamp with time zone','timestamp') 
                                                   ,'time without time zone','time')  AS ds_tipo,
						   (CASE WHEN a.attnotnull = TRUE
								 THEN 'NOT NULL'
								 ELSE 'NULL'
						   END) AS ds_notnull,
						   (SELECT adsrc 
							  FROM pg_attrdef adef 
							 WHERE a.attrelid = adef.adrelid 
							   AND a.attnum   = adef.adnum) AS adsrc	   
					  FROM modelagem.modelos_tabelas mt,
						   pg_catalog.pg_class c, 
						   pg_catalog.pg_attribute a, 
						   pg_catalog.pg_type t,
						   pg_catalog.pg_namespace n 
					 WHERE a.attnum     > 0 
					   AND a.attrelid   = c.oid 
					   AND n.nspname    = mt.ds_esquema 
					   AND c.relname    = mt.ds_tabela 
					   AND a.atttypid   = t.oid
					   AND n.oid        = c.relnamespace		 
					   AND mt.cd_modelo = ".$_REQUEST['cd_modelo']."
	             ";
	$ob_result = pg_query($db, $qr_select);	
	$ds_tabela_atual = "";
	$lt_tabelas = "";	
	$nr_conta = 1;
	$nr_prox_y = 0;
	while ($ar_reg = pg_fetch_array($ob_result)) 
	{
		if($ds_tabela_atual != $ar_reg['ds_esquema'].".".$ar_reg['ds_tabela'])
		{
			$ds_tabela_atual = $ar_reg['ds_esquema'].".".$ar_reg['ds_tabela'];
			$nr_prox_y = addTabela(($ar_reg['nr_x']/(72/2.54)),($ar_reg['nr_y']/(72/2.54)),ceil($ar_reg['nr_largura']/(72/2.54)), strtoupper($ar_reg['ds_esquema'].".".$ar_reg['ds_tabela']),$ar_reg['ds_cor']);

			$nr_conta++;
		}
		
		$ds_chave = getCampoKey(($ar_reg['nr_x']/(72/2.54)),$nr_prox_y,$ar_reg['ds_esquema'],$ar_reg['ds_tabela'],$ar_reg['ds_campo'],$db);
		addTabela(($ar_reg['nr_x']/(72/2.54)),$nr_prox_y,1,$ds_chave,'');	
        addTabela(($ar_reg['nr_x']/(72/2.54))+1,$nr_prox_y,ceil($ar_reg['nr_largura']/(72/2.54))-3.5, $ar_reg['ds_campo'],'');	
		$nr_prox_y =  addTabela(($ar_reg['nr_x']/(72/2.54))+(ceil($ar_reg['nr_largura']/(72/2.54)))-2.5,$nr_prox_y,2.5,$ar_reg['ds_tipo'],'');
		$ar_tabelas[$ar_reg['ds_esquema'].".".$ar_reg['ds_tabela']] = array(
																			"x" => ($ar_reg['nr_x']/(72/2.54)),
																			"y" => $nr_prox_y,
																			"w" => ceil($ar_reg['nr_largura']/(72/2.54)),
																			"h" => $nr_prox_y - ($ar_reg['nr_y']/(72/2.54)) 
																			);
	}

	#### LISTA RELACIONAMENTOS ####
	$qr_select = "
					SELECT DISTINCT 'FK_' || mt.ds_esquema || '.' || mt.ds_tabela || '.' || mt2.ds_esquema || '.' || mt2.ds_tabela AS ds_chave,
						   mt.ds_esquema || '.' || mt.ds_tabela AS ds_tabela_ini,  
						   mt2.ds_esquema || '.' || mt2.ds_tabela AS ds_tabela_fim						   
					  FROM pg_catalog.pg_class r,
						   pg_catalog.pg_class f,
						   pg_catalog.pg_constraint c,
						   pg_catalog.pg_namespace n,
						   modelagem.modelos_tabelas mt,
						   modelagem.modelos_tabelas mt2
					 WHERE contype        IN('f')
					   AND r.oid          = c.conrelid
					   AND n.oid          = r.relnamespace 
					   AND f.oid          = c.confrelid 
					   AND mt.ds_esquema  = n.nspname
					   AND mt.ds_tabela   = r.relname 
					   AND mt.cd_modelo   = ".$_REQUEST['cd_modelo']."						   
					   AND mt2.cd_modelo  = mt.cd_modelo
					   AND mt2.ds_esquema = n.nspname
					   AND mt2.ds_tabela  = f.relname 
				 ";
	$ob_result = pg_query($db, $qr_select);	
	$nr_conta = 0;
	while ($ar_reg = pg_fetch_array($ob_result)) 
	{
		$nr_ini_x  = $ar_tabelas[$ar_reg['ds_tabela_ini']]['x'];
		$nr_ini_y  = $ar_tabelas[$ar_reg['ds_tabela_ini']]['y'];
		$nr_ini_w  = $ar_tabelas[$ar_reg['ds_tabela_ini']]['w'];
		$nr_ini_h  = $ar_tabelas[$ar_reg['ds_tabela_ini']]['h'];

		$nr_fim_x  = $ar_tabelas[$ar_reg['ds_tabela_fim']]['x'];
		$nr_fim_y  = $ar_tabelas[$ar_reg['ds_tabela_fim']]['y'];
		$nr_fim_w  = $ar_tabelas[$ar_reg['ds_tabela_fim']]['w'];
		$nr_fim_h  = $ar_tabelas[$ar_reg['ds_tabela_fim']]['h'];

		$nr_conta++;
		
		if (($nr_ini_y - $nr_ini_h) < $nr_fim_y)
		{
			if (($nr_ini_x + $nr_ini_w) < ($nr_fim_x))
			{
				//$ob_pdf->Text(0.5, $nr_conta+1,"A");
				addRelacionamento(
									#I_AX
									$nr_ini_x + $nr_ini_w, 
									#I_AY
									$nr_ini_y - ($nr_ini_h/2),
									#F_DX
									$nr_fim_x,
									#F_DY
									$nr_fim_y - ($nr_fim_h/2) 
								 );				
			}
			else if (($nr_fim_x + $nr_fim_w) < ($nr_ini_x))
			{
				//$ob_pdf->Text(0.5, $nr_conta+1,"B");
				addRelacionamento(
									#F_AX
									$nr_fim_x + $nr_fim_w, 
									#F_AY
									$nr_fim_y - ($nr_fim_h/2),
									#I_DX
									$nr_ini_x,
									#I_DY
									$nr_ini_y - ($nr_ini_h/2) 
								 );				
			}	
            else	
			{
				//$ob_pdf->Text(0.5, $nr_conta+1,"C");
				addRelacionamento(
									#I_AX
									$nr_ini_x + ($nr_ini_w/2),
									#I_AX
									$nr_ini_y,
									#F_CX
									$nr_fim_x + ($nr_fim_w/2),
									#F_CY
									$nr_fim_y - $nr_fim_h 
							      );
			}
			
		}
		else if (($nr_ini_y - $nr_ini_h) > $nr_fim_y)
		{
			//$ob_pdf->Text(0.5, $nr_conta+1,"D");
			addRelacionamento(
								#F_CX
								$nr_fim_x + ($nr_fim_w/2) ,
								#F_CY
								$nr_fim_y,
								#I_CX
								$nr_ini_x + ($nr_ini_w/2),
								#I_CX
								$nr_ini_y - $nr_ini_h
							 );
		}
	}	
	
	$ob_pdf->Output();
	
	function addTabela($x,$y,$largura,$ds_texto,$nr_cor)
	{
		GLOBAL $ob_pdf;

		$style = array('width' => 0.02, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		if(trim($nr_cor) == "")
		{
			$ar_cor = array(255, 255, 255);
		}
		else
		{
			$ar_cor = convertHexToRGB($nr_cor);
		}
		
		$x1 = $x;
		$y1 = $y;
		$x2 = $x + $largura;
		$y2 = $y + 0.38;

		$ob_pdf->Polygon(array($x1,$y1,$x2,$y1,$x2,$y2,$x1,$y2),'DF', array('all' => $style), $ar_cor);
		$ob_pdf->Text($x1 + 0.05, $y2 - 0.1, $ds_texto);
		
		return $y2;
	}


	function addRelacionamento($x1,$y1,$x2,$y2)
	{
		GLOBAL $ob_pdf;
		$style = array('width' => 0.03, 'cap' => 'round', 'join' => 'round', 'dash' => '0', 'color' => array(0, 0, 0));
		$ob_pdf->Line($x1,$y1,$x2,$y2, $style);	
	}

	
	function getCampoKey($x,$y,$ds_esquema,$ds_tabela,$ds_campo,$db)
	{
		GLOBAL $ob_pdf;
		$qr_select = "
						SELECT a.ds_esquema,
						       a.ds_tabela,
						       a.ds_campo,
						       c.conname AS ds_chave,
							   (CASE WHEN c.contype = 'p' THEN 'table_key.jpg'
							         WHEN c.contype = 'f' THEN 'table_relationship.jpg'
							   END) AS tp_chave,
							   (CASE WHEN c.contype = 'p' THEN 'PK'
							         WHEN c.contype = 'f' THEN 'FK'
							   END) AS id_chave							   
						  FROM pg_catalog.pg_class r,
						       pg_catalog.pg_constraint c,
						       pg_catalog.pg_namespace n,
						       (SELECT n1.nspname AS ds_esquema,
						               c1.relname AS ds_tabela,
						               a1.attnum AS nr_ordem, 
						               a1.attname AS ds_campo
						          FROM pg_catalog.pg_class c1, 
						               pg_catalog.pg_attribute a1, 
						               pg_catalog.pg_namespace n1 
						         WHERE a1.attnum     > 0 
						           AND a1.attrelid   = c1.oid 
						           AND n1.oid        = c1.relnamespace) a
						 WHERE contype      IN('p','f')
						   AND r.oid        = c.conrelid
						   AND n.oid        = r.relnamespace  
						   AND n.nspname    = a.ds_esquema
						   AND r.relname    = a.ds_tabela
						   AND a.ds_esquema = '".$ds_esquema."' 
						   AND a.ds_tabela  = '".$ds_tabela."' 
						   AND a.ds_campo   = '".$ds_campo."'
						   AND (a.nr_ordem = c.conkey[1] OR a.nr_ordem = c.conkey[2] OR a.nr_ordem = c.conkey[3] OR a.nr_ordem = c.conkey[4] OR a.nr_ordem = c.conkey[5] OR a.nr_ordem = c.conkey[6] OR a.nr_ordem = c.conkey[7] OR a.nr_ordem = c.conkey[8] OR a.nr_ordem = c.conkey[9])		
						 ORDER BY id_chave DESC
		             ";
		$ob_result = pg_query($db, $qr_select);	
		$ds_retorno = "";
		while ($ar_reg = pg_fetch_array($ob_result)) 
		{	
			//$ds_retorno.= " <img src='img/".$ar_reg['tp_chave']."' border='0' title='".$ar_reg['ds_chave']."''>";
			if(trim($ds_retorno) == "")
			{
				$ds_retorno.= $ar_reg['id_chave'];
			}
			else
			{
				$ds_retorno.= "/".$ar_reg['id_chave'];
			}
		}
		return $ds_retorno;
	}	
	
	function convertHexToRGB($nr_hex)
	{
	    if ($nr_hex[0] == '#')
	        $nr_hex = substr($color, 1);

	    if (strlen($nr_hex) == 6)
	        list($r, $g, $b) = array($nr_hex[0].$nr_hex[1],
	                                 $nr_hex[2].$nr_hex[3],
	                                 $nr_hex[4].$nr_hex[5]);
	    elseif (strlen($nr_hex) == 3)
	        list($r, $g, $b) = array($nr_hex[0], $nr_hex[1], $nr_hex[2]);
	    else
	        return false;

	    $r = hexdec($r); 
		$g = hexdec($g); 
		$b = hexdec($b);

	    return array($r, $g, $b);
	}	
?>
