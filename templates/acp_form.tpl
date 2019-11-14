<form action="{form_action}" method="POST" class="well">

	<div class="row">
		<div class="col-md-3">
	
			<div class="form-group">
		    <label for="InputShorthand">{label_shorthand}</label>
		    <input type="text" class="form-control" id="InputShorthand" name="alp_shorthand" value="{alp_shorthand}">
		  </div>
		<div class="form-group">
	    <label for="InputLang">{label_language}</label>
			{alp_langs}
	  </div>
	  
		</div>
		<div class="col-md-9">
		
			<div class="form-group">
				<label for="InputText">{label_text}</label>
				<textarea name="alp_text" class="form-control aceEditor_html" id="InputText" rows="10">{alp_text}</textarea>
				<div id="HTMLeditor"></div>
			</div>
	  
		</div>
	</div>

	<input type="submit" name="save_entry" value="{btn_value}" class="btn btn-save">
	<input type="hidden" name="edit_id" value="{edit_id}">
	<input type="hidden" name="csrf_token" value="{token}">
</form>