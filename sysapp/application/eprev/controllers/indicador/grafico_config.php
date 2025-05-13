<?php
class grafico_config extends Controller
{
	function __construct()
	{
		parent::Controller();
		$this->load->helper('indicador');
		$this->load->helper('string');
	}

	function index($cd_indicador_tabela=0)
	{
		if(CheckLogin())
		{
			/*$q=$this->db->query("
			SELECT it.cd_indicador_tabela as value, it.ds_indicador_tabela || ' - ' || ip.ds_periodo || ' (' || it.cd_indicador_tabela::varchar || ')' as text 
			FROM indicador.indicador_tabela it
			JOIN indicador.indicador i ON i.cd_indicador=it.cd_indicador
			JOIN indicador.indicador_periodo ip ON ip.cd_indicador_periodo=it.cd_indicador_periodo
			WHERE it.dt_exclusao IS NULL AND i.dt_exclusao IS NULL 
			AND i.cd_usuario_responsavel=? 
			AND it.dt_fechamento_periodo IS NULL
			AND current_timestamp BETWEEN ip.dt_inicio AND ip.dt_fim
			ORDER BY it.ds_indicador_tabela
			",array(usuario_id()));*/

			$q=$this->db->query("
			SELECT it.cd_indicador_tabela as value, it.ds_indicador_tabela || ' - ' || ip.ds_periodo || ' (' || it.cd_indicador_tabela::varchar || ')' as text 
			FROM indicador.indicador_tabela it
			JOIN indicador.indicador i ON i.cd_indicador=it.cd_indicador
			JOIN indicador.indicador_periodo ip ON ip.cd_indicador_periodo=it.cd_indicador_periodo
			WHERE i.cd_indicador_grupo=7
			ORDER BY it.ds_indicador_tabela
			",array(usuario_id()));

			$data['indicador_dd']=$q->result_array();
			$data['cd_indicador_tabela']=$cd_indicador_tabela;

			$this->load->view( 'indicador/tabela/grafico_escolher.php',$data );
		}
	}

	function jpgraph_linha()
	{
        $this->load->plugin('jpgraph');

        $tick_labels = indicador_db::pegar_rotulos( 12, 0,0, 1,13 );
        $legends = indicador_db::pegar_rotulos( 12, 5,7, 0,0 );

        $valores[] = indicador_db::pegar_valores( 12, 5,5, 1,13 );
        $valores[] = indicador_db::pegar_valores( 12, 6,6, 1,13 );
        $valores[] = indicador_db::pegar_valores( 12, 7,7, 1,13 );

        $data['graph'] = linechart( $tick_labels, $valores, $legends, 750, 600 );

        $this->load->view('indicador/tabela/jpgraph_grafico.php', $data);
	}

	function jpgraph_barra()
	{
        $this->load->plugin('jpgraph');

        $legenda = indicador_db::pegar_rotulos( 12, 0,0, 1,13 );
        $valores[] = indicador_db::pegar_valores( 12, 5,5, 1,13 );
        $valores[] = indicador_db::pegar_valores( 12, 6,6, 1,13 );
        $valores[] = indicador_db::pegar_valores( 12, 7,7, 1,13 );

        $data['graph'] = accumulate_barchart($legenda,$valores,750,300);

        $this->load->view('indicador/tabela/jpgraph_grafico.php', $data);
	}

	function jpgraph_pizza()
	{
        $this->load->plugin('jpgraph');

        $legenda = indicador_db::pegar_rotulos( 14, 1,1, 1,9 );        
        $valores = indicador_db::pegar_valores( 14, 2,2, 1,9 );        

        $data['graph'] = piechart( $legenda, $valores, 600, 400 );

        $this->load->view('indicador/tabela/jpgraph_grafico.php', $data);
	}

	function grafico($cd_indicador)
	{
		
		
		$cd_indicador=intval($cd_indicador);
		if( intval($cd_indicador)!=0 )
		{
			$this->load->helper( 'indicador' );
			
			$ob_resul = new indicador_tools;
			$ret= $ob_resul->gerar_grafico(intval( $cd_indicador_tabela ));
			

			if($ret!='')
			{
				$ret=base_url().$ret;
			}

			echo $ret;
		}
		else
		{
			echo 'Indicador deve ser informado.';
		}
		
		
		
		exit;
		$q=$this->db->query("
		SELECT grafico.* 
		FROM indicador.indicador_tabela it 
		JOIN indicador.indicador_tabela_grafico grafico 
		ON grafico.cd_indicador_tabela=it.cd_indicador_tabela 
		WHERE it.cd_indicador=? AND grafico.dt_exclusao IS NULL 
		", array(intval($cd_indicador)) );

		$row=$q->row_array();

		$INDICADOR_GRAFICO_LINHA=1;
		$INDICADOR_GRAFICO_BARRA_ACUMULADO=2;
		$INDICADOR_GRAFICO_PIZZA=4;

        $this->load->plugin('jpgraph');
		if( $row['cd_indicador_grafico_tipo']==$INDICADOR_GRAFICO_PIZZA )
		{
			$l = explode( ',', $row['ds_range_legenda'] );
			$v = explode( ',', $row['ds_range_valor'] );
			
			$legenda=array();
			$valores=array();

	        $this->load->plugin('jpgraph');
	        $legenda = indicador_db::pegar_rotulos( $cd_indicador, $l[0],$l[1], $l[2],$l[3] );        
	        $valores = indicador_db::pegar_valores( $cd_indicador, $v[0],$v[1], $v[2],$v[3] );

	        $data['graph'] = piechart($legenda,$valores,600,400);
	        $this->load->view('indicador/tabela/jpgraph_grafico.php', $data);
		}
		elseif( $row['cd_indicador_grafico_tipo']==$INDICADOR_GRAFICO_BARRA_ACUMULADO )
		{
			$l = explode( ',', $row['ds_range_tick'] );
			$av = explode( ';', $row['ds_range_valor'] );
			
			$legenda=array();
			$valores=array();

            $legenda = indicador_db::pegar_rotulos( $cd_indicador, $l[0],$l[1], $l[2],$l[3] );
            foreach($av as $item)
            {
            	$v = explode( ',', $item );
            	$valores[] = indicador_db::pegar_valores( $cd_indicador, $v[0],$v[1], $v[2],$v[3] );
            }

	        $data['graph'] = accumulate_barchart($legenda,$valores,750,300);

	        $this->load->view('indicador/tabela/jpgraph_grafico.php', $data);
		}
		elseif( $row['cd_indicador_grafico_tipo']==$INDICADOR_GRAFICO_LINHA )
		{
			$t = explode( ',', $row['ds_range_tick'] );
			$al = explode( ',', $row['ds_range_legenda'] );
			$av = explode( ';', $row['ds_range_valor'] );
			
			$legenda=array();
			$tick=array();
			$valores=array();

	        $tick = indicador_db::pegar_rotulos( $cd_indicador, $t[0],$t[1], $t[2],$t[3] );
	        
	        foreach($al as $item)
            {
            	$l = explode( ',', $item );
            	$legendas[] = indicador_db::pegar_rotulos( $cd_indicador, $l[0],$l[1], $l[2],$l[3] );
            	
            }
        	foreach( $legendas as $legs )
        	{
        		foreach( $legs as $leg )
        		$legenda[] = $leg;
        	}

            foreach($av as $item)
            {
            	$v = explode( ',', $item );
            	$valores[] = indicador_db::pegar_valores( $cd_indicador, $v[0],$v[1], $v[2],$v[3] );
            }

	        $data['graph'] = linechart( $tick, $valores, $legenda, 750, 600 );

	        $this->load->view('indicador/tabela/jpgraph_grafico.php', $data);
		}
	}

	function carregar_grafico_config_ajax()
	{
		if(CheckLogin())
		{
			$cd_indicador_tabela=$this->input->post('cd');
			$this->load->model('projetos/Indicador_model','dbm');
			$row=$this->dbm->carregar_grafico( intval($cd_indicador_tabela) );
			$row['erro']='';
		}
		else
		{
			$row['erro']='Usuario nao esta configurado como responsavel por Indicadores';
		}
		
		echo json_encode(  $row  );
	}

	function salvar()
	{
		if(CheckLogin() && usuario_responsavel_indicador(usuario_id()))
		{
			$cd_indicador_tabela=$this->input->post( 'cd_indicador_tabela' );
			$cd_indicador_grafico_tipo=$this->input->post( 'cd_indicador_grafico_tipo' );
			$ds_range_valor=$this->input->post( 'ds_range_valor' );
			$ds_range_legenda=$this->input->post( 'ds_range_legenda' );
			$ds_range_tick=$this->input->post( 'ds_range_tick' );

			$q=$this->db->query("
			SELECT it.cd_indicador_tabela 
			FROM indicador.indicador_tabela_grafico grafico 
			JOIN indicador.indicador_tabela it ON grafico.cd_indicador_tabela=it.cd_indicador_tabela 
			WHERE grafico.dt_exclusao IS NULL 
			AND it.cd_indicador_tabela=? 
			",array(intval($cd_indicador_tabela)));
			$r=$q->row_array();

			if( $r && isset($r['cd_indicador_tabela']) && intval($r['cd_indicador_tabela'])>0 )
			{
				//echo 'update';exit;
				// update
				$sql="
				UPDATE 
					indicador.indicador_tabela_grafico 
				SET
					cd_indicador_grafico_tipo={cd_indicador_grafico_tipo}
					,ds_range_valor='{ds_range_valor}'
					,ds_range_legenda='{ds_range_legenda}'
					,ds_range_tick='{ds_range_tick}'
				WHERE 
					cd_indicador_tabela={cd_indicador_tabela}
				;";

				esc('{cd_indicador_grafico_tipo}',$cd_indicador_grafico_tipo,$sql,'int');
				esc('{ds_range_valor}',$ds_range_valor,$sql,'str');
				esc('{ds_range_legenda}',$ds_range_legenda,$sql,'str');
				esc('{ds_range_tick}',$ds_range_tick,$sql,'str');
				esc('{cd_indicador_tabela}',$cd_indicador_tabela,$sql,'int');
			}
			else
			{
				//echo 'insert';exit;
				// insert
				$sql="
				INSERT INTO indicador.indicador_tabela_grafico 
				(
					cd_indicador_tabela
					,cd_indicador_grafico_tipo
					,ds_range_valor
					,ds_range_legenda
					,ds_range_tick
					,dt_inclusao
					,cd_usuario_inclusao
				)
				VALUES
				(
					{cd_indicador_tabela}
					,{cd_indicador_grafico_tipo}
					,'{ds_range_valor}'
					,'{ds_range_legenda}'
					,'{ds_range_tick}'
					,current_timestamp
					,{cd_usuario_inclusao} 
				)					
				;";

				esc('{cd_indicador_grafico_tipo}',$cd_indicador_grafico_tipo,$sql,'int');
				esc('{ds_range_valor}',$ds_range_valor,$sql,'str');
				esc('{ds_range_legenda}',$ds_range_legenda,$sql,'str');
				esc('{ds_range_tick}',$ds_range_tick,$sql,'str');
				esc('{cd_usuario_inclusao}',usuario_id(),$sql,'int');
				esc('{cd_indicador_tabela}',$cd_indicador_tabela,$sql,'int');
			}

			$q=$this->db->query($sql);

			echo 'true';
		}
	}	
	
	function grafico_ajax($cd = 0)
	{
		if(CheckLogin())
		{
			$cd_indicador_tabela = (intval($this->input->post('cd')) > 0 ? intval($this->input->post('cd')) : intval($cd));
			$nr_largura = intval($this->input->post('nr_largura'));
			$nr_altura  = intval($this->input->post('nr_altura'));
			
			if(intval($cd_indicador_tabela)!= 0)
			{
				
				
				$ob_resul = new indicador_tools;
				
				if($nr_largura > 0)
				{				
					$ob_resul->nr_largura = $nr_largura;
				}
				if($nr_altura > 0)
				{				
					$ob_resul->nr_altura = $nr_altura;
				}				
				
				$ret = $ob_resul->gerar_grafico(intval($cd_indicador_tabela));
				if(trim($ret) != "")
				{
					$ret = base_url().trim($ret);
				}
				echo $ret;				
			}
			else
			{
				echo 'Indicador deve ser informado.';
			}
		}
	}
	
	function gerar_grafico_analise($cd_indicador_tabela = 0)
	{
		CheckLogin();
		
		$cd_indicador_tabela = (intval($this->input->post('cd_indicador_tabela')) > 0 ? intval($this->input->post('cd_indicador_tabela')) : intval($cd_indicador_tabela));
		
		$qr_sql = "
					SELECT CASE WHEN i.tp_analise = '+' THEN 'mais'
								WHEN i.tp_analise = '-' THEN 'menos'
								ELSE ''
						   END AS tp_analise
					  FROM indicador.indicador i
					  JOIN indicador.indicador_tabela it
						ON it.cd_indicador = i.cd_indicador
					 WHERE it.cd_indicador_tabela = ".intval($cd_indicador_tabela)."		
		          ";
		$ar_reg = $this->db->query($qr_sql)->row_array();		
		
		if(count($ar_reg) > 0)
		{
			if(trim($ar_reg['tp_analise']) != "")
			{
				echo '<img src="'.base_url().'/img/indicador_melhor_'.$ar_reg['tp_analise'].'.png" id="img_grafico_analise" border="0">';
			}
		}
	}
	
	function tabela($cd = 0)
	{
		if(CheckLogin())
		{
			
			$cd_indicador_tabela = (intval($this->input->post('cd_indicador_tabela')) > 0 ? intval($this->input->post('cd_indicador_tabela')) : intval($cd));
			
			if(intval($cd_indicador_tabela) > 0)
			{
				echo indicador_tools::gerar_tabela(intval($cd_indicador_tabela), TRUE, TRUE);
			}
			else
			{
				echo "Informe o código da tabela";
			}
		}
	}	

	function geraPPT($cd_indicador_tabela = 0)
	{
		CheckLogin();
		if(intval($cd_indicador_tabela) > 0)
		{		
			$this->load->model('indicador/Indicador_tabela_model');
			$args        = Array();
			$ar_info     = Array();
			$ar_tabela   = Array();
			$ob_result   = null;		
			$img_grafico = null;
			
			#### INFORMAÇÕES DO INDICADOR ####
			$args["cd_indicador_tabela"] = intval($cd_indicador_tabela);
			$this->Indicador_tabela_model->info_indicador_tabela($ob_result, $args);
			$ar_info = $ob_result->row_array();			
			
			#### GERA GRAFICO E TABELA ####
			$ob_resul = new indicador_tools;
			$ob_resul->nr_largura = 800;
			$img_grafico = $ob_resul->gerar_grafico($args["cd_indicador_tabela"]);
			$ar_tabela   = $ob_resul->gerar_tabela($args["cd_indicador_tabela"], TRUE, TRUE, TRUE);
			
			#echo "<PRE>";
			#echo $img_grafico;
			#echo br(2);
			#print_r($ar_tabela);
			#echo br(2);
			#print_r($ar_info);
			#echo "</PRE>";
						
			#### PPT ####
			$this->load->plugin('phppowerpoint');
			$ob_ppt = new PHPPowerPoint();
			$ob_slide = $ob_ppt->getActiveSlide();
			
			#### TITULO DO INDICADOR ###
			$ob_shape = $ob_slide->createRichTextShape()->setHeight(80)
			                                            ->setWidth(940)
														->setOffsetX(10)
														->setOffsetY(30);
			$ob_shape->getActiveParagraph()->getAlignment()->setHorizontal(PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER);
			$ob_text = $ob_shape->createTextRun(utf8_encode($ar_info['ds_indicador']));
			$ob_text->getFont()->setBold(TRUE)
							   ->setSize(26);		
			
			#### GRAFICO ####
			$ob_shape = $ob_slide->createDrawingShape();
			$ob_shape->setName('grafico')->setDescription('Grafico')
										 ->setPath('./'.$img_grafico)
										 ->setWidth(800)
										 ->setOffsetX(80)
				                         ->setOffsetY(120);			
										 
			#### NOVO SLIDE ####					 
			$ob_slide = $ob_ppt->createSlide();
			
			#### TITULO DO INDICADOR ###
			$ob_shape = $ob_slide->createRichTextShape()->setHeight(50)
			                                            ->setWidth(940)
														->setOffsetX(10)
														->setOffsetY(15);
			$ob_shape->getActiveParagraph()->getAlignment()->setHorizontal(PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER);
			$ob_text = $ob_shape->createTextRun(utf8_encode($ar_info['ds_indicador']));
			$ob_text->getFont()->setBold(TRUE)
							   ->setSize(18);				
			
			#### TABELA ####
			$ob_shape = $ob_slide->createTableShape(count($ar_tabela[0]));
			$ob_shape->setWidth(940)
			         ->setOffsetX(10)
			         ->setOffsetY(80);
			
			$nr_conta = 0;
			foreach($ar_tabela as $ar_item)
			{
				$ob_row = $ob_shape->createRow();
				$ob_row->setHeight(15);
				
				
				foreach($ar_item as $item)
				{
					$ob_cell = $ob_row->nextCell();
					$ob_cell->getActiveParagraph()->getAlignment()->setHorizontal(PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER);
					$ob_cell->getBorders()->GetTop()->setLineWidth(0.5);
					$ob_cell->getBorders()->GetBottom()->setLineWidth(0.5);                 
					$ob_cell->getBorders()->GetLeft()->setLineWidth(0.5);
					$ob_cell->getBorders()->GetRight()->setLineWidth(0.5);
					
					$item = strip_tags($item);
					if($nr_conta == 0)
					{
						$ob_cell->createTextRun(utf8_encode($item))->getFont()->setBold(TRUE)
						                                                      ->setSize(14);
					}
					else
					{
						$ob_cell->createTextRun(utf8_encode($item))->getFont()->setSize(12);
					}
				}
				$nr_conta++;
			}							 
			
			#### GERA ARQUIVO PPT ###
			$dir_img = $_SERVER['DOCUMENT_ROOT']."/cieprev/charts/";
			$ds_ppt = random_string().'.pptx';
			$objWriter = PHPPowerPoint_IOFactory::createWriter($ob_ppt, 'PowerPoint2007');
			$objWriter->save($dir_img.$ds_ppt);
			#echo '<a href="'.base_url()."charts/".$ds_ppt.'">Download</a>';
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($dir_img.$ds_ppt));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($dir_img.$ds_ppt));
			ob_clean();
			flush();
			readfile($dir_img.$ds_ppt);
			@unlink($dir_img.$ds_ppt);
			exit;			
			
			
			/*
			#### GERA ARQUIVO PPT ###
			$ds_ppt = random_string().'.pptx';
			header('Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation');
			header("Cache-Control: public, must-revalidate");
			header("Pragma: hack");
			header('Content-Disposition: inline; filename="'.$ds_ppt.'"');
			header("Content-Transfer-Encoding: binary");	
			$objWriter = PHPPowerPoint_IOFactory::createWriter($ob_ppt, 'PowerPoint2007');
			$objWriter->save('php://output');	
			exit;
			*/
			
		}
		else
		{
			exibir_mensagem("INFORME O CÓDIGO DA TABELA");
		}	
	}

	function geraEXCEL($cd_indicador_tabela = 0, $debug = 0)
	{
		CheckLogin();
		if(intval($cd_indicador_tabela) > 0)
		{		
			$this->load->model('indicador/Indicador_tabela_model');
			$args        = Array();
			$ar_info     = Array();
			$ar_tabela   = Array();
			$ob_result   = null;		
			$img_grafico = null;
			$nr_col_ini  = 1;
			$nr_row_ini  = 30;
			
			#### INFORMAÇÕES DO INDICADOR ####
			$args["cd_indicador_tabela"] = intval($cd_indicador_tabela);
			$this->Indicador_tabela_model->info_indicador_tabela($ob_result, $args);
			$ar_info = $ob_result->row_array();			
			
			#### GERA GRAFICO E TABELA ####
			$ob_resul = new indicador_tools;
			$ob_resul->nr_largura = 800;
			$img_grafico = $ob_resul->gerar_grafico($args["cd_indicador_tabela"]);
			$ar_tabela   = $ob_resul->gerar_tabela($args["cd_indicador_tabela"], TRUE, TRUE, TRUE);
			
			if(intval($debug) > 0)
			{
				echo "<PRE>";
				echo $img_grafico;
				echo br(2);
				print_r($ar_tabela);
				echo br(2);
				print_r($ar_info);
				echo "</PRE>";
				exit;
			}
						
			#### EXCEL ####
			$this->load->plugin('phpexcel');
			
			#### Create new PHPExcel object ####
			$objPHPExcel = new PHPExcel();

			#### CRIA PLANILHA ####
			$objPHPExcel->setActiveSheetIndex(0);	
			$objPHPExcel->getActiveSheet()->setTitle('Indicador');

			#### TITULO DO INDICADOR ###
			$objPHPExcel->getActiveSheet()->mergeCells('B1:H1');
			$objPHPExcel->getActiveSheet()->setCellValue('B1', utf8_encode($ar_info['ds_indicador']));
			$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(20);
			$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);		
						
			#### GRAFICO INDICADOR ####
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Terms and conditions');
			$objDrawing->setDescription('Terms and conditions');
			$objDrawing->setPath('./'.$img_grafico);
			$objDrawing->setCoordinates('B3');
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			
			#### TABELA DO INDICADOR ####
			$nr_row = $nr_row_ini;
			foreach($ar_tabela as $ar_item)
			{
				$nr_col = $nr_col_ini;
				$qt_col = count($ar_item);
				foreach($ar_item as $item)
				{
					if(!(($ar_info['cd_tipo'] == "P") and ($qt_col == $nr_col)))
					{
						$item = str_replace("<b>","",$item);
						$item = str_replace("</b>","",$item);
						
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col, $nr_row, utf8_encode($item));
					}
					$nr_col++;
				}
				$nr_row++;
			}
			
			if($ar_info['cd_tipo'] == "P")
			{
				$nr_row--;
				$nr_col--;
			}
			
			#### FORMATA CABEÇALHO DA TABELA ####
			$I = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_ini, $nr_row_ini)->getCoordinate();
			$F = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col-1, $nr_row_ini)->getCoordinate();
			$objPHPExcel->getActiveSheet()->getStyle($I.':'.$F)->applyFromArray(
					array(
							'font' => array(
								'bold' => true
							),
							'alignment' => array(
								'wrap' => true, 
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
							),
							'borders' => array(
								'top'     => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'bottom'     => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'left'     => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'right'     => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)								
							),							
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
								'rotation' => 90,
								'startcolor' => array(
									'argb' => 'FFA0A0A0'
								),
								'endcolor' => array(
									'argb' => 'FFFFFFFF'
								)
							)
					)
			);
			
			#### FORMATA TABELA ####
			$I = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_ini, $nr_row_ini + 1)->getCoordinate();
			$F = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col-1, $nr_row)->getCoordinate();			
			$objPHPExcel->getActiveSheet()->getStyle($I.':'.$F)->applyFromArray(
					array(
							'alignment' => array(
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							),
							'borders' => array(
								'top'     => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'bottom'     => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'left'     => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'right'     => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)								
							),							
					)
			);			
			
			#### AUTO REDIMENSIONA CELULAS ####
			$nr_conta = $nr_col_ini;
			while($nr_conta <= ($nr_col - 1))
			{			
				$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($nr_conta)->setAutosize(true);		
				$nr_conta++;
			}
			
			#### GERA EXCEL ####
			$ds_xls = random_string().'.xlsx';
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="'.$ds_xls.'"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');	
			exit;
			
		}
		else
		{
			exibir_mensagem("INFORME O CÓDIGO DA TABELA");
		}	
	}	
	
}
