<?php

/*#######################################################
|														|
| Arquivo com a classe do cadastro de Tabela Dinâmica   |
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

/*### CLASSE DO CADASTRO DE TABELA DINÂMICA (HERDA CRUD) ###*/
class TabelaDinamica extends \Database\Crud {

	protected $DynamicTable;

	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/
	public function __construct($container) {

		parent::__construct($container['Connect'], $container);

		//CHAMADA PARA CLASSE TABELADINAMICA
		$this->DynamicTable = $container['DynamicTable'];
	}
	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/


	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/
	public function MaskResultSQLAction () {

		if($this->Action=="selecionar")
			{
				$ResultSQLAction = $this->ResultSQLAction;

				if(count($ResultSQLAction[0])>0) {

					$ResultSQLAction[0]->tabedina_reccreatedon = $this->MaskValue->Data($ResultSQLAction[0]->tabedina_reccreatedon,'US2BR_TIME');
					$ResultSQLAction[0]->tabedina_recmodifiedon = $this->MaskValue->Data($ResultSQLAction[0]->tabedina_recmodifiedon,'US2BR_TIME');
					$ResultSQLAction[0]->tabedina_tabela_antigo = $ResultSQLAction[0]->tabedina_tabela;

					$SQLValues = "SELECT * FROM TabelasDinamicasValores WHERE TabeDinaValo_Tabela = '".$ResultSQLAction[0]->tabedina_tabela."' AND TabeDinaValo_Delete = 0 ORDER BY TabeDinaValo_Codigo ASC, TabeDinaValo_Descricao ASC";

					$ExecuteSQLValues = $this->db->query($SQLValues);

					$ResultSQLValues = $ExecuteSQLValues->fetchAll(\PDO::FETCH_OBJ);

					if(count($ResultSQLValues)>0) {

						foreach ($ResultSQLValues as $key => $DataObject) {

							$Values[$DataObject->tabedinavalo_id]["id"] = $DataObject->tabedinavalo_id;
							$Values[$DataObject->tabedinavalo_id]["codigo"] = $DataObject->tabedinavalo_codigo;
							$Values[$DataObject->tabedinavalo_id]["descricao"] = $DataObject->tabedinavalo_descricao;
						}
					}

					$ResultSQLAction[0]->itens = $Values;
				}

				$this->setResultSQLAction($ResultSQLAction);
			}
	}
	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/







	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/
	public function BeforeExecuteAction () {


		if(($this->Action=="inserir")||($this->Action=="alterar"))
			{
				if($this->Request["TabeDina_Tabela"]<>$this->Request["TabeDina_Tabela_Antigo"])
					{
						$this->BeforeExecuteAction = false;

						$ExecuteChecaTabela = $this->db->query("SELECT COUNT(*) AS REGISTROS FROM ".$this->ModuleDefs->Table." WHERE ".$this->ModuleDefs->Prefix."Tabela = '".$this->Request["TabeDina_Tabela"]."' AND ".$this->ModuleDefs->Prefix."Delete = 0");
						$ResultChecaTabela = $ExecuteChecaTabela->fetchAll(\PDO::FETCH_OBJ);
						if($ResultChecaTabela[0]->registros > 0) {

							$this->ErrorBeforeExecuteAction = "<p>O identificador de tabela digitado já existe na base de dados.</p>";
						} else {

							$this->BeforeExecuteAction = true;
						}
					}

			}

	}
	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/






	/*### EXECUTA AÇÕES DEPOIS DO MÉTODO ExecuteAction ###*/
	public function AfterExecuteAction () {

		if(($this->Action=="alterar")||($this->Action=="excluir")) {

			/*### APAGA TODOS OS ITENS ###*/
			$SQLDeleteItem = "UPDATE TabelasDinamicasValores SET TabeDinaValo_Delete = 1,
																	TabeDinaValo_RecCreatedon = '".$this->Date["NowUS"]."',
																	TabeDinaValo_RecModifiedby = ".$this->TokenClass->getClaim("UserData")->Usua_id."
																WHERE
																	TabeDinaValo_Tabela  = (SELECT TabeDina_Tabela FROM TabelasDinamicas WHERE TabeDina_id = ".$this->PrimaryKey.")
																	AND TabeDinaValo_Delete = 0";
			// echo $SQLDeleteItem;
			// exit;
			$ExecuteSQLDeleteItem = $this->db->query($SQLDeleteItem);
			/*### APAGA TODOS OS ITENS ###*/
		}


		if(($this->Action=="inserir")||($this->Action=="alterar")) {


			if(count($this->Request["opt-item-codigo"])>0)
				{
					foreach ($this->Request["opt-item-codigo"] as $itemSeq => $itemCodigo)
						{

							$SQLInsertItemValues .= "('".$this->Request["TabeDina_Tabela"]."',
													'".$this->Request["opt-item-codigo"][$itemSeq]."',
													'".$this->Request["opt-item-descricao"][$itemSeq]."',
													".$this->TokenClass->getClaim("UserData")->Usua_id.",
													'".$this->Date["NowUS"]."'), ";
						}

					$ExecuteSQLInsertItens = $this->db->query("INSERT INTO TabelasDinamicasValores (TabeDinaValo_Tabela,
																										TabeDinaValo_Codigo,
																										TabeDinaValo_Descricao,
																										TabeDinaValo_RecCreatedby,
																										TabeDinaValo_RecCreatedon)
																									VALUES
																										".substr($SQLInsertItemValues,0,-2));

				}


		}

	}
	/*### EXECUTA AÇÕES DEPOIS DO MÉTODO ExecuteAction ###*/


}
/*### CLASSE DO CADASTRO DE TABELA DINÂMICA (HERDA CRUD) ###*/
