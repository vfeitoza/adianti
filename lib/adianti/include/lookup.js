var sacks = new Array();
function ajaxLookup(string_action, field)
{
    var count = sacks.length;
    sacks[count] = new sack();
    var id = field.value;
    if (id)
    {
        sacks[count].requestFile = 'engine.php';
        sacks[count].method  = 'GET';
        sacks[count].onCompletion = function(){
            comando = new String(sacks[count].xmlhttp.responseText);
            tmp = comando;
            tmp = new String(tmp.replace(/\<script language=\'JavaScript\'\>/g, ''));
            tmp = new String(tmp.replace(/\<script type=\'text\/javascript\'\>/g, ''));
            tmp = new String(tmp.replace(/\<script type=\"text\/javascript\"\>/g, ''));
            tmp = new String(tmp.replace(/\<\/script\>/g, ''));
            tmp = new String(tmp.replace(/window\.opener\./g, ''));
            tmp = new String(tmp.replace(/window\.close\(\)\;/g, ''));
            tmp = new String(tmp.replace(/(\n\r|\n|\r)/gm,''));
            tmp = new String(tmp.replace(/^\s+|\s+$/g,"")); //trim
            
            if ($('[widget="TWindow"]').length > 0)
            {
               // o código dinâmico gerado em ajax lookups (ex: seekbutton)
               // deve ser modificado se estiver dentro de window para pegar window2
               tmp = new String(tmp.replace(/TWindow/g, 'TWindow2'));
            }
            
            try {
                eval(''+tmp+''); 
            } catch (e) {
                if (e instanceof SyntaxError) {
                    alert(e.message + ': ' + tmp);
                }
            }
            
        };
        sacks[count].runAJAX(string_action+'&key='+id+'&static=1&is_ajax=1');
    }
}