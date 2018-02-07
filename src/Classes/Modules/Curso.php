<?php

/*#######################################################
|														|
| Arquivo com a classe do cadastro de Cursos			|
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

/*### CLASSE DO CADASTRO DE CURSO (HERDA CRUD) ###*/
class Curso extends \Database\Crud {

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

				if(count($ResultSQLAction)>0) {

					foreach ($ResultSQLAction as $key => $DataObject) {

						if(is_numeric($key)) {

							$ResultSQLAction[$key]->curs_reccreatedon = $this->MaskValue->Data($DataObject->curs_reccreatedon,'US2BR_TIME');
							$ResultSQLAction[$key]->curs_recmodifiedon = $this->MaskValue->Data($DataObject->curs_recmodifiedon,'US2BR_TIME');
							$ResultSQLAction[$key]->curs_valor_matricula = $this->MaskValue->Moeda($DataObject->curs_valor_matricula,'add');
							$ResultSQLAction[$key]->curs_valor_mensalidade = $this->MaskValue->Moeda($DataObject->curs_valor_mensalidade,'add');
						}

					}
				}

				if($this->Request["origem"]=="matricula") {

					$ResponseOptions = $this->ResponseOptions;
					$ResponseOptions["ShowAlertMsg"] = false;
					$this->ResponseOptions = $ResponseOptions;
				}

				// if(count($ResultSQLAction)>1) $ResultSQLAction
				$this->setResultSQLAction($ResultSQLAction);
			}
	}
	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/





	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/
	public function BeforeExecuteAction () {

		$this->BeforeExecuteAction = true;

		if($this->Action=="excluir") {

			$SQLChecaMatricula = "SELECT
										matr_id
									FROM
										matriculas
									WHERE
										cursos_curs_id = ".$this->Request["id_reg"]."
										AND matr_delete = 0";
			$ExecuteSQLChecaMatricula = $this->db->query($SQLChecaMatricula);
			$ResultSQLChecaMatricula = $ExecuteSQLChecaMatricula->fetchAll(\PDO::FETCH_OBJ);
			if($ResultSQLChecaMatricula[0]->matr_id > 0) {

				$this->BeforeExecuteAction = false;
				$this->ErrorBeforeExecuteAction = "<p>Não é possível excluir um curso que possui matrículas</p>";
			} else {

				$this->BeforeExecuteAction = true;
			}
		}
	}
	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/



	/*### EXECUTA AS AÇÕES DE MÁSCARA DE VALORES NOS DADOS DE UPDATE OU INSERT ###*/
	public function MaskInsertUpdateValues () {

		$Request = $this->Request;

		if(($this->Action=="inserir")||($this->Action=="alterar"))
			{
				$Request["curs_valor_matricula"] = ($Request["curs_valor_matricula"]=="") ? 0 : $this->MaskValue->Moeda($Request["curs_valor_matricula"],'remove');
				$Request["curs_valor_mensalidade"] = ($Request["curs_valor_mensalidade"]=="") ? 0 : $this->MaskValue->Moeda($Request["curs_valor_mensalidade"],'remove');

				$this->setRequest($Request);
			}

	}
	/*### EXECUTA AS AÇÕES DE MÁSCARA DE VALORES NOS DADOS DE UPDATE OU INSERT ###*/




}
/*### CLASSE DO CADASTRO DE CURSO (HERDA CRUD) ###*/
