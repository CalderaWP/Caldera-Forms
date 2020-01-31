<?php


namespace calderawp\calderaforms\Tests\Util;


class TestForms
{

    /**
     * @param $filePath
     * @return array
     */
    public static function getTestFormIds($filePath)
    {
        $formFiles = scandir($filePath);
        $formIds = [];
        foreach ($formFiles as $file) {
            $fullPath = $filePath . '/' . $file;
            $info = pathinfo($fullPath);
            if ('json' === $info['extension']) {
                $formIds[] = str_replace('.json', '', $file);
            }
        }
        return $formIds;
    }


    /**
     * @param $filePath
     * @return array
     */
    public static function getTestPages($filePath)
    {
        $data = json_decode(file_get_contents($filePath), true);
        $testForms = $data['forms'];
        $pages = [];
        foreach ($testForms as $testForm) {
            $formId = $testForm['formId'];
            $pageSlug = $testForm['pageSlug'];

            $pages[$formId] = $pageSlug;
        }

        return $pages;
    }
}