<form>
<select name="template">
<?


$directory = '/gitbox/Service-Office/templates';


    // create an array to hold directory list
    $results = array();

    // create a handler for the directory
    $handler = opendir($directory);

    // keep going until all files in directory have been read
    while ($file = readdir($handler)) {

        // if $file isn't this directory or its parent, 
        // add it to the results array
        if ($file != '.' && $file != '..' && $file != 'CVS')
            echo "<option>$file</option>";
    }

    // tidy up: close the handler
    closedir($handler);

    // done!
    






?>
</select><input type="submit"></form>
<?
$filename = "/gitbox/Service-Office/templates/".$_GET[template];
$handle = fopen($filename, "rb");
$template .= fread($handle, filesize($filename));
$cord="TEMPLATE%";
echo "<center><div style='width:800px;padding-top:50px;'>$template</div><IMG SRC='barcode.php?barcode=$cord&width=300&height=40'></center>";
?>