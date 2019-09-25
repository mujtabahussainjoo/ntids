require([
    'jquery'
], function ($) {
    'use strict';

    $(document).ready(function(){
        $('#generate').click(function() {
            $.ajax({
                type: "POST",
                url: "/rest/V1/sso/certificate",
                // The key needs to match your method's input parameter (case-sensitive).
                data: JSON.stringify({
                    "certificate": {
                        "countryName": $('#country_name').val(),
                        "stateOrProvinceName": $('#state_or_province_name').val(),
                        "localityName": $('#locality_name').val(),
                        "organizationName": $('#organization_name').val(),
                        "organizationalUnitName": $('#organizational_unit_name').val(),
                        "commonName": $('#common_name').val(),
                        "emailAddress": $('#email_address').val()
                    }
                }),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function(xhr){
                    //Empty to remove magento's default handler which malforms JSON
                },
                success: function(data){
                    $('#crt_data').val(data['certificate']);
                    $('#pem_data').val(data['private_key']);
                },
                failure: function(errMsg) {
                    console.log(errMsg);
                }
            });
        });
    });
});