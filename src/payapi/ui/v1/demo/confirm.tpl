<?=$header?>
    <h1><?=$title_confirm?><img class="f-right" src="<?=$branding['partnerLogoUrl']?>" height="28" alt="<?=$branding['partnerName']?>" title="><?=$branding['partnerName']?>"></h1>
    <form class="form-horizontal" action="/index.php?route=api/demo&mode=success" method="post">
    <row>
      <div class="col-sm-12">
      <?=$paymentInfo?>
        <fieldset><legend><?=$block_order?></legend>
        <div class="col-md-12 this-order-information" id="this-order-information">
          <dl class="dl-horizontal">
            <dt><?=$text_total?></dt>
            <dd><?=$payment['total_html']?></dd>
          </dl>
        </div>
        </fieldset>
      </div>
    </row>
    <row>
      <div class="col-sm-12">
        <fieldset>
          <legend><?=$block_product?></legend>
          <div class="table-responsive">
            <table class="table table-striped">
              <tr>
                <th></th>
                <th><?=$label_title?></th>
                <th><?=$label_quantity?></th>
                <th><?=$label_unit_price?></th>
              </tr>
              <tr>
                <td class="show-image">
                  <div class="product-header">
                    <div class="product-container">
                      <a href="<?=$product['url' ]?>">
                        <img class="product-image" src="<?=$product['image']?>" alt="<?=$product [ 'name' ]?>" onerror="this.style.display=&quot;none&quot;">
                      </a>
                    </div>
                  </div>
                </td>
                <td>
                  <?=$product['name']?>
                </td>
                <td><?=$product['quantity']?></td>
                <td><?=$product['price_html']?></td>
              </tr>
              <tr>
                <td class="show-image"></td>
                <td><?=$label_handling?></td>
                <td>1</td>
                <td><?=$product['shipping_html']?></td>
              </tr>
            </table>
          </div>
        </fieldset>
      </div>
    </row>
    <row>
      <div class="col-sm-6">
        <fieldset>
          <legend><?=$block_delivery?></legend>
          <div class="col-md-12">
            <dl class="dl-horizontal">

              <dd class="shippingAddress">
                <?=$consumer['fullname']?><br>
                <?=$consumer['address']?><br>
                <?=$consumer['postal']?> <?=$consumer['city']?>, <?=$consumer['region']?><br>
                <?=$consumer['country']?><br>
              </dd>
            </dl>
          </div>
        </fieldset>
      </div>
      <div class="col-sm-6">
        <fieldset>
          <legend><?=$block_payment?></legend>
          <div class="col-md-12">
              <dl class="dl-horizontal">
                <?=$paymentBLock?>
              </dl>
              <div class="form-group">
                <label class="col-md-4 control-label" for="payment"></label>
              </div>
            </div>
            </fieldset>
          </div>
        </row>
        <span class="clear-color"></span>
        <div class="but-control">
          <div class="fixed-control">
            <button class="btn btn-success" id="payment" name="payment"><?=$button_confirm?></button>
          </div>
          <a class="btn btn-danger" id="cancel" href="/index.php?route=api/demo&mode=cancel"><?=$button_cancel?></a>
          <span class="clear"></span>
          <!--script>jQuery(\'#payment\').scrollToFixed({marginBottom: 0, zIndex: 1});</script-->
        </div>
      </form>
    </div>
<?=$footer?>
