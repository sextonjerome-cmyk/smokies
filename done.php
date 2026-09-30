<?php
// Shared "Mark done" state for the trip page. Everyone sees the same state;
// only someone with the trip password can change it.
header("Content-Type: application/json");
header("Cache-Control: no-store");
$PW = "dragon2026";
$dir = __DIR__."/state"; $f = $dir."/done.json";
$done = is_file($f) ? (json_decode(file_get_contents($f), true) ?: []) : ["0"=>true,"1"=>true,"2"=>true,"3"=>true];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (($_POST["pw"] ?? "") !== $PW) { http_response_code(403); echo json_encode(["error"=>"bad password"]); exit; }
  $d = preg_replace('/[^0-9]/','', $_POST["day"] ?? "");
  if ($d === "" || intval($d) > 8) { http_response_code(400); echo json_encode(["error"=>"bad day"]); exit; }
  $done[$d] = ($_POST["done"] ?? "") === "1";
  if (!is_dir($dir)) mkdir($dir, 0775, true);
  file_put_contents($f, json_encode($done));
}
echo json_encode(["done"=>$done]);
