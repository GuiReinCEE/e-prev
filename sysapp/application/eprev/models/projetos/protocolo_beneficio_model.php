<?php
class Protocolo_beneficio_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
		SELECT

			pb.cd_protocolo_beneficio
			, pb.nr_protocolo
			, pb.nr_ano
			, pb.cd_empresa
			, pb.cd_registro_empregado
			, pb.seq_dependencia
			, pb.nome
			, pb.observacao
			, pb.endereco
			, pb.bairro
			, pb.cidade
			, pb.uf
			, pb.cep
			, to_char(pb.dt_inclusao,'DD/MM/YYYY') AS dt_inclusao
			, ds_protocolo_beneficio_assunto
			, ds_protocolo_beneficio_forma_envio
			, uc.nome as nome_usuario_inclusao

		FROM projetos.protocolo_beneficio pb
		JOIN projetos.protocolo_beneficio_assunto pba ON pb.cd_protocolo_beneficio_assunto=pba.cd_protocolo_beneficio_assunto
		JOIN projetos.protocolo_beneficio_forma_envio pbfe ON pb.cd_protocolo_beneficio_forma_envio=pbfe.cd_protocolo_beneficio_forma_envio
		JOIN projetos.usuarios_controledi uc ON uc.codigo=pb.cd_usuario_inclusao

		WHERE

			pb.dt_exclusao IS NULL
			AND ( pb.nr_protocolo={nr_protocolo} OR {nr_protocolo}=0 )
			AND ( pb.nr_ano={nr_ano} OR {nr_ano}=0 )
			AND ( date_trunc('day', pb.dt_inclusao) BETWEEN to_date('{inclusao_inicio}', 'DD/MM/YYYY') AND to_date('{inclusao_fim}', 'DD/MM/YYYY') )
			AND ( pb.cd_empresa={cd_empresa} OR {cd_empresa}=-1 )
			AND ( pb.cd_registro_empregado={cd_registro_empregado} OR {cd_registro_empregado}=0 )
			AND ( pb.seq_dependencia={seq_dependencia} OR {seq_dependencia}=-1 )
			AND ( pb.nome like '%{nome}%' )
			AND ( pb.cd_protocolo_beneficio_assunto={cd_protocolo_beneficio_assunto} OR {cd_protocolo_beneficio_assunto}=0 )
			AND ( pb.cd_protocolo_beneficio_forma_envio={cd_protocolo_beneficio_forma_envio} OR {cd_protocolo_beneficio_forma_envio}=0 )
			AND ( pb.cd_usuario_inclusao={cd_usuario_inclusao} OR {cd_usuario_inclusao}=0 )

		";

		// parse query ...
		esc( "{nr_protocolo}", $args["nr_protocolo"], $sql, "int" );
		esc( "{nr_ano}", $args["nr_ano"], $sql, "int" );
		esc( "{inclusao_inicio}", $args["dt_inclusao_inicio"], $sql );
		esc( "{inclusao_fim}", $args["dt_inclusao_fim"], $sql );
		esc( "{cd_empresa}", $args["cd_empresa"], $sql, "int" );
		esc( "{cd_registro_empregado}", $args["cd_registro_empregado"], $sql, "int" );
		esc( "{seq_dependencia}", $args["seq_dependencia"], $sql, "int" );
		esc( "{nome}", $args["nome"], $sql );
		esc( "{cd_protocolo_beneficio_assunto}", $args["cd_protocolo_beneficio_assunto"], $sql, "int" );
		esc( "{cd_protocolo_beneficio_forma_envio}", $args["cd_protocolo_beneficio_forma_envio"], $sql, "int" );
		esc( "{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int" );

		// return result ...
		$result = $this->db->query($sql);
	}
}
?>