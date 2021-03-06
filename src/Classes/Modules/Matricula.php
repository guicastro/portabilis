<?php

/*#######################################################
|														|
| Arquivo com a classe do cadastro de Matrículas		|
|														|
| Esta classe herda as variáveis e métodos da classe	|
| Crud. A documentação dos métodos está na classe pai 	|
|														|
| Data de criação: 04/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Modules;

/*### CLASSE DO CADASTRO DE PESSOA (HERDA CRUD) ###*/
class Matricula extends \Database\Crud {

	protected $DynamicTable;

	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/
	public function __construct($container) {

		parent::__construct($container['Connect'], $container);

		//CHAMADA PARA CLASSE TABELADINAMICA
		$this->DynamicTable = $container['DynamicTable'];
	}
	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/




	/*### MONTA O INÍCIO DO SELECT, COM AS COLUNAS QUE SERÃO RETORNADAS ###*/
	public function setSQLSelectFields () {

		$this->SQLSelectFields = "SELECT
									TBL.*,
									alunos.*,
									cursos.*,
									periodos.*,
									status.tabedinavalo_descricao AS status_nome,
									status_pgto.tabedinavalo_descricao AS status_pgto_nome,
									DATE_TRUNC('MONTH', CURRENT_TIMESTAMP::date) + INTERVAL '1 MONTH - 1 DAY' AS ULTIMO_DIA_MES "
								.", (SELECT Usua_Nome FROM Usuarios WHERE Usua_id = TBL.".$this->ModuleDefs->Prefix."RecCreatedby) as ".$this->ModuleDefs->Prefix."RecCreatedbyName"
								.", (SELECT Usua_Nome FROM Usuarios WHERE Usua_id = TBL.".$this->ModuleDefs->Prefix."RecModifiedby) as ".$this->ModuleDefs->Prefix."RecModifiedbyName ";
		return $this->SQLSelectFields;
	}
	/*### MONTA O INÍCIO DO SELECT, COM AS COLUNAS QUE SERÃO RETORNADAS ###*/





	/*### MONTA O FROM DO SELECT, COM A(S) TABELA(S) DA ORIGEM DE DADOS ###*/
	public function setSQLSelectFrom () {

		//CONDIÇÃO PADRÃO DO WHERE
		$this->SQLSelectFrom = "FROM
									".$this->ModuleDefs->Table." TBL
								INNER JOIN
									alunos ON alunos.alun_id = TBL.alunos_alun_id
									AND alunos.alun_delete = 0
								INNER JOIN
									cursos ON cursos.curs_id = TBL.cursos_curs_id
									AND cursos.curs_delete = 0
								INNER JOIN
									tabelasdinamicasvalores periodos ON periodos.tabedinavalo_codigo = CAST(cursos.curs_periodo AS VARCHAR)
																	AND periodos.tabedinavalo_tabela = 'TURNOS'
																	AND periodos.tabedinavalo_delete = 0
								INNER JOIN
									tabelasdinamicasvalores status ON status.tabedinavalo_codigo = CAST(TBL.matr_status AS VARCHAR)
																	AND status.tabedinavalo_tabela = 'STATUS_MATRICULAS'
																	AND status.tabedinavalo_delete = 0
								INNER JOIN
									tabelasdinamicasvalores status_pgto ON status_pgto.tabedinavalo_codigo = CAST(TBL.matr_paga AS VARCHAR)
																	AND status_pgto.tabedinavalo_tabela = 'STATUS_PAGAMENTOS'
																	AND status_pgto.tabedinavalo_delete = 0";
		return $this->SQLSelectFrom;
	}
	/*### MONTA O FROM DO SELECT, COM A(S) TABELA(S) DA ORIGEM DE DADOS ###*/



	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/
	public function BeforeExecuteAction () {

		$this->BeforeExecuteAction = true;

		if($this->Action=="inserir") {

			$SQLChecaMatricula = "SELECT
										matr_id
									FROM
										matriculas
									INNER JOIN
										cursos ON cursos.curs_id = matriculas.cursos_curs_id
												AND cursos.curs_delete = 0
									WHERE
										alunos_alun_id = ".$this->Request["alunos_alun_id"]."
										AND cursos.curs_periodo = ".$this->Request["curs_periodo"]."
										AND matr_ano = ".$this->Request["matr_ano"]."
										AND matr_status = 1
										AND matr_delete = 0";
			$ExecuteSQLChecaMatricula = $this->db->query($SQLChecaMatricula);
			$ResultSQLChecaMatricula = $ExecuteSQLChecaMatricula->fetchAll(\PDO::FETCH_OBJ);
			if($ResultSQLChecaMatricula[0]->matr_id > 0) {

				$this->BeforeExecuteAction = false;
				$this->ErrorBeforeExecuteAction = "<p>O aluno escolhido já possui uma matrícula ATIVA em um curso no mesmo período e ano letivo</p>";
			} else {

				$this->BeforeExecuteAction = true;
			}
		}
	}
	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/





	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/
	public function MaskResultSQLAction () {

		if($this->Action=="selecionar")
			{
				$ResultSQLAction = $this->ResultSQLAction;

				if(count($ResultSQLAction)>0) {

					foreach ($ResultSQLAction as $key => $DataObject) {

						if(is_numeric($key)) {

							$ResultSQLAction[$key]->matr_reccreatedon = $this->MaskValue->Data($DataObject->matr_reccreatedon,'US2BR_TIME');
							$ResultSQLAction[$key]->matr_recmodifiedon = $this->MaskValue->Data($DataObject->matr_recmodifiedon,'US2BR_TIME');
							$ResultSQLAction[$key]->curs_valor_matricula = $this->MaskValue->Moeda($DataObject->curs_valor_matricula,'add');
							$ResultSQLAction[$key]->curs_valor_mensalidade = $this->MaskValue->Moeda($DataObject->curs_valor_mensalidade,'add');

							$ResultSQLAction[$key]->alun_cpf = $this->MaskValue->Cpf($DataObject->alun_cpf,'add');
							$ResultSQLAction[$key]->alun_dtnascimento = $this->MaskValue->Data($DataObject->alun_dtnascimento,'US2BR');
							$ResultSQLAction[$key]->alun_endereco_cep = $this->MaskValue->Cep($DataObject->alun_endereco_cep,'add');
							$ResultSQLAction[$key]->alun_celular = $this->MaskValue->Telefone($DataObject->alun_celular,'add');
							$ResultSQLAction[$key]->alun_telefone = $this->MaskValue->Telefone($DataObject->alun_telefone,'add');

							$ResultSQLAction[$key]->matr_status_nome = ($DataObject->matr_status==0) ? $DataObject->status_nome. " (cancelada em ".$this->MaskValue->Data($DataObject->matr_dt_cancelamento,'US2BR').")" : $DataObject->status_nome;
							$ResultSQLAction[$key]->matr_status_pgto = $DataObject->status_pgto_nome;

							$SQLParcelas[$key] = "SELECT
														matriculas.matr_reccreatedon,
														matriculas.matr_paga,
														matriculas.matr_dt_cancelamento,
														matriculas.matr_dt_pagto,
														cursos.curs_valor_matricula,
														status_pgto_matr.tabedinavalo_descricao AS status_pgto_matr,
														status_pgto_parc.tabedinavalo_descricao AS status_pgto_parc,
														financeiro.fina_id,
														CAST(financeiro.fina_parcela AS int) AS fina_parcela,
														financeiro.fina_vencimento,
														financeiro.fina_dt_pagto,
														financeiro.fina_valor,
														financeiro.fina_status
													FROM
														financeiro
													INNER JOIN
														matriculas ON matriculas.matr_id = financeiro.matriculas_matr_id
																	AND matriculas.matr_delete = 0
													INNER JOIN
														cursos ON cursos.curs_id = matriculas.cursos_curs_id
																AND cursos.curs_delete = 0
													INNER JOIN
														tabelasdinamicasvalores status_pgto_parc ON status_pgto_parc.tabedinavalo_codigo = CAST(financeiro.fina_status AS VARCHAR)
																									AND status_pgto_parc.tabedinavalo_tabela = 'STATUS_PAGAMENTOS'
																									AND status_pgto_parc.tabedinavalo_delete = 0
													INNER JOIN
														tabelasdinamicasvalores status_pgto_matr ON status_pgto_matr.tabedinavalo_codigo = CAST(matriculas.matr_paga AS VARCHAR)
																									AND status_pgto_matr.tabedinavalo_tabela = 'STATUS_PAGAMENTOS'
																									AND status_pgto_matr.tabedinavalo_delete = 0
													WHERE
														matriculas_matr_id = ".$ResultSQLAction[$key]->matr_id."
													ORDER BY
														CAST(financeiro.fina_parcela AS int)";
							$ExecuteSQLParcelas[$key] = $this->db->query($SQLParcelas[$key]);
							$ResultSQLParcelas[$key] = $ExecuteSQLParcelas[$key]->fetchAll(\PDO::FETCH_OBJ);
							if(count($ResultSQLParcelas[$key])>0) {

								$tbl_parcelas[$key] = '<table class="table table-striped table-hover">
														<thead>
															<tr>
																<th>&nbsp</th>
																<th>Parcela</th>
																<th>Vencimento</th>
																<th>Valor</th>
																<th>Status</th>
															</tr>
														</thead>
														<tbody>';

								$tbl_parcelas[$key] .= '<tr>
													<td>';
								$tbl_parcelas[$key] .= ($ResultSQLParcelas[$key][0]->matr_paga==0) ? '<label class="checkbox checkbox-inline" style="margin-top:0px;"><input type="checkbox" name="parc[0]" id="parc_0" value="1"  data-value="'.$ResultSQLParcelas[$key][0]->curs_valor_matricula.'" class="checkbox style-2" /><span></span></label>' : "&nbsp;";
								$tbl_parcelas[$key] .= '</td>';
								$tbl_parcelas[$key] .= '<td>Taxa de Matrícula</td>
													<td>'.$this->MaskValue->Data($ResultSQLParcelas[$key][0]->matr_reccreatedon, 'US2BR').'</td>
													<td>'.$this->MaskValue->Moeda($ResultSQLParcelas[$key][0]->curs_valor_matricula, 'add').'</td>';
								$tbl_parcelas[$key] .= '<td>';
								$tbl_parcelas[$key] .= ($ResultSQLParcelas[$key][0]->matr_paga==1) ? $ResultSQLParcelas[$key][0]->status_pgto_matr." EM ".$this->MaskValue->Data($ResultSQLParcelas[$key][0]->matr_dt_pagto, 'US2BR') : $ResultSQLParcelas[$key][0]->status_pgto_matr;
								$tbl_parcelas[$key] .= '</td>
												</tr>';

								foreach ($ResultSQLParcelas[$key] as $keyP => $DataObject) {


									if($DataObject->fina_status==0) {

										$ParcelasRestantes[$key]++;
										$ValorMulta[$key] += ($DataObject->fina_valor*0.01);
									}

									$ValorParcela[$key] = $DataObject->fina_valor;


									$tbl_parcelas[$key] .= '<tr>
														<td>';
									$tbl_parcelas[$key] .= ($DataObject->fina_status==0) ? '<label class="checkbox checkbox-inline" style="margin-top:0px;"><input type="checkbox" name="parc['.$DataObject->fina_parcela.']" id="parc_'.$DataObject->fina_parcela.'" value="'.$DataObject->fina_id.'" data-value="'.$DataObject->fina_valor.'" class="checkbox style-2" /><span></span></label>' : "&nbsp;";
									$tbl_parcelas[$key] .= '</td>
														<td>'.$DataObject->fina_parcela.'</td>
														<td>'.$this->MaskValue->Data($DataObject->fina_vencimento, 'US2BR').'</td>
														<td>'.$this->MaskValue->Moeda($DataObject->fina_valor, 'add').'</td>';
									$tbl_parcelas[$key] .= '<td>';
									$tbl_parcelas[$key] .= ($DataObject->fina_status==1) ? $DataObject->status_pgto_parc." EM ".$this->MaskValue->Data($DataObject->fina_dt_pagto, 'US2BR') : $DataObject->status_pgto_parc;
									$tbl_parcelas[$key] .= '</td>
													</tr>';
								}

								$tbl_parcelas[$key] .= '</tbody></table>';

							}

							$ResultSQLAction[$key]->tbl_parcelas = $tbl_parcelas[$key];
							$ResultSQLAction[$key]->ParcelasRestantes = $ParcelasRestantes[$key];
							$ResultSQLAction[$key]->ValorMulta = $ValorMulta[$key];
							$ResultSQLAction[$key]->ValorParcela = $ValorParcela[$key];
							$ResultSQLAction[$key]->DataCancelamento = substr($this->Date["NowBR"],0,10);

							$ResultSQLAction[$key]->TextoCancelamento = '<br/>O cancelamento será em <span id="canc_data">'.$ResultSQLAction[$key]->DataCancelamento.'<span>
																			<br/>Existem <span id="canc_parc_rest" style="font-weight:bold">'.$ResultSQLAction[$key]->ParcelasRestantes.'</span> parcela(s) restante(s) de <span id="canc_parc_valor" style="font-weight:bold">R$ '.number_format($ResultSQLAction[$key]->ValorParcela,2,',','.').'</span> resultando em uma multa por cancelamento de <span id="canc_multa" style="font-weight:bold"> R$'.number_format($ResultSQLAction[$key]->ValorMulta,2,',','.').'</span> (1% por mês não cumprido)
																			<br/><br/><h5>Confirma o cancelamento?</h5>
																			<br/><i>O aluno poderá frequentar as aulas até dia <strong>'.$this->MaskValue->Data($ResultSQLAction[$key]->ultimo_dia_mes,'US2BR').'</strong></i>';


						}
					}
				}

				// if(count($ResultSQLAction)>1) $ResultSQLAction
				$this->setResultSQLAction($ResultSQLAction);
			}
	}
	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/




	/*### EXECUTA AS AÇÕES DE MÁSCARA DE VALORES NOS DADOS DE UPDATE OU INSERT ###*/
	public function MaskInsertUpdateValues () {

		if($this->Request["origem"]=="cancelamento") {

			$Request = $this->Request;

			$Request["matr_dt_cancelamento"] = $this->Date["NowUS"];

			$this->Request = $Request;
		}
	}
	/*### EXECUTA AS AÇÕES DE MÁSCARA DE VALORES NOS DADOS DE UPDATE OU INSERT ###*/



	/*### EXECUTA AÇÕES DEPOIS DO MÉTODO ExecuteAction ###*/
	public function AfterExecuteAction () {

		/*### AÇÕES APÓS INSERIR A MATRÍCULAS ###*/
		if(($this->Action=="inserir")&&($this->ExecuteAction==true)) {

			if($this->Request["matr_status"]==1) {

				/*### SELECIONA O ÚLTIMO VALOR DA SEQUENCE DE MATRÍCULA ###*/
				$SQLUltimaMatricula = "SELECT last_value FROM matriculas_matr_id_seq";
				$ExecuteSQLUltimaMatricula = $this->db->query($SQLUltimaMatricula);
				$ResultSQLUltimaMatricula = $ExecuteSQLUltimaMatricula->fetch(\PDO::FETCH_OBJ);
				/*### SELECIONA O ÚLTIMO VALOR DA SEQUENCE DE MATRÍCULA ###*/

				//ARMAZENA A CHAVE PRIMÁRIA DO REGISTRO QUE FOI INSERIDO
				$this->PrimaryKey = $ResultSQLUltimaMatricula->last_value;


				/*### AÇÕES DE INSERÇÃO DAS PARCELAS FINANCEIRAS DE MENSALIDADE ###*/
				$SQLDadosCurso = "SELECT
										curs_duracao,
										curs_valor_mensalidade,
										DATE_PART('year', CURRENT_TIMESTAMP::date) AS ano_atual,
										DATE_PART('month', CURRENT_TIMESTAMP::date) AS mes_atual,
										((DATE_PART('year', '".$this->Request["matr_ano"]."-12-31'::date) - DATE_PART('year', CURRENT_TIMESTAMP::date)) * 12 + (DATE_PART('month', '".$this->Request["matr_ano"]."-12-31'::date) - DATE_PART('month', CURRENT_TIMESTAMP::date))) AS meses_final_ano
									FROM
										cursos
									WHERE
										curs_id = ".$this->Request["cursos_curs_id"]."
										AND curs_delete = 0";
				$ExecuteSQLDadosCurso = $this->db->query($SQLDadosCurso);
				$ResultSQLDadosCurso = $ExecuteSQLDadosCurso->fetchAll(\PDO::FETCH_OBJ);
				if($ResultSQLDadosCurso[0]->curs_duracao > 0) {

					//SE A DURAÇÃO DO CURSO FOR <= QUE O NÚMERO DE MESES RESTANTES ATÉ O FINAL DO ANO LETIVO,
					//ENTÃO O VENCIMENTO DAS PARCELAS INICIA A PARTIR NO DIA 10 DO MÊS CORRENTE,
					//SENÃO O VENCIMENTO DAS PARCELAS É A PARTIR DO DIA 10 DE JANEIRO DO ANO CORRENTE
					$ParcelaInicial = ($ResultSQLDadosCurso[0]->curs_duracao<=$ResultSQLDadosCurso[0]->meses_final_ano) ? $ResultSQLDadosCurso[0]->mes_atual : 1;
					$AnoInicial = $ResultSQLDadosCurso[0]->ano_atual;

					$SQLInsertParcelas = "INSERT INTO financeiro (matriculas_matr_id,
																	fina_parcela,
																	fina_valor,
																	fina_vencimento,
																	fina_reccreatedby,
																	fina_reccreatedon) VALUES ";

					for ($i=1; $i <= $ResultSQLDadosCurso[0]->curs_duracao; $i++) {

						$SQLParcelas .= "(".$this->PrimaryKey.", ".$i.", ".$ResultSQLDadosCurso[0]->curs_valor_mensalidade.", '".$AnoInicial."-".str_pad($ParcelaInicial,2,0,STR_PAD_LEFT)."-10', ".$this->TokenClass->getClaim("UserData")->Usua_id.", '".$this->Date["NowUS"]."'), ";
						$ParcelaInicial++;
						if($ParcelaInicial=="12") {

							$ParcelaInicial = 1;
							$AnoInicial++;
						}
					}

					$SQLInsertParcelas .= substr($SQLParcelas,0,-2);
					$ExecuteSQLInsertParcelas = $this->db->query($SQLInsertParcelas);
				}
				/*### AÇÕES DE INSERÇÃO DAS PARCELAS FINANCEIRAS DE MENSALIDADE ###*/
			}
		}
		/*### AÇÕES APÓS INSERIR A MATRÍCULAS ###*/




		/*### AÇÕES APÓS O CANCELAMENTO DE MATRÍCULA ###*/
		if(($this->Request["origem"]=="cancelamento")&&($this->ExecuteAction==true)) {

				$SQLCancelaParcela = "UPDATE
										financeiro
									SET
										fina_status = 2,
										fina_recmodifiedon = '".$this->Date["NowUS"]."',
										fina_recmodifiedby = ".$this->TokenClass->getClaim("UserData")->Usua_id."
									WHERE
										matriculas_matr_id = ".$this->PrimaryKey."
										AND fina_status = 0";
				$ExecuteSQLCancelaParcela = $this->db->query($SQLCancelaParcela);
				$ResultSQLCancelaParcela = $ExecuteSQLCancelaParcela->fetchAll(\PDO::FETCH_OBJ);
		}
		/*### AÇÕES APÓS O CANCELAMENTO DE MATRÍCULA ###*/


	}
	/*### EXECUTA AÇÕES DEPOIS DO MÉTODO ExecuteAction ###*/




}
/*### CLASSE DO CADASTRO DE PESSOA (HERDA CRUD) ###*/
