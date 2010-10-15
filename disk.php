<?
// monitor application usage on disk

function getDirectorySize($path)
{
  $totalsize = 0;
  $totalcount = 0;
  $dircount = 0;
  if ($handle = opendir ($path))
  {
    while (false !== ($file = readdir($handle)))
    {
      $nextpath = $path . '/' . $file;
      if ($file != '.' && $file != '..' && !is_link ($nextpath))
      {
        if (is_dir ($nextpath))
        {
          $dircount++;
          $result = getDirectorySize($nextpath);
          $totalsize += $result['size'];
          $totalcount += $result['count'];
          $dircount += $result['dircount'];
        }
        elseif (is_file ($nextpath))
        {
          $totalsize += filesize ($nextpath);
          $totalcount++;
        }
      }
    }
  }
  closedir ($handle);
  $total['size'] = $totalsize;
  $total['count'] = $totalcount;
  $total['dircount'] = $dircount;
  return $total;
}

function sizeFormat($size)
{
    if($size<1024)
    {
        return $size." bytes";
    }
    else if($size<(1024*1024))
    {
        $size=round($size/1024,1);
        return $size." KB";
    }
    else if($size<(1024*1024*1024))
    {
        $size=round($size/(1024*1024),1);
        return $size." MB";
    }
    else
    {
        $size=round($size/(1024*1024*1024),1);
        return $size." GB";
    }

}  


function craw($path){
$ar=getDirectorySize($path);
echo "<b>Details for the path : $path</b><br>";
echo "Total size : ".sizeFormat($ar['size'])."<br>";
echo "No. of files : ".$ar['count']."<br>";
echo "No. of directories : ".$ar['dircount']."<br>";  
return $ar['size'];
}
?>





<?



$total = craw("/logs/");
$total = $total + craw("/sandbox/");
$total = $total + craw("/thirdParty/");
$total = $total + craw("/data/service/fileCopy/");
$total = $total + craw("/data/service/invoices/");
$total = $total + craw("/data/service/orders/");
$total = $total + craw("/data/service/photos/");
$total = $total + craw("/data/service/scans/");
$total = $total + craw("/data/service/statements/");
$total = $total + craw("/data/service/unknown/");
$total = $total + craw("/data/service/zips/");
$total = $total + craw("/var/lib/mysql/");

echo "<b>Total Usage:".sizeFormat($total)."</b>";
?>
