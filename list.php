<?php
header("Content-Type: application/json");
header("Cache-Control: no-store");
$d = preg_replace('/[^0-9]/','', $_GET["day"] ?? "");
$out = ["photos"=>[]];
if ($d!=="") {
  $dir = __DIR__."/photos/day".$d;
  $capf = $dir."/_captions.json";
  $caps = is_file($capf)? (json_decode(file_get_contents($capf),true) ?: []) : [];
  if (is_dir($dir)) {
    $files = [];
    foreach (scandir($dir) as $f) {
      if (preg_match('/\.(jpe?g|png|gif|webp|heic)$/i',$f)) $files[$f] = true;
    }
    // Story order: files in the order they appear in _captions.json, then anything else alphabetically
    $ordered = [];
    foreach (array_keys($caps) as $f) { if (isset($files[$f])) { $ordered[] = $f; unset($files[$f]); } }
    $rest = array_keys($files); sort($rest);
    foreach (array_merge($ordered, $rest) as $f) {
      $out["photos"][] = ["file"=>$f, "caption"=>($caps[$f] ?? "")];
    }
  }
}
echo json_encode($out);
