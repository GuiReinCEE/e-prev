<?php
class intranet extends Controller
{
	private $fl_libera = false;

    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/intranet_model');
    }

    private function gera_pasta($cd_intranet, $pasta = '')
    {
    	
    	
    }

    private function gera_menu_filho($cd_gerencia, $cd_intranet, $pasta = '')
    {
    	$collection = $this->intranet_model->get_menu($cd_gerencia, $cd_intranet);

    	if(count($collection) == 0)
    	{
    		$link = $this->intranet_model->get_link($cd_intranet);
    		
    		if(count($link) == 0)
    		{
    			echo $cd_gerencia.';'.$pasta.';'."<br/>";
    		}

    		foreach ($link as $key => $item) 
    		{
    			echo $cd_gerencia.';'.$pasta.'/'.$item['texto_link'].';'.$item['link']."<br/>";
    		}

    		return ;
    	}
    	else
    	{
    		if(trim($pasta) != '')
    		{
    			$pasta = $pasta.'/';
    		}

    		foreach ($collection as $key => $item) 
    		{
    			$this->gera_menu_filho($cd_gerencia, $item['cd_intranet'], $pasta.$item['titulo']);
    		}
    	}
    }

    public function gera($cd_gerencia = 'GTI')
    {
    	/*
    	$collection = $this->intranet_model->gerar_intranet_link($cd_gerencia);

    	foreach ($collection as $key => $item) 
    	{
    		$pasta = $this->gera_pasta($item['cd_intranet']);

    		echo $item['cd_gerencia'].';'.$pasta.$item['texto_link'].';'.$item['link'].';<br/>';
    	}
    	*/

    	$menu_raiz = $this->intranet_model->get_menu($cd_gerencia);

    	$this->gera_menu_filho($cd_gerencia, $menu_raiz['cd_intranet']);
    }
	
	private function liberar($cd_gerencia = '')
	{
		$cd_gerencia = strtoupper($cd_gerencia);
		
		if($this->session->userdata('divisao') == $cd_gerencia)
		{
			$this->fl_libera = true;
		}
		else
		{
			#26 -> ANUNES
			#287 -> CSILVA
			
			if( ($cd_gerencia == 'GQ') AND (in_array(usuario_id(), array(78, 298, 26))) ) 
			{
				$this->fl_libera = true;
			}
			else if(($cd_gerencia == 'CQ') AND ((usuario_id() == 352) OR ($this->session->userdata('indic_12') == "*")))
			{
				$this->fl_libera = true;
			}
			else if(($cd_gerencia == 'CRQC') AND ($this->session->userdata('indic_12') == "*"))
			{
				$this->fl_libera = true;
			}
			elseif(($cd_gerencia == 'GIN') AND ((usuario_id() == 26) OR (usuario_id() == 287)))
			{
				$this->fl_libera = true;
			}			
			elseif(($cd_gerencia == 'CP') AND (usuario_id() == 26))
			{
				$this->fl_libera = true;
			}
			elseif(($cd_gerencia == 'CEA') AND (usuario_id() == 26))
			{
				$this->fl_libera = true;
			}
			elseif(($cd_gerencia == 'CCI') AND ((gerencia_in(array('GC'))) OR (gerencia_in(array('SG')))))
			{
				$this->fl_libera = true;
			}
			elseif(($cd_gerencia == 'CAP') AND ((usuario_id() == 26) OR (usuario_id() == 480) OR (usuario_id() == 431) OR (usuario_id() == 424) OR (usuario_id() == 468)))
			{
				$this->fl_libera = true;
			}	
			elseif(($cd_gerencia == 'CE') AND (
				(usuario_id() == 35) 
				OR 
				(usuario_id() == 424) 
				OR 
				(usuario_id() == 468)
				OR 
				(usuario_id() == 437)
			))
			{
				$this->fl_libera = true;
			}	
			elseif(usuario_id() == 170 OR usuario_id() == 251) #CRISTIANO / LUCIANO
			{
				$this->fl_libera = true;
			}
			elseif($cd_gerencia == 'PUB')
			{
				$this->fl_libera = true;
			}
			elseif(($cd_gerencia == 'PODER') AND (gerencia_in(array('GC'))))
			{
				$this->fl_libera = true;
			}
			elseif(($cd_gerencia == 'CIPA') AND (gerencia_in(array('GC'))))
			{
				$this->fl_libera = true;
			}			
			else
			{
				$this->fl_libera = false;
			}
		}
	}

    function index($cd_gerencia = '')
    {
		$args = array();	
		$data = array();
		
		if(trim($cd_gerencia) == '')
		{
			redirect("ecrm/intranet/pagina/".$this->session->userdata('divisao'), "refresh");
		}
		else
		{	
			$this->liberar($cd_gerencia);

			if($this->fl_libera)
			{
				$data['cd_gerencia'] = strtoupper($cd_gerencia);
				$this->load->view('ecrm/intranet/index',$data);
			}
			else
			{
				exibir_mensagem("ACESSO NÃO PERMITIDO");
			}
		}
		
		
    }
	
	function listar()
    {
		$args   = array();
		$data   = array();
		$result = null;	
	
		$cd_gerencia = strtoupper($this->input->post("cd_gerencia", TRUE));
		
		$this->liberar($cd_gerencia);
		
		if($this->fl_libera)
		{						
			$args['cd_gerencia'] = strtoupper($this->input->post("cd_gerencia", TRUE));
			$args['cd_intranet'] = intval($this->input->post("cd_intranet", TRUE));

			$data['cd_gerencia'] = strtoupper($this->input->post("cd_gerencia", TRUE));

			$this->intranet_model->listar( $result, $args );
			$collection = $result->result_array();
			
			$i = 0;
			$data['collection'] = Array();
			foreach ($collection as $item)
			{
				$data['collection'][$i]['cd_intranet_voltar'] = $item['cd_intranet_voltar'];
				$data['collection'][$i]['cd_intranet']        = $item['cd_intranet'];
				$data['collection'][$i]['cd_intranet_pai']    = $item['cd_intranet_pai'];
				$data['collection'][$i]['cd_gerencia']        = $item['cd_gerencia'];
				$data['collection'][$i]['titulo']             = $item['titulo'];
				$data['collection'][$i]['dt_inclusao']        = $item['dt_inclusao'];
				$data['collection'][$i]['dt_alteracao']       = $item['dt_alteracao'];
				$data['collection'][$i]['usuario_alteracao']  = $item['usuario_alteracao'];
				$data['collection'][$i]['nr_ordem']           = $item['nr_ordem'];
				
				$args['cd_intranet_pai'] = $item['cd_intranet'];
				
				$this->intranet_model->listar_subitem($result, $args);
				$data['collection'][$i]['subitem'] = $result->result_array();
				
				$args['cd_intranet'] = $item['cd_intranet'];
				$this->intranet_model->itens_superior($result, $args);
				$data['collection'][$i]['itemsuperior'] = $result->result_array();					
				
				$i++;
			}
			
			$this->load->view('ecrm/intranet/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function editar_ordem_principal()
	{
		$args   = array();
		$data   = array();
		$result = null;	
	
		$args['cd_intranet'] = $this->input->post("cd_intranet", TRUE);
		$args['nr_ordem']    = $this->input->post("nr_ordem", TRUE);
		$args['cd_usuario']  = $this->session->userdata("codigo");
		
		$this->intranet_model->editar_ordem_principal($result, $args);
	}
	
	function cadastro($cd_gerencia = '', $cd_intranet = 0)
	{
		$args   = array();
		$data   = array();
		$result = null;	
		
		$this->liberar($cd_gerencia);

		if($this->fl_libera)
		{		
			$data['cd_gerencia'] = strtoupper($cd_gerencia);
			$args['cd_gerencia'] = strtoupper($cd_gerencia);
			$args["cd_intranet"] = intval($cd_intranet);
			
			$this->intranet_model->itens_superior( $result, $args );
			$data['arr_itens_sup'] = $result->result_array();
			
			if(intval($args["cd_intranet"]) == 0)
			{
				$data['row'] = array(
					'cd_intranet'      => 0,
					'cd_intranet_pai' => '',
					'titulo'           => '',
					'conteudo'         => '',
					'arquivo'          => '',
					'arquivo_nome'     => ''
				);
			}
			else
			{
				$this->intranet_model->carrega( $result, $args );
				$data['row'] = $result->row_array();
			}
			
			$this->load->view('ecrm/intranet/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}	
	}
	
	function salvar()
    {
		$args   = array();
		$data   = array();
		$result = null;	
		
		$args['cd_gerencia'] = strtoupper($this->input->post("cd_gerencia", TRUE));
		
		$this->liberar($args['cd_gerencia']);
		
		if($this->fl_libera)
		{
			$args['cd_intranet']      = $this->input->post("cd_intranet", TRUE);
			$args['cd_intranet_pai']  = $this->input->post("cd_intranet_pai", TRUE);
			$args['titulo']           = $this->input->post("titulo", TRUE);
			$args['conteudo']         = $this->input->post("conteudo_pagina", TRUE);
			$args['arquivo_nome']     = $this->input->post("arquivo_nome", TRUE);
            $args['arquivo']          = $this->input->post("arquivo", TRUE);
			$args['cd_usuario']       = $this->session->userdata("codigo");

			$this->intranet_model->salvar($result, $args);

            redirect("ecrm/intranet/index/".$args['cd_gerencia'], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function excluir($cd_gerencia = '', $cd_intranet = 0)
	{
		$args   = array();
		$data   = array();
		$result = null;	
		
		$args['cd_gerencia'] = strtoupper($cd_gerencia);
		
		$this->liberar($args['cd_gerencia']);
		
		if($this->fl_libera)
		{
			$args['cd_intranet'] = $cd_intranet;
			$args['cd_usuario']  = $this->session->userdata("codigo");
			
			$this->intranet_model->excluir($result, $args);

            redirect("ecrm/intranet/index/".$args['cd_gerencia'], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function salvar_link()
	{
		$args   = array();
		$data   = array();
		$result = null;	
	
		$args['cd_gerencia'] = strtoupper($this->input->post("cd_gerencia", TRUE));
		
		$this->liberar($args['cd_gerencia']);

		if($this->fl_libera)
		{
			$args['cd_intranet']       = $this->input->post("cd_intranet", TRUE);
			$args['cd_intranet_link']  = $this->input->post("cd_intranet_link", TRUE);
			$args['link']              = $this->input->post("local_link");
			$args['texto_link']        = $this->input->post("texto_link", TRUE);
			$args['cd_usuario']        = $this->session->userdata("codigo");

			$bodytag = str_replace("%body%", "black", "<body text='%body%'>");

			$args['link'] = str_replace('\'', '/', $args['link']);
			
			//if(trim(substr($args['link'],0,2)) == "\\\\")
			/*
			if($this->session->userdata("codigo") == 251)
			{
				$this->intranet_model->salvar_link($result, $args);

				redirect("ecrm/intranet/cadastro/".$args['cd_gerencia']."/".$args['cd_intranet'], "refresh");
			}
			else
			{
				//exibir_mensagem("CAMINHO DO ARQUIVO NÃO É DA REDE");
				exibir_mensagem("SISTEMA INDISPONÍVEL, ENTRAR EM CONTATO COM O LUCIANO DA GTI.");
			}*/
			$this->intranet_model->salvar_link($result, $args);

			redirect("ecrm/intranet/cadastro/".$args['cd_gerencia']."/".$args['cd_intranet'], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function listar_links()
	{
		$args   = array();
		$data   = array();
		$result = null;	
		
		$args['cd_gerencia'] = strtoupper($this->input->post("cd_gerencia", TRUE));
		
		$this->liberar($args['cd_gerencia']);
		
		if($this->fl_libera)
		{
			$args['cd_intranet'] = $this->input->post("cd_intranet", TRUE);
			
			$this->intranet_model->carrega_intranet_link($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('ecrm/intranet/links_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function excluir_link($cd_gerencia = '', $cd_intranet = 0, $cd_intranet_link = 0)
	{
		$args   = array();
		$data   = array();
		$result = null;	
		
		$args['cd_gerencia'] = strtoupper($cd_gerencia);
		
		$this->liberar($args['cd_gerencia']);
		
		if($this->fl_libera)
		{
			$args['cd_intranet']      = $cd_intranet;
			$args['cd_intranet_link'] = $cd_intranet_link;
			$args['cd_usuario']       = $this->session->userdata("codigo");
			
			$this->intranet_model->excluir_link($result, $args);
			
			redirect("ecrm/intranet/cadastro/".$args['cd_gerencia']."/".$args['cd_intranet'], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function editar_ordem()
	{
		$args   = array();
		$data   = array();
		$result = null;	
	
		$args['cd_intranet_link'] = $this->input->post("cd_intranet_link", TRUE);
		$args['nr_ordem']         = $this->input->post("nr_ordem", TRUE);
		$args['cd_usuario']       = $this->session->userdata("codigo");
		
		$this->intranet_model->editar_ordem($result, $args);
	}
	
	function pagina($cd_gerencia = '', $cd_intranet = 0, $nr_aba = 0)
	{
		if(trim($cd_gerencia) == '')
		{
			redirect("ecrm/intranet/pagina/".$this->session->userdata('divisao'), "refresh");
		}
		else
		{	
			$args   = array();
			$data   = array();
			$result = null;	
			
			$args['cd_gerencia'] = $cd_gerencia;
			$data['cd_gerencia'] = $cd_gerencia;
			$data['nr_aba']      = intval($nr_aba);
			$args['cd_intranet'] = $cd_intranet;
			
			if(intval($args['cd_intranet']) == 0)
			{
				$this->intranet_model->gerencia_principal( $result, $args );
				$arr = $result->row_array();
				
				$args['cd_intranet'] = $arr['cd_intranet'];
			}
			
			$this->intranet_model->gerencia_titulo( $result, $args );
			$data['row'] = $result->row_array();
			
			$this->intranet_model->gerencia_pag_internas( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->intranet_model->carrega_intranet_link( $result, $args );
			$data['doc_collection'] = $result->result_array();
			
			$this->intranet_model->gerencia_menu( $result, $args );
			$data['menu_collection'] = $result->result_array();
			
			$this->load->view('ecrm/intranet/pagina', $data);
	
		}
	}
	
	function setItemPai()
	{
		$args   = array();
		$data   = array();
		$result = null;	
		
		$args['cd_intranet']     = $this->input->post("cd_intranet", TRUE);
		$args['cd_intranet_pai'] = $this->input->post("cd_intranet_pai", TRUE);
		$args['cd_usuario']      = $this->session->userdata("codigo");
		
		$this->intranet_model->setItemPai($result, $args);
	}	
}



?>
