<?php
class ri_torcida_torcedor extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		if(CheckLogin())
		{
	        $this->load->view('ecrm/ri_torcida_torcedor/index.php');
		}
    }

    function listar()
    {
		if(CheckLogin())
        {
	        $this->load->model('torcida/Torcedor_model');
	
	        $data['collection'] = array();
	        $result = null;
	
	        // --------------------------
			// filtros ...
	
			$args=array();
			
			$args['fl_brinde']       = $this->input->post('fl_brinde');
			$args['fl_liberado']     = $this->input->post('fl_liberado');
			$args['dt_inclusao_ini'] = $this->input->post('dt_inclusao_ini');			
			$args['dt_inclusao_fim'] = $this->input->post('dt_inclusao_fim');			
	
			manter_filtros($args);
	
			// --------------------------
			// listar ...
	
	        $this->Torcedor_model->listar( $result, $args );
	
			$data['collection'] = $result->result_array();
	
	        if( $result )
	        {
	            $data['collection'] = $result->result_array();
	        }
	
	        // --------------------------
	
	        $this->load->view('ecrm/ri_torcida_torcedor/partial_result', $data);
        }
    }
	
    function etiqueta()
    {
        if(CheckLogin())
        {
	        
			$this->load->model('torcida/Torcedor_model');
	
	        $data['collection'] = array();
	        $result = null;
			$args=array();
			
			$args['fl_brinde']       = $this->input->post('fl_brinde');
			$args['fl_liberado']     = $this->input->post('fl_liberado');
			$args['dt_inclusao_ini'] = $this->input->post('dt_inclusao_ini');			
			$args['dt_inclusao_fim'] = $this->input->post('dt_inclusao_fim');			
	
			manter_filtros($args);
	
	        $this->Torcedor_model->etiqueta( $result, $args );
	
			$data['collection'] = $result->result_array();
   
			$this->load->plugin('fpdf');
			
			$ob_pdf = new PDF('P','mm','Letter'); 
			$ob_pdf->SetMargins(5,14,5);
			$ob_pdf->AddPage();
			$ob_pdf->AddFont('ECTSymbol');
					
			$nr_x = 0;
			$nr_y = 0;
			$nr_conta = 0;
			$nr_conta_x = 0;
			foreach( $data['collection'] as $ar_reg )
			{
				$ob_pdf->SetXY($ob_pdf->GetX() + $nr_x, $ob_pdf->GetY() + $nr_y);
				$ob_pdf->SetFont('ECTSymbol','',16);
				$ob_pdf->Text($ob_pdf->GetX() + 4,$ob_pdf->GetY() + 6.7, $ar_reg['cep_net']);	
				
				$ob_pdf->SetFont('Courier','',7);
				$ob_pdf->Text($ob_pdf->GetX() + 4,$ob_pdf->GetY() + 9.5, $ar_reg['nome']);	
				$re = $ar_reg['cd_empresa']." ".$ar_reg['cd_registro_empregado']." ".$ar_reg['seq_dependencia'];
				$ob_pdf->Text($ob_pdf->GetX() + (62 - $ob_pdf->GetStringWidth($re)),$ob_pdf->GetY() + 9.5, $re);	
				$ob_pdf->Text($ob_pdf->GetX() + 4,$ob_pdf->GetY() + 13, $ar_reg['logradouro']);	
				$ob_pdf->Text($ob_pdf->GetX() + 4,$ob_pdf->GetY() + 16.5, $ar_reg['bairro']);	
				$cidade = $ar_reg['cidade']." ".$ar_reg['uf'];
				$ob_pdf->Text($ob_pdf->GetX() + (62 - $ob_pdf->GetStringWidth($cidade)),$ob_pdf->GetY() + 16.5, $cidade);	
				$ob_pdf->Text($ob_pdf->GetX() + 4,$ob_pdf->GetY() + 20, $ar_reg['cep']);	
				
				$nr_conta++;
				$nr_conta_x++;
				
				if($nr_conta_x == 3)
				{
					$ob_pdf->SetX(5);
					$nr_x = 0;
					$nr_y = 25.5;
					$nr_conta_x = 0;
				}
				else
				{
					$nr_x = 68.5;
					$nr_y = 0;
				}

				if($nr_conta == 30)
				{
					$ob_pdf->AddPage();
					$ob_pdf->SetMargins(5,14,5);
					$nr_conta = 0;
					$nr_x = 0;
					$nr_y = 0;
				}
			}
			$ob_pdf->Output();
			
			$args=array();
			
			$args['fl_brinde']       = $this->input->post('fl_brinde');
			$args['fl_liberado']     = $this->input->post('fl_liberado');
			$args['dt_inclusao_ini'] = $this->input->post('dt_inclusao_ini');			
			$args['dt_inclusao_fim'] = $this->input->post('dt_inclusao_fim');			
			$args['usuario']         = $this->session->userdata('usuario');		
	
			$msg=array();
			$b = $this->Torcedor_model->etiquetaMarca($args);		
        }		
    }	
	
	function liberar()
	{
		if(CheckLogin())
		{
			$this->load->model( 'torcida/Torcedor_model', 'dbModel' );
			$cd=$this->input->post('cd');
			
			$msg=array();
			$b = $this->dbModel->liberar( $cd, usuario_id(), $msg );
			
			if($b){ echo 'true'; }
			else{ echo 'Algum problema ocorreu.'; }
		}
	}
	
	function brinde()
	{
		if(CheckLogin())
		{
			$this->load->model( 'torcida/Torcedor_model', 'dbModel' );
			$cd_torcedor = $this->input->post('cd_torcedor');
			$fl_brinde   = $this->input->post('fl_brinde');
			
			$msg=array();
			$b = $this->dbModel->brinde( $cd_torcedor, $fl_brinde, $msg );
			
			if($b) { echo 'true'; }
			else{ echo 'Algum problema ocorreu.'; }
		}
	}	

	function bloquear()
	{
		if(CheckLogin())
		{
			$this->load->model( 'torcida/Torcedor_model', 'dbModel' );
			$cd=$this->input->post('cd');
			
			$msg=array();
			$b = $this->dbModel->bloquear( $cd, $msg );
			
			if($b){ echo 'true'; }
			else{ echo 'Algum problema ocorreu.'; }
		}
	}
	
	function excluir($id)
	{
		if(CheckLogin())
		{
			$this->load->model('torcida/Torcedor_model','dbModel');

			$this->dbModel->excluir( $id );

			redirect( 'ecrm/ri_torcida_torcedor', 'refresh' );
		}
	}
}