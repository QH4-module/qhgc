<?php
/**
 * File Name: GenerateUpdateFile.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/19 9:56 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\qhgc\models\antd;


use qh4module\qhgc\models\GenerateTool;
use qttx\helper\StringHelper;

class GenerateUpdateFile
{
    public $dir;
    public $active_record_classname;

    protected $import = [];

    public function run()
    {
        $primary = call_user_func([$this->active_record_classname, 'primaryKey']);

        $content = GenerateTool::loadTemplate($this->template(), [
            'time' => date('Y-m-d H:i:s'),
            'primary'=>$primary,
            'import' => GenerateTool::formatImport($this->import),
        ]);

        $filename = StringHelper::combPath($this->dir, 'update.jsx');
        file_put_contents($filename, $content);
    }



    protected function template()
    {
        return file_get_contents(__DIR__ . '/update.txt');
    }
}