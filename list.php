<?php
header("Content-Type: application/json");
$d = preg_replace('/[^0-9]/','', $_GET["day"] ?? "");
$out = ["photos"=>[]];
if ($d!=="") {
  $dir = __DIR__."/photos/day".$d;
  $capf = $dir."/_captions.json";
  $caps = is_file($capf)? (json_decode(file_get_contents($capf),true) ?: []) : [];
  if (is_dir($dir)) {
    $files = scandir($dir);
    sort($files);
    foreach ($files as $f) {
      if (preg_match('/\.(jpe?g|png|gif|webp|heic)$/i',$f)) {
        $out["photos"][] = ["file"=>$f, "caption"=>($caps[$f] ?? "")];
      }
    }
  }
}
echo json_encode($out);
