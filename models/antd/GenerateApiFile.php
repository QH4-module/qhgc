<?php
/**
 * File Name: GenerateApiFile.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/19 10:44 下午
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

class GenerateApiFile
{
    public $dir;

    public $ui_list_api;
    public $ui_create_api;
    public $ui_update_api;
    public $ui_detail_api;
    public $ui_delete_api;
    public $ui_dictionary;
    public $ui_list_file;
    public $ui_create_file;
    public $ui_update_file;
    public $ui_detail_file;

    protected $import = [];

    public function run()
    {
        $content = GenerateTool::loadTemplate($this->template(), [
            'time' => date('Y-m-d H:i:s'),

            'list_api' => $this->ui_list_api,
            'create_api' => $this->ui_create_api,
            'update_api' => $this->ui_update_api,
            'detail_api' => $this->ui_detail_api,
            'delete_api' => $this->ui_delete_api,
            'list_path' => AntdTool::getRoutePath($this->ui_dictionary, $this->ui_list_file, 'list'),
            'create_path' => AntdTool::getRoutePath($this->ui_dictionary, $this->ui_create_file, 'create'),
            'update_path' => AntdTool::getRoutePath($this->ui_dictionary, $this->ui_update_file, 'update'),
            'detail_path' => AntdTool::getRoutePath($this->ui_dictionary, $this->ui_detail_file, 'detail'),

            'import' => GenerateTool::formatImport($this->import),
        ]);

        $filename = StringHelper::combPath($this->dir, 'api.js');
        file_put_contents($filename, $content);
    }


    protected function template()
    {
        return file_get_contents(__DIR__ . '/api.txt');
    }
}