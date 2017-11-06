/**
 * 2015-2017 PayApi
 *
 *  @author    Efim Polevoi <efim@payapi.in>
 *  @copyright 2017 PayApi Ltd
 *  @license   GPL v3.0
 */

$(document).ready(function() {
    var form = document.createElement("form");
    form.style.display = "none";
    form.setAttribute("method", "POST");
    if (payapi_staging_env) {
        form.setAttribute("action", "https://staging-input.payapi.io/v1/secureform/"+payapi_public_id);
    } else {
        form.setAttribute("action", "https://input.payapi.io/v1/secureform/"+payapi_public_id);
    }
    form.setAttribute("enctype", "application/json");
    form.setAttribute("id", "order-form-post");
    var input = document.createElement("input");
    input.name = "data";
    input.type = "text";
    input.setAttribute("value", payapi_payload_data);
    form.appendChild(input);
    document.getElementsByTagName("body")[0].appendChild(form);
});
