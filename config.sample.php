<?php

/**
 * システム設定情報
 */

define('DS', DIRECTORY_SEPARATOR);

// モーションヒストリー画像格納ディレクトリのベースパス
define('IMAGES_BASE_PATH', './images_base_dir');


// データベース接続情報
$_dsn = 'mysql:dbname=inreal_data;host=localhost;charset=utf8';

// データベース接続ユーザ
$_db_user = 'xxx';

// データベース接続パスワード
$_db_password = 'xxx';



// 入力フォームの選択肢情報
// 学部ID
$_grade_id_options = array(
    1 => '小学部',
    2 => '中学部',
    3 => '高学部'
    );

// 姿勢ID
$_pose_id_options = array(
    1 => '仰臥位（仰向け）',
    2 => '右向き',
    3 => '左向き',
    4 => '伏臥位（うつ伏せ）',
    5 => '座位'
    );

// 体の動き
$_motion_level_options = array(
    0 => '0',
    1 => '1',
    2 => '2',
    3 => '3',
    4 => '4',
    5 => '5',
    6 => '6'
    );

// 動きの種別
$_motion_type_id_options = array(
    1 => '無意識行動',
    2 => '意図的行動'
    );


