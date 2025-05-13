<?php
class Sg_correspondencia_relatorio extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }
	
	public function index()
    {
		$this->load->view('cadastro/sg_correspondencia_relatorio/index');
    }
	
	public function relatorio()
	{
		$this->load->model('projetos/correspondencias_model');

		$date_ini = $this->input->post('date_ini', TRUE);
		$date_fim = $this->input->post('date_fim', TRUE);
		
		$gerencia = $this->correspondencias_model->correspondencias_total_gerencia($date_ini, $date_fim);
		
		$gerencia_relatorio = $this->correspondencias_model->gerencia_relatorio();
		
		$this->load->plugin('fpdf');
		
		$ob_pdf = new PDF();

        $ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10,14,5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = 'Relatório de Correspondências';
		
		$ob_pdf->AddPage();
		
		$ob_pdf->MultiCell(0, 190, 'Período abrangido entre '.$date_ini.' e '.$date_fim, '0', 'C');
		$ob_pdf->MultiCell(0, 20, 'Publicado em '.date('d').' de '.mes_extenso().' de '.date('Y').'.', '0', 'C');
		
		$ob_pdf->AddPage();
		
		$ob_pdf->SetLineWidth(0);
        $ob_pdf->SetDrawColor(0,0,0);
		
		$ob_pdf->SetWidths(array(192));
        $ob_pdf->SetAligns(array('C'));
        $ob_pdf->SetFont('Courier','B',10);
        $ob_pdf->Row(array('Contagem por Divisão'));
		
		$ob_pdf->SetWidths(array(125,67));
        $ob_pdf->SetAligns(array('C', 'C'));
        $ob_pdf->SetFont('Courier','',10);
        $ob_pdf->Row(array('Divisão', 'Nº Correspondências'));
        $ob_pdf->SetAligns(array('L', 'C'));
		
		foreach($gerencia as $item)
        {
            $ob_pdf->Row(array($item['nome'], $item['total']));
        }
		
		$ob_pdf->MultiCell(0, 10, 'Período entre '.$date_ini.' e '.$date_fim, '0', 'C');
		
		foreach($gerencia_relatorio as $item)
		{
			$row = $this->correspondencias_model->correspondencias_gerencia($date_ini, $date_fim, $item['codigo']);
			
			if(count($row) > 0)
			{
				$ob_pdf->AddPage();
				
				$ob_pdf->SetWidths(array(192));
				$ob_pdf->SetAligns(array('C'));
				$ob_pdf->SetFont('Courier','B',10);
				$ob_pdf->Row(array($item["nome"]));
				
				$ob_pdf->SetWidths(array(30, 55, 107));
				$ob_pdf->SetAligns(array('C', 'C', 'C'));
				$ob_pdf->SetFont('Courier','',10);
				$ob_pdf->Row(array('Número', 'Destinatário', 'Assunto'));
				$ob_pdf->SetAligns(array('C', 'L', 'L'));
				
				foreach($row as $item2)
				{
					$ob_pdf->Row(array(
						$item2['ano_numero'], 
						$item2['destinatario_nome'],
						$item2['assunto'])
					);
				}
				
				$ob_pdf->MultiCell(0, 10, 'Período entre '.$date_ini.' e '.$date_fim, '0', 'C');
			}			
		}
		
		$ob_pdf->Output();
        exit;
	}
	
}
?>