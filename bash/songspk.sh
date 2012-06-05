function songspk () {
    currdir=`pwd`
    cd ~/.bashscripts/songspk/

    case "$#" in
        1)
            cat > console.php << __PHP_EOF__
<?php
    require './getsongspk.php';

    \$allsongs = getSongsList("$1");
    foreach(\$allsongs as \$movie => \$songs) {
        \$i = 0;
        echo "[+] Movie : \$movie\n";
        foreach(\$songs as \$song => \$url)
            echo ++\$i . "]\t" . \$song . "\n";
        echo "\n";
    }
?>
__PHP_EOF__
            echo -ne "Search songs\t\t`date`\t\t$1\n" > songs.log
            /opt/lampp/bin/php -f console.php
            wait
            ;;

        2)
            cat > console.php << __PHP_EOF__
<?php
    require './getsongspk.php';

    \$res = downloadSongs("$1", "$2");
    if(is_array(\$res))
        echo implode("\\n", \$res) . "\\n";
    else
        echo "\$res\\n";
__PHP_EOF__
            echo -ne "Download songs\t\t`date`\t\t$1\t\t$2\n" > songs.log
            /opt/lampp/bin/php -f console.php
            wait
            \mv *.mp3 "$currdir"
            ;;

        3)
            cat > console.php << __PHP_EOF__
<?php
    require './getsongspk.php';

    \$res = downloadSongs("$1", "$2");
    if(is_array(\$res))
        echo implode("\\n", \$res) . "\\n";
    else
        echo "\$res\\n";
__PHP_EOF__
            echo -ne "Download songs\t\t`date`\t\t$1\t\t$2\t\t$3\n" > songs.log
            /opt/lampp/bin/php -f console.php
            wait
            \mv *.mp3 "$3"
            ;;

        *)
            echo "$#"
    esac

    \rm -f console.php
    cd "$currdir"
}
