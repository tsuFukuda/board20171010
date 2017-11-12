<?php
require("DB_connection.php");
require("security.php");
$result = DB_all_tweets($pdo);
$img_ext = array("png", "jpg", "tiff", "jpeg", "gif", "bmp");
$movie_ext = array("mp4");
ini_set('session.cookie_httponly', true);
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="view_main.css">
    <title>メイン画面</title>
</head>
<body>
    <div class="title">IESO掲示板</div>
    <table class="menu" border="3">
        <tr class="test">
            <td align="center"><a href="view_main.php">ホーム</a></td>
            <td align="center"><a href="view_my_tweets.php">編集</a></td>
            <td align="center"><a href="view_access.php">検索</a></td>
            <td align="center"><a href="view_user_info.php">ユーザー情報変更</a></td>
            <td align="center"><a href="view_login.php">ログアウト</a></td>
            <!-- rootでログインしている時だけ、登録内容確認画面を表示するメニューが出る -->
            <?php if($_SESSION['username']=="root") { ?>
                <td align="center"><a href="root_view_register.php">登録内容確認</a></td>
            <?php } ?>
        </tr>
    </table>

    <div class="center_position">
        <!-- ヘッダー -->
        <div class="comment">コメント</div>
        <form class="comment_form" enctype="multipart/form-data" action="init.php" method="POST">
            <div class="comment_form_contents">
                <div class="header_name">本文<br>
                    <textarea class="textbox_comment", name="input_comment" placeholder="書き込み内容"></textarea>
                </div>
                <div>
                    <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
                    画像・動画を添付:
                    <input type="file" name="input_file" value="upload">
                </div>
            </div>
            <button class="fill_out_button" type="submit" name="action" value="contribution">書き込む</button>
        </form>

        <!-- 過去の投稿内容を表示 -->
        <?php if($result != ""){ ?>
            <?php while($row = $result->fetchObject()){ ?>
                <div class="previous_tweets_contents">
                    <div class="pp">
                        <div class="previous_tweets_contents_title">
                            <?=$row->id?>
                            <?=h($row->name)?>
                        </div>
                        <div class="previous_tweets_contents_time"><?=$row->created_time?></div>
                    </div>
                    <div class="previous_tweets_contents_comment">
                        <div><?=h($row->comment)?><br></div>
                        <?php if(in_array($row->ext, $img_ext)){ ?>
                            <img class="previous_tweets_file" src="<?="images/".$row->filename?>">
                        <?php } else if(in_array($row->ext, $movie_ext)) {?>
                            <video class="previous_tweets_file" src="<?="images/".$row->filename?>" controls>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>

    </body>
    </html>
