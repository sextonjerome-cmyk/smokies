<?php
// Simple photo upload backend for the Smokies trip page.
$PW = "dragon2026";
$days = ["0","1","2","3","4","5","6","7","8"];
$msg = ""; $ok = false;

function capfile($d){ return __DIR__."/photos/day".$d."/_captions.json"; }
function loadcaps($d){ $f=capfile($d); return is_file($f)? (json_decode(file_get_contents($f),true) ?: []) : []; }
function savecaps($d,$c){ file_put_contents(capfile($d), json_encode($c, JSON_PRETTY_PRINT)); }

if ($_SERVER["REQUEST_METHOD"]==="POST") {
  if (($_POST["pw"] ?? "") !== $PW) {
    $msg = "Wrong password.";
  } else {
    $d = $_POST["day"] ?? "";
    if (!in_array($d,$days)) { $msg="Pick a day."; }
    elseif (empty($_FILES["photo"]["name"])) { $msg="No photo chosen."; }
    else {
      $dir = __DIR__."/photos/day".$d;
      if (!is_dir($dir)) mkdir($dir,0775,true);
      $orig = basename($_FILES["photo"]["name"]);
      $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
      $allow = ["jpg","jpeg","png","gif","webp","heic"];
      if (!in_array($ext,$allow)) { $msg="That file type is not allowed."; }
      else {
        $safe = preg_replace('/[^A-Za-z0-9._-]/','_', pathinfo($orig, PATHINFO_FILENAME));
        $name = $safe.".".$ext;
        $i=1; while (is_file($dir."/".$name)) { $name=$safe."-".$i.".".$ext; $i++; }
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $dir."/".$name)) {
          $cap = trim($_POST["caption"] ?? "");
          if ($cap!==""){ $c=loadcaps($d); $c[$name]=$cap; savecaps($d,$c); }
          $ok=true; $msg="Posted to Day ".$d."! It will show on the trip page.";
        } else { $msg="Upload failed. Try again."; }
      }
    }
  }
}
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow"><title>Post a photo</title>
<style>
body{font-family:-apple-system,Segoe UI,Roboto,sans-serif;background:#122A1E;color:#EDF0E8;margin:0;padding:22px;line-height:1.5}
h1{font-size:22px;margin:0 0 14px}
form{background:#1D3A2B;border:1px solid #2c5240;border-radius:14px;padding:16px;max-width:480px}
label{display:block;font-size:14px;color:#9db0a4;margin:12px 0 4px}
input,select,textarea{width:100%;box-sizing:border-box;padding:11px;border-radius:10px;border:1px solid #2c5240;background:#0f2418;color:#EDF0E8;font-size:16px}
textarea{min-height:70px}
button{margin-top:16px;width:100%;padding:13px;border:0;border-radius:10px;background:#F0D51E;color:#122A1E;font-size:17px;font-weight:700}
.msg{padding:11px 14px;border-radius:10px;margin:0 0 14px;max-width:480px;box-sizing:border-box}
.ok{background:#1f5133}.err{background:#5a2222}
a{color:#F0D51E}
</style></head><body>
<h1>Post a photo</h1>
<?php if($msg): ?><div class="msg <?php echo $ok?'ok':'err';?>"><?php echo htmlspecialchars($msg);?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data">
  <label>Which day?</label>
  <select name="day">
    <?php foreach($days as $d){ echo '<option value="'.$d.'"'.((($_POST['day']??'')===$d)?' selected':'').'>Day '.$d.'</option>'; } ?>
  </select>
  <label>Photo</label>
  <input type="file" name="photo" accept="image/*" capture="environment">
  <label>Description (optional)</label>
  <textarea name="caption" placeholder="Say something about this shot..."></textarea>
  <label>Password</label>
  <input type="password" name="pw" autocomplete="current-password">
  <button type="submit">Post it</button>
</form>
<p style="margin-top:16px"><a href="./">Back to the trip page</a></p>
</body></html>
