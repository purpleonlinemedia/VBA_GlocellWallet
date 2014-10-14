<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>KCMOBILE</title>
        <link rel='stylesheet' type='text/css' href='style/iframepages.css'>
        <meta name='robots' content='noindex,follow' />
    </head>
    <script src="js/libs/jquery/jquery.js" type="text/javascript"></script>
    <script type="text/javascript">
        //////////////////////////////////////////////////////////////////////////////////////////////////
        //Generic JS////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////////
        function validatelogin()
        {
            var user_login = $('#user_login').val();
            var user_pass = $('#user_pass').val();
            var errorTxt = '';
            
            if (user_login.length === 0)
            {
                errorTxt = 'Please enter a cellphone number';
            }
            
            if (!$.isNumeric(user_login) && errorTxt === '')
            {
               
                errorTxt = 'Invalid cellphone number';
            }
            
            if (user_login.length != 10 && errorTxt === '')
            {                             
                errorTxt = 'Invalid cellphone number';
            }
            
            if (user_pass.length === 0 && errorTxt === '')
            {
                errorTxt = 'Please enter a password';
            }
            
            if (user_pass.length !== 4 && errorTxt === '')
            {
                errorTxt = 'Password must be four characters';
            }
            
            if(!isAlphaNumeric(user_pass) && errorTxt === '')
            {
                errorTxt = 'Password must be alphanumeric';
            }
                        
            
            if(errorTxt !== '')
            {
                alert(errorTxt);
            }else{
                if(user_pass === '1234' && (user_login === '0798975549' || user_login === '0827891193'))
                {
                    getSession();
                }else{
                    alert('Invalid login credentials');
                }
            }
        }
        
        function showTransfer()
        {
            $('#balanceDiv').css('display','none');
            $('#statementDiv').css('display','none');
            $('#loadGCashDiv').css('display','none');
            $('#loadLayByeDiv').css('display','none');
            $('#fundtransferDiv').css('display','inline');
        }

        function showGCash()
        {
            $('#balanceDiv').css('display','none');
            $('#statementDiv').css('display','none');
            $('#fundtransferDiv').css('display','none');
            $('#loadLayByeDiv').css('display','none');
            $('#loadGCashDiv').css('display','inline');
        }

        function showLayBye()
        {
            $('#balanceDiv').css('display','none');
            $('#statementDiv').css('display','none');
            $('#fundtransferDiv').css('display','none');
            $('#loadGCashDiv').css('display','none');
            $('#loadLayByeDiv').css('display','inline');
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
                    $('#homediv').css('display','inline');
                    
                }else{
                    alert('Login failed:'+xmlobj.find("error").text());
                }
            }
            else if(action === 'get_balance')
            {
                if(xmlobj.find("status").text() === 'Success')
                {
                    $('#showbalancePool').val('R'+xmlobj.find("pool_balance").text() );
                    $('#showbalanceGlo').val('R'+xmlobj.find("kcm_wallet_balance").text() );
                    $('#showbalanceKCM').val('R'+xmlobj.find("glo_wallet_balance").text() );

                    $('#fundtransferDiv').css('display','none');
                    $('#statementDiv').css('display','none');
                    $('#loadGCashDiv').css('display','none');
                    $('#loadLayByeDiv').css('display','none');
                    $('#balanceDiv').css('display','inline');
                }else{
                    alert('Get balance failed:'+xmlobj.find("error").text());
                }
            }
            else if(action === 'get_statement')
            {
                if(xmlobj.find("status").text() === 'Success')
                {
                    $('#fundtransferDiv').css('display','none');
                    $('#balanceDiv').css('display','none');
                    $('#loadLayByeDiv').css('display','none');
                    $('#loadGCashDiv').css('display','none');
                    $('#statementDiv').css('display','inline');

                    var statementString = xmlobj.find("statement").text();

                    var statementArray = statementString.split('|');

                    var divContent = "<table border='1'>"
                    divContent += "<tr>";
                    divContent += " <td><b>Transaction Date</b></td>";
                    divContent += " <td><b>Description</b></td>";
                    divContent += " <td><b>Value</b></td>";
                    divContent += "</tr>";


                    statementArray.forEach(function(records) {
                        var recordArray = records.split(',');

                        divContent += "<tr>";
                        divContent += " <td>"+recordArray[0]+"</td>";
                        divContent += " <td>"+recordArray[1]+"</td>";
                        divContent += " <td>"+recordArray[2]+"</td>";
                        divContent += "</tr>";
                    });

                    divContent += "</table>";

                    $('#statementDiv').html(divContent);

                }else{
                    alert('Get statement failed:'+xmlobj.find("error").text());
                }
            }
            else if(action === 'load_gcash')
            {
                if(xmlobj.find("status").text() === 'Success')
                {
                    alert('Success');
                }else{
                    alert('Load failed:'+xmlobj.find("error").text());
                }
            }
            else if(action === 'funds_transfer')
            {
                if(xmlobj.find("status").text() === 'Success')
                {
                    alert('Success');
                }else{
                    alert('Transfer failed:'+xmlobj.find("error").text());
                }
            }
            else if(action === 'generateLayByeVoucher')
            {
                if(xmlobj.find("status").text() === 'Success')
                {
                    alert('Your laybye voucher has been generated. Your voucher number is:'+xmlobj.find("vouchernumber").text());
                }else{
                    alert('Transfer failed:'+xmlobj.find("error").text());
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

        function getBalance()
        {
            var xmlString ="<?xml version='1.0'?><serviceRequest><action>get_balance</action><session_id>"+$('#sessionid').val()+"</session_id><uid>"+$('#userid').val()+"</uid></serviceRequest>";
            postXML(xmlString,'http://localhost/VBA_GlocellWallet/index.php','get_balance');
        }

        function getStatement()
        {
            var xmlString ="<?xml version='1.0'?><serviceRequest><action>get_statement</action><session_id>"+$('#sessionid').val()+"</session_id><uid>"+$('#userid').val()+"</uid></serviceRequest>";
            postXML(xmlString,'http://localhost/VBA_GlocellWallet/index.php','get_statement');
        }

        function generateLayBye()
        {
            var errorTxt = '';

            var sourceaccount = $('#sourceaccLB').val();

            if(sourceaccount === "...")
            {
                errorTxt = "Please select a source account";
            }

            if(sourceaccount === 'pool')
            {
                var custid = "GLO001";
            }
            if(sourceaccount === 'glocell')
            {
                var custid = "GLO001";
            }
            if(sourceaccount === 'kcmobile')
            {
                var custid = "KCO001";
            }

            if(errorTxt != '')
            {
                alert(errorTxt);
            }else{
                var xmlString ="<?xml version='1.0'?><serviceRequest><action>generateLayByeVoucher</action><session_id>"+$('#sessionid').val()+"</session_id><uid>"+$('#userid').val()+"</uid><sourcepool>"+sourceaccount+"</sourcepool><custid>"+custid+"</custid></serviceRequest>";
                postXML(xmlString,'http://localhost/VBA_GlocellWallet/index.php','generateLayByeVoucher');
            }
        }

        function load_gcash()
        {
            var errorTxt = '';

            var vouchernumber = $("#vouchernumber").val();

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

            if(errorTxt != '')
            {
                alert(errorTxt);
            }else{
                var xmlString ="<?xml version='1.0'?><serviceRequest><action>load_gcash</action><session_id>"+$('#sessionid').val()+"</session_id><uid>"+$('#userid').val()+"</uid><voucher_number>"+vouchernumber+"</voucher_number></serviceRequest>";
                postXML(xmlString,'http://localhost/VBA_GlocellWallet/index.php','load_gcash');
            }

        }

        function fundsTransfer()
        {
            var errorTxt = '';

            var amount = $('#transferAmount').val();
            var sourceaccount = $('#sourceacc').val();
            var destacc = $('#destacc').val();

            if(amount.length === 0)
            {
                errorTxt = "Please enter an amount";
            }

            if (!$.isNumeric(amount) && errorTxt === '')
            {
                errorTxt = "Invalid amount";
            }

            if(!amount.match(/^\d{2,3}(\.\d{2})?$/i) && errorTxt === '')
            {
                errorTxt = "Invalid amount. Please use two decimal places eg (100.10)";
            }

            if((sourceaccount === "..." || destacc  === "...") && errorTxt === '')
            {
                errorTxt = "Please select a source account and a destination account";
            }

            if(errorTxt !== '')
            {
                alert(errorTxt);
            }else{
                var xmlString ="<?xml version='1.0'?><serviceRequest><action>funds_transfer</action><session_id>"+$('#sessionid').val()+"</session_id><uid>"+$('#userid').val()+"</uid><value>"+amount+"</value><sourceacc>"+sourceaccount+"</sourceacc><destacc>"+destacc+"</destacc></serviceRequest>";                
                postXML(xmlString,'http://localhost/VBA_GlocellWallet/index.php','funds_transfer');
            }


        }
    </script>
    <body>
        <input type="hidden" name="sessionid" id="sessionid"/>
        <input type="hidden" name="userid" id="userid" value="123"/>
        <div id="logindiv" name="logindiv">
            <p>
                <label><strong>Cell Number</strong></label>
                <br/>
                <input type='text' id='user_login' value='0798975549' />
                <br/>
                <br/>
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
                        <input type="button" value="Funds Transfer" onclick="showTransfer()">
                        <input type="button" value="Get Balance" onclick="getBalance()">
                        <input type="button" value="Get Statement" onclick="getStatement()">
                        <input type="button" value="Load GCash Voucher" onclick="showGCash()">
                        <input type="button" value="Get Lay-Bye Voucher" onclick="showLayBye()">
                        <hr>
                        <!-- ------------------------------------------- -->
                        <div id="fundtransferDiv" style="display: none">
                            Please enter the amount you want to transfer
                            <br>
                            <input type="text" id="transferAmount" name="transferAmount" maxlength="10"/>
                            <br>
                            From:
                            <select id="sourceacc">
                                <option value="...">...</option>
                                <option value="pool">My Pool</option>
                                <option value="glocell">Glocell</option>
                                <option value="kcmobile">KC Mobile</option>
                            </select>
                            To:
                            <select id="destacc">
                                <option value="...">...</option>
                                <option value="pool">My Pool</option>
                                <option value="glocell">Glocell</option>
                                <option value="kcmobile">KC Mobile</option>
                            </select>
                            <br>
                            <input type="button" value="Transfer" onclick="fundsTransfer()"/>
                        </div>
                        <!-- ------------------------------------------- -->
                        <div id="balanceDiv" style="display: none">
                            Pool balance
                            <input type="text" id="showbalancePool" readonly="true">
                            Glocell balance
                            <input type="text" id="showbalanceGlo" readonly="true">
                            KCMobile balance
                            <input type="text" id="showbalanceKCM" readonly="true">
                        </div>
                        <!-- ------------------------------------------- -->
                        <div id="statementDiv" style="display: none">

                        </div>
                        <!-- ------------------------------------------- -->
                        <div id="loadGCashDiv" style="display: none">
                            Please enter voucher number
                            <input type="text" id="vouchernumber"/>
                            <br>
                            <input type="button" value="Load" onclick="load_gcash()">
                        </div>
                        <!-- ------------------------------------------- -->
                        <div id="loadLayByeDiv" style="display: none">
                            Please select the voucher source account
                            <br>
                            <select id="sourceaccLB">
                                <option value="...">...</option>
                                <option value="pool">My Pool</option>
                                <option value="glocell">Glocell</option>
                                <option value="kcmobile">KC Mobile</option>
                            </select>
                            <br>
                            <input type="button" value="Generate" onclick="generateLayBye()"/>
                        </div>
                    </td>
                </tr>
            </table>

        </div>
    </body>
</html>