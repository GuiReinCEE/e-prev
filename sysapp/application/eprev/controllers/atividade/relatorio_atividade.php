<?php
class Relatorio_atividade extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    public function index()
    {
        $this->load->model('projetos/relatorio_atividade_model');
		
		$data = array();
		
		$data['gerencia'] = $this->relatorio_atividade_model->gerencia();

		$this->load->view('atividade/relatorio_atividade/index', $data);
    }

    public function listar()
    {
        $this->load->model('projetos/relatorio_atividade_model');
		
		$data = array();
		$args = array();
		
		$args = array(
			'gerencia' => $this->input->post('gerencia', TRUE),
			'status'   => $this->input->post('status', TRUE)
		);
		
		$cd_usuario = $this->session->userdata('codigo');
		
		$data['collection'] = $this->relatorio_atividade_model->listar(intval($cd_usuario), $args);
		
		$this->load->view('atividade/relatorio_atividade/index_result', $data);
    }
	
	public function pdf()
    {
		$this->load->model('projetos/relatorio_atividade_model');
		
		$data = array();
		
		$cd_usuario = $this->session->userdata('codigo');
		
		$args = array(
			'gerencia' => $this->input->post('gerencia', TRUE),
			'status'   => $this->input->post('status', TRUE)
		);
		
		$collection = $this->relatorio_atividade_model->listar(intval($cd_usuario), $args);
		
        $this->load->plugin('fpdf');
				
		$ob_pdf = new PDF('L','mm','A4');
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');				
		$ob_pdf->SetNrPag(true);
		$ob_pdf->SetMargins(10, 14, 5);
		$ob_pdf->header_exibe = true;
		$ob_pdf->header_logo = true;
		$ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = "Relatório Atividade - ".$this->session->userdata('nome');
        
        $ob_pdf->AddPage();
        
		$divisao_antiga = '';
		
        foreach($collection as $key => $item)
		{
            if($item['divisao'] != $divisao_antiga)
			{	
				$ob_pdf->SetFont('segoeuib', '', 13);
				$ob_pdf->MultiCell(0, 7, $item['nome'], '0', 'L');
				
				$ob_pdf->SetWidths(array(15, 50, 40, 115, 30, 25));
				$ob_pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C'));
				$ob_pdf->SetFont('segoeuib', '', 12);
				$ob_pdf->Row(array('OS', 'SOLICITANTE', 'STATUS', 'DESCRIÇÃO', 'PROJETO', 'DT. LIMITE'));
				$ob_pdf->SetAligns(array('C', 'L', 'C', 'J', 'C', 'C'));
				
				$ob_pdf->SetFont('segoeuil', '', 12);

				$ob_pdf->Row(array(
					$item['numero'],
					$item['solicitante'],
					$item['status'],
					$item['descricao'],
					$item['projeto_nome'],
					$item['data_limite']
				));
				$ob_pdf->SetY($ob_pdf->GetY() + 5);
            }
			else
			{
				$ob_pdf->SetFont('segoeuil', '', 12);

				$ob_pdf->Row(array(
					$item['numero'],
					$item['solicitante'],
					$item['status'],
					$item['descricao'],
					$item['projeto_nome'],
					$item['data_limite']
				));
				$ob_pdf->SetY($ob_pdf->GetY() + 5);
			}
			
			$divisao_antiga = $item['divisao'];
		}
        $ob_pdf->Output();
	}
}
?>