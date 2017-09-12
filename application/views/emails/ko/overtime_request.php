<?php
/**
 * Email template.You can change the content of this template
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>
<html lang="km">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <meta charset="UTF-8">
    </head>
    <body>
        <h3>{Title}</h3>
        {Firstname} {Lastname} 초과근무신청서 {ForFirstname} {ForLastname} ។ 상세내용 :
        <table border="0">
            <tr>
                <td>신청일자 &nbsp;</td><td>{Date}</td>
            </tr>
            <tr>
                <td>신청기간 &nbsp;</td><td>{Duration}</td>
            </tr>
            <tr>
                <td>사유 &nbsp;</td><td>{Cause}</td>
            </tr>
        </table>
        <a href="{UrlAccept}">승인</a>
        <a href="{UrlReject}">반려</a>
    </body>
</html>
