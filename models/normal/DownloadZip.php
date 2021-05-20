<?php
/**
 * File Name: DownloadZip.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/9 1:10 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\qhgc\models\normal;


use qttx\basic\Loader;
use qttx\helper\StringHelper;
use qttx\web\Model;

class DownloadZip extends Model
{
    public $file;


    public function run()
    {
        if (empty($this->file) || !is_string($this->file)) {
            return false;
        }
        $dir = Loader::getAlias('@runtime');
        $build_dir = StringHelper::combPath($dir, 'qhgc_build');
        $file_path = StringHelper::combPath($build_dir, $this->file);
        if (!is_file($file_path)) {
            return false;
        }

        //告诉浏览器这是一个文件流格式的文件
        Header ( "Content-type: application/octet-stream" );
        //请求范围的度量单位
        Header ( "Accept-Ranges: bytes" );
        //Content-Length是指定包含于请求或响应中数据的字节长度
        Header ( "Accept-Length: " . filesize ( $file_path) );
        //用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
        Header ( "Content-Disposition: attachment; filename=" . $this->file );

        // 发送文件内容
        set_time_limit(0);
        readfile($file_path);
    }
}