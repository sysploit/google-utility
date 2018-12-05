<?php
$SVG = '<svg width="64px" height="64px" aria-hidden="true" data-prefix="fas" data-icon="undo" class="svg-inline--fa fa-undo fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M212.333 224.333H12c-6.627 0-12-5.373-12-12V12C0 5.373 5.373 0 12 0h48c6.627 0 12 5.373 12 12v78.112C117.773 39.279 184.26 7.47 258.175 8.007c136.906.994 246.448 111.623 246.157 248.532C504.041 393.258 393.12 504 256.333 504c-64.089 0-122.496-24.313-166.51-64.215-5.099-4.622-5.334-12.554-.467-17.42l33.967-33.967c4.474-4.474 11.662-4.717 16.401-.525C170.76 415.336 211.58 432 256.333 432c97.268 0 176-78.716 176-176 0-97.267-78.716-176-176-176-58.496 0-110.28 28.476-142.274 72.333h98.274c6.627 0 12 5.373 12 12v48c0 6.627-5.373 12-12 12z"></path></svg>';

function webface(){
	return '
<head>
	<title>Google Image Scraper</title>
	<style type="text/css">
	html {
		background-color: #FFF;
		color: #000;
		font-family: monospace;
		font-size: 30pt;
		text-align: center;
		word-wrap: break-word;
	}
	h1 {
		font-weight: 100;
		font-size: 72pt;
		margin: 20px 0 0;
	}
	a {
		text-decoration: none;
		color: #DDD;
		transition: 0.5s;
	}

	a:hover {
		color: #000;
	}

	input[type=text] {
		width: 584px;
		height: 44px;
		padding: 12px 20px;
		margin: 8px 0;
		box-sizing: border-box;
		box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
		transition: 0.5s;
	}

	input[type=text]:hover, input[type=text]:active {
		box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
	}

	@media screen and (min-width: 300px) {
		.user {
			font-size: 30pt;
		}
	}

	@media screen and (max-width: 300px) {
		.user {
			font-size: 20pt;
		}
	}
    </style>
</head>';
}

function crawl($user, $raw=false){
	$url = 'https://picasaweb.google.com/data/entry/api/user/' . $user . '?alt=json';
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($ch);
	if ($response == 'Unable to find user with email ' . $user || $response == 'Invalid version at start of obfuscated ID ' . $user) {
		return false;
	} elseif ($raw = true) {
		return $response;
	} else {
		return json_decode($response, true);
	}
	curl_close($ch);
}

function image($url, $alt, $h='', $w=''){
	return '<img width="' . $w . '" height="' . $h . '" src="' . $url . '" alt="' . $alt . '">';
}

function anchor($url, $name, $title, $target = '_self'){
	return '<a href="' . $url . '" target="' . $target . '" title="' . $title . '">' . $name . '</a>';
}

function span($text, $class='', $id=''){
	return '<span class="' . $class . '" id="' . $id . '">' . $text . '</span>';
}

function headings($text, $name=1, $class='', $id=''){
	switch ($name) {
		case 1: return '<h1 class="' . $class . '" id="' . $id . '">' . $text . '</h1>'; break;
		case 2: return '<h2 class="' . $class . '" id="' . $id . '">' . $text . '</h2>'; break;
		case 3: return '<h3 class="' . $class . '" id="' . $id . '">' . $text . '</h3>'; break;
		case 4: return '<h4 class="' . $class . '" id="' . $id . '">' . $text . '</h4>'; break;
		case 5: return '<h5 class="' . $class . '" id="' . $id . '">' . $text . '</h5>'; break;
		case 6: return '<h6 class="' . $class . '" id="' . $id . '">' . $text . '</h6>'; break;
		default: return '<h1>' . $text . '</h1>'; break;
	}

}

function validated($data){
	if (empty($data)) {
		return 0;
	} else {
		return 1;
	}
}

if (!empty($_POST['id'])) {
	$json = crawl($_POST['id']);
	if (validated($json)) {
		print webface();
		$data = json_decode($json, true);
		$gallery = $data['entry']['author'][0]['uri']['$t'];
		$user = $data['entry']['gphoto$user']['$t'];
		$nickname = $data['entry']['gphoto$nickname']['$t'];
		$thumb = $data['entry']['gphoto$thumbnail']['$t'];

		print image($thumb, "$nickname Photos", '256px', '256px');
		print headings($nickname) . span('User: ' . $user, 'user') . '<br>';
		print anchor($gallery, 'Photo Gallery', $nickname . ' Photos', '_blank') . '<br>';
		print anchor('?refresh=true', $SVG, 'Refresh Crawler', '_self');
	} else {
		print "<h1>404 Not Found</h1>";
	}
} elseif (isset($_GET['refresh'])) {
	@clearstatcache();
	header('Location: ' . $_SERVER['PHP_SELF'], true, 303);
} elseif (isset($_GET['help'])) {
	print webface();
	print '<h1>Whut is dis?</h1> Photos crawler from Google Profile using Googl API, built from Monday 22 October 2018 at 02.15AM until 05.00AM (3H DeepDev)';
	print '<h1>H3LP!!!</h1> W3ll Hll0, doo u need 4PI? yes we hav it for u. and wee r here to help you node the request and anonymously crawl data<br>';
	print '<h1>H0W 2 USE?</h1> just use "<a title="YE5 DIS IS THE URI!!!" href="?refresh">' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?user=${email or user here}</a>" then jason will coming.';
	exit;
} elseif (!empty($_GET['user'])) {
	header('server: Unknown');
	header('cache-control: private, max-age=0, must-revalidate, no-transform');
	header('X-Powered-By: Fray117');
	header('content-type: application/json; charset=UTF-8');
	print crawl($_GET['user']);
	exit;
} else {
	print webface();
	print '<img alt="Google" id="hplogo" src="https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png" srcset="https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png 1x, https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png 2x" style="padding-top:109px" onload="window.lol&amp;&amp;lol()" data-atf="3" width="272" height="92">';
	print '<form autocomplete="list" method="POST"><input title="Google Profile Crawler" type="text" name="id"></form>';
	print 'This t00ls is Legal cuz the API is Public, u also can use the 4PI weed 0ur <a href="?help">nodes</a>.';
}
