

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

        setTimeout(function () {
          $(".spotii-price").html(instaPrice + " " + currency);
        }, 1000);
      }

      /** SPOTII OC CHANGE TO BE DONE - For Variant Price Updation for Spotii Widget */

      $("select:first").on("change", function () {
        var prod_price = $(".product-price").text();
        prod_price = prod_price ? prod_price.replace("AED", "") : "";
        var instaPrice = parseFloat(prod_price / 4).toFixed(2);
        var currency = "د.إ";
        setTimeout(function () {
          $(".spotii-price").html(instaPrice + " " + currency);
        }, 2000);
      });
    }

    /** SPOTII OC CHANGE TO BE DONE - For Cart Widget */

    var spotii_cart_class = $(".spotii_widget_cart");
    if (spotii_cart_class && spotii_cart_class.length) {
      var cart_total = $("#cart-total").text();
      cart_total = cart_total
        ? cart_total.split("-")[1].replace("AED", "")
        : "";
      if (cart_total) {
        var instaPrice = parseFloat(cart_total.replace(/,/g, "") / 4).toFixed(
          2
        );
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
      }
    }
  });

  ///////////////////////// V1.0.0 - Spotii Widget Code Snippet -- END -- ////////////////////////////////
});

