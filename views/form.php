<form name="pasteForm" id="pasteForm" method="post" action="<?=$url; ?>">
	<h1>create paste</h1>
	<input type="submit" name="button" id="button" value="save" />
	<select name="ttl" id="ttl">
		<option value="5">self destruct</option>
		<option value="4">1 hour</option>
		<option value="3">1 day</option>
		<option value="2">1 month</option>
		<option value="1">1 year</option>
		<option value="0">never</option>
	</select>
	<br class="clear"/>
	<textarea></textarea>
</form>