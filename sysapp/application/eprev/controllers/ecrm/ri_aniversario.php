<?php
class ri_aniversario extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		
		
		$this->load->model('projetos/aniversario_model');
    }

	#### CADASTRO ####
    function index()
    {
		CheckLogin();
		if(gerencia_in(array('GC')))
		{
			$result = null;
			$data   = array();
			$args   = array();
			
			$this->aniversario_model->comboArea($result, $args);
			$data['ar_area'] = $result->result_array();	
			
			$this->load->view('ecrm/ri_aniversario/index.php', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
    function listar()
    {
		CheckLogin();
		if(gerencia_in(array('GC')))
		{
			$result = null;
			$data   = array();
			$args   = array();
			
			$args['nome']    = $this->input->post('nome', true); 
			$args['area']    = $this->input->post('area', true); 
			$args['origem']  = $this->input->post('origem', true); 
			$args['mes']     = $this->input->post('mes', true); 
			$args['fl_data'] = $this->input->post('fl_data', true); 
			
			manter_filtros($args);
			
			$this->aniversario_model->listar($result, $args);
			$data['ar_reg'] = $result->result_array();	
			
			$this->load->view('ecrm/ri_aniversario/index_result', $data);  
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
	function cadastro($origem = "", $cd_aniversario = 0)
	{
		CheckLogin();
		if(gerencia_in(array('GC')))
		{
			$result = null;
			$data   = array();
			$args   = array();
			
			if ((trim($origem) != "") and ((trim($origem) == "CAD") or (trim($origem) == "USU")))
			{
				$args['cd_aniversario'] = intval($cd_aniversario);
				$args['origem']         = trim($origem);
				
				if (($args['cd_aniversario'] == 0) and ($args['origem'] == "CAD"))
				{
					$data['row'] = Array(
											'cd_aniversario' => $args['cd_aniversario'],
											'origem'         => 'CAD',
											'nome'           => '',
											'area'           => '',
											'dt_nascimento'  => '',
											'dt_exclusao'    => ''
										 );
				}
				else if ($args['cd_aniversario'] > 0)
				{
					$this->aniversario_model->carrega($result, $args);
					$data['row'] = $result->row_array();
				}
				else
				{
					exibir_mensagem("ERRO: informações não encontradas");
				}				
				
				$this->load->view('ecrm/ri_aniversario/cadastro', $data);
			}
			else
			{
				exibir_mensagem("ERRO: origem não informada ou inválida");
			}	
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}
	
	function salvar()
	{
		CheckLogin();
		if(gerencia_in(array('GC')))
		{
			$result = null;
			$data   = array();
			$args   = array();
			
			$origem = $this->input->post('origem', true);
			
			if((trim($origem) == "CAD") or (trim($origem) == "USU"))
			{
				$args['cd_aniversario'] = $this->input->post('cd_aniversario', true);
				$args['origem']         = $this->input->post('origem', true);
				$args['nome']           = $this->input->post('nome', true); 
				$args['area']           = $this->input->post('area', true); 
				$args['dt_nascimento']  = $this->input->post('dt_nascimento', true); 
				$args['cd_usuario']     = $this->session->userdata('codigo');

				$cd_aniversario = $this->aniversario_model->salvar($result, $args);

				redirect("ecrm/ri_aniversario/cadastro/".trim($origem)."/".$cd_aniversario, "refresh"); 
			}
			else
			{
				exibir_mensagem("ERRO: origem não informada ou inválida");
			}			
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}	
	}
	
	function excluir($cd_aniversario = 0)
	{
		CheckLogin();
		if(gerencia_in(array('GC')))
		{
			$result = null;
			$data   = array();
			$args   = array();
			
			$args['cd_aniversario'] = intval($cd_aniversario); 
			$args['cd_usuario']     = $this->session->userdata('codigo');
			
			$this->aniversario_model->excluir($result, $args);
			
			redirect("ecrm/ri_aniversario", "refresh"); 
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}
	
	
	#### ASSUNTO ####
    function assunto()
    {
		CheckLogin();
		if(gerencia_in(array('GC')))
		{
			$result = null;
			$data   = array();
			$args   = array();
			
			$this->load->view('ecrm/ri_aniversario/assunto.php', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }

    function assuntoListar()
    {
		CheckLogin();
		if(gerencia_in(array('GC')))
		{
			$result = null;
			$data   = array();
			$args   = array();
			
			$args['assunto'] = $this->input->post('assunto', true); 
			
			$this->aniversario_model->assuntoListar($result, $args);
			$data['ar_reg'] = $result->result_array();	
			
			$this->load->view('ecrm/ri_aniversario/assunto_result', $data);  
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }		
	
	function assuntoCadastro($cd_aniversario_assunto = 0)
	{
		CheckLogin();
		if(gerencia_in(array('GC')))
		{
			$result = null;
			$data   = array();
			$args   = array();
			
			$args['cd_aniversario_assunto'] = intval($cd_aniversario_assunto);
				
			if (intval($cd_aniversario_assunto) == 0)
			{
				$data['row'] = Array(
										'cd_aniversario_assunto' => 0,
										'assunto'                => '',
										'dt_exclusao'            => ''
									 );
			}
			else
			{
				$this->aniversario_model->assuntoCarrega($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$this->load->view('ecrm/ri_aniversario/assunto_cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}	
	
	function assuntoSalvar()
	{
		CheckLogin();
		if(gerencia_in(array('GC')))
		{
			$result = null;
			$data   = array();
			$args   = array();

			$args['cd_aniversario_assunto'] = $this->input->post('cd_aniversario_assunto', true);
			$args['assunto']                = $this->input->post('assunto', true);
			$args['cd_usuario']             = $this->session->userdata('codigo');

			$cd_aniversario_assunto = $this->aniversario_model->assuntoSalvar($result, $args);

			redirect("ecrm/ri_aniversario/assuntoCadastro/".$cd_aniversario_assunto, "refresh"); 
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}	
	}	
	
	function assuntoExcluir($cd_aniversario_assunto = 0)
	{
		CheckLogin();
		if(gerencia_in(array('GC')))
		{
			$result = null;
			$data   = array();
			$args   = array();
			
			$args['cd_aniversario_assunto'] = intval($cd_aniversario_assunto); 
			$args['cd_usuario']             = $this->session->userdata('codigo');
			
			$this->aniversario_model->assuntoExcluir($result, $args);
			
			redirect("ecrm/ri_aniversario/assunto", "refresh"); 
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}	
	
	#### CARTAO ####
	function cartao($cd_cartao = 0, $origem = "", $cd_aniversario = 0)
    {
		$data = array();
		$args = array();

		if((intval($cd_cartao) == 0) or (trim($origem) == "") or (intval($cd_aniversario) == 0) )
		{
			$this->erro();
		}
		else
		{
			$args['cd_cartao']      = intval($cd_cartao);
			$args['origem']         = trim($origem);
			$args['cd_aniversario'] = intval($cd_aniversario);
			
			
			#### INFO ANIVERSARIANTE ####
			$this->aniversario_model->aniversariante($result, $args);
			$ar_aniversario = $result->row_array();
			
			if(count($ar_aniversario) == 0)
			{
				$this->erro();
			}
			else
			{
				/*
				#### 2013 ####
				#### TEMPLATE ####
				$ob_img = imagecreatefromjpeg("./up/aniversario/2013.jpg");
				$font = $_SERVER['DOCUMENT_ROOT']."/TTF/calibrib.ttf";

				#### NOME ####
				$this->escreve($ob_img, 
								22, 
								0,
								238,
								598, 
								imagecolorallocate($ob_img, 205,63,62),
								$font, 
								$ar_aniversario['nome']."\n[".$ar_aniversario['area']."]",
								11,
								18
								);	

				#### DT ANIVERSARIO ####
				$this->escreve($ob_img, 
								24, 
								0,
								560,
								625, 
								imagecolorallocate($ob_img, 205,63,62),
								$font, 
								$ar_aniversario['dt_nascimento']
								);										
				*/
				
				/*
				#### 2014 ####
				#### TEMPLATE ####
				$ob_img = imagecreatefromjpeg("./up/aniversario/2014.jpg");
				$font = $_SERVER['DOCUMENT_ROOT']."/TTF/calibrib.ttf";

				#### NOME ####
				//escreve($img, $font_size, $angle, $x, $y, $color, $font_name, $text, $h_linha = 5, $w_max = 999999999)
				$this->escreve($ob_img, 
								45, 
								0,
								390,
								470, 
								imagecolorallocate($ob_img, 255, 233, 1),
								$font, 
								$ar_aniversario['nome']."\n[".$ar_aniversario['area']."]",
								45,
								18
								);	

				#### DT ANIVERSARIO ####
				$this->escreve($ob_img, 
								24, 
								0,
								390,
								700, 
								imagecolorallocate($ob_img, 255, 233, 1),
								$font, 
								$ar_aniversario['dt_nascimento']
								);	
				*/
				
				/*
				#### 2015 ####
				#### TEMPLATE ####
				$ob_img = imagecreatefromjpeg("./up/aniversario/2015.jpg");
				$font = $_SERVER['DOCUMENT_ROOT']."/TTF/calibrib.ttf";

				#### DT ANIVERSARIO ####
				$this->escreve($ob_img, 
								24, 
								0,
								390,
								390, 
								imagecolorallocate($ob_img, 18, 100, 58),
								$font, 
								$ar_aniversario['dt_nascimento']
								);	
								
				#### NOME ####
				$this->escreve($ob_img, 
								45, 
								0,
								390,
								475, 
								imagecolorallocate($ob_img, 18, 100, 58),
								$font, 
								$ar_aniversario['nome']."\n[".$ar_aniversario['area']."]\n\n",
								45,
								22
								);	

			
				
				
				#### GERA IMAGEM ####
				#imagejpeg($ob_img, "./up/aniversario/tmp/".md5(uniqid(rand(), true)).".jpg"); #PARA ARQUIVO
				
				$nome_imagem = "./up/aniversario/tmp/".md5(uniqid(rand(), true)).".jpg";
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
				*/

				/*
				#### 2017 ####
				#### TEMPLATE ####
				$ob_img = imagecreatefromjpeg("./up/aniversario/2017.jpg");
				$font = $_SERVER['DOCUMENT_ROOT']."/TTF/calibrib.ttf";
				
				#### NOME ####
				$this->escreve($ob_img, 
								40, 
								0,
								350,
								180, 
								imagecolorallocate($ob_img, 18, 100, 58),
								$font, 
								$ar_aniversario['nome'],
								45,
								20
								);	

				#### DT ANIVERSARIO ####
				$this->escreve($ob_img, 
								20, 
								0,
								360,
								280, 
								imagecolorallocate($ob_img, 18, 100, 58),
								$font, 
								$ar_aniversario['dt_nascimento']." - ".$ar_aniversario['area']
								);									
								
				*/


								/*
				#### 2019 ####
				#### TEMPLATE ####
				$ob_img = imagecreatefromjpeg("./up/aniversario/2019.jpg");
				$font = $_SERVER['DOCUMENT_ROOT']."/TTF/calibrib.ttf";
				
				#### NOME ####
				$this->escreve($ob_img, 
								40, 
								0,
								350,
								400, 
								imagecolorallocate($ob_img, 255, 255, 255),
								$font, 
								$ar_aniversario['nome'],
								45,
								20
								);	

				#### DT ANIVERSARIO ####
				$this->escreve($ob_img, 
								20, 
								0,
								360,
								500, 
								imagecolorallocate($ob_img, 255, 255, 255),
								$font, 
								$ar_aniversario['dt_nascimento']." - ".$ar_aniversario['area']
								);									
							*/

				/*
				#### 2024 ####
				#### TEMPLATE ####
				#$ob_img = imagecreatefromjpeg("./up/aniversario/2024.jpg");
				$ob_img = imagecreatefromjpeg("./up/aniversario/2024_2.jpg");
				$font = $_SERVER['DOCUMENT_ROOT']."/TTF/calibrib.ttf";
				
				#### NOME ####
				$this->escreve($ob_img, 
								40, 
								0,
								320,
								400, 
								imagecolorallocate($ob_img, 255, 255, 255),
								$font, 
								$ar_aniversario['nome'],
								45,
								20
								);	

				#### DT ANIVERSARIO ####
				$this->escreve($ob_img, 
								20, 
								0,
								320,
								500, 
								imagecolorallocate($ob_img, 255, 255, 255),
								$font, 
								$ar_aniversario['dt_nascimento']." - ".$ar_aniversario['area']
								);
				*/
				
				#### 2025 ####
				#### TEMPLATE ####
				$ob_img = imagecreatefromjpeg("./up/aniversario/2025.jpg");
				$font = $_SERVER['DOCUMENT_ROOT']."/TTF/calibrib.ttf";
				
				#### NOME ####
				$this->escreve($ob_img, 
								60, 
								0,
								540,
								800, 
								imagecolorallocate($ob_img, 255, 255, 255),
								$font, 
								$ar_aniversario['nome'],
								45,
								25
								);	

				#### DT ANIVERSARIO ####
				$this->escreve($ob_img, 
								30, 
								0,
								550,
								950, 
								imagecolorallocate($ob_img, 255, 255, 255),
								$font, 
								$ar_aniversario['dt_nascimento']." - ".$ar_aniversario['area']
								);
				
				
				#### GERA IMAGEM ####
				$nome_imagem = "./up/aniversario/tmp/".md5(uniqid(rand(), true)).".jpg";
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
	
	private function erro()
	{
		$ob_img = imagecreatefrompng("./up/aniversario/erro.png");
		header('Content-type: image/jpeg');
		imagesavealpha($ob_img, true);
		imagejpeg($ob_img);
		imagedestroy($ob_img);	
	}
    
	private function escreve($img, $font_size, $angle, $x, $y, $color, $font_name, $text, $h_linha = 5, $w_max = 999999999)
	{
		$text = wordwrap($text, $w_max, "\n", false);
		
		$ar_text = explode("\n", $text);
		
		$qt_linha = count($ar_text);
		
		if($qt_linha > 1)
		{
			$nr_conta = 0;
			
			if($qt_linha == 4)
			{
				$y = $y - 13;
			}	

			if($qt_linha == 5)
			{
				$y = $y - 23;
			}			
			
			if($qt_linha == 2)
			{
				$box = imagettfbbox($font_size,$angle,$font_name,$ar_text[0]);
				$b_width = $box[2]-$box[0]; // lower right corner - lower left corner
				$b_height = $box[3]-$box[1];
				$x1 = $x - ($b_width/2);
				$y1 = $y - ($b_height/2);
				imagettftext($img,$font_size,0,$x1, ($y1 + 12 ) ,$color,$font_name,$ar_text[0]);		

				$box = imagettfbbox(($font_size - 10),$angle,$font_name,$ar_text[1]);
				$b_width = $box[2]-$box[0]; // lower right corner - lower left corner
				$b_height = $box[3]-$box[1];
				$x1 = $x - ($b_width/2);
				$y1 = $y - ($b_height/2);
				imagettftext($img,($font_size - 10),0,$x1, ($y1 + 55 ) ,$color,$font_name,$ar_text[1]);				
				
			}
			else
			{
				foreach ($ar_text as $item)
				{
					$font_size = ($nr_conta == 1 ? $font_size - 4 : $font_size);
					
					$font_size = ($nr_conta == ($qt_linha - 1) ? $font_size - 6 : $font_size);
					
					$box = imagettfbbox($font_size,$angle,$font_name,$item);
					$b_width = $box[2]-$box[0]; // lower right corner - lower left corner
					$b_height = $box[3]-$box[1];
					$x1 = $x - ($b_width/2);
					$y1 = $y - ($b_height/2) + ($nr_conta > 0 ? ($nr_conta * (ImageFontHeight($font_size) + $h_linha)) : 0);

					imagettftext($img,$font_size,0,$x1,$y1,$color,$font_name,$item);
					
					$nr_conta++;
				}
			}
		}
		else
		{
				$box = imagettfbbox($font_size,$angle,$font_name,$text);
				$b_width = $box[2]-$box[0]; // lower right corner - lower left corner
				$b_height = $box[3]-$box[1];
				$x1 = $x - ($b_width/2);
				$y1 = $y - ($b_height/2);

				imagettftext($img,$font_size,0,$x1,$y1,$color,$font_name,$text);
		}
	}	
	
	#### ANIVERSARIANTES DO MES ####
	function mensal($nr_mes_referencia)
    {
		$data = array();
		$args = array();
		$meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");

		if(intval($nr_mes_referencia) == 0)
		{
			$this->erro();
		}
		else
		{
			$args['nr_mes_referencia'] = intval($nr_mes_referencia);
			
			#### LISTA ANIVERSARIANTE ####
			$this->aniversario_model->aniversariantesDoMes($result, $args);
			$ar_aniversariante = $result->result_array();
			
			if(count($ar_aniversariante) == 0)
			{
				$this->erro();
			}
			else
			{
				
				#echo "<PRE>"; print_r($ar_aniversariante);
				
				#echo "<PRE>"; print_r($ar_aniversariante);
				
				$ob_img = imagecreatefromjpeg("./up/aniversario/niver_mensal_2025.jpg");
				$font = $_SERVER['DOCUMENT_ROOT']."/TTF/calibrib.ttf";
		
				imagettftext($ob_img,
							 26, #tamanho da fonte
							 0, #angulo
							 60, #posicao x
							 155, #posicao y
							 imagecolorallocate($ob_img, 255, 255, 255), # cor
							 $font,
							 "Mês de ".$meses[$args['nr_mes_referencia']-1]
							 );				
				
				
				$y = 190;
				$a = ((15 - count($ar_aniversariante)) * 2.3); #ajusta linhas no layout
				foreach ($ar_aniversariante as $item)
				{
					#### NOME ####
					#escreve($img, $font_size, $angle, $x, $y, $color, $font_name, $text, $h_linha = 5, $w_max = 999999999)
					
					imagettftext($ob_img,
					             16, #tamanho da fonte
								 0, #angulo
								 60, #posicao x
								 $y, #posicao y
								 imagecolorallocate($ob_img, 255, 255, 255), # cor
								 $font,
								 $item['dt_niver']." - ".$item['nome'].(trim($item['divisao']) != "" ? " (".trim($item['divisao']).")": "")
								 );
					$y+= 21 + $a;
				}
				
				#### GERA IMAGEM ####
				$nome_imagem = "./up/aniversario/tmp/".md5(uniqid(rand(), true)).".jpg";
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
		if(gerencia_in(array('GC')))
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
		if(gerencia_in(array('GC')))
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
