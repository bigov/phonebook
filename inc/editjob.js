/**
 * Editing service functions
 *  
 */

function FormCheck() {
	
	var err_form = false;
	var digits_pattern = /\d/;
	var form = document.form_edit;
	var text = form.new_job.value;

	if ( digits_pattern.test( text )) err_form = "Название не должно содержать цифры";
	if ( text.length < 5 ) err_form = "Название не может быть таким коротким";
	if ( text=="" ) err_form = "Необходимо заполнить название должности";
	if ( err_form ) {
		alert ( err_form ); 
		return false;
	}
	return true;
} 


function submit_on() {
   submit_btm = document.getElementById( "submit" );
   submit_btm.style.visibility="visible"; 				
}

function edit_rec() {
   turn_on( document.getElementById( "job" ));
   turn_on( document.getElementById( "kab" ));
   turn_on( document.getElementById( "phone" ));
   turn_on( document.getElementById( "fax" ));
   turn_on( document.getElementById( "order" ));
   submit_on();
}

function turn_on(el) {
   el.readOnly=false;
   el.disabled=false; 				
   el.className ="edit_on"; 				
   el.style.visibility='visible';
}
function delete_rec() {
   submit_on();
}
