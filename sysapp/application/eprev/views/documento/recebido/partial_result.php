	<form name="comando_form" id="comando_form" method="post">
			<!-- BARRA DE PAGINAÇÃO -->
			Quantidade: <?php echo $quantos; ?>
			<div id="pagination_bar" class="pagination-bar-top">
				<?= $this->pagination->create_links() ?>
			</div>
			<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
		    	<thead>
				<tr>
					<td><b>ID</b></td>
					<td><b>Protocolo</b></td>
					<td><b>Tipo</b></td>
					<td><b>Cadastro</b></td>
					<td><b></b></td>
				</tr>
		    	</thead>
				<tbody>
					<?php $bgcolor=""; ?>
					<?php foreach( $collection as $item ): ?>
						<?php if($bgcolor=="#C9D0C8") $bgcolor="#F4F4F4"; else $bgcolor="#C9D0C8"; ?>
						<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);" bgcolor="<?php echo $bgcolor; ?>">
							<td align="center"><?php echo $item['cd_documento_recebido']; ?></td>
							<td align="center"><?php echo $item["cd_documento_recebido"] . " - " . $item["dt_cadastro"]; ?></td>
							<td align="center"><?php echo $item["ds_tipo"]; ?></td>
							<td align="center"><?php echo $item["nome_usuario_cadastro"]; ?></td>
							<td align="center">
								<a href="javascript:abrir( '<?php echo $item['cd_documento_recebido'];?>' );">abrir</a>
								|
								<a href="javascript:fechar( '<?php echo $item['cd_documento_recebido'];?>' );">fechar</a>
							</td>
						</tr>
						<tr id="linha_<?php echo $item['cd_documento_recebido']; ?>" style="display:none;">
						<td></td>
						<td colspan="4">
							<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
						    	<thead>
								<tr>
									<td>ID</td>
									<td>Documento</td>
									<td>Observação</td>
									<td>Folhas</td>
									<td>Envio</td>
									<td>Destino</td>
									<td>Recebimento</td>
									<td>Histórico</td>
								</tr>
						    	</thead>
								<?php foreach($item['collection'] as $item_doc): ?>
								<tbody>
								<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);" bgcolor="<?php echo $bgcolor; ?>">
									<td><?php echo $item_doc['cd_documento_recebido_item']; ?></td>
									<td><?php echo $item_doc['nome_documento']; ?></td>
									<td><?php echo $item_doc['ds_observacao']; ?></td>
									<td><?php echo $item_doc['nr_folha']; ?></td>
									<td align="center" id="data_envio_td_<?php echo $item_doc['cd_documento_recebido_item']; ?>">
										<?php
										if( $item_doc['dt_envio']!='' )
										{
											echo $item_doc['dt_envio']; 
										}
										else
										{
											if($item['cd_usuario_cadastro']==$cd_usuario_logado)
											{
												echo form_input(
													array(
														'id'=>'enviar'
														, 'name'=>'enviar'
														, 'type'=>'button'
														, 'class'=>'botao'
														, 'onclick'=>'enviar_form('.$item_doc['cd_documento_recebido_item'].')'
													)

													, "Enviar"
													);
											}
										}
										?>
									</td>
									<td id="destino_envio_td_<?php echo $item_doc['cd_documento_recebido_item']; ?>"><?php echo $item_doc['usuario_destino']; ?></td>
									<td align="center">
										<?php
											if( $item_doc['dt_recebimento']!='' )
											{
												echo $item_doc['dt_recebimento'];
											}
											else
											{
												if($item_doc['cd_usuario_destino']==$cd_usuario_logado)
												{
													echo form_input(
														array(
														'id'=>'receber'
														, 'name'=>'receber'
														, 'type'=>'button'
														, 'class'=>'botao'
														, 'onclick'=>'receber_form('.$item_doc['cd_documento_recebido_item'].')'
													)

													, "Receber");

													echo form_input(
														array(
														'id'=>'devolver'
														, 'name'=>'devolver'
														, 'type'=>'button'
														, 'class'=>'botao'
														, 'onclick'=>'reencaminhar_form('.$item_doc['cd_documento_recebido_item'].')'
														)

													, "Devolver");
												}
											}
										?>
									</td>
									<td>...</td>
								</tr>
								</tbody>
								<?php endforeach; ?>
							</table>
						</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<!-- BARRA DE PAGINAÇÃO -->
			<div id="pagination_bar" class="pagination-bar-bottom">
				<?= $this->pagination->create_links(); ?>
				<br />
			</div>
	</form>