
/*#######################################################
|														|
| Arquivo com todas as funções javascript utilizadas 	|
| na tela de Exportar Dados do atendimento				|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/




/*#### NOTE: FUNÇÃO QUE RESETA OS FILTROS DO FORMULÁRIO DE EXPORTAR DADOS DE ATENDIMENTO ####*/
//IdElementForm = ID do elemento FORM do formulário de exportar dados
function ResetarFiltrosExport(IdElementForm) {

	$("#"+IdElementForm+" .filtro").find("option").each(function(index, element) {

		if((element.value=="")&&(element.text=="")) $(this).remove();
	});

	//MARCA A OPÇÃO DE TODOS OS EVENTOS
	$("#"+IdElementForm+" #evento").val('').trigger('change');

	//MARCA A OPÇÃO DE TODOS OS PARCEIROS
	$("#"+IdElementForm+" #parceiro").val('').trigger('change');

	//MARCA A OPÇÃO DE TODOS OS SERVIÇOS
	$("#"+IdElementForm+" #servico").val('').trigger('change');

}
/*#### FUNÇÃO QUE RESETA OS FILTROS DO FORMULÁRIO DE EXPORTAR DADOS DE ATENDIMENTO ####*/
