<?php
class Contrato_aditivo extends Controller
{
	function __construct()
    {
        parent::Controller();
    }

	public function index_BKP_20230411($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
    	$this->load->plugin('fpdiprotection');

    	$this->load->model('contrato_aditivo_model');

    	$pdf = new FPDI_Protection('P', 'mm', 'A4');
		$pdf->SetMargins(10, 12, 5);

		$arquivo = '20191223.pdf';
		$pagecount = $pdf->setSourceFile('./up/contrato_aditivo/'.$arquivo);

    	$participante = $this->contrato_aditivo_model->get($cd_empresa, $cd_registro_empregado, $seq_dependencia);

    	for($i = 1; $i <= $pagecount; $i++)
		{
			$tplidx = $pdf->importPage($i);
			$size = $pdf->getTemplateSize($tplidx);
			
			$pdf->addPage($size['h'] > $size['w'] ? 'P' : 'L', array($size['w'], $size['h']));
			$pdf->useTemplate($tplidx);

			if($i == 1)
			{
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Arial', 'B', 12);

				$pdf->Text(30, 35, $participante['nome']);
				$pdf->Text(30, 40, utf8_decode('Empresa: '.$cd_empresa.'   Re: '.$cd_registro_empregado.'   Sequência: '.$seq_dependencia));
				$pdf->Text(30, 45, 'CPF:  '.$participante['cpf_mf']);
			}

			if($i == 12)
			{
				$pdf->SetFont('Arial', '', 12);
				$pdf->Text(30, 32, 'Porto Alegre, '.date('d/m/Y H:i:s'));
			}

			
		}

		return $pdf->Output();	
    }
	
	public function index_BKP_20250430($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
    	$this->load->plugin('fpdiprotection');

    	$this->load->model('contrato_aditivo_model');

    	$pdf = new FPDI_Protection('P', 'mm', 'A4');
		$pdf->SetMargins(10, 12, 5);

		$arquivo = '20230411.pdf';
		$pagecount = $pdf->setSourceFile('./up/contrato_aditivo/'.$arquivo);

    	$participante = $this->contrato_aditivo_model->get($cd_empresa, $cd_registro_empregado, $seq_dependencia);

    	for($i = 1; $i <= $pagecount; $i++)
		{
			$tplidx = $pdf->importPage($i);
			$size = $pdf->getTemplateSize($tplidx);
			
			$pdf->addPage($size['h'] > $size['w'] ? 'P' : 'L', array($size['w'], $size['h']));
			$pdf->useTemplate($tplidx);

			if($i == 1)
			{
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Arial', 'B', 12);

				$pdf->Text(30, 35, $participante['nome']);
				$pdf->Text(30, 40, utf8_decode('Empresa: '.$cd_empresa.'   Re: '.$cd_registro_empregado.'   Sequência: '.$seq_dependencia));
				$pdf->Text(30, 45, 'CPF:  '.$participante['cpf_mf']);
			}

			if($i == 12)
			{
				$pdf->SetFont('Arial', '', 12);
				$pdf->Text(30, 32, 'Porto Alegre, '.date('d/m/Y H:i:s'));
			}
			
			if($i == 15)
			{
				$pdf->SetFont('Arial', '', 12);
				$pdf->Text(23, 191, 'Porto Alegre, '.date('d/m/Y H:i:s'));
			}			

			
		}

		return $pdf->Output();	
    }	
	
	public function index($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
    	$this->load->plugin('fpdiprotection');

    	$this->load->model('contrato_aditivo_model');

    	$pdf = new FPDI_Protection('P', 'mm', 'A4');
		$pdf->SetMargins(10, 12, 5);

		$arquivo = '20250430.pdf';
		$pagecount = $pdf->setSourceFile('./up/contrato_aditivo/'.$arquivo);

    	$participante = $this->contrato_aditivo_model->get($cd_empresa, $cd_registro_empregado, $seq_dependencia);

    	for($i = 1; $i <= $pagecount; $i++)
		{
			$tplidx = $pdf->importPage($i);
			$size = $pdf->getTemplateSize($tplidx);
			
			$pdf->addPage($size['h'] > $size['w'] ? 'P' : 'L', array($size['w'], $size['h']));
			$pdf->useTemplate($tplidx);

			if($i == 1)
			{
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Arial', 'B', 12);

				$pdf->Text(30, 35, $participante['nome']);
				$pdf->Text(30, 40, utf8_decode('Empresa: '.$cd_empresa.'   Re: '.$cd_registro_empregado.'   Sequência: '.$seq_dependencia));
				$pdf->Text(30, 45, 'CPF:  '.$participante['cpf_mf']);
			}

			if($i == 12)
			{
				$pdf->SetFont('Arial', '', 12);
				$pdf->Text(30, 32, 'Porto Alegre, '.date('d/m/Y H:i:s'));
			}
			
			if($i == 15)
			{
				$pdf->SetFont('Arial', '', 12);
				$pdf->Text(23, 191, 'Porto Alegre, '.date('d/m/Y H:i:s'));
			}			

			
		}

		return $pdf->Output();	
    }	
}