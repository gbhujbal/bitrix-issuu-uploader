<?php

/**
 * Issuu PDF Uploader for 1C-Bitrix
 * PHP Version 5.4.0
 * @package bitrix-issuu-uploader
 * @link https://github.com/EduardNikolenko/bitrix-issuu-uploader/
 * @author Eduard Nikolenko <eduard@grossell.com>
 * @copyright 2014 Eduard Nikolenko
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

define(ISSUU_API_URL, 'http://upload.issuu.com/1_0');
# define(ISSUU_API_KEY, '');
# define(ISSUU_API_SECRET, '');

AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CIBlockPropertyIssuuUpload', 'GetIBlockPropertyDescription'));
AddEventHandler('main', 'OnUserTypeBuildList', array('CIBlockPropertyIssuuUpload', 'GetUserTypeDescription'));

class CIBlockPropertyIssuuUpload {
    function GetUserTypeDescription() {
        return array(
            'PROPERTY_TYPE'         => 'S',
            'USER_TYPE'             => 'ISSUU',
            'DESCRIPTION'           => 'Issuu Uploader',
            'GetPropertyFieldHtml'  => array('CIBlockPropertyIssuuUpload', 'GetPropertyFieldHtml'),
            'ConvertToDB'           => array('CIBlockPropertyIssuuUpload', 'ConvertToDB')
        );
    }
    
    function GetIBlockPropertyDescription() {
        return array(
            'PROPERTY_TYPE'         => 'S',
            'USER_TYPE'             => 'ISSUU',
            'DESCRIPTION'           => 'Issuu Uploader',
            'GetPropertyFieldHtml'  => array('CIBlockPropertyIssuuUpload', 'GetPropertyFieldHtml'),
            'ConvertToDB'           => array('CIBlockPropertyIssuuUpload', 'ConvertToDB')
        );
    }
    
    function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) {
        $arResult = '';
        $arValues = explode(',', $value['VALUE']);
        
        return $arResult;
    }
    
    function ConvertToDB($arProperty, $value) {
        $arResult = array();
        $arValues = $value['VALUE'];
        
        if ($arValues['ISSUU_DEL']) {
            DeleteFile($arValues);
            $arValues = false;
        }
        
        if ($arValues['ISSUU_NEW']['NAME'] && !$arValues['ISSUU_NEW']['ERROR']) {
            if ($arValues['ISSUU_OLD_NAME'] && !$arValues['ISSUU_DEL']) {
                DeleteFile($arValues);
            }
            
            $arValues = UploadFile($arValues);
        }
        
        if ($arValues['ISSUU_NEW']['ERROR'] && !$arValues['ISSUU_DEL']) {
            $arValues = $arValues['ISSUU_OLD_NAME'] . ',' . $arValues['ISSUU_OLD_ID'];
        }
        
        return $arResult;
    }
    
    /**
     * Function for upload file to Issuu server
     * @param array $arValues
     */
    function UploadFile($arValues) {
        $file_id  = CFile::SaveFile($arValues['ISSUU_NEW'], 'tmp');
        $file_url = CFile::GetPath($file_id);
        
        $arPostFields = array();
        $arPostFields['action']     = 'issuu.document.upload';
        $arPostFields['apiKey']     = ISSUU_API_KEY;
        $arPostFields['name']       = 'PDF_' . time();
        $arPostFields['title']      = '';
        $arPostFields['signature']  = md5(ISSUU_API_SECRET . 'actionissuu.document.uploadapiKey' . ISSUU_API_KEY . 'name' . $arPostFields['name'] . 'title' . $arPostFields['title']);
        $arPostFields['file']       = '@' . $_SERVER['DOCUMENT_ROOT'] . $file_url;
        
        curl_setopt($curl, CURLOPT_URL, ISSUU_API_URL);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $arPostFields);
        $curl_answer = curl_exec($curl);
        curl_close($curl);
        
        $xml = new SimpleXMLElement($curl_answer);
            
        foreach ($xml->document->attributes() as $k => $v) {
            $issuu_newdoc_id = false;

            if ($k == 'documentId') {
                $issuu_newdoc_id = $v;
                break;
            }
        }
        
        CFile::Delete($bx_file_id);
        
        return $arPostFields['name'] . ',' . $issuu_newdoc_id;
    }
    
    /**
     * Function for delete file from Issuu server
     * @param array $arValues
     */
    function DeleteFile($arValues) {
        $arPostFields = array();
        $arPostFields['action']     = 'issuu.document.delete';
        $arPostFields['apiKey']     = ISSUU_API_KEY;
        $arPostFields['names']      = $arValues['ISSUU_OLD_NAME'];
        $arPostFields['signature']  = md5(ISSUU_API_SECRET . 'actionissuu.document.deleteapiKey' . ISSUU_API_KEY . 'names' . $arValues['ISSUU_OLD_NAME']);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, ISSUU_API_URL);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $arPostFields);
        curl_exec($curl);
        curl_close($curl);
    }
}

?>
