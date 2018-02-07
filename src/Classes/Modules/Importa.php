<?php


/*#######################################################
|														|
| Arquivo com a classe de importação de dados 			|
|														|
| Esta classe herda as variáveis e métodos da classe	|
| Crud. A documentação dos métodos está na classe pai 	|
|														|
| Data de criação: 06/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Modules;

set_time_limit(0);

/*### CLASSE DE IMPORTAÇÃO DE DADOS (HERDA CRUD) ###*/
class Importa extends \Database\Crud {

	protected $DynamicTable;
	protected $Container;
	protected $ModuleDefsLayouts;

	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/
	public function __construct($container) {

		parent::__construct($container['Connect'], $container);

		//CHAMADA PARA CLASSE TABELADINAMICA
		$this->DynamicTable = $container['DynamicTable'];

		//ARMAZENAR O CONTAINER
		$this->Container = $container;


		$ModuleDefsLayouts[1] = (object) array (
			"Name" => "Aluno", //IDENTIFICADOR INTERNO DO MÓDULO (REFERÊNCIA PARA PERMISSÃO DE ACESSO)
			"Prefix" => "alun_", //PREFIXO DOS CAMPOS DO MÓDULO (USADOS NA TABELA DO BANCO DE DADOS)
			"Entity" => "Aluno", //ENTIDADE DO MÓDULO (REFERÊNCIA PARA CLASSE)
			"Table" => "Alunos",//NOME DA TABELA NO BANCO DE DADOS
		);

		$ModuleDefsLayouts[2] = (object) array (
			"Name" => "Curso", //IDENTIFICADOR INTERNO DO MÓDULO (REFERÊNCIA PARA PERMISSÃO DE ACESSO)
			"Prefix" => "curs_", //PREFIXO DOS CAMPOS DO MÓDULO (USADOS NA TABELA DO BANCO DE DADOS)
			"Entity" => "Curso", //ENTIDADE DO MÓDULO (REFERÊNCIA PARA CLASSE)
			"Table" => "Cursos" //NOME DA TABELA NO BANCO DE DADOS
		);

		$ModuleDefsLayouts[3] = (object) array (
			"Name" => "Matricula", //IDENTIFICADOR INTERNO DO MÓDULO (REFERÊNCIA PARA PERMISSÃO DE ACESSO)
			"Prefix" => "matr_", //PREFIXO DOS CAMPOS DO MÓDULO (USADOS NA TABELA DO BANCO DE DADOS)
			"Entity" => "Matricula", //ENTIDADE DO MÓDULO (REFERÊNCIA PARA CLASSE)
			"Table" => "Matriculas" //NOME DA TABELA NO BANCO DE DADOS
		);

		$this->ModuleDefsLayouts = $ModuleDefsLayouts;

	}
	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/









	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/
	public function BeforeExecuteAction () {

		$ModuleDefsLayouts = $this->ModuleDefsLayouts;

		$this->BeforeExecuteAction = true;

		$File = $_FILES["arquivo"]["tmp_name"];

		$CheckLayoutControl[1] = 6;
		$CheckLayoutControl[2] = 5;
		$CheckLayoutControl[3] = 4;

		if (($handle = fopen($File, "r")) !== FALSE) {

		    while (($data = fgetcsv($handle, 1000, $this->Request["separador"])) !== FALSE) {

		    	$LineNumber++;

		    	if($LineNumber==1) {

		    		foreach ($data as $KeyHeader => $HeaderName) {


				    	/*### VALIDAÇÃO DO LAYOUT 1 - ALUNOS ###*/
						if($this->Request["layout"]==1) {

			    			if(($KeyHeader==0)&&($HeaderName=="id")) $CheckLayout++;
			    			if(($KeyHeader==1)&&($HeaderName=="name")) $CheckLayout++;
			    			if(($KeyHeader==2)&&($HeaderName=="cpf")) $CheckLayout++;
			    			if(($KeyHeader==3)&&($HeaderName=="rg")) $CheckLayout++;
			    			if(($KeyHeader==4)&&($HeaderName=="phone")) $CheckLayout++;
			    			if(($KeyHeader==5)&&($HeaderName=="birthday")) $CheckLayout++;
						}
				    	/*### VALIDAÇÃO DO LAYOUT 1 - ALUNOS ###*/



				    	/*### VALIDAÇÃO DO LAYOUT 2 - CURSOS ###*/
						elseif($this->Request["layout"]==2) {

			    			if(($KeyHeader==0)&&($HeaderName=="id")) $CheckLayout++;
			    			if(($KeyHeader==1)&&($HeaderName=="course_name")) $CheckLayout++;
			    			if(($KeyHeader==2)&&($HeaderName=="monthly_amount")) $CheckLayout++;
			    			if(($KeyHeader==3)&&($HeaderName=="registration_tax")) $CheckLayout++;
			    			if(($KeyHeader==4)&&($HeaderName=="period")) $CheckLayout++;
						}
				    	/*### VALIDAÇÃO DO LAYOUT 2 - CURSOS ###*/




				    	/*### VALIDAÇÃO DO LAYOUT 3 - MATRÍCULAS ###*/
						elseif($this->Request["layout"]==3) {

			    			if(($KeyHeader==0)&&($HeaderName=="id")) $CheckLayout++;
			    			if(($KeyHeader==1)&&($HeaderName=="student_id")) $CheckLayout++;
			    			if(($KeyHeader==2)&&($HeaderName=="course_id")) $CheckLayout++;
			    			if(($KeyHeader==3)&&($HeaderName=="year")) $CheckLayout++;
						}
				    	/*### VALIDAÇÃO DO LAYOUT 3 - MATRÍCULAS ###*/


		    		}

		    		if($CheckLayoutControl[$this->Request["layout"]]<>$CheckLayout) {

		    			$this->BeforeExecuteAction = false;
		    			$this->ErrorBeforeExecuteAction = "O layout do arquivo não está correto, a importação não será efetuada";
		    		}

	    		}
	    	}
	    }

	}
	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/





	/*### EXECUTA A QUERY DA SQLAction ###*/
	public function ExecuteAction() {

		$ModuleDefsLayouts = $this->ModuleDefsLayouts;

		/*### SOMENTE EXECUTA SE O BEFOREACTION FOR TRUE ###*/
		if($this->BeforeExecuteAction==true)
			{

				$File = $_FILES["arquivo"]["tmp_name"];

				if (($handle = fopen($File, "r")) !== FALSE) {

				    while (($data = fgetcsv($handle, 1000, $this->Request["separador"])) !== FALSE) {

				    	$LineNumber++;

				    	if($LineNumber==1) $Header = $data;
				    	else {
				    		$DataLine++;
				    		foreach ($data as $key => $value) {

				    			/*### TRATAMENTO DE DADOS PARA LAYOUT 1 - ALUNOS ###*/
				    			if($this->Request["layout"]==1) {

				    				// echo "<br>----";
				    				// echo "<br>".$key;
				    				// echo "<br>".$DataLine;
				    				// echo "<br>".$Header[$key];
				    				// echo "<br>".$value;

				    				if($Header[$key]=="cpf") {

				    					$cpf = $this->MaskValue->Cpf($value,'remove');
				    					$cpf = str_pad($cpf,11,0,STR_PAD_LEFT);

										$ActionForm = $this->Container[$ModuleDefsLayouts[$this->Request["layout"]]->Entity];
										$ActionForm->setModuleDefs(json_encode($ModuleDefsLayouts[$this->Request["layout"]]));
										$ActionForm->setAction('selecionar');
										$ActionForm->setPrimaryKey($cpf);
										$ActionForm->setPrimaryKeyName("cpf");
										$ActionForm->setSQLSelectFields();
										$ActionForm->setSQLSelectFrom();
										$ActionForm->setSQLSelectWhere();
										$ActionForm->setSQLSelectGroup();
										$ActionForm->setSQLSelectAfterGroup();
										$ActionForm->setSQLSelectOrder();
										$ActionForm->setSQLDescribeEntity();
										$ActionForm->ExecuteDescribeEntity();
										$ActionForm->BuildSqlAction();
										$ActionForm->BeforeExecuteAction();
										$ActionForm->ExecuteAction();
										$ResultSQLAction = $ActionForm->getDataObject();

										if($ResultSQLAction[0]->alun_id=="") {

											$DataETL[$DataLine]["alun_cpf"] = $cpf;
										}
				    				}
				    				elseif($Header[$key]=="id") $DataETL[$DataLine]["alun_legado"] = $value;
				    				elseif($Header[$key]=="name") {

				    					$nome = str_replace("sr.","",str_replace("sra.","",str_replace("srta.","",mb_strtolower($value))));
				    					$DataETL[$DataLine]["alun_nome"] = mb_strtoupper($nome);
				    				}
				    				elseif($Header[$key]=="rg") $DataETL[$DataLine]["alun_rg"] = str_replace(".","",str_replace(",","",str_replace("-","",$value)));
				    				elseif($Header[$key]=="phone") {

				    					$telefone = str_replace(".","",str_replace(",","",str_replace("-","",$value)));
				    					$telefone = (substr($telefone,0,3)=="+55") ? substr($value,3) : $value;
				    					$telefone = $this->MaskValue->Telefone($telefone,'remove');

				    					if(strlen($telefone)==11) $DataETL[$DataLine]["alun_celular"] = $telefone;
				    					else $DataETL[$DataLine]["alun_telefone"] = $telefone;
				    				}
				    				elseif($Header[$key]=="birthday") {

				    					if(is_numeric(substr($value,0,4))) $DataETL[$DataLine]["alun_dtnascimento"] = $value;
				    					else $DataETL[$DataLine]["alun_dtnascimento"] = $this->MaskValue->Data($value,'BR2US');
				    				}

				    				$DataETL[$DataLine]["alun_delete"] = 0;
				    			}
				    			/*### TRATAMENTO DE DADOS PARA LAYOUT 1 - ALUNOS ###*/





				    			/*### TRATAMENTO DE DADOS PARA LAYOUT 2 - CURSOS ###*/
				    			elseif($this->Request["layout"]==2) {

				    				if($Header[$key]=="id") $DataETL[$DataLine]["curs_legado"] = $value;
				    				elseif($Header[$key]=="course_name") $DataETL[$DataLine]["curs_nome"] = mb_strtoupper($value);
				    				elseif($Header[$key]=="monthly_amount") $DataETL[$DataLine]["curs_valor_mensalidade"] = (is_numeric($value)) ? $value : 0;
				    				elseif($Header[$key]=="registration_tax") $DataETL[$DataLine]["curs_valor_matricula"] = (is_numeric($value)) ? $value : 0;
			    					elseif($Header[$key]=="period") {

			    						switch(strtolower($value)) {

			    							case "matutino":
			    								$DataETL[$DataLine]["curs_periodo"] = 1;
			    								break;
			    							case "vespertino":
			    								$DataETL[$DataLine]["curs_periodo"] = 2;
			    								break;
			    							case "noturno":
			    								$DataETL[$DataLine]["curs_periodo"] = 3;
			    								break;
			    							case "integral":
			    								$DataETL[$DataLine]["curs_periodo"] = 4;
			    								break;
			    							default:
			    								$DataETL[$DataLine]["curs_periodo"] = 1;
			    						}
			    					}
				    				elseif(($key=="5")&&($value<>"")) $DataETL[$DataLine]["curs_duracao"] = (is_numeric($value)) ? $value : 1;

				    				$DataETL[$DataLine]["curs_delete"] = 0;
				    			}
				    			/*### TRATAMENTO DE DADOS PARA LAYOUT 2 - CURSOS ###*/






				    			/*### TRATAMENTO DE DADOS PARA LAYOUT 3 - MATRÍCULAS ###*/
				    			elseif($this->Request["layout"]==3) {

				    				if($Header[$key]=="student_id") {

				    					if(is_numeric($value)) {

											$ActionForm = $this->Container[$ModuleDefsLayouts[1]->Entity];
											$ActionForm->setModuleDefs(json_encode($ModuleDefsLayouts[1]));
											$ActionForm->setAction('selecionar');
											$ActionForm->setPrimaryKey($value);
											$ActionForm->setPrimaryKeyName("legado");
											$ActionForm->setSQLSelectFields();
											$ActionForm->setSQLSelectFrom();
											$ActionForm->setSQLSelectWhere();
											$ActionForm->setSQLSelectGroup();
											$ActionForm->setSQLSelectAfterGroup();
											$ActionForm->setSQLSelectOrder();
											$ActionForm->setSQLDescribeEntity();
											$ActionForm->ExecuteDescribeEntity();
											$ActionForm->BuildSqlAction();
											$ActionForm->BeforeExecuteAction();
											$ActionForm->ExecuteAction();
											$ResultSQLAction = $ActionForm->getDataObject();

											if($ResultSQLAction[0]->alun_id>0) {

												$DataETL[$DataLine]["alunos_alun_id"] = $$ResultSQLAction[0]->alun_id;
											}
				    					}
				    				}
				    				elseif($Header[$key]=="id") $DataETL[$DataLine]["matr_legado"] = $value;
				    				elseif($Header[$key]=="course_id") {

				    					if(is_numeric($value)) {

											$ActionForm = $this->Container[$ModuleDefsLayouts[2]->Entity];
											$ActionForm->setModuleDefs(json_encode($ModuleDefsLayouts[2]));
											$ActionForm->setAction('selecionar');
											$ActionForm->setPrimaryKey($value);
											$ActionForm->setPrimaryKeyName("legado");
											$ActionForm->setSQLSelectFields();
											$ActionForm->setSQLSelectFrom();
											$ActionForm->setSQLSelectWhere();
											$ActionForm->setSQLSelectGroup();
											$ActionForm->setSQLSelectAfterGroup();
											$ActionForm->setSQLSelectOrder();
											$ActionForm->setSQLDescribeEntity();
											$ActionForm->ExecuteDescribeEntity();
											$ActionForm->BuildSqlAction();
											$ActionForm->BeforeExecuteAction();
											$ActionForm->ExecuteAction();
											$ResultSQLAction = $ActionForm->getDataObject();

											if($ResultSQLAction[0]->curs_id>0) {

												$DataETL[$DataLine]["cursos_curs_id"] = $ResultSQLAction[0]->curs_id;
											}
				    					}
				    				}
				    				elseif($Header[$key]=="year") $DataETL[$DataLine]["matr_ano"] = $value;

				    				$DataETL[$DataLine]["matr_status"] = 1;
				    				$DataETL[$DataLine]["matr_paga"] = 0;
				    				$DataETL[$DataLine]["matr_delete"] = 0;

				    			}
				    			/*### TRATAMENTO DE DADOS PARA LAYOUT 3 - MATRÍCULAS ###*/




				    		}


				    		/*### REMOVE ALUNOS JÁ EXISTENTES NO BANCO DE DADOS ###*/
			    			if($this->Request["layout"]==1) {

			   					foreach ($DataETL as $key => $Request) {

			   						if($Request["alunos_alun_id"]=="") unset($DataETL[$key]);
			   						if($Request["cursos_curs_id"]=="") unset($DataETL[$key]);
			   					}
			   				}
				    		/*### REMOVE ALUNOS JÁ EXISTENTES NO BANCO DE DADOS ###*/




				    		/*### REMOVE AS MATRÍCULAS SEM CURSO E/OU ALUNO ###*/
			    			elseif($this->Request["layout"]==3) {

			   					foreach ($DataETL as $key => $Request) {

			   						// if($Request["alun_cpf"]=="") unset($DataETL[$key]);
			   					}
			   				}
				    		/*### REMOVE AS MATRÍCULAS SEM CURSO E/OU ALUNO ###*/

				    	}

				    }

				    print_r($Header);
				    print_r($DataETL);

				    //REVALIDAR IMPORTAÇÃO DE ALUNOS
				    //REVALIDAR IMPORTAÇÃO DE CURSOS
				    //FINALIZAR IMPORTAÇÃO DE MATRÍCULAS


					// print_r($ModuleDefsLayouts[$this->Request["layout"]]);
			   		// echo "<h3>Iniciando o processo de importação de CURSOS...";

				    $LayoutTitle[1] = "ALUNOS";
				    $LayoutTitle[2] = "CURSOS";
				    $LayoutTitle[3] = "MATRÍCULAS";

				    // print_r($ModuleDefsLayouts);

			   		foreach ($DataETL as $key => $Request) {

		   				print_r($Request);
						$ActionForm = $this->Container[$ModuleDefsLayouts[$this->Request["layout"]]->Entity];
						$ActionForm->setModuleDefs(json_encode($ModuleDefsLayouts[$this->Request["layout"]]));
						$ActionForm->setRequest($Request);
						$ActionForm->setAction('inserir');
						$ActionForm->setSQLDescribeEntity();
						$ActionForm->ExecuteDescribeEntity();
						$ActionForm->setSQLInsertInto();
						$ActionForm->setSQLInsertFields();
						$ActionForm->setSQLInsertValues();
						$ActionForm->BuildSqlAction();
						$ActionForm->BeforeExecuteAction();
						$Result = $ActionForm->ExecuteAction();
						$ActionForm->AfterExecuteAction();

						if($Result==true) {

							$this->ExecuteAction = true;
							$TotalImport++;
						}
			   		}
			   		// print_r($Result);
			   		// var_dump($Result);

				   	if($TotalImport>0) {

				   		$ResponseOptions = $this->ResponseOptions;
				   		$ResponseOptions["ImportResult"] = "OK";
				   		$ResponseOptions["CustomMsg"] = "Foram importados um total de ".$TotalImport." registros de ".$LayoutTitle[$this->Request["layout"]];
				   		$this->ResponseOptions = $ResponseOptions;
				   	}
				   	else {

				   		$ResponseOptions = $this->ResponseOptions;
				   		$ResponseOptions["ImportResult"] = "NOK";
				   		$ResponseOptions["CustomMsg"] = "Não foram importados registros para ".$LayoutTitle[$this->Request["layout"]];
				   		$this->ResponseOptions = $ResponseOptions;
				   	}

				    fclose($handle);
				}
			}
		/*### SOMENTE EXECUTA SE O BEFOREACTION FOR TRUE ###*/

	}
	/*### EXECUTA A QUERY DA SQLAction ###*/

}
/*### CLASSE DE IMPORTAÇÃO DE DADOS (HERDA CRUD) ###*/
