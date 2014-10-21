<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Terminal</title>
        <link rel='stylesheet' type='text/css' href='style/terminal.css'>
        <meta name='robots' content='noindex,follow' />
    </head>
    <script src="../js/libs/jquery/jquery.js" type="text/javascript"></script>
    <script type="text/javascript">
        //////////////////////////////////////////////////////////////////////////////////////////////////
        //Generic JS////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////////
        function validatelogin()
        {            
            var user_pass = $('#user_pass').val();
            var errorTxt = '';
                        
            if (user_pass.length === 0 && errorTxt === '')
            {
                errorTxt = 'Please enter a pin';
            }
            
            if (user_pass.length !== 4 && errorTxt === '')
            {
                errorTxt = 'Pin must be four characters';
            }
            
            if (!$.isNumeric(user_pass) && errorTxt === '')
            {
                errorTxt = 'Pin must be numeric';
            }
            
            if(errorTxt !== '')
            {
                alert(errorTxt);
            }else{
                if(user_pass === '1234')
                {
                    getSession();
                }else{
                    alert('Invalid login credentials');
                }
            }
        }
        
        function showBuyVoucher()
        {            
            $('#redeemVoucherDiv').css('display','none');
            $('#buyVoucherDiv').css('display','block');
        }

        function showRedeemVoucher()
        {
            $('#buyVoucherDiv').css('display','none');
            $('#redeemVoucherDiv').css('display','block');            
        }
      

        function isAlphaNumeric(str) {
            var code, i, len;

            for (i = 0, len = str.length; i < len; i++) {
              code = str.charCodeAt(i);
              if (!(code > 47 && code < 58) && // numeric (0-9)
                  !(code > 64 && code < 91) && // upper alpha (A-Z)
                  !(code > 96 && code < 123)) { // lower alpha (a-z)
                return false;
              }
            }
            return true;
          }

        function htmlEntities(str) {
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////
        //Webservice calls////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////////
        function postXML(xmlString,url,action)
        {
            //var sendString = '';
            //sendString = objToString(dataObject);

            //sendString = $(this).serialize() + "&" + $.param(dataObject);
            
            $.ajax({
                type: "POST",
                url: url,
                data: xmlString,
                contentType: "text/xml",
                cache: false,
                dataType: "xml",
                success: function(data, textStatus, jqXHR){processResponse({xml: data, textStatus: textStatus, action: action});},
                error:function(jqXHR, textStatus, errorThrown){errorAlert(jqXHR,textStatus,errorThrown,action);}
            });
        }

        function processResponse(parameters) {
            var xml = parameters.xml;
            var textStatus = parameters.textStatus;
            var action = parameters.action;
            var dataReturned = 0;

            //var xmlobj = jQuery.parseXML(data);
            var xmlobj = $(xml).find('serviceResponse');

            if(action === 'login')
            {
                if(xmlobj.find("status").text() === 'Success')
                {
                    $('#sessionid').val(xmlobj.find("session_id").text());
                    
                    $('#logindiv').css('display','none');
                    $('#homediv').css('display','block');
                    
                }else{
                    alert('Login failed:'+xmlobj.find("error").text());
                }
            }
            else if(action === 'allocate_voucher')
            {
                if(xmlobj.find("status").text() === 'Success')
                {
                    $('#allocatedVoucher').val(xmlobj.find("voucher_number").text() );
                    
                    alert('Your voucher has been redeemed');
                }else{
                    alert('Error:'+xmlobj.find("error").text());
                }
            }            
            else if(action === 'redeem_laybye')
            {
                if(xmlobj.find("status").text() === 'Success')
                {
                    alert('Your voucher has been redeemed successfully');
                }else{
                    alert('Error:'+xmlobj.find("error").text());
                }
            }            

        }

        function errorAlert(jqXHR,textStatus,errorThrown,dataObject)
        {
            if(errorThrown !== '')
            {
                alert('[AJAX ERROR]: '+errorThrown);
            }
        }

        function objToString (obj)
        {
            var tabjson=[];
            for (var p in obj) {
                if (obj.hasOwnProperty(p)) {
                    tabjson.push('"'+p +'"'+ ':"' + obj[p]+'"');
                }
            }  tabjson.push()
            return '{'+tabjson.join(',')+'}';
        }
        // /////////////////////////////////////////////////////////////////
        function getSession()
        {
            var xmlString ="<?xml version='1.0'?><serviceRequest><action>login</action><login_str>tha001testcompany</login_str><password>thapelo1234</password></serviceRequest>";
            postXML(xmlString,'http://localhost/VBA_GlocellWallet/index.php','login');
        }
       
        function buyVoucher(product)
        {
            var errorTxt = '';

            var sourceaccount = $('#sourceaccLB').val();
            
            if(errorTxt != '')
            {
                alert(errorTxt);
            }else{
                var xmlString ="<?xml version='1.0'?><serviceRequest><action>allocate_voucher</action><session_id>"+$('#sessionid').val()+"</session_id><product>"+product+"</product></serviceRequest>";
                postXML(xmlString,'http://localhost/VBA_GlocellWallet/index.php','allocate_voucher');
            }
        }

        function redeemLayByeVoucher()
        {
            var errorTxt = '';

            var vouchernumber = $("#redeemvoucherNumber").val();
            var value = $("#value").val();

            if(vouchernumber.length === 0)
            {
                errorTxt = 'Please enter a voucher number';
            }

            if(vouchernumber.length != 9  && errorTxt === '')
            {
                errorTxt = 'Voucher numbers must be nine digits';
            }

            if (!$.isNumeric(vouchernumber) && errorTxt === '')
            {
                errorTxt = 'Invalid voucher number';
            }
            
            if(value.length === 0 && errorTxt === '')
            {
                errorTxt = 'Please enter a value to redeem';
            }
            
            if (!$.isNumeric(value) && errorTxt === '')
            {
                errorTxt = 'Invalid value';
            }

            if(errorTxt != '')
            {
                alert(errorTxt);
            }else{
                var xmlString ="<?xml version='1.0'?><serviceRequest><action>redeem_laybye</action><session_id>"+$('#sessionid').val()+"</session_id><value>"+value+"</value><voucher_number>"+vouchernumber+"</voucher_number></serviceRequest>";
                postXML(xmlString,'http://localhost/VBA_GlocellWallet/index.php','redeem_laybye');
            }

        }

    </script>
    <body>
        <input type="hidden" name="sessionid" id="sessionid"/>
        <input type="hidden" name="userid" id="userid" value="123"/>
        <div id="logindiv" name="logindiv">
            <p>                
                <label><strong>Enter Pin</strong></label>
                <br/>
                <input type='password' id='user_pass' value='1234' />
                <br/>
                <br/>
                <input type='button' id='login_btn' value='Login' onclick="validatelogin();"/>
            </p>
        </div>
        <div id="homediv" name="homediv" style="display: none">
            <table border="1" width="100%">
                <tr>
                    <td>
                        <input type="button" value="Buy voucher" class='pagebuttons' onclick="showBuyVoucher()">
                        <input type="button" value="Redeem Lay-Bye" class='pagebuttons' onclick="showRedeemVoucher()">                       
                        <hr>
                        <!-- ------------------------------------------- -->
                         <!-- ------------------------------------------- -->
                        <div id="buyVoucherDiv" style="display: none">
                            Select a voucher
                            <br>
                            <input type="button" value="PGC010" onclick="buyVoucher('PGC010')">
                            <input type="button" value="PGC020" onclick="buyVoucher('PGC020')">
                            <br>
                            <input type="button" value="PGC050" onclick="buyVoucher('PGC050')">
                            <input type="button" value="PGC100" onclick="buyVoucher('PGC100')">
                            <br>
                            <br>
                            Your voucher number is
                            <br>
                            <input type="text" id="allocatedVoucher" readonly=""/>
                            <input type="button" value="Print">
                        </div>
                        <!-- ------------------------------------------- -->
                        <div id="redeemVoucherDiv" style="display: none">
                            Enter voucher number
                            <input type="text" id="redeemvoucherNumber">
                            <br>
                            Enter value to redeem
                            <br>
                            <input type="text" id="value">
                            <br>
                            <input type="button" value="Redeem Lay-Bye" readonly="true" onclick="redeemLayByeVoucher()">                           
                        </div>
                        <!-- ------------------------------------------- -->                        
                    </td>
                </tr>
            </table>

        </div>
    </body>
</html>