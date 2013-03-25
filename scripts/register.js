function registerUser(form)
{
  if (validateForm(form) == true)
  {
    input = $(form).find("input[type='password']");
    input.each(function ()
    {
      md5 = MD5(jQuery(this).val());
      $(this).val(md5);
    });
    return true;
  }
  else
    return false;
}