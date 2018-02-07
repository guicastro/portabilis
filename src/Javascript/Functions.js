
/*#######################################################
|														|
| Arquivo geral com todas as funções JavaScript comuns	|
| em todos os módulos									|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/




/*#### NOTE: FUNÇÃO QUE SETA OS DADOS BÁSICOS DO MÓDULO E ENTIDADE ####*/
//ModuleDefs = objeto com todas as variáveis de configuração do módulo
function SetModuleEntity(ModuleDefs)
	{
		// console.log(ModuleDefs[0]);

		//SETA A ENTIDADE DO MÓDULO (SINGULAR)
		$("#txt-entity-single").html(ModuleDefs.EntitySingle);

		//SETA A ENTIDADE DO MÓDULO (PLURAL)
		$("#txt-entity-multiple").html(ModuleDefs.EntityMultiple);

		//SETA A LEGENDA DO BOTÃO DE INSERIR
		$("#btn-novo-cadastro").attr('title','Inserir '+ModuleDefs.EntitySingle);

		//SETA O TÍTULO DA PÁGINA
		$("title").html(ModuleDefs.Title);
	}
/*#### FUNÇÃO QUE SETA OS DADOS BÁSICOS DO MÓDULO E ENTIDADE ####*/
















/*#### NOTE: FUNÇÃO QUE GERA O GRID DE DADOS ####*/
//IdElementDataTable = ID do elemento TABLE que contém o GRID
//Source = caminho da origem dos dados
//ModuleDefs = objeto com todas as variáveis de configuração do módulo
//ColumnDefs = objeto com as configurações das colunas que irão ser exibidas
//ColumnOrder = objeto com a ordenação inicial do grid
//IdElementForm = ID do elemento FORM dos cadastros principais
//IdModalElement = ID do elemento MODAL dos cadastros principais
function GenerateDataTable(IdElementDataTable, Source, ModuleDefs, ColumnDefs, ColumnOrder, IdElementForm, IdModalElement) {

	/*### NOTE: CONFIGURAÇÕES DO ModuleDefs (GenerateDataTable) ###*/
	//Name = identificador interno do módulo
	//Prefix = prefixo dos campos do módulo (usados na tabela do banco de dados)
	//Title = título do módulo
	//Entity = entidade do módulo
	//Table = nome da tabela no banco de dados
	//EntitySingle = entidade do módulo (singular)
	//EntityMultiple = entidade do módulo (plural)
	/*### CONFIGURAÇÕES DO ModuleDefs ###*/



	/*### NOTE: CONFIGURAÇÕES DO ColumnDefs (GenerateDataTable) ###*/
	//Objeto com todas as configurações das colunas do GRID
	//name = título da coluna no GRID
	//data = nome do campo da coluna no banco de dados
	//primary_key = indica se a coluna é a chave primária da tabela
	//orderable = se true permite que seja ordenado o grid pela coluna
	//minSearchValue = quantidade mínima de caracteres para acionar a busca por coluna
	//width = espaço que a coluna ocupa no grid, em percentual
	//responsive = detalhes de responsividade
	//				class: expand = coluna agrupadora quando as demais colunas são ocultadas pelo tamanho do grid
	//				hide = determina se a coluna é ocultada no celular ou tablet (phone/tablet)
	//DT_Filter = detalhes do filtro de coluna
	//				hasFilter: true para incluir filtro de coluna
	//				type: para o tipo de filtro, sendo texto (input) ou data (date)
	//				searchOperator: opção para informar se o operador da busca do filtro será diferente do padrão ( = igual) - LIKE | DateTimeWithoutTime (opcional)
	//mask = opções de máscara de dados
	//				name: nome do método dentro da classe MaskValue
	//				View: operação de máscara na View
	//				Database: operação de máscara no banco de dados
	//DataType = indica o tipo de dados do filtro  - date, text, int (opcional)
	//defaultContent = conteúdo de texto padrão da coluna, quando não será retornado do banco de dados
	//className = classe CSS a ser aplicada na coluna e nos textos
	//checkbox = true quando o conteúdo da coluna será checkbox para selecionar dados do grid
	//targets = utilizado quando a coluna for checkbox, e o padrão é 0 (zero)
	//render = conteúdo que será adicionado na coluna, utilizando dados dinâmicos de outras colunas do próprio grid (texto padrão para checkbox: function (data, type, full, meta){ return '<input type="checkbox" name="selecionado[]" value="' + full.DT_RowId + '">'; })
	//otherTable = indica que o campo é obtido em um relacionamento com outra tabela (Tabela Dinâmica)
	//				DynamicTable: nome da tabela dinâmica
	//				Alias: alias da tabela dinâmica, caso exista mais de um JOIN no mesmo GRID (opcional)
	//				Join: tipo de JOIN com a outra tabela
	//				FieldKey: campo na tabela primária que faz o relacionamento
	//				FieldKeyOtherTable: campo relacionado na outra tabela
	//				View: nome do campo que será exibido no GRID
	//otherTable = indica que o campo é obtido em um relacionamento com outra tabela (outras tabelas)
	//				Table: nome da outra tabela
	//				Alias: alias da outra tabela, caso tenha nome igual à tabela original (obrigatório)
	//				Prefix: prefixo dos campos da outra tabela
	//				Join: tipo de JOIN com a outra tabela
	//				OtherJoin: tipo de JOIN customizado, sendo necessário indicar todos os campos de JOIN e/ou ALIAS (quando utilizado FieldKey e FieldKeyOtherTable são descartados)
	//				FieldKey: campo na tabela primária que faz o relacionamento
	//				FieldKeyOtherTable: campo relacionado na outra tabela
	//				View: nome do campo que será exibido no GRID
	//AdditionalConditions = condições adicionais no WHERE daquele campo, por exemplo um filtro permanente (preencer apenas com a condição e valor)
	/*### CONFIGURAÇÕES DO ColumnDefs ###*/



	/*### NOTE: CONFIGURAÇÕES DO ColumnOrder (GenerateDataTable) ###*/
	//Objeto com a ordenação inicial das colunas do grid (em array)
	//campo 1 = número da coluna no grid (zero é a primeira)
	//campo 2 = ordem da coluna (asc|desc)
	/*### CONFIGURAÇÕES DO ColumnOrder ###*/




	//SE O SOURCE NÃO FOI DEFINIDO, RETORNA O PADRÃO
	// console.log(Source);
	Source = Source != '' ? Source : 'src/Controller/Controller.php';

	/* // DOM Position key index //

		l - Length changing (dropdown)
		f - Filtering input (search)
		t - The Table! (datatable)
		i - Information (records)
		p - Pagination (paging)
		r - pRocessing
		< and > - div elements
		<"#id" and > - div with an id
		<"class" and > - div with a class
		<"#id.class" and > - div with an id and class

		Also see: http://legacy.datatables.net/usage/features
	*/


	/*#### VARIÁVEIS BÁSCIAS DE INICIALIZAÇÃO ####*/
	var responsiveHelper_dt_basic = undefined;
	var responsiveHelper_datatable_fixed_column = undefined;
	var responsiveHelper_datatable_col_reorder = undefined;
	var responsiveHelper_datatable_tabletools = undefined;
    var minSearchValueDefault = 3;

	var breakpointDefinition = {
		tablet : 1024,
		phone : 480
	};
	/*#### VARIÁVEIS BÁSCIAS DE INICIALIZAÇÃO ####*/


	//CRIA VARIÁVEL DE CONTROLE PARA NÚMERO DA COLUNA ACTION DO GRID
	var NumColumnAction = 0;


	//CRIA ARRAY PARA minSearchValue
	var AllMinSearchValue = [];

	/*### NOTE: PERCORRE O COLUMNDEFS E CRIA AS COLUNAS DA TABELA HTML DO GRID (GenerateDataTable) ###*/
	$.each(ColumnDefs,function(key,Column){

		/*### NOTE: SE FOR COLUNA DE FILTRO, CRIA DE ACORDO COM O TIPO, SENÃO CRIA COLUNA EM BRANCO (GenerateDataTable) ###*/
		if((Column.DT_Filter)&&(Column.searchable!=false)) {

			/*### SE DT_FILTER FOR DO TIPO INPUT ###*/
			if(Column.DT_Filter.type=="input") {

				$("#"+IdElementDataTable+" thead tr.DT_Filter").append('<th id="DT_Filter_'+Column.data+'" data-field="'+Column.data+'" class="hasinput filtro-coluna" style="width:'+Column.witdh+'"><input type="text" class="form-control" placeholder="'+Column.name+'" /></th>');
			}
			/*### SE DT_FILTER FOR DO TIPO INPUT ###*/



			/*### SE DT_FILTER FOR DO TIPO DATE ###*/
			else if(Column.DT_Filter.type=="date") {

				$("#"+IdElementDataTable+" thead tr.DT_Filter").append('<th id="DT_Filter_'+Column.data+'" data-field="'+Column.data+'" class="hasinput icon-addon filtro-coluna"><input id="dateselect_filter" type="text" placeholder="'+Column.name+'" class="form-control datepicker" data-dateformat="dd/mm/yy"><label for="dateselect_filter" class="glyphicon glyphicon-calendar no-margin padding-top-15" rel="tooltip" title="" data-original-title="'+Column.name+'"></label></th>');
			}
			/*### SE DT_FILTER FOR DO TIPO DATE ###*/
		} else {

			$("#"+IdElementDataTable+" thead tr.DT_Filter").append('<th></th>');
		}
		/*### SE FOR COLUNA DE FILTRO, CRIA DE ACORDO COM O TIPO, SENÃO CRIA COLUNA EM BRANCO ###*/




		/*### NOTE: SE A COLUNA FOR DO TIPO CHECKBOX, ADICIONA NA DT_TITLE O ITEM PARA SELECIONAR TODOS (GenerateDataTable),
				SE A COLUNA FOR DO TIPO ACTIONS, ADICIONA O DT_TITLE SEM O TÍTULO,
				SENÃO CRIA DT_TITLE PADRÃO ###*/
		if(Column.checkbox==true)  {

			$("#"+IdElementDataTable+" thead tr.DT_Title").append('<th id="DT_Title_checkbox"><input type="checkbox" name="seleciona_todos" value="1" id="seleciona_todos"></th>');
		}
		else if(Column.name=='actions')  {

			$("#"+IdElementDataTable+" thead tr.DT_Title").append('<th id="DT_Title_'+Column.data+'"></th>');
		}
		else {

			$("#"+IdElementDataTable+" thead tr.DT_Title").append('<th id="DT_Title_'+Column.data+'">'+Column.name+'</th>');
		}
		/*### SE A COLUNA FOR DO TIPO CHECKBOX, ADICIONA NA DT_TITLE O ITEM PARA SELECIONAR TODOS,
				SE A COLUNA FOR DO TIPO ACTIONS, ADICIONA O DT_TITLE SEM O TÍTULO,
				SENÃO CRIA DT_TITLE PADRÃO ###*/





		/*### ADICIONA DEFINIÇÕES DE RESPONSIVIDADE ###*/
		if(Column.responsive) {

			/*### ADICIONA DATA-CLASS ###*/
			if(Column.responsive.class) {

				$("#"+Column.data).data("class",Column.responsive.class);
			}
			/*### ADICIONA DATA-CLASS ###*/


			/*### ADICIONA DATA-HIDE ###*/
			if(Column.responsive.hide) {

				$("#"+Column.data).data("hide",Column.responsive.hide);
			}
			/*### ADICIONA DATA-HIDE ###*/
		}
		/*### ADICIONA DEFINIÇÕES DE RESPONSIVIDADE ###*/





		/*### PREENCHA VARIÁVEL COM TODOS OS minSearchValue DAS COLUNAS ###*/
		if(Column.minSearchValue) {

			AllMinSearchValue[Column.data] = Column.minSearchValue;
		}
		/*### PREENCHA VARIÁVEL COM TODOS OS minSearchValue DAS COLUNAS ###*/





		/*### SE A COLUNA TIVER DataType COMO int, DEFINE O minSearchValue COMO 1 ###*/
		if(Column.DataType) {

			AllMinSearchValue[Column.data] = 1;
		}
		/*### SE A COLUNA TIVER DataType COMO int, DEFINE O minSearchValue COMO 1 ###*/





		//SE EXISTIR A COLUNA ACTION INCREMENTA A VARIÁVEL DE CONTROLE
		if(Column.name=="actions") NumColumnAction++;

	});
	/*### PERCORRE O COLUMNDEFS E CRIA AS COLUNAS DA TABELA HTML DO GRID ###*/



	/*### NOTE: SE NÃO EXISTIR COLUNA ACTION, ADICIONA NO COLUMNDEFS E CRIA AS COLUNAS NA TABELA HTML (GenerateDataTable) ###*/
	if(NumColumnAction==0) {

		ColumnDefs.push({ "name":"actions", "data":"acoes", "searchable":false, "orderable":false, "defaultContent":'<button type="button" data-id="alterar" title="Alterar" class="btn btn-success btn-xs"><i class="fa fa-edit"></i></button>&nbsp;<button type="button" data-id="excluir" title="Excluir" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>' });

		$("#"+IdElementDataTable+" thead tr.DT_Filter").append('<th style="width:10%"></th>');
		$("#"+IdElementDataTable+" thead tr.DT_Title").append('<th></th>');
	}
	/*### SE NÃO EXISTIR COLUNA ACTION, ADICIONA NO COLUMNDEFS E CRIA AS COLUNAS NA TABELA HTML ###*/



	//DEBUG DE VARIÁVEIS
	// console.log(ColumnDefs);

	//DESABILITA O ALERT DE ERROS DO DATATABLE
	$.fn.dataTable.ext.errMode = 'none';


	/*#### NOTE: GRID DE DADOS (GenerateDataTable) ####*/
    var otable = $("#"+IdElementDataTable).DataTable({

		/*#### HABILITA SERVERSIDE E DEFINE ORIGEM DOS DADOS ####*/
		"processing": false,
		"serverSide": true,
		"ajax": {
			//URL E INFORMAÇÕES PARA CARREGAMENTO DOS DADOS NO GRID
			"url": Source,
			"method":"POST",
			"data": function (d) {
				d.Route = 'DataTablesGrid',
				d.ModuleDefs = ModuleDefs,
				d.ColumnDefs = ColumnDefs,
				d.ColumnOrder = ColumnOrder,
				d.Token = token
			}
		},
		/*#### HABILITA SERVERSIDE E DEFINE ORIGEM DOS DADOS ####*/

    	//HABILITA PAGINAÇÃO
		"paging": true,

		//PADRÃO DE REGISTROS POR PÁGINA
		"pageLength": 10,

		//OPÇÕES DE REGISTROS POR PÁGINA
        "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Todos"] ],

		/*#### HTML DA BARRA DE FERRAMENTAS ####*/
		"dom": "<'dt-toolbar'<'col-xs-12 col-sm-6 hidden-xs'f><'col-sm-6 col-xs-12 hidden-xs'Tl>r>"+
				"t"+
				"<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
		/*#### HTML DA BARRA DE FERRAMENTAS ####*/

		//LARGURA AUTOMÁTICA
		"autoWidth" : true,

		//DEFINIÇÃO DAS COLUNAS
		"columns": ColumnDefs,

		//ORDENAÇÃO INICIAL
		"order": ColumnOrder,

		/*#### TRADUÇÃO PARA PT-BR ####*/
		"language": {
			"search": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>',
			"lengthMenu": "Mostrar _MENU_ registros por página",
			"zeroRecords": "A busca não retornou resultados",
			"info": "Página _PAGE_ de _PAGES_ | Total de registros: _TOTAL_",
			"infoEmpty": "0 registros",
			"infoFiltered": "de um total de _MAX_ cadastrados",
			"loadingRecords": "Carregando...",
			"processing": "<i class='ace-icon fa fa-spin fa-cog blue bigger-160'></i> Processado...",
			"paginate": {
					"first":      "Primeira",
					"last":       "Última",
					"next":       "Próxima",
					"previous":   "Anterior"
				},
		},
		/*#### TRADUÇÃO PARA PT-BR ####*/


		/*#### BOTÕES DE EXPORT DO GRID ####*/
        "oTableTools": {
        	 "aButtons": [
                {
                    "sExtends": "xls",
					"sButtonText": "Excel",
					"sToolTip":"Exportar para Excel",
                },
                {
                    "sExtends": "pdf",
                    "sTitle": "Listagem_Resultados",
                    "sPdfMessage": "Exportar para PDF",
					"sToolTip":"Exportar para PDF",
                    "sPdfSize": "A4"
                },
             	{
                	"sExtends": "print",
					"sButtonText": "Imprimir",
					"sToolTip":"Imprimir",
					"sInfo":"<h6>Visualização para impressão</h6><p>Utilize as opções de impressão do seu navegador para imprimir a tela</p>",
                	"sMessage": "Visualização para impressão <i>(Esc para fechar)</i>"
            	}
             ],
            "sSwfPath": "lib/smartadmin/js/plugin/datatables/swf/copy_csv_xls_pdf.swf"
        },
		/*#### BOTÕES DE EXPORT DO GRID ####*/


		/*#### CALLBACKS DE RESPONSIVIDADE DO GRID ####*/
		"preDrawCallback" : function() {
			// Initialize the responsive datatables helper once.
			if (!responsiveHelper_datatable_fixed_column) {
				responsiveHelper_datatable_fixed_column = new ResponsiveDatatablesHelper($('#datatable_fixed_column'), breakpointDefinition);
			}
		},
		"rowCallback" : function(nRow) {
			responsiveHelper_datatable_fixed_column.createExpandIcon(nRow);
		},
		"drawCallback" : function(oSettings) {
			responsiveHelper_datatable_fixed_column.respond();
		},
		/*#### CALLBACKS DE RESPONSIVIDADE DO GRID ####*/

    }).on( 'error.dt', function ( e, settings, techNote, message ) {
        console.log( 'An error has been reported by DataTables: ', message );
        console.log(e);
        console.log(settings);
        ValidateToken(settings.jqXHR);
        var Permission = CheckPermission(settings.jqXHR);
        if(Permission!=false) {
        	AlertMsg('DataTable','error', settings.jqXHR, message);
        }
    }).on( 'draw', function (oSettings) { });
	/*#### GRID DE DADOS ####*/





    //BARRA DE FERRAMENTAS (PERSONALIZAÇÃO)
    // $("div.toolbar").html('<div class="text-right"><img src="img/logo.png" alt="SmartAdmin" style="width: 111px; margin-top: 3px; margin-right: 10px;"></div>');





	/*#### NOTE: FUNÇÃO QUE APLICA OS FILTROS DE CAMPOS TIPO TEXTO DA COLUNA DA LINHA DT_FILTER (GenerateDataTable) ####*/
    $("#"+IdElementDataTable+" thead th input[type=text]").on( 'keyup', function () {

    	// console.log("ColumnFilter");
    	// console.log(ColumnDefs);
    	// console.log(AllMinSearchValue);
    	// console.log(AllMinSearchValue[$(this).parent().data('field')]);

    	//VERIFICA SE HÁ UM VALOR MÍNIMO DE BUSCA NA COLUMNDEFS, SENÃO MANTÉM O PADRÃO
    	minSearchValue = AllMinSearchValue[$(this).parent().data('field')] > 0 ? AllMinSearchValue[$(this).parent().data('field')] : minSearchValueDefault;

    	// console.log(ColumnDefs[$(this).parent().index()].minSearchValue);
    	// console.log(minSearchValue);
    	// console.log(this.value.length);

    	if(((this.value.length)>=minSearchValue)||(this.value==""))
	    	{
	    		// console.log();
		        otable
		            .column( $(this).parent().index()+':visible' )
		            .search( this.value )
		            .draw();
	    	}

    } );
	/*#### FUNÇÃO QUE APLICA OS FILTROS DE CAMPOS TIPO TEXTO DA COLUNA DA LINHA DT_FILTER ####*/



	/*#### NOTE: FUNÇÃO QUE APLICA OS FILTROS DE CAMPOS TIPO DATESELECT DA COLUNA DA LINHA DT_FILTER (GenerateDataTable) ####*/
    $("#"+IdElementDataTable+" thead th input[id=dateselect_filter]").on( 'change', function () {

    	//VERIFICA SE HÁ UM VALOR MÍNIMO DE BUSCA NA COLUMNDEFS, SENÃO MANTÉM O PADRÃO
    	minSearchValue = ColumnDefs[$(this).parent().index()].minSearchValue > 0 ? ColumnDefs[$(this).parent().index()].minSearchValue : minSearchValueDefault;

    	if(((this.value.length)>=minSearchValue)||(this.value==""))
	    	{
		        otable
		            .column( $(this).parent().index()+':visible' )
		            .search( this.value )
		            .draw();
	    	}

    } );
	/*#### FUNÇÃO QUE APLICA OS FILTROS DE CAMPOS TIPO DATESELECT DA COLUNA DA LINHA DT_FILTER ####*/





	/*#### NOTE: FUNÇÃO QUE APLICA OS FILTROS DA ÁREA DE FILTROS (GenerateDataTable) ####*/
    $(".filtro-grid").on('change', function () {

	    otable
	        .column( $(this).data('id') )
	        .search( this.value )
	        .draw();
    });
	/*#### FUNÇÃO QUE APLICA OS FILTROS DA ÁREA DE FILTROS ####*/




	/*#### NOTE: FUNÇÃO QUE LIMPA OS FILTROS E ATUALIZA O GRID (GenerateDataTable) ####*/
    $("#btn-resetar-filtros").on('click', function () {

    	$(".filtro-grid").val('').trigger('change');
    	$.each($("#"+IdElementDataTable+" thead th input[type=text]"), function(index, element) {

    		$(this).val('').trigger('change');
    		otable.column($(this).parent().index()+':visible' ).search('').draw();
    	});

	    otable.ajax.reload(null,false);
    });
	/*#### FUNÇÃO QUE LIMPA OS FILTROS E ATUALIZA O GRID ####*/







	/*#### AÇÕES PARA MOSTRAR MENSAGEM DE CARREGANDO PERSONALIZADA QUANDO ENVIA CONSULTA À BASE DE DADOS ####*/
    var contentArea = $('#content');
    var offset = contentArea.offset();
    contentArea.css('opacity', 0.25);
    $("#DataTablesProcessingBehaviour").show();

    // console.log(otable);

	otable.on('processing.dt',function( e, settings, processing ){

		if (processing){

     		// console.log("processando");
            contentArea.css('opacity', 0.25)
            $("#DataTablesProcessingBehaviour").show();
     	}else {

     		// console.log("parado");
            contentArea.css('opacity', 1);
            $("#DataTablesProcessingBehaviour").hide();
     	}
     });
	/*#### AÇÕES PARA MOSTRAR MENSAGEM DE CARREGANDO PERSONALIZADA QUANDO ENVIA CONSULTA À BASE DE DADOS ####*/





	/*#### ATUALIZA O GRID QUANDO CLICA NO BOTÃO ATUALIZAR ####*/
    $("#btn-atualizar").on('click', function () {

	    otable.ajax.reload(null,false);
    });
	/*#### ATUALIZA O GRID QUANDO CLICA NO BOTÃO ATUALIZAR ####*/





	//CHAMA AS FUNÇÕES PADRÃO DO SMARTADMIN
	runAllForms();



	//CHAMA A FUNÇÃO QUE HABILTA OS WIDGETS
	setup_widgets_desktop();




	/*#### NOTE: AÇÕES QUANDO CLICAR NO BOTÃO DE INSERIR NOVO CADASTRO (GenerateDataTable) ####*/
	$("#btn-novo-cadastro").on('click', function (event) {
		event.preventDefault();

		//CHAMA A FUNÇÃO QUE RESETA A VALIDAÇÃO DO FORMULÁRIO
		ResetValidation(IdElementForm)

		//CHAMA FUNÇÃO QUE RESETA O FORMULÁRIO
		ResetForm('novo', IdElementForm);


		//ESCONDE A LINHA DE ID E HISTÓRICO DO REGISTRO
		$('#'+IdElementForm+' #id-reg').hide();

		/*#### MOSTRA/ESCONDE BOTÕES DE INSERIR E ALTERAR ####*/
		$('#btn-inserir').show();
		$('#btn-alterar').hide();
		/*#### MOSTRA/ESCONDE BOTÕES DE INSERIR E ALTERAR ####*/


		/*#### CHAMA FUNÇÃO QUE MOSTRA/ESCONDE OUTROS BOTÕES DO FORM, SE EXISTIR ####*/
		if (typeof ShowHiddenFormButtons === "function") {
			ShowHiddenFormButtons('novo');
		}
		/*#### CHAMA FUNÇÃO QUE MOSTRA/ESCONDE OUTROS BOTÕES DO FORM, SE EXISTIR ####*/

		//ACIONA FUNÇÃO PARA AÇÕES ADICIONAIS
		AdditionalActionForm(IdElementForm, 'novo', '', '', '', otable, ModuleDefs, IdModalElement)

		//ABRE O MODAL DE CADASTRO
		$("#"+IdModalElement).modal('show');
	});
	/*#### AÇÕES QUANDO CLICAR NO BOTÃO DE INSERIR NOVO CADASTRO ####*/








	/*#### NOTE: AÇÕES QUANDO CLICAR NO BOTÃO DE ALTERAR NO GRID (GenerateDataTable) ####*/
	$("#"+IdElementDataTable+" tbody").on('click', 'tr td:last-child button[data-id=alterar]', function (event) {
		event.preventDefault();

		//OBTEM O ID DA LINHA
		var id_reg = $(this).closest('tr').attr('id');

		//CHAMA A FUNÇÃO QUE RESETA A VALIDAÇÃO DO FORMULÁRIO
		ResetValidation(IdElementForm)

		//CHAMA FUNÇÃO QUE RESETA O FORMULÁRIO
		ResetForm('alterar', IdElementForm);

		/*#### MOSTRA/ESCONDE BOTÕES DE INSERIR E ALTERAR ####*/
		$('#btn-inserir').hide();
		$('#btn-alterar').show();
		/*#### MOSTRA/ESCONDE BOTÕES DE INSERIR E ALTERAR ####*/


		/*#### CHAMA FUNÇÃO QUE MOSTRA/ESCONDE OUTROS BOTÕES DO FORM, SE EXISTIR ####*/
		if (typeof ShowHiddenFormButtons === "function") {
			ShowHiddenFormButtons('alterar');
		}
		/*#### CHAMA FUNÇÃO QUE MOSTRA/ESCONDE OUTROS BOTÕES DO FORM, SE EXISTIR ####*/


		//CHAMA A FUNÇÃO PADRÃO DE AÇÕES DO FORM
		ActionForm('selecionar', '', id_reg, IdElementDataTable, otable, IdElementForm, ModuleDefs, IdModalElement);


		//ABRE O MODAL DE CADASTRO
		$("#"+IdModalElement).modal('show');
	});
	/*#### AÇÕES QUANDO CLICAR NO BOTÃO DE ALTERAR NO GRID ####*/







	/*#### NOTE: AÇÕES QUANDO CLICAR NO BOTÃO DE EXCLUIR NO GRID (GenerateDataTable) ####*/
	$("#"+IdElementDataTable+" tbody").on('click', 'tr td:last-child button[data-id=excluir]', function (e) {

		//OBTEM O ID DA LINHA
		var id_reg = $(this).closest('tr').attr('id');


		/*#### EXIBE MENSAGEM DE AVISO ####*/
		if(bootbox) {

			var dialog = bootbox.dialog({
				title: 'Excluir cadastro',
				message: "Confirma a exclusão do registro?",
				buttons: {
				    cancel: {
				        label: "Não",
				        className: 'btn-default'
				    },
				    ok: {
				        label: "Sim",
				        className: 'btn-danger',
				        callback: function(){

							//CHAMA A FUNÇÃO PADRÃO DE AÇÕES DO FORM
							ActionForm('excluir', '', id_reg, IdElementDataTable, otable, IdElementForm, ModuleDefs, IdModalElement);
				        }
				    }
				}
			});
		}
		/*#### EXIBE MENSAGEM DE AVISO ####*/

		e.preventDefault();
	});
	/*#### AÇÕES QUANDO CLICAR NO BOTÃO DE EXCLUIR NO GRID ####*/






	/*### NOTE: SELECIONA TODOS OS CHECKBOX QUANDO CLICA NO CHECKBOX DA LINHA DT_TITLE (GenerateDataTable) ###*/
	$('#seleciona_todos').on('click', function(){

	  //BUSCA TODOS OS ELEMENTOS DO GRID
	  var rows = otable.rows({ 'search': 'applied' }).nodes();

	  //MARCA SE O ELEMENTO FOI CLICADO
	  $('input[type="checkbox"]', rows).prop('checked', this.checked);
	});
	/*### SELECIONA TODOS OS CHECKBOX QUANDO CLICA NO CHECKBOX DA LINHA DT_TITLE ###*/








	/*### NOTE: MODIFICA A MARCAÇÃO DO CHECKBOX DA LINHA DT_TITLE SE MODIFICAR OS CHECKBOX DO GRID (GenerateDataTable) ###*/
	$("#"+IdElementDataTable+" tbody").on('change', 'input[type="checkbox"]', function(){

		/*### SE O CHECKBOX NÃO ESTIVER MARCADO, TROCA A MARCAÇÃO PARA "INDETERMINATE" ###*/
		if(!this.checked) {

	 		var el = $('#seleciona_todos').get(0);

			if(el && el.checked && ('indeterminate' in el)) {

	    		el.indeterminate = true;
	    	}
		}
		/*### SE O CHECKBOX NÃO ESTIVER MARCADO, TROCA A MARCAÇÃO PARA "INDETERMINATE" ###*/

	});
	/*### MODIFICA A MARCAÇÃO DO CHECKBOX DA LINHA DT_TITLE SE MODIFICAR OS CHECKBOX DO GRID ###*/






	//RETORNA A VARIÁVEL DO GRID
    return otable;

}
/*#### FUNÇÃO QUE GERA O GRID DE DADOS ####*/











/*### MÉTODO ADICIONAL DE VALIDAÇÃO DE DATA EM FORMATO DD/MM/AAAA ###*/
$.validator.addMethod("dataBR",function(value,element) {
		return this.optional(element)||/^\d{1,2}[\/-]\d{1,2}[\/-]\d{4}$/.test(value);
}, $.validator.messages.dataBR);
/*### MÉTODO ADICIONAL DE VALIDAÇÃO DE DATA EM FORMATO DD/MM/AAAA ###*/


/*### MÉTODO ADICIONAL DE VALIDAÇÃO DE DATA EM FORMATO DD/MM/AAAA HH:MM ###*/
$.validator.addMethod("datahoraBR",function(value,element) {
        return this.optional(element)||/^\d{1,2}[\/-]\d{1,2}[\/-]\d{4}[\/ ]\d{1,2}[\/:]\d{1,2}$/.test(value);
}, $.validator.messages.datahoraBR);
/*### MÉTODO ADICIONAL DE VALIDAÇÃO DE DATA EM FORMATO DD/MM/AAAA HH:MM ###*/


/*### MÉTODO ADICIONAL DE VALIDAÇÃO DE CEP NO FORMATO 99999-999 ###*/
$.validator.addMethod("CEP",function(value,element) {
		return this.optional(element)||/^\d{1,5}[-]\d{1,3}$/.test(value);
}, $.validator.messages.CEP);
/*### MÉTODO ADICIONAL DE VALIDAÇÃO DE CEP NO FORMATO 99999-999


/*### MÉTODO ADICIONAL DE VALIDAÇÃO DE CPF NO FORMATO 999.999.999-99 ###*/
$.validator.addMethod("CPF",function(value,element) {
		return this.optional(element)||/^\d{1,3}[.]\d{1,3}[.]\d{1,3}[-]\d{1,2}$/.test(value);
}, $.validator.messages.CPF);
/*### MÉTODO ADICIONAL DE VALIDAÇÃO DE CPF NO FORMATO 999.999.999-99 ###*/


/*### MÉTODO ADICIONAL DE VALIDAÇÃO DE URLS INTERNAS ###*/
$.validator.addMethod( "url2", function( value, element ) {
    return this.optional( element ) || /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)*(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test( value );
}, $.validator.messages.url2);
/*### MÉTODO ADICIONAL DE VALIDAÇÃO DE URLS INTERNAS ###*/













/*### NOTE: FUNÇÃO QUE FAZ A VALIDAÇÃO PADRÃO DOS FORMULÁRIOS ###*/
//IdElementForm = ID do elemento FORM dos cadastros principais
//IdElementDataTable = ID do elemento TABLE que contém o GRID
//DataTable = objeto DataTable
//ModuleDefs = objeto com todas as variáveis de configuração do módulo
//IdModalElement = ID do elemento MODAL dos cadastros principais
function FormValidation(IdElementForm, IdElementDataTable, DataTable, ModuleDefs, IdModalElement)
	{

		/*### CONFIGURA TODOS OS CAMPOS DO FORMULÁRIO PARA MAIÚSCULA ###*/
		$("#"+IdElementForm+" :input").keyup(function(){
		    if((this.type!='password')
		    	&&(this.type!='email')
		    	&&(ModuleDefs.Entity!="Configuracao")) this.value = this.value.toUpperCase();
		});
		/*### CONFIGURA TODOS OS CAMPOS DO FORMULÁRIO PARA MAIÚSCULA ###*/


		// console.log($('#'+IdElementForm));
		var $validator = $('#'+IdElementForm).validate({
								errorElement: 'div',
								errorClass: 'help-block',
								focusInvalid: false,
								ignore: [],

								highlight: function (e) {

									ValidateHighlight(e);
								},

								success: function (e) {

									ValidateSuccess(e);
								},

								errorPlacement: function (error, element) {

									ValidateErrorPlacement(error, element);
								},

								submitHandler: function (form) {

									// console.log("validado");
									var formData = new FormData(form);
									formData.append('action',$(this.submitButton).data('id'));

									if((ModuleDefs.Entity=="Relatorio")||(ModuleDefs.Entity=="Exporta")) {

										form.submit();
									}
									else {

										ActionForm($(this.submitButton).data('id'), formData, $('#'+IdElementForm+' #id_reg').html(), IdElementDataTable, DataTable, IdElementForm, ModuleDefs, IdModalElement);
									}
								},
								invalidHandler: function (form) {

								}
							});


		/*### VALIDAR CAMPOS APÓS SAIR DO CAMPO OU TROCAR VALOR ###*/
		$('#'+IdElementForm+' input, #'+IdElementForm+' textarea').each(function(){
		  $(this).blur(function(){
		    $(this).closest('form').validate().element($(this));
		  });
		});
		$('#'+IdElementForm+' select, #'+IdElementForm+' .select2').on("change", function(e) {
		   $(this).closest('form').validate().element($(this));
		});
		/*### VALIDAR CAMPOS APÓS SAIR DO CAMPO OU TROCAR VALOR ###*/

		return $validator;
	}
/*### FUNÇÃO QUE FAZ A VALIDAÇÃO PADRÃO DOS FORMULÁRIOS ###*/

















/*### NOTE: FUNÇÃO QUE RESETA A VALIDAÇÃO DO FORMULÁRIO ###*/
//IdElementForm = ID do elemento FORM dos cadastros principais
function ResetValidation(IdElementForm)
	{

		$('#'+IdElementForm).validate().resetForm();
		$('#'+IdElementForm).find(".has-error").removeClass("has-error");
	}
/*### FUNÇÃO QUE RESETA A VALIDAÇÃO DO FORMULÁRIO ###*/






/*### NOTE: FUNÇÃO QUE EXIBE AS MENSAGENS DE AVISO OU ERRO ###*/
//action = action da ação (inserir, alterar, excluir, selecionar, etc)
//type = tipo de mensagem de aviso (alert ou error)
//jqXHR = objeto jqXHR para erros de parse e JSON
//ErrorMsg = mensagem de erro específica retornada via JSON
//CustomMsg = mensagem personalizada para se exibida no lugar da mensagem_aviso
function AlertMsg(action, type, jqXHR, ErrorMsg, CustomMsg) {

	// console.log('AlertMsg');
	// console.log(action);
	// console.log(type);
	// console.log(jqXHR);
	// console.log(ErrorMsg);
	// console.log(CustomMsg);

	/*#### TÍTULOS PADRÃO PARA MENSAGENS SMARTBOX APÓS AS AÇÕES ####*/
	var mensagem_aviso = {
		'inserir':'Registro inserido com sucesso!',
		'alterar':'Registro alterado com sucesso!',
		'excluir':'Registro excluído com sucesso!',
		'logout':'Logout executado com sucesso!',
		'erro':'Houve um erro no processamento!',
		'copiar_para_evento':'Os serviços selecionados foram copiados para o novo evento.',
		'excluir_selecionados':'Registros selecionados excluídos com sucesso!',
		'alterar_permissoes':'As permissões do usuário foram atualizadas.',
		'alterar_parceiros':'Os parceiros do usuário foram atualizados.',
		'transferir_usuarios':'O perfil atual foi excluído e os usuários transferidos para o novo',
		'transferir_usuarios':'O perfil atual foi excluído e os usuários transferidos para o novo',
	};
	/*#### TÍTULOS PADRÃO PARA MENSAGENS SMARTBOX APÓS AS AÇÕES ####*/



	/*#### CORES PADRÃO PARA MENSAGENS SMARTBOX APÓS AS AÇÕES ####*/
	var cor_aviso = {
		'inserir':'#3276B1',
		'alterar':'#739E73',
		'excluir':'#C46A69',
		'logout':'#C46A69',
		'erro':'#C79121',
		'copiar_para_evento':'#739E73',
		'excluir_selecionados':'#C46A69',
		'alterar_permissoes':'#739E73',
		'alterar_parceiros':'#739E73',
		'transferir_usuarios':'#C46A69',
	};
	/*#### CORES PADRÃO PARA MENSAGENS SMARTBOX APÓS AS AÇÕES ####*/



	/*#### ÍCONES PADRÃO PARA MENSAGENS SMARTBOX APÓS AS AÇÕES ####*/
	var icone_aviso = {
		'inserir':'fa fa-thumbs-up bounce animated',
		'alterar':'fa fa-thumbs-up bounce animated',
		'excluir':'fa fa-thumbs-up bounce animated',
		'logout':'fa fa-sign-out bounce animated',
		'erro':'fa fa-exclamation-triangle swing animated',
		'copiar_para_evento':'fa fa-thumbs-up bounce animated',
		'excluir_selecionados':'fa fa-thumbs-up bounce animated',
		'alterar_permissoes':'fa fa-thumbs-up bounce animated',
		'alterar_parceiros':'fa fa-thumbs-up bounce animated',
		'transferir_usuarios':'fa fa-exclamation-triangle swing animated',
	};
	/*#### ÍCONES PADRÃO PARA MENSAGENS SMARTBOX APÓS AS AÇÕES ####*/



	/*#### COMPLEMENTOS PARA MENSAGENS SMARTBOX APÓS AS AÇÕES ####*/
	var complemento_aviso = {
		'inserir':'',
		'alterar':'',
		'excluir':''
	};
	/*#### COMPLEMENTOS PARA MENSAGENS SMARTBOX APÓS AS AÇÕES ####*/




	/*#### EXIBIR MENSAGEM DE ALERTA DE UMA LINHA ####*/
	if(type=="alert") {

		TitleMsg = CustomMsg != "" ? CustomMsg : mensagem_aviso[action];

		/*#### EXIBE SMARTBOX COM A MENSAGEM ####*/
		$.smallBox({
			title : TitleMsg,
			content : complemento_aviso[action],
			color : cor_aviso[action],
			iconSmall : icone_aviso[action],
			timeout : 2000
		});
		/*#### EXIBE SMARTBOX COM A MENSAGEM ####*/
	}
	/*#### EXIBIR MENSAGEM DE ALERTA DE UMA LINHA ####*/






	/*#### EXIBIR MENSAGEM ERRO ####*/
	else if(type=="error") {

		if(action=="DataTable") {

			var returnErrorMsg = "<pre>"+jqXHR.responseJSON+"\n"+jqXHR.responseText+"</pre>"+ErrorMsg;
		}
		else if(action=="vazio") {

			var returnErrorMsg = "O objeto de dados está vazio. Verifique se você tem permissão para acessar esta informação."+ErrorMsg;
		}
		else if(CustomMsg!='')  {

			var returnErrorMsg = CustomMsg;
		}
		else  {

			var returnErrorMsg = typeof jqXHR.responseJSON == 'object' ? jqXHR.responseJSON.ErrorMsg : "<pre>"+jqXHR.responseText+"</pre>" +"\n"+ ErrorMsg;
		}


		if(returnErrorMsg!="") {

			if(bootbox) {

				/*#### EXIBE MENSAGEM DE AVISO DO ERRO ####*/
				var dialog = bootbox.dialog({
					title: '<i class="'+icone_aviso['erro']+'"></i> '+ mensagem_aviso['erro'],
					message: returnErrorMsg,
					buttons: {
					    ok: {
					        label: "OK",
					        className: 'btn-primary',
					    }
					}
				});
				/*#### EXIBE MENSAGEM DE AVISO DO ERRO ####*/
			}

		}
	}
	/*#### EXIBIR MENSAGEM ERRO ####*/


}
/*### FUNÇÃO QUE EXIBE AS MENSAGENS DE AVISO OU ERRO ###*/











/*#### NOTE: AÇÕES DE ENVIO DO FORMULÁRIO DE CADASTRO PADRÃO ####*/
//action = ação do formulário
//formData = objeto FormData
//id_reg = id do registro que será movimentado
//IdElementDataTable = ID do elemento TABLE que contém o GRID
//DataTable = objeto DataTable
//IdElementForm = ID do elemento FORM dos cadastros principais
//ModuleDefs = objeto com todas as variáveis de configuração do módulo
//IdModalElement = ID do elemento MODAL dos cadastros principais
function ActionForm(action, formData, id_reg, IdElementDataTable, DataTable, IdElementForm, ModuleDefs, IdModalElement) {

	/*#### DEFINE O ELEMENTO PARENT DA MENSAGEM DE CARREGANDO ####*/
	if(action=="excluir") {
		var contentArea = $('#content');
		$("#DataTablesProcessingBehaviour").show();
	} else {
		var contentArea = $('#'+IdElementForm);
	}
	contentArea.css('opacity', 0.25)
	/*#### DEFINE O ELEMENTO PARENT DA MENSAGEM DE CARREGANDO ####*/


	if(action!='excluir')
		{
			/*#### CRIA A MENSAGEM DE CARREGANDO ####*/
			var loader = $('<h1 id="ActionFormProcessingBehaviour" class="ajax-loading-animation"><i class="fa fa-cog fa-spin"></i> Processando...</h1>').insertBefore(contentArea);
			loader.css({top: '50%', left: '50%', 'margin-top':'-23px', 'margin-left':'-93px' });
			/*#### CRIA A MENSAGEM DE CARREGANDO ####*/
		}


	//CRIA O OBJETO FORMDATA SE VEIO EM BRANCO
	if(formData=="") var formData = new FormData();

	//ADICIONA O ID_REG NO FORMDATA
    formData.append("id_reg", id_reg);

	//ADICIONA O ACTION NO FORMDATA
    formData.append("action", action);

	//ADICIONA O MODULEDEFS NO FORMDATA
    formData.append("ModuleDefs", JSON.stringify(ModuleDefs));

    //DEFINE A ROTA DO ACTIONFORM
    Route = (action=="login") ? "Login" : "ActionForm";

	//ADICIONA A ROTA NO FORMDATA
    formData.append("Route", Route);

	//ADICIONA O TOKEN NO FORMDATA, QUANDO NÃO É O FORM DE LOGIN
    if(Route!="Login") {
    	formData.append("Token", token);
    }

    /*### NOTE: AJAX PARA POSTAGEM DO FORMULÁRIO (ActionForm) ###*/
    $.ajax({
        type: "POST",
        dataType: "json",
        url: 'src/Controller/Controller.php',
        data: formData,
        contentType: false,
        enctype: 'multipart/form-data',
        processData: false,
        success: function(data, textStatus, jqXHR) {

        	ValidateToken(data.InvalidToken);

			// console.log("data ActionForm");
			// console.log(data);
			// console.log(typeof data[0]);

        	if(data.action!="")
	        	{

					/*### NOTE: AÇÕES QUANDO A ACTION É SELECIONAR (ActionForm) ###*/
	        		if((action=="selecionar")&&(typeof data[0]!='undefined'))
		        		{
							/*### ALIMENTA O POPOVER DE ID E HISTÓRICO DE REGISTRO ###*/
							$('#'+IdElementForm+' #id-reg').show();
							$('#'+IdElementForm+' #id_reg').html(id_reg);
							var txt_reg = "<strong>Criação:</strong> <br> "+data[0][ModuleDefs.Prefix+"reccreatedon"]+ " - "+data[0][ModuleDefs.Prefix+"reccreatedbyname"];
							txt_reg += data[0][ModuleDefs.Prefix+"recmodifiedon"]!=null ? "<br><br><strong>Última atualização:</strong> <br> "+data[0][ModuleDefs.Prefix+"recmodifiedon"]+ " - "+data[0][ModuleDefs.Prefix+"recmodifiedbyname"] : "";
							$('#'+IdElementForm+' #txt_id_reg').attr("data-content", txt_reg);
							/*### ALIMENTA O POPOVER DE ID E HISTÓRICO DE REGISTRO ###*/

							/*### SE EXISTIR UM RegisterAlertMsg, PREECHE A MENSAGEM ###*/
							if(data[0].RegisterAlertMsg) {

								$('#'+IdElementForm+' #RegisterAlertMsg').html(data[0].RegisterAlertMsg);
							}
							/*### SE EXISTIR UM RegisterAlertMsg, PREECHE A MENSAGEM ###*/

						    /*### NOTE: PERCORRE TODOS OS ELEMENTOS DO FORMULÁRIO E PREENCHE COM AS INFORMAÇÕES DA BASE (ActionForm) ###*/
		        			$.each($('#'+IdElementForm+' :input'), function(index, element) {

                                /*console.log("type: "+index+" => "+element.type);
                                console.log("name: "+index+" => "+element.name);
                                console.log("id: "+index+" => "+element.id);
                                console.log("value: "+index+" => "+element.value);
                                console.log("status: "+index+" => "+$(element).data('status'));
                                console.log("data[id]: "+data[element.id]);
                                console.log("data[name]: "+data[element.name]);
                                console.log("this.val(): "+$(this).val());
                                console.log("--------\n");*/


							    /*### MARCA SE O ELEMENTO FOR RADIO E O VALUE DO OPTION FOR O MESMO O VALOR NA BASE ###*/
                                if((element.type=="radio")&&($(element).data('status')!="NaoAtribuir"))
                                    {
                                        if($(this).val() == data[0][element.name])
                                            {
                                                $(this).prop('checked',true);
                                            }
                                        else $(this).prop('checked',false);
                                    }
							    /*### MARCA SE O ELEMENTO FOR RADIO E O VALUE DO OPTION FOR O MESMO O VALOR NA BASE ###*/





							    /*### SE FOR ELEMENTO CHECKBOX, PERCORRE O OBJETO DA BASE E MARCA OS CORRESPONDENTES ###*/
                                if((element.type=="checkbox")&&($(element).data('status')!="NaoAtribuir"))
                                    {
                                    	// console.log("\ncheckbox");
                                    	// console.log(element);
                                    	// console.log(element.name);
                                    	var element_name = element.name;
                                    	var element_name_obj = element_name.replace("[]","");
                                    	// console.log(element_name);
                                    	// console.log(element_name_obj);
                                    	var real_element = data[0][element.name] != null ? element_name : element_name_obj;
                                    	// console.log(real_element);
                                        $(this).prop('checked', false);
                                        if(data[0][real_element] != null)
                                            {
                                            	var CheckElement = $(this);
                                            	// console.log(CheckElement);
                                            	// console.log(CheckElement.attr('name'));
                                            	// console.log(real_element);
                                                $.each(data[0][real_element], function(key, value) {
                                                    // console.log('--');
                                                    // console.log(key);
                                                    // console.log(value);
                                                    if(CheckElement.val() == value)
                                                        {
                                                            // console.log("#"+element.id+" é true");
                                                            CheckElement.prop('checked', true);
                                                        }
                                                });
                                            }
                                    }
							    /*### SE FOR ELEMENTO CHECKBOX, PERCORRE O OBJETO DA BASE E MARCA OS CORRESPONDENTES ###*/



							    /*### SETA O VALOR DA BASE PARA OS DEMAIS ELEMENTOS, QUE NÃO ESTÃO MARCADOS PARA NÃO ATRIBUIR ###*/
                                else if(($(element).data('status')!="NaoAtribuir")&&(element.type!="radio")&&(element.type!="checkbox"))
                                    {
                                        // console.log("\nDEMAIS ELEMENTOS");
                                        // console.log($(this));
                                        // console.log(element.id);
                                        // console.log($(element).data('status'));
                                        // console.log(data[0][element.id]);
                                        $(this).val(data[0][element.id]).trigger('change');
                                    }
							    /*### SETA O VALOR DA BASE PARA OS DEMAIS ELEMENTOS, QUE NÃO ESTÃO MARCADOS PARA NÃO ATRIBUIR ###*/

		        			});
						    /*### PERCORRE TODOS OS ELEMENTOS DO FORMULÁRIO E PREENCHE COM AS INFORMAÇÕES DA BASE ###*/

		        		}
					/*### AÇÕES QUANDO A ACTION É SELECIONAR ###*/






					/*#### NOTE: AÇÕES QUANDO A ACTION É INSERIR OU ALTERAR (ActionForm) ####*/
					else if((action=="inserir")||(action=="alterar")) {

						// console.log(data);

						if((data.inserir=="OK")||(data.alterar=="OK")) {

							//ESCONDE O MODAL DE CADASTRO
							$("#"+IdModalElement).modal('hide');


							/*### EXIBE OU NÃO A MENSAGEM DE RETORNO DE ACORDO COM O ShowAlertMsg ###*/
							if(data.Options!=null) {

								if(data.Options["ShowAlertMsg"]!=false) {

									//EXIBE A MENSAGEM DE RESULTADO
									AlertMsg(action, 'alert', jqXHR, '', '', '');
								}
							}
							else {

									//EXIBE A MENSAGEM DE RESULTADO
									AlertMsg(action, 'alert', jqXHR, '', '', '');
							}
							/*### EXIBE OU NÃO A MENSAGEM DE RETORNO DE ACORDO COM O ShowAlertMsg ###*/


							//RECARREGA O GRID
							if(DataTable) {
								DataTable.ajax.reload(null,false);
							}

						}
						else {

							//EXIBE A MENSAGEM DE ERRO
							AlertMsg(action, 'error', jqXHR, data.ErrorMsg, '');
						}
					}
					/*#### AÇÕES QUANDO A ACTION É INSERIR OU ALTERAR ####*/





					/*#### NOTE: AÇÕES QUANDO A ACTION É UMA OUTRA OPÇÃO DIFERENCIADA (ActionForm) ####*/
					else if((action=="alterar_permissoes")||
							(action=="copiar_para_evento")||
							(action=="alterar_parceiros")||
							(action=="transferir_usuarios")||
							(action=="excluir_selecionados")) {

						// console.log(data);

						if((data.alterar_permissoes=="OK")||
							(data.copiar_para_evento=="OK")||
							(data.alterar_parceiros=="OK")||
							(data.transferir_usuarios=="OK")||
							(data.excluir_selecionados=="OK")) {

							//ESCONDE O MODAL DE CADASTRO
							$("#"+IdModalElement).modal('hide');

							/*### EXIBE OU NÃO A MENSAGEM DE RETORNO DE ACORDO COM O ShowAlertMsg ###*/
							if(data.Options!=null) {

								if(data.Options["ShowAlertMsg"]!=false) {

									//EXIBE A MENSAGEM DE RESULTADO
									AlertMsg(action, 'alert', jqXHR, '', '', '');
								}
							}
							else {

									//EXIBE A MENSAGEM DE RESULTADO
									AlertMsg(action, 'alert', jqXHR, '', '', '');
							}
							/*### EXIBE OU NÃO A MENSAGEM DE RETORNO DE ACORDO COM O ShowAlertMsg ###*/


							//RECARREGA O GRID
							if(DataTable) {
								DataTable.ajax.reload(null,false);
							}

						}
						else {

							//EXIBE A MENSAGEM DE ERRO
							AlertMsg(action, 'error', jqXHR, data.ErrorMsg, '');
						}
					}
					/*#### AÇÕES QUANDO A ACTION É UMA OUTRA OPÇÃO DIFERENCIADA ####*/








					/*#### NOTE: SE A AÇÃO FOR DE EXCLUIR FADEOUT NA LINHA (ActionForm) ####*/
					else if(action=='excluir') {

						if(data.excluir=="OK") {

							//REMOVE A LINHA EXCLUÍDA DO GRID
							$("#"+IdElementDataTable+" tr[id="+id_reg+"]").fadeOut();

							/*### EXIBE OU NÃO A MENSAGEM DE RETORNO DE ACORDO COM O ShowAlertMsg ###*/
							if(data.Options!=null) {

								if(data.Options["ShowAlertMsg"]!=false) {

									//EXIBE A MENSAGEM DE RESULTADO
									AlertMsg(action, 'alert', jqXHR, '', '', '');
								}
							}
							else {

									//EXIBE A MENSAGEM DE RESULTADO
									AlertMsg(action, 'alert', jqXHR, '', '', '');
							}
							/*### EXIBE OU NÃO A MENSAGEM DE RETORNO DE ACORDO COM O ShowAlertMsg ###*/


							//RECARREGA O GRID
							if(DataTable) {
								DataTable.ajax.reload(null,false);
							}
						}
						else {

							if(data.Options!=null) {

								if(data.Options["ShowAlertMsg"]!=false) {

									//EXIBE A MENSAGEM DE RESULTADO
									AlertMsg(action, 'error', jqXHR, data.ErrorMsg, '');
								}
							}
							else {

									//EXIBE A MENSAGEM DE RESULTADO
									AlertMsg(action, 'error', jqXHR, '', '');
							}

						}
					}
					/*#### SE A AÇÃO FOR DE EXCLUIR FADEOUT NA LINHA ####*/


					/*#### NOTE: SE NÃO ATENDER NENHUMA CONDIÇÃO, RETORNA MESAGEM DE ERRO (ActionForm) ####*/
					else {

							/*### EXIBE OU NÃO A MENSAGEM DE RETORNO DE ACORDO COM O ShowAlertMsg ###*/
							if(data.Options!=null) {

								if(data.Options["ShowAlertMsg"]!=false) {

									/*#### RETORNA MESAGEM DE ERRO, SE FOI SETADA ####*/
									if(data.ErrorMsg!="") {

										//EXIBE A MENSAGEM DE ERRO
										AlertMsg(action, 'error', jqXHR, data.ErrorMsg, '');
									}
									/*#### RETORNA MESAGEM DE ERRO, SE FOI SETADA ####*/

									/*#### SE A ACTION FOR SELECIONAR O OBJETO FOR VAZIO, RETORNA ERRO ####*/
									if((action=="selecionar")
										&&(typeof data[0]=='undefined')
										&&(data.origem!='importar')) {

										//EXIBE A MENSAGEM DE ERRO
										AlertMsg('vazio', 'error', '', '', '');

										//ESCONDE O MODAL DE CADASTRO
										$("#"+IdModalElement).modal('hide');
									}
									/*#### SE A ACTION FOR SELECIONAR O OBJETO FOR VAZIO, RETORNA ERRO ####*/

								}
							}
							else {

									/*#### RETORNA MESAGEM DE ERRO, SE FOI SETADA ####*/
									if(data.ErrorMsg!="") {

										//EXIBE A MENSAGEM DE ERRO
										AlertMsg(action, 'error', jqXHR, data.ErrorMsg, '');
									}
									/*#### RETORNA MESAGEM DE ERRO, SE FOI SETADA ####*/

									/*#### SE A ACTION FOR SELECIONAR O OBJETO FOR VAZIO, RETORNA ERRO ####*/
									if((action=="selecionar")
										&&(typeof data[0]=='undefined')
										&&(data.origem!='importar')) {

										//EXIBE A MENSAGEM DE ERRO
										AlertMsg('vazio', 'error', '', '', '');

										//ESCONDE O MODAL DE CADASTRO
										$("#"+IdModalElement).modal('hide');
									}
									/*#### SE A ACTION FOR SELECIONAR O OBJETO FOR VAZIO, RETORNA ERRO ####*/
							}
							/*### EXIBE OU NÃO A MENSAGEM DE RETORNO DE ACORDO COM O ShowAlertMsg ###*/

					}
					/*#### SE NÃO ATENDER NENHUMA CONDIÇÃO, RETORNA MESAGEM DE ERRO ####*/


				    //ACIONA FUNÇÃO PARA AÇÕES ADICIONAIS
			    	AdditionalActionForm(IdElementForm, action, data, textStatus, jqXHR, DataTable, ModuleDefs, IdModalElement);


					/*#### REMOVE A MENSAGEM DE CARREGANDO ####*/
					if(contentArea) {
						contentArea.css('opacity', 1)
						contentArea.prevAll('#ActionFormProcessingBehaviour').remove();
				    	$("#DataTablesProcessingBehaviour").hide();
					}
					/*#### REMOVE A MENSAGEM DE CARREGANDO ####*/
	        	}
        },
	    error: function (jqXHR, status, error) {

			//EXIBE A MENSAGEM DE ERRO
			AlertMsg(action, 'error', jqXHR, error, '');

			/*#### REMOVE A MENSAGEM DE CARREGANDO ####*/
			if(contentArea) {
				contentArea.css('opacity', 1)
				contentArea.prevAll('#ActionFormProcessingBehaviour').remove();
		    	$("#DataTablesProcessingBehaviour").hide();
			}
			/*#### REMOVE A MENSAGEM DE CARREGANDO ####*/
	    }
    });
    /*### AJAX PARA POSTAGEM DO FORMULÁRIO ###*/

}
/*#### AÇÕES DE ENVIO DO FORMULÁRIO DE CADASTRO PADRÃO ####*/




/*#### NOTE: FUNÇÃO QUE ADICIONA CLASSES CSS NOS ELEMENTOS DO FORM NA VALIDAÇÃO ####*/
//error = elemento error dentro do $.ajax
//element = elemento element dentro do $.ajax
function ValidateErrorPlacement(error, element) {


	/*#### QUANDO CHECKBOX OU RADIO ####*/
	if(element.is('input[type=checkbox]') || element.is('input[type=radio]')) {

		//OBTÉM A ÁREA DIV COM CLASS form-goup
		var controls = element.closest('div[class*="form-group"]');

		//SE TIVER MAIS DE UMA OPÇÃO, INSERE O DIV DE ERRO DENTRO DO controls
		if(controls.find(':checkbox,:radio').length > 1) controls.append(error);

		//SENÃO INSERE DEPOIS DO ÚLTIMO ELEMENTO COM CLASS CSS lbl
		else error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
	}
	/*#### QUANDO CHECKBOX OU RADIO ####*/


	/*#### SELECT2 ####*/
	else if(element.is('.select2')) {
		error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
	}
	/*#### SELECT2 ####*/


	/*#### CHOSEN ####*/
	else if(element.is('.chosen-select')) {
		error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
	}
	/*#### CHOSEN ####*/


	/*#### INPUT ####*/
	else if(element.is('.input-group')) {
		error.insertAfter(element.parent());
	}
	/*#### INPUT ####*/


	/*#### DATEPICKER ####*/
	else if(element.is('.datepicker')) {
		error.insertAfter(element.parent());
	}
	/*#### DATEPICKER ####*/


	/*#### SPINNER ####*/
	else if(element.is('.spinner')) {
		error.insertAfter(element.parent());
	}
	/*#### SPINNER ####*/


	/*#### OUTROS ELEMENTOS ####*/
	else {
		error.insertAfter(element);
	}
	/*#### OUTROS ELEMENTOS ####*/

}
/*#### FUNÇÃO QUE ADICIONA CLASSES CSS NOS ELEMENTOS DO FORM NA VALIDAÇÃO ####*/















/*#### NOTE: FUNÇÃO QUE ADICIONA CLASSES HAS-ERROR NOS ELEMENTOS DO FORM NA VALIDAÇÃO ####*/
//e = elemento e dentro do $.ajax
function ValidateHighlight(e) {

	// console.log(e);
	$(e).closest('.form-group').removeClass('has-info').addClass('has-error');
}
/*#### FUNÇÃO QUE ADICIONA CLASSES HAS-ERROR NOS ELEMENTOS DO FORM NA VALIDAÇÃO ####*/













/*#### NOTE: FUNÇÃO QUE REMOVE CLASSES HAS-ERROR NOS ELEMENTOS DO FORM NA VALIDAÇÃO ####*/
//e = elemento e dentro do $.ajax
function ValidateSuccess(e) {

	$(e).closest('.form-group').removeClass('has-error');
	$(e).remove();
}
/*#### FUNÇÃO QUE REMOVE CLASSES HAS-ERROR NOS ELEMENTOS DO FORM NA VALIDAÇÃO ####*/





















/*#### NOTE: FUNÇÃO QUE RETORNA O CONTEÚDO DE UMA TABELA NO FORMATO DE TABELA DINÂMICA (Código|Descricao) ####*/
//DynamicTable = nome da tabela dinâmica
//FieldToOrder = campo que será ordenado (Codigo | Descricao) - opcional
//Order = ordem de classificação da tabela - opcional
//NOTE: OtherTable = objeto de dados para consulta em outras tabelas diferentes da TabelasDinamicas (GetDynamicTable) (Table: nome da tabela
//																														Prefix: prefixo dos campos da tabela
//																														Key: campo que será o Codigo
//																														OtherKey: indica se a Key será padrão ou outro campo (se TRUE o Key deve ser o campo completo inclusive com o alias)
//																														Desc: campo que será a Descricao
//																														OtherDesc: indica se o campo de Desc será padrão ou outro campo (se TRUE o Desc deve ser o campo completo inclusive com o alias, formatação ou concatenação)
//																														MultipleDesc: campos para o objeto de dados, quando houver mais de um campo para descrição (deve-se escrever todos os campos, com ALIAS e formatações ou concatenações - se ativo, Desc o OtherDesd ficam inativos - opcional)
//																														Condition: condições adicionais where
//																														FieldToOrder: campo que será ordenado
//																														OtherOrder: indica se o campo de Ordenação será padrão ou outro personalizado (se TRUE o FieldToOrder deve ser a descrição completa da ordenação, inclusive com a classificação)
//																														Order: orderm de classificação
//																														Join: sintaxe completa de JOIN com outras tabelas (opcional)
//																														Distinct: determina se o Select será DISTINCT - true para usar
//																														OptGroup: opção para agrupar itens relacionando a tabela com ela própria
//																																				Field: campo na tabela principal que será agrupado
//																																				Key: campo na tabela destino que será o value do select
//																																				Label: campo do Label do OptGroup
//																																				FieldToOrder: campo de ordenação do agrupamento
//																																				Order: ordem de classificação do agrupamento
function GetDynamicTable(DynamicTable, FieldToOrder, Order, OtherTable) {

	/*### EXECUTA A REQUISIÇÃO AJAX E RETORNA COMO RESULTADO ###*/
    return $.ajax({
        type: "POST",
        dataType: "json",
        url: 'src/Controller/Controller.php',
        data: {
        	Route: 'DynamicTable',
        	FieldToOrder: FieldToOrder,
        	Order: Order,
        	DynamicTable: DynamicTable,
        	OtherTable: OtherTable,
        	Token: token,
        }
    });
    /*### EXECUTA A REQUISIÇÃO AJAX E RETORNA COMO RESULTADO ###*/

}
/*#### NOTE: FUNÇÃO QUE RETORNA O CONTEÚDO DE UMA TABELA NO FORMATO DE TABELA DINÂMICA (Código|Descricao) ####*/

















/*### NOTE: FUNÇÃO QUE INSERE NO ELEMENTO SELECT OS DADOS DE UMA ORIGEM (SetSelectFromDynamicTable) ###*/
//IdElementSelect = ID do element SELECT que vai receber o resultado
//DynamicTable = objeto de dados JSON do resultado da GetDynamicTable
//Placeholder = texto do placeholder que vai ter valor nulo
//NullText = opção adicional com valor nulo
//InitialValue = valor inicial do select2
function SetSelectFromDynamicTable(IdElementSelect, DynamicTable, Placeholder, NullText, InitialValue) {

	// console.log('InitialValue');
	// console.log(IdElementSelect);
	// console.log(DynamicTable);
	// console.log(Placeholder);
	// console.log(NullText);
	// console.log(InitialValue);

	//REMOVE TODAS AS OPÇÕES EXISTENTES NO CAMPO ATUAL
	$("#"+IdElementSelect).find('option').remove().end();

	/*### INSERE A OPÇÃO DE PLACEHOLDER, SE EXISTIR ###*/
	if(Placeholder!='') {

		/*### SE O ELEMENTO FOR SELECT2 ###*/
		if($("#"+IdElementSelect).hasClass('select2')==true)
			{
				/*### ADICIONA O OPTION ###*/
				$("#"+IdElementSelect).select2({
					allowClear: true,
					placeholder: {
					      id: "-1",
					      text: Placeholder,
					      selected:'selected'
					    }
				});
				/*### ADICIONA O OPTION ###*/
			}
		/*### SE O ELEMENTO FOR SELECT2 ###*/

		// ADICIONA O OPTION PLACEHOLDER
		$("#"+IdElementSelect).append($('<option>', { value: '', text: Placeholder }));
	}
	/*### INSERE A OPÇÃO DE PLACEHOLDER, SE EXISTIR ###*/


	/*### INSERE A OPÇÃO DE NULLTEXT, SE EXISTIR ###*/
	if(NullText!='') {

		/*### SE O ELEMENTO FOR SELECT2 ###*/
		if($("#"+IdElementSelect).hasClass('select2')==true)
			{
				/*### ADICIONA O OPTION ###*/
				$("#"+IdElementSelect).select2({
					allowClear: true,
					placeholder: {
					      id: "-1",
					      text: NullText,
					    }
				});
				/*### ADICIONA O OPTION ###*/
			}
		/*### SE O ELEMENTO FOR SELECT2 ###*/

		// ADICIONA O OPTION NULLTEXT
		$("#"+IdElementSelect).append($('<option>', { value: '', text: NullText }));
	}
	/*### INSERE A OPÇÃO DE NULLTEXT, SE EXISTIR ###*/


	/*### ADICIONA CADA OPÇÃO NO ELEMENTO SELECT, SE HOUVER DADOS DynamicTable ###*/
	if(DynamicTable) {

		DynamicTable.success(function (data) {

			if(data.OptGroup==true) {

				$.each(data["Options"], function(key, label ) {

					if(data["Groups"][key]) {

						var Group = $('<optgroup/>');
						Group.attr('label', label);
						$.each(data["GroupData"][key], function(keyGroup, labelGroup ) {

							Group.append($('<option>', { value: keyGroup, text: labelGroup }));
						});

						$("#"+IdElementSelect).append(Group);

					} else {

						$("#"+IdElementSelect).append($('<option>', { value: key, text: label }));
					}
				});


			} else {

				$.each(data, function(key, DataObject ) {

					$("#"+IdElementSelect).append($('<option>', { value: DataObject.codigo, text: DataObject.descricao }));
				});
			}

			/*### SELECIONA O ITEM DEFINIDO NO InitialValue, QUANDO INFORMADO ###*/
			if(InitialValue!="") {

				if($("#"+IdElementSelect).hasClass('select2')==true) {

					$("#"+IdElementSelect).val(InitialValue).trigger('change.select2');

				} else {

					$("#"+IdElementSelect).val(InitialValue);
				}

				//VALIDA O ELEMENTO, SE NECESSÁRIO
				if($("#"+IdElementSelect).closest('form').length>0) {

					$("#"+IdElementSelect).closest('form').validate().element($("#"+IdElementSelect));
				}
			}
			/*### SELECIONA O ITEM DEFINIDO NO InitialValue, QUANDO INFORMADO ###*/

		});
	}
	/*### ADICIONA CADA OPÇÃO NO ELEMENTO SELECT, SE HOUVER DADOS DynamicTable ###*/
}
/*### FUNÇÃO QUE INSERE NO ELEMENTO SELECT OS DADOS DE UMA ORIGEM ###*/





/*### NOTE: FUNÇÃO QUE MARCA OS CAMPOS OBRIGATÓRIOS DE UM FORMULÁRIO COM UMA CLASSE CSS ###*/
//IdElementForm = ID do elemento FORM dos cadastros principais
function MarkRequiredFields (IdElementForm) {

	$.each($("#"+IdElementForm+" :input").not(':button'), function(index, element) {

		if($(element).prop('required')==true) {

			$("label[for="+element.name+"]").addClass('required-field');
		}
	});
}
/*### FUNÇÃO QUE MARCA OS CAMPOS OBRIGATÓRIOS DE UM FORMULÁRIO COM UMA CLASSE CSS ###*/







/*### NOTE: FUNÇÃO QUE MARCA OS CAMPOS (COM VALIDAÇÃO) OBRIGATÓRIOS DE UM FORMULÁRIO COM UMA CLASSE CSS ###*/
//IdElementForm = ID do elemento FORM dos cadastros principais
function MarkRequiredFieldsValidation (IdElementForm) {

	$.each($("#"+IdElementForm+" :input").not(':button'), function(index, element) {

		// console.log('MarkRequiredFieldsValidation');
		// console.log(index);
		// console.log(element);
		// console.log($(element));
		// console.log($(element).prop('required'));
		// console.log($(element).attr('required'));
		// console.log(typeof $(element).rules());
		// console.log($(element).rules());
		// console.log($(element).rules().required);

		if(($(element).prop('required')==true)||($(element).rules().required==true)) {

			$("label[for="+element.name+"]").addClass('required-field');
		}
	});
}
/*### FUNÇÃO QUE MARCA OS CAMPOS (COM VALIDAÇÃO) OBRIGATÓRIOS DE UM FORMULÁRIO COM UMA CLASSE CSS ###*/







/*### NOTE: FUNÇÃO QUE GERA RADIO OU CHECKBOX A PARTIR DO RESULTADO DE UMA TABELA DINÂMICA ###*/
//IdElementRadioCheckGroup = ID do elemento radio-group ou checkbox-group onde serão exibidas as opções
//FieldLabel = label do campo para exibir na tela
//FieldName = nome do campo (igual ao banco de dados)
//FieldType = tipo de campo (radio | checkbox)
//DynamicTable = objeto de dados DynamicTable com as informações da tabela dinâmica
//Required = indica se o campo é obrigatório (true | false)
//ValidationOptions = informações adicionais de validação
//InitialValue = valor inicial que virá marcado
function CreateCheckRadioFromDynamicTable(IdElementRadioCheckGroup, FieldLabel, FieldName, FieldType, DynamicTable, Required, ValidationOptions, InitialValue) {

	// console.log("CreateCheckRadioFromDynamicTable");
	// console.log(IdElementRadioCheckGroup);
	// console.log(FieldLabel);
	// console.log(FieldName);
	// console.log(FieldType);
	// console.log(DynamicTable);
	// console.log(Required);
	// console.log(ValidationOptions);
	// console.log(InitialValue);

	//SE O CAMPO FOR OBRIGATÓRIO, ADICIONA A CLASSE REQUIRED-FIELD
	var RequiredClass = Required=='required' ? 'required-field' : "";

	//GERA O LABEL
	$("#"+IdElementRadioCheckGroup).append('<label for="'+FieldName+'" class="control-label radio-inline no-padding '+RequiredClass+'">'+FieldLabel+'</label>');

	/*### NOTE: SE FOR RADIO, GERA AS OPÇÕES (CreateCheckRadioFromDynamicTable) ###*/
	if(FieldType=='radio') {

		//ARRAY COM AS CLASSES CSS ADICIONAIS DE ACORDO COM CADA REGISTRO
		var LabelClass = ["no-margin"];

		/*### GERA AS OPÇÕES DE RADIO COM BASE NO RETORNO DA TABELA DINÂMICA ###*/
		DynamicTable.success(function (data) {

			$.each(data, function(key, DataObject ) {

				if(typeof DataObject == "object") {

					var AddLabelClass = LabelClass[key]!=null ? LabelClass[key] : "";

					$("#"+IdElementRadioCheckGroup).append('<label class="radio radio-inline '+AddLabelClass+' ">\
																<input type="radio" name="'+FieldName+'" id="'+FieldName+'_'+DataObject.codigo+'" value="'+DataObject.codigo+'" class="radiobox style-2" '+Required+' '+ValidationOptions+' />\
																<span>'+DataObject.descricao+'</span>\
															</label>');
					if(InitialValue==DataObject.codigo) {

						$("#"+FieldName+"_"+DataObject.codigo).prop("checked",true);
					}
				}

			});

		});
		/*### GERA AS OPÇÕES DE RADIO COM BASE NO RETORNO DA TABELA DINÂMICA ###*/
	}
	/*### SE FOR RADIO, GERA AS OPÇÕES ###*/


	/*### NOTE: SE FOR CHECKBOX, GERA AS OPÇÕES (CreateCheckRadioFromDynamicTable) ###*/
	else if(FieldType=='checkbox') {

		//ARRAY COM AS CLASSES CSS ADICIONAIS DE ACORDO COM CADA REGISTRO
		var LabelClass = ["no-margin"];

		/*### GERA AS OPÇÕES DE RADIO COM BASE NO RETORNO DA TABELA DINÂMICA ###*/
		DynamicTable.success(function (data) {

			$.each(data, function(key, DataObject ) {

				if(typeof DataObject == "object") {

					var AddLabelClass = LabelClass[key]!=null ? LabelClass[key] : "";

					$("#"+IdElementRadioCheckGroup).append('<label class="checkbox-inline '+AddLabelClass+' ">\
																<input type="checkbox" name="'+FieldName+'[]" id="'+FieldName+'_'+DataObject.codigo+'" value="'+DataObject.codigo+'" class="checkbox style-2" '+Required+' '+ValidationOptions+' />\
																<span>'+DataObject.descricao+'</span>\
															</label>');
				}
			});

		});
		/*### GERA AS OPÇÕES DE RADIO COM BASE NO RETORNO DA TABELA DINÂMICA ###*/
	}
	/*### SE FOR CHECKBOX, GERA AS OPÇÕES ###*/


}
/*### FUNÇÃO QUE GERA RADIO OU CHECKBOX A PARTIR DO RESULTADO DE UMA TABELA DINÂMICA ###*/






/*### NOTE: FUNÇÃO DE MÁSCARA DE VALORES - CPF ###*/
//v = objeto do campo de formulário a ser tratado
function MaskCPF(v){

	var value = v.value;

    value = value.replace(/\D/g,"")                    //Remove tudo o que não é dígito
    value = value.replace(/(\d{3})(\d)/,"$1.$2")       //Coloca um ponto entre o terceiro e o quarto dígitos
    value = value.replace(/(\d{3})(\d)/,"$1.$2")       //Coloca um ponto entre o terceiro e o quarto dígitos de novo (para o segundo bloco de números)
    value = value.replace(/(\d{2})(\d{1,2})$/,"$1-$2") //Coloca um hífen entre o terceiro e o quarto dígitos

    v.value = value;
}
/*### FUNÇÃO DE MÁSCARA DE VALORES - CPF ###*/




/*### NOTE: FUNÇÃO DE MÁSCARA DE VALORES - CNPJ ###*/
//v = objeto do campo de formulário a ser tratado
function MaskCNPJ(v){

	var value = v.value;

    value = value.replace(/\D/g,"")                    //Remove tudo o que não é dígito
    value = value.replace(/(\d{2})(\d)/,"$1.$2")       //Coloca um ponto entre o segundo e o terceiro dígitos
    value = value.replace(/(\d{3})(\d)/,"$1.$2")       //Coloca um ponto entre o terceiro e o quarto dígitos de novo (para o segundo bloco de números)
    value = value.replace(/(\d{3})(\d)/,"$1/$2")       //Coloca uma ponto entre o terceiro e o quarto dígitos de novo (para o segundo bloco de números)
    value = value.replace(/(\d{4})(\d{1,2})$/,"$1-$2") //Coloca um hífen entre o terceiro e o quarto dígitos (no último bloco)

    v.value = value;
}
/*### FUNÇÃO DE MÁSCARA DE VALORES - CNPJ ###*/





/*### NOTE: FUNÇÃO DE MÁSCARA DE VALORES - TELEFONE ###*/
//v = objeto do campo de formulário a ser tratado
function MaskTel(v, event){

	var value = v.value;

    if(value.length==14) {
	    value = value.replace(/\D/g,"")                    //Remove tudo o que não é dígito
	    value = value.replace(/(\d{2})(\d)/,"($1) $2")     //Coloca os parenteses nos dois primeiros dígitos
	    value = value.replace(/(\d{4})(\d)/,"$1-$2")       //Se tiver 14 dígitos separa o telefone com 4 - 4 dígitos
	} else {

	    value = value.replace(/\D/g,"")                    //Remove tudo o que não é dígito
	    value = value.replace(/(\d{2})(\d)/,"($1) $2")     //Coloca um ponto entre o terceiro e o quarto dígitos
	    value = value.replace(/(\d{5})(\d)/,"$1-$2")       //Se tiver 15 dígitos separa o telefone com 5 - 4 dígitos
	}

    v.value = value;
}
/*### FUNÇÃO DE MÁSCARA DE VALORES - TELEFONE ###*/



/*### NOTE: FUNÇÃO DE MÁSCARA DE VALORES - DATA (DD/MM/YYYY) ###*/
//v = objeto do campo de formulário a ser tratado
function MaskData(v){

	var value = v.value;

    value = value.replace(/\D/g,"")                    	//Remove tudo o que não é dígito
    value = value.replace(/(\d{2})(\d)/,"$1/$2")		//Coloca uma barra entre o segundo e terceiro dígito
    value = value.replace(/(\d{2})(\d)/,"$1/$2")		//Coloca uma barra entre o quarto e quinto dígito

    v.value = value;
}
/*### FUNÇÃO DE MÁSCARA DE VALORES - DATA (DD/MM/YYYY) ###*/




/*### NOTE: FUNÇÃO DE MÁSCARA DE VALORES - CEP ###*/
//v = objeto do campo de formulário a ser tratado
function MaskCep(v){

	var value = v.value;

    value = value.replace(/\D/g,"")                    	//Remove tudo o que não é dígito
    value = value.replace(/(\d{5})(\d)/,"$1-$2")		//Coloca um traço entre o quinto e o sexto dígito

    v.value = value;
}
/*### FUNÇÃO DE MÁSCARA DE VALORES - CEP ###*/





/*### NOTE: FUNÇÃO DE MÁSCARA DE VALORES - LOGIN ###*/
//v = objeto do campo de formulário a ser tratado
function MaskLogin(v){

	var value = v.value;

    value = value.replace(/[^a-zA-Z0-9.]/g,'') //Permite apenas letras, números e ponto

    v.value = value;
}
/*### FUNÇÃO DE MÁSCARA DE VALORES - LOGIN ###*/





/*### NOTE: FUNÇÃO DE MÁSCARA DE VALORES - MOEDA ###*/
function MaskCurrency(v){

	var value = v.value;

    value = value.replace(/\D/g,"");//Remove tudo o que não é dígito
    value = value.replace(/(\d)(\d{8})$/,"$1.$2");//coloca o ponto dos milhões
    value = value.replace(/(\d)(\d{5})$/,"$1.$2");//coloca o ponto dos milhares

    value = value.replace(/(\d)(\d{2})$/,"$1,$2");//coloca a virgula antes dos 2 últimos dígitos

    v.value = value;
}
/*### FUNÇÃO DE MÁSCARA DE VALORES - MOEDA ###*/





/*### NOTE: FUNÇÃO DE MÁSCARA DE VALORES - MOEDA - PROTOTYPE ###*/
Number.prototype.formatMoney = function(c, d, t){
var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};
/*### FUNÇÃO DE MÁSCARA DE VALORES - MOEDA - PROTOTYPE ###*/



/*### NOTE: FUNÇÃO DE MÁSCARA DE VALORES - CÓDIGO DA TABELA DINÂMICA ###*/
//v = objeto do campo de formulário a ser tratado
function MaskTabeDina(v){

	var value = v.value;

    value = value.replace(/[^a-zA-Z0-9_]/g,'') //Permite apenas letras, números e underscore

    v.value = value;
}
/*### FUNÇÃO DE MÁSCARA DE VALORES - CÓDIGO DA TABELA DINÂMICA ###*/






/*### NOTE: FUNÇÃO QUE VERIFICA SE O TOKEN É VALIDO E CARREGA PÁGINA DE AVISO DO TOKEN INVÁLIDO ###*/
function ValidateToken(InvalidToken) {

	// console.log("InvalidToken");
	// console.log(InvalidToken);

	if(typeof InvalidToken == 'object') {

		if(InvalidToken.responseJSON) {

			var parseInvalidToken = InvalidToken.responseJSON.InvalidToken;
		} else if (InvalidToken.responseText) {

			if(InvalidToken.responseText.search('The JWT string must have two dots')>0) var parseInvalidToken = true;
		}
	}
	else {

		var parseInvalidToken = InvalidToken
	}

	// console.log(parseInvalidToken);

	if(parseInvalidToken==true) {

		$("#DataTablesProcessingBehaviour").hide();
		window.location.href = 'home.html#invalid-token.html';
	}
}
/*### FUNÇÃO QUE VERIFICA SE O TOKEN É VALIDO E CARREGA PÁGINA DE AVISO DO TOKEN INVÁLIDO ###*/








/*### NOTE: FUNÇÃO QUE GERA A TABELA DE PERMISSÕES DE TODOS OS MÓDULOS ###*/
//IdPermissionsTable = ID do elemento da tabela de permissões
//IdSpecialPermissions = ID do elemento DIV de permissões especiais
function GetModulesPermissions(IdPermissionsTable,IdSpecialPermissions)
	{

	    /*### ENVIAR A REQUISIÇÃO COM A ROTA MODULESPERMISSIONS ###*/
	    $.ajax({
	        type: "POST",
	        dataType: "json",
	        url: 'src/Controller/Controller.php',
	        data: {
	        	Route: 'ModulesPermissions',
	        	Token: token,
	        },
	        success: function(data, textStatus, jqXHR) {

	        	//VALIDAR O TOKEN
	        	ValidateToken(data.InvalidToken);



			    /*### RETORNA AS COLUNAS DAS OPERAÇÕES ###*/
	        	if(data.Operations!="") {

	        		var OperationsColumns;
	        		$.each(data.Operations, function(index, OperationData) {

	        			OperationsColumns += '<th id="'+OperationData.operacao+'" class="text-center coluna-operacao" title="Clique para ativar marcar esta operação em todos os Módulos" style="cursor:pointer;"><i class="fa fa-'+OperationData.operacao_icone+'"></i> '+OperationData.operacao_titulo+'</th>';
	        		});

	        		$("#"+IdPermissionsTable+" thead").append('<tr><th>Módulo</th>'+OperationsColumns+"</tr>");
	        	}
			    /*### RETORNA AS COLUNAS DAS OPERAÇÕES ###*/



			    /*### MONTA AS LINHAS DOS MÓDULOS COM AS COLUNAS DE TODAS AS OPERAÇÕES ###*/
	        	if(data.Modules!="") {

	        		var ModulesLines;

	        		$.each(data.Modules, function(index, ModuleData) {

	        			var OperationsColumns;

	        			$.each(data.Operations, function(index, OperationData) {

	        				OperationsColumns += '<td id="Column_'+ModuleData.modulo+'_'+OperationData.operacao+'" class="text-center"></td>';
	        			});

	        			ModulesLines += '<tr><td id="'+ModuleData.modulo+'" title="Clique para marcar todas as operações deste Módulo" style="cursor:pointer;" class="linha-modulo"><i class="fa fa-'+ModuleData.modulo_icone+'"></i> '+ModuleData.modulo_titulo+'</td>'+OperationsColumns+'</tr>';
	        		});

	        		$("#"+IdPermissionsTable+" tbody").append(ModulesLines);
	        	}
			    /*### MONTA AS LINHAS DOS MÓDULOS COM AS COLUNAS DE TODAS AS OPERAÇÕES ###*/




			    /*### INSERE OS CHECKBOX DE PERMISSÕES PARA CADA MÓDULO ###*/
	        	if(data.ModulesOperations!="") {

	        		$.each(data.ModulesOperations, function(index, ModulesOperationsData) {

	        			$("#"+IdPermissionsTable+" tbody #Column_"+ModulesOperationsData.modulo+"_"+ModulesOperationsData.operacao).append('<label><input type="checkbox" name="permissoes['+ModulesOperationsData.modulo+']['+ModulesOperationsData.operacao+']" id="permissoes_'+ModulesOperationsData.modulo+'_'+ModulesOperationsData.operacao+'" data-module="'+ModulesOperationsData.modulo+'" data-operation="'+ModulesOperationsData.operacao+'" value="1" class="checkbox style-2" /><span></span></label>');
	        		});

	        	}
			    /*### INSERE OS CHECKBOX DE PERMISSÕES PARA CADA MÓDULO ###*/



			    /*### CRIA A TABELA DE PERMISSÕES ESPECIAIS, SE INDICADA ###*/
			    if(IdSpecialPermissions!='') {

		        	if(data.SpecialPermissions!="") {

		        		$.each(data.SpecialPermissions, function(index, SpecialPermissionsData) {

		        			$("#"+IdSpecialPermissions).append('<div class="col-md-4"><label><input type="checkbox" name="permissoes-especiais[]" id="permissoes_especiais_'+SpecialPermissionsData.permissao_valor+'" value="'+SpecialPermissionsData.permissao_valor+'" class="checkbox style-2" /><span>'+SpecialPermissionsData.permissao_titulo+'</span></label></div>');
		        		});

		        	}
			    }
			    /*### CRIA A TABELA DE PERMISSÕES ESPECIAIS, SE INDICADA ###*/




			    /*### AÇÃO QUANDO CLICA NA COLUNA DA OPERAÇÃO (MARCA/DESMARCA OS CHECKBOX) ###*/
	        	$(".coluna-operacao").on('click', function(event) {

	        		var Operation = $(this).attr('id');

	        		$.each($("#"+IdPermissionsTable+" :input[type=checkbox][data-operation="+Operation+"]"), function(index, element) {

	        				if($(this).prop('checked')==false) $(this).prop('checked',true);
	        				else $(this).prop('checked',false);
	        		});
	        	});
			    /*### AÇÃO QUANDO CLICA NA COLUNA DA OPERAÇÃO (MARCA/DESMARCA OS CHECKBOX) ###*/




			    /*### AÇÃO QUANDO CLICA NA LINHA DO MÓDULO (MARCA/DESMARCA OS CHECKBOX) ###*/
	        	$(".linha-modulo").on('click', function(event) {

	        		var Module = $(this).attr('id');

	        		$.each($("#"+IdPermissionsTable+" :input[type=checkbox][data-module="+Module+"]"), function(index, element) {

	        			if($(this).prop('checked')==false) $(this).prop('checked',true);
	        			else $(this).prop('checked',false);
	        		});
	        	});
			    /*### AÇÃO QUANDO CLICA NA LINHA DO MÓDULO (MARCA/DESMARCA OS CHECKBOX) ###*/





	        },
		    error: function (jqXHR, status, error) {

				//EXIBE A MENSAGEM DE ERRO
				AlertMsg('', 'error', jqXHR, error, '');

		    }
	    });
	    /*### ENVIAR A REQUISIÇÃO COM A ROTA MODULESPERMISSIONS ###*/

	}
/*### FUNÇÃO QUE GERA A TABELA DE PERMISSÕES DE TODOS OS MÓDULOS ###*/








/*### NOTE: FUNÇÃO QUE MARCA AS PERMISSÕES DO PERFIL E/OU USUÁRIO  ###*/
//IdPermissionsTable = ID do elemento da tabela de permissões
//IdSpecialPermissions = ID do elemento DIV de permissões especiais
//PermissionsObject = objeto de dados JSON com as permissões para marcar
function SetPermissions(IdPermissionsTable, IdSpecialPermissions, PermissionsObject) {

	// console.log(PermissionsObject);

	$.each(PermissionsObject, function (Module, Permissions) {

		$.each(Permissions, function (Operation, Value) {

			if(Module=="permissoes-especiais") {

				$("#"+IdSpecialPermissions+" input[id=permissoes_especiais_"+Operation+"]").prop("checked",true);
			}
			else {

				if(Value==1) $("#"+IdPermissionsTable+" input[id=permissoes_"+Module+"_"+Operation+"]").prop("checked",true);
			}
		});
	});

}
/*### FUNÇÃO QUE MARCA AS PERMISSÕES DO PERFIL E/OU USUÁRIO  ###*/














/*### NOTE: FUNÇÃO QUE VERIFICA SE O USUÁRIO TEM PERMISSÃO DE ACESSO AO MÓDULO E OPERAÇÃO E EMITE AVISO SE NÃO TIVER ###*/
function CheckPermission(Permission) {

	// console.log("Permission");
	// console.log(Permission);

	if(typeof Permission == 'object') {

		if(Permission.responseJSON) {

			var parsePermission = Permission.responseJSON.Permission;
		}
	}
	else {

		var parsePermission = Permission
	}

	// console.log(parsePermission);

	if(parsePermission==false) {

		$("#DataTablesProcessingBehaviour").hide();
		$("#content").html('');
		AlertMsg('', 'error', Permission, '', '')
	}

	return parsePermission;
}
/*### FUNÇÃO QUE VERIFICA SE O USUÁRIO TEM PERMISSÃO DE ACESSO AO MÓDULO E OPERAÇÃO E EMITE AVISO SE NÃO TIVER ###*/













/*### NOTE: FUNÇÃO QUE EXECUTA A AÇÃO DE LOGOUT DO SISTEMA ###*/
function Logout(token) {

	/*### EXECUTA A REQUISIÇÃO AJAX E RETORNA RESULTADO ###*/
    $.ajax({
        type: "POST",
        dataType: "json",
        url: 'src/Controller/Controller.php',
        data: {
	    	Route: 'Logout',
			Token: token
        },
        success: function(data, textStatus, jqXHR) {

        	/*### SE O LOGOUT FOI REALIZADO, EXECUTA AS AÇÕES DE LOGOUT ###*/
        	if(data.logout=="OK") {

        		//APAGA O TOKEN DO LOCALSTORAGE
				localStorage.setItem('token', '');

				//INDICA QUE O LOGOU FOI FEITO
				localStorage.setItem('logout', 'OK');

				//REDIRECIONA PARA A PÁGINA INICIAL
				window.location.href = 'index.html';
        	}
        	/*### SE O LOGOUT FOI REALIZADO, EXECUTA AS AÇÕES DE LOGOUT ###*/

        }
    });
    /*### EXECUTA A REQUISIÇÃO AJAX E RETORNA RESULTADO ###*/

}
/*### FUNÇÃO QUE EXECUTA A AÇÃO DE LOGOUT DO SISTEMA ###*/







/*### NOTE: FUNÇÃO QUE PREENCHE COM ZEROS A ESQUERDA UMA STRING ###*/
//pad = mascara do tamanho e caracteres a preencher
//user_str = valor a ser formatado
//pad_pos = posição do preenchimento (LEFT | RIGHT)
function StrPad(pad, user_str, pad_pos) {

	if(typeof user_str === 'undefined') return pad;

	if (pad_pos == 'LEFT') {

		return (pad + user_str).slice(-pad.length);
	}
	else {

		return (user_str + pad).substring(0, pad.length);
	}
}
/*### FUNÇÃO QUE PREENCHE COM ZEROS A ESQUERDA UMA STRING ###*/




/*### NOTE: FUNÇÃO QUE GERA E ARMAZENA UM PID ###*/
//IdElementForm = ID do elemento FORM
//IdElementForm = Nome do campo do formulário
function SetPid(IdElementForm, FieldName) {

 	$("#"+IdElementForm+" #"+FieldName).val(((new Date().getTime()).toString(16)) + (Math.random().toString(36).substr(2)));
 }
/*### FUNÇÃO QUE GERA E ARMAZENA UM PID ###*/



/*### NOTE: EXECUTA FUNÇÕES DO PHP ###*/
//FunctionName = nome da função que será executada
//Value = parâmetros para execução da função, se necessário
//Token = Token do JWT para validar o acesso
function ExecutePHPFunction(FunctionName, Value, Token) {

	/*### EXECUTA A REQUISIÇÃO AJAX DO ARQUIVO FUNCTIONS E RETORNA COMO RESULTADO ###*/
    return $.ajax({
        type: "POST",
        dataType: "json",
        url: 'src/Controller/Controller.php',
        data: {
	    	Route: 'ExecutePHPFunction',
	    	FunctionName: FunctionName,
			Value: Value,
			Token: Token
        }
    });
    /*### EXECUTA A REQUISIÇÃO AJAX DO ARQUIVO FUNCTIONS E RETORNA COMO RESULTADO ###*/
}
/*### EXECUTA FUNÇÕES DO PHP ###*/









