<?php

/**
 * getSongsList
 * This function searches for all songs of a movie, by curling the SongsPK
 * interface.
 *
 * @author Saswat Padhi
 * @url https://github.com/SaswatPadhi/GetSongsPK
 *
 * @param String $movie The name (or at least beginning few letters) of the movie name.
 *
 * @return String/Array Error string or array or results for each movie.
 *
 * @example getSongsList ('rockstar');
 *
 * DISCLAMIER:
 * I (Saswat Padhi), am in no way responsible for illegal use of the following code.
 **/

function getSongsList ($movie)
{
    $movie = ucwords(trim($movie));
    if (strlen($movie) == 0)
        return "ERROR :: Empty movie name!";
    $movie = preg_replace('/\s+/', ' ', $movie);
    $pmovie = preg_replace('/\s+/', '[\s]*', $movie);
    $list = strtoupper(substr($movie, 0, 1));

    $res = array();
    $curl = curl_init();

    $curlOpts = array(
        CURLOPT_FOLLOWLOCATION  =>  1,
        CURLOPT_RETURNTRANSFER  =>  1,
        CURLOPT_MAXREDIRS       =>  20,
        CURLOPT_CONNECTTIMEOUT  =>  30,
        //CURLOPT_PROXY           =>  "http://netmon.iitb.ac.in:80/",
        //CURLOPT_PROXYUSERPWD    =>  "username:password",
        CURLOPT_URL             =>  "http://www.songspk.info/indian_movie/".$list."_List.html",
        CURLOPT_USERAGENT       =>  "Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:12.0) Gecko/20100101 Firefox/12.0"
    );
    curl_setopt_array($curl, $curlOpts);
    $content = curl_exec($curl);

    if (curl_errno($curl))
        return "ERROR :: Could not connect to SongsPK! (". curl_error($curl).")";

    $movie_pattern = '/a href="?([^\"]*)?.html" target="_parent">[\s]*' . $pmovie . '?([^a-zA-Z]*)?[\s]*</si';
    preg_match_all($movie_pattern, $content, $match);
    $index = -1;
    while(isset($match[1][++$index])) {
        $movieURL = "http://www.songspk.info/indian_movie/" . $match[1][$index] . ".html";
        curl_setopt($curl, CURLOPT_URL, $movieURL);
        $movie_content = curl_exec($curl);

        $sres = array();
        $song_pattern = '/a href="?([^\"]*link.songspk.info[^\"]*'.$list.'_List[^\"]*download.php\?id=[0-9]*)?">?([^>]*)?</si';
        preg_match_all($song_pattern, $movie_content, $songmatch);
        $sindex = -1;
        while(isset($songmatch[1][++$sindex])) {
            if(strcasecmp(substr($songmatch[2][$sindex], -4), "kbps") == 0)
                continue;
            $sres[$songmatch[2][$sindex]] = $songmatch[1][$sindex];
        }
        $res[$movie.$match[2][$index]] = $sres;
    }
    curl_close($curl);

    return $res;
}

/**
 * downloadSongs
 * This function searches for all the movies whose names match with the
 * name given as argument. And it downloads the song (2nd argument) from SongPK.
 * If there are multiple movies matching the name, an error is thrown.
 *
 * @author Saswat Padhi
 * @url https://github.com/SaswatPadhi/GetSongsPK
 *
 * @param String $movie The  name of the movie.
 * @param String $songnamesornumbers The names or numbers of the songs to be
 * downloaded, separated by comma or semicolon.
 *
 * @return String/Array Error string or array or results for each download.
 *
 * @example downloadSongs ('rockstar', "1, 4, 3");
 * @example downloadSongs ('rockstar', "Tum Ko - Kavita Subamaniam");
 *
 * DISCLAMIER:
 * I (Saswat Padhi), am in no way responsible for illegal use of the following code.
 **/

function downloadSongs ($movie, $songnamesornumbers) {
    $movie = ucwords(preg_replace('/\s+/', ' ', $movie));

    $res = array();
    $songlist = getSongsList($movie, true);
    if($songlist == null)
        return "No such movie $movie found!";
    elseif(count($songlist) > 1)
        return "Multiple movies matching name '$movie' found!";
    elseif(count($songlist) < 1)
        return "No such movie $movie found!";

    $songnamesornumbers = array_values(array_filter(array_map('trim', preg_split("/[\s]*[,;][\s]*/", $songnamesornumbers))));
    $songurl = "";
    $movie = array_keys($songlist);
    $movie = $movie[0];
    $songlist = $songlist[$movie];

    foreach($songnamesornumbers as $songnameornumber) {
        if(is_numeric($songnameornumber)) {
            $songnames = array_keys($songlist);
            if(array_key_exists(intval($songnameornumber) - 1, $songnames))
                $songurl = $songlist[($songnameornumber = $songnames[intval($songnameornumber) - 1])];
            else
                return;
        } else {
            foreach($songlist as $song => $url) {
                if(strcasecmp($song, $songnameornumber) == 0) {
                    $songurl = $url;
                    break;
                }
            }
            if($songurl == "") {
                $res[] = "Failed to get $songnameornumber";
                continue;
            }
        }

        $fp = fopen($songnameornumber.".mp3", 'w');
        $curl = curl_init();
        $curlOpts = array(
            CURLOPT_FOLLOWLOCATION  =>  1,
            CURLOPT_RETURNTRANSFER  =>  1,
            CURLOPT_MAXREDIRS       =>  20,
            CURLOPT_CONNECTTIMEOUT  =>  30,
            //CURLOPT_PROXY           =>  "http://netmon.iitb.ac.in:80/",
            //CURLOPT_PROXYUSERPWD    =>  "username:password",
            CURLOPT_URL             =>  $songurl,
            CURLOPT_FILE            =>  $fp,
            CURLOPT_USERAGENT       =>  "Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:12.0) Gecko/20100101 Firefox/12.0"
        );
        curl_setopt_array($curl, $curlOpts);
        curl_exec($curl);
        fclose($fp);
        $res[] = "$songnameornumber ~~~> $songnameornumber.mp3";
    }
    curl_close($curl);
    return $res;
}

?>
