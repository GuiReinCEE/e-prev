<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');

    include('oo/start.php');

    using( array('projetos.documento_protocolo_item') );

    // begin_class
    class documento_protocolo_partial_relatorio
    {
    	public $filtrado = false;
    	public $comando = "";
    	public $filtro = array();

        function __construct()
        {
        	$this->requestParams();
        	
        	if( $this->comando=="relatorio_listar" )
        	{
        		$this->listar();
        		exit;
        	}
        }

        function __destruct()
        {
        }

        function requestParams()
        {
        	$this->filtro['ano'] = "";
        	$this->filtro['contador'] = "";
        	$this->filtro['cd_empresa'] = "";
        	$this->filtro['cd_registro_empregado'] = "";
        	$this->filtro['seq_dependencia'] = "";
        	$this->filtro['cd_tipo_doc'] = '';
        	$this->filtro['ds_processo'] = '';
        	$this->filtro['dt_envio_inicio'] = '';
        	$this->filtro['dt_envio_fim'] = '';
        	$this->filtro['dt_indexacao_inicio'] = '';
        	$this->filtro['dt_indexacao_fim'] = '';
        	$this->filtro['dt_ok_inicio'] = '';
        	$this->filtro['dt_ok_fim'] = '';
        	$this->filtro['apenas_devolvidos'] = '';

        	if(isset($_POST['comando'])) $this->comando = $_POST['comando'];
        	if(isset($_POST['filtrar'])) $this->filtrado = ($_POST['filtrar']=='true');

        	if(isset($_POST['ano'])) $this->filtro['ano'] = $_POST['ano'];
        	if(isset($_POST['contador'])) $this->filtro['contador'] = $_POST['contador'];
        	if(isset($_POST['cd_empresa'])) $this->filtro['cd_empresa'] = $_POST['cd_empresa'];
        	if(isset($_POST['cd_registro_empregado'])) $this->filtro['cd_registro_empregado'] = $_POST['cd_registro_empregado'];
        	if(isset($_POST['seq_dependencia'])) $this->filtro['seq_dependencia'] = $_POST['seq_dependencia'];
        	if(isset($_POST['cd_tipo_doc'])) $this->filtro['cd_tipo_doc'] = $_POST['cd_tipo_doc'];
        	if(isset($_POST['ds_processo'])) $this->filtro['ds_processo'] = $_POST['ds_processo'];
        	if(isset($_POST['dt_envio_inicio'])) $this->filtro['dt_envio_inicio'] = $_POST['dt_envio_inicio'];
        	if(isset($_POST['dt_envio_fim'])) $this->filtro['dt_envio_fim'] = $_POST['dt_envio_fim'];
        	if(isset($_POST['dt_indexacao_inicio'])) $this->filtro['dt_indexacao_inicio'] = $_POST['dt_indexacao_inicio'];
        	if(isset($_POST['dt_indexacao_fim'])) $this->filtro['dt_indexacao_fim'] = $_POST['dt_indexacao_fim'];
        	if(isset($_POST['dt_ok_inicio'])) $this->filtro['dt_ok_inicio'] = $_POST['dt_ok_inicio'];
        	if(isset($_POST['dt_ok_fim'])) $this->filtro['dt_ok_fim'] = $_POST['dt_ok_fim'];
        	if(isset($_POST['apenas_devolvidos'])) $this->filtro['apenas_devolvidos'] = $_POST['apenas_devolvidos'];
        }

        /**
         * Imprime na tela o conteúdo do método
         *
         */
        function listar()
        {
        	if($this->filtrado)
        	{
        		$collection = documento_protocolo_item::select_01($this->filtro);
        	}
        	else
        	{
        		$collection = array();
        	}
        	?>
			<span style="font-family:arial;font-size:12px;"><b>Total de registros:</b> <?php echo sizeof($collection); ?></span>
			<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">

		    	<thead>
					<tr>
						<td><b>Protocolo</b></td>
						<td><b>Envio</b></td>
						<td><b>Indexação</b></td>
						<td><b>Conclusão</b></td>
						<td><b>Devolução</b></td>
						<td><b>Motivo Devolução</b></td>
						<td><b>EMP</b></td>
						<td><b>RE</b></td>
						<td><b>SEQ</b></td>
						<td><b>Participante</b></td>
						<td title="Código do tipo de documento"><b>Doc</b></td>
						<td><b>Tipo de documento</b></td>
						<td><b>Processo</b></td>
						<td><b>Folha</b></td>
					</tr>
		    	</thead>

				<tbody>
					<?php if($this->filtrado) foreach($collection as $item): ?>
					
					<?php if($item["dt_devolucao"]!="") $cor_devolucao = "color:red;"; else $cor_devolucao = ""; ?>
					
					<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);" style="<?php echo $cor_devolucao; ?>">
						<td><?php echo $item['ano'] . '/' . $item['contador']; ?></td>
						<td><?php echo $item['dt_envio']; ?></td>
						<td><?php echo $item['dt_indexacao']; ?>

							<?php if( $item['ds_observacao_indexacao']!="" ) : ?>

								<img src="img/information.png" title="<?php echo $item['ds_observacao_indexacao']; ?>" />

							<? endif; ?>

						</td>
						<td><?php echo $item['dt_ok']; ?></td>
						<td title="<?php echo $item['motivo_devolucao']; ?>"><?php echo $item['dt_devolucao']; ?>

							<?php //if( $item['motivo_devolucao']!="" ) : ?>

								<!-- <img src="img/information.png" title="<?php //echo $item['motivo_devolucao']; ?>" /> -->

							<? //endif; ?>

						</td>
						<td><?php echo $item['motivo_devolucao']; ?></td>
						<td><?php echo $item['cd_empresa']; ?></td>
						<td><?php echo $item['cd_registro_empregado']; ?></td>
						<td><?php echo $item['seq_dependencia']; ?></td>
						<td><?php echo $item['nome_participante']; ?></td>
						<td><?php echo $item['cd_tipo_doc']; ?></td>
						<td><?php echo $item['nome_documento']; ?></td>
						<td><?php echo $item['ds_processo']; ?></td>
						<td><?php echo $item['nr_folha']; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>

			</table>
        	<?
        }
    }
    // end_class

    $esta = new documento_protocolo_partial_relatorio();
?>
<div id="lista">
	<br />

	<table border="1" style="width:100%" cellpadding="0" cellspacing="0"><tr><td>
	<table width="100%" cellpadding="0" cellpadding="0">
	<tr>
		<th bgcolor="#DAE9F7">
			<a href="javascript:void(0)" onclick="thisPage.showHide_Click(this);">Filtros (clique para exibir/esconder)</a>
		</th>
	</tr>
	<tr id="tr_filtro_form" style="display:">
		<td>
			<table class="tb_cadastro_saida" style="width:100%">
				<tr>
					<td bgcolor="#DAE9F7"><label for="ano">Ano/Sequência:</label></td>
					<td>
						<input id="ano" style="width:50px;"> /
						<input id="contador" style="width:50px;">
					</td>
				</tr>
				<tr>
					<td bgcolor="#DAE9F7"><label for="cd_empresa">EMP/RE/SEQ:</label></td>
					<td>
						<input id="cd_empresa" style="width:50px;">
						<input id="cd_registro_empregado" style="width:90px;">
						<input id="seq_dependencia" style="width:50px;">
					</td>
				</tr>
				<tr>
					<td bgcolor="#DAE9F7"><label for="cd_tipo_doc" title="Você pode informar o código ou o tipo de documento.">Tipo de documento:</label></td>
					<td><input id="cd_tipo_doc" style="width:300px;">(Você pode informar o código ou a descrição)</td>
				</tr>
				<tr>
					<td bgcolor="#DAE9F7"><label for="ds_processo">Processo:</label></td>
					<td><input id="ds_processo" style="width:300px;"></td>
				</tr>
				<tr>
					<td bgcolor="#DAE9F7"><label for="dt_envio_inicio">Data de envio:</label></td>
					<td><input id="dt_envio_inicio" style="width:100px;" value="<?php echo date('d/m/Y'); ?>" /> até <input id="dt_envio_fim" style="width:100px;" value="<?php echo date('d/m/Y'); ?>" /></td>
				</tr>
				<tr>
					<td bgcolor="#DAE9F7"><label for="dt_indexacao_inicio">Data de indexação:</label></td>
					<td><input id="dt_indexacao_inicio" style="width:100px;"> até <input id="dt_indexacao_fim" style="width:100px;"></td>
				</tr>
				<tr>
					<td bgcolor="#DAE9F7"><label for="dt_ok_inicio">Data de conclusão:</label></td>
					<td><input id="dt_ok_inicio" style="width:100px;"> até <input id="dt_ok_fim" style="width:100px;"></td>
				</tr>
				<tr>
					<td bgcolor="#DAE9F7"><label for="apenas_devolvidos">Apenas devolvidos:</label></td>
					<td><input type="checkbox" id="apenas_devolvidos" /></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><input type="button" class="botao" onclick="thisPage.relatorio_filtrar();" value="Buscar" /></td>
				</tr>
			</table>
		</td>
	</tr>
	</table>
	</td></tr></table>

	<br />

	<div id="relatorio">

		<?php $esta->listar(); ?>

	</div>

</div>
<br /><br />
<div id="resultado" style="display:none;"></div>