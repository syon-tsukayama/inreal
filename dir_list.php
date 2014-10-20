<?php
/**
 * ディレクトリ一覧
 */

// 共通機能読み込み
require_once('./common.php');

$error_message = '';

// ディレクトリ存在確認
if(!is_dir(IMAGES_BASE_PATH))
{
    $error_message = '画像ファイルの格納ディレクトリパスが存在しません。：'.IMAGES_BASE_PATH;
}

if(empty($error_message))
{
    $dir_array = scandir(IMAGES_BASE_PATH);

    // [.]、[..]を除外
    $dir_array = array_filter($dir_array, function ($dir_name) {
        return !in_array($dir_name, array('.', '..'));
    });
}
else
{
    $dir_array = array();
}

?>
<html>
    <head>
        <meta charset="utf-8">
        <title>インリアル</title>
        <!-- Bootstrap core CSS -->
        <link href="./css/bootstrap.min.css" rel="stylesheet">
        <script src="./js/jquery-1.11.1.min.js"></script>
        <script src="./js/bootstrap.min.js"></script>
    </head>

    <body>

        <div class="container-fluid">

            <div class="page-header">
                <h1>Example page header <small>Subtext for header</small></h1>
            </div>

            <div class="row">
                <div class="col-md-10 col-md-offset-1">

                    <?php echo create_html_error_alert($error_message); ?>

                    <div class="well">
                        <ul class="list-group">
                            <?php
                            foreach($dir_array as $dir_name)
                            {
                                $img_file_name_array = get_img_lists(IMAGES_BASE_PATH.DS.$dir_name);

                                $get_params = array(
                                    'dir_name' => $dir_name
                                    );
                                $href = create_html_href('input.php', $get_params);
                            ?>
                            <li class="list-group-item">
                                <span class="badge"><?php echo count($img_file_name_array); ?></span>
                                <a href="<?php echo $href; ?>"><?php echo $dir_name.'<br>'; ?></a>
                            </li>
                            <?php
                            }
                            ?>
                        </ul>

                    </div>
                </div>
            </div>

        </div>

    </body>

</html>
