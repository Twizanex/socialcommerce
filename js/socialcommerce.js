function chkform() {
	var tax_rate = $('#tax_rate').val();
	if(tax_rate == '')
	{
		alert('Enter tax rate');
		return false;
	}
	if (isNaN(tax_rate))
	{
		alert('Enter proper tax rate');
		return false;
	}
	else
	{
		return true;
	}

}

function chkforms() {
	
	var tax_rate = $('#taxrate').val();
	if(tax_rate == '')
	{
		alert('Enter tax rate');
		return false;
	}
	if (isNaN(tax_rate))
	{
		alert('Enter proper tax rate');
		return false;
	}
	else
	{
		return true;
	}

}
