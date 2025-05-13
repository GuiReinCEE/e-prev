<?php
class Pre_venda_agenda_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_pre_venda_agenda']) == 0)
		{
			$qr_sql = "
					INSERT INTO projetos.pre_venda_agenda
							  (
								dt_pre_venda_agenda,
								cd_pre_venda_agenda_tipo,
								observacao,
								cd_pre_venda,
								dt_inclusao,
								cd_usuario_inclusao,
								cd_agenda
							  )
						 VALUES
							  (
								TO_TIMESTAMP('".$args['dt_pre_venda_agenda_data']." ".$args['dt_pre_venda_agenda_hora']."', 'DD/MM/YYYY HH24:MI'),
								".(intval($args['cd_pre_venda_agenda_tipo']) > 0 ? intval($args['cd_pre_venda_agenda_tipo']) : "DEFAULT").",
								".(trim($args['observacao']) != "" ? "'".trim($args['observacao'])."'" : "DEFAULT").",
								".(intval($args['cd_pre_venda']) > 0 ? intval($args['cd_pre_venda']) : "DEFAULT").",
								CURRENT_TIMESTAMP,
								".intval($args['cd_usuario_inclusao']).",
							   (SELECT agendar 
								  FROM agenda.agendar(0,
													 ".intval($args['cd_usuario_inclusao']).",
													 ".(trim($args['dt_pre_venda_agenda_data']) != ''? "TO_TIMESTAMP('".$args['dt_pre_venda_agenda_data']." ".$args['dt_pre_venda_agenda_hora']."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
													 ".(trim($args['dt_pre_venda_agenda_data']) != ''? "TO_TIMESTAMP('".$args['dt_pre_venda_agenda_data']." ".$args['dt_pre_venda_agenda_hora']."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
													 COALESCE((SELECT ds_pre_venda_agenda_tipo
																 FROM projetos.pre_venda_agenda_tipo 
																WHERE cd_pre_venda_agenda_tipo = ".intval($args['cd_pre_venda_agenda_tipo'])."), 'Nуo Informado') || ': ' || (SELECT pv.nome FROM projetos.pre_venda pv WHERE pv.cd_pre_venda = ".intval($args['cd_pre_venda'])."),
													 'Fundaчуo',
'Nome: ' || (SELECT pv.nome FROM projetos.pre_venda pv WHERE pv.cd_pre_venda = ".intval($args['cd_pre_venda']).") || '
----------------------------------------------------------------------------------------------------
Link: https://www.e-prev.com.br/cieprev/index.php/ecrm/prevenda/contato/".intval($args['cd_pre_venda'])."
----------------------------------------------------------------------------------------------------
Descriчуo: ".trim($args['observacao'])."
----------------------------------------------------------------------------------------------------
Agendamento realizado por: '|| funcoes.get_usuario_nome(".intval($args['cd_usuario_inclusao']).") ||'
',
													 'S',
													 15,
													 funcoes.get_usuario(".intval($args['cd_usuario_inclusao']).") || '@eletroceee.com.br;previdencia@eletroceee.com.br')
													 )											   
										   
							  );
				  ";				
			
			
		}
		else
		{
            $qr_sql = "
					   UPDATE projetos.pre_venda_agenda
                          SET cd_pre_venda             = ".(intval($args['cd_pre_venda']) > 0 ? intval($args['cd_pre_venda']) : "DEFAULT").",
                              cd_pre_venda_agenda_tipo = ".(intval($args['cd_pre_venda_agenda_tipo']) > 0 ? intval($args['cd_pre_venda_agenda_tipo']) : "DEFAULT").",
                              dt_pre_venda_agenda      = TO_TIMESTAMP('".$args['dt_pre_venda_agenda_data']." ".$args['dt_pre_venda_agenda_hora']."', 'DD/MM/YYYY HH24:MI'),
                              observacao               = ".(trim($args['observacao']) != "" ? "'".trim($args['observacao'])."'" : "DEFAULT").",
							  cd_agenda                = (
															SELECT agendar 
															  FROM agenda.agendar(COALESCE((SELECT cd_agenda FROM projetos.pre_venda_agenda WHERE cd_pre_venda_agenda = ".$args['cd_pre_venda_agenda']."),0),
																				 ".intval($args['cd_usuario_inclusao']).",
																				 ".(trim($args['dt_pre_venda_agenda_data']) != ''? "TO_TIMESTAMP('".$args['dt_pre_venda_agenda_data']." ".$args['dt_pre_venda_agenda_hora']."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
																				 ".(trim($args['dt_pre_venda_agenda_data']) != ''? "TO_TIMESTAMP('".$args['dt_pre_venda_agenda_data']." ".$args['dt_pre_venda_agenda_hora']."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
																				 COALESCE((SELECT ds_pre_venda_agenda_tipo
																 FROM projetos.pre_venda_agenda_tipo 
																WHERE cd_pre_venda_agenda_tipo = ".intval($args['cd_pre_venda_agenda_tipo'])."), 'Nуo Informado') || ': ' || (SELECT pv.nome FROM projetos.pre_venda pv WHERE pv.cd_pre_venda = ".intval($args['cd_pre_venda'])."),
													 'Fundaчуo',
'Nome: ' || (SELECT pv.nome FROM projetos.pre_venda pv WHERE pv.cd_pre_venda = ".intval($args['cd_pre_venda']).") || '
----------------------------------------------------------------------------------------------------
Link: https://www.e-prev.com.br/cieprev/index.php/ecrm/prevenda/contato/".intval($args['cd_pre_venda'])."
----------------------------------------------------------------------------------------------------
Descriчуo: ".trim($args['observacao'])."
----------------------------------------------------------------------------------------------------
Agendamento realizado por: '|| funcoes.get_usuario_nome(".intval($args['cd_usuario_inclusao']).") ||'
',
															'S',
															15,
															funcoes.get_usuario(".intval($args['cd_usuario_inclusao']).") || '@eletroceee.com.br;previdencia@eletroceee.com.br')
														  )
                        WHERE cd_pre_venda_agenda = ".intval($args['cd_pre_venda_agenda']).";
				      ";			
		}
		
		$this->db->query($qr_sql);

	}
	
    function combo_agenda_tipo(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT cd_pre_venda_agenda_tipo AS value,
                           ds_pre_venda_agenda_tipo AS text
                      FROM projetos.pre_venda_agenda_tipo
                     WHERE dt_exclusao IS NULL
                     ORDER BY text
                  ";
        $result = $this->db->query($qr_sql);
    } 	
	
	private function setup_pagination($count, $page="")
	{
		// Setup pagination
		$config['enable_query_strings'] = FALSE;
		$config['base_url'] = $this->config->item('base_url') . index_page(). '/' . $page;
		$config['per_page'] = 10000;
		$config['total_rows'] = $count;
		$this->pagination->initialize($config);
	}
}
?>