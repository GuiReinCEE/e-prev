<?php
class comprovante_irpf_colaborador extends Controller
{
	function __construct()
	{
		parent::Controller();
		CheckLogin();
		$this->load->model('projetos/comprovante_irpf_colaborador_model');
	}

	function index($cd_sessao = "")
	{
		$args = Array();
		$data = Array();
		$result = null;		

		if(session_id() == $cd_sessao)
		{
			$this->load->view('cadastro/comprovante_irpf_colaborador/index.php');
		}
		else
		{
			$data["validar_login_ir_para"] = "cadastro/comprovante_irpf_colaborador/index";
			$this->load->view('home/validar_login.php',$data);
		}
	}

	function listar()
    {
		$args = Array();
		$data = Array();
		$result = null;			
		
		$args['cd_coladorador']    = $this->input->post("cd_coladorador", TRUE);   
		$args['nr_ano_calendario'] = $this->input->post("nr_ano_calendario", TRUE);   
		$args['nr_ano_exercicio']  = $this->input->post("nr_ano_exercicio", TRUE);   
		$args['cd_re_coladorador'] = $this->session->userdata('cd_registro_empregado');   
		
		if((trim($this->session->userdata('indic_04')) == "*") and (intval($this->input->post("cd_coladorador", TRUE)) > 0))
		{
			$args['cd_re_coladorador'] = intval($this->input->post("cd_coladorador", TRUE)); 
		}
			
		manter_filtros($args);

		$this->comprovante_irpf_colaborador_model->listar($result, $args);
		$data["collection"] = $result->result_array();

		$this->load->view('cadastro/comprovante_irpf_colaborador/index_result', $data);		
    }
	
	function pdf($cd_comprovante_irpf_colaborador = "", $cd_re_coladorador = "", $cd_sessao = "")
	{
        $args = Array();
		$data = Array();
		$result = null;	
		
		if(session_id() == $cd_sessao)
		{		
			$args['cd_comprovante_irpf_colaborador'] = trim($cd_comprovante_irpf_colaborador); 
			$args['cd_re_coladorador'] = trim($cd_re_coladorador); 
			
			$this->load->plugin('fpdf');
			$ob_pdf = new PDF('P', 'mm', 'A4');
			$ob_pdf->SetNrPagDe(true);
			$ob_pdf->SetMargins(10, 14, 5);
			$ob_pdf->header_exibe = false;
			$ob_pdf->header_logo = false;			
			$ob_pdf->AddPage();
			$ob_pdf->SetFont('courier','',8);
			
			$this->comprovante_irpf_colaborador_model->comprovante($result, $args);
			$ar_reg = $result->result_array();				

			foreach($ar_reg as $item)
			{
				$ob_pdf->MultiCell(190, 2.8, $item['linha'], 0, 'L');
			}
			
			$ob_pdf->Output();	
		}
	}
}
?>