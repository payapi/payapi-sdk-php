<?=$header?>
  <h1 class="alert alert-<?=$class?>" role="alert"><?=$title;?></h1>
  <row>
    <div class="col-sm-12 notice"><?=$description;?>
       <a href="<?=$redirectLocation;?>"><?=$text_click_here;?></a>.
       <!--script>window.setTimeout(function(){
        window.location.href = "<?=$redirectLocation;?>";
      }, 3000);</script-->
    </div>
  </row>
<?=$footer?>
