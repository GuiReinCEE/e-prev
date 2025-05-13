<?php
class contrato_formulario_pergunta extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

        $this->load->view('cadastro/contrato_formulario_pergunta/index.php');
    }

    function listar()
    {
        CheckLogin();
        $this->load->model('projetos/Contrato_formulario_pergunta_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();

		$args["cd_contrato_formulario"] = intval($this->input->post("cd_contrato_formulario", TRUE));
		$args["cd_contrato_formulario_grupo"] = intval($this->input->post("cd_contrato_formulario_grupo", TRUE));

		// --------------------------
		// listar ...

        $this->Contrato_formulario_pergunta_model->listar( $result, $args );

		$data['collection'] = $result->result_array();

        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

        $this->load->view('cadastro/contrato_formulario_pergunta/partial_result', $data);
    }

	function detalhe($cd_pergunta=0)
	{
		$data['row'] = array('cd_contrato_formulario_pergunta'=>0, 'cd_contrato_formulario'=>0, 'cd_contrato_formulario_grupo'=>0);

		if(intval($cd_pergunta)>0)
		{
			$sql = "
				SELECT a.*, b.cd_contrato_formulario 
				FROM projetos.contrato_formulario_pergunta a 
				JOIN projetos.contrato_formulario_grupo b 
				ON a.cd_contrato_formulario_grupo=b.cd_contrato_formulario_grupo 
				WHERE cd_contrato_formulario_pergunta={codigo}
				AND a.dt_exclusao IS NULL
			";
			esc( "{codigo}", intval($cd_pergunta), $sql );

			$query=$this->db->query($sql);
			$row=$query->row_array();
			if($row) $data['row'] = $row;
		}

		$this->load->view('cadastro/contrato_formulario_pergunta/detalhe', $data);
	}

	function salvar()
	{
		$codigo=$this->input->post('cd_contrato_formulario_pergunta', TRUE);
		if(intval($codigo)==0)
		{
			$sql="
			INSERT INTO projetos.contrato_formulario_pergunta
			(
			ds_contrato_formulario_pergunta
			, cd_contrato_formulario_grupo
			, fl_multipla_resposta
			, nr_ordem
			, dt_inclusao
			, cd_usuario_inclusao
			, dt_exclusao
			, cd_usuario_exclusao
			)
			VALUES 
			(
			'{ds_contrato_formulario_pergunta}'
			, {cd_contrato_formulario_grupo}
			, '{fl_multipla_resposta}'
			, {nr_ordem}
			, CURRENT_TIMESTAMP
			, {cd_usuario_inclusao}
			, null
			, null
			);
			";
		}
		else
		{
			$sql="
			UPDATE projetos.contrato_formulario_pergunta
			SET ds_contrato_formulario_pergunta='{ds_contrato_formulario_pergunta}'
				, cd_contrato_formulario_grupo={cd_contrato_formulario_grupo}
				, fl_multipla_resposta='{fl_multipla_resposta}'
				, nr_ordem={nr_ordem}
			WHERE cd_contrato_formulario_pergunta={cd_contrato_formulario_pergunta};
			";
		}

		esc('{ds_contrato_formulario_pergunta}', $this->input->post('ds_contrato_formulario_pergunta', TRUE), $sql, 'str', FALSE);
		esc('{cd_contrato_formulario_grupo}', $this->input->post('cd_contrato_formulario_grupo', TRUE), $sql, 'int');
		esc('{fl_multipla_resposta}', $this->input->post('fl_multipla_resposta', TRUE), $sql, 'str');
		esc('{nr_ordem}', $this->input->post('nr_ordem', TRUE), $sql, 'int');
		esc('{cd_usuario_inclusao}', usuario_id(), $sql, 'int');
		esc('{cd_contrato_formulario_pergunta}', $this->input->post('cd_contrato_formulario_pergunta', TRUE), $sql, 'int');

		$query = $this->db->query($sql);

		// echo "<pre>$sql</pre>";exit;

		redirect( "cadastro/contrato_formulario_pergunta", "refresh" );
	}

	function carregar_combo_grupo($cd_formulario)
	{
		if(intval($cd_formulario)==0) { echo "Formulário inválido!"; exit; }

		echo form_dropdown_db(
			"cd_contrato_formulario_grupo"
			, array("projetos.contrato_formulario_grupo", "cd_contrato_formulario_grupo", "ds_contrato_formulario_grupo")
			, ''
			, ''
			, ' dt_exclusao IS NULL AND cd_contrato_formulario='.intval($cd_formulario)
		);
	}
}
