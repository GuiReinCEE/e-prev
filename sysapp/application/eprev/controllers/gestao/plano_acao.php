<?php
class Plano_acao extends Controller
{
    function __construct()
    {
		parent::Controller();

		CheckLogin();
	}

    private function get_permissao()
    {
        if(gerencia_in(array('AI')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    private function get_permissao_gerencia()
    {
        if($this->session->userdata('tipo') == 'G')
        {
            return TRUE;
        }
        else if($this->session->userdata('indic_01') == 'S')
        {
            return TRUE;
        }
        else if(gerencia_in(array('DE')))
        {
            return TRUE;
        }
        else if(gerencia_in(array('AI')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    private function get_status()
    {
        return array(
            array('value' => 'N', 'text' => 'Não iniciada'),
            array('value' => 'A', 'text' => 'Em andamento'), 
            array('value' => 'E', 'text' => 'Encerrada')
        );
    }

    public function index()
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/plano_acao_model');

            $data['processo'] = $this->plano_acao_model->get_processo();

            $this->load->view('gestao/plano_acao/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

    public function listar()
    {
		$this->load->model('gestao/plano_acao_model');

    	$args = array(
            'nr_plano_acao'        => $this->input->post('nr_plano_acao', TRUE),
            'nr_ano'               => $this->input->post('nr_ano', TRUE),
            'dt_envio_responsavel' => $this->input->post('dt_envio_responsavel', TRUE),
            'cd_processo'          => $this->input->post('cd_processo', TRUE),
            'ds_situacao'          => $this->input->post('ds_situacao', TRUE)
    	);
			
		manter_filtros($args);

		$data['collection'] = $this->plano_acao_model->listar($args);

        $this->load->view('gestao/plano_acao/index_result', $data);
    }

    public function cadastro($cd_plano_acao = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/plano_acao_model');

            $data['processo'] = $this->plano_acao_model->get_processo();

            if(intval($cd_plano_acao) == 0)
            {
                $nr_ano = date('Y');

                $row = $this->plano_acao_model->get_numero_plano_acao($nr_ano);

                $data['row'] = array(
                    'cd_plano_acao'          => intval($cd_plano_acao),
                    'nr_ano'                 => $nr_ano,
                    'nr_plano_acao'          => $row['nr_plano_acao'],
                    'cd_processo'            => '', 
                    'ds_situacao'            => '',
                    'ds_relatorio_auditoria' => ''
                );
            }
            else
            {
               $data['row'] = $this->plano_acao_model->carrega($cd_plano_acao);
            }

    		$this->load->view('gestao/plano_acao/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
    	if($this->get_permissao())
        {
            $this->load->model('gestao/plano_acao_model');

        	$cd_plano_acao = $this->input->post('cd_plano_acao', TRUE);

        	$args = array(
                'cd_plano_acao'          => $this->input->post('cd_plano_acao', TRUE),
                'nr_ano'                 => $this->input->post('nr_ano', TRUE),
                'nr_plano_acao'          => $this->input->post('nr_plano_acao', TRUE),
                'cd_processo'            => $this->input->post('cd_processo', TRUE),
                'ds_situacao'            => $this->input->post('ds_situacao', TRUE),
                'ds_relatorio_auditoria' => $this->input->post('ds_relatorio_auditoria', TRUE),
        		'cd_usuario'             => $this->session->userdata('codigo')
        	);

        	if(intval($cd_plano_acao) == 0)
    		{
        		$cd_plano_acao = $this->plano_acao_model->salvar($args);
            } 
    		else
    		{
    			$this->plano_acao_model->atualizar(intval($cd_plano_acao), $args);
    		}

    		redirect('gestao/plano_acao/itens/'.$cd_plano_acao, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function itens($cd_plano_acao, $cd_plano_acao_item = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/plano_acao_model');

            $data['row'] = $this->plano_acao_model->carrega($cd_plano_acao);

            $cd_diretoria = '';
            
            if((gerencia_in(array('DE')) AND $this->session->userdata('diretoria') != 'PRE'))
            {
                $cd_diretoria = $this->session->userdata('diretoria');
            }

            $data['collection'] = $this->plano_acao_model->listar_itens($cd_plano_acao, $cd_diretoria);

            foreach ($data['collection'] as $key => $item)
            {
               $data['collection'][$key]['ds_recomendacao'] = array();

               $recomendacao = $this->plano_acao_model->listar_recomendacao($item['cd_plano_acao_item']);

               foreach ($recomendacao as $key1 => $ds_recomendacao) 
               {
                   $data['collection'][$key]['ds_recomendacao'][] = $ds_recomendacao['ds_recomendacao_item'];
               }
            }
            
            if(intval($cd_plano_acao_item) == 0)
            {
                $row = $this->plano_acao_model->get_nr_item($cd_plano_acao);

                $data['plano_acao'] = array(
                    'cd_plano_acao_item'      => intval($cd_plano_acao_item),
                    'nr_plano_acao_item'      => (isset($row['nr_plano_acao_item']) ? intval($row['nr_plano_acao_item']) : 1),
                    'ds_constatacao'          => '', 
                    'ds_recomendacao'         => '',
                    'cd_gerencia_responsavel' => '',
                    'cd_usuario_responsavel'  => '',
                    'cd_usuario_substituto'   => '',
                    'dt_prazo'                => ''
                );

                $data['usuario'] = array();
            }
            else
            {
                $data['plano_acao'] = $this->plano_acao_model->carrega_item($cd_plano_acao_item);

                $data['usuario'] = $this->plano_acao_model->get_usuarios($data['plano_acao']['cd_gerencia_responsavel']);
            }

            $this->load->view('gestao/plano_acao/itens', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function set_ordem($cd_plano_acao_item)
    {
        $this->load->model('gestao/plano_acao_model');

        $args = array(
            'nr_plano_acao_item' => $this->input->post('nr_plano_acao_item', TRUE),
            'cd_usuario'         => $this->session->userdata('codigo')
        );
        
        $this->plano_acao_model->set_ordem($cd_plano_acao_item, $args);
    }

    public function set_ordem_recomendacao($cd_plano_acao_item_recomendacao)
    {
        $this->load->model('gestao/plano_acao_model');

        $args = array(
            'nr_plano_acao_item_recomendacao' => $this->input->post('nr_plano_acao_item_recomendacao', TRUE),
            'cd_usuario'         => $this->session->userdata('codigo')
        );
        
        $this->plano_acao_model->set_ordem_recomendacao($cd_plano_acao_item_recomendacao, $args);
    }

    public function salvar_item()
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/plano_acao_model');
            
            $cd_plano_acao      = $this->input->post('cd_plano_acao', TRUE);
            $cd_plano_acao_item = $this->input->post('cd_plano_acao_item', TRUE);
            $cd_plano_acao_item_recomendacao = $this->input->post('cd_plano_acao_item_recomendacao', TRUE);

            $args = array(
                'cd_plano_acao'                   => $cd_plano_acao,
                'cd_plano_acao_item'              => $cd_plano_acao_item,
                'cd_plano_acao_item_recomendacao' => $cd_plano_acao_item_recomendacao,
                'nr_plano_acao_item'              => $this->input->post('nr_plano_acao_item', TRUE),
                'ds_constatacao'                  => $this->input->post('ds_constatacao', TRUE), 
                'ds_recomendacao'                 => $this->input->post('ds_recomendacao', TRUE),
                'cd_gerencia_responsavel'         => $this->input->post('cd_gerencia_responsavel', TRUE),
                'cd_usuario_responsavel'          => $this->input->post('cd_usuario_responsavel', TRUE),
                'cd_usuario_substituto'           => $this->input->post('cd_usuario_substituto', TRUE),
                'dt_prazo'                        => $this->input->post('dt_prazo', TRUE),
                'nr_plano_acao_item_recomendacao' => '1',
                'cd_usuario'                      => $this->session->userdata('codigo')
            );            
    
            if(intval($cd_plano_acao_item) == 0)
            {
                $cd_plano_acao_item = $this->plano_acao_model->salvar_item($args);

                $this->plano_acao_model->salvar_recomendacao($cd_plano_acao_item, $args);
            }
            else
            {
                $this->plano_acao_model->atualizar_item($cd_plano_acao_item, $args);
            }
           
            redirect('gestao/plano_acao/itens/'.intval($cd_plano_acao), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function enviar_email($cd_plano_acao)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'gestao/plano_acao_model',
                'projetos/eventos_email_model'
            ));

            $cd_evento = 239;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $plano_acao = $this->plano_acao_model->carrega($cd_plano_acao);

            $item_plano = $this->plano_acao_model->listar_itens($cd_plano_acao);
            
            foreach ($item_plano as $key => $item) 
            { 
                $para = $item['ds_usuario_gerente'].'@eletroceee.com.br';

                if(trim($item['ds_usuario_substituto']) != '')
                {
                   $para .= ';'.$item['ds_usuario_substituto'].'@eletroceee.com.br'; 
                }
                
                $tags = array('[NR_ANO_NUMERO]', '[LINK]');

                $subs = array(
                    $plano_acao['ds_ano_numero'], 
                    site_url('gestao/plano_acao/responder/'.intval($cd_plano_acao).'/'.intval($item['cd_plano_acao_item']))
                );
           
                $texto = str_replace($tags, $subs, $email['email']);

                $cd_usuario = $this->session->userdata('codigo');

                $args = array( 
                    'de'      => 'Plano de Ação',
                    'assunto' => str_replace('[NR_ANO_NUMERO]', $plano_acao['ds_ano_numero'], $email['assunto']),
                    'para'    => $para,
                    'cc'      => $email['cc'],
                    'cco'     => $email['cco'],
                    'texto'   => $texto
                );
   
                $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
            } 

            $this->plano_acao_model->encaminhar_email(
                $cd_plano_acao, 
                $this->session->userdata('codigo')
            );

            redirect('gestao/plano_acao/itens/'.intval($cd_plano_acao), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function pdf_item($cd_plano_acao = 0, $cd_plano_acao_item = 0)
    {
        if($this->get_permissao())
        {
            $this->load->plugin('fpdf');

            $this->load->model('gestao/plano_acao_model');
            
            $row = $this->plano_acao_model->carrega($cd_plano_acao);

            $collection[] = $this->plano_acao_model->carrega_item($cd_plano_acao_item); 

             $recomendacao = $this->plano_acao_model->listar_recomendacao($cd_plano_acao_item);

            $this->pdf($row, $collection, $recomendacao);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function pdf_todos($cd_plano_acao = 0)
    {
        if($this->get_permissao_gerencia())
        {
            $this->load->plugin('fpdf');

            $this->load->model('gestao/plano_acao_model');

            $recomendacao = 0;
            
            $row = $this->plano_acao_model->carrega($cd_plano_acao);

            $collection = $this->plano_acao_model->listar_itens($cd_plano_acao); 

            $this->pdf($row, $collection, $recomendacao);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excel($cd_plano_acao = 0)
    {
        if($this->get_permissao_gerencia())
        {
            $this->load->model('gestao/plano_acao_model');

            $row = $this->plano_acao_model->carrega($cd_plano_acao);

            $collection = $this->plano_acao_model->listar_itens($cd_plano_acao); 
                        
            #### EXCEL ####
            $this->load->plugin('phpexcel');
            $nr_col_ini  = 0;
            $nr_col_fim  = 0;
            $nr_row_ini  = 6;           
            
            #### Create new PHPExcel object ####
            $objPHPExcel = new PHPExcel();

            #### CRIA PLANILHA ####
            $objPHPExcel->setActiveSheetIndex(0);   
      
            #### LOGO ####
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setName('Terms and conditions');
            $objDrawing->setDescription('Terms and conditions');
            list($width, $height) = getimagesize('./img/logofundacao_carta.jpg'); 
            $objDrawing->setPath('./img/logofundacao_carta.jpg');
            $objDrawing->setCoordinates("A1");
            $objDrawing->setHeight(48);
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());              

            #### TITULO ###
            $objPHPExcel->getActiveSheet()->mergeCells('C1:F1');
            $objPHPExcel->getActiveSheet()->setCellValue('C1', utf8_encode('AUDITORIA INTERNA - PLANO DE AÇÃO'));
            $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(16);
            $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);   
            
            $objPHPExcel->getActiveSheet()->mergeCells('D2:E2');
            $objPHPExcel->getActiveSheet()->setCellValue('D2', utf8_encode('Referente:'.$row['ds_ano_numero']));
            $objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setSize(12);
            $objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->mergeCells('A3:G3');

            if(trim($row['cd_processo']) != '')
            {
                $objPHPExcel->getActiveSheet()->mergeCells('B4:D4');
                $objPHPExcel->getActiveSheet()->setCellValue('B4', utf8_encode('Processo:  '.$row['procedimento']));
                $objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setSize(11);
                $objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->mergeCells('B4:D4');
                $objPHPExcel->getActiveSheet()->setCellValue('B4', utf8_encode('Situação:  '.$row['ds_situacao']));
                $objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setSize(11);
                $objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
            }

            $objPHPExcel->getActiveSheet()->mergeCells('E4:G4');
            $objPHPExcel->getActiveSheet()->setCellValue('E4', utf8_encode(date('d/m/Y H:i:s')));
            $objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setSize(12);
                    
            $nr_row = $nr_row_ini;
            $nr_col = $nr_col_ini;

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col ,$nr_row,utf8_encode("N°"))->getColumnDimension("A")->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 1, $nr_row, utf8_encode("Constatação"));
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 2, $nr_row, utf8_encode("Recomendação"));
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 3, $nr_row, utf8_encode("Ação"));
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 4, $nr_row, utf8_encode("Responsável"));
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 5, $nr_row, utf8_encode("Dt. Prazo"));
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 6, $nr_row, utf8_encode("Status"));

            $nr_col_fim+=6;
            $nr_row = $nr_row_ini + 1;
            
            foreach($collection as $item)
            {
                if(trim($item['ds_status']) != '')
                {
                    $status = $item['ds_status'];
                }
                else
                {
                    $status = 'Não Iniciado';
                }

                $ds_recomendacao = array();
                
                $nr_col = $nr_col_ini;

                $objPHPExcel->getActiveSheet()->mergeCells('A'.$nr_row.':A'.$nr_row);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col, $nr_row, utf8_encode($item['nr_plano_acao_item']));

                $objPHPExcel->getActiveSheet()->mergeCells('B'.$nr_row.':B'.$nr_row);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 1, $nr_row, utf8_encode($item['ds_constatacao']))->getColumnDimension("B")->setWidth(30);

                $recomendacao = $this->plano_acao_model->listar_recomendacao($item['cd_plano_acao_item']);
                
                foreach ($recomendacao as $key1 => $item1) 
                {
                    $ds_recomendacao[] =  $item1['ds_recomendacao_item'];
                } 

                $objPHPExcel->getActiveSheet()->mergeCells('C'.$nr_row.':C'.$nr_row);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 2, $nr_row, 
                utf8_encode(implode("\n",$ds_recomendacao)))->getColumnDimension("C")->setWidth(30);               

                $objPHPExcel->getActiveSheet()->mergeCells('D'.$nr_row.':D'.$nr_row);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 3, $nr_row, utf8_encode($item['ds_acao']))->getColumnDimension("D")->setWidth(30);

                $objPHPExcel->getActiveSheet()->mergeCells('E'.$nr_row.':E'.$nr_row);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 4, $nr_row, utf8_encode($item['cd_gerencia_responsavel']))->getColumnDimension("E")->setWidth(12);

                $objPHPExcel->getActiveSheet()->mergeCells('F'.$nr_row.':F'.$nr_row);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 5, $nr_row, utf8_encode($item['dt_prazo']));

                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 6, $nr_row, utf8_encode($status));
                 
                $nr_row = $nr_row + 1;
            }  

            #### FORMATA CABEÇALHO DA TABELA ####
            $I = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_ini, $nr_row_ini)->getCoordinate();
            $F = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_fim, $nr_row_ini)->getCoordinate();
            $sharedStyle = new PHPExcel_Style();
            $sharedStyle->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'size' => 12
                    ),
                    'alignment' => array(
                        'wrap'       => true, 
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    ),
                    'borders' => array(
                        'top' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        ),
                        'bottom' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        ),
                        'left' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        ),
                        'right' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )                               
                    ),                          
                    'fill' => array(
                        'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                        'rotation'   => 90,
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
            $F = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_fim, $nr_row -1)->getCoordinate();          
    
            $sharedStyle = new PHPExcel_Style();
            $sharedStyle->applyFromArray(
                array(
                    'borders' => array(
                        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                    ),
                    'alignment' => array(
                        'wrap'       => true,
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY,
                        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_TOP
                    ),
                    'font' => array(
                        'size' => 10
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
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function pdf($row, $collection, $recomendacao)
    {
        $this->load->plugin('fpdf');
        $ob_pdf = new PDF('P', 'mm', 'A4');
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetNrPagDe(true);
        $ob_pdf->SetMargins(10, 15, 5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = 'AUDITORIA INTERNA';

        $ob_pdf->header_subtitulo = true;
        $ob_pdf->header_subtitulo_texto = 'Plano de Ação: '.$row['ds_ano_numero'];

        $ob_pdf->AddPage(); 

        if(trim($row['cd_processo']) != '')
        {
            $ob_pdf->SetFont('segoeuib','',13);
            $ob_pdf->MultiCell(190, 5, 'Processo:', '0', 'L');
            $ob_pdf->SetFont('segoeuil','',13);
            $ob_pdf->MultiCell(190, 5, $row['procedimento'],'0', 'L');

            $ob_pdf->SetY($ob_pdf->GetY() + 3);
        }
        else
        {
            $ob_pdf->SetFont('segoeuib','',13);
            $ob_pdf->MultiCell(190, 5, 'Situação:', '0', 'L');
            $ob_pdf->SetFont('segoeuil','',13);
            $ob_pdf->MultiCell(190, 5, $row['ds_situacao'],'0', 'L');

            $ob_pdf->SetY($ob_pdf->GetY() + 5);
        }

        foreach($collection as $item)
        {
            if(trim($item['ds_status']) != '')
            {
                $status = $item['ds_status'];
            }
            else
            {
                $status = 'Não Iniciado';
            }
            
            $ds_recomendacao = '';

            $recomendacao = $this->plano_acao_model->listar_recomendacao($item['cd_plano_acao_item']); 

            foreach ($recomendacao as $key2 => $item1)
            {
                $ds_recomendacao[] = $item1['ds_recomendacao_item'];
            }

            $ob_pdf->SetFont('segoeuib','',13);
            $ob_pdf->MultiCell(190, 5, 'Nº:', '0', 'L');
            $ob_pdf->SetFont('segoeuil','',13);
            $ob_pdf->MultiCell(190, 5, $item['nr_plano_acao_item'],'0', 'L');

            $ob_pdf->SetY($ob_pdf->GetY() + 3);

            $ob_pdf->SetFont('segoeuib','',13);
            $ob_pdf->MultiCell(190, 5, 'Dt. Prazo: ','0','L');
            $ob_pdf->SetFont('segoeuil','',13);
            $ob_pdf->MultiCell(190, 5, $item['dt_prazo'], '0', 'L');

            $ob_pdf->SetY($ob_pdf->GetY() + 3);

            $ob_pdf->SetFont('segoeuib','',13);
            $ob_pdf->MultiCell(190, 5, 'Status: ','0','L');
            $ob_pdf->SetFont('segoeuil','',13);
            $ob_pdf->MultiCell(190, 5, $status, '0', 'L');

            $ob_pdf->SetY($ob_pdf->GetY() + 3);

            $ob_pdf->SetFont('segoeuib','',13);
            $ob_pdf->MultiCell(190, 5, 'Responsável: ','0','L');
            $ob_pdf->SetFont('segoeuil','',13);
            $ob_pdf->MultiCell(190, 5, $item['cd_gerencia_responsavel'], '0', 'L');

            $ob_pdf->SetY($ob_pdf->GetY() + 3);

            $ob_pdf->SetFont('segoeuib','',13);
            $ob_pdf->MultiCell(190, 5, 'Constatação: ','0','L');
            $ob_pdf->SetFont('segoeuil','',13);
            $ob_pdf->MultiCell(190, 5, $item['ds_constatacao'],'0','J');

            $ob_pdf->SetY($ob_pdf->GetY() + 3);

            $ob_pdf->SetFont('segoeuib','',13);
            $ob_pdf->MultiCell(190, 5, 'Recomendações: ','0','L');
            $ob_pdf->SetFont('segoeuil','',13);
            $ob_pdf->MultiCell(190, 5, implode("\n",$ds_recomendacao),'0','J');

            $ob_pdf->SetY($ob_pdf->GetY() + 3);

            $ob_pdf->SetFont('segoeuib','',13);
            $ob_pdf->MultiCell(190, 5, 'Ação: ','0','L');
            $ob_pdf->SetFont('segoeuil','',13);
            $ob_pdf->MultiCell(190, 5, $item['ds_acao'],'0','J');

            $ob_pdf->SetY($ob_pdf->GetY() + 5);
            $ob_pdf->MultiCell(190, 5, "------------------------------------------------------------------------------------------------------", '0', 'J');
            $ob_pdf->SetY($ob_pdf->GetY() + 5);                 
           
            if($ob_pdf->GetY() >= 260) 
            {
                #### FORCA A QUEBRA DA PAGINA ####
                $ob_pdf->AddPage();
            }   
        }

        $ob_pdf->Output();
        exit;
    }

    public function acompanhamento($cd_plano_acao, $cd_plano_acao_item, $cd_plano_acao_acompanhamento = 0)
    {
        $this->load->model('gestao/plano_acao_model');

        $row = $this->plano_acao_model->carrega($cd_plano_acao);

        $item = $this->plano_acao_model->carrega_item($cd_plano_acao_item);

        $cd_usuario = $this->session->userdata('codigo');

        if($this->get_permissao_gerencia() OR intval($item['cd_usuario_responsavel']) == $cd_usuario OR intval($item['cd_usuario_substituto']) == $cd_usuario)
        {
            $data = array(
                'row'             => $row,
                'itens'           => $item,
                'collection'      => $this->plano_acao_model->listar_acompanhamento($cd_plano_acao_item),
                'status'          => $this->get_status(),
                'ds_recomendacao' => array()
            );

            $recomendacao = $this->plano_acao_model->listar_recomendacao($data['itens']['cd_plano_acao_item']);

            foreach ($recomendacao as $key => $ds_recomendacao) 
            {
                $data['ds_recomendacao'][] = $ds_recomendacao['ds_recomendacao_item'];
            }            

            if(intval($cd_plano_acao_acompanhamento) == 0)
            {
                $data['acompanhamento'] = array(
                    'cd_plano_acao_acompanhamento'  => $cd_plano_acao_acompanhamento,
                    'fl_status'                     => '',
                    'ds_acompanhamento'             => ''
                );
            }
            else    
            {
                $data['acompanhamento'] = $this->plano_acao_model->carrega_acompanhamento($cd_plano_acao_acompanhamento);
            }

            $this->load->view('gestao/plano_acao/acompanhamento', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_acompanhamento($fl_responder = 'N')
    {
        $this->load->model('gestao/plano_acao_model');

        $cd_plano_acao      = $this->input->post('cd_plano_acao', TRUE);
        $cd_plano_acao_item = $this->input->post('cd_plano_acao_item', TRUE);           

        $row = $this->plano_acao_model->carrega($cd_plano_acao);

        $item = $this->plano_acao_model->carrega_item($cd_plano_acao_item);

        $cd_usuario = $this->session->userdata('codigo');

        if($this->get_permissao_gerencia() OR intval($item['cd_usuario_responsavel']) == $cd_usuario OR intval($item['cd_usuario_substituto']) == $cd_usuario)
        {
            $cd_plano_acao_acompanhamento = $this->input->post('cd_plano_acao_acompanhamento', TRUE);

            $args = array(
                'cd_plano_acao_item'           => $cd_plano_acao_item,             
                'cd_plano_acao_acompanhamento' => $cd_plano_acao_acompanhamento,
                'fl_status'                    => $this->input->post('fl_status', TRUE),
                'ds_acompanhamento'            => $this->input->post('ds_acompanhamento', TRUE),
                'cd_usuario'                   => $this->session->userdata('codigo')
            );

            if(intval($cd_plano_acao_acompanhamento) == 0)
            {
                $cd_plano_acao_acompanhamento = $this->plano_acao_model->salvar_acomapanhamento($args);
            }
            else
            {
                $this->plano_acao_model->atualizar_acompanhamento($cd_plano_acao_acompanhamento, $args);
            }

            if(trim($fl_responder) == 'S')
            {
                redirect('gestao/plano_acao/responder/'.intval($cd_plano_acao).'/'.intval($cd_plano_acao_item), 'refresh');
            }
            else
            {
                redirect('gestao/plano_acao/acompanhamento/'.intval($cd_plano_acao).'/'.intval($cd_plano_acao_item), 'refresh');
            }
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir_acompanhamento($cd_plano_acao,$cd_plano_acao_item, $cd_plano_acao_acompanhamento)
    {
        if($this->get_permissao_gerencia())
        {
            $this->load->model('gestao/plano_acao_model');

            $this->plano_acao_model->excluir_acompanhamento(
                $cd_plano_acao_acompanhamento, $this->session->userdata('codigo')
            );

            redirect('gestao/plano_acao/acompanhamento/'.intval($cd_plano_acao).'/'.intval($cd_plano_acao_item), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function anexo($cd_plano_acao, $cd_plano_acao_item)
    {
        if($this->get_permissao_gerencia())
        {
            $this->load->model('gestao/plano_acao_model');

            $data = array(
                'row'        => $this->plano_acao_model->carrega($cd_plano_acao),
                'itens'      => $this->plano_acao_model->carrega_item($cd_plano_acao_item),
                'collection' => $this->plano_acao_model->listar_anexo($cd_plano_acao_item),
                'ds_recomendacao' => array()
            );

            $recomendacao = $this->plano_acao_model->listar_recomendacao($data['itens']['cd_plano_acao_item']);

            foreach ($recomendacao as $key => $ds_recomendacao) 
            {
                $data['ds_recomendacao'][] = $ds_recomendacao['ds_recomendacao_item'];
            }            

            $this->load->view('gestao/plano_acao/anexo', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function minhas()
    {
        $this->load->view('gestao/plano_acao/minhas');
    }

    public function minhas_listar()
    {
        $this->load->model('gestao/plano_acao_model');

        $cd_diretoria = '';

        if(gerencia_in(array('DE')))
        {
            $cd_diretoria = $this->session->userdata('diretoria');
        }

        $args = array(
            'cd_gerencia_responsavel' => $this->session->userdata('divisao'),
            'dt_prazo_ini'            => $this->input->post('dt_prazo_ini', TRUE),
            'dt_prazo_fim'            => $this->input->post('dt_prazo_fim', TRUE),
            'nr_ano'                  => $this->input->post('nr_ano', TRUE),
            'nr_plano_acao'           => $this->input->post('nr_plano_acao', TRUE),
            'fl_status'               => $this->input->post('fl_status', TRUE),
            'fl_acao'                 => $this->input->post('fl_acao', TRUE),
            'cd_diretoria'            => $cd_diretoria,
            'cd_usuario'              => $this->session->userdata('codigo')
        );
    
        manter_filtros($args);

        $data['collection'] = $this->plano_acao_model->minhas_listar($args);

        foreach ($data['collection'] as $key => $item)
        {
           $data['collection'][$key]['ds_recomendacao'] = array();

           $recomendacao = $this->plano_acao_model->listar_recomendacao($item['cd_plano_acao_item']);

           foreach ($recomendacao as $key1 => $ds_recomendacao) 
           {
               $data['collection'][$key]['ds_recomendacao'][] = $ds_recomendacao['ds_recomendacao_item'];
           }
        }
         
        $this->load->view('gestao/plano_acao/minhas_result', $data);
    }

    public function responder($cd_plano_acao, $cd_plano_acao_item, $cd_plano_acao_acompanhamento = 0)
    {
        $this->load->model('gestao/plano_acao_model');

        $row = $this->plano_acao_model->carrega($cd_plano_acao);

        $item = $this->plano_acao_model->carrega_item($cd_plano_acao_item);

        $cd_usuario = $this->session->userdata('codigo');

        if($this->get_permissao_gerencia() OR intval($item['cd_usuario_responsavel']) == $cd_usuario OR intval($item['cd_usuario_substituto']) == $cd_usuario)
        {
            $data = array(
                'row'             => $row,
                'itens'           => $item,
                'collection'      => $this->plano_acao_model->listar_acompanhamento($cd_plano_acao_item),
                'status'          => $this->get_status(),
                'ds_recomendacao' => array()
            );

            $recomendacao = $this->plano_acao_model->listar_recomendacao($data['itens']['cd_plano_acao_item']);

            foreach ($recomendacao as $key => $ds_recomendacao) 
            {
                $data['ds_recomendacao'][] = $ds_recomendacao['ds_recomendacao_item'];
            }            

            if(intval($cd_plano_acao_acompanhamento) == 0)
            {
                $data['acompanhamento'] = array(
                    'cd_plano_acao_acompanhamento'  => '',
                    'fl_status'                     => '',
                    'ds_acompanhamento'             => ''
                );
            }    
            else
            {
                $data['acompanhamento'] = $this->plano_acao_model->carrega_acompanhamento($cd_plano_acao_acompanhamento);
            }

            $this->load->view('gestao/plano_acao/responder', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_resposta()
    {
        $this->load->model('gestao/plano_acao_model');

        $cd_plano_acao      = $this->input->post('cd_plano_acao', TRUE);
        $cd_plano_acao_item = $this->input->post('cd_plano_acao_item', TRUE);

        $row = $this->plano_acao_model->carrega($cd_plano_acao);

        $item = $this->plano_acao_model->carrega_item($cd_plano_acao_item);

        $cd_usuario = $this->session->userdata('codigo');

        if($this->get_permissao_gerencia() OR intval($item['cd_usuario_responsavel']) == $cd_usuario OR intval($item['cd_usuario_substituto']) == $cd_usuario)
        {
            $cd_plano_acao_resposta = $this->input->post('cd_plano_acao_resposta', TRUE);

            $args = array(
                'cd_plano_acao_item'     => $cd_plano_acao_item,             
                'cd_plano_acao_resposta' => $cd_plano_acao_resposta,
                'dt_prazo'               => $this->input->post('dt_prazo', TRUE),
                'ds_acao'                => $this->input->post('ds_acao', TRUE),
                'cd_usuario'             => $this->session->userdata('codigo')
            );

            $this->plano_acao_model->atualizar_prazo($cd_plano_acao_item, $args);

            if(intval($cd_plano_acao_resposta) == 0)
            {
                if(trim($args['ds_acao']) != '')
                {
                    $cd_plano_acao_resposta = $this->plano_acao_model->salvar_resposta($args);

                    $this->email_encerramento($cd_plano_acao, $cd_plano_acao_item);
                }
            }
            else
            {
                $this->plano_acao_model->atualizar_resposta($cd_plano_acao_resposta, $args);
            }

            redirect('gestao/plano_acao/responder/'.intval($cd_plano_acao).'/'.intval($cd_plano_acao_item), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function email_encerramento($cd_plano_acao, $cd_plano_acao_item)
    {
        $this->load->model(array(
            'gestao/plano_acao_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 251;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $item_plano = $this->plano_acao_model->carrega_item($cd_plano_acao_item);

        $tags = array('[NR_ITEM]','[NR_ANO_NUMERO]', '[LINK]');

        $subs = array(
            $item_plano['nr_plano_acao_item'],
            $item_plano['ds_ano_numero'], 
            site_url('gestao/plano_acao/itens/'.intval($cd_plano_acao))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Plano de Ação',
            'assunto' => str_replace($tags, $subs, $email['assunto']),
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
        
        $this->plano_acao_model->encaminhar_email($cd_plano_acao, $this->session->userdata('codigo'));
    }

    public function minhas_anexo($cd_plano_acao, $cd_plano_acao_item)
    {
        $this->load->model('gestao/plano_acao_model');

        $row = $this->plano_acao_model->carrega($cd_plano_acao);

        $item = $this->plano_acao_model->carrega_item($cd_plano_acao_item);

        $cd_usuario = $this->session->userdata('codigo');

        if($this->get_permissao_gerencia() OR intval($item['cd_usuario_responsavel']) == $cd_usuario OR intval($item['cd_usuario_substituto']) == $cd_usuario)
        {
            $data = array(
                'row'        => $row,
                'itens'      => $this->plano_acao_model->carrega_item($cd_plano_acao_item),
                'collection' => $this->plano_acao_model->listar_anexo($cd_plano_acao_item),
                'ds_recomendacao' => array()
            );

            $recomendacao = $this->plano_acao_model->listar_recomendacao($data['itens']['cd_plano_acao_item']);

            foreach ($recomendacao as $key => $ds_recomendacao) 
            {
                $data['ds_recomendacao'][] = $ds_recomendacao['ds_recomendacao_item'];
            }            

            $this->load->view('gestao/plano_acao/minhas_anexo', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_anexo($ir = 0)
    {
        $this->load->model('gestao/plano_acao_model');

        $cd_plano_acao      = $this->input->post('cd_plano_acao', TRUE);
        $cd_plano_acao_item = $this->input->post('cd_plano_acao_item', TRUE);
   
        $row = $this->plano_acao_model->carrega($cd_plano_acao);

        $item = $this->plano_acao_model->carrega_item($cd_plano_acao_item);

        $cd_usuario = $this->session->userdata('codigo');

        if($this->get_permissao_gerencia() OR intval($item['cd_usuario_responsavel']) == $cd_usuario OR intval($item['cd_usuario_substituto']) == $cd_usuario)
        {
            $qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));
            
            if($qt_arquivo > 0)
            {
                $nr_conta = 0;

                while($nr_conta < $qt_arquivo)
                {
                    $args = array();

                    $args = array(
                        'cd_plano_acao_item' => $cd_plano_acao_item,
                        'arquivo_nome'       => $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE),
                        'arquivo'            => $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE),
                        'cd_usuario'         => $this->session->userdata('codigo')
                    );
                    
                    $this->plano_acao_model->salvar_anexo($args);
                    
                    $nr_conta++;
                }
            }       
            
            if(intval($ir) == 0)
            {
                redirect('gestao/plano_acao/minhas_anexo/'.intval($cd_plano_acao).'/'.intval($cd_plano_acao_item), 'refresh');
            }
            else
            {
                redirect('gestao/plano_acao/anexo/'.intval($cd_plano_acao).'/'.intval($cd_plano_acao_item), 'refresh');
            }

        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir_anexo($cd_plano_acao, $cd_plano_acao_item, $cd_plano_acao_item_anexo)
    {
        $this->load->model('gestao/plano_acao_model');

        $row = $this->plano_acao_model->carrega($cd_plano_acao);

        $item = $this->plano_acao_model->carrega_item($cd_plano_acao_item);

        $cd_usuario = $this->session->userdata('codigo');

        if($this->get_permissao_gerencia() OR intval($item['cd_usuario_responsavel']) == $cd_usuario OR intval($item['cd_usuario_substituto']) == $cd_usuario)
        {
            $this->plano_acao_model->excluir_anexo($cd_plano_acao_item_anexo, $this->session->userdata('codigo'));
            
            redirect('gestao/plano_acao/minhas_anexo/'.intval($cd_plano_acao).'/'.intval($cd_plano_acao_item), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function recomendacao($cd_plano_acao, $cd_plano_acao_item, $cd_plano_acao_item_recomendacao = 0)
    {
        $this->load->model('gestao/plano_acao_model');

        $row = $this->plano_acao_model->carrega($cd_plano_acao);

        $item = $this->plano_acao_model->carrega_item($cd_plano_acao_item);

        $cd_usuario = $this->session->userdata('codigo');

        if($this->get_permissao_gerencia() OR intval($item['cd_usuario_responsavel']) == $cd_usuario OR intval($item['cd_usuario_substituto']) == $cd_usuario)
        {
            $data = array(
                'row'             => $row,
                'itens'           => $this->plano_acao_model->carrega_item($cd_plano_acao_item),
                'collection'      => $this->plano_acao_model->listar_recomendacao($cd_plano_acao_item),
                'status'     => $this->get_status()
            );

            if(intval($cd_plano_acao_item_recomendacao) == 0)
            {
                $row = $this->plano_acao_model->get_nr_recomendacao($cd_plano_acao_item);

                $data['recomendacao'] = array(
                    'cd_plano_acao_item_recomendacao' => $cd_plano_acao_item_recomendacao,
                    'nr_plano_acao_item_recomendacao' => (isset($row['nr_plano_acao_item_recomendacao']) ? intval($row['nr_plano_acao_item_recomendacao']) : 1),
                    'ds_recomendacao'                 => ''
                );
            }
            else    
            {
                $data['recomendacao'] = $this->plano_acao_model->carrega_recomendacao($cd_plano_acao_item_recomendacao);
            }

            $this->load->view('gestao/plano_acao/recomendacao', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_recomendacao()
    {
        $this->load->model('gestao/plano_acao_model');

        $cd_plano_acao      = $this->input->post('cd_plano_acao', TRUE);
        $cd_plano_acao_item = $this->input->post('cd_plano_acao_item', TRUE);   

        $row = $this->plano_acao_model->carrega($cd_plano_acao);

        $item = $this->plano_acao_model->carrega_item($cd_plano_acao_item);

        $cd_usuario = $this->session->userdata('codigo');

        if($this->get_permissao_gerencia() OR intval($item['cd_usuario_responsavel']) == $cd_usuario OR intval($item['cd_usuario_substituto']) == $cd_usuario)
        {       
            $cd_plano_acao_item_recomendacao = $this->input->post('cd_plano_acao_item_recomendacao', TRUE);

            $args = array(
                'cd_plano_acao_item_recomendacao' => $cd_plano_acao_item_recomendacao,
                'nr_plano_acao_item_recomendacao' => $this->input->post('nr_plano_acao_item_recomendacao', TRUE),
                'ds_recomendacao'                 => $this->input->post('ds_recomendacao', TRUE),
                'cd_usuario'                      => $this->session->userdata('codigo')
            );

            if(intval($cd_plano_acao_item_recomendacao) == 0)
            {
                $cd_plano_acao_item_recomendacao = $this->plano_acao_model->salvar_recomendacao($cd_plano_acao_item,$args);
            }
            else
            {
                $this->plano_acao_model->atualizar_acompanhamento($cd_plano_acao_item_recomendacao, $args);
            }

            redirect('gestao/plano_acao/recomendacao/'.intval($cd_plano_acao).'/'.intval($cd_plano_acao_item), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir_recomendacao($cd_plano_acao,$cd_plano_acao_item, $cd_plano_acao_item_recomendacao)
    {
        if($this->get_permissao_gerencia())
        {
            $this->load->model('gestao/plano_acao_model');

            $this->plano_acao_model->excluir_recomendacao(
                $cd_plano_acao_item_recomendacao, $this->session->userdata('codigo')
            );

            redirect('gestao/plano_acao/recomendacao/'.intval($cd_plano_acao).'/'.intval($cd_plano_acao_item), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get_usuarios()
    {
        $this->load->model('gestao/plano_acao_model');

        $cd_gerencia = $this->input->post('cd_gerencia', TRUE);

        foreach($this->plano_acao_model->get_usuarios($cd_gerencia) as $item)
        {
            $data['usuarios'][] = array(
                'value' => $item['value'],
                'text'  => utf8_encode($item['text'])
            );
        }

        $row = $this->plano_acao_model->get_gerente($cd_gerencia);
        $data['responsavel'] = $row['cd_usuario'];

        $row = $this->plano_acao_model->get_substituto($cd_gerencia);
        $data['substituto'] = $row['cd_usuario'];

        echo json_encode($data);
    }

}
?>
