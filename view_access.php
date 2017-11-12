<!--
DBにおいて、NULL は 「未定義」または「不明」を表す。
値が入っているかどうかは、="" で判定する。
 -->

<?php
require("DB_connection.php");
require("security.php");
$result2 = DB_all_tweets($pdo);
$img_ext = array("png", "jpg", "tiff", "jpeg", "gif", "bmp");
$movie_ext = array("mp4");
session_start();

// 検索条件に入力された値から、sql文のwhere条件を構築する
function create_sql($number, $name, $year_from, $month_from, $day_from, $year_to, $month_to, $day_to, $file){
    $sql_num = ($number != "") ? "AND id = \"$number\" " : "";
    $sql_name = ($name != "") ? "AND name = \"$name\" " : "";

    $sql_time = "";
    if($year_from!="" && $month_from!="" && $day_from!="" && $year_to!="" && $month_to!="" && $day_to!=""){
        if(checkdate($month_from, $day_from, $year_from) === true && checkdate($month_to, $day_to, $year_to) == true){
            $time_from = $year_from."-".str_pad($month_from, 2, 0, STR_PAD_LEFT)."-".str_pad($day_from, 2, 0, STR_PAD_LEFT);
            // DBには秒まで含めた時間が記録されているので、21日までを検索する場合は22日までを範囲にしなければならない
            $time_to = $year_to."-".str_pad($month_to, 2, 0, STR_PAD_LEFT)."-".str_pad($day_to+1, 2, 0, STR_PAD_LEFT);
            $sql_time = "AND created_time BETWEEN \"$time_from\" AND \"$time_to\" ";
        }
    }

    switch ($file) {
        case 0:
            $sql_file = "AND image = \"\" ";
            break;
        case 1:
            $sql_file = "AND image != \"\" ";
            break;
        default:
            $sql_file = "";
            break;
    }

    $sql = $sql_num.$sql_name.$sql_time.$sql_file;
    if($sql != ""){
        $sql = preg_replace('/AND/', '', $sql, 1);
    }
    return $sql;
}

// 検索条件の値の受け取り
$number = $_POST['input_number'];
$name = $_POST['input_name'];
$year_from = $_POST['input_time_year_from'];
$month_from = $_POST['input_time_month_from'];
$day_from = $_POST['input_time_day_from'];
$year_to = $_POST['input_time_year_to'];
$month_to = $_POST['input_time_month_to'];
$day_to = $_POST['input_time_day_to'];
$file = $_POST['input_file'];

$sql = create_sql($number, $name, $year_from, $month_from, $day_from, $year_to, $month_to, $day_to, $file);

if($sql != ""){
    $result2 = DB_access($pdo, $sql);
}
?>



<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="view_access.css">
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
            <?php if($_SESSION['username']=="root") { ?>
            <td align="center"><a href="root_view_register.php">登録内容確認</a></td>
            <?php } ?>
        </tr>
    </table>

    <div class="center_position">
        <!-- ヘッダー -->
        <div class="comment">詳細検索</div>
        <form class="comment_form" enctype="multipart/form-data" action="view_access.php" method="POST">
            <div class="comment_form_contents">
                <div class="header_name">番号<br>
                    <input class="textbox_name" type="number" name="input_number" value="<?=h($number)?>" min="1" max=""/>
                </div>
                <div class="header_name">名前<br>
                    <input class="textbox_name" type="text" name="input_name" value="<?=h($name)?>"/>
                </div>
                <div class="header_name">投稿時間<br>
                    <!-- <input class="textbox_name" type="date" placeholder="(例)2016-01-01" name="input_time_from">〜
                    <input class="textbox_name" type="date" placeholder="(例)2016-01-01" name="input_time_to"> -->
                    <input class="input_time" type="number" name="input_time_year_from" value="<?=h($year_from)?>" min="2016" max="2100"> 年
                    <input class="input_time" type="number" name="input_time_month_from" value="<?=h($month_from)?>" min="1" max="12"> 月
                    <input class="input_time" type="number" name="input_time_day_from" value="<?=h($day_from)?>" min="1" max="31"> 日　〜　
                    <input class="input_time" type="number" name="input_time_year_to" value="<?=h($year_to)?>" min="2016" max="2100"> 年
                    <input class="input_time" type="number" name="input_time_month_to" value="<?=h($month_to)?>" min="1" max="12"> 月
                    <input class="input_time" type="number" name="input_time_day_to" value="<?=h($day_to)?>" min="1" max="31"> 日
                </div>
                <div class="header_name">画像の有無<br>
                    <input type="radio" name="input_file" value="-1">指定しない
                    <input type="radio" name="input_file" value="1">画像あり
                    <input type="radio" name="input_file" value="0">画像なし
                </div>
            </div>
            <button class="fill_out_button" type="submit" name="action" value="access">検索</button>
        </form>

        <?php if($result2 != ""){ ?>
            <?php while($row = $result2->fetchObject()){ ?>
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
