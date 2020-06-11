$(document).ready(function () {
///////////////////////// V1.0.0 - Spotii Widget Code Snippet -- START-- SPOTII OC CHANGE TO BE DONE ////////////////////////////////

$(function () {
  var spotii_product_class = $(".spotii_widget_product");
  if (spotii_product_class && spotii_product_class.length) {
    var prod_price = $(".product-price").text();
    prod_price = prod_price ? prod_price.replace("AED", "") : "";
    if (prod_price) {
      var instaPrice = parseFloat(prod_price / 4).toFixed(2);
      var currency = "د.إ";
      window.spotiiConfig = {
        targetXPath: [".spotii_widget_product"],
        renderToPath: [".spotii_widget_product"],
        currency: prod_price,
      };

      (function (w, d, s) {
        var f = d.getElementsByTagName(s)[0];
        var a = d.createElement("script");
        a.async = true;
        a.src = "https://widget.spotii.me/v1/javascript/price-widget";
        f.parentNode.insertBefore(a, f);
      })(window, document, "script");
      $(".spotii-price").html(instaPrice + " " + currency);
    }
  }

  /** SPOTII OC CHANGE TO BE DONE - For Variant Price Updation for Spotii Widget */

  $(".option").change(function () {
    var prod_price = $(".product-price").text();
    prod_price = prod_price ? prod_price.replace("AED", "") : "";
    var instaPrice = parseFloat(prod_price / 4).toFixed(2);
    var currency = "د.إ";
    $(".spotii-price").html(instaPrice + " " + currency);
    //   console.log("[spotii]:", instaPrice + " " + currency);
  });

  /** SPOTII OC CHANGE TO BE DONE - For Cart Widget */

  var spotii_cart_class = $(".spotii_widget_cart");
  if (spotii_cart_class && spotii_cart_class.length) {
    var cart_total =
      $("#total tr:nth-child(2)")
        .text()
        .replace(/[^0-9]/g, "") / 100;
    if (cart_total) {
      var instaPrice = parseFloat(cart_total / 4).toFixed(2);
      var currency = "د.إ";
      window.spotiiConfig = {
        targetXPath: [".spotii_widget_cart"],
        renderToPath: [".spotii_widget_cart"],
        currency: cart_total,
      };

      (function (w, d, s) {
        var f = d.getElementsByTagName(s)[0];
        var a = d.createElement("script");
        a.async = true;
        a.src = "https://widget.spotii.me/v1/javascript/price-widget";
        f.parentNode.insertBefore(a, f);
      })(window, document, "script");
      $(".spotii-price").html(instaPrice + " " + currency);
    }
  }
});

///////////////////////// V1.0.0 - Spotii Widget Code Snippet -- END -- ////////////////////////////////
});
