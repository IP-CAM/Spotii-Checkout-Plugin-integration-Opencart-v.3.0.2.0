///////////////////////// V1.0.0 - Spotii Widget Code Snippet -- START-- SPOTII OC CHANGE TO BE DONE ////////////////////////////////
$(document).ready(function () 
{
  $(function () { /** This is the Start of Function for all widgets */
    var sptii_product_class = $(".spotii_widget_product");
    if (sptii_product_class && sptii_product_class.length) { /** This is the Start of product div check */
      var prod_price = $(".product-price").text();
      prod_price = prod_price ? prod_price.replace("AED", "") : "";
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

      setTimeout(function () {
        $(".spotii-price").html(instaPrice + " " + currency);
      }, 1000);

      /** SPOTII OC CHANGE TO BE DONE - For Variant Price Updation for Spotii Widget */

      $(".option").change(function () { /** This is the Start of Function for Product Option Price Update in Widget */
        var prod_price = $(".product-price").text();
        prod_price = prod_price ? prod_price.replace("AED", "") : "";
        var instaPrice = parseFloat(prod_price / 4).toFixed(2);
        var currency = "د.إ";
        $(".spotii-price").html(instaPrice + " " + currency);
      }); /** This is the End of Function for Product Option Price Update in Widget */

    } /** This is the Start of product div check */

    /** SPOTII OC CHANGE TO BE DONE -  */

    var sptii_cart_class = $(".spotii_widget_cart");
    if (sptii_cart_class && sptii_cart_class.length) { /** This is the Start of Function for Cart Widget */
      var cart_total =
        $("#total tr:nth-child(2)")
          .text()
          .replace(/[^0-9]/g, "") / 100;
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

      setTimeout(function () {
        $(".spotii-price").html(instaPrice + " " + currency);
      }, 1000);
    } /** This is the End of Function for Cart Widget */
  }); /** This is the End of Function for all widgets */
});
///////////////////////// V1.0.0 - Spotii Widget Code Snippet -- END -- ////////////////////////////////
