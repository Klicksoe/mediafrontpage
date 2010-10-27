<?php
//require_once "../config.php";
//require_once "../functions.php";

$videodetailfields = '"genre", "director", "trailer", "tagline", "plot", "plotoutline", "title", "originaltitle", "lastplayed", "showtitle", "firstaired", "duration", "season", "episode", "runtime", "year", "playcount", "rating", "writer", "studio", "mpaa", "premiered", "album"';

function executeVideo($style = "w", $action, $breadcrumb, $params = array()) {
	global $COMM_ERROR;
	global $videodetailfields;

	$breadcrumbs = explode("|", $breadcrumb);
	$previousaction = end($breadcrumbs);
	
	switch ($action) {
		case "l":  // Library
			displayLibraryMenu($style, $params);
			break;
		case "lp": // Photo Library
			displayLibraryPhotoMenu($style, $params);
			break;
		case "lv": // Video Library
			displayLibraryVideoMenu($style, $params);
			break;
		case "lm": // Music Library
			displayLibraryMusicMenu($style, $params);
			break;
		case "p":  // Play
			if (($previousaction == "re") || ($previousaction == "e")) {
				if ($previousaction == "re") {
					$request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetRecentlyAddedEpisodes", "params" : { "fields": [ '.$videodetailfields.' ] }, "id" : 1 }';
				} else {
					$showid = $params['showid'];
					$season = $params['season'];
					$request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetEpisodes", "params" : { "tvshowid" : '.$showid.', "season" : '.$season.', "fields": [ '.$videodetailfields.' ] }, "id" : 1 }';
				}
				$results = jsoncall($request);
				$videos = $results['result']['episodes'];
				$typeId = "episodeid";
			} elseif (($previousaction == "rm") || ($previousaction == "m")) {
				$request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies", "params": { "sortorder" : "ascending", "fields" : [ '.$videodetailfields.' ] }, "id": 1}';
				$results = jsoncall($request);
				$videos = $results['result']['movies'];
				$typeId = "movieid";
			}
			if (!empty($videos)) {
				$videoId = $params['videoid'];
				playVideoFromList($videos, $typeId, $videoId); 
			} else {
				echo $COMM_ERROR;
				echo "<pre>$request</pre>";
			}
			//break;  // Don't break and flow into display.
		case "d":  // Display
			if (($previousaction == "re") || ($previousaction == "e")) {
				if ($previousaction == "re") {
					$request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetRecentlyAddedEpisodes", "params" : { "fields": [ '.$videodetailfields.' ] }, "id" : 1 }';
				} else {
					$showid = $params['showid'];
					$season = $params['season'];
					$request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetEpisodes", "params" : { "tvshowid" : '.$showid.', "season" : '.$season.', "fields": [ '.$videodetailfields.' ] }, "id" : 1 }';
				}
				$results = jsoncall($request);
				$videos = $results['result']['episodes'];
				$params['typeid'] = "episodeid";
			} elseif (($previousaction == "rm") || ($previousaction == "m")) {
				$request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies", "params": { "sortorder" : "ascending", "fields" : [ '.$videodetailfields.' ] }, "id": 1}';
				$results = jsoncall($request);
				$videos = $results['result']['movies'];
				$params['typeid'] = "movieid";
			}
			if (!empty($videos)) {
				displayVideoFromList($videos, $style, $action, $breadcrumb, $params);
			} else {
				echo $COMM_ERROR;
				echo "<pre>$request</pre>";
			}
			break;
		case "t":  // TV Shows
			$request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetTVShows", "id" : 1 }';
			$results = jsoncall($request);
			if (!empty($results['result'])) {
				$videos = $results['result']['tvshows'];
				displayVideoListTVShows($videos, $style, $action, $breadcrumb, $params);
			} else {
				echo $COMM_ERROR;
				echo "<pre>$request</pre>";
			}
			break;
		case "s":  // Seasons
			$showid = $params['showid'];
			$request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetSeasons", "params" : { "tvshowid" : '.$showid.', "fields": [ "genre", "title", "showtitle", "duration", "season", "episode", "year", "playcount", "rating", "studio", "mpaa" ] }, "id" : 1 }';
			$results = jsoncall($request);
			if (!empty($results['result'])) {
				$videos = $results['result']['seasons'];
				displayVideoListSeasons($videos, $style, $action, $breadcrumb, $params);
			} else {
				echo $COMM_ERROR;
				echo "<pre>$request</pre>";
			}
			break;
		case "e":  // Episodes
			$showid = $params['showid'];
			$season = $params['season'];
			$request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetEpisodes", "params" : { "tvshowid" : '.$showid.', "season" : '.$season.', "fields": [ '.$videodetailfields.' ] }, "id" : 1 }';
			$results = jsoncall($request);
			if (!empty($results['result'])) {
				$videos = $results['result']['episodes'];
				displayVideoListEpisodes($videos, $style, $action, $breadcrumb, $params);
			} else {
				echo $COMM_ERROR;
				echo "<pre>$request</pre>";
			}
			break;
		case "re": // Recent Episodes
			if(!empty($params['count'])) {
				$count = $params['count'];
			} else {
				$count = 15;
			}
			$request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetRecentlyAddedEpisodes", "params" : { "start" : 0 , "end" : '.$count.' , "fields": [ '.$videodetailfields.' ] }, "id" : 1 }';
			$results = jsoncall($request);
			if (!empty($results['result'])) {
				$videos = $results['result']['episodes'];
				displayVideoListEpisodes($videos, $style, $action, $breadcrumb, $params);
			} else {
				echo $COMM_ERROR;
				echo "<pre>$request</pre>";
			}
			break;
		case "m":  // Movies
			$request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies", "params": { "sortorder" : "ascending", "fields" : [ '.$videodetailfields.' ] }, "id": 1}';
			$results = jsoncall($request);
			if (!empty($results['result'])) {
				$videos = $results['result']['movies'];
				displayVideoListMovie($videos, $style, $action, $breadcrumb, $params);
			} else {
				echo $COMM_ERROR;
				echo "<pre>$request</pre>";
			}
			break;
		case "rm": // Recent Movies
			if(!empty($params['count'])) {
				$count = $params['count'];
			} else {
				$count = 15;
			}
			$request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetRecentlyAddedMovies", "params": { "start" : 0 , "end" : '.$count.' , "fields" : [ '.$videodetailfields.' ] }, "id" : 1 }';
			$results = jsoncall($request);
			if (!empty($results['result'])) {
				$videos = $results['result']['movies'];
				displayVideoListMovie($videos, $style, $action, $breadcrumb, $params);
			} else {
				echo $COMM_ERROR;
				echo "<pre>$request</pre>";
			}
			break;
		case "mv": // Music Videos
			echo "<ul><li>Not Supported Yet</li></ul>";
			$anchor = buildBackAnchor($style, "l|lv", $params);
			echo "<div class='widgetoptions tvoptions'>".$anchor."</div>\n";
			break;
	}
}
function getParameters($request) {
	$params = array();
	if(!empty($request['c'])) {
		$params['count'] = $request['c'];
	}
	if(!empty($request['showid'])) {
		$params['showid'] = $request['showid'];
	}
	if(!empty($request['season'])) {
		$params['season'] = $request['season'];
	}
	if(!empty($request['videoid'])) {
		$params['videoid'] = $request['videoid'];
	}

	return $params;
}

function renderMenu($data) {
	echo "<ul>\n";
	foreach ($data as $id => $info) {
		echo "\t<li><a href=\"".$info['href']."\" id='".$id."' class='menu'".$info['onclick'].">".$info['label']."</a></li>\n";
	}
	echo "</ul>\n";
}

function displayLibraryMenu($style, $params) {
	if ($style == "w") {
		$data = array (
						  "menu-lp" => array( "href" => "#", "onclick" => " onclick=\"".$params['onclickcmd']."('".$params['wrapper']."', '".$params['harness']."', 'lp', 'l', '');\"", "label" => "Photos")
						, "menu-lv" => array( "href" => "#", "onclick" => " onclick=\"".$params['onclickcmd']."('".$params['wrapper']."', '".$params['harness']."', 'lv', 'l', '');\"", "label" => "Videos")
						, "menu-lm" => array( "href" => "#", "onclick" => " onclick=\"".$params['onclickcmd']."('".$params['wrapper']."', '".$params['harness']."', 'lm', 'l', '');\"", "label" => "Music")
					  );
	} else {
		$data = array (
							  "menu-lp" => array( "href" => "wXBMCLibrary.php?style=s&a=lp&bc=l", "onclick" => "", "label" => "Photos")
						, "menu-lv" => array( "href" => "wXBMCLibrary.php?style=s&a=lv&bc=l", "onclick" => "", "label" => "Videos")
						, "menu-lm" => array( "href" => "wXBMCLibrary.php?style=s&a=lm&bc=l", "onclick" => "", "label" => "Music")
					  );
	}

	renderMenu($data);
}
function displayLibraryPhotoMenu($style, $params) {
	echo "<ul><li>Not Supported Yet</li></ul>";

	$anchor = buildBackAnchor($style, "l", $params);
	echo "<div class='widgetoptions tvoptions'>".$anchor."</div>\n";
}
function displayLibraryVideoMenu($style, $params) {
	if ($style == "w") {
		$data = array (
						  "menu-t" => array( "href" => "#", "onclick" => " onclick=\"".$params['onclickcmd']."('".$params['wrapper']."', '".$params['harness']."', 't', 'l|lv', '');\"", "label" => "TV Shows")
						, "menu-m" => array( "href" => "#", "onclick" => " onclick=\"".$params['onclickcmd']."('".$params['wrapper']."', '".$params['harness']."', 'm', 'l|lv', '');\"", "label" => "Movies")
						, "menu-re" => array( "href" => "#", "onclick" => " onclick=\"".$params['onclickcmd']."('".$params['wrapper']."', '".$params['harness']."', 're', 'l|lv', '');\"", "label" => "Recent Episodes")
						, "menu-rm" => array( "href" => "#", "onclick" => " onclick=\"".$params['onclickcmd']."('".$params['wrapper']."', '".$params['harness']."', 'rm', 'l|lv', '');\"", "label" => "Recent Movies")
						, "menu-mv" => array( "href" => "#", "onclick" => " onclick=\"".$params['onclickcmd']."('".$params['wrapper']."', '".$params['harness']."', 'mv', 'l|lv', '');\"", "label" => "Music")
					  );
	} else {
		$data = array (
						  "menu-t" => array( "href" => "wXBMCLibrary.php?style=s&a=t&bc=l|lv", "onclick" => "", "label" => "TV Shows")
						, "menu-m" => array( "href" => "wXBMCLibrary.php?style=s&a=m&bc=l|lv", "onclick" => "", "label" => "Movies")
						, "menu-re" => array( "href" => "wXBMCLibrary.php?style=s&a=re&bc=l|lv", "onclick" => "", "label" => "Recent Episodes")
						, "menu-rm" => array( "href" => "wXBMCLibrary.php?style=s&a=rm&bc=l|lv", "onclick" => "", "label" => "Recent Movies")
						, "menu-mv" => array( "href" => "wXBMCLibrary.php?style=s&a=mv&bc=l|lv", "onclick" => "", "label" => "Music")
					  );
	}

	renderMenu($data);

	$anchor = buildBackAnchor($style, "l", $params);
	echo "<div class='widgetoptions tvoptions'>".$anchor."</div>\n";
}
function displayLibraryMusicMenu($style, $params) {
	echo "<ul><li>Not Supported Yet</li></ul>";

	$anchor = buildBackAnchor($style, "l", $params);
	echo "<div class='widgetoptions tvoptions'>".$anchor."</div>\n";
}

function getTVShowId($showtitle) {
	$return = -1;

	$request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetTVShows", "id" : 1 }';
	$results = jsoncall($request);
	$videos = $results['result']['tvshows'];

	foreach ($videoList as $videoInfo) {
		if(!empty($videoInfo['showtitle']) && ($videoInfo['showtitle'] == $showtitle)) {
			if (!empty($videoInfo['tvshowid'])) {
				$return = $videoInfo['tvshowid'];
			}
			break;
		}
	}
}

function playVideoFromList($videoList, $idType = "episodeid", $videoId = -1) {
	foreach ($videoList as $videoInfo) {
		if(!empty($videoInfo[$idType]) && ($videoInfo[$idType] == $videoId) && !empty($videoInfo['file'])) {
			$videoLocation = $videoInfo['file'];
			$request = '{"jsonrpc" : "2.0", "method": "XBMC.Play", "params" : { "file" : "' . $videoLocation . '"}, "id": 1}';
			jsoncall($request);
			break;
		}
	}
}

function displayVideoFromList($videoList, $style, $action, $breadcrumb, $params) {
	$idType = $params['typeid'];
	foreach ($videoList as $videoInfo) {
		if(!empty($videoInfo[$idType]) && ($videoInfo[$idType] == $params['videoid'])) {
			switch($idType) {
				case "episodeid": // Episodes
					displayVideoEpisode($videoInfo, $style, $action, $breadcrumb, $params);
					break;
				case "movieid":   // Movies
					displayVideoMovie($videoInfo, $style, $action, $breadcrumb, $params);
					break;
			}
			break;
		}
	}
}

function displayVideoEpisode($videoInfo, $style, $action, $breadcrumb, $params) {
	global $xbmcimgpath;
	
	echo "<div id='recentTV'>\n";
	echo "\t<div class='tvtitle'><h1>".$videoInfo['showtitle']."</h1></div>\n";
	echo "\t<div class='tvinfo'>\n";
	echo "\t<span class='tvimg'>\n";

	if(!empty($videoInfo['thumbnail'])) {
		echo "\t\t<a href=\"".$xbmcimgpath.$videoInfo['thumbnail']."\" class=\"highslide\" onclick=\"return hs.expand(this)\">\n";
		echo "\t\t\t<img src='".$xbmcimgpath.$videoInfo['thumbnail']."' title='Click to enlarge'/>\n";
		echo "\t\t<a>\n";
	} elseif(!empty($videoInfo['fanart'])) {
		echo "\t\t<a href=\"".$xbmcimgpath.$videoInfo['fanart']."\" class=\"highslide\" onclick=\"return hs.expand(this)\">\n";
		echo "\t\t\t<img src='".$xbmcimgpath.$videoInfo['fanart']."' title='Click to enlarge'/>\n";
		echo "\t\t<a>";
	}
	echo "<div class=\"highslide-caption\">"; 
	echo $videoInfo['showtitle']." - ".$videoInfo['season']."x".str_pad($videoInfo['episode'], 2, '0', STR_PAD_LEFT)." - ".$videoInfo['label']."<br />\n";
	echo "\t\t".$videoInfo['plot']."\n";
	echo "\t\t</div>\n"; 

	echo "\t</span>\n";
	echo "\t<span class='tvdesc'>\n";
	echo "\t\t<p>";
	echo "\t\t\t<strong>Season: ".$videoInfo['season']." Episode: ".$videoInfo['episode']."<br />".$videoInfo['label']."</strong>";
	//echo "\t\t\t<strong>".$videoInfo['season']."x".str_pad($videoInfo['episode'], 2, '0', STR_PAD_LEFT)."<br />".$videoInfo['label']."</strong>";
	echo "\t\t</p>\n";
	echo "\t\t<p class=\"plot\">".$videoInfo['plot']."</p>\n";
	if(!empty($videoInfo['firstaired'])) {
		echo "\t\t<p>Aired: ".$videoInfo['firstaired']."</p>\n";
	}
	
	if(!empty($videoInfo['duration'])) {
		echo "\t\t<p>Runtime: ".(int)($videoInfo['duration']/60)." min.</p>\n";
	} elseif(!empty($videoInfo['runtime'])) {
		echo "\t\t<p>Runtime: ".$videoInfo['runtime']." min.</p>\n";
	}
	if(!empty($videoInfo['rating'])) {
		echo "\t\t<p>Rating: ".number_format($videoInfo['rating'], 1)."</p>\n";
	}
	echo "\t</span>\n";
	echo "\t</div>\n";
	
	$query = "&showid=".$params['showid']."&season=".$params['season']."&videoid=".$videoInfo["episodeid"];
	$playanchor = buildAnchor("Play", $style, "", "", "p", $breadcrumb, $params, $query);
	$backanchor = buildBackAnchor($style, $breadcrumb, $params, "&showid=".$params["showid"]."&season=".$videoInfo["season"]);
	echo "\t<div class='tvoptions'>".$playanchor." | ".$backanchor."</div>\n";
	echo "</div>\n";
}

function displayVideoMovie($videoInfo, $style, $action, $breadcrumb, $params) {
	global $xbmcimgpath;
	
	echo "<div id='movies'>\n";
	echo "\t<div class='movietitle'><h1>".$videoInfo['label']." &nbsp;(".$videoInfo['year'].")</h1></div>\n";
	echo "\t<div class='movieinfo'>\n";
	echo "\t<span class='movieimg'>\n";

	if(!empty($videoInfo['thumbnail'])) {
		echo "\t\t<a href=\"".$xbmcimgpath.$videoInfo['thumbnail']."\" class=\"highslide\" onclick=\"return hs.expand(this)\">\n";
		echo "\t\t\t<img src='".$xbmcimgpath.$videoInfo['thumbnail']."' title='Click to enlarge'/>\n";
		echo "\t\t<a>\n";
	} elseif(!empty($videoInfo['fanart'])) {
		echo "\t\t<a href=\"".$xbmcimgpath.$videoInfo['thumbnail']."\" class=\"highslide\" onclick=\"return hs.expand(this)\">\n";
		echo "\t\t\t<img src='".$xbmcimgpath.$videoInfo['fanart']."' title='Click to enlarge'/>\n";
		echo "\t\t<a>\n";
	}
	echo "\t\t<div class=\"highslide-caption\">\n"; 
	echo "\t\t".$videoInfo['label']." &nbsp;(".$videoInfo['year'].")<br />\n";
	echo "\t\t".$videoInfo['plot']."\n";
	echo "\t\t</div>\n"; 

	echo "\t</span>\n";
	echo "\t<span class='moviedesc'>\n";
	if($videoInfo['originaltitle'] != $videoInfo['title']) {
		echo "\t\t<p>".$videoInfo['originaltitle']."</p>\n";
	}
	echo "\t\t<p>".$videoInfo['genre']."</p>\n";
	echo "\t\t<p class=\"plot\">".$videoInfo['plot']."</p>\n";
	if(!empty($videoInfo['premiered'])) {
		echo "\t\t<p>Premiered: ".$videoInfo['premiered']."</p>\n";
	}
	if(!empty($videoInfo['director'])) {
		echo "\t\t<p>Director: ".$videoInfo['director']."</p>\n";
	}
	if(!empty($videoInfo['runtime'])) {
		echo "\t\t<p>Runtime: ".$videoInfo['runtime']." min.</p>\n";
	}
	if(!empty($videoInfo['rating'])) {
		echo "\t\t<p>Rating: ".number_format($videoInfo['rating'], 1)."</p>\n";
	}
	echo "\t</span>\n";
	echo "\t</div>\n";
	$playanchor = buildAnchor("Play", $style, "", "", "p", $breadcrumb, $params, "&videoid=".$videoInfo["movieid"]);
	$backanchor = buildBackAnchor($style, $breadcrumb, $params, "");
	echo "\t<div class='movieoptions'>".$playanchor." | ".$backanchor."</div>\n";
	echo "</div>\n";
}

function displayVideoListTVShows($videos, $style, $action, $breadcrumb, $params) {
	$newbreadcrumb = getNewBreadcrumb($action, $breadcrumb);

	echo "<ul>";
	if (!empty($videos)) {
		foreach ($videos as $videoInfo) {
			$label = $videoInfo['label'];
			$id = "tvshow-".$videoInfo["tvshowid"];
			$class = "recent-tv";
			$query = "&showid=".$videoInfo["tvshowid"];
			$anchor = buildAnchor($label, $style, $id, $class, "s", $newbreadcrumb, $params, $query);
			echo "<li>".$anchor."</li>\n";
		}
	} else {
		echo "<li>[empty]</li>\n";
	}
	echo "</ul>";

	$anchor = buildBackAnchor($style, $breadcrumb, $params);
	echo "<div class='tvoptions'>".$anchor."</div>\n";
}

function displayVideoListSeasons($videos, $style, $action, $breadcrumb, $params) {
	$newbreadcrumb = getNewBreadcrumb($action, $breadcrumb);

	echo "<ul>";
	if (!empty($videos)) {
		foreach ($videos as $videoInfo) {
			$label = $videoInfo['showtitle']." - ".$videoInfo['label'];
			$id = "season-".$videoInfo["season"];
			$class = "recent-tv";
			$query = "&showid=".$params["showid"]."&season=".$videoInfo["season"];
			$anchor = buildAnchor($label, $style, $id, $class, "e", $newbreadcrumb, $params, $query);
			echo "<li>".$anchor."</li>\n";
		}
	} else {
		echo "<li>[empty]</li>\n";
	}
	echo "</ul>";

	$anchor = buildBackAnchor($style, $breadcrumb, $params, "&showid=".$params["showid"]);
	echo "<div class='tvoptions'>".$anchor."</div>\n";
}

function displayVideoListEpisodes($videos, $style, $action, $breadcrumb, $params) {
	$newbreadcrumb = getNewBreadcrumb($action, $breadcrumb);

	echo "<ul>";
	if (!empty($videos)) {
		foreach ($videos as $videoInfo) {
			if(!empty($videoInfo['label'])) {
				$title = " - ".$videoInfo['label'];
			} else {
				$title = "";
				if(!empty($videoInfo['season'])) {
					$title .= " Season: ".$videoInfo['season'];
				}
				if(!empty($videoInfo['episode'])) {
					$title .= " Episode: ".$videoInfo['episode'];
				}
				if($title != "") {
					$title = " - ".$title;
				}
			}
			$label = $videoInfo['showtitle']." - ".$videoInfo['season']."x".str_pad($videoInfo['episode'], 2, '0', STR_PAD_LEFT).$title;
			$id = "episode-".$videoInfo["episodeid"];
			$class = "recent-tv";
			$query = "&showid=".$params["showid"]."&season=".$videoInfo["season"]."&videoid=".$videoInfo["episodeid"];
			$anchor = buildAnchor($label, $style, $id, $class, "d", $newbreadcrumb, $params, $query);
			echo "<li>".$anchor."</li>\n";
		}
	} else {
		echo "<li>[empty]</li>\n";
	}
	echo "</ul>";

	$anchor = buildBackAnchor($style, $breadcrumb, $params, "&showid=".$params["showid"]."&season=".$videoInfo["season"]);
	echo "<div class='tvoptions'>".$anchor."</div>\n";
}

function displayVideoListMovie($videos, $style, $action, $breadcrumb, $params) {
	$newbreadcrumb = getNewBreadcrumb($action, $breadcrumb);

	echo "<ul>";
	foreach ($videos as $videoInfo) {
		$label = $videoInfo['label']." &nbsp;(".$videoInfo['year'].")";
		$id = "movie-".$videoInfo["movieid"];
		$class = "recent-movie";
		$query = "&videoid=".$videoInfo["movieid"];
		$anchor = buildAnchor($label, $style, $id, $class, "d", $newbreadcrumb, $params, $query);
		echo "<li>".$anchor."</li>\n";
	}
	echo "</ul>";

	$anchor = buildBackAnchor($style, $breadcrumb, $params);
	echo "<div class='movieoptions'>".$anchor."</div>\n";
}

function getNewBreadcrumb($action, $breadcrumb) {
	if(strlen($breadcrumb) > 0) {
		$newbreadcrumb = $breadcrumb."|".$action;
	} else {
		$newbreadcrumb = $action;
	}

	return $newbreadcrumb;
}

function buildAnchor($label, $style, $id, $class, $action, $breadcrumb, $params, $query = "") {
	if ($style == "w") {
		$onclick =  " onclick=\"".$params['onclickcmd']."('".$params['wrapper']."', '".$params['harness']."', '".$action."', '".$breadcrumb."', '".$query."');\"";
		$href = "#";
	} else {
		$href = $params['href']."?style=".$style."&a=".$action."&bc=".$breadcrumb.$query;
	}
	if (strlen($id) > 0) {
		$id = " id=\"".$id."\"";
	}
	if (strlen($class) > 0) {
		$class = " class=\"".$class."\"";
	}
	return "<a href=\"".$href."\"".$id.$class.$onclick.">".$label."</a>";
}

function buildBackAnchor($style, $breadcrumb, $params, $query = "") {
	if(strlen($breadcrumb) > 0) {
		$breadcrumbs = explode("|", $breadcrumb);	
		$previousaction = array_pop($breadcrumbs);
		$previousbreadcrumb = implode("|", $breadcrumbs);
		return buildAnchor("Back", $style, "", "", $previousaction, $previousbreadcrumb, $params, $query);
	}
}
?>