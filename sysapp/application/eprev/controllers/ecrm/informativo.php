<?php
class informativo extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('acs/noticias_model');
    }

    function index()
    {
		$args = Array();
		$data = Array();
		$result = null;
	
		$args['fl_exclusao'] = false;
	
		$this->noticias_model->editorial( $result, $args );
		$data['arr_editorial'] = $result->result_array();
	
        $this->load->view('ecrm/informativo/index', $data);
    }

    function listar()
    {
        $args = Array();
		$data = Array();
		$result = null;

		$args['dt_ini']               = $this->input->post('dt_ini', TRUE);
		$args['dt_fim']               = $this->input->post('dt_fim', TRUE);
		$args['id_noticia_editorial'] = $this->input->post('id_noticia_editorial', TRUE);

        manter_filtros($args);
		
		$this->noticias_model->listar( $result, $args );
		$data['collection'] = $result->result_array();

        $this->load->view('ecrm/informativo/index_result', $data);
    }
	
	function resumo()
    {
        $this->load->view('ecrm/informativo/resumo');
    }
	
	function listar_resumo()
	{
		$args = Array();
		$data = Array();
		$result = null;

		$args['nr_ano'] = $this->input->post('ano', TRUE);
		$args['nr_mes'] = $this->input->post('mes', TRUE);
		
		manter_filtros($args);
		 
		$this->noticias_model->listar_resumo( $result, $args );
		$data['collection'] = $result->result_array();

        $this->load->view('ecrm/informativo/resumo_result', $data);
	}

	function excluir()
	{
        if(gerencia_in(array('GRI')))
        {		
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['codigo']     = $this->input->post('codigo', TRUE);
			$args["cd_usuario"] = $this->session->userdata('codigo');

			$this->noticias_model->excluir( $result, $args );
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	}
	
	function cadastro($codigo = 0, $formato = "HTML")
	{
        if(gerencia_in(array('GRI')))
        {		
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['codigo'] = intval($codigo);
			
			$args['fl_exclusao'] = true;
	
			$this->noticias_model->editorial( $result, $args );
			$data['arr_editorial'] = $result->result_array();
			
			if(!in_array($formato, array("HTML","TEXT")))
			{
				$formato = "HTML";
			}
			
			if(intval($args['codigo']) == 0)
			{
				$data['row'] = array(
					'codigo'     => $args['codigo'],
					'titulo'     => '',
					'descricao'  => '',
					'editorial'  => '',
					'fl_formato' => $formato
				);
			}
			else
			{
				$this->noticias_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$this->load->view('ecrm/informativo/cadastro', $data);
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }			
	}

	function salvar()
	{
        if(gerencia_in(array('GRI')))
        {		
			$args = Array();
			$data = Array();
			$result = null;
		
			$args["titulo"]               = $this->input->post("titulo",TRUE);
			$args["descricao"]            = $this->input->post("descricao",TRUE);
			$args["id_noticia_editorial"] = $this->input->post("id_noticia_editorial",TRUE);
			$args["cd_noticia"]           = $this->input->post("codigo",TRUE);
			$args["fl_formato"]           = $this->input->post("fl_formato",TRUE);
			$args["cd_usuario"]           = $this->session->userdata('codigo');
			$args["ordem"]                = 0;
			
			switch ($args["id_noticia_editorial"])
			{
				case 'FC': $args["ordem"] = 0; break;
				case 'FP': $args["ordem"] = 1; break;
				case 'PR': $args["ordem"] = 2; break;
				case 'PO': $args["ordem"] = 3; break;
				case 'EC': $args["ordem"] = 4; break;
				case 'EN': $args["ordem"] = 5; break;
				case 'CO': $args["ordem"] = 6; break;
				case 'ET': $args["ordem"] = 7; break;
				case 'GE': $args["ordem"] = 8; break;
				case 'QV': $args["ordem"] = 9; break;
				case 'CT': $args["ordem"] = 10; break;
				case 'RH': $args["ordem"] = 11; break;
				case 'QU': $args["ordem"] = 12; break;
			}
			
			$cd_noticia = $this->noticias_model->salvar($result, $args);
			
			redirect("ecrm/informativo/cadastro/".intval($cd_noticia), "refresh" );
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }			
	}
}
?>