<?php
class email_reclamacao extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

        $this->load->view('ecrm/email_reclamacao/index.php');
    }

    function listar()
    {
        CheckLogin();
        $this->load->model('projetos/Atendimento_programa_gerencia_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();

		manter_filtros($args);

		// --------------------------
		// listar ...

        $this->Atendimento_programa_gerencia_model->listar( $result, $args );

		$data['collection'] = $result->result_array();

        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

        $this->load->view('ecrm/email_reclamacao/partial_result', $data);
    }

	function detalhe($cd=0)
	{
		$sql = " SELECT * 
			FROM projetos.atendimento_programa_gerencia
		 ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_atendimento_programa_gerencia={cd_atendimento_programa_gerencia} ";
			esc( "{cd_atendimento_programa_gerencia}", intval($cd), $sql );
			$query=$this->db->query($sql);
			$row=$query->row_array();
		}

		if($row) $data['row'] = $row;
		$this->load->view('ecrm/email_reclamacao/detalhe', $data);
	}

	function salvar()
	{
		CheckLogin();

		$codigo=$this->input->post('cd_atendimento_programa_gerencia', TRUE);

		$args["cd_programa"] = $this->input->post("cd_programa",TRUE);
		$args["cd_usuario"] = $this->input->post("cd_usuario",TRUE);
		$args["cd_atendimento_programa_gerencia"] = $this->input->post("cd_atendimento_programa_gerencia",TRUE);

		if(intval($codigo)==0)
		{
			$sql="
				INSERT INTO projetos.atendimento_programa_gerencia ( cd_programa 
				, cd_usuario 
				) VALUES ( '{cd_programa}'
				, {cd_usuario} 
				)
			";
		}

		esc("{cd_programa}", $args["cd_programa"], $sql, "str", FALSE);
		esc("{cd_usuario}", $args["cd_usuario"], $sql, "int", FALSE);
		esc("{cd_atendimento_programa_gerencia}", $args["cd_atendimento_programa_gerencia"], $sql, "int", FALSE);

		//echo $sql;exit;
		$query = $this->db->query($sql);

		redirect( "ecrm/email_reclamacao", "refresh" );
	}

	function excluir($id)
	{
		CheckLogin();

		$sql = "
		UPDATE projetos.atendimento_programa_gerencia
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao}
		WHERE md5(cd_atendimento_programa_gerencia::varchar)='{cd_atendimento_programa_gerencia}'
		";
		esc("{cd_usuario_exclusao}", usuario_id(), $sql, 'int');
		esc("{cd_atendimento_programa_gerencia}", $id, $sql, 'str');

		$query=$this->db->query($sql);

		// echo $sql;

		redirect( 'ecrm/email_reclamacao', 'refresh' );
	}
}
?>