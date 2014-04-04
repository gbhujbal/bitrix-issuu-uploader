<?php

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
        
    }
    
    function ConvertToDB($arProperty, $value) {
        
    }
}

?>
