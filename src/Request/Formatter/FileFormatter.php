<?php

namespace PhalApi\Request\Formatter;

use PhalApi\Contracts\Request\Formatter;
use PhalApi\Exceptions\BadRequest;

class FileFormatter extends BaseFormatter implements Formatter
{
    /**
     * 格式化文件类型
     *
     * @param string $value 变量值
     * @param array $rule ['name' => '', 'type' => 'file', 'default' => [], 'min' => '', 'max' => '', 'range' => []]
     * @throws BadRequest
     * @return mixed
     */
    public function parse($value, $rule)
    {
        $default = isset($rule['default']) ? $rule['default'] : null;

        $index = $rule['name'];
        // 未上传
        if (!isset($_FILES[$index])) {
            // 有默认值 || 非必须
            if ($default !== null || (isset($rule['require']) && !$rule['require'])) {
                return $default;
            }
        }

        if (!isset($_FILES[$index]) || !isset($_FILES[$index]['error']) || !is_array($_FILES[$index])) {
            throw new BadRequest(T('miss upload file: {file}', ['file' => $index]));
        }

        if ($_FILES[$index]['error'] != UPLOAD_ERR_OK) {
            throw new BadRequest(T('fail to upload file with error = {error}', ['error' => $_FILES[$index]['error']]));
        }

        $sizeRule = $rule;
        $sizeRule['name'] = $sizeRule['name'] . '.size';
        $this->filterByRange($_FILES[$index]['size'], $sizeRule);

        if (!empty($rule['range']) && is_array($rule['range'])) {
            $rule['range'] = array_map('strtolower', $rule['range']);
            $this->formatEnumValue(strtolower($_FILES[$index]['type']), $rule);
        }

        //对于文件后缀进行验证
        if (!empty($rule['ext'])) {
            $ext = trim(strrchr($_FILES[$index]['name'], '.'), '.');
            if (is_string($rule['ext'])) {
                $rule['ext'] = explode(',', $rule['ext']);
            }
            if (!$ext) {
                throw new BadRequest(T('Not the file type {ext}', ['ext' => json_encode($rule['ext'])]));
            }
            if (is_array($rule['ext'])) {
                $rule['ext'] = array_map('strtolower', $rule['ext']);
                $rule['ext'] = array_map('trim', $rule['ext']);
                if (!in_array(strtolower($ext), $rule['ext'])) {
                    throw new BadRequest(T('Not the file type {ext}', ['ext' => json_encode($rule['ext'])]));
                }
            }
        }

        return $_FILES[$index];
    }
}
