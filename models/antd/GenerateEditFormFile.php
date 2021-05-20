<?php
/**
 * File Name: GenerateEditFormFile.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/19 10:24 下午
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

class GenerateEditFormFile
{
    public $dir;
    public $columns;

    protected $import = [];

    public function run()
    {
        $content = GenerateTool::loadTemplate($this->template(), [
            'time' => date('Y-m-d H:i:s'),
            'form_item'=>$this->getFormItem(),
            'import' => GenerateTool::formatImport($this->import),
        ]);

        $filename = StringHelper::combPath($this->dir, 'EditForm.jsx');
        file_put_contents($filename, $content);
    }



    protected function template()
    {
        return file_get_contents(__DIR__ . '/editform.txt');
    }


    protected function getFormItem()
    {
        $temp = '      <Form.Item name="{{field}}" label="{{title}}"{{require}}>
        <Input/>
      </Form.Item>
';
        $temp_require = ' rules={[{required: true}]}';
        $str = '';
        foreach ($this->columns as $col) {
            if (!$col['is_edit']) {
                continue;
            }
            $str .= GenerateTool::loadTemplate($temp, [
                'field' => $col['COLUMN_NAME'],
                'title' => AntdTool::getTitle($col),
                'require' => $col['required'] ? $temp_require : '',
            ]);
        }

        return $str;
    }
}