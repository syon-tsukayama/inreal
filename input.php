<?php

/**
 * インリアル
 */

// 共通機能読み込み
require_once('./common.php');

$error_message = '';

if(!empty($_POST))
{
    print_r($_POST);
}

$dir_name = '';

// GET変数からディレクトリ名取得
if(isset($_GET['dir_name']))
{
    $dir_name = trim($_GET['dir_name']);
}

if(empty($dir_name))
{
    // ディレクトリ名が空っぽの場合は、ディレクトリ一覧へ戻る
    header('Location: dir_list.php');
}

// ディレクトリパス設定
$_dir_name = rtrim($dir_name, DS);
$_dir_path = IMAGES_BASE_PATH.DS.$_dir_name;

// ディレクトリ内の画像ファイル名取得
$_img_file_name_array = get_img_lists($_dir_path);




// 画像がない場合
if(count($_img_file_name_array) == 0)
{
    header('Location: dir_list.php');
}

$_max_img_index = max(array_keys($_img_file_name_array));

$_file_name = '';

// GET変数からファイル名取得
if(isset($_GET['file_name']))
{
    $_file_name = trim($_GET['file_name']);
}

if(empty($_file_name))
{
    // ファイル名の指定がない場合、ディレクトリ内の最初のファイルを設定
    if(isset($_img_file_name_array[0]))
    {
        $_img_index = 0;
        $_file_name = $_img_file_name_array[0];
    }
}
else
{
    $_img_index = array_search($_file_name, $_img_file_name_array);
}

$_file_path = rtrim($_dir_path, DS).DS.$_file_name;

if(!file_get_contents($_file_path, null, null, 1, 1))
{
    header('Location: dir_list.php');
}

// 前へ、次へのリンク作成のため
// 表示ファイルの前後のファイル名取得

try
{
    // データベース接続
    $conn = connect_database();
}
catch(PDOException $e)
{
    $error_message = $e->getMessage();
}

// ディレクトリ名、ファイル名でデータベース検索
if(1 == count_data_file_path($conn, $_dir_name, $_file_name))
{
    // 初期表示時のデータ取得
    $id = get_id_file_path($conn, $_dir_name, $_file_name);

    if(empty($id))
    {
        $motion_history = get_motion_history($conn, $id);
    }
}
else
{
    $id = 0;
}




$subject = 'どの楽器の音に興味を示したか把握する';
$grade_id = '';
$person_name = '';
$pose_id = '';
$record_date = '';
$exposure_time = '';
$speak_remark =<<<EOS
「カスタネットだよ」と言いながらカスタネットの音を鳴らして音を聞かせる
EOS
;
$part_name_1 = '親指';
$motion_level_1 = '';
$part_name_2 = '人差し指';
$motion_level_2 = '';
$part_name_3 = '';
$motion_level_3 = '';
$part_name_4 = '';
$motion_level_4 = '';
$motion_remark =<<<EOS
教師の方を見る
左手を動かす
EOS
;
$motion_type_id = '';

?>
<html>
    <head>
        <meta charset="utf-8">
        <title>インリアル</title>
        <!-- Bootstrap core CSS -->
        <link href="./css/bootstrap.min.css" rel="stylesheet">
        <link href="./css/bootstrap-datetimepicker.min.css" rel="stylesheet">
        <script src="./js/jquery-1.11.1.min.js"></script>
        <script src="./js/moment-with-langs.js"></script>
        <script src="./js/bootstrap.min.js"></script>
        <script src="./js/bootstrap-datetimepicker.min.js"></script>
        <script>
//<![CDATA[
$(function()
{
    $('.datepicker').datetimepicker(
    {
        language: 'ja',
        format: 'YYYY-MM-DD',
        pickTime: false
    });

});

//]]>
        </script>
    </head>

    <body>

        <div class="container-fluid">

            <div class="page-header">
                <h1>Example page header <small>Subtext for header</small></h1>
            </div>

            <?php
            // formのPOSTデータ送信先を作成
            $get_params = array(
                'dir_name' => $_dir_name,
                'file_name' => $_file_name
                );
            $form_action = create_html_href('input.php', $get_params);
            ?>
            <form action="<?php echo $form_action; ?>" method="post" role="form">

                <div class="row">
                    <div class="col-md-10 col-md-offset-1">

                        <?php echo create_html_error_alert($error_message); ?>

                        <div class="well">

                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">ねらい</div>
                                            <input type="text" name="subject" class="form-control" value="<?php echo $subject; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-block">登録</button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon">学部</span>
                                                    <select name="grade_id" class="form-control">
                                                        <?php echo create_html_select_options($_grade_id_options, $grade_id); ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon">氏名</span>
                                                    <input type="text" name="person_name" class="form-control" value="<?php echo $person_name; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon">姿勢</span>
                                                    <select name="pose_id" class="form-control">
                                                        <?php echo create_html_select_options($_pose_id_options, $pose_id); ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group datepicker date">
                                                    <span class="input-group-addon">記録日</span>
                                                    <input type="text" name="record_date" class="form-control" value="<?php echo $record_date; ?>">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon">撮影時間</span>
                                                    <input type="text" name="exposure_time" class="form-control" value="<?php echo $exposure_time; ?>">
                                                    <span class="input-group-addon">秒</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="well">

                            <div class="row">

                                <div class="col-md-7">
                                    <div class="panel panel-info">
                                        <div class="panel-heading">
                                            モーションヒストリーの画像<?php echo '：'.$_dir_name.DS.$_file_name; ?>
                                        </div>
                                        <div class="panel-body">
                                            <p>
                                                <img src="<?php echo $_file_path; ?>" class="img-responsive" alt="Responsive image">
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="panel panel-info">
                                                <div class="panel-heading">教師の言葉かけ</div>
                                                <div class="panel-body">
                                                    <div class="form-group">
                                                        <textarea name="speak_remark" class="form-control" rows="3"><?php echo $speak_remark; ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="panel panel-info">
                                                <div class="panel-heading">
                                                    体の動き：
                                                    少
                                                    <span class="label label-default">１</span>
                                                    &rarr;
                                                    <span class="label label-primary">２</span>
                                                    &rarr;
                                                    <span class="label label-info">３</span>
                                                    &rarr;
                                                    <span class="label label-success">４</span>
                                                    &rarr;
                                                    <span class="label label-warning">５</span>
                                                    &rarr;
                                                    <span class="label label-danger">６</span>
                                                    多
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-9">
                                                            <div class="form-group">
                                                                <input type="text" name="part_name_1" class="form-control" value="<?php echo $part_name_1; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <select name="motion_level_1" class="form-control">
                                                                    <?php echo create_html_select_options($_motion_level_options, $motion_level_1); ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-9">
                                                            <div class="form-group">
                                                                <input type="text" name="part_name_2" class="form-control" value="<?php echo $part_name_2; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <select name="motion_level_2" class="form-control">
                                                                    <?php echo create_html_select_options($_motion_level_options, $motion_level_2); ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-9">
                                                            <div class="form-group">
                                                                <input type="text" name="part_name_3" class="form-control" value="<?php echo $part_name_3; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <select name="motion_level_3" class="form-control">
                                                                    <?php echo create_html_select_options($_motion_level_options, $motion_level_3); ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-9">
                                                            <div class="form-group">
                                                                <input type="text" name="part_name_4" class="form-control" value="<?php echo $part_name_4; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <select name="motion_level_4" class="form-control">
                                                                    <?php echo create_html_select_options($_motion_level_options, $motion_level_4); ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="panel panel-info">
                                                <div class="panel-heading">身体の動きの様子</div>
                                                <div class="panel-body">
                                                    <div class="form-group">
                                                        <textarea name="motion_remark" class="form-control" rows="3"><?php echo $motion_remark; ?></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <select name="motion_type_id" class="form-control">
                                                            <?php echo create_html_select_options($_motion_type_id_options, $motion_type_id); ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="well">

                            <ul class="pager">
                                <?php
                                if($_img_index == 0)
                                {
                                    // 最初の画像なのでPrevious無効化
                                    $class = 'previous disabled';
                                }
                                else
                                {
                                    $class = 'previous';

                                    $file_name = $_img_file_name_array[($_img_index - 1)];

                                    $get_params = array(
                                        'dir_name' => $_dir_name,
                                        'file_name' => $file_name
                                        );
                                    $href = create_html_href('input.php', $get_params);
                                }
                                ?>
                                <li class="<?php echo $class; ?>"><a href="<?php echo $href; ?>">&larr; Previous</a></li>
                                <?php
                                if($_img_index == $_max_img_index)
                                {
                                    // 最後の画像なのでNext無効化
                                    $class = 'next disabled';
                                }
                                else
                                {
                                    $class = 'next';

                                    $file_name = $_img_file_name_array[($_img_index + 1)];

                                    $get_params = array(
                                        'dir_name' => $_dir_name,
                                        'file_name' => $file_name
                                        );
                                    $href = create_html_href('input.php', $get_params);
                                }
                                ?>
                                <li class="<?php echo $class; ?>"><a href="<?php echo $href; ?>">Next &rarr;</a></li>
                            </ul>

                            <div class="panel-group" id="accordion">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">リンク</a>
                                        </h4>
                                    </div>
                                    <div id="collapseOne" class="panel-collapse collapse">
                                        <div class="panel-body">

                                            <div class="list-group">
                                                <a href="dir_list.php" class="list-group-item">ディレクトリ一覧へ</a>
                                                <?php
                                                foreach($_img_file_name_array as $key => $file_name):
                                                    $get_params = array(
                                                        'dir_name' => $_dir_name,
                                                        'file_name' => $file_name
                                                        );
                                                    $href = create_html_href('input.php', $get_params);
                                                    if($key == $_img_index)
                                                    {
                                                        $active_class = ' active';
                                                    }
                                                    else
                                                    {
                                                        $active_class = '';
                                                    }
                                                ?>
                                                <a href="<?php echo $href; ?>" class="list-group-item<?php echo $active_class; ?>"><?php echo $file_name; ?></a>
                                            <?php endforeach; ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </form>

        </div>

    </body>

</html>