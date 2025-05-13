<?php
class contrato_formulario_grupo extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

        $this->load->view('cadastro/contrato_formulario_grupo/index.php');
    }

    function listar()
    {
        CheckLogin();
        $this->load->model('projetos/Contrato_formulario_grupo_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();

		$args["formulario"] = $this->input->post("formulario", TRUE);


		// --------------------------
		// listar ...

        $this->Contrato_formulario_grupo_model->listar( $result, $args );

		$data['collection'] = $result->result_array();

        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

        $this->load->view('cadastro/contrato_formulario_grupo/partial_result', $data);
    }

	function detalhe($cd=0)
	{
		$sql = " SELECT * 
FROM projetos.contrato_formulario_grupo ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_contrato_formulario_grupo={cd_contrato_formulario_grupo} ";
			esc( "{cd_contrato_formulario_grupo}", intval($cd), $sql );
			$query=$this->db->query($sql);
			$row=$query->row_array();
		}

		if($row) $data['row'] = $row;
		$this->load->view('cadastro/contrato_formulario_grupo/detalhe', $data);
	}

	function salvar()
	{
		CheckLogin();

		$codigo=$this->input->post('cd_contrato_formulario_grupo', TRUE);
		if(intval($codigo)==0)
		{
			$sql="
			INSERT INTO projetos.contrato_formulario_grupo ( ds_contrato_formulario_grupo 
, cd_contrato_formulario 
, dt_inclusao 
, cd_usuario_inclusao 
, nr_ordem 
) VALUES ( '{ds_contrato_formulario_grupo}' 
, {cd_contrato_formulario} 
, CURRENT_TIMESTAMP 
, {cd_usuario_inclusao} 
, {nr_ordem} 
)

			";
		}
		else
		{
			$sql="
			UPDATE projetos.contrato_formulario_grupo SET 
 ds_contrato_formulario_grupo = '{ds_contrato_formulario_grupo}' 
, cd_contrato_formulario = {cd_contrato_formulario} 
, nr_ordem = {nr_ordem} 
 WHERE 
cd_contrato_formulario_grupo = {cd_contrato_formulario_grupo} 
			";
		}

		esc("{ds_contrato_formulario_grupo}", $this->input->post("ds_contrato_formulario_grupo",TRUE), $sql, "str", FALSE);
esc("{cd_contrato_formulario}", $this->input->post("cd_contrato_formulario",TRUE), $sql, "int", FALSE);
esc("{cd_usuario_inclusao}", usuario_id(), $sql, "int", FALSE);
esc("{nr_ordem}", $this->input->post("nr_ordem",TRUE), $sql, "int", FALSE);
esc("{cd_contrato_formulario_grupo}", $this->input->post("cd_contrato_formulario_grupo",TRUE), $sql, "int", FALSE);


		$query = $this->db->query($sql);

		redirect( "cadastro/contrato_formulario_grupo", "refresh" );
	}

	function excluir($id)
	{
		CheckLogin();

		$sql = "
		UPDATE projetos.contrato_formulario_grupo
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao}
		WHERE md5(cd_contrato_formulario_grupo::varchar)='{cd_contrato_formulario_grupo}'
		";
		esc("{cd_usuario_exclusao}", usuario_id(), $sql, 'int');
		esc("{cd_contrato_formulario_grupo}", $id, $sql, 'str');

		$query=$this->db->query($sql);

		// echo $sql;

		redirect( 'cadastro/contrato_formulario_grupo', 'refresh' );
	}
}
