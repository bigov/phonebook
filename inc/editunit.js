/**
 * Editing service functions
 *  
 */

function submit_on() {
   submit_btm = document.getElementById( "submit" );
   submit_btm.style.visibility="visible"; 				
}

function edit_rec() {
   turn_on( document.getElementById( "unit" ));
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
