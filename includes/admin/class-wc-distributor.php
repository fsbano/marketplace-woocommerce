<?php

class wc_distributor {

  function setCep() {
    ?>
    <input type="text" id="postcode" class="input-text" maxlength="19" >
    <?php
  }

  function getShippingZone($zone_name) {

    if ( ! is_front_page() && is_home() ) die;

    $shipping_zones = WC_Shipping_Zones::get_zones();
    print "<select name=\"zone_name\" id=\"zone_name\">";
    foreach ($shipping_zones as $shipping_zone) {
      if ($zone_name == $shipping_zone["zone_name"]) {
        echo "<option value='".$shipping_zone["zone_name"]."' selected=\"\">".$shipping_zone["zone_name"]."</option>";
      } else {
        echo "<option value='".$shipping_zone["zone_name"]."'>".$shipping_zone["zone_name"]."</option>";
      }
    }
    print "</select>";
  
  }  

}
