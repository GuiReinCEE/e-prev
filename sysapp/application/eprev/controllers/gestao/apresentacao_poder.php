<?php
class Apresentacao_poder extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    public function index()
	{
		$this->load->model('gestao/apresentacao_poder_model');

		$data['collection'] = $this->apresentacao_poder_model->get_indicador_lista();

		$this->load->view('gestao/apresentacao_poder/apresentacao', $data);
	}

	public function indicador_result()
	{
		$this->load->helper('reuniao_gestao_indicador');

		$this->load->model('gestao/apresentacao_poder_model');

		$cd_indicador = $this->input->post('cd_indicador', TRUE);

		$indicador_tabela = $this->apresentacao_poder_model->get_indicador(intval($cd_indicador));

		$indicador_tabela = array_map('arrayToUTF8', $indicador_tabela);		

		$parametro = $this->apresentacao_poder_model->get_indicador_parametro(intval($indicador_tabela['cd_indicador_tabela']));

		$indicador_tabela['parametro'] = array();

		foreach($parametro as $key3 => $parametro_item)
		{
			$indicador_tabela['parametro'][$parametro_item['nr_linha']][$parametro_item['nr_coluna']] = array_map("arrayToUTF8", $parametro_item);	
		}

		$data['indicador'] = $indicador_tabela;

		$data['grafico'] = get_grafico_indicador($data['indicador']);
		$data['tabela']  = get_tabela_indicador($data['indicador'], TRUE);

		$this->load->view('gestao/apresentacao_poder/apresentacao_result', $data);
	}

	public function salvar_imagem()
	{
		$id_imagem = $this->input->post('id_imagem');
		$ob_imagem = $this->input->post('ob_imagem');
		
		$ob_imagem = str_replace('data:image/png;base64,', '', $ob_imagem);
		$ob_imagem = str_replace(' ', '+', $ob_imagem);

		$ob_data = base64_decode($ob_imagem);
		//$arq = md5(uniqid(rand(), true));
		$arq = strtolower($this->session->userdata('usuario')).'_'.$id_imagem;
		$file = '../cieprev/up/apresentacao_poder/'.$arq.'.png';
		
		file_put_contents($file, $ob_data);		
	}

	public function gera_pdf($qt)
	{
		$this->load->plugin('fpdf');
				
		$ob_pdf = new PDF();
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');	
		$ob_pdf->SetNrPag(true);
		$ob_pdf->SetMargins(10, 14, 5);
		$ob_pdf->header_exibe = true;
		$ob_pdf->header_logo = true;
		$ob_pdf->header_titulo = true;
		$ob_pdf->header_titulo_texto = 'Apresentação Indicadores PODER';
		$ob_pdf->SetLineWidth(0);
		$ob_pdf->SetDrawColor(0, 0, 0);

		$i=0;

		while($i < $qt)
		{
			$margem_x = 10;
				
			$arq = './up/apresentacao_poder/'.strtolower($this->session->userdata('usuario'))."_".$i.".png";
			list($w, $h) = getimagesize($arq);  
			
			if($w > $h)
			{
				$lim_width  = 1050;
				$lim_height = 640;	
				$pr_height = ceil(($lim_width * 100) / $w);
				$height = ($pr_height * $h) / 100;					
				$width  = $lim_width;	

				if($height > $lim_height)
				{
					$pr_width = ceil(($lim_height * 100) / $h);
					$width = ($pr_width * $w) / 100;					
					$height  = $lim_height;								
				}
				
				$ob_pdf->AddPage('L');
			}
			else
			{
				$lim_width  = 720;
				$lim_height = 900;
				$pr_width = ceil(($lim_height * 100) / $h);
				$width = ($pr_width * $w) / 100;					
				$height  = $lim_height;						
				
				if($width > $lim_width)
				{
					$pr_height = ceil(($lim_width * 100) / $w);
					$height = ($pr_height * $h) / 100;					
					$width  = $lim_width;							
				}		
				
				$ob_pdf->AddPage('P');
			}

			if($width < $lim_width)
			{
				$margem_x += $ob_pdf->ConvertSize(floor(($lim_width - $width) / 2));
			}				
				
			
			#$ob_pdf->MultiCell(190, 2, $w."|".$h."|".$width."|".$height."|".$lim_width."|".$lim_height."|".$margem_x, '0', 'L');
			$ob_pdf->Image($arq, $margem_x, $ob_pdf->GetY(), $ob_pdf->ConvertSize($width), $ob_pdf->ConvertSize($height),'','',true);
			
			unlink($arq);
			$i++;
		}

        $ob_pdf->Output();
        exit;	
	}
}