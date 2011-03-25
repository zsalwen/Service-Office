<?
foreach (glob("modules/*.php") as $filename)
{
    include $filename;
}
?>