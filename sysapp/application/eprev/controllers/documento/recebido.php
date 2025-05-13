<?php
class recebido extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
		$this->load->view("documento/recebido/index");
	}

	function protocolo()
	{
		$query = $this->db->query('
			SELECT 
				cd_documento_recebido_tipo as value
				, ds_tipo as text 
			FROM projetos.documento_recebido_tipo 
			ORDER BY ds_tipo;
		');
		$view_args['tipo_documentos_collection'] = $query->result_array();

		$this->load->view( 'documento/recebido/protocolo.php', $view_args );
	}

	function documento()
	{
		// Dropdown protocolos
		$this->load->model("projetos/Documento_recebido");
		$view_args['documento_recebido_collection'] = $this->Documento_recebido->select_dropdown($this->session->userdata('codigo'));

		// Dropdown tipo de documentos
		$this->load->model("public/Tipo_documentos");
		$view_args['tipo_documentos_collection'] = $this->Tipo_documentos->select_dropdown();

		// Dropdown Gerências
		$this->load->model("projetos/Divisoes");
		$view_args['gerencia_collection'] = $this->Divisoes->select_dropdown();

		// Dropdown Gerências
		$gerencia = 'GAP';
		$this->load->model("projetos/Usuarios_controledi");
		$view_args['usuario_destino_collection'] = $this->Usuarios_controledi->select_dropdown_1( $gerencia );

		$this->load->view( 'documento/recebido/documento.php', $view_args );
	}

	function criar()
	{
		//------------------------

		$cd_documento_recebido_tipo = (int)$this->input->post('cd_documento_recebido_tipo');
		$cd_usuario_cadastro = (int)$this->session->userdata("codigo");
		$nr_ano = (int)$this->input->post("nr_ano");
		$nr_contador = (int)$this->input->post("nr_contador");

		//------------------------

		if($cd_usuario_cadastro==0) 		return show_error("Nenhum usuário logado.");
		if($cd_documento_recebido_tipo==0) 	return show_error("Tipo de documento não informado.");
		if($nr_ano==0) 						return show_error("Ano não informado.");
		if($nr_contador==0) 				return show_error("Número sequencial não informado.");

		$s = "
			INSERT INTO projetos.documento_recebido ( cd_documento_recebido_tipo, cd_usuario_cadastro, dt_cadastro, dt_envio, nr_ano, nr_contador ) 
			VALUES ( ?, ?, CURRENT_TIMESTAMP, NULL, ?, ? );
		";

		$dados = array($cd_documento_recebido_tipo,$cd_usuario_cadastro,$nr_ano,$nr_contador);
		if ( ($result=$this->db->query($s, $dados)) === TRUE )
		{
			redirect( 'documento/recebido/protocolo' );
		}
		else
		{
			show_error("Um erro ocorreu ao tentar incluir o protocolo.");
		}

		//------------------------
	}

	function adicionar()
	{
		if($this->input->post('cd_documento_recebido')=='') { show_error('protocolo não informado'); return FALSE; }
		if($this->input->post('cd_empresa')=='') 			{ show_error('participante não informado'); return FALSE; }
		if($this->input->post('cd_registro_empregado')=='') { show_error('participante não informado'); return FALSE; }
		if($this->input->post('seq_dependencia')=='') 		{ show_error('participante não informado'); return FALSE; }
		if($this->input->post('nr_folha')=='') 				{ show_error('número de folhas não informado'); return FALSE; }
		if($this->input->post('nr_folha')=='0') 			{ show_error('número de folhas não informado'); return FALSE; }
		if($this->input->post('cd_tipo_doc')=='') 			{ show_error('tipo de documento não informado'); return FALSE; }

		$dados['cd_documento_recebido'] = (int)$this->input->post('cd_documento_recebido');
		$dados['cd_empresa'] = (int)$this->input->post('cd_empresa');
		$dados['cd_registro_empregado'] = (int)$this->input->post('cd_registro_empregado');
		$dados['seq_dependencia'] = (int)$this->input->post('seq_dependencia');
		$dados['ds_observacao'] = $this->input->post('ds_observacao');
		$dados['nr_folha'] = (int)$this->input->post('nr_folha');
		$dados['cd_tipo_doc'] = (int)$this->input->post('cd_tipo_doc');
		$dados['cd_usuario_destino'] = (int)$this->input->post('cd_usuario_destino');
		$dados['cd_usuario_cadastro'] = (int)$this->session->userdata('codigo');

		$this->load->model( 'projetos/Documento_recebido' );
		if( ($result=$this->Documento_recebido->adicionar_item( $dados ))===TRUE )
		{
			redirect( 'documento/recebido/documento' );
		}
		else
		{
			show_error("Um erro ocorreu ao tentar incluir o protocolo.");
		}
	}

	function listar()
	{
		$count = 0;
		$count_itens = 0;
		$data = array();
		$this->load->model( "projetos/Documento_recebido" );
		$args['page'] = $this->input->post('current_page');

		$this->Documento_recebido->lista_documento( $result_protocolo, $count, $args );
		$grupo = $result_protocolo->result_array();
		$data['quantos'] = $count;

		$indice = 0;
		for ($indice=0;$indice<sizeof($grupo);$indice++)
		{
			$args['cd_documento_recebido'] = $grupo[$indice]['cd_documento_recebido'];
			$this->Documento_recebido->lista_item( $result_item, $count_itens, $args );
			$grupo[$indice]['collection'] = $result_item->result_array();
		}

		$data['collection'] = $grupo;

		$data['cd_usuario_logado'] = $this->session->userdata('codigo');

		$this->load->view( "documento/recebido/partial_result", $data );
	}

	/**
	 * Recebe dados por post e realiza alteração na base 
	 * gravando data e usuário de destino para documento
	 *
	 */
	function salvar_envio()
	{
		$dados = array();
		if(isset($_POST['cd_documento_recebido'])) $dados['cd_documento_recebido'] = (int)$_POST['cd_documento_recebido'];
		if(isset($_POST['cd_usuario_destino'])) $dados['cd_usuario_destino'] = (int)$_POST['cd_usuario_destino'];

		// salvar dados
		$this->load->model('projetos/Documento_recebido');
		if($this->Documento_recebido->salvar_envio_item( $dados ))
		{
			echo 'true';
		}
		else
		{
			show_error("Erro ao tentar salvar o envio de documento.");
		}
	}
	
	function tipo()
	{
		$this->load->view("documento/tipo/index");
	}
}
?>