<?php
class atualiza_intranet extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index($gerencia = '')
    {
		CheckLogin();

		$args = Array();	
		$data = Array();
		$data['div'] = strtoupper($gerencia);
		$fl_libera = FALSE;
		
		if($this->session->userdata('divisao') == $gerencia)
		{
			$fl_libera = TRUE;
		}
		else
		{
			#26 -> ANUNES
			if(($gerencia == 'CQ') and ((usuario_id() == 26) or ($this->session->userdata('indic_12') == "*")))
			{
				$fl_libera = TRUE;
			}
			elseif(($gerencia == 'CP') and (usuario_id() == 26))
			{
				$fl_libera = TRUE;
			}
			elseif(($gerencia == 'CEA') and (usuario_id() == 26))
			{
				$fl_libera = TRUE;
			}
			elseif(($gerencia == 'CCI') and (gerencia_in(array('GC'))))
			{
				$fl_libera = TRUE;
			}	
			elseif(usuario_id() == 170)
			{
				$fl_libera = TRUE;
			}			
			else
			{
				$fl_libera = FALSE;
			}
		}

		if($fl_libera)
		{
	        $this->load->view('ecrm/atualiza_intranet/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
    function subitem($gerencia = '', $cd_item_pai = -1)
    {
		CheckLogin();
		$fl_libera = FALSE;
		
		if($this->session->userdata('divisao') == $gerencia)
		{
			$fl_libera = TRUE;
		}
		else
		{
			#26 -> ANUNES
			if(($gerencia == 'CQ') and ((usuario_id() == 26) or ($this->session->userdata('indic_12') == "*")))
			{
				$fl_libera = TRUE;
			}
			elseif(($gerencia == 'CP') and (usuario_id() == 26))
			{
				$fl_libera = TRUE;
			}
			elseif(($gerencia == 'CEA') and (usuario_id() == 26))
			{
				$fl_libera = TRUE;
			}
			elseif(($gerencia == 'CCI') and (gerencia_in(array('GC'))))
			{
				$fl_libera = TRUE;
			}	
			elseif(usuario_id() == 170)
			{
				$fl_libera = TRUE;
			}			
			else
			{
				$fl_libera = FALSE;
			}
		}

		if($fl_libera)
		{		
			$this->load->model('projetos/Intra_div_model');
			
			$args   = Array();
			$data   = Array();
			$result = null;

			$data['div'] = strtoupper($gerencia);
			$args['div'] = strtoupper($gerencia);
			$args["cd_item_pai"] = intval($cd_item_pai);
			
			$this->Intra_div_model->listarSubitem( $result, $args );
			$data['collection'] = $result->result_array();
			$this->load->view('ecrm/atualiza_intranet/subitem.php', $data);	
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
    }	

    function listar()
    {
        CheckLogin();

		$gerencia = strtoupper($this->input->post("div", TRUE));
		$fl_libera = FALSE;
		
		if($this->session->userdata('divisao') == $gerencia)
		{
			$fl_libera = TRUE;
		}
		else
		{
			#26 -> ANUNES
			if(($gerencia == 'CQ') and ((usuario_id() == 26) or ($this->session->userdata('indic_12') == "*")))
			{
				$fl_libera = TRUE;
			}
			elseif(($gerencia == 'CP') and (usuario_id() == 26))
			{
				$fl_libera = TRUE;
			}
			elseif(($gerencia == 'CEA') and (usuario_id() == 26))
			{
				$fl_libera = TRUE;
			}
			elseif(($gerencia == 'CCI') and (gerencia_in(array('GC'))))
			{
				$fl_libera = TRUE;
			}	
			elseif(usuario_id() == 170)
			{
				$fl_libera = TRUE;
			}			
			else
			{
				$fl_libera = FALSE;
			}
		}

		if($fl_libera)
		{
			$this->load->model('projetos/Intra_div_model');

			$args   = Array();
			$data   = Array();
			$result = null;			
			
			$args['div'] = strtoupper($this->input->post("div", TRUE));
			$data['div'] = strtoupper($this->input->post("div", TRUE));

			$this->Intra_div_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			$this->load->view('ecrm/atualiza_intranet/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
}
