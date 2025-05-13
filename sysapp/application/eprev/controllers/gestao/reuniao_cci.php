<?php

class reuniao_cci extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
        $this->load->model('gestao/reuniao_cci_model');
    }
	
	public function index()
    {
		if (gerencia_in(array('GIN')))
        {
			$args = Array();
			$data = Array();
			$result = null;
            
            $this->reuniao_cci_model->tipo($result, $args);
            $data['arr_tipo'] = $result->result_array();

			$this->load->view('gestao/reuniao_cci/index', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function listar()
    {
		if (gerencia_in(array('GIN')))
        {
			$args = Array();
			$data = Array();
			$result = null;
            
            $args['nr_ano']              = $this->input->post("nr_ano", TRUE);
            $args['nr_numero']           = $this->input->post("nr_numero", TRUE);
            $args['dt_ini']              = $this->input->post("dt_ini", TRUE);
            $args['dt_fim']              = $this->input->post("dt_fim", TRUE);
            $args['fl_status']           = $this->input->post("fl_status", TRUE);
            $args['cd_reuniao_cci_tipo'] = $this->input->post("cd_reuniao_cci_tipo", TRUE);
							
			manter_filtros($args);

			$this->reuniao_cci_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('gestao/reuniao_cci/index_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function cadastro($cd_reuniao_cci = 0)
	{
        if (gerencia_in(array('GIN')))
        {
            $result   = null;
            $data     = array();
            $args     = array();

            $args['cd_reuniao_cci'] = $cd_reuniao_cci;

            if((intval($args['cd_reuniao_cci']) == 0) )
            {	
                $data['row'] = array(
                    'cd_reuniao_cci'              => intval($args['cd_reuniao_cci']),
                    'nr_ano'                      => '',
                    'nr_numero'                   => '',
                    'dt_reuniao_cci'              => '',
                    'hr_reuniao_cci'              => '',
                    'cd_reuniao_cci_tipo'         => '',
                    'cd_reuniao_cci_local'        => '',
                    'cd_usuario_coordenador_cci'  => '',
                    'cd_gerencia_coordenador_cci' => '',
                    'dt_enviado'                  => '',
                    'dt_aprovado'                 => '',
                    'dt_desaprovado'              => '',
                    'qt_membro_efetivo'           => 0,
                    'qt_convidado'                => 0,
                    'qt_pauta'                    => 0
                );
            }
            else
            {
                $this->reuniao_cci_model->carrega($result, $args);
                $data['row'] = $result->row_array();
                
                $this->reuniao_cci_model->listar_membro_efetivo($result, $args);
                $data['collection_membro_efetivo'] = $result->result_array();
                
                $this->reuniao_cci_model->listar_convidado($result, $args);
                $data['collection_convidado'] = $result->result_array();
                
                $this->reuniao_cci_model->listar_pauta($result, $args);
                $data['collection_pauta'] = $result->result_array();
            }
         
            $this->load->view('gestao/reuniao_cci/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function salvar()
	{
		if (gerencia_in(array('GIN')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_reuniao_cci']             = $this->input->post("cd_reuniao_cci", TRUE);
            $args['nr_ano']                     = $this->input->post("nr_ano", TRUE);
            $args['nr_numero']                  = $this->input->post("nr_numero", TRUE);
            $args['dt_reuniao_cci']             = $this->input->post("dt_reuniao_cci", TRUE);
            $args['hr_reuniao_cci']             = $this->input->post("hr_reuniao_cci", TRUE);
            $args['cd_reuniao_cci_tipo']        = $this->input->post("cd_reuniao_cci_tipo", TRUE);
            $args['cd_reuniao_cci_local']       = $this->input->post("cd_reuniao_cci_local", TRUE);
            $args['cd_usuario_coordenador_cci'] = $this->input->post("cd_usuario_coordenador_cci", TRUE);
			$args['cd_usuario']                 = $this->session->userdata("codigo");
			
			$cd_reuniao_cci = $this->reuniao_cci_model->salvar($result, $args);
			
			redirect("gestao/reuniao_cci/cadastro/".intval($cd_reuniao_cci), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    public function salvar_membro_efetivo($cd_reuniao_cci, $cd_reuniao_cci_membro_efetivo)
    {
        if (gerencia_in(array('GIN')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_reuniao_cci']                = $cd_reuniao_cci;
            $args['cd_reuniao_cci_membro_efetivo'] = $cd_reuniao_cci_membro_efetivo;
			$args['cd_usuario']                    = $this->session->userdata("codigo");
			
			$this->reuniao_cci_model->salvar_membro_efetivo($result, $args);
			
			redirect("gestao/reuniao_cci/cadastro/".intval($cd_reuniao_cci), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    public function excluir_membro_efetivo($cd_reuniao_cci, $cd_reuniao_cci_membro_efetivo_item)
    {
        if (gerencia_in(array('GIN')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_reuniao_cci']                     = $cd_reuniao_cci;
            $args['cd_reuniao_cci_membro_efetivo_item'] = $cd_reuniao_cci_membro_efetivo_item;
			$args['cd_usuario']                         = $this->session->userdata("codigo");
			
			$this->reuniao_cci_model->excluir_membro_efetivo($result, $args);
			
			redirect("gestao/reuniao_cci/cadastro/".intval($cd_reuniao_cci), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    public function salvar_convidado($cd_reuniao_cci, $cd_reuniao_cci_convidado)
    {
        if (gerencia_in(array('GIN')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_reuniao_cci']           = $cd_reuniao_cci;
            $args['cd_reuniao_cci_convidado'] = $cd_reuniao_cci_convidado;
			$args['cd_usuario']               = $this->session->userdata("codigo");
			
			$this->reuniao_cci_model->salvar_convidado($result, $args);
			
			redirect("gestao/reuniao_cci/cadastro/".intval($cd_reuniao_cci), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    public function excluir_convidado($cd_reuniao_cci, $cd_reuniao_cci_convidado_item)
    {
        if (gerencia_in(array('GIN')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_reuniao_cci']                = $cd_reuniao_cci;
            $args['cd_reuniao_cci_convidado_item'] = $cd_reuniao_cci_convidado_item;
			$args['cd_usuario']                    = $this->session->userdata("codigo");
			
			$this->reuniao_cci_model->excluir_convidado($result, $args);
			
			redirect("gestao/reuniao_cci/cadastro/".intval($cd_reuniao_cci), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    public function salvar_pauta()
    {
        if (gerencia_in(array('GIN')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_reuniao_cci']       = $this->input->post("cd_reuniao_cci", TRUE);
            $args['ds_reuniao_cci_pauta'] = utf8_decode($this->input->post("ds_reuniao_cci_pauta", TRUE));
			$args['cd_usuario']           = $this->session->userdata("codigo");
			
			$this->reuniao_cci_model->salvar_pauta($result, $args);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    public function excluir_pauta($cd_reuniao_cci, $cd_reuniao_cci_pauta)
    {
        if (gerencia_in(array('GIN')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_reuniao_cci']       = $cd_reuniao_cci;
            $args['cd_reuniao_cci_pauta'] = $cd_reuniao_cci_pauta;
			$args['cd_usuario']           = $this->session->userdata("codigo");
			
			$this->reuniao_cci_model->excluir_pauta($result, $args);
			
			redirect("gestao/reuniao_cci/cadastro/".intval($cd_reuniao_cci), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
	public function enviar($cd_reuniao_cci)
	{
		if (gerencia_in(array('GIN')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_reuniao_cci'] = $cd_reuniao_cci;
			$args['cd_usuario']     = $this->session->userdata("codigo");
			
			$this->reuniao_cci_model->enviar($result, $args);
			
			redirect("gestao/reuniao_cci/cadastro/".intval($cd_reuniao_cci), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    public function minhas()
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $this->reuniao_cci_model->tipo($result, $args);
        $data['arr_tipo'] = $result->result_array();

        $this->load->view('gestao/reuniao_cci/minhas', $data);
    }
    
    public function listar_minhas()
    {
		if (gerencia_in(array('GIN')))
        {
			$args = Array();
			$data = Array();
			$result = null;
            
            $args['nr_ano']              = $this->input->post("nr_ano", TRUE);
            $args['nr_numero']           = $this->input->post("nr_numero", TRUE);
            $args['dt_ini']              = $this->input->post("dt_ini", TRUE);
            $args['dt_fim']              = $this->input->post("dt_fim", TRUE);
            $args['fl_status']           = $this->input->post("fl_status", TRUE);
            $args['cd_reuniao_cci_tipo'] = $this->input->post("cd_reuniao_cci_tipo", TRUE);
							
			manter_filtros($args);
            
            $args['cd_usuario'] = $this->session->userdata("codigo");

			$this->reuniao_cci_model->listar_minhas($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('gestao/reuniao_cci/minhas_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    public function responder($cd_reuniao_cci)
    {
        $result   = null;
        $data     = array();
        $args     = array();

        $args['cd_reuniao_cci'] = $cd_reuniao_cci;

        $this->reuniao_cci_model->carrega($result, $args);
        $data['row'] = $result->row_array();

        $this->reuniao_cci_model->listar_pauta($result, $args);
        $data['collection'] = $result->result_array();

        $this->load->view('gestao/reuniao_cci/responder', $data);
    }
    
    public function aprovar()
    {
        $result   = null;
        $data     = array();
        $args     = array();

        $args['cd_reuniao_cci']    = $this->input->post("cd_reuniao_cci", TRUE);
        $args['reuniao_cci_pauta'] = $this->input->post("reuniao_cci_pauta", TRUE);
        $args['cd_usuario']        = $this->session->userdata("codigo");
       
        $this->reuniao_cci_model->aprovar($result, $args);
    }
    
    public function desaprovar()
    {
        $result   = null;
        $data     = array();
        $args     = array();

        $args['cd_reuniao_cci'] = $this->input->post("cd_reuniao_cci", TRUE);
        $args['cd_usuario']     = $this->session->userdata("codigo");
       
        $this->reuniao_cci_model->desaprovar($result, $args);
    }
    
    public function pdf($cd_reuniao_cci)
    {
        $result = null;
        $data   = array();
        $args   = array();

        $args['cd_reuniao_cci'] = $cd_reuniao_cci;

        $this->reuniao_cci_model->carrega($result, $args);
        $row = $result->row_array();
        
        $this->reuniao_cci_model->listar_membro_efetivo($result, $args);
        $collection_membro_efetivo = $result->result_array();

        $this->reuniao_cci_model->listar_convidado($result, $args);
        $collection_convidado = $result->result_array();
        
        $this->reuniao_cci_model->listar_pauta_aprovada($result, $args);
        $collection_pauta = $result->result_array();
        
        $this->load->plugin('fpdf');
        $ob_pdf = new PDF('P', 'mm', 'A4');
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10, 14, 5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;	
        
        $font_1 = 12;
        $font_2 = 11;
        
        $ob_pdf->AddPage();
        $ob_pdf->SetY($ob_pdf->GetY()+2);

        $ob_pdf->SetFont('segoeuib','',$font_1 + 8);
        $ob_pdf->MultiCell(190, 5, "Reunião CCI", '0', 'C');
        
        $ob_pdf->SetY($ob_pdf->GetY()+6);
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Ano/Número:", '0', 'L');
		$ob_pdf->SetFont('segoeuil','',$font_2);
		$ob_pdf->MultiCell(190, 5, $row['ano_numero'], '0', 'L');
        
        $ob_pdf->SetY($ob_pdf->GetY()+6);
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Tipo:", '0', 'L');
		$ob_pdf->SetFont('segoeuil','',$font_2);
		$ob_pdf->MultiCell(190, 5, $row['ds_reuniao_cci_tipo'], '0', 'L');
        
        $ob_pdf->SetY($ob_pdf->GetY()+6);
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Data:", '0', 'L');
		$ob_pdf->SetFont('segoeuil','',$font_2);
		$ob_pdf->MultiCell(190, 5, $row['dt_reuniao_cci'], '0', 'L');
        
        $ob_pdf->SetY($ob_pdf->GetY()+6);
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Hora:", '0', 'L');
		$ob_pdf->SetFont('segoeuil','',$font_2);
		$ob_pdf->MultiCell(190, 5, $row['hr_reuniao_cci'], '0', 'L');
        
        $ob_pdf->SetY($ob_pdf->GetY()+6);
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Local:", '0', 'L');
		$ob_pdf->SetFont('segoeuil','',$font_2);
		$ob_pdf->MultiCell(190, 5, $row['ds_reuniao_cci_local'], '0', 'L');
        
        $ob_pdf->SetY($ob_pdf->GetY()+6);
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Coordenador CCI:", '0', 'L');
		$ob_pdf->SetFont('segoeuil','',$font_2);
		$ob_pdf->MultiCell(190, 5, $row['usuario_coordenador_cci'], '0', 'L');
        
        $ob_pdf->SetY($ob_pdf->GetY()+6);
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Membros Efetivos:", '0', 'L');
        
        foreach($collection_membro_efetivo as $item)
        {
            $ob_pdf->SetFont('segoeuil','',$font_2);
            $ob_pdf->MultiCell(190, 5, ' - '.$item['ds_reuniao_cci_membro_efetivo'], '0', 'L');
        }
        
        $ob_pdf->SetY($ob_pdf->GetY()+6);
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Convidados:", '0', 'L');
        
        foreach($collection_convidado as $item)
        {
            $ob_pdf->SetFont('segoeuil','',$font_2);
            $ob_pdf->MultiCell(190, 5, ' - '.$item['ds_reuniao_cci_convidado'], '0', 'L');
        }
        
        $ob_pdf->SetY($ob_pdf->GetY()+6);
		$ob_pdf->SetFont('segoeuib','',$font_1);
		$ob_pdf->MultiCell(190, 5, "Pauta:", '0', 'L');
        
        foreach($collection_pauta as $item)
        {
            $ob_pdf->SetFont('segoeuil','',$font_2);
            $ob_pdf->MultiCell(190, 5, ' - '.$item['ds_reuniao_cci_pauta'], '0', 'L');
        }
        
        $ob_pdf->Output();
    }
    
    
}
?>