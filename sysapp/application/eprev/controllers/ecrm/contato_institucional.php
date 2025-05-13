<?php

class contato_institucional extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model('projetos/contato_institucional_model');
    }
    
    function index()
    {
        if (gerencia_in(array('SG', 'GRI')))
        {
            $args = array();
            $data = array();
            $result = null;
            
            $this->contato_institucional_model->combo_tipo($result, $args);
            $data['arr_tipo'] = $result->result_array();
            
            $this->contato_institucional_model->combo_empresa($result, $args);
            $data['arr_empresa'] = $result->result_array();
            
            $this->contato_institucional_model->combo_cargo($result, $args);
            $data['arr_cargo'] = $result->result_array();

            $this->load->view('ecrm/contato_institucional/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function listar()
    {
        if (gerencia_in(array('SG', 'GRI')))
        {
            $args = array();
            $data = array();
            $result = null;
            
            $args['cd_contato_institucional_tipo']    = $this->input->post("cd_contato_institucional_tipo", TRUE);
            $args['cd_contato_institucional_empresa'] = $this->input->post("cd_contato_institucional_empresa", TRUE);
            $args['cd_contato_institucional_cargo']   = $this->input->post("cd_contato_institucional_cargo", TRUE);
            $args['nome']                             = $this->input->post("nome", TRUE);
            $args['sec_nome']                         = $this->input->post("sec_nome", TRUE);
            
            manter_filtros($args);
            
            $this->contato_institucional_model->listar($result, $args);
            $data['collection'] = $result->result_array();
            
            $this->load->view('ecrm/contato_institucional/partial_result', $data);
            
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }    
    }
    
    function cadastro($cd_contato_institucional = 0)
    {
        if (gerencia_in(array('SG', 'GRI')))
        {
            $args = array();
            $data = array();
            $result = null;
            
            $data['cd_contato_institucional'] = intval($cd_contato_institucional);
            $args['cd_contato_institucional'] = intval($cd_contato_institucional);
            
            $this->contato_institucional_model->combo_uf($result, $args);
            $data['arr_uf'] = $result->result_array();
            
            if ($cd_contato_institucional == 0)
            {
                $data['row'] = Array(
                  'cd_contato_institucional' => 0,
                  'cd_contato_institucional_tipo' => '',
                  'cd_contato_institucional_empresa' => '',
                  'cd_contato_institucional_cargo' => '',
                  'nome' => '',
                  'telefone_1' => '',
                  'telefone_2' => '',
                  'email_1' => '',
                  'email_2' => '',
                  'cep' => '',
                  'logradouro' => '',
                  'numero' => '',
                  'complemento' => '',
                  'cidade' => '',
                  'bairro' => '',
                  'uf' => '',
                  'sec_nome' => '',
                  'sec_telefone_1' => '',
                  'sec_telefone_2' => '',
                  'sec_email_1' => '',
                  'sec_email_2' => '',
                );
            }
            else
            {
                $this->contato_institucional_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }
            
            $this->load->view('ecrm/contato_institucional/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar()
    {
        if (gerencia_in(array('SG', 'GRI')))
        {
            $args = array();
            $data = array();
            $result = null;
                        
            $args['cd_contato_institucional'] = $this->input->post("cd_contato_institucional", TRUE);
            $args['cd_contato_institucional_tipo'] = $this->input->post("cd_contato_institucional_tipo", TRUE);
            $args['cd_contato_institucional_empresa'] = $this->input->post("cd_contato_institucional_empresa", TRUE);
            $args['cd_contato_institucional_cargo'] = $this->input->post("cd_contato_institucional_cargo", TRUE);
            $args['nome'] = $this->input->post("nome", TRUE);
            $args['telefone_1'] = $this->input->post("telefone_1", TRUE);
            $args['telefone_2'] = $this->input->post("telefone_2", TRUE);
            $args['email_1'] = $this->input->post("email_1", TRUE);
            $args['email_2'] = $this->input->post("email_2", TRUE);
            $args['cep'] = $this->input->post("cep", TRUE);
            $args['logradouro'] = $this->input->post("logradouro", TRUE);
            $args['numero'] = $this->input->post("numero", TRUE);
            $args['complemento'] = $this->input->post("complemento", TRUE);
            $args['cidade'] = $this->input->post("cidade", TRUE);
            $args['bairro'] = $this->input->post("bairro", TRUE);
            $args['uf'] = $this->input->post("uf", TRUE);
            $args['sec_nome'] = $this->input->post("sec_nome", TRUE);
            $args['sec_telefone_1'] = $this->input->post("sec_telefone_1", TRUE);
            $args['sec_telefone_2'] = $this->input->post("sec_telefone_2", TRUE);
            $args['sec_email_1'] = $this->input->post("sec_email_1", TRUE);
            $args['sec_email_2'] = $this->input->post("sec_email_2", TRUE);
            $args['cd_usuario'] = $this->session->userdata('codigo');
            
            $this->contato_institucional_model->salvar($result, $args);

            redirect("ecrm/contato_institucional/" , "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function excluir($cd_contato_institucional)
    {
        $args = array();
        $data = array();
        $result = null;
        
        $args['cd_contato_institucional'] = $cd_contato_institucional;
        $args['cd_usuario'] = $this->session->userdata('codigo');
        
        $this->contato_institucional_model->excluir($result, $args);
        
        redirect("ecrm/contato_institucional/" , "refresh");
    }
	
	function etiquetas()
    {
        if (gerencia_in(array('SG', 'GRI')))
        {
            $args = array();
            $data = array();
            $result = null;
            
            $args['cd_contato_institucional_tipo']    = $this->input->post("cd_contato_institucional_tipo", TRUE);
            $args['cd_contato_institucional_empresa'] = $this->input->post("cd_contato_institucional_empresa", TRUE);
            $args['cd_contato_institucional_cargo']   = $this->input->post("cd_contato_institucional_cargo", TRUE);
            $args['nome']                             = $this->input->post("nome", TRUE);
            $args['sec_nome']                         = $this->input->post("sec_nome", TRUE);
			
			$nr_etiqueta_ini                          = $this->input->post("nr_etiqueta_ini", TRUE);
                        
            $this->contato_institucional_model->listar($result, $args);
            $collection = $result->result_array();
            
			$this->load->plugin('fpdf');
			
            $fpdf = new PDF('P','mm','Letter'); 
			$fpdf->AddFont('ECTSymbol');
			$fpdf->SetMargins(5,14,5);
			$fpdf->AddPage();

			$vl_x = 69.5;
			$vl_y = 25.5;
			$nr_x = 0;
			$nr_y = 0;
			$nr_conta = 0;
			$nr_conta_x = 0;
			
			$ar_pos_etiqueta[0] = array(1,4,7,10,13,16,19,22,25,28);
			$ar_pos_etiqueta[1] = array(2,5,8,11,14,17,20,23,26,29);
			$ar_pos_etiqueta[2] = array(3,6,9,12,15,18,21,24,27,30);

			
			if (in_array(intval($nr_etiqueta_ini), $ar_pos_etiqueta[0]))
			{
				#COLUNA 1
				$nr_x = 0;
				$nr_conta_x = 0;
				
				$nr_y = $vl_y * intval(array_search(intval($nr_etiqueta_ini), $ar_pos_etiqueta[0]));
				$nr_conta = (intval($nr_etiqueta_ini) - 1);
			}
			else if (in_array(intval($nr_etiqueta_ini), $ar_pos_etiqueta[1]))
			{
				#COLUNA 2
				$nr_x = $vl_x;
				$nr_conta_x = 1;

				$nr_y = $vl_y * intval(array_search(intval($nr_etiqueta_ini), $ar_pos_etiqueta[1]));
				$nr_conta = (intval($nr_etiqueta_ini) - 1);
			}
			else if (in_array(intval($nr_etiqueta_ini), $ar_pos_etiqueta[2]))
			{
				#COLUNA 3
				$nr_x = $vl_x * 2;
				$nr_conta_x = 2;
				
				$nr_y = $vl_y * intval(array_search(intval($nr_etiqueta_ini), $ar_pos_etiqueta[2]));
				$nr_conta = (intval($nr_etiqueta_ini) - 1);
			}	
			
			foreach($collection as $item)
			{
				$fpdf->SetXY($fpdf->GetX() + $nr_x, $fpdf->GetY() + $nr_y);
				$fpdf->SetFont('ECTSymbol','',16);
				$fpdf->Text($fpdf->GetX() + (33.5 - ($fpdf->GetStringWidth($item['etiq_cep_net'])/2)),$fpdf->GetY() + 5.5 , $item['etiq_cep_net']);
				$fpdf->SetFont('Courier','B',8);
				$fpdf->Text($fpdf->GetX() + 1.75,$fpdf->GetY() + 9, $item['etiq_nome']);	
				$fpdf->SetFont('Courier','',8);
				$fpdf->Text($fpdf->GetX() + 1.75,$fpdf->GetY() + 12, $item['etiq_endereco']);	
				$fpdf->Text($fpdf->GetX() + 1.75,$fpdf->GetY() + 15, $item['etiq_nr_complemento']);	
				$fpdf->Text($fpdf->GetX() + 1.75,$fpdf->GetY() + 18, $item['etiq_localidade']);	
				$fpdf->Text($fpdf->GetX() + 1.75,$fpdf->GetY() + 21, $item['etiq_cep']);	
				
				$nr_conta++;
				$nr_conta_x++;
				
				if($nr_conta_x == 3)
				{
					$fpdf->SetX(5);
					$nr_x = 0;
					$nr_y = $vl_y;
					$nr_conta_x = 0;
				}
				else
				{
					$nr_x = $vl_x;
					$nr_y = 0;
				}

				if($nr_conta == 30)
				{
					$fpdf->AddPage();
					$fpdf->SetMargins(5,14,5);
					$nr_conta = 0;
					$nr_x = 0;
					$nr_y = 0;
				}
				
			}

			$fpdf->Output();
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }    
    }
}

?>