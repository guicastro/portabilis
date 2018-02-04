<?php

/*#######################################################
|														|
| Arquivo com a classe do Painel Online					|
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

/*### CLASSE DO PAINEL ONLINE (HERDA CRUD) ###*/
class Painel extends \Database\Crud {

	protected $DynamicTable;
	protected $Path;

	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/
	public function __construct($container) {

		parent::__construct($container['Connect'], $container);

		//CHAMADA PARA CLASSE TABELADINAMICA
		$this->DynamicTable = $container['DynamicTable'];

		//ARMAZENA AS VARIÁVEIS DOS CAMINHOS DAS PASTAS E HTTP
		$this->Path = array("AbsolutePath" => $container['AbsolutePath'],
							"RelativePath" => $container['RelativePath'],
							"HttpReferer" => $container['HttpReferer']);
	}
	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/









	/*### MONTA O INÍCIO DO SELECT, COM AS COLUNAS QUE SERÃO RETORNADAS ###*/
	public function setSQLSelectFields () {


		/*### SQL PADRÃO ###*/
		$SQLSelectFields = "SELECT TBL.* "
								.", (SELECT Usua_Nome FROM Usuarios WHERE Usua_id = TBL.".$this->ModuleDefs->Prefix."RecCreatedby) as ".$this->ModuleDefs->Prefix."RecCreatedbyName"
								.", (SELECT Usua_Nome FROM Usuarios WHERE Usua_id = TBL.".$this->ModuleDefs->Prefix."RecModifiedby) as ".$this->ModuleDefs->Prefix."RecModifiedbyName ";
		/*### SQL PADRÃO ###*/


		/*### SQL DO GRÁFICO atendimentos-real-time ###*/
		if($this->Request["Graph"]=="atendimentos-real-time") {

			$SQLSelectFields = "SELECT
									HOUR(Atendimentos.Aten_Data) + (MINUTE(Atendimentos.Aten_Data)/60) AS HORA_MIN,
									SUM(Atendimentos.Aten_Qtd) AS TOTAL ";
		}
		/*### SQL DO GRÁFICO atendimentos-real-time ###*/





		/*### SQL DO GRÁFICO atendimentos-spark ###*/
		else if($this->Request["Graph"]=="atendimentos-spark") {

			$SQLSelectFields = "SELECT
									HOUR(Atendimentos.Aten_Data) AS HORA,
									SUM(Atendimentos.Aten_Qtd) AS TOTAL ";
		}
		/*### SQL DO GRÁFICO atendimentos-spark ###*/





		/*### SQL DO GRÁFICO pessoas-total ###*/
		else if($this->Request["Graph"]=="pessoas-total") {

			$SQLSelectFields = "SELECT DISTINCT
										Atendimentos.Pessoas_Pess_id ";
		}
		/*### SQL DO GRÁFICO pessoas-total ###*/





		/*### SQL DO GRÁFICO pessoas-spark ###*/
		else if($this->Request["Graph"]=="pessoas-spark") {

			$SQLSelectFields = "SELECT DISTINCT
										TBL.HORA AS HORA,
										COUNT(TBL.Pessoas_Pess_id) AS TOTAL ";
		}
		/*### SQL DO GRÁFICO pessoas-spark ###*/





		/*### SQL DO GRÁFICO servicos-senhas ###*/
		else if($this->Request["Graph"]=="servicos-senhas") {

			$SQLSelectFields = "SELECT DISTINCT
									Servicos.Serv_Titulo,
								    Parceiros.Parc_Razao_Nome,
								    ParceirosServicos.ParcServ_id,
    								IFNULL(Atendimentos.Aten_Status,0) AS Aten_Status,
								    SUM(Atendimentos.Aten_Qtd) TOTAL_POR_STATUS,
								    IFNULL(ParceirosSenhas.ParcSenh_SenhasEmitidas + ParceirosSenhas.ParcSenh_SenhasReservadas,0) AS SenhasUtilizadas,
								    IFNULL(ParceirosSenhas.ParcSenh_Numero + ParceirosSenhas.ParcSenh_NumAdicional,0) AS SenhasTotais,
								    ROUND((IFNULL(ParceirosSenhas.ParcSenh_SenhasEmitidas + ParceirosSenhas.ParcSenh_SenhasReservadas,0) / IFNULL(ParceirosSenhas.ParcSenh_Numero + ParceirosSenhas.ParcSenh_NumAdicional,0))*100) AS SenhasPercentual ";
		}
		/*### SQL DO GRÁFICO servicos-senhas ###*/





		/*### SQL DO GRÁFICO servicos-categorias ###*/
		else if($this->Request["Graph"]=="servicos-categorias") {

			$SQLSelectFields = "SELECT DISTINCT
									ServicosTipos.ServTipo_id,
									ServicosTipos.ServTipo_Titulo,
									Servicos.Serv_id,
									Servicos.Serv_Titulo,
									Parceiros.Parc_id,
									Parceiros.Parc_Razao_Nome,
									ParceirosServicos.ParcServ_id,
									SUM(Atendimentos.Aten_Qtd) AS QTD ";
		}
		/*### SQL DO GRÁFICO servicos-categorias ###*/




		$this->SQLSelectFields = $SQLSelectFields;

		return $this->SQLSelectFields;
	}
	/*### MONTA O INÍCIO DO SELECT, COM AS COLUNAS QUE SERÃO RETORNADAS ###*/














	/*### MONTA O FROM DO SELECT, COM A(S) TABELA(S) DA ORIGEM DE DADOS ###*/
	public function setSQLSelectFrom () {


		/*### SQL PADRÃO ###*/
		$SQLSelectFrom = "FROM ".$this->ModuleDefs->Table." TBL  ";
		/*### SQL PADRÃO ###*/




		/*### SQL DO GRÁFICO atendimentos-real-time OU atendimentos-spark ###*/
		if(($this->Request["Graph"]=="atendimentos-real-time")||
			($this->Request["Graph"]=="atendimentos-spark")||
			($this->Request["Graph"]=="pessoas-total")) {

			$SQLSelectFrom = "FROM
								Atendimentos
							INNER JOIN
								ParceirosServicos ON ParceirosServicos.ParcServ_id = Atendimentos.ParceirosServicos_ParcServ_id
													AND ParceirosServicos.ParcServ_Delete = 0 ";
		}
		/*### SQL DO GRÁFICO atendimentos-real-time OU atendimentos-spark ###*/







		/*### SQL DO GRÁFICO pessoas-spark ###*/
		elseif($this->Request["Graph"]=="pessoas-spark") {


			//USA O PrimaryKeyName SE TIVER SIDO DEFINIDO, SENÃO USA O PADRÃO id
			$PrimaryKeyName = ($this->PrimaryKeyName<>"") ? $this->PrimaryKeyName : "id";

			//ADICIONA ASPAS SIMPLES NO PrimaryKey SE PrimaryKeyName SE TIVER SIDO DEFINIDO, SENÃO USA O PADRÃO
			$PrimaryKeyValue = ($this->PrimaryKeyName<>"") ? "'".$this->PrimaryKey."'" : $this->PrimaryKey;


			$SQLSelectFrom = "FROM
								(SELECT DISTINCT
									HOUR(Atendimentos.Aten_Data) AS HORA,
									Atendimentos.Pessoas_Pess_id
								FROM
									Atendimentos
								INNER JOIN
									ParceirosServicos ON ParceirosServicos.ParcServ_id = Atendimentos.ParceirosServicos_ParcServ_id
										AND ParceirosServicos.ParcServ_Delete = 0
								WHERE
									Atendimentos.Aten_Delete = 0
									AND Atendimentos.Pessoas_Pess_id IS NOT NULL
										AND Atendimentos.Aten_Status <> 99
										AND ParceirosServicos.Eventos_Even_id = ".$PrimaryKeyValue."
										%SQL_PARCEIRO%
								GROUP BY
									HOUR(Atendimentos.Aten_Data), Atendimentos.Pessoas_Pess_id) TBL ";



			/*### ADICIONA CONDIÇÕES WHERE QUANDO HÁ SPECIALPERMISSIONS ###*/
			if($this->SpecialPermissions<>"") {

				//EXTRAI A PERMISSÃO ESPECIAL DE RESTRIÇÃO DE ACESSO POR PARCEIRO
				$Parceiros = json_decode($this->SpecialPermissions)->parceiros;
				// print_r($Parceiros);


				/*### SE EXISTIR RESTRIÇÃO POR PARCEIRO, ADICIONA NA SQL A RESTRIÇÃO ###*/
				if(count($Parceiros)>0)
					{
						foreach ($Parceiros as $key => $idParceiro) {

							$SQLINParceiros .= $idParceiro.", ";

						}

						//ARRAY COM AS TABELAS QUE POSSUEM RESTRIÇÃO DE PERMISSÃO DE PARCEIROS
						$RestrictedTables = $this->TokenClass->getClaim("Data")->DefaultConfig->TabelasRestricaoParceiros;
						// print_r($RestrictedTables);


						/*### SE EXISTIR TABELAS COM RESTRIÇÃO DE PARCEIROS, ADICIONA NAS CONSULTAS A RESTRIÇÃO ###*/
						if(count($RestrictedTables)>0) {

							/*### CRIA UM ARRAY COM AS TABELAS DO INNER JOIN QUE POSSUEM RESTRIÇÃO ###*/
							foreach ($RestrictedTables as $key => $RestrictedTablesObject) {

								foreach ($RestrictedTablesObject as $Table => $Field) {
									// echo "\n\nTable: ".$Table;
									// echo "\nField: ".$Field;
									// echo "\nCount: ".substr_count($SQLSelectFrom, $Table.".");
									if(substr_count($SQLSelectFrom, $Table.".")>0) {
										$RestrictedJoinTables[] = $Table;
									}
								}
							}
							/*### CRIA UM ARRAY COM AS TABELAS DO INNER JOIN QUE POSSUEM RESTRIÇÃO ###*/



							/*### ADICIONA A RESTRIÇÃO NAS TABELAS DE JOIN, SE EXISTIR ###*/
							if(count($RestrictedJoinTables)>0) {

								foreach ($RestrictedJoinTables as $key => $Table) {

									foreach ($RestrictedTables as $key => $RestrictedTablesObject) {

										if($RestrictedTablesObject->$Table) {

											//ADICIONA AS CONDIÇÕES WHERE
											$SQLRestrictParceiro .= " AND ".$Table.".".$RestrictedTablesObject->$Table." IN (".substr($SQLINParceiros,0,-2).") ";
										}
									}
								}
							}
							/*### ADICIONA A RESTRIÇÃO NAS TABELAS DE JOIN, SE EXISTIR ###*/

						}
						/*### SE EXISTIR TABELAS COM RESTRIÇÃO DE PARCEIROS, ADICIONA NAS CONSULTAS A RESTRIÇÃO ###*/

					}
				/*### SE EXISTIR RESTRIÇÃO POR PARCEIRO, ADICIONA NA SQL A RESTRIÇÃO ###*/



			}
			/*### ADICIONA CONDIÇÕES WHERE QUANDO HÁ SPECIALPERMISSIONS ###*/


			//FILTRA A LINHA DE CONDIÇÃO QUANDO EXISTIR RESTRIÇÃO DE PARCEIRO
			$SQLSelectFrom = str_replace("%SQL_PARCEIRO%",$SQLRestrictParceiro,$SQLSelectFrom);

		}
		/*### SQL DO GRÁFICO pessoas-spark ###*/









		/*### SQL DO GRÁFICO servicos-senhas ###*/
		if($this->Request["Graph"]=="servicos-senhas") {

			$SQLSelectFrom = "FROM
									ParceirosServicos

								INNER JOIN
									ParceirosSenhas ON ParceirosSenhas.ParceirosServicos_ParcServ_id = ParceirosServicos.ParcServ_id
													AND ParceirosSenhas.ParcSenh_Delete = 0

								INNER JOIN
									Servicos ON Servicos.Serv_id = ParceirosServicos.Servicos_Serv_id
												AND Servicos.Serv_Delete = 0

								INNER JOIN
									Parceiros ON Parceiros.Parc_id = ParceirosServicos.Parceiros_Parc_id
												AND Parceiros.Parc_Delete = 0

								LEFT JOIN
									Atendimentos ON Atendimentos.ParceirosServicos_ParcServ_id = ParceirosServicos.ParcServ_id
													AND Atendimentos.Aten_Delete = 0
													AND Atendimentos.Aten_Status <> 99 ";
		}
		/*### SQL DO GRÁFICO servicos-senhas ###*/









		/*### SQL DO GRÁFICO servicos-categorias ###*/
		if($this->Request["Graph"]=="servicos-categorias") {

			$SQLSelectFrom = "FROM
									Atendimentos
								INNER JOIN
									ParceirosServicos ON ParceirosServicos.ParcServ_id = Atendimentos.ParceirosServicos_ParcServ_id
														AND ParceirosServicos.ParcServ_Delete = 0
								INNER JOIN
									Servicos ON Servicos.Serv_id = ParceirosServicos.Servicos_Serv_id
												AND Servicos.Serv_Delete = 0
								INNER JOIN
									ServicosTipos ON ServicosTipos.ServTipo_id = Servicos.ServicosTipos_ServTipo_id
													AND ServicosTipos.ServTipo_Delete = 0
								INNER JOIN
									Parceiros ON Parceiros.Parc_id = ParceirosServicos.Parceiros_Parc_id
												AND Parceiros.Parc_Delete = 0 ";
		}
		/*### SQL DO GRÁFICO servicos-categorias ###*/






		$this->SQLSelectFrom = $SQLSelectFrom;

		return $this->SQLSelectFrom;

	}
	/*### MONTA O FROM DO SELECT, COM A(S) TABELA(S) DA ORIGEM DE DADOS ###*/













	/*### MONTA O WHERE DO SELECT, COM AS CONDIÇÕES DE BUSCA ###*/
	public function setSQLSelectWhere () {

		//USA O PrimaryKeyName SE TIVER SIDO DEFINIDO, SENÃO USA O PADRÃO id
		$PrimaryKeyName = ($this->PrimaryKeyName<>"") ? $this->PrimaryKeyName : "id";

		//ADICIONA ASPAS SIMPLES NO PrimaryKey SE PrimaryKeyName SE TIVER SIDO DEFINIDO, SENÃO USA O PADRÃO
		$PrimaryKeyValue = ($this->PrimaryKeyName<>"") ? "'".$this->PrimaryKey."'" : $this->PrimaryKey;





		/*### SQL PADRÃO ###*/
		$SQLSelectWhere = "WHERE ".$this->ModuleDefs->Prefix.$PrimaryKeyName." = ".$PrimaryKeyValue." AND ".$this->ModuleDefs->Prefix."Delete = 0";
		/*### SQL PADRÃO ###*/




		/*### SQL DO GRÁFICO atendimentos-real-time ###*/
		if($this->Request["Graph"]=="atendimentos-real-time") {

			$SQLSelectWhere = "WHERE Atendimentos.Aten_Delete = 0
									AND ParceirosServicos.Eventos_Even_id =  ".$PrimaryKeyValue." ";
		}
		/*### SQL DO GRÁFICO atendimentos-real-time ###*/





		/*### SQL DO GRÁFICO atendimentos-spark ###*/
		elseif($this->Request["Graph"]=="atendimentos-spark") {

			$SQLSelectWhere = "WHERE Atendimentos.Aten_Delete = 0
									AND Atendimentos.Aten_Status <> 99
									AND ParceirosServicos.Eventos_Even_id =  ".$PrimaryKeyValue." ";
		}
		/*### SQL DO GRÁFICO atendimentos-spark ###*/




		/*### SQL DO GRÁFICO servicos-senhas ###*/
		elseif($this->Request["Graph"]=="servicos-senhas") {

			$SQLSelectWhere = "WHERE
									ParceirosServicos.ParcServ_Delete = 0
    								AND ParceirosServicos.ParcServ_Status = 1
									AND ParceirosServicos.Eventos_Even_id =  ".$PrimaryKeyValue." ";
		}
		/*### SQL DO GRÁFICO servicos-senhas ###*/






		/*### SQL DO GRÁFICO pessoas-total ###*/
		elseif($this->Request["Graph"]=="pessoas-total") {

			$SQLSelectWhere = "WHERE Atendimentos.Aten_Delete = 0
									AND Atendimentos.Pessoas_Pess_id IS NOT NULL
									AND Atendimentos.Aten_Status <> 99
									AND ParceirosServicos.Eventos_Even_id =  ".$PrimaryKeyValue." ";
		}
		/*### SQL DO GRÁFICO pessoas-total ###*/








		/*### SQL DO GRÁFICO pessoas-spark ###*/
		if($this->Request["Graph"]=="pessoas-spark") {

			$SQLSelectWhere = " ";
		}
		/*### SQL DO GRÁFICO pessoas-spark ###*/








		/*### SQL DO GRÁFICO servicos-categorias ###*/
		if($this->Request["Graph"]=="servicos-categorias") {

			$SQLSelectWhere = "WHERE
									Atendimentos.Aten_Delete = 0
        							AND Atendimentos.Aten_Status <> 99
									AND ParceirosServicos.Eventos_Even_id = ".$PrimaryKeyValue." ";
		}
		/*### SQL DO GRÁFICO servicos-categorias ###*/







		/*### ADICIONA CONDIÇÕES WHERE QUANDO HÁ SPECIALPERMISSIONS ###*/
		if($this->SpecialPermissions<>"") {

			//EXTRAI A PERMISSÃO ESPECIAL DE RESTRIÇÃO DE ACESSO POR PARCEIRO
			$Parceiros = json_decode($this->SpecialPermissions)->parceiros;
			// print_r($Parceiros);


			/*### SE EXISTIR RESTRIÇÃO POR PARCEIRO, ADICIONA NA SQL A RESTRIÇÃO ###*/
			if(count($Parceiros)>0)
				{
					foreach ($Parceiros as $key => $idParceiro) {

						$SQLINParceiros .= $idParceiro.", ";

					}

					//ARRAY COM AS TABELAS QUE POSSUEM RESTRIÇÃO DE PERMISSÃO DE PARCEIROS
					$RestrictedTables = $this->TokenClass->getClaim("Data")->DefaultConfig->TabelasRestricaoParceiros;
					// print_r($RestrictedTables);


					/*### SE EXISTIR TABELAS COM RESTRIÇÃO DE PARCEIROS, ADICIONA NAS CONSULTAS A RESTRIÇÃO ###*/
					if(count($RestrictedTables)>0) {

						/*### CRIA UM ARRAY COM AS TABELAS DO INNER JOIN QUE POSSUEM RESTRIÇÃO ###*/
						foreach ($RestrictedTables as $key => $RestrictedTablesObject) {

							foreach ($RestrictedTablesObject as $Table => $Field) {
								// echo "\n\nTable: ".$Table;
								// echo "\nField: ".$Field;
								// echo "\nCount: ".substr_count($SQLSelectWhere, $Table.".");
								if(substr_count($this->SQLSelectFrom, $Table.".")>0) {
									$RestrictedJoinTables[] = $Table;
								}
							}
						}
						/*### CRIA UM ARRAY COM AS TABELAS DO INNER JOIN QUE POSSUEM RESTRIÇÃO ###*/



						/*### ADICIONA A RESTRIÇÃO NAS TABELAS DE JOIN, SE EXISTIR ###*/
						if(count($RestrictedJoinTables)>0) {

							foreach ($RestrictedJoinTables as $key => $Table) {

								foreach ($RestrictedTables as $key => $RestrictedTablesObject) {

									if(($RestrictedTablesObject->$Table)&&($this->Request["Graph"]<>"pessoas-spark")) {

										//ADICIONA AS CONDIÇÕES WHERE
										$SQLSelectWhere .= " AND ".$Table.".".$RestrictedTablesObject->$Table." IN (".substr($SQLINParceiros,0,-2).") ";
									}
								}
							}
						}
						/*### ADICIONA A RESTRIÇÃO NAS TABELAS DE JOIN, SE EXISTIR ###*/

					}
					/*### SE EXISTIR TABELAS COM RESTRIÇÃO DE PARCEIROS, ADICIONA NAS CONSULTAS A RESTRIÇÃO ###*/

				}
			/*### SE EXISTIR RESTRIÇÃO POR PARCEIRO, ADICIONA NA SQL A RESTRIÇÃO ###*/



		}
		/*### ADICIONA CONDIÇÕES WHERE QUANDO HÁ SPECIALPERMISSIONS ###*/










		$this->SQLSelectWhere = $SQLSelectWhere;
		return $this->SQLSelectWhere;
	}
	/*### MONTA O WHERE DO SELECT, COM AS CONDIÇÕES DE BUSCA ###*/














	/*### MONTA O GROUP DO SELECT, COM OS AGRUPAMENTOS ###*/
	public function setSQLSelectGroup () {


		/*### SQL DO GRÁFICO atendimentos-real-time ###*/
		if($this->Request["Graph"]=="atendimentos-real-time") {

			$SQLSelectGroup = "GROUP BY
								HOUR(Atendimentos.Aten_Data) + (MINUTE(Atendimentos.Aten_Data)/60) ";
		}
		/*### SQL DO GRÁFICO atendimentos-real-time ###*/



		/*### SQL DO GRÁFICO atendimentos-spark ###*/
		elseif($this->Request["Graph"]=="atendimentos-spark") {

			$SQLSelectGroup = "GROUP BY
								HOUR(Atendimentos.Aten_Data) ";
		}
		/*### SQL DO GRÁFICO atendimentos-spark ###*/



		/*### SQL DO GRÁFICO pessoas-spark ###*/
		elseif($this->Request["Graph"]=="pessoas-spark") {

			$SQLSelectGroup = "GROUP BY
								TBL.HORA ";
		}
		/*### SQL DO GRÁFICO pessoas-spark ###*/



		/*### SQL DO GRÁFICO servicos-senhas ###*/
		elseif($this->Request["Graph"]=="servicos-senhas") {

			$SQLSelectGroup = "GROUP BY
									Servicos.Serv_Titulo,
									Parceiros.Parc_Razao_Nome,
									Atendimentos.Aten_Status,
									ParceirosServicos.ParcServ_id ";
		}
		/*### SQL DO GRÁFICO servicos-senhas ###*/




		/*### SQL DO GRÁFICO servicos-categorias ###*/
		elseif($this->Request["Graph"]=="servicos-categorias") {

			$SQLSelectGroup = "GROUP BY
									ServicosTipos.ServTipo_id,
									ServicosTipos.ServTipo_Titulo,
									Servicos.Serv_id,
									Servicos.Serv_Titulo,
									Parceiros.Parc_id,
									Parceiros.Parc_Razao_Nome,
									ParceirosServicos.ParcServ_id ";
		}
		/*### SQL DO GRÁFICO servicos-categorias ###*/


		$this->SQLSelectGroup = $SQLSelectGroup;

		return $this->SQLSelectGroup;
	}
	/*### MONTA O GROUP DO SELECT, COM OS AGRUPAMENTOS ###*/


















	/*### MONTA O ORDER DO SELECT, COM A ORDEM DAS COLUNAS ###*/
	public function setSQLSelectOrder () {


		/*### SQL DO GRÁFICO atendimentos-real-time ###*/
		if($this->Request["Graph"]=="atendimentos-real-time") {

			$SQLSelectOrder = "ORDER BY HOUR(Atendimentos.Aten_Data) + (MINUTE(Atendimentos.Aten_Data)/60) ";
		}
		/*### SQL DO GRÁFICO atendimentos-real-time ###*/



		/*### SQL DO GRÁFICO atendimentos-spark ###*/
		elseif($this->Request["Graph"]=="atendimentos-spark") {

			$SQLSelectOrder = "ORDER BY HOUR(Atendimentos.Aten_Data) ";
		}
		/*### SQL DO GRÁFICO atendimentos-spark ###*/



		/*### SQL DO GRÁFICO pessoas-spark ###*/
		elseif($this->Request["Graph"]=="pessoas-spark") {

			$SQLSelectOrder = "ORDER BY TBL.HORA ";
		}
		/*### SQL DO GRÁFICO pessoas-spark ###*/




		/*### SQL DO GRÁFICO servicos-senhas ###*/
		elseif($this->Request["Graph"]=="servicos-senhas") {

			$SQLSelectOrder = "ORDER BY
									Servicos.Serv_Titulo,
    								Parceiros.Parc_Razao_Nome ";
		}
		/*### SQL DO GRÁFICO servicos-senhas ###*/




		/*### SQL DO GRÁFICO servicos-categorias ###*/
		elseif($this->Request["Graph"]=="servicos-categorias") {

			$SQLSelectOrder = "ORDER BY
									ServicosTipos.ServTipo_Titulo,
									Servicos.Serv_Titulo,
									Parceiros.Parc_Razao_Nome ";
		}
		/*### SQL DO GRÁFICO servicos-categorias ###*/


		$this->SQLSelectOrder = $SQLSelectOrder;
		return $this->SQLSelectOrder;
	}
	/*### MONTA O ORDER DO SELECT, COM A ORDEM DAS COLUNAS ###*/


















	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/
	public function MaskResultSQLAction () {

		if($this->Action=="selecionar")
			{
				$ResultSQLAction = $this->ResultSQLAction;






				/*### TRATAMENTO DOS DADOS QUANDO FOR O GRÁFICO atendimentos-real-time ###*/
				if($this->Request["Graph"]=="atendimentos-real-time") {

					if(count($ResultSQLAction[0])>0) {

						foreach ($ResultSQLAction as $key => $DataObject) {

							if(is_numeric($key)) {

								$ResultSQLAction["Data"][$key] = array($DataObject->hora_min, $DataObject->total);
							}
						}
					}
				}
				/*### TRATAMENTO DOS DADOS QUANDO FOR O GRÁFICO atendimentos-real-time ###*/







				/*### TRATAMENTO DOS DADOS QUANDO FOR O GRÁFICO atendimentos-spark ###*/
				elseif($this->Request["Graph"]=="atendimentos-spark") {

					if(count($ResultSQLAction[0])>0) {

						foreach ($ResultSQLAction as $key => $DataObject) {

							if(is_numeric($key)) {

								$AtendimentosHora .= $DataObject->total.", ";
								$ResultSQLAction["Data"][$key]["HORA"] = $DataObject->hora;
								$ResultSQLAction["Data"][$key]["TOTAL"] = $DataObject->total;
								$ResultSQLAction["Data"][$key]["TOTAL_FORMAT"] = number_format($DataObject->total,0,",",".");
								$ResultSQLAction["TOTAL_GERAL"] += $DataObject->total;
							}
						}

						$ResultSQLAction["ATENDIMENTOS_HORA"] .= substr($AtendimentosHora,0,-2);
						$ResultSQLAction["TOTAL_GERAL_FORMAT"] = number_format($ResultSQLAction["TOTAL_GERAL"],0,",",".");
					}
				}
				/*### TRATAMENTO DOS DADOS QUANDO FOR O GRÁFICO atendimentos-spark ###*/








				/*### TRATAMENTO DOS DADOS QUANDO FOR O GRÁFICO pessoas-spark ###*/
				elseif($this->Request["Graph"]=="pessoas-spark") {

					if(count($ResultSQLAction[0])>0) {

						foreach ($ResultSQLAction as $key => $DataObject) {

							if(is_numeric($key)) {

								$PessoasHora .= $DataObject->total.", ";
								$ResultSQLAction["Data"][$key]["HORA"] = $DataObject->hora;
								$ResultSQLAction["Data"][$key]["TOTAL"] = $DataObject->total;
								$ResultSQLAction["Data"][$key]["TOTAL_FORMAT"] = number_format($DataObject->total,0,",",".");
							}
						}

						$ResultSQLAction["PESSOAS_HORA"] .= substr($PessoasHora,0,-2);
					}
				}
				/*### TRATAMENTO DOS DADOS QUANDO FOR O GRÁFICO pessoas-spark ###*/








				/*### TRATAMENTO DOS DADOS QUANDO FOR O GRÁFICO pessoas-total ###*/
				elseif($this->Request["Graph"]=="pessoas-total") {

					if(count($ResultSQLAction[0])>0) {

						foreach ($ResultSQLAction as $key => $DataObject) {

							if(is_numeric($key)) {

								$ResultSQLAction["Data"][$key]["PESSOA"] = $DataObject->pessoas_pess_id;
								$Pessoas++;
							}
						}

						$ResultSQLAction["TOTAL_GERAL"] = $Pessoas;
						$ResultSQLAction["TOTAL_GERAL_FORMAT"] = number_format($ResultSQLAction["TOTAL_GERAL"],0,",",".");
					}
				}
				/*### TRATAMENTO DOS DADOS QUANDO FOR O GRÁFICO pessoas-total ###*/









				/*### TRATAMENTO DOS DADOS QUANDO FOR O GRÁFICO servicos-senhas ###*/
				elseif($this->Request["Graph"]=="servicos-senhas") {

					// echo "\nINICIO\n"; print_r($ResultSQLAction);
					if(count($ResultSQLAction[0])>0) {

						foreach ($ResultSQLAction as $key => $DataObject) {

							if(is_numeric($key)) {

								$ResultSQLAction["Data"][$DataObject->parcserv_id]["SERVICO"] = $DataObject->serv_titulo;
								$ResultSQLAction["Data"][$DataObject->parcserv_id]["PARCEIRO"] = $DataObject->parc_razao_nome;
								$ResultSQLAction["Data"][$DataObject->parcserv_id]["SENHAS_UTILIZADAS"] = $DataObject->senhasutilizadas;
								$ResultSQLAction["Data"][$DataObject->parcserv_id]["SENHAS_TOTAIS"] = $DataObject->senhastotais;
								$ResultSQLAction["Data"][$DataObject->parcserv_id]["SENHAS_PERCENTUAL"] = $DataObject->senhaspercentual;

								if($DataObject->aten_status==3) {

									$ResultSQLAction["Data"][$DataObject->parcserv_id]["EM_ATENDIMENTO"] = $DataObject->total_por_status;
								}

								if(($DataObject->aten_status==1)||($DataObject->aten_status==2)) {

									$ResultSQLAction["Data"][$DataObject->parcserv_id]["AGUARDANDO_ATENDIMENTO"] = $DataObject->total_por_status;
								}

								if($DataObject->aten_status==4) {

									$ResultSQLAction["Data"][$DataObject->parcserv_id]["ATENDIDO"] = $DataObject->total_por_status;
								}
							}
						}

						if(count($ResultSQLAction["Data"])>0) {

							foreach ($ResultSQLAction["Data"] as $ParcServ_id => $DataArray) {

								$ResultSQLAction["TOTAIS"]["TOTAL_SENHAS_UTILIZADAS"] += $DataArray["SENHAS_UTILIZADAS"];
								$ResultSQLAction["TOTAIS"]["TOTAL_SENHAS_TOTAIS"] += $DataArray["SENHAS_TOTAIS"];
								$ResultSQLAction["TOTAIS"]["TOTAL_SENHAS_AGUARDANDO"] += $DataArray["AGUARDANDO_ATENDIMENTO"];
								$ResultSQLAction["TOTAIS"]["TOTAL_SENHAS_ATENDIMENTO"] += $DataArray["EM_ATENDIMENTO"];
								$ResultSQLAction["TOTAIS"]["TOTAL_SENHAS_ATENDIDAS"] += $DataArray["ATENDIDO"];
							}

							$ResultSQLAction["TOTAIS"]["PERC_SENHAS_DISTRIBUIDAS"] = @round(($ResultSQLAction["TOTAIS"]["TOTAL_SENHAS_UTILIZADAS"] / $ResultSQLAction["TOTAIS"]["TOTAL_SENHAS_TOTAIS"])*100);
							$ResultSQLAction["TOTAIS"]["PERC_SENHAS_ATENDIDAS"] = @round(($ResultSQLAction["TOTAIS"]["TOTAL_SENHAS_ATENDIDAS"] / $ResultSQLAction["TOTAIS"]["TOTAL_SENHAS_UTILIZADAS"])*100);
						}
					}
				}
				/*### TRATAMENTO DOS DADOS QUANDO FOR O GRÁFICO servicos-senhas ###*/





				/*### TRATAMENTO DOS DADOS QUANDO FOR O GRÁFICO servicos-categorias ###*/
				elseif($this->Request["Graph"]=="servicos-categorias") {

					if(count($ResultSQLAction[0])>0) {

						foreach ($ResultSQLAction as $key => $DataObject) {

							if(is_numeric($key)) {

								// $ResultSQLAction["Data"][$key]["CATEGORIA"] = $DataObject->servtipo_titulo;
								// $ResultSQLAction["Data"][$key]["SERVICO"] = $DataObject->serv_titulo;
								// $ResultSQLAction["Data"][$key]["PARCEIRO"] = $DataObject->parc_razao_nome;

								$Categorias["CATEGORIA"][$DataObject->servtipo_id] = $DataObject->servtipo_titulo;
								$Categorias["TOTAL_CATEGORIA"][$DataObject->servtipo_id] += $DataObject->qtd;

								// $ResultSQLAction["Data"]["CATEGORIA"][0] = "OUTRAS";
								// $ResultSQLAction["Data"]["TOTAL_CATEGORIA"][0] += $DataObject->qtd;

							}
						}

						arsort($Categorias["TOTAL_CATEGORIA"]);

						foreach ($Categorias["TOTAL_CATEGORIA"] as $ServTipo_id => $Total) {

							$NumCat++;

							if($NumCat<=8) {

								$ResultSQLAction["Data"]["CATEGORIA"][$ServTipo_id] = $Categorias["CATEGORIA"][$ServTipo_id];
								$ResultSQLAction["Data"]["TOTAL_CATEGORIA"][$ServTipo_id] = $Total;
							}
							else {

								$ResultSQLAction["Data"]["CATEGORIA"][0] = "OUTRAS";
								$ResultSQLAction["Data"]["TOTAL_CATEGORIA"][0] += $Total;
							}
						}

					}
				}
				/*### TRATAMENTO DOS DADOS QUANDO FOR O GRÁFICO servicos-categorias ###*/











				/*### TRATAMENTO DOS DADOS PADRÃO ###*/
				else {

					if(count($ResultSQLAction[0])>0) {

						$ResultSQLAction[0]->Even_RecCreatedon = $this->MaskValue->Data($ResultSQLAction[0]->Even_RecCreatedon,'US2BR_TIME');
						$ResultSQLAction[0]->Even_RecModifiedon = $this->MaskValue->Data($ResultSQLAction[0]->Even_RecModifiedon,'US2BR_TIME');
						$ResultSQLAction[0]->Even_Data = $this->MaskValue->Data($ResultSQLAction[0]->Even_Data,'US2BR');
					}
				}
				/*### TRATAMENTO DOS DADOS PADRÃO ###*/

				$ResultSQLAction["Graph"] = $this->Request["Graph"];

				$this->setResultSQLAction($ResultSQLAction);
			}
	}
	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/



}
/*### CLASSE DO PAINEL ONLINE (HERDA CRUD) ###*/
