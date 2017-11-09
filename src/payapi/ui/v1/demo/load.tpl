<?=$header?>
    <br>
    <h1 align="center"><?=$text_wait?></h1>
    <div class="row">
      <div class="loading">
        <p><ul class="fa-ul loading-steps" id="loadingSteps">
        <li><i class="fa-li fa fa-spinner fa-spin fa-2x fa-pull-left"></i><?=$loading_1?></li>
        <li><i class="fa-li fa fa-spinner fa-spin fa-2x fa-pull-left"></i><?=$loading_2?></li>
        <li><i class="fa-li fa fa-spinner fa-spin fa-2x fa-pull-left"></i><?=$loading_3?></li>
        <li><i class="fa-li fa fa-spinner fa-spin fa-2x fa-pull-left"></i><?=$loading_4?></li>
        <li><i class="fa-li fa fa-spinner fa-spin fa-2x fa-pull-left"></i><?=$loading_5?></li>
        </ul></p>
      </div>
    </div><br><br><br><br>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="<?=$media?>lib/ScrollToFixed/jquery-scrolltofixed-min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <script>
    var showTick = function(elem){
      elem.find('i').removeClass('fa-spinner fa-spin').addClass('fa-check').css('color','#4CAF50');
    };
    function showLoadingTicks() {
      var loadingStepsLi = $('#loadingSteps').find('li');
      var delaySteps = 0;
      loadingStepsLi.each(function(i) {
        if(i < loadingStepsLi.length){
          delaySteps += Math.floor(Math.random() * (500));
          setTimeout(showTick, delaySteps, $(this));
          if (i == (loadingStepsLi.length - 1)){
            setTimeout(function(){
              window.location = "<?=$redirectLocation?>";
            }, delaySteps);
          }
        }
      });
      return true;
    }
    $(function(){
      showLoadingTicks();
    });
    </script>
    <?=$footer?>
  </body>
</html>
