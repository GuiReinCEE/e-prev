<?php
class prevenda extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
		CheckLogin();
		if(gerencia_in(array('GCM')))
		{			
			$this->load->model("projetos/Pre_venda_model");
			$result = null;
			$data = Array();
			$args = Array();
			
			$this->Pre_venda_model->local($result);
			$data['ar_local'] = $result->result_array();
			
			$this->Pre_venda_model->usuario_contato($result);
			$data['ar_usuario_contato'] = $result->result_array();			
			
			$this->load->view('ecrm/prevenda/index.php', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
	}
	
	function excel()
	{
		CheckLogin();
		if(gerencia_in(array('GCM')))
		{			
			$this->load->model("projetos/Pre_venda_model");

			$result = null;
			$data = Array();
			$args = Array();
			
			$args['cd_empresa']            = $this->input->post("cd_empresa");
			$args['cd_registro_empregado'] = $this->input->post("cd_registro_empregado");
			$args['seq_dependencia']       = $this->input->post("seq_dependencia");		
			$args['nome']                  = $this->input->post("nome");
			$args['dt_contato_ini']        = $this->input->post("dt_contato_ini");
			$args['dt_contato_fim']        = $this->input->post("dt_contato_fim");		
			$args['dt_envio_ini']          = $this->input->post("dt_envio_ini");
			$args['dt_envio_fim']          = $this->input->post("dt_envio_fim");
			$args['dt_opcao_ini']          = $this->input->post("dt_opcao_ini");
			$args['dt_opcao_fim']          = $this->input->post("dt_opcao_fim");
			$args['dt_ingresso_ini']       = $this->input->post("dt_ingresso_ini");
			$args['dt_ingresso_fim']       = $this->input->post("dt_ingresso_fim");
			$args['cd_pre_venda_local']    = $this->input->post("cd_pre_venda_local");
			$args['fl_inscricao']          = $this->input->post("fl_inscricao");
			$args['cd_usuario_contato']    = $this->input->post("cd_usuario_contato");
			
			$this->Pre_venda_model->listar($result,$args);
			$collection = $result;
			
			#### EXCEL ####
			$this->load->plugin('phpexcel');
			$nr_col_ini  = 0;
			$nr_col_fim  = 0;
			$nr_row_ini  = 5;	
			
			#### Create new PHPExcel object ####
			$objPHPExcel = new PHPExcel();

			#### CRIA PLANILHA ####
			$objPHPExcel->setActiveSheetIndex(0);	
			$objPHPExcel->getActiveSheet()->setTitle('Prevenda');
			
			#### LOGO ####
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Terms and conditions');
			$objDrawing->setDescription('Terms and conditions');
			list($width, $height) = getimagesize('./img/logofundacao_carta.jpg'); 
			$objDrawing->setPath('./img/logofundacao_carta.jpg');
			$objDrawing->setCoordinates("A1");
			$objDrawing->setHeight(38);
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());	
			
			#### TITULO ###
			$objPHPExcel->getActiveSheet()->mergeCells('D1:H1');
			$objPHPExcel->getActiveSheet()->setCellValue('D1', utf8_encode("Pré-Venda"));
			$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setSize(16);
			$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);	
			
			$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');
			$objPHPExcel->getActiveSheet()->setCellValue('A3', utf8_encode(date('d/m/Y H:i:s')));
			$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);

			#### CABEÇALHO ####
			$nr_row = $nr_row_ini;
			$nr_col = $nr_col_ini;			
	
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col, $nr_row, utf8_encode("Cód."));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 1, $nr_row, utf8_encode("RE"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 2, $nr_row, utf8_encode("Nome"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 3, $nr_row, utf8_encode("1º Contato"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 4, $nr_row, utf8_encode("Contatos"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 5, $nr_row, utf8_encode("Próx. Agenda"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 6, $nr_row, utf8_encode("Dt Opção"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 7, $nr_row, utf8_encode("Dt Ingresso"));
			
			$nr_row = $nr_row_ini + 1;
			
			$nr_col_fim+=7;
	
            foreach($collection as $item)
            {
				$contato = "";
				foreach($item['contatos'] as $subitem)
				{
					$contato.= $subitem['dt_pre_venda_contato']." (".$subitem['ds_usuario_inclusao'].") - ".($subitem['dt_envio_inscricao'] != "" ? $subitem['dt_envio_inscricao']." (Inscrição Preenchida)" : $subitem['ds_pre_venda_motivo']);
					$contato.= "\n";
				}	
			
				$nr_col = $nr_col_ini;
				
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col, $nr_row, utf8_encode($item['cd_pre_venda']));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 1, $nr_row, utf8_encode($item['cd_empresa']."/".$item['cd_registro_empregado']."/".$item['seq_dependencia']));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 2, $nr_row, utf8_encode($item['nome']));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 3, $nr_row, utf8_encode($item['dt_primeiro_contato']));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 4, $nr_row, utf8_encode($contato));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 5, $nr_row, utf8_encode($item['dt_proximo_agendamento']));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 6, $nr_row, utf8_encode($item['dt_opcao_plano']));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 7, $nr_row, utf8_encode($item['dt_ingresso_plano']));

				$nr_row++;	
			}
			
			#### FORMATA CABEÇALHO DA TABELA ####
			$I = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_ini, $nr_row_ini)->getCoordinate();
			$F = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_fim, $nr_row_ini)->getCoordinate();
			$sharedStyle = new PHPExcel_Style();
			$sharedStyle->applyFromArray(
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
					));			
			
			$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, $I.':'.$F);		

			#### FORMATA TABELA ####
			$I = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_ini, $nr_row_ini + 1)->getCoordinate();
			$F = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_fim, $nr_row)->getCoordinate();			
	
			$sharedStyle = new PHPExcel_Style();
			$sharedStyle->applyFromArray(
				array('borders' => array(
											'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
											'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
											'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
										),
						'alignment' => array(
										'wrap' => true, 
										'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
									)										
					 ));			
			
			$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, $I.':'.$F);

			
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
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}	
	}

	function listar()
	{
		CheckLogin();
		if(gerencia_in(array('GCM')))
		{			
			$this->load->model("projetos/Pre_venda_model");

			$result = null;
			$data = Array();
			$args = Array();
			
			$args['cd_empresa']            = $this->input->post("cd_empresa");
			$args['cd_registro_empregado'] = $this->input->post("cd_registro_empregado");
			$args['seq_dependencia']       = $this->input->post("seq_dependencia");		
			$args['nome']                  = $this->input->post("nome");
			$args['dt_contato_ini']        = $this->input->post("dt_contato_ini");
			$args['dt_contato_fim']        = $this->input->post("dt_contato_fim");		
			$args['dt_envio_ini']          = $this->input->post("dt_envio_ini");
			$args['dt_envio_fim']          = $this->input->post("dt_envio_fim");
			$args['dt_opcao_ini']          = $this->input->post("dt_opcao_ini");
			$args['dt_opcao_fim']          = $this->input->post("dt_opcao_fim");
			$args['dt_ingresso_ini']       = $this->input->post("dt_ingresso_ini");
			$args['dt_ingresso_fim']       = $this->input->post("dt_ingresso_fim");
			$args['cd_pre_venda_local']    = $this->input->post("cd_pre_venda_local");
			$args['fl_inscricao']          = $this->input->post("fl_inscricao");
			$args['cd_usuario_contato']    = $this->input->post("cd_usuario_contato");
			
			manter_filtros($args);
			
			$this->Pre_venda_model->listar($result,$args);
			$data['collection'] = $result;
			$this->load->view('ecrm/prevenda/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
	}

	function excluir($codigo)
	{
		CheckLogin();
		if(gerencia_in(array('GCM')))
		{		
			if(trim($codigo)=="")
			{
				echo "código não informado"; return false; exit;
			}

			$this->db->query( "
				UPDATE projetos.pre_venda 
				SET dt_exclusao=current_timestamp, cd_usuario_exclusao=? 
				WHERE md5(cd_pre_venda::varchar)=?
			", array(usuario_id(), $codigo) );

			redirect( 'ecrm/prevenda', 'refresh' );
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
	}

	function abrir($id=0)
	{
		CheckLogin();
		if(gerencia_in(array('GCM')))
		{
			$this->load->model( 'projetos/Pre_venda_model' );
			$data['record'] = $this->Pre_venda_model->abrir( intval($id) );
			$this->load->view( 'ecrm/prevenda/detalhe', $data );
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}

	function salvar()
	{
		CheckLogin();
		if(gerencia_in(array('GCM')))
		{		
			$name='cd_pre_venda'; $dados[$name] = $this->input->post($name);
			$name='cd_empresa'; $dados[$name] = $this->input->post($name);
			$name='cd_registro_empregado'; $dados[$name] = $this->input->post($name);
			$name='seq_dependencia'; $dados[$name] = $this->input->post($name);
			
			$name='nome'; $dados[$name] = $this->input->post($name);
			$name='cpf'; $dados[$name] = $this->input->post($name);
			$name='cd_pre_venda_local'; $dados[$name] = $this->input->post($name);
			$name='observacao'; $dados[$name] = $this->input->post($name);
			$name='cd_usuario'; $dados[$name] = usuario_id();

			$sql = "
						SELECT cd_pre_venda
				          FROM projetos.pre_venda
				         WHERE cd_empresa            = ".intval($dados['cd_empresa'])."
				           AND cd_registro_empregado = ".intval($dados['cd_registro_empregado'])."
				           AND seq_dependencia       = ".intval($dados['seq_dependencia'])."
				           AND dt_exclusao           IS NULL
						   AND cd_registro_empregado > 0
			       ";
			$query = $this->db->query($sql);
			$row   = $query->row_array();
			$cd_pre_venda_existe = (count($row) > 0 ? intval($row['cd_pre_venda']) : 0);
			
			if(intval($cd_pre_venda_existe) == 0)
			{
				$sql = "
							SELECT cd_pre_venda
							  FROM projetos.pre_venda
							 WHERE cd_empresa                        = ".intval($dados['cd_empresa'])."
							   AND COALESCE(cd_registro_empregado,0) = 0
							   AND seq_dependencia                   = ".intval($dados['seq_dependencia'])."
							   AND cpf                               = '".trim($dados['cpf'])."'
							   AND dt_exclusao                       IS NULL
							   AND cpf                               <> '000.000.000-00'
					   ";
				$query = $this->db->query($sql);
				$row   = $query->row_array();
				$cd_pre_venda_existe = (count($row) > 0 ? intval($row['cd_pre_venda']) : 0);				
			}
			
			
			if((intval($dados['cd_pre_venda']) == 0) and (intval($cd_pre_venda_existe) > 0))
			{
				#echo 1;
				redirect('ecrm/prevenda/abrir/'.intval($cd_pre_venda_existe), 'refresh');
				exit;
			}
			else
			{
				$this->load->model("projetos/Pre_venda_model");
				
				$cd_pre_venda_new = $this->Pre_venda_model->salvar($result, $dados);
				redirect("ecrm/prevenda/abrir/".$cd_pre_venda_new, "refresh");	
				exit;
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}

	function contato($cd_pre_venda=0, $cd_pre_venda_contato=0)
	{
		CheckLogin();
		if(gerencia_in(array('GCM')))
		{	
			$result = null;
			$data = Array();
			$args = Array();
			
			$this->load->model( 'projetos/Pre_venda_model' );
			
			$this->Pre_venda_model->evento($result);
			$data['arr_evento'] = $result->result_array();	
		
			if( intval($cd_pre_venda)==0 && intval($cd_pre_venda_contato)==0 )
			{
				echo "Informe cd_pre_venda ou cd_pre_venda_contato!";
				return false;
				exit;
			}

			$data['ar_participante'] = $this->Pre_venda_model->abrir( intval($cd_pre_venda) );
			
			if( intval($cd_pre_venda_contato)>0 )
			{
				$q = $this->db->query("
					SELECT pvc.cd_pre_venda_contato, 
						   pvc.cd_pre_venda, 
						   TO_CHAR(pvc.dt_pre_venda_contato, 'DD/MM/YYYY') AS dt_pre_venda_contato_data, 
						   TO_CHAR(pvc.dt_pre_venda_contato, 'HH24:MI') AS dt_pre_venda_contato_hora, 
						   TO_CHAR(pvc.dt_envio_inscricao, 'DD/MM/YYYY') AS dt_envio_inscricao,
						   pvc.cd_pre_venda_motivo,					   
						   pvc.observacao,
						   pvc.cd_pre_venda_local,
						   pvc.cd_evento_institucional
					  FROM projetos.pre_venda_contato pvc
					 WHERE pvc.dt_exclusao IS NULL
					   AND pvc.cd_pre_venda_contato = ?", array(intval($cd_pre_venda_contato)));
				$record = $q->row_array();
				
				$data['record'] = $record;
				$cd_pre_venda = $record['cd_pre_venda'];
			}
			else
			{
				$data['record'] = array( 
					'cd_pre_venda_contato'=>$cd_pre_venda_contato
					, 'cd_pre_venda'=>$cd_pre_venda
					, 'dt_pre_venda_contato_data'=>''
					, 'dt_pre_venda_contato_hora'=>''
					, 'dt_envio_inscricao'=>''
					, 'cd_pre_venda_motivo'=>''
					, 'cd_pre_venda_local'=>''
					, 'observacao'=>''
					, 'cd_evento_institucional'=>''
				);
			}

			$q = $this->db->query( "
						SELECT pvc.cd_pre_venda_contato, 
							   TO_CHAR(pvc.dt_pre_venda_contato, 'DD/MM/YYYY HH24:MI' ) AS dt_pre_venda_contato, 
							   TO_CHAR(pvc.dt_envio_inscricao, 'DD/MM/YYYY') AS dt_envio_inscricao,
							   pvc.cd_pre_venda_motivo,
							   pvc.observacao,
							   pvm.ds_pre_venda_motivo,
							   pvl.ds_pre_venda_local,
							   uc.guerra AS ds_usuario_contato,
							   '['||TO_CHAR(ei.dt_inicio,'DD/MM/YYYY')||'] - ' || ei.nome AS ds_evento
						  FROM projetos.pre_venda_contato pvc
					      JOIN projetos.usuarios_controledi uc
						    ON uc.codigo = pvc.cd_usuario_inclusao						  
						  LEFT JOIN projetos.pre_venda_motivo pvm 
							ON pvm.cd_pre_venda_motivo = pvc.cd_pre_venda_motivo
						  LEFT JOIN projetos.pre_venda_local pvl
							ON pvl.cd_pre_venda_local = pvc.cd_pre_venda_local
						  LEFT JOIN projetos.eventos_institucionais ei
						    ON ei.cd_evento = pvc.cd_evento_institucional
						 WHERE pvc.cd_pre_venda = ? 
						   AND pvc.dt_exclusao IS NULL", array( intval($cd_pre_venda) ) );

			$data['collection'] = $q->result_array();

			$data['collection_protocolo_interno'] = array();

			if(trim($data['ar_participante']['cd_empresa']) != '' AND trim($data['ar_participante']['cd_registro_empregado']) != '' AND trim($data['ar_participante']['seq_dependencia']) != '')
			{
				$data['collection_protocolo_interno'] = $this->Pre_venda_model->get_protocolo_interno_inscricao(
					intval($data['ar_participante']['cd_empresa']),
					intval($data['ar_participante']['cd_registro_empregado']),
					intval($data['ar_participante']['seq_dependencia'])
				);
			}

			$this->load->view( "ecrm/prevenda/contato", $data );
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}				
	}

	function agenda($cd_pre_venda=0,$cd_pre_venda_agenda=0)
	{
		CheckLogin();
		$result = null;
		$data   = Array();
		$args   = Array();
			
		if(gerencia_in(array('GCM')))
		{
			$this->load->model("projetos/Pre_venda_model");
			$this->load->model("projetos/Pre_venda_agenda_model");
			
			
			$this->Pre_venda_agenda_model->combo_agenda_tipo($result, $args);
			$data['ar_agenda_tipo'] = $result->result_array();				
			
			
			if( intval($cd_pre_venda)==0 && intval($cd_pre_venda_agenda)==0 )
			{
				echo "Informe cd_pre_venda ou cd_pre_venda_agenda!";
				return false;
				exit;
			}

			$data['ar_participante'] = $this->Pre_venda_model->abrir( intval($cd_pre_venda) );
			
			if( intval($cd_pre_venda_agenda)>0 )
			{
				$q = $this->db->query("
				SELECT cd_pre_venda_agenda, cd_pre_venda_agenda_tipo, cd_pre_venda, to_char(dt_pre_venda_agenda, 'DD/MM/YYYY') as dt_pre_venda_agenda_data, to_char(dt_pre_venda_agenda, 'HH24:MI') as dt_pre_venda_agenda_hora, observacao
				FROM projetos.pre_venda_agenda 
				WHERE cd_pre_venda_agenda=?", array(intval($cd_pre_venda_agenda)));
				$record = $q->row_array();
				
				$data['record'] = $record;
				$cd_pre_venda = $record['cd_pre_venda'];
			}
			else
			{
				$data['record'] = array( 
					'cd_pre_venda_agenda'=>$cd_pre_venda_agenda
					, 'cd_pre_venda'=>$cd_pre_venda
					, 'cd_pre_venda_agenda_tipo'=>''
					, 'dt_pre_venda_agenda_data'=>''
					, 'dt_pre_venda_agenda_hora'=>''
					, 'observacao'=>''
					
				);
			}

			$q = $this->db->query("
									SELECT pva.cd_pre_venda_agenda, 
										   pva.cd_pre_venda_agenda_tipo, 
										   pvat.ds_pre_venda_agenda_tipo, 
										   TO_CHAR(pva.dt_pre_venda_agenda, 'DD/MM/YYYY HH24:MI') AS dt_pre_venda_agenda, 
										   to_char(pva.dt_pre_venda_agenda, 'YYYY/MM/DD') as dt_pre_venda_agenda_padrao, 
										   pva.observacao, 
										   pva.dt_pre_venda_agenda_enviado
									  FROM projetos.pre_venda_agenda pva
									  LEFT JOIN projetos.pre_venda_agenda_tipo pvat
									    ON pvat.cd_pre_venda_agenda_tipo = pva.cd_pre_venda_agenda_tipo
									 WHERE pva.cd_pre_venda = ".intval($cd_pre_venda)." 
									   AND pva.dt_exclusao IS NULL" 
								  );

			$data['collection'] = $q->result_array();

			$this->load->view( "ecrm/prevenda/agenda", $data );
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}

	function contato_salvar()
	{
		CheckLogin();

		if(gerencia_in(array('GCM')))
		{		
			$this->load->model("projetos/Pre_venda_contato_model");

			$name='cd_pre_venda_contato'; $dados[$name] = $this->input->post($name);
			$name='dt_pre_venda_contato_data'; $dados[$name] = $this->input->post($name);
			$name='dt_pre_venda_contato_hora'; $dados[$name] = $this->input->post($name);
			$name='observacao'; $dados[$name] = $this->input->post($name);
			$name='cd_pre_venda'; $dados[$name] = $this->input->post($name);
			
			$name='dt_envio_inscricao'; $dados[$name] = $this->input->post($name);
			$name='cd_pre_venda_motivo'; $dados[$name] = $this->input->post($name);
			$name='cd_pre_venda_local'; $dados[$name] = $this->input->post($name);
			$name='cd_evento_institucional'; $dados[$name] = $this->input->post($name);
			
			$name='cd_usuario_inclusao'; $dados[$name] = usuario_id();

			$saved = $this->Pre_venda_contato_model->salvar( $dados, $erros );

			if($saved)
			{
				redirect( 'ecrm/prevenda/contato/' . $dados['cd_pre_venda'], 'refresh' );
			}
			else
			{
				echo '<pre>';
				var_dump($erros);
				echo '</pre>';
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}

	function agenda_salvar()
	{
		CheckLogin();
		if(gerencia_in(array('GCM')))
		{
			$this->load->model("projetos/Pre_venda_agenda_model");

			$result = null;
			$data   = Array();
			$args   = Array();
			
			$args["cd_pre_venda"]             = $this->input->post("cd_pre_venda", TRUE);
			$args["cd_pre_venda_agenda"]      = $this->input->post("cd_pre_venda_agenda", TRUE);
			$args["dt_pre_venda_agenda_data"] = $this->input->post("dt_pre_venda_agenda_data", TRUE);
			$args["dt_pre_venda_agenda_hora"] = $this->input->post("dt_pre_venda_agenda_hora", TRUE);
			$args["observacao"]               = $this->input->post("observacao", TRUE);			
			$args["cd_pre_venda_agenda_tipo"] = $this->input->post("cd_pre_venda_agenda_tipo", TRUE);			
			$args["cd_usuario_inclusao"]      = $this->session->userdata('codigo');			
			
			$this->Pre_venda_agenda_model->salvar($result, $args);

			redirect('ecrm/prevenda/agenda/'.$args['cd_pre_venda'], 'refresh');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}

	function contato_excluir($codigo)
	{
		CheckLogin();
		if(gerencia_in(array('GCM')))
		{
			if(trim($codigo)=="")
			{
				echo "código não informado"; return false; exit;
			}
			
			$q = $this->db->query("SELECT cd_pre_venda FROM projetos.pre_venda_contato WHERE md5(cd_pre_venda_contato::varchar)=?
			", array($codigo) );
			$r = $q->row_array();

			$this->db->query( "
				UPDATE projetos.pre_venda_contato
				SET dt_exclusao=current_timestamp, cd_usuario_exclusao=? 
				WHERE md5(cd_pre_venda_contato::varchar)=?
			", array(usuario_id(), $codigo) );

			redirect( 'ecrm/prevenda/contato/'.$r['cd_pre_venda'], 'refresh' );
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
	}

	function agenda_excluir($codigo)
	{
		CheckLogin();
		if(gerencia_in(array('GCM')))
		{
			if(trim($codigo)=="")
			{
				echo "código não informado"; return false; exit;
			}

			$q = $this->db->query("SELECT cd_pre_venda FROM projetos.pre_venda_agenda WHERE md5(cd_pre_venda_agenda::varchar)=?", array($codigo) );
			$r = $q->row_array();

			$this->db->query( "
				UPDATE projetos.pre_venda_agenda
				SET dt_exclusao=current_timestamp, cd_usuario_exclusao=? 
				WHERE md5(cd_pre_venda_agenda::varchar)=?
			", array(usuario_id(), $codigo) );

			redirect( 'ecrm/prevenda/agenda/'.$r['cd_pre_venda'], 'refresh' );
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}

	function relatorio()
	{
		CheckLogin();
		if(gerencia_in(array('GCM')))
		{
			$args   = Array();
			$data   = Array();
			$result = null;
			
			$qr_sql = "
						SELECT cd_empresa AS value,
						       sigla AS text
						  FROM public.patrocinadoras
						 ORDER BY sigla
			          ";
			$result = $this->db->query($qr_sql);
			$data['ar_empresa'] = $result->result_array();				
			
			$this->load->view("ecrm/prevenda/relatorio", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}				
	}	
	
	function relatorioListar()
	{
		/* Este método também é utilizado pelos indicadores*/
		
		CheckLogin();
		if(gerencia_in(array('GCM')))
		{
			$this->load->model("projetos/Pre_venda_model");
			
			$args   = Array();
			$data   = Array();
			$result = null;
			
			$args['nr_ano']     = $this->input->post("nr_ano");
			$args['nr_mes']     = $this->input->post("nr_mes");
			$args['cd_empresa'] = $this->input->post("cd_empresa");
			$args['tp_empresa'] = $this->input->post("tp_empresa");
			$fl_json            = $this->input->post("fl_json");
			
			manter_filtros($args);

			if(intval($args['nr_ano']) == 0)
			{
				$args['nr_ano'] = date("Y");
			}
			
			$this->Pre_venda_model->relatorioListar($result,$args);
			$data['collection'] = $result->result_array();
			
			if($fl_json == "S")
			{
				echo json_encode($data['collection']);
				#echo json_encode(array("name"=>"John","time"=>"2pm"));
			}
			else
			{
				$this->load->view("ecrm/prevenda/relatorio_result.php", $data);
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}				
	}
	
	function protocolo_interno()
	{
		CheckLogin();
		
		if(gerencia_in(array('GCM')))
		{
			$args   = Array();
			$data   = Array();
			$result = null;	
			
			$this->load->view("ecrm/prevenda/protocolo_interno", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
	}
	
	function protocolo_interno_listar()
	{
		CheckLogin();
		if(gerencia_in(array('GCM')))
		{
			$this->load->model("projetos/Pre_venda_model");
			
			$args   = Array();
			$data   = Array();
			$result = null;
			
			$args['cd_empresa']             = $this->input->post("cd_empresa");
			$args['cd_registro_empregado']  = $this->input->post("cd_registro_empregado");
			$args['seq_dependencia']        = $this->input->post("seq_dependencia");
			$args['nome']                   = $this->input->post("nome");
			$args['dt_contato_ini']         = $this->input->post("dt_contato_ini");
			$args['dt_contato_fim']         = $this->input->post("dt_contato_fim");
			$args['dt_protocolo_envio_ini'] = $this->input->post("dt_protocolo_envio_ini");
			$args['dt_protocolo_envio_fim'] = $this->input->post("dt_protocolo_envio_fim");			
			$args['fl_protocolo']           = $this->input->post("fl_protocolo");
			$args['fl_protocolo_enviado']   = $this->input->post("fl_protocolo_enviado");
			$args['cd_pre_venda_contato']   = "";
			
			manter_filtros($args);
			
			$this->Pre_venda_model->protocoloInternoListar($result,$args);
			$data['collection'] = $result->result_array();

			$this->load->view("ecrm/prevenda/protocolo_interno_result.php", $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}				
	}	
	
	function protocolo_interno_criar()
	{
		CheckLogin();
		
		if(gerencia_in(array('GCM')))
		{
			$this->load->model("projetos/Pre_venda_model");
			
			$args   = Array();
			$data   = Array();
			$result = null;
			
			$args['cd_empresa']             = "";
			$args['cd_registro_empregado']  = "";
			$args['seq_dependencia']        = "";
			$args['nome']                   = "";
			$args['dt_contato_ini']         = "";
			$args['dt_contato_fim']         = "";
			$args['dt_protocolo_envio_ini'] = "";
			$args['dt_protocolo_envio_fim'] = "";				
			$args['fl_protocolo']           = "";
			$args['fl_protocolo_enviado']   = "";
			$args['cd_pre_venda_contato']   = $this->input->post("prevendacontato_selecionado");
			
			$this->Pre_venda_model->protocoloInternoListar($result,$args);
			$ar_prevenda = $result->result_array();

			#### CRIA PROTOCOLO INTERNO ####
			$this->load->model("projetos/documento_recebido_model");
			$ar_doc = Array();
			$result = null;			
			$ar_doc["cd_documento_recebido_tipo"] = 1; #Central de Atendimento
			$ar_doc["cd_usuario"]                 = usuario_id();				
			$cd_documento_recebido = $this->documento_recebido_model->incluir_protocolo($result, $ar_doc);			

			$ar_doc = Array();
			$result = null;
			foreach($ar_prevenda as $item)
			{
				#### ADICIONA DOCUMENTOS ####
				$ar_doc["cd_documento_recebido"]      = intval($cd_documento_recebido);
				$ar_doc['cd_documento_recebido_item'] = 0;
				$ar_doc['cd_tipo_doc']                = 225;
				$ar_doc['cd_empresa']                 = $item['cd_empresa'];
				$ar_doc['cd_registro_empregado']      = $item['cd_registro_empregado'];
				$ar_doc['seq_dependencia']            = $item['seq_dependencia'];
				$ar_doc['nome']                       = $item['nome'];
				$ar_doc['ds_observacao']              = '';
				$ar_doc['nr_folha']                   = 1;
				$ar_doc['arquivo']                    = '';
				$ar_doc['arquivo_nome']               = '';
				$ar_doc["cd_usuario"]                 = usuario_id();	
				
				$this->documento_recebido_model->adicionar_documento($result, $ar_doc);					
			}
			
			if(intval($cd_documento_recebido) > 0)
			{
				$args['cd_documento_recebido'] = intval($cd_documento_recebido);
				$this->Pre_venda_model->protocoloInternoSetProtocolo($result,$args);
			
				redirect("ecrm/cadastro_protocolo_interno/detalhe/".$cd_documento_recebido, "refresh");
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function protocolo_interno_contato($cd_pre_venda)
	{
		CheckLogin();
		
		if(gerencia_in(array('GCM')))
		{
			$args   = Array();
			$data   = Array();
			$result = null;
			
			$this->load->model("projetos/Pre_venda_model");
			
			$arr = $this->Pre_venda_model->abrir( intval($cd_pre_venda) );

			$args['cd_documento_recebido_item'] = 0;
			$args['cd_tipo_doc']                = 225;
			$args['cd_empresa']                 = $arr['cd_empresa'];
			$args['cd_registro_empregado']      = $arr['cd_registro_empregado'];
			$args['seq_dependencia']            = $arr['seq_dependencia'];
			$args['nome']                       = $arr['nome'];
			$args['ds_observacao']              = '';
			$args['nr_folha']                   = 1;
			$args['arquivo']                    = '';
			$args['arquivo_nome']               = '';
			$args['cd_usuario_cadastro']        = usuario_id();
						
			$args['cd_documento_recebido']      = 0;
			$args["cd_documento_recebido_tipo"] = 1; #Central de Atendimento
			$args["cd_usuario_cadastro"]        = usuario_id();
			$args["cd_usuario"]                 = usuario_id();
	
			$this->load->model("projetos/documento_recebido_model");
			
			$cd_documento_recebido = $this->documento_recebido_model->incluir_protocolo($result, $args);		
			
			$args["cd_documento_recebido"] = $cd_documento_recebido;
			$args["cd_usuario"] = $args['cd_usuario_cadastro'];
			$this->documento_recebido_model->adicionar_documento($result, $args);	
		
			redirect( "ecrm/cadastro_protocolo_interno/detalhe/".$cd_documento_recebido, "refresh" );
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}	

}
?>