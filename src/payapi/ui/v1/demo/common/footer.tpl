  </div>
  <div class="footer">
    <table>
      <tr>
        <td class="f-left">
          <img src="<?=$branding['partnerIconUrl']?>">
        </td>
        <td class="payapi-logo">
          <a class="nomob _externalink" href="<?=$branding['partnerWebUrl']?>"><img src="<?=$branding['partnerLogoUrl']?>" height="38" id="payapi-logo" alt="<?=$branding['partnerName']?>, <?=$branding['partnerSlogan']?>"></a>
          <br>
          <span class="mob-partner-name"><?=$branding['partnerName']?></span>
          <span><?=$text_powered?> </span>
          <a href="<?=$branding['partnerWebUrl']?>"><?=$branding['partnerName']?></a><span>. <?=$branding['partnerSlogan']?>.</span>
          <br>
        </td>
        <td class="f-right">
          <img src="https://input.payapi.io/modules/core/img/brand/creditcard_lock_icon.png">
        </td>
      </tr>
    </table>
  </div>
</body>
</html>
<script>
  var forms = document.getElementsByTagName('form');
  for(var i=0;i<forms.length;i++) {
  	forms[i].addEventListener('submit',function(){
  		var hidden = document.createElement("input");
  		hidden.setAttribute('type','hidden');
  		hidden.setAttribute('name','hash');
  		hidden.setAttribute('value',window.location.hash);
  		this.appendChild(hidden); 
  	})
  };
</script>