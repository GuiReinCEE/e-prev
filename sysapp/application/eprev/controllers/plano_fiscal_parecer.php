<?php

class Plano_fiscal_parecer extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        $this->load->model('gestao/plano_fiscal_parecer_model');
    }
    
    public function index()
    {
        if (gerencia_in(array('GC')))
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
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;

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
        if (gerencia_in(array('GC')))
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
        if (gerencia_in(array('GC')))
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
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_plano_fiscal_parecer']      = $this->input->post("cd_plano_fiscal_parecer", TRUE);
            $args['cd_plano_fiscal_parecer_item'] = $this->input->post("cd_plano_fiscal_parecer_item", TRUE);
            $args['cd_responsavel']                 = $this->input->post("usuario", TRUE);
            $args['cd_gerencia']                    = $this->input->post("usuario_gerencia", TRUE);
            $args['descricao']                      = $this->input->post("descricao", TRUE);
            $args['nr_item']                        = $this->input->post("nr_item", TRUE);
            $args['cd_usuario']                     = $this->session->userdata('codigo');
            $args['cd_gerente']                     = $this->input->post("responsavel", TRUE);
            $args['cd_gerencia_gerente']            = $this->input->post("responsavel_gerencia", TRUE);
            $args['parecer']                        = $this->input->post("parecer", TRUE);

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
        if (gerencia_in(array('GC')))
        {
            $args = Array();
            $data = Array();
            $result = null;
            
            $args['cd_plano_fiscal_parecer'] = $this->input->post("cd_plano_fiscal_parecer", TRUE);

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
        if (gerencia_in(array('GC')))
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
    
    function enviar($cd_plano_fiscal_parecer, $cd_plano_fiscal_parecer_item)
    {
        if (gerencia_in(array('GC')))
        {
            $args['cd_plano_fiscal_parecer']      = $cd_plano_fiscal_parecer;
            $args['cd_plano_fiscal_parecer_item'] = $cd_plano_fiscal_parecer_item;
            $args['cd_usuario']                   = $this->session->userdata('codigo');
            
            $this->plano_fiscal_parecer_model->carrega_item($result, $args);
            $row = $result->row_array();
            
            $args['dia_limite'] = 15;
            
            if(trim($row['cd_gerencia']) == 'GA')
            {
                $args['dia_limite'] = 20;
            }

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
        if (gerencia_in(array('GC')))
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
        if (gerencia_in(array('GC')))
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
    
    function enviar_todos($cd_plano_fiscal_parecer)
    {
        if (gerencia_in(array('GC')))
        {
            $args['cd_plano_fiscal_parecer']  = $cd_plano_fiscal_parecer;
            $args['cd_usuario']     = $this->session->userdata('codigo');

            $this->plano_fiscal_parecer_model->listar_itens($result, $args);
            $arr = $result->result_array();
            
            foreach ($arr as $item)
            {
                $args['dia_limite'] = 15;
            
                if(trim($item['cd_gerencia']) == 'GA')
                {
                    $args['dia_limite'] = 20;
                }
                
                $args['cd_plano_fiscal_parecer_item'] = $item['cd_plano_fiscal_parecer_item'];
                
                $this->plano_fiscal_parecer_model->enviar($result, $args);
            }

            redirect("gestao/plano_fiscal_parecer/cadastro/".$cd_plano_fiscal_parecer, "refresh");
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
        
        $this->plano_fiscal_parecer_model->carrega_sumula_item_resposta($result, $args);
        
        $data['row'] = $result->row_array();
        
        if($data['row']['cd_responsavel'] == $args['cd_usuario'] OR $data['row']['cd_gerente'] == $args['cd_usuario'])
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
    
    function confirmar($cd_plano_fiscal_parecer_item)
    {
        $args = Array();
        $data = Array();
        $result = null; 
        
        $args['cd_plano_fiscal_parecer_item'] = $cd_plano_fiscal_parecer_item;
        $args['cd_usuario']     = $this->session->userdata('codigo');
        
        $this->plano_fiscal_parecer_model->confirmar($result, $args);
        
        redirect("gestao/plano_fiscal_parecer/resposta/".$args['cd_plano_fiscal_parecer_item'], "refresh");
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
    
    
    function imprimir()
    {
        if (gerencia_in(array('GC')))
        {
            $args['cd_plano_fiscal_parecer'] = $this->input->post("cd_plano_fiscal_parecer", TRUE);

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
                $status = "";
                $args['cd_usuario'] = $item['cd_gerente'];
                
                $this->plano_fiscal_parecer_model->get_assinatura($result, $args);
                $assinatura = $result->row_array();
                
                if(trim($assinatura['assinatura']) != '')
                {
                    list($width, $height) = getimagesize('./img/assinatura/'.$assinatura['assinatura']);   
                    
                }
                
                if($item['fl_status'] == 'S')
                {
                    $status = 'ATENDE';
                }
                else if($item['fl_status'] == 'J')
                {
                    $status = 'JUSTIFICATIVA';  
                }
                else
                {
                    $status = "NÃO INFORMADO";
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
                  (trim($item['dt_confirmacao']) != '' ? $item['cd_gerencia'] : ''),
                  (trim($item['dt_confirmacao']) != '' ? (trim($assinatura['assinatura']) != '' ? "\n\n" : '') .$item['gerente'] : '')));	
   
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