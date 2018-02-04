<?php

/*#######################################################
|														|
| Arquivo com a classe para gerar Relatórios			|
|														|
| Esta classe herda as variáveis e métodos da classe	|
| Crud. A documentação dos métodos está na classe pai 	|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Modules;

/*### CLASSE DE EXPORTAÇÃO DE DADOS (HERDA CRUD) ###*/
class Relatorio extends \Database\Crud {

	protected $DynamicTable;
	protected $Path;
	protected $PDFClass;
	protected $WordClass;
	protected $MonthsN;

	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/
	public function __construct($container) {

		parent::__construct($container['Connect'], $container);

		//CHAMADA PARA CLASSE TABELADINAMICA
		$this->DynamicTable = $container['DynamicTable'];

		//ARMAZENA AS VARIÁVEIS DOS CAMINHOS DAS PASTAS E HTTP
		$this->Path = array("AbsolutePath" => $container['AbsolutePath'],
							"RelativePath" => $container['RelativePath'],
							"HttpReferer" => $container['HttpReferer']);

		//CHAMADA PARA CLASSE PDF
		$this->PDFClass = $container["PDF"];

		//CHAMADA PARA CLASSE WORD
		$this->WordClass = $container["Word"];

		//ARMAZENA VARIÁVEIS DE MESES DO ANO
		$this->MonthsN = $container["MonthsN"];
	}
	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/



	/*### MONTA O INÍCIO DO SELECT, COM AS COLUNAS QUE SERÃO RETORNADAS ###*/
	public function setSQLSelectFields () {


		if(($this->Request["relatorio"]==1)
			||($this->Request["relatorio"]==2)
			||($this->Request["relatorio"]==3)) {

			$SQLSelectFields = "SELECT DISTINCT
											Eventos.Even_id,
											Eventos.Even_Titulo,
											Eventos.Even_Data,
											Parceiros.Parc_id,
											Parceiros.Parc_Razao_Nome,
											Servicos.Serv_id,
											Servicos.Serv_Titulo,
											STATUS.TabeDinaValo_Codigo AS Aten_Status,
											STATUS.TabeDinaValo_Descricao AS Aten_Status_Nome,
											SUM(Atendimentos.Aten_Qtd) AS Total_Atendimentos,
											SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(Aten_Hora_Atend,Aten_Hora_Inicio)))) AS TEMPO_MEDIO_ESPERA,
											SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(Aten_Hora_Fim,Aten_Hora_Atend)))) AS TEMPO_MEDIO_ATEND,
											SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(Aten_Hora_Fim,Aten_Hora_Inicio)))) AS TEMPO_MEDIO_TOTAL,
											ParceirosServicos.ParcServ_Custo,
											SUM(ParceirosServicos.ParcServ_Custo) AS CUSTO_TOTAL,
											ParceirosServicos.ParcServ_Preco,
											SUM(ParceirosServicos.ParcServ_Preco) AS PRECO_TOTAL ";

			if(($this->Request["relatorio"]==2)
				||($this->Request["relatorio"]==3)) {

				$SQLSelectFields .= ",Atendimentos.Aten_id,
											(CASE
												WHEN Atendimentos.Aten_SenhaNumero IS NULL
													THEN CONCAT(Atendimentos.Aten_Qtd,' Atd')
												ELSE Atendimentos.Aten_SenhaNumero
											END) AS Aten_SenhaNumero,
											Atendimentos.Aten_Hora_Inicio,
											Atendimentos.Aten_Hora_Atend,
											Atendimentos.Aten_Hora_Fim,
											Pessoas.Pess_id,
											(CASE
												WHEN Pessoas.Pess_id IS NULL
													THEN 'SEM PESSOA'
												ELSE Pessoas.Pess_Nome
											END) AS Pess_Nome,
											Pessoas.Pess_CPF,
											Pessoas.Pess_Endereco_Bairro,
											Pessoas.Pess_Endereco_Cidade,
											Pessoas.Pess_Endereco_UF,
											Pessoas.Pess_Telefone,
											Pessoas.Pess_Celular,
											Pessoas.Pess_Email ";
			}
		}
		elseif($this->Request["relatorio"]==4) {

			$SQLSelectFields = "SELECT
									Parceiros.Parc_Razao_Nome,
								    Eventos.Even_Titulo,
								    Eventos.Even_Data,
									DAY(Even_Data) AS Even_Data_Dia,
									MONTH(Even_Data) AS Even_Data_Mes,
									YEAR(Even_Data) AS Even_Data_Ano,
									COUNT(Atendimentos.Aten_id) ATENDIMENTOS ";

		}

		$this->SQLSelectFields = $SQLSelectFields;

		return $this->SQLSelectFields;
	}
	/*### MONTA O INÍCIO DO SELECT, COM AS COLUNAS QUE SERÃO RETORNADAS ###*/





	/*### MONTA O FROM DO SELECT, COM A(S) TABELA(S) DA ORIGEM DE DADOS ###*/
	public function setSQLSelectFrom () {


		if(($this->Request["relatorio"]==1)
			||($this->Request["relatorio"]==2)
			||($this->Request["relatorio"]==3)) {

			$SQLSelectFrom = "FROM
										Atendimentos

									INNER JOIN
										ParceirosServicos ON ParceirosServicos.ParcServ_id = Atendimentos.ParceirosServicos_ParcServ_id
															AND ParceirosServicos.ParcServ_Delete = 0

									INNER JOIN
										Eventos ON Eventos.Even_id = ParceirosServicos.Eventos_Even_id
												AND Eventos.Even_Delete = 0

									INNER JOIN
										Servicos ON Servicos.Serv_id = ParceirosServicos.Servicos_Serv_id
												 AND Servicos.Serv_Delete = 0

									INNER JOIN
										Parceiros ON Parceiros.Parc_id = ParceirosServicos.Parceiros_Parc_id
												  AND Parceiros.Parc_Delete = 0

									INNER JOIN
										TabelasDinamicasValores STATUS ON STATUS.TabeDinaValo_Codigo = Atendimentos.Aten_Status
																	   AND STATUS.TabeDinaValo_Tabela = 'STATUS_ATENDIMENTOS'
																	   AND STATUS.TabeDinaValo_Delete = 0 ";


			if($this->Request["relatorio"]==2) {

				$SQLSelectFrom .= "LEFT JOIN
											Pessoas ON Pessoas.Pess_id = Atendimentos.Pessoas_Pess_id
													AND Pessoas.Pess_Delete = 0 ";
			}
			elseif($this->Request["relatorio"]==3) {

				$SQLSelectFrom .= "INNER JOIN
											Pessoas ON Pessoas.Pess_id = Atendimentos.Pessoas_Pess_id
													AND Pessoas.Pess_Delete = 0 ";
			}

		}
		elseif($this->Request["relatorio"]==4) {

			$SQLSelectFrom .= "FROM
									Atendimentos

								INNER JOIN
									ParceirosServicos ON ParceirosServicos.ParcServ_id = Atendimentos.ParceirosServicos_ParcServ_id
														AND ParceirosServicos.ParcServ_Delete = 0

								INNER JOIN
									Parceiros ON Parceiros.Parc_id = ParceirosServicos.Parceiros_Parc_id
												AND Parceiros.Parc_Delete = 0

								INNER JOIN
									Eventos ON Eventos.Even_id = ParceirosServicos.Eventos_Even_id
											AND Eventos.Even_Delete = 0 ";
		}

		$this->SQLSelectFrom = $SQLSelectFrom;

		return $this->SQLSelectFrom;
	}
	/*### MONTA O FROM DO SELECT, COM A(S) TABELA(S) DA ORIGEM DE DADOS ###*/




	/*### MONTA O WHERE DO SELECT, COM AS CONDIÇÕES DE BUSCA ###*/
	public function setSQLSelectWhere () {


		// echo "<pre>"; print_r($this->Request); echo "</pre>";

		/*### FILTRO POR EVENTO ###*/
		if(count($this->Request["evento"])>0) {

			foreach ($this->Request["evento"] as $key => $Even_id) {

				if($Even_id>0) {

					$SQL_IN_Evento .= $Even_id.", ";
				}
			}

		}

		if($SQL_IN_Evento<>"") $ConditionEvento = "AND Eventos.Even_id IN (".substr($SQL_IN_Evento,0,-2).") ";
		/*### FILTRO POR EVENTO ###*/



		/*### FILTRO POR PARCEIRO ###*/
		if(count($this->Request["parceiro"])>0) {

			foreach ($this->Request["parceiro"] as $key => $Parc_id) {

				if($Parc_id>0) {

					$SQL_IN_Parceiro .= $Parc_id.", ";
				}
			}

		}

		if($SQL_IN_Parceiro<>"") $ConditionParceiro = "AND Parceiros.Parc_id IN (".substr($SQL_IN_Parceiro,0,-2).") ";
		/*### FILTRO POR PARCEIRO ###*/




		/*### FILTRO POR SERVIÇO ###*/
		if(count($this->Request["servico"])>0) {

			foreach ($this->Request["servico"] as $key => $Serv_id) {

				if($Serv_id>0) {

					$SQL_IN_Servico .= $Serv_id.", ";
				}
			}

		}

		if($SQL_IN_Servico<>"") $ConditionServico = "AND Servicos.Serv_id IN (".substr($SQL_IN_Servico,0,-2).") ";
		/*### FILTRO POR SERVIÇO ###*/





		/*### VERIFICA SE HÁ RESTRIÇÃO DE PARCEIRO E APLICA NA CONDIÇÃO DA CONSULTA ###*/
		$CheckPermission = $this->CheckPermission;
		$Parceiros = $CheckPermission->ReturnSpecialPermissions();
		$Parceiros = json_decode($Parceiros);
		// print_r($Parceiros);
		if($Parceiros<>"") {

			foreach ($Parceiros->parceiros as $key => $idParceiro) {
				$SQL_IN .= $idParceiro.", ";
			}

			$ConditionParceiroPerm = "AND ParceirosServicos.Parceiros_Parc_id IN (".substr($SQL_IN,0,-2).") ";
		}
		/*### VERIFICA SE HÁ RESTRIÇÃO DE PARCEIRO E APLICA NA CONDIÇÃO DA CONSULTA ###*/


		if(($this->Request["relatorio"]==1)
			||($this->Request["relatorio"]==2)
			||($this->Request["relatorio"]==3)) {

			//MONTA AS CONDIÇÕES WHERE DO SELECT
			$SQLSelectWhere = "WHERE
									Atendimentos.Aten_Delete = 0
									AND Atendimentos.Aten_Status <> 99 "
									.$ConditionEvento
									.$ConditionParceiro
									.$ConditionServico
									.$ConditionParceiroPerm;

			if($this->Request["relatorio"]==3) {

				$SQLSelectWhere .= " AND Atendimentos.Aten_PosMultiacao = 1 ";
			}
		}
		elseif($this->Request["relatorio"]==4) {

			//MONTA AS CONDIÇÕES WHERE DO SELECT
			$SQLSelectWhere = "WHERE
									Atendimentos.Aten_Delete = 0
									AND Atendimentos.Aten_Status = 4 "
									.$ConditionEvento
									.$ConditionParceiro
									.$ConditionServico
									.$ConditionParceiroPerm;
		}


		$this->SQLSelectWhere = $SQLSelectWhere;
		return $this->SQLSelectWhere;

	}
	/*### MONTA O WHERE DO SELECT, COM AS CONDIÇÕES DE BUSCA ###*/





	/*### MONTA O GROUP DO SELECT, COM OS AGRUPAMENTOS ###*/
	public function setSQLSelectGroup () {

		if(($this->Request["relatorio"]==1)
			||($this->Request["relatorio"]==2)
			||($this->Request["relatorio"]==3)) {

			$SQLSelectGroup = "GROUP BY
										Eventos.Even_id,
										Eventos.Even_Titulo,
										Eventos.Even_Data,
										Parceiros.Parc_id,
										Parceiros.Parc_Razao_Nome,
										Servicos.Serv_id,
										Servicos.Serv_Titulo,
										STATUS.TabeDinaValo_Codigo,
										STATUS.TabeDinaValo_Descricao,
										ParceirosServicos.ParcServ_Custo,
										ParceirosServicos.ParcServ_Preco ";

			if(($this->Request["relatorio"]==2)
				||($this->Request["relatorio"]==3)) {

				$SQLSelectGroup .= ",Atendimentos.Aten_id,
											Atendimentos.Aten_SenhaNumero,
											Atendimentos.Aten_Hora_Inicio,
											Atendimentos.Aten_Hora_Atend,
											Atendimentos.Aten_Hora_Fim,
											Pessoas.Pess_id,
											Pessoas.Pess_Nome,
											Pessoas.Pess_CPF,
											Pessoas.Pess_Endereco_Bairro,
											Pessoas.Pess_Endereco_Cidade,
											Pessoas.Pess_Endereco_UF,
											Pessoas.Pess_Telefone,
											Pessoas.Pess_Celular,
											Pessoas.Pess_Email ";
			}

		}
		elseif($this->Request["relatorio"]==4) {

				$SQLSelectGroup = "GROUP BY
										Parceiros.Parc_Razao_Nome,
										Eventos.Even_Titulo,
										Eventos.Even_Data ";
		}

		$this->SQLSelectGroup = $SQLSelectGroup;

		return $this->SQLSelectGroup;
	}
	/*### MONTA O GROUP DO SELECT, COM OS AGRUPAMENTOS ###*/





	/*### MONTA O ORDER DO SELECT, COM A ORDEM DAS COLUNAS ###*/
	public function setSQLSelectOrder () {


		if(($this->Request["relatorio"]==1)
			||($this->Request["relatorio"]==2)
			||($this->Request["relatorio"]==3)) {

			$SQLSelectOrder = "ORDER BY
									Eventos.Even_Data,
									Parceiros.Parc_Razao_Nome,
									Servicos.Serv_Titulo,
									STATUS.TabeDinaValo_Codigo";

			if(($this->Request["relatorio"]==2)
				||($this->Request["relatorio"]==3)) {

				$SQLSelectOrder .= ",Atendimentos.Aten_Hora_Inicio ";
			}

		}

		$this->SQLSelectOrder = $SQLSelectOrder;

		return $this->SQLSelectOrder;
	}
	/*### MONTA O ORDER DO SELECT, COM A ORDEM DAS COLUNAS ###*/





	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/
	public function MaskResultSQLAction () {

		if(($this->Request["relatorio"]==1)
			||($this->Request["relatorio"]==2)
			||($this->Request["relatorio"]==3)) {

			$ResultSQLAction = $this->ResultSQLAction;


			if(count($ResultSQLAction)>0) {

				foreach ($ResultSQLAction as $key => $DataObject) {

					$ResultSQLAction[$key]->Even_Data = $this->MaskValue->Data($DataObject->Even_Data,'US2BR');

					if(($this->Request["relatorio"]==2)||
						($this->Request["relatorio"]==3)) {

						$ResultSQLAction[$key]->Pess_CPF = $this->MaskValue->Cpf($DataObject->Pess_CPF,'add');
						$ResultSQLAction[$key]->Pess_Telefone = $this->MaskValue->Telefone($DataObject->Pess_Telefone,'add');
						$ResultSQLAction[$key]->Pess_Celular = $this->MaskValue->Telefone($DataObject->Pess_Celular,'add');
						$ResultSQLAction[$key]->Aten_SenhaNumero = ($ResultSQLAction[$key]->Pess_id>0) ? str_pad($DataObject->Aten_SenhaNumero,3,0,STR_PAD_LEFT) : $ResultSQLAction[$key]->Aten_SenhaNumero;
					}

				}
			}

			$this->setResultSQLAction($ResultSQLAction);
		}

	}
	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/












	/*### EXECUTA A QUERY DA SQLAction ###*/
	public function ExecuteAction() {

		/*### SOMENTE EXECUTA SE O BEFOREACTION FOR TRUE ###*/
		if($this->BeforeExecuteAction==true)
			{
				// echo "\nAction: ".$this->Action;
				// echo "\nSQLAction: ".$this->SQLAction;
				// exit;

				//EXECUTA QUERY DA SQLAction
				$ExecuteSQLAction = $this->db->query($this->SQLAction);
				$this->ExecuteAction = $ExecuteSQLAction;

				/*### SE A ACTION FOR SELECIONAR|LOGIN|GRID OU SE A CHAMADA DE UMA TABELA DINÂMICA ###*/
				if($this->Action=="selecionar") {

					//CRIA OBJETO COM O RESULTADO DA SQLAction
					$ResultSQLAction = $ExecuteSQLAction->fetchAll(\PDO::FETCH_OBJ);

					//ADICIONA NO OBJETO A ACTION
					// if($this->Action<>"Grid") $ResultSQLAction["action"] = $this->Action;

					//ARMAZENA O OBJETO PDO NA ResultSQLAction
					$this->setResultSQLAction($ResultSQLAction);
					return $this->ResultSQLAction;
				}
				/*### SE A ACTION FOR SELECIONAR|LOGIN|GRID OU SE A CHAMADA DE UMA TABELA DINÂMICA ###*/



				/*### SENÃO RETORNA OBJETO DO PDO E ACTION ###*/
				else {

					//CRIA OBJETO COM O RESULTADO DA SQLAction
					$ResultSQLAction = $ExecuteSQLAction;

					//ARMAZENA O OBJETO PDO NA ResultSQLAction
					$this->setResultSQLAction($ResultSQLAction);
					return $this->ResultSQLAction;
				}
				/*### SENÃO RETORNA OBJETO DO PDO E ACTION ###*/

			}
		/*### SOMENTE EXECUTA SE O BEFOREACTION FOR TRUE ###*/

	}
	/*### EXECUTA A QUERY DA SQLAction ###*/






	/*### RETORNA O RESULTADO DA ExecuteAction CONFORME A ACTION ###*/
	public function ReturnAction() {

		if($this->ExecuteAction==true) {

			// echo "<br>".$this->SQLAction;

			// echo "<br>relatorio: ".$this->Request["relatorio"];

			// echo "<pre>"; print_r($this->ResultSQLAction); echo "</pre>";

			if(($this->Request["relatorio"]==1)
				||($this->Request["relatorio"]==2)
				||($this->Request["relatorio"]==3)) {

				$Orientation = "P";
				$Margin = "stretch";

				$Filename[1] = $this->MaskValue->Filename("Relatório de Atendimentos - Sintético - ".$this->Date["NowBR"],'add');
				$Filename[2] = $this->MaskValue->Filename("Relatório de Atendimentos - Analítico - ".$this->Date["NowBR"],'add');
				$Filename[3] = $this->MaskValue->Filename("Relatório de Atendimentos - Pós-Multiação - ".$this->Date["NowBR"],'add');

				$Logo = "logo-topo.png";

				$Header[1] = "<div class='col-xs-3'><img src='http://".$this->Path["HttpReferer"]."/images/".$Logo."'></div><div class='col-xs-8' style='text-align:center;'><h3>RELATÓRIO DE ATENDIMENTOS<br>SINTÉTICO</h3></div>";
				$Header[2] = "<div class='col-xs-3'><img src='http://".$this->Path["HttpReferer"]."/images/".$Logo."'></div><div class='col-xs-8' style='text-align:center;'><h3>RELATÓRIO DE ATENDIMENTOS<br>ANALÍTICO</h3></div>";
				$Header[3] = "<div class='col-xs-3'><img src='http://".$this->Path["HttpReferer"]."/images/".$Logo."'></div><div class='col-xs-8' style='text-align:center;'><h3>RELATÓRIO DE ATENDIMENTOS<br>PÓS-MULTIAÇÃO</h3></div>";

				$Footer = "<div class='col-xs-10'>Emitido em ".$this->Date["NowBR"]." por ".$this->TokenClass->getClaim("UserData")->Usua_PrimeiroUltimoNome."</div><div class='col-xs-1' style='tex-align:right;'>{PAGENO}/{nbpg}</div>";

				if(count($this->ResultSQLAction)>0) {

					foreach ($this->ResultSQLAction as $key => $DataObject) {

						$TotalEvento[$DataObject->Even_id] += $DataObject->Total_Atendimentos;
						$TotalParceiro[$DataObject->Even_id][$DataObject->Parc_id] += $DataObject->Total_Atendimentos;
						$TotalServico[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id] += $DataObject->Total_Atendimentos;

						$CustoTotalEvento[$DataObject->Even_id] += $DataObject->CUSTO_TOTAL;
						$CustoTotalParceiro[$DataObject->Even_id][$DataObject->Parc_id] += $DataObject->CUSTO_TOTAL;
						$CustoTotalServico[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id] += $DataObject->CUSTO_TOTAL;

						$PrecoTotalEvento[$DataObject->Even_id] += $DataObject->PRECO_TOTAL;
						$PrecoTotalParceiro[$DataObject->Even_id][$DataObject->Parc_id] += $DataObject->PRECO_TOTAL;
						$PrecoTotalServico[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id] += $DataObject->PRECO_TOTAL;

						$Evento[$DataObject->Even_id] = $DataObject->Even_Titulo;
						$EventoData[$DataObject->Even_id] = $DataObject->Even_Data;
						$Parceiro[$DataObject->Even_id][$DataObject->Parc_id] = $DataObject->Parc_Razao_Nome;
						$Servico[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id] = $DataObject->Serv_Titulo;
						$Status[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_Status] = $DataObject->Aten_Status_Nome;
						$Atendimentos[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_Status] = $DataObject->Total_Atendimentos;

						$PessoaNome[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_id] = $DataObject->Pess_Nome;
						$PessoaCPF[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_id] = $DataObject->Pess_CPF;
						$PessoaCelular[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_id] = $DataObject->Pess_Celular;
						$PessoaTelefone[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_id] = $DataObject->Pess_Telefone;
						$PessoaEmail[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_id] = $DataObject->Pess_Email;
						$PessoaBairro[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_id] = $DataObject->Pess_Endereco_Bairro;
						$PessoaCidade[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_id] = $DataObject->Pess_Endereco_Cidade;
						$PessoaUF[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_id] = $DataObject->Pess_Endereco_UF;

						$AtendimentoSenha[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_id] = $DataObject->Aten_SenhaNumero;
						$AtendimentoHoraInicio[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_id] = $DataObject->Aten_Hora_Inicio;
						$AtendimentoHoraAtend[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_id] = $DataObject->Aten_Hora_Atend;
						$AtendimentoHoraFim[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_id] = $DataObject->Aten_Hora_Fim;
						$AtendimentoStatus[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_id] = $DataObject->Aten_Status_Nome;

						$AtendimentoEspera[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_id] = $DataObject->TEMPO_MEDIO_ESPERA;
						$AtendimentoTempo[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_id] = $DataObject->TEMPO_MEDIO_ATEND;

						if($DataObject->Aten_Status==3) {

							$TempoMedioEspera[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_Status] = $DataObject->TEMPO_MEDIO_ESPERA;
						}

						if($DataObject->Aten_Status==4) {

							$TempoMedioAtendimento[$DataObject->Even_id][$DataObject->Parc_id][$DataObject->Serv_id][$DataObject->Aten_Status] = $DataObject->TEMPO_MEDIO_ATEND;
						}
					}
				}

				if(count($Evento)>0) {

					foreach ($Evento as $idEvento => $Even_Titulo) {

						$Content .= "<h1 style='width:100%; border-bottom:1px solid #CCC;'>".$Even_Titulo." (".$EventoData[$idEvento].")</h1>";
						$Content .= "<div class='col-xs-11'>TOTAL DE ATENDIMENTOS DO EVENTO: ".$TotalEvento[$idEvento]."</div>";
						if($CustoTotalEvento[$idEvento]>0) $Content .= "<div class='col-xs-5'>CUSTO TOTAL DO EVENTO: ".$this->MaskValue->Moeda($CustoTotalEvento[$idEvento],'add')."</div>";
						if($PrecoTotalEvento[$idEvento]>0)$Content .= "<div class='col-xs-6'>PREÇO TOTAL DO EVENTO: ".$this->MaskValue->Moeda($PrecoTotalEvento[$idEvento],'add')."</div>";

						foreach ($Parceiro[$idEvento] as $idParceiro => $Parc_Razao_Nome) {

							$Content .= "<h2><span style='border-bottom:1px solid #CCC;'>PARCEIRO: ".$Parc_Razao_Nome."</span></h2>";
							$Content .= "<div class='col-xs-11'>TOTAL DE ATENDIMENTOS DO PARCEIRO: ".$TotalParceiro[$idEvento][$idParceiro]."</div>";
							if($CustoTotalParceiro[$idEvento][$idParceiro]>0) $Content .= "<div class='col-xs-5'>CUSTO TOTAL DO PARCEIRO: ".$this->MaskValue->Moeda($CustoTotalParceiro[$idEvento][$idParceiro],'add')."</div>";
							if($PrecoTotalParceiro[$idEvento][$idParceiro]>0) $Content .= "<div class='col-xs-6'>PREÇO TOTAL DO PARCEIRO: ".$this->MaskValue->Moeda($PrecoTotalParceiro[$idEvento][$idParceiro],'add')."</div>";

							foreach ($Servico[$idEvento][$idParceiro] as $idServico => $Serv_Titulo) {

								$Content .= "<h3>SERVIÇO: ".$Serv_Titulo."</h3>";
								if($CustoTotalServico[$idEvento][$idParceiro][$idServico]>0) $Content .= "<div class='col-xs-5'>CUSTO TOTAL: ".$this->MaskValue->Moeda($CustoTotalServico[$idEvento][$idParceiro][$idServico],'add')."</div>";
								if($PrecoTotalServico[$idEvento][$idParceiro][$idServico]>0) $Content .= "<div class='col-xs-6'>PREÇO TOTAL: ".$this->MaskValue->Moeda($PrecoTotalServico[$idEvento][$idParceiro][$idServico],'add')."</div>";







								/*### RELATÓRIO DE ATENDIMENTOS - SINTÉTICO ###*/
								if($this->Request["relatorio"]==1) {

									$Content .= "<div class='col-xs-11' style='margin-top:6px; margin-bottom:6px;'>ATENDIMENTOS POR STATUS</div>";

									foreach ($Status[$idEvento][$idParceiro][$idServico] as $idStatus => $Aten_Status_Nome) {

										if(($idStatus=="0")||($idStatus=="3")) $class = "2";
										else $class = "3";

										$Content .= "<div class='col-xs-".$class."' style='font-size:11px;'><span style='font-weight:bold;'>".$Aten_Status_Nome.":</span> ".$Atendimentos[$idEvento][$idParceiro][$idServico][$idStatus]."</div>";
									}
								}
								/*### RELATÓRIO DE ATENDIMENTOS - SINTÉTICO ###*/















								/*### RELATÓRIO DE ATENDIMENTOS - ANALÍTICO ###*/
								elseif($this->Request["relatorio"]==2) {

									$Content .= "<div class='col-xs-11' style='margin-top:6px; margin-bottom:6px;'>ATENDIMENTOS</div>";

									$Content .= "<div style='width:100%;'>";
									$Content .= "<table class='table table-bordered' style='font-size:9px;'>";
									$Content .= "<thead>";
									$Content .= "<tr>";
									$Content .= "<th style='width:5%; padding:2px;'>Senha</th>";
									$Content .= "<th style='width:20%; padding:2px;'>Nome</th>";
									$Content .= "<th style='width:12%; padding:2px;'>CPF</th>";
									$Content .= "<th style='width:13%; padding:2px;'>Celular</th>";
									$Content .= "<th style='width:8%; padding:2px;'>Hora Início</th>";
									$Content .= "<th style='width:8%; padding:2px;'>Hora Atend.</th>";
									$Content .= "<th style='width:8%; padding:2px;'>Tmpo Espera</th>";
									$Content .= "<th style='width:8%; padding:2px;'>Hora Saída</th>";
									$Content .= "<th style='width:8%; padding:2px;'>Tempo Atend.</th>";
									$Content .= "<th style='width:10%; padding:2px;'>Status</th>";
									$Content .= "</tr>";
									$Content .= "</thead>";
									$Content .= "<tbody>";

									foreach ($PessoaNome[$idEvento][$idParceiro][$idServico] as $idAtendimento => $Pess_Nome) {

										$Content .= "<tr>";
										$Content .= "<td style='padding:2px;'>".$AtendimentoSenha[$idEvento][$idParceiro][$idServico][$idAtendimento]."</td>";
										$Content .= "<td style='padding:2px;'>".$Pess_Nome."</td>";
										$Content .= "<td style='padding:2px;'>".$PessoaCPF[$idEvento][$idParceiro][$idServico][$idAtendimento]."</td>";
										$Content .= "<td style='padding:2px;'>".$PessoaCelular[$idEvento][$idParceiro][$idServico][$idAtendimento]."</td>";
										$Content .= "<td style='padding:2px;'>".$AtendimentoHoraInicio[$idEvento][$idParceiro][$idServico][$idAtendimento]."</td>";
										$Content .= "<td style='padding:2px;'>".$AtendimentoHoraAtend[$idEvento][$idParceiro][$idServico][$idAtendimento]."</td>";
										$Content .= "<td style='padding:2px;'>".$AtendimentoEspera[$idEvento][$idParceiro][$idServico][$idAtendimento]."</td>";
										$Content .= "<td style='padding:2px;'>".$AtendimentoHoraFim[$idEvento][$idParceiro][$idServico][$idAtendimento]."</td>";
										$Content .= "<td style='padding:2px;'>".$AtendimentoTempo[$idEvento][$idParceiro][$idServico][$idAtendimento]."</td>";
										$Content .= "<td style='padding:2px;'>".$AtendimentoStatus[$idEvento][$idParceiro][$idServico][$idAtendimento]."</td>";
										$Content .= "</tr>";
									}

									$Content .= "</tbody>";
									$Content .= "</table>";
									$Content .= "</div>";
								}
								/*### RELATÓRIO DE ATENDIMENTOS - ANALÍTICO ###*/













								/*### RELATÓRIO DE ATENDIMENTOS - PÓS-MULTIAÇÃO ###*/
								elseif($this->Request["relatorio"]==3) {

									$Content .= "<div class='col-xs-11' style='margin-top:6px; margin-bottom:6px;'>ATENDIMENTOS</div>";

									$Content .= "<div style='width:100%;'>";
									$Content .= "<table class='table table-bordered' style='font-size:9px;'>";
									$Content .= "<thead>";
									$Content .= "<tr>";
									$Content .= "<th style='width:5%; padding:2px;'>Senha</th>";
									$Content .= "<th style='width:20%; padding:2px;'>Nome</th>";
									$Content .= "<th style='width:11%; padding:2px;'>CPF</th>";
									$Content .= "<th style='width:12%; padding:2px;'>Telefone</th>";
									$Content .= "<th style='width:12%; padding:2px;'>Celular</th>";
									$Content .= "<th style='width:15%; padding:2px;'>Bairro</th>";
									$Content .= "<th style='width:15%; padding:2px;'>Cidade/UF</th>";
									$Content .= "<th style='width:10%; padding:2px;'>Status</th>";
									$Content .= "</tr>";
									$Content .= "</thead>";
									$Content .= "<tbody>";

									foreach ($PessoaNome[$idEvento][$idParceiro][$idServico] as $idAtendimento => $Pess_Nome) {

										$Content .= "<tr>";
										$Content .= "<td style='padding:2px;'>".$AtendimentoSenha[$idEvento][$idParceiro][$idServico][$idAtendimento]."</td>";
										$Content .= "<td style='padding:2px;'>".$Pess_Nome."</td>";
										$Content .= "<td style='padding:2px;'>".$PessoaCPF[$idEvento][$idParceiro][$idServico][$idAtendimento]."</td>";
										$Content .= "<td style='padding:2px;'>".$PessoaTelefone[$idEvento][$idParceiro][$idServico][$idAtendimento]."</td>";
										$Content .= "<td style='padding:2px;'>".$PessoaCelular[$idEvento][$idParceiro][$idServico][$idAtendimento]."</td>";
										$Content .= "<td style='padding:2px;'>".$PessoaBairro[$idEvento][$idParceiro][$idServico][$idAtendimento]."</td>";
										$Content .= "<td style='padding:2px;'>".$PessoaCidade[$idEvento][$idParceiro][$idServico][$idAtendimento]."/".$PessoaUF[$idEvento][$idParceiro][$idServico][$idAtendimento]."</td>";
										$Content .= "<td style='padding:2px;'>".$AtendimentoStatus[$idEvento][$idParceiro][$idServico][$idAtendimento]."</td>";
										$Content .= "</tr>";
									}

									$Content .= "</tbody>";
									$Content .= "</table>";
									$Content .= "</div>";
								}
								/*### RELATÓRIO DE ATENDIMENTOS - PÓS-MULTIAÇÃO ###*/






								$Content .= "<div class='col-xs-11' style='margin-top:8px;'>TOTAL DE ATENDIMENTOS: ".array_sum($Atendimentos[$idEvento][$idParceiro][$idServico])."</div>";
							}
						}
					}
				}
			}

			elseif($this->Request["relatorio"]==4) {

				$Header[4] = "";

				$Filename[4] = $this->MaskValue->Filename("Certificado de Participação - Parceiro - ".$this->Date["NowBR"],'add');

				$Footer[4] = "";

				$Orientation = "L";
				$Margin = "zero";

				if(count($this->ResultSQLAction)>0) {

					foreach ($this->ResultSQLAction as $key => $DataObject) {

						$NumReg++;

						$Content .= "<div style='position:absolute; left:0; top: 0; width:297mm; height:210mm; background-image:url(http://".$this->Path["HttpReferer"]."/images/fundo-certificado.jpg); background-size:297mm 210mm;'></div>";
						$Content .= "<div style='position:absolute; left:30mm; top:80mm; width:237mm; height:50mm; font-size:14pt; color:#4F80C1;'>";
						$Content .= "Certificamos que <span style='font-weight:bold'>".$DataObject->Parc_Razao_Nome."</span> ";
						$Content .= "participou do <span style='font-weight:bold'>".$DataObject->Even_Titulo."</span>, ";
						$Content .= "realizado no dia ".$DataObject->Even_Data_Dia." de ".$this->MonthsN[$DataObject->Even_Data_Mes]." de ".$DataObject->Even_Data_Ano.", ";
						$Content .= "como PARCEIRO, no Estado de Mato Grosso.";
						$Content .= "<br><br>Total de Atendimentos: ".number_format($DataObject->ATENDIMENTOS,0,",",".")." ";
						$Content .= "<br><br><p style='text-align:center'>Cuiabá, MT, ".$this->Date["NowBR_Data_Ext"]."</p>";
						$Content .= "</div>";
						if((count($this->ResultSQLAction)>1)&&($NumReg<count($this->ResultSQLAction))) $Content .= "<pagebreak />";
					}
				}

			}


			// echo "<pre>".$Header."</pre>";
			// echo "<pre>".$Content."</pre>";
			// echo "<pre>".$Footer."</pre>";

			// echo $this->SQLAction;
			// exit;


			if(($this->Request["formato"]=="pdf")&&($Content<>"")) {

				$PDF = $this->PDFClass;
				$PDF->setFilename($Filename[$this->Request["relatorio"]]);
				$PDF->setHeader($Header[$this->Request["relatorio"]]);
				$PDF->setFooter($Footer);
				$PDF->setContent($Content);
				$PDF->SetOrientation($Orientation);
				$PDF->SetMargin($Margin);
				$PDF->GeneratePDF();
			}

			// elseif($this->Request["formato"]=="word") {

			// 	$Word = $this->WordClass;
			// 	$Word->setFilename($Filename);
			// 	$Word->setHeader($Header);
			// 	$Word->setFooter($Footer);
			// 	$Word->setContent($Content);
			// 	$Word->GenerateWord();
			// }


		}




	}
	/*### RETORNA O RESULTADO DA ExecuteAction CONFORME A ACTION ###*/


}
/*### CLASSE DE EXPORTAÇÃO DE DADOS (HERDA CRUD) ###*/
