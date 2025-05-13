<?php
class relatorio_central_telefonica extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();
		
	
		if(($this->session->userdata('tipo') == 'G') OR ($this->session->userdata('tipo') == 'D') OR ($this->session->userdata('codigo') == 170) OR ($this->session->userdata('codigo') == 251))
		{
			$this->load->view('gestao/relatorio_central_telefonica/index.php');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function duracao()
    {
		CheckLogin();
	
		if(($this->session->userdata('tipo') == 'G') OR ($this->session->userdata('tipo') == 'D') OR ($this->session->userdata('codigo') == 170) OR ($this->session->userdata('codigo') == 251))
		{
			$this->load->view('gestao/relatorio_central_telefonica/duracao.php');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
    function quantidade()
    {
		CheckLogin();
	
		if(($this->session->userdata('tipo') == 'G') OR ($this->session->userdata('tipo') == 'D') OR ($this->session->userdata('codigo') == 170) OR ($this->session->userdata('codigo') == 251))
		{
			$this->load->view('gestao/relatorio_central_telefonica/quantidade.php');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function gerencia()
    {
		CheckLogin();
	
		if(($this->session->userdata('tipo') == 'G') OR ($this->session->userdata('tipo') == 'D') OR ($this->session->userdata('codigo') == 170) OR ($this->session->userdata('codigo') == 251))
		{
			$this->load->view('gestao/relatorio_central_telefonica/gerencia.php');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function minha_gerencia()
    {
		CheckLogin();
	
		if($this->session->userdata('tipo') == 'G')
		{
			$this->load->view('gestao/relatorio_central_telefonica/minha_gerencia.php');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	

    function minha_gerencia_destino()
    {
		CheckLogin();
	
		if($this->session->userdata('tipo') == 'G')
		{
			$this->load->view('gestao/relatorio_central_telefonica/minha_gerencia_destino.php');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
    function minha_diretoria()
    {
		CheckLogin();
	
		if($this->session->userdata('tipo') == 'D')
		{
			$this->load->view('gestao/relatorio_central_telefonica/minha_diretoria.php');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function minha_diretoria_destino()
    {
		CheckLogin();
	
		if($this->session->userdata('tipo') == 'D')
		{
			$this->load->view('gestao/relatorio_central_telefonica/minha_diretoria_destino.php');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function maiorValorRamal()
    {
        CheckLogin();
        $this->load->model('asterisk/Relatorio_central_telefonica_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();

		$args["dt_ini"]      = $this->input->post("dt_ini", TRUE);
		$args["dt_fim"]      = $this->input->post("dt_fim", TRUE);
		$args["nr_top"]      = $this->input->post("nr_top", TRUE);
		$args["cd_conta"]    = "";

		// --------------------------
		// listar ...

        $this->Relatorio_central_telefonica_model->maiorValorRamal( $result, $args );

		$data['collection'] = $result->result_array();

        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------
		
		foreach( $data['collection'] as $item )
		{
			$ar_titulo[] = $item['ramal']."\n(".$item['conta'].")";
			$ar_dado[] = $item['vl_ligacao'];
		}
		
		$largura = 400;

		if(($args["nr_top"] >= 10) and ($args["nr_top"] < 15))
		{
			$largura = 700;
		}
		
		if(($args["nr_top"] >= 15) and ($args["nr_top"] <= 20))
		{
			$largura = 850;
		}
		
		if($args["nr_top"] > 20)
		{
			$largura = 1200;
		}	
		
		$this->load->library('charts');
		$configuracao = Array('Xlabel' => '','Ylabel' => 'Valor da chamada (R$)', 'TextboxFontSize' => 10, 'Textbox' => 'Os '.$args["nr_top"].' mais - Valor da Chamada');
		$data['image'] = $this->charts->cartesianChart('bar',$ar_titulo,$ar_dado,$largura,300,'', $configuracao);		

        $this->load->view('gestao/relatorio_central_telefonica/partial_result_valor', $data);
    }

	function maiorDuracaoRamal()
    {
        CheckLogin();
        $this->load->model('asterisk/Relatorio_central_telefonica_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();

		$args["dt_ini"]      = $this->input->post("dt_ini", TRUE);
		$args["dt_fim"]      = $this->input->post("dt_fim", TRUE);
		$args["nr_top"]      = $this->input->post("nr_top", TRUE);
		$args["cd_conta"]    = "";

		// --------------------------
		// listar ...

        $this->Relatorio_central_telefonica_model->maiorDuracaoRamal( $result, $args );

		$data['collection'] = $result->result_array();

        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

		foreach( $data['collection'] as $item )
		{
			$ar_titulo[] = $item['ramal']."\n(".$item['conta'].")";
			$ar_tmp = explode(":",$item['hr_ligacao']);
			$ar_dado[] = ($ar_tmp[0]*3600) + ($ar_tmp[1]*60) + $ar_tmp[2];
		}

		$largura = 400;

		if(($args["nr_top"] >= 10) and ($args["nr_top"] < 15))
		{
			$largura = 700;
		}
		
		if(($args["nr_top"] >= 15) and ($args["nr_top"] <= 20))
		{
			$largura = 850;
		}
		
		if($args["nr_top"] > 20)
		{
			$largura = 1200;
		}		
		
		$this->load->library('charts');
		$configuracao = Array('Xlabel' => '','Ylabel' => 'Duração da chamada', 'TextboxFontSize' => 10, 'Textbox' => 'Os '.$args["nr_top"].' mais - Duração da Chamada', 'YAxisFormat' => 'time');
		$data['image'] = $this->charts->cartesianChart('bar',$ar_titulo,$ar_dado,$largura,300,'', $configuracao);		
		
        $this->load->view('gestao/relatorio_central_telefonica/partial_result_duracao', $data);
    }

	function maiorQuantidadeRamal()
    {

		CheckLogin();
        $this->load->model('asterisk/Relatorio_central_telefonica_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();

		$args["dt_ini"]      = $this->input->post("dt_ini", TRUE);
		$args["dt_fim"]      = $this->input->post("dt_fim", TRUE);
		$args["nr_top"]      = $this->input->post("nr_top", TRUE);
		$args["cd_conta"]    = "";

		// --------------------------
		// listar ...

        $this->Relatorio_central_telefonica_model->maiorQuantidadeRamal( $result, $args );

		$data['collection'] = $result->result_array();

        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

		foreach( $data['collection'] as $item )
		{
			$ar_titulo[] = $item['ramal']."\n(".$item['conta'].")";
			$ar_dado[] = $item['qt_ligacao'];
		}
		
		$largura = 400;

		if(($args["nr_top"] >= 10) and ($args["nr_top"] < 15))
		{
			$largura = 700;
		}
		
		if(($args["nr_top"] >= 15) and ($args["nr_top"] <= 20))
		{
			$largura = 850;
		}
		
		if($args["nr_top"] > 20)
		{
			$largura = 1200;
		}		
		
		$this->load->library('charts');
		$configuracao = Array('Xlabel' => '','Ylabel' => 'Quantidade de chamada', 'TextboxFontSize' => 10, 'Textbox' => 'Os '.$args["nr_top"].' mais - Quantidade de Chamada');
		$data['image'] = $this->charts->cartesianChart('bar',$ar_titulo,$ar_dado,$largura,300,'', $configuracao);
		
		$this->load->view('gestao/relatorio_central_telefonica/partial_result_quantidade',$data);		
		
    }
	
	function gerenciaResumo()
    {

		CheckLogin();
        $this->load->model('asterisk/Relatorio_central_telefonica_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();
		$args["dt_ini"]      = $this->input->post("dt_ini", TRUE);
		$args["dt_fim"]      = $this->input->post("dt_fim", TRUE);

		// --------------------------

        $this->Relatorio_central_telefonica_model->gerenciaValor( $result, $args );
		$data['collection'] = $result->result_array();
        if( $result )
        {
            $data['collection'] = $result->result_array();
        }
		foreach( $data['collection'] as $item )
		{
			$ar_titulo_valor[] = $item['conta'];
			$ar_dado_valor[] = $item['vl_ligacao'];
		}		
		
        $this->Relatorio_central_telefonica_model->gerenciaDuracao( $result, $args );
		$data['collection'] = $result->result_array();
        if( $result )
        {
            $data['collection'] = $result->result_array();
        }
		foreach( $data['collection'] as $item )
		{
			$ar_titulo_duracao[] = $item['conta'];
			$ar_tmp = explode(":",$item['hr_ligacao']);
			$ar_dado_duracao[] = ($ar_tmp[0]*3600) + ($ar_tmp[1]*60) + $ar_tmp[2];
		}		

        $this->Relatorio_central_telefonica_model->gerenciaQuantidade( $result, $args );
		$data['collection'] = $result->result_array();
        if( $result )
        {
            $data['collection'] = $result->result_array();
        }
		foreach( $data['collection'] as $item )
		{
			$ar_titulo_quantidade[] = $item['conta'];
			$ar_dado_quantidade[] = $item['qt_ligacao'];
		}
		
		$largura = 700;
		
		$this->load->library('charts');
		
		$configuracao = Array('Xlabel' => '','Ylabel' => 'Valor da chamada (R$)', 'TextboxFontSize' => 10, 'Textbox' => 'Valor da Chamada');
		$data['image_valor'] = $this->charts->cartesianChart('bar',$ar_titulo_valor,$ar_dado_valor,$largura,400,'', $configuracao);
		
		$configuracao = Array('Xlabel' => '','Ylabel' => 'Duração da chamada', 'TextboxFontSize' => 10, 'Textbox' => 'Duração da Chamada', 'YAxisFormat' => 'time');
		$data['image_duracao'] = $this->charts->cartesianChart('bar',$ar_titulo_duracao,$ar_dado_duracao,$largura,400,'', $configuracao);			
		
		$configuracao = Array('Xlabel' => '','Ylabel' => 'Quantidade de chamada', 'TextboxFontSize' => 10, 'Textbox' => 'Quantidade de Chamada');
		$data['image_quantidade'] = $this->charts->cartesianChart('bar',$ar_titulo_quantidade,$ar_dado_quantidade,$largura,400,'', $configuracao);
		
		
		
		$this->load->view('gestao/relatorio_central_telefonica/partial_result_gerencia',$data);		
		
    }	
	
	
	function minhaGerenciaResumo()
    {

		CheckLogin();
	
        $this->load->model('asterisk/Relatorio_central_telefonica_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();
		$args["dt_ini"]      = $this->input->post("dt_ini", TRUE);
		$args["dt_fim"]      = $this->input->post("dt_fim", TRUE);
		$args["cd_conta"]    = $this->session->userdata('divisao');
		$args["nr_top"]      = 9999;
		$tp_relatorio        = $this->input->post("tp_relatorio", TRUE);
		
		
		if($tp_relatorio == "V")
		{
			$data['tipo'] = "Valor (R$)";
			$data['coluna'] = "vl_ligacao";
			
			$this->Relatorio_central_telefonica_model->maiorValorRamal( $result, $args );
			$data['collection'] = $result->result_array();
			if( $result )
			{
				$data['collection'] = $result->result_array();
			}
			
			foreach( $data['collection'] as $item )
			{
				$ar_titulo[] = $item['ramal'];
				$ar_dado[] = $item['vl_ligacao'];
			}	
			
			$config_grafico = Array('Xlabel' => '','Ylabel' => 'Valor da chamada (R$)', 'TextboxFontSize' => 10, 'Textbox' => 'Valor da Chamada');
		}
		
		if($tp_relatorio == "D")
		{
			$data['tipo'] = "Duração";
			$data['coluna'] = "hr_ligacao";
			
			$this->Relatorio_central_telefonica_model->maiorDuracaoRamal( $result, $args );
			$data['collection'] = $result->result_array();
			if( $result )
			{
				$data['collection'] = $result->result_array();
			}
			foreach( $data['collection'] as $item )
			{
				$ar_titulo[] = $item['ramal'];
				$ar_tmp = explode(":",$item['hr_ligacao']);
				$ar_dado[] = ($ar_tmp[0]*3600) + ($ar_tmp[1]*60) + $ar_tmp[2];
			}	
			
			$config_grafico = Array('Xlabel' => '','Ylabel' => 'Duração da chamada', 'TextboxFontSize' => 10, 'Textbox' => 'Duração da Chamada', 'YAxisFormat' => 'time');
		}
		
		if($tp_relatorio == "Q")
		{
			$data['tipo'] = "Quantidade";
			$data['coluna'] = "qt_ligacao";
			
			$this->Relatorio_central_telefonica_model->maiorQuantidadeRamal( $result, $args );
			$data['collection'] = $result->result_array();
			if( $result )
			{
				$data['collection'] = $result->result_array();
			}
			foreach( $data['collection'] as $item )
			{
				$ar_titulo[] = $item['ramal'];
				$ar_dado[] = $item['qt_ligacao'];
			}		
			
			$config_grafico = Array('Xlabel' => '','Ylabel' => 'Quantidade de chamada', 'TextboxFontSize' => 10, 'Textbox' => 'Quantidade de Chamada');
		}		
		
		$largura = 700;
		$this->load->library('charts');			
		$data['image'] = $this->charts->cartesianChart('bar',$ar_titulo,$ar_dado,$largura,300,'', $config_grafico);			
		
		$this->load->view('gestao/relatorio_central_telefonica/partial_result_minha_gerencia',$data);		
    }		
	
	function minhaGerenciaDestino()
    {

		CheckLogin();
	
        $this->load->model('asterisk/Relatorio_central_telefonica_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();
		$args["dt_ini"]      = $this->input->post("dt_ini", TRUE);
		$args["dt_fim"]      = $this->input->post("dt_fim", TRUE);
		$args["cd_conta"]    = $this->session->userdata('divisao');
		$args["nr_top"]      = 9999;
		$tp_relatorio        = $this->input->post("tp_relatorio", TRUE);
		
		
		if($tp_relatorio == "V")
		{
			$data['tipo'] = "Valor (R$)";
			$data['coluna'] = "vl_ligacao";
			
			$this->Relatorio_central_telefonica_model->maiorValorDestino($result, $args );
			$data['collection'] = $result->result_array();
			if( $result )
			{
				$data['collection'] = $result->result_array();
			}
		}
		
		if($tp_relatorio == "D")
		{
			$data['tipo'] = "Duração";
			$data['coluna'] = "hr_ligacao";			
			
			$this->Relatorio_central_telefonica_model->maiorDuracaoDestino($result, $args );
			$data['collection'] = $result->result_array();
			if( $result )
			{
				$data['collection'] = $result->result_array();
			}
		}
		
		if($tp_relatorio == "Q")
		{
			$data['tipo'] = "Quantidade";
			$data['coluna'] = "qt_ligacao";			
			
			$this->Relatorio_central_telefonica_model->maiorQuantidadeDestino($result, $args );
			$data['collection'] = $result->result_array();
			if( $result )
			{
				$data['collection'] = $result->result_array();
			}
		}		
		
		$this->load->view('gestao/relatorio_central_telefonica/partial_result_minha_gerencia_destino',$data);		
    }			
	
	function minhaDiretoriaResumo()
    {

		CheckLogin();
	
        $this->load->model('asterisk/Relatorio_central_telefonica_model');

        $data['collection'] = array();
        $result = null;

		$args=array();
		$args["dt_ini"]      = $this->input->post("dt_ini", TRUE);
		$args["dt_fim"]      = $this->input->post("dt_fim", TRUE);
		$args["cd_diretoria"] = $this->session->userdata('diretoria');
		$args["nr_top"]      = $this->input->post("nr_top", TRUE);
		$tp_relatorio        = $this->input->post("tp_relatorio", TRUE);
		
		if($tp_relatorio == "V")
		{
			$data['tipo'] = "Valor (R$)";
			$data['coluna'] = "vl_ligacao";
			
			$this->Relatorio_central_telefonica_model->maiorValorRamalDiretoria( $result, $args );
			$data['collection'] = $result->result_array();
			
			foreach( $data['collection'] as $item )
			{
				$ar_titulo[] = $item['ramal'];
				$ar_dado[] = $item['vl_ligacao'];
			}	

			$config_grafico = Array('Xlabel' => '','Ylabel' => 'Valor da chamada (R$)', 'TextboxFontSize' => 10, 'Textbox' => 'Valor da Chamada');
		}
		
		if($tp_relatorio == "D")
		{
			$data['tipo'] = "Duração";
			$data['coluna'] = "hr_ligacao";
			
			$this->Relatorio_central_telefonica_model->maiorDuracaoRamalDiretoria( $result, $args );
			$data['collection'] = $result->result_array();

			foreach( $data['collection'] as $item )
			{
				$ar_titulo[] = $item['ramal'];
				$ar_tmp = explode(":",$item['hr_ligacao']);
				$ar_dado[] = ($ar_tmp[0]*3600) + ($ar_tmp[1]*60) + $ar_tmp[2];
			}	
			
			$config_grafico = Array('Xlabel' => '','Ylabel' => 'Duração da chamada', 'TextboxFontSize' => 10, 'Textbox' => 'Duração da Chamada', 'YAxisFormat' => 'time');
		}
		
		if($tp_relatorio == "Q")
		{
			$data['tipo'] = "Quantidade";
			$data['coluna'] = "qt_ligacao";
			
			$this->Relatorio_central_telefonica_model->maiorQuantidadeRamalDiretoria( $result, $args );
			$data['collection'] = $result->result_array();

			foreach( $data['collection'] as $item )
			{
				$ar_titulo[] = $item['ramal'];
				$ar_dado[] = $item['qt_ligacao'];
			}		
			
			$config_grafico = Array('Xlabel' => '','Ylabel' => 'Quantidade de chamada', 'TextboxFontSize' => 10, 'Textbox' => 'Quantidade de Chamada');
		}		
		

		if($this->input->post("nr_top", TRUE) > 20)
		{
			$largura = 1000;
		}
		else
		{
			$largura = 700;
		}
		$this->load->library('charts');			
		$data['image'] = $this->charts->cartesianChart('bar',$ar_titulo,$ar_dado,$largura,300,'', $config_grafico);			
		
		$this->load->view('gestao/relatorio_central_telefonica/partial_result_minha_diretoria',$data);		
    }	
	
	function minhaDiretoriaDestino()
    {

		CheckLogin();
	
        $this->load->model('asterisk/Relatorio_central_telefonica_model');

        $data['collection'] = array();
        $result = null;

		$args=array();
		$args["dt_ini"]      = $this->input->post("dt_ini", TRUE);
		$args["dt_fim"]      = $this->input->post("dt_fim", TRUE);
		$args["cd_diretoria"] = $this->session->userdata('diretoria');
		$args["nr_top"]      = $this->input->post("nr_top", TRUE);
		$tp_relatorio        = $this->input->post("tp_relatorio", TRUE);
		
		if($tp_relatorio == "V")
		{
			$data['tipo'] = "Valor (R$)";
			$data['coluna'] = "vl_ligacao";
			
			$this->Relatorio_central_telefonica_model->maiorValorRamalDiretoriaDestino($result, $args );
			$data['collection'] = $result->result_array();
		}
		
		if($tp_relatorio == "D")
		{
			$data['tipo'] = "Duração";
			$data['coluna'] = "hr_ligacao";
			
			$this->Relatorio_central_telefonica_model->maiorDuracaoRamalDiretoriaDestino($result, $args );
			$data['collection'] = $result->result_array();
		}
		
		if($tp_relatorio == "Q")
		{
			$data['tipo'] = "Quantidade";
			$data['coluna'] = "qt_ligacao";
			
			$this->Relatorio_central_telefonica_model->maiorQuantidadeRamalDiretoriaDestino($result, $args );
			$data['collection'] = $result->result_array();
		}		
		
		
		
		$this->load->view('gestao/relatorio_central_telefonica/partial_result_minha_diretoria_destino',$data);		
    }		
}
