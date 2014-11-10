<?php

/**
 * インリアル入力画面
 */

session_start();

// 共通機能読み込み
require_once('./common.php');

$success_message = '';
$error_message   = '';

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


try
{
    // データベース接続
    $conn = connect_database();
}
catch(PDOException $e)
{
    // 例外が発生した場合、エラーメッセージを取得
    $error_message = $e->getMessage();
}

// ディレクトリ名、ファイル名でデータベース検索＆件数取得
// 結果が1件の場合、既に登録データが存在するとみなし、ID取得
if(1 == count_data_file_path($conn, $_dir_name, $_file_name))
{
    // ディレクトリ名、ファイル名からデータのID取得
    $id = get_id_file_path($conn, $_dir_name, $_file_name);
}
else
{
    $id = 0;
}

if(empty($id))
{
    // POSTデータが存在する場合、新規登録
    if(isset($_POST['motion_history']) && !empty($_POST['motion_history']))
    {
        // ID無し、POSTデータ有り = データ新規登録処理

        if(insert_motion_history($conn, $_POST['motion_history'], $error_info))
        {
            $_SESSION['success_message'] = 'データを新規登録しました。';
        }
        else
        {
            $_SESSION['error_message'] = 'データの新規登録に失敗しました。<br>'.print_r($error_info, true);
        }

        // スクリプト終了前に別ページへ遷移するので、SESSIONデータを書き込み
        session_write_close();

        $get_params = array(
            'dir_name' => $_dir_name,
            'file_name' => $_file_name
            );
        $redirect_url = create_html_href('input.php', $get_params);
        header('Location: '.$redirect_url);
    }
    else
    {
        // ID無し、POSTデータ無し = 新規登録画面初期表示

        $html_subject        = '';
        $html_grade_id       = '';
        $html_person_name    = '';
        $html_pose_id        = '';
        $html_record_date    = date('Y-m-d');
        $html_exposure_time  = '0';
        $html_speak_remark   = '';
        $html_part_name_1    = '';
        $html_motion_level_1 = '';
        $html_part_name_2    = '';
        $html_motion_level_2 = '';
        $html_part_name_3    = '';
        $html_motion_level_3 = '';
        $html_part_name_4    = '';
        $html_motion_level_4 = '';
        $html_motion_remark  = '';
        $html_motion_type_id = '';

        // SESSIONデータを確認して、前画面からの引き継ぎデータの有無を判別
        if(isset($_SESSION['handover_data'][$_dir_name]) && is_array($_SESSION['handover_data'][$_dir_name]))
        {
            if(!empty($_SESSION['handover_data'][$_dir_name]['subject']))
            {
                $html_subject = htmlspecialchars($_SESSION['handover_data'][$_dir_name]['subject'], ENT_QUOTES);
            }

            if(!empty($_SESSION['handover_data'][$_dir_name]['grade_id']))
            {
                $html_grade_id = htmlspecialchars($_SESSION['handover_data'][$_dir_name]['grade_id'], ENT_QUOTES);
            }

            if(!empty($_SESSION['handover_data'][$_dir_name]['person_name']))
            {
                $html_person_name = htmlspecialchars($_SESSION['handover_data'][$_dir_name]['person_name'], ENT_QUOTES);
            }

            if(!empty($_SESSION['handover_data'][$_dir_name]['pose_id']))
            {
                $html_pose_id = htmlspecialchars($_SESSION['handover_data'][$_dir_name]['pose_id'], ENT_QUOTES);
            }

            if(!empty($_SESSION['handover_data'][$_dir_name]['record_date']))
            {
                $html_record_date = htmlspecialchars($_SESSION['handover_data'][$_dir_name]['record_date'], ENT_QUOTES);
            }

            if(!empty($_SESSION['handover_data'][$_dir_name]['exposure_time']))
            {
                $html_exposure_time = htmlspecialchars($_SESSION['handover_data'][$_dir_name]['exposure_time'], ENT_QUOTES);
            }

            if(!empty($_SESSION['handover_data'][$_dir_name]['part_name_1']))
            {
                $html_part_name_1 = htmlspecialchars($_SESSION['handover_data'][$_dir_name]['part_name_1'], ENT_QUOTES);
            }

            if(!empty($_SESSION['handover_data'][$_dir_name]['part_name_2']))
            {
                $html_part_name_2 = htmlspecialchars($_SESSION['handover_data'][$_dir_name]['part_name_2'], ENT_QUOTES);
            }

            if(!empty($_SESSION['handover_data'][$_dir_name]['part_name_3']))
            {
                $html_part_name_3 = htmlspecialchars($_SESSION['handover_data'][$_dir_name]['part_name_3'], ENT_QUOTES);
            }

            if(!empty($_SESSION['handover_data'][$_dir_name]['part_name_4']))
            {
                $html_part_name_4 = htmlspecialchars($_SESSION['handover_data'][$_dir_name]['part_name_4'], ENT_QUOTES);
            }

            unset($_SESSION['handover_data'][$_dir_name]);
        }
    }
}
else
{
    // POSTデータが存在する場合、更新
    if(isset($_POST['motion_history']) && !empty($_POST['motion_history']))
    {
        // ID有り、POSTデータ有り = データ更新処理

        if(update_motion_history($conn, $_POST['motion_history'], $error_info))
        {
            $_SESSION['success_message'] = 'データを更新しました。';
        }
        else
        {
            $_SESSION['error_message'] = 'データの更新に失敗しました。<br>'.print_r($error_info, true);
        }

        // スクリプト終了前に別ページへ遷移するので、SESSIONデータを書き込み
        session_write_close();

        $get_params = array(
            'dir_name' => $_dir_name,
            'file_name' => $_file_name
            );
        $redirect_url = create_html_href('input.php', $get_params);
        header('Location: '.$redirect_url);
    }
    else
    {
        // ID有り、POSTデータ無し = 更新画面初期表示

        // IDから初期表示のためのデータ取得
        $motion_history = get_motion_history($conn, $id);

        $html_subject        = htmlspecialchars($motion_history['subject'], ENT_QUOTES);
        $html_grade_id       = htmlspecialchars($motion_history['grade_id'], ENT_QUOTES);
        $html_person_name    = htmlspecialchars($motion_history['person_name'], ENT_QUOTES);
        $html_pose_id        = htmlspecialchars($motion_history['pose_id'], ENT_QUOTES);
        $html_record_date    = htmlspecialchars($motion_history['record_date'], ENT_QUOTES);
        $html_exposure_time  = htmlspecialchars($motion_history['exposure_time'], ENT_QUOTES);
        $html_speak_remark   = htmlspecialchars($motion_history['speak_remark'], ENT_QUOTES);
        $html_part_name_1    = htmlspecialchars($motion_history['part_name_1'], ENT_QUOTES);
        $html_motion_level_1 = htmlspecialchars($motion_history['motion_level_1'], ENT_QUOTES);
        $html_part_name_2    = htmlspecialchars($motion_history['part_name_2'], ENT_QUOTES);
        $html_motion_level_2 = htmlspecialchars($motion_history['motion_level_2'], ENT_QUOTES);
        $html_part_name_3    = htmlspecialchars($motion_history['part_name_3'], ENT_QUOTES);
        $html_motion_level_3 = htmlspecialchars($motion_history['motion_level_3'], ENT_QUOTES);
        $html_part_name_4    = htmlspecialchars($motion_history['part_name_4'], ENT_QUOTES);
        $html_motion_level_4 = htmlspecialchars($motion_history['motion_level_4'], ENT_QUOTES);
        $html_motion_remark  = htmlspecialchars($motion_history['motion_remark'], ENT_QUOTES);
        $html_motion_type_id = htmlspecialchars($motion_history['motion_type_id'], ENT_QUOTES);

        // 引き継ぐ値をSESSIONへ格納
        $_SESSION['handover_data'][$_dir_name] = $motion_history;
    }
}

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

                <?php if(!empty($id) && is_numeric($id)): ?>
                <input type="hidden" name="motion_history[id]" value="<?php echo $id; ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-10 col-md-offset-1">

                        <?php
                        // セッションデータ中のメッセージ確認
                        if(isset($_SESSION['success_message']))
                        {
                            $success_message .= $_SESSION['success_message'];
                            unset($_SESSION['success_message']);
                        }
                        echo create_html_success_alert($success_message);

                        if(isset($_SESSION['error_message']))
                        {
                            $error_message .= $_SESSION['error_message'];
                            unset($_SESSION['error_message']);
                        }
                        echo create_html_error_alert($error_message);
                        ?>

                        <div class="well">

                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">ねらい</div>
                                            <input type="text" name="motion_history[subject]" class="form-control" value="<?php echo $html_subject; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <?php
                                        // ボタン名変更
                                        if(!empty($id) && is_numeric($id))
                                        {
                                            $btn_name = '更新';
                                        }
                                        else
                                        {
                                            $btn_name = '登録';
                                        }
                                        ?>
                                        <button type="submit" class="btn btn-primary btn-block"><?php echo $btn_name; ?></button>
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
                                                    <select name="motion_history[grade_id]" class="form-control">
                                                        <?php echo create_html_select_options($_grade_id_options, $html_grade_id); ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon">氏名</span>
                                                    <input type="text" name="motion_history[person_name]" class="form-control" value="<?php echo $html_person_name; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon">姿勢</span>
                                                    <select name="motion_history[pose_id]" class="form-control">
                                                        <?php echo create_html_select_options($_pose_id_options, $html_pose_id); ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group datepicker date">
                                                    <span class="input-group-addon">記録日</span>
                                                    <input type="text" name="motion_history[record_date]" class="form-control" value="<?php echo $html_record_date; ?>">
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
                                                    <input type="text" name="motion_history[exposure_time]" class="form-control" value="<?php echo $html_exposure_time; ?>">
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
                                            <input type="hidden" name="motion_history[dir_name]" value="<?php echo $_dir_name; ?>">
                                            <input type="hidden" name="motion_history[file_name]" value="<?php echo $_file_name; ?>">
                                        </div>
                                        <div class="panel-body">
                                            <p>
                                                <img src="<?php echo $_file_path; ?>" class="img-responsive" alt="Responsive image">
                                            </p>
                                        </div>
                                    </div>

                                    <div class="panel panel-info">
                                        <div class="panel-heading">リンク</div>
                                        <div class="panel-body">
                                            <div class="row" style="display: flex; align-items: center;">
                                                <div class="col-xs-6 col-md-2 text-center">
                                                    <?php
                                                    $img_index = $_img_index - 2;
                                                    if(isset($_img_file_name_array[$img_index]))
                                                    {
                                                        $file_name = $_img_file_name_array[($img_index)];

                                                        $get_params = array(
                                                            'dir_name' => $_dir_name,
                                                            'file_name' => $file_name
                                                            );
                                                        $href = create_html_href('input.php', $get_params);
                                                        $img_src = $_dir_path.DS.$file_name;
                                                    ?>
                                                    <a href="<?php echo $href; ?>">
                                                        <img alt="<?php echo $file_name; ?>" src="<?php echo $img_src; ?>" class="img-thumbnail" style="width: 64px; height: 64px;">
                                                    </a>
                                                    <?php
                                                    }
                                                    else
                                                    {
                                                        $img_src = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+PGRlZnMvPjxyZWN0IHdpZHRoPSI2NCIgaGVpZ2h0PSI2NCIgZmlsbD0iI0VFRUVFRSIvPjxnPjx0ZXh0IHg9IjEzLjQ2MDkzNzUiIHk9IjMyIiBzdHlsZT0iZmlsbDojQUFBQUFBO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1mYW1pbHk6QXJpYWwsIEhlbHZldGljYSwgT3BlbiBTYW5zLCBzYW5zLXNlcmlmLCBtb25vc3BhY2U7Zm9udC1zaXplOjEwcHQ7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+NjR4NjQ8L3RleHQ+PC9nPjwvc3ZnPg==';
                                                    ?>
                                                    <img alt="none" src="<?php echo $img_src; ?>" class="img-thumbnail" style="width: 64px; height: 64px;">
                                                    <?php
                                                    }
                                                    ?>

                                                </div>
                                                <div class="col-xs-6 col-md-2 text-center">
                                                    <?php
                                                    $img_index = $_img_index - 1;
                                                    if(isset($_img_file_name_array[$img_index]))
                                                    {
                                                        $file_name = $_img_file_name_array[($img_index)];

                                                        $get_params = array(
                                                            'dir_name' => $_dir_name,
                                                            'file_name' => $file_name
                                                            );
                                                        $href = create_html_href('input.php', $get_params);
                                                        $img_src = $_dir_path.DS.$file_name;
                                                    ?>
                                                    <a href="<?php echo $href; ?>">
                                                        <img alt="<?php echo $file_name; ?>" src="<?php echo $img_src; ?>" class="img-thumbnail" style="width: 64px; height: 64px;">
                                                    </a>
                                                    <?php
                                                    }
                                                    else
                                                    {
                                                        $img_src = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+PGRlZnMvPjxyZWN0IHdpZHRoPSI2NCIgaGVpZ2h0PSI2NCIgZmlsbD0iI0VFRUVFRSIvPjxnPjx0ZXh0IHg9IjEzLjQ2MDkzNzUiIHk9IjMyIiBzdHlsZT0iZmlsbDojQUFBQUFBO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1mYW1pbHk6QXJpYWwsIEhlbHZldGljYSwgT3BlbiBTYW5zLCBzYW5zLXNlcmlmLCBtb25vc3BhY2U7Zm9udC1zaXplOjEwcHQ7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+NjR4NjQ8L3RleHQ+PC9nPjwvc3ZnPg==';
                                                    ?>
                                                    <img alt="none" src="<?php echo $img_src; ?>" class="img-thumbnail" style="width: 64px; height: 64px;">
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                                <div class="col-xs-6 col-md-1">
                                                    <span class="glyphicon glyphicon-chevron-left"></span>
                                                </div>
                                                <div class="col-xs-6 col-md-2 text-center">
                                                    <?php
                                                    $img_index = $_img_index;
                                                    if(isset($_img_file_name_array[$img_index]))
                                                    {
                                                        $file_name = $_img_file_name_array[($img_index)];

                                                        $get_params = array(
                                                            'dir_name' => $_dir_name,
                                                            'file_name' => $file_name
                                                            );
                                                        $href = '#';
                                                        $img_src = $_dir_path.DS.$file_name;
                                                    ?>
                                                    <img alt="<?php echo $file_name; ?>" src="<?php echo $img_src; ?>" class="img-thumbnail" style="width: 64px; height: 64px;">
                                                    <p class="text-center">表示中</p>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                                <div class="col-xs-6 col-md-1">
                                                    <span class="glyphicon glyphicon-chevron-right"></span>
                                                </div>
                                                <div class="col-xs-6 col-md-2 text-center">
                                                    <?php
                                                    $img_index = $_img_index + 1;
                                                    if(isset($_img_file_name_array[$img_index]))
                                                    {
                                                        $file_name = $_img_file_name_array[($img_index)];

                                                        $get_params = array(
                                                            'dir_name' => $_dir_name,
                                                            'file_name' => $file_name
                                                            );
                                                        $href = create_html_href('input.php', $get_params);
                                                        $img_src = $_dir_path.DS.$file_name;
                                                    ?>
                                                    <a href="<?php echo $href; ?>">
                                                        <img alt="<?php echo $file_name; ?>" src="<?php echo $img_src; ?>" class="img-thumbnail" style="width: 64px; height: 64px;">
                                                    </a>
                                                    <?php
                                                    }
                                                    else
                                                    {
                                                        $img_src = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+PGRlZnMvPjxyZWN0IHdpZHRoPSI2NCIgaGVpZ2h0PSI2NCIgZmlsbD0iI0VFRUVFRSIvPjxnPjx0ZXh0IHg9IjEzLjQ2MDkzNzUiIHk9IjMyIiBzdHlsZT0iZmlsbDojQUFBQUFBO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1mYW1pbHk6QXJpYWwsIEhlbHZldGljYSwgT3BlbiBTYW5zLCBzYW5zLXNlcmlmLCBtb25vc3BhY2U7Zm9udC1zaXplOjEwcHQ7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+NjR4NjQ8L3RleHQ+PC9nPjwvc3ZnPg==';
                                                    ?>
                                                    <img alt="none" src="<?php echo $img_src; ?>" class="img-thumbnail" style="width: 64px; height: 64px;">
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                                <div class="col-xs-6 col-md-2 text-center">
                                                    <?php
                                                    $img_index = $_img_index + 2;
                                                    if(isset($_img_file_name_array[$img_index]))
                                                    {
                                                        $file_name = $_img_file_name_array[($img_index)];

                                                        $get_params = array(
                                                            'dir_name' => $_dir_name,
                                                            'file_name' => $file_name
                                                            );
                                                        $href = create_html_href('input.php', $get_params);
                                                        $img_src = $_dir_path.DS.$file_name;
                                                    ?>
                                                    <a href="<?php echo $href; ?>">
                                                        <img alt="<?php echo $file_name; ?>" src="<?php echo $img_src; ?>" class="img-thumbnail" style="width: 64px; height: 64px;">
                                                    </a>
                                                    <?php
                                                    }
                                                    else
                                                    {
                                                        $img_src = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+PGRlZnMvPjxyZWN0IHdpZHRoPSI2NCIgaGVpZ2h0PSI2NCIgZmlsbD0iI0VFRUVFRSIvPjxnPjx0ZXh0IHg9IjEzLjQ2MDkzNzUiIHk9IjMyIiBzdHlsZT0iZmlsbDojQUFBQUFBO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1mYW1pbHk6QXJpYWwsIEhlbHZldGljYSwgT3BlbiBTYW5zLCBzYW5zLXNlcmlmLCBtb25vc3BhY2U7Zm9udC1zaXplOjEwcHQ7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+NjR4NjQ8L3RleHQ+PC9nPjwvc3ZnPg==';
                                                    ?>
                                                    <img alt="none" src="<?php echo $img_src; ?>" class="img-thumbnail" style="width: 64px; height: 64px;">
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
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
                                                        <textarea name="motion_history[speak_remark]" class="form-control" rows="3"><?php echo $html_speak_remark; ?></textarea>
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
                                                                <input type="text" name="motion_history[part_name_1]" class="form-control" value="<?php echo $html_part_name_1; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <select name="motion_history[motion_level_1]" class="form-control">
                                                                    <?php echo create_html_select_options($_motion_level_options, $html_motion_level_1); ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-9">
                                                            <div class="form-group">
                                                                <input type="text" name="motion_history[part_name_2]" class="form-control" value="<?php echo $html_part_name_2; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <select name="motion_history[motion_level_2]" class="form-control">
                                                                    <?php echo create_html_select_options($_motion_level_options, $html_motion_level_2); ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-9">
                                                            <div class="form-group">
                                                                <input type="text" name="motion_history[part_name_3]" class="form-control" value="<?php echo $html_part_name_3; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <select name="motion_history[motion_level_3]" class="form-control">
                                                                    <?php echo create_html_select_options($_motion_level_options, $html_motion_level_3); ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-9">
                                                            <div class="form-group">
                                                                <input type="text" name="motion_history[part_name_4]" class="form-control" value="<?php echo $html_part_name_4; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <select name="motion_history[motion_level_4]" class="form-control">
                                                                    <?php echo create_html_select_options($_motion_level_options, $html_motion_level_4); ?>
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
                                                        <textarea name="motion_history[motion_remark]" class="form-control" rows="3"><?php echo $html_motion_remark; ?></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <select name="motion_history[motion_type_id]" class="form-control">
                                                            <?php echo create_html_select_options($_motion_type_id_options, $html_motion_type_id); ?>
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