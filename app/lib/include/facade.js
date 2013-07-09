function __adianti_goto_page(page)
{
    window.location = page;
}

function __adianti_load_html(content)
{
   if ($('[widget="TWindow"]').length > 0)
   {
       $('[widget="TWindow"]').attr('remove', 'yes');
       $('#adianti_online_content').hide();
       $('#adianti_online_content').html(content);
       $('[widget="TWindow"][remove="yes"]').remove();
       $('#adianti_online_content').show();
   }
   else
   {
       if (content.indexOf("TWindow") > 0)
       {
           $('#adianti_online_content').html(content);
       }
       else
       {
           $('#adianti_div_content').html(content);
       }
   }
}

function __adianti_load_page_no_register(page)
{
    $.get(page, function(data)
    {
        __adianti_load_html(data);
    });
}

function __adianti_append_page(page)
{
    $.get(page, function(data)
    {
        $('#adianti_online_content').after('<div></div>').html(data);
    });
}

function __adianti_load_page(page)
{
    url = page;
    url = url.replace('index.php', 'engine.php');
    __adianti_load_page_no_register(url);
    
    if ( history.pushState && ($('[widget="TWindow"]').length == 0) )
    {
        var stateObj = { url: url };
        history.pushState(stateObj, "", url.replace('engine.php', 'index.php'));
    }
}