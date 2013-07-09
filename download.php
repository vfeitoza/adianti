<?php
if (isset($_GET['file']))
{
    $file      = $_GET['file'];
    $info      = pathinfo($file);
    $extension = $info['extension'];
    
    if (in_array($extension, array('html', 'pdf', 'rtf', 'csv')))
    {
        $basename  = basename($file);
        
        // get the filesize
        $filesize = filesize($file);
        
        header("Pragma: public");
        header("Expires: 0"); // set expiration time
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-type: application/{$extension}");
        header("Content-Length: {$filesize}");
        header("Content-disposition: inline; filename=\"{$basename}\"");
        header("Content-Transfer-Encoding: binary");
        
        // a readfile da problemas no internet explorer
        // melhor jogar direto o conteudo do arquivo na tela
        echo file_get_contents($file);
    }
}
?>
