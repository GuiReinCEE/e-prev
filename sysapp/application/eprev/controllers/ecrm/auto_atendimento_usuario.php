<?php
class auto_atendimento_usuario extends Controller
{
	var $fl_entrar = false;
	var $fl_libera = false;
	var $ar_per_entrar = Array();
	var $ar_per_libera = Array('dpastore', 'coliveira', 'lrodriguez', 'vdornelles', 'aconte');
	
    function __construct()
    {
        parent::Controller();
		CheckLogin();
		$this->fl_entrar = in_array($this->session->userdata('usuario'), $this->ar_per_entrar);
		$this->fl_libera = in_array($this->session->userdata('usuario'), $this->ar_per_libera);
    }
	
    function index()
    {
		if(($this->fl_entrar) or ($this->fl_libera))
		{
			$result = null;
			$data   = Array();
			$args   = Array();	

			$this->load->view('ecrm/auto_atendimento_usuario/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function listar()
    {
        if(($this->fl_entrar) or ($this->fl_libera))
		{		
			$this->load->model('projetos/auto_atendimento_usuario_model');

			$result = null;
			$data   = Array();
			$args   = Array();

			$args["cd_situacao"] = trim($this->input->post("cd_situacao", TRUE));
						
			manter_filtros($args);
			
			$this->auto_atendimento_usuario_model->listar($result, $args);
			$data['ar_lista'] = $result->result_array();
			
			$data['fl_libera'] = $this->fl_libera;
			
			$this->load->view('ecrm/auto_atendimento_usuario/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}
	
    function cadastro()
    {
		if($this->fl_libera)
		{
			$result = null;
			$data   = Array();
			$args   = Array();	

			$this->load->view('ecrm/auto_atendimento_usuario/cadastro.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function salvar()
    {
        if($this->fl_libera)
		{		
			$this->load->model('projetos/auto_atendimento_usuario_model');

			$result = null;
			$data   = Array();
			$args   = Array();

			$args["cd_usuario"]          = trim($this->input->post("cd_usuario", TRUE));
			$args["cd_usuario_inclusao"] = usuario_id();

			$this->auto_atendimento_usuario_model->salvar($result, $args);
			
			$this->load->view('ecrm/auto_atendimento_usuario/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}	
	
    function excluir()
    {
		if($this->fl_libera)
		{
			$this->load->model('projetos/auto_atendimento_usuario_model');
			$result = null;
			$data   = Array();
			$args   = Array();	
			
			$args['cd_auto_atendimento_usuario'] = intval( $this->input->post("cd_auto_atendimento_usuario", TRUE));
			$args['cd_usuario_exclusao']         = usuario_id();
		
			$retorno = $this->auto_atendimento_usuario_model->excluir($result, $args);
		
			echo $retorno;
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}
	
    function acesso($cd_usuario = 0)
    {
		if(($this->fl_entrar) or ($this->fl_libera))
		{
			$result = null;
			$data   = Array();
			$args   = Array();	
			$data['cd_usuario'] = intval($cd_usuario);
			
			$qr_sql = "
						SELECT uc.nome || ' (' || uc.divisao || ')' AS nome
						  FROM projetos.usuarios_controledi uc
						 WHERE uc.codigo = ".$data['cd_usuario']."
					  ";
			$result = $this->db->query($qr_sql);			
			$ar_usu = $result->row_array();
			$data['ds_usuario'] = $ar_usu['nome'];
			
			$this->load->view('ecrm/auto_atendimento_usuario/acesso.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function acessoListar()
    {
        if(($this->fl_entrar) or ($this->fl_libera))
		{		
			$this->load->model('projetos/auto_atendimento_usuario_model');

			$result = null;
			$data   = Array();
			$args   = Array();

			$args["cd_usuario"]    = trim($this->input->post("cd_usuario", TRUE));
			$args["dt_acesso_ini"] = trim($this->input->post("dt_acesso_ini", TRUE));
			$args["dt_acesso_fim"] = trim($this->input->post("dt_acesso_fim", TRUE));
			$args["cd_empresa"]            = trim($this->input->post("cd_empresa", TRUE));
			$args["cd_registro_empregado"] = trim($this->input->post("cd_registro_empregado", TRUE));
			$args["seq_dependencia"]       = trim($this->input->post("seq_dependencia", TRUE));
						
			#manter_filtros($args);
			
			$this->auto_atendimento_usuario_model->acessoListar($result, $args);
			$data['ar_lista'] = $result->result_array();
			
			$this->load->view('ecrm/auto_atendimento_usuario/acesso_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}	

	
    function termo($cd_auto_atendimento_usuario = 0)
    {
		 if(($this->fl_entrar) or ($this->fl_libera))
		{
			$result = null;
			$data   = Array();
			$args   = Array();	
			
			$qr_sql = "
						SELECT TO_CHAR(aau.dt_inclusao, 'DD') AS dia,
						       TO_CHAR(aau.dt_inclusao, 'MM') AS mes,
						       TO_CHAR(aau.dt_inclusao, 'YYYY') AS ano,
						       UPPER(funcoes.remove_acento(nome)) AS nome,
							   uc.cd_patrocinadora AS cd_empresa,
							   uc.cd_registro_empregado
						  FROM projetos.auto_atendimento_usuario aau
						  JOIN projetos.usuarios_controledi uc
						    ON uc.codigo = aau.cd_usuario
						 WHERE aau.cd_auto_atendimento_usuario = ".intval($cd_auto_atendimento_usuario)."						
					  ";
			$result = $this->db->query($qr_sql);			
			$ar_usu = $result->row_array();
			
			$this->load->plugin('fpdf');
			$ar_mes = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
			$ob_pdf = new PDF('P','mm','A4'); 
			$ob_pdf->AddPage();
			$ob_pdf->Image('img/logofundacao_carta.jpg', 20, 10, $ob_pdf->ConvertSize(150), $ob_pdf->ConvertSize(33));	

			$ob_pdf->SetXY(20,30);
			$ob_pdf->SetFont('Courier','B',20);			
			$ob_pdf->MultiCell(170, 6, 'TERMO DE COMPROMISSO',0,"C");			
			

			$ob_pdf->SetXY(20,50);
			$ob_pdf->SetFont('Courier','',12);			
			$ob_pdf->MultiCell(170, 6, '      Eu, '.$ar_usu['nome'].', declaro que recebi, nesta data, a senha Master para acesso ao sistema de autoatendimento da Fundação CEEE, com a finalidade de acompanhar e orientar ao participante/assistido durante o atendimento, analisar e propor melhorias no referido sistema.',0,"J");	
			
			$ob_pdf->SetXY(20, $ob_pdf->GetY() + 6);	
			$ob_pdf->MultiCell(170, 6, '     Declaro, também, estar plenamente ciente de que com essa senha é possível conceder empréstimo pessoal e realizar todas as transações e consultas disponíveis ao participante/assistido no autoatendimento e, que, caso venha a utilizá-la de forma a causar prejuízo à Fundação CEEE, estarei sujeito a medidas de ordem disciplinar, previstas internamente com base no artigo 482 e seus incisos da C.L.T. (Consolidação das Leis do Trabalho), bem como de ordem legal.',0,"J");			

			$ob_pdf->SetXY(20, $ob_pdf->GetY() + 6);	
			$ob_pdf->MultiCell(170, 6, 'Porto Alegre, '.$ar_usu['dia'].' de '.strtolower($ar_mes[($ar_usu['mes'] - 1)]).' de '.$ar_usu['ano'].'.',0,"J");			


			
			$ob_pdf->SetXY(20, $ob_pdf->GetY() + 25);	
			$ob_pdf->MultiCell(170, 6, '___________________________________',0,"J");	
			$ob_pdf->SetX(20);			
			$ob_pdf->MultiCell(170, 6, 'Assinatura empregado',0,"J");			
			
		
			$ob_pdf->SetXY(20, $ob_pdf->GetY() + 10);	
			$ob_pdf->MultiCell(170, 6, 'Nome do empregado: '.$ar_usu['nome'],0,"J");	
			$ob_pdf->SetX(20);			
			$ob_pdf->MultiCell(170, 6, 'Re.d.: '.$ar_usu['cd_empresa']."/".$ar_usu['cd_registro_empregado']."/0",0,"J");		
			
			$ob_pdf->Output();
			exit;			
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
}
