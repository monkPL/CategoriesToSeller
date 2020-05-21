<form action="" method="post">	
	<div class="panel kpi-container">
		<h4>Przypisz sprzedawców jako opiekunów do kategorii zamówień</h4>
	</div>
	
	<div class="row">
	
		{foreach from=$res.sprzedawcy item=user key=keyuser}
		{assign var=user_id value=$user.id_employee}
		<div class="col-md-4">
			<div class="panel">
				<div class="panel-heading">#{$user.id_employee} {$user.firstname} {$user.lastname} ({$user.email})</div>
				{foreach from=$res.kategorie item=kategoria key=keykategoria}
					<div class="form-check form-check-inline">
						<label class="form-check-label"><input type="checkbox" class="form-check-input" name="cat2sel[{$user.id_employee}][]" value="{$kategoria.id_category}" {if $kategoria.id_category|in_array:$res.przypisane[$user_id]}checked{/if}/> {$kategoria.name}</label>
					</div>
				{/foreach}
			</div>
		</div>
		{/foreach}
		
		<div class="col-md-4">
			<div class="panel">
				<div class="panel-heading">Przypisz kategorie do grupy zamówień</div>
				<p><b>UWAGA! nie zmieniać bez konsultacji z IT</b></p>
				<p>Numeracja zamówień: #[data]-<b>[grupa_zamówień]</b>[losowy_numer]</p>
				{foreach from=$res.kategorie item=kategoria key=keykategoria}
					<div class="input-group">
						<div class="input-group-addon">
							<span class="input-group-text" id="cat-{$kategoria.id_category}">{$kategoria.name}</span>
						</div>
						<input name="cat2sel_c[{$kategoria.id_category}]" class="form-control" id="ord-{$kategoria.id_category}" aria-describedby="cat-{$kategoria.id_category}" type="text" maxlength="1" size="2" value="{if $res.oznaczenia[$kategoria.id_category]}{$res.oznaczenia[$kategoria.id_category]}{/if}" />
					</div>
				{/foreach}
			</div>
		</div>
		
	</div>

	<div class="panel kpi-container">
		<input type="submit" class="btn btn-primary form-control" name="module_cat2sell" value="Zapisz" />
	</div>
</form>