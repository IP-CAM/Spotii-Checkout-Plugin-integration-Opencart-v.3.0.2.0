
## Spotii Checkout Plugin integration for OpenCart 

The Spotii Checkout Plugin serves as a payment gateway and has been developed for OpenCart version 3.0.2.0. 

 

*OpenCart install files and paths*: 

Installation of the Spotii Checkout Plugin requires *10 files* to be placed in certain locations within your OpenCart code / project folder. Please copy each file (all named spotii.*) to the corresponding location in the Opencart folder as mentioned below: 

File inside the following folder in OC_Spotii_plugin_master.zip / folder 

Desired location of this file in Opencart folder 

    Admin Controller : admin/controller/extension/payment/ 

    Admin Language File : admin/language/en-gb/extension/payment/ 

    Admin Model File : admin/model/extension/payment/ 

    Admin View File : admin/view/template/extension/payment/ 

    Image File : admin/view/image/payment/ 

    Catalog Controller : catalog/controller/extension/payment/ 

    Catalog Language File : catalog/language/en-gb/extension/payment/  

    Catalog Model : catalog/model/extension/payment/ 

    Catalog View File – Spotii.twig and Spotii_error.twig : catalog/view/theme/default/template/extension/payment/ 

 

Once these files are in their respective paths, please open your Opencart Admin Panel and follow these steps to install the Spotii Checkout plugin on your Opencart instance: 

 

1. In the sidebar, select Extensions 

 

2. In the sub-list, select Extensions again 

 

3. In the drop-down menu under “Choose the Extension type”, please select Payments 

 

4. Scroll down to Spotii Payment Method and click on Install 

 

5. Once installed, click on Edit 

 

6. In the Edit Spotii Pay section under Spotii Payment Method (see screenshot below), please update the Public / Private keys and select the order status required to be shown when a Spotii payment is successfully processed.  

7. Under test mode, either select Sandbox for test environment or,  Live for production environment 

8. The Total field sets a minimum cart amount that is required to enable Spotii checkout option on the cart / checkout page. Default value for this minimum cart amount is AED 200. You can always change this later, as per your business requirements and agreement with Spotii 

9. Order Status: you can select the Order Status that you want to be shown once an order is successfully processed through the Spotii Payment Gateway. You can create a custom status or use any of the default status options that are available (custom order status creation is factory feature of OpenCart). 

10. Please select Status as enabled. This is required for Spotii to be displayed as a payment option on the checkout page. A payment gateway can be installed but will not be available for commercial use unless set as enabled in this screen 

11. Sort order can be set as per merchant preference with relation to other payment gateways 

12. Please click on the Save button on the top right corner to save changes to your Spotii Checkout Plugin 

 

## Steps to setup Spotii widget on the product and cart page 

  

1. Add the below “div" element just below the price block of code inside the file called “product.twig” (Reference path \catalog\view\theme\default\template\product\product.twig)  

    <div class="spotii_widget_product">Buy Now, Pay Later</div>  

 
2. Add the “div" element just below the price block of code inside the file called “cart.twig”, (Reference path \catalog\view\theme\default\template\checkout\cart.twig)  

     <div class=" spotii_widget_cart ">Buy Now, Pay Later</div>  

3. Copy the file "common.js" from the Spotii plugin folder (\OC_Spotii_Plugin-master\Spotii_OC3.0.2.0_Plugin_V1.0.0) to the common.js file of corresponding location in the file folder (at reference path \catalog\view\javascript\common.js)


## At this stage, Spotii installation is complete!  

-Team Spotii 😊 

 

 
