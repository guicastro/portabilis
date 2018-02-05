<?php

/*#######################################################
|														|
| Arquivo com a classe de Exportação de Dados			|
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
class Exporta extends \Database\Crud {

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


		if($this->Request["origem"]=="dados-atendimento") {

			$this->SQLSelectFields = "SELECT DISTINCT
											Eventos.Even_Titulo,
											Eventos.Even_Data,
											Parceiros.Parc_Razao_Nome,
											Parceiros.Parc_Pessoa,
											(CASE Parceiros.Parc_Pessoa
												WHEN 'PJ'
													THEN Parceiros.Parc_CNPJ
												WHEN 'PF'
													THEN Parceiros.Parc_CPF
												ELSE NULL
											END) AS Parc_CNPJ_CPF,
											ServicosTipos.ServTipo_Titulo,
											Servicos.Serv_Titulo,
											Pessoas.Pess_id,
											Pessoas.Pess_Nome,
											Pessoas.Pess_CPF,
											Pessoas.Pess_RG,
											Pessoas.Pess_DtNascimento,
											EST_CIVIL.TabeDinaValo_Descricao AS Pess_Estado_Civil,
											SEXO.TabeDinaValo_Descricao AS Pess_Sexo,
											Pessoas.Pess_Endereco_Logradouro,
											Pessoas.Pess_Endereco_Numero,
											Pessoas.Pess_Endereco_Complemento,
											Pessoas.Pess_Endereco_Bairro,
											Pessoas.Pess_Endereco_CEP,
											Pessoas.Pess_Endereco_Cidade,
											Pessoas.Pess_Endereco_UF,
											Pessoas.Pess_Celular,
											Pessoas.Pess_Telefone,
											Pessoas.Pess_Email,
											Atendimentos.Aten_SenhaNumero,
											Atendimentos.Aten_Hora_Inicio,
											Atendimentos.Aten_Hora_Atend,
											Atendimentos.Aten_Hora_Fim,
											STATUS_ATEN.TabeDinaValo_Descricao AS Aten_Status,
											POSM.TabeDinaValo_Descricao AS Aten_PosMultiacao ";
		}


		return $this->SQLSelectFields;
	}
	/*### MONTA O INÍCIO DO SELECT, COM AS COLUNAS QUE SERÃO RETORNADAS ###*/




	/*### MONTA O FROM DO SELECT, COM A(S) TABELA(S) DA ORIGEM DE DADOS ###*/
	public function setSQLSelectFrom () {


		if($this->Request["origem"]=="dados-atendimento") {

			$this->SQLSelectFrom = "FROM
										Atendimentos
									INNER JOIN
										Pessoas ON Pessoas.Pess_id = Atendimentos.Pessoas_Pess_id
												AND Pessoas.Pess_Delete = 0
									INNER JOIN
										ParceirosServicos ON ParceirosServicos.ParcServ_id = Atendimentos.ParceirosServicos_ParcServ_id
															AND ParceirosServicos.ParcServ_Delete = 0
									INNER JOIN
										Eventos ON Eventos.Even_id = ParceirosServicos.Eventos_Even_id
												AND Eventos.Even_Delete = 0
									INNER JOIN
										Parceiros ON Parceiros.Parc_id = ParceirosServicos.Parceiros_Parc_id
													AND Parceiros.Parc_Delete = 0
									INNER JOIN
										Servicos ON Servicos.Serv_id = ParceirosServicos.Servicos_Serv_id
													AND Servicos.Serv_Delete = 0
									INNER JOIN
										ServicosTipos ON ServicosTipos.ServTipo_id = Servicos.ServicosTipos_ServTipo_id
														AND ServicosTipos.ServTipo_Delete = 0
									INNER JOIN
										TabelasDinamicasValores SEXO ON SEXO.TabeDinaValo_Codigo = Pessoas.Pess_Sexo
																	AND SEXO.TabeDinaValo_Tabela = 'SEXO'
																	AND SEXO.TabeDinaValo_Delete = 0
									INNER JOIN
										TabelasDinamicasValores EST_CIVIL ON EST_CIVIL.TabeDinaValo_Codigo = Pessoas.Pess_Estado_Civil
																		AND EST_CIVIL.TabeDinaValo_Tabela = 'ESTADO_CIVIL'
																		AND EST_CIVIL.TabeDinaValo_Delete = 0
									INNER JOIN
										TabelasDinamicasValores STATUS_ATEN ON STATUS_ATEN.TabeDinaValo_Codigo = Atendimentos.Aten_Status
																			AND STATUS_ATEN.TabeDinaValo_Tabela = 'STATUS_ATENDIMENTOS'
																			AND STATUS_ATEN.TabeDinaValo_Delete = 0
									INNER JOIN
										TabelasDinamicasValores POSM ON POSM.TabeDinaValo_Codigo = Atendimentos.Aten_PosMultiacao
																	AND POSM.TabeDinaValo_Tabela = 'SIM_NAO'
																	AND POSM.TabeDinaValo_Delete = 0 ";
		}


		return $this->SQLSelectFrom;
	}
	/*### MONTA O FROM DO SELECT, COM A(S) TABELA(S) DA ORIGEM DE DADOS ###*/




	/*### MONTA O WHERE DO SELECT, COM AS CONDIÇÕES DE BUSCA ###*/
	public function setSQLSelectWhere () {

		if($this->Request["origem"]=="dados-atendimento") {

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


			//MONTA AS CONDIÇÕES WHERE DO SELECT
			$SQLSelectWhere = "WHERE
									Atendimentos.Aten_Delete = 0 "
									.$ConditionEvento
									.$ConditionParceiro
									.$ConditionServico
									.$ConditionParceiroPerm;
		}

		$this->SQLSelectWhere = $SQLSelectWhere;
		return $this->SQLSelectWhere;

	}
	/*### MONTA O WHERE DO SELECT, COM AS CONDIÇÕES DE BUSCA ###*/



	/*### MONTA O ORDER DO SELECT, COM A ORDEM DAS COLUNAS ###*/
	public function setSQLSelectOrder () {


		if($this->Request["origem"]=="dados-atendimento") {

			$this->SQLSelectOrder = "ORDER BY
										Eventos.Even_Titulo,
										Parceiros.Parc_Razao_Nome,
										ServicosTipos.ServTipo_Titulo,
										Servicos.Serv_Titulo,
										Pessoas.Pess_Nome,
										Atendimentos.Aten_Hora_Inicio";
		}

		return $this->SQLSelectOrder;
	}
	/*### MONTA O ORDER DO SELECT, COM A ORDEM DAS COLUNAS ###*/





	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/
	public function MaskResultSQLAction () {

		if($this->Request["origem"]=="dados-atendimento") {

			$ResultSQLAction = $this->ResultSQLAction;

			if(count($ResultSQLAction)>0) {

				foreach ($ResultSQLAction as $key => $DataArray) {

					if(is_array($DataArray)) {

						$ResultSQLAction[$key]["Even_Data"] = $this->MaskValue->Data($ResultSQLAction[$key]["Even_Data"],'US2BR');
						$ResultSQLAction[$key]["Pess_DtNascimento"] = $this->MaskValue->Data($ResultSQLAction[$key]["Pess_DtNascimento"],'US2BR');
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
					$ResultSQLAction = $ExecuteSQLAction->fetchAll(\PDO::FETCH_ASSOC);

					//ADICIONA NO OBJETO A ACTION
					if($this->Action<>"Grid") $ResultSQLAction["action"] = $this->Action;

					//ARMAZENA O OBJETO PDO NA ResultSQLAction
					$this->setResultSQLAction($ResultSQLAction);
					return $this->ResultSQLAction;
				}
				/*### SE A ACTION FOR SELECIONAR|LOGIN|GRID OU SE A CHAMADA DE UMA TABELA DINÂMICA ###*/



				/*### SENÃO RETORNA OBJETO DO PDO E ACTION ###*/
				else {

					//CRIA OBJETO COM O RESULTADO DA SQLAction
					$ResultSQLAction = $ExecuteSQLAction;

					//ADICIONA NO OBJETO A ACTION
					// $ResultSQLAction->action = $this->Action;

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


			//REALIZA NOVA CONSULTA PARA ESTRUTURA DE COLUNAS
			$ExecuteSQLActionColumns = $this->db->query($this->SQLAction);

			//CRIA OBJETO COM O RESULTADO DA ESTRUTURA DE COLUNAS
			$ResultColumns = $ExecuteSQLActionColumns->fetch(\PDO::FETCH_OBJ);


			foreach ($ResultColumns as $Column => $Value) {

				$CSVHeader[] = $Column;
			}

			$filename = "multiacao_dados_atendimentos_".str_replace(" ", "_", str_replace(":","-", $this->Date["NowUS"])).".csv";
			$fp = fopen('php://output', 'w');

			header('Content-type: application/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename='.$filename);
			fputcsv($fp, $CSVHeader);

			foreach ($this->ResultSQLAction as $key => $DataArray) {

				if(is_array($DataArray)) {

					fputcsv($fp, $DataArray);
				}
			}

		}
		/*### SENÃO RETORNA MENSAGEM DE ERRO ###*/

	}
	/*### RETORNA O RESULTADO DA ExecuteAction CONFORME A ACTION ###*/


}
/*### CLASSE DE EXPORTAÇÃO DE DADOS (HERDA CRUD) ###*/
