<!--
変更点
・aタグによるリンクから、buttonによるリンクに変更
-->


<?php
require("DB_connection.php");
require("security.php");
session_start();
// ログインユーザーがrootであれば全ユーザーの投稿内容を表示し、一般ユーザーならログインしているユーザーの投稿内容を表示する
$result = ($_SESSION['username'] == "root") ? DB_all_tweets($pdo) : DB_my_tweets($pdo);

$img_ext = array("png", "jpg", "tiff", "jpeg", "gif", "bmp");
$movie_ext = array("mp4");

// 編集する番号が空欄でなかった場合、編集する番号の投稿内容をresult2に格納
if($_POST['input_editing_num']!=""){
    $result2 = DB_get_selected_tweet($pdo, $_POST['input_editing_num']);
    if($result2->name != $_SESSION['username'] && $_SESSION['username']!="root"){
        $result2 = "";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="view_my_tweets.css">
    <title>メイン画面</title>
</head>

<body>
    <!-- タイトル -->
    <div class="title">IESO掲示板</div>
    <!-- メニュー -->
    <table class="menu" border="3">
        <tr class="test">
            <td align="center"><a href="view_main.php">ホーム</a></td>
            <td align="center"><a href="view_my_tweets.php">編集</a></td>
            <td align="center"><a href="view_access.php">検索</a></td>
            <td align="center"><a href="view_user_info.php">ユーザー情報変更</a></td>
            <td align="center"><a href="view_login.php">ログアウト</a></td>
        </tr>
    </table>

    <!-- 編集フォーム -->
    <div class="center_position">
        <div class="text_editing">編集</div>
        <div>
            <form class="editing_num" method="post" action="view_my_tweets.php">
                <div class="header_name">番号<br>
                    <input class="textbox_name" type="number" name="input_editing_num" value="<?=h($_POST['input_editing_num'])?>" min="1" max="" required />
                </div>
                <div class="show_tweet">
                    <!-- <button type="submit" class="button_num" name="editing_num">投稿内容表示</button> -->
                    <!-- <input type="submit"> -->
                    <button class="show_tweet_button" type="submit">表示</button>
                </div>
            </form>

            <form class="comment_form_edit" enctype="multipart/form-data" action="init.php" method="POST">
                <div class="comment_form_contents">
                    <input type="hidden" name="tweet_id" value="<?=h($_POST['input_editing_num'])?>">  <!-- 編集する投稿番号 -->
                    <!-- <div class="header_name">名前<br>
                        <input type="text" class="textbox_name" name="input_displayname" value="<?=h($result2->displayname)?>" />
                    </div> -->
                    <div class="header_name">本文<br>
                        <textarea class="textbox_comment", name="input_comment"><?=h($result2->comment)?></textarea>
                    </div>
                    <div>
                        <!-- 100Mまでの画像ならOK -->
                        <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
                        <input type="file" id="fopen" style="display:none;" name="input_file"/>

                        <div>
                            <?php if(in_array($result2->ext, $img_ext)){ ?>
                                <img class="previous_tweets_file" src="<?="images/".$result2->filename?>">
                            <?php } else if(in_array($result2->ext, $movie_ext)) { ?>
                                <video class="previous_tweets_file" src="<?="images/".$result2->filename?>" controls>
                                <?php } ?>
                            </div>
                            <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
                            画像・動画を添付:
                            <!-- buttonタグを使いたいが、アップロードファイル名が表示されないので、type=fileを使用 -->
                            <input class="file_upload" type="file" name="input_file" value="upload">
                        </div>
                    </div>
                    <div class="save_or_delete">
                        <button class="edit_save_button" type="submit" name="action" value="decide_editing">編集を保存する</button>
                        <button class="delete_button" type="submit" name="action" value="delete_tweet">投稿内容を削除</button>
                    </div>
                </form>
            </div>

            <!-- 過去にログインしているユーザーが投稿したものを表示 -->
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
                </div>

            <?php } ?>
        </div>



    </body>
    </html>
