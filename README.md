GetSongsPK
============
A set of PHP functions to search and download music, by cURLing SongsPK.
These can be directly included in PHP scripts and then, it just takes 1 line to download songs of an entire movie.

The real power of these functions is realized when they are used along with a tiny bash script that enables you to
directly search and download songs with just a single bash line.

### How to use GetSongsPK ###
-----------------------------
Really simple. Just `require` the library file, and then `getSongsList` and `downloadSongs`:
```php
<?php
    require("getsongspk.php");

    var_dump(getSongsList('rockstar'));
?>
```

and

```php
<?php
    require("getsongspk.php");

    var_dump(downloadSongs('rockstar', 'Tum Ko - Kavita Subramaniam'));
    var_dump(downloadSongs('rockstar', '1, 4, 3'));
?>
```

### .: DISCLAIMER :. ###
------------------------
If you intend to use this piece of code, only **YOU** are responsible for what you use this code for.
The author does not take any responsibility for any kind of misuse of this code.
