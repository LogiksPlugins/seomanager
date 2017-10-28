<?php
// $_ENV['PAGECONFIG']['robot']
$title=$_ENV['PAGECONFIG']['title'];
$descs=$_ENV['PAGECONFIG']['description'];
$keywords=$_ENV['PAGECONFIG']['keywords'];

$titleApps=getConfig("APPS_TITLE");

if($title==null || strlen($title)<=0) {
  $title=$titleApps;
} else {
  $title="{$title} -{$titleApps}";
}
if($descs==null || strlen($descs)<=0) {
  $descs=getConfig("APPS_DESCRIPTION");
}
if($keywords==null || strlen($keywords)<=0) {
  $keywords=getConfig("APPS_KEYWORDS");
}

$slug=_slug();
$pageURI=current(explode("/",PAGE));

if(isset($_REQUEST['debug']) && $_REQUEST['debug']=="true") {
  printArray($slug);
}

foreach($slug as $a=>$b) {
  if($b==null || strlen($b)<=0) {
    unset($slug[$a]);
  } else {
    if($a=="place") {
      $_REQUEST["placename"]=toTitle(str_replace("-"," ",$b));
      $_REQUEST["place"]=explode("-",$b);
      $_REQUEST["place"]=toTitle(end($_REQUEST["place"]));
    } else {
      $_REQUEST[$a]=toTitle(str_replace("-"," ",$b));
    }
  }
}

$slugURI=implode("/",array_keys($slug));
$slugVAL=implode("/",array_values($slug));

switch($pageURI) {
  case "place":

    break;
  case "flights":
    if(isset($slug['to'])) {
      if($slug['to']=="listing") {
        if(isset($_POST['to'])) $slug['to']=$_POST['to'];
        if(isset($_POST['from'])) $slug['from']=$_POST['from'];
  //       printArray($_POST);
      } elseif(strlen($slug['to'])>1) {
        $airportTo=_db()->_selectQ("data_iatatbl","*",["code"=>$slug['to']])->_GET();
        if(isset($airportTo[0])) {
          $slug['to']="{$airportTo[0]['city']} ({$airportTo[0]['code']})";
        }
        if(isset($slug['from'])) {
          $airportFrom=_db()->_selectQ("data_iatatbl","*",["code"=>$slug['from']])->_GET();
          if(isset($airportFrom[0])) {
            $slug['from']="{$airportFrom[0]['city']} ({$airportFrom[0]['code']})";
          }
        }
      }
    }
    break;
  case "hotels":
    if(isset($slug['place'])) {
      if($slug['place']=="listing") {
        if(isset($_POST['destination'])) $slug['place']=$_POST['destination'];
        if(isset($_POST['destination_country'])) $slug['country']=$_POST['destination_country'];
      } elseif(strlen($slug['place'])>1) {
        $slug['place'] = fetchRegionFromSlug($slug['place']); 
      }
    }
    break;
}

$sqlData=_db()->_selectQ("do_seo","*",["blocked"=>'false',"page_URI"=>"{$pageURI}/","page_slug"=>"{$slugURI}"])->_GET();
// printArray($sqlData);
// echo _db()->_selectQ("do_seo","*",["blocked"=>'false',"page_URI"=>"{$pageURI}/","page_slug"=>"{$slugURI}"])->_SQL();

if(count($sqlData)>0) {
  $sqlData=$sqlData[0];
//   printArray($sqlData);
  
  $title=str_replace("{","#",str_replace("}","#",$sqlData['title']))." -PlacPic";
  $descs=str_replace("{","#",str_replace("}","#",$sqlData['descs']));
  $keywords=str_replace("{","#",str_replace("}","#",$sqlData['keywords']));
}

// printArray($sqlData);
// printArray($_ENV['PAGECONFIG']);

$url=_url();//SiteLocation.$_SERVER['REQUEST_PATH'];
//<meta property="og:image" content="<?=$image? >" />
?>
<title><?=$title?></title>
<meta name='description' content='<?=$descs?>' />
<meta name='keywords' content='<?=$keywords?>' />

<meta property="og:app_id" content="<?=getConfig("FACEBOOK_APP_ID")?>" />
<meta property="og:title" content="<?=$title?>" />
<meta property="og:site_name" content="PlacPic" />
<meta property="og:url" content="<?=$url?>" />
<meta property="og:description" content="<?=$descs?>" />
