<?php
class comprovante_irpf_colaborador_arquivo_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
	function listar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT cic.cd_comprovante_irpf_colaborador, 
					       cic.ds_arquivo_nome,
						   cic.ds_arquivo_fisico,
						   cic.nr_ano_exercicio, 
						   cic.nr_ano_calendario,
						   TO_CHAR(dt_carga,'DD/MM/YYYY HH24:MI:SS') AS dt_carga,
						   funcoes.get_usuario_nome(cic.cd_usuario_carga) AS usuario_carga,
						   TO_CHAR(cic.dt_liberacao,'DD/MM/YYYY HH24:MI:SS') AS dt_liberacao,
						   funcoes.get_usuario_nome(cic.cd_usuario_liberacao) AS usuario_liberacao,
						   (SELECT COUNT(DISTINCT cici.cd_registro_empregado) 
						      FROM projetos.comprovante_irpf_colaborador_item cici
							 WHERE cici.cd_comprovante_irpf_colaborador = cic.cd_comprovante_irpf_colaborador) AS qt_comprovante
					  FROM projetos.comprovante_irpf_colaborador cic
					 WHERE cic.dt_exclusao IS NULL
                       ".(trim($args['nr_ano_calendario']) != '' ? "AND cic.nr_ano_calendario = ".intval($args['nr_ano_calendario']) : "")."
                       ".(trim($args['nr_ano_exercicio']) != '' ? "AND cic.nr_ano_exercicio = ".intval($args['nr_ano_exercicio']) : "")."
					ORDER BY cic.nr_ano_calendario DESC
			      ";
        #echo "<PRE>$qr_sql</PRE>";
		$result = $this->db->query($qr_sql);
    }
	
	function liberar(&$result, $args=array())
    {
        $qr_sql = "
					UPDATE projetos.comprovante_irpf_colaborador
					   SET dt_liberacao         = CURRENT_TIMESTAMP,
					       cd_usuario_liberacao = ".intval($args["cd_usuario"])."
					 WHERE cd_comprovante_irpf_colaborador = ".intval($args["cd_comprovante_irpf_colaborador"])."
			      ";
        #echo "<PRE>$qr_sql</PRE>";
		$result = $this->db->query($qr_sql);
    }

	function excluir(&$result, $args=array())
    {
        $qr_sql = "
					UPDATE projetos.comprovante_irpf_colaborador
					   SET dt_exclusao         = CURRENT_TIMESTAMP,
					       cd_usuario_exclusao = ".intval($args["cd_usuario"])."
					 WHERE cd_comprovante_irpf_colaborador = ".intval($args["cd_comprovante_irpf_colaborador"])."
			      ";
        #echo "<PRE>$qr_sql</PRE>";
		$result = $this->db->query($qr_sql);
    }	
	
	function salvar(&$result, $args=array())
	{
		$ar_infor = $args["ar_infor"];
		$ar_colab = $args["ar_colab"];
		
		#### INSERE ARQUIVO ####
		$cd_new = intval($this->db->get_new_id("projetos.comprovante_irpf_colaborador", "cd_comprovante_irpf_colaborador"));
		$qr_sql = "
					INSERT INTO projetos.comprovante_irpf_colaborador
					     (
							cd_comprovante_irpf_colaborador, 
							nr_ano_exercicio, 
							nr_ano_calendario, 
							ds_arquivo_nome, 
							ds_arquivo_fisico, 
							cd_usuario_carga
			             )
					VALUES 
					     (
							".$cd_new.",
							".$args["nr_ano_exercicio"].",
							".$args["nr_ano_calendario"].",
							'".$args["ds_arquivo_nome"]."',
							'".$args["ds_arquivo_fisico"]."',
							".$args["cd_usuario_carga"]."
						 );
		          ";
				  
			
		#### INSERE ITENS ####	
		$nr_conta = 0;
		$nr_fim = count($ar_colab)-1;
		while($nr_conta <= $nr_fim)
		{
			if((trim($ar_colab[$nr_conta]["CPF"]) != "") and (intval(trim($ar_colab[$nr_conta]["RE"])) > 0))
			{
				#echo "<PRE>".print_r($ar_infor[$nr_conta],true)."</PRE>"; #exit;
				
				$ar_item = $ar_infor[$nr_conta];
				
				#echo "<PRE>".print_r($ar_item,true)."</PRE>"; exit;
				
				$nr_idx      = count($ar_item)-1;
				$fl_limpar = true;
				while($fl_limpar)
				{
					if(trim($ar_item[$nr_idx]) == "")
					{
						unset($ar_item[$nr_idx]);
					}
					else
					{
						$fl_limpar = false;
					}
					$nr_idx--;
				}
				
				$nr_linha = 1;
				foreach($ar_item as $item)
				{
					#echo "<PRE>".print_r($item,true)."</PRE>"; exit;
					$qr_sql.= "
								INSERT INTO projetos.comprovante_irpf_colaborador_item
									 (
										cd_comprovante_irpf_colaborador, 
										cpf, 
										cd_registro_empregado, 
										nr_linha,
										linha, 
										cd_usuario_inclusao
									 )
								VALUES
									 (
										".$cd_new.",
										'".trim($ar_colab[$nr_conta]["CPF"])."',
										".intval(trim($ar_colab[$nr_conta]["RE"])).",
										".$nr_linha.",
										E'".$item."',
										170
									 );	
							  ";
					$nr_linha++;
				}
				
			}
			
			#echo "<PRE>".print_r($qr_sql,true)."</PRE>"; exit;
			$nr_conta++;
		}
		
		#echo "<PRE>".print_r($qr_sql,true)."</PRE>"; exit;		
		$result = $this->db->query($qr_sql);
	}
	
	function itemListar(&$result, $args=array())
    {
		$qr_sql = "
					SELECT DISTINCT cici.cd_comprovante_irpf_colaborador, 
						   cici.cpf, 
						   cici.cd_registro_empregado, 
						   funcoes.remove_acento(UPPER(uc.nome)) AS nome
					  FROM projetos.comprovante_irpf_colaborador_item cici
					  JOIN projetos.comprovante_irpf_colaborador cic
						ON cic.cd_comprovante_irpf_colaborador = cici.cd_comprovante_irpf_colaborador
					   AND cic.dt_exclusao IS NULL
					  JOIN projetos.usuarios_controledi uc
						ON uc.cd_registro_empregado = cici.cd_registro_empregado
					   AND uc.cd_patrocinadora      = 9
					 WHERE cici.dt_exclusao IS NULL
					   AND cici.cd_comprovante_irpf_colaborador = ".intval($args["cd_comprovante_irpf_colaborador"])."
                       ".(trim($args['cd_re_colaborador']) != '' ? "AND cici.cd_registro_empregado = ".intval($args['cd_re_colaborador']) : "")."																		   
                       ".(trim($args['nome']) != '' ? "AND funcoes.remove_acento(UPPER(uc.nome)) LIKE funcoes.remove_acento(UPPER('%".trim($args['nome'])."%'))" : "")."																		   
			      ";		
        #echo "<PRE>$qr_sql</PRE>"; #exit;
		$result = $this->db->query($qr_sql);
    }

	function itemExcluir(&$result, $args=array())
    {
        $qr_sql = "
					UPDATE projetos.comprovante_irpf_colaborador_item
					   SET dt_exclusao         = CURRENT_TIMESTAMP,
					       cd_usuario_exclusao = ".intval($args["cd_usuario"])."
					 WHERE cd_comprovante_irpf_colaborador = ".intval($args["cd_comprovante_irpf_colaborador"])."
					   AND cd_registro_empregado           = ".intval($args['cd_re_colaborador'])."
			      ";
        #echo "<PRE>$qr_sql</PRE>";
		$result = $this->db->query($qr_sql);
    }	
}
?>