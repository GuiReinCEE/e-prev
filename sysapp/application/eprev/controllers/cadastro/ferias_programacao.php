<?php
class ferias_programacao extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		$this->load->model('public/benef_rh_ferias_model');
    }

	private function erro()
	{
		$ob_img = imagecreatefrompng("./up/aniversario/erro.png");
		header('Content-type: image/jpeg');
		imagesavealpha($ob_img, true);
		imagejpeg($ob_img);
		imagedestroy($ob_img);	
	}
    
	#### COLABORADORES EM FERIAS NO MES ####
	function mes($nr_mes_referencia, $nr_ano_referencia)
    {
		$data = array();
		$args = array();
		$meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");

		if((intval($nr_mes_referencia) == 0) or (intval($nr_ano_referencia) == 0))
		{
			$this->erro();
		}
		else
		{
			$args['nr_mes_referencia'] = intval($nr_mes_referencia);
			$args['nr_ano_referencia'] = intval($nr_ano_referencia);
			
			#### LISTA COLABORADORES EM FERIAS NO MES ####
			$this->benef_rh_ferias_model->programacao_mes($result, $args);
			$ar_ferias = $result->result_array();
			
			if(count($ar_ferias) == 0)
			{
				$this->erro();
			}
			else
			{
				
				#echo "<PRE>"; print_r($ar_ferias);
				
				$ob_img = imagecreatefromjpeg("./up/ferias_programacao/ferias_2025.jpg");
				$font = $_SERVER['DOCUMENT_ROOT']."/TTF/calibrib.ttf";
				/*
				imagettftext($ob_img,
							 30, #tamanho da fonte
							 0, #angulo
							 50, #posicao x
							 65, #posicao y
							 imagecolorallocate($ob_img, 0, 0, 0), # cor
							 $font,
							 "Colegas em Férias - ".$meses[$args['nr_mes_referencia']-1]." de ".$args['nr_ano_referencia']
							 );				
				
				*/
				
				#((15 - count($ar_ferias)) * 2.3); #ajusta linhas no layout
				if(count($ar_ferias) <= 10)
				{
					$t_font = 32;
					$a = 20;
					$y = 200;
				}				
				elseif(count($ar_ferias) <= 20)
				{
					$t_font = 30;
					$a = 8;
					$y = 200;
				}
				else
				{
					$t_font = 25;
					$a = 5;
					$y = 100;
				}

				foreach ($ar_ferias as $item)
				{
					imagettftext($ob_img,
					             $t_font, #tamanho da fonte
								 0, #angulo
								 50, #posicao x
								 $y, #posicao y
								 imagecolorallocate($ob_img,  255, 255, 255), # cor
								 $font,
								 $item['nome'].(trim($item['divisao']) != "" ? " (".trim($item['divisao']).")": "")." - ".$item['dt_ferias_ini']." - ".$item['dt_ferias_fim']
								 );
					$y+= 30 + $a;
				}
				
				#### GERA IMAGEM ####
				$nome_imagem = "./up/ferias_programacao/tmp/".md5(uniqid(rand(), true)).".jpg";
				imagejpeg($ob_img,$nome_imagem,100);
				imagedestroy($ob_img);
				
				if($handle = @fopen($nome_imagem, 'r'))
				{
					$data = fread($handle, filesize($nome_imagem));
					fclose($handle);
					@unlink($nome_imagem);
					
					header('Content-type: image/jpeg');
					echo($data);
				}				
				
			}
		}
    }
	
	#### RELATORIO ####
	function resumo()
    {
		CheckLogin();
		if(gerencia_in(array('GRI','GGS')))
		{
			$result = null;
			$data   = array();
			$args   = array();
			
			$this->load->view('ecrm/ri_aniversario/resumo', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
	function listar_resumo()
    {
		CheckLogin();
		if(gerencia_in(array('GRI','GGS')))
		{
			$result = null;
			$data   = array();
			$args   = array();
			
			$args['ano'] = $this->input->post('ano', true); 
			
			manter_filtros($args);
			
			$this->aniversario_model->resumo($result, $args);
			$data['collection'] = $result->result_array();	
			
			$this->load->view('ecrm/ri_aniversario/resumo_result', $data);  
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
}

?>
