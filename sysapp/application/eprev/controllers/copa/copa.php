<?php
class copa extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model('copa/copa_model');
    }
	
	public function index()
    {
		$args = Array();
		$data = Array();
		$result = null;

		$this->load->view('copa/copa/index', $data);
    }
	
	public function listar()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_usuario'] = 0;
		$args['cd_fase']    = $this->input->post("cd_fase", TRUE);   
		$data['cd_fase']    = $this->input->post("cd_fase", TRUE);   

		$this->copa_model->listar($result, $args);
		$data['collection'] = $result->result_array();

		$this->load->view('copa/copa/index_result', $data);
    }
	
	public function grupo()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_usuario'] = 0;
		
		$ar_g = array("A","B","C","D","E","F","G","H");
		foreach($ar_g as $i)
		{
			$args['cd_grupo']   = $i;   
			$this->copa_model->grupo($result, $args);
			$data['ar_grupo'][$args['cd_grupo']] = $result->result_array();		
		}

		$this->load->view('copa/copa/grupo_result', $data);
    }	
	
	public function minha($cd_usuario_palpite = "")
    {
		$args = Array();
		$data = Array();
		$result = null;

		if(intval($cd_usuario_palpite) > 0)
		{
			$cd_usuario_palpite = intval($cd_usuario_palpite);
			$args['cd_usuario'] = $cd_usuario_palpite;
			$this->copa_model->getNomeUsuario($result, $args);
			$ar_usuario = $result->row_array();	
			$ds_usuario_palpite = $ar_usuario["nome"];
		}
		else
		{
			$cd_usuario_palpite = $this->session->userdata('codigo');
			$ds_usuario_palpite = $this->session->userdata('nome');
		}
		
		$data["cd_usuario_palpite"] = $cd_usuario_palpite;
		$data["ds_usuario_palpite"] = $ds_usuario_palpite;
		
		$this->load->view('copa/copa/minha', $data);
    }	
	
	public function minhaListar()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$data['cd_usuario_palpite'] = $this->input->post("cd_usuario_palpite", TRUE);
		$args['cd_usuario'] = $this->input->post("cd_usuario_palpite", TRUE);
		$args['cd_fase']    = $this->input->post("cd_fase", TRUE);  
		$data['cd_fase']    = $this->input->post("cd_fase", TRUE);		

		$this->copa_model->listar($result, $args);
		$data['collection'] = $result->result_array();

		$this->load->view('copa/copa/minha_result', $data);
    }
	
	public function minhaGrupo()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_usuario'] = $this->input->post("cd_usuario_palpite", TRUE);
		
		$ar_g = array("A","B","C","D", "E","F","G","H");
		foreach($ar_g as $i)
		{
			$args['cd_grupo']   = $i;   
			$this->copa_model->grupo($result, $args);
			$data['ar_grupo'][$args['cd_grupo']] = $result->result_array();		
		}

		$this->load->view('copa/copa/grupo_result', $data);
    }	
	
	public function setResultadoTabela()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_jogo']     = $this->input->post("cd_jogo", TRUE); 
		$args['nr_pais']     = $this->input->post("nr_pais", TRUE); 
		$args['nr_gol_pais'] = $this->input->post("nr_gol_pais", TRUE); 
	
		#echo "<PRE>".print_r($args,true)."</PRE>"; exit;
	
		$this->copa_model->setResultadoTabela($result, $args);
	}	
	
	public function setResultadoProrrogacao()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_jogo']     = $this->input->post("cd_jogo", TRUE); 
		$args['nr_pais']     = $this->input->post("nr_pais", TRUE); 
		$args['nr_gol_pais'] = $this->input->post("nr_gol_pais", TRUE); 
	
		#echo "<PRE>".print_r($args,true)."</PRE>"; exit;
	
		$this->copa_model->setResultadoProrrogacao($result, $args);
	}	

	public function setResultadoPenaltis()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_jogo']     = $this->input->post("cd_jogo", TRUE); 
		$args['nr_pais']     = $this->input->post("nr_pais", TRUE); 
		$args['nr_gol_pais'] = $this->input->post("nr_gol_pais", TRUE); 
	
		#echo "<PRE>".print_r($args,true)."</PRE>"; exit;
	
		$this->copa_model->setResultadoPenaltis($result, $args);
	}	

	public function setResultado()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_jogo']     = $this->input->post("cd_jogo", TRUE); 
		$args['nr_pais']     = $this->input->post("nr_pais", TRUE); 
		$args['nr_gol_pais'] = $this->input->post("nr_gol_pais", TRUE); 
		$args['cd_usuario']  = $this->session->userdata('codigo');
	
		#echo "<PRE>".print_r($args,true)."</PRE>"; exit;
	
		$this->copa_model->setResultado($result, $args);
	}	
	
	public function setVencedor()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_jogo']     = $this->input->post("cd_jogo", TRUE); 
		$args['cd_vencedor'] = $this->input->post("cd_vencedor", TRUE); 
		$args['cd_usuario']  = $this->session->userdata('codigo');
	
		#echo "<PRE>".print_r($args,true)."</PRE>"; exit;
	
		$this->copa_model->setVencedor($result, $args);
	}

	public function resultado()
    {
		$args = Array();
		$data = Array();
		$result = null;

		$this->load->view('copa/copa/resultado', $data);
    }

	public function resultadoListar()
    {
		$args = Array();
		$data = Array();
		$result = null;

		$args["fl_palpite"] = "S";
		$args["fl_pagou"]   = "";
		$this->copa_model->resultadoListar($result, $args);
		$data['ar_resultado'] = $result->result_array();
		
		$args["fl_palpite"] = "";
		$args["fl_pagou"]   = "S";
		$this->copa_model->resultadoListar($result, $args);
		$data['ar_resultado_pagou'] = $result->result_array();		

		$this->load->view('copa/copa/resultado_result', $data);
    }	
	
	public function palpiteVerifica()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_usuario'] = $this->session->userdata('codigo');
		$this->copa_model->palpiteVerifica($result, $args);
		$ar_palpite = $result->row_array();		
		
		if((is_array($ar_palpite)) and (array_key_exists('fl_palpite', $ar_palpite)))
		{
			echo json_encode($ar_palpite);
		}
		else
		{
			echo '{"fl_palpite":"N"}';
		}
    }	
	
	public function regulamento()
    {
		$args = Array();
		$data = Array();
		$result = null;

		$this->load->view('copa/copa/regulamento', $data);
    }	
	
	public function getAcertouResultado()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_jogo'] = $this->input->post("cd_jogo", TRUE); 
		
		/*
		$this->copa_model->getAcertouResultado($result, $args);
		$ar_acertou = $result->row_array();			
		
		if(trim($ar_acertou["acertadores"]) != "")
		{
			echo str_replace(",","<BR>",$ar_acertou["acertadores"]);
		}
		else
		{
			echo "Ninguém acertou.";
		}
		*/
		
		$this->copa_model->getUsuarioResultado($result, $args);
		$ar_acertou = $result->result_array();				
		if(count($ar_acertou) > 0)
		{
			echo '
					<table border="0" cellspacing="2" cellpadding="2" style="font-size: 80%;">
					<tr>
						<td>Nome</td>
						<td>Pontuação</td>
					</tr>					
			     ';
			
			$nr_conta = 0;
			while($nr_conta < count($ar_acertou))
			{
				
				
				echo '
						<tr>
							<td>'.$ar_acertou[$nr_conta]["nome"].'</td>
							<td align="center">'.$ar_acertou[$nr_conta]["nr_ponto"].'</td>
						</tr>
					 ';	
				if(($nr_conta+1 < count($ar_acertou)) and ($ar_acertou[$nr_conta]["nr_ponto"] != $ar_acertou[$nr_conta+1]["nr_ponto"]))
				{
				echo '
					<tr>
						<td><BR></td>
						<td></td>
					</tr>				
					<tr>
						<td><hr></td>
					</tr>	
					 ';					
				}					 

				
				$nr_conta++;	 
			}
			echo '
					</table>
			     ';				 
		}
		else
		{
			echo "Ninguém acertou.";
		}		

    }	
}
?>