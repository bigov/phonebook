/**
 * Editing service functions
 *  
 */

function submit_on() {
   submit_btm = document.getElementById( "submit" );
   submit_btm.style.visibility="visible"; 				
}

function NameCheck() {

	var err_form = false;
	var digits_pattern = /\d/;
	var name_pattern = /\S{1,}\s\S{1,}\s\S{1,}(?:ич|на)/;
	var text;

	text = document.form.name.value; 
	if ( digits_pattern.test( text )) err_form = "Имя не может содержать цифры";
	if ( !name_pattern.test( text )) err_form = "Укажите полностью фамилию имя и отчество";
	if ( text.length < 8 ) err_form = "Имя не может быть таким коротким";
	if ( text=="" ) err_form = "Поле имени необходимо заполнить";
	if ( err_form ) {
		alert ( err_form ); 
		return false;
	}
	return true;
} 
