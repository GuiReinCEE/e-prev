<?php

class Plano_fiscal_parecer extends Controller
{
	var $fl_acesso = false;
	
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        $this->load->model('gestao/plano_fiscal_parecer_model');
		
        if ($this->session->userdata('usuario') == "dsilva") #Danielle
        {
            $this->fl_acesso = true;
        }
        elseif ($this->session->userdata('usuario') == "cgoncalves") #Cristina
        {
            $this->fl_acesso = true;
        }		
        elseif ($this->session->userdata('usuario') == "jfetter") #Jorge
        {
            $this->fl_acesso = true;
        }
        elseif ($this->session->userdata('usuario') == "rcoliveira") #Rosane
        {
            $this->fl_acesso = true;
        }	
		elseif ($this->session->userdata('usuario') == "lrodriguez") #Rosane
        {
            $this->fl_acesso = true;
        }			
		else
        {
            $this->fl_acesso = false;
        }		
    }
    
    public function index()
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;

            $this->load->view('gestao/plano_fiscal_parecer/index.php', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    public function listar()
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;
			
			$args['nr_ano'] = $this->input->post("nr_ano", TRUE);
            $args['nr_mes'] = $this->input->post("nr_mes", TRUE);
			
			manter_filtros($args);

            $this->plano_fiscal_parecer_model->listar($result, $args);

            $data['collection'] = $result->result_array();

            $this->load->view('gestao/plano_fiscal_parecer/partial_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function cadastro($cd_plano_fiscal_parecer = 0)
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;

            $data['cd_plano_fiscal_parecer'] = intval($cd_plano_fiscal_parecer);
            $args['cd_plano_fiscal_parecer'] = intval($cd_plano_fiscal_parecer);

            $this->plano_fiscal_parecer_model->total_enviados($result, $args);
            $data['total_enviados'] = $result->row_array();
            
            $this->plano_fiscal_parecer_model->get_usuarios_de($result, $args);
            $data['arr_diretoria'] = $result->result_array();
			
            $this->plano_fiscal_parecer_model->comboStatus($result, $args);
            $data['arr_status'] = $result->result_array();		

			$this->plano_fiscal_parecer_model->comboGerencia($result, $args);
            $data['cb_gerencia'] = $result->result_array();			
			

            if ($cd_plano_fiscal_parecer == 0)
            {
                $data['row'] = Array(
                  'cd_plano_fiscal_parecer' => 0,
                  'nr_ano' => date('Y'),
                  'nr_mes' => date('m'),
                  'cd_dir_administrativo' => '',
                  'cd_dir_financeiro' => '',
                  'cd_dir_seguridade' => '',
                  'cd_dir_seguridade' => '',
                  'cd_presidente' => ''
                );
            }
            else
            {
                $this->plano_fiscal_parecer_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }

            $this->load->view('gestao/plano_fiscal_parecer/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar()
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_plano_fiscal_parecer'] = $this->input->post("cd_plano_fiscal_parecer", TRUE);
            $args['nr_ano']                    = $this->input->post("nr_ano", TRUE);
            $args['nr_mes']                    = $this->input->post("nr_mes", TRUE);
            $args['cd_dir_financeiro']         = $this->input->post("cd_dir_financeiro", TRUE);
            $args['cd_dir_administrativo']     = $this->input->post("cd_dir_administrativo", TRUE);
            $args['cd_dir_seguridade']         = $this->input->post("cd_dir_seguridade", TRUE);
            $args['cd_presidente']             = $this->input->post("cd_presidente", TRUE);
            $args['cd_usuario']                = $this->session->userdata('codigo');

            $retorno = $this->plano_fiscal_parecer_model->salvar($result, $args);

            redirect("gestao/plano_fiscal_parecer/cadastro/".$retorno, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar_item()
    {
       if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_plano_fiscal_parecer']      = $this->input->post("cd_plano_fiscal_parecer", TRUE);
            $args['cd_plano_fiscal_parecer_item'] = $this->input->post("cd_plano_fiscal_parecer_item", TRUE);
            $args['cd_responsavel']               = $this->input->post("usuario", TRUE);
            $args['cd_gerencia']                  = $this->input->post("usuario_gerencia", TRUE);
            $args['descricao']                    = $this->input->post("descricao", TRUE);
            $args['nr_item']                      = $this->input->post("nr_item", TRUE);
            $args['cd_usuario']                   = $this->session->userdata('codigo');
            $args['cd_gerente']                   = $this->input->post("responsavel", TRUE);
            $args['cd_gerencia_gerente']          = $this->input->post("responsavel_gerencia", TRUE);
            $args['parecer']                      = $this->input->post("parecer", TRUE);
			$args['retorno']                      = $this->input->post("retorno", TRUE);
			$args['dt_limite']                    = $this->input->post("dt_limite", TRUE);
			$args['fl_status']                    = $this->input->post("fl_status", TRUE);
			$args['fl_copiar_resultado']          = $this->input->post("fl_copiar_resultado", TRUE);

            $this->plano_fiscal_parecer_model->salvar_item($result, $args);

            redirect("gestao/plano_fiscal_parecer/cadastro/".$args['cd_plano_fiscal_parecer'], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function listar_itens()
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;
            
            $args['cd_plano_fiscal_parecer'] = $this->input->post("cd_plano_fiscal_parecer", TRUE);
            $args['fl_respondido']           = $this->input->post("fl_respondido", TRUE);
            $args['fl_assinado']             = $this->input->post("fl_assinado", TRUE);
            $args['cd_gerencia_gerente']     = $this->input->post("cd_gerencia_gerente", TRUE);
			$args['fl_status_filtro']        = $this->input->post("fl_status_filtro", TRUE);			

            $this->plano_fiscal_parecer_model->listar_itens($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('gestao/plano_fiscal_parecer/item_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function carrega_item()
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null; 

            $args['cd_plano_fiscal_parecer_item'] = $this->input->post("cd_plano_fiscal_parecer_item", TRUE);

            $this->plano_fiscal_parecer_model->carrega_item($result, $args);

            $row = $result->row_array();

            $row = array_map("arrayToUTF8", $row);
            echo json_encode($row);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function enviar($cd_plano_fiscal_parecer, $cd_plano_fiscal_parecer_item= 0)
    {
        if ($this->fl_acesso)
        {
            $args['cd_plano_fiscal_parecer']      = $cd_plano_fiscal_parecer;
            $args['cd_plano_fiscal_parecer_item'] = $cd_plano_fiscal_parecer_item;
            $args['cd_usuario']                   = $this->session->userdata('codigo');
            
            $this->plano_fiscal_parecer_model->enviar($result, $args);

            redirect("gestao/plano_fiscal_parecer/cadastro/".$cd_plano_fiscal_parecer, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function excluir_plano_item($cd_plano_fiscal_parecer, $cd_plano_fiscal_parecer_item)
    {
        if ($this->fl_acesso)
        {
            $args['cd_plano_fiscal_parecer']      = $cd_plano_fiscal_parecer;
            $args['cd_plano_fiscal_parecer_item'] = $cd_plano_fiscal_parecer_item;
            $args['cd_usuario']     = $this->session->userdata('codigo');

            $this->plano_fiscal_parecer_model->excluir_plano_item($result, $args);

            redirect("gestao/plano_fiscal_parecer/cadastro/".$cd_plano_fiscal_parecer, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function excluir_plano($cd_plano_fiscal_parecer)
    {
        if ($this->fl_acesso)
        {
            $args['cd_plano_fiscal_parecer']      = $cd_plano_fiscal_parecer;
            $args['cd_usuario']     = $this->session->userdata('codigo');

            $this->plano_fiscal_parecer_model->excluir_plano($result, $args);

            redirect("gestao/plano_fiscal_parecer", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
        
    function resposta($cd_plano_fiscal_parecer_item)
    {
        $args = Array();
        $data = Array();
        $result = null; 
        
        $args['cd_plano_fiscal_parecer_item'] = $cd_plano_fiscal_parecer_item;
        $args['cd_usuario']     = $this->session->userdata('codigo');
        
        $this->plano_fiscal_parecer_model->carrega_parecer_item_resposta($result, $args);
        $data['row'] = $result->row_array();
		
		$this->plano_fiscal_parecer_model->comboStatus($result, $args);
		$data['arr_status'] = $result->result_array();			
        
        if(
			($data['row']['cd_responsavel'] == $args['cd_usuario']) #RESPONDENTE
			OR ($data['row']['cd_gerente'] == $args['cd_usuario']) #RESPONSAVEL
			OR ($this->session->userdata('divisao') == "DE") #DIRETOR
			OR (($this->session->userdata('tipo') == "G") AND ($data['row']['cd_gerencia_gerente'] == $this->session->userdata('divisao')))#GERENTE
			OR (($this->session->userdata('indic_01') == "S") AND ($data['row']['cd_gerencia_gerente'] == $this->session->userdata('divisao')))#GERENTE SUBSTITUTO
		  )		
        {
            $this->load->view('gestao/plano_fiscal_parecer/resposta', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar_resposta()
    {
        $args = Array();
        $data = Array();
        $result = null; 
        
        $args['cd_plano_fiscal_parecer_item'] = $this->input->post("cd_plano_fiscal_parecer_item", TRUE);
        $args['fl_status']  = $this->input->post("fl_status", TRUE);
        $args['parecer']  = $this->input->post("parecer", TRUE);
        $args['cd_usuario'] = $this->session->userdata('codigo');
        
        $this->plano_fiscal_parecer_model->salvar_resposta($result, $args);
        
        redirect("gestao/plano_fiscal_parecer/resposta/".$args['cd_plano_fiscal_parecer_item'], "refresh");
    }
    
    function confirmar()
    {
        $args = Array();
        $data = Array();
        $result = null; 
        
        $args['cd_plano_fiscal_parecer_item'] = $this->input->post("cd_plano_fiscal_parecer_item", TRUE);
        $args['fl_status']  = $this->input->post("fl_status", TRUE);
        $args['parecer']  = $this->input->post("parecer", TRUE);
        $args['cd_usuario'] = $this->session->userdata('codigo');
		
		$this->plano_fiscal_parecer_model->salvar_resposta($result, $args);
        
        $this->plano_fiscal_parecer_model->confirmar($result, $args);
        
        redirect("gestao/plano_fiscal_parecer/resposta/".$args['cd_plano_fiscal_parecer_item'], "refresh");
    }
	
	function reabrir($cd_plano_fiscal_parecer, $cd_plano_fiscal_parecer_item)
    {
        $args = Array();
        $data = Array();
        $result = null; 
        
        $args['cd_plano_fiscal_parecer_item'] = $cd_plano_fiscal_parecer_item;
        $args['cd_usuario']                   = $this->session->userdata('codigo');
		
        $this->plano_fiscal_parecer_model->reabrir($result, $args);
        
        redirect("gestao/plano_fiscal_parecer/cadastro/".$cd_plano_fiscal_parecer, "refresh");
    }
    
    function encaminhar($cd_plano_fiscal_parecer_item)
    {
        $args = Array();
        $data = Array();
        $result = null; 
        
        $args['cd_plano_fiscal_parecer_item'] = $cd_plano_fiscal_parecer_item;
        $args['cd_usuario']     = $this->session->userdata('codigo');
        
        $this->plano_fiscal_parecer_model->encaminhar($result, $args);
        
        redirect("gestao/plano_fiscal_parecer/resposta/".$args['cd_plano_fiscal_parecer_item'], "refresh");
    }
    
	function minhas()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
        $this->plano_fiscal_parecer_model->comboAnoMes($result, $args);
        $data['ar_ano_mes'] = $result->result_array();		
		
        $this->plano_fiscal_parecer_model->comboStatus($result, $args);
        $data['ar_status'] = $result->result_array();			
			
		$this->load->view('gestao/plano_fiscal_parecer/minhas.php', $data);        
    }
	
	function minhas_listar()
    {
        $args = array();
        $data = array();
        $result = null;

        $args['cd_usuario']    = $this->session->userdata('codigo');
        $args['nr_ano_mes']    = $this->input->post("nr_ano_mes", TRUE);
        $args['fl_status']     = $this->input->post("fl_status", TRUE);		
        $args['fl_respondido'] = $this->input->post("fl_respondido", TRUE);
		$args['fl_assinado']   = $this->input->post("fl_assinado", TRUE);
        $args['dt_ini_envio']  = $this->input->post("dt_ini_envio", TRUE);
        $args['dt_fim_envio']  = $this->input->post("dt_fim_envio", TRUE);
        $args['dt_ini_resp']   = $this->input->post("dt_ini_resp", TRUE);
        $args['dt_fim_resp']   = $this->input->post("dt_fim_resp", TRUE);

        manter_filtros($args);

        $this->plano_fiscal_parecer_model->carrega_minhas($result, $args);

        $data['collection'] = $result->result_array();

        $this->load->view('gestao/plano_fiscal_parecer/minhas_result', $data);
    }
    
  	function imprimir()
	{
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;
            
            $args['cd_plano_fiscal_parecer'] = $this->input->post("cd_plano_fiscal_parecer", TRUE);
            $args['fl_respondido']           = "";
            $args['fl_assinado']             = "";
            $args['fl_status_filtro']        = "";
            $args['cd_gerencia_gerente']     = "";	            
			
            $this->plano_fiscal_parecer_model->carrega($result, $args);
            $row = $result->row_array();

            $this->plano_fiscal_parecer_model->listar_itens($result, $args);
            $collection = $result->result_array();			
						
			#### EXCEL ####
			$this->load->plugin('phpexcel');
			$nr_col_ini  = 0;
			$nr_col_fim  = 0;
			$nr_row_ini  = 6;			
			
			#### Create new PHPExcel object ####
			$objPHPExcel = new PHPExcel();

			#### CRIA PLANILHA ####
			$objPHPExcel->setActiveSheetIndex(0);	
			$objPHPExcel->getActiveSheet()->setTitle('Parecer');
			
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
			$objPHPExcel->getActiveSheet()->mergeCells('D1:I1');
			$objPHPExcel->getActiveSheet()->setCellValue('D1', utf8_encode("PLANO DE FISCALIZAÇÃO - CONSELHO FISCAL - PARECER"));
			$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setSize(16);
			$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);	
			
			$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');
			$objPHPExcel->getActiveSheet()->setCellValue('A3', utf8_encode('Referente: '.$row['nr_mes'].'/'.$row['nr_ano']));
			$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);		

			$objPHPExcel->getActiveSheet()->mergeCells('A4:E4');
			$objPHPExcel->getActiveSheet()->setCellValue('A4', utf8_encode(date('d/m/Y H:i:s')));
			$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(false);				
			

			#### CABEÇALHO ####
			$nr_row = $nr_row_ini;
			$nr_col = $nr_col_ini;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col, $nr_row, utf8_encode("Item"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 1, $nr_row, utf8_encode("Descrição"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 2, $nr_row, utf8_encode("Status"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 3, $nr_row, utf8_encode("Parecer Sintético"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 4, $nr_row, utf8_encode("Gerência"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 5, $nr_row, utf8_encode("Responsável"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 6, $nr_row, utf8_encode("Assinatura"));
			
			#### TAMANHO COLUNA ASSINATURA ####
			$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(30);
			$nr_col_fim+=6;
		
			#### TABELA DO PARECER ####
			$nr_row = $nr_row_ini + 1;
            foreach($collection as $item)
            {
                $status = $item['ds_status'];
                $args['cd_usuario'] = intval($item['cd_usuario_confirmacao']);
                
                $this->plano_fiscal_parecer_model->get_assinatura($result, $args);
				$assinatura = (count($result->row_array()) > 0 ? $result->row_array() : array('assinatura'=>''));
				
				$nr_col = $nr_col_ini;
				
				$objPHPExcel->getActiveSheet()->getRowDimension($nr_row)->setRowHeight(70);
				
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col, $nr_row, utf8_encode($item['nr_item']));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 1, $nr_row, utf8_encode($item['descricao']));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 2, $nr_row, utf8_encode($status));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 3, $nr_row, utf8_encode($item['parecer']));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 4, $nr_row, utf8_encode((trim($item['dt_confirmacao']) != '' ? $item['cd_gerencia_gerente'] : '')));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 5, $nr_row, utf8_encode((trim($item['dt_confirmacao']) != '' ? (trim($assinatura['assinatura']) != '' ? "" : '') .$item['usuario_confirmacao'] : '')));

				if((trim($assinatura['assinatura']) != '') AND (trim($item['dt_confirmacao']) != ''))
                {
					#### ASSINATURA ####
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					$objDrawing->setName('Terms and conditions');
					$objDrawing->setDescription('Terms and conditions');
					list($width, $height) = getimagesize('./img/assinatura/'.$assinatura['assinatura']); 
					$objDrawing->setPath('./img/assinatura/'.$assinatura['assinatura']);
					$col_assina = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col + 6, $nr_row)->getCoordinate();
					$objDrawing->setCoordinates($col_assina);
					$objDrawing->setHeight($height/5);
					$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());					
				}

				$nr_row++;				
			}	
			
			
			#### FORMATA CABEÇALHO DA TABELA ####
			$I = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_ini, $nr_row_ini)->getCoordinate();
			$F = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_fim, $nr_row_ini)->getCoordinate();
			$sharedStyle = new PHPExcel_Style();
			$sharedStyle->applyFromArray(
				array(
							'font' => array(
								'bold' => true,
								'size' => 14
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
									),
                        'font' => array(
							'size' => 14
						)										
					 ));			
			
			$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, $I.':'.$F);
	
			$nr_row = $nr_row+3;
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$nr_row, utf8_encode("De acordo,  "));
			$objPHPExcel->getActiveSheet()->getStyle('A'.$nr_row)->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$nr_row)->getFont()->setBold(true);	
			
			$nr_row = $nr_row+3;
			
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$nr_row, utf8_encode($row['usuario_presidente']));
			$objPHPExcel->getActiveSheet()->getStyle('B'.$nr_row)->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('B'.$nr_row)->getFont()->setBold(true);	
			
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($nr_row+1), utf8_encode('Presidente'));
			$objPHPExcel->getActiveSheet()->getStyle('B'.($nr_row+1))->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($nr_row+1))->getFont()->setBold(true);	

			$objPHPExcel->getActiveSheet()->setCellValue('E'.$nr_row, utf8_encode($row['usuario_dir_financeiro']));
			$objPHPExcel->getActiveSheet()->getStyle('E'.$nr_row)->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$nr_row)->getFont()->setBold(true);	
			
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($nr_row+1), utf8_encode('Diretor Financeiro'));
			$objPHPExcel->getActiveSheet()->getStyle('E'.($nr_row+1))->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('E'.($nr_row+1))->getFont()->setBold(true);	
			
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$nr_row, utf8_encode($row['usuario_dir_seguridade']));
			$objPHPExcel->getActiveSheet()->getStyle('H'.$nr_row)->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('H'.$nr_row)->getFont()->setBold(true);	
			
			$objPHPExcel->getActiveSheet()->setCellValue('H'.($nr_row+1), utf8_encode('Diretor de Seguridade'));
			$objPHPExcel->getActiveSheet()->getStyle('H'.($nr_row+1))->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($nr_row+1))->getFont()->setBold(true);	
			
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$nr_row, utf8_encode($row['usuario_dir_administrativo']));
			$objPHPExcel->getActiveSheet()->getStyle('K'.$nr_row)->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('K'.$nr_row)->getFont()->setBold(true);	
			
			$objPHPExcel->getActiveSheet()->setCellValue('K'.($nr_row+1), utf8_encode('Diretor Administrativo'));
			$objPHPExcel->getActiveSheet()->getStyle('K'.($nr_row+1))->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('K'.($nr_row+1))->getFont()->setBold(true);		
	
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
	
	function imprimirPDF()
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;
            
            $args['cd_plano_fiscal_parecer'] = $this->input->post("cd_plano_fiscal_parecer", TRUE);
            $args['fl_respondido']           = "";
            $args['fl_assinado']             = "";
            $args['fl_status_filtro']        = "";
            $args['cd_gerencia_gerente']     = "";	

            $this->load->plugin('fpdf');
            
            $this->plano_fiscal_parecer_model->carrega($result, $args);
            $row = $result->row_array();

            $this->plano_fiscal_parecer_model->listar_itens($result, $args);
            $collection = $result->result_array();
 
            $ob_pdf = new PDF('L','mm','A4');
            
            $ob_pdf->SetNrPag(true);
            $ob_pdf->SetMargins(10,14,5);
            $ob_pdf->header_exibe = true;
            $ob_pdf->header_logo = true;
            $ob_pdf->header_titulo = true;
            $ob_pdf->header_titulo_texto = "PLANO DE FISCALIZAÇÃO - CONSELHO FISCAL - PARECER";
            
            $ob_pdf->AddPage();	
            $ob_pdf->SetFont( 'Courier', 'B', 10 );            
            $ob_pdf->MultiCell(0, 5, 'Referente: '.$row['nr_mes'].'/'.$row['nr_ano'], '0', 'L');
            
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);

            $ob_pdf->SetWidths( array(15, 90, 25, 90, 25, 35) );
            $ob_pdf->SetAligns( array('C','C','C','C','C','C') );
            $ob_pdf->SetFont( 'Courier', 'B', 10 );
            $ob_pdf->Row(array("Item", "Descrição", "Status", "Parecer Sintético", "Gerência", "Responsável"));
            $ob_pdf->SetAligns( array('C','L','C','L', 'C', 'C') );
            $ob_pdf->SetFont( 'Courier', '', 10 );
            
            foreach($collection as $item)
            {
                $status = $item['ds_status'];
                $args['cd_usuario'] = $item['cd_usuario_confirmacao'];
                
                $this->plano_fiscal_parecer_model->get_assinatura($result, $args);
                $assinatura = (count($result->row_array()) > 0 ? $result->row_array() : array('assinatura'=>''));
                
                if(trim($assinatura['assinatura']) != '')
                {
                    list($width, $height) = getimagesize('./img/assinatura/'.$assinatura['assinatura']);   
                    
                }
                
                if((trim($assinatura['assinatura']) != '') AND (trim($item['dt_confirmacao']) != ''))
                {
                    if($ob_pdf->GetY() >= 174)
                    {
                        $ob_pdf->AddPage();
                    }

                    $ob_pdf->Image('./img/assinatura/'.$assinatura['assinatura'], 255, $ob_pdf->GetY(), $ob_pdf->ConvertSize($width/8), $ob_pdf->ConvertSize($height/8));
                }
                
                $ob_pdf->Row(array(
					$item['nr_item'], 
					$item['descricao'], 
					$status ,
					$item['parecer'], 
					(trim($item['dt_confirmacao']) != '' ? $item['cd_gerencia_gerente'] : ''),
					(trim($item['dt_confirmacao']) != '' ? (trim($assinatura['assinatura']) != '' ? "\n\n" : '') .$item['usuario_confirmacao'] : '')));	
   
            }
            
            if(($ob_pdf->GetY()+ 8) >= 192)
            {
                $ob_pdf->AddPage();	
            }
            
            $ob_pdf->SetY($ob_pdf->GetY() + 8);
            $ob_pdf->SetFont( 'Courier', '', 8 );
            $ob_pdf->Text(10, $ob_pdf->GetY(), "De acordo,");
            
            $ob_pdf->SetY($ob_pdf->GetY() + 16);
            
            $nr_df = $ob_pdf->GetStringWidth($row['usuario_dir_financeiro']);
            $nr_c_df = $ob_pdf->GetStringWidth('Diretor Financeiro');
            
            $coluna_df    = abs(($nr_df-$nr_c_df)/2);
            $coluna_nm_df = abs(($nr_df-$nr_c_df)/2);
            
            if($nr_df > $nr_c_df)
            {
                $coluna_nm_df = 0;
                $nr_x = $nr_df;
            }
            else
            {
                $coluna_df = 0;
                $nr_x = $nr_c_df;
            }
            
            $ob_pdf->SetX(10);
            
            $ob_pdf->Text($ob_pdf->GetX() + $coluna_nm_df, $ob_pdf->GetY(), $row['usuario_dir_financeiro']);
            $ob_pdf->Text($ob_pdf->GetX() + $coluna_df , $ob_pdf->GetY()+4, "Diretor Financeiro");
            
            $nr_ds = $ob_pdf->GetStringWidth($row['usuario_dir_seguridade']);
            $nr_c_ds = $ob_pdf->GetStringWidth('Diretor de Seguridade');
            
            $coluna_ds    = abs(($nr_ds-$nr_c_ds)/2);
            $coluna_nm_ds = abs(($nr_ds-$nr_c_ds)/2);
            
            $ob_pdf->SetX($ob_pdf->GetX()+$nr_x+15);
            
            if($nr_ds > $nr_c_ds)
            {
                $coluna_nm_ds = 0;
                $nr_x = $nr_ds;
            }
            else
            {
                $coluna_ds = 0;
                $nr_x = $nr_c_ds;
            }
            
            $ob_pdf->Text($ob_pdf->GetX() + $coluna_nm_ds, $ob_pdf->GetY(), $row['usuario_dir_seguridade']);
            $ob_pdf->Text($ob_pdf->GetX()  + $coluna_ds, $ob_pdf->GetY()+4, "Diretor de Seguridade");
            
            $nr_da = $ob_pdf->GetStringWidth($row['usuario_dir_administrativo']);
            $nr_c_da = $ob_pdf->GetStringWidth('Diretor Administrativo');
            
            $coluna_da = abs(($nr_da-$nr_c_da)/2);
            $coluna_nm_da = abs(($nr_da-$nr_c_da)/2);
            
            $ob_pdf->SetX($ob_pdf->GetX()+$nr_x+15);
            
            if($nr_da > $nr_c_da)
            {
                $coluna_nm_da = 0;
                $nr_x = $nr_da;
            }
            else
            {
                $coluna_da = 0;
                $nr_x = $nr_c_da;
                
            }
            
            $ob_pdf->Text($ob_pdf->GetX() + $coluna_nm_da, $ob_pdf->GetY(), $row['usuario_dir_administrativo']);
            $ob_pdf->Text($ob_pdf->GetX() + $coluna_da, $ob_pdf->GetY()+4, "Diretor Administrativo");
            
            
            $nr_p = $ob_pdf->GetStringWidth($row['usuario_presidente']);
            $nr_c_p = $ob_pdf->GetStringWidth('Presidente');
            
            $coluna_p = abs(($nr_p-$nr_c_p)/2);
            $coluna_nm_p = abs(($nr_p-$nr_c_p)/2)+10;
            
            if($nr_p > $nr_c_p)
            {
                $coluna_nm_p = 0;
            }
            else
            {
                $coluna_p = 0;    
            }
            
            $ob_pdf->SetX($ob_pdf->GetX()+$nr_x+15);
            
            $ob_pdf->Text($ob_pdf->GetX()+$coluna_nm_p, $ob_pdf->GetY(), $row['usuario_presidente']);
            $ob_pdf->Text($ob_pdf->GetX()+$coluna_p, $ob_pdf->GetY()+4, "Presidente");
            
            $ob_pdf->Output();
            exit;
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
}
?>