/**
 * Editing service functions
 */
function FormCheck() {
  var err_form = false;
  var digits_pattern = /\d/;
  var name_pattern = /\S{1,}\s{1,}\S{1,}\s{1,}\S{1,}(?:вич|вна)/;
  var form = document.form_edit;
  var text = form.new_name.value;

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

function submit_on() {
   submit_btm = document.getElementById( "submit" );
   submit_btm.style.visibility="visible";
}

function edit_on() {
   turn_on( document.getElementById( "email" ));
   turn_on( document.getElementById( "phones" ));
   my_f = document.getElementById( "name" );
   turn_on( my_f );
   submit_on();
   my_f.focus();
}

function turn_on(el) {
   el.readOnly=false;
   el.disabled=false;
   el.className ="edit_on";
}
function delete_name() {
   turn_off( document.getElementById( "name" ));
   turn_off( document.getElementById( "email" ));
   turn_off( document.getElementById( "phones" ));
   submit_on();
}

function turn_off(el) {
   el.readOnly=true;
   el.className ="edit_off";
}

function hide_element(el) {
   el.style.visibility='hidden';
}
function visible_element(el) {
   el.style.visibility='visible';
}

function on_disable(el) {
   el.disabled=true;
   turn_off(el);
}
