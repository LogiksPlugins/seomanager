<?php
if(!defined('ROOT')) exit('No direct script access allowed');

//printArray($_ENV['PAGECONFIG']);

// $_ENV['PAGECONFIG']['robot']
$title=$_ENV['PAGECONFIG']['title'];
$descs=$_ENV['PAGECONFIG']['description'];

$metaAddons = "";
$seoSchema = "";

if(isset($_ENV['PAGECONFIG']['keywords'])) $keywords=$_ENV['PAGECONFIG']['keywords'];
else $keywords= "";

if(isset($_ENV['PAGECONFIG']['robot'])) $robot=$_ENV['PAGECONFIG']['robot'];
else $robot="";

if(isset($_ENV['PAGECONFIG']['featured_image'])) $featuredImage=$_ENV['PAGECONFIG']['featured_image'];
else $featuredImage = false;

if(isset($_ENV['PAGECONFIG']['featured_video'])) $featuredVideo=$_ENV['PAGECONFIG']['featured_video'];
else $featuredVideo = false;


$titleApps=getConfig("APPS_TITLE");

if($title==null || strlen($title)<=0) {
  $title=$titleApps;
} elseif(strlen($titleApps)>0) {
  $title="{$title} - {$titleApps}";
}

if($descs==null || strlen($descs)<=0) {
  $descs=getConfig("APPS_DESCRIPTION");
}

if($keywords==null || strlen($keywords)<=0) {
  $keywords=getConfig("APPS_KEYWORDS");
}

$slug=_slug();
$pageURI=current(explode("/",PAGE));

foreach($slug as $a=>$b) {
  if($b==null || strlen($b)<=0) {
    unset($slug[$a]);
  }
}
// printArray($slug);

$slugURI=implode("/",array_keys($slug));
$slugVAL=implode("/",array_values($slug));

$sqlData=_db()->_selectQ("do_seo","*",["blocked"=>'false',"page_URI"=>"/".PAGE, "for_site"=>SITENAME])->_GET();//"/{$pageURI}"

if($sqlData) {
    if(count($sqlData)>1) {
        $foundData = false;
        foreach ($sqlData as $key => $record) {
            if(strtolower($record['page_slug'])==strtolower($slugURI)) {
                $foundData = $record;
            }
        }
        if($foundData) {
            $sqlData = $foundData;
        } else {
            if(getFeature("SEO_USE_PAGE_TREE")=="true") {
                $sqlData = $sqlData[0];
            } else {
                return;
            }
        }

        if(!$sqlData['title']) $sqlData['title'] = $title;
        if(!$sqlData['descs']) $sqlData['descs'] = $descs;
        if(!$sqlData['keywords']) $sqlData['keywords'] = $keywords;
        if(!$sqlData['robots']) $sqlData['robots'] = $robot;

        if($sqlData['meta_addons']) $metaAddons = $sqlData['meta_addons'];
        if($sqlData['seo_schema']) $seoSchema = $sqlData['seo_schema'];

        $title=str_replace("{","#",str_replace("}","#",$sqlData['title']));
        $descs=str_replace("{","#",str_replace("}","#",$sqlData['descs']));
        $keywords=str_replace("{","#",str_replace("}","#",$sqlData['keywords']));
        if(strlen($sqlData['robots'])>0) $robot=str_replace("{","#",str_replace("}","#",$sqlData['robots']));
        $featuredImage=str_replace("{","#",str_replace("}","#",$sqlData['featured_image']));
        $featuredVideo=str_replace("{","#",str_replace("}","#",$sqlData['featured_video']));
    } elseif(count($sqlData)==1) {
        $sqlData=$sqlData[0];

        if(!$sqlData['title']) $sqlData['title'] = $title;
        if(!$sqlData['descs']) $sqlData['descs'] = $descs;
        if(!$sqlData['keywords']) $sqlData['keywords'] = $keywords;
        if(!$sqlData['robots']) $sqlData['robots'] = $robot;

        if($sqlData['meta_addons']) $metaAddons = $sqlData['meta_addons'];
        if($sqlData['seo_schema']) $seoSchema = $sqlData['seo_schema'];


        $title=str_replace("{","#",str_replace("}","#",$sqlData['title']));
        $descs=str_replace("{","#",str_replace("}","#",$sqlData['descs']));
        $keywords=str_replace("{","#",str_replace("}","#",$sqlData['keywords']));
        if(strlen($sqlData['robots'])>0) $robot=str_replace("{","#",str_replace("}","#",$sqlData['robots']));
        $featuredImage=str_replace("{","#",str_replace("}","#",$sqlData['featured_image']));
        $featuredVideo=str_replace("{","#",str_replace("}","#",$sqlData['featured_video']));
    }
}

if($featuredImage && strlen($featuredImage)>0) {
    if(!(substr($featuredImage, 0,7)=="http://" || substr($featuredImage, 0,8)=="https://" || substr($featuredImage, 0,2)=="//")) {
        //$featuredImage = loadMedia("/usermedia/{$featuredImage}");
        if(file_exists(APPROOT."/usermedia/{$featuredImage}")) {
            $featuredImage = WEBAPPROOT."usermedia/{$featuredImage}";
        }
    }
}
if($featuredVideo && strlen($featuredVideo)>0) {
    if(!(substr($featuredVideo, 0,7)=="http://" || substr($featuredVideo, 0,8)=="https://" || substr($featuredVideo, 0,2)=="//")) {
        //$featuredVideo = loadMedia("/usermedia/{$featuredVideo}");
        if(file_exists(APPROOT."/usermedia/{$featuredVideo}")) {
            $featuredVideo = WEBAPPROOT."usermedia/{$featuredVideo}";
        }
    }
}

$pageConfig=$_ENV['PAGECONFIG']['meta'];
if($pageConfig && is_array($pageConfig)) {
    $metaAddons.="\n";
    foreach($pageConfig as $meta) {
        $metaAddons.="<meta ";

        foreach ($meta as $key => $value) {
          $metaAddons .= "{$key}=\"{$value}\" ";
        }

        $metaAddons .= " />\n";
    }
}

$url=_url();//SiteLocation.$_SERVER['REQUEST_PATH'];

$seoFacebook = getFeature("SEO_ENABLE_FACEBOOK","seomanager");
$seoTwitter = getFeature("SEO_ENABLE_TWITTER","seomanager");
$seoGoogle = getFeature("SEO_ENABLE_GOOGLE_PLUS","seomanager");

?>

<?=$metaAddons?>

<!-- start: SEOMETADATA -->
<?php
    if($seoFacebook===true || $seoFacebook=="true") {
        $facebookID = getFeature("FACEBOOK_APP_ID","seomanager");
        $facebookUserID = getFeature("FACEBOOK_USER_ID","seomanager");
        //type : website, article
        if($facebookID) {
            echo "<meta property='og:app_id' content='{$facebookID}' />";
        }
        if($facebookUserID) {
            echo "<meta property='fb:admins' content='{$facebookUserID}' />";
        }
        echo "<meta property='og:title' content='{$title}' /><meta property='og:description' content=\"{$descs}\" /><meta property='og:site_name' content='".APPS_NAME."' /><meta property='og:url' content='{$url}' /><meta property='og:type' content='website' /><meta property='og:locale' content='en_US' />";
        if($featuredImage) {
            echo "<meta property='og:image' content='{$featuredImage}' />";
        }
        if($featuredVideo) {
            echo "<meta name='og:video' content='{$featuredVideo}'>";
        }
        echo "\n";
    }
    if($seoTwitter===true || $seoTwitter=="true") {
        //summary_large_image
        $publisherHandle = getFeature("TWITTER_PUBLISHER_HANDLE","seomanager");
        $authorHandle = getFeature("TWITTER_AUTHOR_HANDLE","seomanager");
        echo "<meta name='twitter:card' content='summary' /><meta name='twitter:title' content='{$title}' /><meta name='twitter:description' content='{$descs}' />";
        if($publisherHandle) {
            echo "<meta name='twitter:site' content='@{$publisherHandle}' />";
        }
        if($authorHandle) {
            echo "<meta name='twitter:creator' content='@{$authorHandle}' />";
        }
        if($featuredImage) {
            //<-- Twitter Summary card images must be at least 120x120px -->
            echo "<meta name='twitter:image' content='{$featuredImage}' />";
            //<!-- Twitter summary card with large image must be at least 280x150px -->
            echo "<meta name='twitter:image:src' content='{$featuredImage}' />";
        }
        if($featuredVideo) {
            echo "<meta name='twitter:player' content='{$featuredVideo}'>";
        }
        echo "\n";
    }
    if($seoGoogle===true || $seoGoogle=="true") {
        echo "<meta itemprop='name' content='{$title}'><meta itemprop='description' content='{$descs}'>";
        if($featuredImage) {
            echo "<meta itemprop='image' content='{$featuredImage}'>";
        }
        if($featuredVideo) {
            echo "<meta name='video' content='{$featuredVideo}'>";
        }
        echo "\n";
    }
?>
<!-- end: SEOMETADATA -->

<?=$seoSchema?>
