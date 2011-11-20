<?php
$PUBLIC_DIR     = "public";
$AUDIO_DIR      = "$PUBLIC_DIR/audio";
$BROADCAST_DIR  = "$PUBLIC_DIR/broadcast_audio";
$ICON_DIR       = "$PUBLIC_DIR/icons";
$WEB_PUBLIC_DIR = "/$PUBLIC_DIR";
$WEB_ICON_DIR   = "$WEB_PUBLIC_DIR/icons";

function panda_response()
{
  $file_to_play = get_var('playfile');
  if ($file_to_play != '')
    return play($file_to_play);

  else if (get_var('broadcast') != '')
    return broadcast();

  else if (get_var('create') != '')
    return create();

  else
    return index();
}

function play($file_to_play)
{
  global $AUDIO_DIR;
  $vlc_response = vlc_play("$AUDIO_DIR/$file_to_play");

  $html = $vlc_response == '' ?
    flash("Played $file_to_play") :
    flash("Error playing file: $vlc_response", 'error');
  return $html . index();
}

function broadcast()
{
  global $BROADCAST_DIR;

  $tmp_file_name = "$BROADCAST_DIR/tmp.wav";
  move_uploaded_file($_FILES['userfile']['tmp_name'], $tmp_file_name);
  $vlc_response = vlc_play($tmp_file_name);

  if ($vlc_response == '')
    {
      header("Cache-control: private");
      header("Content-Type: text/plain");
      echo "SUCCESS - broadcastn\n";
    }
  return null;
}

function create()
{
  global $AUDIO_DIR;
  global $ICON_DIR;

  try
    {
      $title = htmlspecialchars($_POST['title']);
      $audiofile = $_FILES['audiofile']['tmp_name'];
      $imagefile = $_FILES['imagefile']['tmp_name'];
      if ($title == '')
        throw new Exception("no title provided");
      if ($audiofile == '')
        throw new Exception("no audio file provided");
      if ($imagefile == '')
        throw new Exception("no image file provided");

      $new_filename = choose_filename($_POST['title']);
      $new_audio_fn = $new_filename . filename_extension($_FILES['audiofile']['name']);
      $new_image_fn = $new_filename . filename_extension($_FILES['imagefile']['name']);
      if ($new_filename == '' || $new_audio_fn == '' || $new_image_fn == '')
        throw new Exception("file or title sucks too hard");

      if (!move_uploaded_file($audiofile, "$AUDIO_DIR/$new_audio_fn"))
        throw new Exception("could not copy uploaded audio file");
      if (!move_uploaded_file($imagefile, "$ICON_DIR/$new_image_fn"))
        throw new Exception("could not copy uploaded image file");

      if (!db_insert_row($title, $new_audio_fn))
        throw new Exception("could not insert info into DB");

      $html = flash("added 2 panda");
    }
  catch (Exception $e)
    {
      $html = flash("Error adding 2 panda: {$e->getMessage()}", 'error');
    }

  return $html . index();
}

function index()
{
  global $ICON_DIR;
  global $WEB_ICON_DIR;

  try
    {
      $public_mp3s = all_public_mp3s();
      $html = "<table>\n<tr>\n";
      $icon_fns = scandir($ICON_DIR);
      $i = 0;
      foreach ($public_mp3s as $mp3)
        {
          if ($i++ % 5 == 0)
            $html .= "</tr>\n<tr>";
          $filename_audio = $mp3['filename'];
          $filename_icon  = find_icon_name($icon_fns, $filename_audio);
          $html .= "<td><a href=\"play/$filename_audio\" class=\"player-link\">";
          $html .= "<img class=\"audio-icon\" src=\"$WEB_ICON_DIR/$filename_icon\" alt=\"{$mp3['public_name']}\"/>";
          $html .= "</a></td>";
        }
      $html .= "</tr>\n</table>\n";
    }
  catch (Exception $e)
    {
      $html = flash("Error: {$e->getMessage()}", 'error');
    }

  return $html;
}

function all_public_mp3s()
{
  return db_public_mp3s(db_readonly_connect());
}

function find_icon_name($icon_fns, $filename_audio)
{
  $stripped_audio_fn = strip_file_extension($filename_audio);

  foreach ($icon_fns as $icon_fn)
    if ($stripped_audio_fn == strip_file_extension($icon_fn))
      return $icon_fn;

  return '';
}

function choose_filename($s)
{
  global $AUDIO_DIR;

  $patterns = array(
    "/\s/",
    "/\&/",
    "/\+/"
  );
  $replacements = array(
    "_",
    "_and_",
    "_plus_"
  );
  $fn = preg_replace($patterns, $replacements, trim($s));
  $fn = preg_replace('/[^\w\d\.\-_]/', '', $fn);

  $afs = array_map("strip_file_extension", scandir($AUDIO_DIR));
  while (in_array($fn, $afs, true))
    $fn .= '_';

  return $fn;
}

function filename_extension($fn)
{
  return preg_replace('/(.*)(\..*)$/', '$2', $fn);
}

function strip_file_extension($fn)
{
  return preg_replace('/\..*/', '', $fn);
}

function flash($message, $type = '')
{
  return "<h4 class=\"flash $type\">$message</h4>\n";
}

function vlc_play($filename)
{
  return exec("vlc --play-and-exit $filename");
}


/* DB / HTTP infrastructure */

function db_public_mp3s($db_connection)
{
  return pg_fetch_all(pg_query($db_connection,
    "SELECT * FROM public_mp3s ORDER BY id DESC"));
}

function db_insert_row($title, $new_audio_fn)
{
  return pg_query(db_readwrite_connect(),
    "INSERT INTO public_mp3s ".
      "(public_name, filename) VALUES ".
      "('$title', '$new_audio_fn')");
}

function db_readonly_connect()
{
  $db_connection = pg_pconnect("dbname=panda user=panda_readonly password=panda");
  if (!$db_connection)
    throw new Exception("Connection to P A N D A database failed. Go away.");
  pg_exec($db_connection, "SET search_path = public");
  return $db_connection;
}

function db_readwrite_connect()
{
  $db_connection = pg_pconnect("dbname=panda user=panda_creator password=pandarr");
  if (!$db_connection)
    throw new Exception("Connection to P A N D A database failed. Go away.");
  pg_exec($db_connection, "SET search_path = public");
  return $db_connection;
}

function get_var($key_name)
{
  $value = '';
  if (in_array($key_name,array_keys($_GET)))
    $value = $_GET[$key_name];
  return $value;
}
?>