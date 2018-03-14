/**
 * Copyright (c) 2018.
 * Itach-soft
 * www.itach.by
 * Minsk. Belarus
 */
//функции для постраения bootstrap таблиц


function linkFormatterext(e, row){
	var html='<input name="'+(row.PHONE)+'" value='+value+'></td>';

	return html;
}
//список телефонов в группе
function linkFormatterphone(e, row) {
	var html = '<a href="javascript:void(0)" class="editnum" title="editnum" data-pnumber="'+(row.phonenum)+'" data-pname="'+(row.name)+'" data-pcallee="'+(row.id)+'"><i class="fa fa-pencil"></i></a>';
	//var html = '<a href="?display=autodial&view=gphone_form&itemid='+(row.id)+'&groupid='+(row.group_id)+'"><i class="fa fa-pencil"></i></a>';
		html += '&nbsp;<a href="?display=autodial&view=gphone_form&action=deleteCallee&itemid='+(row.id)+'&groupid='+(row.group_id)+'" class="delAction"><i class="fa fa-trash"></i></a>';

	return html;

}
	//Функции стат таблицы
function Formatterfirstcall(value){
		var html = '';
		if (value == 1){
			html  += '<i class="fa fa-thumbs-o-up"></i>';}
		else
		{ html +=  '<i class=""></i>'
		};
		return html;
}
function FormatterDirection(value){
	var html = '';
	if (value == 'out'){
		html  += '<i class="fa fa-sign-out"></i>';}
	else
	{ html +=  '<i class="fa fa-sign-in"></i>'
	};
	return html;
}
function qc_edit(e, row){
	var html = '';

	html  += '<a href="/admin/config.php?display=callbackqueue&view=newcallqueue&action=qc_edit&qc_id='+row	.qc_id+'"><i class="fa fa-edit"></i>';
	html  += '<a href="/admin/config.php?display=callbackqueue&action=qc_delete&qc_id='+row	.qc_id+'"><i class="fa fa-trash"></i>';

	return html;
}


	//Конец Функции стат таблицы


