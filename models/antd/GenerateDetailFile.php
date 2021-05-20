<?php
/**
 * File Name: GenerateDetailFile.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/19 10:14 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\qhgc\models\antd;


use qh4module\qhgc\external\ExtQhgc;
use qh4module\qhgc\models\GenerateTool;
use qttx\helper\StringHelper;

class GenerateDetailFile
{
    public $dir;
    public $columns;
    public $active_record_classname;

    /**
     * @var ExtQhgc
     */
    public $external;

    protected $import = [];

    public function run()
    {
        $primary = call_user_func([$this->active_record_classname, 'primaryKey']);

        $content = GenerateTool::loadTemplate($this->template(), [
            'time' => date('Y-m-d H:i:s'),
            'primary'=>$primary,
            'desc_item'=>$this->getDetailItem(),
            'import' => GenerateTool::formatImport($this->import),
        ]);

        $filename = StringHelper::combPath($this->dir, 'detail.jsx');
        file_put_contents($filename, $content);
    }


    protected function template()
    {
        return file_get_contents(__DIR__ . '/detail.txt');
    }

    protected function getDetailItem()
    {
        $temp = '
          <Descriptions.Item label="{{title}}">{dataSource.{{field}}}</Descriptions.Item>';

        // 排除不显示的字段
        $exclude = array_keys($this->external->deleteTagField());

        $str = '';

        foreach ($this->columns as $col) {
            if (in_array($col['COLUMN_NAME'], $exclude)) {
                continue;
            }
            $str .= GenerateTool::loadTemplate($temp, [
                'title' => AntdTool::getTitle($col),
                'field' => $col['COLUMN_NAME']
            ]);
        }

        return $str;
    }
}