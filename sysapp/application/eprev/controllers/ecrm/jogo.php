<?php
class jogo extends Controller
{
    function __construct()
    {
        parent::Controller();
    }
	
    function index()
    {
		CheckLogin();
		$this->load->model('projetos/Jogo_model');
		$data = Array();
		$args = Array();
		$result = null;
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->view('ecrm/jogo/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function jogoListar()
    {
        CheckLogin();
        $this->load->model('projetos/Jogo_model');
		$data = Array();
		$args = Array();
		$result = null;

		$args["dt_inclusao_ini"] = $this->input->post("dt_inclusao_ini", TRUE);
		$args["dt_inclusao_fim"] = $this->input->post("dt_inclusao_fim", TRUE);
		
        $this->Jogo_model->jogoListar( $result, $args );
		$data['collection'] = $result->result_array();
        if( $result )
        {
            $data['collection'] = $result->result_array();
        }
        $this->load->view('ecrm/jogo/index_partial_result', $data);
    }	
	
    function detalhe($cd_jogo = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');
			$data = Array();
			$args = Array();
			$result = null;	
			$data['cd_jogo'] = intval($cd_jogo);
			
			if(intval($cd_jogo) == 0)
			{
				$data['row'] = Array('cd_jogo'=>0,
				                     'ds_jogo'=>'',  
				                     'dt_inclusao'=>'',  
				                     'dt_inicio'=>'',  
				                     'hr_inicio'=>'',  
				                     'dt_final'=>'',  
				                     'hr_final'=>'',  
				                     'dt_exclusao'=>'',
									 'fl_randomico'=>'',
									 'tp_jogo'=>'A',
									 'qt_randomico'=>0,
									 'cd_jogo_pre'=>0,
									 'cd_jogo_pos'=>0,
									 'nr_margem_pergunta'=>0,
									 'nr_largura_pergunta'=>0,
									 'nr_altura_pergunta'=>0,
									 'nr_tamanho_fonte_pergunta'=>0,
									 'nr_tamanho_fonte_resposta'=>0,
									 'nr_tamanho_fonte_acerto'=>0,
									 'nr_tamanho_fonte_acerto_mensagem'=>0,
									 'cor_fundo'=>'FFFFFF',
									 'cor_pergunta'=>'000000',
									 'cor_acerto'=>'000000',
									 'cor_acerto_mensagem'=>'000000',
									 'fl_audio'=>'',
									 'fl_exibe_resultado'=>'',
									 'fl_tempo_exibe'=>'');
			}
			else
			{
				$args['cd_jogo'] = intval($cd_jogo);
				$this->Jogo_model->jogo($result, $args);
				$data['row'] = $result->row_array();	
			}
			
			$args['cd_jogo'] = intval($cd_jogo);
			$this->Jogo_model->perguntaListar($result, $args);
			$data['qt_pergunta'] = $result->num_rows();			
			
			$this->load->view('ecrm/jogo/detalhe.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
    function jogoSalvar()
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');

			$data = Array();
			$args = Array();
			$result = null;

			$args["cd_jogo"]   = $this->input->post("cd_jogo", TRUE);
			$args["ds_jogo"]   = $this->input->post("ds_jogo", TRUE);
			$args["dt_inicio"] = $this->input->post("dt_inicio", TRUE);
			$args["hr_inicio"] = $this->input->post("hr_inicio", TRUE);
			$args["dt_final"]  = $this->input->post("dt_final", TRUE);
			$args["hr_final"]  = $this->input->post("hr_final", TRUE);
			
			$args["cd_jogo_pos"] = $this->input->post("cd_jogo_pos", TRUE);
			$args["cd_jogo_pre"] = $this->input->post("cd_jogo_pre", TRUE);			
			
			$args["cor_fundo"]           = $this->input->post("cor_fundo", TRUE);
			$args["cor_pergunta"]        = $this->input->post("cor_pergunta", TRUE);
			$args["cor_acerto"]          = $this->input->post("cor_acerto", TRUE);
			$args["cor_acerto_mensagem"] = $this->input->post("cor_acerto_mensagem", TRUE);
			$args["fl_exibe_resultado"]  = $this->input->post("fl_exibe_resultado", TRUE);
			$args["fl_randomico"]        = $this->input->post("fl_randomico", TRUE);
			$args["qt_randomico"]        = (trim($args["fl_randomico"]) != "S" ? 0 : $this->input->post("qt_randomico", TRUE));
			$args["fl_tempo_exibe"]      = $this->input->post("fl_tempo_exibe", TRUE);
			$args["fl_audio"]            = $this->input->post("fl_audio", TRUE);
			$args["tp_jogo"]             = $this->input->post("tp_jogo", TRUE);
			$args["arquivo_audio"]       = $this->input->post("arquivo_audio", TRUE);
			
			$args["nr_margem_pergunta"]  = $this->input->post("nr_margem_pergunta", TRUE);
			$args["nr_largura_pergunta"] = $this->input->post("nr_largura_pergunta", TRUE);
			$args["nr_altura_pergunta"]  = $this->input->post("nr_altura_pergunta", TRUE);
			
			$args["nr_tamanho_fonte_pergunta"]  = $this->input->post("nr_tamanho_fonte_pergunta", TRUE);
			$args["nr_tamanho_fonte_resposta"]  = $this->input->post("nr_tamanho_fonte_resposta", TRUE);
			$args["nr_tamanho_fonte_acerto"]  = $this->input->post("nr_tamanho_fonte_acerto", TRUE);
			$args["nr_tamanho_fonte_acerto_mensagem"]  = $this->input->post("nr_tamanho_fonte_acerto_mensagem", TRUE);
			
			if(($args["fl_audio"] == "S") and (trim($args["arquivo_audio"]) != ""))
			{
				copy("./up/jogo/".$args["arquivo_audio"], "./../eletroceee/img/jogo/".$args["cd_jogo"]."_musica.mp3");
				unlink("./up/jogo/".$args["arquivo_audio"]);
			}
			
			$args["cd_usuario"] = $this->session->userdata('codigo');
			
			$cd_jogo_new = $this->Jogo_model->jogoSalvar( $result, $args );
			redirect("ecrm/jogo/detalhe/".$cd_jogo_new, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

	
    function jogoFixaPergunta($cd_jogo = 0, $cd_jogo_pergunta_fixa_inicio = 0, $cd_jogo_pergunta_fixa_ultima = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');

			$data = Array();
			$args = Array();
			$result = null;

			$args["cd_jogo"]                      = intval($cd_jogo);
			$args["cd_jogo_pergunta_fixa_inicio"] = intval($cd_jogo_pergunta_fixa_inicio);
			$args["cd_jogo_pergunta_fixa_ultima"] = intval($cd_jogo_pergunta_fixa_ultima);
			
			$this->Jogo_model->jogoFixaPergunta($result, $args);
			
			redirect("ecrm/jogo/estrutura/".intval($cd_jogo), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function jogoExcluir($cd_jogo = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');

			$data = Array();
			$args = Array();
			$result = null;

			$args["cd_jogo"] = intval($cd_jogo);
			$args["cd_usuario"]       = $this->session->userdata('codigo');
			$this->Jogo_model->jogoExcluir( $result, $args );
			redirect("ecrm/jogo/detalhe/".$cd_jogo, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	

    function jogoExcluirResposta($cd_jogo = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');

			$data = Array();
			$args = Array();
			$result = null;

			$args["cd_jogo"]    = intval($cd_jogo);
			$this->Jogo_model->jogoExcluirResposta( $result, $args );
			redirect("ecrm/jogo/detalhe/".$cd_jogo, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function pergunta($cd_jogo = 0, $cd_jogo_pergunta = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');
			$args=array();	
			$data['cd_jogo'] = intval($cd_jogo);
			$args['cd_jogo'] = intval($cd_jogo);
			
			$this->Jogo_model->jogo($result, $args);
			$data['ar_jogo'] = $result->row_array();				
			
			if((intval($cd_jogo) == 0) or (intval($cd_jogo_pergunta) == 0))
			{
				$data['row'] = Array('cd_jogo_pergunta'=>0,
				                     'ds_pergunta'=>'',  
				                     'ds_complemento'=>'',  
				                     'nr_ordem'=>'',  
				                     'fl_exibe_resposta'=>'',  
				                     'dt_inclusao'=>'',  
				                     'dt_exclusao'=>'');
			}
			else
			{
				$args['cd_jogo_pergunta'] = intval($cd_jogo_pergunta);
				
				$this->Jogo_model->pergunta($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('ecrm/jogo/pergunta.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function perguntaSalvar()
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');

			$data = Array();
			$args = Array();
			$result = null;

			$args["cd_jogo"]           = $this->input->post("cd_jogo", TRUE);
			$args["cd_jogo_pergunta"]  = $this->input->post("cd_jogo_pergunta", TRUE);
			$args["ds_pergunta"]       = $this->input->post("ds_pergunta", TRUE);
			$args["ds_complemento"]    = $this->input->post("ds_complemento", TRUE);
			$args["nr_ordem"]          = $this->input->post("nr_ordem", TRUE);
			$args["fl_exibe_resposta"] = $this->input->post("fl_exibe_resposta", TRUE);
			$args["cd_usuario"]        = $this->session->userdata('codigo');

			$cd_jogo_pergunta_new = $this->Jogo_model->perguntaSalvar( $result, $args );
			redirect("ecrm/jogo/pergunta/".$args["cd_jogo"]."/".$cd_jogo_pergunta_new, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function perguntaExcluir($cd_jogo = 0, $cd_jogo_pergunta = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');

			$data = Array();
			$args = Array();
			$result = null;

			$args["cd_jogo_pergunta"] = intval($cd_jogo_pergunta);
			$args["cd_usuario"]       = $this->session->userdata('codigo');
			$this->Jogo_model->perguntaExcluir( $result, $args );
			redirect("ecrm/jogo/pergunta/".$cd_jogo."/".$cd_jogo_pergunta, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function pergunta_item($cd_jogo = 0, $cd_jogo_pergunta = 0, $cd_jogo_pergunta_item = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');
			$args=array();	
			$data['cd_jogo']          = intval($cd_jogo);
			$data['cd_jogo_pergunta'] = intval($cd_jogo_pergunta);
			$data['cd_jogo_pergunta_item'] = intval($cd_jogo_pergunta_item);
			
			$args['cd_jogo'] = intval($cd_jogo);
			$args['cd_jogo_pergunta'] = intval($cd_jogo_pergunta);
			$args['cd_jogo_pergunta_item'] = intval($cd_jogo_pergunta_item);

			$this->Jogo_model->jogo($result, $args);
			$data['ar_jogo'] = $result->row_array();			
			
			if((intval($cd_jogo) == 0) or (intval($cd_jogo_pergunta) == 0) or (intval($cd_jogo_pergunta_item) == 0))
			{
				$data['row'] = Array('cd_jogo_pergunta_item'=>0,
									 'cd_jogo_pergunta'=> intval($cd_jogo_pergunta),
				                     'ds_item'=>'',  
				                     'fl_certo'=>'',  
				                     'vl_resposta'=>0,  
				                     'nr_ordem'=>'',  
				                     'dt_inclusao'=>'',  
				                     'dt_exclusao'=>'');
				
				$args['cd_jogo'] = intval($cd_jogo);
				$args['cd_jogo_pergunta'] = intval($cd_jogo_pergunta);	
				
				$this->Jogo_model->pergunta($result, $args);
				$data['ar_pergunta'] = $result->row_array();				
			}
			else
			{
				$this->Jogo_model->pergunta($result, $args);
				$data['ar_pergunta'] = $result->row_array();	
				
				$this->Jogo_model->perguntaItem($result, $args);
				$data['row'] = $result->row_array();
			}
			$this->load->view('ecrm/jogo/pergunta_item.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function perguntaItemExcluir($cd_jogo = 0, $cd_jogo_pergunta = 0, $cd_jogo_pergunta_item = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');

			$data = Array();
			$args = Array();
			$result = null;

			$args["cd_jogo_pergunta_item"] = intval($cd_jogo_pergunta_item);
			$args["cd_usuario"]            = $this->session->userdata('codigo');
			$this->Jogo_model->perguntaItemExcluir( $result, $args );
			redirect("ecrm/jogo/pergunta_item/".$cd_jogo."/".$cd_jogo_pergunta."/".$cd_jogo_pergunta_item, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function perguntaItemSalvar()
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');

			$data = Array();
			$args = Array();
			$result = null;

			$args["cd_jogo"]               = $this->input->post("cd_jogo", TRUE);
			$args["cd_jogo_pergunta"]      = $this->input->post("cd_jogo_pergunta", TRUE);
			$args["cd_jogo_pergunta_item"] = $this->input->post("cd_jogo_pergunta_item", TRUE);
			$args["ds_item"]               = $this->input->post("ds_item", TRUE);
			$args["fl_certo"]              = $this->input->post("fl_certo", TRUE);
			$args["nr_ordem"]              = $this->input->post("nr_ordem", TRUE);
			$args["vl_resposta"]           = $this->input->post("vl_resposta", TRUE);
			$args["cd_usuario"]            = $this->session->userdata('codigo');
			
			$cd_jogo_pergunta_item_new = $this->Jogo_model->perguntaItemSalvar( $result, $args );
			redirect("ecrm/jogo/pergunta_item/".$args["cd_jogo"]."/".$args["cd_jogo_pergunta"] ."/".$cd_jogo_pergunta_item_new, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	

    function acerto($cd_jogo = 0, $cd_jogo_acerto = 0)
    {
		CheckLogin();
		
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');
			$data = Array();
			$args = Array();
			$result = null;	
			$data['cd_jogo'] = intval($cd_jogo);
			$data['cd_jogo_acerto'] = intval($cd_jogo_acerto);
			
			if((intval($cd_jogo) == 0) or (intval($cd_jogo_acerto) == 0))
			{
				$data['row'] = Array('cd_jogo_pergunta'=>0,
				                     'ds_mensagem'=>'',  
				                     'qt_inicio'=>'',  
				                     'qt_final'=>'',  
				                     'dt_inclusao'=>'',  
				                     'dt_exclusao'=>'');
			}
			else
			{
				$args['cd_jogo'] = intval($cd_jogo);
				$args['cd_jogo_acerto'] = intval($cd_jogo_acerto);
				$this->Jogo_model->acerto($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('ecrm/jogo/acerto.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function acertoSalvar()
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');
			$data = Array();
			$args = Array();
			$result = null;

			$args["cd_jogo"]        = $this->input->post("cd_jogo", TRUE);
			$args["cd_jogo_acerto"] = $this->input->post("cd_jogo_acerto", TRUE);
			$args["ds_mensagem"]    = $this->input->post("ds_mensagem", TRUE);
			$args["qt_inicio"]      = $this->input->post("qt_inicio", TRUE);
			$args["qt_final"]       = $this->input->post("qt_final", TRUE);
			$args["cd_usuario"]     = $this->session->userdata('codigo');

			$cd_jogo_acerto_new = $this->Jogo_model->acertoSalvar( $result, $args );
			redirect("ecrm/jogo/acerto/".$args["cd_jogo"]."/".$cd_jogo_acerto_new, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function acertoExcluir($cd_jogo = 0, $cd_jogo_acerto = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');
			$data = Array();
			$args = Array();
			$result = null;

			$args["cd_jogo_acerto"] = intval($cd_jogo_acerto);
			$args["cd_usuario"]     = $this->session->userdata('codigo');
			$this->Jogo_model->acertoExcluir( $result, $args );
			redirect("ecrm/jogo/acerto/".$cd_jogo."/".$cd_jogo_acerto, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function estrutura($cd_jogo = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');
			$data = Array();
			$args = Array();
			$result = null;	

			if(intval($cd_jogo) == 0)
			{
				exibir_mensagem("ERRO - JOGO NÃO IDENTIFICADO");
			}
			else
			{
				$args['cd_jogo'] = intval($cd_jogo);
				$data['cd_jogo'] = intval($cd_jogo);
				
				$this->Jogo_model->jogo($result, $args);
				$data['ar_jogo'] = $result->row_array();

				$this->Jogo_model->acertoListar($result, $args);
				$data['acerto'] = $result->result_array();	
				
				$this->Jogo_model->perguntaListar($result, $args);
				$data['pergunta'] = $result->result_array();	
				foreach($data['pergunta'] as $ar_pergunta)
				{
					$args=array();
					$args['cd_jogo_pergunta'] = $ar_pergunta['cd_jogo_pergunta'];
					$this->Jogo_model->perguntaItemListar($result, $args);
					$data['pergunta_item'][$ar_pergunta['cd_jogo_pergunta']] = $result->result_array();				
				}				

				$this->load->view('ecrm/jogo/estrutura.php',$data);
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function grafico($cd_jogo = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');
			$data = Array();
			$args = Array();
			$result = null;

			if(intval($cd_jogo) == 0)
			{
				exibir_mensagem("ERRO - JOGO NÃO IDENTIFICADO");
			}
			else
			{
				$args['cd_jogo'] = intval($cd_jogo);
				$data['cd_jogo'] = intval($cd_jogo);
				
				$this->Jogo_model->jogo($result, $args);
				$data['ar_jogo'] = $result->row_array();				
				
				$this->Jogo_model->perguntaListarResultado($result, $args);
				$data['pergunta'] = $result->result_array();	

				$this->load->view('ecrm/jogo/grafico.php',$data);
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function graficoItem($cd_jogo_pergunta = 0)
    {
		CheckLogin();
		if(gerencia_in(array('GRI')))
		{
			$this->load->library('charts');
			$this->load->model('projetos/Jogo_model');
			$data = Array();
			$args = Array();
			$result = null;

			if(intval($cd_jogo_pergunta) == 0)
			{
				exibir_mensagem("ERRO - JOGO NÃO IDENTIFICADO");
			}
			else
			{
				$args=array();
				$args['cd_jogo_pergunta'] = $cd_jogo_pergunta;
				$this->Jogo_model->perguntaItemListarResultado($result, $args);
				$ar_pergunta_item = $result->result_array();				
						
				$ar_titulo = Array();
				$ar_dado = Array();				

				foreach($ar_pergunta_item as $ar_item )
				{
					$ar_titulo[] = $ar_item["nr_ordem"]." - ".$ar_item["ds_item"].($ar_item["fl_certo"] == "S" ? " (*)" : "");
					$ar_dado[]   = $ar_item["qt_resposta"];
				}	
				
				$ar_image = $this->charts->pieChart(80,$ar_dado,$ar_titulo,'', "");	
				
				if($handle = @fopen("..".$ar_image['name'], 'r'))
				{
					$data = fread($handle, filesize("..".$ar_image['name']));
					fclose($handle);
					header("Content-Type: image/png");
					echo $data;
				}
				else
				{
					echo 'ERRO';
				}
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
		
    }	

	function graficoItemAcerto($cd_jogo_pergunta = 0)
    {
		CheckLogin();
		if(gerencia_in(array('GRI')))
		{
			$this->load->library('charts');
			$this->load->model('projetos/Jogo_model');
			$data = Array();
			$args = Array();
			$result = null;	

			if(intval($cd_jogo_pergunta) == 0)
			{
				exibir_mensagem("ERRO - JOGO NÃO IDENTIFICADO");
			}
			else
			{
				$args=array();
				$args['cd_jogo_pergunta'] = $cd_jogo_pergunta;
				$this->Jogo_model->perguntaItemListarResultado($result, $args);
				$ar_pergunta_item = $result->result_array();				
						
				$ar_titulo = Array();
				$ar_dado = Array();
				$qt_acerto = 0;
				$qt_erro = 0;
				foreach($ar_pergunta_item as $ar_item )
				{
					if($ar_item["fl_certo"] == "S")
					{
						$qt_acerto += $ar_item["qt_resposta"];
					}
					else
					{
						$qt_erro += $ar_item["qt_resposta"];
					}
					
				}	
				
				$ar_titulo[] = "Acertou";
				$ar_titulo[] = "Errou";
				$ar_dado[] = $qt_acerto;
				$ar_dado[] = $qt_erro;
				$ar_image = $this->charts->pieChart(80,$ar_dado,$ar_titulo,'', "");	
				
				if($handle = @fopen("..".$ar_image['name'], 'r'))
				{
					$data = fread($handle, filesize("..".$ar_image['name']));
					fclose($handle);
					header("Content-Type: image/png");
					echo $data;
				}
				else
				{
					echo 'ERRO';
				}
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
		
    }	
	
    function resultado($cd_jogo = 0)
    {
		CheckLogin();
		$this->load->model('projetos/Jogo_model');
		$data = Array();
		$args = Array();
		$result = null;
	
		if(gerencia_in(array('GRI')))
		{
			
			$this->Jogo_model->jogoCombo($result, $args);
			$data['cd_jogo'] = $cd_jogo;
			$data['jogo_dd'] = $result->result_array();	
			
			$this->load->view('ecrm/jogo/resultado.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function resultadoListar()
    {
        CheckLogin();
        $this->load->model('projetos/Jogo_model');
		$data = Array();
		$args = Array();
		$result = null;

		$args["cd_jogo"]     = $this->input->post("cd_jogo", TRUE);
		$args["dt_jogo_ini"] = $this->input->post("dt_jogo_ini", TRUE);
		$args["dt_jogo_fim"] = $this->input->post("dt_jogo_fim", TRUE);
		$args["qt_acerto"]   = $this->input->post("qt_acerto", TRUE);
		$args["idade"]       = $this->input->post("idade", TRUE);
		$args["sexo"]        = $this->input->post("sexo", TRUE);

        $this->Jogo_model->resultado( $result, $args );
		$data['collection'] = $result->result_array();

        $this->load->view('ecrm/jogo/resultado_partial_result', $data);
    }

    function imagem($cd_jogo = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');
			$data = Array();
			$args = Array();
			$result = null;
			$data['cd_jogo'] = intval($cd_jogo);
			
			if(intval($cd_jogo) == 0)
			{
				$data['row'] = Array('cd_jogo'=>0,
				                     'ds_jogo'=>'',  
				                     'dt_inclusao'=>'',  
				                     'dt_inicio'=>'',  
				                     'hr_inicio'=>'',  
				                     'dt_final'=>'',  
				                     'hr_final'=>'',  
				                     'dt_exclusao'=>'');
			}
			else
			{
				$args['cd_jogo'] = intval($cd_jogo);
				$this->Jogo_model->jogo($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('ecrm/jogo/imagem.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

	function salvarImagem()
	{
		CheckLogin();
		if(gerencia_in(array('GRI')))
		{
			$data = Array();
			$args = Array();
			$result = null;
			$args["cd_jogo"]       = $this->input->post("cd_jogo", TRUE);
			$args["img_botao"]     = $this->input->post("img_botao", TRUE);
			$args["img_inicio"]    = $this->input->post("img_inicio", TRUE);
			$args["img_instrucao"] = $this->input->post("img_instrucao", TRUE);
			$args["img_pergunta"]  = $this->input->post("img_pergunta", TRUE);
			$args["img_proxima"]   = $this->input->post("img_proxima", TRUE);
			$args["img_resultado"] = $this->input->post("img_resultado", TRUE);
			

			if(intval($args["cd_jogo"]) > 0)
			{
				if(trim($args["img_botao"]) != "")
				{
					$ar_tmp = explode(".",$args["img_botao"]);
					if($ar_tmp[1] == "jpg")
					{
						list($width, $height) = getimagesize("./up/jogo/".$args["img_botao"]); 
						
						if(($width != 450) or ($height != 180))
						{
							echo " 	<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>
									Tamanho atual<br>
									L = $width px, A = $height px<br>
									<br><br>
									Tamanho máximo<br>
									L = 450 px, A = 180 px<br>
									</div>
								 ";
							exit;
						}
						else
						{
							copy("./up/jogo/".$args["img_botao"], "./../eletroceee/img/jogo/botao_".$args["cd_jogo"].".jpg");
						}
					}
					else
					{
						echo "
								<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>A extensão do arquivo não é .jpg
								</div>
							 ";
						exit;
					}
					unlink("./up/jogo/".$args["img_botao"]);
				}
				
				if(trim($args["img_inicio"]) != "")
				{
					$ar_tmp = explode(".",$args["img_inicio"]);
					if($ar_tmp[1] == "jpg")
					{
						list($width, $height) = getimagesize("./up/jogo/".$args["img_inicio"]); 
						
						#if(($width != 700) or ($height != 450))
						if($width != 700)
						{
							echo " 	<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>
									Tamanho atual<br>
									L = $width px, A = $height px<br>
									<br><br>
									Tamanho máximo<br>
									L = 700 px, A = 450 px<br>
									</div>
								 ";
							exit;
						}
						else
						{
							copy("./up/jogo/".$args["img_inicio"], "./../eletroceee/img/jogo/inicio_".$args["cd_jogo"].".jpg");
						}
					}
					else
					{
						echo "
								<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>A extensão do arquivo não é .jpg
								</div>
							 ";
						exit;
					}
					unlink("./up/jogo/".$args["img_inicio"]);					
				}

				if(trim($args["img_instrucao"]) != "")
				{
					$ar_tmp = explode(".",$args["img_instrucao"]);
					if($ar_tmp[1] == "jpg")
					{
						list($width, $height) = getimagesize("./up/jogo/".$args["img_instrucao"]); 
						
						#if(($width != 700) or ($height != 450))
						if($width != 700)
						{
							echo " 	<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>
									Tamanho atual<br>
									L = $width px, A = $height px<br>
									<br><br>
									Tamanho máximo<br>
									L = 700 px, A = 450 px<br>
									</div>
								 ";
							exit;
						}
						else
						{
							copy("./up/jogo/".$args["img_instrucao"], "./../eletroceee/img/jogo/instrucao_".$args["cd_jogo"].".jpg");
						}
					}
					else
					{
						echo "
								<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>A extensão do arquivo não é .jpg
								</div>
							 ";
						exit;
					}
					unlink("./up/jogo/".$args["img_instrucao"]);	
				}

				if(trim($args["img_pergunta"]) != "")
				{
					$ar_tmp = explode(".",$args["img_pergunta"]);
					if($ar_tmp[1] == "jpg")
					{
						list($width, $height) = getimagesize("./up/jogo/".$args["img_pergunta"]); 
						
						#if(($width != 700) or ($height != 450))
						if($width != 700)
						{
							echo " 	<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>
									Tamanho atual<br>
									L = $width px, A = $height px<br>
									<br><br>
									Tamanho máximo<br>
									L = 700 px, A = 450 px<br>
									</div>
								 ";
							exit;
						}
						else
						{
							copy("./up/jogo/".$args["img_pergunta"], "./../eletroceee/img/jogo/pergunta_".$args["cd_jogo"].".jpg");
						}
					}
					else
					{
						echo "
								<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>A extensão do arquivo não é .jpg
								</div>
							 ";
						exit;
					}
					unlink("./up/jogo/".$args["img_pergunta"]);	
				}

				if(trim($args["img_proxima"]) != "")
				{
					$ar_tmp = explode(".",$args["img_proxima"]);
					if($ar_tmp[1] == "png")
					{
						list($width, $height) = getimagesize("./up/jogo/".$args["img_proxima"]); 
						
						if(($width != 150) or ($height > 60))
						{
							echo " 	<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>
									Tamanho atual<br>
									L = $width px, A = $height px<br>
									<br><br>
									Tamanho máximo<br>
									L = 150 px, A = até 60 px, <br>
									</div>
								 ";
							exit;
						}
						else
						{
							copy("./up/jogo/".$args["img_proxima"], "./../eletroceee/img/jogo/proxima_".$args["cd_jogo"].".png");
						}
					}
					else
					{
						echo "
								<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>A extensão do arquivo não é .png
								</div>
							 ";
						exit;
					}
					unlink("./up/jogo/".$args["img_proxima"]);	
				}				
				
				if(trim($args["img_resultado"]) != "")
				{
					$ar_tmp = explode(".",$args["img_resultado"]);
					if($ar_tmp[1] == "jpg")
					{
						list($width, $height) = getimagesize("./up/jogo/".$args["img_resultado"]); 
						
						#if(($width != 700) or ($height != 450))
						if($width != 700)
						{
							echo " 	<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>
									Tamanho atual<br>
									L = $width px, A = $height px<br>
									<br><br>
									Tamanho máximo<br>
									L = 700 px, A = 450 px<br>
									</div>
								 ";
							exit;
						}
						else
						{
							copy("./up/jogo/".$args["img_resultado"], "./../eletroceee/img/jogo/resultado_".$args["cd_jogo"].".jpg");
						}
					}
					else
					{
						echo "
								<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>A extensão do arquivo não é .jpg
								</div>
							 ";
						exit;
					}
					unlink("./up/jogo/".$args["img_resultado"]);	
				}				
				
				redirect( "ecrm/jogo/imagem/".$args["cd_jogo"], "refresh");	
			}
			else
			{
				echo "ERRO - Jogo não identificado";
			}
			
			echo "<PRE>";
			print_r($args);
			
			/*
			if($retorno)
			{
				copy( "./up/torcida_precavida/".$args["imagem"], "./../torcida/precavida/".$args["imagem"] );

				redirect( "ecrm/ri_torcida_precavida_imagem/", "refresh" );			
			}
			else
			{
				$mensagens = implode('<br>', $msg);
				exibir_mensagem($msg[0]);
			}
			*/
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}	
	
    function resumo()
    {
		CheckLogin();
		
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');	
			
			$result = null;
			$data   = array();
			$args   = array();
			
			$this->Jogo_model->comboJogo($result, $args);
			$data['ar_jogo'] = $result->result_array();				
			
			$this->Jogo_model->comboTipoParticipante($result, $args);
			$data['ar_tipo_participante'] = $result->result_array();		

			$this->Jogo_model->comboIdade($result, $args);
			$data['ar_idade'] = $result->result_array();

			$this->Jogo_model->comboRenda($result, $args);
			$data['ar_renda'] = $result->result_array();	

			$this->Jogo_model->comboCidade($result, $args);
			$data['ar_cidade'] = $result->result_array();			
			
			$this->load->view('ecrm/jogo/resumo.php', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }

    function resumoListar()
    {
		CheckLogin();
		
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('projetos/Jogo_model');			
			
			$result = null;
			$data   = array();
			$args   = array();
			
			$args["cd_jogo"]              = $this->input->post("cd_jogo", TRUE);
			$args["cd_tipo_participante"] = $this->input->post("cd_tipo_participante", TRUE);
			$args["cd_sexo"]              = $this->input->post("cd_sexo", TRUE);
			$args["cd_idade"]             = $this->input->post("cd_idade", TRUE);
			$args["cd_renda"]             = $this->input->post("cd_renda", TRUE);
			$args["cd_cidade"]            = $this->input->post("cd_cidade", TRUE);
			
			manter_filtros($args);
			
			$this->Jogo_model->resumoListar($result, $args);
			$data['ar_reg'] = $result->result_array();	
			
			$this->load->view('ecrm/jogo/resumo_result', $data);  
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
}
