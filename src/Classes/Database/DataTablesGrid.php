<?php

/*#######################################################
|														|
| Arquivo com a classe que gera o GRID de dados 		|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Database;

/*### CLASSE DO GRID DE DADOS ###*/
class DataTablesGrid extends Crud {

	protected $db;
	protected $MaskValue;
	protected $Request;
	protected $ModuleDefs;
	protected $ColumnDefs;
	protected $ColumnOrder;
	protected $ResponseDraw;
	protected $ResponseColumns;
	protected $ResponseOrder;
	protected $ResponseStart;
	protected $ResponseLength;
	protected $ResponseSearch;
	protected $SQLRecordsTotal;
	protected $SQLTotalRecordsFiltered;
	protected $SQLRecordsFiltered;
	protected $DynamicTable;

	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/
	public function __construct($container) {

		parent::__construct($container['Connect'], $container);

		//CHAMADA PARA CLASSE TABELADINAMICA
		$this->DynamicTable = $container['DynamicTable'];
	}
	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/


	/*### ARMAZENA TODA A VARIÁVEL REQUEST ###*/
	public function setRequest ($Request) {

		$this->Request = $Request;
		return $this->Request;
	}
	/*### ARMAZENA TODA A VARIÁVEL REQUEST ###*/


	/*### ARMAZENA AS DEFINIÇÕES DO MÓDULO ###*/
	public function setModuleDefs ($ModuleDefs) {

		$this->ModuleDefs = $ModuleDefs;
		return $this->ModuleDefs;
	}
	/*### ARMAZENA AS DEFINIÇÕES DO MÓDULO ###*/


	/*### ARMAZENA AS CONFIGURAÇÕES GERAIS DO GRID ###*/
	public function ConfigGrid () {

		//ARMAZENA AS CONFIGURAÇÕES DAS COLUNAS
		$this->ColumnDefs = $this->Request["ColumnDefs"];

		//ARMAZENA A ORDEM DE CLASSIFICAÇÃO
		$this->ColumnOrder = $this->Request["ColumnOrder"];
	}
	/*### ARMAZENA AS CONFIGURAÇÕES GERAIS DO GRID ###*/


	/*### ARMAZENA AS VARIÁVEIS DE RESPOSTA DO GRID ###*/
	public function setResponse () {

		$this->ResponseDraw = $this->Request["draw"];
		$this->ResponseColumns = $this->Request["columns"];
		$this->ResponseOrder = $this->Request["order"];
		$this->ResponseStart = $this->Request["start"];
		$this->ResponseLength = $this->Request["length"];
		$this->ResponseSearch = $this->AntiInjection->Prepare($this->Request["search"]["value"]);
	}
	/*### ARMAZENA AS VARIÁVEIS DE RESPOSTA DO GRID ###*/


	/*### MONTA O SQL DO GRID ###*/
	public function BuildSqlGrid() {

		//INÍCIO DO SQL DE TODOS OS REGISTROS
		$SQLRecordsTotal = "SELECT COUNT(*) AS RecordsTotal";

		//INÍCIO DO SQL DOS REGISTROS FILTRADOS (TOTALIZADOR)
		$SQLTotalRecordsFiltered = "SELECT COUNT(*) AS TotalRecordsFiltered";

		//INÍCIO DO SQL DOS REGISTROS FILTRADOS
		$SQLRecordsFiltered = "SELECT ";

		/*### MONTA O SQL COM OS CAMPOS DO COLUMNSDEFS ###*/
		foreach($this->ColumnDefs as $key => $Column)
			{
				/*### SE FOR CAMPO DE CHAVE PRIMÁRIA, RETORNA O ALIAS DE DT_ROW_ID ###*/
				if($Column['primary_key']<>'') {

					$FieldName[$key] = $this->ModuleDefs["Table"].".".$this->ModuleDefs["Prefix"].$Column['primary_key'];
					$Fields .= $this->ModuleDefs["Table"].".".$this->ModuleDefs["Prefix"].$Column['primary_key']." AS \"DT_RowId\", ";
				}
				/*### SE FOR CAMPO DE CHAVE PRIMÁRIA, RETORNA O ALIAS DE DT_ROW_ID ###*/


				/*### SE FOR CAMPO TIPO CASE, ARMAZENA O CASE NO SELECT ###*/
				elseif($Column['Case']<>'') {

					//ARMAZENA O NOME DO CAMPO PARA CONSULTA POSTERIOR DE FILTRO
					$FieldName[$key] = "(CASE ".$this->AntiInjection->Prepare($Column['Case'])." END)";

					//ADICIONA NA LINHA DO SELECT O CASE
					$Fields .= "(CASE ".$this->AntiInjection->Prepare($Column['Case'])." END) AS ".$Column['data'].", ";
				}
				/*### SE FOR CAMPO TIPO CASE, ARMAZENA O CASE NO SELECT ###*/



				/*### SENÃO, MONTA O CAMPO CONFORME NAME E DATA, OU OTHERTABLE ###*/
				else if($Column['name']<>"actions") {


					/*### SE FOR OTHERTABLE, MONTA CONFORME CONFIGURAÇÃO ###*/
					if($Column['otherTable']) {

						/*### SE O OTHERTABLE FOR UMA TABELA DINÂMICA FAZ O RELACIONAMENTO COM A TABELA DINÂMICA INFORMADA ###*/
						if($Column['otherTable']['DynamicTable']) {

							$Alias = ($Column['otherTable']['Alias']<>"") ? $Column['otherTable']['Alias'] : $Column['otherTable']['DynamicTable'];

							//ARMAZENA O NOME DO CAMPO PARA CONSULTA POSTERIOR DE FILTRO
							$FieldName[$key] = $Alias.".TabeDinaValo_".$Column['otherTable']['View'];

							//ADICIONA NA LINHA DO SELECT A REFERÊNCIA DO CAMPO E O ALIAS
							$Fields .= $Alias.".TabeDinaValo_".$Column['otherTable']['View']." AS ".$Column['data'].", ";

							//ADICIONA A INFORMAÇÃO DE JOIN
							$AddJoin .= " ".$Column['otherTable']['Join']." JOIN TabelasDinamicasValores ".$Alias." ON ".$Alias.".TabeDinaValo_".$Column['otherTable']['FieldKeyOtherTable']." = CAST(".$this->ModuleDefs["Table"].".".$this->ModuleDefs["Prefix"].$Column['otherTable']['FieldKey']." AS VARCHAR) AND ".$Alias.".TabeDinaValo_Tabela = '".$Column['otherTable']['DynamicTable']."' AND ".$Alias.".TabeDinaValo_Delete = 0";

						}
						/*### SE O OTHERTABLE FOR UMA TABELA DINÂMICA FAZ O RELACIONAMENTO COM A TABELA DINÂMICA INFORMADA ###*/



						/*### SENÃO, FAZ O RELACIONAMENTO CONFORME DADOS INFORMADOS DA OUTRA TABELA ###*/
						else {

							$Alias = ($Column['otherTable']['Alias']<>"") ? $Column['otherTable']['Alias']."." : "";

							//ARMAZENA O NOME DO CAMPO PARA CONSULTA POSTERIOR DE FILTRO
							$FieldName[$key] = $Alias.$Column['otherTable']['View'];

							//ADICIONA NA LINHA DO SELECT A REFERÊNCIA DO CAMPO E O ALIAS
							$Fields .= $Alias.$Column['otherTable']['View']." AS ".$Column['data'].", ";


							if($Column['otherTable']['OtherJoin']<>"") {

								//ADICIONA A INFORMAÇÃO DE JOIN DIFERENCIADA
								$AddJoin .= " ".$Column['otherTable']['OtherJoin'];
							}
							elseif($Column['otherTable']['Join']<>"") {

								//ADICIONA A INFORMAÇÃO DE JOIN PADRÃO
								$AddJoin .= " ".$Column['otherTable']['Join']." JOIN ".$Column['otherTable']['Table']." ".$Column['otherTable']['Alias']." ON ".$Column['otherTable']['Alias'].".".$Column['otherTable']['FieldKeyOtherTable']." = ".$this->ModuleDefs["Table"].".".$Column['otherTable']['FieldKey']." AND ".$Column['otherTable']['Alias'].".".$Column['otherTable']['Prefix']."Delete = 0";
							}
						}
						/*### SENÃO, FAZ O RELACIONAMENTO CONFORME DADOS INFORMADOS DA OUTRA TABELA ###*/


					}
					/*### SE FOR OTHERTABLE, MONTA CONFORME CONFIGURAÇÃO ###*/


					/*### SENÃO, RETORNA NOME DO CAMPO COM PREFIXO E ALIAS PADRÃO ###*/
					elseif ($Column['checkbox']<>true) {

						//SE A OPÇÃO noPrefix FOR TRUE NO COLUMNDEFS, REMOVE O PREFIXO
						$Prefix = ($Column['noPrefix']==true) ? "" : $this->ModuleDefs["Prefix"];

						//ARMAZENA O NOME DO CAMPO PARA CONSULTA POSTERIOR DE FILTRO
						$FieldName[$key] = $this->ModuleDefs["Table"].".".$Prefix.$Column['data'];

						//ADICIONA NA LINHA DO SELECT A REFERÊNCIA DO CAMPO E O ALIAS
						$Fields .= $this->ModuleDefs["Table"].".".$Prefix.$Column['data']." AS ".$Column['data'].", ";
					}
					/*### SENÃO, RETORNA NOME DO CAMPO COM PREFIXO E ALIAS PADRÃO ###*/

				}
				/*### SENÃO, MONTA O CAMPO CONFORME NAME E DATA, OU OTHERTABLE ###*/



				/*### MONTA O AdditionalConditions (WHERE) QUANDO HOUVER ###*/
				if($Column['AdditionalConditions']<>'') {

					$AdditionalConditions .= " AND (".$FieldName[$key]." ".$Column['AdditionalConditions'].") ";
				}
				/*### MONTA O AdditionalConditions (WHERE) QUANDO HOUVER ###*/

			}
		/*### MONTA O SQL COM OS CAMPOS DO COLUMNSDEFS ###*/


		//ADICIONA OS CAMPOS DO SELECT
		$SQLRecordsFiltered .= substr($Fields,0,-2);


		//ADICIONA O FROM DA TABELA DE ORIGEM (TODOS OS REGISTROS)
		$SQLRecordsTotal .= " FROM ".$this->ModuleDefs["Table"]." ".$AddJoin;

		//ADICIONA O FROM DA TABELA DE ORIGEM (REGISTROS FILTRADOS)
		$SQLRecordsFiltered .= " FROM ".$this->ModuleDefs["Table"]." ".$AddJoin;

		//ADICIONA O FROM DA TABELA DE ORIGEM (TOTALIZADOR REGISTROS FILTRADOS)
		$SQLTotalRecordsFiltered .= " FROM ".$this->ModuleDefs["Table"]." ".$AddJoin;



		/*### MONTA AS CONDIÇÕES DE BUSCA DE DADOS DAS COLUNAS OU FILTROS ###*/
		foreach ($this->ResponseColumns as $key => $dados_colunas) {
			if(($dados_colunas["search"]["value"]<>"")&&($dados_colunas["searchable"]=="true"))
				{
					/*### APLICA A MÁSCARA DE DADOS, SE HOUVER ###*/
					if($this->ColumnDefs[$key]["mask"]["Database"]<>"")
						{
							$MaskName = $this->ColumnDefs[$key]["mask"]["name"];
							$dados_colunas["search"]["value"] = $this->MaskValue->$MaskName($this->AntiInjection->Prepare($dados_colunas["search"]["value"]),$this->ColumnDefs[$key]["mask"]["Database"]);
						}
					else
						{
							$dados_colunas["search"]["value"] = $this->AntiInjection->Prepare($dados_colunas["search"]["value"]);
						}
					/*### APLICA A MÁSCARA DE DADOS, SE HOUVER ###*/

					/*### MONTA AS CONDIÇÕES DE BUSCA, VALIDANDO O TIPO DE DADO DA STRING DE BUSCA ###*/
					if(((is_numeric($dados_colunas["search"]["value"]))&&($this->ColumnDefs[$key]["DataType"]=="int"))||($this->ColumnDefs[$key]["DataType"]<>"int")) {

						if($this->ColumnDefs[$key]["DT_Filter"]["searchOperator"]=="LIKE") $SearchColumns .= " AND ".$FieldName[$key]." LIKE '%".$dados_colunas["search"]["value"]."%'";
						elseif($this->ColumnDefs[$key]["DT_Filter"]["searchOperator"]=="DateTimeWithoutTime") $SearchColumns .= " AND ".$FieldName[$key]." BETWEEN '".$dados_colunas["search"]["value"]." 00:00:00' AND '".$dados_colunas["search"]["value"]." 23:59:59'";
						else $SearchColumns .= " AND ".$FieldName[$key]." = '".$dados_colunas["search"]["value"]."'";
					}
					/*### MONTA AS CONDIÇÕES DE BUSCA, VALIDANDO O TIPO DE DADO DA STRING DE BUSCA ###*/


					// echo "<pre>".$dados_colunas["search"]["value"].": "; echo var_dump(is_numeric($dados_colunas["search"]["value"])); echo "</pre>";
				}
			}
		/*### MONTA AS CONDIÇÕES DE BUSCA DE DADOS DAS COLUNAS OU FILTROS ###*/


		/*### MONTA AS CONDIÇÕES DE BUSCA DE DADOS GERAL ###*/
		if($this->ResponseSearch<>"")
			{
				// echo "<pre>".$this->ResponseSearch.": "; echo var_dump(is_numeric($this->ResponseSearch)); echo "</pre>";

				$SearchColumns = " AND (";
				foreach ($this->ResponseColumns as $key => $dados_colunas) {
					if($dados_colunas["searchable"]=="true")
						{
							/*### APLICA A MÁSCARA DE DADOS, SE HOUVER ###*/
							if(($this->ColumnDefs[$key]["mask"]["Database"]<>"")&&($this->ColumnDefs[$key]["mask"]["name"]<>"Data"))
								{
									$MaskName = $this->ColumnDefs[$key]["mask"]["name"];
									$this->ResponseSearch = $this->MaskValue->$MaskName($this->AntiInjection->Prepare($this->ResponseSearch),$this->ColumnDefs[$key]["mask"]["Database"]);
								}
							else
								{
									$this->ResponseSearch = $this->AntiInjection->Prepare($this->ResponseSearch);
								}
							/*### APLICA A MÁSCARA DE DADOS, SE HOUVER ###*/


							/*### MONTA AS CONDIÇÕES DE BUSCA, VALIDANDO O TIPO DE DADO DA STRING DE BUSCA ###*/
							if(((is_numeric($this->ResponseSearch))&&($this->ColumnDefs[$key]["DataType"]=="int"))||($this->ColumnDefs[$key]["DataType"]<>"int")) {

								if($this->ColumnDefs[$key]["DT_Filter"]["searchOperator"]=="LIKE") $FieldsSearchColumns .= $FieldName[$key]." LIKE '%".$this->AntiInjection->Prepare($this->ResponseSearch)."%' OR ";
								elseif($this->ColumnDefs[$key]["DT_Filter"]["searchOperator"]=="DateTimeWithoutTime") $FieldsSearchColumns .= $FieldName[$key]." BETWEEN '".$this->AntiInjection->Prepare($this->ResponseSearch)." 00:00:00' AND '".$this->AntiInjection->Prepare($this->ResponseSearch)." 23:59:59' OR ";
								else $FieldsSearchColumns .= $FieldName[$key]." = '".$this->AntiInjection->Prepare($this->ResponseSearch)."' OR ";
							}
							/*### MONTA AS CONDIÇÕES DE BUSCA, VALIDANDO O TIPO DE DADO DA STRING DE BUSCA ###*/
						}
					}
				$SearchColumns .= substr($FieldsSearchColumns,0,-4).") ";
			}
		/*### MONTA AS CONDIÇÕES DE BUSCA DE DADOS GERAL ###*/




		//ADICIONA AS CONDIÇÕES WHERE (TODOS OS REGISTROS)
		$SQLRecordsTotal .= " WHERE ".$this->ModuleDefs["Table"].".".$this->ModuleDefs["Prefix"]."Delete = 0".$AdditionalConditions;

		//ADICIONA AS CONDIÇÕES WHERE (REGISTROS FILTRADOS)
		$SQLRecordsFiltered .= " WHERE ".$this->ModuleDefs["Table"].".".$this->ModuleDefs["Prefix"]."Delete = 0".$AdditionalConditions;

		//ADICIONA AS CONDIÇÕES DE BUSCA DE DADOS DAS COLUNAS OU FILTROS (SQL REGISTROS FILTRADOS)
		$SQLRecordsFiltered .= $SearchColumns;

		//ADICIONA AS CONDIÇÕES WHERE (TOTALIZADOR REGISTROS FILTRADOS)
		$SQLTotalRecordsFiltered .= " WHERE ".$this->ModuleDefs["Table"].".".$this->ModuleDefs["Prefix"]."Delete = 0".$AdditionalConditions;

		//ADICIONA AS CONDIÇÕES DE BUSCA DE DADOS DAS COLUNAS OU FILTROS (TOTALIZADOR REGISTROS FILTRADOS)
		$SQLTotalRecordsFiltered .= $SearchColumns;


		/*### MONTA O SQL COM OS CAMPOS DO ORDER BY ###*/
		foreach($this->ResponseOrder as $key => $ColumnOrder)
			{
				$Order .= $FieldName[$ColumnOrder['column']]." ".$ColumnOrder['dir'].", ";
			}
		/*### MONTA O SQL COM OS CAMPOS DO ORDER BY ###*/


		//ADICIONA O SQL DE ORDENAÇÃO (SQL REGISTROS FILTRADOS)
		$SQLRecordsFiltered .= " ORDER BY ".substr($Order,0,-2);

		//ADICIONA O LIMIT DA PAGINAÇÃO
		$SQLRecordsFiltered .= ($this->ResponseLength>0) ? " LIMIT ".$this->ResponseLength." OFFSET ".$this->ResponseStart : "";

		//ARMAZENA A SQL DE TODOS OS REGISTROS
		$this->SQLRecordsTotal = $SQLRecordsTotal;

		//ARMAZENA A SQL DO TOTALIZADOR DOS REGISTROS FILTRADOS
		$this->SQLTotalRecordsFiltered = $SQLTotalRecordsFiltered;

		//ARMAZENA A SQL DOS REGISTROS FILTRADOS
		$this->SQLRecordsFiltered = $SQLRecordsFiltered;

	}
	/*### MONTA O SQL DO GRID ###*/



	/*### EXECUTA E RETORNA O OBJETO DE DADOS DO GRID ###*/
	public function ReturnAction() {

		//DEFINE A Action COMO Grid
		$this->setAction("Grid");
		// echo $this->Action;

		//DEFINE A SQLAction COM A CONSULTA COM TODOS OS REGISTROS
		$this->setSQLAction($this->SQLRecordsTotal);

		//CRIA OBJETO COM O RESULTADO DA CONSULTA DE TODOS OS REGISTROS
		$ResultRecordsTotal = $this->ExecuteAction();

		//DEFINE A SQLAction COM A CONSULTA COM O TOTALIZADOR DOS REGISTROS FILTRADOS
		$this->setSQLAction($this->SQLTotalRecordsFiltered);

		//CRIA OBJETO COM O RESULTADO DA CONSULTA DO TOTALIZADOR DOS REGISTROS FILTRADOS
		$ResultTotalRecordsFiltered = $this->ExecuteAction();

		//DEFINE A SQLAction COM A CONSULTA COM OS REGISTROS FILTRADOS
		$this->setSQLAction($this->SQLRecordsFiltered);


		//CRIA OBJETO COM O RESULTADO DA CONSULTA DOS REGISTROS FILTRADOS
		$ResultRecordsFiltered = $this->ExecuteAction();


		/*### MONTA O OBJETO COM OS DADOS DO RESULTADO DA CONSULTA DOS REGISTROS FILTRADOS ###*/
		if(count($ResultRecordsFiltered)>0) {

			/*### VERIFICA OS CAMPOS QUE POSSUEM MÁSCARA DE DADOS NO COLUMNDEFS E ARMAZENA EM VARIÁVEL ###*/
			foreach($this->ColumnDefs as $key => $Column)
				{
					/*### SE EXISTIR MÁSCARA, APLICA, CONFORME CONFIG DE NAME E VIEW ###*/
					if(count($Column["mask"])>0) {
						$Mask[$Column["data"]]["name"] = $Column["mask"]["name"];
						$Mask[$Column["data"]]["View"] = $Column["mask"]["View"];
					}
					/*### SE EXISTIR MÁSCARA, APLICA, CONFORME CONFIG DE NAME E VIEW ###*/
				}
			/*### VERIFICA OS CAMPOS QUE POSSUEM MÁSCARA DE DADOS NO COLUMNDEFS E ARMAZENA EM VARIÁVEL ###*/


			/*### SE HOUVER MÁSCARA DE DADOS, APLICA EM CADA COLUNA, SENÃO RETORNA OBJETO FETCH_OBJ ###*/
			if(count($Mask)>0)
				{
					foreach($ResultRecordsFiltered as $key => $array_dados)
						{
							foreach ($array_dados as $coluna => $valor)
								{
									/*### SE EXISTIR MÁSCARA, APLICA, CONFORME CONFIG DE NAME E VIEW ###*/
									if($Mask[$coluna]<>"")
										{
											$MaskName = $Mask[$coluna]["name"];
											$response["data"][$key][$coluna] = $this->MaskValue->$MaskName($valor,$Mask[$coluna]["View"]);
										}
									/*### SE EXISTIR MÁSCARA, APLICA, CONFORME CONFIG DE NAME E VIEW ###*/

									/*### SENÃO O VALOR SERÁ RETORNADO SEM FORMATAÇÃO ###*/
									else
										{
											$response["data"][$key][$coluna] = $valor;
										}
								}
						}
				}
			else
				{
					$response["data"] = $ResultRecordsFiltered;
				}
			/*### SE HOUVER MÁSCARA DE DADOS, APLICA EM CADA COLUNA, SENÃO RETORNA OBJETO FETCH_OBJ ###*/

		} else {

			$response["data"] = [];
		}
		/*### MONTA O OBJETO COM OS DADOS DO RESULTADO DA CONSULTA DOS REGISTROS FILTRADOS ###*/


		/*### ADICIONA OS DADOS OBRIGATÓRIOS AO OBJETO DO GRID DATATABLES ###*/
		$response["draw"] = $this->ResponseDraw;
		$response["recordsTotal"] = $ResultRecordsTotal[0]->recordstotal;
		$response["recordsFiltered"] = $ResultTotalRecordsFiltered[0]->totalrecordsfiltered;
		$response["SQLRecordsFiltered"] = $this->SQLRecordsFiltered;
		/*### ADICIONA OS DADOS OBRIGATÓRIOS AO OBJETO DO GRID DATATABLES ###*/


		//RETORNA O GRID EM JSON
		echo json_encode($response);

	}
	/*### EXECUTA E RETORNA O OBJETO DE DADOS DO GRID ###*/

}
/*### CLASSE DO GRID DE DADOS ###*/
