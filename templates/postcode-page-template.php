<?php
  if ( isset($_POST["buscar"]) ) {
    $postcode=preg_replace('/\D/', '',$_POST["postcode"]);
    $shipping_zones = WC_Shipping_Zones::get_zones();
    foreach ($shipping_zones as $shipping_zone) {
      $zone_locations_name = $shipping_zone["zone_name"];
      $zone_locations_code = $shipping_zone["zone_locations"];
      foreach ($zone_locations_code as $value) {
        if ($value->code == $postcode) {
          session_start();
          $_SESSION["zone_name"] = $zone_locations_name;
          $_SESSION["postcode"] = $_POST["postcode"];
          wp_redirect(home_url());
          exit();
        }
      }
    }
  }
?>
<?php
  get_header();
?>
<?php
  if ($_POST) {
    session_destroy();
  }
?>
  <div style="margin: 0 auto;">
    <form method="post">
      <label>Cep:</label>
      <input type="text" name="postcode" id="postcode">
      <input type="submit" name="buscar" id="buscar" value="Buscar">
    </form>
  </div>
<?php
  get_footer();
?>

