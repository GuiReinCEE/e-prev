<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
include_once('inc/sessao.php');
include_once('inc/conexao.php');

include_once('inc/ePrev.Service.Projetos.php');

class avaliacao_partial_resultado
{ #begin_class
    private $db;
    private $command;
    private $id;
    private $cd_usuario_logado;
    private $usuario_avaliado;
    private $usuario_avaliador;
    private $row;
    private $avaliacao;

    private $grau_ci;
    private $val_ci;
    private $grau_esc;
    private $val_esc;
    private $grau_ce;
    private $val_ce;
    private $grau_resp;
    private $val_resp;
    private $grau_1;
    private $grau_2;
    private $grau_final;
    private $media_ci_comite;

    // Collections
    private $result_comp_inst;
    private $result_escolaridade;
    private $result_comp_espec;
    private $result_resp;
    
    // Entidades
    private $capas;
    public $capa;

    function avaliacao_partial_resultado( $_db )
    {
        $this->db = $_db;
        $this->avaliacao = new entity_projetos_avaliacao();
        $this->requestParams();
        if ($this->command=="view") 
        {
			$this->load();
		}
        if ($this->command=="edit") 
        {
            $this->load();
        }
        if ($this->command=="publicar_avaliacao") 
        {
            $this->ajax_publicar();
        }
        
    	if ($this->command!="publicar_avaliacao")
    	{
	        // se o avaliado desta avaliação for o usuário logado então redireciona pra lista
			if( ! $this->pode_ver_essa_pagina($this->capa) )
			{
				echo '<center><span style="font-family: verdana; font-weight: bold;">ACESSO NÃO PERMITIDO.</span></center>';
				exit;
			}
    	}
    }
    private function pode_ver_essa_pagina( entity_projetos_avaliacao_capa_extended $capa )
    {
    	$sr = false;
    	// se avaliado é o usuário logado e avaliação ainda não finalizada/publicada
    	if( $capa->get_cd_usuario_avaliado()==$this->cd_usuario_logado && $capa->get_dt_publicacao()=='' )
    	{
    		$sr = false;
    	}
    	else
    	{
    		// se usuario logado é superior na avaliação OU 
    		// se usuário logado é avaliado
    		if( $this->cd_usuario_logado==$capa->get_cd_usuario_avaliador() OR $this->cd_usuario_logado==$capa->get_cd_usuario_avaliado() )
    		{
	    		$sr = true;
    		}
    		else
    		{
    			$sr = false;
    			// se usuário logado é integrante do comite da avaliação
    			$integrante = new entity_projetos_avaliacao_comite_extended();
    			foreach( $capa->comite as $integrante )
    			{
    				if($integrante)
	    				if( $integrante->get_cd_usuario_avaliador()==$this->cd_usuario_logado )
	    				{
	    					$sr = true;
	    					break;
	    				}
    			}
    		}
    	}
    	
    	return $sr;
    }

    function __destruct()
    {
        $this->db = null;
    }

    public function ajax_publicar()
    {
    	$srv = new service_projetos($this->db);
        $capa = new entity_projetos_avaliacao_capa_extended();
        $capa->set_cd_avaliacao_capa( $_POST["cd_avaliacao_selected_hidden"] );
        $capa->set_status( "C" );
        $capa->set_media_geral( $_POST["media_geral__text"] );
        $retorno = $srv->avaliacao_capa_Publicar( $capa );
        
        if ($retorno)
        {
			// retorno para tratamento no javascript que chamou o método
            echo("1");
		}
        else
        {
            echo("0");
        }
        
        $capa = null;
        $srv = null;
    }
    
    public function get_command()
    {
        return $this->command;
    }

    function requestParams()
    {
        $this->id = $_POST["cd_avaliacao_selected_hidden"];
        $this->command = utf8_decode( $_POST["ajax_command_hidden"] );
        $this->cd_usuario_logado = $_SESSION["Z"];
        
        $this->avaliacao->set_cd_avaliacao( $this->id );
    }
    
    function load()
    {
        $filtro = new entity_projetos_avaliacao_capa_extended();
        $filtro->set_cd_avaliacao_capa( $this->id );
        
        $service = new service_projetos($this->db);
        $this->capas = $service->avaliacao_capa_FetchAll( $filtro );
        $this->capa = $this->capas[0];
        
        $this->capas = null;
        $filtro = null;
        $service = null;
    }

    function load_valores( $cd )
    {
        $dal = new DBConnection();
        $dal->loadConnection( $this->db );

        // VAL_ESC
        $dal->createQuery("

            SELECT grau_escolaridade 
              FROM projetos.avaliacao_capa
             WHERE cd_avaliacao_capa = {cd_avaliacao_capa} 

        ");
        $dal->setAttribute("{cd_avaliacao_capa}", (int)$this->capa->get_cd_avaliacao_capa());
        $result_esc = $dal->getResultset();
        $row_esc = pg_fetch_array($result_esc);
        $this->val_esc = $row_esc["grau_escolaridade"];
        $row_esc = null;

        // load grau_ci
        $dal->createQuery("

            SELECT SUM(grau) AS grau_ci 
              FROM projetos.avaliacoes_comp_inst 
             WHERE cd_avaliacao = {cd_avaliacao} 

        ");
        $dal->setAttribute("{cd_avaliacao}", (int)$cd);
        $result_grau_ci = $dal->getResultset();
        $row_grau_ci = pg_fetch_array($result_grau_ci);
        $this->grau_ci = $row_grau_ci["grau_ci"];

        // load val_ci
        $dal->createQuery("

            SELECT COUNT(*) AS ocorr_ci 
              FROM projetos.avaliacoes_comp_inst 
             WHERE cd_avaliacao = {cd_avaliacao} 

        ");
        $dal->setAttribute("{cd_avaliacao}", (int)$cd);
        $result_val_ci = $dal->getResultset();
        $row_val_ci = pg_fetch_array($result_val_ci);
        if ($row_val_ci["ocorr_ci"] == "0") 
        {   
            $this->val_ci = "Não realizada!";
        }
        else 
        {
            $this->val_ci = number_format(($this->grau_ci / $row_val_ci['ocorr_ci']), 2);
        }
        
        // GRAU_CE e VAL_CE
        $dal->createQuery("

            SELECT SUM(grau) AS grau_ce
              FROM projetos.avaliacoes_comp_espec
             WHERE cd_avaliacao = {cd_avaliacao}

        ");
        $dal->setAttribute("{cd_avaliacao}", (int)$cd);
        $result_grau_ce = $dal->getResultset();
        $row_grau_ce = pg_fetch_array($result_grau_ce);
        $this->grau_ce = $row_grau_ce["grau_ce"];

        // load val_ce
        $dal->createQuery("

            SELECT COUNT(*) AS ocorr_ce
              FROM projetos.avaliacoes_comp_espec
             WHERE cd_avaliacao = {cd_avaliacao} 

        ");
        $dal->setAttribute( "{cd_avaliacao}", (int)$cd );
        $result_val_ce = $dal->getResultset();
        // echo( $dal->getMessage() );
        $row_val_ce = pg_fetch_array($result_val_ce);
        if ($row_val_ce["ocorr_ce"] == "0") 
        {   
            $this->val_ce = "Não realizada!";
        }
        else 
        {
            $this->val_ce = number_format( ($this->grau_ce / $row_val_ce['ocorr_ce']), 2 );
        }
        

        // GRAU_RESP e VAL_RESP
        $dal->createQuery("

            SELECT SUM(grau) AS grau_resp
              FROM projetos.avaliacoes_responsabilidades 
             WHERE cd_avaliacao = {cd_avaliacao} 

        ");
        $dal->setAttribute("{cd_avaliacao}", (int)$cd);
        $result_grau_resp = $dal->getResultset();
        $row_grau_resp = pg_fetch_array($result_grau_resp);
        $this->grau_resp = $row_grau_resp["grau_resp"];
        
        $dal->createQuery("

            SELECT COUNT(*) AS ocorr_resp
              FROM projetos.avaliacoes_responsabilidades
             WHERE cd_avaliacao = {cd_avaliacao}

        ");
        $dal->setAttribute( "{cd_avaliacao}", (int)$cd);
        $result_val_resp = $dal->getResultset();
        $row_val_resp = pg_fetch_array($result_val_resp);
        if ($row_val_resp["ocorr_resp"] == "0") 
        {   
            $this->val_resp = "Não realizada!";
        }
        else 
        {
            $this->val_resp = number_format( ($this->grau_resp / $row_val_resp['ocorr_resp']), 2 );
        }
        
        $dal = null;
    }
    public function get_val_ci()
    {
        return $this->val_ci;
    }
    public function get_val_resp()
    {
        return $this->val_resp;
    }
    public function get_val_ce()
    {
        return $this->val_ce;
    }
    public function get_val_esc()
    {
        return $this->val_esc;
    }
    public function get_grau_1()
    {
        $this->grau_1 = ($this->val_ci+$this->val_esc)/2;
        return $this->grau_1;
    }
    public function get_grau_2()
    {
        $this->grau_2 = ($this->val_ce+$this->val_resp)/2;
        return $this->grau_2;
    }
    public function get_grau_final()
    {
        $resultado = ( (  $this->grau_1*40  ) + (  $this->grau_2*60  ) ) / 100;
        return $resultado;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_cd_usuario_logado()
    {
        return $this->cd_usuario_logado;
    }

    public function get_avaliacao()
    {
        return $this->avaliacao;
    }

    public function calcula_media_ci_comite()
    {
		$idx=0;
        // Para cada avaliação guarda em array a média de grau de CI. Apenas para avaliações de Comite ou Superior
        foreach($this->capa->avaliacoes as $avaliacao)
        {
            if (!is_null($avaliacao))
            {
                if ($avaliacao->get_tipo()=="C" || $avaliacao->get_tipo()=="S")
                {
                    $grau=0;
                    foreach( $avaliacao->competencias_institucionais as $ci )
                    {
                    	// parray($ci);
                    	if($ci)
                    	{
                        	$grau += $ci->get_grau();
                    	}
                    }
                    $arr_CI[$idx] = $grau / sizeof($avaliacao->competencias_institucionais);
                    $idx++;
				}
			}
        }

        // Soma os graus de CI e divide pelo número de avaliações encontradas (sizeof())
        $media_comite=0;
        foreach( $arr_CI as $media )
        {
            $media_comite += $media;
        }
        $this->media_ci_comite = number_format( $media_comite / sizeof($arr_CI), 2 );
    }

    public function get_media_ci_comite()
    {
        return $this->media_ci_comite;
    }
    public function get_media_final_comite()
    {
        return number_format( ( (  $this->get_grau_media_comite_esc()*40  ) + (  $this->grau_2*60  ) ) / 100 , 2 );
    }
    public function get_grau_media_comite_esc()
    {
        return number_format( ($this->media_ci_comite+$this->capa->get_grau_escolaridade())/2 , 2 );
    }

    /**
     * Verifica se o usuário logado é o responsável pelo comite
     */
    public function is_responsavel_comite()
    {
        $ret = false;
        $responsavel = "";
        
        if( ($this->cd_usuario_logado==$this->capa->get_cd_usuario_avaliador()) && ($this->capa->get_avaliador_responsavel_comite()=="S") )
        {
        	$responsavel = "S";
        }
        else
        {
	        foreach($this->capa->comite as $integrante)
	        {
	            if(isset($integrante))
	            {
		        	if($integrante->get_cd_usuario_avaliador()==$this->cd_usuario_logado)
		            {
		                $responsavel = $integrante->get_fl_responsavel();
		                break;
		            }
	            }
	        }
        }

        return ($responsavel=="S");
    }

    public function todo_comite_avaliou()
    {
        $contador=0;
        // Para cada integrante do comite verifica se tem uma avaliação correspondente

        foreach( $this->capa->comite as $integrante )
        {
            if( !$integrante==null )
            {
                foreach( $this->capa->avaliacoes as $avaliacao )
                {
                    if(   $integrante->get_cd_usuario_avaliador()==$avaliacao->get_cd_usuario_avaliador()
                       && $avaliacao->get_dt_conclusao()!="")
                    {
                        $contador++;
                    }
                }
            }
        }

        // retorna verdadeiro se o nro de integrantes for o mesmo que o número de avaliações encontradas
        if($contador==sizeof($this->capa->comite))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}#end_class

function get_acordo_ciente($cd_avalicao_capa)
{
	global $db;
	
	$qr_sql = "
				SELECT fl_acordo
				  FROM projetos.avaliacao_capa
				 WHERE cd_avaliacao_capa = ".intval($cd_avalicao_capa)."
			   ";
	$ob_data = pg_query($db, $qr_sql);
	$ar_reg = pg_fetch_array($ob_data);
	return $ar_reg['fl_acordo'];
}

function get_dt_acordo_ciente($cd_avalicao_capa)
{
	global $db;
	
	$qr_sql = "
				SELECT TO_CHAR(dt_acordo,'DD/MM/YYYY HH24:MI:SS') AS dt_acordo
				  FROM projetos.avaliacao_capa
				 WHERE cd_avaliacao_capa = ".intval($cd_avalicao_capa)."
			   ";
	$ob_data = pg_query($db, $qr_sql);
	$ar_reg = pg_fetch_array($ob_data);
	return $ar_reg['dt_acordo'];
}

function get_acordo_ciente_solicita($cd_avalicao_capa)
{
	global $db;
	
	$qr_sql = "
				SELECT COUNT(*) AS fl_solicita
				  FROM projetos.avaliacao_capa
				 WHERE cd_avaliacao_capa = ".intval($cd_avalicao_capa)."
				   AND dt_periodo >= 2012
			   ";
	$ob_data = pg_query($db, $qr_sql);
	$ar_reg = pg_fetch_array($ob_data);
	return intval($ar_reg['fl_solicita']);
}


$thisPage = new avaliacao_partial_resultado( $db );

if ($thisPage->get_command()=="publicar_avaliacao")
{
    $thisPage = null;
	exit();
}
?>
    <div id="message_panel"></div>

    <table cellpadding="0" cellpadding="0" align="center" style="width:700px">
    <tr>
        <td>
            <table style="width:100%" cellpadding="0" cellpadding="0">

			<!-- START RESULTADO -->

                      <tr> 
                        <td colspan="7">
	<table cellspacing="0" cellpadding="0" border="0" style="width:690px">
		<tbody>
			<tr>
				<td valign="middle" align="right">
					<a href="#" onclick="javascript: window.print();">
						<img id="print_image" border="0" src="img/print_avaliacao_resultado.png">
					</a>
				</td>
			</tr>
		</tbody>
	</table>
	<BR>
	<table width="100%" class="sort-table" id="table-1" align="center" cellspacing="2" cellpadding="2">
		<thead>
			<tr>
				<td  style="font-size: 120%;" colspan="2" align="left"><?= $thisPage->capa->avaliado->get_nome()?></td>
			</tr>
		</thead>
		<tbody>
			<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
				<td style="font-weight:bold;">Ano:</td>
				<td style="font-weight:bold;"><?= $thisPage->capa->get_dt_periodo()?></td>					
			</tr>		
			<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
				<td>RE:</td>
				<td><?= $thisPage->capa->avaliado->get_cd_registro_empregado()?></td>					
			</tr>						
			<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
				<td>Cargo:</td>
				<td><?= $thisPage->capa->cargo->get_desc_cargo() ?></td>					
			</tr>
			<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
				<td>Família:</td>
				<td><?= $thisPage->capa->cargo->get_familia()->get_nome_familia()?></td>					
			</tr>	
			<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
				<td>Avaliador:</td>
				<td><?= $thisPage->capa->avaliador->get_nome()?> - <?= $thisPage->capa->avaliador->get_cd_registro_empregado() ?></td>					
			</tr>	
			<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
				<td>Comitê:</td>
				<td>
					<?
					$virgula = "";
					foreach($thisPage->capa->comite as $integrante)
					{
						if( ! is_null($integrante) )
						{
							echo( $virgula );
							echo( $integrante->avaliador->get_guerra() );

							foreach( $thisPage->capa->avaliacoes as $avaliacao )
							{
								if(   $integrante->get_cd_usuario_avaliador()==$avaliacao->get_cd_usuario_avaliador()
								   && $avaliacao->get_dt_conclusao()!="")
								{
									echo( " [já avaliou]" );
									break;
								}
							}

							$virgula = "<BR /> "; 
						}
					}
					?>				
				</td>					
			</tr>
			<?php if($thisPage->capa->get_dt_publicacao() !="") { ?>		
			<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
				<td>Finalizada em:</td>
				<td><?= $thisPage->capa->get_dt_publicacao() ?></td>					
			</tr>			
			<?php } ?>
		</tbody>
	</table>						
						


                          </td>
                      </tr>

                      <tr>
                        <td height="20px"></td>
                      </tr>

                      <? if( $thisPage->todo_comite_avaliou() && $thisPage->is_responsavel_comite() && $thisPage->capa->get_dt_publicacao()=="") { ?>
                          <tr>
                            <td height="20px">
                                    <center>
								<!--
									<input 
                                        id="publicar_button"
                                        name="publicar_button"
                                        type="button" 
                                        value="Finalizar avaliação"
                                        title="Finalizando a avaliação, o resultado será visualizado pelo avaliado."
                                        style="width:200" 
                                        onclick="thisPage.publicar_Click(this);"
                                        class="botao"
                                    />
								-->	
								<a  href="javascript:void(0)"
									id="publicar_button"
									onclick="thisPage.publicar_Click(this);"
									title="Finalizando a avaliação, o resultado será visualizado pelo avaliado."
									><img src="img/avaliacao_finaliza.png" border="0" /></a>										
									
									
									</center>
                            </td>
                          </tr>
                      <? } elseif($thisPage->capa->get_dt_publicacao() != "") { ?>
							<tr>
								<td align="center">
								<?php
									$fl_acordo = get_acordo_ciente(intval($thisPage->capa->get_cd_avaliacao_capa()));
									$dt_acordo = get_dt_acordo_ciente(intval($thisPage->capa->get_cd_avaliacao_capa()));
									$fl_solicita_acordo = get_acordo_ciente_solicita(intval($thisPage->capa->get_cd_avaliacao_capa()));
									
									if(($fl_acordo == "C") OR ($fl_acordo == "A"))
									{
										echo '<span style="font-family: verdana, tahoma; font-size: 10pt; font-weight:bold; color: blue; " >';
										
										if($fl_acordo == "A")
										{
											echo "CONCORDOU com o resultado da avaliação (houve consenso).<BR><span style='font-size: 8pt;'>".$dt_acordo."</span>";
										}
										
										if($fl_acordo == "C")
										{
											echo "Está CIENTE do resultado da avaliação (não houve consenso).<BR><span style='font-size: 8pt;'>".$dt_acordo."</span>";
										}

										echo "</span>";										
										
									}
									else if(($thisPage->capa->get_cd_usuario_avaliado() == $_SESSION["Z"]) and (intval($fl_solicita_acordo) > 0))
									{
								?>						
									<table align="center" border="0">
										<tr>
											<td style="font-family: Verdana, Tahoma; font-size: 11pt; font-weight: bold;" id="aval_acordo_pergunta">Informe se você está CIENTE ou CONCORDA com o resultado da avaliação:</td>
										</tr>
										<tr>
											<td>
												<BR>
												<table>
													<tr>
														<td><input type="radio" name="fl_acordo" id="fl_acordo_A" value="A"></td><td style="padding-left: 10px; font-family: Verdana, Tahoma; font-size: 10pt;">CONCORDO com o resultado da avaliação (houve consenso)</td>
													</tr>
													<tr>										
														<td><input type="radio" name="fl_acordo" id="fl_acordo_C" value="C"></td><td style="padding-left: 10px;font-family: Verdana, Tahoma; font-size: 10pt;">Estou CIENTE do resultado da avaliação (não houve consenso)</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td>
												<BR>

													<a href="javascript:void(0);" onclick="validaFormAcordo(<?php echo intval($thisPage->capa->get_cd_avaliacao_capa()); ?>);" title="Confirmar"><img src="img/avaliacao_confirmar_acordo.png" border="0" /></a>

											</td>
										</tr>						
									</table>
								<?php
									}
								?>					
								</td>
							</tr>
                      <?php 
						} 
						else 
						{ 
						?>
                          <tr><td><CENTER><b>AVALIAÇÃO NÃO FINALIZADA</b></CENTER></td></tr>
                      <?php 
						} 
					  ?>

                      <tr>
                        <td height="20px"></td>
                      </tr>

                      <tr>
                        <td>
                            <?
                            foreach($thisPage->capa->avaliacoes as $avaliacao)
                            {
                                // $avaliacao=null;
                                if(!is_null($avaliacao))
                                {
                                    if ( $avaliacao->get_tipo()!="C" AND !($avaliacao->get_tipo()=="A" AND $thisPage->capa->get_tipo_promocao()=="V") )
                                    {
                                        $thisPage->load_valores( $avaliacao->get_cd_avaliacao() );
                                        ?>

										<table width="100%" class="sort-table" id="table-1" align="center" cellspacing="2" cellpadding="2">
											<thead>
												<tr>
													<td colspan="3" align="left">
														Resultado do
																	 <? if($avaliacao->get_tipo()=="A") { ?>
																			Avaliado
																		<? } elseif($avaliacao->get_tipo()=="S") {?>
																			Superior
																		<? } elseif($avaliacao->get_tipo()=="C") {?>
																			Comite
																		<? } ?>					
													</td>
												</tr>
											</thead>
											<tbody>
												<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
													<td>Competências Institucionais:</td>
													<td align="right" width="12"></td>
													<td align="right" width="50"><?php echo number_format($thisPage->get_val_ci(), 2,",","."); ?></td>					
												</tr>
												<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
													<td>Escolaridade:</td>
													<td align="right"></td>
													<td align="right"><?php echo number_format($thisPage->capa->get_grau_escolaridade(), 2,",","."); ?></td>					
												</tr>
												<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
													<td style="font-weight: bold; font-size: 102%;">Média: <span style='font-size: 80%; color:999999; font-weight: bold;'>(<b>M1</b>)</span></td>
													<td align="right"></td>
													<td style="font-weight: bold; font-size: 102%;" align="right"><?php echo number_format($thisPage->get_grau_1(), 2,",","."); ?></td>					
												</tr>		

												<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);" style="height: 8px;">
													<td></td>
													<td></td>
													<td></td>					
												</tr>

												<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
													<td>Competências Específicas:</td>
													<td align="right">
													<?php if($avaliacao->get_tipo()=="S") {?>
														<a title="Comparativo" href="<?php echo site_url('servico/avaliacao/competencia_especifica/'.md5($thisPage->capa->get_cd_avaliacao_capa()));?>"><img src="img/avaliacao_resultado_info.gif" border="0"></a>
													<?php } ?>														
													</td>													
													<td align="right"><?php echo number_format($thisPage->get_val_ce(), 2,",","."); ?></td>					
												</tr>		
												<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
													<td>Responsabilidades:</td>
													<td align="right">
													<?php if($avaliacao->get_tipo()=="S") {?>
														<a title="Comparativo" href="<?php echo site_url('servico/avaliacao/responsabilidade/'.md5($thisPage->capa->get_cd_avaliacao_capa()));?>"><img src="img/avaliacao_resultado_info.gif" border="0"></a>
													<?php } ?>														
													</td>	
													<td align="right"><?php echo number_format($thisPage->get_val_resp(), 2,",","."); ?></td>					
												</tr>
												<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
													<td style="font-weight: bold; font-size: 102%;">Média: <span style='font-size: 80%; color:999999; font-weight: bold;'>(<b>M2</b>)</span></td>
													<td></td>
													<td style="font-weight: bold; font-size: 102%;" align="right"><?php echo number_format($thisPage->get_grau_2(), 2,",","."); ?></td>					
												</tr>

												<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);" style="height: 8px;">
													<td></td>
													<td></td>
													<td></td>					
												</tr>

												<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
													<td style="font-weight: bold; font-size: 110%;">Resultado: <span style='font-size: 80%; color:999999; font-weight: bold;'>(40% DE M1) + (60% de M2)</span></td>
													<td></td>
													<td style="font-weight: bold; font-size: 110%;" align="right"><?php echo number_format($thisPage->get_grau_final(), 2,",","."); ?></td>					
												</tr>	
											</tbody>
										</table>

                                        <br><br>
										
                                        <?
                                    }
                                }
                            }
                            ?>

                            <? if( sizeof($thisPage->capa->comite)>1 ) if( $thisPage->capa->get_dt_publicacao()!="" || $thisPage->is_responsavel_comite() ) {  /*resultado_do_comite*/ ?>
                            <? // if(true) {  /*resultado_do_comite*/ ?>

                                <!-- RESULTADO DO COMITE -->
                                <? $thisPage->calcula_media_ci_comite(); ?>
								
								
								<table width="100%" class="sort-table" id="table-1" align="center" cellspacing="2" cellpadding="2">
									<thead>
										<tr>
											<td colspan="3" align="left">
												Resultado do Comitê				
											</td>
										</tr>
									</thead>
									<tbody>
										<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
											<td width="638">Competências Institucionais:</td>
											<td align="right" width="12"><a title="Comparativo" href="<?php echo site_url('servico/avaliacao/comite_media/'.md5($thisPage->capa->get_cd_avaliacao_capa()));?>"><img src="img/avaliacao_resultado_info.gif" border="0"></a></td>
											<td align="right" width="50"><?php echo number_format($thisPage->get_media_ci_comite(), 2,",","."); ?></td>	
											
										</tr>
										<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
											<td>Escolaridade:</td>
											<td></td>
											<td align="right"><?php echo number_format($thisPage->capa->get_grau_escolaridade(), 2,",","."); ?></td>					
										</tr>
										<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
											<td style="font-weight: bold; font-size: 102%;">Média: <span style='font-size: 80%; color:999999; font-weight: bold;'>(<b>M1</b>)</span></td>
											<td></td>
											<td style="font-weight: bold; font-size: 102%;" align="right"><?php echo number_format($thisPage->get_grau_media_comite_esc(), 2,",","."); ?></td>					
										</tr>		

										<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);" style="height: 8px;">
											<td></td>
											<td></td>
											<td></td>					
										</tr>

										<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
											<td>Competências Específicas:</td>
											<td></td>
											<td align="right"><?php echo number_format($thisPage->get_val_ce(), 2,",","."); ?></td>		
										</tr>		
										<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
											<td>Responsabilidades:</td>
											<td></td>
											<td align="right"><?php echo number_format($thisPage->get_val_resp(), 2,",","."); ?></td>	
										</tr>
										<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
											<td style="font-weight: bold; font-size: 102%;">Média: <span style='font-size: 80%; color:999999; font-weight: bold;'>(<b>M2</b>)</span></td>
											<td></td>
											<td style="font-weight: bold; font-size: 102%;" align="right"><?php echo number_format($thisPage->get_grau_2(), 2,",","."); ?></td>
										</tr>

										<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);" style="height: 8px;">
											<td></td>
											<td></td>					
											<td></td>					
										</tr>	

										<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
											<td style="font-weight: bold; font-size: 110%;">Resultado: <span style='font-size: 80%; color:999999; font-weight: bold;'>(40% DE M1) + (60% de M2)</span></td>
											<td></td>
											<td style="font-weight: bold; font-size: 110%;" align="right"><?php echo number_format($thisPage->get_media_final_comite(), 2,",","."); ?></td>					
										</tr>											
									</tbody>
								</table>								

                            <? } /*resultado_do_comite*/ ?>
                            <input type="hidden" name="media_geral__text" id="media_geral__text" value="<?= $thisPage->get_media_final_comite() ?>" />

                        </td>
                      </tr>

                    </table></td>
                </tr>

			<!-- END RESULTADO -->

            </table>

    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>	

    <? $thisPage = null; ?>